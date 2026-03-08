-- Migration: Menambahkan fitur status akun siswa dan tracking pembayaran denda
-- Dijalankan untuk enable/disable siswa yang memiliki denda belum dibayar

-- 1. Tambahkan kolom status ke tb_anggota (AKTIF/NONAKTIF)
ALTER TABLE `tb_anggota` ADD COLUMN `status` ENUM('AKTIF', 'NONAKTIF') DEFAULT 'AKTIF' AFTER `no_hp`;

-- 2. Tambahkan kolom untuk tracking tanggal nonaktif dan alasan
ALTER TABLE `tb_anggota` ADD COLUMN `tgl_nonaktif` DATETIME NULL AFTER `status`;
ALTER TABLE `tb_anggota` ADD COLUMN `alasan_nonaktif` VARCHAR(255) NULL AFTER `tgl_nonaktif`;

-- 3. Buat tabel untuk tracking pembayaran denda (opsional tapi membantu tracking)
CREATE TABLE IF NOT EXISTS `tb_denda` (
  `id_denda` INT(11) NOT NULL AUTO_INCREMENT,
  `id_sk` VARCHAR(10) NOT NULL,
  `id_anggota` VARCHAR(10) NOT NULL,
  `tanggal_denda` DATE NOT NULL,
  `nominal_denda` INT(11) NOT NULL DEFAULT 0,
  `status_bayar` ENUM('BELUM_BAYAR', 'SUDAH_BAYAR', 'PERPANJANGAN') DEFAULT 'BELUM_BAYAR',
  `tanggal_bayar` DATETIME NULL,
  `catatan` VARCHAR(255) NULL,
  `created_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
  `updated_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`id_denda`),
  KEY `id_sk` (`id_sk`),
  KEY `id_anggota` (`id_anggota`),
  KEY `status_bayar` (`status_bayar`),
  FOREIGN KEY (`id_sk`) REFERENCES `tb_sirkulasi` (`id_sk`) ON DELETE CASCADE,
  FOREIGN KEY (`id_anggota`) REFERENCES `tb_anggota` (`id_anggota`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- 4. Tambahkan kolom perpanjangan di tb_sirkulasi (untuk tracking perpanjangan)
ALTER TABLE `tb_sirkulasi` ADD COLUMN `tgl_perpanjangan` DATE NULL AFTER `tgl_kembali`;
ALTER TABLE `tb_sirkulasi` ADD COLUMN `sudah_diperpanjang` INT(1) DEFAULT 0 AFTER `tgl_perpanjangan`;
