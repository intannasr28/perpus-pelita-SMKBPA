<?php
if(isset($_GET['kode'])){
    // Ambil tanggal hari ini sebagai tanggal pengembalian yang sebenarnya
    $tgl_dikembalikan = date('Y-m-d'); 
    
    // Step 1: Ambil id_buku dari record sirkulasi
    $sql_get_buku = "SELECT id_buku FROM tb_sirkulasi WHERE id_sk='".$_GET['kode']."'";
    $result_get_buku = mysqli_query($koneksi, $sql_get_buku);
    $row_buku = mysqli_fetch_array($result_get_buku);
    $id_buku = $row_buku['id_buku'];

    // Step 2: Update status dan set tgl_kembali, PLUS increment stok
    $sql_ubah = "UPDATE tb_sirkulasi SET 
        status='KEM', 
        tgl_kembali='$tgl_dikembalikan' 
        WHERE id_sk='".$_GET['kode']."';";
    // Step 3: Increment stok buku (kembalikan 1 buku ke stok)
    $sql_ubah .= "UPDATE tb_buku SET stok = stok + 1 WHERE id_buku='$id_buku'";
        
    $query_ubah = mysqli_multi_query($koneksi, $sql_ubah);

    if ($query_ubah) {
        echo "<script>
        Swal.fire({title: 'Kembalikan Buku Berhasil', text: 'Buku dikembalikan pada: $tgl_dikembalikan, Stok buku bertambah 1', icon: 'success', confirmButtonText: 'OK'
        }).then((result) => {
            if (result.value) {
                window.location = 'index.php?page=data_sirkul';
            }
        })</script>";
    } else {
        echo "<script>
        Swal.fire({title: 'Kembalikan Buku Gagal', icon: 'error', confirmButtonText: 'OK'
        }).then((result) => {
            if (result.value) {
                window.location = 'index.php?page=data_sirkul';
            }
        })</script>";
    }
}
?>