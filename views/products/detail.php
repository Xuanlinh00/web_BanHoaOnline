<?php
define('ROOT_DIR', dirname(dirname(__DIR__)));
require_once ROOT_DIR . '/config/constants.php';
require_once ROOT_DIR . '/config/session.php';

$page_title = 'Chi tiết sản phẩm';
$conn = require ROOT_DIR . '/config/database.php';
require_once ROOT_DIR . '/models/Product.php';
require_once ROOT_DIR . '/models/Review.php';

if (!isset($_GET['id'])) {
    header('Location: ' . APP_URL . '/views/products/index.php');
    exit;
}

$product_model = new Product($conn);
$review_model = new Review($conn);

$product = $product_model->getProductById($_GET['id']);

if (!$product) {
    header('Location: ' . APP_URL . '/views/products/index.php');
    exit;
}

// Increment view count
$product_model->incrementViewCount($product['product_id']);

// Get reviews
$reviews = $review_model->getProductReviews($product['product_id']);

// Get related products
$related_products = $product_model->getRelatedProducts($product['product_id'], $product['category_id'], 4);

// Handle add to cart
$message = '';
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['add_to_cart'])) {
    if (!isLoggedIn()) {
        header('Location: ' . APP_URL . '/views/auth/login.php');
        exit;
    }

    require_once ROOT_DIR . '/models/Cart.php';
    $cart = new Cart($conn);
    $quantity = isset($_POST['quantity']) ? (int)$_POST['quantity'] : 1;
    
    if ($cart->addItem(getCurrentUserId(), $product['product_id'], $quantity)) {
        $message = 'Đã thêm vào giỏ hàng!';
    }
}
?>
<?php include ROOT_DIR . '/views/layout/header.php'; ?>

