
DROP DATABASE IF EXISTS web_banhoa;
CREATE DATABASE web_banhoa;
USE web_banhoa;

-- =============================================
-- 1. QUẢN LÝ NGƯỜI DÙNG & ĐỊA CHỈ
-- =============================================

CREATE TABLE users (
    user_id INT AUTO_INCREMENT PRIMARY KEY,
    username VARCHAR(50) UNIQUE NOT NULL,
    password_hash VARCHAR(255) NOT NULL,
    email VARCHAR(100) UNIQUE NOT NULL,
    full_name VARCHAR(100) NOT NULL,
    phone VARCHAR(15),
    role ENUM('admin', 'customer') DEFAULT 'customer',
    status ENUM('active', 'inactive', 'blocked') DEFAULT 'active',
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
);

CREATE TABLE user_addresses (
    address_id INT AUTO_INCREMENT PRIMARY KEY,
    user_id INT NOT NULL,
    recipient_name VARCHAR(100) NOT NULL, 
    recipient_phone VARCHAR(15) NOT NULL,
    address_line VARCHAR(255) NOT NULL,   
    ward VARCHAR(100),
    district VARCHAR(100),
    city VARCHAR(100),
    is_default BOOLEAN DEFAULT FALSE,     
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (user_id) REFERENCES users(user_id) ON DELETE CASCADE
);

-- =============================================
-- 2. QUẢN LÝ SẢN PHẨM (Đã cập nhật Gallery ảnh)
-- =============================================

CREATE TABLE categories (
    category_id INT AUTO_INCREMENT PRIMARY KEY,
    name VARCHAR(100) NOT NULL,
    description TEXT,
    status ENUM('active', 'inactive') DEFAULT 'active'
);

CREATE TABLE products (
    product_id INT AUTO_INCREMENT PRIMARY KEY,
    name VARCHAR(255) NOT NULL,
    description TEXT,
    price DECIMAL(10, 2) NOT NULL,
    stock INT DEFAULT 0,
    -- Giữ lại image_url làm ảnh đại diện chính (thumbnail) để query cho nhanh
    image_url VARCHAR(500), 
    category_id INT,
    status ENUM('available', 'out_of_stock', 'discontinued') DEFAULT 'available',
    sold_count INT DEFAULT 0,
    view_count INT DEFAULT 0, 
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    FOREIGN KEY (category_id) REFERENCES categories(category_id) ON DELETE SET NULL
);

-- [MỚI] Bảng chứa bộ sưu tập ảnh cho từng sản phẩm
CREATE TABLE product_images (
    image_id INT AUTO_INCREMENT PRIMARY KEY,
    product_id INT NOT NULL,
    image_url VARCHAR(500) NOT NULL,
    sort_order INT DEFAULT 0, -- Dùng để sắp xếp thứ tự hiển thị (0 hiện trước)
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (product_id) REFERENCES products(product_id) ON DELETE CASCADE
);

-- =============================================
-- 3. CẤU HÌNH THANH TOÁN & VẬN CHUYỂN
-- =============================================

CREATE TABLE payment_methods (
    payment_method_id INT AUTO_INCREMENT PRIMARY KEY,
    name VARCHAR(100) NOT NULL,    
    code VARCHAR(50) UNIQUE NOT NULL, 
    status ENUM('active', 'inactive') DEFAULT 'active'
);

CREATE TABLE shipping_methods (
    shipping_method_id INT AUTO_INCREMENT PRIMARY KEY,
    name VARCHAR(100) NOT NULL,       
    base_fee DECIMAL(10, 2) NOT NULL, 
    estimated_time VARCHAR(50),       
    status ENUM('active', 'inactive') DEFAULT 'active'
);

-- =============================================
-- 4. GIỎ HÀNG (Cart)
-- =============================================

CREATE TABLE carts (
    cart_id INT AUTO_INCREMENT PRIMARY KEY,
    user_id INT NOT NULL,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    FOREIGN KEY (user_id) REFERENCES users(user_id) ON DELETE CASCADE,
    UNIQUE KEY unique_user_cart (user_id)
);

CREATE TABLE cart_items (
    cart_item_id INT AUTO_INCREMENT PRIMARY KEY,
    cart_id INT NOT NULL,
    product_id INT NOT NULL,
    quantity INT NOT NULL DEFAULT 1,
    FOREIGN KEY (cart_id) REFERENCES carts(cart_id) ON DELETE CASCADE,
    FOREIGN KEY (product_id) REFERENCES products(product_id) ON DELETE CASCADE,
    UNIQUE KEY unique_cart_product (cart_id, product_id)
);

