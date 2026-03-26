# E-PERPUS - Sistem Informasi Perpustakaan Pelita

Aplikasi Manajemen Perpustakaan Berbasis Website dengan fitur lengkap untuk peminjaman, pengembalian, dan pelaporan buku.

## 📋 Daftar Isi
- [Fitur Utama](#fitur-utama)
- [Teknologi yang Digunakan](#teknologi-yang-digunakan)
- [Requirements](#requirements)
- [Instalasi](#instalasi)
- [Struktur Folder](#struktur-folder)
- [Pengguna & Role](#pengguna--role)
- [Database Schema](#database-schema)
- [Changelog](#changelog)

---

## ✨ Fitur Utama

### 1. **Manajemen Pengguna**
- Registrasi siswa otomatis dengan ID auto-generated
- Multi-role authentication (Admin, Petugas, Siswa)
- Manajemen data pengguna

### 2. **Manajemen Buku**
- CRUD buku dengan kategori
- Tracking stok buku real-time
- Upload gambar/cover buku
- Data agent/penerbit

### 3. **Sirkulasi Peminjaman & Pengembalian**
- Sistem 1 SK = 1 transaksi (dengan jumlah item variable)
- Peminjaman via Admin dan Self-Service (Siswa)
- Pengembalian single & bulk
- Auto-generate SK dengan nomor unik
- Perhitungan denda otomatis untuk keterlambatan
- Pembatasan akun siswa otomatis untuk keterlambatan membayar denda
- Admin/Petugas dapat menonaktifkan akun siswa dengan denda jatuh tempo

### 4. **Activity Tracking & Logging**
- Log login semua user
- Log peminjaman (admin + siswa)
- Log pengembalian (single + bulk)
- Laporan aktivitas per tanggal

### 5. **Statistik & Reporting**
- **Statistik Pengunjung:**
  - Per bulan, pekan, tahun, atau range tanggal custom
  - Hitung pengunjung unik dan hari aktif
  - Filter by jenis aktivitas (Login/Peminjaman/Pengembalian)
  
- **Laporan Sirkulasi:**
  - Detail peminjaman per periode
  - Perhitungan denda otomatis
  - Export & print functionality

### 6. **Dashboard**
- Ringkasan statistik
- Best peminjam (siswa terbanyak meminjam)
- Informasi buku terbaru
- Activity overview

---

## 🛠️ Teknologi yang Digunakan

| Teknologi | Versi | Keterangan |
|-----------|-------|-----------|
| **PHP** | 5.x+ | Server-side scripting |
| **MySQL** | 5.7+ | Database management |
| **Bootstrap** | 3.3.6 | UI Framework |
| **jQuery** | 2.2.3 | JavaScript library |
| **AdminLTE** | - | Admin dashboard theme |
| **Poppins Font** | Google Fonts | Typography |
| **SweetAlert2** | Latest | User notifications |

---

## 📦 Requirements

### Server Requirements
- PHP 5.6 atau lebih tinggi
- MySQL 5.7 atau MariaDB 10.x+
- Apache Web Server dengan mod_rewrite enabled
- Laragon / XAMPP / WAMP

### Browser Support
- Chrome (recommended)
- Firefox
- Safari
- Edge

---

## 🚀 Instalasi

### Step 1: Clone/Download Aplikasi
```bash
# Download dari repository
git clone https://github.com/intannasr28/perpus-pelita-SMKBPA.git
cd perpuspelita
```

### Step 2: Setup Database
```bash
# Import SQL ke MySQL
1. Buka phpMyAdmin
2. Create database "perpuspelita"
3. Import file "data_perpus.sql"
4. Jalankan migration files jika ada (update_tb_kunjungan.sql, dll)
```

### Step 3: Konfigurasi Database
Edit file `inc/koneksi.php`:
```php
$host = "localhost";
$user = "root";
$password = "";  // Sesuaikan password MySQL Anda
$database = "perpuspelita";
```

### Step 4: Setup File Permissions
```bash
# Buat folder tmp untuk session
mkdir tmp
chmod 777 tmp

# Buat folder upload jika belum ada
mkdir -p assets_style/assets/images/buku
chmod 777 assets_style/assets/images/buku
```

### Step 5: Akses Aplikasi
```
URL: http://localhost/perpuspelita
Login: 
- Username: intannasr
- Password: 123 
```

---

## 📁 Struktur Folder

```
perpuspelita/
├── admin/                          # Panel Admin
│   ├── agt/                       # Manajemen Agent/Penerbit
│   ├── buku/                      # Manajemen Buku
│   ├── laporan/                   # Laporan Sirkulasi & Statistik
│   ├── log/                       # Log Aktivitas
│   ├── pengguna/                  # Manajemen Pengguna
│   └── sirkul/                    # Sirkulasi Peminjaman & Pengembalian
├── home/                          # Dashboard per role
│   ├── admin.php
│   ├── petugas.php
│   └── siswa.php
├── plugins/                       # jQuery, DataTables, Select2
├── assets/                        # CSS, JS, Images
├── bootstrap/                     # Bootstrap Framework
├── inc/                          # File Internal
│   ├── koneksi.php              # Konfigurasi Database
│   └── vercel.json              # Deploy config
├── login.php                     # Halaman Login
├── register.php                  # Halaman Registrasi Siswa
├── index.php                     # Router Utama
├── logout.php                    # Logout
└── data_perpus.sql              # Database SQL
```

---

## 👥 Pengguna & Role

### 1. **Admin**
- Manajemen seluruh data sistem
- Akses ke semua modul
- Input peminjaman
- Lihat laporan & statistik
- Manajemen pengguna & agen
- Menonaktifkan akun siswa yang memiliki denda jatuh tempo
- Mengaktifkan kembali akun siswa setelah denda lunas

### 2. **Petugas**
- Input pengembalian buku
- Lihat data peminjaman
- Lihat statistik dasar
- Tidak bisa delete/edit
- Menonaktifkan akun siswa yang memiliki denda jatuh tempo
- Mengaktifkan kembali akun siswa setelah denda lunas

### 3. **Siswa**
- Self-service peminjaman (jika akun aktif dan tidak ada denda jatuh tempo)
- Lihat riwayat peminjaman
- Lihat data buku
- Daftar akun sendiri via register.php
- Akun dapat dinonaktifkan jika memiliki denda yang jatuh tempo
- Akun diaktifkan kembali setelah membayar denda

---

## 📊 Database Schema (Key Tables)

### tb_anggota (Anggota/Siswa)
```
id_anggota (PK) | nama | jekel | kelas | no_hp
```

### tb_buku (Master Buku)
```
id_buku (PK) | judul | id_agt (FK) | id_kategori (FK) | isbn | stok | tgl_masuk
```

### tb_sirkulasi (Master Transaksi)
```
id_sk (PK) | id_anggota (FK) | tgl_pinjam | tgl_kembali | status (PIN/KEM)
```

### tb_sirkulasi_detail (Detail Item per Transaksi)
```
id_detail (PK) | id_sk (FK) | id_buku (FK) | jumlah | status (PIN/KEM)
```

### tb_kunjungan (Activity Log)
```
id_kunjungan (PK) | id_anggota | nama | tgl_kunjungan | waktu_kunjungan
jenis_aktivitas (Login/Peminjaman/Pengembalian) | id_buku | id_sk | keterangan
```

### tb_pengguna (User Account)
```
id_pengguna (PK) | nama_pengguna | username | password (md5) | level
```

---

## 🎯 Changelog

### Latest Updates (v2.1)
- ✅ Fitur Pembatasan Akun Siswa: Admin/Petugas dapat menonaktifkan akun siswa dengan denda jatuh tempo
- ✅ Verifikasi Status Akun: Siswa tidak bisa meminjam jika akun dinonaktifkan karena denda
- ✅ Pembayaran Denda: Akun siswa diaktifkan kembali setelah denda lunas
- ✅ Monitoring Denda: Laporan lengkap siswa dengan denda jatuh tempo

### v2.0
- ✅ UI/UX Improvement: Login & Register pages dengan background image, animations
- ✅ Implemented Activity Tracking: Login, Peminjaman, Pengembalian logging
- ✅ Schema Migration ke Opsi 2: 1 SK = 1 transaksi dengan jumlah variable
- ✅ Fixed Statistik Calculations: Accurate unique visitor & period counting
- ✅ Fixed number_format() deprecation warning
- ✅ Comprehensive Sirkulasi Filtering: by bulan/pekan/tahun/range tanggal
- ✅ DataTables integration dengan proper pagination & sorting

### v1.5
- CRUD Buku dengan kategori
- Peminjaman & pengembalian basic
- Dashboard statistik
- Laporan sirkulasi

---

## 🔧 Konfigurasi Penting

### Timezone
Edit `inc/koneksi.php` untuk set timezone default:
```php
date_default_timezone_set('Asia/Jakarta');
```

### Session Path (Windows)
Login.php sudah set custom session path untuk Windows compatibility:
```php
ini_set('session.save_path', realpath(__DIR__) . DIRECTORY_SEPARATOR . 'tmp');
```

### Database Strict Mode
Aplikasi sudah compatible dengan MySQL strict mode (only_full_group_by). Semua query sudah dioptimasi.

---

## 📝 Tips & Best Practices

### Backup Database Reguler
```bash
# Backup manual
mysqldump -u root -p perpuspelita > backup_perpus_$(date +%Y%m%d).sql

# Restore
mysql -u root -p perpuspelita < backup_perpus_20260302.sql
```

### Monitoring Activity
- Cek `admin/log/log_kunjungan.php` untuk activity tracking
- Lihat `admin/laporan/statistik_pengunjung.php` untuk visitor stats

### Maintenance
- Clear tmp folder secara berkala: `tmp/*`
- Backup upload images: `assets_style/assets/images/buku/`
- Monitor database size & optimize tables

---

## ⚠️ Catatan Penting

1. **Password Default**: Ganti password admin default setelah instalasi pertama
2. **MD5 Hashing**: Saat ini menggunakan MD5 (untuk production, pertimbangkan bcrypt)
3. **HTTPS**: Untuk production, selalu gunakan HTTPS
4. **File Upload**: Validasi tipe file sudah diimplementasi di form upload
5. **SQL Injection**: Sudah menggunakan mysqli_real_escape_string(), namun prepared statements lebih recommended

---

## 📞 Support & Contact

Untuk pertanyaan atau bug report, hubungi admin perpustakaan atau developer.
- email : intannasr78@gmail.com

---

**Last Updated:** March 14, 2026  
**Version:** 2.1  
**Status:** Production Ready ✅


