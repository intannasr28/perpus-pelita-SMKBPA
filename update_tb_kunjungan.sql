-- SQL untuk UPDATE schema tb_kunjungan
-- Jalankan SQL ini di database untuk menambah kolom tracking aktivitas

-- Ubah struktur tb_kunjungan untuk track semua aktivitas
ALTER TABLE `tb_kunjungan` 
ADD COLUMN `jenis_aktivitas` ENUM('Login', 'Peminjaman', 'Pengembalian', 'Akses Halaman') DEFAULT 'Login' AFTER `jenis_kunjungan`,
ADD COLUMN `id_buku` VARCHAR(10) AFTER `jenis_aktivitas`,
ADD COLUMN `id_sk` VARCHAR(20) AFTER `id_buku`,
ADD COLUMN `keterangan` VARCHAR(255) AFTER `id_sk`;

-- Tambah index untuk query lebih cepat
CREATE INDEX idx_jenis_aktivitas ON tb_kunjungan(jenis_aktivitas);
CREATE INDEX idx_tgl_aktivitas ON tb_kunjungan(tgl_kunjungan);
CREATE INDEX idx_id_buku ON tb_kunjungan(id_buku);
