<section class="content-header">
	<h1>
		Data Buku
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
			<a href="?page=MyApp/add_buku" title="Tambah Data" class="btn btn-primary">
				<i class="glyphicon glyphicon-plus"></i> Tambah Data</a>
		</div>
		<!-- /.box-header -->
		<div class="box-body">
			<div class="table-responsive">
				<table id="example1" class="table table-bordered table-striped">
					<thead>
						<tr>
							<th>No</th>
							<th>Id Buku</th>
							<th>Judul Buku</th>
							<th>Pengarang</th>
							<th>Penerbit</th>
							<th>Tahun</th>
							<th>Kategori</th>
							<th>Stok</th>
							<th>Kelola</th>
						</tr>
					</thead>
					<tbody>

						<?php
                  $no = 1;
                  $sql = $koneksi->query("SELECT * from tb_buku");
                  while ($data= $sql->fetch_assoc()) {
                ?>

						<tr>
							<td>
								<?php echo $no++; ?>
							</td>
							<td>
								<?php echo $data['id_buku']; ?>
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
								<?php 
									$kategori = isset($data['kategori']) ? $data['kategori'] : 'Pelajaran';
									$badge_class = '';
									if ($kategori == 'Pelajaran') $badge_class = 'label-info';
									else if ($kategori == 'Fiksi') $badge_class = 'label-success';
									else if ($kategori == 'Non Fiksi') $badge_class = 'label-warning';
									else if ($kategori == 'Referensi') $badge_class = 'label-primary';
									else if ($kategori == 'Komik') $badge_class = 'label-danger';
									else $badge_class = 'label-default';
								?>
								<span class="label <?php echo $badge_class; ?>"><?php echo $kategori; ?></span>
							</td>
							<td>
								<?php echo $data['stok']; ?>
							</td>
							<td>
								<a href="?page=MyApp/edit_buku&kode=<?php echo $data['id_buku']; ?>" title="Ubah"
								 class="btn btn-success">
									<i class="glyphicon glyphicon-edit"></i>
								</a>
								<button type="button" title="Lihat Sinopsis" class="btn btn-info" data-toggle="modal" data-target="#modalSinopsis" onclick="loadSinopsis('<?php echo $data['id_buku']; ?>', '<?php echo htmlspecialchars($data['judul_buku']); ?>', '<?php echo htmlspecialchars(isset($data['sinopsis']) ? $data['sinopsis'] : ''); ?>')">
									<i class="fa fa-eye"></i>
								</button>
								<a href="?page=MyApp/del_buku&kode=<?php echo $data['id_buku']; ?>" onclick="return confirm('Yakin Hapus Data Ini ?')"
								 title="Hapus" class="btn btn-danger">
									<i class="glyphicon glyphicon-trash"></i>
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

<!-- Modal Sinopsis -->
<div class="modal fade" id="modalSinopsis" tabindex="-1" role="dialog" aria-labelledby="sinopsisLabel" aria-hidden="true">
  <div class="modal-dialog modal-lg" role="document">
    <div class="modal-content">
      <div class="modal-header">
        <h5 class="modal-title" id="sinopsisLabel">Sinopsis Buku</h5>
        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
          <span aria-hidden="true">&times;</span>
        </button>
      </div>
      <div class="modal-body">
        <div class="form-group">
          <label><strong>Judul Buku:</strong></label>
          <p id="judul-sinopsis"></p>
        </div>
        <div class="form-group">
          <label><strong>Sinopsis:</strong></label>
          <p id="isi-sinopsis" style="line-height: 1.6; white-space: pre-wrap; word-wrap: break-word;"></p>
        </div>
      </div>
      <div class="modal-footer">
        <button type="button" class="btn btn-secondary" data-dismiss="modal">Tutup</button>
      </div>
    </div>
  </div>
</div>

<script>
function loadSinopsis(idBuku, judulBuku, sinopsis) {
  // Decode HTML entities
  var judulDecoded = $('<div/>').html(judulBuku).text();
  var sinopsisDecoded = $('<div/>').html(sinopsis).text();
  
  // Set modal content
  document.getElementById('judul-sinopsis').textContent = judulDecoded || '(Tidak ada judul)';
  document.getElementById('isi-sinopsis').textContent = sinopsisDecoded || '(Belum ada sinopsis untuk buku ini)';
}
</script>