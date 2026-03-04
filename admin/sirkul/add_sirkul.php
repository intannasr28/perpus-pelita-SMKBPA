<?php
//kode 9 digit
  
$carikode = mysqli_query($koneksi,"SELECT id_sk FROM tb_sirkulasi order by id_sk desc");
$datakode = mysqli_fetch_array($carikode);
$kode = ($datakode && isset($datakode['id_sk'])) ? $datakode['id_sk'] : 'S000';
$urut = substr($kode, 1, 3);
$tambah = (int) $urut + 1;

if (strlen($tambah) == 1){
	$format = "S"."00".$tambah;
} else if (strlen($tambah) == 2){
	$format = "S"."0".$tambah;
} else if (strlen($tambah) == 3){
	$format = "S".$tambah;
}
?>

<section class="content-header">
	<h1>
		Sirkulasi-Buku

	</h1>
	<ol class="breadcrumb">
		<li>
			<a href="index.php">
				<i class="fa fa-home"></i>
				<b>Kembali</b>
			</a>
		</li>
	</ol>
</section>

<section class="content">
	<div class="row">
		<div class="col-md-12">
			<!-- general form elements -->
			<div class="box box-info">
				<div class="box-header with-border">
					<h3 class="box-title">Tambah Peminjaman</h3>
					<div class="box-tools pull-right">
						<button type="button" class="btn btn-box-tool" data-widget="collapse">
							<i class="fa fa-minus"></i>
						</button>
						<button type="button" class="btn btn-box-tool" data-widget="remove">
							<i class="fa fa-remove"></i>
						</button>
					</div>
				</div>
				<!-- /.box-header -->
				<!-- form start -->
				<form action="" method="post" enctype="multipart/form-data">
					<div class="box-body">

						<div class="form-group">
							<label>Nama Peminjam</label>
							<select name="id_anggota" id="id_anggota" class="form-control select2" style="width: 100%;">
								<option selected="selected">-- Pilih --</option>
								<?php
								// ambil data dari database
								$query = "select * from tb_anggota";
								$hasil = mysqli_query($koneksi, $query);
								while ($row = mysqli_fetch_array($hasil)) {
								?>
								<option value="<?php echo $row['id_anggota'] ?>">
									<?php echo $row['id_anggota'] ?>
									-
									<?php echo $row['nama'] ?>
								</option>
								<?php
								}
								?>
							</select>
						</div>

						<div class="form-group">
							<label>Buku <span class="text-danger">*</span></label>
							<select name="id_buku" id="id_buku" class="form-control select2" style="width: 100%;" required onchange="checkStok()">
								<option selected="selected" value="">-- Pilih Buku --</option>
								<?php
								// ambil data dari database dengan tampilkan stok dan kategori
								$query = "select id_buku, judul_buku, stok, kategori from tb_buku order by judul_buku asc";
								$hasil = mysqli_query($koneksi, $query);
								while ($row = mysqli_fetch_array($hasil)) {
									$stok_status = $row['stok'] > 0 ? '<span style="color:green">('.$row['stok'].' stok)</span>' : '<span style="color:red">(Stok Habis)</span>';
									$kategori = isset($row['kategori']) ? $row['kategori'] : 'Pelajaran';
								?>
								<option value="<?php echo $row['id_buku'] ?>" data-stok="<?php echo $row['stok'] ?>" data-kategori="<?php echo $kategori ?>">
									<?php echo $row['id_buku'] ?> - <?php echo $row['judul_buku'] ?> <?php echo $stok_status ?>
								</option>
								<?php
								}
								?>
							</select>
							<small id="stok_info" class="form-text text-muted" style="margin-top: 5px;"></small>
							<small id="kategori_info" class="form-text text-muted" style="margin-top: 5px;"></small>
						</div>

						<div class="form-group">
							<label>Jumlah Buku <span class="text-danger">*</span></label>
							<input type="number" name="jumlah" id="jumlah" class="form-control" min="1" max="99" value="1" required onchange="checkStok()">
							<small id="jumlah_info" class="form-text text-muted" style="margin-top: 5px;"></small>
						</div>

						<div class="form-group">
							<label>Tgl Pinjam</label>
							<input type="date" name="tgl_pinjam" id="tgl_pinjam" class="form-control" />
						</div>

					</div>
					<!-- /.box-body -->

					<div class="box-footer">
						<input type="submit" name="Simpan" value="Simpan" class="btn btn-info">
						<a href="?page=data_sirkul" class="btn btn-warning">Batal</a>
					</div>
				</form>
			</div>
			<!-- /.box -->
