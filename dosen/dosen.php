<?php
// =============================================
// Data Dosen - List
// =============================================
$page_title = 'Data Dosen';
require_once __DIR__ . '/../layout/header.php';

// Flash message
$msg = isset($_GET['msg']) ? $_GET['msg'] : '';
$msg_type = isset($_GET['type']) ? $_GET['type'] : 'success';

// Search
$search = isset($_GET['search']) ? $conn->real_escape_string($_GET['search']) : '';
$where = '';
if ($search) {
    $where = "WHERE nama LIKE '%$search%' OR nidn LIKE '%$search%' OR prodi LIKE '%$search%'";
}

// Pagination
$per_page = isset($_GET['per_page']) ? (int)$_GET['per_page'] : 10;
$page = isset($_GET['page']) ? (int)$_GET['page'] : 1;
if ($page < 1) $page = 1;

$total = $conn->query("SELECT COUNT(*) as total FROM dosen $where")->fetch_assoc()['total'];
$total_pages = ceil($total / $per_page);
$offset = ($page - 1) * $per_page;

$dosen_list = $conn->query("SELECT * FROM dosen $where ORDER BY nama ASC LIMIT $per_page OFFSET $offset");
?>

<?php if ($msg): ?>
<div class="alert alert-<?= $msg_type == 'success' ? 'success' : 'danger' ?> alert-custom alert-dismissible fade show" role="alert">
    <i class="bi bi-<?= $msg_type == 'success' ? 'check-circle-fill' : 'exclamation-triangle-fill' ?>"></i>
    <?= htmlspecialchars($msg) ?>
    <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
</div>
<?php endif; ?>

