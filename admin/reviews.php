<?php
<<<<<<< HEAD
require_once __DIR__ . '/../config/constants.php';
require_once __DIR__ . '/../config/session.php';

$page_title = 'Quản lý đánh giá';

// Require admin
requireAdmin();

$conn = require __DIR__ . '/../config/database.php';
require_once __DIR__ . '/../models/Review.php';

$review = new Review($conn);
=======
require_once 'config/constants.php';
require_once 'config/session.php';
requireAdmin();

$page_title = 'Duyệt đánh giá';
$conn = require 'config/database.php';
require_once 'models/Review.php';
>>>>>>> 37c17f0dac4bb260a987b53f0f92d6e4a0c6a329

$page = isset($_GET['page']) ? (int)$_GET['page'] : 1;
$reviews = $review->getPendingReviews($page, 20);

$message = '';
$error = '';

// Handle review actions
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (isset($_POST['approve_review'])) {
        $review_id = (int)$_POST['review_id'];
        if ($review->approveReview($review_id)) {
            $message = 'Duyệt đánh giá thành công!';
        } else {
            $error = 'Có lỗi xảy ra khi duyệt đánh giá';
        }
    } elseif (isset($_POST['reject_review'])) {
        $review_id = (int)$_POST['review_id'];
        if ($review->rejectReview($review_id)) {
            $message = 'Từ chối đánh giá thành công!';
        } else {
            $error = 'Có lỗi xảy ra khi từ chối đánh giá';
        }
    }
<<<<<<< HEAD
}
?>
<?php include __DIR__ . '/../views/layout/header.php'; ?>
=======
    header('Location: /web_banhoa/admin-reviews.php');
    exit;
}
?>
<?php include 'views/layout/header.php'; ?>
>>>>>>> 37c17f0dac4bb260a987b53f0f92d6e4a0c6a329

<div class="container-fluid">
    <div class="row">
        <div class="col-md-2">
            <div class="card">
                <div class="card-body">
                    <h6 class="card-title">Menu Admin</h6>
                    <div class="list-group list-group-flush">
                        <a href="<?php echo APP_URL; ?>/admin-dashboard.php" class="list-group-item list-group-item-action">
                            <i class="fas fa-tachometer-alt"></i> Dashboard
                        </a>
                        <a href="<?php echo APP_URL; ?>/admin-products.php" class="list-group-item list-group-item-action">
                            <i class="fas fa-box"></i> Sản phẩm
                        </a>
                        <a href="<?php echo APP_URL; ?>/admin-orders.php" class="list-group-item list-group-item-action">
                            <i class="fas fa-shopping-cart"></i> Đơn hàng
                        </a>
                        <a href="<?php echo APP_URL; ?>/admin-reviews.php" class="list-group-item list-group-item-action active">
                            <i class="fas fa-star"></i> Đánh giá
                        </a>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-md-10">
            <h2 class="mb-4">Quản lý đánh giá</h2>

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

            <div class="card">
                <div class="card-body">
                    <h5 class="card-title">Đánh giá chờ duyệt</h5>
                    
                    <?php if (empty($reviews)): ?>
                        <div class="text-center py-5">
                            <i class="fas fa-star fa-3x text-muted mb-3"></i>
                            <h5>Không có đánh giá nào chờ duyệt</h5>
                        </div>
                    <?php else: ?>
                        <?php foreach ($reviews as $rev): ?>
                            <div class="card mb-3">
                                <div class="card-body">
                                    <div class="row">
                                        <div class="col-md-8">
                                            <h6 class="card-title">
                                                <?php echo $rev['product_name']; ?>
                                                <span class="ms-2">
                                                    <?php for ($i = 0; $i < $rev['rating']; $i++): ?>
                                                        <i class="fas fa-star text-warning"></i>
                                                    <?php endfor; ?>
                                                    <?php for ($i = $rev['rating']; $i < 5; $i++): ?>
                                                        <i class="far fa-star text-warning"></i>
                                                    <?php endfor; ?>
                                                </span>
                                            </h6>
                                            <p class="card-text"><?php echo $rev['comment']; ?></p>
                                            <small class="text-muted">
                                                Bởi: <?php echo $rev['full_name']; ?> - 
                                                <?php echo date('d/m/Y H:i', strtotime($rev['created_at'])); ?>
                                            </small>
                                        </div>
                                        <div class="col-md-4 text-end">
                                            <form method="POST" class="d-inline">
                                                <input type="hidden" name="review_id" value="<?php echo $rev['review_id']; ?>">
                                                <button type="submit" name="approve_review" class="btn btn-success btn-sm">
                                                    <i class="fas fa-check"></i> Duyệt
                                                </button>
                                                <button type="submit" name="reject_review" class="btn btn-danger btn-sm">
                                                    <i class="fas fa-times"></i> Từ chối
                                                </button>
                                            </form>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        <?php endforeach; ?>
                    <?php endif; ?>
                </div>
            </div>
        </div>
    </div>
</div>

<<<<<<< HEAD
<?php include __DIR__ . '/../views/layout/footer.php'; ?>
=======
<?php include 'views/layout/footer.php'; ?>
>>>>>>> 37c17f0dac4bb260a987b53f0f92d6e4a0c6a329
