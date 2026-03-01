-- MIGRATION: Opsi 2 - Multiple Buku per Peminjaman dengan Detail Table
-- Dari: 1 SK = 1 buku
-- Ke: 1 SK = 1 transaksi, multiple buku di tb_sirkulasi_detail

-- Create tb_sirkulasi_detail (Detail items per transaksi)
CREATE TABLE IF NOT EXISTS `tb_sirkulasi_detail` (
  `id_detail` int NOT NULL AUTO_INCREMENT,
  `id_sk` varchar(20) NOT NULL,
  `id_buku` varchar(10) NOT NULL,
  `jumlah` int NOT NULL DEFAULT 1,
  `status` enum('PIN','KEM') NOT NULL DEFAULT 'PIN',
  PRIMARY KEY (`id_detail`),
  KEY `idx_id_sk` (`id_sk`),
  KEY `idx_id_buku` (`id_buku`),
  CONSTRAINT `fk_detail_sk` FOREIGN KEY (`id_sk`) REFERENCES `tb_sirkulasi` (`id_sk`) ON DELETE CASCADE,
  CONSTRAINT `fk_detail_buku` FOREIGN KEY (`id_buku`) REFERENCES `tb_buku` (`id_buku`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1 COMMENT='Detail buku dalam sekali transaksi peminjaman';

-- Tambah foreign key ke tb_sirkulasi dari tb_anggota
ALTER TABLE tb_sirkulasi 
ADD CONSTRAINT `fk_sk_anggota` FOREIGN KEY (`id_anggota`) REFERENCES `tb_anggota` (`id_anggota`);

-- Tambah index untuk performance
CREATE INDEX idx_status ON tb_sirkulasi(status);
CREATE INDEX idx_tgl_pinjam ON tb_sirkulasi(tgl_pinjam);
CREATE INDEX idx_anggota ON tb_sirkulasi(id_anggota);

-- View untuk kompatibilitas dengan query lama (optional)
-- Berguna jika ada laporan yang query langsung dari tb_sirkulasi
CREATE OR REPLACE VIEW v_sirkulasi_flat AS
SELECT 
  s.id_sk,
  d.id_buku,
  s.id_anggota,
  s.tgl_pinjam,
  s.tgl_kembali,
  COALESCE(d.status, s.status) as status,
  d.jumlah,
  d.id_detail
FROM tb_sirkulasi s
LEFT JOIN tb_sirkulasi_detail d ON s.id_sk = d.id_sk
ORDER BY s.id_sk, d.id_detail;

