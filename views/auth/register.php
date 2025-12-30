<?php
require_once 'config/constants.php';
require_once 'config/session.php';
require_once 'config/database.php';
require_once 'models/User.php';

$page_title = 'Đăng ký';
$error = '';
$success = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $conn = require 'config/database.php';
    $user = new User($conn);

    // Validate input
    if (empty($_POST['username']) || empty($_POST['email']) || empty($_POST['password']) || empty($_POST['full_name'])) {
        $error = 'Vui lòng điền đầy đủ thông tin';
    } elseif ($user->usernameExists($_POST['username'])) {
        $error = 'Tên đăng nhập đã tồn tại';
    } elseif ($user->emailExists($_POST['email'])) {
        $error = 'Email đã được đăng ký';
    } else {
        $result = $user->register(
            $_POST['username'],
            $_POST['email'],
            $_POST['password'],
            $_POST['full_name'],
            $_POST['phone'] ?? ''
        );

        if ($result['success']) {
            $success = 'Đăng ký thành công! Vui lòng đăng nhập.';
        } else {
            $error = $result['message'];
        }
    }
}
?>
<?php include 'views/layout/header.php'; ?>

<div class="container">
    <div class="row justify-content-center">
        <div class="col-md-6">
            <div class="card soft-shadow rounded-lg border-0">
                <div class="card-body p-5">
                    <div class="text-center mb-4">
                        <i class="fas fa-user-plus text-primary" style="font-size: 3rem;"></i>
                        <h2 class="card-title gradient-text flower-decoration mt-3">Đăng ký tài khoản</h2>
                        <p class="text-muted">Tham gia cùng chúng tôi!</p>
                    </div>

                    <?php if ($error): ?>
                        <div class="alert alert-danger alert-dismissible fade show" role="alert">
                            <i class="fas fa-exclamation-circle"></i> <?php echo $error; ?>
                            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                        </div>
                    <?php endif; ?>

                    <?php if ($success): ?>
                        <div class="alert alert-success alert-dismissible fade show" role="alert">
                            <i class="fas fa-check-circle"></i> <?php echo $success; ?>
                            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                        </div>
                    <?php endif; ?>

                    <form method="POST">
                        <div class="mb-3">
                            <label for="username" class="form-label text-primary">
                                <i class="fas fa-user"></i> Tên đăng nhập
                            </label>
                            <input type="text" class="form-control rounded-pill" id="username" name="username" required>
                        </div>

                        <div class="mb-3">
                            <label for="email" class="form-label text-primary">
                                <i class="fas fa-envelope"></i> Email
                            </label>
                            <input type="email" class="form-control rounded-pill" id="email" name="email" required>
                        </div>

                        <div class="mb-3">
                            <label for="full_name" class="form-label text-primary">
                                <i class="fas fa-id-card"></i> Họ và tên
                            </label>
                            <input type="text" class="form-control rounded-pill" id="full_name" name="full_name" required>
                        </div>

                        <div class="mb-3">
                            <label for="phone" class="form-label text-primary">
                                <i class="fas fa-phone"></i> Số điện thoại
                            </label>
                            <input type="tel" class="form-control rounded-pill" id="phone" name="phone">
                        </div>

                        <div class="mb-4">
                            <label for="password" class="form-label text-primary">
                                <i class="fas fa-lock"></i> Mật khẩu
                            </label>
                            <input type="password" class="form-control rounded-pill" id="password" name="password" required>
                        </div>

                        <button type="submit" class="btn btn-primary w-100 rounded-pill py-2">
                            <i class="fas fa-user-plus"></i> Đăng ký
                        </button>
                    </form>

                    <hr class="my-4">
                    <div class="text-center">
                        <p class="text-muted">Đã có tài khoản? 
                            <a href="<?php echo APP_URL; ?>/login.php" class="text-primary text-decoration-none fw-bold">
                                Đăng nhập <i class="fas fa-arrow-right"></i>
                            </a>
                        </p>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<?php include 'views/layout/footer.php'; ?>
