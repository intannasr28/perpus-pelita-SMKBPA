<?php
// Test session dan toggle favorit secara manual

// Set session path
$tmp_path = realpath(__DIR__) . DIRECTORY_SEPARATOR . 'tmp';
if (!is_dir($tmp_path)) {
    mkdir($tmp_path, 0777, true);
}
ini_set('session.save_path', $tmp_path);

// Start session
session_start();

// Load DB connection
include "inc/koneksi.php";

// Test data
echo "<h2>Test Toggle Favorit</h2>";
echo "<pre>";

// Check session
echo "Session ID: " . session_id() . "\n";
echo "Session Status: " . session_status() . "\n";
echo "Session Data Count: " . count($_SESSION) . "\n";
echo "Session Username: " . ($_SESSION["ses_username"] ?? "KOSONG") . "\n";
echo "</pre>";

// Jika ada test data, jalankan toggle manual
if (isset($_GET['test_siswa']) && isset($_GET['test_buku'])) {
    echo "<h3>Manual Test Insert</h3>";
    
    // Set session secara manual untuk test
    $_SESSION["ses_username"] = $_GET['test_siswa'];
    
    $id_siswa = $koneksi->real_escape_string($_GET['test_siswa']);
    $id_buku = $koneksi->real_escape_string($_GET['test_buku']);
    
    echo "<p>Testing dengan ID Siswa: <strong>$id_siswa</strong> dan ID Buku: <strong>$id_buku</strong></p>";
    
    // Check if exists
    $sql_check = "SELECT id_favorit FROM tb_favorit WHERE id_anggota='$id_siswa' AND id_buku='$id_buku'";
    $result = $koneksi->query($sql_check);
    
    if ($result->num_rows > 0) {
        echo "<p>✓ Record sudah ada di favorit. Trying DELETE...</p>";
        
        $sql_delete = "DELETE FROM tb_favorit WHERE id_anggota='$id_siswa' AND id_buku='$id_buku'";
        if ($koneksi->query($sql_delete)) {
            echo "<p style='color: green;'>✓ DELETE Berhasil!</p>";
        } else {
            echo "<p style='color: red;'>✗ DELETE Gagal: " . $koneksi->error . "</p>";
        }
    } else {
        echo "<p>✓ Record tidak ada. Trying INSERT...</p>";
        
        $sql_insert = "INSERT INTO tb_favorit (id_anggota, id_buku) VALUES ('$id_siswa', '$id_buku')";
        if ($koneksi->query($sql_insert)) {
            echo "<p style='color: green;'>✓ INSERT Berhasil!</p>";
        } else {
            echo "<p style='color: red;'>✗ INSERT Gagal: " . $koneksi->error . "</p>";
        }
    }
    
    echo "<h3>Verify tb_favorit sekarang:</h3>";
    $sql_all = "SELECT * FROM tb_favorit WHERE id_anggota='$id_siswa'";
    $result_all = $koneksi->query($sql_all);
    
    echo "<table border='1' cellpadding='10'>";
    echo "<tr><th>ID Favorit</th><th>ID Anggota</th><th>ID Buku</th></tr>";
    while ($row = $result_all->fetch_assoc()) {
        echo "<tr><td>{$row['id_favorit']}</td><td>{$row['id_anggota']}</td><td>{$row['id_buku']}</td></tr>";
    }
    echo "</table>";
}

echo "<h3>Test Links:</h3>";
echo "<a href='test_toggle.php?test_siswa=A007&test_buku=B001'>Test Toggle A007 + B001</a><br>";
echo "<a href='test_toggle.php?test_siswa=A001&test_buku=B003'>Test Toggle A001 + B003</a><br>";
echo "<a href='test_toggle.php'>Reset</a>";
?>
