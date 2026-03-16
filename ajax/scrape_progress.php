<?php
// =============================================
// AJAX Scraping dengan Login SINTA + Progress (SSE)
// Endpoint: ajax/scrape_progress.php?id=XX
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
            'User-Agent: Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/120.0.0.0 Safari/537.36',
            'Accept: text/html,application/xhtml+xml,application/xml;q=0.9,*/*;q=0.8',
            'Accept-Language: id-ID,id;q=0.9,en;q=0.8',
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
    return false;
}

function normalizeTitle($title) {
    // lowercase, strip non-alphanumeric, strip spaces
    return preg_replace('/[^a-z0-9]/', '', strtolower($title));
}

// =============================================
// SINTA Login
// =============================================
function loginSinta($username, $password, $cookieFile) {
    if (empty($username) || empty($password)) return false;

    // --- SESSION PERSISTENCE ---
    if (file_exists($cookieFile) && filesize($cookieFile) > 0) {
        // Cek apakah session masih valid. Jika akses profil tidak me-redirect ke login, berarti masih valid.
        $ch = curl_init('https://sinta.kemdiktisaintek.go.id/authors');
        curl_setopt_array($ch, [
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_FOLLOWLOCATION => true,
            CURLOPT_MAXREDIRS      => 2,
            CURLOPT_COOKIEFILE     => $cookieFile,
            CURLOPT_SSL_VERIFYPEER => false,
            CURLOPT_SSL_VERIFYHOST => false,
            CURLOPT_TIMEOUT        => 10
        ]);
        curl_exec($ch);
        $finalUrl = curl_getinfo($ch, CURLINFO_EFFECTIVE_URL);
        curl_close($ch);
        
        if (strpos($finalUrl, '/logins') === false) {
             sendLog('🔄 Sesi SINTA masih valid, menggunakan kembali session.');
             return true; // Session masih valid!
        }
    }
    // ----------------------------

    fetchPage('https://sinta.kemdiktisaintek.go.id/logins', $cookieFile);
    usleep(300000);

    $ch = curl_init();
    curl_setopt_array($ch, [
        CURLOPT_URL            => 'https://sinta.kemdiktisaintek.go.id/logins/do_login',
        CURLOPT_RETURNTRANSFER => true,
        CURLOPT_POST           => true,
        CURLOPT_POSTFIELDS     => http_build_query(['username' => $username, 'password' => $password]),
        CURLOPT_FOLLOWLOCATION => true,
        CURLOPT_MAXREDIRS      => 5,
        CURLOPT_TIMEOUT        => 30,
        CURLOPT_SSL_VERIFYPEER => false,
        CURLOPT_SSL_VERIFYHOST => false,
        CURLOPT_ENCODING       => 'gzip, deflate',
        CURLOPT_COOKIEFILE     => $cookieFile,
        CURLOPT_COOKIEJAR      => $cookieFile,
        CURLOPT_HTTPHEADER     => [
            'User-Agent: Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36',
            'Content-Type: application/x-www-form-urlencoded',
            'Referer: https://sinta.kemdiktisaintek.go.id/logins',
        ],
    ]);

    $response = curl_exec($ch);
    $finalUrl = curl_getinfo($ch, CURLINFO_EFFECTIVE_URL);
    curl_close($ch);

    return (strpos($finalUrl, '/logins') === false) || ($response && strpos($response, 'do_login') === false);
}

// Helper untuk fetch banyak halaman sekaligus
function fetchPagesParallel($urls, $cookieFile) {
    if (empty($urls)) return [];
    
    $mh = curl_multi_init();
    $handles = [];
    $results = [];
    
    foreach ($urls as $i => $url) {
        $ch = curl_init($url);
        $opts = [
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_FOLLOWLOCATION => true,
            CURLOPT_MAXREDIRS      => 3,
            CURLOPT_TIMEOUT        => 20,
            CURLOPT_CONNECTTIMEOUT => 10,
            CURLOPT_SSL_VERIFYPEER => false,
            CURLOPT_SSL_VERIFYHOST => false,
            CURLOPT_ENCODING       => 'gzip, deflate',
            CURLOPT_HTTPHEADER     => [
                'User-Agent: Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36',
                'Accept: text/html,application/xhtml+xml,application/xml;q=0.9,*/*;q=0.8',
            ],
        ];
        if ($cookieFile) {
            $opts[CURLOPT_COOKIEFILE] = $cookieFile;
            $opts[CURLOPT_COOKIEJAR]  = $cookieFile;
        }
        curl_setopt_array($ch, $opts);
        curl_multi_add_handle($mh, $ch);
        $handles[$i] = $ch;
    }
    
    $active = null;
    do {
        $mrc = curl_multi_exec($mh, $active);
    } while ($mrc == CURLM_CALL_MULTI_PERFORM);

    while ($active && $mrc == CURLM_OK) {
        if (curl_multi_select($mh) != -1) {
            do {
                $mrc = curl_multi_exec($mh, $active);
            } while ($mrc == CURLM_CALL_MULTI_PERFORM);
        } else {
            usleep(100); // minimal sleep to avoid busy-waiting if select returns -1
        }
    }
    
    foreach ($handles as $i => $ch) {
        $results[$i] = curl_multi_getcontent($ch);
        curl_multi_remove_handle($mh, $ch);
        curl_close($ch);
    }
    
    curl_multi_close($mh);
    return $results;
}

