<?php
// Mengambil data dari Variables di Railway
$host     = getenv('MYSQLHOST') ?: 'mysql.railway.internal'; 
$user     = getenv('MYSQLUSER') ?: 'root';
$password = getenv('MYSQLPASSWORD') ?: 'jsmGMvjLtEpQYXgbqQMVKcWvTQSZwEnA'; 
$database = getenv('MYSQLDATABASE') ?: 'railway'; 
$port     = intval(getenv('MYSQLPORT') ?: 3306);

// Membuat koneksi
$koneksi = new mysqli($host, $user, $password, $database, $port);

// Cek koneksi
if ($koneksi->connect_error) {
    die("Koneksi gagal: " . $koneksi->connect_error);
}

// Set charset agar tidak error saat baca data
$koneksi->set_charset("utf8mb4");
?>