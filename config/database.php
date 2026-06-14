<?php
// Database Configuration
$db_host = 'localhost';
$db_name = 'u518420372_ebo_db';
$db_user = 'u518420372_ebo';
$db_password = 'Arunnig@1234';

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