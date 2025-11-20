<?php
/**
 * Database Configuration
 */

// Database credentials
define('DB_HOST', 'localhost');
define('DB_NAME', 'online_shop');
define('DB_USER', 'root');
define('DB_PASSWORD', '');

// Initialize database connection
require_once __DIR__ . '/../helpers/Database.php';
$database = new Database(DB_HOST, DB_NAME, DB_USER, DB_PASSWORD);
