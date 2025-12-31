<?php
define('ROOT_DIR', dirname(dirname(__DIR__)));
require_once ROOT_DIR . '/config/constants.php';
require_once ROOT_DIR . '/config/session.php';

$page_title = 'Sản phẩm';
$conn = require ROOT_DIR . '/config/database.php';
require_once ROOT_DIR . '/models/Product.php';
require_once ROOT_DIR . '/models/Category.php';

$product = new Product($conn);
$category = new Category($conn);

$page = isset($_GET['page']) ? (int)$_GET['page'] : 1;
$category_id = isset($_GET['category']) ? (int)$_GET['category'] : null;
$search = isset($_GET['search']) ? $_GET['search'] : '';

// Get products
if ($search) {
    $products = $product->searchProducts($search, $page);
} elseif ($category_id) {
    $products = $product->getProductsByCategory($category_id, $page);
} else {
    $products = $product->getAllProducts($page);
}

$categories = $category->getAllCategories();
$total_products = $product->getTotalProducts();
$total_pages = ceil($total_products / ITEMS_PER_PAGE);
?>
<?php include ROOT_DIR . '/views/layout/header.php'; ?>

<div class="container">
    <div class="row">
        <!-- Sidebar -->
        <div class="col-md-3">
            <div class="card mb-4">
                <div class="card-body">
                    <h5 class="card-title gradient-text flower-decoration">Danh mục</h5>
                    <div class="list-group">
                        <a href="<?php echo APP_URL; ?>/views/products/index.php" class="list-group-item list-group-item-action <?php echo !$category_id ? 'active' : ''; ?>">
                            Tất cả
                        </a>
                        <?php foreach ($categories as $cat): ?>
                            <a href="<?php echo APP_URL; ?>/views/products/index.php?category=<?php echo $cat['category_id']; ?>" 
                               class="list-group-item list-group-item-action <?php echo $category_id == $cat['category_id'] ? 'active' : ''; ?>">
                                <?php echo $cat['name']; ?>
                            </a>
                        <?php endforeach; ?>
                    </div>
                </div>
            </div>

            <div class="card">
                <div class="card-body">
                    <h5 class="card-title gradient-text rose-decoration">Tìm kiếm</h5>
                    <form method="GET">
                        <div class="input-group">
                            <input type="text" class="form-control" name="search" placeholder="Tìm hoa..." value="<?php echo htmlspecialchars($search); ?>">
                            <button class="btn btn-outline-primary" type="submit">
                                <i class="fas fa-search"></i>
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>

        <!-- Products -->
        <div class="col-md-9">
            <h2 class="mb-4 gradient-text">
                <?php 
                if ($search) {
                    echo 'Kết quả tìm kiếm: ' . htmlspecialchars($search);
                } elseif ($category_id) {
                    $cat = $category->getCategoryById($category_id);
                    echo $cat['name'];
                } else {
                    echo 'Tất cả sản phẩm';
                }
                ?>
            </h2>

            <?php if (empty($products)): ?>
                <div class="alert alert-info">Không có sản phẩm nào.</div>
            <?php else: ?>
                <div class="row g-4">
                    <?php foreach ($products as $prod): ?>
                        <div class="col-md-4">
                            <div class="card product-card h-100">
                                <?php
                                // Get product image
                                $product_image = '';
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
                                
                                if (!empty($product_image)) {
                                    echo "<img src='$product_image' class='card-img-top product-image' alt='{$prod['name']}' style='height: 250px; object-fit: cover;' onerror=\"this.onerror=null; this.style.display='none'; this.parentElement.innerHTML='<div class=\\'card-img-top d-flex align-items-center justify-content-center bg-light\\' style=\\'height: 250px;\\'><i class=\\'fas fa-image fa-3x text-muted\\'></i></div>';\">";
                                } else {
                                    echo "<div class='card-img-top product-image d-flex align-items-center justify-content-center bg-light' style='height: 250px;'>";
                                    echo "<i class='fas fa-image fa-3x text-muted'></i>";
                                    echo "</div>";
                                }
                                ?>
                                <div class="card-body">
                                    <h5 class="card-title text-primary"><?php echo $prod['name']; ?></h5>
                                    <p class="card-text text-muted small"><?php echo substr($prod['description'], 0, 100) . '...'; ?></p>
                                    <div class="d-flex justify-content-between align-items-center">
                                        <span class="h5 mb-0 price-text"><?php echo number_format($prod['price'], 0, ',', '.'); ?>đ</span>
                                        <small class="text-muted">Đã bán: <?php echo $prod['sold_count']; ?></small>
                                    </div>
                                </div>
                                <div class="card-footer bg-transparent border-0">
                                    <a href="<?php echo APP_URL; ?>/views/products/detail.php?id=<?php echo $prod['product_id']; ?>" 
                                       class="btn btn-primary btn-sm w-100 rounded-pill">
                                        <i class="fas fa-eye"></i> Xem chi tiết
                                    </a>
                                </div>
                            </div>
                        </div>
                    <?php endforeach; ?>
                </div>

                <!-- Pagination -->
                <?php if ($total_pages > 1): ?>
                    <nav class="mt-5">
                        <ul class="pagination justify-content-center">
                            <?php for ($i = 1; $i <= $total_pages; $i++): ?>
                                <li class="page-item <?php echo $page == $i ? 'active' : ''; ?>">
                                    <a class="page-link" href="<?php echo APP_URL; ?>/views/products/index.php?page=<?php echo $i; ?><?php echo $category_id ? '&category=' . $category_id : ''; ?><?php echo $search ? '&search=' . urlencode($search) : ''; ?>">
                                        <?php echo $i; ?>
                                    </a>
                                </li>
                            <?php endfor; ?>
                        </ul>
                    </nav>
                <?php endif; ?>
            <?php endif; ?>
        </div>
    </div>
</div>

<?php include ROOT_DIR . '/views/layout/footer.php'; ?>
