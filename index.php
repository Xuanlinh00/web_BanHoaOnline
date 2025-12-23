<?php
require_once 'config/constants.php';
require_once 'config/session.php';

$page_title = 'Trang chủ';
$conn = require 'config/database.php';
require_once 'models/Product.php';
require_once 'models/Category.php';

$product = new Product($conn);
$category = new Category($conn);

// Get featured products
$featured_products = $product->getAllProducts(1, 6);
$categories = $category->getAllCategories();
?>
<?php include 'views/layout/header.php'; ?>

<div class="container">
    <!-- Hero Section -->
    <div class="row mb-5">
        <div class="col-md-12">
            <div class="bg-light p-5 rounded text-center">
                <h1 class="display-4 mb-3">
                    <i class="fas fa-flower text-danger"></i> Hoa Tươi Chất Lượng Cao
                </h1>
                <p class="lead text-muted mb-4">Gửi tặng yêu thương với những bó hoa tươi đẹp nhất</p>
                <a href="<?php echo APP_URL; ?>/products/index.php" class="btn btn-primary btn-lg">
                    Mua sắm ngay
                </a>
            </div>
        </div>
    </div>

    <!-- Categories -->
    <div class="mb-5">
        <h3 class="mb-4">Danh mục sản phẩm</h3>
        <div class="row g-3">
            <?php foreach ($categories as $cat): ?>
                <div class="col-md-3">
                    <a href="<?php echo APP_URL; ?>/products/index.php?category=<?php echo $cat['category_id']; ?>" 
                       class="card text-decoration-none text-dark h-100 shadow-sm">
                        <div class="card-body text-center">
                            <h5 class="card-title"><?php echo $cat['name']; ?></h5>
                            <p class="card-text text-muted small"><?php echo $cat['description']; ?></p>
                        </div>
                    </a>
                </div>
            <?php endforeach; ?>
        </div>
    </div>

    <!-- Featured Products -->
    <div class="mb-5">
        <h3 class="mb-4">Sản phẩm nổi bật</h3>
        <div class="row g-4">
            <?php foreach ($featured_products as $prod): ?>
                <div class="col-md-4">
                    <div class="card h-100 shadow-sm">
                        <img src="<?php echo $prod['image_url'] ?? APP_URL . '/assets/images/placeholder.jpg'; ?>" 
                             class="card-img-top" alt="<?php echo $prod['name']; ?>" style="height: 250px; object-fit: cover;">
                        <div class="card-body">
                            <h5 class="card-title"><?php echo $prod['name']; ?></h5>
                            <p class="card-text text-muted small"><?php echo substr($prod['description'], 0, 80) . '...'; ?></p>
                            <div class="d-flex justify-content-between align-items-center">
                                <span class="h5 mb-0 text-danger"><?php echo number_format($prod['price'], 0, ',', '.'); ?>đ</span>
                                <small class="text-muted">Đã bán: <?php echo $prod['sold_count']; ?></small>
                            </div>
                        </div>
                        <div class="card-footer bg-white">
                            <a href="<?php echo APP_URL; ?>/products/detail.php?id=<?php echo $prod['product_id']; ?>" 
                               class="btn btn-primary btn-sm w-100">
                                <i class="fas fa-eye"></i> Xem chi tiết
                            </a>
                        </div>
                    </div>
                </div>
            <?php endforeach; ?>
        </div>
    </div>

    <!-- Info Section -->
    <div class="row mb-5">
        <div class="col-md-4 text-center mb-3">
            <i class="fas fa-truck text-primary" style="font-size: 2rem;"></i>
            <h5 class="mt-3">Giao hàng nhanh</h5>
            <p class="text-muted">Giao hàng trong ngày tại các khu vực nội thành</p>
        </div>
        <div class="col-md-4 text-center mb-3">
            <i class="fas fa-lock text-primary" style="font-size: 2rem;"></i>
            <h5 class="mt-3">Thanh toán an toàn</h5>
            <p class="text-muted">Hỗ trợ nhiều phương thức thanh toán</p>
        </div>
        <div class="col-md-4 text-center mb-3">
            <i class="fas fa-redo text-primary" style="font-size: 2rem;"></i>
            <h5 class="mt-3">Hoàn tiền 100%</h5>
            <p class="text-muted">Nếu không hài lòng với sản phẩm</p>
        </div>
    </div>
</div>

<?php include 'views/layout/footer.php'; ?>
