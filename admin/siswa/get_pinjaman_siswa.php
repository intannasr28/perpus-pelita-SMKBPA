<?php
// Set custom session save path
$tmp_path = realpath(__DIR__ . '/../../') . DIRECTORY_SEPARATOR . 'tmp';
if (!is_dir($tmp_path)) {
    mkdir($tmp_path, 0777, true);
}
ini_set('session.save_path', $tmp_path);

header('Content-Type: application/json; charset=utf-8');
session_start();

include "../../inc/koneksi.php";

$response = [
    'success' => false,
    'count' => 0,
    'data' => []
];

if (empty($_SESSION['ses_username'])) {
    echo json_encode($response);
    exit;
}

$id_siswa = $_SESSION['ses_username'];

// Get pinjaman yang masih aktif (status PIN)
$sql = "SELECT s.id_sk, b.judul_buku, b.id_buku, s.tgl_pinjam, s.tgl_kembali, s.status 
        FROM tb_sirkulasi s 
        JOIN tb_buku b ON s.id_buku = b.id_buku 
        WHERE s.id_anggota = '$id_siswa' AND s.status = 'PIN' 
        ORDER BY s.tgl_pinjam DESC";

$result = $koneksi->query($sql);

if ($result && $result->num_rows > 0) {
    $response['success'] = true;
    $response['count'] = $result->num_rows;
    
    while ($row = $result->fetch_assoc()) {
        $response['data'][] = [
            'id_sk' => $row['id_sk'],
            'judul_buku' => $row['judul_buku'],
            'id_buku' => $row['id_buku'],
            'tgl_pinjam' => date('d-m-Y', strtotime($row['tgl_pinjam'])),
            'tgl_kembali' => date('d-m-Y', strtotime($row['tgl_kembali'])),
            'status' => $row['status']
        ];
    }
}

echo json_encode($response);
