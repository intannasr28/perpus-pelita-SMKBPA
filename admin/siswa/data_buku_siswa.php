<?php
$id_siswa = $_SESSION["ses_username"];

// Ambil data buku
$sql = $koneksi->query("SELECT * FROM tb_buku ORDER BY judul_buku ASC");
$data_buku = array();
while ($row = $sql->fetch_assoc()) {
    $data_buku[] = $row;
}

// Cek buku favorit
$sql_favorit = $koneksi->query("SELECT id_buku FROM tb_favorit WHERE id_anggota='$id_siswa'");
$favorit_list = array();
while ($row = $sql_favorit->fetch_assoc()) {
    $favorit_list[] = $row['id_buku'];
}
?>

<section class="content-header">
    <h1>
        Data Buku
        <small style="color: #0073b7; font-weight: 500;">Daftar lengkap buku perpustakaan</small>
    </h1>
</section>

<section class="content">
    <div class="row">
        <div class="col-md-12">
            <div class="box box-primary">
                <div class="box-header with-border">
                    <h3 class="box-title">Daftar Buku Perpustakaan</h3>
                    <div class="box-tools pull-right">
                        <button type="button" class="btn btn-box-tool" data-widget="collapse"><i class="fa fa-minus"></i></button>
                    </div>
                </div>
                <div class="box-body">
                    <div class="table-responsive">
                        <table id="example1" class="table table-bordered table-striped">
                            <thead>
                                <tr>
                                    <th style="width: 5%">No</th>
                                    <th>ID Buku</th>
                                    <th>Judul Buku</th>
                                    <th>Pengarang</th>
                                    <th>Penerbit</th>
                                    <th>Tahun Terbit</th>
                                    <th>Kategori</th>
                                    <th style="width: 15%">Stok</th>
                                    <th style="width: 12%">Aksi</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php
                                $no = 1;
                                foreach ($data_buku as $buku) {
                                    $is_favorit = in_array($buku['id_buku'], $favorit_list);
                                    $icon_class = $is_favorit ? 'fa-heart' : 'fa-heart-o';
                                    $btn_class = $is_favorit ? 'btn-danger' : 'btn-default';
                                    $btn_text = $is_favorit ? 'Hapus' : 'Favorit';
                                ?>
                                    <tr>
                                        <td><?php echo $no++; ?></td>
                                        <td><?php echo $buku['id_buku']; ?></td>
                                        <td><?php echo $buku['judul_buku']; ?></td>
                                        <td><?php echo $buku['pengarang']; ?></td>
                                        <td><?php echo $buku['penerbit']; ?></td>
                                        <td><?php echo $buku['th_terbit']; ?></td>
                                        <td>
                                            <?php 
                                                $kategori = isset($buku['kategori']) ? $buku['kategori'] : 'Pelajaran';
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
                                            <span class="label <?php echo ($buku['stok'] > 0) ? 'label-success' : 'label-danger'; ?>">
                                                <?php echo $buku['stok']; ?> Tersedia
                                            </span>
                                        </td>
                                        <td>
                                            <button type="button" class="btn btn-sm <?php echo $btn_class; ?> toggle-favorit" data-id="<?php echo $buku['id_buku']; ?>" title="<?php echo $btn_text; ?> dari Favorit">
                                                <i class="fa <?php echo $icon_class; ?>"></i> <?php echo $btn_text; ?>
                                            </button>
                                        </td>
                                    </tr>
                                <?php } ?>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>

<!-- Toggle Favorit Script dipindahkan ke assets/js/toggle-favorit.js dan di-load di index.php footer -->