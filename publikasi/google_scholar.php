<?php
// =============================================
// Publikasi Google Scholar - Redesigned
// =============================================
$page_title = 'Google Scholar';
require_once __DIR__ . '/../layout/header.php';

// Filter dosen
$dosen_id = isset($_GET['dosen_id']) ? (int)$_GET['dosen_id'] : 0;
$dosen_list = $conn->query("SELECT id, nama FROM dosen ORDER BY nama ASC");

// Get available years for filter
$years = $conn->query("SELECT DISTINCT tahun FROM publikasi_gs WHERE tahun != '' AND tahun IS NOT NULL ORDER BY tahun DESC");

// Stats
$stats_where = $dosen_id > 0 ? "WHERE dosen_id = $dosen_id" : "";
$total_pub = $conn->query("SELECT COUNT(*) as cnt FROM publikasi_gs $stats_where")->fetch_assoc()['cnt'];
$total_sitasi = $conn->query("SELECT COALESCE(SUM(sitasi),0) as cnt FROM publikasi_gs $stats_where")->fetch_assoc()['cnt'];

// h-index: jumlah paper h yang masing-masing dikutip minimal h kali
$h_index = 0;
$sitasi_list = $conn->query("SELECT sitasi FROM publikasi_gs $stats_where ORDER BY sitasi DESC");
$rank = 0;
while ($s = $sitasi_list->fetch_assoc()) {
    $rank++;
    if ($s['sitasi'] >= $rank) {
        $h_index = $rank;
    } else {
        break;
    }
}

// i10-index: jumlah paper dengan sitasi >= 10
$i10_index = $conn->query("SELECT COUNT(*) as cnt FROM publikasi_gs " . ($dosen_id > 0 ? "WHERE dosen_id = $dosen_id AND" : "WHERE") . " sitasi >= 10")->fetch_assoc()['cnt'];
?>

<!-- Stat Cards -->
<div class="row g-3 mb-4">
    <div class="col-md-3 col-6">
        <div class="stat-card primary">
            <div class="stat-label">Total Publikasi</div>
            <div class="stat-value" id="statTotal"><?= number_format($total_pub) ?></div>
            <small class="text-muted">artikel</small>
        </div>
    </div>
    <div class="col-md-3 col-6">
        <div class="stat-card success">
            <div class="stat-label">Total Sitasi</div>
            <div class="stat-value" id="statSitasi"><?= number_format($total_sitasi) ?></div>
            <small class="text-muted">kutipan</small>
        </div>
    </div>
    <div class="col-md-3 col-6">
        <div class="stat-card warning">
            <div class="stat-label">h-index</div>
            <div class="stat-value" id="statHIndex"><?= $h_index ?></div>
            <small class="text-muted">indeks</small>
        </div>
    </div>
    <div class="col-md-3 col-6">
        <div class="stat-card danger">
            <div class="stat-label">i10-index</div>
            <div class="stat-value" id="statI10"><?= $i10_index ?></div>
            <small class="text-muted">artikel ≥10 sitasi</small>
        </div>
    </div>
</div>

<div class="card-custom">
    <div class="card-header-custom">
        <h5><i class="bi bi-journal-richtext me-2"></i>Publikasi Google Scholar</h5>
        <div class="d-flex gap-2 flex-wrap align-items-center">
            <select id="filterDosen" class="form-select form-select-sm" style="width:200px;" onchange="loadData(1)">
                <option value="0">Semua Dosen</option>
                <?php while ($d = $dosen_list->fetch_assoc()): ?>
                <option value="<?= $d['id'] ?>" <?= $dosen_id == $d['id'] ? 'selected' : '' ?>><?= htmlspecialchars($d['nama']) ?></option>
                <?php endwhile; ?>
            </select>
            <select id="filterTahun" class="form-select form-select-sm" style="width:120px;" onchange="loadData(1)">
                <option value="">Semua Tahun</option>
                <?php while ($y = $years->fetch_assoc()): ?>
                <option value="<?= $y['tahun'] ?>"><?= $y['tahun'] ?></option>
                <?php endwhile; ?>
            </select>
            <select id="perPage" class="form-select form-select-sm" style="width:80px;" onchange="loadData(1)">
                <option value="10">10</option>
                <option value="20">20</option>
                <option value="50">50</option>
                <option value="100">100</option>
            </select>
            <a href="<?= $base_url ?>/export/export_excel.php?type=google_scholar&dosen_id=<?= $dosen_id ?>" class="btn btn-success-custom btn-sm-custom">
                <i class="bi bi-file-earmark-spreadsheet"></i> Export
            </a>
        </div>
    </div>
    <div class="card-body-custom p-0" id="dataContainer">
        <div class="text-center py-5">
            <div class="spinner-border text-primary" role="status"></div>
            <p class="text-muted mt-2">Memuat data...</p>
        </div>
    </div>
</div>

<?php
$extra_js = <<<JS
<script>
let currentPage = 1;

function loadData(page) {
    currentPage = page;
    const dosenId = $('#filterDosen').val();
    const tahun = $('#filterTahun').val();
    const perPage = $('#perPage').val();
    
    $('a[href*="export_excel"]').attr('href', '{$base_url}/export/export_excel.php?type=google_scholar&dosen_id=' + dosenId);
    
    loadPagination('{$base_url}/ajax/pagination_gs.php', 'dataContainer', page, perPage, dosenId, '&tahun=' + tahun);
}

$(document).ready(function() {
    loadData(1);
});
</script>
JS;

require_once __DIR__ . '/../layout/footer.php';
?>
