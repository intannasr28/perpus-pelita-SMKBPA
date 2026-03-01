<?php
// Test script to verify activity logging
include 'inc/koneksi.php';

echo "=== ACTIVITY LOGGING TEST ===\n\n";

// Check login activities
echo "1. LOGIN ACTIVITIES:\n";
$sql_login = "SELECT id_anggota, nama, tgl_kunjungan, waktu_kunjungan, jenis_aktivitas 
              FROM tb_kunjungan 
              WHERE jenis_aktivitas='Login' 
              ORDER BY id_kunjungan DESC 
              LIMIT 5";
$result = mysqli_query($koneksi, $sql_login);
if (mysqli_num_rows($result) > 0) {
    echo "Found " . mysqli_num_rows($result) . " login records:\n";
    while ($row = mysqli_fetch_assoc($result)) {
        echo "  - {$row['nama']} at {$row['tgl_kunjungan']} {$row['waktu_kunjungan']}\n";
    }
} else {
    echo "No login records found yet.\n";
}

echo "\n2. PEMINJAMAN (BORROW) ACTIVITIES:\n";
$sql_pinjam = "SELECT id_anggota, nama, id_buku, id_sk, tgl_kunjungan, jenis_aktivitas, keterangan
               FROM tb_kunjungan 
               WHERE jenis_aktivitas='Peminjaman' 
               ORDER BY id_kunjungan DESC 
               LIMIT 5";
$result = mysqli_query($koneksi, $sql_pinjam);
if (mysqli_num_rows($result) > 0) {
    echo "Found " . mysqli_num_rows($result) . " borrow records:\n";
    while ($row = mysqli_fetch_assoc($result)) {
        echo "  - {$row['nama']} borrowed book {$row['id_buku']} (SK: {$row['id_sk']}) - {$row['keterangan']}\n";
    }
} else {
    echo "No borrow records found yet.\n";
}

echo "\n3. PENGEMBALIAN (RETURN) ACTIVITIES:\n";
$sql_kembali = "SELECT id_anggota, nama, id_buku, id_sk, tgl_kunjungan, jenis_aktivitas, keterangan
                FROM tb_kunjungan 
                WHERE jenis_aktivitas='Pengembalian' 
                ORDER BY id_kunjungan DESC 
                LIMIT 5";
$result = mysqli_query($koneksi, $sql_kembali);
if (mysqli_num_rows($result) > 0) {
    echo "Found " . mysqli_num_rows($result) . " return records:\n";
    while ($row = mysqli_fetch_assoc($result)) {
        echo "  - {$row['nama']} returned book {$row['id_buku']} (SK: {$row['id_sk']}) - {$row['keterangan']}\n";
    }
} else {
    echo "No return records found yet.\n";
}

echo "\n4. SUMMARY STATISTICS:\n";
$sql_summary = "SELECT 
    jenis_aktivitas, 
    COUNT(*) as total,
    COUNT(DISTINCT id_anggota) as unique_users
FROM tb_kunjungan 
GROUP BY jenis_aktivitas
ORDER BY total DESC";
$result = mysqli_query($koneksi, $sql_summary);
while ($row = mysqli_fetch_assoc($result)) {
    echo "  {$row['jenis_aktivitas']}: {$row['total']} activities by {$row['unique_users']} users\n";
}

echo "\n=== TEST COMPLETE ===\n";
echo "To test manually:\n";
echo "1. Login to the application (should log 'Login' activity)\n";
echo "2. Borrow a book via admin or siswa panel (should log 'Peminjaman' activity)\n";
echo "3. Return the book (should log 'Pengembalian' activity)\n";
echo "4. Then run this script again via browser to see the results\n";
?>
