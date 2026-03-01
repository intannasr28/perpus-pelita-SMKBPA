<section class="content-header">
	<h1>
		Statistik Pengunjung
		<small style="color: #0073b7; font-weight: 500;">Data Kunjungan Perpustakaan Pelita</small>
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

<!-- Main content -->
<section class="content">
	<div class="box box-primary">
		<div class="box-header with-border">
			<h3 class="box-title">Filter Data</h3>
		</div>
		<div class="box-body">
			<form method="GET" action="index.php" class="form-horizontal">
				<input type="hidden" name="hal" value="statistik_pengunjung">
				<input type="hidden" name="page" value="statistik_pengunjung">
				<div class="form-group">
					<label class="col-sm-2 control-label">Tipe Filter</label>
					<div class="col-sm-3">
						<select name="stat_tipe" class="form-control" onchange="this.form.submit();">
							<option value="bulan" <?php echo (isset($_GET['stat_tipe']) && $_GET['stat_tipe'] == 'bulan') ? 'selected' : ''; ?>>Per Bulan</option>
							<option value="pekan" <?php echo (isset($_GET['stat_tipe']) && $_GET['stat_tipe'] == 'pekan') ? 'selected' : ''; ?>>Per Pekan</option>
							<option value="tahun" <?php echo (isset($_GET['stat_tipe']) && $_GET['stat_tipe'] == 'tahun') ? 'selected' : ''; ?>>Per Tahun</option>
						</select>
					</div>

					<?php if ((isset($_GET['stat_tipe']) && $_GET['stat_tipe'] == 'bulan') || !isset($_GET['stat_tipe'])): ?>
						<label class="col-sm-1 control-label">Bulan</label>
						<div class="col-sm-2">
							<select name="stat_bulan" class="form-control" onchange="this.form.submit();">
								<?php 
								$bulan_names = array(1 => 'Januari', 2 => 'Februari', 3 => 'Maret', 4 => 'April', 5 => 'Mei', 6 => 'Juni', 
													 7 => 'Juli', 8 => 'Agustus', 9 => 'September', 10 => 'Oktober', 11 => 'November', 12 => 'Desember');
								$selected_bulan = isset($_GET['stat_bulan']) ? (int)$_GET['stat_bulan'] : (int)date('m');
								for ($m = 1; $m <= 12; $m++):
									$m_formatted = str_pad($m, 2, '0', STR_PAD_LEFT);
								?>
									<option value="<?php echo $m_formatted; ?>" <?php echo ($m == $selected_bulan) ? 'selected' : ''; ?>><?php echo $bulan_names[$m]; ?></option>
								<?php endfor; ?>
							</select>
						</div>
					<?php endif; ?>

					<label class="col-sm-1 control-label">Tahun</label>
					<div class="col-sm-2">
						<input type="number" name="stat_tahun" class="form-control" 
							   value="<?php echo isset($_GET['stat_tahun']) ? $_GET['stat_tahun'] : date('Y'); ?>" 
							   min="2020" max="<?php echo date('Y') + 1; ?>" onchange="this.form.submit();">
					</div>
				</div>
			</form>
		</div>
	</div>

	<div class="box box-success">
		<div class="box-header with-border">
			<h3 class="box-title">
				<?php 
				$tipe_stat = isset($_GET['stat_tipe']) ? $_GET['stat_tipe'] : 'bulan';
				$tahun_stat = isset($_GET['stat_tahun']) ? $_GET['stat_tahun'] : date('Y');
				
				if ($tipe_stat == 'bulan') {
					$selected_bulan = isset($_GET['stat_bulan']) ? (int)$_GET['stat_bulan'] : (int)date('m');
					$bulan_names = array(1 => 'Januari', 2 => 'Februari', 3 => 'Maret', 4 => 'April', 5 => 'Mei', 6 => 'Juni', 
										 7 => 'Juli', 8 => 'Agustus', 9 => 'September', 10 => 'Oktober', 11 => 'November', 12 => 'Desember');
					echo "Statistik Pengunjung " . $bulan_names[$selected_bulan] . " " . $tahun_stat;
				} elseif ($tipe_stat == 'pekan') {
					echo "Statistik Pengunjung Per Pekan - Tahun " . $tahun_stat;
				} else {
					echo "Statistik Pengunjung Per Bulan - Tahun " . $tahun_stat;
				}
				?>
			</h3>
		</div>
		<div class="box-body">
			<?php
			// Build WHERE clause based on filter
			$tipe_stat = isset($_GET['stat_tipe']) ? $_GET['stat_tipe'] : 'bulan';
			$tahun_stat = isset($_GET['stat_tahun']) ? (int)$_GET['stat_tahun'] : (int)date('Y');
			
			// Build filter criteria untuk queries
			if ($tipe_stat == 'bulan') {
				$bulan_stat = isset($_GET['stat_bulan']) ? (int)$_GET['stat_bulan'] : (int)date('m');
				$where_filter = "WHERE MONTH(tgl_kunjungan) = '$bulan_stat' AND YEAR(tgl_kunjungan) = '$tahun_stat'";
				
				$sql_stat = "SELECT 
					DATE(tgl_kunjungan) as tgl,
					COUNT(*) as total_kunjungan,
					COUNT(DISTINCT id_anggota) as unique_pengunjung,
					level
				FROM tb_kunjungan 
				$where_filter
				GROUP BY DATE(tgl_kunjungan), level
				ORDER BY tgl DESC";
				
			} elseif ($tipe_stat == 'pekan') {
				$where_filter = "WHERE YEAR(tgl_kunjungan) = '$tahun_stat'";
				$sql_stat = "SELECT 
					WEEK(tgl_kunjungan, 1) as pekan,
					YEAR(tgl_kunjungan) as tahun_pekan,
					COUNT(*) as total_kunjungan,
					COUNT(DISTINCT id_anggota) as unique_pengunjung,
					level
				FROM tb_kunjungan 
				$where_filter
				GROUP BY YEAR(tgl_kunjungan), WEEK(tgl_kunjungan, 1), level
				ORDER BY YEAR(tgl_kunjungan) DESC, pekan DESC
				LIMIT 10";
				
			} else {
				$where_filter = "WHERE YEAR(tgl_kunjungan) = '$tahun_stat'";
				$sql_stat = "SELECT 
					MONTH(tgl_kunjungan) as bulan,
					YEAR(tgl_kunjungan) as tahun,
					COUNT(*) as total_kunjungan,
					COUNT(DISTINCT id_anggota) as unique_pengunjung,
					level
				FROM tb_kunjungan 
				$where_filter
				GROUP BY YEAR(tgl_kunjungan), MONTH(tgl_kunjungan), level
				ORDER BY bulan ASC";
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

			// ===== QUERY TERPISAH UNTUK MENGHITUNG UNIQUE PENGUNJUNG GLOBAL =====
			$sql_unique = "SELECT COUNT(DISTINCT id_anggota) as total_unique FROM tb_kunjungan $where_filter";
			$result_unique = @mysqli_query($koneksi, $sql_unique);
			$row_unique = mysqli_fetch_assoc($result_unique);
			$total_unique_pengunjung = $row_unique['total_unique'] ? $row_unique['total_unique'] : 0;

			// ===== QUERY TERPISAH UNTUK MENGHITUNG JUMLAH PERIODE/HARI =====
			if ($tipe_stat == 'bulan') {
				$sql_periode = "SELECT COUNT(DISTINCT DATE(tgl_kunjungan)) as jumlah_periode FROM tb_kunjungan $where_filter";
			} elseif ($tipe_stat == 'pekan') {
				$sql_periode = "SELECT COUNT(DISTINCT WEEK(tgl_kunjungan, 1)) as jumlah_periode FROM tb_kunjungan $where_filter";
			} else {
				$sql_periode = "SELECT COUNT(DISTINCT MONTH(tgl_kunjungan)) as jumlah_periode FROM tb_kunjungan $where_filter";
			}
			$result_periode = @mysqli_query($koneksi, $sql_periode);
			$row_periode = mysqli_fetch_assoc($result_periode);
			$total_periode = $row_periode['jumlah_periode'] ? $row_periode['jumlah_periode'] : 0;
			?>

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
							<h3><?php echo $total_unique_pengunjung; ?></h3>
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
							<h3><?php echo $total_periode; ?></h3>
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
							<h3><?php echo ($total_kunjungan_keseluruhan > 0 && $total_periode > 0) ? round($total_kunjungan_keseluruhan / $total_periode, 0) : 0; ?></h3>
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
				<table class="table table-bordered table-striped">
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
</section>
