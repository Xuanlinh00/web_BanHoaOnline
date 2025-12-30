<?php
require_once 'config/constants.php';
require_once 'config/session.php';
requireLogin();

$page_title = 'Hồ sơ cá nhân';
$conn = require 'config/database.php';
require_once 'models/User.php';

$user_model = new User($conn);
$user = $user_model->getUserById(getCurrentUserId());

$message = '';
$error = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Update profile
    $query = "UPDATE users SET full_name = ?, phone = ? WHERE user_id = ?";
    $stmt = $conn->prepare($query);
    $stmt->bind_param("ssi", $_POST['full_name'], $_POST['phone'], getCurrentUserId());
    
    if ($stmt->execute()) {
        $_SESSION['full_name'] = $_POST['full_name'];
        $message = 'Cập nhật hồ sơ thành công!';
        $user['full_name'] = $_POST['full_name'];
        $user['phone'] = $_POST['phone'];
    } else {
        $error = 'Lỗi cập nhật hồ sơ';
    }
}
?>
<?php include 'views/layout/header.php'; ?>

<div class="container">
    <div class="row">
        <div class="col-md-3">
            <div class="list-group">
                <a href="/web_banhoa/views/user/profile.php" class="list-group-item list-group-item-action active">
                    <i class="fas fa-user"></i> Hồ sơ
                </a>
                <a href="/web_banhoa/views/user/orders.php" class="list-group-item list-group-item-action">
                    <i class="fas fa-shopping-bag"></i> Đơn hàng
                </a>
                <a href="/web_banhoa/views/user/addresses.php" class="list-group-item list-group-item-action">
                    <i class="fas fa-map-marker-alt"></i> Địa chỉ
                </a>
            </div>
        </div>

        <div class="col-md-9">
            <div class="card">
                <div class="card-header">
                    <h5 class="mb-0">Thông tin cá nhân</h5>
                </div>
                <div class="card-body">
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

                    <form method="POST">
                        <div class="mb-3">
                            <label for="username" class="form-label">Tên đăng nhập</label>
                            <input type="text" class="form-control" id="username" value="<?php echo $user['username']; ?>" disabled>
                        </div>

                        <div class="mb-3">
                            <label for="email" class="form-label">Email</label>
                            <input type="email" class="form-control" id="email" value="<?php echo $user['email']; ?>" disabled>
                        </div>

                        <div class="mb-3">
                            <label for="full_name" class="form-label">Họ và tên</label>
                            <input type="text" class="form-control" id="full_name" name="full_name" value="<?php echo $user['full_name']; ?>" required>
                        </div>

                        <div class="mb-3">
                            <label for="phone" class="form-label">Số điện thoại</label>
                            <input type="tel" class="form-control" id="phone" name="phone" value="<?php echo $user['phone']; ?>">
                        </div>

                        <button type="submit" class="btn btn-primary">Cập nhật</button>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>

<?php include 'views/layout/footer.php'; ?>
