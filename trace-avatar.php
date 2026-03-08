<?php
/**
 * TRACE COMPLETE FLOW
 * Script untuk trace lengkap dari database, function, sampai file check
 * Akses: http://localhost/perpuspelita/trace-avatar.php?id=SISWA001
 */

include "inc/koneksi.php";

$user_id = isset($_GET['id']) ? $_GET['id'] : '';
$debug_info = array();

if (!empty($user_id)) {
    // 1. Query database
    $sql = "SELECT id_anggota, nama, jekel FROM tb_anggota WHERE id_anggota = '$user_id'";
    $result = $koneksi->query($sql);
    
    if ($result && $result->num_rows > 0) {
        $row = $result->fetch_assoc();
        $id_anggota = $row['id_anggota'];
        $nama = $row['nama'];
        $jekel_raw = $row['jekel'];
        
        // 2. Test variables
        $jekel_trimmed = trim($jekel_raw);
        $jekel_lower = strtolower($jekel_trimmed);
        $jekel_hex = bin2hex($jekel_raw);
        
        // 3. Test conditions
        $is_laki = (
            $jekel_lower == 'laki-laki' || 
            $jekel_lower == 'laki laki' || 
            $jekel_lower == 'laki' ||
            $jekel_lower == 'l' || 
            $jekel_lower == 'm' ||
            strpos($jekel_lower, 'laki') === 0
        );
        
        $is_perempuan = (
            $jekel_lower == 'perempuan' || 
            $jekel_lower == 'wanita' ||
            $jekel_lower == 'p' || 
            $jekel_lower == 'f' ||
            strpos($jekel_lower, 'perem') === 0
        );
        
        // 4. Get avatar path
        $avatar_path = getAvatarByGender($jekel_raw);
        $avatar_exists = file_exists($avatar_path);
        $avatar_fallback = getAvatarWithFallback($jekel_raw);
        
        // 5. Check both files
        $file_laki = file_exists('dist/img/avatar_laki_laki.png');
        $file_perempuan = file_exists('dist/img/avatar_perempuan.png');
        $file_default = file_exists('dist/img/avatar.png');
        
        // Store debug info
        $debug_info = array(
            'user_found' => true,
            'id' => $id_anggota,
            'nama' => $nama,
            'jekel_raw' => $jekel_raw,
            'jekel_trimmed' => $jekel_trimmed,
            'jekel_lower' => $jekel_lower,
            'jekel_hex' => $jekel_hex,
            'jekel_length' => strlen($jekel_raw),
            'is_laki' => $is_laki,
            'is_perempuan' => $is_perempuan,
            'avatar_selected' => $avatar_path,
            'avatar_exists' => $avatar_exists,
            'avatar_fallback' => $avatar_fallback,
            'file_laki' => $file_laki,
            'file_perempuan' => $file_perempuan,
            'file_default' => $file_default
        );
    } else {
        $debug_info = array('user_found' => false, 'message' => 'User not found');
    }
}

?>

