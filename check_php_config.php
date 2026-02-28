<?php
/**
 * Check PHP session configuration
 */
// Set custom session save path SEBELUM session_start()
$tmp_path = realpath(__DIR__) . DIRECTORY_SEPARATOR . 'tmp';
if (!is_dir($tmp_path)) {
	mkdir($tmp_path, 0777, true);
}
ini_set('session.save_path', $tmp_path);

header('Content-Type: application/json; charset=utf-8');

$php_info = [
    'php_version' => phpversion(),
    'session_save_path' => ini_get('session.save_path'),
    'session_save_path_exists' => is_dir(ini_get('session.save_path')),
    'session_save_path_writable' => @is_writable(ini_get('session.save_path')),
    'session_auto_start' => ini_get('session.auto_start'),
    'session_use_cookies' => ini_get('session.use_cookies'),
    'session_use_only_cookies' => ini_get('session.use_only_cookies'),
    'session_cookie_httponly' => ini_get('session.cookie_httponly'),
    'session_name' => ini_get('session.name'),
    'session_gc_maxlifetime' => ini_get('session.gc_maxlifetime'),
    'php_uname' => php_uname(),
    'windows_temp_dir' => getenv('TEMP'),
    'sys_temp_dir' => sys_get_temp_dir(),
];

echo json_encode($php_info, JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES);
