<?php
// =============================================
// Layout Header - Sistem Monitoring Publikasi
// =============================================
require_once __DIR__ . '/../config/koneksi.php';

$current_page = basename($_SERVER['PHP_SELF'], '.php');
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= isset($page_title) ? $page_title . ' - ' : '' ?><?= $app_name ?></title>
    <meta name="description" content="Sistem Monitoring Publikasi Dosen LPPM - <?= $institusi ?>">
    
    <!-- Bootstrap 5 CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <!-- Bootstrap Icons -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css" rel="stylesheet">
    <!-- Custom CSS -->
    <link href="<?= $base_url ?>/assets/css/style.css" rel="stylesheet">
</head>
<body>

<!-- Loading Overlay -->
<div class="loading-overlay">
    <div class="spinner-custom"></div>
</div>

<!-- Sidebar -->
<aside class="sidebar">
    <div class="sidebar-brand">
        <div class="brand-icon">
            <i class="bi bi-mortarboard-fill"></i>
        </div>
        <div class="brand-text">
            <h5>LPPM Monitoring</h5>
            <small><?= $institusi ?></small>
        </div>
    </div>
    <nav class="sidebar-menu">
        <div class="menu-label">Menu Utama</div>
        <a href="<?= $base_url ?>/index.php" class="menu-item <?= $current_page == 'index' ? 'active' : '' ?>">
            <i class="bi bi-grid-1x2-fill"></i> Dashboard
        </a>
        <a href="<?= $base_url ?>/dosen/dosen.php" class="menu-item <?= in_array($current_page, ['dosen','tambah_dosen','edit_dosen']) ? 'active' : '' ?>">
            <i class="bi bi-people-fill"></i> Data Dosen
        </a>
        
        <div class="menu-label">Publikasi</div>
        <a href="<?= $base_url ?>/publikasi/google_scholar.php" class="menu-item <?= $current_page == 'google_scholar' ? 'active' : '' ?>">
            <i class="bi bi-journal-richtext"></i> Google Scholar
        </a>
        <a href="<?= $base_url ?>/publikasi/scopus.php" class="menu-item <?= $current_page == 'scopus' ? 'active' : '' ?>">
            <i class="bi bi-journal-bookmark-fill"></i> Scopus
        </a>
        <a href="<?= $base_url ?>/publikasi/garuda.php" class="menu-item <?= $current_page == 'garuda' ? 'active' : '' ?>">
            <i class="bi bi-journal-medical"></i> Garuda
        </a>
        <a href="<?= $base_url ?>/publikasi/hki.php" class="menu-item <?= $current_page == 'hki' ? 'active' : '' ?>">
            <i class="bi bi-shield-check"></i> HKI
        </a>
        <a href="<?= $base_url ?>/publikasi/buku.php" class="menu-item <?= $current_page == 'buku' ? 'active' : '' ?>">
            <i class="bi bi-book-fill"></i> Buku
        </a>

        <div class="menu-label">Tools</div>
        <a href="<?= $base_url ?>/export/export_excel.php" class="menu-item <?= $current_page == 'export_excel' ? 'active' : '' ?>">
            <i class="bi bi-file-earmark-spreadsheet"></i> Export Excel
        </a>
    </nav>
</aside>

<!-- Main Content -->
<div class="main-content">
    <!-- Top Header -->
    <header class="top-header">
        <div class="d-flex align-items-center gap-3">
            <button class="btn btn-link text-dark d-lg-none p-0" id="sidebarToggle">
                <i class="bi bi-list fs-4"></i>
            </button>
            <h1 class="page-title"><?= isset($page_title) ? $page_title : 'Dashboard' ?></h1>
        </div>
        <div class="header-right">
            <span class="badge-institusi d-none d-md-inline-block">
                <i class="bi bi-building me-1"></i><?= $institusi ?>
            </span>
        </div>
    </header>

    <!-- Content Wrapper -->
    <div class="content-wrapper fade-in">
