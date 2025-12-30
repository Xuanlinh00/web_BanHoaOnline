<?php
require_once 'config/database.php';

try {
    $conn = require 'config/database.php';
    
    echo "<h2>Kiểm tra sản phẩm và ảnh</h2>";
    
    // Check products
    $result = $conn->query("SELECT product_id, name, image_url FROM products ORDER BY product_id");
    echo "<h3>Sản phẩm trong database:</h3>";
    
    if ($result->num_rows > 0) {
        while ($row = $result->fetch_assoc()) {
            echo "<div style='border: 1px solid #ccc; margin: 10px; padding: 10px;'>";
            echo "<h4>ID: {$row['product_id']} - {$row['name']}</h4>";
            echo "<p>Main image: {$row['image_url']}</p>";
            
            // Check product_images for this product
            $img_query = "SELECT image_id, image_url, sort_order FROM product_images WHERE product_id = ? ORDER BY sort_order";
            $img_stmt = $conn->prepare($img_query);
            $img_stmt->bind_param("i", $row['product_id']);
            $img_stmt->execute();
            $img_result = $img_stmt->get_result();
            
            echo "<p><strong>Gallery images:</strong></p>";
            if ($img_result->num_rows > 0) {
                while ($img_row = $img_result->fetch_assoc()) {
                    echo "<p>- Sort {$img_row['sort_order']}: {$img_row['image_url']}</p>";
                    
                    // Show actual image if exists
                    if (file_exists($img_row['image_url'])) {
                        echo "<img src='./{$img_row['image_url']}' style='width: 100px; height: 100px; object-fit: cover; margin: 5px;'>";
                    } else {
                        echo "<span style='color: red;'>[File not found]</span>";
                    }
                }
            } else {
                echo "<p style='color: red;'>No gallery images found</p>";
            }
            echo "</div>";
        }
    } else {
        echo "<p style='color: red;'>No products found! <a href='setup-data.php'>Run Setup Data</a></p>";
    }
    
} catch (Exception $e) {
    echo "<p style='color: red;'>Error: " . $e->getMessage() . "</p>";
}
?>

<style>
img {
    border: 1px solid #ddd;
    border-radius: 4px;
}
</style>