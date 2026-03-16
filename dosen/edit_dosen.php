<?php
// =============================================
// Edit Dosen
// =============================================
$page_title = 'Edit Dosen';
require_once __DIR__ . '/../config/koneksi.php';

$id = isset($_GET['id']) ? (int)$_GET['id'] : 0;
$dosen = $conn->query("SELECT * FROM dosen WHERE id = $id")->fetch_assoc();

if (!$dosen) {
    header("Location: {$base_url}/dosen/dosen.php?msg=" . urlencode("Dosen tidak ditemukan.") . "&type=danger");
    exit;
}

require_once __DIR__ . '/../layout/header.php';
?>

<div class="row justify-content-center">
    <div class="col-lg-8">
        <div class="card-custom">
            <div class="card-header-custom">
                <h5><i class="bi bi-pencil-square me-2"></i>Edit Dosen</h5>
                <a href="<?= $base_url ?>/dosen/dosen.php" class="btn btn-primary-custom btn-sm-custom">
                    <i class="bi bi-arrow-left"></i> Kembali
                </a>
            </div>
            <div class="card-body-custom">
                <form action="<?= $base_url ?>/dosen/simpan_dosen.php" method="POST">
                    <input type="hidden" name="id" value="<?= $dosen['id'] ?>">
                    <div class="row g-3">
                        <div class="col-md-6">
                            <label class="form-label-custom">Nama Lengkap <span class="text-danger">*</span></label>
                            <input type="text" name="nama" class="form-control form-control-custom" value="<?= htmlspecialchars($dosen['nama']) ?>" required>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label-custom">NIDN <span class="text-danger">*</span></label>
                            <input type="text" name="nidn" class="form-control form-control-custom" value="<?= htmlspecialchars($dosen['nidn']) ?>" required>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label-custom">Program Studi <span class="text-danger">*</span></label>
                            <select name="prodi" class="form-select form-control-custom" required>
                                <option value="">-- Pilih Prodi --</option>
                                <option value="Manajemen" <?= $dosen['prodi'] == 'Manajemen' ? 'selected' : '' ?>>Manajemen</option>
                                <option value="Akuntansi" <?= $dosen['prodi'] == 'Akuntansi' ? 'selected' : '' ?>>Akuntansi</option>
                                <option value="Ekonomi Syariah" <?= $dosen['prodi'] == 'Ekonomi Syariah' ? 'selected' : '' ?>>Ekonomi Syariah</option>
                            </select>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label-custom">SINTA ID</label>
                            <input type="text" name="sinta_id" class="form-control form-control-custom" value="<?= htmlspecialchars($dosen['sinta_id']) ?>">
                        </div>
                        <div class="col-12">
                            <label class="form-label-custom">URL Profil SINTA</label>
                            <input type="url" name="sinta_url" class="form-control form-control-custom" value="<?= htmlspecialchars($dosen['sinta_url']) ?>">
                        </div>
                        <div class="col-12">
                            <label class="form-label-custom">URL Profil Google Scholar</label>
                            <input type="url" name="scholar_url" class="form-control form-control-custom" value="<?= htmlspecialchars($dosen['scholar_url'] ?? '') ?>">
                            <small class="text-muted mt-1 d-block">
                                <i class="bi bi-info-circle me-1"></i>
                                Contoh: <code>https://scholar.google.com/citations?hl=id&user=aQ72cK4AAAAJ</code>
                            </small>
                        </div>
                        <div class="col-12">
                            <hr class="my-2">
                            <div class="d-flex gap-2 justify-content-end">
                                <a href="<?= $base_url ?>/dosen/dosen.php" class="btn btn-light px-4">Batal</a>
                                <button type="submit" class="btn btn-success-custom px-4">
                                    <i class="bi bi-check-lg"></i> Update
                                </button>
                            </div>
                        </div>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>

<?php require_once __DIR__ . '/../layout/footer.php'; ?>
