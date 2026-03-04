<?php
// =====================================================
// Database Connection Config
// Support: Local Development & Railway Production
// =====================================================

// Get database config from environment variables (Railway) or use defaults (Local)
$host = getenv('DB_HOST') ?: 'localhost';
$user = getenv('DB_USER') ?: 'root';
$password = getenv('DB_PASSWORD') ?: '';
$database = getenv('DB_NAME') ?: 'data_perpus';
$port = intval(getenv('DB_PORT') ?: 3306);

// Connection
$koneksi = new mysqli($host, $user, $password, $database, $port);

// Check connection
if ($koneksi->connect_error) {
    die("Connection failed: " . $koneksi->connect_error);
}

// Set charset to UTF-8
$koneksi->set_charset("utf8mb4");

// Set timezone
date_default_timezone_set(getenv('TIMEZONE') ?: 'Asia/Jakarta');
?>