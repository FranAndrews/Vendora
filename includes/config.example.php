<?php
/**
 * Example Configuration File
 *
 * Copy this file to config.php and fill in your own settings.
 */

// Prevent direct access to this file
if (!defined('SECURE_ACCESS')) {
    die('Direct access not permitted');
}

// Base configuration
define('BASE_URL', 'http://localhost:8081/');  // Base URL of the application
define('SITE_NAME', 'Vendora');                   // Name of the marketplace
define('ADMIN_EMAIL', 'admin@example.com');       // Admin contact email (change this)

// Database configuration
define('DB_HOST', 'your-db-host');              // Database host
define('DB_USER', 'your-db-user');              // Database username
define('DB_PASS', 'your-db-password');          // Database password
define('DB_NAME', 'your-db-name');              // Database name

// Security settings
define('PASSWORD_MIN_LENGTH', 8);
define('PASSWORD_MAX_LENGTH', 72);
define('USERNAME_MIN_LENGTH', 3);
define('USERNAME_MAX_LENGTH', 50);
define('MAX_LOGIN_ATTEMPTS', 10);
define('LOGIN_TIMEOUT', 300);
define('SESSION_LIFETIME', 3600);
define('CSRF_TOKEN_LIFETIME', 7200);

// File upload settings
define('MAX_FILE_SIZE', 5242880);
define('ALLOWED_IMAGE_TYPES', [
    'image/jpeg',
    'image/png',
    'image/gif'
]);
define('UPLOAD_DIR', 'uploads/');

// Error reporting configuration
error_reporting(E_ALL);
ini_set('display_errors', 0);
ini_set('log_errors', 1);
ini_set('error_log', 'logs/error.log');

// Session security configuration
ini_set('session.cookie_httponly', 1);
ini_set('session.cookie_secure', 0);
ini_set('session.cookie_samesite', 'Strict');
ini_set('session.gc_maxlifetime', SESSION_LIFETIME);
ini_set('session.use_strict_mode', 1);

// Security headers
header('X-Frame-Options: DENY');
header('X-XSS-Protection: 1; mode=block');
header('X-Content-Type-Options: nosniff');
header('Referrer-Policy: strict-origin-when-cross-origin');
header('Content-Security-Policy: default-src \'self\'; script-src \'self\' \'unsafe-inline\' https://cdn.tailwindcss.com; style-src \'self\' \'unsafe-inline\'; img-src \'self\' data: https:; font-src \'self\';');
?> 