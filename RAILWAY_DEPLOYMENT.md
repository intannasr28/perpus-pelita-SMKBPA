# 🚀 Panduan Deploy E-PERPUS ke Railway.app

Railway.app adalah platform cloud yang sempurna untuk aplikasi PHP + MySQL. Berikut langkah-langkah lengkapnya.

---

## 📋 Prerequisites

✅ GitHub account (untuk push kode)  
✅ Railway account (sign up gratis di https://railway.app)  
✅ Git installed di komputer  
✅ Code sudah di-push ke GitHub repository  

---

## Step 1: Persiapan di GitHub

### 1a. Buat GitHub Repository
```bash
# Inisialisasi git di folder perpuspelita
cd perpuspelita
git init
git add .
git commit -m "Initial commit E-PERPUS"
```

### 1b. Push ke GitHub
```bash
# Tambahkan remote repository
git remote add origin https://github.com/username/perpuspelita.git

# Push ke GitHub
git branch -M main
git push -u origin main
```

---

## Step 2: Setup di Railway.app

### 2a. Sign Up / Login
1. Buka https://railway.app
2. Klik "Login with GitHub" atau "Sign Up"
3. Authorize Railway mengakses GitHub account Anda

### 2b. Buat New Project
1. Klik **"New Project"** di dashboard
2. Pilih **"Deploy from GitHub repo"**
3. Cari repository `perpuspelita` dan klik **"Import"**
4. Railway akan otomatis detect PHP dan setup basic config

### 2c. Tambah MySQL Service
1. Di Railway project, klik **"+ Add Service"**
2. Pilih **"Database"** → **"MySQL"**
3. Railway otomatis buat MySQL instance
4. Tunggu ~30 detik sampai service running

---

## Step 3: Setup Environment Variables

### 3a. Get Database Credentials dari Railway
1. Klik service **"MySQL"** di project
2. Buka tab **"Variables"**
3. Copy value dari environment variables:
   - `MYSQL_HOSTNAME` / `MYSQL_HOST`
   - `MYSQL_USER`
   - `MYSQL_PASSWORD`
   - `MYSQL_DB`
   - `MYSQL_PORT` (default: 3306)

### 3b. Setup Environment Variables untuk PHP App
1. Klik service **"web"** (PHP app) di project
2. Buka tab **"Variables"**
3. **Add new variables:**

```
DB_HOST = ${{ MySQL.MYSQL_HOSTNAME }}
DB_USER = ${{ MySQL.MYSQL_USER }}
DB_PASSWORD = ${{ MySQL.MYSQL_PASSWORD }}
DB_NAME = ${{ MySQL.MYSQL_DB }}
DB_PORT = ${{ MySQL.MYSQL_PORT }}
APP_ENV = production
TIMEZONE = Asia/Jakarta
```

**Note:** String `${{ MySQL.MYSQL_HOSTNAME }}` adalah Railway syntax untuk reference MySQL service variables secara otomatis.

### 3c. Buat .env File di Railway
1. Klik **"Environment"** pada service PHP
2. Create new file `.env` dengan isi:

```
DB_HOST=${{ MySQL.MYSQL_HOSTNAME }}
DB_USER=${{ MySQL.MYSQL_USER }}
DB_PASSWORD=${{ MySQL.MYSQL_PASSWORD }}
DB_NAME=${{ MySQL.MYSQL_DB }}
DB_PORT=${{ MySQL.MYSQL_PORT }}
APP_URL=${{ Railway.RAILWAY_PUBLIC_DOMAIN }}
TIMEZONE=Asia/Jakarta
```

---

## Step 4: Update Database Configuration

### 4a. Update inc/koneksi.php untuk Railway
Edit file `inc/koneksi.php`:

```php
<?php
// Gunakan environment variables jika tersedia (Railway), fallback ke localhost (development)

$host = getenv('DB_HOST') ?: 'localhost';
$user = getenv('DB_USER') ?: 'root';
$password = getenv('DB_PASSWORD') ?: '';
$database = getenv('DB_NAME') ?: 'perpuspelita';
$port = getenv('DB_PORT') ?: 3306;

// Timezone
date_default_timezone_set('Asia/Jakarta');

// Create connection
$koneksi = new mysqli($host, $user, $password, $database, $port);

// Check connection
if ($koneksi->connect_error) {
    die("Connection failed: " . $koneksi->connect_error);
}

// Set charset
$koneksi->set_charset("utf8mb4");

// Set session path untuk tmp folder
$tmp_path = realpath(__DIR__ . '/..') . DIRECTORY_SEPARATOR . 'tmp';
if (!is_dir($tmp_path)) {
    @mkdir($tmp_path, 0777, true);
}
ini_set('session.save_path', $tmp_path);
?>
```

### 4b. Push Changes ke GitHub
```bash
git add inc/koneksi.php
git commit -m "Update database config untuk Railway"
git push
```

Railway otomatis redeploy setelah push ke GitHub ✅

---

## Step 5: Import Database

### 5a. Connect ke Railway MySQL via CLI
```bash
# Install MySQL client jika belum
# Windows: Download dari https://dev.mysql.com/downloads/mysql/
# or gunakan WSL/Git Bash

# Connect ke Railway MySQL
mysql -h <MYSQL_HOSTNAME> -u <MYSQL_USER> -p -P <MYSQL_PORT>
# Input password saat diminta
```

### 5b. Create Database & Import SQL
```sql
-- Jika belum ada database
CREATE DATABASE perpuspelita;
USE perpuspelita;

-- Import dari file SQL (lakukan dari terminal)
-- mysql -h <MYSQL_HOSTNAME> -u <MYSQL_USER> -p <MYSQL_DB> < data_perpus.sql
```

### 5c. Alternatif: Upload via Railway Console
1. Di Railway dashboard, klik MySQL service
2. Buka **"Data"** tab
3. Paste SQL queries langsung atau
4. Gunakan **"Connect"** → **"Railway Dashboard"** → upload SQL file

---

## Step 6: Verify Deployment

### 6a. Cek Railway Logs
1. Buka project di Railway
2. Klik service **"web"** 
3. Buka tab **"Logs"**
4. Cek error atau warning messages

### 6b. Test Live URL
1. Di Railway, cari **"Public Domain"** atau **"URL"**
2. Format: `https://perpuspelita-production.railway.app` (atau domain custom)
3. Akses: `https://perpuspelita-production.railway.app/login.php`
4. Coba login dengan credentials

---

## Step 7: Setup Custom Domain (Optional)

### 7a. Di Railway Console
1. Buka service **"web"**
2. Klik **"Settings"** → **"Domains"**
3. Klik **"+ Add Domain"**
4. Masukkan domain: `perpustakaan.yourdomain.com`

### 7b. Konfigurasi DNS
- Go to domain registrar (Niagahoster, Cloudflare, dll)
- Tambah CNAME record:
  ```
  Host: perpustakaan
  CNAME: <railway-domain>
  TTL: 3600
  ```
- Tunggu ~24 jam DNS propagate

---

## Step 8: Continuous Deployment Setup

Railway sudah otomatis deploy setiap kali push ke GitHub!

Workflow:
1. Edit code di laptop
2. `git add .`
3. `git commit -m "Update fitur"`
4. `git push`
5. Railway otomatis detect perubahan → rebuild → deploy ✅

---

## 🔧 Troubleshooting

### Error: "Connection refused" ke MySQL
**Solusi:**
1. Pastikan MySQL service sudah running di Railway
2. Check environment variables ada di PHP service
3. Verify credentials di `inc/koneksi.php`
4. Restart PHP service: klik service → "Redeploy"

### Error: "Permission denied" untuk tmp folder
**Solusi:**
```bash
# SSH ke Railway container
railway shell

# Create tmp folder
mkdir -p tmp
chmod 777 tmp
```

### Database import error
**Solusi:**
1. Check charset: pastikan SQL file menggunakan UTF-8
2. Split file: jika file terlalu besar, split jadi bagian-bagian
3. Gunakan Railway Data tab untuk execute query one-by-one

### PHP Memory Limit Error
**Solusi:**
Buat file `php.ini` di root folder:
```ini
memory_limit = 256M
upload_max_filesize = 50M
post_max_size = 50M
max_execution_time = 300
```

---

## 📊 Monitoring & Maintenance

### View Logs
```bash
# Terminal (pastikan Railway CLI installed)
railway logs -s web

# or dari Railway dashboard
```

### Database Backup
```bash
# Export database
mysqldump -h <MYSQL_HOSTNAME> -u <MYSQL_USER> -p <MYSQL_DB> > backup.sql

# Save ke GitHub atau external storage
```

### Update Application
```bash
# Edit code → commit → push
git add .
git commit -m "Fix bug atau fitur baru"
git push

# Railway otomatis redeploy
```

---

## 💰 Pricing & Limits

| Plan | Price | Limits |
|------|-------|--------|
| **Free** | $0 | 5GB storage, 1GB RAM, limited compute |
| **Hobby** | $5/bulan | 20GB to 100GB storage, custom resources |
| **Pro** | Pay as you go | Unlimited with pay-per-use model |

**Catatan:**
- E-PERPUS (basic) bisa jalan di free plan
- Kalau traffic naik, Railway auto-scale πŸš€

---

## βœ… Checklist Deployment

- [ ] GitHub repo siap dengan kode terbaru
- [ ] Railway project created
- [ ] MySQL service added & running
- [ ] Environment variables configured
- [ ] `inc/koneksi.php` updated untuk Railway
- [ ] Database SQL imported
- [ ] Login page accessible
- [ ] Admin login working
- [ ] Dapat buku dari database
- [ ] Peminjaman bisa diinput (test)
- [ ] Custom domain configured (optional)

---

## 🎉 Selesai!

Aplikasi E-PERPUS sudah live di Railway! 🌐

**URL Live:** https://perpuspelita-production.railway.app  
**Admin Access:** Login dengan credentials yang ada di database  
**Database:** MySQL di Railway (auto-managed & backed up)  

Setiap `git push` → Railway otomatis rebuild & deploy πŸš€

---

## 📞 Resources

- **Railway Docs:** https://docs.railway.app
- **PHP Support:** https://railway.app/guides/php
- **MySQL Docs:** https://docs.railway.app/databases/mysql
- **Custom Domain:** https://docs.railway.app/guides/domains

Sukses! 🎊
