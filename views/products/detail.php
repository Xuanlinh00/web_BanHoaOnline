<?php
require_once 'config/constants.php';
require_once 'config/session.php';

$page_title = 'Chi tiết sản phẩm';
$conn = require 'config/database.php';
require_once 'models/Product.php';
require_once 'models/Review.php';

if (!isset($_GET['id'])) {
<<<<<<< HEAD
    header('Location: ' . APP_URL . '/products.php');
=======
    header('Location: /web_banhoa/views/products/index.php');
>>>>>>> 37c17f0dac4bb260a987b53f0f92d6e4a0c6a329
    exit;
}

$product_model = new Product($conn);
$review_model = new Review($conn);

$product = $product_model->getProductById($_GET['id']);

if (!$product) {
<<<<<<< HEAD
    header('Location: ' . APP_URL . '/products.php');
=======
    header('Location: /web_banhoa/views/products/index.php');
>>>>>>> 37c17f0dac4bb260a987b53f0f92d6e4a0c6a329
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
<<<<<<< HEAD
        header('Location: ' . APP_URL . '/login.php');
=======
        header('Location: /web_banhoa/views/auth/login.php');
>>>>>>> 37c17f0dac4bb260a987b53f0f92d6e4a0c6a329
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
<<<<<<< HEAD
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
                <img id="mainImage" src="<?php echo $main_image; ?>" 
                     class="img-fluid rounded shadow" alt="<?php echo $product['name']; ?>" 
                     style="width: 100%; height: 400px; object-fit: cover;"
                     onerror="this.src='https://via.placeholder.com/400x400?text=Image+Error'">
=======
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
>>>>>>> 37c17f0dac4bb260a987b53f0f92d6e4a0c6a329
            </div>

            <!-- Thumbnail Gallery -->
            <?php if (!empty($product['images']) && count($product['images']) > 1): ?>
            <div class="row g-2">
<<<<<<< HEAD
                <?php foreach (array_slice($product['images'], 0, 4) as $index => $img): ?>
=======
                <?php foreach ($images as $index => $img): ?>
>>>>>>> 37c17f0dac4bb260a987b53f0f92d6e4a0c6a329
                    <div class="col-3">
                        <img src="<?php echo $img['image_url']; ?>" 
                             class="img-thumbnail cursor-pointer thumbnail-img <?php echo $index === 0 ? 'active' : ''; ?>" 
                             onclick="changeMainImage('<?php echo $img['image_url']; ?>', this)"
                             alt="<?php echo $product['name']; ?>" 
                             style="height: 80px; object-fit: cover; cursor: pointer;"
                             onerror="this.style.display='none'">
                    </div>
                <?php endforeach; ?>
            </div>
            <?php endif; ?>
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

    <!-- Related Products -->
    <?php if (!empty($related_products)): ?>
    <div class="row mt-5">
        <div class="col-12">
            <h3 class="mb-4">Sản phẩm liên quan</h3>
            <div class="row">
                <?php foreach ($related_products as $related): ?>
                    <div class="col-md-3 mb-4">
                        <div class="card h-100 shadow-sm">
                            <?php 
                            $related_image = $related['main_image'] ?: 'https://via.placeholder.com/300x300?text=No+Image';
                            ?>
                            <img src="<?php echo $related_image; ?>" 
                                 class="card-img-top" alt="<?php echo $related['name']; ?>"
                                 style="height: 200px; object-fit: cover;">
                            <div class="card-body d-flex flex-column">
                                <h6 class="card-title"><?php echo $related['name']; ?></h6>
                                <p class="card-text text-muted small flex-grow-1"><?php echo substr($related['description'], 0, 80) . '...'; ?></p>
                                <div class="mt-auto">
                                    <p class="text-danger fw-bold mb-2"><?php echo number_format($related['price'], 0, ',', '.'); ?>đ</p>
                                    <a href="<?php echo APP_URL; ?>/product-detail.php?id=<?php echo $related['product_id']; ?>" 
                                       class="btn btn-outline-primary btn-sm w-100">Xem chi tiết</a>
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
    border: 2px solid #007bff !important;
}

.thumbnail-img:hover {
    opacity: 0.8;
}
</style>

<?php include 'views/layout/footer.php'; ?>
