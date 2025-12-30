# 🌸 Web Bán Hoa - Hệ thống bán hoa trực tuyến

## Tổng quan

Web Bán Hoa là một nền tảng e-commerce hiện đại được xây dựng bằng PHP Native, MySQL, và Bootstrap 5. Hệ thống cung cấp trải nghiệm mua sắm hoa tươi trực tuyến với giao diện đẹp, thân thiện và dễ sử dụng.

## 🎯 Tính năng chính

### Phía Khách hàng (Frontend)
- ✅ **Trang chủ** - Slideshow, sản phẩm nổi bật, danh mục
- ✅ **Danh sách sản phẩm** - Lọc theo danh mục, tìm kiếm, phân trang
- ✅ **Chi tiết sản phẩm** - Xem ảnh, mô tả, đánh giá
- ✅ **Giỏ hàng** - Thêm/xóa/cập nhật số lượng sản phẩm
- ✅ **Thanh toán** - Chọn địa chỉ, khung giờ giao hàng, thiệp chúc mừng, gửi ẩn danh
- ✅ **Hồ sơ người dùng** - Chỉnh sửa thông tin cá nhân
- ✅ **Sổ địa chỉ** - Quản lý nhiều địa chỉ giao hàng
- ✅ **Đơn hàng của tôi** - Xem lịch sử đơn hàng
- ✅ **Đánh giá sản phẩm** - Gửi đánh giá và hình ảnh
- ✅ **Về chúng tôi** - Thông tin công ty
- ✅ **Liên hệ** - Form liên hệ
- ✅ **FAQ** - Câu hỏi thường gặp
- ✅ **Chính sách** - Chính sách giao hàng, hoàn trả, thanh toán, bảo mật

### Phía Quản trị (Admin)
- ✅ **Bảng điều khiển** - Thống kê tổng quan (đơn hàng, doanh thu, sản phẩm, khách hàng)
- ✅ **Quản lý sản phẩm** - Thêm/sửa/xóa sản phẩm, upload ảnh
- ✅ **Quản lý đơn hàng** - Xem chi tiết, cập nhật trạng thái
- ✅ **Duyệt đánh giá** - Phê duyệt/từ chối đánh giá
- ✅ **Quản lý khách hàng** - Xem danh sách, xóa khách hàng
- ✅ **Quản lý danh mục** - Thêm/sửa/xóa danh mục sản phẩm

## 📁 Cấu trúc thư mục

```
web_banhoa/
├── admin/                    # Các trang admin
│   ├── dashboard.php
│   ├── products.php
│   ├── product-add.php
│   ├── product-edit.php
│   ├── orders.php
│   ├── order-detail.php
│   ├── reviews.php
│   ├── users.php
│   └── categories.php
├── assets/                   # CSS, JS, hình ảnh
│   ├── css/
│   │   └── style.css
│   ├── js/
│   │   └── main.js
│   └── images/
├── config/                   # Cấu hình
│   ├── constants.php
│   ├── database.php
│   └── session.php
├── db/                       # Database
│   ├── hoa.sql
│   └── seed.sql
├── models/                   # Model classes
│   ├── User.php
│   ├── Product.php
│   ├── Category.php
│   ├── Cart.php
│   ├── Order.php
│   ├── Review.php
│   └── Address.php
├── uploads/                  # Thư mục upload ảnh
│   └── products/
├── views/                    # View files
│   ├── auth/
│   ├── cart/
│   ├── checkout/
│   ├── layout/
│   ├── products/
│   ├── user/
│   ├── about.php
│   ├── contact.php
│   ├── policy.php
│   ├── faq.php
│   └── 404.php
├── index.php                 # Trang chủ
├── products.php              # Danh sách sản phẩm
├── product-detail.php        # Chi tiết sản phẩm
├── cart.php                  # Giỏ hàng
├── checkout.php              # Thanh toán
├── checkout-confirmation.php # Xác nhận đơn hàng
├── profile.php               # Hồ sơ người dùng
├── addresses.php             # Sổ địa chỉ
├── orders.php                # Đơn hàng của tôi
├── login.php                 # Đăng nhập
├── register.php              # Đăng ký
├── logout.php                # Đăng xuất
├── about.php                 # Về chúng tôi
├── contact.php               # Liên hệ
├── policy.php                # Chính sách
├── faq.php                   # FAQ
├── 404.php                   # Trang 404
├── admin-*.php               # Wrapper files cho admin
└── README.md                 # File này
```

