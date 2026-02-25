<?php
include "inc/koneksi.php";
?>
<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <title>Registrasi Siswa | Perpus Pelita</title>
    <link rel="icon" href="dist/img/logo.png">
    <meta content="width=device-width, initial-scale=1, maximum-scale=1, user-scalable=no" name="viewport">
    <link rel="stylesheet" href="bootstrap/css/bootstrap.min.css">
    <link rel="stylesheet" href="dist/css/AdminLTE.min.css">
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@9"></script>
</head>
<body class="hold-transition register-page">
    <div class="register-box">
        <div class="register-logo">
            <a href="login.php"><b>Perpus</b>Pelita</a>
        </div>
        <div class="register-box-body">
            <p class="login-box-msg">Daftar Anggota Baru</p>
            <form action="" method="post">
                <div class="form-group has-feedback">
                    <input type="text" name="id_anggota" class="form-control" placeholder="NIS / ID Anggota" required>
                </div>
                <div class="form-group has-feedback">
                    <input type="text" name="nama" class="form-control" placeholder="Nama Lengkap" required>
                </div>
                <div class="form-group has-feedback">
                    <select name="jekel" class="form-control" required>
                        <option value="">-- Pilih Jenis Kelamin --</option>
                        <option value="Laki-laki">Laki-laki</option>
                        <option value="Perempuan">Perempuan</option>
                    </select>
                </div>
                <div class="form-group has-feedback">
                    <input type="text" name="kelas" class="form-control" placeholder="Kelas" required>
                </div>
                <div class="form-group has-feedback">
                    <input type="text" name="no_hp" class="form-control" placeholder="No HP" required>
                </div>
                <div class="form-group has-feedback">
                    <input type="password" name="password" class="form-control" placeholder="Password untuk Login" required>
                </div>
                <div class="row">
                    <div class="col-xs-12">
                        <button type="submit" name="btnDaftar" class="btn btn-primary btn-block btn-flat">Daftar</button>
                    </div>
                </div>
            </form>
            <br>
            <a href="login.php" class="text-center">Sudah punya akun? Login di sini</a>
        </div>
    </div>

<?php
if (isset($_POST['btnDaftar'])) {
    $id_anggota = mysqli_real_escape_string($koneksi, $_POST['id_anggota']);
    $nama = mysqli_real_escape_string($koneksi, $_POST['nama']);
    $jekel = $_POST['jekel'];
    $kelas = mysqli_real_escape_string($koneksi, $_POST['kelas']);
    $no_hp = mysqli_real_escape_string($koneksi, $_POST['no_hp']);
    $pass = mysqli_real_escape_string($koneksi, md5($_POST['password']));

    // 1. Simpan ke tb_anggota
    $sql_mhs = "INSERT INTO tb_anggota (id_anggota, nama, jekel, kelas, no_hp) VALUES 
                ('$id_anggota', '$nama', '$jekel', '$kelas', '$no_hp')";
    
    // 2. Simpan ke tb_pengguna untuk login
    $sql_user = "INSERT INTO tb_pengguna (nama_pengguna, username, password, level) VALUES 
                 ('$nama', '$id_anggota', '$pass', 'Siswa')";

    $query_mhs = mysqli_query($koneksi, $sql_mhs);
    $query_user = mysqli_query($koneksi, $sql_user);

    if ($query_mhs && $query_user) {
        echo "<script>
            Swal.fire({title: 'Registrasi Berhasil', text: 'Silahkan Login menggunakan NIS Anda', icon: 'success'}
            ).then((result) => { if (result.value) { window.location = 'login.php'; } })
        </script>";
    } else {
        echo "<script>
            Swal.fire({title: 'Registrasi Gagal', text: 'NIS mungkin sudah terdaftar', icon: 'error'}
            ).then((result) => { if (result.value) { window.location = 'register.php'; } })
        </script>";
    }
}
?>
</body>
</html>