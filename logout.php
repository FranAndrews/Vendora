<?php
define('SECURE_ACCESS', true);
require_once 'includes/config.php';
require_once 'includes/security.php';

// Initialize session
secureSession();

// Clear all session data
$_SESSION = array();

// Destroy the session cookie
if (isset($_COOKIE[session_name()])) {
    setcookie(session_name(), '', time() - 3600, '/');
}

// Destroy the session
session_destroy();

// Redirect to login page
header('Location: ' . BASE_URL . 'login.php');
exit();
?>
