# VISUAL GUIDE - ALUR SISTEM ID SISWA

## 📊 Diagram Alur Registrasi - Before vs After

### **BEFORE (Sistem Lama)**
```
Register Siswa
    ↓
Query: "SELECT id_anggota FROM tb_anggota ORDER BY DESC LIMIT 1"
    ↓
Ambil: A999
    ↓
Increment: 999 + 1 = 1000
    ↓
❌ PROBLEM: Nilai 1000 tidak bisa diformat ke A1000 (hanya bisa 3 digit)
❌ Kapasitas habis di A999 = hanya 999 siswa maksimal
```

---

### **AFTER (Sistem Baru - Opsi 1)**
```
Register Siswa
    ↓
Include: inc/helper_id_siswa.php
    ↓
Call: generateID_4Digit($koneksi)
    ↓
Query: "SELECT id_anggota FROM tb_anggota WHERE id_anggota REGEXP '^A[0-9]{4}$'"
    ↓
Ambil: A0999
    ↓
Increment: 999 + 1 = 1000
    ↓
Format: str_pad(1000, 4, "0") = "1000"
    ↓
Hasil: A1000 ✓
    ↓
Max Capacity: A9999 = 9,999 siswa (10x lebih besar!)
```

---

## 🔄 Diagram - Soft Delete + Reuse (Opsi 2)

### **Skenario Sebelum:**
```
Siswa Aktif ID: A0001, A0002, A0003, A0004, A0005
                 Budi   Anto   Cipta  Diana  Eka

Siswa Lulus: Tidak ada yang tadinya
             (data dihapus, ID tidak bisa reuse)

Kapasitas maksimal tetap 9,999
```

---

### **Skenario Sesudah (Opsi 2):**
```
TAHUN 2024:
┌────────────────────────────┐
│ Siswa Aktif (400)          │
│ A0001: Budi       [AKTIF]  │
│ A0002: Anto       [AKTIF]  │
│ A0003: Cipta      [AKTIF]  │
│ A0004: Diana      [AKTIF]  │
│ A0005: Eka        [AKTIF]  │
└────────────────────────────┘

Tahun 2025:
Siswa Lulus (status = LULUS):
   A0001: Budi (LULUS, tgl_lulus=2025-04-10)
   A0002: Anto (LULUS, tgl_lulus=2025-04-11)

Siswa BARU daftar -> Dapat ID A0001 (REUSE dari Budi lulus)
Status ID A0001 update:
   Old: [LULUS] Budi
   New: [AKTIF] Fatimah (siswa baru)

Database tetap track history:
   - Budi: A0001 (2024-2025, LULUS)
   - Fatimah: A0001 (2025-..., AKTIF)
```

---

## 📈 Chart - Kapasitas ID Comparison

```
OPSI 1: FORMAT 4 DIGIT (A0001-A9999)
┌─────────────────────────────────┐
│ Kapasitas: 9,999 siswa          │
│ Untuk: 5-10 tahun (sekolah besar)
│ Effort: 🟢 Mudah (1-2 jam)      │
└─────────────────────────────────┘

OPSI 2: SOFT DELETE + REUSE
┌─────────────────────────────────┐
│ Kapasitas: UNLIMITED (selama recycle)│
│ Untuk: Long-term sustainability  │
│ Effort: 🟠 Medium (3-4 jam)      │
└─────────────────────────────────┘

OPSI 3: FORMAT TAHUN (A2024001-A2024999)
┌─────────────────────────────────┐
│ Kapasitas: 999 siswa/tahun       │
│ Untuk: Medium school, reset/tahun │
│ Effort: 🟡 Medium (1-2 jam)      │
└─────────────────────────────────┘
```

---

## 🗂️ File Structure - Lokasi File

```
c:\laragon\www\perpuspelita\
│
├── 📄 README_SOLUSI_ID.md              ← START HERE (ringkasan)
├── 📄 SOLUSI_ID_SISWA.md               ← Analisis detil
├── 📄 IMPLEMENTASI_ID_SISWA.md         ← Step-by-step guide
├── 📄 COPY_PASTE_GUIDE.md              ← Copy-paste code ready
│
├── 📄 register.php                     ← [MODIFY] Update ID generation
├── 📄 register_v2.php                  ← Reference versi baru (optional)
│
├── inc/
│   ├── koneksi.php                     ← Existing
│   └── 📄 helper_id_siswa.php          ← [CREATE] Helper functions
│
├── admin/siswa/
│   ├── ... (existing files)
│   └── 📄 manage_lulus.php             ← [OPTIONAL] Admin interface
│
└── 📄 MIGRATION_ID_SISWA.sql           ← SQL migration scripts
```

