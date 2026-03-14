-- ============================================
-- MIGRATION SCRIPT untuk Solusi ID Siswa
-- ============================================
-- 
-- OPSI 1: Ubah format ID dari 3 digit menjadi 4 digit
-- OPSI 2: Tambahkan kolom untuk Soft Delete
-- 
-- Jalankan sesuai kebutuhan:
-- 1. Jika ingin OPSI 1A (4 digit) -> Run OPSI_1A_MIGRATION
-- 2. Jika ingin OPSI 2 (soft delete) -> Run kedua script
-- ============================================

-- ============================================
-- OPSI 1A: CONVERT EXISTING ID ke Format 4 Digit
-- ============================================
-- Ubah A001 menjadi A0001, A999 menjadi A0999, dsb

START TRANSACTION;

-- Backup data lama sebelum konversi
CREATE TABLE IF NOT EXISTS tb_anggota_backup AS SELECT * FROM tb_anggota;

-- Konversi format ID
-- Jika sudah A0001 format, skip
UPDATE tb_anggota
SET id_anggota = CONCAT('A', LPAD(SUBSTRING(id_anggota, 2), 4, '0'))
WHERE id_anggota REGEXP '^A[0-9]{3}$';

-- Verifikasi hasilnya
SELECT COUNT(*) as total_siswa, COUNT(DISTINCT id_anggota) as unique_id FROM tb_anggota;

-- Jika ada duplikat, periksa
SELECT id_anggota, COUNT(*) as count FROM tb_anggota GROUP BY id_anggota HAVING count > 1;

-- ROLLBACK jika terjadi error, atau COMMIT jika berhasil
-- ROLLBACK;
-- COMMIT;


-- ============================================
-- OPSI 2A: TAMBAH KOLOM untuk Soft Delete
-- ============================================
-- Tambahkan kolom status, tanggal lulus, alasan nonaktif

-- 1. Tambah kolom status
ALTER TABLE tb_anggota ADD COLUMN status ENUM('AKTIF', 'LULUS', 'PINDAH', 'NONAKTIF') 
DEFAULT 'AKTIF' AFTER `alamat`;

-- 2. Tambah kolom tanggal lulus
ALTER TABLE tb_anggota ADD COLUMN tgl_lulus DATE NULL AFTER status;

-- 3. Tambah kolom tanggal nonaktif
ALTER TABLE tb_anggota ADD COLUMN tgl_nonaktif DATE NULL AFTER tgl_lulus;

-- 4. Tambah kolom alasan nonaktif
ALTER TABLE tb_anggota ADD COLUMN alasan_nonaktif VARCHAR(255) NULL AFTER tgl_nonaktif;

-- 5. Tambah kolom untuk tracking perubahan
ALTER TABLE tb_anggota ADD COLUMN dibuat_oleh VARCHAR(50) NULL,
                         ADD COLUMN diubah_oleh VARCHAR(50) NULL,
                         ADD COLUMN tgl_ubah TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP;

-- Verifikasi struktur tabel
DESCRIBE tb_anggota;


-- ============================================
-- OPSI 2B: CREATE FUNCTION untuk Soft Delete
-- ============================================

-- Fungsi untuk non-aktifkan siswa (soft delete)
DELIMITER //

CREATE PROCEDURE sp_nonaktifkan_siswa(
    IN p_id_anggota VARCHAR(10),
    IN p_alasan VARCHAR(255),
    IN p_diubah_oleh VARCHAR(50)
)
BEGIN
    DECLARE v_count INT;
    
    -- Check jika siswa ada
    SELECT COUNT(*) INTO v_count FROM tb_anggota WHERE id_anggota = p_id_anggota;
    
    IF v_count = 0 THEN
        SIGNAL SQLSTATE '45000' SET MESSAGE_TEXT = 'Siswa tidak ditemukan';
    END IF;
    
    -- Update status menjadi NONAKTIF
    UPDATE tb_anggota 
    SET status = 'NONAKTIF',
        tgl_nonaktif = NOW(),
        alasan_nonaktif = p_alasan,
        diubah_oleh = p_diubah_oleh
    WHERE id_anggota = p_id_anggota;
    
    -- Log aktivitas
    INSERT INTO tb_log_activity (id_anggota, nama_anggota, tgl_aktivitas, waktu_aktivitas, jenis_aktivitas, id_buku, id_sk, keterangan)
    SELECT 
        id_anggota,
        nama_anggota,
        DATE(NOW()),
        TIME(NOW()),
        'Delete Siswa',
        NULL,
        NULL,
        CONCAT('Siswa non-aktif. Alasan: ', p_alasan)
    FROM tb_anggota
    WHERE id_anggota = p_id_anggota;
    
