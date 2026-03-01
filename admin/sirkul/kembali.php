<?php
if(isset($_GET['kode'])){
    // OPSI 2: 1 SK = 1 Transaksi dengan detail
    // Step 1: Ambil detail peminjaman dari tb_sirkulasi_detail
    $sql_detail = "SELECT d.id_detail, d.id_buku, d.jumlah, s.id_anggota 
                   FROM tb_sirkulasi_detail d
                   JOIN tb_sirkulasi s ON d.id_sk = s.id_sk
                   WHERE s.id_sk='".$_GET['kode']."'";
    $result_detail = mysqli_query($koneksi, $sql_detail);
    $row_detail = mysqli_fetch_array($result_detail);
    
    if (!$row_detail) {
        echo "<script>
        Swal.fire({title: 'Data Tidak Ditemukan', icon: 'error', confirmButtonText: 'OK'
        }).then((result) => {
            if (result.value) {
                window.location = 'index.php?page=data_sirkul';
            }
        })</script>";
        exit;
    }
    
    $id_buku = $row_detail['id_buku'];
    $id_anggota = $row_detail['id_anggota'];
    $jumlah_kembali = $row_detail['jumlah'];

    // Ambil nama anggota untuk logging
    $sql_anggota = "SELECT nama FROM tb_anggota WHERE id_anggota='$id_anggota'";
    $result_anggota = mysqli_query($koneksi, $sql_anggota);
    $row_anggota = mysqli_fetch_array($result_anggota);
    $nama_anggota = $row_anggota['nama'];

    // Step 2: Update status detail dan master SK menggunakan NOW()
    $sql_ubah = "UPDATE tb_sirkulasi_detail SET 
        status='KEM' 
        WHERE id_sk='".$_GET['kode']."';";
    
    $sql_ubah .= "UPDATE tb_sirkulasi SET 
        status='KEM', 
        tgl_kembali=DATE(NOW()) 
        WHERE id_sk='".$_GET['kode']."';";
    
    // Step 3: Increment stok buku sebanyak jumlah yang dikembalikan
    $sql_ubah .= "UPDATE tb_buku SET stok = stok + $jumlah_kembali WHERE id_buku='$id_buku';";
    
    // Step 4: Log aktivitas pengembalian ke tb_kunjungan menggunakan NOW()
    $petugas_id = $_SESSION['ses_username'];
    $sql_ubah .= "INSERT INTO tb_kunjungan (id_anggota, nama, level, tgl_kunjungan, waktu_kunjungan, jenis_aktivitas, id_buku, id_sk, keterangan) 
                  VALUES ('$id_anggota', '$nama_anggota', 'Siswa', DATE(NOW()), TIME(NOW()), 'Pengembalian', '$id_buku', '".$_GET['kode']."', 'Oleh $petugas_id, $jumlah_kembali buku(s)');";
        
    $query_ubah = mysqli_multi_query($koneksi, $sql_ubah);

    if ($query_ubah) {
        echo "<script>
        Swal.fire({title: 'Kembalikan Buku Berhasil', text: 'Mengembalikan $jumlah_kembali buku. Stok buku bertambah $jumlah_kembali', icon: 'success', confirmButtonText: 'OK'
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