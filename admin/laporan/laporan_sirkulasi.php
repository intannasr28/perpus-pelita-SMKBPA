<section class="content-header">
	<h1>
		Laporan Sirkulasi
		<small style="color: #0073b7; font-weight: 500;">Daftar tagihan denda</small>
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
			<form method="POST" action="" class="form-horizontal">
				<div class="form-group">
					<label class="col-sm-2 control-label">Tipe Filter</label>
					<div class="col-sm-4">
						<select name="tipe_filter" class="form-control" id="tipe_filter" onchange="document.getElementById('filter_form').submit();">
							<option value="">-- Semua Data --</option>
							<option value="bulan" <?php echo (isset($_POST['tipe_filter']) && $_POST['tipe_filter'] == 'bulan') ? 'selected' : ''; ?>>Per Bulan</option>
							<option value="pekan" <?php echo (isset($_POST['tipe_filter']) && $_POST['tipe_filter'] == 'pekan') ? 'selected' : ''; ?>>Per Pekan</option>
							<option value="tahun" <?php echo (isset($_POST['tipe_filter']) && $_POST['tipe_filter'] == 'tahun') ? 'selected' : ''; ?>>Per Tahun</option>
							<option value="range" <?php echo (isset($_POST['tipe_filter']) && $_POST['tipe_filter'] == 'range') ? 'selected' : ''; ?>>Range Tanggal</option>
						</select>
					</div>
				</div>

				<?php 
				$tipe_filter = isset($_POST['tipe_filter']) ? $_POST['tipe_filter'] : '';
				if ($tipe_filter == 'bulan'):
				?>
				<div class="form-group">
					<label class="col-sm-2 control-label">Bulan</label>
					<div class="col-sm-2">
						<select name="filter_bulan" class="form-control" required>
							<option value="">-- Pilih Bulan --</option>
							<?php
							$bulan_list = array(
								'01' => 'Januari', '02' => 'Februari', '03' => 'Maret',
								'04' => 'April', '05' => 'Mei', '06' => 'Juni',
								'07' => 'Juli', '08' => 'Agustus', '09' => 'September',
								'10' => 'Oktober', '11' => 'November', '12' => 'Desember'
							);
							$selected_bulan = isset($_POST['filter_bulan']) ? $_POST['filter_bulan'] : '';
							foreach ($bulan_list as $key => $bulan):
							?>
								<option value="<?php echo $key; ?>" <?php echo ($selected_bulan == $key) ? 'selected' : ''; ?>><?php echo $bulan; ?></option>
							<?php endforeach; ?>
						</select>
					</div>
					<label class="col-sm-2 control-label">Tahun</label>
					<div class="col-sm-2">
						<select name="filter_tahun" class="form-control" required>
							<option value="">-- Pilih Tahun --</option>
							<?php
							$current_year = date('Y');
							$selected_tahun = isset($_POST['filter_tahun']) ? $_POST['filter_tahun'] : $current_year;
							for ($y = 2020; $y <= $current_year; $y++):
							?>
								<option value="<?php echo $y; ?>" <?php echo ($selected_tahun == $y) ? 'selected' : ''; ?>><?php echo $y; ?></option>
							<?php endfor; ?>
						</select>
					</div>
					<button type="submit" class="btn btn-primary"><i class="fa fa-filter"></i> Filter</button>
				</div>
				<?php elseif ($tipe_filter == 'pekan'): ?>
				<div class="form-group">
					<label class="col-sm-2 control-label">Pekan</label>
					<div class="col-sm-2">
						<select name="filter_pekan" class="form-control" required>
							<option value="">-- Pilih Pekan --</option>
							<?php
							$selected_pekan = isset($_POST['filter_pekan']) ? $_POST['filter_pekan'] : '';
							for ($w = 1; $w <= 52; $w++):
							?>
								<option value="<?php echo $w; ?>" <?php echo ($selected_pekan == $w) ? 'selected' : ''; ?>>Pekan <?php echo $w; ?></option>
							<?php endfor; ?>
						</select>
					</div>
					<label class="col-sm-2 control-label">Tahun</label>
					<div class="col-sm-2">
						<select name="filter_tahun" class="form-control" required>
							<option value="">-- Pilih Tahun --</option>
							<?php
							$current_year = date('Y');
							$selected_tahun = isset($_POST['filter_tahun']) ? $_POST['filter_tahun'] : $current_year;
							for ($y = 2020; $y <= $current_year; $y++):
							?>
								<option value="<?php echo $y; ?>" <?php echo ($selected_tahun == $y) ? 'selected' : ''; ?>><?php echo $y; ?></option>
							<?php endfor; ?>
						</select>
					</div>
					<button type="submit" class="btn btn-primary"><i class="fa fa-filter"></i> Filter</button>
				</div>
				<?php elseif ($tipe_filter == 'tahun'): ?>
				<div class="form-group">
					<label class="col-sm-2 control-label">Tahun</label>
					<div class="col-sm-2">
						<select name="filter_tahun" class="form-control" required>
							<option value="">-- Pilih Tahun --</option>
							<?php
							$current_year = date('Y');
							$selected_tahun = isset($_POST['filter_tahun']) ? $_POST['filter_tahun'] : $current_year;
							for ($y = 2020; $y <= $current_year; $y++):
							?>
								<option value="<?php echo $y; ?>" <?php echo ($selected_tahun == $y) ? 'selected' : ''; ?>><?php echo $y; ?></option>
							<?php endfor; ?>
						</select>
					</div>
					<button type="submit" class="btn btn-primary"><i class="fa fa-filter"></i> Filter</button>
				</div>
				<?php elseif ($tipe_filter == 'range'): ?>
				<div class="form-group">
					<label class="col-sm-2 control-label">Range Tanggal</label>
					<div class="col-sm-2">
						<input type="date" name="filter_dari" class="form-control" value="<?php echo isset($_POST['filter_dari']) ? $_POST['filter_dari'] : ''; ?>" required>
					</div>
					<div class="col-sm-2">
						<input type="date" name="filter_sampai" class="form-control" value="<?php echo isset($_POST['filter_sampai']) ? $_POST['filter_sampai'] : ''; ?>" required>
					</div>
					<button type="submit" class="btn btn-primary"><i class="fa fa-filter"></i> Filter</button>
				</div>
				<?php endif; ?>
			</form>
		</div>
		<form id="filter_form" method="POST" style="display:none;"></form>
	</div>

	<div class="box box-primary">
		<div class="box-header with-border">
		<a href="?page=MyApp/print_laporan" title="Print" class="btn btn-success" stlye="color : green;">
				<i class="glyphicon glyphicon-print"></i>Print</a>
		</div>
		<!-- /.box-header -->

		
		<div class="box-body">
			<div class="table-responsive">
			
				<table id="example1" class="table table-bordered table-striped">
					<thead>
						<tr>
							<th>No</th>
							<th>ID SKL</th>
							<th>Buku</th>
							<th>Peminjam</th>
							<th>Tgl Pinjam</th>
							<th>Jatuh Tempo</th>
							<th>Denda</th>
							
						</tr>
					</thead>
				<tbody>

				<?php
			// Build WHERE clause based on filter
			// Menampilkan: Buku yang sudah dikembalikan terlambat OR buku yang belum dikembalikan tapi sudah lewat jatuh tempo
			$where_clause = "WHERE (
				(tb_sirkulasi.status='KEM' AND DATEDIFF(tb_sirkulasi.tgl_kembali, DATE_ADD(tb_sirkulasi.tgl_pinjam, INTERVAL 7 DAY)) > 0)
				OR (tb_sirkulasi.status='PIN' AND CURDATE() > DATE_ADD(tb_sirkulasi.tgl_pinjam, INTERVAL 7 DAY))
			)";
			$tipe_filter = isset($_POST['tipe_filter']) ? $_POST['tipe_filter'] : '';

			if ($tipe_filter == 'bulan') {
				$bulan = isset($_POST['filter_bulan']) ? $_POST['filter_bulan'] : '';
				$tahun = isset($_POST['filter_tahun']) ? $_POST['filter_tahun'] : '';
				if ($bulan && $tahun) {
					$where_clause .= " AND MONTH(tb_sirkulasi.tgl_pinjam) = '$bulan' AND YEAR(tb_sirkulasi.tgl_pinjam) = '$tahun'";
				}
			} elseif ($tipe_filter == 'pekan') {
				$pekan = isset($_POST['filter_pekan']) ? $_POST['filter_pekan'] : '';
				$tahun = isset($_POST['filter_tahun']) ? $_POST['filter_tahun'] : '';
				if ($pekan && $tahun) {
					$where_clause .= " AND WEEK(tb_sirkulasi.tgl_pinjam) = '$pekan' AND YEAR(tb_sirkulasi.tgl_pinjam) = '$tahun'";
				}
			} elseif ($tipe_filter == 'tahun') {
				$tahun = isset($_POST['filter_tahun']) ? $_POST['filter_tahun'] : '';
				if ($tahun) {
					$where_clause .= " AND YEAR(tb_sirkulasi.tgl_pinjam) = '$tahun'";
				}
			} elseif ($tipe_filter == 'range') {
				$dari = isset($_POST['filter_dari']) ? $_POST['filter_dari'] : '';
				$sampai = isset($_POST['filter_sampai']) ? $_POST['filter_sampai'] : '';
				if ($dari && $sampai) {
					$where_clause .= " AND tb_sirkulasi.tgl_pinjam >= '$dari' AND tb_sirkulasi.tgl_pinjam <= '$sampai'";
				}
			}

			$sql = mysqli_query($koneksi, "SELECT tb_sirkulasi.id_buku, 
			tb_buku.judul_buku, 
			tb_anggota.id_anggota,
			tb_anggota.nama,
			tb_sirkulasi.id_sk,
			tb_sirkulasi.tgl_pinjam,
			tb_sirkulasi.tgl_kembali,
			tb_sirkulasi.status,
                  	IF(tb_sirkulasi.status='KEM',
			  	IF(DATEDIFF(tb_sirkulasi.tgl_kembali, DATE_ADD(tb_sirkulasi.tgl_pinjam, INTERVAL 7 DAY))<=0, 0, DATEDIFF(tb_sirkulasi.tgl_kembali, DATE_ADD(tb_sirkulasi.tgl_pinjam, INTERVAL 7 DAY))),
			  	IF(DATEDIFF(CURDATE(), DATE_ADD(tb_sirkulasi.tgl_pinjam, INTERVAL 7 DAY))<=0, 0, DATEDIFF(CURDATE(), DATE_ADD(tb_sirkulasi.tgl_pinjam, INTERVAL 7 DAY)))
			  	) AS telat_pengembalian 
			FROM tb_sirkulasi 
			JOIN tb_anggota ON tb_anggota.id_anggota=tb_sirkulasi.id_anggota 
			JOIN tb_buku ON tb_buku.id_buku=tb_sirkulasi.id_buku $where_clause
			Order By tb_sirkulasi.tgl_pinjam DESC");

			$no = 0;
			$total_denda = 0;
			$tarif_denda = 1000;
            while ($data = mysqli_fetch_array($sql, MYSQLI_ASSOC)) {
					$no++;
					$total_denda=$total_denda+($data['telat_pengembalian']*$tarif_denda);
					// Jika belum dikembalikan, gunakan jatuh tempo (tgl_pinjam + 7 hari)
					$tgl_jatuh_tempo = $data['status'] == 'PIN' ? date('d/M/Y', strtotime($data['tgl_pinjam'] . ' +7 days')) : date_format(new DateTime($data['tgl_kembali']),'d/M/Y');
					$status_badge = $data['status'] == 'PIN' ? '<span class="label label-warning">BELUM DIKEMBALIKAN</span>' : '<span class="label label-success">DIKEMBALIKAN TERLAMBAT</span>';
					
					echo '<tr>
						<td>'.$no.'</td>
						<td>'.$data['id_sk'].'</td>
						<td>'.$data['judul_buku'].'</td>
				
						<td>'.$data['nama'].'</td>
						<td>'.date_format(new DateTime($data['tgl_pinjam']),'d/M/Y').'</td>
						<td>'.$tgl_jatuh_tempo.' '.$status_badge.'</td>
						<td>Rp. '.number_format($data['telat_pengembalian']*$tarif_denda,0,',','.').'</td>
						</tr>';
				}
				?>
				<tr>
					<th colspan="7" style="text-align:right; padding-right:0.90cm;">
					Total Denda Rp. <?php echo number_format($total_denda ?? 0,0,',','.');?>
					</th>
				
				</tr>
					</tbody>

				</table>
			</div>
		</div>
	</div>
</section>

