-- Insert sample data for testing

USE web_banhoa;

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
('Bó hoa hồng đỏ 12 bông', 'Bó hoa hồng đỏ tươi, tuyệt đẹp dành cho người yêu', 250000, 50, 'assets/images/hoatinhyeu/z7381670109021_6ae68533520f3279fe93ce5e14883de6.jpg', 5, 'available', 15, 120),
('Bó hoa sinh nhật rực rỡ', 'Bó hoa sinh nhật đầy màu sắc, tươi sáng', 180000, 30, 'assets/images/hoasn/z7381588660007_6b681535387273719fddd4c61ab65a6d.jpg', 1, 'available', 8, 85),
('Bó hoa sinh nhật đặc biệt', 'Bó hoa sinh nhật thiết kế độc đáo, ý nghĩa', 220000, 25, 'assets/images/hoasn/z7381588665014_5c8becb3c02461df35bf7d26b46b7921.jpg', 1, 'available', 12, 95),
('Bó hoa khai trương may mắn', 'Bó hoa khai trương mang lại may mắn và thịnh vượng', 150000, 40, 'assets/images/hoakhaitruong/hoa1.jpg', 2, 'available', 20, 110),
('Bó hoa cưới trắng tinh khôi', 'Bó hoa cưới trắng thanh tao, lãng mạn', 200000, 35, 'assets/images/hoacuoi/hoa1.jpg', 3, 'available', 10, 75),
('Bó hoa tình yêu lãng mạn', 'Bó hoa tình yêu thể hiện tình cảm chân thành', 280000, 20, 'assets/images/hoatinhyeu/z7381670113110_83bbbd42cec67574f0a0c022c8e1958d.jpg', 5, 'available', 5, 60),
('Bó hoa chúc mừng sang trọng', 'Bó hoa chúc mừng thiết kế sang trọng, đẳng cấp', 320000, 15, 'assets/images/hoachucmung/hoa1.jpg', 6, 'available', 3, 45),
('Bó hoa sinh nhật ngọt ngào', 'Bó hoa sinh nhật ngọt ngào, đáng yêu', 120000, 60, 'assets/images/hoasn/z7381588694730_4183ddea2e6ae7f552b0c2aa8241edbe.jpg', 1, 'available', 25, 140);

-- Insert sample product images (multiple images per product)
INSERT INTO product_images (product_id, image_url, sort_order) VALUES
-- Product 1: Bó hoa hồng đỏ (Hoa tình yêu)
(1, 'assets/images/hoatinhyeu/z7381670109021_6ae68533520f3279fe93ce5e14883de6.jpg', 0),
(1, 'assets/images/hoatinhyeu/z7381670113110_83bbbd42cec67574f0a0c022c8e1958d.jpg', 1),
(1, 'assets/images/hoatinhyeu/z7381670120488_57991dc236e68caee10f1ed65289f1c3.jpg', 2),
(1, 'assets/images/hoatinhyeu/z7381670146783_323a2db093af31b45ea6eb96d5b64ef5.jpg', 3),

-- Product 2: Bó hoa sinh nhật rực rỡ
(2, 'assets/images/hoasn/z7381588660007_6b681535387273719fddd4c61ab65a6d.jpg', 0),
(2, 'assets/images/hoasn/z7381588665014_5c8becb3c02461df35bf7d26b46b7921.jpg', 1),
(2, 'assets/images/hoasn/z7381588694730_4183ddea2e6ae7f552b0c2aa8241edbe.jpg', 2),

-- Product 3: Bó hoa sinh nhật đặc biệt
(3, 'assets/images/hoasn/z7381588665014_5c8becb3c02461df35bf7d26b46b7921.jpg', 0),
(3, 'assets/images/hoasn/z7381588698455_645ad6cfacdce6293a7f2d6e802fa233.jpg', 1),
(3, 'assets/images/hoasn/z7381588714325_012c07f5e06bfb491e244e5a45163dd2.jpg', 2),
(3, 'assets/images/hoasn/z7381588961682_75420decfc5c101ec3e7a251a3d2b767.jpg', 3),

-- Product 4: Bó hoa khai trương (placeholder - cần thêm ảnh thật)
(4, 'https://via.placeholder.com/400x400?text=Hoa+Khai+Truong+1', 0),
(4, 'https://via.placeholder.com/400x400?text=Hoa+Khai+Truong+2', 1),

-- Product 5: Bó hoa cưới (placeholder - cần thêm ảnh thật)
(5, 'https://via.placeholder.com/400x400?text=Hoa+Cuoi+1', 0),
(5, 'https://via.placeholder.com/400x400?text=Hoa+Cuoi+2', 1),
(5, 'https://via.placeholder.com/400x400?text=Hoa+Cuoi+3', 2),

-- Product 6: Bó hoa tình yêu lãng mạn
(6, 'assets/images/hoatinhyeu/z7381670113110_83bbbd42cec67574f0a0c022c8e1958d.jpg', 0),
(6, 'assets/images/hoatinhyeu/z7381670152038_484847f5cd4360ce2b4b73300f57e198.jpg', 1),

-- Product 7: Bó hoa chúc mừng (placeholder - cần thêm ảnh thật)
(7, 'https://via.placeholder.com/400x400?text=Hoa+Chuc+Mung+1', 0),
(7, 'https://via.placeholder.com/400x400?text=Hoa+Chuc+Mung+2', 1),
(7, 'https://via.placeholder.com/400x400?text=Hoa+Chuc+Mung+3', 2),

-- Product 8: Bó hoa sinh nhật ngọt ngào
(8, 'assets/images/hoasn/z7381588694730_4183ddea2e6ae7f552b0c2aa8241edbe.jpg', 0),
(8, 'assets/images/hoasn/z7381588967394_5bdc1dc2b9b3fdcbaf1d3388016bc786.jpg', 1);

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
