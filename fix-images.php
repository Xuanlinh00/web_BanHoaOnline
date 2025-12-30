<?php
require_once 'config/database.php';

try {
    $conn = require 'config/database.php';
    
    echo "<h2>Sửa ảnh sản phẩm</h2>";
    
    // Clear all existing data
    $conn->query("DELETE FROM product_images");
    $conn->query("DELETE FROM products WHERE product_id > 0");
    $conn->query("ALTER TABLE products AUTO_INCREMENT = 1");
    
    echo "<p>✓ Đã xóa dữ liệu cũ</p>";
    
    // Create products with correct images
    $products_data = [
        [
            'name' => 'Hoa Tang Lễ',
            'description' => 'Hoa tang lễ trang trọng, thể hiện lòng thành kính',
            'price' => 400000,
            'stock' => 15,
            'category_id' => 4, // Hoa tang lễ
            'main_image' => 'assets/images/hoatang/hoa1.jpg',
            'gallery' => [
                'assets/images/hoatang/hoa1.jpg',
                'assets/images/hoatang/hoa2.jpg'
            ]
        ],
        [
            'name' => 'Hoa Sinh Nhật',
            'description' => 'Hoa sinh nhật rực rỡ, mang lại niềm vui',
            'price' => 150000,
            'stock' => 30,
            'category_id' => 1, // Hoa sinh nhật
            'main_image' => 'assets/images/hoasn/z7381588660007_6b681535387273719fddd4c61ab65a6d.jpg',
            'gallery' => [
                'assets/images/hoasn/z7381588660007_6b681535387273719fddd4c61ab65a6d.jpg',
                'assets/images/hoasn/z7381588665014_5c8becb3c02461df35bf7d26b46b7921.jpg',
                'assets/images/hoasn/z7381588694730_4183ddea2e6ae7f552b0c2aa8241edbe.jpg'
            ]
        ],
        [
            'name' => 'Hoa Cưới',
            'description' => 'Hoa cưới trắng tinh khôi, lãng mạn',
            'price' => 300000,
            'stock' => 20,
            'category_id' => 3, // Hoa cưới
            'main_image' => 'assets/images/hoacuoi/hoa1.jpg',
            'gallery' => [
                'assets/images/hoacuoi/hoa1.jpg',
                'assets/images/hoacuoi/hoa2.jpg'
            ]
        ],
        [
            'name' => 'Hoa Tình Yêu',
            'description' => 'Hoa tình yêu lãng mạn, thể hiện tình cảm',
            'price' => 250000,
            'stock' => 25,
            'category_id' => 5, // Hoa tình yêu
            'main_image' => 'assets/images/hoatinhyeu/z7381670109021_6ae68533520f3279fe93ce5e14883de6.jpg',
            'gallery' => [
                'assets/images/hoatinhyeu/z7381670109021_6ae68533520f3279fe93ce5e14883de6.jpg',
                'assets/images/hoatinhyeu/z7381670113110_83bbbd42cec67574f0a0c022c8e1958d.jpg',
                'assets/images/hoatinhyeu/z7381670120488_57991dc236e68caee10f1ed65289f1c3.jpg',
                'assets/images/hoatinhyeu/z7381670146783_323a2db093af31b45ea6eb96d5b64ef5.jpg'
            ]
        ],
        [
            'name' => 'Hoa Khai Trương',
            'description' => 'Hoa khai trương may mắn, thịnh vượng',
            'price' => 180000,
            'stock' => 20,
            'category_id' => 2, // Hoa khai trương
            'main_image' => 'assets/images/hoakhaitruong/hoa1.jpg',
            'gallery' => [
                'assets/images/hoakhaitruong/hoa1.jpg',
                'assets/images/hoakhaitruong/hoa2.jpg'
            ]
        ],
        [
            'name' => 'Hoa Chúc Mừng',
            'description' => 'Hoa chúc mừng sang trọng, đẳng cấp',
            'price' => 220000,
            'stock' => 18,
            'category_id' => 6, // Hoa chúc mừng
            'main_image' => 'assets/images/hoachucmung/hoa1.jpg',
            'gallery' => [
                'assets/images/hoachucmung/hoa1.jpg',
                'assets/images/hoachucmung/hoa2.jpg'
            ]
        ]
    ];
    
    foreach ($products_data as $product_data) {
        // Check if main image exists
        if (!file_exists($product_data['main_image'])) {
            echo "<p style='color: orange;'>⚠ Ảnh chính không tồn tại: {$product_data['main_image']}</p>";
            // Use a fallback from hoasn folder
            $product_data['main_image'] = 'assets/images/hoasn/z7381588660007_6b681535387273719fddd4c61ab65a6d.jpg';
        }
        
        // Insert product
        $query = "INSERT INTO products (name, description, price, stock, category_id, image_url, status) 
                  VALUES (?, ?, ?, ?, ?, ?, 'available')";
        $stmt = $conn->prepare($query);
        $stmt->bind_param("ssdiss", 
            $product_data['name'], 
            $product_data['description'], 
            $product_data['price'], 
            $product_data['stock'], 
            $product_data['category_id'], 
            $product_data['main_image']
        );
        
        if ($stmt->execute()) {
            $product_id = $conn->insert_id;
            echo "<p>✓ Thêm sản phẩm: {$product_data['name']} (ID: $product_id)</p>";
            
            // Insert gallery images
            foreach ($product_data['gallery'] as $index => $image_path) {
                if (file_exists($image_path)) {
                    $img_query = "INSERT INTO product_images (product_id, image_url, sort_order) VALUES (?, ?, ?)";
                    $img_stmt = $conn->prepare($img_query);
                    $img_stmt->bind_param("isi", $product_id, $image_path, $index);
                    
                    if ($img_stmt->execute()) {
                        echo "<p>&nbsp;&nbsp;→ Ảnh $index: $image_path ✓</p>";
                    } else {
                        echo "<p style='color: red;'>&nbsp;&nbsp;✗ Lỗi ảnh $index: " . $img_stmt->error . "</p>";
                    }
                } else {
                    echo "<p style='color: orange;'>&nbsp;&nbsp;⚠ Ảnh không tồn tại: $image_path</p>";
                }
            }
        } else {
            echo "<p style='color: red;'>✗ Lỗi thêm sản phẩm: " . $stmt->error . "</p>";
        }
    }
    
    echo "<h3>✅ Hoàn thành sửa ảnh!</h3>";
    echo "<p><a href='index.php' class='btn btn-primary'>Xem trang chủ</a> ";
    echo "<a href='sync-images.php' class='btn btn-info'>Kiểm tra đồng bộ</a></p>";
    
    // Show final summary
    $total_products = $conn->query("SELECT COUNT(*) as count FROM products")->fetch_assoc()['count'];
    $total_images = $conn->query("SELECT COUNT(*) as count FROM product_images")->fetch_assoc()['count'];
    echo "<p><strong>Kết quả:</strong> $total_products sản phẩm, $total_images ảnh</p>";
    
} catch (Exception $e) {
    echo "<p style='color: red;'>Lỗi: " . $e->getMessage() . "</p>";
}
?>

<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/css/bootstrap.min.css" rel="stylesheet">
<style>
body { font-family: Arial, sans-serif; margin: 20px; }
</style>