<?php
// =============================================
// AJAX Pagination - Google Scholar (Compact Layout)
// =============================================
require_once __DIR__ . '/../config/koneksi.php';

$page = isset($_GET['page']) ? (int)$_GET['page'] : 1;
$per_page = isset($_GET['per_page']) ? (int)$_GET['per_page'] : 10;
$dosen_id = isset($_GET['dosen_id']) ? (int)$_GET['dosen_id'] : 0;
$tahun_mulai = isset($_GET['tahun_mulai']) ? (int)$_GET['tahun_mulai'] : 0;
$tahun_selesai = isset($_GET['tahun_selesai']) ? (int)$_GET['tahun_selesai'] : 0;
$keyword = isset($_GET['keyword']) ? $conn->real_escape_string($_GET['keyword']) : '';

if ($page < 1) $page = 1;
if (!in_array($per_page, [10, 20, 50, 100])) $per_page = 10;

$where = [];
if ($dosen_id > 0) $where[] = "gs.dosen_id = $dosen_id";

if ($tahun_mulai > 0 && $tahun_selesai > 0) {
    $where[] = "gs.tahun BETWEEN $tahun_mulai AND $tahun_selesai";
} elseif ($tahun_mulai > 0) {
    $where[] = "gs.tahun >= $tahun_mulai";
} elseif ($tahun_selesai > 0) {
    $where[] = "gs.tahun <= $tahun_selesai";
}

if ($keyword !== '') $where[] = "gs.judul LIKE '%$keyword%'";

$where_sql = !empty($where) ? 'WHERE ' . implode(' AND ', $where) : '';

$total = $conn->query("SELECT COUNT(*) as total FROM publikasi_gs gs $where_sql")->fetch_assoc()['total'];
$total_pages = ceil($total / $per_page);
$offset = ($page - 1) * $per_page;

$data = $conn->query("
    SELECT gs.*, d.nama as nama_dosen 
    FROM publikasi_gs gs 
    JOIN dosen d ON gs.dosen_id = d.id 
    $where_sql
    ORDER BY gs.sitasi DESC 
    LIMIT $per_page OFFSET $offset
");

if ($total == 0) {
    echo '<div class="empty-state">
            <i class="bi bi-journal-richtext"></i>
            <h5>Belum Ada Data Google Scholar</h5>
            <p>Lakukan scraping untuk mengambil data publikasi.</p>
          </div>';
    exit;
}
?>

<div class="table-responsive">
    <table class="table-custom">
        <thead>
            <tr>
                <th style="width:45px;">No</th>
                <th>Publikasi</th>
                <th style="width:80px;text-align:center;">Sitasi</th>
                <th style="width:140px;">Dosen</th>
                <th style="width:50px;text-align:center;">Link</th>
            </tr>
        </thead>
        <tbody>
            <?php $no = $offset + 1; while ($row = $data->fetch_assoc()): 
                $sitasi = (int)$row['sitasi'];
                // Badge warna berdasarkan sitasi
                if ($sitasi >= 100) $badge_class = 'badge-danger';
                elseif ($sitasi >= 50) $badge_class = 'badge-warning';
                elseif ($sitasi >= 10) $badge_class = 'badge-success';
                elseif ($sitasi > 0)  $badge_class = 'badge-primary';
                else $badge_class = 'badge-secondary';
            ?>
            <tr>
                <td><?= $no++ ?></td>
                <td>
                    <div class="mb-1">
                        <strong><?= htmlspecialchars($row['judul']) ?></strong>
                    </div>
                    <?php if (!empty($row['penulis'])): ?>
                    <div class="small text-muted mb-1">
                        <i class="bi bi-people-fill me-1"></i><?= htmlspecialchars(mb_strimwidth($row['penulis'], 0, 120, '...')) ?>
                    </div>
                    <?php endif; ?>
                    <?php if (!empty($row['nama_jurnal'])): ?>
                    <div class="small mb-1">
                        <i class="bi bi-journal-text me-1 text-primary"></i>
                        <em><?= htmlspecialchars($row['nama_jurnal']) ?></em>
                    </div>
                    <?php endif; ?>
                    <div class="small text-muted">
                        <?php
                        $meta_parts = [];
                        if (!empty($row['jilid'])) $meta_parts[] = "Vol. {$row['jilid']}";
                        if (!empty($row['terbitan'])) $meta_parts[] = "No. {$row['terbitan']}";
                        if (!empty($row['halaman'])) $meta_parts[] = "Hal. {$row['halaman']}";
                        if (!empty($row['tahun'])) $meta_parts[] = $row['tahun'];
                        if (!empty($meta_parts)):
                        ?>
                        <i class="bi bi-info-circle me-1"></i><?= implode(' · ', $meta_parts) ?>
                        <?php endif; ?>
                    </div>
                </td>
                <td class="text-center">
                    <span class="badge-custom <?= $badge_class ?>" style="font-size:13px;min-width:40px;display:inline-block;">
                        <?= number_format($sitasi) ?>
                    </span>
                </td>
                <td><small class="fw-semibold"><?= htmlspecialchars($row['nama_dosen']) ?></small></td>
                <td class="text-center">
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
