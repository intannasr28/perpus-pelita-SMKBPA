<?php
/**
 * AUTO AVATAR CREATOR
 * Script untuk auto-create avatar_laki_laki.png jika belum ada
 * Akses: http://localhost/perpuspelita/auto-create-avatar.php
 */

$img_path = __DIR__ . '/dist/img/';
$avatar_laki = $img_path . 'avatar_laki_laki.png';
$avatar_perempuan = $img_path . 'avatar_perempuan.png';
$avatar_default = $img_path . 'avatar.png';

$status = array();

// Check files status
$f1 = file_exists($avatar_perempuan);
$f2 = file_exists($avatar_laki);
$f3 = file_exists($avatar_default);

// Try to create avatar_laki_laki.png
if (!$f2) {
    // Try copy dari avatar_perempuan
    if ($f1) {
        if (@copy($avatar_perempuan, $avatar_laki)) {
            $status['created'] = true;
            $status['method'] = 'copy from avatar_perempuan.png';
            $status['message'] = 'SUCCESS: avatar_laki_laki.png berhasil dibuat (copy from perempuan sebagai placeholder)';
        } else {
            $status['created'] = false;
            $status['error'] = 'Failed to copy avatar_perempuan.png';
        }
    }
    // Fallback: try copy dari avatar.png
    else if ($f3) {
        if (@copy($avatar_default, $avatar_laki)) {
            $status['created'] = true;
            $status['method'] = 'copy from avatar.png';
            $status['message'] = 'SUCCESS: avatar_laki_laki.png berhasil dibuat (copy from default)';
        } else {
            $status['created'] = false;
            $status['error'] = 'Failed to copy avatar.png';
        }
    } else {
        $status['created'] = false;
        $status['error'] = 'No source file found (perempuan dan default tidak ada)';
    }
} else {
    $status['created'] = null;
    $status['message'] = 'avatar_laki_laki.png sudah ada, tidak ada action needed';
}

?>

<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>Auto Create Avatar</title>
    <style>
        body {
            font-family: 'Segoe UI', Tahoma, sans-serif;
            padding: 20px;
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            min-height: 100vh;
        }
        .container {
            max-width: 600px;
            margin: 0 auto;
            background: white;
            padding: 30px;
            border-radius: 10px;
            box-shadow: 0 10px 40px rgba(0,0,0,0.2);
        }
        h1 { color: #333; }
        .result {
            padding: 20px;
            border-radius: 5px;
            margin: 20px 0;
            font-size: 16px;
            font-weight: 500;
        }
        .success {
            background: #d4edda;
            border: 1px solid #c3e6cb;
            color: #155724;
        }
        .error {
            background: #f8d7da;
            border: 1px solid #f5c6cb;
            color: #721c24;
        }
        .info {
            background: #d1ecf1;
            border: 1px solid #bee5eb;
            color: #0c5460;
        }
        .details {
            background: #f9f9f9;
            padding: 15px;
            border-radius: 5px;
            margin: 15px 0;
            border-left: 4px solid #667eea;
        }
        code {
            background: #f4f4f4;
            padding: 3px 6px;
            border-radius: 3px;
            font-family: monospace;
        }
        .btn {
            display: inline-block;
            padding: 10px 20px;
            background: #667eea;
            color: white;
            text-decoration: none;
            border-radius: 5px;
            margin-top: 10px;
        }
        .btn:hover { background: #5568d3; }
    </style>
</head>
<body>
    <div class="container">
        <h1>🎨 Auto Create Avatar Laki-laki</h1>
        
        <?php if ($status['created'] === true): ?>
            <div class="result success">
                ✅ <?php echo $status['message']; ?>
                <p style="margin: 10px 0 0 0; font-size: 14px;">
                    Method: <?php echo htmlspecialchars($status['method']); ?>
                </p>
            </div>
            
            <div class="details">
                <strong>File Status Sekarang:</strong><br>
                avatar_perempuan.png: <strong><?php echo file_exists($avatar_perempuan) ? '✓ ADA' : '✗ TIDAK ADA'; ?></strong><br>
                avatar_laki_laki.png: <strong><?php echo file_exists($avatar_laki) ? '✓ ADA' : '✗ TIDAK ADA'; ?></strong><br>
                avatar.png: <strong><?php echo file_exists($avatar_default) ? '✓ ADA' : '✗ TIDAK ADA'; ?></strong>
            </div>
            
            <div class="details" style="background: #fff3cd; border-left-color: #ffc107;">
                <strong>⚠️ Penting!</strong><br><br>
                File avatar_laki_laki.png berhasil dibuat sebagai PLACEHOLDER (sekarang sama visual dengan perempuan).<br><br>
                <strong>Untuk hasil terbaik:</strong>
                <ol>
                    <li>Buka: https://www.dicebear.com</li>
                    <li>Pilih style "Avataaars"</li>
                    <li>Filter "male"</li>
                    <li>Download PNG</li>
                    <li>Rename: <code>avatar_laki_laki.png</code></li>
                    <li>Replace file di folder <code>dist/img/</code></li>
                </ol>
            </div>
            
            <a href="index.php" class="btn">✓ Kembali ke Dashboard</a>
            
        <?php elseif ($status['created'] === false): ?>
            <div class="result error">
                ❌ Gagal membuat avatar_laki_laki.png<br>
                <p style="margin: 10px 0 0 0; font-size: 14px;">
                    Error: <?php echo htmlspecialchars($status['error']); ?>
                </p>
            </div>
            
            <div class="details" style="background: #fff3cd; border-left-color: #ffc107;">
                <strong>⚠️ Troubleshooting:</strong><br><br>
                1. Cek folder: <code>dist/img/</code><br>
                2. Apakah ada 1 file minimal:
                   <ul>
                       <li>avatar_perempuan.png</li>
                       <li>avatar.png</li>
                   </ul>
                3. Jika tidak ada dari keduanya, upload salah satu terlebih dahulu<br>
                4. Run script ini lagi
            </div>
            
        <?php else: ?>
            <div class="result info">
                ℹ️ <?php echo $status['message']; ?>
            </div>
            
            <div class="details">
                <strong>File Status:</strong><br>
                avatar_perempuan.png: <strong><?php echo file_exists($avatar_perempuan) ? '✓ ADA' : '✗ TIDAK ADA'; ?></strong><br>
                avatar_laki_laki.png: <strong><?php echo file_exists($avatar_laki) ? '✓ ADA' : '✗ TIDAK ADA'; ?></strong><br>
                avatar.png: <strong><?php echo file_exists($avatar_default) ? '✓ ADA' : '✗ TIDAK ADA'; ?></strong>
            </div>
        <?php endif; ?>
    </div>
</body>
</html>
