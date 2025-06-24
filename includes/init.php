<?php
/**
 * Application Initialization
 * 
 * Centralized initialization file that loads all necessary components.
 * This should be included at the top of every page.
 */

// Define secure access constant
define('SECURE_ACCESS', true);

// Start output buffering for better performance
ob_start();

// Load configuration
require_once 'config.php';

// Load database connection
require_once 'db.php';

// Load security functions
require_once 'security.php';

// Load session management
require_once 'session.php';

// Load asset management
require_once 'assets.php';

/**
 * Initialize the application
 * 
 * Sets up error handling, security headers, and basic functionality
 */
function initApp() {
    // Set error handler
    set_error_handler('handleError');
    
    // Set exception handler
    set_exception_handler(function($e) {
        error_log("Uncaught Exception: " . $e->getMessage());
        if (ini_get('display_errors')) {
            echo "An error occurred. Please try again later.";
        } else {
            echo "An error occurred. Please try again later.";
        }
    });
    
    // Set shutdown function to handle fatal errors
    register_shutdown_function(function() {
        $error = error_get_last();
        if ($error && in_array($error['type'], [E_ERROR, E_PARSE, E_CORE_ERROR, E_COMPILE_ERROR])) {
            error_log("Fatal Error: " . $error['message']);
            if (ini_get('display_errors')) {
                echo "A fatal error occurred. Please try again later.";
            } else {
                echo "A fatal error occurred. Please try again later.";
            }
        }
    });
}

/**
 * Clean up and end the application
 * 
 * Flushes output buffer and performs cleanup
 */
function endApp() {
    // Flush output buffer
    ob_end_flush();
}

// Initialize the application
initApp();
?> 