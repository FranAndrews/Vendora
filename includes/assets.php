<?php
/**
 * Asset Management
 * 
 * Centralized asset handling for CSS, JavaScript, and images.
 * Provides clean, maintainable asset paths and versioning.
 */

// Prevent direct access to this file
if (!defined('SECURE_ACCESS')) {
    die('Direct access not permitted');
}

// Asset configuration
define('ASSETS_VERSION', '1.0.0'); // Change this to bust cache when needed
define('ASSETS_PATH', BASE_URL . 'assets/');
define('UPLOADS_PATH', BASE_URL . 'uploads/');

/**
 * Get CSS file path with versioning
 * 
 * @param string $filename CSS filename
 * @return string Full CSS URL
 */
function css($filename) {
    return ASSETS_PATH . 'css/' . $filename . '?v=' . ASSETS_VERSION;
}

/**
 * Get JavaScript file path with versioning
 * 
 * @param string $filename JS filename
 * @return string Full JS URL
 */
function js($filename) {
    return ASSETS_PATH . 'js/' . $filename . '?v=' . ASSETS_VERSION;
}

/**
 * Get image file path
 * 
 * @param string $filename Image filename
 * @return string Full image URL
 */
function img($filename) {
    return ASSETS_PATH . 'images/' . $filename;
}

/**
 * Get upload file path
 * 
 * @param string $filename Upload filename
 * @param string $type Type of upload ('products' or 'profiles')
 * @return string Full upload URL
 */
function upload($filename, $type = 'products') {
    return UPLOADS_PATH . $type . '/' . $filename;
}

/**
 * Include CSS file in HTML head
 * 
 * @param string $filename CSS filename
 */
function includeCSS($filename) {
    echo '<link rel="stylesheet" href="' . css($filename) . '">' . "\n";
}

/**
 * Include JavaScript file
 * 
 * @param string $filename JS filename
 * @param bool $defer Whether to add defer attribute
 */
function includeJS($filename, $defer = false) {
    $deferAttr = $defer ? ' defer' : '';
    echo '<script src="' . js($filename) . '"' . $deferAttr . '></script>' . "\n";
}

/**
 * Get product image URL with fallback
 * 
 * @param string|null $imagePath Product image path
 * @return string Image URL or default image
 */
function productImage($imagePath = null) {
    if ($imagePath && file_exists('uploads/products/' . basename($imagePath))) {
        return upload(basename($imagePath), 'products');
    }
    return img('default-product.jpg'); // Fallback image
}

/**
 * Get profile image URL with fallback
 * 
 * @param string|null $imagePath Profile image path
 * @return string Image URL or default image
 */
function profileImage($imagePath = null) {
    if ($imagePath && file_exists('uploads/profiles/' . basename($imagePath))) {
        return upload(basename($imagePath), 'profiles');
    }
    return img('default-profile.jpg'); // Fallback image
}

/**
 * Include common CSS files
 */
function includeCommonCSS() {
    includeCSS('tailwind.css');
    includeCSS('custom.css');
}

/**
 * Include common JavaScript files
 */
function includeCommonJS() {
    includeJS('main.js');
    includeJS('cart.js');
}

/**
 * Include admin-specific assets
 */
function includeAdminAssets() {
    includeCSS('admin.css');
    includeJS('admin.js', true);
}

/**
 * Include seller-specific assets
 */
function includeSellerAssets() {
    includeCSS('seller.css');
    includeJS('seller.js', true);
}

/**
 * Include buyer-specific assets
 */
function includeBuyerAssets() {
    includeCSS('buyer.css');
    includeJS('buyer.js', true);
}
?> 