<?php
// Simple router for XAMPP
$request = parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH);
$request = str_replace('/web_banhoa', '', $request);

// Map routes to files
$routes = [
    '/' => 'index.php',
    '/index.php' => 'index.php',
    '/products/index.php' => 'views/products/index.php',
    '/products/detail.php' => 'views/products/detail.php',
    '/cart/index.php' => 'views/cart/index.php',
    '/checkout/index.php' => 'views/checkout/index.php',
    '/checkout/confirmation.php' => 'views/checkout/confirmation.php',
    '/auth/login.php' => 'views/auth/login.php',
    '/auth/register.php' => 'views/auth/register.php',
    '/auth/logout.php' => 'views/auth/logout.php',
    '/user/profile.php' => 'views/user/profile.php',
    '/user/orders.php' => 'views/user/orders.php',
    '/user/addresses.php' => 'views/user/addresses.php',
    '/admin/dashboard.php' => 'admin/dashboard.php',
    '/admin/orders.php' => 'admin/orders.php',
    '/admin/order-detail.php' => 'admin/order-detail.php',
    '/admin/reviews.php' => 'admin/reviews.php',
];

// Check if route exists
if (isset($routes[$request])) {
    include $routes[$request];
} else {
    // Try direct file access
    $file = __DIR__ . $request;
    if (file_exists($file) && is_file($file)) {
        include $file;
    } else {
        http_response_code(404);
        echo "404 Not Found";
    }
}
?>
