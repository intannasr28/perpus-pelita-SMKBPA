<?php
/**
 * Helper Functions untuk Fitur Denda dan Status Siswa
 * File: inc/helper_denda.php
 * 
 * Fungsi-fungsi untuk:
 * - Cek status akun siswa (AKTIF/NONAKTIF)
 * - Cek denda siswa yang belum dibayar
 * - Update status siswa menjadi nonaktif
 * - Reaktivasi akun siswa
 * - Hitung total denda per siswa
 */

/**
 * Cek apakah siswa punya denda belum dibayar (terlambat kembali buku)
 * 
 * @param string $id_anggota - ID siswa/anggota
 * @param mysqli $koneksi - Koneksi database
 * @return array [
 *   'ada_denda' => true/false,
 *   'jumlah_belum_dibayar' => int (nominal denda belum dibayar),
 *   'jumlah_buku_belum_dikembali' => int (jumlah buku terlambat),
 *   'daftar_denda' => array of denda details
 * ]
 */
function cekDendaSiswa($id_anggota, $koneksi) {
	$tarif_denda = 1000; // Rp 1000 per hari
	
	// Query untuk cek buku yang belum dikembalikan dan sudah lewat deadline
	$query = "SELECT 
		tb_sirkulasi.id_sk,
		tb_sirkulasi.tgl_pinjam,
		tb_sirkulasi.tgl_kembali,
		tb_sirkulasi.status,
		tb_buku.judul_buku,
		IF(tb_sirkulasi.status='PIN',
			IF(DATEDIFF(CURDATE(), DATE_ADD(tb_sirkulasi.tgl_pinjam, INTERVAL 7 DAY))<=0, 0, DATEDIFF(CURDATE(), DATE_ADD(tb_sirkulasi.tgl_pinjam, INTERVAL 7 DAY))),
			IF(DATEDIFF(tb_sirkulasi.tgl_kembali, DATE_ADD(tb_sirkulasi.tgl_pinjam, INTERVAL 7 DAY))<=0, 0, DATEDIFF(tb_sirkulasi.tgl_kembali, DATE_ADD(tb_sirkulasi.tgl_pinjam, INTERVAL 7 DAY)))
		) AS telat_pengembalian
	FROM tb_sirkulasi
	JOIN tb_buku ON tb_buku.id_buku = tb_sirkulasi.id_buku
	WHERE tb_sirkulasi.id_anggota = '$id_anggota'
	AND (
		(tb_sirkulasi.status='PIN' AND CURDATE() > DATE_ADD(tb_sirkulasi.tgl_pinjam, INTERVAL 7 DAY))
		OR (tb_sirkulasi.status='KEM' AND DATEDIFF(tb_sirkulasi.tgl_kembali, DATE_ADD(tb_sirkulasi.tgl_pinjam, INTERVAL 7 DAY)) > 0)
	)
	AND COALESCE((SELECT status_bayar FROM tb_denda WHERE id_sk=tb_sirkulasi.id_sk LIMIT 1), 'BELUM_BAYAR') != 'SUDAH_BAYAR'
	ORDER BY tb_sirkulasi.tgl_pinjam DESC";
	
	$result = mysqli_query($koneksi, $query);
	
	$total_denda = 0;
	$jumlah_buku_terlambat = 0;
	$daftar_denda = [];
	
	while ($data = mysqli_fetch_assoc($result)) {
		$nominal_denda = $data['telat_pengembalian'] * $tarif_denda;
		$total_denda += $nominal_denda;
		$jumlah_buku_terlambat++;
		
		$daftar_denda[] = [
			'id_sk' => $data['id_sk'],
			'judul_buku' => $data['judul_buku'],
			'tgl_pinjam' => $data['tgl_pinjam'],
			'hari_terlambat' => $data['telat_pengembalian'],
			'nominal_denda' => $nominal_denda,
			'status' => $data['status']
		];
	}
	
	return [
		'ada_denda' => $total_denda > 0,
		'jumlah_belum_dibayar' => $total_denda,
		'jumlah_buku_terlambat' => $jumlah_buku_terlambat,
		'daftar_denda' => $daftar_denda
	];
}

/**
 * Cek status akun siswa (AKTIF atau NONAKTIF)
 * 
 * @param string $id_anggota - ID siswa/anggota
 * @param mysqli $koneksi - Koneksi database
 * @return array ['status' => 'AKTIF'|'NONAKTIF', 'alasan' => string, 'tgl_nonaktif' => date]
 */
function cekStatusSiswa($id_anggota, $koneksi) {
	$query = "SELECT status, alasan_nonaktif, tgl_nonaktif FROM tb_anggota WHERE id_anggota='$id_anggota'";
	$result = mysqli_query($koneksi, $query);
	
	if ($result && mysqli_num_rows($result) > 0) {
		$data = mysqli_fetch_assoc($result);
		return [
			'status' => $data['status'] ?? 'AKTIF',
			'alasan' => $data['alasan_nonaktif'] ?? '',
			'tgl_nonaktif' => $data['tgl_nonaktif'] ?? null
		];
	}
	
	return ['status' => 'AKTIF', 'alasan' => '', 'tgl_nonaktif' => null];
}

