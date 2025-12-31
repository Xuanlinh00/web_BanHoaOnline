<?php
require_once __DIR__ . '/../config/constants.php';
require_once __DIR__ . '/../config/session.php';

$page_title = 'Sửa sản phẩm';
requireAdmin();

if (!isset($_GET['id'])) {
    header('Location: ' . APP_URL . '/admin-products.php');
    exit;
}

$conn = require __DIR__ . '/../config/database.php';
require_once __DIR__ . '/../models/Product.php';
require_once __DIR__ . '/../models/Category.php';

$product_model = new Product($conn);
$category_model = new Category($conn);

$product_id = (int)$_GET['id'];
$product = $product_model->getProductById($product_id);

if (!$product) {
    header('Location: ' . APP_URL . '/admin-products.php');
    exit;
}

$categories = $category_model->getAllCategories();
$message = '';
$error = '';

// Handle form submission
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $name = trim($_POST['name'] ?? '');
    $description = trim($_POST['description'] ?? '');
    $price = (float)($_POST['price'] ?? 0);
    $stock = (int)($_POST['stock'] ?? 0);
    $category_id = !empty($_POST['category_id']) ? (int)$_POST['category_id'] : null;
    $status = $_POST['status'] ?? 'available';
    
    if (empty($name) || empty($description) || $price <= 0) {
        $error = 'Vui lòng điền đầy đủ thông tin bắt buộc';
    } else {
        try {
            // Update basic product info
            $query = "UPDATE products SET name = ?, description = ?, price = ?, stock = ?, 
                      category_id = ?, status = ? WHERE product_id = ?";
            $stmt = $conn->prepare($query);
            
            if (!$stmt) {
                throw new Exception('Lỗi prepare: ' . $conn->error);
            }
            
            $stmt->bind_param("ssdiisi", $name, $description, $price, $stock, $category_id, $status, $product_id);
            
            if (!$stmt->execute()) {
                throw new Exception('Lỗi khi cập nhật sản phẩm: ' . $stmt->error);
            }
            
            $message = 'Cập nhật sản phẩm thành công!';
            
            // Refresh product data
            $product = $product_model->getProductById($product_id);
            
        } catch (Exception $e) {
            $error = $e->getMessage();
        }
    }
}
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
                <h2>Sửa sản phẩm: <?php echo htmlspecialchars($product['name']); ?></h2>
                <a href="<?php echo APP_URL; ?>/admin-products.php" class="btn btn-secondary">
                    <i class="fas fa-arrow-left"></i> Quay lại
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
                    <form method="POST" enctype="multipart/form-data">
                        <div class="mb-3">
                            <label for="name" class="form-label">Tên sản phẩm *</label>
                            <input type="text" class="form-control" id="name" name="name" 
                                   value="<?php echo htmlspecialchars($product['name']); ?>" required>
                        </div>

                        <div class="mb-3">
                            <label for="description" class="form-label">Mô tả *</label>
                            <textarea class="form-control" id="description" name="description" rows="5" required><?php echo htmlspecialchars($product['description']); ?></textarea>
                        </div>

                        <div class="row">
                            <div class="col-md-4">
                                <div class="mb-3">
                                    <label for="price" class="form-label">Giá (VNĐ) *</label>
                                    <input type="number" class="form-control" id="price" name="price" 
                                           value="<?php echo $product['price']; ?>" min="0" step="1000" required>
                                </div>
                            </div>
                            <div class="col-md-4">
                                <div class="mb-3">
                                    <label for="stock" class="form-label">Tồn kho</label>
                                    <input type="number" class="form-control" id="stock" name="stock" 
                                           value="<?php echo $product['stock']; ?>" min="0">
                                </div>
                            </div>
                            <div class="col-md-4">
                                <div class="mb-3">
                                    <label for="category_id" class="form-label">Danh mục</label>
                                    <select class="form-select" id="category_id" name="category_id">
                                        <option value="">Chọn danh mục</option>
                                        <?php foreach ($categories as $cat): ?>
                                            <option value="<?php echo $cat['category_id']; ?>" 
                                                    <?php echo $product['category_id'] == $cat['category_id'] ? 'selected' : ''; ?>>
                                                <?php echo $cat['name']; ?>
                                            </option>
                                        <?php endforeach; ?>
                                    </select>
                                </div>
                            </div>
                        </div>

                        <div class="mb-3">
                            <label for="status" class="form-label">Trạng thái</label>
                            <select class="form-select" id="status" name="status">
                                <option value="available" <?php echo $product['status'] === 'available' ? 'selected' : ''; ?>>Có sẵn</option>
                                <option value="unavailable" <?php echo $product['status'] === 'unavailable' ? 'selected' : ''; ?>>Không có sẵn</option>
                            </select>
                        </div>

                        <hr>
                        <div class="d-flex justify-content-end">
                            <a href="<?php echo APP_URL; ?>/admin-products.php" class="btn btn-secondary me-2">Hủy</a>
                            <button type="submit" class="btn btn-primary">
                                <i class="fas fa-save"></i> Cập nhật sản phẩm
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>

<?php include __DIR__ . '/../views/layout/footer.php'; ?>
