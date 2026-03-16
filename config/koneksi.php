<?php
// =============================================
// Koneksi Database
// Sistem Monitoring Publikasi Dosen LPPM
// STIE Miftahul Huda Subang
// =============================================

$host = 'localhost';
$user = 'root';
$pass = '';
$dbname = 'sinta_monitoring';

$conn = new mysqli($host, $user, $pass, $dbname);

if ($conn->connect_error) {
    die("Koneksi gagal: " . $conn->connect_error);
}

$conn->set_charset("utf8mb4");

// Base URL
$base_url = '/Scraping/sinta-monitoring';

// App Info
$app_name = 'Monitoring Publikasi Dosen';
$institusi = 'STIE Miftahul Huda Subang';

// =============================================
// SINTA Login Credentials
// Isi username dan password akun SINTA Anda
// agar scraping bisa mengambil SEMUA data publikasi
// (tanpa login, SINTA hanya menampilkan 10 data per kategori)
// =============================================
$sinta_username = 'fajarnugrahayusman@gmail.com'; // Isi dengan username SINTA
$sinta_password = '0406038903'; // Isi dengan password SINTA
$sinta_cookie_file = sys_get_temp_dir() . '/sinta_session_cookie.txt';