<div class="card-custom">
    <div class="card-header-custom">
        <h5><i class="bi bi-people-fill me-2"></i>Daftar Dosen</h5>
        <div class="d-flex gap-2 flex-wrap">
            <form class="d-flex gap-2" method="GET">
                <input type="text" name="search" class="form-control form-control-custom" placeholder="Cari nama/NIDN..." value="<?= htmlspecialchars($search) ?>" style="width: 200px;">
                <button type="submit" class="btn btn-primary-custom btn-sm-custom">
                    <i class="bi bi-search"></i>
                </button>
            </form>
            <a href="<?= $base_url ?>/dosen/tambah_dosen.php" class="btn btn-success-custom">
                <i class="bi bi-plus-lg"></i> Tambah Dosen
            </a>
        </div>
    </div>
    <div class="card-body-custom p-0">
        <?php if ($dosen_list->num_rows > 0): ?>
        <div class="table-responsive">
            <table class="table-custom">
                <thead>
                    <tr>
                        <th>No</th>
                        <th>Nama Dosen</th>
                        <th>NIDN</th>
                        <th>Prodi</th>
                        <th>SINTA ID</th>
                        <th>Last Scraped (SINTA)</th>
                        <th>Last Scraped (Scholar)</th>
                        <th>Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    <?php $no = $offset + 1; while ($d = $dosen_list->fetch_assoc()): ?>
                    <tr>
                        <td><?= $no++ ?></td>
                        <td>
                            <div class="d-flex align-items-center gap-2">
                                <div class="dosen-avatar"><?= strtoupper(substr($d['nama'], 0, 1)) ?></div>
                                <span class="fw-semibold"><?= htmlspecialchars($d['nama']) ?></span>
                            </div>
                        </td>
                        <td><span class="badge-custom badge-primary"><?= $d['nidn'] ?></span></td>
                        <td><?= htmlspecialchars($d['prodi']) ?></td>
                        <td><?= $d['sinta_id'] ?: '<span class="text-muted">-</span>' ?></td>
                        <td>
                            <?php 
                            if ($d['last_scraped']): 
                                $days = (time() - strtotime($d['last_scraped'])) / (60 * 60 * 24);
                                $status_class = $days > 30 ? 'badge-danger' : 'text-muted';
                            ?>
                                <?php if($days > 30): ?>
                                    <span class="badge-custom <?= $status_class ?>"><i class="bi bi-exclamation-circle text-white me-1"></i><?= date('d M Y', strtotime($d['last_scraped'])) ?></span>
                                    <small class="text-danger d-block mt-1" style="font-size: 10px;">> 30 Hari (Outdated)</small>
                                <?php else: ?>
                                    <small class="<?= $status_class ?>"><?= date('d M Y H:i', strtotime($d['last_scraped'])) ?></small>
                                <?php endif; ?>
                            <?php else: ?>
                                <span class="badge-custom badge-warning">Belum</span>
                            <?php endif; ?>
                        </td>
                        <td>
                            <?php 
                            if (isset($d['last_scraped_gs']) && $d['last_scraped_gs']): 
                                $days_gs = (time() - strtotime($d['last_scraped_gs'])) / (60 * 60 * 24);
                                $status_class_gs = $days_gs > 30 ? 'badge-danger' : 'text-muted';
                            ?>
                                <?php if($days_gs > 30): ?>
                                    <span class="badge-custom <?= $status_class_gs ?>"><i class="bi bi-exclamation-circle text-white me-1"></i><?= date('d M Y', strtotime($d['last_scraped_gs'])) ?></span>
                                    <small class="text-danger d-block mt-1" style="font-size: 10px;">> 30 Hari (Outdated)</small>
                                <?php else: ?>
                                    <small class="<?= $status_class_gs ?>"><?= date('d M Y H:i', strtotime($d['last_scraped_gs'])) ?></small>
                                <?php endif; ?>
                            <?php else: ?>
                                <span class="badge-custom badge-warning">Belum</span>
                            <?php endif; ?>
                        </td>
                        <td>
                            <div class="d-flex gap-1">
                                <a href="<?= $base_url ?>/scraping/scrape_sinta.php?id=<?= $d['id'] ?>" class="btn btn-info-custom btn-sm-custom" data-bs-toggle="tooltip" title="Scrape SINTA">
                                    <i class="bi bi-arrow-repeat"></i>
                                </a>
                                <a href="<?= $base_url ?>/scraping/scrape_scholar.php?id=<?= $d['id'] ?>" class="btn btn-success-custom btn-sm-custom" data-bs-toggle="tooltip" title="Scrape Google Scholar">
                                    <i class="bi bi-google"></i>
                                </a>
                                <a href="<?= $base_url ?>/dosen/edit_dosen.php?id=<?= $d['id'] ?>" class="btn btn-warning-custom btn-sm-custom" data-bs-toggle="tooltip" title="Edit">
                                    <i class="bi bi-pencil-square"></i>
                                </a>
                                <a href="<?= $base_url ?>/dosen/hapus_dosen.php?id=<?= $d['id'] ?>" class="btn btn-danger-custom btn-sm-custom" data-bs-toggle="tooltip" title="Hapus" onclick="return confirm('Yakin hapus dosen ini? Semua data publikasi akan ikut terhapus.')">
                                    <i class="bi bi-trash3"></i>
                                </a>
                            </div>
                        </td>
                    </tr>
                    <?php endwhile; ?>
                </tbody>
            </table>
        </div>
        
        <!-- Pagination -->
        <div class="pagination-wrapper px-3">
            <div class="d-flex align-items-center gap-3">
                <div class="pagination-info">
                    Menampilkan <?= $offset + 1 ?>-<?= min($offset + $per_page, $total) ?> dari <?= $total ?> data
                </div>
                <select class="form-select form-select-sm" style="width: auto;" onchange="window.location.href='?per_page=' + this.value + '&search=<?= urlencode($search) ?>'">
                    <option value="10" <?= $per_page == 10 ? 'selected' : '' ?>>10</option>
                    <option value="20" <?= $per_page == 20 ? 'selected' : '' ?>>20</option>
                    <option value="50" <?= $per_page == 50 ? 'selected' : '' ?>>50</option>
                </select>
            </div>
            <?php if ($total_pages > 1): ?>
            <nav>
                <ul class="pagination mb-0">
                    <li class="page-item <?= $page <= 1 ? 'disabled' : '' ?>">
                        <a class="page-link" href="?page=<?= $page-1 ?>&per_page=<?= $per_page ?>&search=<?= urlencode($search) ?>"><i class="bi bi-chevron-left"></i></a>
                    </li>
                    <?php for ($i = max(1, $page-2); $i <= min($total_pages, $page+2); $i++): ?>
                    <li class="page-item <?= $i == $page ? 'active' : '' ?>">
                        <a class="page-link" href="?page=<?= $i ?>&per_page=<?= $per_page ?>&search=<?= urlencode($search) ?>"><?= $i ?></a>
                    </li>
                    <?php endfor; ?>
                    <li class="page-item <?= $page >= $total_pages ? 'disabled' : '' ?>">
                        <a class="page-link" href="?page=<?= $page+1 ?>&per_page=<?= $per_page ?>&search=<?= urlencode($search) ?>"><i class="bi bi-chevron-right"></i></a>
                    </li>
                </ul>
            </nav>
            <?php endif; ?>
        </div>
        <?php else: ?>
        <div class="empty-state">
            <i class="bi bi-people"></i>
            <h5>Belum Ada Data Dosen</h5>
            <p>Klik tombol "Tambah Dosen" untuk menambahkan data.</p>
        </div>
        <?php endif; ?>
    </div>
</div>

<?php require_once __DIR__ . '/../layout/footer.php'; ?>
