<?php
require_once 'config/constants.php';
require_once 'config/session.php';
requireAdmin();

$page_title = 'Quản lý sản phẩm';
$conn = require 'config/database.php';
require_once 'models/Product.php';
require_once 'models/Category.php';

$product_model = new Product($conn);
$category_model = new Category($conn);

$page = isset($_GET['page']) ? (int)$_GET['page'] : 1;
$products = $product_model->getAllProducts($page, 20);
$categories = $category_model->getAllCategories();

$message = '';
$error = '';

// Handle delete
if (isset($_GET['delete'])) {
    $query = "DELETE FROM products WHERE product_id = ?";
    $stmt = $conn->prepare($query);
    $stmt->bind_param("i", $_GET['delete']);
    if ($stmt->execute()) {
        $message = 'Xóa sản phẩm thành công!';
        $products = $product_model->getAllProducts($page, 20);
    } else {
        $error = 'Lỗi xóa sản phẩm';
    }
}
?>
<?php include 'views/layout/header.php'; ?>

<div class="container-fluid">
    <div class="row mb-4">
        <div class="col-md-12">
            <h2>Quản lý sản phẩm</h2>
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

    <div class="row mb-3">
        <div class="col-md-12">
            <a href="/web_banhoa/admin-product-add.php" class="btn btn-primary">
                <i class="fas fa-plus"></i> Thêm sản phẩm
            </a>
        </div>
    </div>

    <div class="card">
        <div class="table-responsive">
            <table class="table table-hover mb-0">
                <thead class="table-light">
                    <tr>
                        <th>Ảnh</th>
                        <th>Tên sản phẩm</th>
                        <th>Danh mục</th>
                        <th>Giá</th>
                        <th>Tồn kho</th>
                        <th>Đã bán</th>
                        <th>Trạng thái</th>
                        <th>Hành động</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($products as $prod): ?>
                        <tr>
                            <td>
                                <?php if ($prod['image_url']): ?>
                                    <img src="<?php echo $prod['image_url']; ?>" alt="<?php echo $prod['name']; ?>" 
                                         style="width: 50px; height: 50px; object-fit: cover; border-radius: 4px;">
                                <?php else: ?>
                                    <span class="text-muted">Không có ảnh</span>
                                <?php endif; ?>
                            </td>
                            <td><?php echo $prod['name']; ?></td>
                            <td><?php echo $prod['category_name']; ?></td>
                            <td><?php echo number_format($prod['price'], 0, ',', '.'); ?>đ</td>
                            <td><?php echo $prod['stock']; ?></td>
                            <td><?php echo $prod['sold_count']; ?></td>
                            <td>
                                <span class="badge bg-<?php echo $prod['status'] === 'available' ? 'success' : 'warning'; ?>">
                                    <?php echo $prod['status']; ?>
                                </span>
                            </td>
                            <td>
                                <a href="/web_banhoa/admin-product-edit.php?id=<?php echo $prod['product_id']; ?>" 
                                   class="btn btn-sm btn-warning">
                                    <i class="fas fa-edit"></i>
                                </a>
                                <a href="/web_banhoa/admin-products.php?delete=<?php echo $prod['product_id']; ?>" 
                                   class="btn btn-sm btn-danger" onclick="return confirm('Xóa sản phẩm này?')">
                                    <i class="fas fa-trash"></i>
                                </a>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>
    </div>
</div>

<?php include 'views/layout/footer.php'; ?>
