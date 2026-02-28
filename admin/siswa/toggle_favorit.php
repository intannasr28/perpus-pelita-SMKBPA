<?php
// Set custom session save path SEBELUM session_start()
$tmp_path = realpath(__DIR__ . '/../../') . DIRECTORY_SEPARATOR . 'tmp';
if (!is_dir($tmp_path)) {
	mkdir($tmp_path, 0777, true);
}
ini_set('session.save_path', $tmp_path);

// Set header JSON PERTAMA
header('Content-Type: application/json; charset=utf-8');

// Session options untuk compatibility
ini_set('session.use_only_cookies', 0);
ini_set('session.use_trans_sid', 1);

// Start atau resume session
if (session_status() === PHP_SESSION_NONE) {
	session_start();
}

// Sekarang include koneksi database
include "../../inc/koneksi.php";

// Response default
$response = ['status' => 'error', 'message' => 'Terjadi kesalahan'];

// Debug: Log session state
error_log('Toggle Favorit - Session ID: ' . session_id() . ', Has ses_username: ' . (isset($_SESSION["ses_username"]) ? 'YES' : 'NO') . ', Session count: ' . count($_SESSION) . ', POST id_buku: ' . (!empty($_POST['id_buku']) ? $_POST['id_buku'] : 'KOSONG'));

// Step 1: Cek session
if (empty($_SESSION["ses_username"])) {
    // Session kosong = user belum login atau session expired
    $response['message'] = 'Session expired - silakan login ulang (anda harus login terlebih dahulu)';
    $response['status'] = 'session_error';
    http_response_code(401);
    header('Content-Type: application/json; charset=utf-8');
    die(json_encode($response));
}

// Step 2: Cek method request
if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    $response['message'] = 'Method request harus POST';
    echo json_encode($response);
    exit;
}

// Step 3: Cek id_buku
if (empty($_POST['id_buku'])) {
    $response['message'] = 'ID buku tidak ditemukan';
    echo json_encode($response);
    exit;
}

// Step 4: Validasi koneksi database
if (!isset($koneksi) || $koneksi->connect_error) {
    $response['message'] = 'Koneksi database gagal: ' . ($koneksi->connect_error ?? 'tidak diketahui');
    echo json_encode($response);
    exit;
}

// Step 5: Proses favorit
try {
    $id_siswa = $koneksi->real_escape_string($_SESSION["ses_username"]);
    $id_buku = $koneksi->real_escape_string($_POST['id_buku']);
    
    // Log untuk debugging
    error_log("Proses Favorit - ID Siswa: $id_siswa, ID Buku: $id_buku");
    
    // Cek apakah sudah ada di favorit
    $sql_check = "SELECT id_favorit FROM tb_favorit WHERE id_anggota='$id_siswa' AND id_buku='$id_buku' LIMIT 1";
    $result_check = $koneksi->query($sql_check);
    
    if ($result_check === false) {
        throw new Exception("Query check error: " . $koneksi->error);
    }
    
    if ($result_check->num_rows > 0) {
        // Favorit sudah ada, maka hapus
        $sql_delete = "DELETE FROM tb_favorit WHERE id_anggota='$id_siswa' AND id_buku='$id_buku'";
        error_log("Menjalankan DELETE: " . $sql_delete);
        
        if ($koneksi->query($sql_delete)) {
            $response = ['status' => 'removed', 'message' => 'Buku dihapus dari favorit'];
            error_log("Berhasil DELETE favorit");
        } else {
            throw new Exception("Delete error: " . $koneksi->error);
        }
    } else {
        // Favorit belum ada, maka tambah
        $sql_insert = "INSERT INTO tb_favorit (id_anggota, id_buku) VALUES ('$id_siswa', '$id_buku')";
        error_log("Menjalankan INSERT: " . $sql_insert);
        
        if ($koneksi->query($sql_insert)) {
            $response = ['status' => 'added', 'message' => 'Buku ditambahkan ke favorit'];
            error_log("Berhasil INSERT favorit");
        } else {
            throw new Exception("Insert error: " . $koneksi->error);
        }
    }
    
} catch (Exception $e) {
    error_log("Exception: " . $e->getMessage());
    $response = ['status' => 'error', 'message' => $e->getMessage()];
}

// Save session explicitly
session_write_close();

// Send response dan exit
echo json_encode($response);
exit;
?>

