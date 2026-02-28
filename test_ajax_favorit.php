<?php
// PENTING: Set custom session save path SEBELUM session_start() - SAMA dengan login.php!
$tmp_path = realpath(__DIR__) . DIRECTORY_SEPARATOR . 'tmp';
if (!is_dir($tmp_path)) {
	mkdir($tmp_path, 0777, true);
}
ini_set('session.save_path', $tmp_path);

// Ini penting - session_start() akan RESUME session yang ada berdasarkan PHPSESSID cookie dari browser
session_start();

$is_logged_in = !empty($_SESSION["ses_username"]);

// Debug info
$debug_info = [
    'session_id' => session_id(),
    'session_status' => session_status(),
    'session_path' => session_save_path(),
    'session_count' => count($_SESSION),
    'session_name' => session_name(),
    'cookie_phpsessid' => !empty($_COOKIE[session_name()]) ? 'Ada' : 'Tidak Ada',
    'server_cookies' => array_keys($_COOKIE)
];
?>
<!DOCTYPE html>
<html>
<head>
    <title>Test AJAX Toggle Favorit</title>
    <style>
        body { font-family: Arial; margin: 20px; background: #f5f5f5; }
        .test-box { border: 1px solid #ddd; padding: 15px; margin: 20px 0; background: white; }
        .success { color: green; font-weight: bold; }
        .error { color: red; font-weight: bold; }
        .info { color: blue; }
        pre { background: #f9f9f9; padding: 10px; overflow-x: auto; border: 1px solid #ddd; border-radius: 3px; }
        button { padding: 10px 20px; background: #007bff; color: white; border: none; border-radius: 3px; cursor: pointer; }
        button:hover { background: #0056b3; }
    </style>
</head>
<body>
    <h1>Test AJAX Toggle Favorit</h1>
    
    <div class="test-box">
        <h3>Session Status:</h3>
        <p class="<?php echo $is_logged_in ? 'success' : 'error'; ?>">
            <?php 
            if ($is_logged_in) {
                echo "✓ Logged in as: <strong>" . htmlspecialchars($_SESSION["ses_username"]) . "</strong>";
            } else {
                echo "✗ Not logged in - Session is EMPTY";
            }
            ?>
        </p>
        
        <h4>Debug Info:</h4>
        <pre><?php print_r($debug_info); ?></pre>
        
        <h4>Session Data:</h4>
        <pre><?php print_r($_SESSION); ?></pre>
        
        <h4>Browser Cookies:</h4>
        <pre><?php print_r($_COOKIE); ?></pre>
    </div>
    
    <?php if ($is_logged_in): ?>
    <div class="test-box">
        <h3>Test AJAX Call to toggle_favorit.php:</h3>
        <label>Test Book ID: <input type="text" id="testBookId" value="B001" style="width: 100px;"></label>
        <button id="testBtn">Test AJAX Toggle Favorit</button>
        <div id="result"></div>
    </div>
    
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script>
    $(document).ready(function() {
        console.log('Test page loaded. Session ID: ' + '<?php echo session_id(); ?>');
        
        $.ajaxSetup({
            xhrFields: {
                withCredentials: true
            }
        });
        
        $('#testBtn').click(function() {
            var bookId = $('#testBookId').val();
            console.log('Starting AJAX test with book ID: ' + bookId);
            $('#result').html('<p class="info">Loading...</p>');
            
            $.ajax({
                url: 'admin/siswa/toggle_favorit.php',
                method: 'POST',
                data: { 
                    id_buku: bookId
                },
                dataType: 'json',
                timeout: 10000,
                xhrFields: {
                    withCredentials: true
                },
                success: function(response) {
                    console.log('SUCCESS:', response);
                    var html = '<p class="success">✓ Response received!</p>';
                    html += '<pre>' + JSON.stringify(response, null, 2) + '</pre>';
                    $('#result').html(html);
                },
                error: function(xhr, status, error) {
                    console.error('ERROR:', {
                        status: status, 
                        error: error, 
                        xhr_status: xhr.status, 
                        response: xhr.responseText
                    });
                    var html = '<p class="error">✗ AJAX Error!</p><pre>';
                    html += 'HTTP Status: ' + xhr.status + ' ' + xhr.statusText + '\n';
                    html += 'Error: ' + error + '\n';
                    html += 'Status: ' + status + '\n\n';
                    html += 'Response:\n' + xhr.responseText;
                    html += '</pre>';
                    $('#result').html(html);
                }
            });
        });
    });
    </script>
    <?php else: ?>
    <div class="test-box error">
        <p>❌ Session kosong! Ini berarti:</p>
        <ul>
            <li>Anda belum login, atau</li>
            <li>Session sudah expired, atau</li>
            <li>Session PHPSESSID cookie tidak dikirim ke test page</li>
        </ul>
        <p><strong>Solusi:</strong> <a href="login.php">Login dulu</a>, kemudian akses test page ini kembali</p>
    </div>
    <?php endif; ?>
</body>
</html>
