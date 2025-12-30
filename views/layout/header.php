<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo (isset($page_title) ? $page_title . ' - ' : '') . 'Web Bán Hoa'; ?></title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link rel="stylesheet" href="<?php echo APP_URL; ?>/assets/css/style.css">
</head>
<body class="bg-soft-pink">
    <nav class="navbar navbar-expand-lg navbar-light border-bottom" style="background: linear-gradient(135deg, #e91e63, #f06292) !important;">
        <div class="container">
            <a class="navbar-brand fw-bold text-white" href="<?php echo APP_URL; ?>/">
                <i class="fas fa-seedling"></i> Web Bán Hoa
            </a>
            <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav">
                <span class="navbar-toggler-icon"></span>
            </button>
            <div class="collapse navbar-collapse" id="navbarNav">
                <ul class="navbar-nav ms-auto">
                    <li class="nav-item">
                        <a class="nav-link text-white" href="<?php echo APP_URL; ?>/">
                            <i class="fas fa-home"></i> Trang chủ
                        </a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link text-white" href="<?php echo APP_URL; ?>/products.php">
                            <i class="fas fa-leaf"></i> Sản phẩm
                        </a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link text-white" href="<?php echo APP_URL; ?>/cart.php">
                            <i class="fas fa-shopping-cart"></i>
                        </a>
                    </li>
                    <?php if (isLoggedIn()): ?>
                        <li class="nav-item dropdown">
                            <a class="nav-link dropdown-toggle text-white" href="#" id="userDropdown" role="button" data-bs-toggle="dropdown">
                                <i class="fas fa-user-circle"></i> <?php echo $_SESSION['full_name']; ?>
                            </a>
                            <ul class="dropdown-menu" aria-labelledby="userDropdown">
                                <?php if (isAdmin()): ?>
                                    <li><a class="dropdown-item" href="<?php echo APP_URL; ?>/admin-dashboard.php">
                                        <i class="fas fa-tachometer-alt"></i> Quản trị
                                    </a></li>
                                    <li><hr class="dropdown-divider"></li>
                                <?php endif; ?>
                                <li><a class="dropdown-item" href="<?php echo APP_URL; ?>/profile.php">
                                    <i class="fas fa-user"></i> Hồ sơ
                                </a></li>
                                <li><a class="dropdown-item" href="<?php echo APP_URL; ?>/orders.php">
                                    <i class="fas fa-shopping-bag"></i> Đơn hàng
                                </a></li>
                                <li><a class="dropdown-item" href="<?php echo APP_URL; ?>/addresses.php">
                                    <i class="fas fa-map-marker-alt"></i> Địa chỉ
                                </a></li>
                                <li><hr class="dropdown-divider"></li>
                                <li><a class="dropdown-item" href="<?php echo APP_URL; ?>/logout.php">
                                    <i class="fas fa-sign-out-alt"></i> Đăng xuất
                                </a></li>
                            </ul>
                        </li>
                    <?php else: ?>
                        <li class="nav-item">
                            <a class="nav-link text-white" href="<?php echo APP_URL; ?>/login.php">
                                <i class="fas fa-sign-in-alt"></i> Đăng nhập
                            </a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link text-white" href="<?php echo APP_URL; ?>/register.php">
                                <i class="fas fa-user-plus"></i> Đăng ký
                            </a>
                        </li>
                    <?php endif; ?>
                </ul>
            </div>
        </div>
    </nav>
    <main class="py-4">
