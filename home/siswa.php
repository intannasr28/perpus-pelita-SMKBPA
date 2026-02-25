<?php
// Pastikan variabel session tersedia dari index.php
$id_siswa = $_SESSION["ses_username"]; 

// Hitung Buku Sedang Dipinjam
$sql_pinjam = $koneksi->query("SELECT COUNT(*) as total FROM tb_sirkulasi WHERE id_anggota='$id_siswa' AND status='PIN'");
$data_pinjam = $sql_pinjam->fetch_assoc();

// Hitung Buku Sudah Kembali
$sql_kembali = $koneksi->query("SELECT COUNT(*) as total FROM tb_sirkulasi WHERE id_anggota='$id_siswa' AND status='KEM'");
$data_kembali = $sql_kembali->fetch_assoc();

// Cari Buku Non-Pelajaran Terpopuler
$sql_populer = $koneksi->query("SELECT b.judul_buku, COUNT(s.id_buku) as jumlah 
                                FROM tb_sirkulasi s 
                                JOIN tb_buku b ON s.id_buku = b.id_buku 
                                GROUP BY s.id_buku ORDER BY jumlah DESC LIMIT 1");
$data_populer = $sql_populer->fetch_assoc();
?>

<section class="content-header">
    <h1>Dashboard Siswa <small><?php echo $data_nama; ?></small></h1>
</section>

<section class="content">
    <div class="row">
        <div class="col-lg-4 col-xs-6">
            <div class="small-box bg-aqua">
                <div class="inner">
                    <h3><?php echo $data_pinjam['total']; ?></h3>
                    <p>Buku Dipinjam</p>
                </div>
                <div class="icon"><i class="fa fa-book"></i></div>
            </div>
        </div>
        <div class="col-lg-4 col-xs-6">
            <div class="small-box bg-green">
                <div class="inner">
                    <h3><?php echo $data_kembali['total']; ?></h3>
                    <p>Buku Dikembalikan</p>
                </div>
                <div class="icon"><i class="fa fa-check"></i></div>
            </div>
        </div>
        <div class="col-lg-4 col-xs-12">
            <div class="small-box bg-yellow">
                <div class="inner">
                    <h4 style="font-weight:bold;"><?php echo $data_populer['judul_buku'] ?? 'Belum ada data'; ?></h4>
                    <p>Buku Terpopuler</p>
                </div>
                <div class="icon"><i class="fa fa-star"></i></div>
            </div>
        </div>
    </div>
</section>