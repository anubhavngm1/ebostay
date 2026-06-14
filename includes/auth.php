<?php
if (session_status() == PHP_SESSION_NONE) {
    session_start();
}

function isLoggedIn() {
    return isset($_SESSION['user_id']);
}

function isAdmin() {
    return isset($_SESSION['user_type']) && $_SESSION['user_type'] == 'admin';
}

function logout() {
    session_destroy();
    header('Location: index.php');
    exit();
}

function redirectToLogin() {
    if (!isLoggedIn()) {
        header('Location: login.php');
        exit();
    }
}
?>