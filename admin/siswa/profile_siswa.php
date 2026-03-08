<?php
$id_siswa = $_SESSION["ses_username"];

// Ambil data siswa
$sql = $koneksi->query("SELECT * FROM tb_anggota WHERE id_anggota='$id_siswa'");
$data_siswa = $sql->fetch_assoc();
?>

<section class="content-header">
    <h1>
        Profil Siswa
        <small style="color: #0073b7; font-weight: 500;"><?php echo $data_siswa['nama']; ?></small>
    </h1>
</section>

<section class="content">
    <div class="row">
        <div class="col-md-4">
            <div class="box box-primary">
                <div class="box-header with-border">
                    <h3 class="box-title">Foto Profil</h3>
                </div>
                <div class="box-body text-center" style="padding: 20px;">
                    <img src="<?php echo getAvatarWithFallback($data_siswa['jekel']); ?>" class="img-circle" alt="User Image" style="width: 150px; height: 150px; border: 3px solid #007bff;">
                    <p style="margin-top: 15px;"><small><?php echo ucfirst(strtolower($data_siswa['jekel'])); ?></small></p>
                </div>
            </div>
        </div>

        <div class="col-md-8">
            <div class="box box-primary">
                <div class="box-header with-border">
                    <h3 class="box-title">Informasi Profil</h3>
                    <div class="box-tools pull-right">
                        <a href="?page=siswa/edit_profil" class="btn btn-sm btn-warning">
                            <i class="fa fa-edit"></i> Edit Profil
                        </a>
                    </div>
                </div>
                <div class="box-body">
                    <table class="table table-bordered">
                        <tr>
                            <td style="width: 30%; font-weight: bold;">ID Anggota / NIS</td>
                            <td><?php echo $data_siswa['id_anggota']; ?></td>
                        </tr>
                        <tr>
                            <td style="font-weight: bold;">Nama Lengkap</td>
                            <td><?php echo $data_siswa['nama']; ?></td>
                        </tr>
                        <tr>
                            <td style="font-weight: bold;">Jenis Kelamin</td>
                            <td><?php echo $data_siswa['jekel']; ?></td>
                        </tr>
                        <tr>
                            <td style="font-weight: bold;">Kelas</td>
                            <td><?php echo $data_siswa['kelas']; ?></td>
                        </tr>
                        <tr>
                            <td style="font-weight: bold;">Nomor HP</td>
                            <td><?php echo $data_siswa['no_hp']; ?></td>
                        </tr>
                    </table>
                </div>
            </div>

            <div class="box box-info">
                <div class="box-header with-border">
                    <h3 class="box-title">Informasi Akun</h3>
                </div>
                <div class="box-body">
                    <table class="table table-bordered">
                        <tr>
                            <td style="width: 30%; font-weight: bold;">Username</td>
                            <td><?php echo $_SESSION["ses_username"]; ?></td>
                        </tr>
                        <tr>
                            <td style="font-weight: bold;">Level</td>
                            <td><span class="label label-info"><?php echo $_SESSION["ses_level"]; ?></span></td>
                        </tr>
                    </table>
                </div>
                <div class="box-footer">
                    <a href="?page=siswa/ganti_password" class="btn btn-warning">
                        <i class="fa fa-key"></i> Ganti Password
                    </a>
                </div>
            </div>
        </div>
    </div>
</section>
