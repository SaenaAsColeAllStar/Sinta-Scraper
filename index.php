<?php
// =============================================
// Dashboard - Sistem Monitoring Publikasi
// =============================================
$page_title = 'Dashboard Analitik';
require_once __DIR__ . '/layout/header.php';

// Statistik Utama
$total_dosen  = $conn->query("SELECT COUNT(*) as total FROM dosen")->fetch_assoc()['total'];
$total_gs     = $conn->query("SELECT COUNT(*) as total FROM publikasi_gs")->fetch_assoc()['total'];
$total_scopus = $conn->query("SELECT COUNT(*) as total FROM publikasi_scopus")->fetch_assoc()['total'];
$total_garuda = $conn->query("SELECT COUNT(*) as total FROM publikasi_garuda")->fetch_assoc()['total'];
$total_hki    = $conn->query("SELECT COUNT(*) as total FROM hki")->fetch_assoc()['total'];
$total_buku   = $conn->query("SELECT COUNT(*) as total FROM buku")->fetch_assoc()['total'];

// Data untuk Grafik Distribusi Kategori (Pie Chart)
$dist_labels = ['Google Scholar', 'Scopus', 'Garuda', 'HKI', 'Buku'];
$dist_data   = [$total_gs, $total_scopus, $total_garuda, $total_hki, $total_buku];

// Data untuk Grafik Tren 5 Tahun Terakhir (Line Chart)
$trend_years = [];
$trend_gs = [];
$trend_scopus = [];
$trend_garuda = [];

$current_year = (int)date('Y');
for ($i = $current_year - 4; $i <= $current_year; $i++) {
    $trend_years[] = $i;
    $trend_gs[$i] = 0;
    $trend_scopus[$i] = 0;
    $trend_garuda[$i] = 0;
}

// Fetch tren GS
$res_gs = $conn->query("SELECT tahun, COUNT(*) as cnt FROM publikasi_gs WHERE tahun >= " . ($current_year - 4) . " GROUP BY tahun");
while ($r = $res_gs->fetch_assoc()) { if (isset($trend_gs[$r['tahun']])) $trend_gs[$r['tahun']] = $r['cnt']; }

// Fetch tren Scopus
$res_scopus = $conn->query("SELECT tahun, COUNT(*) as cnt FROM publikasi_scopus WHERE tahun >= " . ($current_year - 4) . " GROUP BY tahun");
while ($r = $res_scopus->fetch_assoc()) { if (isset($trend_scopus[$r['tahun']])) $trend_scopus[$r['tahun']] = $r['cnt']; }

// Fetch tren Garuda
$res_garuda = $conn->query("SELECT tahun, COUNT(*) as cnt FROM publikasi_garuda WHERE tahun >= " . ($current_year - 4) . " GROUP BY tahun");
while ($r = $res_garuda->fetch_assoc()) { if (isset($trend_garuda[$r['tahun']])) $trend_garuda[$r['tahun']] = $r['cnt']; }

$year_str        = "'" . implode("','", $trend_years) . "'";
$gs_data_str     = implode(',', array_values($trend_gs));
$scopus_data_str = implode(',', array_values($trend_scopus));
$garuda_data_str = implode(',', array_values($trend_garuda));

