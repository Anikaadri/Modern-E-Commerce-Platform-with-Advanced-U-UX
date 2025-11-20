<?php
/**
 * Utility Helper Functions
 */

/**
 * Format currency
 */
function formatCurrency($value) {
    return '$' . number_format($value, 2);
}

/**
 * Format date
 */
function formatDate($date, $format = 'Y-m-d') {
    return date($format, strtotime($date));
}

/**
 * Truncate string
 */
function truncateString($string, $length = 100) {
    if (strlen($string) > $length) {
        return substr($string, 0, $length) . '...';
    }
    return $string;
}

/**
 * Get file extension
 */
function getFileExtension($filename) {
    return pathinfo($filename, PATHINFO_EXTENSION);
}

/**
 * Sanitize filename
 */
function sanitizeFilename($filename) {
    return preg_replace('/[^A-Za-z0-9\-_\.]/', '', $filename);
}

/**
 * Get file size formatted
 */
function getFormattedFileSize($bytes) {
    $sizes = ['B', 'KB', 'MB', 'GB'];
    if ($bytes == 0) return '0 B';
    $i = intval(floor(log($bytes, 1024)));
    return round($bytes / pow(1024, $i), 2) . ' ' . $sizes[$i];
}

/**
 * Check if request method is POST
 */
function isPost() {
    return $_SERVER['REQUEST_METHOD'] === 'POST';
}

/**
 * Check if request method is GET
 */
function isGet() {
    return $_SERVER['REQUEST_METHOD'] === 'GET';
}

/**
 * Redirect to URL
 */
function redirect($url) {
    header('Location: ' . $url);
    exit;
}

/**
 * Get POST value
 */
function post($key, $default = null) {
    return isset($_POST[$key]) ? $_POST[$key] : $default;
}

/**
 * Get GET value
 */
function get($key, $default = null) {
    return isset($_GET[$key]) ? $_GET[$key] : $default;
}

/**
 * Generate slug from string
 */
function slugify($string) {
    $string = strtolower(trim($string));
    $string = preg_replace('/[^\w\s-]/', '', $string);
    $string = preg_replace('/[\s_]+/', '-', $string);
    $string = preg_replace('/^-+|-+$/', '', $string);
    return $string;
}

/**
 * Get current user
 */
function getCurrentUser() {
    return isset($_SESSION['user_id']) ? $_SESSION['user_id'] : null;
}

/**
 * Check if user is logged in
 */
function isUserLoggedIn() {
    return isset($_SESSION['user_id']);
}

/**
 * Get current page URL
 */
function getCurrentPageURL() {
    $protocol = isset($_SERVER['HTTPS']) && $_SERVER['HTTPS'] === 'on' ? 'https' : 'http';
    $host = $_SERVER['HTTP_HOST'];
    $requestURI = $_SERVER['REQUEST_URI'];
    return $protocol . '://' . $host . $requestURI;
}

/**
 * Calculate discount
 */
function calculateDiscount($originalPrice, $discountPercentage) {
    return $originalPrice * ($discountPercentage / 100);
}

/**
 * Calculate final price after discount
 */
function calculateFinalPrice($originalPrice, $discountPercentage) {
    return $originalPrice - calculateDiscount($originalPrice, $discountPercentage);
}
