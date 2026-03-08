<?php
/**
 * FILE COMPARISON SCRIPT
 * Untuk cek apakah avatar_laki_laki.png dan avatar_perempuan.png benar-benar berbeda
 * atau hanya duplicate dengan nama berbeda
 */

$img_path = __DIR__ . '/dist/img/';
$file_laki = $img_path . 'avatar_laki_laki.png';
$file_perempuan = $img_path . 'avatar_perempuan.png';

$analysis = array(
    'laki_exists' => file_exists($file_laki),
    'perempuan_exists' => file_exists($file_perempuan),
    'laki_size' => file_exists($file_laki) ? filesize($file_laki) : 0,
    'perempuan_size' => file_exists($file_perempuan) ? filesize($file_perempuan) : 0,
    'laki_hash' => file_exists($file_laki) ? md5_file($file_laki) : '',
    'perempuan_hash' => file_exists($file_perempuan) ? md5_file($file_perempuan) : '',
);

$is_identical = ($analysis['laki_hash'] === $analysis['perempuan_hash'] && !empty($analysis['laki_hash']));
$is_different = ($analysis['laki_hash'] !== $analysis['perempuan_hash'] && !empty($analysis['laki_hash']) && !empty($analysis['perempuan_hash']));

?>

<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>File Comparison - Avatar</title>
    <style>
        body {
            font-family: 'Segoe UI', Tahoma, sans-serif;
            padding: 20px;
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            min-height: 100vh;
        }
        .container {
            max-width: 800px;
            margin: 0 auto;
            background: white;
            padding: 30px;
            border-radius: 10px;
            box-shadow: 0 10px 40px rgba(0,0,0,0.2);
        }
        h1 { color: #333; border-bottom: 3px solid #667eea; padding-bottom: 10px; }
        
        .comparison {
            display: grid;
            grid-template-columns: 1fr 1fr;
            gap: 20px;
            margin: 20px 0;
        }
        
        .file-card {
            background: #f9f9f9;
            padding: 20px;
            border-radius: 5px;
            border-left: 4px solid #007bff;
        }
        
        .file-card h3 {
            margin-top: 0;
            color: #333;
        }
        
        .file-card.laki { border-left-color: #007bff; }
        .file-card.perempuan { border-left-color: #e91e63; }
        
        .info-item {
            margin: 10px 0;
            padding: 8px;
            background: white;
            border-radius: 3px;
        }
        
        .label { color: #666; font-weight: 500; display: block; font-size: 12px; }
        .value { color: #333; font-weight: bold; word-break: break-all; }
        
        .result {
            padding: 20px;
            border-radius: 5px;
            margin: 20px 0;
            font-size: 16px;
            font-weight: 600;
        }
        
        .identical {
            background: #f8d7da;
            border: 1px solid #f5c6cb;
            color: #721c24;
        }
        
        .different {
            background: #d4edda;
            border: 1px solid #c3e6cb;
            color: #155724;
        }
        
        .preview {
            display: grid;
            grid-template-columns: 1fr 1fr;
            gap: 20px;
            margin: 30px 0;
        }
        
        .preview-item {
            text-align: center;
        }
        
        .preview-item img {
            max-width: 100%;
            max-height: 300px;
            border: 2px solid #ddd;
            border-radius: 5px;
        }
        
        .preview-item h4 {
            margin-top: 10px;
            color: #333;
        }
        
        .action-box {
            background: #fff3cd;
            border: 1px solid #ffc107;
            padding: 20px;
            border-radius: 5px;
            margin: 20px 0;
        }
        
        .action-box h3 {
            color: #856404;
            margin-top: 0;
        }
        
        .action-box p {
            color: #856404;
            line-height: 1.6;
        }
        
        a.btn {
            display: inline-block;
            padding: 10px 20px;
            background: #667eea;
            color: white;
            text-decoration: none;
            border-radius: 5px;
            margin: 10px 5px 10px 0;
        }
        
        a.btn:hover {
            background: #5568d3;
        }
        
        .code {
            background: #f4f4f4;
            padding: 3px 6px;
            border-radius: 3px;
            font-family: monospace;
            font-size: 12px;
        }
    </style>
</head>
<body>
    <div class="container">
        <h1>🔍 Avatar File Comparison</h1>
        
        <div class="comparison">
            <div class="file-card laki">
                <h3>👨 avatar_laki_laki.png</h3>
                <div class="info-item">
                    <span class="label">Status</span>
                    <span class="value"><?php echo $analysis['laki_exists'] ? '✓ EXISTS' : '✗ NOT FOUND'; ?></span>
                </div>
                <div class="info-item">
                    <span class="label">File Size</span>
                    <span class="value"><?php echo $analysis['laki_size'] > 0 ? number_format($analysis['laki_size']) . ' bytes' : 'N/A'; ?></span>
                </div>
                <div class="info-item">
                    <span class="label">MD5 Hash</span>
                    <span class="value"><code><?php echo substr($analysis['laki_hash'], 0, 16); ?>...</code></span>
                </div>
            </div>
            
            <div class="file-card perempuan">
                <h3>👩 avatar_perempuan.png</h3>
                <div class="info-item">
                    <span class="label">Status</span>
                    <span class="value"><?php echo $analysis['perempuan_exists'] ? '✓ EXISTS' : '✗ NOT FOUND'; ?></span>
                </div>
                <div class="info-item">
                    <span class="label">File Size</span>
                    <span class="value"><?php echo $analysis['perempuan_size'] > 0 ? number_format($analysis['perempuan_size']) . ' bytes' : 'N/A'; ?></span>
                </div>
                <div class="info-item">
                    <span class="label">MD5 Hash</span>
                    <span class="value"><code><?php echo substr($analysis['perempuan_hash'], 0, 16); ?>...</code></span>
                </div>
            </div>
        </div>
        
        <?php if ($is_identical): ?>
            <div class="result identical">
                ⚠️ DUPLIKAT! Kedua file memiliki hash MD5 yang SAMA
                <p style="margin: 10px 0 0 0; font-size: 14px;">
                    Ini berarti file avatar_laki_laki.png adalah exact copy dari avatar_perempuan.png.<br>
                    Avatar lokal laki-laki muncul sama dengan perempuan maka WAJIB diganti!
                </p>
            </div>
            
            <div class="action-box">
                <h3>✅ Solusi: Update avatar_laki_laki.png</h3>
                <p>File avatar_laki_laki.png perlu diganti dengan gambar laki-laki yang BENAR-BENAR BERBEDA:</p>
                
                <ol>
                    <li><strong>Download avatar laki-laki dari:</strong>
                        <p><a href="https://www.dicebear.com/" target="_blank" class="btn">→ dicebear.com</a></p>
                        <ul>
                            <li>Pilih style: <code>Avataaars</code></li>
                            <li>Filter: <code>Male</code> ⭐ PENTING</li>
                            <li>Click Explore untuk vary different male avatars</li>
                            <li>Download PNG format</li>
                        </ul>
                    </li>
                    
                    <li><strong>Siapkan file:</strong>
                        <ul>
                            <li>Rename ke: <code>avatar_laki_laki.png</code></li>
                            <li>Format HARUS: PNG</li>
                            <li>Ukuran: Square preferred (150x150 atau lebih)</li>
                        </ul>
                    </li>
                    
                    <li><strong>Upload file:</strong>
                        <ul>
                            <li>Folder: <code>c:\laragon\www\perpuspelita\dist\img\</code></li>
                            <li>Delete/replace file lama <code>avatar_laki_laki.png</code></li>
                            <li>Upload/paste file baru</li>
                        </ul>
                    </li>
                    
                    <li><strong>Verify:</strong>
                        <ul>
                            <li>Refresh halaman ini</li>
                            <li>Harusnya hash berbeda sekarang</li>
                            <li>Clear browser cache (Ctrl+Shift+Delete)</li>
                            <li>Test login dengan akun laki-laki</li>
                        </ul>
                    </li>
                </ol>
            </div>
            
        <?php elseif ($is_different): ?>
            <div class="result different">
                ✅ Files BERBEDA! Hash MD5 tidak sama.
                <p style="margin: 10px 0 0 0; font-size: 14px;">
                    File avatar_laki_laki.png dan avatar_perempuan.png adalah gambar yang berbeda.<br>
                    Jika avatar di dashboard masih muncul sama, ini adalah BROWSER CACHE issue!
                </p>
            </div>
            
            <div class="action-box">
                <h3>✅ Solusi: Clear Browser Cache</h3>
                <p>File sudah benar, tapi browser menyimpan cache gambar lama. Ikuti langkah:</p>
                
                <ol>
                    <li><strong>Clear Cache Penuh:</strong>
                        <ul>
                            <li>Tekan: <code>Ctrl + Shift + Delete</code></li>
                            <li>Time range: <strong>All time</strong></li>
                            <li>Beri cek pada: <strong>Images and files</strong></li>
                            <li>Click: <strong>Clear data</strong></li>
                        </ul>
                    </li>
                    
                    <li><strong>Hard Refresh:</strong>
                        <ul>
                            <li>Tekan: <code>Ctrl + F5</code> (3-5 kali)</li>
                            <li>Atau: <code>Ctrl + Shift + R</code></li>
                        </ul>
                    </li>
                    
                    <li><strong>Close & Reopen Browser:</strong>
                        <ul>
                            <li>Close browser sepenuhnya</li>
                            <li>Buka ulang browser</li>
                            <li>Login kembali</li>
                        </ul>
                    </li>
                    
                    <li><strong>Use Incognito/Private Window:</strong>
                        <ul>
                            <li>Buka Private/Incognito window (Ctrl+Shift+N)</li>
                            <li>Login dengan akun laki-laki</li>
                            <li>Cek avatar - seharusnya berbeda sekarang</li>
                        </ul>
                    </li>
                </ol>
            </div>
            
        <?php else: ?>
            <div class="result identical">
                ⚠️ Tidak dapat membandingkan - salah satu file tidak ada atau kosong
            </div>
        <?php endif; ?>
        
        <div style="margin: 30px 0;">
            <h2>📸 Visual Preview</h2>
            <div class="preview">
                <div class="preview-item">
                    <h4>👩 Perempuan</h4>
                    <?php if ($analysis['perempuan_exists']): ?>
                        <img src="dist/img/avatar_perempuan.png?t=<?php echo time(); ?>" alt="Avatar Perempuan">
                    <?php else: ?>
                        <p style="color: red;">File tidak ditemukan</p>
                    <?php endif; ?>
                </div>
                
                <div class="preview-item">
                    <h4>👨 Laki-laki</h4>
                    <?php if ($analysis['laki_exists']): ?>
                        <img src="dist/img/avatar_laki_laki.png?t=<?php echo time(); ?>" alt="Avatar Laki-laki">
                    <?php else: ?>
                        <p style="color: red;">File tidak ditemukan</p>
                    <?php endif; ?>
                </div>
            </div>
        </div>
        
        <div style="text-align: center; margin-top: 30px;">
            <a href="trace-avatar.php?id=A004" class="btn">← Kembali ke Trace A004</a>
            <a href="index.php" class="btn">→ Ke Dashboard</a>
        </div>
    </div>
</body>
</html>
