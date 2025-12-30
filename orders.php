<?php
require_once 'config/constants.php';
require_once 'config/session.php';

$page_title = 'Đơn hàng của tôi';

// Require login
requireLogin();

$conn = require 'config/database.php';
require_once 'models/Order.php';

$order_model = new Order($conn);
$user_id = getCurrentUserId();

$page = isset($_GET['page']) ? (int)$_GET['page'] : 1;
$orders = $order_model->getUserOrders($user_id, $page);
?>
<?php include 'views/layout/header.php'; ?>

<div class="container">
    <div class="row">
        <div class="col-md-3">
            <div class="card">
                <div class="card-body">
                    <h5 class="card-title">Tài khoản của tôi</h5>
                    <div class="list-group list-group-flush">
                        <a href="<?php echo APP_URL; ?>/profile.php" class="list-group-item list-group-item-action">
                            <i class="fas fa-user"></i> Hồ sơ
                        </a>
                        <a href="<?php echo APP_URL; ?>/orders.php" class="list-group-item list-group-item-action active">
                            <i class="fas fa-shopping-bag"></i> Đơn hàng
                        </a>
                        <a href="<?php echo APP_URL; ?>/addresses.php" class="list-group-item list-group-item-action">
                            <i class="fas fa-map-marker-alt"></i> Địa chỉ
                        </a>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-md-9">
            <div class="card">
                <div class="card-body">
                    <h5 class="card-title">Đơn hàng của tôi</h5>

                    <?php if (empty($orders)): ?>
                        <div class="text-center py-5">
                            <i class="fas fa-shopping-bag fa-3x text-muted mb-3"></i>
                            <h6>Bạn chưa có đơn hàng nào</h6>
                            <p class="text-muted">Hãy bắt đầu mua sắm để tạo đơn hàng đầu tiên</p>
                            <a href="<?php echo APP_URL; ?>/products.php" class="btn btn-primary">
                                <i class="fas fa-shopping-cart"></i> Mua sắm ngay
                            </a>
                        </div>
                    <?php else: ?>
                        <?php foreach ($orders as $order): ?>
                            <div class="card mb-3">
                                <div class="card-body">
                                    <div class="row align-items-center">
                                        <div class="col-md-3">
                                            <h6 class="mb-1"><?php echo $order['order_code']; ?></h6>
                                            <small class="text-muted"><?php echo date('d/m/Y H:i', strtotime($order['order_date'])); ?></small>
                                        </div>
                                        <div class="col-md-3">
                                            <span class="fw-bold text-danger"><?php echo number_format($order['total_amount'], 0, ',', '.'); ?>đ</span>
                                        </div>
                                        <div class="col-md-3">
                                            <?php
                                            $status_class = '';
                                            $status_text = '';
                                            switch ($order['status']) {
                                                case ORDER_PENDING:
                                                    $status_class = 'bg-warning';
                                                    $status_text = 'Chờ xác nhận';
                                                    break;
                                                case ORDER_CONFIRMED:
                                                    $status_class = 'bg-info';
                                                    $status_text = 'Đã xác nhận';
                                                    break;
                                                case ORDER_SHIPPING:
                                                    $status_class = 'bg-primary';
                                                    $status_text = 'Đang giao';
                                                    break;
                                                case ORDER_COMPLETED:
                                                    $status_class = 'bg-success';
                                                    $status_text = 'Hoàn thành';
                                                    break;
                                                case ORDER_CANCELLED:
                                                    $status_class = 'bg-danger';
                                                    $status_text = 'Đã hủy';
                                                    break;
                                                default:
                                                    $status_class = 'bg-secondary';
                                                    $status_text = 'Không xác định';
                                            }
                                            ?>
                                            <span class="badge <?php echo $status_class; ?>"><?php echo $status_text; ?></span>
                                        </div>
                                        <div class="col-md-3 text-end">
                                            <a href="<?php echo APP_URL; ?>/order-detail.php?id=<?php echo $order['order_id']; ?>" 
                                               class="btn btn-outline-primary btn-sm">
                                                <i class="fas fa-eye"></i> Chi tiết
                                            </a>
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

<?php include 'views/layout/footer.php'; ?>