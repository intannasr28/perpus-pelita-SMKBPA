<?php
/**
 * Test session persistence sederhana
 */
// Set custom session save path SEBELUM session_start()
$tmp_path = realpath(__DIR__) . DIRECTORY_SEPARATOR . 'tmp';
if (!is_dir($tmp_path)) {
	mkdir($tmp_path, 0777, true);
}
ini_set('session.save_path', $tmp_path);

header('Content-Type: application/json; charset=utf-8');

session_start();

// Test set session
$_SESSION['test_time'] = date('Y-m-d H:i:s');
$_SESSION['test_value'] = 'Session test value - ' . uniqid();
$_SESSION['test_array'] = ['item1', 'item2', 'item3'];

// Force write session to disk
session_write_close();

// Now re-open and read
session_start();

$response = [
    'action' => 'Set and verify session data',
    'session_id' => session_id(),
    'session_data_before_close' => [
        'test_time' => $_SESSION['test_time'] ?? 'NOT SET',
        'test_value' => $_SESSION['test_value'] ?? 'NOT SET',
        'test_array' => $_SESSION['test_array'] ?? 'NOT SET',
    ],
    'session_save_path' => session_save_path(),
    'session_file_path' => session_save_path() . '/sess_' . session_id(),
    'session_file_exists' => file_exists(session_save_path() . '/sess_' . session_id()),
    'session_file_size' => @filesize(session_save_path() . '/sess_' . session_id()),
    'all_session_data' => $_SESSION,
    'test_result' => (isset($_SESSION['test_value']) && !empty($_SESSION['test_value'])) ? 'PASS - Session persisted!' : 'FAIL - Session lost!'
];

echo json_encode($response, JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES);
