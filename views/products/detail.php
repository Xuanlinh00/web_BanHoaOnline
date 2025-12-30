<?php
require_once 'config/constants.php';
require_once 'config/session.php';

$page_title = 'Chi tiết sản phẩm';
$conn = require 'config/database.php';
require_once 'models/Product.php';
require_once 'models/Review.php';

if (!isset($_GET['id'])) {
    header('Location: /web_banhoa/products.php');
    exit;
}

$product_model = new Product($conn);
$review_model = new Review($conn);

$product = $product_model->getProductById($_GET['id']);

if (!$product) {
    header('Location: /web_banhoa/products.php');
    exit;
}

// Increment view count
$product_model->incrementViewCount($product['product_id']);

// Get reviews
$reviews = $review_model->getProductReviews($product['product_id']);

// Handle add to cart
$message = '';
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['add_to_cart'])) {
    if (!isLoggedIn()) {
        header('Location: /web_banhoa/login.php');
        exit;
    }

    require_once 'models/Cart.php';
    $cart = new Cart($conn);
    $quantity = isset($_POST['quantity']) ? (int)$_POST['quantity'] : 1;
    
    if ($cart->addItem(getCurrentUserId(), $product['product_id'], $quantity)) {
        $message = 'Đã thêm vào giỏ hàng!';
    }
}
?>
<?php include 'views/layout/header.php'; ?>

<div class="container">
    <div class="row">
        <!-- Gallery -->
        <div class="col-md-6">
            <div id="productCarousel" class="carousel slide mb-3" data-bs-ride="carousel">
                <div class="carousel-inner">
                    <?php 
                    $images = !empty($product['images']) ? $product['images'] : [];
                    // If no images in product_images table, use main image_url
                    if (empty($images) && !empty($product['image_url'])) {
                        $images = [['image_url' => $product['image_url']]];
                    }
                    ?>
                    <?php foreach ($images as $index => $img): ?>
                        <div class="carousel-item <?php echo $index === 0 ? 'active' : ''; ?>">
                            <img src="<?php echo $img['image_url']; ?>" class="d-block w-100" alt="<?php echo $product['name']; ?>" style="height: 400px; object-fit: cover;">
                        </div>
                    <?php endforeach; ?>
                </div>
                <?php if (count($images) > 1): ?>
                    <button class="carousel-control-prev" type="button" data-bs-target="#productCarousel" data-bs-slide="prev">
                        <span class="carousel-control-prev-icon"></span>
                    </button>
                    <button class="carousel-control-next" type="button" data-bs-target="#productCarousel" data-bs-slide="next">
                        <span class="carousel-control-next-icon"></span>
                    </button>
                <?php endif; ?>
            </div>

            <!-- Thumbnails -->
            <div class="row g-2">
                <?php foreach ($images as $index => $img): ?>
                    <div class="col-3">
                        <img src="<?php echo $img['image_url']; ?>" class="img-thumbnail cursor-pointer" 
                             onclick="document.querySelector('#productCarousel').querySelector('.carousel-item.active').classList.remove('active'); document.querySelectorAll('#productCarousel .carousel-item')[<?php echo $index; ?>].classList.add('active');"
                             alt="<?php echo $product['name']; ?>" style="height: 80px; object-fit: cover;">
                    </div>
                <?php endforeach; ?>
            </div>
        </div>

        <!-- Product Info -->
        <div class="col-md-6">
            <h1><?php echo $product['name']; ?></h1>
            
            <div class="mb-3">
                <span class="badge bg-primary"><?php echo $product['category_name']; ?></span>
                <span class="text-muted ms-2">Lượt xem: <?php echo $product['view_count']; ?></span>
            </div>

            <p class="text-muted"><?php echo $product['description']; ?></p>

            <div class="mb-4">
                <h3 class="text-danger"><?php echo number_format($product['price'], 0, ',', '.'); ?>đ</h3>
                <p class="text-muted">Tồn kho: <strong><?php echo $product['stock']; ?></strong></p>
            </div>

            <?php if ($message): ?>
                <div class="alert alert-success"><?php echo $message; ?></div>
            <?php endif; ?>

            <?php if ($product['stock'] > 0): ?>
                <form method="POST" class="mb-4">
                    <div class="mb-3">
                        <label for="quantity" class="form-label">Số lượng</label>
                        <input type="number" class="form-control" id="quantity" name="quantity" value="1" min="1" max="<?php echo $product['stock']; ?>">
                    </div>
                    <button type="submit" name="add_to_cart" class="btn btn-primary btn-lg w-100">
                        <i class="fas fa-shopping-cart"></i> Thêm vào giỏ hàng
                    </button>
                </form>
            <?php else: ?>
                <div class="alert alert-warning">Sản phẩm hiện hết hàng</div>
            <?php endif; ?>
        </div>
    </div>

    <!-- Reviews -->
    <div class="row mt-5">
        <div class="col-md-8">
            <h3>Đánh giá sản phẩm</h3>
            
            <?php if (empty($reviews)): ?>
                <p class="text-muted">Chưa có đánh giá nào.</p>
            <?php else: ?>
                <?php foreach ($reviews as $review): ?>
                    <div class="card mb-3">
                        <div class="card-body">
                            <div class="d-flex justify-content-between">
                                <div>
                                    <h6 class="card-title"><?php echo $review['full_name']; ?></h6>
                                    <div class="mb-2">
                                        <?php for ($i = 0; $i < $review['rating']; $i++): ?>
                                            <i class="fas fa-star text-warning"></i>
                                        <?php endfor; ?>
                                        <?php for ($i = $review['rating']; $i < 5; $i++): ?>
                                            <i class="far fa-star text-warning"></i>
                                        <?php endfor; ?>
                                    </div>
                                </div>
                                <small class="text-muted"><?php echo date('d/m/Y', strtotime($review['created_at'])); ?></small>
                            </div>
                            <p class="card-text"><?php echo $review['comment']; ?></p>
                        </div>
                    </div>
                <?php endforeach; ?>
            <?php endif; ?>
        </div>
    </div>
</div>

<?php include 'views/layout/footer.php'; ?>
