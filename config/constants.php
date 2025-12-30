<?php
/// Application Constants
if (!defined('APP_URL')) {
    // Auto-detect base application URL relative to the web server document root.
    // This works when the project is placed in a subfolder of the server's docroot.
    $scriptDir = str_replace('\\', '/', dirname($_SERVER['SCRIPT_NAME']));
    // If script is in root, use empty string else use the directory path
    $base = $scriptDir === '/' ? '' : $scriptDir;
    define('APP_URL', $base);
}
if (!defined('APP_NAME')) {
    define('APP_NAME', 'Web Bán Hoa');
}
if (!defined('UPLOAD_DIR')) {
    define('UPLOAD_DIR', __DIR__ . '/../uploads/');
}
if (!defined('UPLOAD_URL')) {
    define('UPLOAD_URL', APP_URL . '/uploads/');
}

// Roles
if (!defined('ROLE_ADMIN')) {
    define('ROLE_ADMIN', 'admin');
}
if (!defined('ROLE_CUSTOMER')) {
    define('ROLE_CUSTOMER', 'customer');
}

// Order Status
if (!defined('ORDER_PENDING')) {
    define('ORDER_PENDING', 'pending');
}
if (!defined('ORDER_CONFIRMED')) {
    define('ORDER_CONFIRMED', 'confirmed');
}
if (!defined('ORDER_SHIPPING')) {
    define('ORDER_SHIPPING', 'shipping');
}
if (!defined('ORDER_COMPLETED')) {
    define('ORDER_COMPLETED', 'completed');
}
if (!defined('ORDER_CANCELLED')) {
    define('ORDER_CANCELLED', 'cancelled');
}
if (!defined('ORDER_RETURNED')) {
    define('ORDER_RETURNED', 'returned');
}

// Payment Status
if (!defined('PAYMENT_UNPAID')) {
    define('PAYMENT_UNPAID', 'unpaid');
}
if (!defined('PAYMENT_PAID')) {
    define('PAYMENT_PAID', 'paid');
}
if (!defined('PAYMENT_REFUNDED')) {
    define('PAYMENT_REFUNDED', 'refunded');
}

// Review Status
if (!defined('REVIEW_PENDING')) {
    define('REVIEW_PENDING', 'pending');
}
if (!defined('REVIEW_APPROVED')) {
    define('REVIEW_APPROVED', 'approved');
}
if (!defined('REVIEW_REJECTED')) {
    define('REVIEW_REJECTED', 'rejected');
}

// Pagination
if (!defined('ITEMS_PER_PAGE')) {
    define('ITEMS_PER_PAGE', 12);
}

?>
