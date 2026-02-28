<?php
// Test sederhana untuk cek apakah file favorit bisa diakses
session_start();
?>
<!DOCTYPE html>
<html>
<head>
    <title>Test Toggle Favorit</title>
    <script src="https://code.jquery.com/jquery-2.2.3.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@9"></script>
</head>
<body>
    <h1>Test Toggle Favorit</h1>
    <p>Session Username: <strong><?php echo $_SESSION['ses_username'] ?? 'NOT SET'; ?></strong></p>
    <button id="test-btn">Test Klik Favorit (B001)</button>

    <script>
    $(document).ready(function() {
        $('#test-btn').click(function() {
            console.log("Button clicked!");
            $.ajax({
                url: 'admin/siswa/toggle_favorit.php',
                method: 'POST',
                data: { id_buku: 'B001' },
                dataType: 'json',
                success: function(response) {
                    console.log("Success response:", response);
                    alert('Response: ' + JSON.stringify(response));
                },
                error: function(xhr, status, error) {
                    console.error("Error:", {status: status, error: error, response: xhr.responseText});
                    alert('Error: ' + error + '\n\nResponse: ' + xhr.responseText);
                }
            });
        });
    });
    </script>
</body>
</html>
