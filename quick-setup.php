<?php
require_once 'config/database.php';

try {
    $conn = require 'config/database.php';
    
    echo "<h2>Quick Setup - Thêm dữ liệu mẫu nhanh</h2>";
    
    // Clear existing data
    $conn->query("DELETE FROM product_images");
    $conn->query("DELETE FROM products WHERE product_id > 0");
    $conn->query("ALTER TABLE products AUTO_INCREMENT = 1");
    
    echo "<p>✓ Đã xóa dữ liệu cũ</p>";
    
    // Quick products with multiple images
    $products = [
        [
            'name' => 'Bó hoa hồng đỏ tình yêu',
            'description' => 'Bó hoa hồng đỏ tươi thắm, thể hiện tình yêu nồng nàn',
            'price' => 250000,
            'stock' => 50,
            'category_id' => 5,
            'images' => [
                'assets/images/hoatinhyeu/z7381670109021_6ae68533520f3279fe93ce5e14883de6.jpg',
                'assets/images/hoatinhyeu/z7381670113110_83bbbd42cec67574f0a0c022c8e1958d.jpg',
                'assets/images/hoatinhyeu/z7381670120488_57991dc236e68caee10f1ed65289f1c3.jpg',
                'assets/images/hoatinhyeu/z7381670146783_323a2db093af31b45ea6eb96d5b64ef5.jpg'
            ]
        ],
        [
            'name' => 'Bó hoa sinh nhật rực rỡ',
            'description' => 'Bó hoa sinh nhật đầy màu sắc, mang niềm vui',
            'price' => 180000,
            'stock' => 30,
            'category_id' => 1,
            'images' => [
                'assets/images/hoasn/z7381588660007_6b681535387273719fddd4c61ab65a6d.jpg',
                'assets/images/hoasn/z7381588665014_5c8becb3c02461df35bf7d26b46b7921.jpg',
                'assets/images/hoasn/z7381588694730_4183ddea2e6ae7f552b0c2aa8241edbe.jpg'
            ]
        ],
        [
            'name' => 'Bó hoa sinh nhật ngọt ngào',
            'description' => 'Bó hoa sinh nhật thiết kế tinh tế',
            'price' => 220000,
            'stock' => 25,
            'category_id' => 1,
            'images' => [
                'assets/images/hoasn/z7381588698455_645ad6cfacdce6293a7f2d6e802fa233.jpg',
                'assets/images/hoasn/z7381588714325_012c07f5e06bfb491e244e5a45163dd2.jpg'
            ]
        ]
    ];
    
    foreach ($products as $product_data) {
        // Insert product
        $query = "INSERT INTO products (name, description, price, stock, category_id, image_url, status) 
                  VALUES (?, ?, ?, ?, ?, ?, 'available')";
        $stmt = $conn->prepare($query);
        $main_image = $product_data['images'][0];
        $stmt->bind_param("ssdiss", 
            $product_data['name'], 
            $product_data['description'], 
            $product_data['price'], 
            $product_data['stock'], 
            $product_data['category_id'], 
            $main_image
        );
        
        if ($stmt->execute()) {
            $product_id = $conn->insert_id;
            echo "<p>✓ Thêm sản phẩm: {$product_data['name']} (ID: $product_id)</p>";
            
            // Insert multiple images
            foreach ($product_data['images'] as $index => $image_path) {
                $img_query = "INSERT INTO product_images (product_id, image_url, sort_order) VALUES (?, ?, ?)";
                $img_stmt = $conn->prepare($img_query);
                $img_stmt->bind_param("isi", $product_id, $image_path, $index);
                
                if ($img_stmt->execute()) {
                    echo "<p>&nbsp;&nbsp;→ Ảnh $index: $image_path</p>";
                } else {
                    echo "<p style='color: red;'>&nbsp;&nbsp;✗ Lỗi ảnh $index: " . $img_stmt->error . "</p>";
                }
            }
        } else {
            echo "<p style='color: red;'>✗ Lỗi thêm sản phẩm: " . $stmt->error . "</p>";
        }
    }
    
    echo "<h3>✅ Hoàn thành!</h3>";
    echo "<p><a href='index.php' class='btn btn-primary'>Xem trang chủ</a> ";
    echo "<a href='check-products.php' class='btn btn-info'>Kiểm tra dữ liệu</a> ";
    echo "<a href='check-images.php' class='btn btn-warning'>Kiểm tra ảnh</a></p>";
    
    // Show summary
    echo "<h4>Tóm tắt:</h4>";
    $total_products = $conn->query("SELECT COUNT(*) as count FROM products")->fetch_assoc()['count'];
    $total_images = $conn->query("SELECT COUNT(*) as count FROM product_images")->fetch_assoc()['count'];
    echo "<p>- Tổng sản phẩm: $total_products</p>";
    echo "<p>- Tổng ảnh: $total_images</p>";
    
} catch (Exception $e) {
    echo "<p style='color: red;'>Lỗi: " . $e->getMessage() . "</p>";
}
?>

<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/css/bootstrap.min.css" rel="stylesheet">