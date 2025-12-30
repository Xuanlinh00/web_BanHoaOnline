# 📖 Hướng dẫn cài đặt Web Bán Hoa

## 📋 Yêu cầu hệ thống

- **PHP**: 7.4 hoặc cao hơn
- **MySQL**: 5.7 hoặc cao hơn
- **Apache**: 2.4 hoặc cao hơn (hoặc Nginx)
- **Trình duyệt**: Chrome, Firefox, Safari, Edge (phiên bản mới nhất)

## 🛠️ Cài đặt trên XAMPP (Khuyến nghị)

### Bước 1: Tải và cài đặt XAMPP

1. Tải XAMPP từ [https://www.apachefriends.org/](https://www.apachefriends.org/)
2. Chọn phiên bản PHP 7.4 hoặc cao hơn
3. Cài đặt XAMPP vào thư mục mặc định (C:\xampp trên Windows)

### Bước 2: Khởi động XAMPP

1. Mở XAMPP Control Panel
2. Nhấp "Start" cho Apache
3. Nhấp "Start" cho MySQL
4. Chờ cho đến khi cả hai dịch vụ chuyển sang màu xanh

### Bước 3: Tải project

1. Tải project từ GitHub hoặc nhận từ nhà phát triển
2. Giải nén vào thư mục: `C:\xampp\htdocs\web_banhoa`

### Bước 4: Tạo database

1. Mở trình duyệt và truy cập: `http://localhost/phpmyadmin`
2. Đăng nhập (mặc định: username = root, password = trống)
3. Nhấp "New" để tạo database mới
4. Nhập tên database: `web_banhoa`
5. Chọn Collation: `utf8mb4_unicode_ci`
6. Nhấp "Create"

### Bước 5: Import database

1. Chọn database `web_banhoa` vừa tạo
2. Nhấp tab "Import"
3. Nhấp "Choose File" và chọn file `db/hoa.sql`
4. Nhấp "Go" để import

### Bước 6: Thêm dữ liệu mẫu (tùy chọn)

1. Chọn database `web_banhoa`
2. Nhấp tab "Import"
3. Nhấp "Choose File" và chọn file `db/seed.sql`
4. Nhấp "Go" để import

### Bước 7: Cấu hình database

1. Mở file `config/database.php`
2. Kiểm tra thông tin kết nối:
   ```php
   $host = 'localhost';
   $user = 'root';
   $password = '';
   $database = 'web_banhoa';
   ```
3. Lưu file

### Bước 8: Tạo thư mục uploads

1. Mở File Explorer
2. Điều hướng đến: `C:\xampp\htdocs\web_banhoa\uploads`
3. Tạo thư mục mới: `products`
4. Cấp quyền ghi cho thư mục (chuột phải → Properties → Security)

### Bước 9: Truy cập website

1. Mở trình duyệt
2. Truy cập: `http://localhost/web_banhoa/`
3. Bạn sẽ thấy trang chủ

## 🔐 Tài khoản mẫu

### Admin
- **Username**: `admin`
- **Password**: `admin123`
- **URL**: `http://localhost/web_banhoa/admin-dashboard.php`

### Customer
- **Username**: `customer`
- **Password**: `customer123`
- **URL**: `http://localhost/web_banhoa/`

## 🚀 Cài đặt trên Linux/Mac

### Bước 1: Cài đặt PHP, MySQL, Apache

**Ubuntu/Debian:**
```bash
sudo apt-get update
sudo apt-get install apache2 mysql-server php php-mysql php-mbstring php-xml
```

**Mac (với Homebrew):**
```bash
brew install php mysql apache2
```

### Bước 2: Khởi động dịch vụ

**Ubuntu/Debian:**
```bash
sudo systemctl start apache2
sudo systemctl start mysql
```

**Mac:**
```bash
brew services start apache2
brew services start mysql
```

### Bước 3: Tải project

```bash
cd /var/www/html  # hoặc /Library/WebServer/Documents trên Mac
git clone <repository-url> web_banhoa
cd web_banhoa
```

### Bước 4: Tạo database

```bash
mysql -u root -p
CREATE DATABASE web_banhoa CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
USE web_banhoa;
SOURCE db/hoa.sql;
SOURCE db/seed.sql;
EXIT;
```

### Bước 5: Cấu hình database

```bash
nano config/database.php
```

Chỉnh sửa thông tin kết nối nếu cần.

### Bước 6: Cấp quyền thư mục

```bash
sudo chown -R www-data:www-data /var/www/html/web_banhoa
sudo chmod -R 755 /var/www/html/web_banhoa
sudo chmod -R 777 /var/www/html/web_banhoa/uploads
```

### Bước 7: Truy cập website

Mở trình duyệt và truy cập: `http://localhost/web_banhoa/`

## 🔧 Cấu hình Apache (nếu cần)

Nếu bạn gặp lỗi 404, hãy bật mod_rewrite:

**Ubuntu/Debian:**
```bash
sudo a2enmod rewrite
sudo systemctl restart apache2
```

**Mac:**
```bash
sudo apachectl restart
```

## 🐛 Khắc phục sự cố

### Lỗi: "Cannot connect to database"
- Kiểm tra MySQL đang chạy
- Kiểm tra thông tin kết nối trong `config/database.php`
- Kiểm tra database `web_banhoa` đã được tạo

### Lỗi: "404 Not Found"
- Kiểm tra mod_rewrite đã được bật
- Kiểm tra .htaccess có trong thư mục gốc
- Kiểm tra đường dẫn trong URL

### Lỗi: "Permission denied" khi upload ảnh
- Cấp quyền ghi cho thư mục `uploads/products`
- Trên Windows: Chuột phải → Properties → Security
- Trên Linux: `chmod 777 uploads/products`

### Lỗi: "Blank page"
- Kiểm tra error log: `php_error.log`
- Bật debug mode trong `config/constants.php`
- Kiểm tra PHP version

## 📝 Cấu hình bổ sung

### Thay đổi múi giờ

Mở `config/constants.php` và thay đổi:
```php
date_default_timezone_set('Asia/Ho_Chi_Minh');
```

### Thay đổi tên ứng dụng

Mở `config/constants.php` và thay đổi:
```php
define('APP_NAME', 'Web Bán Hoa');
define('APP_URL', 'http://localhost/web_banhoa');
```

### Thay đổi số mục trên trang

Mở `config/constants.php` và thay đổi:
```php
define('ITEMS_PER_PAGE', 12);
```

## 🔒 Bảo mật

### Thay đổi mật khẩu admin

1. Đăng nhập vào admin
2. Vào hồ sơ cá nhân
3. Thay đổi mật khẩu

### Xóa tài khoản mẫu

Sau khi cài đặt, hãy xóa tài khoản mẫu:
```sql
DELETE FROM users WHERE username = 'admin' OR username = 'customer';
```

### Bật HTTPS

Cấu hình SSL certificate cho Apache hoặc Nginx.

## 📞 Hỗ trợ

Nếu bạn gặp vấn đề:
1. Kiểm tra README.md
2. Kiểm tra CHANGELOG.md
3. Liên hệ: info@webbanhoa.com
4. Gọi: 0123 456 789

---

**Chúc mừng! Bạn đã cài đặt thành công Web Bán Hoa!** 🎉
