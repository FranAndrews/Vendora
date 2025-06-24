<?php
/**
 * Security Helper Functions
 * 
 * This file contains essential security functions for the Vendora marketplace.
 * It provides functionality for:
 * - CSRF protection
 * - Input sanitization
 * - Password validation
 * - Username validation
 * - Session security
 * - Rate limiting
 * - File upload security
 * - XSS protection
 * - Error handling
 */

// Prevent direct access to this file
if (!defined('SECURE_ACCESS')) {
    die('Direct access not permitted');
}

/**
 * CSRF Protection Functions
 */

/**
 * Generate a new CSRF token
 * 
 * Creates a new CSRF token if one doesn't exist or if the current one has expired.
 * The token is stored in the session and has a limited lifetime.
 * 
 * @return string The generated CSRF token
 */
function generateCSRFToken() {
    if (empty($_SESSION['csrf_token'])) {
        $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
        $_SESSION['csrf_token_time'] = time();
    }
    return $_SESSION['csrf_token'];
}

/**
 * Validate a CSRF token
 * 
 * Checks if the provided token matches the stored token and hasn't expired.
 * 
 * @param string $token The token to validate
 * @return bool True if token is valid, false otherwise
 */
function validateCSRFToken($token) {
    if (!isset($_SESSION['csrf_token']) || !isset($_SESSION['csrf_token_time'])) {
        return false;
    }
    
    if (time() - $_SESSION['csrf_token_time'] > CSRF_TOKEN_LIFETIME) {
        unset($_SESSION['csrf_token']);
        unset($_SESSION['csrf_token_time']);
        return false;
    }
    
    return hash_equals($_SESSION['csrf_token'], $token);
}

/**
 * Input Sanitization Functions
 */

/**
 * Sanitize input data
 * 
 * Recursively sanitizes input data to prevent XSS attacks.
 * Handles both single values and arrays.
 * 
 * @param mixed $data The data to sanitize
 * @return mixed The sanitized data
 */
function sanitizeInput($data) {
    if (is_array($data)) {
        return array_map('sanitizeInput', $data);
    }
    return htmlspecialchars(trim($data), ENT_QUOTES, 'UTF-8');
}

/**
 * Password Validation Functions
 */

/**
 * Validate a password
 * 
 * Checks if a password meets the security requirements:
 * - Minimum length
 * - Maximum length
 * - Contains uppercase letter
 * - Contains lowercase letter
 * - Contains number
 * - Contains special character
 * 
 * @param string $password The password to validate
 * @return bool True if password is valid, false otherwise
 */
function validatePassword($password) {
    if (strlen($password) < PASSWORD_MIN_LENGTH || strlen($password) > PASSWORD_MAX_LENGTH) {
        return false;
    }
    
    // Require at least one uppercase letter, one lowercase letter, one number, and one special character
    if (!preg_match('/^(?=.*[a-z])(?=.*[A-Z])(?=.*\d)(?=.*[@$!%*?&])[A-Za-z\d@$!%*?&]{8,}$/', $password)) {
        return false;
    }
    
    return true;
}

/**
 * Username Validation Functions
 */

/**
 * Validate a username
 * 
 * Checks if a username meets the requirements:
 * - Minimum length
 * - Maximum length
 * - Contains only allowed characters (letters, numbers, underscores)
 * 
 * @param string $username The username to validate
 * @return bool True if username is valid, false otherwise
 */
function validateUsername($username) {
    if (strlen($username) < USERNAME_MIN_LENGTH || strlen($username) > USERNAME_MAX_LENGTH) {
        return false;
    }
    
    // Allow letters, numbers, and underscores only
    if (!preg_match('/^[a-zA-Z0-9_]+$/', $username)) {
        return false;
    }
    
    return true;
}

/**
 * Session Security Functions
 */

/**
 * Initialize a secure session
 * 
 * Starts a session with secure settings and regenerates the session ID periodically
 * to prevent session fixation attacks.
 */
