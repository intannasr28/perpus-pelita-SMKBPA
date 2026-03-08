# 🔍 Step-by-Step Avatar Debug

Karena avatar masih perempuan padahal user laki-laki, mari kita cek one-by-one.

---

## STEP 1: Cek Seluruh User di Database

**Akses:** http://localhost/perpuspelita/direct-debug.php

Ini akan menampilkan:
- ✅ Semua user di database
- ✅ Nilai jekel untuk setiap user  
- ✅ Avatar path yang seharusnya dipilih
- ✅ File status (exists atau tidak)

**Yang harus di-cek:**
- Ada user dengan jenis kelamin "Laki-laki"?
- File `avatar_laki_laki.png` ada (column "File Exists")?
- File `avatar_perempuan.png` ada?

---

## STEP 2: Trace Specific User

Setelah identify user laki-laki di STEP 1, salin ID-nya (misal: SISWA001).

**Akses:** http://localhost/perpuspelita/trace-avatar.php?id=SISWA001

Ganti `SISWA001` dengan ID user yang laki-laki.

Ini akan show:
- Database value jekel (raw)
- String analysis (hex, lowercase, trimmed)
- Logic decision (is_laki_laki check)
- Exact file path selected
- Final decision flow
- **Problems detected** (jika ada)

**Lihat di section "⚡ Decision Flow":**
- ① Jekel recognized?
- ② Decision made?
- ③ File exists?
- ④ Final result?

Jika ada yang ✗, itu adalah masalahnya.

---

## STEP 3: Common Issues & Fixes

### Issue A: "Jekel value tidak dikenali"

**Symptom di trace-avatar.php:**
```
① Jekel recognized? ✗ NO
```

**Cause:** Format jekel di database tidak standard

**Fix:**
1. Periksa nilai jekel (Raw): apa yang tertulis?
2. Lihat hex value - ada karakter aneh?
3. Edit profile user → ubah jenis kelamin ke "Laki-laki" standard
4. Simpan & test ulang

---

### Issue B: "avatar_laki_laki.png tidak ditemukan"

**Symptom di trace-avatar.php:**
```
③ File exists? ✗ NO - FALLBACK
```

**Cause:** File avatar_laki_laki.png belum ada atau hilang

**Fix:**
1. Cek langsung di folder: `c:\laragon\www\perpuspelita\dist\img\`
2. Apakah file `avatar_laki_laki.png` ada?
3. Jika tidak ada:
   - Download avatar laki-laki dari https://www.dicebear.com
   - Rename ke: `avatar_laki_laki.png`
   - Upload ke: `dist/img/`
4. Refresh & test ulang

---

### Issue C: "avatar_perempuan.png tidak ditemukan"

**Symptom di trace-avatar.php:**
```
③ File exists? ✗ NO - FALLBACK
```

Untuk user perempuan

**Fix:**
1. Cek folder: `c:\laragon\www\perpuspelita\dist\img\`
2. Apakah file `avatar_perempuan.png` ada?
3. Jika tidak, rename `avatar.png` → `avatar_perempuan.png`
4. Atau copy dari setup tool: http://localhost/perpuspelita/setup-avatar.php

---

## STEP 4: Verify Avatar Files

**Akses:** http://localhost/perpuspelita/check-avatars.php

Ini akan:
- Show status semua avatar files
- Preview kedua gambar
- Detect jika files duplikat

**Pastikan:**
- ✓ avatar_perempuan.png ada
- ✓ avatar_laki_laki.png ada  
- ✓ Keduanya BERBEDA (bukan duplikat)
- ✓ Preview menunjukkan gambar yang berbeda visual

---

## STEP 5: Full Test Cycle

Setelah semua fix:

1. **Buka incognito/private window** (untuk fresh session)
2. **Login sebagai user PEREMPUAN**
   - Lihat avatar di sidebar
   - Harus gambar PEREMPUAN
   - Refresh (Ctrl+F5) - tetap perempuan?

3. **Close incognito, buka ulang**
4. **Login sebagai user LAKI-LAKI yang bermasalah**  
   - Lihat avatar di sidebar
   - Harus gambar LAKI-LAKI (BERBEDA dari perempuan)
   - Refresh (Ctrl+F5) - tetap laki-laki?

5. **Check di profile:**
   - Go to: ?page=siswa/profile_siswa
   - Avatar di "Foto Profil" sesuai gender?
   - Jenis kelamin tertulis benar?

---

## 🆘 Jika Masih Gagal

Kumpulkan info berikut dari tools:

**Dari direct-debug.php:**
- Copas screenshot tabel (terutama user laki-laki)

**Dari trace-avatar.php (untuk user laki-laki yang gagal):**
- Jekel (Raw) value
- Jekel (Hex) value
- Is Laki-laki? (TRUE/FALSE)
- Selected Path
- Final Avatar

**File status:**
- Apakah avatar_laki_laki.png ada?
- Apakah avatar_perempuan.png ada?

Report dengan info di atas untuk detailed troubleshooting ✓

---

## 📞 Quick Links

| Tool | URL | Purpose |
|------|-----|---------|
| 🗄️ Direct Debug | http://localhost/perpuspelita/direct-debug.php | Lihat semua user & file status |
| 🔬 Trace Avatar | http://localhost/perpuspelita/trace-avatar.php?id=USER_ID | Trace flow untuk 1 user |
| 🖼️ Check Files | http://localhost/perpuspelita/check-avatars.php | Compare & preview avatar files |

---

## 💡 Root Cause Likely

Based on issue deskripsi (masih perempuan padahal laki-laki):

**Most likely:** File `avatar_laki_laki.png` belum ada  
**Atau:** File ada tapi isi sama dengan perempuan (duplikat)

Check langsung dengan **direct-debug.php** atau **check-avatars.php** untuk confirm!

