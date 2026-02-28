<?php
/**
 * Diagnosa lengkap untuk session storage issues
 */
// Set custom session save path SEBELUM session_start()
$tmp_path = realpath(__DIR__ . '/../../') . DIRECTORY_SEPARATOR . 'tmp';
if (!is_dir($tmp_path)) {
	mkdir($tmp_path, 0777, true);
}
ini_set('session.save_path', $tmp_path);

header('Content-Type: application/json; charset=utf-8');

session_start();

header('Content-Type: application/json; charset=utf-8');

// Cek session storage configuration
$session_save_path = session_save_path();
$session_status = session_status();

// Status codes: 0 = PHP_SESSION_DISABLED, 1 = PHP_SESSION_NONE, 2 = PHP_SESSION_ACTIVE
$session_status_text = [
    0 => 'PHP_SESSION_DISABLED (Session disabled)',
    1 => 'PHP_SESSION_NONE (Session not started)',
    2 => 'PHP_SESSION_ACTIVE (Session active)'
];

// Cek apakah session save path ada dan writable
$save_path_exists = is_dir($session_save_path);
$save_path_writable = @is_writable($session_save_path);

// Get all session files
$session_files = [];
if ($save_path_exists && is_readable($session_save_path)) {
    $files = glob($session_save_path . '/sess_*');
    if ($files) {
        foreach ($files as $file) {
            $content = @file_get_contents($file);
            if ($content === false) {
                $content = '[ERROR: Cannot read file - permission denied]';
            }
            $size = strlen($content);
            $basename = basename($file);
            $session_files[] = [
                'name' => $basename,
                'size' => $size . ' bytes',
                'content_preview' => substr($content, 0, 200)
            ];
        }
    }
}

// Check current session
$current_session_file = $session_save_path . '/sess_' . session_id();
$current_file_exists = file_exists($current_session_file);
$current_file_content = '';
if ($current_file_exists) {
    $current_file_content = @file_get_contents($current_session_file);
    if ($current_file_content === false) {
        $current_file_content = '[ERROR: Cannot read file - permission denied]';
    }
}

$response = [
    'session_config' => [
        'session_status' => $session_status,
        'session_status_text' => $session_status_text[$session_status],
        'session_id' => session_id(),
        'session_name' => session_name(),
        'session_save_path' => $session_save_path,
        'session_save_path_exists' => $save_path_exists,
        'session_save_path_writable' => $save_path_writable,
        'session_auto_start' => ini_get('session.auto_start'),
        'session_use_cookies' => ini_get('session.use_cookies'),
        'session_use_only_cookies' => ini_get('session.use_only_cookies')
    ],
    'current_session_file' => [
        'path' => $current_session_file,
        'exists' => $current_file_exists,
        'content' => $current_file_content
    ],
    'session_data' => [
        'count' => count($_SESSION),
        'data' => $_SESSION
    ],
    'all_session_files' => $session_files,
    'php_info' => [
        'php_version' => phpversion(),
        'os' => php_uname()
    ]
];

echo json_encode($response, JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES);
