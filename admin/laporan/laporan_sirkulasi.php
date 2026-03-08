<section class="content-header">
	<h1>
		Laporan Sirkulasi
		<small style="color: #0073b7; font-weight: 500;">Daftar tagihan denda dan status siswa</small>
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
	<?php
	// Include helper functions untuk denda
	require_once __DIR__ . '/../../inc/helper_denda.php';

	// Handle toggle status siswa
	if (isset($_GET['action']) && $_GET['action'] == 'toggle_siswa') {
		$id_anggota = isset($_GET['id_anggota']) ? mysqli_real_escape_string($koneksi, $_GET['id_anggota']) : null;
		$new_status = isset($_GET['new_status']) ? $_GET['new_status'] : null;
		
		if ($id_anggota && ($new_status == 'AKTIF' || $new_status == 'NONAKTIF')) {
			if ($new_status == 'AKTIF') {
				$result = aktifkanSiswa($id_anggota, $koneksi);
				$msg_type = 'success';
				$msg = 'Akun siswa berhasil diaktifkan';
			} else {
				$alasan = isset($_GET['alasan']) ? $_GET['alasan'] : 'Denda belum dibayar';
				$result = nonaktifkanSiswa($id_anggota, $alasan, $koneksi);
				$msg_type = 'warning';
				$msg = 'Akun siswa berhasil dinonaktifkan';
			}
			
			if ($result) {
				echo "<script>Swal.fire({icon: '$msg_type', title: 'Berhasil', text: '$msg'}).then(() => { window.location = '?page=laporan_sirkulasi'; });</script>";
			}
		}
	}
	
	// Handle pembayaran denda
	if (isset($_POST['bayar_denda'])) {
		$id_sk = isset($_POST['id_sk']) ? mysqli_real_escape_string($koneksi, $_POST['id_sk']) : null;
		$nominal_denda = isset($_POST['nominal_denda']) ? (int)$_POST['nominal_denda'] : 0;
		$catatan = isset($_POST['catatan']) ? mysqli_real_escape_string($koneksi, $_POST['catatan']) : 'Pembayaran denda';
		
		if ($id_sk && $nominal_denda > 0) {
			$result = catetPembayaranDenda($id_sk, $nominal_denda, $catatan, $koneksi);
			
			if ($result) {
				echo "<script>Swal.fire({icon: 'success', title: 'Pembayaran Denda Tersimpan', text: 'Denda telah dicatat sebagai sudah dibayar'}).then(() => { window.location = '?page=laporan_sirkulasi'; });</script>";
			}
		}
	}
	
	// Handle perpanjangan
	if (isset($_POST['catat_perpanjangan'])) {
		$id_sk = isset($_POST['id_sk']) ? mysqli_real_escape_string($koneksi, $_POST['id_sk']) : null;
		
		if ($id_sk) {
			$result = catetPerpanjangan($id_sk, $koneksi);
			
			if ($result) {
				echo "<script>Swal.fire({icon: 'success', title: 'Perpanjangan Dicatat', text: 'Perpanjangan telah dicatat. Denda ditangguhkan.'}).then(() => { window.location = '?page=laporan_sirkulasi'; });</script>";
			}
		}
	}
	?>
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
							<th>Status Akun</th>
							<th>Tgl Pinjam</th>
							<th>Jatuh Tempo</th>
							<th>Denda</th>
							<th>Aksi</th>
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
			tb_anggota.status as status_akun,
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
			Order By tb_sirkulasi.tgl_pinjam DESC");;
			$no = 0;
			$total_denda = 0;
			$tarif_denda = 1000;
            while ($data = mysqli_fetch_array($sql, MYSQLI_ASSOC)) {
				$no++;
				$total_denda=$total_denda+($data['telat_pengembalian']*$tarif_denda);
				
				// Jika belum dikembalikan, gunakan jatuh tempo (tgl_pinjam + 7 hari)
				$tgl_jatuh_tempo = $data['status'] == 'PIN' ? date('d/M/Y', strtotime($data['tgl_pinjam'] . ' +7 days')) : date_format(new DateTime($data['tgl_kembali']),'d/M/Y');
				$status_badge = $data['status'] == 'PIN' ? '<span class="label label-warning">BELUM DIKEMBALIKAN</span>' : '<span class="label label-success">DIKEMBALIKAN TERLAMBAT</span>';
				
				// Status akun badge
				$status_akun_badge = $data['status_akun'] == 'NONAKTIF' ? '<span class="label label-danger">NONAKTIF</span>' : '<span class="label label-success">AKTIF</span>';
				
				// Action buttons
				$nominal_denda = $data['telat_pengembalian']*$tarif_denda;
				$action_buttons = '<button class="btn btn-xs btn-info" data-toggle="modal" data-target="#modalDenda" onclick="setBayarDenda(\'' . $data['id_sk'] . '\', ' . $nominal_denda . ')">';
				$action_buttons .= '<i class="fa fa-money"></i> Bayar</button> ';
				
				$action_buttons .= '<button class="btn btn-xs btn-primary" data-toggle="modal" data-target="#modalPerpanjangan" onclick="setPerpanjangan(\'' . $data['id_sk'] . '\')"><i class="fa fa-refresh"></i> Perpanjang</button> ';
				
				if ($data['status_akun'] == 'AKTIF') {
					$action_buttons .= '<a href="?page=laporan_sirkulasi&action=toggle_siswa&id_anggota=' . $data['id_anggota'] . '&new_status=NONAKTIF&alasan=Denda%20terlambat%20belum%20dibayar" class="btn btn-xs btn-danger" onclick="return confirm(\'Nonaktifkan akun siswa ini?\');"><i class="fa fa-ban"></i> Nonaktif</a>';
				} else {
					$action_buttons .= '<a href="?page=laporan_sirkulasi&action=toggle_siswa&id_anggota=' . $data['id_anggota'] . '&new_status=AKTIF" class="btn btn-xs btn-success" onclick="return confirm(\'Aktifkan kembali akun siswa ini?\');"><i class="fa fa-check"></i> Aktifkan</a>';
				}
				
				echo '<tr>
					<td>'.$no.'</td>
					<td>'.$data['id_sk'].'</td>
					<td>'.$data['judul_buku'].'</td>
					<td>'.$data['nama'].'</td>
					<td>'.$status_akun_badge.'</td>
					<td>'.date_format(new DateTime($data['tgl_pinjam']),'d/M/Y').'</td>
					<td>'.$tgl_jatuh_tempo.' '.$status_badge.'</td>
					<td>Rp. '.number_format($nominal_denda,0,',','.').'</td>
					<td>'.$action_buttons.'</td>
				</tr>';
			}
				?>
				<tr>
					<th colspan="9" style="text-align:right; padding-right:0.90cm;">
						Total Denda Rp. <?php echo number_format($total_denda ?? 0,0,',','.');?>
					</th>
				</tr>
					</tbody>

				</table>
			</div>
		</div>
	</div>
