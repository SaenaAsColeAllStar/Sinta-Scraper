<?php
// =============================================
// Publikasi Garuda
// =============================================
$page_title = 'Garuda';
require_once __DIR__ . '/../layout/header.php';

$dosen_id = isset($_GET['dosen_id']) ? (int)$_GET['dosen_id'] : 0;
$dosen_list = $conn->query("SELECT id, nama FROM dosen ORDER BY nama ASC");
?>

<div class="card-custom">
    <div class="card-header-custom">
        <h5><i class="bi bi-journal-medical me-2"></i>Publikasi Garuda</h5>
        <div class="d-flex gap-2 flex-wrap align-items-center">
            <input type="text" id="searchKeyword" class="form-control form-control-sm" placeholder="Cari judul..." style="width:200px;" onkeyup="if(event.key === 'Enter') loadData(1)">
            <select id="filterDosen" class="form-select form-select-sm" style="width:200px;" onchange="loadData(1)">
                <option value="0">Semua Dosen</option>
                <?php while ($d = $dosen_list->fetch_assoc()): ?>
                <option value="<?= $d['id'] ?>" <?= $dosen_id == $d['id'] ? 'selected' : '' ?>><?= htmlspecialchars($d['nama']) ?></option>
                <?php endwhile; ?>
            </select>
            <select id="perPage" class="form-select form-select-sm" style="width:80px;" onchange="loadData(1)">
                <option value="10">10</option>
                <option value="20">20</option>
                <option value="50">50</option>
            </select>
            <button onclick="loadData(1)" class="btn btn-primary-custom btn-sm-custom"><i class="bi bi-search"></i> Cari</button>
            <a href="<?= $base_url ?>/export/export_excel.php?type=garuda&dosen_id=<?= $dosen_id ?>" class="btn btn-success-custom btn-sm-custom">
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
    const perPage = $('#perPage').val();
    const keyword = $('#searchKeyword').val();
    
    $('a[href*="export_excel"]').attr('href', '{$base_url}/export/export_excel.php?type=garuda&dosen_id=' + dosenId);
    
    loadPagination('{$base_url}/ajax/pagination_garuda.php', 'dataContainer', page, perPage, dosenId, { keyword: keyword });
}

$(document).ready(function() {
    loadData(1);
});
</script>
JS;

require_once __DIR__ . '/../layout/footer.php';
?>
