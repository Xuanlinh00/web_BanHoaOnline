<?php
require_once 'config/constants.php';
require_once 'config/session.php';
requireAdmin();

$page_title = 'Chỉnh sửa sản phẩm';
$conn = require 'config/database.php';
require_once 'models/Category.php';
require_once 'models/Product.php';

$product_model = new Product($conn);
$category_model = new Category($conn);

$product_id = isset($_GET['id']) ? (int)$_GET['id'] : 0;
$product = $product_model->getProductById($product_id);

if (!$product) {
    header('Location: /web_banhoa/admin-products.php');
    exit;
}

$categories = $category_model->getAllCategories();
$message = '';
$error = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $name = $_POST['name'];
    $description = $_POST['description'];
    $price = $_POST['price'];
    $stock = $_POST['stock'];
    $category_id = $_POST['category_id'];
    $status = $_POST['status'];
    
    // Handle image upload
    $image_url = $product['image_url'];
    if (isset($_FILES['image']) && $_FILES['image']['error'] === UPLOAD_ERR_OK) {
        $upload_dir = getcwd() . '/uploads/products/';
        if (!is_dir($upload_dir)) {
            mkdir($upload_dir, 0755, true);
        }
        
        $file_name = time() . '_' . basename($_FILES['image']['name']);
        $file_path = $upload_dir . $file_name;
        
        if (move_uploaded_file($_FILES['image']['tmp_name'], $file_path)) {
            $image_url = '/web_banhoa/uploads/products/' . $file_name;
        } else {
            $error = 'Lỗi upload ảnh: ' . $_FILES['image']['error'];
        }
    } elseif (!empty($_POST['image_url'])) {
        $image_url = $_POST['image_url'];
    }

    if (!$error) {
        $query = "UPDATE products SET name = ?, description = ?, price = ?, stock = ?, category_id = ?, image_url = ?, status = ? WHERE product_id = ?";
        $stmt = $conn->prepare($query);
        $stmt->bind_param("ssdiiisi", $name, $description, $price, $stock, $category_id, $image_url, $status, $product_id);

        if ($stmt->execute()) {
            $message = 'Cập nhật sản phẩm thành công!';
            $product = $product_model->getProductById($product_id);
        } else {
            $error = 'Lỗi: ' . $stmt->error;
        }
    }
}
?>
<?php include 'views/layout/header.php'; ?>

<div class="container">
    <div class="row mb-4">
        <div class="col-md-12">
            <a href="/web_banhoa/admin-products.php" class="btn btn-outline-secondary mb-3">
                <i class="fas fa-arrow-left"></i> Quay lại
            </a>
            <h2>Chỉnh sửa sản phẩm</h2>
        </div>
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

    <div class="row">
        <div class="col-md-8">
            <div class="card">
                <div class="card-body">
                    <form method="POST" enctype="multipart/form-data">
                        <div class="mb-3">
                            <label for="name" class="form-label">Tên sản phẩm *</label>
                            <input type="text" class="form-control" id="name" name="name" value="<?php echo htmlspecialchars($product['name']); ?>" required>
                        </div>

                        <div class="mb-3">
                            <label for="description" class="form-label">Mô tả</label>
                            <textarea class="form-control" id="description" name="description" rows="4"><?php echo htmlspecialchars($product['description']); ?></textarea>
                        </div>

                        <div class="row">
                            <div class="col-md-6 mb-3">
                                <label for="price" class="form-label">Giá (đ) *</label>
                                <input type="number" class="form-control" id="price" name="price" step="1000" value="<?php echo $product['price']; ?>" required>
                            </div>
                            <div class="col-md-6 mb-3">
                                <label for="stock" class="form-label">Tồn kho *</label>
                                <input type="number" class="form-control" id="stock" name="stock" value="<?php echo $product['stock']; ?>" required>
                            </div>
                        </div>

                        <div class="row">
                            <div class="col-md-6 mb-3">
                                <label for="category_id" class="form-label">Danh mục *</label>
                                <select class="form-select" id="category_id" name="category_id" required>
                                    <option value="">-- Chọn danh mục --</option>
                                    <?php foreach ($categories as $cat): ?>
                                        <option value="<?php echo $cat['category_id']; ?>" <?php echo $cat['category_id'] == $product['category_id'] ? 'selected' : ''; ?>>
                                            <?php echo $cat['name']; ?>
                                        </option>
                                    <?php endforeach; ?>
                                </select>
                            </div>
                            <div class="col-md-6 mb-3">
                                <label for="status" class="form-label">Trạng thái *</label>
                                <select class="form-select" id="status" name="status" required>
                                    <option value="available" <?php echo $product['status'] === 'available' ? 'selected' : ''; ?>>Có sẵn</option>
                                    <option value="out_of_stock" <?php echo $product['status'] === 'out_of_stock' ? 'selected' : ''; ?>>Hết hàng</option>
                                    <option value="discontinued" <?php echo $product['status'] === 'discontinued' ? 'selected' : ''; ?>>Ngừng bán</option>
                                </select>
                            </div>
                        </div>

                        <div class="mb-3">
                            <label class="form-label">Ảnh hiện tại</label>
                            <?php if ($product['image_url']): ?>
                                <div class="mb-2">
                                    <img src="<?php echo $product['image_url']; ?>" alt="<?php echo $product['name']; ?>" style="max-width: 200px; border-radius: 8px;">
                                </div>
                            <?php endif; ?>
                        </div>

                        <div class="mb-3">
                            <label for="image" class="form-label">Thay đổi ảnh</label>
                            <input type="file" class="form-control" id="image" name="image" accept="image/*">
                            <small class="text-muted">Để trống nếu không muốn thay đổi</small>
                        </div>

                        <div class="mb-3">
                            <label for="image_url" class="form-label">URL ảnh (nếu không upload)</label>
                            <input type="url" class="form-control" id="image_url" name="image_url" value="<?php echo htmlspecialchars($product['image_url']); ?>" 
                                   placeholder="https://via.placeholder.com/300x300">
                        </div>

                        <div class="mb-3">
                            <button type="submit" class="btn btn-primary">
                                <i class="fas fa-save"></i> Cập nhật sản phẩm
                            </button>
                            <a href="/web_banhoa/admin-products.php" class="btn btn-outline-secondary">
                                Hủy
                            </a>
                        </div>
                    </form>
                </div>
            </div>
        </div>

        <div class="col-md-4">
            <div class="card">
                <div class="card-header">
                    <h5 class="mb-0">Thông tin sản phẩm</h5>
                </div>
                <div class="card-body">
                    <p><strong>ID:</strong> <?php echo $product['product_id']; ?></p>
                    <p><strong>Đã bán:</strong> <?php echo $product['sold_count']; ?></p>
                    <p><strong>Lượt xem:</strong> <?php echo $product['view_count']; ?></p>
                    <p><strong>Ngày tạo:</strong> <?php echo date('d/m/Y H:i', strtotime($product['created_at'])); ?></p>
                </div>
            </div>
        </div>
    </div>
</div>

<?php include 'views/layout/footer.php'; ?>
