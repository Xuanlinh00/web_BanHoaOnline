<?php
require_once 'config/database.php';

try {
    $conn = require 'config/database.php';
    
    echo "<h2>Đồng bộ ảnh sản phẩm</h2>";
    
    // Get all products
    $result = $conn->query("SELECT product_id, name, image_url FROM products ORDER BY product_id");
    
    if ($result->num_rows > 0) {
        while ($row = $result->fetch_assoc()) {
            echo "<div style='border: 1px solid #ccc; margin: 10px; padding: 10px;'>";
            echo "<h4>Sản phẩm: {$row['name']} (ID: {$row['product_id']})</h4>";
            
            // Check product_images
            $img_query = "SELECT image_url FROM product_images WHERE product_id = ? ORDER BY sort_order ASC";
            $img_stmt = $conn->prepare($img_query);
            $img_stmt->bind_param("i", $row['product_id']);
            $img_stmt->execute();
            $img_result = $img_stmt->get_result();
            
            echo "<p><strong>Ảnh chính trong products:</strong> {$row['image_url']}</p>";
            echo "<p><strong>Ảnh trong product_images:</strong></p>";
            
            if ($img_result->num_rows > 0) {
                $images = [];
                while ($img_row = $img_result->fetch_assoc()) {
                    $images[] = $img_row['image_url'];
                    echo "<p>- {$img_row['image_url']}</p>";
                }
                
                // Show what homepage will display
                echo "<p><strong>Trang chủ sẽ hiển thị:</strong> {$images[0]}</p>";
                echo "<p><strong>Trang chi tiết sẽ hiển thị:</strong> {$images[0]}</p>";
                
                // Show actual images
                echo "<div style='display: flex; gap: 10px; margin: 10px 0;'>";
                foreach (array_slice($images, 0, 3) as $img) {
                    if (file_exists($img)) {
                        echo "<img src='$img' style='width: 100px; height: 100px; object-fit: cover; border: 1px solid #ddd;'>";
                    } else {
                        echo "<div style='width: 100px; height: 100px; border: 1px solid red; display: flex; align-items: center; justify-content: center; font-size: 10px;'>NOT FOUND</div>";
                    }
                }
                echo "</div>";
                
            } else {
                echo "<p style='color: red;'>Không có ảnh trong product_images!</p>";
                
                // Show fallback logic
                $default_images = [
                    'assets/images/hoasn/z7381588660007_6b681535387273719fddd4c61ab65a6d.jpg',
                    'assets/images/hoatinhyeu/z7381670109021_6ae68533520f3279fe93ce5e14883de6.jpg',
                    'assets/images/hoasn/z7381588665014_5c8becb3c02461df35bf7d26b46b7921.jpg',
                    'assets/images/hoatinhyeu/z7381670113110_83bbbd42cec67574f0a0c022c8e1958d.jpg',
                    'assets/images/hoasn/z7381588694730_4183ddea2e6ae7f552b0c2aa8241edbe.jpg',
                    'assets/images/hoatinhyeu/z7381670120488_57991dc236e68caee10f1ed65289f1c3.jpg'
                ];
                
                $image_index = ($row['product_id'] - 1) % count($default_images);
                $fallback_image = $default_images[$image_index];
                
                echo "<p><strong>Trang chủ sẽ dùng fallback:</strong> $fallback_image</p>";
                echo "<p><strong>Trang chi tiết sẽ dùng:</strong> " . ($row['image_url'] ?: 'placeholder') . "</p>";
                
                if (file_exists($fallback_image)) {
                    echo "<img src='$fallback_image' style='width: 100px; height: 100px; object-fit: cover; border: 1px solid #ddd;'>";
                }
            }
            
            echo "</div>";
        }
    } else {
        echo "<p style='color: red;'>Không có sản phẩm nào! <a href='quick-setup.php'>Chạy Quick Setup</a></p>";
    }
    
} catch (Exception $e) {
    echo "<p style='color: red;'>Lỗi: " . $e->getMessage() . "</p>";
}
?>

<style>
body { font-family: Arial, sans-serif; margin: 20px; }
img { margin: 5px; }
</style>