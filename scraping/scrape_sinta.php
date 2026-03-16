<?php
// =============================================
// Scraping SINTA - Halaman UI dengan Progress Indicator
// =============================================
$page_title = 'Scraping SINTA';
require_once __DIR__ . '/../config/koneksi.php';

$id = isset($_GET['id']) ? (int)$_GET['id'] : 0;
$dosen = $conn->query("SELECT * FROM dosen WHERE id = $id")->fetch_assoc();

if (!$dosen) {
    header("Location: {$base_url}/dosen/dosen.php?msg=" . urlencode("Dosen tidak ditemukan.") . "&type=danger");
    exit;
}

require_once __DIR__ . '/../layout/header.php';
?>

<div class="row g-4">
    <!-- Dosen Info Card -->
    <div class="col-lg-4">
        <div class="card-custom">
            <div class="card-header-custom">
                <h5><i class="bi bi-person-circle me-2"></i>Info Dosen</h5>
            </div>
            <div class="card-body-custom">
                <div class="text-center mb-3">
                    <div class="dosen-avatar mx-auto mb-2" style="width:70px;height:70px;font-size:28px;">
                        <?= strtoupper(substr($dosen['nama'], 0, 1)) ?>
                    </div>
                    <h6 class="fw-bold mb-1"><?= htmlspecialchars($dosen['nama']) ?></h6>
                    <span class="badge-custom badge-primary"><?= $dosen['nidn'] ?></span>
                    <p class="text-muted small mt-1"><?= htmlspecialchars($dosen['prodi']) ?></p>
                </div>

                <div class="mb-3">
                    <small class="text-muted d-block mb-1"><strong>SINTA ID:</strong> <?= $dosen['sinta_id'] ?: '-' ?></small>
                    <small class="text-muted d-block"><strong>Terakhir Scrape:</strong> <?= $dosen['last_scraped'] ? date('d M Y H:i', strtotime($dosen['last_scraped'])) : 'Belum pernah' ?></small>
                </div>

                <?php if (!empty($dosen['sinta_url'])): ?>
                <a href="<?= htmlspecialchars($dosen['sinta_url']) ?>" target="_blank" class="btn btn-primary-custom w-100 mb-2">
                    <i class="bi bi-box-arrow-up-right"></i> Lihat Profil SINTA
                </a>
                <?php endif; ?>

                <button type="button" id="btnStartScrape" class="btn btn-success-custom w-100"
                    <?= (empty($dosen['sinta_url']) && empty($dosen['sinta_id'])) ? 'disabled' : '' ?>
                    onclick="startScraping(<?= $id ?>)">
                    <i class="bi bi-arrow-repeat"></i> Mulai Scraping
                </button>

                <?php if (empty($dosen['sinta_url']) && empty($dosen['sinta_id'])): ?>
                <div class="alert alert-warning alert-custom mt-3 small">
                    <i class="bi bi-exclamation-triangle-fill"></i>
                    URL SINTA / SINTA ID belum diisi.
                    <a href="<?= $base_url ?>/dosen/edit_dosen.php?id=<?= $id ?>">Edit dosen</a> terlebih dahulu.
                </div>
                <?php endif; ?>
            </div>
        </div>
    </div>

    <!-- Scraping Results -->
    <div class="col-lg-8">
        <!-- Progress Section (hidden by default) -->
        <div id="progressSection" style="display:none;">
            <div class="card-custom mb-3">
                <div class="card-header-custom">
                    <h5><i class="bi bi-hourglass-split me-2"></i>Progress Scraping</h5>
                </div>
                <div class="card-body-custom">
                    <div class="mb-3">
                        <div class="d-flex justify-content-between mb-1">
                            <small id="progressLabel" class="fw-bold">Memulai scraping...</small>
                            <small id="progressPercent" class="fw-bold">0%</small>
                        </div>
                        <div class="progress" style="height: 20px; border-radius: 10px; background: rgba(0,0,0,0.1);">
                            <div id="progressBar" class="progress-bar progress-bar-striped progress-bar-animated" 
                                 role="progressbar" style="width: 0%; border-radius: 10px; background: linear-gradient(135deg, #667eea, #764ba2);">
                            </div>
                        </div>
                    </div>
                    <div id="progressLog" class="small text-muted" style="max-height:200px;overflow-y:auto;font-family:monospace;font-size:12px;">
                    </div>
                    <div id="timerDisplay" class="text-end mt-2">
                        <small class="text-muted"><i class="bi bi-stopwatch"></i> Waktu: <span id="elapsedTime">0</span> detik</small>
                    </div>
                </div>
            </div>
        </div>

        <!-- Error Section -->
        <div id="errorSection" style="display:none;">
            <div class="alert alert-danger alert-custom mb-3">
                <div>
                    <i class="bi bi-exclamation-triangle-fill me-2"></i><strong>Terjadi Kesalahan:</strong>
                    <ul id="errorList" class="mb-0 mt-1"></ul>
                </div>
            </div>
        </div>

        <!-- Results Section (hidden by default) -->
        <div id="resultsSection" style="display:none;">
            <div class="alert alert-success alert-custom mb-3">
                <i class="bi bi-check-circle-fill me-2"></i>
                <strong>Scraping selesai!</strong> <span id="resultSummary"></span>
            </div>

            <div id="profilInfo" class="alert alert-info alert-custom mb-3 small" style="display:none;">
                <i class="bi bi-person-badge me-2"></i>
                <strong>Profil SINTA:</strong> <span id="profilText"></span>
            </div>

            <div class="row g-3 mb-4" id="statsCards">

                <div class="col-md-6 col-6">
                    <div class="stat-card warning">
                        <div class="stat-label">Scopus</div>
                        <div class="stat-value" id="statScopus">0</div>
                        <small class="text-muted">publikasi</small>
                        <small class="d-block text-success" id="statScopusDetail"></small>
                    </div>
                </div>
                <div class="col-md-6 col-6">
                    <div class="stat-card danger">
                        <div class="stat-label">Garuda</div>
                        <div class="stat-value" id="statGaruda">0</div>
                        <small class="text-muted">publikasi</small>
                        <small class="d-block text-success" id="statGarudaDetail"></small>
                    </div>
                </div>
                <div class="col-md-6 col-6">
                    <div class="stat-card info">
                        <div class="stat-label">HKI</div>
                        <div class="stat-value" id="statHKI">0</div>
                        <small class="text-muted">data</small>
                        <small class="d-block text-success" id="statHKIDetail"></small>
                    </div>
                </div>
                <div class="col-md-6 col-6">
                    <div class="stat-card secondary">
                        <div class="stat-label">Buku</div>
                        <div class="stat-value" id="statBuku">0</div>
                        <small class="text-muted">data</small>
                        <small class="d-block text-success" id="statBukuDetail"></small>
                    </div>
                </div>
            </div>
        </div>

        <div class="card-custom">
            <div class="card-header-custom">
                <h5><i class="bi bi-info-circle me-2"></i>Panduan Scraping</h5>
            </div>
            <div class="card-body-custom">
                <div class="mb-3">
                    <h6 class="fw-bold">Cara Kerja:</h6>
                    <ol class="small text-muted">
                        <li>Pastikan <strong>URL SINTA</strong> atau <strong>SINTA ID</strong> sudah diisi di data dosen.</li>
                        <li>Format URL yang benar: <code>https://sinta.kemdiktisaintek.go.id/authors/profile/XXXXXXX</code></li>
                        <li>Klik tombol <strong>"Mulai Scraping"</strong> untuk mengambil data.</li>
                        <li>Sistem akan mengambil data: <strong>Scopus, Garuda, HKI, dan Buku</strong>.</li>
                        <li>Semua halaman pagination akan di-scrape otomatis.</li>
                        <li>Data duplikat <strong>tidak akan disimpan ulang</strong>, hanya data baru yang ditambahkan.</li>
                    </ol>
                </div>
                <div class="alert alert-info alert-custom small mb-0">
                    <i class="bi bi-lightbulb-fill me-2"></i>
                    <strong>Tips:</strong> Pastikan ekstensi <strong>cURL</strong> dan <strong>DOM</strong> aktif di PHP XAMPP.
                </div>
            </div>
        </div>
    </div>
