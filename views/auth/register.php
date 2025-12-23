<?php
require_once '../../config/constants.php';
require_once '../../config/session.php';

$page_title = 'Đăng ký';
$error = '';
$success = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    require_once '../../config/database.php';
    require_once '../../models/User.php';

    $conn = require '../../config/database.php';
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
<?php include '../../views/layout/header.php'; ?>

<div class="container">
    <div class="row justify-content-center">
        <div class="col-md-6">
            <div class="card shadow-sm">
                <div class="card-body p-5">
                    <h2 class="card-title text-center mb-4">Đăng ký tài khoản</h2>

                    <?php if ($error): ?>
                        <div class="alert alert-danger alert-dismissible fade show" role="alert">
                            <?php echo $error; ?>
                            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                        </div>
                    <?php endif; ?>

                    <?php if ($success): ?>
                        <div class="alert alert-success alert-dismissible fade show" role="alert">
                            <?php echo $success; ?>
                            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                        </div>
                    <?php endif; ?>

                    <form method="POST">
                        <div class="mb-3">
                            <label for="username" class="form-label">Tên đăng nhập</label>
                            <input type="text" class="form-control" id="username" name="username" required>
                        </div>

                        <div class="mb-3">
                            <label for="email" class="form-label">Email</label>
                            <input type="email" class="form-control" id="email" name="email" required>
                        </div>

                        <div class="mb-3">
                            <label for="full_name" class="form-label">Họ và tên</label>
                            <input type="text" class="form-control" id="full_name" name="full_name" required>
                        </div>

                        <div class="mb-3">
                            <label for="phone" class="form-label">Số điện thoại</label>
                            <input type="tel" class="form-control" id="phone" name="phone">
                        </div>

                        <div class="mb-3">
                            <label for="password" class="form-label">Mật khẩu</label>
                            <input type="password" class="form-control" id="password" name="password" required>
                        </div>

                        <button type="submit" class="btn btn-primary w-100">Đăng ký</button>
                    </form>

                    <hr>
                    <p class="text-center">Đã có tài khoản? <a href="<?php echo APP_URL; ?>/auth/login.php">Đăng nhập</a></p>
                </div>
            </div>
        </div>
    </div>
</div>

<?php include '../../views/layout/footer.php'; ?>