// =============================================
// SINTA Category Scraper
// =============================================
function scrapeSintaCategory($base_url, $view, $label, $cookieFile, $conn = null, $table = null, $dosen_id = null) {
    $all_data = [];
    $page = 1;
    $max_pages = 100;
    $prev_hashes = [];

    // --- SMART SYNC ---
    $existing_normalized = [];
    if ($conn && $table && $dosen_id) {
        $res = $conn->query("SELECT judul FROM $table WHERE dosen_id = $dosen_id");
        if ($res) {
            while ($row = $res->fetch_assoc()) {
                $existing_normalized[normalizeTitle($row['judul'])] = true;
            }
        }
    }
    // ------------------

    while ($page <= $max_pages) {
        // Multi-Page Request (Batches of 2)
        $batch_urls = [];
        $batch_urls[] = "{$base_url}/?view={$view}&page={$page}";
        if ($page + 1 <= $max_pages) {
            $batch_urls[] = "{$base_url}/?view={$view}&page=" . ($page + 1);
        }

        sendLog("📄 {$label} halaman " . implode(" & ", array_map(function($p, $i) use ($page) { return $page + $i; }, $batch_urls, array_keys($batch_urls))) . "...");
        
        $html_results = fetchPagesParallel($batch_urls, $cookieFile);
        
        $batch_stop = false;
        foreach ($html_results as $idx => $html) {
            if (!$html) {
                $batch_stop = true;
                break;
            }

            $items = parseSintaItems($html);
            if (empty($items)) {
                $batch_stop = true;
                break;
            }

            // Detect duplicate page (SINTA guest limitation)
            $page_hash = md5(serialize($items));
            if (in_array($page_hash, $prev_hashes)) {
                $batch_stop = true;
                break;
            }
            $prev_hashes[] = $page_hash;

            // Smart Sync Check
            $new_count = 0;
            $old_count = 0;
            if (!empty($existing_normalized)) {
                foreach ($items as $item) {
                    if (isset($existing_normalized[normalizeTitle($item['judul'])])) {
                        $old_count++;
                    } else {
                        $new_count++;
                    }
                }
                
                // Jika ditemukan item lama, kemungkinan kita sudah mencapai data baru terakhir.
                // Menggunakan threshold minimal 1 item lama untuk memicu pengecekan lebih lanjut,
                // tapi threshold 50% tetap aman untuk menghentikan loop.
                if (count($items) > 0 && ($old_count > (count($items) / 2))) {
                    sendLog("⚡ Smart Sync: Ditemukan $old_count data lama & $new_count data baru. Menghentikan scraping {$label}.");
                    $batch_stop = true;
                } else {
                    if ($old_count > 0) {
                        sendLog("🔍 Smart Sync: Halaman ini memiliki $old_count data lama & $new_count data baru.");
                    } else {
                        sendLog("🔍 Smart Sync: Halaman ini semua data baru ($new_count item).");
                    }
                }
            }

            $all_data = array_merge($all_data, $items);
            sendLog("📋 {$label} halaman " . ($page + $idx) . ": " . count($items) . " item (" . count($all_data) . " total)");
            
            if (count($items) < 10 || $batch_stop) {
                $batch_stop = true;
                break;
            }
        }

        if ($batch_stop) break;

        $page += 2;
        usleep(rand(500000, 1500000)); // Random sleep 0.5s - 1.5s
    }

    return $all_data;
}

