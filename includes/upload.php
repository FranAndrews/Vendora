<?php
/**
 * File Upload Handler
 * 
 * This class handles secure file uploads for the Vendora marketplace.
 * It provides validation, security checks, and proper file management.
 * 
 * Features:
 * - Secure file type validation
 * - File size limits
 * - Unique filename generation
 * - Automatic old file cleanup
 * - Error handling and logging
 */

// Prevent direct access to this file
if (!defined('SECURE_ACCESS')) {
    die('Direct access not permitted');
}

class FileUpload {
    // Allowed file types (defined in config.php)
    private $allowedTypes;
    
    // Maximum file size in bytes (defined in config.php)
    private $maxSize;
    
    // Base upload directory (defined in config.php)
    private $uploadDir;
    
    // Array to store any errors that occur during upload
    private $errors = [];

    /**
     * Constructor
     * 
     * @param string $type The type of upload ('product' or 'profile')
     *                     This determines which subdirectory to use
     */
    public function __construct($type = 'product') {
        $this->allowedTypes = ALLOWED_IMAGE_TYPES;
        $this->maxSize = MAX_FILE_SIZE;
        $this->uploadDir = UPLOAD_DIR . ($type === 'product' ? 'products/' : 'profiles/');
    }

    /**
     * Upload a file
     * 
     * @param array $file The $_FILES array element for the uploaded file
     * @param string|null $oldFile Path to the old file to delete (if replacing)
     * @return string|false Returns the file path on success, false on failure
     */
    public function upload($file, $oldFile = null) {
        try {
            // First validate the file
            if (!$this->validateFile($file)) {
                return false;
            }

            // Generate a unique filename to prevent collisions
            // Format: unique_id_timestamp.extension
            $extension = pathinfo($file['name'], PATHINFO_EXTENSION);
            $filename = uniqid() . '_' . time() . '.' . $extension;
            $filepath = $this->uploadDir . $filename;

            // Move the uploaded file to its final location
            if (!move_uploaded_file($file['tmp_name'], $filepath)) {
                $this->errors[] = 'Failed to move uploaded file.';
                return false;
            }

            // If we're replacing an old file, delete it
            if ($oldFile && file_exists($oldFile)) {
                unlink($oldFile);
            }

            return $filepath;

        } catch (Exception $e) {
            // Log the error and return false
            error_log("File upload error: " . $e->getMessage());
            $this->errors[] = 'An error occurred during file upload.';
            return false;
        }
    }

    /**
     * Validate the uploaded file
     * 
     * @param array $file The $_FILES array element
     * @return bool True if file is valid, false otherwise
     */
    private function validateFile($file) {
        // Check if file was actually uploaded
        if (!isset($file['error']) || is_array($file['error'])) {
            $this->errors[] = 'Invalid file upload.';
            return false;
        }

        // Check for PHP upload errors
        switch ($file['error']) {
            case UPLOAD_ERR_OK:
                break;
            case UPLOAD_ERR_INI_SIZE:
            case UPLOAD_ERR_FORM_SIZE:
                $this->errors[] = 'File is too large.';
                return false;
            case UPLOAD_ERR_PARTIAL:
                $this->errors[] = 'File was only partially uploaded.';
                return false;
            case UPLOAD_ERR_NO_FILE:
                $this->errors[] = 'No file was uploaded.';
                return false;
            default:
                $this->errors[] = 'Unknown upload error.';
                return false;
        }

        // Check if file size is within limits
        if ($file['size'] > $this->maxSize) {
            $this->errors[] = 'File is too large. Maximum size is ' . ($this->maxSize / 1024 / 1024) . 'MB.';
            return false;
        }

        // Check if file type is allowed
        $finfo = new finfo(FILEINFO_MIME_TYPE);
        $mimeType = $finfo->file($file['tmp_name']);
        
        if (!in_array($mimeType, $this->allowedTypes)) {
            $this->errors[] = 'Invalid file type. Allowed types: ' . implode(', ', $this->allowedTypes);
            return false;
        }

        return true;
    }

    /**
     * Get all errors that occurred during upload
     * 
     * @return array Array of error messages
     */
    public function getErrors() {
        return $this->errors;
    }

    /**
     * Get the last error that occurred
     * 
     * @return string|null The last error message or null if no errors
     */
    public function getLastError() {
        return end($this->errors);
    }
} 