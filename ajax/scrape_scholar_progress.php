<?php
// =============================================
// AJAX Scraping Google Scholar Direct + Progress (SSE)
// Endpoint: ajax/scrape_scholar_progress.php?id=XX
// =============================================
@ini_set('output_buffering', 'off');
@ini_set('zlib.output_compression', false);
while (ob_get_level()) ob_end_clean();

header('Content-Type: text/event-stream');
header('Cache-Control: no-cache');
header('Connection: keep-alive');
header('X-Accel-Buffering: no');

set_time_limit(0);
ignore_user_abort(true);

require_once __DIR__ . '/../config/koneksi.php';

// =============================================
// SSE Helpers
// =============================================
function sendProgress($step, $total_steps, $message) {
    $payload = json_encode([
        'step'        => $step,
        'total_steps' => $total_steps,
        'percent'     => round(($step / $total_steps) * 100),
        'message'     => $message,
    ]);
    echo "data: {$payload}\n\n";
    flush();
}

function sendLog($message) {
    $payload = json_encode(['log' => $message]);
    echo "data: {$payload}\n\n";
    flush();
}

function sendDone($results, $errors) {
    $payload = json_encode([
        'done'    => true,
        'results' => $results,
        'errors'  => $errors,
    ]);
    echo "data: {$payload}\n\n";
    flush();
}

// =============================================
// Generic cURL Fetcher
// =============================================
function fetchPage($url, $cookieFile = null) {
    $ch = curl_init();
    $opts = [
        CURLOPT_URL            => $url,
        CURLOPT_RETURNTRANSFER => true,
        CURLOPT_FOLLOWLOCATION => true,
        CURLOPT_TIMEOUT        => 30,
        CURLOPT_CONNECTTIMEOUT => 10,
        CURLOPT_SSL_VERIFYPEER => false,
        CURLOPT_SSL_VERIFYHOST => false,
        CURLOPT_ENCODING       => 'gzip, deflate',
        CURLOPT_HTTPHEADER     => [
            'User-Agent: Mozilla/5.0 (Macintosh; Intel Mac OS X 10_15_7) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/122.0.0.0 Safari/537.36',
            'Accept: text/html,application/xhtml+xml,application/xml;q=0.9,image/avif,image/webp,image/apng,*/*;q=0.8,application/signed-exchange;v=b3;q=0.7',
            'Accept-Language: en-US,en;q=0.9',
        ],
    ];
    if ($cookieFile) {
        $opts[CURLOPT_COOKIEFILE] = $cookieFile;
        $opts[CURLOPT_COOKIEJAR]  = $cookieFile;
    }
    curl_setopt_array($ch, $opts);

    $response = curl_exec($ch);
    $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
    $error    = curl_error($ch);
    curl_close($ch);

    if ($httpCode == 200 && $response && !$error) {
        return $response;
    }
    if ($httpCode >= 400 || $error) {
        sendLog("⚠️ HTTP Error: $httpCode | cURL: $error");
    }
    return false;
}

function normalizeTitle($title) {
    // lowercase, strip non-alphanumeric, strip spaces
    return preg_replace('/[^a-z0-9]/', '', strtolower($title));
}

