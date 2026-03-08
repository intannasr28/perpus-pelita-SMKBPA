# Fix Avatar & Level Issues

## Issues yang dilaporkan:
1. ❌ Avatar masih perempuan padahal user laki-laki
2. ❌ Level pengguna hilang di sidebar

---

## STEP 1: Check Avatar Files Status

**Akses**: http://localhost/perpuspelita/check-avatars.php

Script ini akan:
- ✓ Show status file avatar_perempuan.png dan avatar_laki_laki.png
- ✓ Compare apakah files identical (duplikat)
- ✓ Preview kedua gambar
- ✓ Determine masalah yang sebenarnya

**Apa yang harus Anda lihat:**
- Jika "DUPLIKAT TERDETEKSI" → File laki-laki perlu diganti
- Jika "Files berbeda" → Avatar function mungkin yang issue

---

## STEP 2: Debug Jekel Data

**Akses**: http://localhost/perpuspelita/debug-avatar.php

Script ini akan:
- ✓ Show nilai jekel untuk user yang sedang login
- ✓ Cek format data (ada spasi, karakter aneh, dll)
- ✓ Show hex representation untuk verify karakter
- ✓ Trace logic decision untuk avatar selection
- ✓ Show file size dan path yang dipilih

**Interpretasi output:**
```
Jekel: "Laki-laki"          → Harus match logic
Length: 11                  → Panjang string
Jekel Lowercase: "laki-laki" → Untuk perbandingan
```

---

## STEP 3: Improvements Made

### A. Function `getAvatarByGender()` (diperbaiki)

Sekarang support lebih banyak format:
- ✓ "Laki-laki" (standard)
- ✓ "laki laki" (dengan spasi)
- ✓ "laki" (singkat)
- ✓ "L" atau "M" (single letter)
- ✓ Case-insensitive (LAKI-LAKI, Laki-laki, dll bisa)

---

## STEP 4: Common Problems & Solutions

### Problem A: Avatar Duplikat

**Symptom:** `check-avatars.php` menunjukkan "DUPLIKAT TERDETEKSI"

**Cause:** File avatar_laki_laki.png adalah copy dari avatar_perempuan.png

**Solution:**
```
1. Download/siapkan gambar avatar laki-laki:
   - Dari: https://www.dicebear.com (Avataaars style, male)
   - Atau: Freepik, Generator online lainnya
   
2. Rename gambar menjadi: avatar_laki_laki.png
   - Format HARUS PNG
   - Ukuran: sebaiknya square (150x150 atau lebih)
   
3. Ganti file di: c:\laragon\www\perpuspelita\dist\img\
   - Backup old file (opsional)
   - Delete/replace avatar_laki_laki.png
   - Copy file baru
   
4. Refresh browser (Ctrl+F5) untuk clear cache
```

### Problem B: Jekel Data Kosong/Invalid

**Symptom:** `debug-avatar.php` menunjukkan Jekel: "NULL" atau value aneh

**Cause:** Field jekel di database kosong atau format tidak standard

**Solution:**
```
1. Edit profile siswa yang male
   Akses: Dashboard → Profil siswa → Edit Profil
   
2. Pastikan pilih "Laki-laki" di dropdown Jenis Kelamin
   
3. Simpan perubahan
   
4. Refresh dan check lagi
```

### Problem C: Level Hilang di Sidebar

**Symptom:** Avatar di sidebar ada, tapi label level tidak muncul

**Analysis:** Kemungkinan styling/CSS issue

**Temporary Solution:**
- Refresh browser dengan Ctrl+Shift+Delete (clear cache)
- Tekan Ctrl+F5 beberapa kali
- Shutdown browser sepenuhnya, buka ulang

**Permanent Solution:**
Jika masalah persisten, mungkin ada CSS yang menyembunyikan label.
Cek di browser's Developer Tools (F12):
1. Klik element inspector (panah di top-left console)
2. Click pada label level di sidebar
3. Lihat CSS yang applied
4. Check apakah ada `display: none` atau `visibility: hidden`

---

## STEP 5: Manual Test

Setelah fix, test dengan:

```
1. Login sebagai user PEREMPUAN
   → Check sidebar avatar (harus perempuan)
   → Check level label
   → Refresh page (avatar tetap perempuan)
   
2. Login sebagai user LAKI-LAKI
   → Check sidebar avatar (harus laki-laki BERBEDA dari perempuan)
   → Check level label
   → Refresh page (avatar tetap laki-laki)
   
3. Check di Profile Page
   → Akses: ?page=siswa/profile_siswa
   → Avatar di box foto profil harus sesuai jenis kelamin
   → Level harus ada di "Informasi Akun" section
```

---

## STEP 6: Browser Cache

Jika setelah ganti avatar masih muncul yang lama:

**Clear Cache:**
1. Tekan `Ctrl+Shift+Delete` (buka Clear Browsing Data)
2. Select "All time"
3. Cek: Images and files ✓
4. Click "Clear data"
5. Refresh page (Ctrl+F5)

**Alternative (Force refresh):**
Add query string ke img tag:
```html
<img src="dist/img/avatar_laki_laki.png?v=<?php echo time(); ?>" ...>
```

---

## STEP 7: Verify Improvements

Setelah semua fix, ini yang seharusnya berfungsi:

✅ Avatar dinamis sesuai jenis kelamin  
✅ Level terlihat di sidebar  
✅ 2-3 gambar avatar berbeda di sistem (dipilih sesuai gender)  
✅ Fallback ke universal avatar jika file tidak ada  
✅ Support berbagai format nilai jekel (case-insensitive, dengan/tanpa spasi)  

---

## File yang Dirubah:

1. `inc/koneksi.php` - Function avatar diperbaiki
2. `index.php` - Tambah query jekel saat init
3. `admin/siswa/profile_siswa.php` - Avatar dinamis
4. `debug-avatar.php` - NEW: Debug tool
5. `check-avatars.php` - NEW: Avatar file checker

---

## Helpful Links:

- 🎨 Avatar Generator: https://www.dicebear.com
- 🔍 Debug Avatar: http://localhost/perpuspelita/debug-avatar.php
- 🖼️ Check Files: http://localhost/perpuspelita/check-avatars.php

---

**Updated:** 7 Mar 2026  
**System:** Perpustakaan Pelita v1.0
