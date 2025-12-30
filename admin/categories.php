<?php
require_once 'config/constants.php';
require_once 'config/session.php';
requireAdmin();

$page_title = 'Quản lý danh mục';
$conn = require 'config/database.php';
require_once 'models/Category.php';

$category_model = new Category($conn);
$categories = $category_model->getAllCategories();

$message = '';
$error = '';

// Handle add
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['action'])) {
    if ($_POST['action'] === 'add') {
        $name = $_POST['name'];
        $description = $_POST['description'];
        
        $query = "INSERT INTO categories (name, description) VALUES (?, ?)";
        $stmt = $conn->prepare($query);
        $stmt->bind_param("ss", $name, $description);
        
        if ($stmt->execute()) {
            $message = 'Thêm danh mục thành công!';
            $categories = $category_model->getAllCategories();
        } else {
            $error = 'Lỗi: ' . $stmt->error;
        }
    } elseif ($_POST['action'] === 'edit') {
        $category_id = $_POST['category_id'];
        $name = $_POST['name'];
        $description = $_POST['description'];
        
        $query = "UPDATE categories SET name = ?, description = ? WHERE category_id = ?";
        $stmt = $conn->prepare($query);
        $stmt->bind_param("ssi", $name, $description, $category_id);
        
        if ($stmt->execute()) {
            $message = 'Cập nhật danh mục thành công!';
            $categories = $category_model->getAllCategories();
        } else {
            $error = 'Lỗi: ' . $stmt->error;
        }
    }
}

// Handle delete
if (isset($_GET['delete'])) {
    $category_id = (int)$_GET['delete'];
    $query = "DELETE FROM categories WHERE category_id = ?";
    $stmt = $conn->prepare($query);
    $stmt->bind_param("i", $category_id);
    if ($stmt->execute()) {
        $message = 'Xóa danh mục thành công!';
        $categories = $category_model->getAllCategories();
    } else {
        $error = 'Lỗi xóa danh mục';
    }
}
?>
<?php include 'views/layout/header.php'; ?>

<div class="container">
    <div class="row mb-4">
        <div class="col-md-12">
            <h2>Quản lý danh mục</h2>
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
        <div class="col-md-6">
            <div class="card mb-4">
                <div class="card-header">
                    <h5 class="mb-0">Thêm danh mục mới</h5>
                </div>
                <div class="card-body">
                    <form method="POST">
                        <input type="hidden" name="action" value="add">
                        <div class="mb-3">
                            <label for="name" class="form-label">Tên danh mục *</label>
                            <input type="text" class="form-control" id="name" name="name" required>
                        </div>
                        <div class="mb-3">
                            <label for="description" class="form-label">Mô tả</label>
                            <textarea class="form-control" id="description" name="description" rows="3"></textarea>
                        </div>
                        <button type="submit" class="btn btn-primary">
                            <i class="fas fa-plus"></i> Thêm danh mục
                        </button>
                    </form>
                </div>
            </div>
        </div>

        <div class="col-md-6">
            <div class="card">
                <div class="card-header">
                    <h5 class="mb-0">Danh sách danh mục</h5>
                </div>
                <div class="table-responsive">
                    <table class="table table-hover mb-0">
                        <thead class="table-light">
                            <tr>
                                <th>Tên</th>
                                <th>Mô tả</th>
                                <th>Hành động</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach ($categories as $cat): ?>
                                <tr>
                                    <td><?php echo htmlspecialchars($cat['name']); ?></td>
                                    <td><?php echo htmlspecialchars(substr($cat['description'], 0, 30)); ?></td>
                                    <td>
                                        <button class="btn btn-sm btn-warning" data-bs-toggle="modal" data-bs-target="#editModal<?php echo $cat['category_id']; ?>">
                                            <i class="fas fa-edit"></i>
                                        </button>
                                        <a href="/web_banhoa/admin/categories.php?delete=<?php echo $cat['category_id']; ?>" 
                                           class="btn btn-sm btn-danger" onclick="return confirm('Xóa danh mục này?')">
                                            <i class="fas fa-trash"></i>
                                        </a>
                                    </td>
                                </tr>

                                <!-- Edit Modal -->
                                <div class="modal fade" id="editModal<?php echo $cat['category_id']; ?>" tabindex="-1">
                                    <div class="modal-dialog">
                                        <div class="modal-content">
                                            <div class="modal-header">
                                                <h5 class="modal-title">Chỉnh sửa danh mục</h5>
                                                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                                            </div>
                                            <form method="POST">
                                                <div class="modal-body">
                                                    <input type="hidden" name="action" value="edit">
                                                    <input type="hidden" name="category_id" value="<?php echo $cat['category_id']; ?>">
                                                    <div class="mb-3">
                                                        <label for="edit_name<?php echo $cat['category_id']; ?>" class="form-label">Tên danh mục</label>
                                                        <input type="text" class="form-control" id="edit_name<?php echo $cat['category_id']; ?>" name="name" value="<?php echo htmlspecialchars($cat['name']); ?>" required>
                                                    </div>
                                                    <div class="mb-3">
                                                        <label for="edit_desc<?php echo $cat['category_id']; ?>" class="form-label">Mô tả</label>
                                                        <textarea class="form-control" id="edit_desc<?php echo $cat['category_id']; ?>" name="description" rows="3"><?php echo htmlspecialchars($cat['description']); ?></textarea>
                                                    </div>
                                                </div>
                                                <div class="modal-footer">
                                                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Hủy</button>
                                                    <button type="submit" class="btn btn-primary">Cập nhật</button>
                                                </div>
                                            </form>
                                        </div>
                                    </div>
                                </div>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</div>

<?php include 'views/layout/footer.php'; ?>
