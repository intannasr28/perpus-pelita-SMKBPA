<?php
// Statistik Pengunjung Widget
// File ini bisa di-include di dashboard admin.php dan petugas.php

$tipe_stat = isset($_GET['stat_tipe']) ? $_GET['stat_tipe'] : 'bulan';
$bulan_stat = isset($_GET['stat_bulan']) ? (int)$_GET['stat_bulan'] : (int)date('m');
$tahun_stat = isset($_GET['stat_tahun']) ? (int)$_GET['stat_tahun'] : (int)date('Y');

// Array nama bulan dengan index numeric
$bulan_list = array(0 => "", 1 => "Januari", 2 => "Februari", 3 => "Maret", 4 => "April", 5 => "Mei", 6 => "Juni", 7 => "Juli", 8 => "Agustus", 9 => "September", 10 => "Oktober", 11 => "November", 12 => "Desember");

// Array nama bulan dengan index numeric
$bulan_list = array(0 => "", 1 => "Januari", 2 => "Februari", 3 => "Maret", 4 => "April", 5 => "Mei", 6 => "Juni", 7 => "Juli", 8 => "Agustus", 9 => "September", 10 => "Oktober", 11 => "November", 12 => "Desember");

// Ambil data statistik pengunjung berdasarkan filter
if ($tipe_stat == 'bulan') {
	// Filter by Bulan
	$sql_stat = "SELECT 
		DATE(tgl_kunjungan) as tgl,
		COUNT(*) as total_kunjungan,
		COUNT(DISTINCT id_anggota) as unique_pengunjung,
		level
	FROM tb_kunjungan 
	WHERE MONTH(tgl_kunjungan) = '$bulan_stat' AND YEAR(tgl_kunjungan) = '$tahun_stat'
	GROUP BY DATE(tgl_kunjungan), level
	ORDER BY tgl DESC";
	
	$judul = "Statistik Pengunjung " . $bulan_list[$bulan_stat] . " " . $tahun_stat;
	
} elseif ($tipe_stat == 'pekan') {
	// Filter by Pekan - simplified query
	$sql_stat = "SELECT 
		WEEK(tgl_kunjungan, 1) as pekan,
		YEAR(tgl_kunjungan) as tahun_pekan,
		COUNT(*) as total_kunjungan,
		COUNT(DISTINCT id_anggota) as unique_pengunjung,
		level
	FROM tb_kunjungan 
	WHERE YEAR(tgl_kunjungan) = '$tahun_stat'
	GROUP BY YEAR(tgl_kunjungan), WEEK(tgl_kunjungan, 1), level
	ORDER BY YEAR(tgl_kunjungan) DESC, pekan DESC
	LIMIT 10";
	
	$judul = "Statistik Pengunjung Per Pekan - Tahun " . $tahun_stat;
	
} else {
	// Filter by Tahun - simplified query
	$sql_stat = "SELECT 
		MONTH(tgl_kunjungan) as bulan,
		YEAR(tgl_kunjungan) as tahun,
		COUNT(*) as total_kunjungan,
		COUNT(DISTINCT id_anggota) as unique_pengunjung,
		level
	FROM tb_kunjungan 
	WHERE YEAR(tgl_kunjungan) = '$tahun_stat'
	GROUP BY YEAR(tgl_kunjungan), MONTH(tgl_kunjungan), level
	ORDER BY bulan ASC";
	
	$judul = "Statistik Pengunjung Per Bulan - Tahun " . $tahun_stat;
}

$result_stat = @mysqli_query($koneksi, $sql_stat);
$data_stat = array();
$total_kunjungan_keseluruhan = 0;

if ($result_stat && mysqli_num_rows($result_stat) > 0) {
	while ($row = mysqli_fetch_assoc($result_stat)) {
		// Build labels dinamis berdasarkan tipe filter
		if ($tipe_stat == 'pekan') {
			$row['label_pekan'] = 'Pekan ' . $row['pekan'] . ' ' . $row['tahun_pekan'];
		} elseif ($tipe_stat == 'tahun') {
			$bulan_names = array(1 => 'Januari', 2 => 'Februari', 3 => 'Maret', 4 => 'April', 5 => 'Mei', 6 => 'Juni', 
								 7 => 'Juli', 8 => 'Agustus', 9 => 'September', 10 => 'Oktober', 11 => 'November', 12 => 'Desember');
			$row['label_bulan'] = $bulan_names[$row['bulan']] . ' ' . $row['tahun'];
		}
		
		$data_stat[] = $row;
		$total_kunjungan_keseluruhan += $row['total_kunjungan'];
	}
}
?>