</section>

<?php

    if (isset ($_POST['Simpan'])){

		//menangkap post tgl pinjam
		$tgl_p=$_POST['tgl_pinjam'];
		//membuat tgl kembali
		$tgl_k=date('Y-m-d', strtotime('+7 days', strtotime($tgl_p)));
		$tgl_hk=date('Y-m-d');
		$id_buku = $_POST['id_buku'];
		$jumlah = (int)$_POST['jumlah'];
		
		// Validasi jumlah
		if ($jumlah <= 0 || $jumlah > 99) {
			echo "<script>
			Swal.fire({title: 'Error', text: 'Jumlah buku tidak valid', icon: 'error', confirmButtonText: 'OK'
			}).then((result) => {
				if (result.value) {
					window.location = 'index.php?page=add_sirkul';
				}
			})</script>";
			mysqli_close($koneksi);
			exit;
		}
		
		// Step 1: Check stok sebelum pinjam
		$cek_stok = mysqli_query($koneksi, "SELECT stok FROM tb_buku WHERE id_buku='$id_buku'");
		$row_stok = mysqli_fetch_array($cek_stok);
		
		$stok_tersedia = (!$row_stok) ? 0 : $row_stok['stok'];
		
		if (!$row_stok || $row_stok['stok'] < $jumlah) {
			echo "<script>
			Swal.fire({title: 'Peminjaman Gagal', text: 'Stok buku tidak cukup. Stok tersedia: $stok_tersedia', icon: 'error', confirmButtonText: 'OK'
			}).then((result) => {
				if (result.value) {
					window.location = 'index.php?page=add_sirkul';
				}
			})</script>";
			mysqli_close($koneksi);
			exit;
		}
		
		// Get last id_sk untuk generate ID
		$carikode = mysqli_query($koneksi, "SELECT id_sk FROM tb_sirkulasi ORDER BY id_sk DESC LIMIT 1");
		$datakode = mysqli_fetch_array($carikode);
		$kode = $datakode['id_sk'];
		$urut = substr($kode, 1, 3);
		$urut = (int)$urut;
		
		// OPSI 2: 1 SK = 1 Transaksi, berapapun qty
		$tambah = $urut + 1;
		$id_sk_new = 'S' . str_pad($tambah, 3, '0', STR_PAD_LEFT);
		
		// Insert peminjaman sekali saja (Master Transaksi)
		$sql_simpan = "INSERT INTO tb_sirkulasi (id_sk,id_buku,id_anggota,tgl_pinjam,status,tgl_kembali) VALUES (
		   '$id_sk_new',
		  '$id_buku',
		  '".$_POST['id_anggota']."',
		  '$tgl_p',
		  'PIN',
		  '$tgl_k');";
		
		// Insert detail buku dengan quantity
		$sql_simpan .= "INSERT INTO tb_sirkulasi_detail (id_sk, id_buku, jumlah, status) 
					   VALUES ('$id_sk_new', '$id_buku', $jumlah, 'PIN');";
		
		// Log peminjaman
		$sql_simpan .= "INSERT INTO log_pinjam (id_buku,id_anggota,tgl_pinjam) VALUES (
			'$id_buku',
			'".$_POST['id_anggota']."',
			'$tgl_p');";
		
		// Log aktivitas peminjaman ke tb_kunjungan
		$id_anggota = $_POST['id_anggota'];
		$nama_anggota = mysqli_fetch_assoc(mysqli_query($koneksi, "SELECT nama FROM tb_anggota WHERE id_anggota='$id_anggota'"))['nama'];
		$petugas_id = $_SESSION['ses_username'];
		
		// Log peminjaman ke tb_kunjungan menggunakan NOW() untuk konsistensi timezone
		$sql_simpan .= "INSERT INTO tb_kunjungan (id_anggota, nama, level, tgl_kunjungan, waktu_kunjungan, jenis_aktivitas, id_buku, id_sk, keterangan) 
					   VALUES ('$id_anggota', '$nama_anggota', 'Siswa', DATE(NOW()), TIME(NOW()), 'Peminjaman', '$id_buku', '$id_sk_new', 'Input oleh $petugas_id');";
		
		// Decrement stok sebanyak jumlah yang dipinjam
		$sql_simpan .= "UPDATE tb_buku SET stok = stok - $jumlah WHERE id_buku='$id_buku';";
		
		$query_simpan = mysqli_multi_query($koneksi, $sql_simpan);
		
		mysqli_close($koneksi);

        if ($query_simpan) {
            echo "<script>
            Swal.fire({title: 'Tambah Data Berhasil',text: 'Berhasil meminjamkan $jumlah buku. Stok buku berkurang $jumlah',icon: 'success',confirmButtonText: 'OK'
            }).then((result) => {
                if (result.value) {
                    window.location = 'index.php?page=data_sirkul';
                }
            })</script>";
        } else {
            echo "<script>
            Swal.fire({title: 'Tambah Data Gagal',text: 'Terjadi kesalahan saat memproses peminjaman',icon: 'error',confirmButtonText: 'OK'
            }).then((result) => {
                if (result.value) {
                    window.location = 'index.php?page=add_sirkul';
                }
            })</script>";
        }
  }
    