<div class="container">
    <div class="row">
        <!-- Gallery -->
        <div class="col-md-6">
            <!-- Main Image -->
            <div class="mb-3">
                <?php 
                // Simplified image logic - prioritize database images
                $main_image = '';
                
                // First: try product_images table
                if (!empty($product['images']) && count($product['images']) > 0) {
                    $main_image = $product['images'][0]['image_url'];
                }
                // Second: try main image_url field
                elseif (!empty($product['image_url'])) {
                    $main_image = $product['image_url'];
                }
                // Last: use placeholder
                else {
                    $main_image = 'https://via.placeholder.com/400x400?text=Product+' . $product['product_id'];
                }
                ?>
                <img id="mainImage" src="<?php echo htmlspecialchars($main_image); ?>" 
                     class="img-fluid rounded shadow" alt="<?php echo htmlspecialchars($product['name']); ?>" 
                     style="width: 100%; height: 400px; object-fit: cover; background-color: #f5f5f5;"
                     onerror="this.onerror=null; this.src='https://via.placeholder.com/400x400?text=No+Image'; this.style.opacity='0.5';">
            </div>

            <!-- Thumbnail Gallery -->
            <?php if (!empty($product['images']) && count($product['images']) > 1): ?>
            <div class="row g-2">
                <?php foreach (array_slice($product['images'], 0, 4) as $index => $img): ?>
                    <div class="col-3">
                        <img src="<?php echo htmlspecialchars($img['image_url']); ?>" 
                             class="img-thumbnail cursor-pointer thumbnail-img <?php echo $index === 0 ? 'active' : ''; ?>" 
                             onclick="changeMainImage('<?php echo htmlspecialchars($img['image_url']); ?>', this)"
                             alt="<?php echo htmlspecialchars($product['name']); ?>" 
                             style="height: 80px; object-fit: cover; cursor: pointer; background-color: #f5f5f5;"
                             onerror="this.onerror=null; this.src='https://via.placeholder.com/80x80?text=No+Image'; this.style.opacity='0.5';">
                    </div>
                <?php endforeach; ?>
            </div>
            <?php endif; ?>
        </div>

        <!-- Product Info -->
        <div class="col-md-6">
            <h1 class="gradient-text"><?php echo $product['name']; ?></h1>
            
            <div class="mb-3">
                <span class="badge bg-primary"><?php echo $product['category_name']; ?></span>
                <span class="text-muted ms-2">Lượt xem: <?php echo $product['view_count']; ?></span>
            </div>

            <p class="text-muted"><?php echo $product['description']; ?></p>

            <div class="mb-4">
                <h3 class="price-text"><?php echo number_format($product['price'], 0, ',', '.'); ?>đ</h3>
                <p class="text-muted">Tồn kho: <strong><?php echo $product['stock']; ?></strong></p>
            </div>

            <?php if ($message): ?>
                <div class="alert alert-success"><?php echo $message; ?></div>
            <?php endif; ?>

            <?php if (isAdmin()): ?>
                <div class="alert alert-warning">
                    <i class="fas fa-info-circle"></i> Tài khoản quản trị không thể mua hàng. Vui lòng sử dụng tài khoản khách hàng để mua sắm.
                </div>
            <?php elseif ($product['stock'] > 0): ?>
                <form method="POST" class="mb-4">
                    <div class="mb-3">
                        <label for="quantity" class="form-label">Số lượng</label>
                        <input type="number" class="form-control" id="quantity" name="quantity" value="1" min="1" max="<?php echo $product['stock']; ?>">
                    </div>
                    <button type="submit" name="add_to_cart" class="btn btn-primary btn-lg w-100 rounded-pill">
                        <i class="fas fa-shopping-cart"></i> Thêm vào giỏ hàng
                    </button>
                </form>
            <?php else: ?>
                <div class="alert alert-warning">Sản phẩm hiện hết hàng</div>
            <?php endif; ?>
        </div>
    </div>

    <!-- Related Products -->
    <?php if (!empty($related_products)): ?>
    <div class="row mt-5">
        <div class="col-12">
            <h3 class="mb-4 gradient-text rose-decoration">Sản phẩm liên quan</h3>
            <div class="row g-4">
                <?php foreach ($related_products as $related): ?>
                    <div class="col-md-3">
                        <div class="card product-card h-100">
                            <?php 
                            $related_image = !empty($related['main_image']) ? $related['main_image'] : 'https://via.placeholder.com/300x300?text=No+Image';
                            ?>
                            <img src="<?php echo htmlspecialchars($related_image); ?>" 
                                 class="card-img-top product-image" alt="<?php echo htmlspecialchars($related['name']); ?>"
                                 style="height: 250px; object-fit: cover; background-color: #f5f5f5;"
                                 onerror="this.onerror=null; this.src='https://via.placeholder.com/300x300?text=No+Image'; this.style.opacity='0.5';">
                            <div class="card-body d-flex flex-column">
                                <h6 class="card-title text-primary"><?php echo htmlspecialchars($related['name']); ?></h6>
                                <p class="card-text text-muted small flex-grow-1"><?php echo htmlspecialchars(substr($related['description'], 0, 80)) . '...'; ?></p>
                                <div class="mt-auto">
                                    <p class="price-text mb-2"><?php echo number_format($related['price'], 0, ',', '.'); ?>đ</p>
                                    <a href="<?php echo APP_URL; ?>/views/products/detail.php?id=<?php echo $related['product_id']; ?>" 
                                       class="btn btn-outline-primary btn-sm w-100 rounded-pill">Xem chi tiết</a>
                                </div>
                            </div>
                        </div>
                    </div>
                <?php endforeach; ?>
            </div>
        </div>
    </div>
    <?php endif; ?>

    <!-- Reviews -->
    <div class="row mt-5">
        <div class="col-md-8">
            <h3 class="gradient-text flower-decoration">Đánh giá sản phẩm</h3>
            
            <?php if (empty($reviews)): ?>
                <p class="text-muted">Chưa có đánh giá nào.</p>
            <?php else: ?>
                <?php foreach ($reviews as $review): ?>
                    <div class="card mb-3 soft-shadow">
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

<script>
function changeMainImage(imageUrl, thumbnail) {
    // Change main image
    document.getElementById('mainImage').src = imageUrl;
    
    // Update active thumbnail
    document.querySelectorAll('.thumbnail-img').forEach(img => img.classList.remove('active'));
    thumbnail.classList.add('active');
}
</script>

<style>
.thumbnail-img.active {
    border: 2px solid #e91e63 !important;
}

.thumbnail-img:hover {
    opacity: 0.8;
}
</style>

<?php include ROOT_DIR . '/views/layout/footer.php'; ?>
