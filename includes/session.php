<?php
/**
 * Session Management
 * 
 * Centralized session handling for the Vendora marketplace.
 * Provides secure session initialization and management.
 */

// Prevent direct access to this file
if (!defined('SECURE_ACCESS')) {
    die('Direct access not permitted');
}

/**
 * Initialize a secure session
 * 
 * Starts a session with secure settings and regenerates the session ID periodically
 * to prevent session fixation attacks.
 */
function initSecureSession() {
    if (session_status() === PHP_SESSION_NONE) {
        // Set secure session parameters
        ini_set('session.cookie_httponly', 1);
        ini_set('session.cookie_secure', 0); // Set to 1 in production with HTTPS
        ini_set('session.cookie_samesite', 'Strict');
        ini_set('session.use_strict_mode', 1);
        ini_set('session.gc_maxlifetime', SESSION_LIFETIME);
        
        session_start();
        
        // Regenerate session ID periodically to prevent session fixation
        if (!isset($_SESSION['last_regeneration']) || 
            time() - $_SESSION['last_regeneration'] > 300) {
            session_regenerate_id(true);
            $_SESSION['last_regeneration'] = time();
        }
        
        return true;
    }
    return false;
}

/**
 * Check if user is logged in
 * 
 * @return bool True if user is logged in, false otherwise
 */
function isLoggedIn() {
    return isset($_SESSION['user_id']) && !empty($_SESSION['user_id']);
}

/**
 * Get current user ID
 * 
 * @return int|null User ID if logged in, null otherwise
 */
function getCurrentUserId() {
    return $_SESSION['user_id'] ?? null;
}

/**
 * Get current user role
 * 
 * @return string|null User role if logged in, null otherwise
 */
function getCurrentUserRole() {
    return $_SESSION['user_role'] ?? null;
}

/**
 * Check if user has specific role
 * 
 * @param string $role Role to check for
 * @return bool True if user has the role, false otherwise
 */
function hasRole($role) {
    return getCurrentUserRole() === $role;
}

/**
 * Require authentication
 * 
 * Redirects to login page if user is not authenticated
 */
function requireAuth() {
    if (!isLoggedIn()) {
        header('Location: ' . BASE_URL . 'login.php');
        exit();
    }
}

/**
 * Require specific role
 * 
 * Redirects to appropriate page if user doesn't have required role
 * 
 * @param string $role Required role
 * @param string $redirectUrl URL to redirect to if access denied
 */
function requireRole($role, $redirectUrl = null) {
    requireAuth();
    
    if (!hasRole($role)) {
        if ($redirectUrl) {
            header('Location: ' . $redirectUrl);
        } else {
            header('Location: ' . BASE_URL . 'index.php');
        }
        exit();
    }
}

/**
 * Destroy session and logout user
 */
function logout() {
    // Clear all session data
    $_SESSION = array();
    
    // Destroy the session cookie
    if (ini_get("session.use_cookies")) {
        $params = session_get_cookie_params();
        setcookie(session_name(), '', time() - 42000,
            $params["path"], $params["domain"],
            $params["secure"], $params["httponly"]
        );
    }
    
    // Destroy the session
    session_destroy();
}

// Initialize session when this file is included
initSecureSession();
?> 