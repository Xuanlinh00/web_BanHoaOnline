<?php
require_once 'config/constants.php';
require_once 'config/session.php';
require_once 'config/database.php';
require_once 'models/User.php';

$page_title = 'Đăng nhập';
$error = '';
$success = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $conn = require 'config/database.php';
    $user = new User($conn);

    $result = $user->login($_POST['username'], $_POST['password']);
    
    if ($result['success']) {
        header('Location: ' . APP_URL . '/index.php');
        exit;
    } else {
        $error = $result['message'];
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
                        <i class="fas fa-user-circle text-primary" style="font-size: 3rem;"></i>
                        <h2 class="card-title gradient-text flower-decoration mt-3">Đăng nhập</h2>
                        <p class="text-muted">Chào mừng bạn trở lại!</p>
                    </div>

                    <?php if ($error): ?>
                        <div class="alert alert-danger alert-dismissible fade show" role="alert">
                            <i class="fas fa-exclamation-circle"></i> <?php echo $error; ?>
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

                        <div class="mb-4">
                            <label for="password" class="form-label text-primary">
                                <i class="fas fa-lock"></i> Mật khẩu
                            </label>
                            <input type="password" class="form-control rounded-pill" id="password" name="password" required>
                        </div>

                        <button type="submit" class="btn btn-primary w-100 rounded-pill py-2">
                            <i class="fas fa-sign-in-alt"></i> Đăng nhập
                        </button>
                    </form>

<<<<<<< HEAD
                    <hr class="my-4">
                    <div class="text-center">
                        <p class="text-muted">Chưa có tài khoản? 
                            <a href="<?php echo APP_URL; ?>/register.php" class="text-primary text-decoration-none fw-bold">
                                Đăng ký ngay <i class="fas fa-arrow-right"></i>
                            </a>
                        </p>
                    </div>
=======
                    <hr>
                    <p class="text-center">Chưa có tài khoản? <a href="/web_banhoa/views/auth/register.php">Đăng ký ngay</a></p>
>>>>>>> 37c17f0dac4bb260a987b53f0f92d6e4a0c6a329
                </div>
            </div>
        </div>
    </div>
</div>

<?php include 'views/layout/footer.php'; ?>
