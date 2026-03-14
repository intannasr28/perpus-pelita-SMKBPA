# PANDUAN INTEGRASI CEPAT - COPY PASTE READY

Dokumen ini berisi **code ready-to-use** yang bisa langsung di-copy ke project Anda.

---

## 📋 OPSI 1: FORMAT 4 DIGIT (RECOMMENDED) - COPY PASTE

### **A. Update register.php (Baris 1-21)**

**Find & Replace yang baris 1-21 di `register.php` dengan:**

```php
<?php
include "inc/koneksi.php";
include "inc/helper_id_siswa.php";

// Configuration
define('ID_FORMAT', 'FORMAT_4DIGIT');

// Generate ID sesuai format yang dipilih
$id_result = generateID($koneksi, ID_FORMAT);

if ($id_result['success']) {
    $format_id = $id_result['id'];
    $is_reuse = isset($id_result['is_reuse']) ? $id_result['is_reuse'] : false;
} else {
    $format_id = 'ERROR';
    $error_message = $id_result['message'];
}
?>
```

---

### **B. Buat file `inc/helper_id_siswa.php` - COPY FULL FILE**

**Copy seluruh isi file dari:**
```
c:\laragon\www\perpuspelita\inc\helper_id_siswa.php
```

---

### **C. Migration SQL - RUN DI PHPMYADMIN**

**Go to SQL Tab → Paste code ini:**

```sql
-- Step 1: Backup existing data
CREATE TABLE IF NOT EXISTS tb_anggota_backup_20240309 AS 
SELECT * FROM tb_anggota;

-- Step 2: Convert ID from A001 format to A0001 format
START TRANSACTION;

UPDATE tb_anggota
SET id_anggota = CONCAT('A', LPAD(SUBSTRING(id_anggota, 2), 4, '0'))
WHERE id_anggota REGEXP '^A[0-9]{1,3}$';

-- Step 3: Verify conversion
SELECT 
    COUNT(*) as total_siswa,
    MIN(id_anggota) as id_min,
    MAX(id_anggota) as id_max,
    COUNT(DISTINCT id_anggota) as unique_id
FROM tb_anggota;

-- If result OK, COMMIT. If error, ROLLBACK;
-- COMMIT;
```

**Note:** Verify dulu hasil SELECT, baru COMMIT.

---

## 📱 OPSI 2: SOFT DELETE + ADMIN INTERFACE

### **A. Database Migration**

```sql
-- Add columns untuk soft delete
ALTER TABLE tb_anggota ADD COLUMN IF NOT EXISTS status 
    ENUM('AKTIF', 'LULUS', 'PINDAH', 'NONAKTIF') 
    DEFAULT 'AKTIF';

ALTER TABLE tb_anggota ADD COLUMN IF NOT EXISTS tgl_lulus DATE NULL;
ALTER TABLE tb_anggota ADD COLUMN IF NOT EXISTS tgl_nonaktif DATE NULL;
ALTER TABLE tb_anggota ADD COLUMN IF NOT EXISTS alasan_nonaktif VARCHAR(255) NULL;
ALTER TABLE tb_anggota ADD COLUMN IF NOT EXISTS diubah_oleh VARCHAR(50) NULL;

-- Add indexes
CREATE INDEX idx_status ON tb_anggota(status);
CREATE INDEX idx_id_status ON tb_anggota(id_anggota, status);
```

### **B. Copy Helper Functions**

Copy file `inc/helper_id_siswa.php` (sudah ada, sama seperti Opsi 1).

### **C. Copy Admin Interface**

Copy file `admin/siswa/manage_lulus.php` ke folder yang sesuai.

### **D. Update register.php**

Sama seperti Opsi 1, tapi gunakan:
```php
define('ID_FORMAT', 'FORMAT_REUSE');
```

---

## 🔧 OPSI 3: FORMAT TAHUN AJARAN

### Setting di `register.php`:**

```php
define('ID_FORMAT', 'FORMAT_TAHUN');
```

**Contoh hasil:** A2024001, A2024002, ..., A2024999

---

## 🎯 INTEGRASI KE EXISTING ADMIN

