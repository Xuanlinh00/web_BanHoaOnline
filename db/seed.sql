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