-- =============================================
-- 5. ĐƠN HÀNG (Đã cập nhật Message Card & Delivery Time)
-- =============================================

CREATE TABLE orders (
    order_id INT AUTO_INCREMENT PRIMARY KEY,
    user_id INT NOT NULL,
    order_code VARCHAR(50) UNIQUE NOT NULL, 
    order_date TIMESTAMP DEFAULT CURRENT_TIMESTAMP, -- Ngày tạo đơn
    
    -- [MỚI] Thông tin giao hàng đặc thù cho Hoa
    delivery_date DATE, -- Ngày khách muốn giao (VD: 14/02/2025)
    delivery_time_slot VARCHAR(50), -- Khung giờ (VD: 08:00 - 10:00)
    message_card TEXT, -- Lời chúc in trên thiệp
    is_anonymous BOOLEAN DEFAULT FALSE, -- Giấu tên người gửi (Optional)

    status ENUM('pending', 'confirmed', 'shipping', 'completed', 'cancelled', 'returned') DEFAULT 'pending', 
    
    subtotal DECIMAL(10, 2) NOT NULL,      
    shipping_fee DECIMAL(10, 2) DEFAULT 0, 
    discount_amount DECIMAL(10, 2) DEFAULT 0, 
    total_amount DECIMAL(10, 2) NOT NULL,  
    
    recipient_name VARCHAR(100) NOT NULL,
    recipient_phone VARCHAR(15) NOT NULL,
    shipping_address VARCHAR(255) NOT NULL,
    shipping_ward VARCHAR(100),
    shipping_district VARCHAR(100) NOT NULL,
    shipping_city VARCHAR(100) NOT NULL,
    
    shipping_method_id INT,
    payment_method_id INT,
    
    payment_status ENUM('unpaid', 'paid', 'refunded') DEFAULT 'unpaid',
    
    notes TEXT, -- Ghi chú vận chuyển (VD: Gọi trước khi giao, nhà có chó dữ...)
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    
    FOREIGN KEY (user_id) REFERENCES users(user_id) ON DELETE RESTRICT,
    FOREIGN KEY (shipping_method_id) REFERENCES shipping_methods(shipping_method_id) ON DELETE SET NULL,
    FOREIGN KEY (payment_method_id) REFERENCES payment_methods(payment_method_id) ON DELETE SET NULL
);

CREATE TABLE order_items (
    order_item_id INT AUTO_INCREMENT PRIMARY KEY,
    order_id INT NOT NULL,
    product_id INT, 
    product_name VARCHAR(255) NOT NULL,    
    product_price DECIMAL(10, 2) NOT NULL, 
    quantity INT NOT NULL,
    subtotal DECIMAL(10, 2) NOT NULL,
    FOREIGN KEY (order_id) REFERENCES orders(order_id) ON DELETE CASCADE,
    FOREIGN KEY (product_id) REFERENCES products(product_id) ON DELETE SET NULL
);

CREATE TABLE transactions (
    transaction_id INT AUTO_INCREMENT PRIMARY KEY,
    order_id INT NOT NULL,
    payment_method_id INT,
    transaction_code VARCHAR(100), 
    amount DECIMAL(10, 2) NOT NULL,
    status ENUM('pending', 'success', 'failed') DEFAULT 'pending',
    payment_time TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    extra_info JSON, 
    FOREIGN KEY (order_id) REFERENCES orders(order_id) ON DELETE CASCADE,
    FOREIGN KEY (payment_method_id) REFERENCES payment_methods(payment_method_id) ON DELETE SET NULL
);

-- =============================================
-- 6. ĐÁNH GIÁ (Reviews)
-- =============================================

CREATE TABLE reviews (
    review_id INT AUTO_INCREMENT PRIMARY KEY,
    user_id INT NOT NULL,
    product_id INT NOT NULL,
    order_id INT NOT NULL, 
    rating INT NOT NULL CHECK(rating >= 1 AND rating <= 5),
    comment TEXT,
    images JSON, 
    status ENUM('pending', 'approved', 'rejected') DEFAULT 'pending', 
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (user_id) REFERENCES users(user_id) ON DELETE CASCADE,
    FOREIGN KEY (product_id) REFERENCES products(product_id) ON DELETE CASCADE,
    FOREIGN KEY (order_id) REFERENCES orders(order_id) ON DELETE CASCADE,
    UNIQUE KEY unique_order_product_review (order_id, product_id)
);
