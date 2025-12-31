<?php
define('ROOT_DIR', dirname(dirname(__DIR__)));

require_once ROOT_DIR . '/config/constants.php';
require_once ROOT_DIR . '/config/session.php';

$page_title = 'Trang không tìm thấy';
?>
<?php include ROOT_DIR . '/views/layout/header.php'; ?>

<div class="container">
    <div class="row justify-content-center">
        <div class="col-md-8">
            <div class="text-center py-5">
                <h1 class="display-1 fw-bold" style="color: var(--primary-color);">404</h1>
                <h2 class="mb-3 fw-bold">Trang không tìm thấy</h2>
                <p class="lead text-muted mb-4">Xin lỗi, trang bạn đang tìm kiếm không tồn tại hoặc đã bị xóa.</p>
                
                <div class="mb-4">
                    <i class="fas fa-search" style="font-size: 4rem; color: var(--primary-color); opacity: 0.5;"></i>
                </div>

                <div class="row g-3">
                    <div class="col-md-6">
                        <a href="<?php echo APP_URL; ?>/" class="btn btn-primary btn-lg w-100">
                            <i class="fas fa-home"></i> Về trang chủ
                        </a>
                    </div>
                    <div class="col-md-6">
                        <a href="<?php echo APP_URL; ?>/views/products/index.php" class="btn btn-outline-primary btn-lg w-100">
                            <i class="fas fa-shopping-bag"></i> Xem sản phẩm
                        </a>
                    </div>
                </div>

                <div class="mt-5">
                    <p class="text-muted">Bạn cần giúp đỡ? <a href="<?php echo APP_URL; ?>/views/contact.php">Liên hệ với chúng tôi</a></p>
                </div>
            </div>
        </div>
    </div>
</div>

<?php include ROOT_DIR . '/views/layout/footer.php'; ?>
