<?php
// =============================================
// Simpan Dosen (Insert/Update)
// =============================================
require_once __DIR__ . '/../config/koneksi.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $id       = isset($_POST['id']) ? (int)$_POST['id'] : 0;
    $nama     = $conn->real_escape_string(trim($_POST['nama']));
    $nidn     = $conn->real_escape_string(trim($_POST['nidn']));
    $prodi    = $conn->real_escape_string(trim($_POST['prodi']));
    $sinta_id = $conn->real_escape_string(trim($_POST['sinta_id'] ?? ''));
    $sinta_url = $conn->real_escape_string(trim($_POST['sinta_url'] ?? ''));
    $scholar_url = $conn->real_escape_string(trim($_POST['scholar_url'] ?? ''));

    if (empty($nama) || empty($nidn) || empty($prodi)) {
        header("Location: {$base_url}/dosen/dosen.php?msg=" . urlencode("Nama, NIDN, dan Prodi wajib diisi.") . "&type=danger");
        exit;
    }

    if ($id > 0) {
        // Update
        $sql = "UPDATE dosen SET 
                    nama = '$nama', 
                    nidn = '$nidn', 
                    prodi = '$prodi', 
                    sinta_id = '$sinta_id', 
                    sinta_url = '$sinta_url',
                    scholar_url = '$scholar_url'
                WHERE id = $id";
        $msg = "Data dosen berhasil diperbarui.";
    } else {
        // Insert
        $sql = "INSERT INTO dosen (nama, nidn, prodi, sinta_id, sinta_url, scholar_url) 
                VALUES ('$nama', '$nidn', '$prodi', '$sinta_id', '$sinta_url', '$scholar_url')";
        $msg = "Dosen baru berhasil ditambahkan.";
    }

    if ($conn->query($sql)) {
        header("Location: {$base_url}/dosen/dosen.php?msg=" . urlencode($msg) . "&type=success");
    } else {
        header("Location: {$base_url}/dosen/dosen.php?msg=" . urlencode("Gagal menyimpan: " . $conn->error) . "&type=danger");
    }
    exit;
}

header("Location: {$base_url}/dosen/dosen.php");
exit;
