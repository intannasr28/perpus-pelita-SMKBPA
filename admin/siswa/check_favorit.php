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
    'in_favorit' => false,
    'message' => 'Buku tidak ada di favorit'
];

if (empty($_SESSION["ses_username"]) || empty($_POST['id_buku'])) {
    echo json_encode($response);
    exit;
}

try {
    include "../../inc/koneksi.php";
    
    $id_siswa = $koneksi->real_escape_string($_SESSION["ses_username"]);
    $id_buku = $koneksi->real_escape_string($_POST['id_buku']);
    
    $sql = "SELECT id_favorit FROM tb_favorit WHERE id_anggota='$id_siswa' AND id_buku='$id_buku'";
    $result = $koneksi->query($sql);
    
    if ($result && $result->num_rows > 0) {
        $response = [
            'in_favorit' => true,
            'message' => 'Buku ada di favorit'
        ];
    }
    
} catch (Exception $e) {
    $response['error'] = $e->getMessage();
}

echo json_encode($response);
?>
