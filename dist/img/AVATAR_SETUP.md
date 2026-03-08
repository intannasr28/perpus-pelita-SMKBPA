<!-- Helper file untuk fungsi avatar yang bisa di-include -->
<!-- Lokasi: dist/js/avatar-helper.js atau langsung di functions -->

<!--
AVATAR DYNAMIC GENDER-BASED SYSTEM
====================================

Sistem ini menggunakan jenis kelamin (jekel) dari database untuk
menampilkan avatar yang sesuai untuk setiap pengguna.

FILE YANG BERUBAH:
1. inc/koneksi.php - Tambah fungsi getAvatarByGender() dan getAvatarWithFallback()
2. admin/siswa/profile_siswa.php - Update tampilan avatar di profil
3. index.php - Update avatar di navbar/sidebar

FUNGSI HELPER:
- getAvatarByGender($jekel): Mengembalikan path avatar berdasarkan jenis kelamin
- getAvatarWithFallback($jekel): Amankan dengan fallback jika file tidak ada

SETUP AVATAR FILES:
====================

Required directories:
- dist/img/ (sudah ada)

Required files:
1. dist/img/avatar_perempuan.png (rename dari avatar.png yang sudah ada)
2. dist/img/avatar_laki_laki.png (tambah gambar laki-laki baru)
3. dist/img/avatar.png (fallback universal - boleh tetap ada)

CARA SETUP:
===========

Opsi 1 - Using File Manager (GUI):
1. Buka folder: c:\laragon\www\perpuspelita\dist\img\
2. Rename avatar.png → avatar_perempuan.png
3. Untuk avatar_laki_laki.png:
   - Copy avatar_perempuan.png
   - Rename copy menjadi avatar_laki_laki.png
   - Edit gambar dengan photo editor untuk ubah gaya rambut/penampilan atau gunakan gambar baru

Opsi 2 - Using PowerShell (Command Line):
```powershell
cd "c:\laragon\www\perpuspelita\dist\img"
Rename-Item -Path "avatar.png" -NewName "avatar_perempuan.png"
Copy-Item -Path "avatar_perempuan.png" -Destination "avatar_laki_laki.png"
```

Opsi 3 - Using PHP Script (Auto Rename):
Jalankan script ini di browser: http://localhost/perpuspelita/setup-avatar.php

TESTING:
=========
1. Login sebagai siswa yang jekelnya "Perempuan"
   - Lihat avatar di profile siswa atau dashboard
   - Harus menampilkan avatar_perempuan.png
   
2. Login sebagai siswa yang jekelnya "Laki-laki"  
   - Lihat avatar di profile siswa atau dashboard
   - Harus menampilkan avatar_laki_laki.png

TIPS & TRICKS:
===============
- Untuk hasil terbaik, gunakan gambar PNG dengan transparent background
- Ukuran gambar sebaiknya square (150x150px atau lebih)
- Jangan terlalu besar (> 100KB) untuk performa optimal
- Generator avatar online: https://api.dicebear.com/7.x/avataaars/svg
              Petunjuk: Buka link dan download SVG/PNG, ubah seed sesuai gender

FALLBACK BEHAVIOR:
===================
Jika file avatar tidak ditemukan:
- Sistem akan cek apakah avatar.png ada
- Jika ya, gunakan avatar.png sebagai fallback
- Jika tidak, gambar tidak akan tampil

UNTUK GAMBAR BERKUALITAS:
==========================
Sumber gambar avatar gratis:
1. Dicebear (https://www.dicebear.com)
   - Avataaars style (cocok untuk siswa)
   - Select gender saat generate
   - Download PNG

2. Freepik (https://www.freepik.com)
   - Search "student avatar boy" atau "student avatar girl"
   - Filter: PNG Transparent

3. Pixel Art Maker online:
   - Buat sendiri dengan style konsisten

PARAMETER JENIS KELAMIN:
========================
Database field: jekel (di table tb_anggota)
Valid values:
- "Laki-laki" → avatar_laki_laki.png
- "Perempuan" → avatar_perempuan.png
- Empty/NULL → fallback ke avatar_perempuan.png

SUPPORT & TROUBLESHOOTING:
==========================
Problem: Avatar tidak berubah
Solusi:
- Cek apakah file avatar_perempuan.png dan avatar_laki_laki.png sudah ada
- Cek nama file (case-sensitive di Linux, tapi tidak di Windows)
- Refresh browser (Ctrl+F5) untuk clear cache
- Check file permissions (harus readable)

Problem: Avatar broken image 404
Solusi:
- Pastikan file path benar: dist/img/avatar_*.png
- Pastikan file tidak corrupt
- Cek di browser console untuk error message

Problem: Hanya satu avatar yang muncul untuk semua user
Solusi:
- Cek field jekel di table tb_anggota apakah sudah terisi
- Eksekusi query: SELECT id_anggota, nama, jekel FROM tb_anggota;
- Edit profil user jika jekelnya belum diisi

================================================
File: dist/img/avatar-helper.md
Created: 2026-03-07
System: Perpustakaan Pelita
Version: 1.0
================================================
-->