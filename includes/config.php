<?php
// WARNING: Do NOT commit this file to public repositories. Contains sensitive credentials.
/**
 * Configuration File
 * 
 * This file contains all the configuration settings for the Vendora marketplace.
 * It defines constants and settings for:
 * - Base configuration (URLs, site name)
 * - Database connection
 * - Security settings
 * - File upload settings
 * - Error reporting
 * - Session configuration
 * - Security headers
 */

// Prevent direct access to this file
if (!defined('SECURE_ACCESS')) {
    die('Direct access not permitted');
}

// Base configuration
define('BASE_URL', 'http://localhost:8081/');  // Base URL of the application
define('SITE_NAME', 'Vendora');                   // Name of the marketplace
define('ADMIN_EMAIL', 'admin@vendora.com');       // Admin contact email

// Database configuration
define('DB_HOST', 'vendora-db');              // Database host (Docker service name)
define('DB_USER', 'vendora');                 // Database username
define('DB_PASS', 'root');                    // Database password
define('DB_NAME', 'vendora');                 // Database name

// Security settings
define('PASSWORD_MIN_LENGTH', 8);                 // Minimum password length
define('PASSWORD_MAX_LENGTH', 72);                // Maximum password length (bcrypt limit)
define('USERNAME_MIN_LENGTH', 3);                 // Minimum username length
define('USERNAME_MAX_LENGTH', 50);                // Maximum username length
define('MAX_LOGIN_ATTEMPTS', 10);                  // Maximum failed login attempts before timeout
define('LOGIN_TIMEOUT', 300);                     // Login timeout in seconds (5 minutes)
define('SESSION_LIFETIME', 3600);                 // Session lifetime in seconds (1 hour)
define('CSRF_TOKEN_LIFETIME', 7200);              // CSRF token lifetime in seconds (2 hours)

// File upload settings
define('MAX_FILE_SIZE', 5242880);                 // Maximum file size in bytes (5MB)
define('ALLOWED_IMAGE_TYPES', [                   // Allowed image MIME types
    'image/jpeg',
    'image/png',
    'image/gif'
]);
define('UPLOAD_DIR', 'uploads/');                 // Base directory for uploads (relative path)

// Error reporting configuration
error_reporting(E_ALL);                           // Report all errors
ini_set('display_errors', 0);                     // Don't display errors to users
ini_set('log_errors', 1);                         // Enable error logging
ini_set('error_log', 'logs/error.log');           // Error log file location (relative path)

// Session security configuration
ini_set('session.cookie_httponly', 1);            // Prevent JavaScript access to session cookie
ini_set('session.cookie_secure', 0);              // Allow cookies over HTTP in development
ini_set('session.cookie_samesite', 'Strict');     // Prevent CSRF attacks
ini_set('session.gc_maxlifetime', SESSION_LIFETIME); // Session garbage collection
ini_set('session.use_strict_mode', 1);            // Use strict session mode

// Security headers
header('X-Frame-Options: DENY');                  // Prevent clickjacking
header('X-XSS-Protection: 1; mode=block');        // Enable XSS protection
header('X-Content-Type-Options: nosniff');        // Prevent MIME type sniffing
header('Referrer-Policy: strict-origin-when-cross-origin'); // Control referrer information
header('Content-Security-Policy: default-src \'self\'; script-src \'self\' \'unsafe-inline\' https://cdn.tailwindcss.com; style-src \'self\' \'unsafe-inline\'; img-src \'self\' data: https:; font-src \'self\';'); // Control resource loading
?>