// =============================================
// GOOGLE SCHOLAR DIRECT SCRAPER
// =============================================
function scrapeGoogleScholar($scholar_url, $conn = null, $dosen_id = null) {
    if (empty($scholar_url)) return false;

    // Extract user ID from URL
    $user_id = '';
    if (preg_match('/user=([a-zA-Z0-9_-]+)/', $scholar_url, $m)) {
        $user_id = $m[1];
    }
    if (empty($user_id)) return false;

    // --- PRE-LOAD EXISTING DATA ---
    $existing_articles = [];
    if ($conn && $dosen_id) {
        $res = $conn->query("SELECT judul, jilid, terbitan, halaman FROM publikasi_gs WHERE dosen_id = $dosen_id");
        if ($res) {
            while ($row = $res->fetch_assoc()) {
                $norm = normalizeTitle($row['judul']);
                $has_meta = !empty($row['jilid']) || !empty($row['terbitan']) || !empty($row['halaman']);
                $existing_articles[$norm] = [
                    'has_meta' => $has_meta
                ];
            }
        }
    }
    // ------------------------------

    $all_data = [];
    $cstart = 0;
    $pagesize = 100; // Google Scholar allows up to 100 per page
    $page_num = 1;
    $stop_sync = false;

    while (!$stop_sync) {
        $url = "https://scholar.google.com/citations?user={$user_id}&hl=id&cstart={$cstart}&pagesize={$pagesize}";
        sendLog("📄 Google Scholar halaman {$page_num} (offset {$cstart})...");

        $html = fetchPage($url);
        if (!$html) {
            if ($cstart == 0) return false;
            sendLog("⚠️ Google Scholar: gagal fetch halaman {$page_num}");
            break;
        }

        // Cek apakah Google memblokir (CAPTCHA)
        if (strpos($html, 'automated requests') !== false || strpos($html, 'captcha') !== false) {
            sendLog("⚠️ Google Scholar: CAPTCHA terdeteksi, berhenti.");
            break;
        }

        $items = parseGoogleScholarItems($html);

        if (empty($items)) {
            sendLog("✅ Google Scholar: tidak ada data lagi di halaman {$page_num}.");
            break;
        }

        // --- SMART SYNC CHECK ---
        $old_count = 0;
        foreach ($items as $item) {
            if (isset($existing_articles[normalizeTitle($item['judul'])])) {
                $old_count++;
            }
        }
        
        if (count($items) > 0 && ($old_count > (count($items) / 2))) {
            sendLog("⚡ Smart Sync: Ditemukan $old_count data lama di halaman ini. Menghentikan list fetch.");
            $stop_sync = true;
        }
        // -------------------------

        $all_data = array_merge($all_data, $items);
        sendLog("📋 Google Scholar halaman {$page_num}: " . count($items) . " item (" . count($all_data) . " total)");

        if (count($items) < $pagesize) {
            sendLog("✅ Google Scholar: halaman terakhir tercapai.");
            break;
        }

        $cstart += $pagesize;
        $page_num++;

        // Delay acak 1-2 detik agar tidak diblokir Google
        usleep(rand(1000000, 2000000));
    }

    // ============ TAHAP 1: SIMPAN LIST AWAL ============
    if (!empty($all_data)) {
        sendLog("💾 Menyimpan list awal " . count($all_data) . " artikel...");
        batchInsertGS($conn, $dosen_id, $all_data);
    }

    // ============ TAHAP 2: SYNC METADATA (INCREMENTAL) ============
    if (!empty($all_data)) {
        sendLog("🔍 Sinkronisasi metadata detail...");
        $processed = 0;
        $skipped = 0;
        $total = count($all_data);

        foreach ($all_data as $item) {
            $norm = normalizeTitle($item['judul']);
            
            // SKIP jika sudah ada di database DAN sudah punya metadata
            if (isset($existing_articles[$norm]) && $existing_articles[$norm]['has_meta']) {
                $skipped++;
                continue;
            }

            if (!empty($item['url'])) {
                $processed++;
                if ($processed % 5 == 0 || $processed == 1) {
                    sendLog("📖 Fetch detail baru ({$processed} diproses, {$skipped} dilewati)...");
                }

                $detail_html = fetchPage($item['url']);
                if ($detail_html) {
                    $meta = parseGSDetailPage($detail_html);
                    $full_item = array_merge($item, $meta);
                    
                    // Simpan satu per satu agar progress tidak hilang
                    batchInsertGS($conn, $dosen_id, [$full_item]);
                }

                // Delay acak 1.2 - 2.5 detik agar lebih aman dari limit Google
                usleep(rand(1200000, 2500000)); 
            }
        }
        sendLog("✅ Sinkronisasi selesai: {$processed} metadata baru diambil, {$skipped} data lama dilewati.");
    }

    return $all_data;
}

function parseGoogleScholarItems($html) {
    $data = [];
    $dom = new DOMDocument();
    libxml_use_internal_errors(true);
    @$dom->loadHTML(mb_convert_encoding($html, 'HTML-ENTITIES', 'UTF-8'));
    libxml_clear_errors();
    $xpath = new DOMXPath($dom);

    $rows = $xpath->query("//tr[contains(@class,'gsc_a_tr')]");

    foreach ($rows as $row) {
        $entry = [
            'judul' => '', 'tahun' => '', 'url' => '', 'sitasi' => 0,
            'penulis' => '', 'nama_jurnal' => '',
            'jilid' => '', 'terbitan' => '', 'halaman' => '',
        ];

        // Judul + URL
        $titleNode = $xpath->query(".//a[contains(@class,'gsc_a_at')]", $row);
        if ($titleNode->length > 0) {
            $entry['judul'] = trim($titleNode->item(0)->textContent);
            $href = $titleNode->item(0)->getAttribute('href');
            if ($href) {
                if (strpos($href, 'http') !== 0) $href = 'https://scholar.google.com' . $href;
                $entry['url'] = $href;
            }
        }

        // Penulis + Jurnal dari teks abu-abu di bawah judul
        $grayNodes = $xpath->query(".//div[contains(@class,'gs_gray')]", $row);
        if ($grayNodes->length > 0) {
            $entry['penulis'] = trim($grayNodes->item(0)->textContent);
        }
        if ($grayNodes->length > 1) {
            $entry['nama_jurnal'] = trim($grayNodes->item(1)->textContent);
        }

        // Tahun
        $yearNode = $xpath->query(".//td[contains(@class,'gsc_a_y')]//span", $row);
        if ($yearNode->length > 0) {
            $entry['tahun'] = trim($yearNode->item(0)->textContent);
        }

        // Sitasi
        $citeNode = $xpath->query(".//td[contains(@class,'gsc_a_c')]//a", $row);
        if ($citeNode->length > 0) {
            $citeText = trim($citeNode->item(0)->textContent);
            $entry['sitasi'] = is_numeric($citeText) ? (int)$citeText : 0;
        }

        if (!empty($entry['judul'])) $data[] = $entry;
    }

    return $data;
}

