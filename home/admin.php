<?php
	$sql = $koneksi->query("SELECT count(id_buku) as buku from tb_buku");
	while ($data= $sql->fetch_assoc()) {
	
		$buku=$data['buku'];
	}
?>

<?php
	$sql = $koneksi->query("SELECT count(id_anggota) as agt from tb_anggota");
	while ($data= $sql->fetch_assoc()) {
	
		$agt=$data['agt'];
	}
?>

<?php
	$sql = $koneksi->query("SELECT count(id_sk) as pin from tb_sirkulasi where status='PIN'");
	while ($data= $sql->fetch_assoc()) {
	
		$pin=$data['pin'];
	}
?>

<?php
	$sql = $koneksi->query("SELECT count(id_sk) as kem from tb_sirkulasi where status='KEM'");
	while ($data= $sql->fetch_assoc()) {
	
		$kem=$data['kem'];
	}
?>

<?php
	$sql = $koneksi->query("SELECT count(id_sk) as kem from tb_sirkulasi where status='KEM'");
	while ($data= $sql->fetch_assoc()) {
	
		$kem=$data['kem'];
	}
?>

<?php
	// Best Peminjam (non-textbook materials like novels)
	$sql_best = $koneksi->query("
		SELECT a.id_anggota, a.nama, COUNT(s.id_sk) as jumlah_pinjam 
		FROM tb_sirkulasi s 
		JOIN tb_buku b ON s.id_buku = b.id_buku 
		JOIN tb_anggota a ON s.id_anggota = a.id_anggota 
		WHERE b.kategori != 'Pelajaran' 
		GROUP BY a.id_anggota, a.nama 
		ORDER BY jumlah_pinjam DESC 
		LIMIT 10
	");
	$best_peminjam = array();
	while ($data = $sql_best->fetch_assoc()) {
		$best_peminjam[] = $data;
	}
?>

<!-- Content Header (Page header) -->
<section class="content-header">
	<h1>
		Dashboard  Administrator
	</h1>
</section>

<!-- Main content -->
<section class="content">
	<!-- Small boxes (Stat box) -->
	<div class="row">

		<div class="col-lg-3 col-xs-6">
			<!-- small box -->
			<div class="small-box bg-blue">
				<div class="inner">
					<h4>
						<?= $buku; ?>
					</h4>

					<p>Buku</p>
				</div>
				<div class="icon">
					<i class="ion ion-stats-bars"></i>
				</div>
				<a href="?page=MyApp/data_buku" class="small-box-footer">More info
					<i class="fa fa-arrow-circle-right"></i>
				</a>
			</div>
		</div>

		<div class="col-lg-3 col-xs-6">
			<!-- small box -->
			<div class="small-box bg-yellow">
				<div class="inner">
					<h4>
						<?= $agt; ?>
					</h4>

					<p>Anggota</p>
				</div>
				<div class="icon">
					<i class="ion ion-person-add"></i>
				</div>
				<a href="?page=MyApp/data_agt" class="small-box-footer">More info
					<i class="fa fa-arrow-circle-right"></i>
				</a>
			</div>
		</div>

		<div class="col-lg-3 col-xs-6">
			<!-- small box -->
			<div class="small-box bg-green">
				<div class="inner">
					<h4>
						<?= $pin; ?>
					</h4>

					<p>Tagihan Berjalan</p>
				</div>
				<div class="icon">
					<i class="ion ion-stats-bars"></i>
				</div>
				<a href="?page=data_sirkul" class="small-box-footer">More info
					<i class="fa fa-arrow-circle-right"></i>
				</a>
			</div>
		</div>

		<div class="col-lg-3 col-xs-6">
			<!-- small box -->
			<div class="small-box bg-red">
				<div class="inner">
					<h4>
						<?= $kem; ?>
					</h4>

					<p>Laporan Sirkulasi</p>
				</div>
				<div class="icon">
					<i class="ion ion-stats-bars"></i>
				</div>
				<a href="?page=log_kembali" class="small-box-footer">More info
					<i class="fa fa-arrow-circle-right"></i>
				</a>
			</div>
		</div>
	</div>
	<!-- /.row -->

	<!-- Best Peminjam Section -->
	<div class="row">
		<div class="col-md-12">
			<div class="box box-success">
				<div class="box-header with-border">
					<h3 class="box-title">
						<i class="fa fa-star"></i> Peminjam Terbaik (Buku Non-Pelajaran)
					</h3>
					<small class="pull-right">Berdasarkan jumlah peminjaman novel, fiksi, dan buku referensi lainnya</small>
				</div>
				<div class="box-body">
					<?php if (count($best_peminjam) > 0): ?>
					<table class="table table-striped table-hover">
						<thead>
							<tr>
								<th style="width: 50px; text-align: center;">Peringkat</th>
								<th>ID Anggota</th>
								<th>Nama</th>
								<th style="width: 150px; text-align: center;">Jumlah Pinjam</th>
							</tr>
						</thead>
						<tbody>
							<?php 
							$rank = 1;
							foreach ($best_peminjam as $peminjam): 
							$badge_color = '';
							if ($rank == 1) $badge_color = 'badge-danger';
							else if ($rank == 2) $badge_color = 'badge-warning';
							else if ($rank == 3) $badge_color = 'badge-info';
							else $badge_color = 'badge-primary';
							?>
							<tr>
								<td style="text-align: center;">
									<span class="badge <?php echo $badge_color; ?>" style="font-size: 14px;"><?php echo $rank; ?></span>
								</td>
								<td><strong><?php echo $peminjam['id_anggota']; ?></strong></td>
								<td><?php echo $peminjam['nama']; ?></td>
								<td style="text-align: center;">
									<span class="badge badge-success"><?php echo $peminjam['jumlah_pinjam']; ?> buku</span>
								</td>
							</tr>
							<?php 
							$rank++;
							endforeach; 
							?>
						</tbody>
					</table>
					<?php else: ?>
					<div class="alert alert-info">
						<i class="fa fa-info-circle"></i> Belum ada peminjaman buku non-pelajaran
					</div>
					<?php endif; ?>
				</div>
			</div>
		</div>
	</div>
	<!-- /.row -->