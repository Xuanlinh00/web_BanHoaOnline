<?php
require_once 'config/constants.php';
require_once 'config/session.php';

$page_title = 'Hồ sơ cá nhân';

// Require login
requireLogin();

$conn = require 'config/database.php';
require_once 'models/User.php';

$user_model = new User($conn);
$user_id = getCurrentUserId();
$user = $user_model->getUserById($user_id);

$message = '';
$error = '';

// Handle profile update
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $full_name = $_POST['full_name'] ?? '';
    $email = $_POST['email'] ?? '';
    $phone = $_POST['phone'] ?? '';
    $current_password = $_POST['current_password'] ?? '';
    $new_password = $_POST['new_password'] ?? '';
    $confirm_password = $_POST['confirm_password'] ?? '';

    if (empty($full_name) || empty($email)) {
        $error = 'Vui lòng điền đầy đủ thông tin bắt buộc';
    } elseif ($email !== $user['email'] && $user_model->emailExists($email)) {
        $error = 'Email đã được sử dụng bởi tài khoản khác';
    } else {
        // Update basic info
        $query = "UPDATE users SET full_name = ?, email = ?, phone = ? WHERE user_id = ?";
        $stmt = $conn->prepare($query);
        $stmt->bind_param("sssi", $full_name, $email, $phone, $user_id);
        
        if ($stmt->execute()) {
            $_SESSION['full_name'] = $full_name;
            $_SESSION['email'] = $email;
            
            // Update password if provided
            if (!empty($new_password)) {
                if (empty($current_password)) {
                    $error = 'Vui lòng nhập mật khẩu hiện tại';
                } elseif ($new_password !== $confirm_password) {
                    $error = 'Mật khẩu xác nhận không khớp';
                } else {
                    // Verify current password
                    $pass_query = "SELECT password_hash FROM users WHERE user_id = ?";
                    $pass_stmt = $conn->prepare($pass_query);
                    $pass_stmt->bind_param("i", $user_id);
                    $pass_stmt->execute();
                    $pass_result = $pass_stmt->get_result()->fetch_assoc();
                    
                    if (password_verify($current_password, $pass_result['password_hash'])) {
                        $new_hash = password_hash($new_password, PASSWORD_BCRYPT);
                        $update_pass_query = "UPDATE users SET password_hash = ? WHERE user_id = ?";
                        $update_pass_stmt = $conn->prepare($update_pass_query);
                        $update_pass_stmt->bind_param("si", $new_hash, $user_id);
                        $update_pass_stmt->execute();
                        
                        $message = 'Cập nhật thông tin và mật khẩu thành công!';
                    } else {
                        $error = 'Mật khẩu hiện tại không đúng';
                    }
                }
            } else {
                $message = 'Cập nhật thông tin thành công!';
            }
            
            // Refresh user data
            $user = $user_model->getUserById($user_id);
        } else {
            $error = 'Có lỗi xảy ra khi cập nhật thông tin';
        }
    }
}
?>
<?php include 'views/layout/header.php'; ?>

<div class="container">
    <div class="row">
        <div class="col-md-3">
<<<<<<< HEAD
            <div class="card">
                <div class="card-body">
                    <h5 class="card-title">Tài khoản của tôi</h5>
                    <div class="list-group list-group-flush">
                        <a href="<?php echo APP_URL; ?>/profile.php" class="list-group-item list-group-item-action active">
                            <i class="fas fa-user"></i> Hồ sơ
                        </a>
                        <a href="<?php echo APP_URL; ?>/orders.php" class="list-group-item list-group-item-action">
                            <i class="fas fa-shopping-bag"></i> Đơn hàng
                        </a>
                        <a href="<?php echo APP_URL; ?>/addresses.php" class="list-group-item list-group-item-action">
                            <i class="fas fa-map-marker-alt"></i> Địa chỉ
                        </a>
                    </div>
                </div>
=======
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
>>>>>>> 37c17f0dac4bb260a987b53f0f92d6e4a0c6a329
            </div>
        </div>

        <div class="col-md-9">
            <div class="card">
                <div class="card-body">
                    <h5 class="card-title">Hồ sơ cá nhân</h5>

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
                        <div class="row">
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label for="username" class="form-label">Tên đăng nhập</label>
                                    <input type="text" class="form-control" id="username" value="<?php echo $user['username']; ?>" disabled>
                                    <div class="form-text">Tên đăng nhập không thể thay đổi</div>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label for="full_name" class="form-label">Họ và tên *</label>
                                    <input type="text" class="form-control" id="full_name" name="full_name" 
                                           value="<?php echo $user['full_name']; ?>" required>
                                </div>
                            </div>
                        </div>

                        <div class="row">
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label for="email" class="form-label">Email *</label>
                                    <input type="email" class="form-control" id="email" name="email" 
                                           value="<?php echo $user['email']; ?>" required>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label for="phone" class="form-label">Số điện thoại</label>
                                    <input type="tel" class="form-control" id="phone" name="phone" 
                                           value="<?php echo $user['phone']; ?>">
                                </div>
                            </div>
                        </div>

                        <hr>
                        <h6>Đổi mật khẩu</h6>
                        <p class="text-muted small">Để trống nếu không muốn đổi mật khẩu</p>

                        <div class="row">
                            <div class="col-md-4">
                                <div class="mb-3">
                                    <label for="current_password" class="form-label">Mật khẩu hiện tại</label>
                                    <input type="password" class="form-control" id="current_password" name="current_password">
                                </div>
                            </div>
                            <div class="col-md-4">
                                <div class="mb-3">
                                    <label for="new_password" class="form-label">Mật khẩu mới</label>
                                    <input type="password" class="form-control" id="new_password" name="new_password">
                                </div>
                            </div>
                            <div class="col-md-4">
                                <div class="mb-3">
                                    <label for="confirm_password" class="form-label">Xác nhận mật khẩu</label>
                                    <input type="password" class="form-control" id="confirm_password" name="confirm_password">
                                </div>
                            </div>
                        </div>

                        <button type="submit" class="btn btn-primary">
                            <i class="fas fa-save"></i> Cập nhật thông tin
                        </button>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>

<?php include 'views/layout/footer.php'; ?>