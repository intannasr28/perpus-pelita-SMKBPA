<?php
// =====================================================
// Database Connection Config
// Support: Local Development & Railway Production
// =====================================================

// Get database config from environment variables (Railway) or use defaults (Local)
$host = getenv('DB_HOST') ?: 'mysql.railway.internal';
$user = getenv('DB_USER') ?: 'root';
$password = getenv('DB_PASSWORD') ?: 'jsmGMvjLtEpQYXgbqQMVKcWvTQSZwEnA';
$database = getenv('DB_NAME') ?: 'railway';
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

// Session configuration untuk Windows compatibility
$tmp_path = realpath(__DIR__ . '/..') . DIRECTORY_SEPARATOR . 'tmp';
if (!is_dir($tmp_path)) {
    @mkdir($tmp_path, 0777, true);
}
ini_set('session.save_path', $tmp_path);
?>