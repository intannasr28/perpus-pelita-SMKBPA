<?php
include "inc/koneksi.php";

// Auto-generate ID Anggota
$carikode = mysqli_query($koneksi, "SELECT id_anggota FROM tb_anggota ORDER BY id_anggota DESC LIMIT 1");
$datakode = mysqli_fetch_array($carikode);
$kode = $datakode['id_anggota'];

if ($kode) {
    $urut = substr($kode, 1, 3);
    $tambah = (int) $urut + 1;
} else {
    $tambah = 1;
}

if (strlen($tambah) == 1) {
    $format_id = "A" . "00" . $tambah;
} else if (strlen($tambah) == 2) {
    $format_id = "A" . "0" . $tambah;
} else if (strlen($tambah) == 3) {
    $format_id = "A" . $tambah;
}
?>
<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <title>Registrasi Siswa | Perpus Pelita</title>
    <link rel="icon" href="dist/img/logo.png">
    <meta content="width=device-width, initial-scale=1, maximum-scale=1, user-scalable=no" name="viewport">
    <!-- Google Fonts - Poppins -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    <!-- Bootstrap 3.3.6 -->
    <link rel="stylesheet" href="bootstrap/css/bootstrap.min.css">
    <!-- Font Awesome -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/4.5.0/css/font-awesome.min.css">
    <!-- Theme style -->
    <link rel="stylesheet" href="dist/css/AdminLTE.min.css">
    <!-- Sweet Alert -->
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@9"></script>

    <style>
        body.register-page {
            background: url('dist/img/background.png') no-repeat center center fixed;
            background-size: cover;
            background-attachment: fixed;
            min-height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
            font-family: 'Poppins', sans-serif;
            position: relative;
        }

        body.register-page::before {
            content: '';
            position: fixed;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            background: rgba(0, 31, 63, 0.5);
            z-index: -1;
        }

        .register-box {
            width: 380px;
            margin: auto;
            position: relative;
            z-index: 1;
        }

        .register-logo {
            text-align: center;
            margin-bottom: 30px;
        }

        .register-logo h3 {
            font-size: 28px;
            font-weight: 700;
            text-shadow: 2px 2px 8px rgba(0, 0, 0, 0.5);
            color: #ffffff;
        }

        .register-box-body {
            background: #fff;
            border-radius: 16px;
            box-shadow: 0 10px 40px rgba(0, 0, 0, 0.15);
            padding: 32px 30px 30px 30px;
            border: 1.5px solid #f0f0f0;
            backdrop-filter: blur(10px);
            position: relative;
            overflow: hidden;
        }

        .register-box-body .login-box-msg {
            font-size: 18px;
            font-weight: 600;
            color: #001f3f;
            margin-bottom: 25px;
            text-align: center;
        }

        .form-control {
            border: 2px solid #e0e0e0;
            border-radius: 6px;
            height: 42px;
            font-size: 14px;
            transition: all 0.3s ease;
            font-family: 'Poppins', sans-serif;
        }

        .form-control:focus {
            border-color: #0051b3;
            box-shadow: 0 0 10px rgba(0, 81, 179, 0.2);
            background-color: #f8f9ff;
        }

        .form-control::placeholder {
            color: #999;
            font-size: 13px;
        }

        select.form-control {
            appearance: none;
            background-image: url("data:image/svg+xml;charset=UTF-8,%3csvg xmlns='http://www.w3.org/2000/svg' viewBox='0 0 24 24' fill='none' stroke='%230051b3' stroke-width='2' stroke-linecap='round' stroke-linejoin='round'%3e%3cpolyline points='6 9 12 15 18 9'%3e%3c/polyline%3e%3c/svg%3e");
            background-repeat: no-repeat;
            background-position: right 10px center;
            background-size: 20px;
            padding-right: 35px;
        }

        .btn-primary {
            background: linear-gradient(90deg, #0051b3 0%, #003d82 100%);
            border: none;
            color: #fff;
            font-weight: 700;
            height: 44px;
            font-size: 16px;
            border-radius: 8px;
            box-shadow: 0 2px 12px rgba(0, 81, 179, 0.18), 0 1.5px 0 #fff inset;
            transition: all 0.3s cubic-bezier(.4,2,.3,1);
            position: relative;
            overflow: hidden;
        }

        .btn-primary::after {
            content: '';
            position: absolute;
            left: 0; top: 0; width: 100%; height: 100%;
            background: linear-gradient(120deg, rgba(255,255,255,0.35) 0%, rgba(255,255,255,0.08) 100%);
            opacity: 0.7;
            pointer-events: none;
            border-radius: 8px;
            transition: opacity 0.3s;
        }

        .btn-primary:hover,
        .btn-primary:focus {
            background: linear-gradient(90deg, #003d82 0%, #0051b3 100%);
            box-shadow: 0 6px 20px rgba(0, 81, 179, 0.22), 0 2px 0 #fff inset;
            transform: translateY(-2px) scale(1.03);
        }

        .btn-primary:active {
            transform: translateY(0) scale(0.98);
        }

        .btn-primary b {
            letter-spacing: 0.5px;
            text-shadow: 0 1px 4px rgba(0,0,0,0.08);
        }

        .input-label {
            font-size: 12px;
            color: #666;
            font-weight: 500;
            margin-bottom: 6px;
            display: block;
        }

        .register-footer {
            text-align: center;
            margin-top: 20px;
            padding-top: 20px;
            border-top: 1px solid #f0f0f0;
        }

        .register-footer a {
            color: #0051b3;
            text-decoration: none;
            font-weight: 600;
            transition: color 0.3s;
        }

        .register-footer a:hover {
            color: #003d82;
            text-decoration: underline;
        }

        .register-logo-img {
            animation: slideDown 0.6s ease-out;
        }

        @keyframes slideDown {
            from {
                opacity: 0;
                transform: translateY(-20px);
            }
            to {
                opacity: 1;
                transform: translateY(0);
            }
        }

        .form-group {
            margin-bottom: 16px;
        }
    </style>
</head>
<body class="hold-transition register-page">
    <div class="register-box">
        <div class="register-logo">
            <h3>
                <b>Registrasi Anggota Baru</b>
            </h3>
        </div>

        <div class="register-box-body">
            <center class="register-logo-img">
                <img src="dist/img/logo.jpeg" width="140px" />
            </center>
            <br>
            <p class="login-box-msg">Daftar Akun Siswa</p>
            <form action="" method="post">
                <div class="form-group">
                    <label class="input-label">ID Anggota (Auto-Generated)</label>
                    <input type="text" name="id_anggota" class="form-control" value="<?php echo $format_id; ?>" readonly style="background-color: #f5f5f5; cursor: not-allowed;">
                </div>

                <div class="form-group">
                    <label class="input-label">Nama Lengkap</label>
                    <input type="text" name="nama" class="form-control" placeholder="Masukkan nama lengkap Anda" required>
                </div>

                <div class="form-group">
                    <label class="input-label">Jenis Kelamin</label>
                    <select name="jekel" class="form-control" required>
                        <option value="">-- Pilih Jenis Kelamin --</option>
                        <option value="Laki-laki">Laki-laki</option>
                        <option value="Perempuan">Perempuan</option>
                    </select>
                </div>

                <div class="form-group">
                    <label class="input-label">Kelas</label>
                    <input type="text" name="kelas" class="form-control" placeholder="Contoh: X IPA 1" required>
                </div>

                <div class="form-group">
                    <label class="input-label">No. HP / WhatsApp</label>
                    <input type="text" name="no_hp" class="form-control" placeholder="08xxxxxxxxxx" required>
                </div>

                <div class="form-group">
                    <label class="input-label">Password (untuk Login)</label>
                    <input type="password" name="password" class="form-control" id="password" placeholder="Buat password yang aman" required>
                </div>

                <div class="row" style="margin-top: 25px;">
                    <div class="col-xs-12">
                        <button type="submit" name="btnDaftar" class="btn btn-primary btn-block btn-flat">
                            <b>Daftar</b>
                        </button>
                    </div>
                </div>
            </form>

            <div class="register-footer">
                <p style="margin-bottom: 0; font-size: 13px; color: #666;">Sudah punya akun? <a href="login.php">Login di sini</a></p>
            </div>
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
            Swal.fire({
                title: 'Registrasi Berhasil', 
                text: 'ID Anggota Anda: " . $id_anggota . ". Silahkan Login menggunakan ID dan Password Anda', 
                icon: 'success'
            }).then((result) => { if (result.value) { window.location = 'login.php'; } })
        </script>";
    } else {
        echo "<script>
            Swal.fire({title: 'Registrasi Gagal', text: 'Terjadi kesalahan saat registrasi', icon: 'error'}
            ).then((result) => { if (result.value) { window.location = 'register.php'; } })
        </script>";
    }
}
?>

    <!-- jQuery 2.2.3 -->
    <script src="plugins/jQuery/jquery-2.2.3.min.js"></script>
    <!-- Bootstrap 3.3.6 -->
    <script src="bootstrap/js/bootstrap.min.js"></script>
    <script>
    // Form validation dan effects
    $(function() {
        // Add focus effects to form inputs
        $('input[type="text"], input[type="password"], select').on('focus', function() {
            $(this).parent().find('label').css('color', '#0051b3');
        }).on('blur', function() {
            $(this).parent().find('label').css('color', '#666');
        });

        // Form submission validation
        $('form').on('submit', function(e) {
            if (!$('input[name="nama"]').val() || !$('select[name="jekel"]').val() || 
                !$('input[name="kelas"]').val() || !$('input[name="no_hp"]').val() || 
                !$('input[name="password"]').val()) {
                e.preventDefault();
                Swal.fire({
                    title: 'Form Tidak Lengkap',
                    text: 'Silakan isi semua field yang diperlukan',
                    icon: 'warning',
                    confirmButtonText: 'OK'
                });
            }
        });
    });
    </script>
</body>
</html>