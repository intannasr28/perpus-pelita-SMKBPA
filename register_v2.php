<?php
/**
 * ============================================
 * REGISTER.PHP - VERSI 2.0 DENGAN OPSI SOLUSI
 * ============================================
 * 
 * Pilihan Solusi:
 * 1. OPSI 1A: Format 4 Digit (A0001-A9999)
 * 2. OPSI 1B: Format Tahun Ajaran (A2024001-A2024999)
 * 3. OPSI 2: Soft Delete + ID Reuse
 * 
 * Maraca: Ubah $ID_FORMAT ke opsi yang diinginkan
 */

include "inc/koneksi.php";

// ============================================
// KONFIGURASI: Pilih Format ID
// ============================================
define('ID_FORMAT', 'FORMAT_4DIGIT'); // Pilih: FORMAT_4DIGIT | FORMAT_TAHUN | FORMAT_REUSE

// ============================================
// FUNGSI-FUNGSI HELPER
// ============================================

/**
 * OPSI 1A: Generate ID dengan Format 4 Digit (A0001-A9999)
 * Kapasitas: 9,999 siswa
 */
function generateID_4Digit($koneksi) {
    // Ambil ID terakhir
    $carikode = mysqli_query($koneksi, "SELECT id_anggota FROM tb_anggota WHERE id_anggota REGEXP '^A[0-9]{4}$' ORDER BY id_anggota DESC LIMIT 1");
    $datakode = mysqli_fetch_array($carikode);
    $kode = $datakode['id_anggota'];
    
    if ($kode) {
        // Extract 4 digit setelah 'A'
        $urut = (int)substr($kode, 1, 4);
        $tambah = $urut + 1;
        
        // Check if reached maximum
        if ($tambah > 9999) {
            return ['success' => false, 'message' => 'Kapasitas ID A0001-A9999 sudah penuh! Hubungi administrator.', 'id' => null];
        }
    } else {
        $tambah = 1;
    }
    
    // Format dengan 4 digit
    $format_id = "A" . str_pad($tambah, 4, "0", STR_PAD_LEFT);
    
    return ['success' => true, 'message' => 'ID generated successfully', 'id' => $format_id];
}

/**
 * OPSI 1B: Generate ID dengan Format Tahun Ajaran (A2024001-A2024999)
 * Kapasitas: 999 siswa per tahun
 */
function generateID_TahunAjaran($koneksi) {
    $tahun_ajaran = date('Y');
    $prefix = "A" . $tahun_ajaran; // A2024, A2025, dll
    
    // Ambil ID terakhir untuk tahun ajaranini
    $carikode = mysqli_query($koneksi, "SELECT id_anggota FROM tb_anggota WHERE id_anggota LIKE '$prefix%' ORDER BY id_anggota DESC LIMIT 1");
    $datakode = mysqli_fetch_array($carikode);
    $kode = $datakode['id_anggota'];
    
    if ($kode) {
        // Extract 3 digit di akhir
        $urut = (int)substr($kode, -3);
        $tambah = $urut + 1;
        
        // Check if reached maximum (999 per tahun)
        if ($tambah > 999) {
            return ['success' => false, 'message' => "Kapasitas ID tahun $tahun_ajaran sudah penuh! Gunakan tahun ajaran baru.", 'id' => null];
        }
    } else {
        $tambah = 1;
    }
    
    // Format: A2024001
    $format_id = $prefix . str_pad($tambah, 3, "0", STR_PAD_LEFT);
    
    return ['success' => true, 'message' => 'ID generated successfully', 'id' => $format_id];
}

/**
 * OPSI 2: Generate ID dengan Soft Delete + Reuse
 * - Jika ada siswa lulus, reuse ID mereka
 * - Jika tidak ada, generate normal (4 digit format)
 */
function generateID_WithReuse($koneksi) {
    // Cek ID available untuk reuse (status != AKTIF, urut dari terkecil)
    $queryReuse = "SELECT id_anggota FROM tb_anggota 
                   WHERE status IN ('LULUS', 'PINDAH', 'NONAKTIF') 
                   AND id_anggota REGEXP '^A[0-9]{4}$'
                   ORDER BY CAST(SUBSTRING(id_anggota, 2, 4) AS UNSIGNED) ASC 
                   LIMIT 1";
    
    $reuse_result = mysqli_query($koneksi, $queryReuse);
    $reuse_data = mysqli_fetch_array($reuse_result);
    
    if ($reuse_data) {
        // Ada ID yang bisa direuse
        $format_id = $reuse_data['id_anggota'];
        return ['success' => true, 'message' => 'ID reused dari siswa lulus', 'id' => $format_id, 'is_reuse' => true];
    } else {
        // Tidak ada reuse, generate normal
        return generateID_4Digit($koneksi);
    }
}

