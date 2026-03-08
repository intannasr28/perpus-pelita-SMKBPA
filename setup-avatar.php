<?php
/**
 * Avatar Setup Helper
 * Script untuk memudahkan setup file avatar gender-based
 * Akses: http://localhost/perpuspelita/setup-avatar.php
 */

// Simple check if user is admin (optional)
session_start();

$message = '';
$status = '';

// Get current avatar files status
$avatar_path = __DIR__ . '/dist/img/';
$avatar_perempuan = $avatar_path . 'avatar_perempuan.png';
$avatar_laki = $avatar_path . 'avatar_laki_laki.png';
$avatar_default = $avatar_path . 'avatar.png';

$file_status = array(
    'avatar.png' => file_exists($avatar_default),
    'avatar_perempuan.png' => file_exists($avatar_perempuan),
    'avatar_laki_laki.png' => file_exists($avatar_laki)
);

// Process rename if requested
if ($_POST && isset($_POST['action'])) {
    if ($_POST['action'] == 'rename_default') {
        if (file_exists($avatar_default) && !file_exists($avatar_perempuan)) {
            if (rename($avatar_default, $avatar_perempuan)) {
                $message = "✓ Berhasil! avatar.png direname menjadi avatar_perempuan.png";
                $status = "success";
                $file_status['avatar.png'] = false;
                $file_status['avatar_perempuan.png'] = true;
            } else {
                $message = "✗ Gagal rename file. Cek permissions.";
                $status = "error";
            }
        } else {
            $message = "✗ File avatar.png tidak ditemukan atau avatar_perempuan.png sudah ada.";
            $status = "error";
        }
    }
    
    if ($_POST['action'] == 'copy_as_placeholder') {
        if (file_exists($avatar_perempuan) && !file_exists($avatar_laki)) {
            if (copy($avatar_perempuan, $avatar_laki)) {
                $message = "✓ Berhasil! avatar_perempuan.png dicopy menjadi avatar_laki_laki.png (placeholder)";
                $status = "success";
                $file_status['avatar_laki_laki.png'] = true;
            } else {
                $message = "✗ Gagal copy file. Cek permissions.";
                $status = "error";
            }
        } else {
            $message = "✗ avatar_perempuan.png tidak ditemukan atau avatar_laki_laki.png sudah ada.";
            $status = "error";
        }
    }
}
?>

<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>Avatar Setup - Perpustakaan Pelita</title>
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }
        
        body {
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            min-height: 100vh;
            display: flex;
            justify-content: center;
            align-items: center;
            padding: 20px;
        }
        
        .container {
            background: white;
            border-radius: 10px;
            box-shadow: 0 10px 40px rgba(0,0,0,0.2);
            max-width: 600px;
            width: 100%;
            padding: 40px;
        }
        
        h1 {
            color: #333;
            margin-bottom: 10px;
            font-size: 28px;
        }
        
        .subtitle {
            color: #666;
            margin-bottom: 30px;
            font-size: 14px;
        }
        
        .alert {
            padding: 15px;
            border-radius: 5px;
            margin-bottom: 20px;
            display: none;
        }
        
        .alert.success {
            display: block;
            background: #d4edda;
            border: 1px solid #c3e6cb;
            color: #155724;
        }
        
        .alert.error {
            display: block;
            background: #f8d7da;
            border: 1px solid #f5c6cb;
            color: #721c24;
        }
        
        .status-box {
            background: #f8f9fa;
            border: 1px solid #ddd;
            border-radius: 5px;
            padding: 20px;
            margin-bottom: 20px;
        }
        
        .file-item {
            display: flex;
            justify-content: space-between;
            align-items: center;
            padding: 10px 0;
            border-bottom: 1px solid #eee;
        }
        
        .file-item:last-child {
            border-bottom: none;
        }
        
        .file-name {
            font-weight: 500;
            color: #333;
        }
        
        .file-status {
            padding: 5px 12px;
            border-radius: 3px;
            font-size: 12px;
            font-weight: 600;
            text-transform: uppercase;
        }
        
        .file-status.exists {
            background: #d4edda;
            color: #155724;
        }
        
        .file-status.missing {
            background: #f8d7da;
            color: #721c24;
        }
        
        .actions {
            margin-top: 20px;
        }
        
        .action-group {
            background: #f0f0f0;
            border-left: 4px solid #667eea;
            padding: 15px;
            margin-bottom: 15px;
            border-radius: 3px;
        }
        
        .action-group h3 {
            font-size: 14px;
            color: #333;
            margin-bottom: 10px;
        }
        
        .action-group p {
            font-size: 12px;
            color: #666;
            margin-bottom: 12px;
            line-height: 1.5;
        }
        
        .btn {
            display: inline-block;
            padding: 10px 20px;
            border: none;
            border-radius: 5px;
            cursor: pointer;
            font-size: 14px;
            font-weight: 600;
            transition: all 0.3s;
        }
        
        .btn-primary {
            background: #667eea;
            color: white;
        }
        
        .btn-primary:hover {
            background: #5568d3;
            transform: translateY(-2px);
            box-shadow: 0 5px 15px rgba(102, 126, 234, 0.4);
        }
        
        .btn-primary:disabled {
            background: #ccc;
            cursor: not-allowed;
            transform: none;
        }
        
        .info-box {
            background: #e7f3ff;
            border: 1px solid #b3d9ff;
            color: #004085;
            padding: 15px;
            border-radius: 5px;
            margin-top: 20px;
            font-size: 13px;
            line-height: 1.6;
        }
        
        .upload-section {
            background: #fff3cd;
            border: 1px solid #ffc107;
            border-radius: 5px;
            padding: 15px;
            margin-top: 20px;
        }
        
        .upload-section h4 {
            color: #856404;
            margin-bottom: 10px;
            font-size: 14px;
        }
        
        .upload-section p {
            color: #856404;
            font-size: 12px;
            line-height: 1.5;
        }
        
        code {
            background: #f4f4f4;
            padding: 2px 6px;
            border-radius: 3px;
            font-family: 'Courier New', monospace;
        }
    </style>