</div>

<div class="mt-3">
    <a href="<?= $base_url ?>/dosen/dosen.php" class="btn btn-primary-custom">
        <i class="bi bi-arrow-left"></i> Kembali ke Daftar Dosen
    </a>
</div>

<script>
let scrapeTimer = null;
let startTime = null;

function startScraping(dosenId) {
    const btn = document.getElementById('btnStartScrape');
    btn.disabled = true;
    btn.innerHTML = '<span class="spinner-border spinner-border-sm me-1"></span> Scraping...';

    // Show progress, hide results
    document.getElementById('progressSection').style.display = 'block';
    document.getElementById('resultsSection').style.display = 'none';
    document.getElementById('errorSection').style.display = 'none';

    const progressBar = document.getElementById('progressBar');
    const progressLabel = document.getElementById('progressLabel');
    const progressPercent = document.getElementById('progressPercent');
    const progressLog = document.getElementById('progressLog');

    progressBar.style.width = '0%';
    progressLabel.textContent = 'Memulai scraping...';
    progressPercent.textContent = '0%';
    progressLog.innerHTML = '';

    // Start timer
    startTime = Date.now();
    scrapeTimer = setInterval(() => {
        const elapsed = ((Date.now() - startTime) / 1000).toFixed(1);
        document.getElementById('elapsedTime').textContent = elapsed;
    }, 100);

    // Use EventSource for SSE
    const evtSource = new EventSource('<?= $base_url ?>/ajax/scrape_progress.php?id=' + dosenId);

    evtSource.onmessage = function(event) {
        const data = JSON.parse(event.data);

        if (data.done) {
            // Scraping complete
            evtSource.close();
            clearInterval(scrapeTimer);

            const elapsed = ((Date.now() - startTime) / 1000).toFixed(1);
            document.getElementById('elapsedTime').textContent = elapsed;

            progressBar.style.width = '100%';
            progressBar.classList.remove('progress-bar-animated');
            progressBar.style.background = 'linear-gradient(135deg, #2ed573, #17a85e)';
            progressLabel.textContent = 'Scraping selesai!';
            progressPercent.textContent = '100%';

            addLog('✅ Selesai dalam ' + elapsed + ' detik');

            btn.disabled = false;
            btn.innerHTML = '<i class="bi bi-arrow-repeat"></i> Mulai Scraping';

            // Show results
            showResults(data.results, data.errors);

            // Reload page after 1 second to update last_scraped
            setTimeout(() => {
                document.querySelector('.text-muted:last-child').textContent = 'Baru saja';
            }, 500);
        } else {
            // Progress update
            const pct = data.percent + '%';
            progressBar.style.width = pct;
            progressLabel.textContent = data.message;
            progressPercent.textContent = pct;
            addLog('📋 ' + data.message);
        }
    };

    evtSource.onerror = function() {
        evtSource.close();
        clearInterval(scrapeTimer);
        btn.disabled = false;
        btn.innerHTML = '<i class="bi bi-arrow-repeat"></i> Mulai Scraping';
        addLog('❌ Koneksi terputus');
    };
}

