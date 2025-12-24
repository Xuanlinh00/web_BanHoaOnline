<?php
require_once 'config/constants.php';
require_once 'config/session.php';
requireAdmin();

$page_title = 'Thêm sản phẩm';
$conn = require 'config/database.php';
require_once 'models/Category.php';

$category_model = new Category($conn);
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
    $image_url = '';
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
        $query = "INSERT INTO products (name, description, price, stock, category_id, image_url, status) 
                  VALUES (?, ?, ?, ?, ?, ?, ?)";
        $stmt = $conn->prepare($query);
        $stmt->bind_param("ssdiiis", $name, $description, $price, $stock, $category_id, $image_url, $status);

        if ($stmt->execute()) {
            $product_id = $conn->insert_id;
            $message = 'Thêm sản phẩm thành công!';
            
            // Redirect after 2 seconds
            header('Refresh: 2; url=/web_banhoa/admin-products.php');
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
            <h2>Thêm sản phẩm mới</h2>
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
                            <input type="text" class="form-control" id="name" name="name" required>
                        </div>

                        <div class="mb-3">
                            <label for="description" class="form-label">Mô tả</label>
                            <textarea class="form-control" id="description" name="description" rows="4"></textarea>
                        </div>

                        <div class="row">
                            <div class="col-md-6 mb-3">
                                <label for="price" class="form-label">Giá (đ) *</label>
                                <input type="number" class="form-control" id="price" name="price" step="1000" required>
                            </div>
                            <div class="col-md-6 mb-3">
                                <label for="stock" class="form-label">Tồn kho *</label>
                                <input type="number" class="form-control" id="stock" name="stock" required>
                            </div>
                        </div>

                        <div class="row">
                            <div class="col-md-6 mb-3">
                                <label for="category_id" class="form-label">Danh mục *</label>
                                <select class="form-select" id="category_id" name="category_id" required>
                                    <option value="">-- Chọn danh mục --</option>
                                    <?php foreach ($categories as $cat): ?>
                                        <option value="<?php echo $cat['category_id']; ?>"><?php echo $cat['name']; ?></option>
                                    <?php endforeach; ?>
                                </select>
                            </div>
                            <div class="col-md-6 mb-3">
                                <label for="status" class="form-label">Trạng thái *</label>
                                <select class="form-select" id="status" name="status" required>
                                    <option value="available">Có sẵn</option>
                                    <option value="out_of_stock">Hết hàng</option>
                                    <option value="discontinued">Ngừng bán</option>
                                </select>
                            </div>
                        </div>

                        <div class="mb-3">
                            <label for="image" class="form-label">Upload ảnh</label>
                            <input type="file" class="form-control" id="image" name="image" accept="image/*">
                            <small class="text-muted">Hoặc nhập URL ảnh bên dưới</small>
                        </div>

                        <div class="mb-3">
                            <label for="image_url" class="form-label">URL ảnh (nếu không upload)</label>
                            <input type="url" class="form-control" id="image_url" name="image_url" 
                                   placeholder="https://via.placeholder.com/300x300">
                        </div>

                        <div class="mb-3">
                            <button type="submit" class="btn btn-primary">
                                <i class="fas fa-save"></i> Thêm sản phẩm
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
                    <h5 class="mb-0">Hướng dẫn</h5>
                </div>
                <div class="card-body">
                    <p><strong>Lưu ý:</strong></p>
                    <ul>
                        <li>Tên sản phẩm là bắt buộc</li>
                        <li>Giá phải lớn hơn 0</li>
                        <li>Tồn kho phải là số nguyên dương</li>
                        <li>Có thể upload ảnh hoặc nhập URL</li>
                        <li>Định dạng ảnh: JPG, PNG, GIF</li>
                        <li>Kích thước tối đa: 5MB</li>
                    </ul>
                </div>
            </div>
        </div>
    </div>
</div>

<?php include 'views/layout/footer.php'; ?>
