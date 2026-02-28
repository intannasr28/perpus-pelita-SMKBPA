<?php
$id_siswa = $_SESSION["ses_username"];

// Ambil data buku favorit
$sql = $koneksi->query("SELECT b.* FROM tb_buku b 
                       INNER JOIN tb_favorit f ON b.id_buku = f.id_buku 
                       WHERE f.id_anggota='$id_siswa' 
                       ORDER BY b.judul_buku ASC");
$data_favorit = array();
while ($row = $sql->fetch_assoc()) {
    $data_favorit[] = $row;
}
?>

<section class="content-header">
    <h1>
        Buku Favorit
        <small style="color: #0073b7; font-weight: 500;">Daftar buku favorit Anda</small>
    </h1>
</section>

<section class="content">
    <div class="row">
        <div class="col-md-12">
            <div class="box box-danger">
                <div class="box-header with-border">
                    <h3 class="box-title">Buku Favorit Saya</h3>
                    <div class="box-tools pull-right">
                        <button type="button" class="btn btn-box-tool" data-widget="collapse"><i class="fa fa-minus"></i></button>
                    </div>
                </div>
                <div class="box-body">
                    <?php if (count($data_favorit) > 0) { ?>
                        <table id="favorit-table" class="table table-bordered table-striped">
                            <thead>
                                <tr>
                                    <th style="width: 5%">No</th>
                                    <th>ID Buku</th>
                                    <th>Judul Buku</th>
                                    <th>Pengarang</th>
                                    <th>Penerbit</th>
                                    <th>Tahun Terbit</th>
                                    <th style="width: 10%">Aksi</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php
                                $no = 1;
                                foreach ($data_favorit as $buku) {
                                ?>
                                    <tr>
                                        <td><?php echo $no++; ?></td>
                                        <td><?php echo $buku['id_buku']; ?></td>
                                        <td><?php echo $buku['judul_buku']; ?></td>
                                        <td><?php echo $buku['pengarang']; ?></td>
                                        <td><?php echo $buku['penerbit']; ?></td>
                                        <td><?php echo $buku['th_terbit']; ?></td>
                                        <td>
                                            <button type="button" class="btn btn-sm btn-danger hapus-favorit" data-id="<?php echo $buku['id_buku']; ?>" title="Hapus dari Favorit">
                                                <i class="fa fa-trash"></i>
                                            </button>
                                        </td>
                                    </tr>
                                <?php } ?>
                            </tbody>
                        </table>
                    <?php } else { ?>
                        <div class="alert alert-info">
                            <i class="fa fa-info-circle"></i> Anda belum memiliki buku favorit. <a href="?page=siswa/data_buku_siswa">Tambahkan sekarang</a>
                        </div>
                    <?php } ?>
                </div>
            </div>
        </div>
    </div>
</section>

<script>
$(document).ready(function() {
    $('#favorit-table').DataTable({
        columnDefs: [{
            "defaultContent": "-",
            "targets": "_all"
        }]
    });

    // Hapus Favorit
    $('.hapus-favorit').click(function() {
        var id_buku = $(this).data('id');
        var btn = $(this);
        
        if (confirm('Apakah Anda yakin ingin menghapus buku ini dari favorit?')) {
            $.ajax({
                url: 'admin/siswa/toggle_favorit.php',
                method: 'POST',
                data: { id_buku: id_buku },
                dataType: 'json',
                success: function(response) {
                    if (response.status == 'removed') {
                        btn.closest('tr').fadeOut(300, function() {
                            $(this).remove();
                        });
                    }
                },
                error: function() {
                    alert('Gagal menghapus favorit!');
                }
            });
        }
    });
});
</script>