---

## 🔌 Component Diagram

```
┌──────────────────────────────────────────────┐
│         REGISTER FORM (register.php)         │
│  ID: [____] (read-only, auto-generated)     │
│  Nama: [________________]                    │
│  ...                                         │
└──────────────────────────┬──────────────────┘
                           │
                           ↓
                ┌──────────────────────┐
                │  helper_id_siswa.php │
                │  - generateID()      │
                │  - FORMAT_4DIGIT     │
                │  - FORMAT_TAHUN      │
                │  - FORMAT_REUSE      │
                └──────────┬───────────┘
                           │
                           ↓
        ┌──────────────────────────────────┐
        │      Database (tb_anggota)       │
        │  id_anggota VARCHAR(10)          │
        │  nama_anggota VARCHAR(100)       │
        │  status ENUM (jika opsi 2)       │
        │  tgl_lulus DATE (jika opsi 2)    │
        └──────────────────────────────────┘
                           │
                           ↓
        ┌──────────────────────────────────┐
        │  ADMIN PANEL (manage_lulus.php)  │
        │  - View siswa aktif              │
        │  - View siswa lulus              │
        │  - Luluskan siswa                │
        │  - Restore siswa                 │
        │  - Statistik penggunaan ID       │
        └──────────────────────────────────┘
```

---

## 📞 Call Flow - Bagaimana ID Di-Generate

### **Flow Opsi 1: FORMAT_4DIGIT**

```
1. User buka register.php
   ↓
2. PHP load: include "inc/helper_id_siswa.php"
   ↓
3. Call: generateID($koneksi, 'FORMAT_4DIGIT')
   ↓
4. Function cari ID terakhir di DB
   - Query: SELECT id_anggota FROM tb_anggota 
            WHERE id_anggota REGEXP '^A[0-9]{4}$' 
            ORDER BY DESC LIMIT 1
   - Ambil: A0003 (misal)
   ↓
5. Extract number: substr('A0003', 1, 4) = '0003'
   ↓
6. Increment: (int)'0003' + 1 = 4
   ↓
7. Format: str_pad(4, 4, "0") = "0004"
   ↓
8. Concat: "A" . "0004" = "A0004"
   ↓
9. Return: ['success' => true, 'id' => 'A0004']
   ↓
10. Tampil di form: <input value="A0004">
    ↓
11. User submit form → ID A0004 disimpan ke DB
```

---

### **Flow Opsi 2: FORMAT_REUSE**

```
1. User buka register.php
   ↓
2. Call: generateID($koneksi, 'FORMAT_REUSE')
   ↓
3. Cek ada ID yang bisa direuse?
   Query: SELECT id_anggota FROM tb_anggota 
          WHERE status IN ('LULUS', 'PINDAH', 'NONAKTIF')
          ORDER BY CAST(SUBSTRING(...) AS UNSIGNED) ASC
          LIMIT 1
   ↓
4. Ada hasil? (misal: A0001 yang statusnya LULUS)
   ↓
5a. YES → Return: A0001 (dengan flag is_reuse=true)
    ↓
    Tampil di form dengan warning: "ID ini di-reuse dari siswa lulus"
   ↓
5b. NO → Fallback ke FORMAT_4DIGIT
    Generate ID baru normal (A0100, A0101, dst)
```

---

## 🔄 Data Flow - Soft Delete Siswa Lulus