<div class="row">
	<div class="col-md-12">
		<div class="box box-success">
			<div class="box-header with-border">
				<h3 class="box-title"><?php echo $judul; ?></h3>
				<div class="box-tools pull-right">
					<button type="button" class="btn btn-box-tool" data-widget="collapse"><i class="fa fa-minus"></i></button>
				</div>
			</div>
			<div class="box-body">
				<!-- Filter Controls -->
				<div class="row" style="margin-bottom: 15px;">
					<div class="col-md-12">
						<form method="GET" action="" class="form-inline">
							<select name="stat_tipe" class="form-control" style="margin-right: 10px;" onchange="this.form.submit();">
								<option value="bulan" <?php echo ($tipe_stat == 'bulan') ? 'selected' : ''; ?>>Per Bulan</option>
								<option value="pekan" <?php echo ($tipe_stat == 'pekan') ? 'selected' : ''; ?>>Per Pekan</option>
								<option value="tahun" <?php echo ($tipe_stat == 'tahun') ? 'selected' : ''; ?>>Per Tahun</option>
							</select>

							<?php if ($tipe_stat == 'bulan'): ?>
								<select name="stat_bulan" class="form-control" style="margin-right: 10px;" onchange="this.form.submit();">
								<?php for ($m = 1; $m <= 12; $m++): $m_formatted = str_pad($m, 2, '0', STR_PAD_LEFT); ?>
									<option value="<?php echo $m_formatted; ?>" <?php echo ($m == $bulan_stat) ? 'selected' : ''; ?>><?php echo $bulan_list[$m]; ?></option>
								<?php endfor; ?>
							</select>
						<?php endif; ?>
							<?php
							$tahun_sekarang = (int)date('Y');
							for ($y = 2020; $y <= ($tahun_sekarang + 1); $y++):
							?>
								<option value="<?php echo $y; ?>" <?php echo ($y == $tahun_stat) ? 'selected' : ''; ?>><?php echo $y; ?></option>
							<?php endfor; ?>
						</select>
						</form>
					</div>
				</div>

				<!-- Summary Stats -->
				<div class="row" style="margin-bottom: 20px;">
					<div class="col-md-3">
						<div class="small-box bg-aqua">
							<div class="inner">
								<h3><?php echo $total_kunjungan_keseluruhan; ?></h3>
								<p>Total Kunjungan</p>
							</div>
							<div class="icon">
								<i class="fa fa-users"></i>
							</div>
						</div>
					</div>
					<div class="col-md-3">
						<div class="small-box bg-green">
							<div class="inner">
								<h3><?php echo count(array_unique(array_column($data_stat, 'unique_pengunjung'))); ?></h3>
								<p>Pengunjung Unik</p>
							</div>
							<div class="icon">
								<i class="fa fa-user"></i>
							</div>
						</div>
					</div>
					<div class="col-md-3">
						<div class="small-box bg-yellow">
							<div class="inner">
								<h3><?php echo count($data_stat); ?></h3>
								<p>Hari/Periode</p>
							</div>
							<div class="icon">
								<i class="fa fa-calendar"></i>
							</div>
						</div>
					</div>
					<div class="col-md-3">
						<div class="small-box bg-red">
							<div class="inner">
								<h3><?php echo ($total_kunjungan_keseluruhan > 0 && count($data_stat) > 0) ? round($total_kunjungan_keseluruhan / count($data_stat), 0) : 0; ?></h3>
								<p>Rata-rata/Hari</p>
							</div>
							<div class="icon">
								<i class="fa fa-bar-chart"></i>
							</div>
						</div>
					</div>
				</div>

				<!-- Data Table -->
				<div class="table-responsive">
					<table class="table table-bordered table-striped table-condensed">
						<thead>
							<tr>
								<th>Tanggal/Periode</th>
								<th>Total Kunjungan</th>
								<th>Pengunjung Unik</th>
								<th>Level</th>
								<th>Persentase</th>
							</tr>
						</thead>
						<tbody>
							<?php if (count($data_stat) > 0): ?>
								<?php foreach ($data_stat as $stat): ?>
									<tr>
										<td>
											<?php 
											if (isset($stat['tgl'])) {
												$date_obj = DateTime::createFromFormat('Y-m-d', $stat['tgl']);
												echo $date_obj->format('d/M/Y');
											} elseif (isset($stat['label_pekan'])) {
												echo $stat['label_pekan'];
											} else {
												echo $stat['label_bulan'];
											}
											?>
										</td>
										<td><strong><?php echo $stat['total_kunjungan']; ?></strong></td>
										<td><?php echo $stat['unique_pengunjung']; ?></td>
										<td>
											<?php 
											$badge_class = '';
											if ($stat['level'] == 'Administrator') $badge_class = 'label-danger';
											elseif ($stat['level'] == 'Petugas') $badge_class = 'label-warning';
											elseif ($stat['level'] == 'Siswa') $badge_class = 'label-success';
											else $badge_class = 'label-default';
											?>
											<span class="label <?php echo $badge_class; ?>"><?php echo $stat['level']; ?></span>
										</td>
										<td>
											<?php 
											$persen = ($total_kunjungan_keseluruhan > 0) ? round(($stat['total_kunjungan'] / $total_kunjungan_keseluruhan) * 100, 2) : 0;
											echo $persen . '%';
											?>
										</td>
									</tr>
								<?php endforeach; ?>
							<?php else: ?>
								<tr>
									<td colspan="5" style="text-align: center; color: #999;">Data tidak tersedia</td>
								</tr>
							<?php endif; ?>
						</tbody>
					</table>
				</div>
			</div>
		</div>
	</div>
</div>
