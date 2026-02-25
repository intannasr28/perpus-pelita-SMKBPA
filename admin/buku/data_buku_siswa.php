<section class="content-header">
	<h1>
		Daftar Buku
		<small>Perpustakaan Pelita</small>
	</h1>
	<ol class="breadcrumb">
		<li>
			<a href="index.php">
				<i class="fa fa-home"></i>
				<b>Dashboard</b>
			</a>
		</li>
	</ol>
</section>

<section class="content">
	<div class="box box-primary">
		<div class="box-header with-border">
			<h3 class="box-title">Silahkan cari buku yang ingin Anda baca</h3>
		</div>
		<div class="box-body">
			<div class="table-responsive">
				<table id="example1" class="table table-bordered table-striped">
					<thead>
						<tr>
							<th>No</th>
							<th>Judul Buku</th>
							<th>Pengarang</th>
							<th>Penerbit</th>
							<th>Tahun</th>
							<th>Aksi</th>
						</tr>
					</thead>
					<tbody>

						<?php
                  $no = 1;
                  // Mengambil data buku dari database
                  $sql = $koneksi->query("SELECT * FROM tb_buku");
                  while ($data= $sql->fetch_assoc()) {
                ?>

						<tr>
							<td>
								<?php echo $no++; ?>
							</td>
							<td>
								<?php echo $data['judul_buku']; ?>
							</td>
							<td>
								<?php echo $data['pengarang']; ?>
							</td>
							<td>
								<?php echo $data['penerbit']; ?>
							</td>
							<td>
								<?php echo $data['th_terbit']; ?>
							</td>

							<td>
								<a href="?page=data_buku_siswa&action=add_fav&id_buku=<?php echo $data['id_buku']; ?>" 
								   title="Tambah ke Favorit" class="btn btn-warning btn-sm">
									<i class="fa fa-star"></i> Favoritkan
								</a>
							</td>
						</tr>

						<?php
                  }
                ?>
					</tbody>
				</table>
			</div>
		</div>
	</div>
</section>

<?php
// Logika untuk menyimpan ke tabel tb_favorit
if (isset($_GET['action']) && $_GET['action'] == 'add_fav') {
    $id_buku = $_GET['id_buku'];
    $id_anggota = $_SESSION["ses_username"]; // NIS Siswa yang login

    // 1. Cek apakah buku ini sudah pernah difavoritkan sebelumnya
    $cek_favorit = $koneksi->query("SELECT * FROM tb_favorit WHERE id_anggota='$id_anggota' AND id_buku='$id_buku'");
    $ketemu = $cek_favorit->num_rows;

    if ($ketemu > 0) {
        // Jika sudah ada
        echo "<script>
        Swal.fire({title: 'Info', text: 'Buku ini sudah ada di daftar favorit Anda', icon: 'info', confirmButtonText: 'OK'
        }).then((result) => { if (result.value) { window.location = 'index.php?page=data_buku_siswa'; } })</script>";
    } else {
        // Jika belum ada, maka simpan
        $sql_simpan_fav = "INSERT INTO tb_favorit (id_anggota, id_buku) VALUES ('$id_anggota', '$id_buku')";
        $query_simpan_fav = mysqli_query($koneksi, $sql_simpan_fav);

        if ($query_simpan_fav) {
            echo "<script>
            Swal.fire({title: 'Berhasil', text: 'Buku berhasil ditambahkan ke favorit', icon: 'success', confirmButtonText: 'OK'
            }).then((result) => { if (result.value) { window.location = 'index.php?page=data_buku_siswa'; } })</script>";
        }
    }
}
?>