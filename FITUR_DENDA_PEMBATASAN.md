# Dokumentasi Fitur: Pembatasan Peminjaman untuk Siswa dengan Denda Terlambat

## 📋 Ringkasan Fitur

Fitur ini memungkinkan sistem perpustakaan untuk:
1. **Mencegah peminjaman** bagi siswa yang memiliki denda terlambat belum dibayar
2. **Menonaktifkan akun** siswa yang memiliki denda terlambat dan belum membayar
3. **Melacak status denda** dan pembayaran
4. **Memberikan opsi perpanjangan** sebagai alternatif pembayaran langsung

---

## 🔧 Setup Awal

### 1. Jalankan Migration SQL

Jalankan file [add_status_column.sql](../add_status_column.sql) di database Anda untuk menambahkan kolom-kolom baru:

```sql
-- Menjalankan di phpMyAdmin atau command line
source add_status_column.sql;
```

**Kolom yang ditambahkan:**
- `tb_anggota.status` - Status akun (AKTIF/NONAKTIF)
- `tb_anggota.tgl_nonaktif` - Tanggal akun dinonaktifkan
- `tb_anggota.alasan_nonaktif` - Alasan nonaktif
- `tb_sirkulasi.tgl_perpanjangan` - Tanggal perpanjangan
- `tb_sirkulasi.sudah_diperpanjang` - Flag perpanjangan
- `tb_denda` (tabel baru) - Tracking pembayaran denda

---

## 📚 Cara Penggunaan

### A. Untuk Admin/Petugas

#### 1. **Proses Peminjaman (Add_Sirkul)**

Saat admin akan menambah peminjaman:

```
1. Pilih siswa dari dropdown
   ↓
2. Sistem otomatis cek:
   ✓ Status akun (AKTIF/NONAKTIF)
   ✓ Denda terlambat yang belum dibayar

3. Jika siswa memiliki denda belum dibayar:
   → Peminjaman DITOLAK dengan notifikasi:
   "Siswa memiliki denda terlambat yang belum dibayar"
```

**Validasi yang dijalankan:**
- Cek apakah akun siswa AKTIF/NONAKTIF
- Jika NONAKTIF → peminjaman ditolak
- Jika AKTIF tapi ada denda belum dibayar → peminjaman ditolak

![Status Akun & Denda Info](../../doc/add_sirkul_denda_info.png)

#### 2. **Laporan Sirkulasi & Manajemen Denda**

Di [Laporan Sirkulasi](../laporan/laporan_sirkulasi.php), admin dapat:

**Kolom Status Akun:**
- `AKTIF` (badge hijau) - Siswa dapat meminjam
- `NONAKTIF` (badge merah) - Siswa tidak dapat meminjam

**Tombol Aksi untuk setiap buku terlambat:**

a) **[💰 Bayar]** - Catat Pembayaran Denda
   - Klik untuk membuka modal pembayaran
   - Input nominal denda (auto-filled)
   - Tambah catatan (opsional)
   - Klik "Simpan Pembayaran"
   - Denda langsung ditandai "SUDAH_BAYAR"

b) **[🔄 Perpanjang]** - Catat Perpanjangan
   - Ketika siswa meminta perpanjangan ke admin
   - Denda ditangguhkan (status "PERPANJANGAN")
   - Siswa tetap bisa meminjam buku lagi
   - Pembayaran denda dikemudian hari

c) **[⛔ Nonaktif]** - Nonaktifkan Akun Siswa
   - Tekan tombol ini untuk menonaktifkan siswa
   - Siswa tidak dapat meminjam sampai diaktifkan kembali
   - Alasan otomatis: "Denda terlambat belum dibayar"

d) **[✅ Aktifkan]** - Aktifkan Kembali Akun
   - Tekan jika siswa sudah membayar denda atau perpanjangan
   - Akun akan berubah status menjadi AKTIF
   - Siswa bisa meminjam buku lagi

---

## 🔄 Alur Kerja Lengkap

### Skenario: Siswa Terlambat Kembali Buku

```
HARI KE-7 (Tgl Jatuh Tempo)
├─ Buku belum dikembalikan
├─ Status: BELUM DIKEMBALIKAN (badge warning)
└─ Denda: Rp 7,000 (7 hari × Rp 1,000)

HARI KE-10 (3 hari terlambat)
├─ Laporan Sirkulasi menampilkan: Denda Rp 3,000
└─ Siswa masih bisa meminjam (jika belum di-nonaktif)

OPSI 1: BAYAR DENDA LANGSUNG
├─ Admin klik "💰 Bayar"
├─ Input nominal denda
├─ Klik "Simpan Pembayaran"
└─ Denda ditandai SUDAH_BAYAR
    └─ Siswa bisa meminjam buku lagi

OPSI 2: PERPANJANGAN
├─ Siswa datang ke admin minta perpanjangan
├─ Admin klik "🔄 Perpanjang"
├─ Denda ditandai PERPANJANGAN (ditangguhkan)
├─ Siswa tetap dapat meminjam buku lagi
└─ Nanti setelah dikembalikan → bayar denda

OPSI 3: NONAKTIFKAN AKUN (jika denda tak terbayar lama)
├─ Admin klik "⛔ Nonaktif"
├─ Confirm action
├─ Akun siswa berubah status NONAKTIF
├─ Siswa TIDAK dapat meminjam lagi
└─ Setelah bayar denda:
   └─ Admin klik "✅ Aktifkan"
      └─ Siswa bisa meminjam lagi
```

---

## 📊 Status Denda

