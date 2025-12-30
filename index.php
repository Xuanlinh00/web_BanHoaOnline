<?php
define('ROOT_DIR', __DIR__);
require_once ROOT_DIR . '/config/constants.php';
require_once ROOT_DIR . '/config/session.php';

$page_title = 'Trang chủ';
$conn = require ROOT_DIR . '/config/database.php';
require_once ROOT_DIR . '/models/Product.php';
require_once ROOT_DIR . '/models/Category.php';

$product = new Product($conn);
$category = new Category($conn);

// Get featured products
$featured_products = $product->getAllProducts(1, 6);
$categories = $category->getAllCategories();
?>
<?php include 'views/layout/header.php'; ?>

<div class="container">
    <!-- Slideshow -->
    <div id="heroCarousel" class="carousel slide mb-5" data-bs-ride="carousel">
        <div class="carousel-indicators">
            <button type="button" data-bs-target="#heroCarousel" data-bs-slide-to="0" class="active"></button>
            <button type="button" data-bs-target="#heroCarousel" data-bs-slide-to="1"></button>
            <button type="button" data-bs-target="#heroCarousel" data-bs-slide-to="2"></button>
        </div>
        <div class="carousel-inner">
            <div class="carousel-item active">
                <div class="text-white p-5 rounded-4 text-center" style="min-height: 450px; display: flex; align-items: center; justify-content: center; background: var(--gradient-1);">
                    <div>
                        <h1 class="display-3 mb-3 fw-bold">🌸 Hoa Tươi Chất Lượng Cao</h1>
                        <p class="lead mb-4 fs-5">Gửi tặng yêu thương với những bó hoa tươi đẹp nhất</p>
                        <a href="/web_banhoa/views/products/index.php" class="btn btn-light btn-lg fw-bold">Mua sắm ngay</a>
                    </div>
                </div>
            </div>
            <div class="carousel-item">
                <div class="text-white p-5 rounded-4 text-center" style="min-height: 450px; display: flex; align-items: center; justify-content: center; background: var(--gradient-2);">
                    <div>
                        <h1 class="display-3 mb-3 fw-bold">🌹 Hoa Hồng Đỏ</h1>
                        <p class="lead mb-4 fs-5">Biểu tượng của tình yêu và sự lãng mạn</p>
                        <a href="/web_banhoa/views/products/index.php?category=5" class="btn btn-light btn-lg fw-bold">Xem ngay</a>
                    </div>
                </div>
            </div>
            <div class="carousel-item">
                <div class="text-white p-5 rounded-4 text-center" style="min-height: 450px; display: flex; align-items: center; justify-content: center; background: var(--gradient-3);">
                    <div>
                        <h1 class="display-3 mb-3 fw-bold">🌻 Hoa Hướng Dương</h1>
                        <p class="lead mb-4 fs-5">Tươi sáng, rực rỡ, đầy năng lượng</p>
                        <a href="/web_banhoa/views/products/index.php?category=1" class="btn btn-light btn-lg fw-bold">Xem ngay</a>
                    </div>
                </div>
            </div>
        </div>
        <button class="carousel-control-prev" type="button" data-bs-target="#heroCarousel" data-bs-slide="prev">
            <span class="carousel-control-prev-icon"></span>
        </button>
        <button class="carousel-control-next" type="button" data-bs-target="#heroCarousel" data-bs-slide="next">
            <span class="carousel-control-next-icon"></span>
        </button>
    </div>

    <!-- Hero Section -->
    <div class="row mb-5">
        <div class="col-md-12">
            <div class="p-5 rounded-4 text-center" style="background: linear-gradient(135deg, #f5f7fa 0%, #e9ecef 100%); border: 2px solid var(--primary-color);">
                <h2 class="mb-3 fw-bold" style="color: var(--primary-color);">✨ Tại sao chọn chúng tôi?</h2>
                <p class="lead text-muted">Chúng tôi cung cấp những bó hoa tươi nhất với dịch vụ giao hàng nhanh chóng và chuyên nghiệp</p>
            </div>
        </div>
    </div>

    <!-- Categories -->
    <div class="mb-5">
        <h3 class="mb-4 fw-bold" style="color: var(--primary-color);">🎀 Danh mục sản phẩm</h3>
        <div class="row g-3">
            <?php foreach ($categories as $cat): ?>
                <div class="col-md-3">
                    <a href="/web_banhoa/views/products/index.php?category=<?php echo $cat['category_id']; ?>" 
                       class="card text-decoration-none text-dark h-100 shadow-sm">
                        <div class="card-body text-center">
                            <h5 class="card-title" style="color: var(--primary-color);"><?php echo $cat['name']; ?></h5>
                            <p class="card-text text-muted small"><?php echo $cat['description']; ?></p>
                        </div>
                    </a>
                </div>
            <?php endforeach; ?>
        </div>
    </div>

    <!-- Featured Products -->
    <div class="mb-5">
        <h3 class="mb-4 fw-bold" style="color: var(--primary-color);">⭐ Sản phẩm nổi bật</h3>
        <div class="row g-4">
            <?php foreach ($featured_products as $prod): ?>
                <div class="col-md-4">
                    <div class="card h-100 shadow-sm">
                        <img src="<?php echo $prod['image_url'] ?? '/web_banhoa/assets/images/placeholder.jpg'; ?>" 
                             class="card-img-top" alt="<?php echo $prod['name']; ?>" style="height: 250px; object-fit: cover;">
                        <div class="card-body">
                            <h5 class="card-title"><?php echo $prod['name']; ?></h5>
                            <p class="card-text text-muted small"><?php echo substr($prod['description'], 0, 80) . '...'; ?></p>
                            <div class="d-flex justify-content-between align-items-center">
                                <span class="h5 mb-0" style="color: var(--primary-color);"><?php echo number_format($prod['price'], 0, ',', '.'); ?>đ</span>
                                <small class="text-muted">Đã bán: <?php echo $prod['sold_count']; ?></small>
                            </div>
                        </div>
                        <div class="card-footer bg-white">
                            <a href="/web_banhoa/views/products/detail.php?id=<?php echo $prod['product_id']; ?>" 
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
            <div class="p-4 rounded-4" style="background: linear-gradient(135deg, rgba(255, 107, 107, 0.1) 0%, rgba(78, 205, 196, 0.1) 100%);">
                <i class="fas fa-truck" style="font-size: 2.5rem; color: var(--primary-color);"></i>
                <h5 class="mt-3 fw-bold">Giao hàng nhanh</h5>
                <p class="text-muted">Giao hàng trong ngày tại các khu vực nội thành</p>
            </div>
        </div>
        <div class="col-md-4 text-center mb-3">
            <div class="p-4 rounded-4" style="background: linear-gradient(135deg, rgba(78, 205, 196, 0.1) 0%, rgba(255, 217, 61, 0.1) 100%);">
                <i class="fas fa-lock" style="font-size: 2.5rem; color: var(--secondary-color);"></i>
                <h5 class="mt-3 fw-bold">Thanh toán an toàn</h5>
                <p class="text-muted">Hỗ trợ nhiều phương thức thanh toán</p>
            </div>
        </div>
        <div class="col-md-4 text-center mb-3">
            <div class="p-4 rounded-4" style="background: linear-gradient(135deg, rgba(255, 217, 61, 0.1) 0%, rgba(255, 107, 107, 0.1) 100%);">
                <i class="fas fa-redo" style="font-size: 2.5rem; color: var(--accent-color);"></i>
                <h5 class="mt-3 fw-bold">Hoàn tiền 100%</h5>
                <p class="text-muted">Nếu không hài lòng với sản phẩm</p>
            </div>
        </div>
    </div>
</div>

<?php include 'views/layout/footer.php'; ?>
