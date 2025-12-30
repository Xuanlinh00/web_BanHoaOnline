<?php
<<<<<<< HEAD
require_once __DIR__ . '/../config/constants.php';
require_once __DIR__ . '/../config/session.php';

$page_title = 'Quản lý đơn hàng';

// Require admin
requireAdmin();

$conn = require __DIR__ . '/../config/database.php';
require_once __DIR__ . '/../models/Order.php';
=======
require_once 'config/constants.php';
require_once 'config/session.php';
requireAdmin();

$page_title = 'Quản lý đơn hàng';
$conn = require 'config/database.php';
require_once 'models/Order.php';
>>>>>>> 37c17f0dac4bb260a987b53f0f92d6e4a0c6a329

$order_model = new Order($conn);

$message = '';
$error = '';

// Handle status update
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['update_status'])) {
    $order_id = (int)$_POST['order_id'];
    $new_status = $_POST['status'];
    
    if ($order_model->updateOrderStatus($order_id, $new_status)) {
        $message = 'Cập nhật trạng thái đơn hàng thành công!';
    } else {
        $error = 'Có lỗi xảy ra khi cập nhật trạng thái';
    }
}

$page = isset($_GET['page']) ? (int)$_GET['page'] : 1;
$orders = $order_model->getAllOrders($page, 20);
?>
<<<<<<< HEAD
<?php include __DIR__ . '/../views/layout/header.php'; ?>
=======
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
                        <a href="<?php echo APP_URL; ?>/admin-orders.php" class="list-group-item list-group-item-action active">
                            <i class="fas fa-shopping-cart"></i> Đơn hàng
                        </a>
                        <a href="<?php echo APP_URL; ?>/admin-reviews.php" class="list-group-item list-group-item-action">
                            <i class="fas fa-star"></i> Đánh giá
                        </a>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-md-10">
            <h2 class="mb-4">Quản lý đơn hàng</h2>

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
                    <?php if (empty($orders)): ?>
                        <div class="text-center py-5">
                            <i class="fas fa-shopping-cart fa-3x text-muted mb-3"></i>
                            <h5>Chưa có đơn hàng nào</h5>
                            <p class="text-muted">Các đơn hàng sẽ hiển thị ở đây</p>
                        </div>
                    <?php else: ?>
                        <div class="table-responsive">
                            <table class="table">
                                <thead>
                                    <tr>
                                        <th>Mã đơn</th>
                                        <th>Khách hàng</th>
                                        <th>Ngày đặt</th>
                                        <th>Tổng tiền</th>
                                        <th>Trạng thái</th>
                                        <th>Thao tác</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php foreach ($orders as $order): ?>
                                        <tr>
                                            <td>
                                                <strong><?php echo $order['order_code']; ?></strong>
                                            </td>
                                            <td>
                                                <?php echo $order['full_name']; ?>
                                                <br>
                                                <small class="text-muted"><?php echo $order['email']; ?></small>
                                            </td>
                                            <td><?php echo date('d/m/Y H:i', strtotime($order['order_date'])); ?></td>
                                            <td><?php echo number_format($order['total_amount'], 0, ',', '.'); ?>đ</td>
                                            <td>
                                                <form method="POST" class="d-inline">
                                                    <input type="hidden" name="order_id" value="<?php echo $order['order_id']; ?>">
                                                    <select name="status" class="form-select form-select-sm" onchange="this.form.submit()">
                                                        <option value="<?php echo ORDER_PENDING; ?>" <?php echo $order['status'] === ORDER_PENDING ? 'selected' : ''; ?>>Chờ xác nhận</option>
                                                        <option value="<?php echo ORDER_CONFIRMED; ?>" <?php echo $order['status'] === ORDER_CONFIRMED ? 'selected' : ''; ?>>Đã xác nhận</option>
                                                        <option value="<?php echo ORDER_SHIPPING; ?>" <?php echo $order['status'] === ORDER_SHIPPING ? 'selected' : ''; ?>>Đang giao</option>
                                                        <option value="<?php echo ORDER_COMPLETED; ?>" <?php echo $order['status'] === ORDER_COMPLETED ? 'selected' : ''; ?>>Hoàn thành</option>
                                                        <option value="<?php echo ORDER_CANCELLED; ?>" <?php echo $order['status'] === ORDER_CANCELLED ? 'selected' : ''; ?>>Đã hủy</option>
                                                    </select>
                                                    <input type="hidden" name="update_status" value="1">
                                                </form>
                                            </td>
                                            <td>
                                                <a href="<?php echo APP_URL; ?>/admin-order-detail.php?id=<?php echo $order['order_id']; ?>" 
                                                   class="btn btn-sm btn-outline-primary">
                                                    <i class="fas fa-eye"></i> Chi tiết
                                                </a>
                                            </td>
                                        </tr>
                                    <?php endforeach; ?>
                                </tbody>
                            </table>
                        </div>
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
