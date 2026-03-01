<section class="content-header">
	<h1>
		Sirkulasi Buku
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
			<a href="?page=add_sirkul" title="Tambah Data" class="btn btn-primary">
				<i class="glyphicon glyphicon-plus"></i> Tambah Data</a>
			<button type="button" id="bulk_return_btn" class="btn btn-danger" title="Kembalikan Peminjaman Terpilih" style="display: none; margin-left: 10px;">
				<i class="fa fa-undo"></i> Kembalikan Pilihan
			</button>
		</div>
		<!-- /.box-header -->
		<div class="box-body">
			<form id="bulk_return_form" method="POST" action="">
				<div class="table-responsive">
					<table id="example1" class="table table-bordered table-striped">
						<thead>
							<tr>
								<th style="width: 30px;">
									<input type="checkbox" id="select_all">
								</th>
								<th>No</th>
								<th>ID SKL</th>
								<th>Buku</th>
								<th>Jumlah</th>
								<th>Peminjam</th>
								<th>Tgl Pinjam</th>
								<th>Jatuh Tempo</th>
								<th>Denda</th>
								<th>Kelola</th>
							</tr>
						</thead>
						<tbody>

						<?php
						$no = 1;
						// OPSI 2: Query dari tb_sirkulasi_detail (WHERE status='PIN')
						$sql = $koneksi->query("SELECT s.id_sk, b.judul_buku, d.jumlah,
				  a.id_anggota,
				  a.nama,
				  s.tgl_pinjam, 
				  s.tgl_kembali,
				  d.id_buku
                  from tb_sirkulasi s 
                  INNER JOIN tb_sirkulasi_detail d ON s.id_sk = d.id_sk
                  INNER JOIN tb_buku b ON d.id_buku = b.id_buku
				  INNER JOIN tb_anggota a ON s.id_anggota = a.id_anggota 
				  WHERE d.status='PIN' 
				  ORDER BY s.tgl_pinjam DESC");
						while ($data = $sql->fetch_assoc()) {
						?>

							<tr>
								<td style="text-align: center;">
									<input type="checkbox" name="select_sk[]" value="<?php echo $data['id_sk']; ?>" class="select_sk">
								</td>
								<td>
									<?php echo $no++; ?>
								</td>
								<td>
									<?php echo $data['id_sk']; ?>
								</td>
								<td>
									<?php echo $data['judul_buku']; ?>
								</td>
								<td style="text-align: center;">
									<strong><?php echo $data['jumlah']; ?></strong> buku
								</td>
								<td>
									<?php echo $data['id_anggota']; ?>
									-
									<?php echo $data['nama']; ?>
								</td>
								<td>
									<?php $tgl = $data['tgl_pinjam'];
									echo date("d/M/Y", strtotime($tgl)) ?>
								</td>
								<td>
									<?php $tgl = $data['tgl_kembali'];
									echo date("d/M/Y", strtotime($tgl)) ?>
								</td>

								<?php

								$u_denda = 1000;

								// Tanggal hari ini
								$tgl_hari_ini = date("Y-m-d");
								
								// Tanggal jatuh tempo = tanggal pinjam + 7 hari
								$tgl_pinjam = $data['tgl_pinjam'];
								$tgl_jatuh_tempo = date('Y-m-d', strtotime($tgl_pinjam . ' +7 days'));

								// Hitung selisih hari antara hari ini dan jatuh tempo
								$pecah_hari_ini = explode("-", $tgl_hari_ini);
								$pecah_jatuh_tempo = explode("-", $tgl_jatuh_tempo);

								$jd_hari_ini = GregorianToJD($pecah_hari_ini[1], $pecah_hari_ini[2], $pecah_hari_ini[0]);
								$jd_jatuh_tempo = GregorianToJD($pecah_jatuh_tempo[1], $pecah_jatuh_tempo[2], $pecah_jatuh_tempo[0]);

								$selisih = $jd_hari_ini - $jd_jatuh_tempo;
								$denda = ($selisih > 0) ? $selisih * $u_denda : 0;
								?>

								<td>
									<?php if ($selisih <= 0) { ?>
										<span class="label label-primary">Masa Peminjaman (Sisa: <?php echo abs($selisih); ?> hari)</span>
									<?php } else { ?>
										<span class="label label-danger">
											Rp.
											<?= number_format($denda, 0, ',', '.'); ?>
										</span>
										<br> Terlambat :
										<?= $selisih ?>
										Hari
								</td>
							<?php } ?>

							<td>
								<a href="?page=panjang&kode=<?php echo $data['id_sk']; ?>" onclick="return confirm('Perpanjang Data Ini ?')" title="Perpanjang" class="btn btn-success">
									<i class="glyphicon glyphicon-upload"></i>
								</a>
								<a href="?page=kembali&kode=<?php echo $data['id_sk']; ?>" onclick="return confirm('Kembalikan Buku Ini ?')" title="Kembalikan" class="btn btn-danger">
									<i class="glyphicon glyphicon-download"></i>
							</td>
							</tr>
						<?php
						}
						?>
					</tbody>

				</table>
			</form>
		</div>
	</div>
	<h4> *Note
		<br> Masa peminjaman buku adalah <span style="color:red; font-weight:bold;">7 hari</span> dari tanggal peminjaman.
		<br> Jika buku dikembalikan lebih dari masa peminjaman, maka akan dikenakan <span style="color:red; font-weight:bold;">denda</span>
		<br> sebesar <span style="color:red; font-weight:bold;">Rp 1.000/hari</span>.
	</h4>
</section>

<script>
$(document).ready(function() {
	// Select all checkbox
	$('#select_all').on('change', function() {
		var isChecked = $(this).is(':checked');
		$('.select_sk').prop('checked', isChecked);
		toggleBulkButton();
	});
	
	// Individual checkbox
	$('.select_sk').on('change', function() {
		toggleBulkButton();
		updateSelectAllCheckbox();
	});
	
	// Toggle bulk return button visibility
	function toggleBulkButton() {
		var checkedCount = $('.select_sk:checked').length;
		if (checkedCount > 0) {
			$('#bulk_return_btn').show();
		} else {
			$('#bulk_return_btn').hide();
		}
	}
	
	// Update master checkbox state
	function updateSelectAllCheckbox() {
		var totalCheckboxes = $('.select_sk').length;
		var checkedCheckboxes = $('.select_sk:checked').length;
		$('#select_all').prop('checked', totalCheckboxes === checkedCheckboxes);
	}
	
	// Bulk return button click
	$('#bulk_return_btn').on('click', function() {
		var selected = $('.select_sk:checked').map(function() {
			return $(this).val();
		}).get();
		
		if (selected.length === 0) {
			Swal.fire('Info', 'Pilih minimal 1 peminjaman untuk dikembalikan', 'info');
			return;
		}
		
		// Submit form to bulk_kembali page
		$('#bulk_return_form').attr('action', '?page=bulk_kembali').submit();
	});
});
</script>