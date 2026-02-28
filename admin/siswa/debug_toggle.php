<?php
// Start session jika belum
if (session_status() == PHP_SESSION_NONE) {
    session_start();
}

header('Content-Type: application/json');

// Log request untuk debugging
$log_data = [
    'timestamp' => date('Y-m-d H:i:s'),
    'method' => $_SERVER['REQUEST_METHOD'],
    'session_status' => session_status(),
    'session_id' => session_id(),
    'ses_username' => $_SESSION["ses_username"] ?? 'NOT SET',
    'post_data' => $_POST ?? 'EMPTY',
    'files' => get_included_files()
];

// Log ke file
$log_file = '../../logs/toggle_debug.log';
if (!is_dir('../../logs')) {
    mkdir('../../logs', 0777, true);
}
file_put_contents($log_file, json_encode($log_data) . PHP_EOL, FILE_APPEND);

// Coba include koneksi
try {
    include "../../inc/koneksi.php";
    $log_data['db_connected'] = true;
} catch (Exception $e) {
    $log_data['db_error'] = $e->getMessage();
}

// Response
echo json_encode($log_data);
?>
