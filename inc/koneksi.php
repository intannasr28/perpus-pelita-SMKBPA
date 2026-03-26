<?php
// Prevent multiple inclusions
if (defined('DATABASE_CONFIG_LOADED')) {
    return;
}
define('DATABASE_CONFIG_LOADED', true);

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

// =====================================================
// Helper Function - Get Avatar by Gender
// =====================================================
function getAvatarByGender($jekel = '') {
    // Trim dan lowercase untuk comparison
    $jekel_clean = strtolower(trim($jekel));
    
    // Default avatar based on gender
    // Support untuk berbagai format: "Laki-laki", "laki laki", "L", "M", dll
    if (
        $jekel_clean == 'laki-laki' || 
        $jekel_clean == 'laki laki' || 
        $jekel_clean == 'laki' ||
        $jekel_clean == 'l' || 
        $jekel_clean == 'm' ||
        strpos($jekel_clean, 'laki') === 0  // starts with 'laki'
    ) {
        return 'dist/img/avatar_laki_laki.png';
    } 
    // Support untuk "Perempuan", "wanita", "P", "F", dll
    else if (
        $jekel_clean == 'perempuan' || 
        $jekel_clean == 'wanita' ||
        $jekel_clean == 'p' || 
        $jekel_clean == 'f' ||
        strpos($jekel_clean, 'perem') === 0  // starts with 'perem'
    ) {
        return 'dist/img/avatar_perempuan.png';
    } 
    else {
        // Default ke perempuan jika format tidak dikenali
        return 'dist/img/avatar_perempuan.png';
    }
}

/**
 * Fungsi alternatif dengan fallback jika file tidak ada
 */
function getAvatarWithFallback($jekel = '') {
    $avatar = getAvatarByGender($jekel);
    // Jika file tidak ada, gunakan avatar default universal
    if (!file_exists($avatar)) {
        return 'dist/img/avatar.png';
    }
    return $avatar;
}
?>