/**
 * Nonaktifkan akun siswa (biasanya karena denda belum dibayar)
 * 
 * @param string $id_anggota - ID siswa
 * @param string $alasan - Alasan nonaktif
 * @param mysqli $koneksi - Koneksi database
 * @return bool - true jika berhasil
 */
function nonaktifkanSiswa($id_anggota, $alasan, $koneksi) {
	$now = date('Y-m-d H:i:s');
	$query = "UPDATE tb_anggota 
	          SET status='NONAKTIF', 
	              tgl_nonaktif='$now', 
	              alasan_nonaktif='$alasan' 
	          WHERE id_anggota='$id_anggota'";
	
	return mysqli_query($koneksi, $query);
}

/**
 * Aktifkan kembali akun siswa
 * 
 * @param string $id_anggota - ID siswa
 * @param mysqli $koneksi - Koneksi database
 * @return bool - true jika berhasil
 */
function aktifkanSiswa($id_anggota, $koneksi) {
	$query = "UPDATE tb_anggota 
	          SET status='AKTIF', 
	              tgl_nonaktif=NULL, 
	              alasan_nonaktif=NULL 
	          WHERE id_anggota='$id_anggota'";
	
	return mysqli_query($koneksi, $query);
}

/**
 * Tandai denda sebagai "PERPANJANGAN" (denda ditahan sampai perpanjangan selesai)
 * Admin bisa memilih: tangguhkan pengembalian (perpanjangan) atau bayar langsung
 * 
 * @param string $id_sk - ID Sirkulasi
 * @param mysqli $koneksi - Koneksi database
 * @return bool - true jika berhasil
 */
function catetPerpanjangan($id_sk, $koneksi) {
	$now = date('Y-m-d');
	
	// Update status denda menjadi PERPANJANGAN
	$query1 = "UPDATE tb_denda 
	           SET status_bayar='PERPANJANGAN', catatan='Denda ditangguhkan karena perpanjangan' 
	           WHERE id_sk='$id_sk'";
	
	// Update tb_sirkulasi untuk mencatat perpanjangan
	$query2 = "UPDATE tb_sirkulasi 
	           SET tgl_perpanjangan='$now', sudah_diperpanjang=1 
	           WHERE id_sk='$id_sk'";
	
	$result1 = mysqli_query($koneksi, $query1);
	$result2 = mysqli_query($koneksi, $query2);
	
	return $result1 && $result2;
}

/**
 * Catat pembayaran denda
 * 
 * @param string $id_sk - ID Sirkulasi
 * @param int $nominal_denda - Nominal yang dibayar
 * @param string $catatan - Catatan pembayaran
 * @param mysqli $koneksi - Koneksi database
 * @return bool - true jika berhasil
 */
function catetPembayaranDenda($id_sk, $nominal_denda, $catatan, $koneksi) {
	$now = date('Y-m-d H:i:s');
	
	// Ambil id_anggota dari tb_sirkulasi
	$result_anggota = mysqli_query($koneksi, "SELECT id_anggota FROM tb_sirkulasi WHERE id_sk='$id_sk'");
	if (!$result_anggota || mysqli_num_rows($result_anggota) == 0) {
		return false;
	}
	$id_anggota = mysqli_fetch_assoc($result_anggota)['id_anggota'];
	
	// Cek apakah record denda sudah ada
	$cek = mysqli_query($koneksi, "SELECT id_denda FROM tb_denda WHERE id_sk='$id_sk'");
	
	if ($cek && mysqli_num_rows($cek) > 0) {
		// Update existing record
		$query = "UPDATE tb_denda 
		          SET status_bayar='SUDAH_BAYAR', 
		              tanggal_bayar='$now',
		              nominal_denda='$nominal_denda',
		              catatan='$catatan'
		          WHERE id_sk='$id_sk'";
	} else {
		// Insert new record
		$tanggal = date('Y-m-d');
		
		$query = "INSERT INTO tb_denda 
		          (id_sk, id_anggota, tanggal_denda, nominal_denda, status_bayar, tanggal_bayar, catatan)
		          VALUES ('$id_sk', '$id_anggota', '$tanggal', '$nominal_denda', 'SUDAH_BAYAR', '$now', '$catatan')";
	}
	
	$result = mysqli_query($koneksi, $query);
	
	// Jika pembayaran berhasil dicatat, otomatis aktifkan akun siswa
	if ($result) {
		aktifkanSiswa($id_anggota, $koneksi);
	}
	
	return $result;
}

/**
 * Hitung total denda yang belum dibayar untuk satu siswa
 * 
 * @param string $id_anggota - ID siswa
 * @param mysqli $koneksi - Koneksi database
 * @return int - Total nominal denda belum dibayar
 */
function hitungTotalDendarBelumBayar($id_anggota, $koneksi) {
	$denda = cekDendaSiswa($id_anggota, $koneksi);
	return $denda['jumlah_belum_dibayar'];
}

?>
