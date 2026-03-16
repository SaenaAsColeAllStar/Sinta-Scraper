<?php
// =============================================
// Export Data ke Excel
// =============================================
$page_title = 'Export Excel';
require_once __DIR__ . '/../config/koneksi.php';

$type = isset($_GET['type']) ? $_GET['type'] : '';
$dosen_id = isset($_GET['dosen_id']) ? (int)$_GET['dosen_id'] : 0;

// Jika type diberikan, langsung export
if (!empty($type)) {
    exportToExcel($conn, $type, $dosen_id);
    exit;
}

// Jika tidak ada type, tampilkan halaman export
require_once __DIR__ . '/../layout/header.php';

$dosen_list = $conn->query("SELECT id, nama FROM dosen ORDER BY nama ASC");

// Hitung data
$counts = [];
$counts['google_scholar'] = $conn->query("SELECT COUNT(*) as total FROM publikasi_gs")->fetch_assoc()['total'];
$counts['scopus'] = $conn->query("SELECT COUNT(*) as total FROM publikasi_scopus")->fetch_assoc()['total'];
$counts['garuda'] = $conn->query("SELECT COUNT(*) as total FROM publikasi_garuda")->fetch_assoc()['total'];
$counts['hki'] = $conn->query("SELECT COUNT(*) as total FROM hki")->fetch_assoc()['total'];
$counts['buku'] = $conn->query("SELECT COUNT(*) as total FROM buku")->fetch_assoc()['total'];
?>

<div class="row g-4">
    <div class="col-lg-8">
        <div class="card-custom">
            <div class="card-header-custom">
                <h5><i class="bi bi-file-earmark-spreadsheet me-2"></i>Export Data ke Excel</h5>
            </div>
            <div class="card-body-custom">
                <form id="exportForm" method="GET" action="">
                    <div class="row g-3 mb-4">
                        <div class="col-md-6">
                            <label class="form-label-custom">Jenis Publikasi <span class="text-danger">*</span></label>
                            <select name="type" class="form-select form-control-custom" required id="exportType">
                                <option value="">-- Pilih Jenis --</option>
                                <option value="google_scholar">Google Scholar (<?= $counts['google_scholar'] ?> data)</option>
                                <option value="scopus">Scopus (<?= $counts['scopus'] ?> data)</option>
                                <option value="garuda">Garuda (<?= $counts['garuda'] ?> data)</option>
                                <option value="hki">HKI (<?= $counts['hki'] ?> data)</option>
                                <option value="buku">Buku (<?= $counts['buku'] ?> data)</option>
                                <option value="semua_dosen">Rekap Semua Dosen</option>
                            </select>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label-custom">Filter Dosen</label>
                            <select name="dosen_id" class="form-select form-control-custom">
                                <option value="0">Semua Dosen</option>
                                <?php while ($d = $dosen_list->fetch_assoc()): ?>
                                <option value="<?= $d['id'] ?>"><?= htmlspecialchars($d['nama']) ?></option>
                                <?php endwhile; ?>
                            </select>
                        </div>
                    </div>
                    <button type="submit" class="btn btn-success-custom px-4">
                        <i class="bi bi-download me-1"></i> Download Excel
                    </button>
                </form>
            </div>
        </div>
    </div>
    
    <div class="col-lg-4">
        <div class="card-custom">
            <div class="card-header-custom">
                <h5><i class="bi bi-info-circle me-2"></i>Ringkasan Data</h5>
            </div>
            <div class="card-body-custom">
                <ul class="list-unstyled mb-0">
                    <li class="d-flex justify-content-between align-items-center mb-3">
                        <span><i class="bi bi-journal-richtext text-success me-2"></i>Google Scholar</span>
                        <span class="badge-custom badge-success"><?= $counts['google_scholar'] ?></span>
                    </li>
                    <li class="d-flex justify-content-between align-items-center mb-3">
                        <span><i class="bi bi-journal-bookmark-fill text-warning me-2"></i>Scopus</span>
                        <span class="badge-custom badge-warning"><?= $counts['scopus'] ?></span>
                    </li>
                    <li class="d-flex justify-content-between align-items-center mb-3">
                        <span><i class="bi bi-journal-medical text-danger me-2"></i>Garuda</span>
                        <span class="badge-custom badge-danger"><?= $counts['garuda'] ?></span>
                    </li>
                    <li class="d-flex justify-content-between align-items-center mb-3">
                        <span><i class="bi bi-shield-check text-info me-2"></i>HKI</span>
                        <span class="badge-custom badge-info"><?= $counts['hki'] ?></span>
                    </li>
                    <li class="d-flex justify-content-between align-items-center">
                        <span><i class="bi bi-book-fill text-secondary me-2"></i>Buku</span>
                        <span class="badge-custom badge-secondary"><?= $counts['buku'] ?></span>
                    </li>
                </ul>
            </div>
        </div>
    </div>