### Jika ada tempat lain yang generate ID (misalnya admin panel):

**Find semua file yang punya code seperti ini:**

```php
$carikode = mysqli_query($koneksi, "SELECT id_anggota FROM tb_anggota ...");
```

**Replace dengan:**

```php
include "inc/helper_id_siswa.php";
$id_result = generateID($koneksi); // Gunakan format dari config
```

---

## 📊 CONTOH QUERY UNTUK DASHBOARD

### Lihat statistik ID usage:

```php
<?php
include "inc/koneksi.php";
include "inc/helper_id_siswa.php";

$stat = getStatistikID($koneksi);

echo "Total Siswa: " . $stat['total_siswa'] . "<br>";
echo "Siswa Aktif: " . $stat['aktif'] . "<br>";
echo "Sisa Kapasitas: " . $stat['sisa_kapasitas'] . "/9999<br>";
echo "Persentase Aktif: " . $stat['persentase_aktif'] . "%<br>";
?>
```

---

## 🔄 INTEGRASI KE PROSES REGISTER

### Di file `proses_register.php`, pastikan ada:

```php
<?php
// ... existing code ...

// Insert ke database dengan ID dari form
$id_anggota = $_POST['id_anggota']; // Dari form hidden input
$nama_anggota = $_POST['nama_anggota'];
// ... etc

$query = "INSERT INTO tb_anggota (id_anggota, nama_anggota, ...) 
          VALUES ('$id_anggota', '$nama_anggota', ...)";

if (mysqli_query($koneksi, $query)) {
    // Success
} else {
    // Error - mungkin ID duplikat
}
?>
```

ID sudah di-generate di `register.php`, tinggal simpan ke database.

---

## 🧪 TEST CHECKLIST

- [ ] Backup database sudah dibuat
- [ ] File `inc/helper_id_siswa.php` sudah di-create
- [ ] File `register.php` sudah di-update
- [ ] Migration SQL sudah di-run
- [ ] Buka `register.php` → lihat ID field sudah A0001 format?
- [ ] Register siswa baru → ID auto increment (A0002, A0003, dst)?
- [ ] Check database → ID tersimpan dengan format baru?

---

## 🚨 ROLLBACK JIKA ADA ERROR

```sql
-- Restore dari backup
DROP TABLE tb_anggota;
CREATE TABLE tb_anggota AS SELECT * FROM tb_anggota_backup_20240309;
```

---

## 📁 FILE CHECKLIST

Pastikan semua file ada:

- ✓ `register_v2.php` (versi baru, bisa untuk reference)
- ✓ `inc/helper_id_siswa.php` (REQUIRED)
- ✓ `MIGRATION_ID_SISWA.sql` (SQL scripts)
- ✓ `admin/siswa/manage_lulus.php` (admin panel, OPTIONAL)
- ✓ Documentation files (README_SOLUSI_ID.md, dll)

---

## 💾 QUICK REFERENCE

### Generate ID dengan berbagai format:

```php
// Format 4 digit (A0001-A9999)
$result = generateID_4Digit($koneksi);

// Format tahun ajaran (A2024001-A2024999)
$result = generateID_TahunAjaran($koneksi);

// Format dengan reuse (A0001 dari siswa lulus)
$result = generateID_WithReuse($koneksi);

// Main function (gunakan define ID_FORMAT)
$result = generateID($koneksi, 'FORMAT_4DIGIT');
```

---

## 📞 SUPPORT COMMANDS

### Cek ID usage:

```php
$stat = getStatistikID($koneksi);
print_r($stat);
```

### Lihat ID yang bisa reuse:

```php
$siswa_bebas = getSiswaBebasID($koneksi);
foreach ($siswa_bebas as $siswa) {
    echo $siswa['id_anggota'] . " - " . $siswa['nama_anggota'] . "<br>";
}
```

### Luluskan siswa:

```php
$result = luluskanSiswa($koneksi, 'A0001', 'admin_username');
echo $result['message'];
```

### Restore siswa:

```php
$result = restoreSiswa($koneksi, 'A0001', 'admin_username');
echo $result['message'];
```

---

**Last Updated:** 9 Maret 2026
