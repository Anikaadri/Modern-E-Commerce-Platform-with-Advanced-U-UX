<?php
/**
 * Application Settings
 */

// Site Information
define('SITE_NAME', 'Online Shop');
define('SITE_EMAIL', 'info@onlineshop.local');
define('ADMIN_EMAIL', 'admin@onlineshop.local');

// Security
define('SESSION_TIMEOUT', 3600); // 1 hour
define('PASSWORD_MIN_LENGTH', 8);

// Pagination
define('ITEMS_PER_PAGE', 12);
define('ORDERS_PER_PAGE', 10);

// File Upload
define('MAX_UPLOAD_SIZE', 5 * 1024 * 1024); // 5MB
define('ALLOWED_EXTENSIONS', ['jpg', 'jpeg', 'png', 'gif', 'pdf']);
define('UPLOAD_DIR', __DIR__ . '/../../public/assets/uploads/');

// Currency
define('CURRENCY', 'USD');
define('CURRENCY_SYMBOL', '$');

// Tax
define('TAX_RATE', 0.1); // 10%

// Shipping
define('FREE_SHIPPING_THRESHOLD', 100);
define('SHIPPING_COST', 10);

// Email
define('SMTP_HOST', getenv('SMTP_HOST') ?? 'localhost');
define('SMTP_PORT', getenv('SMTP_PORT') ?? 587);
define('SMTP_USER', getenv('SMTP_USER') ?? '');
define('SMTP_PASSWORD', getenv('SMTP_PASSWORD') ?? '');

// Environment
define('ENVIRONMENT', getenv('ENVIRONMENT') ?? 'development');
define('DEBUG_MODE', ENVIRONMENT === 'development');
