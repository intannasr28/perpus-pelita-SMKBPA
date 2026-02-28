<?php
$id_siswa = $_SESSION["ses_username"];
?>

<section class="content-header">
    <h1>
        Riwayat Peminjaman
        <small style="color: #0073b7; font-weight: 500;">Daftar semua peminjaman Anda</small>
    </h1>
</section>

<section class="content">
    <div class="row">
        <div class="col-md-12">
            <div class="box box-primary">
                <div class="box-header with-border">
                    <h3 class="box-title">Riwayat Peminjaman Buku</h3>
                    <div class="box-tools pull-right">
                        <a href="?page=siswa/pinjam_buku_siswa" class="btn btn-sm btn-success">
                            <i class="fa fa-plus"></i> Pinjam Buku Baru
                        </a>
                    </div>
                </div>
                <div class="box-body">
                    <style>
                        .modal {
                            z-index: 1050;
                        }
                        .modal-backdrop {
                            z-index: 1040;
                        }
                        .table-responsive {
                            overflow-x: auto;
                        }
                    </style>
                    <div class="table-responsive">
                        <table id="example1" class="table table-bordered table-striped">
                            <thead>
                                <tr>
                                    <th style="width: 10%">No</th>
                                    <th>ID Peminjaman</th>
                                    <th>Judul Buku</th>
                                    <th>Tanggal Pinjam</th>
                                    <th>Tanggal Kembali</th>
                                    <th>Status</th>
                                    <th style="width: 15%">Aksi</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php
                                $sql = "SELECT s.id_sk, b.judul_buku, b.id_buku, s.tgl_pinjam, s.tgl_kembali, s.status
                                        FROM tb_sirkulasi s
                                        JOIN tb_buku b ON s.id_buku = b.id_buku
                                        WHERE s.id_anggota = '$id_siswa'
                                        ORDER BY s.tgl_pinjam DESC";
                                
                                $result = $koneksi->query($sql);
                                $no = 1;
                                
                                if ($result->num_rows > 0) {
                                    while ($row = $result->fetch_assoc()) {
                                        $status_badge = '';
                                        $tgl_kembali_class = '';
                                        
                                        if ($row['status'] == 'PIN') {
                                            $status_badge = '<span class="badge badge-warning">Sedang Dipinjam</span>';
                                            
                                            // Cek jika sudah melewati tanggal kembali
                                            if (strtotime($row['tgl_kembali']) < strtotime(date('Y-m-d'))) {
                                                $status_badge = '<span class="badge badge-danger">Terlambat</span>';
                                                $tgl_kembali_class = 'text-danger';
                                            }
                                        } else if ($row['status'] == 'KEM') {
                                            $status_badge = '<span class="badge badge-success">Sudah Dikembalikan</span>';
                                        }
                                        
                                        $tgl_pinjam = date('d-m-Y', strtotime($row['tgl_pinjam']));
                                        $tgl_kembali = date('d-m-Y', strtotime($row['tgl_kembali']));
                                ?>
                                <tr>
                                    <td><?php echo $no++; ?></td>
                                    <td><?php echo $row['id_sk']; ?></td>
                                    <td><?php echo $row['judul_buku']; ?></td>
                                    <td><?php echo $tgl_pinjam; ?></td>
                                    <td class="<?php echo $tgl_kembali_class; ?>"><strong><?php echo $tgl_kembali; ?></strong></td>
                                    <td><?php echo $status_badge; ?></td>
                                    <td>
                                        <?php if ($row['status'] == 'PIN') { ?>
                                            <button type="button" class="btn btn-sm btn-info" data-toggle="modal" data-target="#detailModal<?php echo $row['id_sk']; ?>">
                                                <i class="fa fa-eye"></i> Detail
                                            </button>
                                        <?php } else { ?>
                                            <button type="button" class="btn btn-sm btn-secondary" data-toggle="modal" data-target="#detailModal<?php echo $row['id_sk']; ?>">
                                                <i class="fa fa-eye"></i> Detail
                                            </button>
                                        <?php } ?>
                                    </td>
                                </tr>

                                <!-- Modal Detail -->
                                <div class="modal fade" id="detailModal<?php echo $row['id_sk']; ?>" tabindex="-1" role="dialog" aria-labelledby="detailModalLabel" aria-hidden="true">
                                    <div class="modal-dialog" role="document">
                                        <div class="modal-content">
                                            <div class="modal-header">
                                                <h5 class="modal-title" id="detailModalLabel">Detail Peminjaman - <?php echo $row['id_sk']; ?></h5>
                                                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                                                    <span aria-hidden="true">&times;</span>
                                                </button>
                                            </div>
                                            <div class="modal-body">
                                                <dl class="row">
                                                    <dt class="col-sm-5"><strong>ID Peminjaman</strong></dt>
                                                    <dd class="col-sm-7"><?php echo $row['id_sk']; ?></dd>
                                                    
                                                    <dt class="col-sm-5"><strong>ID Buku</strong></dt>
                                                    <dd class="col-sm-7"><?php echo $row['id_buku']; ?></dd>
                                                    
                                                    <dt class="col-sm-5"><strong>Judul Buku</strong></dt>
                                                    <dd class="col-sm-7"><?php echo $row['judul_buku']; ?></dd>
                                                    
                                                    <dt class="col-sm-5"><strong>Tanggal Pinjam</strong></dt>
                                                    <dd class="col-sm-7"><?php echo $tgl_pinjam; ?></dd>
                                                    
                                                    <dt class="col-sm-5"><strong>Tanggal Kembali</strong></dt>
                                                    <dd class="col-sm-7"><?php echo $tgl_kembali; ?></dd>
                                                    
                                                    <dt class="col-sm-5"><strong>Status</strong></dt>
                                                    <dd class="col-sm-7"><?php echo $status_badge; ?></dd>
                                                </dl>
                                            </div>
                                            <div class="modal-footer">
                                                <button type="button" class="btn btn-secondary" data-dismiss="modal">Tutup</button>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                <?php
                                    }
                                } else {
                                ?>
                                <tr>
                                    <td colspan="7" style="text-align: center; color: #999;">
                                        <p>Anda belum pernah meminjam buku</p>
                                        <a href="?page=siswa/pinjam_buku_siswa" class="btn btn-sm btn-primary">
                                            <i class="fa fa-plus"></i> Pinjam Sekarang
                                        </a>
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

<script>
$(document).ready(function() {
    var table = $('#example1').DataTable({
        "destroy": true,
        "paging": true,
        "pageLength": 50,
        "lengthMenu": [[10, 25, 50, 100, -1], [10, 25, 50, 100, "Semua"]],
        "columnDefs": [{
            "defaultContent": "-",
            "targets": "_all"
        }],
        "order": [[3, "desc"]],
        "searching": true,
        "language": {
            "lengthMenu": "Tampilkan _MENU_ baris per halaman",
            "search": "Cari:",
            "paginate": {
                "first": "Pertama",
                "last": "Terakhir",
                "next": "Berikutnya",
                "previous": "Sebelumnya"
            },
            "info": "Menampilkan _START_ sampai _END_ dari _TOTAL_ peminjaman",
            "emptyTable": "Tidak ada data",
            "zeroRecords": "Tidak ada peminjaman yang sesuai"
        }
    });
    
    // Destroy semua modals ketika halaman load untuk menghindari duplicates
    $('.modal').on('hidden.bs.modal', function() {
        $(this).removeData('bs.modal');
    });
});
</script>
