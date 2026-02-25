<?php
if(isset($_GET['kode'])){
    // Ambil tanggal hari ini sebagai tanggal pengembalian yang sebenarnya
    $tgl_dikembalikan = date('Y-m-d'); 

    // Update status menjadi KEM dan set tgl_kembali ke tanggal hari ini
    $sql_ubah = "UPDATE tb_sirkulasi SET 
        status='KEM', 
        tgl_kembali='$tgl_dikembalikan' 
        WHERE id_sk='".$_GET['kode']."'";
        
    $query_ubah = mysqli_query($koneksi, $sql_ubah);

    if ($query_ubah) {
        echo "<script>
        Swal.fire({title: 'Kembalikan Buku Berhasil', text: 'Buku dikembalikan pada: $tgl_dikembalikan', icon: 'success', confirmButtonText: 'OK'
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