function addLog(msg) {
    const log = document.getElementById('progressLog');
    const time = ((Date.now() - startTime) / 1000).toFixed(1);
    log.innerHTML += '<div>[' + time + 's] ' + msg + '</div>';
    log.scrollTop = log.scrollHeight;
}

function showResults(results, errors) {
    // Show errors
    if (errors && errors.length > 0) {
        document.getElementById('errorSection').style.display = 'block';
        const errorList = document.getElementById('errorList');
        errorList.innerHTML = '';
        errors.forEach(err => {
            errorList.innerHTML += '<li>' + err + '</li>';
        });
    }

    document.getElementById('resultsSection').style.display = 'block';

    const elapsed = ((Date.now() - startTime) / 1000).toFixed(1);
    document.getElementById('resultSummary').textContent = 'Selesai dalam ' + elapsed + ' detik.';

    // Profil
    if (results.profil) {
        document.getElementById('profilInfo').style.display = 'block';
        document.getElementById('profilText').textContent = results.profil;
    }

    // Stats
    updateStat('Scopus', results.scopus);
    updateStat('Garuda', results.garuda);
    updateStat('HKI', results.hki);
    updateStat('Buku', results.buku);
}

function updateStat(key, data) {
    const valEl = document.getElementById('stat' + key);
    const detailEl = document.getElementById('stat' + key + 'Detail');
    if (data) {
        valEl.textContent = data.total;
        detailEl.textContent = 'Total di DB: ' + (data.in_db || data.total);
    } else {
        valEl.textContent = '0';
        detailEl.textContent = '';
    }
}
</script>

<?php require_once __DIR__ . '/../layout/footer.php'; ?>