function parseGSDetailPage($html) {
    $meta = [
        'penulis' => '', 'nama_jurnal' => '',
        'jilid' => '', 'terbitan' => '', 'halaman' => '',
    ];

    $dom = new DOMDocument();
    libxml_use_internal_errors(true);
    @$dom->loadHTML(mb_convert_encoding($html, 'HTML-ENTITIES', 'UTF-8'));
    libxml_clear_errors();
    $xpath = new DOMXPath($dom);

    $fields = $xpath->query("//div[contains(@class,'gsc_oci_field')]");
    $values = $xpath->query("//div[contains(@class,'gsc_oci_value')]");

    $fieldCount = min($fields->length, $values->length);
    for ($i = 0; $i < $fieldCount; $i++) {
        $label = strtolower(trim($fields->item($i)->textContent));
        $value = trim($values->item($i)->textContent);

        if (empty($value)) continue;

        if (in_array($label, ['authors', 'penulis', 'author'])) {
            $meta['penulis'] = $value;
        } elseif (in_array($label, ['journal', 'jurnal', 'conference', 'konferensi', 'book', 'buku', 'source'])) {
            $meta['nama_jurnal'] = $value;
        } elseif (in_array($label, ['volume', 'jilid'])) {
            $meta['jilid'] = $value;
        } elseif (in_array($label, ['issue', 'terbitan', 'number', 'nomor'])) {
            $meta['terbitan'] = $value;
        } elseif (in_array($label, ['pages', 'halaman'])) {
            $meta['halaman'] = $value;
        }
    }

    return $meta;
}

function hasMoreGSPages($html) {
    $dom = new DOMDocument();
    libxml_use_internal_errors(true);
    @$dom->loadHTML(mb_convert_encoding($html, 'HTML-ENTITIES', 'UTF-8'));
    libxml_clear_errors();
    $xpath = new DOMXPath($dom);

    $moreBtn = $xpath->query("//button[@id='gsc_bpf_more']");
    if ($moreBtn->length > 0) {
        $disabled = $moreBtn->item(0)->getAttribute('disabled');
        if ($disabled !== null && ($disabled === '' || $disabled === 'disabled' || $disabled === 'true')) {
            return false;
        }
        $style = $moreBtn->item(0)->getAttribute('style');
        if (strpos($style, 'display:none') !== false || strpos($style, 'display: none') !== false) {
            return false;
        }
        return true;
    }
    return false;
}

