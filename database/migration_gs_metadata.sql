-- =============================================
-- Migration: Tambah kolom metadata jurnal ke publikasi_gs
-- Jalankan SQL ini di phpMyAdmin
-- =============================================

USE sinta_monitoring;

-- Tambah kolom metadata jurnal
ALTER TABLE publikasi_gs 
    ADD COLUMN jilid VARCHAR(50) DEFAULT NULL AFTER nama_jurnal,
    ADD COLUMN terbitan VARCHAR(50) DEFAULT NULL AFTER jilid,
    ADD COLUMN halaman VARCHAR(100) DEFAULT NULL AFTER terbitan;
