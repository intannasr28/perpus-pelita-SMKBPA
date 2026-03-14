<?php
/**
 * ============================================
 * HELPER FUNCTIONS untuk Manajemen ID Siswa
 * ============================================
 * 
 * File ini berisi fungsi-fungsi helper untuk:
 * 1. Generate ID dengan berbagai format
 * 2. Soft delete siswa
 * 3. Restore siswa
 * 4. Query siswa available untuk reuse
 * 
 * Include di file yang memerlukan:
 * include "inc/koneksi.php";
 * include "inc/helper_id_siswa.php";
 */

// ============================================
// FUNGSI GENERATE ID
// ============================================

/**
 * Generate ID Format 4 Digit (A0001-A9999)
 * @param mysqli $koneksi
 * @return array ['success' => bool, 'message' => string, 'id' => string]
 */
function generateID_4Digit($koneksi) {
    // Ambil ID terakhir dengan format 4 digit
    $carikode = mysqli_query($koneksi, "SELECT id_anggota FROM tb_anggota 
                                         WHERE id_anggota REGEXP '^A[0-9]{4}$' 
                                         ORDER BY CAST(SUBSTRING(id_anggota, 2, 4) AS UNSIGNED) DESC 
                                         LIMIT 1");
    $datakode = mysqli_fetch_array($carikode);
    $kode = $datakode['id_anggota'] ?? null;
    
    if ($kode) {
        $urut = (int)substr($kode, 1, 4);
        $tambah = $urut + 1;
        
        if ($tambah > 9999) {
            return [
                'success' => false, 
                'message' => 'Kapasitas ID A0001-A9999 sudah penuh! Hubungi administrator.',
                'id' => null
            ];
        }
    } else {
        $tambah = 1;
    }
    
    $format_id = "A" . str_pad($tambah, 4, "0", STR_PAD_LEFT);
    
    return [
        'success' => true, 
        'message' => 'ID generated successfully', 
        'id' => $format_id
    ];
}

/**
 * Generate ID dengan Soft Delete + Reuse
 * Jika ada siswa lulus, ID mereka akan direuse
 * @param mysqli $koneksi
 * @return array ['success' => bool, 'message' => string, 'id' => string, 'is_reuse' => bool]
 */
function generateID_WithReuse($koneksi) {
    // Cek ID available untuk reuse (siswa lulus/pindah/nonaktif, ambil yang terkecil)
    $queryReuse = "SELECT id_anggota FROM tb_anggota 
                   WHERE status IN ('LULUS', 'PINDAH', 'NONAKTIF') 
                   AND id_anggota REGEXP '^A[0-9]{4}$'
                   ORDER BY CAST(SUBSTRING(id_anggota, 2, 4) AS UNSIGNED) ASC 
                   LIMIT 1";
    
    $reuse_result = mysqli_query($koneksi, $queryReuse);
    
    if (!$reuse_result) {
        return [
            'success' => false,
            'message' => 'Database error: ' . mysqli_error($koneksi),
            'id' => null
        ];
    }
    
    $reuse_data = mysqli_fetch_array($reuse_result);
    
    if ($reuse_data) {
        // Ada ID yang bisa direuse
        return [
            'success' => true, 
            'message' => 'ID reused dari siswa yang sudah lulus', 
            'id' => $reuse_data['id_anggota'],
            'is_reuse' => true
        ];
    } else {
        // Tidak ada reuse, generate normal
        $result = generateID_4Digit($koneksi);
        $result['is_reuse'] = false;
        return $result;
    }
}

/**
 * Generate ID Format Tahun Ajaran (A2024001-A2024999)
 * @param mysqli $koneksi
 * @param int $tahun_ajaran (optional, default tahun sekarang)
 * @return array ['success' => bool, 'message' => string, 'id' => string]
 */
function generateID_TahunAjaran($koneksi, $tahun_ajaran = null) {
    if (!$tahun_ajaran) {
        $tahun_ajaran = date('Y');
    }
    
    $prefix = "A" . $tahun_ajaran;
    
    $carikode = mysqli_query($koneksi, "SELECT id_anggota FROM tb_anggota 
                                         WHERE id_anggota LIKE '$prefix%' 
                                         ORDER BY CAST(SUBSTRING(id_anggota, -3) AS UNSIGNED) DESC 
                                         LIMIT 1");
    $datakode = mysqli_fetch_array($carikode);
    $kode = $datakode['id_anggota'] ?? null;
    
    if ($kode) {
        $urut = (int)substr($kode, -3);
        $tambah = $urut + 1;
        
        if ($tambah > 999) {
            return [
                'success' => false, 
                'message' => "Kapasitas ID tahun $tahun_ajaran sudah penuh!",
                'id' => null
            ];
        }
    } else {
        $tambah = 1;
    }
    
    $format_id = $prefix . str_pad($tambah, 3, "0", STR_PAD_LEFT);
    
    return [
        'success' => true, 
        'message' => 'ID generated successfully', 
        'id' => $format_id
    ];
}

/**
 * Main function - Generate ID sesuai format yang dipilih
 * @param mysqli $koneksi
 * @param string $format_type 'FORMAT_4DIGIT' | 'FORMAT_TAHUN' | 'FORMAT_REUSE'
 * @return array
 */
function generateID($koneksi, $format_type = 'FORMAT_4DIGIT') {
    switch ($format_type) {
        case 'FORMAT_4DIGIT':
            return generateID_4Digit($koneksi);
        case 'FORMAT_TAHUN':
            return generateID_TahunAjaran($koneksi);
        case 'FORMAT_REUSE':
            return generateID_WithReuse($koneksi);
        default:
            return generateID_4Digit($koneksi);
    }
}

// ============================================
// FUNGSI SOFT DELETE & RESTORE
// ============================================

/**
 * Non-aktifkan siswa (Soft Delete)
 * @param mysqli $koneksi
 * @param string $id_anggota
 * @param string $alasan
 * @param string $diubah_oleh (username yang melakukan)
 * @return array ['success' => bool, 'message' => string]
 */
function nonaktifkanSiswa($koneksi, $id_anggota, $alasan = '', $diubah_oleh = '') {
    // Cek siswa ada
    $cek = mysqli_query($koneksi, "SELECT id_anggota FROM tb_anggota WHERE id_anggota='$id_anggota'");
    if (mysqli_num_rows($cek) == 0) {
        return ['success' => false, 'message' => 'Siswa tidak ditemukan'];
    }
    
    $tgl_nonaktif = date('Y-m-d');
    $alasan = mysqli_real_escape_string($koneksi, $alasan);
    $diubah_oleh = mysqli_real_escape_string($koneksi, $diubah_oleh);
    
    // Update status + kolom lainnya
    if (isset($koneksi->tb_anggota)) { // Check column exists
        $query = "UPDATE tb_anggota 
                  SET status = 'NONAKTIF',
                      tgl_nonaktif = '$tgl_nonaktif',
                      alasan_nonaktif = '$alasan',
                      diubah_oleh = '$diubah_oleh'
                  WHERE id_anggota = '$id_anggota'";
    } else {
        // Fallback jika belum ada kolom status
        $query = "UPDATE tb_anggota 
                  SET diubah_oleh = '$diubah_oleh'
                  WHERE id_anggota = '$id_anggota'";
    }
    
    if (mysqli_query($koneksi, $query)) {
        // Log aktivitas
        logAktivitas($koneksi, $id_anggota, 'Delete Siswa', "Siswa non-aktif. Alasan: $alasan");
        return ['success' => true, 'message' => 'Siswa berhasil di-non-aktifkan'];
    } else {
        return ['success' => false, 'message' => 'Error: ' . mysqli_error($koneksi)];
    }
}

/**
 * Luluskan siswa
 * @param mysqli $koneksi
 * @param string $id_anggota
 * @param string $diubah_oleh
 * @return array ['success' => bool, 'message' => string]
 */
function luluskanSiswa($koneksi, $id_anggota, $diubah_oleh = '') {
    $cek = mysqli_query($koneksi, "SELECT id_anggota FROM tb_anggota WHERE id_anggota='$id_anggota'");
    if (mysqli_num_rows($cek) == 0) {
        return ['success' => false, 'message' => 'Siswa tidak ditemukan'];
    }
    
    $tgl_lulus = date('Y-m-d');
    $diubah_oleh = mysqli_real_escape_string($koneksi, $diubah_oleh);
    
    $query = "UPDATE tb_anggota 
              SET status = 'LULUS',
                  tgl_lulus = '$tgl_lulus',
                  diubah_oleh = '$diubah_oleh'
              WHERE id_anggota = '$id_anggota'";
    
    if (mysqli_query($koneksi, $query)) {
        logAktivitas($koneksi, $id_anggota, 'Lulus', "Siswa dinyatakan lulus");
        return ['success' => true, 'message' => 'Siswa berhasil diluluskan'];
    } else {
        return ['success' => false, 'message' => 'Error: ' . mysqli_error($koneksi)];
    }
}

/**
 * Restore siswa (undo soft delete atau lulus)
 * @param mysqli $koneksi
 * @param string $id_anggota
 * @param string $diubah_oleh
 * @return array ['success' => bool, 'message' => string]
 */
function restoreSiswa($koneksi, $id_anggota, $diubah_oleh = '') {
    $cek = mysqli_query($koneksi, "SELECT id_anggota FROM tb_anggota WHERE id_anggota='$id_anggota'");
    if (mysqli_num_rows($cek) == 0) {
        return ['success' => false, 'message' => 'Siswa tidak ditemukan'];
    }
    
    $diubah_oleh = mysqli_real_escape_string($koneksi, $diubah_oleh);
    
    $query = "UPDATE tb_anggota 
              SET status = 'AKTIF',
                  tgl_lulus = NULL,
                  tgl_nonaktif = NULL,
                  alasan_nonaktif = NULL,
                  diubah_oleh = '$diubah_oleh'
              WHERE id_anggota = '$id_anggota'";
    
    if (mysqli_query($koneksi, $query)) {
        logAktivitas($koneksi, $id_anggota, 'Restore Siswa', "Siswa di-restore ke status AKTIF");
        return ['success' => true, 'message' => 'Siswa berhasil di-restore'];
    } else {
        return ['success' => false, 'message' => 'Error: ' . mysqli_error($koneksi)];
    }
}

// ============================================
// FUNGSI QUERY
// ============================================

/**
 * Get siswa yang bisa direuse (lulus/pindah/nonaktif)
 * @param mysqli $koneksi
 * @return array
 */
function getSiswaBebasID($koneksi) {
    $query = "SELECT id_anggota, nama_anggota, status, tgl_lulus 
              FROM tb_anggota 
              WHERE status IN ('LULUS', 'PINDAH', 'NONAKTIF')
              ORDER BY CAST(SUBSTRING(id_anggota, 2) AS UNSIGNED) ASC";
    
    $result = mysqli_query($koneksi, $query);
    $siswa = [];
    
    while ($row = mysqli_fetch_assoc($result)) {
        $siswa[] = $row;
    }
    
    return $siswa;
}

/**
 * Get statistik penggunaan ID
 * @param mysqli $koneksi
 * @return array
 */
function getStatistikID($koneksi) {
    $query = "SELECT 
                COUNT(*) as total_siswa,
                SUM(CASE WHEN status = 'AKTIF' THEN 1 ELSE 0 END) as aktif,
                SUM(CASE WHEN status = 'LULUS' THEN 1 ELSE 0 END) as lulus,
                SUM(CASE WHEN status = 'PINDAH' THEN 1 ELSE 0 END) as pindah,
                SUM(CASE WHEN status = 'NONAKTIF' THEN 1 ELSE 0 END) as nonaktif
              FROM tb_anggota";
    
    $result = mysqli_query($koneksi, $query);
    $stat = mysqli_fetch_assoc($result);
    
    // Tambah info kapasitas
    $stat['max_kapasitas'] = 9999;
    $stat['sisa_kapasitas'] = $stat['max_kapasitas'] - $stat['aktif'];
    $stat['persentase_aktif'] = round(($stat['aktif'] / $stat['total_siswa']) * 100, 2);
    
    return $stat;
}

/**
 * Helper logging aktivitas
 * @param mysqli $koneksi
 * @param string $id_anggota
 * @param string $jenis_aktivitas
 * @param string $keterangan
 */
function logAktivitas($koneksi, $id_anggota, $jenis_aktivitas, $keterangan = '') {
    $id_anggota = mysqli_real_escape_string($koneksi, $id_anggota);
    $jenis_aktivitas = mysqli_real_escape_string($koneksi, $jenis_aktivitas);
    $keterangan = mysqli_real_escape_string($koneksi, $keterangan);
    
    // Cek tabel tb_log_activity ada
    $cek_table = mysqli_query($koneksi, "SHOW TABLES LIKE 'tb_log_activity'");
    if (mysqli_num_rows($cek_table) > 0) {
        $query = "INSERT INTO tb_log_activity 
                  (id_anggota, nama_anggota, tgl_aktivitas, waktu_aktivitas, jenis_aktivitas, id_buku, id_sk, keterangan)
                  SELECT id_anggota, nama_anggota, DATE(NOW()), TIME(NOW()), '$jenis_aktivitas', NULL, NULL, '$keterangan'
                  FROM tb_anggota WHERE id_anggota = '$id_anggota'";
        
        mysqli_query($koneksi, $query);
    }
}

?>
