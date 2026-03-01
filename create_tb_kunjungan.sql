-- SQL untuk CREATE TABLE tb_kunjungan (jalankan di database)
-- Untuk tracking pengunjung/siswa yang mengakses perpustakaan

CREATE TABLE IF NOT EXISTS `tb_kunjungan` (
  `id_kunjungan` int(11) NOT NULL AUTO_INCREMENT,
  `id_anggota` varchar(10) NOT NULL,
  `nama` varchar(50) NOT NULL,
  `level` varchar(20) NOT NULL,
  `tgl_kunjungan` date NOT NULL,
  `waktu_kunjungan` time NOT NULL,
  `jenis_kunjungan` enum('Login','Akses') DEFAULT 'Akses',
  `created_at` timestamp DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id_kunjungan`),
  KEY `id_anggota` (`id_anggota`),
  KEY `tgl_kunjungan` (`tgl_kunjungan`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

-- Ensure tb_buku has kategori field
ALTER TABLE `tb_buku` ADD COLUMN `kategori` VARCHAR(20) DEFAULT 'Pelajaran' AFTER `stok`;

-- Ensure tb_favorit exists
ALTER TABLE `tb_favorit` ADD CONSTRAINT `tb_favorit_ibfk_1` FOREIGN KEY (`id_anggota`) REFERENCES `tb_anggota` (`id_anggota`) ON DELETE CASCADE ON UPDATE CASCADE;
ALTER TABLE `tb_favorit` ADD CONSTRAINT `tb_favorit_ibfk_2` FOREIGN KEY (`id_buku`) REFERENCES `tb_buku` (`id_buku`) ON DELETE CASCADE ON UPDATE CASCADE;
