<?php
/**
 * PETUNJUK DEBUG FAVORIT BUKU
 * 
 * Jika fitur favorit tidak berfungsi, ikuti langkah-langkah di bawah:
 */
// Set custom session save path SEBELUM session_start()
$tmp_path = realpath(__DIR__) . DIRECTORY_SEPARATOR . 'tmp';
if (!is_dir($tmp_path)) {
    mkdir($tmp_path, 0777, true);
}
ini_set('session.save_path', $tmp_path);

// Resume session
session_start();
?>

<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>Debug Favorit Buku</title>
    <link rel="stylesheet" href="bootstrap/css/bootstrap.min.css">
    <style>
        body { padding: 20px; font-family: Arial, sans-serif; }
        .test-section { margin: 20px 0; padding: 15px; border: 1px solid #ddd; border-radius: 5px; }
        .test-btn { margin: 5px; }
        .success { color: green; font-weight: bold; }
        .error { color: red; font-weight: bold; }
        code { display: block; background: #f4f4f4; padding: 10px; margin: 5px 0; overflow-x: auto; }
        .result-box { 
            margin-top: 10px; 
            padding: 10px; 
            background: #f9f9f9; 
            border-left: 4px solid #007bff;
            border-radius: 3px;
        }
    </style>
</head>
<body>
    <div class="container">
        <h1>🔧 Debug Favorit Buku Sistem</h1>
        <p><strong>Status:</strong> Session <?php echo !empty($_SESSION['ses_username']) ? '<span class="success">✓ Active</span>' : '<span class="error">✗ Not Active</span>'; ?></p>
        <hr>
        
        <div class="test-section">
            <h3>1️⃣ Cek Session</h3>
            <p>Pastikan sudah login dengan akun siswa</p>
            <button class="btn btn-primary test-btn" onclick="checkSession()">Check Session</button>
            <div id="session-result"></div>
        </div>

        <div class="test-section">
            <h3>2️⃣ Cek Cookies & Session Storage</h3>
            <p>Verifikasi apakah cookies sudah tersimpan di browser</p>
            <button class="btn btn-primary test-btn" onclick="checkCookies()">Check Cookies</button>
            <div id="cookies-result"></div>
        </div>

        <div class="test-section">
            <h3>3️⃣ Cek File Koneksi</h3>
            <button class="btn btn-primary test-btn" onclick="checkConnection()">Check Database Connection</button>
            <div id="connection-result"></div>
        </div>

        <div class="test-section">
            <h3>4️⃣ Test AJAX Request (dengan credentials)</h3>
            <p>Test AJAX ke toggle_favorit.php dengan credentials/cookies</p>
            <button class="btn btn-primary test-btn" onclick="testAjax()">Test AJAX Request</button>
            <div id="ajax-result"></div>
        </div>

        <div class="test-section">
            <h3>5️⃣ Test Favorit Real</h3>
            <p>ID Buku untuk test: <strong>B001</strong></p>
            <button class="btn btn-success test-btn" onclick="testRealFavorit()">Toggle Favorit B001</button>
            <div id="favorit-result"></div>
        </div>

        <div class="test-section bg-warning">
            <h3>📝 Instruksi:</h3>
            <ol>
                <li>Pastikan sudah login sebagai siswa</li>
                <li>Buka Console Browser (F12 → Console Tab)</li>
                <li>Jalankan test satu per satu</li>
                <li>Jika ada error, lihat pesan di sini dan di console</li>
                <li>Screenshot hasil test</li>
            </ol>
        </div>

        <div class="test-section bg-info text-white">
            <h3>🔍 Troubleshooting:</h3>
            <ul>
                <li><strong>Error "Anda harus login"</strong> → Session tidak ter-pass ke AJAX. Refresh halaman dan try again.</li>
                <li><strong>Error "Database"</strong> → Koneksi database bermasalah. Check host/user/password.</li>
                <li><strong>Tombol tidak berubah warna</strong> → JavaScript error. Lihat console browser (F12).</li>
            </ul>
        </div>
    </div>

    <script src="plugins/jQuery/jquery-2.2.3.min.js"></script>
    <script src="bootstrap/js/bootstrap.min.js"></script>
    <script>
    // PENTING: Setup jQuery AJAX untuk mengirim cookies
    $.ajaxSetup({
        xhrFields: {
            withCredentials: true
        }
    });

    function showResult(elementId, success, message, details = '') {
        var resultEl = document.getElementById(elementId);
        var html = '<div class="result-box">';
        
        if (success) {
            html += '<p class="success">✓ Sukses</p>';
            html += '<p>' + message + '</p>';
        } else {
            html += '<p class="error">✗ Error</p>';
            html += '<p>' + message + '</p>';
        }
        
        if (details) {
            html += '<code>' + details + '</code>';
        }
        
        html += '</div>';
        resultEl.innerHTML = html;
    }

    function checkSession() {
        console.log('Checking session...');
        $.ajax({
            url: 'admin/siswa/check_session.php',
            method: 'POST',
            dataType: 'json',
            xhrFields: { withCredentials: true },
            success: function(data) {
                console.log('Session check response:', data);
                var success = data.logged_in === true;
                var message = success ? 
                    'User login: <strong>' + data.username + '</strong>' :
                    'User tidak login / Session expired';
                showResult('session-result', success, message, JSON.stringify(data, null, 2));
            },
            error: function(xhr, status, error) {
                console.error('Session check error:', {status, error, response: xhr.responseText});
                showResult('session-result', false, 'Error checking session', error);
            }
        });
    }

    function checkConnection() {
        console.log('Checking database...');
        $.ajax({
            url: 'admin/siswa/check_db.php',
            method: 'GET',
            dataType: 'json',
            success: function(data) {
                console.log('DB check response:', data);
                var success = data.connected === true;
                var message = success ? 
                    'Database terhubung: <strong>' + data.database + '</strong>' :
                    'Database error: ' + data.error;
                showResult('connection-result', success, message, JSON.stringify(data, null, 2));
            },
            error: function(xhr, status, error) {
                console.error('DB check error:', {status, error});
                showResult('connection-result', false, 'Error checking database', error);
            }
        });
    }

    function checkCookies() {
        console.log('Checking cookies...');
        $.ajax({
            url: 'admin/siswa/check_cookies.php',
            method: 'GET',
            dataType: 'json',
            xhrFields: { withCredentials: true },
            success: function(data) {
                console.log('Cookies check response:', data);
                var success = data.session_status === 2; // PHP_SESSION_ACTIVE = 2
                var message = '<strong>Session Status:</strong> ' + data.php_session_status_text;
                message += '<br><strong>Session ID:</strong> ' + data.session_id;
                message += '<br><strong>Has Cookie:</strong> ' + (data.cookies_received.PHPSESSID ? '✓ Yes' : '✗ No');
                message += '<br><strong>Session Data Count:</strong> ' + data.session_data_count;
                message += '<br><strong>ses_username:</strong> ' + data.ses_username_value;
                showResult('cookies-result', success, message, JSON.stringify(data, null, 2));
            },
            error: function(xhr, status, error) {
                console.error('Cookies check error:', {status, error});
                showResult('cookies-result', false, 'Error checking cookies', error);
            }
        });
    }

    function testAjax() {
        console.log('Testing AJAX...');
        var resultEl = document.getElementById('ajax-result');
        resultEl.innerHTML = '<p>Testing...</p>';
        
        $.ajax({
            url: 'admin/siswa/toggle_favorit.php',
            method: 'POST',
            data: { id_buku: 'TEST' },
            dataType: 'json',
            xhrFields: { withCredentials: true },
            success: function(data) {
                console.log('AJAX Success response:', data);
                var details = JSON.stringify(data, null, 2);
                showResult('ajax-result', true, 'AJAX Request Berhasil!', details);
            },
            error: function(xhr, status, error) {
                console.error('AJAX Error:', {status, error, response: xhr.responseText});
                var details = xhr.responseText ? xhr.responseText.substring(0, 300) : error;
                showResult('ajax-result', false, 'AJAX Error: ' + error, details);
            }
        });
    }

    function testRealFavorit() {
        console.log('Testing real favorit...');
        var resultEl = document.getElementById('favorit-result');
        resultEl.innerHTML = '<p>Processing...</p>';
        
        $.ajax({
            url: 'admin/siswa/toggle_favorit.php',
            method: 'POST',
            data: { id_buku: 'B001' },
            dataType: 'json',
            xhrFields: { withCredentials: true },
            success: function(data) {
                console.log('Favorit Result:', data);
                var details = 'Status: ' + data.status + '\nMessage: ' + data.message;
                var success = data.status == 'added' || data.status == 'removed';
                
                showResult('favorit-result', success, 
                    success ? 'Request berhasil! Status: ' + data.status : 'Error: ' + data.message,
                    details);
                
                if (success) {
                    setTimeout(function() {
                        checkFavoritInDB();
                    }, 500);
                }
            },
            error: function(xhr, status, error) {
                console.error('Favorit Error:', {status, error, response: xhr.responseText});
                showResult('favorit-result', false, 'Error: ' + error, xhr.responseText.substring(0, 300));
            }
        });
    }

    function checkFavoritInDB() {
        console.log('Checking DB for favorit...');
        $.ajax({
            url: 'admin/siswa/check_favorit.php',
            method: 'POST',
            data: { id_buku: 'B001' },
            dataType: 'json',
            xhrFields: { withCredentials: true },
            success: function(data) {
                console.log('DB Check result:', data);
                var resultEl = document.getElementById('favorit-result');
                var status = data.in_favorit ? '<span class="success">✓ Di Favorit</span>' : '<span>Tidak Di Favorit</span>';
                resultEl.innerHTML += '<p style="margin-top: 10px;">Database Status: ' + status + '</p>';
            }
        });
    }

    // Auto check on load
    window.addEventListener('load', function() {
        console.log('Debug page loaded.');
        console.log('jQuery AJAX setup dengan credentials:', $.ajaxSettings.xhrFields);
    });
    </script>
</body>
</html>