?>

<script>
// Check stok saat select buku
function checkStok() {
	var select = document.getElementById('id_buku');
	var selectedOption = select.options[select.selectedIndex];
	var stok = selectedOption.getAttribute('data-stok');
	var kategori = selectedOption.getAttribute('data-kategori') || 'Pelajaran';
	var jumlah = parseInt(document.getElementById('jumlah').value) || 1;
	var stokInfo = document.getElementById('stok_info');
	var kategoriInfo = document.getElementById('kategori_info');
	var jumlahInfo = document.getElementById('jumlah_info');
	
	if (stok === null || stok === '') {
		stokInfo.innerHTML = '';
		kategoriInfo.innerHTML = '';
		jumlahInfo.innerHTML = '';
		return;
	}
	
	stok = parseInt(stok);
	document.getElementById('jumlah').max = stok;
	
	// Display kategori
	var badge_class = 'label-default';
	if (kategori === 'Pelajaran') badge_class = 'label-info';
	else if (kategori === 'Fiksi') badge_class = 'label-success';
	else if (kategori === 'Non Fiksi') badge_class = 'label-warning';
	else if (kategori === 'Referensi') badge_class = 'label-primary';
	else if (kategori === 'Komik') badge_class = 'label-danger';
	
	kategoriInfo.innerHTML = '<strong>Kategori: <span class="label ' + badge_class + '">' + kategori + '</span></strong>';
	
	if (stok > 0) {
		stokInfo.innerHTML = '<strong style="color: green;">✓ Stok tersedia: ' + stok + ' buku</strong>';
		
		if (jumlah > stok) {
			jumlahInfo.innerHTML = '<strong style="color: red;">✗ Jumlah melebihi stok! Maksimal: ' + stok + '</strong>';
			document.querySelector('input[name="Simpan"]').disabled = true;
		} else {
			jumlahInfo.innerHTML = '<strong style="color: green;">✓ Akan meminjam ' + jumlah + ' buku</strong>';
			document.querySelector('input[name="Simpan"]').disabled = false;
		}
	} else {
		stokInfo.innerHTML = '<strong style="color: red;">✗ Stok habis!</strong>';
		document.querySelector('input[name="Simpan"]').disabled = true;
	}
}

// Validate saat jumlah berubah
document.addEventListener('DOMContentLoaded', function() {
	if (document.getElementById('jumlah')) {
		document.getElementById('jumlah').addEventListener('change', checkStok);
		document.getElementById('jumlah').addEventListener('input', checkStok);
	}
});
</script>