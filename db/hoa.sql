
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

-- Insert sample data for testing


-- Insert admin user (password: admin123)
INSERT INTO users (username, email, password_hash, full_name, phone, role, status) VALUES
('admin', 'admin@banhoa.com', '$2y$10$YIjlrPNoS0E9IeZDYtQute7DkJKSK.TqIpy9LQname8SWsuS2PK.i', 'Admin', '0123456789', 'admin', 'active');

-- Insert categories
INSERT INTO categories (name, description, status) VALUES
('Hoa sinh nhật', 'Những bó hoa tươi dành cho sinh nhật', 'active'),
('Hoa khai trương', 'Hoa tươi cho sự kiện khai trương', 'active'),
('Hoa cưới', 'Hoa tươi cho đám cưới', 'active'),
('Hoa tang lễ', 'Hoa tươi cho tang lễ', 'active'),
('Hoa tình yêu', 'Hoa tươi dành cho người yêu', 'active'),
('Hoa chúc mừng', 'Hoa tươi chúc mừng', 'active');

-- Insert products
INSERT INTO products (name, description, price, stock, image_url, category_id, status, sold_count, view_count) VALUES
('Bó hoa hồng đỏ 12 bông', 'Bó hoa hồng đỏ tươi, tuyệt đẹp dành cho người yêu', 250000, 50, 'https://via.placeholder.com/300x300?text=Rose+Red', 5, 'available', 15, 120),
('Bó hoa hướng dương', 'Bó hoa hướng dương vàng rực rỡ, tươi sáng', 180000, 30, 'https://via.placeholder.com/300x300?text=Sunflower', 1, 'available', 8, 85),
('Bó hoa tulip đa sắc', 'Bó hoa tulip nhiều màu sắc, tươi tắn', 220000, 25, 'https://via.placeholder.com/300x300?text=Tulip', 1, 'available', 12, 95),
('Bó hoa cẩm chướng', 'Bó hoa cẩm chướng tươi, bền lâu', 150000, 40, 'https://via.placeholder.com/300x300?text=Carnation', 2, 'available', 20, 110),
('Bó hoa loa kèn trắng', 'Bó hoa loa kèn trắng tinh khôi', 200000, 35, 'https://via.placeholder.com/300x300?text=Lily', 3, 'available', 10, 75),
('Bó hoa hồng trắng', 'Bó hoa hồng trắng thanh tao', 280000, 20, 'https://via.placeholder.com/300x300?text=Rose+White', 3, 'available', 5, 60),
('Bó hoa mẫu đơn', 'Bó hoa mẫu đơn hồng xinh đẹp', 320000, 15, 'https://via.placeholder.com/300x300?text=Peony', 5, 'available', 3, 45),
('Bó hoa cúc vàng', 'Bó hoa cúc vàng tươi sáng', 120000, 60, 'https://via.placeholder.com/300x300?text=Daisy', 1, 'available', 25, 140);

-- Insert payment methods
INSERT INTO payment_methods (name, code, status) VALUES
('Thanh toán khi nhận hàng', 'cod', 'active'),
('Chuyển khoản ngân hàng', 'bank_transfer', 'active'),
('Ví điện tử', 'ewallet', 'active');

-- Insert shipping methods
INSERT INTO shipping_methods (name, base_fee, estimated_time, status) VALUES
('Giao hàng tiêu chuẩn', 30000, '1-2 ngày', 'active'),
('Giao hàng nhanh', 50000, '4-6 giờ', 'active'),
('Giao hàng cùng ngày', 80000, '2-4 giờ', 'active');

-- Insert sample customer
INSERT INTO users (username, email, password_hash, full_name, phone, role, status) VALUES
('customer1', 'customer@example.com', '$2y$10$YIjlrPNoS0E9IeZDYtQute7DkJKSK.TqIpy9LQname8SWsuS2PK.i', 'Nguyễn Văn A', '0987654321', 'customer', 'active');

-- Insert sample addresses
INSERT INTO user_addresses (user_id, recipient_name, recipient_phone, address_line, ward, district, city, is_default) VALUES
(2, 'Nguyễn Văn A', '0987654321', '123 Đường Lê Lợi', 'Phường 1', 'Quận 1', 'TP. Hồ Chí Minh', 1),
(2, 'Nguyễn Thị B', '0912345678', '456 Đường Nguyễn Huệ', 'Phường 2', 'Quận 3', 'TP. Hồ Chí Minh', 0);

