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
    <div id="heroCarousel" class="carousel slide mb-5 rounded-xl soft-shadow" data-bs-ride="carousel">
        <div class="carousel-indicators">
            <button type="button" data-bs-target="#heroCarousel" data-bs-slide-to="0" class="active"></button>
            <button type="button" data-bs-target="#heroCarousel" data-bs-slide-to="1"></button>
            <button type="button" data-bs-target="#heroCarousel" data-bs-slide-to="2"></button>
        </div>
        <div class="carousel-inner">
            <div class="carousel-item active">
                <div class="hero-slide" style="background-image: linear-gradient(rgba(0,0,0,0.4), rgba(0,0,0,0.4)), url('./assets/images/slide/z7381766425359_59890343ace576fda6504690a52bad7d.jpg');">
                    <div class="hero-content">
                        <h1 class="display-4 mb-3 text-white">Hoa Tươi Chất Lượng Cao</h1>
                        <p class="lead mb-4 text-white-50">Gửi tặng yêu thương với những bó hoa tươi đẹp nhất</p>
                        <a href="<?php echo APP_URL; ?>/products.php" class="btn btn-primary btn-lg rounded-pill">
                            <i class="fas fa-shopping-bag"></i> Mua sắm ngay
                        </a>
                    </div>
                </div>
            </div>
            <div class="carousel-item">
                <div class="hero-slide" style="background-image: linear-gradient(rgba(0,0,0,0.4), rgba(0,0,0,0.4)), url('./assets/images/slide/z7381768017715_f62e4e9cf6e2e193d8f8a0e5b877647c.jpg');">
                    <div class="hero-content">
                        <h1 class="display-4 mb-3 text-white">Hoa Sinh Nhật Rực Rỡ</h1>
                        <p class="lead mb-4 text-white-50">Mang lại niềm vui và hạnh phúc trong ngày đặc biệt</p>
                        <a href="<?php echo APP_URL; ?>/products.php?category=1" class="btn btn-primary btn-lg rounded-pill">
                            <i class="fas fa-gift"></i> Xem ngay
                        </a>
                    </div>
                </div>
            </div>
            <div class="carousel-item">
                <div class="hero-slide" style="background-image: linear-gradient(rgba(0,0,0,0.4), rgba(0,0,0,0.4)), url('./assets/images/slide/z7381769713056_91a548234d64bc9c4e2dc9f3453bb9a5.jpg');">
                    <div class="hero-content">
                        <h1 class="display-4 mb-3 text-white">Hoa Tình Yêu Lãng Mạn</h1>
                        <p class="lead mb-4 text-white-50">Biểu tượng của tình yêu và sự lãng mạn</p>
                        <a href="<?php echo APP_URL; ?>/products.php?category=5" class="btn btn-primary btn-lg rounded-pill">
                            <i class="fas fa-heart"></i> Xem ngay
                        </a>
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

    <!-- Categories -->
    <div class="mb-5">
        <h3 class="mb-4 gradient-text flower-decoration">Danh mục sản phẩm</h3>
        <div class="row g-3">
            <?php foreach ($categories as $cat): ?>
                <div class="col-md-2 col-sm-4 col-6">
                    <a href="<?php echo APP_URL; ?>/products.php?category=<?php echo $cat['category_id']; ?>" 
                       class="card category-card text-decoration-none text-dark h-100 soft-shadow">
                        <div class="card-body text-center p-3">
                            <h6 class="card-title text-primary flower-decoration"><?php echo $cat['name']; ?></h6>
                            <p class="card-text text-muted small"><?php echo substr($cat['description'], 0, 40) . '...'; ?></p>
                        </div>
                    </a>
                </div>
            <?php endforeach; ?>
        </div>
    </div>

    <!-- Featured Products -->
    <div class="mb-5">
        <h3 class="mb-4 gradient-text rose-decoration">Sản phẩm nổi bật</h3>
        <div class="row g-4">
            <?php foreach ($featured_products as $prod): ?>
                <div class="col-md-4">
                    <div class="card product-card h-100 rounded-lg">
                        <?php
                        // Simplified image logic - same as detail page
                        $product_image = '';
                        
                        // First: try product_images table
                        if (isset($prod['product_id'])) {
                            $img_query = "SELECT image_url FROM product_images WHERE product_id = ? ORDER BY sort_order ASC LIMIT 1";
                            $img_stmt = $conn->prepare($img_query);
                            $img_stmt->bind_param("i", $prod['product_id']);
                            $img_stmt->execute();
                            $img_result = $img_stmt->get_result();
                            
                            if ($img_result->num_rows > 0) {
                                $img_row = $img_result->fetch_assoc();
                                $product_image = $img_row['image_url'];
                            } elseif (!empty($prod['image_url'])) {
                                $product_image = $prod['image_url'];
                            }
                        }
                        
                        // Display image
                        if (!empty($product_image)) {
                            echo "<img src='$product_image' class='card-img-top product-image' alt='{$prod['name']}' style='height: 250px; object-fit: cover;' onerror=\"this.src='https://via.placeholder.com/250x250?text=Product+{$prod['product_id']}'\">";
                        } else {
                            echo "<div class='card-img-top product-image d-flex align-items-center justify-content-center bg-light' style='height: 250px;'>";
                            echo "<span class='text-muted'>Sản phẩm #{$prod['product_id']}</span>";
                            echo "</div>";
                        }
                        ?>
                        <div class="card-body">
                            <h5 class="card-title text-primary"><?php echo $prod['name']; ?></h5>
                            <p class="card-text text-muted small"><?php echo substr($prod['description'], 0, 80) . '...'; ?></p>
                            <div class="d-flex justify-content-between align-items-center">
                                <span class="h5 mb-0 price-text"><?php echo number_format($prod['price'], 0, ',', '.'); ?>đ</span>
                                <small class="text-muted">Đã bán: <?php echo $prod['sold_count']; ?></small>
                            </div>
                        </div>
                        <div class="card-footer bg-transparent border-0">
                            <a href="<?php echo APP_URL; ?>/product-detail.php?id=<?php echo $prod['product_id']; ?>" 
                               class="btn btn-primary btn-sm w-100 rounded-pill">
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
            <div class="card bg-light-pink rounded-lg soft-shadow h-100">
                <div class="card-body">
                    <i class="fas fa-truck text-primary mb-3" style="font-size: 2.5rem;"></i>
                    <h5 class="text-primary flower-decoration">Giao hàng nhanh</h5>
                    <p class="text-muted">Giao hàng trong ngày tại các khu vực nội thành</p>
                </div>
            </div>
        </div>
        <div class="col-md-4 text-center mb-3">
            <div class="card bg-light-pink rounded-lg soft-shadow h-100">
                <div class="card-body">
                    <i class="fas fa-shield-alt text-primary mb-3" style="font-size: 2.5rem;"></i>
                    <h5 class="text-primary flower-decoration">Thanh toán an toàn</h5>
                    <p class="text-muted">Hỗ trợ nhiều phương thức thanh toán</p>
                </div>
            </div>
        </div>
        <div class="col-md-4 text-center mb-3">
            <div class="card bg-light-pink rounded-lg soft-shadow h-100">
                <div class="card-body">
                    <i class="fas fa-heart text-primary mb-3" style="font-size: 2.5rem;"></i>
                    <h5 class="text-primary flower-decoration">Hoàn tiền 100%</h5>
                    <p class="text-muted">Nếu không hài lòng với sản phẩm</p>
                </div>
            </div>
        </div>
    </div>
</div>

<?php include 'views/layout/footer.php'; ?>
