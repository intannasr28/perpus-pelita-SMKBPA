<?php
$id_siswa = $_SESSION["ses_username"];

if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['btnGantiPassword'])) {
    $password_lama = mysqli_real_escape_string($koneksi, md5($_POST['password_lama']));
    $password_baru = mysqli_real_escape_string($koneksi, md5($_POST['password_baru']));
    $password_konfirmasi = mysqli_real_escape_string($koneksi, md5($_POST['password_konfirmasi']));
    
    // Cek password lama
    $sql_check = $koneksi->query("SELECT password FROM tb_pengguna WHERE username='$id_siswa'");
    $data_check = $sql_check->fetch_assoc();
    
    if ($data_check['password'] == $password_lama) {
        if ($password_baru == $password_konfirmasi) {
            $sql_update = "UPDATE tb_pengguna SET password='$password_baru' WHERE username='$id_siswa'";
            
            if ($koneksi->query($sql_update)) {
                $success = true;
            } else {
                $error = "Gagal mengubah password";
            }
        } else {
            $error = "Password baru dan konfirmasi tidak cocok";
        }
    } else {
        $error = "Password lama tidak sesuai";
    }
}
?>

<section class="content-header">
    <h1>
        Ganti Password
        <small style="color: #0073b7; font-weight: 500;">Ubah password akun Anda</small>
    </h1>
</section>

<section class="content">
    <div class="row">
        <div class="col-md-6">
            <div class="box box-warning">
                <div class="box-header with-border">
                    <h3 class="box-title">Formulir Ganti Password</h3>
                </div>
                <form method="post">
                    <div class="box-body">
                        <?php if (isset($success) && $success) { ?>
                            <div class="alert alert-success alert-dismissible">
                                <button type="button" class="close" data-dismiss="alert" aria-hidden="true">×</button>
                                <h4><i class="icon fa fa-check"></i> Berhasil!</h4>
                                Password Anda berhasil diubah.
                            </div>
                        <?php } ?>

                        <?php if (isset($error)) { ?>
                            <div class="alert alert-danger alert-dismissible">
                                <button type="button" class="close" data-dismiss="alert" aria-hidden="true">×</button>
                                <h4><i class="icon fa fa-ban"></i> Gagal!</h4>
                                <?php echo $error; ?>
                            </div>
                        <?php } ?>

                        <div class="form-group">
                            <label>Password Lama</label>
                            <input type="password" name="password_lama" class="form-control" placeholder="Masukkan password lama" required>
                            <small class="text-muted">Masukkan password yang sedang Anda gunakan</small>
                        </div>

                        <div class="form-group">
                            <label>Password Baru</label>
                            <input type="password" name="password_baru" class="form-control" placeholder="Masukkan password baru" required>
                            <small class="text-muted">Gunakan password yang kuat (minimal 6 karakter)</small>
                        </div>

                        <div class="form-group">
                            <label>Konfirmasi Password Baru</label>
                            <input type="password" name="password_konfirmasi" class="form-control" placeholder="Konfirmasi password baru" required>
                            <small class="text-muted">Ulangi password baru yang sama</small>
                        </div>
                    </div>
                    <div class="box-footer">
                        <button type="submit" name="btnGantiPassword" class="btn btn-warning">
                            <i class="fa fa-save"></i> Ubah Password
                        </button>
                        <a href="?page=siswa/profile_siswa" class="btn btn-default">Batal</a>
                    </div>
                </form>
            </div>

            <div class="box box-info">
                <div class="box-header with-border">
                    <h3 class="box-title">Tips Keamanan Password</h3>
                </div>
                <div class="box-body">
                    <ul>
                        <li>Gunakan kombinasi huruf besar, huruf kecil, angka, dan simbol</li>
                        <li>Jangan gunakan informasi pribadi (nama, tanggal lahir)</li>
                        <li>Jangan bagikan password ke orang lain</li>
                        <li>Ubah password secara berkala</li>
                        <li>Jangan gunakan password yang sama di berbagai akun</li>
                    </ul>
                </div>
            </div>
        </div>
    </div>
</section>
