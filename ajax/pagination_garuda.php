<?php
// =============================================
// AJAX Pagination - Garuda
// =============================================
require_once __DIR__ . '/../config/koneksi.php';

$page = isset($_GET['page']) ? (int)$_GET['page'] : 1;
$per_page = isset($_GET['per_page']) ? (int)$_GET['per_page'] : 10;
$dosen_id = isset($_GET['dosen_id']) ? (int)$_GET['dosen_id'] : 0;

if ($page < 1) $page = 1;
if (!in_array($per_page, [10, 20, 50])) $per_page = 10;

$where = '';
if ($dosen_id > 0) {
    $where = "WHERE g.dosen_id = $dosen_id";
}

$total = $conn->query("SELECT COUNT(*) as total FROM publikasi_garuda g $where")->fetch_assoc()['total'];
$total_pages = ceil($total / $per_page);
$offset = ($page - 1) * $per_page;

$data = $conn->query("
    SELECT g.*, d.nama as nama_dosen 
    FROM publikasi_garuda g 
    JOIN dosen d ON g.dosen_id = d.id 
    $where
    ORDER BY g.tahun DESC 
    LIMIT $per_page OFFSET $offset
");

if ($total == 0) {
    echo '<div class="empty-state">
            <i class="bi bi-journal-medical"></i>
            <h5>Belum Ada Data Garuda</h5>
            <p>Lakukan scraping untuk mengambil data publikasi.</p>
          </div>';
    exit;
}
?>

<div class="table-responsive">
    <table class="table-custom">
        <thead>
            <tr>
                <th>No</th>
                <th>Judul</th>
                <th>Penulis</th>
                <th>Jurnal</th>
                <th>Tahun</th>
                <th>Dosen</th>
                <th>Link</th>
            </tr>
        </thead>
        <tbody>
            <?php $no = $offset + 1; while ($row = $data->fetch_assoc()): ?>
            <tr>
                <td><?= $no++ ?></td>
                <td style="max-width:300px;">
                    <span class="fw-semibold"><?= htmlspecialchars(mb_strimwidth($row['judul'], 0, 100, '...')) ?></span>
                </td>
                <td style="max-width:200px;"><small class="text-muted"><?= htmlspecialchars(mb_strimwidth($row['penulis'] ?? '-', 0, 80, '...')) ?></small></td>
                <td><small><?= htmlspecialchars(mb_strimwidth($row['nama_jurnal'] ?? '-', 0, 60, '...')) ?></small></td>
                <td><span class="badge-custom badge-primary"><?= $row['tahun'] ?: '-' ?></span></td>
                <td><small class="fw-semibold"><?= htmlspecialchars($row['nama_dosen']) ?></small></td>
                <td>
                    <?php if (!empty($row['url'])): ?>
                    <a href="<?= htmlspecialchars($row['url']) ?>" target="_blank" class="btn btn-primary-custom btn-sm-custom">
                        <i class="bi bi-box-arrow-up-right"></i>
                    </a>
                    <?php else: ?>
                    <span class="text-muted">-</span>
                    <?php endif; ?>
                </td>
            </tr>
            <?php endwhile; ?>
        </tbody>
    </table>
</div>

<?php include __DIR__ . '/_pagination_controls.php'; ?>
