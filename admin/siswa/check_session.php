<?php
// Set custom session save path SEBELUM session_start()
$tmp_path = realpath(__DIR__ . '/../../') . DIRECTORY_SEPARATOR . 'tmp';
if (!is_dir($tmp_path)) {
	mkdir($tmp_path, 0777, true);
}
ini_set('session.save_path', $tmp_path);

header('Content-Type: application/json');
session_start();

$response = [
    'logged_in' => !empty($_SESSION["ses_username"]),
    'username' => $_SESSION["ses_username"] ?? 'NOT SET',
    'level' => $_SESSION["ses_level"] ?? 'NOT SET'
];

echo json_encode($response);
?>
