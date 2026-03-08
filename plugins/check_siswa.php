<?php
/**
 * AJAX handler untuk check status siswa dan denda
 * File: plugins/check_siswa.php
 * 
 * Menerima POST request dengan id_anggota
 * Return JSON dengan status, denda, dan informasi
 */

// Include koneksi dan helper
require_once '../inc/koneksi.php';
require_once '../inc/helper_denda.php';

header('Content-Type: application/json');

$id_anggota = isset($_POST['id_anggota']) ? mysqli_real_escape_string($koneksi, $_POST['id_anggota']) : null;

if (!$id_anggota) {
	echo json_encode(['error' => 'ID Anggota tidak ditemukan']);
	exit;
}

// Cek status akun
$status = cekStatusSiswa($id_anggota, $koneksi);

// Cek denda
$denda = cekDendaSiswa($id_anggota, $koneksi);

// Format respon
$response = [
	'status' => $status['status'],
	'alasan' => $status['alasan'] ?? '',
	'tgl_nonaktif' => $status['tgl_nonaktif'] ?? '',
	'ada_denda' => $denda['ada_denda'],
	'jumlah_belum_dibayar' => $denda['jumlah_belum_dibayar'],
	'total_denda_format' => number_format($denda['jumlah_belum_dibayar'], 0, ',', '.'),
	'jumlah_buku_terlambat' => $denda['jumlah_buku_terlambat'],
	'daftar_denda' => $denda['daftar_denda']
];

echo json_encode($response);
?>