function parseSintaItems($html) {
    $data = [];
    $dom = new DOMDocument();
    libxml_use_internal_errors(true);
    @$dom->loadHTML(mb_convert_encoding($html, 'HTML-ENTITIES', 'UTF-8'));
    libxml_clear_errors();
    $xpath = new DOMXPath($dom);

    $items = $xpath->query("//div[contains(@class,'ar-list-item')]");
    if ($items->length == 0) $items = $xpath->query("//div[contains(@class,'ar-item')]");

    foreach ($items as $item) {
        $entry = [
            'judul' => '', 'tahun' => '', 'url' => '', 'penulis' => '', 
            'penerbit' => '', 'isbn' => '', 'pemegang' => '', 
            'kategori' => '', 'nomor' => '', 'nama_jurnal' => '', 'quartile' => ''
        ];

        $titleNode = $xpath->query(".//div[contains(@class,'ar-title')]//a", $item);
        if ($titleNode->length > 0) {
            $entry['judul'] = trim($titleNode->item(0)->textContent);
            $href = $titleNode->item(0)->getAttribute('href');
            if ($href && $href !== '#!' && $href !== '#') $entry['url'] = $href;
        } else {
            $allLinks = $xpath->query(".//a", $item);
            foreach ($allLinks as $link) {
                $text = trim($link->textContent);
                $href = $link->getAttribute('href');
                if (!empty($text) && $href !== '#!' && $href !== '#' && strlen($text) > 10) {
                    $entry['judul'] = $text;
                    if ($href) $entry['url'] = $href;
                    break;
                }
            }
        }

        $metaNodes = $xpath->query(".//div[contains(@class,'ar-meta')]//a", $item);
        foreach ($metaNodes as $meta) {
            $class = $meta->getAttribute('class');
            $text = trim($meta->textContent);

            if (strpos($class, 'ar-pub') !== false) {
                // Buku uses this as Penerbit. Garuda/Scopus use this as Nama Jurnal.
                $entry['penerbit'] = $text;
                $entry['nama_jurnal'] = $text;
            } elseif (stripos($text, 'ISBN') !== false) {
                $entry['isbn'] = trim(str_ireplace(['ISBN :', 'ISBN'], '', $text));
            } elseif (strpos($class, 'ar-quartile') !== false) {
                // If it's Hak Cipta or Paten (HKI)
                if (stripos($text, 'Karya Rakaman Video') !== false || stripos($text, 'Hak Cipta') !== false || stripos($text, 'Paten') !== false) {
                    $entry['kategori'] = $text;
                } else {
                    // It's a Scopus quartile (e.g. Q4 as Journal)
                    $entry['quartile'] = $text;
                }
            } elseif (strpos($class, 'ar-cited') !== false) {
                if (stripos($text, 'Nomor Permohonan') !== false) {
                    $entry['nomor'] = trim(str_ireplace(['Nomor Permohonan :', 'Nomor Permohonan'], '', $text));
                }
            } elseif (empty($class) && strpos($text, 'Category') === false && strpos($text, 'Status :') === false && strpos($text, 'Author Order') === false && strpos($text, 'DOI:') === false) {
                if (stripos($text, 'Inventor :') !== false) {
                    $entry['pemegang'] = trim(str_ireplace(['Inventor :', 'Inventor'], '', $text));
                } elseif (stripos($text, 'Creator :') !== false) {
                    $entry['penulis'] = trim(str_ireplace(['Creator :', 'Creator'], '', $text));
                } elseif (strpos($text, ';') !== false) { // Heuristic: authors list in Garuda often contains semicolon
                    $entry['penulis'] = $text;
                } elseif (empty($entry['penulis']) && empty($entry['pemegang']) && strlen($text) > 5) {
                    // This might be the affiliation OR author if no other found.
                    // We'll store it as author for now as fallback.
                    $entry['penulis'] = $text;
                }
            }
        }

        $yearNode = $xpath->query(".//a[contains(@class,'ar-year')]", $item);
        if ($yearNode->length > 0) {
            $entry['tahun'] = trim($yearNode->item(0)->textContent);
        } else {
            if (preg_match('/\b(19|20)\d{2}\b/', $item->textContent, $m)) $entry['tahun'] = $m[0];
        }

        if (!empty($entry['judul'])) $data[] = $entry;
    }
    return $data;
}

