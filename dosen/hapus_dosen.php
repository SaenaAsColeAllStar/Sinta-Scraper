<?php
// =============================================
// Hapus Dosen
// =============================================
require_once __DIR__ . '/../config/koneksi.php';

$id = isset($_GET['id']) ? (int)$_GET['id'] : 0;

if ($id > 0) {
    $result = $conn->query("DELETE FROM dosen WHERE id = $id");
    if ($result) {
        header("Location: {$base_url}/dosen/dosen.php?msg=" . urlencode("Dosen berhasil dihapus beserta semua data publikasinya.") . "&type=success");
    } else {
        header("Location: {$base_url}/dosen/dosen.php?msg=" . urlencode("Gagal menghapus dosen.") . "&type=danger");
    }
} else {
    header("Location: {$base_url}/dosen/dosen.php?msg=" . urlencode("ID dosen tidak valid.") . "&type=danger");
}
exit;
