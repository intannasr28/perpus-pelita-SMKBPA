<?php
header('Content-Type: application/json');

try {
    include "../../inc/koneksi.php";
    
    if ($koneksi->connect_error) {
        throw new Exception("Connection error: " . $koneksi->connect_error);
    }
    
    // Test query
    $test = $koneksi->query("SELECT 1");
    
    if (!$test) {
        throw new Exception("Query error: " . $koneksi->error);
    }
    
    $response = [
        'connected' => true,
        'host' => 'localhost',
        'database' => 'data_perpus'
    ];
    
} catch (Exception $e) {
    $response = [
        'connected' => false,
        'error' => $e->getMessage()
    ];
}

echo json_encode($response);
?>