// =============================================
// Batch INSERT helpers
// =============================================
function batchInsert($conn, $table, $dosen_id, $items) {
    if (empty($items)) return ['total' => 0, 'new' => 0, 'in_db' => 0];

    // Ambil daftar judul yang sudah ada di database (Hash Map Optimization)
    $existingTitles = [];
    $res = $conn->query("SELECT judul FROM $table WHERE dosen_id = $dosen_id");
    if ($res) {
        while ($row = $res->fetch_assoc()) {
            $t = trim($row['judul']);
            $existingTitles[strtolower($t)] = true;
            $existingTitles['norm_' . normalizeTitle($t)] = true;
        }
    }

    $affected_total = 0;
    $inserted_items = []; // Menyimpan item yang siap dimasukkan (non-duplikat)

    // Filter dengan Fuzzy String Matching + Hash Lookup Optimization
    foreach ($items as $item) {
        $lowTitle = strtolower(trim($item['judul']));
        $normTitle = normalizeTitle($item['judul']);
        $isDuplicate = false;

        // 1. Cek Exact Match / Normalized Match menggunakan Hash Map (O(1))
        if (isset($existingTitles[$lowTitle]) || isset($existingTitles['norm_' . $normTitle])) {
            $isDuplicate = true;
        } else {
            // 2. Cek Fuzzy Match (O(N)) - Hanya jika exact match gagal
            $newLen = strlen($lowTitle);
            foreach ($existingTitles as $existTitle => $val) {
                if (strpos($existTitle, 'norm_') === 0) continue; // Lewati entry normalisasi
                
                // Optimasi: Hanya jalankan similar_text jika panjang string mirip (selisih max 20%)
                $existLen = strlen($existTitle);
                if (abs($newLen - $existLen) > ($newLen * 0.2)) continue;

                similar_text($lowTitle, $existTitle, $percent);
                if ($percent >= 85) { // Threshold 85% kemiripan
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
        $result = $conn->query("SELECT COUNT(*) as cnt FROM $table WHERE dosen_id = $dosen_id");
        return ['total' => count($items), 'new' => 0, 'in_db' => $result ? $result->fetch_assoc()['cnt'] : 0];
    }

    $chunks = array_chunk($inserted_items, 50);

    foreach ($chunks as $chunk) {
        $values = [];
        foreach ($chunk as $item) {
            $judul      = $conn->real_escape_string($item['judul']);
            $judul_hash = md5($item['judul']);
            $tahun      = $conn->real_escape_string($item['tahun']);
            $url        = $conn->real_escape_string($item['url']);
            
            if ($table === 'buku') {
                $penulis = isset($item['penulis']) ? $conn->real_escape_string($item['penulis']) : '';
                $penerbit = isset($item['penerbit']) ? $conn->real_escape_string($item['penerbit']) : '';
                $isbn = isset($item['isbn']) ? $conn->real_escape_string($item['isbn']) : '';
                $values[] = "($dosen_id, '$judul', '$judul_hash', '$penulis', '$penerbit', '$isbn', '$tahun', '$url')";
            } elseif ($table === 'publikasi_garuda') {
                $penulis = isset($item['penulis']) ? $conn->real_escape_string($item['penulis']) : '';
                $nama_jurnal = isset($item['nama_jurnal']) ? $conn->real_escape_string($item['nama_jurnal']) : '';
                $values[] = "($dosen_id, '$judul', '$judul_hash', '$penulis', '$nama_jurnal', '$tahun', '$url')";
            } elseif ($table === 'publikasi_scopus') {
                $penulis = isset($item['penulis']) ? $conn->real_escape_string($item['penulis']) : '';
                $nama_jurnal = isset($item['nama_jurnal']) ? $conn->real_escape_string($item['nama_jurnal']) : '';
                $quartile = isset($item['quartile']) ? $conn->real_escape_string(substr(trim($item['quartile']), 0, 10)) : '';
                $values[] = "($dosen_id, '$judul', '$judul_hash', '$penulis', '$nama_jurnal', '$tahun', '$quartile', '$url')";
            } elseif ($table === 'hki') {
                $pemegang = isset($item['pemegang']) ? $conn->real_escape_string($item['pemegang']) : '';
                // Fallback to penulis if pemegang is empty (in case it wasn't prefixed with Inventor :)
                if (empty($pemegang)) $pemegang = isset($item['penulis']) ? $conn->real_escape_string($item['penulis']) : '';
                $kategori = isset($item['kategori']) ? $conn->real_escape_string($item['kategori']) : '';
                $nomor = isset($item['nomor']) ? $conn->real_escape_string($item['nomor']) : '';
                $values[] = "($dosen_id, '$judul', '$judul_hash', '$pemegang', '$kategori', '$tahun', '$nomor', '$url')";
            } else {
                $values[]   = "($dosen_id, '$judul', '$judul_hash', '$tahun', '$url')";
            }
        }

        if ($table === 'buku') {
            $sql = "INSERT INTO $table (dosen_id, judul, judul_hash, penulis, penerbit, isbn, tahun, url) VALUES "
                 . implode(',', $values)
                 . " ON DUPLICATE KEY UPDATE penulis=VALUES(penulis), penerbit=VALUES(penerbit), isbn=VALUES(isbn), tahun=VALUES(tahun), url=VALUES(url), updated_at=NOW()";
        } elseif ($table === 'publikasi_garuda') {
            $sql = "INSERT INTO $table (dosen_id, judul, judul_hash, penulis, nama_jurnal, tahun, url) VALUES "
                 . implode(',', $values)
                 . " ON DUPLICATE KEY UPDATE penulis=VALUES(penulis), nama_jurnal=VALUES(nama_jurnal), tahun=VALUES(tahun), url=VALUES(url), updated_at=NOW()";
        } elseif ($table === 'publikasi_scopus') {
            $sql = "INSERT INTO $table (dosen_id, judul, judul_hash, penulis, nama_jurnal, tahun, quartile, url) VALUES "
                 . implode(',', $values)
                 . " ON DUPLICATE KEY UPDATE penulis=VALUES(penulis), nama_jurnal=VALUES(nama_jurnal), quartile=VALUES(quartile), tahun=VALUES(tahun), url=VALUES(url), updated_at=NOW()";
        } elseif ($table === 'hki') {
            $sql = "INSERT INTO $table (dosen_id, judul, judul_hash, pemegang, kategori, tahun, nomor, url) VALUES "
                 . implode(',', $values)
                 . " ON DUPLICATE KEY UPDATE pemegang=VALUES(pemegang), kategori=VALUES(kategori), nomor=VALUES(nomor), tahun=VALUES(tahun), url=VALUES(url), updated_at=NOW()";
        } else {
            $sql = "INSERT INTO $table (dosen_id, judul, judul_hash, tahun, url) VALUES "
                 . implode(',', $values)
                 . " ON DUPLICATE KEY UPDATE tahun=VALUES(tahun), url=VALUES(url), updated_at=NOW()";
        }

        $conn->query($sql);
        $affected_total += max(0, $conn->affected_rows);
    }

    $result = $conn->query("SELECT COUNT(*) as cnt FROM $table WHERE dosen_id = $dosen_id");
    $total_in_db = $result->fetch_assoc()['cnt'];

    return ['total' => count($items), 'new' => $affected_total, 'in_db' => $total_in_db];
}

// =============================================
// SINTA Profil Parser
// =============================================
function parseAuthorProfile($html) {
    $dom = new DOMDocument();
    libxml_use_internal_errors(true);
    @$dom->loadHTML(mb_convert_encoding($html, 'HTML-ENTITIES', 'UTF-8'));
    libxml_clear_errors();
    $xpath = new DOMXPath($dom);

    $nama = '';
    $afiliasi = '';
    $nameNode = $xpath->query("//div[contains(@class,'author-name')]//a");
    if ($nameNode->length > 0) $nama = trim($nameNode->item(0)->textContent);
    $affilNode = $xpath->query("//div[contains(@class,'author-affil')]//a");
    if ($affilNode->length > 0) $afiliasi = trim($affilNode->item(0)->textContent);
    if (empty($nama)) {
        $h3 = $xpath->query("//h3");
        if ($h3->length > 0) $nama = trim($h3->item(0)->textContent);
    }
    return ['nama' => $nama, 'afiliasi' => $afiliasi];
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

// Extract SINTA Author ID
$sinta_url = trim($dosen['sinta_url']);
$sinta_id  = trim($dosen['sinta_id']);
$author_id = '';

if (!empty($sinta_url)) {
    if (preg_match('/\/authors\/profile\/(\d+)/', $sinta_url, $m)) $author_id = $m[1];
    elseif (preg_match('/id=(\d+)/', $sinta_url, $m)) $author_id = $m[1];
    elseif (preg_match('/\/authors\/(\d+)/', $sinta_url, $m)) $author_id = $m[1];
}
if (empty($author_id) && !empty($sinta_id)) $author_id = $sinta_id;

$has_sinta = !empty($author_id);

if (!$has_sinta) {
    sendDone([], ['Tidak ada URL SINTA yang bisa di-scrape.']);
    exit;
}

if ($has_sinta && empty($dosen['sinta_id'])) {
    $conn->query("UPDATE dosen SET sinta_id = '" . $conn->real_escape_string($author_id) . "' WHERE id = $id");
}

$results = [];
$errors  = [];
$total_steps = 6; // login + profil + scopus + garuda + hki + buku
$main_start_time = microtime(true);

// ============ STEP 1: LOGIN SINTA ============
$cookieFile = $sinta_cookie_file;

if ($has_sinta) {
    sendProgress(1, $total_steps, 'Login ke SINTA...');
    if (!empty($sinta_username) && !empty($sinta_password)) {
        $loginOk = loginSinta($sinta_username, $sinta_password, $cookieFile);
        if ($loginOk) {
            sendLog('✅ Login SINTA berhasil!');
        } else {
            sendLog('⚠️ Login SINTA gagal. Scraping SINTA terbatas 10 data per kategori.');
        }
    } else {
        sendLog('⚠️ Kredensial SINTA belum diisi. Scraping terbatas.');
    }
}

// ============ STEP 2: PROFIL SINTA ============
$base_sinta = "https://sinta.kemdiktisaintek.go.id/authors/profile/$author_id";

sendProgress(2, $total_steps, 'Mengambil profil SINTA...');
$profile_html = fetchPage($base_sinta, $cookieFile);
if ($profile_html) {
    $profile = parseAuthorProfile($profile_html);
    $results['profil'] = "Nama: {$profile['nama']}, Afiliasi: {$profile['afiliasi']}";
    sendLog("👤 Profil: {$profile['nama']} - {$profile['afiliasi']}");
} else {
    $errors[] = "Gagal mengakses profil SINTA.";
}

// ============ STEP 3: SCOPUS ============
sendProgress(3, $total_steps, 'Scraping Scopus...');
$scopus_data = scrapeSintaCategory($base_sinta, 'scopus', 'Scopus', $cookieFile, $conn, 'publikasi_scopus', $id);
if ($scopus_data !== false) {
    $results['scopus'] = batchInsert($conn, 'publikasi_scopus', $id, $scopus_data);
    sendLog('✅ Scopus: ' . count($scopus_data) . ' publikasi disimpan');
} else {
    $errors[] = "Gagal mengakses Scopus.";
}

// ============ STEP 4: GARUDA ============
sendProgress(4, $total_steps, 'Scraping Garuda...');
$garuda_data = scrapeSintaCategory($base_sinta, 'garuda', 'Garuda', $cookieFile, $conn, 'publikasi_garuda', $id);
if ($garuda_data !== false) {
    $results['garuda'] = batchInsert($conn, 'publikasi_garuda', $id, $garuda_data);
    sendLog('✅ Garuda: ' . count($garuda_data) . ' publikasi disimpan');
} else {
    $errors[] = "Gagal mengakses Garuda.";
}

// ============ STEP 5: HKI ============
sendProgress(5, $total_steps, 'Scraping HKI...');
$hki_data = scrapeSintaCategory($base_sinta, 'iprs', 'HKI', $cookieFile, $conn, 'hki', $id);
if ($hki_data !== false) {
    $results['hki'] = batchInsert($conn, 'hki', $id, $hki_data);
    sendLog('✅ HKI: ' . count($hki_data) . ' data disimpan');
} else {
    $errors[] = "Gagal mengakses HKI.";
}

// ============ STEP 6: BUKU ============
sendProgress(6, $total_steps, 'Scraping Buku...');
$buku_data = scrapeSintaCategory($base_sinta, 'books', 'Buku', $cookieFile, $conn, 'buku', $id);
if ($buku_data !== false) {
    $results['buku'] = batchInsert($conn, 'buku', $id, $buku_data);
    sendLog('✅ Buku: ' . count($buku_data) . ' data disimpan');
} else {
    $errors[] = "Gagal mengakses Buku.";
}

// Update last_scraped
$conn->query("UPDATE dosen SET last_scraped = NOW() WHERE id = $id");

$total_duration = round(microtime(true) - $main_start_time, 2);
sendLog("⏱️ Total durasi scraping: $total_duration detik.");

sendDone($results, $errors);
