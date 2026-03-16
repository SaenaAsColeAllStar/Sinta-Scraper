<?php
// =============================================
// Tambah Dosen
// =============================================
$page_title = 'Tambah Dosen';
require_once __DIR__ . '/../layout/header.php';
?>

<div class="row justify-content-center">
    <div class="col-lg-8">
        <div class="card-custom">
            <div class="card-header-custom">
                <h5><i class="bi bi-person-plus-fill me-2"></i>Tambah Dosen Baru</h5>
                <a href="<?= $base_url ?>/dosen/dosen.php" class="btn btn-primary-custom btn-sm-custom">
                    <i class="bi bi-arrow-left"></i> Kembali
                </a>
            </div>
            <div class="card-body-custom">
                <form action="<?= $base_url ?>/dosen/simpan_dosen.php" method="POST" id="formDosen">
                    <div class="row g-3">
                        <div class="col-md-6">
                            <label class="form-label-custom">Nama Lengkap <span class="text-danger">*</span></label>
                            <input type="text" name="nama" class="form-control form-control-custom" placeholder="Masukkan nama lengkap" required>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label-custom">NIDN <span class="text-danger">*</span></label>
                            <input type="text" name="nidn" class="form-control form-control-custom" placeholder="Masukkan NIDN" required>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label-custom">Program Studi <span class="text-danger">*</span></label>
                            <select name="prodi" class="form-select form-control-custom" required>
                                <option value="">-- Pilih Prodi --</option>
                                <option value="Manajemen">Manajemen</option>
                                <option value="Akuntansi">Akuntansi</option>
                                <option value="Ekonomi Syariah">Ekonomi Syariah</option>
                            </select>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label-custom">SINTA ID</label>
                            <input type="text" name="sinta_id" class="form-control form-control-custom" placeholder="Contoh: 6012345">
                        </div>
                        <div class="col-12">
                            <label class="form-label-custom">URL Profil SINTA</label>
                            <input type="url" name="sinta_url" class="form-control form-control-custom" placeholder="https://sinta.kemdiktisaintek.go.id/authors/profile/XXXXXXX">
                            <small class="text-muted mt-1 d-block">
                                <i class="bi bi-info-circle me-1"></i>
                                Contoh: <code>https://sinta.kemdiktisaintek.go.id/authors/profile/6024693</code>
                            </small>
                        </div>
                        <div class="col-12">
                            <label class="form-label-custom">URL Profil Google Scholar</label>
                            <input type="url" name="scholar_url" class="form-control form-control-custom" placeholder="https://scholar.google.com/citations?hl=id&user=XXXXXXX">
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
                                    <i class="bi bi-check-lg"></i> Simpan
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
