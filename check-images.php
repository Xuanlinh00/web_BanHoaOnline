<?php
$conn = new mysqli('localhost', 'root', '', 'web_banhoa');
$result = $conn->query("SELECT product_id, name, image_url FROM products WHERE image_url IS NOT NULL LIMIT 10");

echo "<pre>";
while ($row = $result->fetch_assoc()) {
    echo "ID: " . $row['product_id'] . "\n";
    echo "Name: " . $row['name'] . "\n";
    echo "URL: " . $row['image_url'] . "\n";
    echo "---\n";
}
echo "</pre>";
?>
