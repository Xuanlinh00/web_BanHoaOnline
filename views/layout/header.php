<!DOCTYPE html>
<html lang="vi">
<head>
    <!-- ========================================
         PHẦN 1: THIẾT LẬP CƠ BẢN HTML
         ======================================== -->
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <!-- Tiêu đề trang (hiển thị trên tab browser) -->
    <title><?php echo (isset($page_title) ? $page_title . ' - ' : '') . 'Web Bán Hoa'; ?></title>
    
    <!-- ========================================
         PHẦN 2: IMPORT BOOTSTRAP & FONT AWESOME
         ======================================== -->
    <!-- Bootstrap 5.3 - Framework CSS cho responsive design -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <!-- Font Awesome 6.4 - Thư viện icon (fa-home, fa-user, v.v.) -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <!-- CSS tùy chỉnh của dự án -->
    <link rel="stylesheet" href="<?php echo APP_URL; ?>/assets/css/style.css?v=<?php echo time(); ?>">
</head>
<body class="bg-soft-pink">
    <!-- ========================================
         PHẦN 3: NAVBAR (THANH ĐIỀU HƯỚNG)
         ======================================== -->
    <nav class="navbar navbar-expand-lg navbar-light border-bottom" 
         style="background: linear-gradient(135deg, #e91e63, #f06292) !important;">
        <div class="container">
            <!-- Logo & Tên website -->
            <a class="navbar-brand fw-bold text-white" href="<?php echo APP_URL; ?>/">
                <i class="fas fa-seedling"></i> Web Bán Hoa
            </a>
            
            <!-- Nút toggle menu cho mobile (khi màn hình nhỏ) -->
            <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav">
                <span class="navbar-toggler-icon"></span>
            </button>
            
            <!-- Menu chính (collapse trên mobile) -->
            <div class="collapse navbar-collapse" id="navbarNav">
                <ul class="navbar-nav ms-auto">
                    <!-- Menu item 1: Trang chủ -->
                    <li class="nav-item">
                        <a class="nav-link text-white" href="<?php echo APP_URL; ?>/">
                            <i class="fas fa-home"></i> Trang chủ
                        </a>
                    </li>
                    
                    <!-- Menu item 2: Sản phẩm -->
                    <li class="nav-item">
                        <a class="nav-link text-white" href="<?php echo APP_URL; ?>/views/products/index.php">
                            <i class="fas fa-leaf"></i> Sản phẩm
                        </a>
                    </li>
                    
                    <!-- Menu item 3: Giỏ hàng -->
                    <li class="nav-item">
                        <a class="nav-link text-white" href="<?php echo APP_URL; ?>/views/cart/index.php">
                            <i class="fas fa-shopping-cart"></i> Giỏ hàng
                        </a>
                    </li>
                    
                    <!-- ========================================
                         PHẦN 4: MENU NGƯỜI DÙNG ĐÃ ĐĂNG NHẬP
                         ======================================== -->
                    <?php if (isLoggedIn()): ?>
                        <!-- Dropdown menu với tên người dùng -->
                        <li class="nav-item dropdown">
                            <a class="nav-link dropdown-toggle text-white" href="#" id="userDropdown" 
                               role="button" data-bs-toggle="dropdown">
                                <i class="fas fa-user-circle"></i> <?php echo $_SESSION['full_name']; ?>
                            </a>
                            <ul class="dropdown-menu" aria-labelledby="userDropdown">
                                <!-- Nếu là admin, hiển thị link quản trị -->
                                <?php if (isAdmin()): ?>
                                    <li><a class="dropdown-item" href="<?php echo APP_URL; ?>/admin-dashboard.php">
                                        <i class="fas fa-tachometer-alt"></i> Quản trị
                                    </a></li>
                                    <li><hr class="dropdown-divider"></li>
                                <?php endif; ?>
                                
                                <!-- Link hồ sơ cá nhân -->
                                <li><a class="dropdown-item" href="<?php echo APP_URL; ?>/views/user/profile.php">
                                    <i class="fas fa-user"></i> Hồ sơ
                                </a></li>
                                
                                <!-- Link xem đơn hàng -->
                                <li><a class="dropdown-item" href="<?php echo APP_URL; ?>/views/user/orders.php">
                                    <i class="fas fa-shopping-bag"></i> Đơn hàng
                                </a></li>
                                
                                <!-- Link quản lý địa chỉ -->
                                <li><a class="dropdown-item" href="<?php echo APP_URL; ?>/views/user/addresses.php">
                                    <i class="fas fa-map-marker-alt"></i> Địa chỉ
                                </a></li>
                                
                                <li><hr class="dropdown-divider"></li>
                                
                                <!-- Link đăng xuất -->
                                <li><a class="dropdown-item" href="<?php echo APP_URL; ?>/views/auth/logout.php">
                                    <i class="fas fa-sign-out-alt"></i> Đăng xuất
                                </a></li>
                            </ul>
                        </li>
                    <?php else: ?>
                        <!-- ========================================
                             PHẦN 5: MENU NGƯỜI DÙNG CHƯA ĐĂNG NHẬP
                             ======================================== -->
                        <!-- Link đăng nhập -->
                        <li class="nav-item">
                            <a class="nav-link text-white" href="<?php echo APP_URL; ?>/views/auth/login.php">
                                <i class="fas fa-sign-in-alt"></i> Đăng nhập
                            </a>
                        </li>
                        
                        <!-- Link đăng ký -->
                        <li class="nav-item">
                            <a class="nav-link text-white" href="<?php echo APP_URL; ?>/views/auth/register.php">
                                <i class="fas fa-user-plus"></i> Đăng ký
                            </a>
                        </li>
                    <?php endif; ?>
                </ul>
            </div>
        </div>
    </nav>
    
    <!-- ========================================
         PHẦN 6: MAIN CONTENT AREA
         ======================================== -->
    <main class="py-4">
