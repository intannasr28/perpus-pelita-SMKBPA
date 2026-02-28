<?php
$id_siswa = $_SESSION["ses_username"];

// Proses update jika form dikirim
$success = false;
$error = '';

if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['btnUpdate'])) {
    $nama = mysqli_real_escape_string($koneksi, $_POST['nama']);
    $jekel = mysqli_real_escape_string($koneksi, $_POST['jekel']);
    $kelas = mysqli_real_escape_string($koneksi, $_POST['kelas']);
    $no_hp = mysqli_real_escape_string($koneksi, $_POST['no_hp']);
    
    $sql_update = "UPDATE tb_anggota SET nama='$nama', jekel='$jekel', kelas='$kelas', no_hp='$no_hp' WHERE id_anggota='$id_siswa'";
    
    if ($koneksi->query($sql_update)) {
        // Update session nama
        $_SESSION["ses_nama"] = $nama;
        $success = true;
    } else {
        $error = "Gagal memperbarui profil: " . $koneksi->error;
    }
}

// Ambil data siswa dari tb_anggota
$sql = $koneksi->query("SELECT * FROM tb_anggota WHERE id_anggota='$id_siswa'");
$data_siswa = $sql->fetch_assoc();
?>

<section class="content-header">
    <h1>
        Edit Profil
        <small style="color: #0073b7; font-weight: 500;">Ubah informasi profil Anda</small>
    </h1>
</section>

<section class="content">
    <div class="row">
        <div class="col-md-8">
            <div class="box box-primary">
                <div class="box-header with-border">
                    <h3 class="box-title">Data Profil</h3>
                </div>

                <?php if ($success) { ?>
                    <div class="alert alert-success alert-dismissible">
                        <button type="button" class="close" data-dismiss="alert" aria-hidden="true">×</button>
                        <h4><i class="icon fa fa-check"></i> Berhasil!</h4>
                        Profil Anda berhasil diperbarui.
                    </div>
                <?php } ?>

                <?php if ($error) { ?>
                    <div class="alert alert-danger alert-dismissible">
                        <button type="button" class="close" data-dismiss="alert" aria-hidden="true">×</button>
                        <h4><i class="icon fa fa-ban"></i> Gagal!</h4>
                        <?php echo $error; ?>
                    </div>
                <?php } ?>

                <form method="post" action="">
                    <div class="box-body">
                        <div class="form-group">
                            <label>ID Anggota / NIS (Tidak dapat diubah)</label>
                            <input type="text" class="form-control" value="<?php echo $data_siswa['id_anggota']; ?>" disabled>
                        </div>

                        <div class="form-group">
                            <label>Nama Lengkap</label>
                            <input type="text" name="nama" class="form-control" value="<?php echo $data_siswa['nama']; ?>" required>
                        </div>

                        <div class="form-group">
                            <label>Jenis Kelamin</label>
                            <select name="jekel" class="form-control" required>
                                <option value="">-- Pilih Jenis Kelamin --</option>
                                <option value="Laki-laki" <?php echo ($data_siswa['jekel'] == 'Laki-laki') ? 'selected' : ''; ?>>Laki-laki</option>
                                <option value="Perempuan" <?php echo ($data_siswa['jekel'] == 'Perempuan') ? 'selected' : ''; ?>>Perempuan</option>
                            </select>
                        </div>

                        <div class="form-group">
                            <label>Kelas</label>
                            <input type="text" name="kelas" class="form-control" value="<?php echo $data_siswa['kelas']; ?>" required>
                        </div>

                        <div class="form-group">
                            <label>Nomor HP</label>
                            <input type="text" name="no_hp" class="form-control" value="<?php echo $data_siswa['no_hp']; ?>" required>
                        </div>
                    </div>
                    <div class="box-footer">
                        <button type="submit" name="btnUpdate" class="btn btn-primary">
                            <i class="fa fa-save"></i> Simpan Perubahan
                        </button>
                        <a href="?page=siswa/profile_siswa" class="btn btn-default">
                            <i class="fa fa-times"></i> Batal
                        </a>
                    </div>
                </form>
            </div>
        </div>

        <div class="col-md-4">
            <div class="box box-info">
                <div class="box-header with-border">
                    <h3 class="box-title">Informasi Akun</h3>
                </div>
                <div class="box-body">
                    <dl class="dl-horizontal">
                        <dt>Username</dt>
                        <dd><?php echo $_SESSION["ses_username"]; ?></dd>
                        <dt>Level</dt>
                        <dd><span class="label label-warning"><?php echo $_SESSION["ses_level"]; ?></span></dd>
                    </dl>
                </div>
            </div>

            <div class="box box-warning">
                <div class="box-header with-border">
                    <h3 class="box-title">Keamanan Akun</h3>
                </div>
                <div class="box-body">
                    <p><small>Untuk mengubah password, silakan klik tombol di bawah.</small></p>
                </div>
                <div class="box-footer">
                    <a href="?page=siswa/ganti_password" class="btn btn-warning btn-block">
                        <i class="fa fa-key"></i> Ganti Password
                    </a>
                </div>
            </div>
        </div>
    </div>
</section>
