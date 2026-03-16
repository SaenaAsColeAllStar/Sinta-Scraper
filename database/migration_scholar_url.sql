-- =============================================
-- Migration: Tambah scholar_url dan update tabel publikasi_gs
-- Jalankan SQL ini di phpMyAdmin
-- =============================================

USE sinta_monitoring;

-- Tambah kolom scholar_url ke tabel dosen
ALTER TABLE dosen 
    ADD COLUMN scholar_url VARCHAR(500) DEFAULT NULL AFTER sinta_url;

-- Tambah kolom sitasi ke publikasi_gs jika belum ada
ALTER TABLE publikasi_gs 
    ADD COLUMN sitasi INT DEFAULT 0 AFTER tahun;
