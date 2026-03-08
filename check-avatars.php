<?php
/**
 * Script untuk compare avatar files dan show info
 * Akses: http://localhost/perpuspelita/check-avatars.php
 */

$img_path = __DIR__ . '/dist/img/';

$files = array(
    'avatar.png',
    'avatar_perempuan.png',
    'avatar_laki_laki.png'
);

// Get file info
$file_data = array();
foreach ($files as $file) {
    $path = $img_path . $file;
    if (file_exists($path)) {
        $file_data[$file] = array(
            'exists' => true,
            'size' => filesize($path),
            'modified' => date('Y-m-d H:i:s', filemtime($path)),
            'hash' => md5_file($path),
            'type' => mime_content_type($path) ?: 'unknown'
        );
    } else {
        $file_data[$file] = array(
            'exists' => false,
            'size' => 0,
            'modified' => 'N/A',
            'hash' => 'N/A',
            'type' => 'N/A'
        );
    }
}
?>

<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>Avatar Files Check</title>
    <style>
        body { font-family: Arial, sans-serif; padding: 20px; background: #f5f5f5; }
        .container { max-width: 900px; margin: 0 auto; background: white; padding: 20px; border-radius: 5px; }
        table { width: 100%; border-collapse: collapse; margin: 20px 0; }
        th, td { padding: 12px; text-align: left; border-bottom: 1px solid #ddd; }
        th { background: #007bff; color: white; }
        tr:hover { background: #f9f9f9; }
        .exists { color: green; font-weight: bold; }
        .missing { color: red; font-weight: bold; }
        .info { background: #e7f3ff; padding: 15px; border-radius: 5px; margin: 15px 0; border-left: 4px solid #007bff; }
        .warning { background: #fff3cd; padding: 15px; border-radius: 5px; margin: 15px 0; border-left: 4px solid #ffc107; }
        code { background: #f4f4f4; padding: 3px 6px; border-radius: 3px; }
        .preview { display: flex; gap: 20px; flex-wrap: wrap; margin: 20px 0; }
        .preview-item { text-align: center; }
        .preview img { max-width: 200px; max-height: 200px; border: 2px solid #ddd; border-radius: 5px; }
    </style>
</head>
<body>
    <div class="container">
        <h1>🖼️ Avatar Files Information</h1>
        
        <table>
            <thead>
                <tr>
                    <th>Filename</th>
                    <th>Status</th>
                    <th>Size</th>
                    <th>Modified</th>
                    <th>Type</th>
                    <th>Hash (MD5)</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($file_data as $file => $data): ?>
                    <tr>
                        <td><strong><?php echo $file; ?></strong></td>
                        <td class="<?php echo $data['exists'] ? 'exists' : 'missing'; ?>">
                            <?php echo $data['exists'] ? '✓ EXISTS' : '✗ NOT FOUND'; ?>
                        </td>
                        <td><?php echo $data['exists'] ? number_format($data['size']) . ' bytes' : 'N/A'; ?></td>
                        <td><?php echo $data['modified']; ?></td>
                        <td><?php echo $data['type']; ?></td>
                        <td><code><?php echo substr($data['hash'], 0, 16); ?>...</code></td>
                    </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
        
        <div class="preview">
            <div class="preview-item">
                <h3>Perempuan</h3>
                <?php if ($file_data['avatar_perempuan.png']['exists']): ?>
                    <img src="dist/img/avatar_perempuan.png?t=<?php echo time(); ?>" alt="Avatar Perempuan">
                <?php else: ?>
                    <p style="color: red;">File tidak ditemukan</p>
                <?php endif; ?>
            </div>
            
            <div class="preview-item">
                <h3>Laki-laki</h3>
                <?php if ($file_data['avatar_laki_laki.png']['exists']): ?>
                    <img src="dist/img/avatar_laki_laki.png?t=<?php echo time(); ?>" alt="Avatar Laki-laki">
                <?php else: ?>
                    <p style="color: red;">File tidak ditemukan</p>
                <?php endif; ?>
            </div>
        </div>
        
        <div class="info">
            <strong>ℹ️ File Comparison</strong><br><br>
            <?php 
            if ($file_data['avatar_perempuan.png']['exists'] && $file_data['avatar_laki_laki.png']['exists']) {
                $p_size = $file_data['avatar_perempuan.png']['size'];
                $l_size = $file_data['avatar_laki_laki.png']['size'];
                $p_hash = $file_data['avatar_perempuan.png']['hash'];
                $l_hash = $file_data['avatar_laki_laki.png']['hash'];
                
                if ($p_hash === $l_hash) {
                    echo "⚠️ <strong>DUPLIKAT TERDETEKSI!</strong><br>";
                    echo "Avatar perempuan dan laki-laki memiliki hash MD5 yang sama, berarti file identical (isinya sama).<br>";
                    echo "File laki-laki perlu diganti dengan gambar laki-laki yang berbeda.";
                } else {
                    echo "✅ <strong>Files berbeda</strong> (Hash berbeda)<br>";
                    echo "Avatar perempuan dan laki-laki adalah file yang berbeda.";
                }
                
                echo "<br><br>";
                echo "Size Perempuan: <strong>" . number_format($p_size) . " bytes</strong><br>";
                echo "Size Laki-laki: <strong>" . number_format($l_size) . " bytes</strong>";
            }
            ?>
        </div>
        
        <?php 
        if ($file_data['avatar_perempuan.png']['exists'] && $file_data['avatar_laki_laki.png']['exists']) {
            if ($file_data['avatar_perempuan.png']['hash'] === $file_data['avatar_laki_laki.png']['hash']) {
                echo '<div class="warning">';
                echo '<strong>⚠️ ACTION REQUIRED</strong><br><br>';
                echo 'Avatar laki-laki saat ini adalah duplikat dari avatar perempuan.<br>';
                echo 'Anda perlu mengganti file <code>avatar_laki_laki.png</code> dengan gambar laki-laki yang berbeda.<br><br>';
                echo '<strong>Cara mengganti:</strong><br>';
                echo '1. Cari/download gambar avatar laki-laki (format PNG)<br>';
                echo '2. Rename menjadi: <code>avatar_laki_laki.png</code><br>';
                echo '3. Upload/copy ke folder: <code>dist/img/</code><br>';
                echo '4. Refresh halaman ini untuk verify<br><br>';
                echo '<strong>Sumber gambar gratis:</strong><br>';
                echo '• Dicebear: https://www.dicebear.com (pilih style Avataaars, select "male")<br>';
                echo '• Freepik: https://www.freepik.com (search "boy avatar")<br>';
                echo '• Gravatar atau avatar generator lainnya';
                echo '</div>';
            }
        }
        ?>
        
        <a href="debug-avatar.php" style="display: inline-block; margin-top: 20px; padding: 10px 20px; background: #007bff; color: white; text-decoration: none; border-radius: 5px;">→ Go to Avatar Debug</a>
    </div>
</body>
</html>