Denda memiliki beberapa status:

| Status | Makna | Keterangan |
|--------|-------|-----------|
| `BELUM_BAYAR` | Belum dibayar | Tampil di laporan, siswa tidak bisa pinjam |
| `SUDAH_BAYAR` | Sudah dibayar | Denda selesai, siswa bisa pinjam |
| `PERPANJANGAN` | Ditangguhkan | Siswa bisa pinjam, bayar nanti |

---

## 📝 File & Fungsi yang Berkaitan

### File Helper: `inc/helper_denda.php`

**Fungsi-fungsi yang tersedia:**

1. **`cekDendaSiswa($id_anggota, $koneksi)`**
   - Cek denda siswa yang belum dibayar
   - Return: Array dengan total denda & list buku terlambat

2. **`cekStatusSiswa($id_anggota, $koneksi)`**
   - Cek status akun (AKTIF/NONAKTIF)
   - Return: Array dengan status & alasan

3. **`nonaktifkanSiswa($id_anggota, $alasan, $koneksi)`**
   - Nonaktifkan akun siswa
   - Catat alasan & waktu nonaktif

4. **`aktifkanSiswa($id_anggota, $koneksi)`**
   - Aktifkan kembali akun siswa

5. **`catetPerpanjangan($id_sk, $koneksi)`**
   - Tandai perpanjangan peminjaman
   - Ubah status denda menjadi "PERPANJANGAN"

6. **`catetPembayaranDenda($id_sk, $nominal_denda, $catatan, $koneksi)`**
   - Catat pembayaran denda
   - Ubah status menjadi "SUDAH_BAYAR"

---

## 🔌 File yang Dimodifikasi

1. **`admin/sirkul/add_sirkul.php`** (Form Peminjaman)
   - Tambah validasi denda & status siswa
   - Tampil info denda real-time via AJAX
   - Cegah peminjaman jika ada denda

2. **`admin/laporan/laporan_sirkulasi.php`** (Laporan Sirkulasi)
   - Tambah kolom "Status Akun"
   - Tambah tombol aksi (Bayar, Perpanjang, Nonaktif, Aktifkan)
   - Tambah modals untuk input pembayaran & perpanjangan

3. **`plugins/check_siswa.php`** (AJAX Handler - BARU)
   - Handle request AJAX untuk cek status & denda siswa

4. **`inc/helper_denda.php`** (Helper Functions - BARU)
   - Semua fungsi untuk denda & status siswa

---

## ⚙️ Konfigurasi

### Tarif Denda

Tarif denda saat ini adalah **Rp 1,000 per hari** terlambat.

Untuk mengubah, edit file `admin/laporan/laporan_sirkulasi.php`:

```php
$tarif_denda = 1000;  // Ubah ke nominal yang diinginkan
```

---

## 🧪 Testing

### Test Case 1: Peminjaman dengan Denda Belum Bayar

```
1. Login sebagai admin
2. Buka "Tambah Peminjaman"
3. Pilih siswa yang punya denda belum bayar
   → (Lihat di laporan sirkulasi)
4. Pilih buku
5. Klik "Simpan"
   → Harusnya DITOLAK dengan pesan denda
```

### Test Case 2: Catat Pembayaran

```
1. Buka Laporan Sirkulasi
2. Cari buku yang terlambat dari siswa
3. Klik "💰 Bayar"
4. Modal terbuka, input nominal denda
5. Klik "Simpan Pembayaran"
   → Denda tersimpan
6. Coba peminjaman lagi → Harusnya BERHASIL
```

### Test Case 3: Perpanjangan

```
1. Buka Laporan Sirkulasi
2. Cari buku terlambat
3. Klik "🔄 Perpanjang"
4. Confirm di modal
   → Denda ditandai PERPANJANGAN
5. Coba peminjaman lagi → Harusnya BERHASIL
6. (Catatan: denda masih perlu dibayar nanti)
```

### Test Case 4: Nonaktifkan & Aktifkan Siswa

```
1. Buka Laporan Sirkulasi
2. Klik "⛔ Nonaktif" pada baris siswa
3. Confirm action
   → Akun siswa NONAKTIF
4. Coba peminjaman dengan siswa ini
   → Harusnya DITOLAK "Akun NONAKTIF"
5. Klik "✅ Aktifkan"
6. Confirm action
   → Akun siswa kembali AKTIF
7. Coba peminjaman lagi → Harusnya BERHASIL
```

---

## ❓ FAQ

**Q: Apakah siswa bisa lihat status akun mereka?**
A: Saat ini tidak. Hanya admin yang bisa lihat di laporan sirkulasi. Bisa ditambah di dashboard siswa kemudian.

**Q: Apakah denda otomatis berkurang jika buku dikembalikan?**
A: Tidak. Admin harus secara manual mengklik "💰 Bayar" untuk mencatat pembayaran denda.

**Q: Bagaimana jika ada kesalahan input nominal denda?**
A: Edit langsung di database tabel `tb_denda` atau buat ulang dengan nominal yang benar.

**Q: Apakah siswa bisa nonaktif otomatis?**
A: Saat ini manual. Admin yang memutuskan kapan harus nonaktif. Bisa dibuat otomatis dengan cron job jika diperlukan.

**Q: Berapa lama akun nonaktif?**
A: Sampai admin mengklik "✅ Aktifkan" kembali.

---

## 📞 Support

Untuk masalah atau pertanyaan lebih lanjut, hubungi admin sistem.

---

**Versi**: 1.0  
**Tanggal**: Maret 2026  
**Status**: Produksi
