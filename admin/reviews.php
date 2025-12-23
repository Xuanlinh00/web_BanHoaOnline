<?php
require_once '../config/constants.php';
require_once '../config/session.php';
requireAdmin();

$page_title = 'Duyệt đánh giá';
$conn = require '../config/database.php';
require_once '../models/Review.php';

$review_model = new Review($conn);
$page = isset($_GET['page']) ? (int)$_GET['page'] : 1;

$reviews = $review_model->getPendingReviews($page);

// Handle approve/reject
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (isset($_POST['approve'])) {
        $review_model->approveReview($_POST['review_id']);
    } elseif (isset($_POST['reject'])) {
        $review_model->rejectReview($_POST['review_id']);
    }
    header('Location: ' . APP_URL . '/admin/reviews.php');
    exit;
}
?>
<?php include '../views/layout/header.php'; ?>

<div class="container-fluid">
    <div class="row mb-4">
        <div class="col-md-12">
            <h2>Duyệt đánh giá</h2>
        </div>
    </div>

    <?php if (empty($reviews)): ?>
        <div class="alert alert-info">Không có đánh giá nào chờ duyệt</div>
    <?php else: ?>
        <div class="row">
            <?php foreach ($reviews as $review): ?>
                <div class="col-md-6 mb-3">
                    <div class="card">
                        <div class="card-body">
                            <div class="d-flex justify-content-between align-items-start mb-2">
                                <div>
                                    <h6 class="card-title"><?php echo $review['full_name']; ?></h6>
                                    <small class="text-muted"><?php echo $review['product_name']; ?></small>
                                </div>
                                <div>
                                    <?php for ($i = 0; $i < $review['rating']; $i++): ?>
                                        <i class="fas fa-star text-warning"></i>
                                    <?php endfor; ?>
                                    <?php for ($i = $review['rating']; $i < 5; $i++): ?>
                                        <i class="far fa-star text-warning"></i>
                                    <?php endfor; ?>
                                </div>
                            </div>

                            <p class="card-text"><?php echo $review['comment']; ?></p>

                            <?php if ($review['images']): ?>
                                <div class="mb-2">
                                    <small class="text-muted">Hình ảnh:</small><br>
                                    <?php 
                                    $images = json_decode($review['images'], true);
                                    if (is_array($images)):
                                        foreach ($images as $img):
                                    ?>
                                        <img src="<?php echo $img; ?>" alt="Review image" style="width: 60px; height: 60px; object-fit: cover; margin-right: 5px;">
                                    <?php 
                                        endforeach;
                                    endif;
                                    ?>
                                </div>
                            <?php endif; ?>

                            <small class="text-muted"><?php echo date('d/m/Y H:i', strtotime($review['created_at'])); ?></small>

                            <div class="mt-3">
                                <form method="POST" class="d-inline">
                                    <input type="hidden" name="review_id" value="<?php echo $review['review_id']; ?>">
                                    <button type="submit" name="approve" class="btn btn-sm btn-success">
                                        <i class="fas fa-check"></i> Duyệt
                                    </button>
                                    <button type="submit" name="reject" class="btn btn-sm btn-danger">
                                        <i class="fas fa-times"></i> Từ chối
                                    </button>
                                </form>
                            </div>
                        </div>
                    </div>
                </div>
            <?php endforeach; ?>
        </div>
    <?php endif; ?>
</div>

<?php include '../views/layout/footer.php'; ?>