</section>

<!-- Modal Pembayaran Denda -->
<div class="modal fade" id="modalDenda" tabindex="-1" role="dialog" aria-labelledby="modalDendaLabel" aria-hidden="true">
	<div class="modal-dialog" role="document">
		<div class="modal-content">
			<div class="modal-header">
				<h5 class="modal-title" id="modalDendaLabel">Catat Pembayaran Denda</h5>
				<button type="button" class="close" data-dismiss="modal" aria-label="Close">
					<span aria-hidden="true">&times;</span>
				</button>
			</div>
			<form method="POST" action="">
				<div class="modal-body">
					<input type="hidden" name="id_sk" id="input_id_sk" value="">
					<div class="form-group">
						<label>Nominal Denda (Rp)</label>
						<input type="number" class="form-control" id="input_nominal_denda" name="nominal_denda" required readonly>
					</div>
					<div class="form-group">
						<label>Catatan</label>
						<textarea class="form-control" name="catatan" rows="3">Pembayaran denda keterlambatan pengembalian buku</textarea>
					</div>
				</div>
				<div class="modal-footer">
					<button type="button" class="btn btn-secondary" data-dismiss="modal">Batal</button>
					<button type="submit" name="bayar_denda" class="btn btn-primary">Simpan Pembayaran</button>
				</div>
			</form>
		</div>
	</div>
</div>

<!-- Modal Perpanjangan -->
<div class="modal fade" id="modalPerpanjangan" tabindex="-1" role="dialog" aria-labelledby="modalPerpanjanganLabel" aria-hidden="true">
	<div class="modal-dialog" role="document">
		<div class="modal-content">
			<div class="modal-header">
				<h5 class="modal-title" id="modalPerpanjanganLabel">Catat Perpanjangan Peminjaman</h5>
				<button type="button" class="close" data-dismiss="modal" aria-label="Close">
					<span aria-hidden="true">&times;</span>
				</button>
			</div>
			<form method="POST" action="">
				<div class="modal-body">
					<input type="hidden" name="id_sk" id="input_id_sk_perpanjang" value="">
					<p><strong>Ketika siswa melakukan perpanjangan ke admin:</strong></p>
					<ul>
						<li>Denda ditangguhkan (tidak perlu dibayar langsung)</li>
						<li>Akun siswa tetap AKTIF</li>
						<li>Siswa dapat meminjam buku lagi</li>
						<li>Denda akan dibayar nanti setelah buku dikembalikan</li>
					</ul>
				</div>
				<div class="modal-footer">
					<button type="button" class="btn btn-secondary" data-dismiss="modal">Batal</button>
					<button type="submit" name="catat_perpanjangan" class="btn btn-primary">Catat Perpanjangan</button>
				</div>
			</form>
		</div>
	</div>
</div>

<script>
function setBayarDenda(id_sk, nominal) {
	document.getElementById('input_id_sk').value = id_sk;
	document.getElementById('input_nominal_denda').value = nominal;
}

function setPerpanjangan(id_sk) {
	document.getElementById('input_id_sk_perpanjang').value = id_sk;
}
</script>