END //

-- Fungsi untuk restore siswa (undo soft delete)
CREATE PROCEDURE sp_restore_siswa(
    IN p_id_anggota VARCHAR(10),
    IN p_diubah_oleh VARCHAR(50)
)
BEGIN
    DECLARE v_count INT;
    
    -- Check jika siswa ada
    SELECT COUNT(*) INTO v_count FROM tb_anggota WHERE id_anggota = p_id_anggota;
    
    IF v_count = 0 THEN
        SIGNAL SQLSTATE '45000' SET MESSAGE_TEXT = 'Siswa tidak ditemukan';
    END IF;
    
    -- Update status kembali ke AKTIF
    UPDATE tb_anggota 
    SET status = 'AKTIF',
        tgl_lulus = NULL,
        tgl_nonaktif = NULL,
        alasan_nonaktif = NULL,
        diubah_oleh = p_diubah_oleh
    WHERE id_anggota = p_id_anggota;
    
END //

-- Fungsi untuk catat siswa lulus
CREATE PROCEDURE sp_lulusi_siswa(
    IN p_id_anggota VARCHAR(10),
    IN p_diubah_oleh VARCHAR(50)
)
BEGIN
    DECLARE v_count INT;
    
    -- Check jika siswa ada
    SELECT COUNT(*) INTO v_count FROM tb_anggota WHERE id_anggota = p_id_anggota;
    
    IF v_count = 0 THEN
        SIGNAL SQLSTATE '45000' SET MESSAGE_TEXT = 'Siswa tidak ditemukan';
    END IF;
    
    -- Update status menjadi LULUS
    UPDATE tb_anggota 
    SET status = 'LULUS',
        tgl_lulus = DATE(NOW()),
        diubah_oleh = p_diubah_oleh
    WHERE id_anggota = p_id_anggota;
    
END //

DELIMITER ;


-- ============================================
-- OPSI 2C: CREATE INDEX untuk Performance
-- ============================================

-- Index untuk query soft delete
CREATE INDEX idx_status ON tb_anggota(status);
CREATE INDEX idx_id_status ON tb_anggota(id_anggota, status);
CREATE INDEX idx_tgl_lulus ON tb_anggota(tgl_lulus);


-- ============================================
-- VERIFICATION QUERIES
-- ============================================

-- Lihat statistik siswa aktif vs non-aktif
SELECT 
    status,
    COUNT(*) as jumlah_siswa,
    ROUND(COUNT(*) * 100 / (SELECT COUNT(*) FROM tb_anggota), 2) as persentase
FROM tb_anggota
GROUP BY status;

-- Lihat siswa yang sudah lulus (bisa di-reuse ID nya)
SELECT 
    id_anggota,
    nama_anggota,
    tgl_lulus,
    status
FROM tb_anggota
WHERE status IN ('LULUS', 'PINDAH', 'NONAKTIF')
ORDER BY CAST(SUBSTRING(id_anggota, 2) AS UNSIGNED) ASC;

-- Lihat kapasitas ID yang tersisa (untuk format 4 digit A0001-A9999)
SELECT 
    FORMAT((SELECT COUNT(*) FROM tb_anggota WHERE status = 'AKTIF'), 0) as siswa_aktif,
    FORMAT(9999 - (SELECT COUNT(*) FROM tb_anggota WHERE status = 'AKTIF'), 0) as sisa_kapasitas;
