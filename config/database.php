<?php
// Database Configuration
$db_host = 'localhost';
$db_name = 'ebostay_db';
$db_user = 'root';
$db_password = '';

try {
    $conn = new mysqli($db_host, $db_user, $db_password, $db_name);
    
    if ($conn->connect_error) {
        die('Connection failed: ' . $conn->connect_error);
    }
    
    $conn->set_charset('utf8');
} catch (Exception $e) {
    echo 'Database connection error: ' . $e->getMessage();
}
?>