// Leaderboard Top Dosen
$leaderboard = $conn->query("
    SELECT d.id, d.nama, d.nidn, d.h_index_gs, d.h_index_scopus,
           IFNULL((SELECT SUM(sitasi) FROM publikasi_gs WHERE dosen_id = d.id), 0) as total_sitasi,
           (SELECT COUNT(*) FROM publikasi_gs WHERE dosen_id = d.id) + 
           (SELECT COUNT(*) FROM publikasi_scopus WHERE dosen_id = d.id) + 
           (SELECT COUNT(*) FROM publikasi_garuda WHERE dosen_id = d.id) as total_publikasi
    FROM dosen d
    ORDER BY total_sitasi DESC, h_index_gs DESC, total_publikasi DESC
    LIMIT 5
");

// Dosen terbaru
$recent_dosen = $conn->query("SELECT * FROM dosen ORDER BY created_at DESC LIMIT 5");
?>

<!-- Stat Cards -->
<div class="row g-3 mb-4">
    <div class="col-xl-2 col-lg-4 col-md-4 col-sm-6">
        <div class="stat-card primary">
            <div class="d-flex align-items-center justify-content-between">
                <div>
                    <div class="stat-label">Total Dosen</div>
                    <div class="stat-value"><?= $total_dosen ?></div>
                </div>
                <div class="stat-icon"><i class="bi bi-people-fill"></i></div>
            </div>
        </div>
    </div>
    <div class="col-xl-2 col-lg-4 col-md-4 col-sm-6">
        <div class="stat-card success">
            <div class="d-flex align-items-center justify-content-between">
                <div>
                    <div class="stat-label">Google Scholar</div>
                    <div class="stat-value"><?= $total_gs ?></div>
                </div>
                <div class="stat-icon"><i class="bi bi-journal-richtext"></i></div>
            </div>
        </div>
    </div>
    <div class="col-xl-2 col-lg-4 col-md-4 col-sm-6">
        <div class="stat-card warning">
            <div class="d-flex align-items-center justify-content-between">
                <div>
                    <div class="stat-label">Scopus</div>
                    <div class="stat-value"><?= $total_scopus ?></div>
                </div>
                <div class="stat-icon"><i class="bi bi-journal-bookmark-fill"></i></div>
            </div>
        </div>
    </div>
    <div class="col-xl-2 col-lg-4 col-md-4 col-sm-6">
        <div class="stat-card danger">
            <div class="d-flex align-items-center justify-content-between">
                <div>
                    <div class="stat-label">Garuda</div>
                    <div class="stat-value"><?= $total_garuda ?></div>
                </div>
                <div class="stat-icon"><i class="bi bi-journal-medical"></i></div>
            </div>
        </div>
    </div>
    <div class="col-xl-2 col-lg-4 col-md-4 col-sm-6">
        <div class="stat-card info">
            <div class="d-flex align-items-center justify-content-between">
                <div>
                    <div class="stat-label">HKI</div>
                    <div class="stat-value"><?= $total_hki ?></div>
                </div>
                <div class="stat-icon"><i class="bi bi-shield-check"></i></div>
            </div>
        </div>
    </div>
    <div class="col-xl-2 col-lg-4 col-md-4 col-sm-6">
        <div class="stat-card secondary">
            <div class="d-flex align-items-center justify-content-between">
                <div>
                    <div class="stat-label">Buku</div>
                    <div class="stat-value"><?= $total_buku ?></div>
                </div>
                <div class="stat-icon"><i class="bi bi-book-fill"></i></div>
            </div>
        </div>
    </div>
</div>

<!-- Charts Row -->
<div class="row g-4 mb-4">
    <!-- Tren Chart -->
    <div class="col-lg-8">
        <div class="card-custom h-100">
            <div class="card-header-custom">
                <h5><i class="bi bi-graph-up-arrow me-2 text-primary"></i>Tren Publikasi (5 Tahun Terakhir)</h5>
            </div>
            <div class="card-body-custom">
                <canvas id="trendChart" style="max-height: 300px; display: block; box-sizing: border-box;"></canvas>
            </div>
        </div>
    </div>
    <!-- Distribusi Chart -->
    <div class="col-lg-4">
        <div class="card-custom h-100">
            <div class="card-header-custom">
                <h5><i class="bi bi-pie-chart-fill me-2 text-danger"></i>Distribusi Kategori</h5>
            </div>
            <div class="card-body-custom d-flex justify-content-center align-items-center">
                <canvas id="distChart" style="max-height: 250px; display: block; box-sizing: border-box;"></canvas>
            </div>
        </div>
    </div>
</div>

<!-- Row: Leaderboard & Recent Dosen -->
<div class="row g-4">
    <!-- Top Dosen Leaderboard -->
    <div class="col-lg-7">
        <div class="card-custom h-100">
            <div class="card-header-custom">
                <h5><i class="bi bi-trophy-fill me-2 text-warning"></i>Leaderboard Top Dosen</h5>
            </div>
            <div class="card-body-custom p-0">
                <?php if ($leaderboard->num_rows > 0): ?>
                <table class="table-custom">
                    <thead>
                        <tr>
                            <th>Rank</th>
                            <th>Dosen</th>
                            <th>H-Index</th>
                            <th>Sitasi</th>
                            <th>Total Pub</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php $rank = 1; while ($t = $leaderboard->fetch_assoc()): ?>
                        <tr>
                            <td>
                                <?php if ($rank == 1): ?>
                                    <span class="badge-custom badge-warning px-3 py-2 fs-6"><i class="bi bi-award-fill me-1"></i>1</span>
                                <?php elseif ($rank == 2): ?>
                                    <span class="badge-custom badge-secondary px-3 py-2 fs-6"><i class="bi bi-award-fill me-1"></i>2</span>
                                <?php elseif ($rank == 3): ?>
                                    <span class="badge-custom badge-danger px-3 py-2 fs-6"><i class="bi bi-award-fill me-1"></i>3</span>
                                <?php else: ?>
                                    <span class="px-3 py-2 fw-semibold text-muted"><?= $rank ?></span>
                                <?php endif; ?>
                            </td>
                            <td>
                                <div class="d-flex align-items-center gap-2">
                                    <div class="dosen-avatar"><?= strtoupper(substr($t['nama'], 0, 1)) ?></div>
                                    <div>
                                        <div class="fw-semibold text-dark"><?= htmlspecialchars($t['nama']) ?></div>
                                        <small class="text-muted d-block" style="font-size: 11px;"><?= $t['nidn'] ?></small>
                                    </div>
                                </div>
                            </td>
                            <td>
                                <span class="badge-custom bg-info text-white" title="H-Index Scholar"><?= $t['h_index_gs'] ?: 0 ?> <i class="bi bi-google"></i></span>
                            </td>
                            <td>
                                <span class="fw-bold text-success fs-6"><i class="bi bi-quote"></i> <?= number_format($t['total_sitasi']) ?></span>
                            </td>
                            <td>
                                <span class="badge-custom badge-primary"><i class="bi bi-journal"></i> <?= number_format($t['total_publikasi']) ?></span>
                            </td>
                        </tr>
                        <?php $rank++; endwhile; ?>
                    </tbody>
                </table>
                <?php else: ?>
                <div class="empty-state">
                    <i class="bi bi-trophy"></i>
                    <h5>Belum Ada Data Dosen</h5>
                    <p>Lakukan scraping ke setidaknya satu dosen terlebih dahulu.</p>
                </div>
                <?php endif; ?>
            </div>
        </div>
    </div>

    <!-- Recent Dosen -->
    <div class="col-lg-5">
        <div class="card-custom h-100">
            <div class="card-header-custom">
                <h5><i class="bi bi-clock-history me-2 text-primary"></i>Dosen Terbaru</h5>
                <a href="<?= $base_url ?>/dosen/dosen.php" class="btn btn-primary-custom btn-sm-custom">
                    Semua <i class="bi bi-arrow-right"></i>
                </a>
            </div>
            <div class="card-body-custom p-0">
                <?php if ($recent_dosen->num_rows > 0): ?>
                <table class="table-custom">
                    <thead>
                        <tr>
                            <th>Dosen</th>
                            <th>Prodi</th>
                            <th>Aksi</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php while ($d = $recent_dosen->fetch_assoc()): ?>
                        <tr>
                            <td>
                                <div class="d-flex align-items-center gap-2">
                                    <div class="dosen-avatar" style="width: 32px; height: 32px; font-size: 14px;"><?= strtoupper(substr($d['nama'], 0, 1)) ?></div>
                                    <div class="fw-semibold" style="font-size: 13px;"><?= htmlspecialchars($d['nama']) ?></div>
                                </div>
                            </td>
                            <td style="font-size: 13px;"><?= htmlspecialchars($d['prodi']) ?></td>
                            <td>
                                <a href="<?= $base_url ?>/scraping/scrape_sinta.php?id=<?= $d['id'] ?>" class="btn btn-info-custom btn-sm-custom px-2 py-1" data-bs-toggle="tooltip" title="Scrape SINTA">
                                    <i class="bi bi-arrow-repeat" style="font-size: 12px;"></i>
                                </a>
                                <a href="<?= $base_url ?>/scraping/scrape_scholar.php?id=<?= $d['id'] ?>" class="btn btn-success-custom btn-sm-custom px-2 py-1" data-bs-toggle="tooltip" title="Scrape Scholar">
                                    <i class="bi bi-google" style="font-size: 12px;"></i>
                                </a>
                            </td>
                        </tr>
                        <?php endwhile; ?>
                    </tbody>
                </table>
                <?php else: ?>
                <div class="empty-state py-4">
                    <i class="bi bi-people" style="font-size: 2rem;"></i>
                    <p class="mb-2 mt-2">Belum ada data</p>
                    <a href="<?= $base_url ?>/dosen/tambah_dosen.php" class="btn btn-primary-custom btn-sm-custom">
                        <i class="bi bi-plus-lg"></i> Tambah
                    </a>
                </div>
                <?php endif; ?>
            </div>
        </div>
    </div>
</div>

<?php 
// Tambahkan script Chart.js di bagian bawah melalui $extra_js
$extra_js = "
<script src='https://cdn.jsdelivr.net/npm/chart.js'></script>
<script>
document.addEventListener('DOMContentLoaded', function() {
    // 1. Line Chart: Tren Publikasi
    const trendCtx = document.getElementById('trendChart').getContext('2d');
    const trendChart = new Chart(trendCtx, {
        type: 'line',
        data: {
            labels: [$year_str],
            datasets: [
                {
                    label: 'Google Scholar',
                    data: [$gs_data_str],
                    borderColor: '#2ed573',
                    backgroundColor: 'rgba(46, 213, 115, 0.1)',
                    borderWidth: 2,
                    tension: 0.3,
                    fill: true
                },
                {
                    label: 'Scopus',
                    data: [$scopus_data_str],
                    borderColor: '#ffa502',
                    backgroundColor: 'rgba(255, 165, 2, 0.1)',
                    borderWidth: 2,
                    tension: 0.3,
                    fill: true
                },
                {
                    label: 'Garuda',
                    data: [$garuda_data_str],
                    borderColor: '#ff4757',
                    backgroundColor: 'rgba(255, 71, 87, 0.1)',
                    borderWidth: 2,
                    tension: 0.3,
                    fill: true
                }
            ]
        },
        options: {
            responsive: true,
            maintainAspectRatio: false,
            plugins: {
                legend: { position: 'top' },
                tooltip: { mode: 'index', intersect: false }
            },
            scales: {
                y: { beginAtZero: true, ticks: { precision: 0 } }
            }
        }
    });

    // 2. Pie Chart: Distribusi Kategori
    const distCtx = document.getElementById('distChart').getContext('2d');
    const distChart = new Chart(distCtx, {
        type: 'doughnut',
        data: {
            labels: ['Google Scholar', 'Scopus', 'Garuda', 'HKI', 'Buku'],
            datasets: [{
                data: [" . implode(',', $dist_data) . "],
                backgroundColor: [
                    '#2ed573', // success
                    '#ffa502', // warning
                    '#ff4757', // danger
                    '#1e90ff', // info
                    '#7bed9f'  // secondary/buku
                ],
                borderWidth: 1,
                hoverOffset: 4
            }]
        },
        options: {
            responsive: true,
            maintainAspectRatio: false,
            plugins: {
                legend: { position: 'bottom' }
            },
            cutout: '65%'
        }
    });
});
</script>
";

require_once __DIR__ . '/layout/footer.php'; 
?>
