<?php
$conn = new mysqli('localhost', 'root', '', 'web_banhoa');

// Fix image_url = 0 to NULL
$conn->query("UPDATE products SET image_url = NULL WHERE image_url = '0' OR image_url = ''");

// Update products with uploaded images
$conn->query("UPDATE products SET image_url = '/web_banhoa/uploads/products/1766543647_slider1.jpg' WHERE product_id = 9");
$conn->query("UPDATE products SET image_url = '/web_banhoa/uploads/products/1766543829_binh1.jpg' WHERE product_id = 10");

echo "Cập nhật thành công!";
?>
