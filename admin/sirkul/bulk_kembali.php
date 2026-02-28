<?php
// Tangkap data dari checkbox form
$selected_sk = isset($_POST['select_sk']) ? $_POST['select_sk'] : array();

// Jika form bulk return disubmit
if (isset($_POST['proses_bulk_kembali'])) {
    // Validasi
    if (empty($_POST['select_sk'])) {
        echo "<script>
        Swal.fire({title: 'Gagal', text: 'Pilih minimal 1 peminjaman', icon: 'error', confirmButtonText: 'OK'
        }).then((result) => {
            if (result.value) {
                window.location = 'index.php?page=data_sirkul';
            }
        })</script>";
        exit;
    }
    
    $tgl_kembali = $_POST['tgl_kembali'];
    $jumlah_array = isset($_POST['jumlah']) ? $_POST['jumlah'] : array();
    $success = 0;
    $failed = 0;
    
    foreach ($_POST['select_sk'] as $id_sk) {
        // Ambil jumlah yang dikembalikan untuk setiap id_sk
        $jumlah_kembali = isset($jumlah_array[$id_sk]) ? intval($jumlah_array[$id_sk]) : 1;
        
        // Safety check: jumlah minimal 1
        if ($jumlah_kembali < 1) {
            $jumlah_kembali = 1;
        }
        
        // Ambil detail peminjaman
        $sql_detail = "SELECT id_buku, id_anggota FROM tb_sirkulasi WHERE id_sk = '$id_sk'";
        $result_detail = mysqli_query($koneksi, $sql_detail);
        $detail = mysqli_fetch_array($result_detail);
        
        if (!$detail) {
            $failed++;
            continue;
        }
        
        $id_buku = $detail['id_buku'];
        $id_anggota = $detail['id_anggota'];
        
        // Update status dan tanggal kembali
        $sql_update = "UPDATE tb_sirkulasi SET 
            status='KEM', 
            tgl_kembali='$tgl_kembali' 
            WHERE id_sk='$id_sk'";
        
        // Update stok buku (kembalikan jumlah yang dikembalikan)
        $sql_stok = "UPDATE tb_buku SET stok = stok + $jumlah_kembali WHERE id_buku='$id_buku'";
        
        // Insert ke log pengembalian (opsional, untuk tracking)
        $sql_log = "INSERT INTO log_kembali (id_sk, id_buku, id_anggota, tgl_kembali, jumlah_kembali) 
                    VALUES ('$id_sk', '$id_buku', '$id_anggota', '$tgl_kembali', '$jumlah_kembali')";
        
        // Execute queries
        $exec_update = mysqli_query($koneksi, $sql_update);
        $exec_stok = mysqli_query($koneksi, $sql_stok);
        
        if ($exec_update && $exec_stok) {
            // Optional: insert log
            @mysqli_query($koneksi, $sql_log);
            $success++;
        } else {
            $failed++;
        }
    }
    
    // Show result
    $message = "Berhasil: $success | Gagal: $failed";
    echo "<script>
    Swal.fire({
        title: 'Proses Pengembalian Selesai',
        text: '$message',
        icon: 'success',
        confirmButtonText: 'OK'
    }).then((result) => {
        if (result.value) {
            window.location = 'index.php?page=data_sirkul';
        }
    })
    </script>";
    exit;
}

// Query untuk ambil detail peminjaman yang dipilih
if (!empty($selected_sk)) {
    $sk_list = "'" . implode("','", $selected_sk) . "'";
    $sql = $koneksi->query("
        SELECT s.id_sk, s.id_buku, b.judul_buku, a.id_anggota, a.nama, s.tgl_pinjam, s.tgl_kembali
        FROM tb_sirkulasi s 
        JOIN tb_buku b ON s.id_buku = b.id_buku 
        JOIN tb_anggota a ON s.id_anggota = a.id_anggota 
        WHERE s.id_sk IN ($sk_list)
        ORDER BY s.tgl_pinjam DESC
    ");
    $data_items = array();
    while ($row = $sql->fetch_assoc()) {
        $data_items[] = $row;
    }
} else {
    $data_items = array();
}
?>

<section class="content-header">
    <h1>
        Pengembalian Buku (Bulk)
        <small style="color: #0073b7; font-weight: 500;">Kembalikan multiple peminjaman sekaligus</small>
    </h1>
    <ol class="breadcrumb">
        <li>
            <a href="?page=data_sirkul">
                <i class="fa fa-arrow-left"></i> Kembali
            </a>
        </li>
    </ol>
</section>

<section class="content">
    <div class="row">
        <div class="col-md-12">
            <div class="box box-info">
                <div class="box-header with-border">
                    <h3 class="box-title">Detail Pengembalian (<?php echo count($data_items); ?> item)</h3>
                </div>

                <form action="" method="POST">
                    <div class="box-body">
                        
                        <?php if (count($data_items) == 0): ?>
                            <div class="alert alert-warning">
                                <i class="fa fa-warning"></i> Tidak ada data peminjaman yang dipilih
                            </div>
                        <?php else: ?>
                            
                            <div class="form-group">
                                <label>Tanggal Pengembalian</label>
                                <input type="date" name="tgl_kembali" class="form-control" required value="<?php echo date('Y-m-d'); ?>">
                            </div>

                            <div class="alert alert-info">
                                <strong><i class="fa fa-info-circle"></i> Instruksi:</strong> Masukkan jumlah buku yang dikembalikan untuk setiap peminjaman di bawah. Jika tidak diisi, sistem akan anggap 1 buku dikembalikan.
                            </div>

                            <div class="table-responsive">
                                <table class="table table-bordered table-striped">
                                    <thead>
                                        <tr>
                                            <th style="width: 80px;">No</th>
                                            <th>ID SKL</th>
                                            <th>Peminjam</th>
                                            <th>Buku</th>
                                            <th>Tgl Pinjam</th>
                                            <th style="width: 120px;">Jumlah Dikembalikan</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <?php $no = 1; foreach ($data_items as $item): ?>
                                        <tr>
                                            <td><?php echo $no++; ?></td>
                                            <td>
                                                <strong><?php echo $item['id_sk']; ?></strong>
                                                <input type="hidden" name="select_sk[]" value="<?php echo $item['id_sk']; ?>">
                                            </td>
                                            <td>
                                                <?php echo $item['id_anggota']; ?> - <?php echo $item['nama']; ?>
                                            </td>
                                            <td><?php echo $item['judul_buku']; ?></td>
                                            <td><?php echo date('d/M/Y', strtotime($item['tgl_pinjam'])); ?></td>
                                            <td>
                                                <input type="number" name="jumlah[<?php echo $item['id_sk']; ?>]" class="form-control" min="1" max="99" value="1" required style="text-align: center;">
                                            </td>
                                        </tr>
                                        <?php endforeach; ?>
                                    </tbody>
                                </table>
                            </div>

                        <?php endif; ?>

                    </div>

                    <div class="box-footer">
                        <?php if (count($data_items) > 0): ?>
                            <button type="submit" name="proses_bulk_kembali" class="btn btn-success">
                                <i class="fa fa-check"></i> Kembalikan <?php echo count($data_items); ?> Peminjaman
                            </button>
                        <?php endif; ?>
                        <a href="?page=data_sirkul" class="btn btn-secondary">
                            <i class="fa fa-times"></i> Batal
                        </a>
                    </div>
                </form>

            </div>
        </div>
    </div>
</section>
