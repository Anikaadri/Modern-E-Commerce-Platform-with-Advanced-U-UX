<?php
/**
 * Application Constants
 */

// HTTP Status Codes
define('HTTP_OK', 200);
define('HTTP_CREATED', 201);
define('HTTP_BAD_REQUEST', 400);
define('HTTP_UNAUTHORIZED', 401);
define('HTTP_FORBIDDEN', 403);
define('HTTP_NOT_FOUND', 404);
define('HTTP_INTERNAL_SERVER_ERROR', 500);

// Order Status
define('ORDER_STATUS_PENDING', 'pending');
define('ORDER_STATUS_PROCESSING', 'processing');
define('ORDER_STATUS_SHIPPED', 'shipped');
define('ORDER_STATUS_DELIVERED', 'delivered');
define('ORDER_STATUS_CANCELLED', 'cancelled');

// Product Status
define('PRODUCT_STATUS_ACTIVE', 'active');
define('PRODUCT_STATUS_INACTIVE', 'inactive');
define('PRODUCT_STATUS_OUT_OF_STOCK', 'out_of_stock');

// Payment Methods
define('PAYMENT_METHOD_CREDIT_CARD', 'credit_card');
define('PAYMENT_METHOD_DEBIT_CARD', 'debit_card');
define('PAYMENT_METHOD_PAYPAL', 'paypal');
define('PAYMENT_METHOD_BANK_TRANSFER', 'bank_transfer');

// User Roles
define('USER_ROLE_CUSTOMER', 'customer');
define('USER_ROLE_ADMIN', 'admin');
define('USER_ROLE_MODERATOR', 'moderator');

// Discount Type
define('DISCOUNT_TYPE_PERCENTAGE', 'percentage');
define('DISCOUNT_TYPE_FIXED', 'fixed');

// Messages
define('MSG_SUCCESS', 'Operation completed successfully');
define('MSG_ERROR', 'An error occurred. Please try again');
define('MSG_INVALID_INPUT', 'Invalid input provided');
define('MSG_UNAUTHORIZED', 'You are not authorized to perform this action');
define('MSG_NOT_FOUND', 'Resource not found');
