-- SQL untuk menambahkan kolom sinopsis ke tabel tb_buku

ALTER TABLE `tb_buku` ADD COLUMN `sinopsis` LONGTEXT NULL AFTER `kategori`;