/**
 * Main Function - Generate ID sesuai pilihan format
 */
function generateID($koneksi, $format_type = ID_FORMAT) {
    switch ($format_type) {
        case 'FORMAT_4DIGIT':
            return generateID_4Digit($koneksi);
            break;
        case 'FORMAT_TAHUN':
            return generateID_TahunAjaran($koneksi);
            break;
        case 'FORMAT_REUSE':
            return generateID_WithReuse($koneksi);
            break;
        default:
            return generateID_4Digit($koneksi); // Default to 4 digit
    }
}

// ============================================
// GENERATE ID UNTUK FORM REGISTER
// ============================================
$id_result = generateID($koneksi);
if ($id_result['success']) {
    $format_id = $id_result['id'];
    $is_reuse = isset($id_result['is_reuse']) && $id_result['is_reuse'];
} else {
    $format_id = 'ERROR';
    $error_message = $id_result['message'];
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

        .alert-info {
            border-radius: 6px;
            margin-bottom: 20px;
            font-size: 13px;
        }

        .alert-warning {
            background-color: #fff3cd;
            border-color: #ffc107;
        }
    </style>
</head>
<body class="register-page">
    <div class="register-box">
        <div class="register-logo">
            <h3>PERPUS PELITA</h3>
        </div>

        <div class="register-box-body">
            <p class="login-box-msg">Daftar Akun Siswa</p>

            <?php if (!$id_result['success']): ?>
                <div class="alert alert-danger">
                    <strong>Error!</strong> <?php echo $error_message; ?>
                </div>
            <?php endif; ?>

            <?php if ($is_reuse): ?>
                <div class="alert alert-warning alert-info" role="alert">
                    <i class="fa fa-info-circle"></i> ID ini direuse dari siswa yang sudah lulus.
                </div>
            <?php endif; ?>

            <form action="proses_register.php" method="post">
                <div class="form-group">
                    <label>ID Anggota</label>
                    <input type="text" class="form-control" name="id_anggota" value="<?php echo $format_id; ?>" readonly>
                    <small class="form-text text-muted">ID otomatis terisi</small>
                </div>

                <div class="form-group">
                    <label>Nama Lengkap</label>
                    <input type="text" class="form-control" name="nama_anggota" placeholder="Masukkan nama lengkap" required>
                </div>

                <div class="form-group">
                    <label>NIS (Nomor Induk Siswa)</label>
                    <input type="text" class="form-control" name="nis" placeholder="Masukkan NIS" required>
                </div>

                <div class="form-group">
                    <label>Username</label>
                    <input type="text" class="form-control" name="username" placeholder="Masukkan username" required>
                </div>

                <div class="form-group">
                    <label>Password</label>
                    <input type="password" class="form-control" name="password" placeholder="Masukkan password" required>
                </div>

                <div class="form-group">
                    <label>Konfirmasi Password</label>
                    <input type="password" class="form-control" name="kpassword" placeholder="Konfirmasi password" required>
                </div>

                <div class="form-group">
                    <label>Email</label>
                    <input type="email" class="form-control" name="email" placeholder="Masukkan email" required>
                </div>

                <div class="form-group">
                    <label>Jenis Kelamin</label>
                    <select class="form-control" name="jekel" required>
                        <option value="">-- Pilih --</option>
                        <option value="Laki-laki">Laki-laki</option>
                        <option value="Perempuan">Perempuan</option>
                    </select>
                </div>

                <div class="form-group">
                    <label>Alamat</label>
                    <textarea class="form-control" name="alamat" rows="3" placeholder="Masukkan alamat" required></textarea>
                </div>

                <button type="submit" name="register" class="btn btn-primary btn-block">Daftar</button>
            </form>

            <div style="text-align: center; margin-top: 15px; font-size: 13px;">
                Sudah punya akun? <a href="login.php" style="color: #0051b3; font-weight: 600;">Login di sini</a>
            </div>
        </div>
    </div>

    <script src="bootstrap/js/bootstrap.min.js"></script>
</body>
</html>