</head>
<body>
    <div class="container">
        <h1>🎨 Avatar Setup Helper</h1>
        <p class="subtitle">Setup avatar dinamis berdasarkan jenis kelamin</p>
        
        <?php if ($message): ?>
            <div class="alert <?php echo $status; ?>">
                <?php echo $message; ?>
            </div>
        <?php endif; ?>
        
        <div class="status-box">
            <h3 style="margin-bottom: 15px; color: #333; font-size: 16px;">📁 Status File Avatar</h3>
            
            <div class="file-item">
                <span class="file-name">avatar.png (default universal)</span>
                <span class="file-status <?php echo $file_status['avatar.png'] ? 'exists' : 'missing'; ?>">
                    <?php echo $file_status['avatar.png'] ? '✓ Ada' : '✗ Tidak Ada'; ?>
                </span>
            </div>
            
            <div class="file-item">
                <span class="file-name">avatar_perempuan.png</span>
                <span class="file-status <?php echo $file_status['avatar_perempuan.png'] ? 'exists' : 'missing'; ?>">
                    <?php echo $file_status['avatar_perempuan.png'] ? '✓ Ada' : '✗ Tidak Ada'; ?>
                </span>
            </div>
            
            <div class="file-item">
                <span class="file-name">avatar_laki_laki.png</span>
                <span class="file-status <?php echo $file_status['avatar_laki_laki.png'] ? 'exists' : 'missing'; ?>">
                    <?php echo $file_status['avatar_laki_laki.png'] ? '✓ Ada' : '✗ Tidak Ada'; ?>
                </span>
            </div>
        </div>
        
        <div class="actions">
            <div class="action-group">
                <h3>📝 Langkah 1: Rename Avatar Default</h3>
                <p>Gambar avatar berhijab (perempuan) yang sudah ada di avatar.png akan direname menjadi avatar_perempuan.png</p>
                <form method="POST" style="display:inline;">
                    <input type="hidden" name="action" value="rename_default">
                    <button type="submit" class="btn btn-primary" <?php echo !$file_status['avatar.png'] || $file_status['avatar_perempuan.png'] ? 'disabled' : ''; ?>>
                        ✓ Rename avatar.png
                    </button>
                </form>
            </div>
            
            <div class="action-group">
                <h3>📋 Langkah 2: Copy Sebagai Placeholder</h3>
                <p>Membuat placeholder avatar_laki_laki.png dari avatar_perempuan.png (untuk testing). Nanti bisa diganti dengan gambar laki-laki yang sebenarnya.</p>
                <form method="POST" style="display:inline;">
                    <input type="hidden" name="action" value="copy_as_placeholder">
                    <button type="submit" class="btn btn-primary" <?php echo !$file_status['avatar_perempuan.png'] || $file_status['avatar_laki_laki.png'] ? 'disabled' : ''; ?>>
                        ✓ Copy Sebagai Placeholder
                    </button>
                </form>
            </div>
        </div>
        
        <div class="upload-section">
            <h4>📤 Langkah 3: Upload Gambar Avatar Laki-laki (Opsional)</h4>
            <p>Untuk hasil terbaik, upload gambar avatar laki-laki yang sebenarnya:</p>
            <ol style="margin-left: 20px; margin-top: 10px; color: #856404; font-size: 12px;">
                <li>Siapkan gambar avatar laki-laki dalam format <strong>PNG</strong></li>
                <li>Ukuran sebaiknya <strong>square</strong> (150x150px atau lebih)</li>
                <li>Simpan dengan nama: <code>avatar_laki_laki.png</code></li>
                <li>Upload ke folder: <code>dist/img/</code></li>
                <li>Atau gunakan FTP client seperti FileZilla</li>
            </ol>
            <p style="margin-top: 10px; color: #856404; font-size: 12px;">
                <strong>Sumber Gambar Gratis:</strong><br>
                • Dicebear (https://www.dicebear.com) - Avataaars style<br>
                • Freepik - Search "student avatar"<br>
                • Pixel Art online generators
            </p>
        </div>
        
        <div class="info-box">
            <strong>ℹ️ Informasi Penting:</strong><br><br>
            ✓ Setelah setup selesai, avatar akan otomatis berubah sesuai jenis kelamin user<br>
            ✓ Login dengan user berbeda jenis kelamin untuk test<br>
            ✓ Jika ada file yang tidak ditemukan, sistem akan fallback ke avatar.png<br>
            ✓ Gambar PNG dengan transparent background akan terlihat lebih baik<br>
            ✓ Update browser cache (Ctrl+F5) jika avatar tidak berubah
        </div>
    </div>
</body>
</html>
