<?php
// Application Constants
define('APP_URL', '/web_banhoa');
define('APP_NAME', 'Web Bán Hoa');
define('UPLOAD_DIR', __DIR__ . '/../uploads/');
define('UPLOAD_URL', APP_URL . '/uploads/');

// Roles
define('ROLE_ADMIN', 'admin');
define('ROLE_CUSTOMER', 'customer');

// Order Status
define('ORDER_PENDING', 'pending');
define('ORDER_CONFIRMED', 'confirmed');
define('ORDER_SHIPPING', 'shipping');
define('ORDER_COMPLETED', 'completed');
define('ORDER_CANCELLED', 'cancelled');
define('ORDER_RETURNED', 'returned');

// Payment Status
define('PAYMENT_UNPAID', 'unpaid');
define('PAYMENT_PAID', 'paid');
define('PAYMENT_REFUNDED', 'refunded');

// Review Status
define('REVIEW_PENDING', 'pending');
define('REVIEW_APPROVED', 'approved');
define('REVIEW_REJECTED', 'rejected');

// Pagination
define('ITEMS_PER_PAGE', 12);
?>
