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
        header('Location: /web_banhoa/index.php');
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
            <div class="card shadow-sm">
                <div class="card-body p-5">
                    <h2 class="card-title text-center mb-4">Đăng nhập</h2>

                    <?php if ($error): ?>
                        <div class="alert alert-danger alert-dismissible fade show" role="alert">
                            <?php echo $error; ?>
                            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                        </div>
                    <?php endif; ?>

                    <form method="POST">
                        <div class="mb-3">
                            <label for="username" class="form-label">Tên đăng nhập</label>
                            <input type="text" class="form-control" id="username" name="username" required>
                        </div>

                        <div class="mb-3">
                            <label for="password" class="form-label">Mật khẩu</label>
                            <input type="password" class="form-control" id="password" name="password" required>
                        </div>

                        <button type="submit" class="btn btn-primary w-100">Đăng nhập</button>
                    </form>

                    <hr>
                    <p class="text-center">Chưa có tài khoản? <a href="/web_banhoa/views/auth/register.php">Đăng ký ngay</a></p>
                </div>
            </div>
        </div>
    </div>
</div>

<?php include 'views/layout/footer.php'; ?>
