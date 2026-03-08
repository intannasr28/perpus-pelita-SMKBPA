<?php
// Debug script untuk cek avatar & level
session_start();

// Redirect jika tidak login
if (empty($_SESSION["ses_username"])) {
    header("location: login.php");
    exit;
}

include "inc/koneksi.php";

// Get current user data
$user_id = $_SESSION["ses_id"];
$user_nama = $_SESSION["ses_nama"];
$user_level = $_SESSION["ses_level"];

// Get jekel from database
$sql = $koneksi->query("SELECT jekel FROM tb_anggota WHERE id_anggota='$user_id'");
$data = $sql->fetch_assoc();
$jekel = $data['jekel'] ?? 'NULL';

// Check avatar function
$avatar = getAvatarWithFallback($jekel);
$avatar_laki = 'dist/img/avatar_laki_laki.png';
$avatar_perempuan = 'dist/img/avatar_perempuan.png';

// File exists check
$laki_exists = file_exists($avatar_laki);
$perempuan_exists = file_exists($avatar_perempuan);

// Get file sizes to compare
$laki_size = file_exists($avatar_laki) ? filesize($avatar_laki) : 0;
$perempuan_size = file_exists($avatar_perempuan) ? filesize($avatar_perempuan) : 0;
?>

<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>Avatar & Level Debug</title>
    <style>
        body { font-family: Arial, sans-serif; padding: 20px; background: #f5f5f5; }
        .container { max-width: 800px; margin: 0 auto; background: white; padding: 20px; border-radius: 5px; }
        h1 { color: #333; }
        .debug-item { margin: 15px 0; padding: 10px; background: #f9f9f9; border-left: 4px solid #007bff; }
        .label { font-weight: bold; color: #333; min-width: 200px; display: inline-block; }
        .value { color: #666; }
        .success { color: green; }
        .error { color: red; }
        .warning { color: orange; }
        .avatar-preview { 
            width: 150px; 
            height: 150px; 
            border: 2px solid #ddd; 
            margin: 10px 0;
            border-radius: 50%;
        }
        .code { 
            background: #f4f4f4; 
            padding: 10px; 
            border-radius: 3px; 
            font-family: monospace; 
            overflow-x: auto;
        }
    </style>
</head>
<body>
    <div class="container">
        <h1>🔍 Debug: Avatar & Level System</h1>
        
        <div class="debug-item">
            <span class="label">User ID:</span>
            <span class="value"><?php echo $user_id; ?></span>
        </div>
        
        <div class="debug-item">
            <span class="label">Username:</span>
            <span class="value"><?php echo $_SESSION["ses_username"]; ?></span>
        </div>
        
        <div class="debug-item">
            <span class="label">Nama:</span>
            <span class="value"><?php echo $user_nama; ?></span>
        </div>
        
        <div class="debug-item">
            <span class="label">Level (Session):</span>
            <span class="value"><?php echo $user_level; ?></span>
        </div>
        
        <hr style="margin: 20px 0;">
        
        <h2>Avatar Debug</h2>
        
        <div class="debug-item">
            <span class="label">Jekel (Raw):</span>
            <span class="value"><code><?php echo htmlspecialchars($jekel); ?></code></span>
        </div>
        
        <div class="debug-item">
            <span class="label">Jekel Length:</span>
            <span class="value"><?php echo strlen($jekel); ?> characters</span>
        </div>
        
        <div class="debug-item">
            <span class="label">Jekel Hex:</span>
            <span class="value code"><?php echo bin2hex($jekel); ?></span>
        </div>
        
        <div class="debug-item">
            <span class="label">Jekel Lowercase:</span>
            <span class="value"><code><?php echo htmlspecialchars(strtolower($jekel)); ?></code></span>
        </div>
        
        <hr style="margin: 20px 0;">
        
        <h2>File Status</h2>
        
        <div class="debug-item">
            <span class="label">avatar_perempuan.png:</span>
            <span class="value <?php echo $perempuan_exists ? 'success' : 'error'; ?>">
                <?php echo $perempuan_exists ? '✓ EXISTS' : '✗ NOT FOUND'; ?> (<?php echo $perempuan_size; ?> bytes)
            </span>
        </div>
        
        <div class="debug-item">
            <span class="label">avatar_laki_laki.png:</span>
            <span class="value <?php echo $laki_exists ? 'success' : 'error'; ?>">
                <?php echo $laki_exists ? '✓ EXISTS' : '✗ NOT FOUND'; ?> (<?php echo $laki_size; ?> bytes)
            </span>
        </div>
        
        <?php if ($laki_exists && $perempuan_exists) { ?>
            <div class="debug-item">
                <span class="label">Files Same Size:</span>
                <span class="value <?php echo $laki_size == $perempuan_size ? 'warning' : 'success'; ?>">
                    <?php 
                    if ($laki_size == $perempuan_size) {
                        echo "⚠️ YES - Files mungkin identical (duplikat)";
                    } else {
                        echo "✓ NO - Files berbeda";
                    }
                    ?>
                </span>
            </div>
        <?php } ?>
        
        <hr style="margin: 20px 0;">
        
        <h2>Avatar Decision</h2>
        
        <div class="debug-item">
            <span class="label">Selected Avatar:</span>
            <span class="value"><code><?php echo htmlspecialchars($avatar); ?></code></span>
        </div>
        
        <div class="debug-item">
            <span class="label">Avatar Preview:</span>
            <div style="margin-top: 10px;">
                <img src="<?php echo $avatar; ?>" class="avatar-preview" alt="Avatar">
            </div>
        </div>
        
        <hr style="margin: 20px 0;">
        
        <h2>Logic Check</h2>
        
        <div class="code">
<?php
echo "Condition Check:\n\n";
echo "strtolower(\$jekel) == 'laki-laki': " . (strtolower($jekel) == 'laki-laki' ? 'TRUE' : 'FALSE') . "\n";
echo "\$jekel == 'L': " . ($jekel == 'L' ? 'TRUE' : 'FALSE') . "\n";
echo "strtolower(\$jekel) == 'perempuan': " . (strtolower($jekel) == 'perempuan' ? 'TRUE' : 'FALSE') . "\n";
echo "\$jekel == 'P': " . ($jekel == 'P' ? 'TRUE' : 'FALSE') . "\n";

// Check for common variations
echo "\n--- Checking variations ---\n";
echo "Contains 'laki': " . (stripos($jekel, 'laki') !== false ? 'TRUE' : 'FALSE') . "\n";
echo "Contains 'perempuan': " . (stripos($jekel, 'perempuan') !== false ? 'TRUE' : 'FALSE') . "\n";
echo "Equals 'Laki-laki': " . ($jekel === 'Laki-laki' ? 'TRUE' : 'FALSE') . "\n";
echo "Equals 'Perempuan': " . ($jekel === 'Perempuan' ? 'TRUE' : 'FALSE') . "\n";
?>
        </div>
        
        <hr style="margin: 20px 0;">
        
        <h2>💡 Possible Issues</h2>
        
        <ul>
            <?php 
            $issues = array();
            
            if ($jekel == '') {
                $issues[] = "<li>❌ Jekel data kosong/NULL di database</li>";
            }
            
            if ($laki_exists && $perempuan_exists && $laki_size == $perempuan_size) {
                $issues[] = "<li>⚠️ avatar_laki_laki.png adalah duplikat dari avatar_perempuan.png (file size sama)</li>";
            }
            
            if (!$laki_exists) {
                $issues[] = "<li>❌ File avatar_laki_laki.png tidak ditemukan</li>";
            }
            
            if (!$perempuan_exists) {
                $issues[] = "<li>❌ File avatar_perempuan.png tidak ditemukan</li>";
            }
            
            if ($jekel != '' && strtolower($jekel) != 'laki-laki' && strtolower($jekel) != 'perempuan') {
                $issues[] = "<li>⚠️ Jekel value tidak standard: <code>$jekel</code></li>";
            }
            
            if (count($issues) == 0) {
                echo "<li style='color: green;'>✅ Tidak ada issue - semua terlihat normal</li>";
            } else {
                foreach ($issues as $issue) {
                    echo $issue;
                }
            }
            ?>
        </ul>
        
        <hr style="margin: 20px 0;">
        
        <div style="background: #e3f2fd; padding: 15px; border-radius: 5px;">
            <h3>Fix Solutions</h3>
            <ol>
                <li><strong>Jika jekel kosong:</strong> Edit profile siswa dan set jenis kelamin</li>
                <li><strong>Jika file duplikat:</strong> Ganti avatar_laki_laki.png dengan gambar laki-laki yang berbeda</li>
                <li><strong>Jika format jekel aneh:</strong> Update data di database atau modify kondisi di koneksi.php</li>
                <li><strong>Refresh browser:</strong> Bersihkan cache (Ctrl+Shift+Delete) lalu Ctrl+F5</li>
            </ol>
        </div>
        
        <hr style="margin: 20px 0;">
        
        <a href="index.php" style="display: inline-block; padding: 10px 20px; background: #007bff; color: white; text-decoration: none; border-radius: 5px;">← Kembali ke Dashboard</a>
    </div>
</body>
</html>