## 🎨 Giao diện & Thiết kế

- **Màu sắc chính**: Đỏ (#ff6b6b), Xanh lục (#4ecdc4), Vàng (#ffd93d)
- **Font**: Poppins (Google Fonts)
- **Framework**: Bootstrap 5
- **Icons**: Font Awesome 6
- **Hiệu ứng**: Smooth transitions, animations, hover effects
- **Responsive**: Tương thích với tất cả thiết bị (mobile, tablet, desktop)

## 🔐 Bảo mật

- ✅ Mật khẩu được mã hóa bằng bcrypt
- ✅ Session management
- ✅ Role-based access control (Admin/Customer)
- ✅ SQL injection prevention (Prepared statements)
- ✅ XSS protection (htmlspecialchars)

## 📊 Cơ sở dữ liệu

### Các bảng chính:
- `users` - Người dùng
- `products` - Sản phẩm
- `product_images` - Ảnh sản phẩm
- `categories` - Danh mục
- `carts` - Giỏ hàng
- `cart_items` - Mục giỏ hàng
- `orders` - Đơn hàng
- `order_items` - Mục đơn hàng
- `reviews` - Đánh giá
- `user_addresses` - Địa chỉ người dùng
- `transactions` - Giao dịch

## 🚀 Cài đặt & Chạy

### Yêu cầu:
- PHP 7.4+
- MySQL 5.7+
- Apache/Nginx
- XAMPP (khuyến nghị)

### Bước cài đặt:

1. **Clone/Download project**
   ```bash
   git clone <repository-url>
   cd web_banhoa
   ```

2. **Tạo database**
   - Mở phpMyAdmin
   - Import file `db/hoa.sql`
   - Chạy file `db/seed.sql` để thêm dữ liệu mẫu

3. **Cấu hình database**
   - Chỉnh sửa `config/database.php` với thông tin database của bạn

4. **Tạo thư mục uploads**
   ```bash
   mkdir uploads/products
   chmod 755 uploads/products
   ```

5. **Truy cập website**
   - Trang chủ: `http://localhost/web_banhoa/`
   - Admin: `http://localhost/web_banhoa/admin-dashboard.php`

### Tài khoản mẫu:
- **Admin**: 
  - Username: `admin`
  - Password: `admin123`
- **Customer**:
  - Username: `customer`
  - Password: `customer123`

## 📝 Các trang chính

| Trang | URL | Mô tả |
|-------|-----|-------|
| Trang chủ | `/` | Slideshow, sản phẩm nổi bật |
| Sản phẩm | `/products.php` | Danh sách sản phẩm |
| Chi tiết sản phẩm | `/product-detail.php?id=X` | Xem chi tiết sản phẩm |
| Giỏ hàng | `/cart.php` | Quản lý giỏ hàng |
| Thanh toán | `/checkout.php` | Quy trình thanh toán |
| Hồ sơ | `/profile.php` | Chỉnh sửa thông tin cá nhân |
| Địa chỉ | `/addresses.php` | Quản lý địa chỉ giao hàng |
| Đơn hàng | `/orders.php` | Xem lịch sử đơn hàng |
| Về chúng tôi | `/about.php` | Thông tin công ty |
| Liên hệ | `/contact.php` | Form liên hệ |
| FAQ | `/faq.php` | Câu hỏi thường gặp |
| Chính sách | `/policy.php` | Chính sách & Điều khoản |
| Admin Dashboard | `/admin-dashboard.php` | Bảng điều khiển admin |

## 🛠️ Công nghệ sử dụng

- **Backend**: PHP 7.4+
- **Database**: MySQL 5.7+
- **Frontend**: HTML5, CSS3, JavaScript
- **Framework**: Bootstrap 5
- **Icons**: Font Awesome 6
- **Font**: Poppins (Google Fonts)

## 📞 Hỗ trợ

Nếu bạn gặp vấn đề hoặc có câu hỏi, vui lòng:
- Liên hệ qua email: info@webbanhoa.com
- Gọi điện: 0123 456 789
- Truy cập trang liên hệ: `/contact.php`

## 📄 Giấy phép

Dự án này được cấp phép dưới MIT License.

## 👨‍💻 Tác giả

Được phát triển bởi **Web Bán Hoa Team**

---

**Cảm ơn bạn đã sử dụng Web Bán Hoa!** 🌸
