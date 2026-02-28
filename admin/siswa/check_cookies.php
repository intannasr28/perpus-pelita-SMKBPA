<?php
/**
 * Debug untuk cek cookies dan session di browser
 */
// Set custom session save path SEBELUM session_start()
$tmp_path = realpath(__DIR__ . '/../../') . DIRECTORY_SEPARATOR . 'tmp';
if (!is_dir($tmp_path)) {
	mkdir($tmp_path, 0777, true);
}
ini_set('session.save_path', $tmp_path);

header('Content-Type: application/json; charset=utf-8');

session_start();
include "../../inc/koneksi.php";

header('Content-Type: application/json; charset=utf-8');

$debug = [
    'session_status' => session_status(),
    'session_id' => session_id(),
    'session_name' => session_name(),
    'session_save_path' => session_save_path(),
    'php_session_status_text' => session_status() == PHP_SESSION_NONE ? 'PHP_SESSION_NONE (belum dimulai)' : (session_status() == PHP_SESSION_ACTIVE ? 'PHP_SESSION_ACTIVE (aktif)' : 'PHP_SESSION_DISABLED (disabled)'),
    'cookies_received' => $_COOKIE,
    'session_data' => $_SESSION,
    'session_data_count' => count($_SESSION),
    'has_ses_username' => isset($_SESSION['ses_username']),
    'ses_username_value' => isset($_SESSION['ses_username']) ? $_SESSION['ses_username'] : 'NOT SET',
    'browser_cookies_list' => array_keys($_COOKIE),
    'server_info' => [
        'php_version' => phpversion(),
        'server_protocol' => $_SERVER['SERVER_PROTOCOL'],
        'http_user_agent' => $_SERVER['HTTP_USER_AGENT'],
        'remote_addr' => $_SERVER['REMOTE_ADDR']
    ]
];

echo json_encode($debug, JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES);