function secureSession() {
    if (session_status() === PHP_SESSION_NONE) {
        session_start();
    }
    
    // Regenerate session ID periodically
    if (!isset($_SESSION['last_regeneration']) || 
        time() - $_SESSION['last_regeneration'] > 300) {
        session_regenerate_id(true);
        $_SESSION['last_regeneration'] = time();
    }
}

/**
 * Rate Limiting Functions
 */

/**
 * Check rate limit for an action
 * 
 * Implements rate limiting to prevent abuse of features like login attempts.
 * 
 * @param string $key The key to rate limit (e.g., 'login_IP')
 * @param int $maxAttempts Maximum number of attempts allowed
 * @param int $timeout Timeout period in seconds
 * @return bool True if action is allowed, false if rate limited
 */
function checkRateLimit($key, $maxAttempts = MAX_LOGIN_ATTEMPTS, $timeout = LOGIN_TIMEOUT) {
    if (!isset($_SESSION['rate_limit'][$key])) {
        $_SESSION['rate_limit'][$key] = [
            'attempts' => 0,
            'timeout' => 0
        ];
    }
    
    $rateLimit = &$_SESSION['rate_limit'][$key];
    
    if ($rateLimit['timeout'] > time()) {
        return false;
    }
    
    if ($rateLimit['attempts'] >= $maxAttempts) {
        $rateLimit['timeout'] = time() + $timeout;
        return false;
    }
    
    $rateLimit['attempts']++;
    return true;
}

/**
 * File Upload Security Functions
 */

/**
 * Validate a file upload
 * 
 * Checks if an uploaded file meets security requirements:
 * - Valid file upload
 * - Within size limits
 * - Allowed file type
 * 
 * @param array $file The $_FILES array element
 * @param array $allowedTypes Array of allowed MIME types
 * @param int $maxSize Maximum file size in bytes
 * @return bool True if file is valid, false otherwise
 */
function validateFileUpload($file, $allowedTypes = ALLOWED_IMAGE_TYPES, $maxSize = MAX_FILE_SIZE) {
    if (!isset($file['error']) || is_array($file['error'])) {
        return false;
    }
    
    if ($file['error'] !== UPLOAD_ERR_OK) {
        return false;
    }
    
    if ($file['size'] > $maxSize) {
        return false;
    }
    
    $finfo = new finfo(FILEINFO_MIME_TYPE);
    $mimeType = $finfo->file($file['tmp_name']);
    
    if (!in_array($mimeType, $allowedTypes)) {
        return false;
    }
    
    return true;
}

/**
 * XSS Protection Functions
 */

/**
 * Clean data to prevent XSS attacks
 * 
 * Recursively cleans data to prevent XSS attacks.
 * Handles both single values and arrays.
 * 
 * @param mixed $data The data to clean
 * @return mixed The cleaned data
 */
function xssClean($data) {
    if (is_array($data)) {
        return array_map('xssClean', $data);
    }
    return htmlspecialchars($data, ENT_QUOTES, 'UTF-8');
}

/**
 * Error Handling Functions
 */

/**
 * Custom error handler
 * 
 * Handles PHP errors and exceptions:
 * - Logs errors to file
 * - Prevents sensitive information from being displayed
 * - Provides user-friendly error messages
 * 
 * @param int $errno Error number
 * @param string $errstr Error message
 * @param string $errfile File where error occurred
 * @param int $errline Line number where error occurred
 * @return bool True to prevent default error handler
 */
function handleError($errno, $errstr, $errfile, $errline) {
    $error = [
        'type' => $errno,
        'message' => $errstr,
        'file' => $errfile,
        'line' => $errline,
        'time' => date('Y-m-d H:i:s')
    ];
    
    error_log(json_encode($error));
    
    if (ini_get('display_errors')) {
        echo "An error occurred. Please try again later.";
    }
    
    return true;
}

// Set the custom error handler
set_error_handler('handleError'); 