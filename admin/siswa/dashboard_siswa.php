<?php
// Pastikan variabel session tersedia dari index.php
$id_siswa = $_SESSION["ses_username"]; 

// Hitung Total Buku Tersedia
$sql_total = $koneksi->query("SELECT COUNT(id_buku) as total FROM tb_buku");
$data_total = $sql_total->fetch_assoc();

// Hitung Buku Sedang Dipinjam
$sql_pinjam = $koneksi->query("SELECT COUNT(*) as total FROM tb_sirkulasi WHERE id_anggota='$id_siswa' AND status='PIN'");
$data_pinjam = $sql_pinjam->fetch_assoc();

// Hitung Buku Sudah Kembali
$sql_kembali = $koneksi->query("SELECT COUNT(*) as total FROM tb_sirkulasi WHERE id_anggota='$id_siswa' AND status='KEM'");
$data_kembali = $sql_kembali->fetch_assoc();

// Hitung Buku Favorit
$sql_favorit = $koneksi->query("SELECT COUNT(id_favorit) as total FROM tb_favorit WHERE id_anggota='$id_siswa'");
$data_favorit = $sql_favorit->fetch_assoc();
?>

<section class="content-header">
    <h1>
        Dashboard Siswa
        <small style="color: #0073b7; font-weight: 500;"><?php echo $data_nama; ?></small>
    </h1>
</section>

<section class="content">
    <div class="row">
        <!-- Total Buku -->
        <div class="col-lg-3 col-xs-6">
            <div class="small-box bg-blue">
                <div class="inner">
                    <h3><?php echo $data_total['total']; ?></h3>
                    <p>Total Buku</p>
                </div>
                <div class="icon">
                    <i class="fa fa-book"></i>
                </div>
                <a href="?page=siswa/data_buku_siswa" class="small-box-footer">
                    Lihat Selengkapnya <i class="fa fa-arrow-circle-right"></i>
                </a>
            </div>
        </div>

        <!-- Buku Sedang Dipinjam -->
        <div class="col-lg-3 col-xs-6">
            <div class="small-box bg-aqua">
                <div class="inner">
                    <h3><?php echo $data_pinjam['total']; ?></h3>
                    <p>Buku Dipinjam</p>
                </div>
                <div class="icon">
                    <i class="fa fa-arrow-circle-o-down"></i>
                </div>
            </div>
        </div>

        <!-- Buku Sudah Kembali -->
        <div class="col-lg-3 col-xs-6">
            <div class="small-box bg-green">
                <div class="inner">
                    <h3><?php echo $data_kembali['total']; ?></h3>
                    <p>Buku Dikembalikan</p>
                </div>
                <div class="icon">
                    <i class="fa fa-check-circle"></i>
                </div>
            </div>
        </div>

        <!-- Buku Favorit -->
        <div class="col-lg-3 col-xs-6">
            <div class="small-box bg-red">
                <div class="inner">
                    <h3><?php echo $data_favorit['total']; ?></h3>
                    <p>Buku Favorit</p>
                </div>
                <div class="icon">
                    <i class="fa fa-heart"></i>
                </div>
                <a href="?page=siswa/data_favorit_siswa" class="small-box-footer">
                    Lihat Selengkapnya <i class="fa fa-arrow-circle-right"></i>
                </a>
            </div>
        </div>
    </div>

    <!-- Welcoming Section -->
    <div class="row">
        <div class="col-md-12">
            <div class="box box-primary">
                <div class="box-header with-border">
                    <h3 class="box-title">Selamat Datang, <?php echo $data_nama; ?>!</h3>
                </div>
                <div class="box-body">
                    <p>Anda masuk sebagai <strong><?php echo $data_level; ?></strong> di Sistem Informasi Perpustakaan Pelita.</p>
                    <p>Gunakan menu di samping untuk:</p>
                    <ul>
                        <li>Melihat daftar lengkap buku perpustakaan</li>
                        <li>Mengelola buku favorit Anda</li>
                        <li>Melihat riwayat peminjaman dan pengembalian</li>
                        <li>Mengaturan profil dan password Anda</li>
                    </ul>
                </div>
            </div>
        </div>
    </div>
</section>
