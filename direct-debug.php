<?php
/**
 * DIRECT DATABASE DEBUG
 * Script untuk langsung cek data jekel di database tanpa session
 * Jalankan: http://localhost/perpuspelita/direct-debug.php
 */

include "inc/koneksi.php";

// Get all users dengan gender different
$sql = "SELECT id_anggota, nama, jekel FROM tb_anggota ORDER BY nama ASC";
$result = $koneksi->query($sql);

?>

<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>Direct Database Debug</title>
    <style>
        body { font-family: Arial, sans-serif; padding: 20px; background: #f5f5f5; }
        .container { max-width: 1000px; margin: 0 auto; background: white; padding: 30px; border-radius: 5px; box-shadow: 0 2px 10px rgba(0,0,0,0.1); }
        h1 { color: #333; }
        table { width: 100%; border-collapse: collapse; margin: 20px 0; }
        th, td { padding: 12px; text-align: left; border-bottom: 1px solid #ddd; }
        th { background: #007bff; color: white; font-weight: bold; }
        tr:hover { background: #f9f9f9; }
        .laki { background: #e3f2fd; }
        .perempuan { background: #fce4ec; }
        .avatar-path { font-family: monospace; background: #f4f4f4; padding: 5px; border-radius: 3px; }
        .check-icon { color: green; font-weight: bold; }
        .xmark-icon { color: red; font-weight: bold; }
        .info-box { background: #e7f3ff; border: 1px solid #b3d9ff; padding: 15px; border-radius: 5px; margin: 20px 0; }
    </style>
</head>
<body>
    <div class="container">
        <h1>🔍 Direct Database Debug - All Users</h1>
        
        <div class="info-box">
            Database Host: <code><?php echo getenv('DB_HOST') ?: 'localhost'; ?></code><br>
            Database: <code><?php echo getenv('DB_NAME') ?: 'data_perpus'; ?></code><br>
            User: <code><?php echo getenv('DB_USER') ?: 'root'; ?></code>
        </div>
        
        <table>
            <thead>
                <tr>
                    <th>ID Anggota</th>
                    <th>Nama</th>
                    <th>Jekel (Raw)</th>
                    <th>Jekel (Hex)</th>
                    <th>Avatar Function</th>
                    <th>File Exists?</th>
                    <th>Row Color</th>
                </tr>
            </thead>
            <tbody>
                <?php 
                if ($result && $result->num_rows > 0) {
                    while ($row = $result->fetch_assoc()) {
                        $id = $row['id_anggota'];
                        $nama = $row['nama'];
                        $jekel = $row['jekel'];
                        $avatar = getAvatarByGender($jekel);
                        $avatar_exists = file_exists($avatar) ? '✓ YES' : '✗ NO';
                        
                        // Determine row class
                        $row_class = '';
                        if (strtolower(trim($jekel)) == 'laki-laki' || in_array(strtolower($jekel), ['l', 'm', 'laki'])) {
                            $row_class = 'laki';
                        } else {
                            $row_class = 'perempuan';
                        }
                ?>
                    <tr class="<?php echo $row_class; ?>">
                        <td><strong><?php echo htmlspecialchars($id); ?></strong></td>
                        <td><?php echo htmlspecialchars($nama); ?></td>
                        <td><code><?php echo htmlspecialchars($jekel); ?></code></td>
                        <td><code><?php echo bin2hex($jekel); ?></code></td>
                        <td>
                            <span class="avatar-path"><?php echo htmlspecialchars($avatar); ?></span>
                        </td>
                        <td>
                            <span class="<?php echo $avatar_exists == '✓ YES' ? 'check-icon' : 'xmark-icon'; ?>">
                                <?php echo $avatar_exists; ?>
                            </span>
                        </td>
                        <td><?php echo $row_class == 'laki' ? '👨 LAKI-LAKI' : '👩 PEREMPUAN'; ?></td>
                    </tr>
                <?php
                    }
                } else {
                    echo '<tr><td colspan="7" style="text-align: center; color: red;">No users found</td></tr>';
                }
                ?>
            </tbody>
        </table>
        
        <hr>
        
        <h2>📁 Avatar Files Status</h2>
        <table>
            <thead>
                <tr>
                    <th>File</th>
                    <th>Exists</th>
                    <th>Size</th>
                    <th>Modified</th>
                </tr>
            </thead>
            <tbody>
                <?php
                $files = ['avatar.png', 'avatar_perempuan.png', 'avatar_laki_laki.png'];
                foreach ($files as $file) {
                    $path = __DIR__ . '/dist/img/' . $file;
                    if (file_exists($path)) {
                        $size = filesize($path);
                        $modified = date('Y-m-d H:i:s', filemtime($path));
                        $exists = '✓ YES';
                    } else {
                        $size = 'N/A';
                        $modified = 'N/A';
                        $exists = '✗ NO';
                    }
                ?>
                    <tr>
                        <td><code><?php echo $file; ?></code></td>
                        <td class="<?php echo $exists == '✓ YES' ? 'check-icon' : 'xmark-icon'; ?>">
                            <?php echo $exists; ?>
                        </td>
                        <td><?php echo $size !== 'N/A' ? number_format($size) . ' bytes' : 'N/A'; ?></td>
                        <td><?php echo $modified; ?></td>
                    </tr>
                <?php } ?>
            </tbody>
        </table>
        
        <div class="info-box" style="background: #fff3cd; border-color: #ffc107;">
            <strong>⚠️ Action Items:</strong><br><br>
            1. ✓ Cek apakah user "Laki-laki" ada di table di atas<br>
            2. ✓ Lihat nilai jekel (Raw) - apakah persis "Laki-laki"?<br>
            3. ✓ Lihat File Exists column - apakah file avatar_laki_laki.png ada? (harus YES)<br>
            4. ✓ Jika ada yang NO (tidak ada file) - ini adalah masalahnya!<br>
            5. ✓ Jika jekel value aneh (hex shows aneh) - ada masalah format data
        </div>
    </div>
</body>
</html>
