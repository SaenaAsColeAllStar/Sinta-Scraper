-- =============================================
-- Migration: Anti-Duplicate + updated_at
-- Jalankan SQL ini di phpMyAdmin
-- =============================================

USE sinta_monitoring;

-- ============ 1. PUBLIKASI GOOGLE SCHOLAR ============
ALTER TABLE publikasi_gs 
    ADD COLUMN judul_hash VARCHAR(64) DEFAULT NULL AFTER judul,
    ADD COLUMN updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP;

UPDATE publikasi_gs SET judul_hash = MD5(judul) WHERE judul_hash IS NULL;

ALTER TABLE publikasi_gs 
    ADD UNIQUE INDEX uq_gs_dosen_judul (dosen_id, judul_hash);

-- ============ 2. PUBLIKASI SCOPUS ============
ALTER TABLE publikasi_scopus 
    ADD COLUMN judul_hash VARCHAR(64) DEFAULT NULL AFTER judul,
    ADD COLUMN updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP;

UPDATE publikasi_scopus SET judul_hash = MD5(judul) WHERE judul_hash IS NULL;

ALTER TABLE publikasi_scopus 
    ADD UNIQUE INDEX uq_scopus_dosen_judul (dosen_id, judul_hash);

-- ============ 3. PUBLIKASI GARUDA ============
ALTER TABLE publikasi_garuda 
    ADD COLUMN judul_hash VARCHAR(64) DEFAULT NULL AFTER judul,
    ADD COLUMN updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP;

UPDATE publikasi_garuda SET judul_hash = MD5(judul) WHERE judul_hash IS NULL;

ALTER TABLE publikasi_garuda 
    ADD UNIQUE INDEX uq_garuda_dosen_judul (dosen_id, judul_hash);

-- ============ 4. HKI ============
ALTER TABLE hki 
    ADD COLUMN judul_hash VARCHAR(64) DEFAULT NULL AFTER judul,
    ADD COLUMN updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP;

UPDATE hki SET judul_hash = MD5(judul) WHERE judul_hash IS NULL;

ALTER TABLE hki 
    ADD UNIQUE INDEX uq_hki_dosen_judul (dosen_id, judul_hash);

-- ============ 5. BUKU ============
ALTER TABLE buku 
    ADD COLUMN judul_hash VARCHAR(64) DEFAULT NULL AFTER judul,
    ADD COLUMN updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP;

UPDATE buku SET judul_hash = MD5(judul) WHERE judul_hash IS NULL;

ALTER TABLE buku 
    ADD UNIQUE INDEX uq_buku_dosen_judul (dosen_id, judul_hash);
