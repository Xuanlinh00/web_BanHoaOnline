<?php
require_once 'config/constants.php';
require_once 'config/session.php';

$page_title = 'Liên hệ';
$message = '';
$error = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $name = $_POST['name'] ?? '';
    $email = $_POST['email'] ?? '';
    $phone = $_POST['phone'] ?? '';
    $subject = $_POST['subject'] ?? '';
    $message_text = $_POST['message'] ?? '';

    if (empty($name) || empty($email) || empty($subject) || empty($message_text)) {
        $error = 'Vui lòng điền đầy đủ thông tin';
    } else {
        // Gửi email (có thể tích hợp với email service sau)
        $message = 'Cảm ơn bạn đã liên hệ! Chúng tôi sẽ phản hồi sớm nhất có thể.';
    }
}
?>
<?php include 'views/layout/header.php'; ?>

<div class="container">
    <div class="row mb-5">
        <div class="col-md-12">
            <h1 class="mb-4 fw-bold" style="color: var(--primary-color);">📞 Liên hệ với chúng tôi</h1>
        </div>
    </div>

    <div class="row mb-5">
        <div class="col-md-4 mb-3">
            <div class="card h-100">
                <div class="card-body text-center">
                    <i class="fas fa-phone" style="font-size: 2.5rem; color: var(--primary-color); margin-bottom: 1rem;"></i>
                    <h5 class="card-title fw-bold">Điện thoại</h5>
                    <p class="card-text">0123 456 789</p>
                    <p class="card-text text-muted small">Thứ 2 - Chủ nhật: 8:00 - 20:00</p>
                </div>
            </div>
        </div>
        <div class="col-md-4 mb-3">
            <div class="card h-100">
                <div class="card-body text-center">
                    <i class="fas fa-envelope" style="font-size: 2.5rem; color: var(--secondary-color); margin-bottom: 1rem;"></i>
                    <h5 class="card-title fw-bold">Email</h5>
                    <p class="card-text">info@webbanhoa.com</p>
                    <p class="card-text text-muted small">Phản hồi trong 24 giờ</p>
                </div>
            </div>
        </div>
        <div class="col-md-4 mb-3">
            <div class="card h-100">
                <div class="card-body text-center">
                    <i class="fas fa-map-marker-alt" style="font-size: 2.5rem; color: var(--accent-color); margin-bottom: 1rem;"></i>
                    <h5 class="card-title fw-bold">Địa chỉ</h5>
                    <p class="card-text">123 Đường Hoa, Quận 1</p>
                    <p class="card-text text-muted small">TP. Hồ Chí Minh, Việt Nam</p>
                </div>
            </div>
        </div>
    </div>

    <div class="row">
        <div class="col-md-8">
            <div class="card">
                <div class="card-header">
                    <h5 class="mb-0 fw-bold" style="color: var(--primary-color);">Gửi tin nhắn cho chúng tôi</h5>
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
                        <div class="row">
                            <div class="col-md-6 mb-3">
                                <label for="name" class="form-label">Tên của bạn *</label>
                                <input type="text" class="form-control" id="name" name="name" required>
                            </div>
                            <div class="col-md-6 mb-3">
                                <label for="email" class="form-label">Email *</label>
                                <input type="email" class="form-control" id="email" name="email" required>
                            </div>
                        </div>

                        <div class="mb-3">
                            <label for="phone" class="form-label">Số điện thoại</label>
                            <input type="tel" class="form-control" id="phone" name="phone">
                        </div>

                        <div class="mb-3">
                            <label for="subject" class="form-label">Tiêu đề *</label>
                            <input type="text" class="form-control" id="subject" name="subject" required>
                        </div>

                        <div class="mb-3">
                            <label for="message" class="form-label">Nội dung tin nhắn *</label>
                            <textarea class="form-control" id="message" name="message" rows="5" required></textarea>
                        </div>

                        <button type="submit" class="btn btn-primary btn-lg w-100">
                            <i class="fas fa-paper-plane"></i> Gửi tin nhắn
                        </button>
                    </form>
                </div>
            </div>
        </div>

        <div class="col-md-4">
            <div class="card mb-3">
                <div class="card-header">
                    <h5 class="mb-0 fw-bold" style="color: var(--primary-color);">Giờ làm việc</h5>
                </div>
                <div class="card-body">
                    <p><strong>Thứ 2 - Thứ 6:</strong> 8:00 - 18:00</p>
                    <p><strong>Thứ 7:</strong> 8:00 - 17:00</p>
                    <p><strong>Chủ nhật:</strong> 9:00 - 16:00</p>
                </div>
            </div>

            <div class="card">
                <div class="card-header">
                    <h5 class="mb-0 fw-bold" style="color: var(--primary-color);">Theo dõi chúng tôi</h5>
                </div>
                <div class="card-body text-center">
                    <a href="#" class="btn btn-outline-primary btn-sm me-2"><i class="fab fa-facebook"></i></a>
                    <a href="#" class="btn btn-outline-primary btn-sm me-2"><i class="fab fa-instagram"></i></a>
                    <a href="#" class="btn btn-outline-primary btn-sm me-2"><i class="fab fa-twitter"></i></a>
                    <a href="#" class="btn btn-outline-primary btn-sm"><i class="fab fa-youtube"></i></a>
                </div>
            </div>
        </div>
    </div>
</div>

<?php include 'views/layout/footer.php'; ?>