```
ADMIN PANEL (manage_lulus.php)
│
├─ Click tombol "LULUS" di siswa
│  ↓
└─ Form Submit: id_anggota=A0001
   ↓
   PROSES BACKEND:
   ├─ Call: luluskanSiswa($koneksi, 'A0001', 'admin1')
   │  ↓
   │  ├─ UPDATE tb_anggota SET status='LULUS' WHERE id_anggota='A0001'
   │  ├─ UPDATE tb_anggota SET tgl_lulus=NOW() WHERE id_anggota='A0001'
   │  ├─ Log ke tb_log_activity
   │  └─ Return: ['success'=>true, 'message'=>'Siswa berhasil diluluskan']
   │
   ├─ Redirect: manage_lulus.php?success=...
   ↓
   DATABASE UPDATE:
   OLD: A0001│Budi  │AKTIF  │NULL      │NULL        │NULL    │
   NEW: A0001│Budi  │LULUS  │2025-03-09│NULL        │admin1  │
   ↓
   HASIL:
   ├─ A0001 hilang dari tab "Siswa Aktif"
   ├─ A0001 muncul di tab "Siswa Lulus"
   ├─ A0001 tersedia untuk REUSE saat siswa baru daftar
   └─ History tetap tersimpan untuk audit trail
```

---

## ✅ State Machine - Status Siswa

```
               ╔═══════════╗
               ║  DEFAULT  ║
               ║  (CREATE) ║
               ╚═════╤═════╝
                     │
                     ↓
             ┌──────────────┐
             │   AKTIF      │ ← Default status saat daftar
             └──────┬───────┘
                    │
        ┌───────────┼───────────┐
        │           │           │
        ↓           ↓           ↓
    ┌────────┐ ┌────────┐ ┌──────────┐
    │ LULUS  │ │ PINDAH │ │ NONAKTIF │
    └────┬───┘ └───┬────┘ └────┬─────┘
         │         │           │
         └─────────┼───────────┘
                   │
        ┌──────────┴──────────┐
        │                     │
        ↓ (dapat di-restore)  ↓
    ┌──────────┐          ┌───────┐
    │ AKTIF    │ ← Kembali│Restore│
    │ (RESTORE)│          └───────┘
    └──────────┘
    
Catatan:
- Soft Delete (tidak hard delete)
- ID bisa di-reuse saat status bukan AKTIF
- Dapat di-restore kapan saja
- Audit trail tetap tersimpan
```

---

## 📊 Capacity Planning

### **Scenario: Opsi 1 - Format 4 Digit**

```
Tahun 1 (2024): 400 siswa aktif
Tahun 2 (2025): 400 + 100 baru = 500 siswa aktif
Tahun 3 (2026): 500 + 150 baru = 650 siswa aktif
...
Tahun 10 (2033): ~2000 siswa aktif

Masih ada kapasitas: 9999 - 2000 = 7999 ID tersisa
Timeline untuk "penuh": ~20 tahun (dengan sistem current growth)

Solusi jika mendekati limit:
- Migrasi ke Opsi 2 (FORMAT_REUSE) dengan soft delete
- Atau migrasi ke format lain (misal B0001, C0001, dst)
```

---

## 🎯 Decision Tree - Pilih Opsi Mana?

```
START: "Berapa siswa yang kemungkinan aktif per tahun?"
    │
    ├─ < 999 siswa/tahun?
    │  └─ → OPSI 3: FORMAT_TAHUN (A2024001-A2024999)
    │       Keuntungan: Reset tahun, mudah tracking generasi
    │
    ├─ 1000-9999 siswa?
    │  └─ → OPSI 1: FORMAT_4DIGIT (A0001-A9999) ⭐ RECOMMENDED
    │       Keuntungan: Cukup lama (5-10 tahun), simple implementasi
    │
    └─ > 9999 siswa?
       └─ → OPSI 2: FORMAT_REUSE (unlimited, soft delete)
            Keuntungan: Sustainability jangka panjang
            Catatan: Perlu implementasi admin interface
```

---

## 🚀 Implementation Timeline

```
DAY 1:
├─ 09:00 - Backup database
├─ 10:00 - Copy file helper_id_siswa.php
├─ 10:30 - Run migration SQL (format convert)
├─ 11:00 - Verify hasil conversion
├─ 12:00 - Update register.php
├─ 13:00 - Testing di local
└─ 14:00 - Deploy ke staging

DAY 2:
├─ 09:00 - Final testing di staging
├─ 10:00 - Deploy ke production
├─ 10:30 - Monitoring & logging
└─ 12:00 - Dokumentasi & close

OPTIONAL (Minggu ke-2):
├─ Copy admin/siswa/manage_lulus.php
├─ Setup soft delete columns
└─ Testing manage lulus interface
```

---

**Diagram Version:** 1.0  
**Updated:** 9 Maret 2026