<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>Avatar Trace Tool</title>
    <style>
        body { 
            font-family: 'Courier New', monospace; 
            padding: 20px; 
            background: #1e1e1e; 
            color: #d4d4d4;
        }
        .container { 
            max-width: 900px; 
            margin: 0 auto; 
            background: #252526; 
            padding: 20px; 
            border-radius: 5px;
            border: 1px solid #3e3e42;
        }
        h1 { color: #4ec9b0; }
        h2 { color: #569cd6; margin-top: 20px; }
        .search-box {
            margin: 20px 0;
            padding: 15px;
            background: #1e1e1e;
            border: 1px solid #3e3e42;
            border-radius: 5px;
        }
        input[type="text"] {
            padding: 8px;
            background: #3c3c3c;
            color: #d4d4d4;
            border: 1px solid #555;
            border-radius: 3px;
            font-family: monospace;
            width: 300px;
        }
        button {
            padding: 8px 15px;
            background: #007acc;
            color: white;
            border: none;
            border-radius: 3px;
            cursor: pointer;
            font-weight: bold;
        }
        button:hover { background: #005a9e; }
        
        .section {
            background: #1e1e1e;
            border-left: 3px solid #007acc;
            padding: 15px;
            margin: 15px 0;
            border-radius: 3px;
        }
        
        .section-title {
            color: #4ec9b0;
            font-size: 14px;
            font-weight: bold;
            margin-bottom: 10px;
            text-transform: uppercase;
        }
        
        .item {
            margin: 8px 0;
            display: flex;
            justify-content: space-between;
            padding: 8px;
            background: #252526;
            border-radius: 3px;
        }
        
        .label { color: #9cdcfe; min-width: 150px; }
        .value { color: #ce9178; font-weight: bold; }
        .true { color: #6a9955; }
        .false { color: #f48771; }
        .path { color: #dcdcaa; }
        
        .flow {
            background: #1e1e1e;
            border: 1px solid #007acc;
            padding: 15px;
            margin: 15px 0;
            border-radius: 5px;
        }
        
        .flow-step {
            padding: 10px;
            margin: 8px 0;
            background: #252526;
            border-left: 2px solid #007acc;
            border-radius: 3px;
        }
        
        .flow-ok { border-left-color: #6a9955; }
        .flow-problem { border-left-color: #f48771; }
        
        .summary {
            background: #1e1e1e;
            padding: 15px;
            margin: 20px 0;
            border-radius: 5px;
            border: 2px solid #007acc;
        }
        
        .problem-list {
            background: #3d2626;
            border: 1px solid #f48771;
            padding: 15px;
            margin: 15px 0;
            border-radius: 5px;
            color: #f48771;
        }
        
        pre {
            background: #1e1e1e;
            padding: 10px;
            border-radius: 3px;
            overflow-x: auto;
            border: 1px solid #3e3e42;
        }
    </style>
</head>
<body>
    <div class="container">
        <h1>🔬 Avatar Trace Tool</h1>
        
        <div class="search-box">
            <form method="GET">
                <label style="color: #9cdcfe;">User ID:</label><br><br>
                <input type="text" name="id" placeholder="Contoh: SISWA001, A001, dll" value="<?php echo htmlspecialchars($user_id); ?>">
                <button type="submit">🔍 Trace</button>
            </form>
        </div>
        
        <?php if (empty($user_id)): ?>
            <div class="section">
                <div class="section-title">ℹ️ Cara Menggunakan</div>
                <pre>1. Masukkan ID anggota di search box
2. Klik "Trace" untuk lihat debug info lengkap
3. Contoh: SISWA001, A001, P001, dll

Atau langsung buka:
http://localhost/perpuspelita/trace-avatar.php?id=SISWA001
                </pre>
            </div>
        <?php elseif (!$debug_info['user_found']): ?>
            <div class="problem-list">
                ❌ User dengan ID "<strong><?php echo htmlspecialchars($user_id); ?></strong>" tidak ditemukan
            </div>
        <?php else: ?>
            
            <div class="section">
                <div class="section-title">📊 Database Data</div>
                <div class="item">
                    <span class="label">ID Anggota:</span>
                    <span class="value"><?php echo htmlspecialchars($debug_info['id']); ?></span>
                </div>
                <div class="item">
                    <span class="label">Nama:</span>
                    <span class="value"><?php echo htmlspecialchars($debug_info['nama']); ?></span>
                </div>
                <div class="item">
                    <span class="label">Jekel (Raw):</span>
                    <span class="value"><?php echo htmlspecialchars($debug_info['jekel_raw']); ?></span>
                </div>
            </div>
            
            <div class="section">
                <div class="section-title">🔍 Data Analysis</div>
                <div class="item">
                    <span class="label">Jekel (Trimmed):</span>
                    <span class="value"><?php echo htmlspecialchars($debug_info['jekel_trimmed']); ?></span>
                </div>
                <div class="item">
                    <span class="label">Jekel (Lowercase):</span>
                    <span class="value"><?php echo htmlspecialchars($debug_info['jekel_lower']); ?></span>
                </div>
                <div class="item">
                    <span class="label">Jekel (Hex):</span>
                    <span class="value"><?php echo $debug_info['jekel_hex']; ?></span>
                </div>
                <div class="item">
                    <span class="label">String Length:</span>
                    <span class="value"><?php echo $debug_info['jekel_length']; ?> chars</span>
                </div>
            </div>
            
            <div class="section">
                <div class="section-title">✓ Logic Decision</div>
                <div class="item">
                    <span class="label">Is Laki-laki?</span>
                    <span class="value <?php echo $debug_info['is_laki'] ? 'true' : 'false'; ?>">
                        <?php echo $debug_info['is_laki'] ? 'TRUE ✓' : 'FALSE ✗'; ?>
                    </span>
                </div>
                <div class="item">
                    <span class="label">Is Perempuan?</span>
                    <span class="value <?php echo $debug_info['is_perempuan'] ? 'true' : 'false'; ?>">
                        <?php echo $debug_info['is_perempuan'] ? 'TRUE ✓' : 'FALSE ✗'; ?>
                    </span>
                </div>
            </div>
            
            <div class="section">
                <div class="section-title">📁 File Status</div>
                <div class="item">
                    <span class="label">avatar_perempuan.png:</span>
                    <span class="value <?php echo $debug_info['file_perempuan'] ? 'true' : 'false'; ?>">
                        <?php echo $debug_info['file_perempuan'] ? '✓ EXISTS' : '✗ NOT FOUND'; ?>
                    </span>
                </div>
                <div class="item">
                    <span class="label">avatar_laki_laki.png:</span>
                    <span class="value <?php echo $debug_info['file_laki'] ? 'true' : 'false'; ?>">
                        <?php echo $debug_info['file_laki'] ? '✓ EXISTS' : '✗ NOT FOUND'; ?>
                    </span>
                </div>
                <div class="item">
                    <span class="label">avatar.png (fallback):</span>
                    <span class="value <?php echo $debug_info['file_default'] ? 'true' : 'false'; ?>">
                        <?php echo $debug_info['file_default'] ? '✓ EXISTS' : '✗ NOT FOUND'; ?>
                    </span>
                </div>
            </div>
            
            <div class="section">
                <div class="section-title">🎯 Avatar Selection</div>
                <div class="item">
                    <span class="label">Selected Path:</span>
                    <span class="path"><?php echo htmlspecialchars($debug_info['avatar_selected']); ?></span>
                </div>
                <div class="item">
                    <span class="label">File Exists?</span>
                    <span class="value <?php echo $debug_info['avatar_exists'] ? 'true' : 'false'; ?>">
                        <?php echo $debug_info['avatar_exists'] ? '✓ YES' : '✗ NO'; ?>
                    </span>
                </div>
                <div class="item">
                    <span class="label">Fallback Used?</span>
                    <span class="value <?php echo $debug_info['avatar_fallback'] != $debug_info['avatar_selected'] ? 'true' : 'false'; ?>">
                        <?php echo $debug_info['avatar_fallback'] != $debug_info['avatar_selected'] ? '✓ YES' : '✗ NO'; ?>
                    </span>
                </div>
                <div class="item">
                    <span class="label">Final Avatar:</span>
                    <span class="path"><?php echo htmlspecialchars($debug_info['avatar_fallback']); ?></span>
                </div>
            </div>
            
            <div class="flow">
                <div class="section-title">⚡ Decision Flow</div>
                
                <div class="flow-step <?php echo $debug_info['is_laki'] || $debug_info['is_perempuan'] ? 'flow-ok' : 'flow-problem'; ?>">
                    ① Jekel value recognized? 
                    <span style="float: right;">
                        <?php echo ($debug_info['is_laki'] || $debug_info['is_perempuan']) ? '✓ YES' : '✗ NO'; ?>
                    </span>
                </div>
                
                <?php if ($debug_info['is_laki']): ?>
                    <div class="flow-step flow-ok">
                        ② Decision: avatar_laki_laki.png ✓
                    </div>
                    <div class="flow-step <?php echo $debug_info['file_laki'] ? 'flow-ok' : 'flow-problem'; ?>">
                        ③ File exists? 
                        <span style="float: right;">
                            <?php echo $debug_info['file_laki'] ? '✓ YES' : '✗ NO - FALLBACK'; ?>
                        </span>
                    </div>
                <?php elseif ($debug_info['is_perempuan']): ?>
                    <div class="flow-step flow-ok">
                        ② Decision: avatar_perempuan.png ✓
                    </div>
                    <div class="flow-step <?php echo $debug_info['file_perempuan'] ? 'flow-ok' : 'flow-problem'; ?>">
                        ③ File exists? 
                        <span style="float: right;">
                            <?php echo $debug_info['file_perempuan'] ? '✓ YES' : '✗ NO - FALLBACK'; ?>
                        </span>
                    </div>
                <?php else: ?>
                    <div class="flow-step flow-problem">
                        ② NO MATCH - Defaulting to avatar_perempuan.png
                    </div>
                    <div class="flow-step <?php echo $debug_info['file_perempuan'] ? 'flow-ok' : 'flow-problem'; ?>">
                        ③ File exists? 
                        <span style="float: right;">
                            <?php echo $debug_info['file_perempuan'] ? '✓ YES' : '✗ NO - FALLBACK'; ?>
                        </span>
                    </div>
                <?php endif; ?>
                
                <div class="flow-step flow-ok">
                    ④ Final Result: <span class="path"><?php echo htmlspecialchars($debug_info['avatar_fallback']); ?></span>
                </div>
            </div>
            
            <?php
            // Identify problems
            $problems = array();
            
            if (!($debug_info['is_laki'] || $debug_info['is_perempuan'])) {
                $problems[] = "Jekel value tidak dikenali: <code>" . htmlspecialchars($debug_info['jekel_raw']) . "</code>";
            }
            
            if ($debug_info['is_laki'] && !$debug_info['file_laki']) {
                $problems[] = "File <code>avatar_laki_laki.png</code> tidak ditemukan!";
            }
            
            if ($debug_info['is_perempuan'] && !$debug_info['file_perempuan']) {
                $problems[] = "File <code>avatar_perempuan.png</code> tidak ditemukan!";
            }
            
            if (count($problems) > 0):
            ?>
                <div class="problem-list">
                    <strong>❌ PROBLEMS DETECTED:</strong><br><br>
                    <ol>
                        <?php foreach ($problems as $problem): ?>
                            <li><?php echo $problem; ?></li>
                        <?php endforeach; ?>
                    </ol>
                </div>
            <?php else: ?>
                <div class="summary" style="border-color: #6a9955;">
                    <strong style="color: #6a9955;">✅ AVATAR SYSTEM WORKING CORRECTLY FOR THIS USER</strong><br><br>
                    Jekel dikenali, file ada, dan avatar akan ditampilkan dengan benar.
                </div>
            <?php endif; ?>
            
        <?php endif; ?>
        
    </div>
</body>
</html>
