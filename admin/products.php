<?php
require_once __DIR__ . '/../config/constants.php';
require_once __DIR__ . '/../config/session.php';

$page_title = 'Quản lý sản phẩm';

// Require admin
requireAdmin();

$conn = require __DIR__ . '/../config/database.php';
require_once __DIR__ . '/../models/Product.php';
require_once __DIR__ . '/../models/Category.php';

$product_model = new Product($conn);
$category_model = new Category($conn);

$message = '';
$error = '';

// Handle actions
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (isset($_POST['delete_product'])) {
        $product_id = (int)$_POST['product_id'];
        $query = "DELETE FROM products WHERE product_id = ?";
        $stmt = $conn->prepare($query);
        $stmt->bind_param("i", $product_id);
        
        if ($stmt->execute()) {
            $message = 'Xóa sản phẩm thành công!';
        } else {
            $error = 'Có lỗi xảy ra khi xóa sản phẩm';
        }
    }
}

$page = isset($_GET['page']) ? (int)$_GET['page'] : 1;
$products = $product_model->getAllProducts($page, 20);
$categories = $category_model->getAllCategories();
?>
<?php include __DIR__ . '/../views/layout/header.php'; ?>

<div class="container-fluid">
    <div class="row">
        <div class="col-md-2">
            <div class="card">
                <div class="card-body">
                    <h6 class="card-title">Menu Admin</h6>
                    <div class="list-group list-group-flush">
                        <a href="<?php echo APP_URL; ?>/admin-dashboard.php" class="list-group-item list-group-item-action">
                            <i class="fas fa-tachometer-alt"></i> Dashboard
                        </a>
                        <a href="<?php echo APP_URL; ?>/admin-products.php" class="list-group-item list-group-item-action active">
                            <i class="fas fa-box"></i> Sản phẩm
                        </a>
                        <a href="<?php echo APP_URL; ?>/admin-orders.php" class="list-group-item list-group-item-action">
                            <i class="fas fa-shopping-cart"></i> Đơn hàng
                        </a>
                        <a href="<?php echo APP_URL; ?>/admin-reviews.php" class="list-group-item list-group-item-action">
                            <i class="fas fa-star"></i> Đánh giá
                        </a>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-md-10">
            <div class="d-flex justify-content-between align-items-center mb-4">
                <h2>Quản lý sản phẩm</h2>
                <a href="<?php echo APP_URL; ?>/admin-product-add.php" class="btn btn-primary">
                    <i class="fas fa-plus"></i> Thêm sản phẩm
                </a>
            </div>

            <?php if ($message): ?>
                <div class="alert alert-success alert-dismissible fade show" role="alert">
                    <?php echo $message; ?>
                    <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                </div>
            <?php endif; ?>

            <?php if ($error): ?>
                <div class="alert alert-danger alert-dismissible fade show" role="alert">
                    <?php echo $error; ?>
                    <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                </div>
            <?php endif; ?>

            <div class="card">
                <div class="card-body">
                    <?php if (empty($products)): ?>
                        <div class="text-center py-5">
                            <i class="fas fa-box fa-3x text-muted mb-3"></i>
                            <h5>Chưa có sản phẩm nào</h5>
                            <p class="text-muted">Hãy thêm sản phẩm đầu tiên</p>
                            <a href="<?php echo APP_URL; ?>/admin-product-add.php" class="btn btn-primary">
                                <i class="fas fa-plus"></i> Thêm sản phẩm
                            </a>
                        </div>
                    <?php else: ?>
                        <div class="table-responsive">
                            <table class="table">
                                <thead>
                                    <tr>
                                        <th>ID</th>
                                        <th>Hình ảnh</th>
                                        <th>Tên sản phẩm</th>
                                        <th>Danh mục</th>
                                        <th>Giá</th>
                                        <th>Tồn kho</th>
                                        <th>Trạng thái</th>
                                        <th>Thao tác</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php foreach ($products as $product): ?>
                                        <tr>
                                            <td><?php echo $product['product_id']; ?></td>
                                            <td>
                                                <img src="<?php echo $product['image_url'] ?? APP_URL . '/assets/images/placeholder.jpg'; ?>" 
                                                     class="img-thumbnail" style="width: 60px; height: 60px; object-fit: cover;" 
                                                     alt="<?php echo $product['name']; ?>">
                                            </td>
                                            <td>
                                                <strong><?php echo $product['name']; ?></strong>
                                                <br>
                                                <small class="text-muted"><?php echo substr($product['description'], 0, 50) . '...'; ?></small>
                                            </td>
                                            <td><?php echo $product['category_name'] ?? 'N/A'; ?></td>
                                            <td><?php echo number_format($product['price'], 0, ',', '.'); ?>đ</td>
                                            <td>
                                                <span class="badge <?php echo $product['stock'] > 0 ? 'bg-success' : 'bg-danger'; ?>">
                                                    <?php echo $product['stock']; ?>
                                                </span>
                                            </td>
                                            <td>
                                                <span class="badge <?php echo $product['status'] === 'available' ? 'bg-success' : 'bg-secondary'; ?>">
                                                    <?php echo $product['status'] === 'available' ? 'Có sẵn' : 'Không có sẵn'; ?>
                                                </span>
                                            </td>
                                            <td>
                                                <div class="btn-group" role="group">
                                                    <a href="<?php echo APP_URL; ?>/admin-product-edit.php?id=<?php echo $product['product_id']; ?>" 
                                                       class="btn btn-sm btn-outline-primary">
                                                        <i class="fas fa-edit"></i>
                                                    </a>
                                                    <form method="POST" class="d-inline">
                                                        <input type="hidden" name="product_id" value="<?php echo $product['product_id']; ?>">
                                                        <button type="submit" name="delete_product" class="btn btn-sm btn-outline-danger"
                                                                onclick="return confirm('Bạn có chắc muốn xóa sản phẩm này?')">
                                                            <i class="fas fa-trash"></i>
                                                        </button>
                                                    </form>
                                                </div>
                                            </td>
                                        </tr>
                                    <?php endforeach; ?>
                                </tbody>
                            </table>
                        </div>
                    <?php endif; ?>
                </div>
            </div>
        </div>
    </div>
</div>

<?php include __DIR__ . '/../views/layout/footer.php'; ?>