</div>

<?php 
require_once __DIR__ . '/../layout/footer.php';

// ============ EXPORT FUNCTION ============
function exportToExcel($conn, $type, $dosen_id) {
    $where_dosen = '';
    $filename = '';
    
    switch ($type) {
        case 'google_scholar':
            $table = 'publikasi_gs';
            $alias = 'p';
            $filename = 'Google_Scholar';
            $columns = ['No', 'Nama Dosen', 'Judul', 'Penulis', 'Jurnal', 'Tahun', 'Sitasi', 'URL'];
            $fields = "p.judul, p.penulis, p.nama_jurnal, p.tahun, p.sitasi, p.url";
            break;
        case 'scopus':
            $table = 'publikasi_scopus';
            $alias = 'p';
            $filename = 'Scopus';
            $columns = ['No', 'Nama Dosen', 'Judul', 'Penulis', 'Jurnal', 'Tahun', 'Sitasi', 'Quartile', 'URL'];
            $fields = "p.judul, p.penulis, p.nama_jurnal, p.tahun, p.sitasi, p.quartile, p.url";
            break;
        case 'garuda':
            $table = 'publikasi_garuda';
            $alias = 'p';
            $filename = 'Garuda';
            $columns = ['No', 'Nama Dosen', 'Judul', 'Penulis', 'Jurnal', 'Tahun', 'URL'];
            $fields = "p.judul, p.penulis, p.nama_jurnal, p.tahun, p.url";
            break;
        case 'hki':
            $table = 'hki';
            $alias = 'p';
            $filename = 'HKI';
            $columns = ['No', 'Nama Dosen', 'Judul', 'Pemegang', 'Kategori', 'Nomor', 'Tahun'];
            $fields = "p.judul, p.pemegang, p.kategori, p.nomor, p.tahun";
            break;
        case 'buku':
            $table = 'buku';
            $alias = 'p';
            $filename = 'Buku';
            $columns = ['No', 'Nama Dosen', 'Judul', 'Penulis', 'Penerbit', 'ISBN', 'Tahun'];
            $fields = "p.judul, p.penulis, p.penerbit, p.isbn, p.tahun";
            break;
        case 'semua_dosen':
            exportRekapDosen($conn, $dosen_id);
            return;
        default:
            return;
    }
    
    if ($dosen_id > 0) {
        $where_dosen = "WHERE p.dosen_id = $dosen_id";
        $dosen_data = $conn->query("SELECT nama FROM dosen WHERE id = $dosen_id")->fetch_assoc();
        if ($dosen_data) {
            $filename .= '_' . str_replace(' ', '_', $dosen_data['nama']);
        }
    }
    
    $data = $conn->query("
        SELECT d.nama as nama_dosen, $fields
        FROM $table $alias
        JOIN dosen d ON $alias.dosen_id = d.id
        $where_dosen
        ORDER BY d.nama ASC, $alias.tahun DESC
    ");
    
    $filename .= '_' . date('Y-m-d') . '.xls';
    
    // Headers
    header('Content-Type: application/vnd.ms-excel');
    header('Content-Disposition: attachment; filename="' . $filename . '"');
    header('Cache-Control: max-age=0');
    
    echo '<html xmlns:o="urn:schemas-microsoft-com:office:office" xmlns:x="urn:schemas-microsoft-com:office:excel">';
    echo '<head><meta charset="UTF-8"><!--[if gte mso 9]><xml><x:ExcelWorkbook><x:ExcelWorksheets><x:ExcelWorksheet><x:Name>' . $filename . '</x:Name><x:WorksheetOptions><x:DisplayGridlines/></x:WorksheetOptions></x:ExcelWorksheet></x:ExcelWorksheets></x:ExcelWorkbook></xml><![endif]--></head>';
    echo '<body>';
    echo '<table border="1" cellpadding="5" cellspacing="0">';
    
    // Title
    echo '<tr><td colspan="' . count($columns) . '" style="font-size:16px;font-weight:bold;text-align:center;background:#4361ee;color:#fff;">Sistem Monitoring Publikasi Dosen LPPM</td></tr>';
    echo '<tr><td colspan="' . count($columns) . '" style="font-size:13px;text-align:center;background:#eef1ff;">STIE Miftahul Huda Subang - Data ' . ucfirst(str_replace('_', ' ', $type)) . '</td></tr>';
    echo '<tr><td colspan="' . count($columns) . '" style="font-size:11px;text-align:center;">Tanggal Export: ' . date('d F Y H:i') . '</td></tr>';
    echo '<tr></tr>';
    
    // Header
    echo '<tr>';
    foreach ($columns as $col) {
        echo '<th style="background:#2d3142;color:#fff;font-weight:bold;text-align:center;padding:8px;">' . $col . '</th>';
    }
    echo '</tr>';
    
    // Data rows
    $no = 1;
    while ($row = $data->fetch_assoc()) {
        echo '<tr>';
        echo '<td style="text-align:center;">' . $no++ . '</td>';
        foreach ($row as $val) {
            echo '<td>' . htmlspecialchars($val ?? '') . '</td>';
        }
        echo '</tr>';
    }
    
    echo '</table></body></html>';
}

function exportRekapDosen($conn, $dosen_id) {
    $where = $dosen_id > 0 ? "WHERE d.id = $dosen_id" : "";
    
    $data = $conn->query("
        SELECT d.*,
            (SELECT COUNT(*) FROM publikasi_gs WHERE dosen_id = d.id) as total_gs,
            (SELECT COUNT(*) FROM publikasi_scopus WHERE dosen_id = d.id) as total_scopus,
            (SELECT COUNT(*) FROM publikasi_garuda WHERE dosen_id = d.id) as total_garuda,
            (SELECT COUNT(*) FROM hki WHERE dosen_id = d.id) as total_hki,
            (SELECT COUNT(*) FROM buku WHERE dosen_id = d.id) as total_buku,
            (SELECT IFNULL(SUM(sitasi),0) FROM publikasi_gs WHERE dosen_id = d.id) as total_sitasi_gs,
            (SELECT IFNULL(SUM(sitasi),0) FROM publikasi_scopus WHERE dosen_id = d.id) as total_sitasi_scopus
        FROM dosen d
        $where
        ORDER BY d.nama ASC
    ");
    
    $filename = 'Rekap_Dosen_' . date('Y-m-d') . '.xls';
    
    header('Content-Type: application/vnd.ms-excel');
    header('Content-Disposition: attachment; filename="' . $filename . '"');
    header('Cache-Control: max-age=0');
    
    echo '<html><head><meta charset="UTF-8"></head><body>';
    echo '<table border="1" cellpadding="5" cellspacing="0">';
    
    echo '<tr><td colspan="12" style="font-size:16px;font-weight:bold;text-align:center;background:#4361ee;color:#fff;">Rekap Data Dosen - LPPM</td></tr>';
    echo '<tr><td colspan="12" style="font-size:13px;text-align:center;background:#eef1ff;">STIE Miftahul Huda Subang</td></tr>';
    echo '<tr><td colspan="12" style="font-size:11px;text-align:center;">Tanggal Export: ' . date('d F Y H:i') . '</td></tr>';
    echo '<tr></tr>';
    
    echo '<tr>';
    $headers = ['No','Nama','NIDN','Prodi','SINTA ID','Skor SINTA','H-Index Scopus','H-Index GS','Google Scholar','Scopus','Garuda','HKI','Buku','Total Sitasi GS','Total Sitasi Scopus'];
    foreach ($headers as $h) {
        echo '<th style="background:#2d3142;color:#fff;font-weight:bold;text-align:center;padding:8px;">' . $h . '</th>';
    }
    echo '</tr>';
    
    $no = 1;
    while ($row = $data->fetch_assoc()) {
        echo '<tr>';
        echo '<td style="text-align:center;">' . $no++ . '</td>';
        echo '<td>' . htmlspecialchars($row['nama']) . '</td>';
        echo '<td>' . $row['nidn'] . '</td>';
        echo '<td>' . htmlspecialchars($row['prodi']) . '</td>';
        echo '<td>' . $row['sinta_id'] . '</td>';
        echo '<td style="text-align:center;">' . $row['sinta_score_overall'] . '</td>';
        echo '<td style="text-align:center;">' . $row['h_index_scopus'] . '</td>';
        echo '<td style="text-align:center;">' . $row['h_index_gs'] . '</td>';
        echo '<td style="text-align:center;">' . $row['total_gs'] . '</td>';
        echo '<td style="text-align:center;">' . $row['total_scopus'] . '</td>';
        echo '<td style="text-align:center;">' . $row['total_garuda'] . '</td>';
        echo '<td style="text-align:center;">' . $row['total_hki'] . '</td>';
        echo '<td style="text-align:center;">' . $row['total_buku'] . '</td>';
        echo '<td style="text-align:center;">' . $row['total_sitasi_gs'] . '</td>';
        echo '<td style="text-align:center;">' . $row['total_sitasi_scopus'] . '</td>';
        echo '</tr>';
    }
    
    echo '</table></body></html>';
}