// =============================================
// Batch INSERT helpers
// =============================================
function batchInsertGS($conn, $dosen_id, $items) {
    if (empty($items)) return ['total' => 0, 'new' => 0, 'in_db' => 0];

    // Ambil daftar judul yang sudah ada di database (Hash Map + Normalization)
    $existingTitles = [];
    $res = $conn->query("SELECT judul FROM publikasi_gs WHERE dosen_id = $dosen_id");
    while ($row = $res->fetch_assoc()) {
        $t = trim($row['judul']);
        $existingTitles[strtolower($t)] = true;
        $existingTitles['norm_' . normalizeTitle($t)] = true;
    }

    $affected_total = 0;
    $inserted_items = []; // Menyimpan item yang siap dimasukkan (non-duplikat)

    // Filter dengan Fuzzy String Matching + Hash Lookup
    foreach ($items as $item) {
        $lowTitle = strtolower(trim($item['judul']));
        $normTitle = normalizeTitle($item['judul']);
        $isDuplicate = false;

        // 1. Cek Exact / Normalized Match (O(1))
        if (isset($existingTitles[$lowTitle]) || isset($existingTitles['norm_' . $normTitle])) {
            $isDuplicate = true;
        } else {
            // 2. Cek Fuzzy Match (O(N))
            $newLen = strlen($lowTitle);
            foreach ($existingTitles as $existTitle => $val) {
                if (strpos($existTitle, 'norm_') === 0) continue;
                
                $existLen = strlen($existTitle);
                if (abs($newLen - $existLen) > ($newLen * 0.2)) continue;

                similar_text($lowTitle, $existTitle, $percent);
                if ($percent >= 85) {
                    $isDuplicate = true;
                    break;
                }
            }
        }

        if (!$isDuplicate) {
            $inserted_items[] = $item;
            $existingTitles[$lowTitle] = true; 
            $existingTitles['norm_' . $normTitle] = true;
        }
    }

    if (empty($inserted_items)) {
        $result = $conn->query("SELECT COUNT(*) as cnt FROM publikasi_gs WHERE dosen_id = $dosen_id");
        return ['total' => count($items), 'new' => 0, 'in_db' => $result->fetch_assoc()['cnt']];
    }

    $chunks = array_chunk($inserted_items, 50);

    foreach ($chunks as $chunk) {
        $values = [];
        foreach ($chunk as $item) {
            $judul       = $conn->real_escape_string($item['judul']);
            $judul_hash  = md5($item['judul']);
            $penulis     = $conn->real_escape_string($item['penulis'] ?? '');
            $nama_jurnal = $conn->real_escape_string($item['nama_jurnal'] ?? '');
            $jilid       = $conn->real_escape_string($item['jilid'] ?? '');
            $terbitan    = $conn->real_escape_string($item['terbitan'] ?? '');
            $halaman     = $conn->real_escape_string($item['halaman'] ?? '');
            $tahun       = $conn->real_escape_string($item['tahun']);
            $sitasi      = (int)($item['sitasi'] ?? 0);
            $url         = $conn->real_escape_string($item['url']);
            $values[]    = "($dosen_id, '$judul', '$judul_hash', '$penulis', '$nama_jurnal', '$jilid', '$terbitan', '$halaman', '$tahun', $sitasi, '$url')";
        }

        $sql = "INSERT INTO publikasi_gs (dosen_id, judul, judul_hash, penulis, nama_jurnal, jilid, terbitan, halaman, tahun, sitasi, url) VALUES "
             . implode(',', $values)
             . " ON DUPLICATE KEY UPDATE penulis=VALUES(penulis), nama_jurnal=VALUES(nama_jurnal), jilid=VALUES(jilid), terbitan=VALUES(terbitan), halaman=VALUES(halaman), tahun=VALUES(tahun), sitasi=VALUES(sitasi), url=VALUES(url), updated_at=NOW()";

        $conn->query($sql);
        $affected_total += max(0, $conn->affected_rows);
    }

    $result = $conn->query("SELECT COUNT(*) as cnt FROM publikasi_gs WHERE dosen_id = $dosen_id");
    $total_in_db = $result->fetch_assoc()['cnt'];

    return ['total' => count($items), 'new' => $affected_total, 'in_db' => $total_in_db];
}

// =============================================
// MAIN PROCESS
// =============================================
$id = isset($_GET['id']) ? (int)$_GET['id'] : 0;
$dosen = $conn->query("SELECT * FROM dosen WHERE id = $id")->fetch_assoc();

if (!$dosen) {
    sendDone([], ['Dosen tidak ditemukan.']);
    exit;
}

$scholar_url = trim($dosen['scholar_url'] ?? '');
$has_scholar = !empty($scholar_url);

if (!$has_scholar) {
    sendDone([], ['Tidak ada URL Google Scholar yang bisa di-scrape.']);
    exit;
}

$results = [];
$errors  = [];
$total_steps = 1;

// ============ STEP 1: GOOGLE SCHOLAR (DIRECT) ============
sendProgress(1, $total_steps, 'Scraping Google Scholar...');

$main_start_time = microtime(true);
sendLog("🔍 Scraping profil Google Scholar: {$scholar_url}");
$gs_data = scrapeGoogleScholar($scholar_url, $conn, $id);
if ($gs_data !== false && !empty($gs_data)) {
    $results['google_scholar'] = batchInsertGS($conn, $id, $gs_data);
    sendLog('✅ Google Scholar: ' . count($gs_data) . ' publikasi ditemukan & disimpan');
} else {
    if ($gs_data === false) {
        $errors[] = "Gagal mengakses Google Scholar. Periksa URL profil.";
    } else {
        sendLog('ℹ️ Google Scholar: tidak ada publikasi ditemukan.');
        $results['google_scholar'] = ['total' => 0, 'new' => 0, 'in_db' => 0];
    }
}

// Update last_scraped
$conn->query("UPDATE dosen SET last_scraped_gs = NOW() WHERE id = $id");

$total_duration = round(microtime(true) - $main_start_time, 2);
sendLog("⏱️ Total durasi scraping Scholar: $total_duration detik.");

sendDone($results, $errors);
