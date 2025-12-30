<?php
<<<<<<< HEAD
require_once __DIR__ . '/../config/constants.php';
require_once __DIR__ . '/../config/session.php';

$page_title = 'Chi tiết đơn hàng - Admin';

// Require admin
requireAdmin();

if (!isset($_GET['id'])) {
    header('Location: ' . APP_URL . '/admin-orders.php');
=======
require_once 'config/constants.php';
require_once 'config/session.php';
requireAdmin();

$page_title = 'Chi tiết đơn hàng';
$conn = require 'config/database.php';
require_once 'models/Order.php';

if (!isset($_GET['id'])) {
    header('Location: /web_banhoa/admin-orders.php');
>>>>>>> 37c17f0dac4bb260a987b53f0f92d6e4a0c6a329
    exit;
}

$conn = require __DIR__ . '/../config/database.php';
require_once __DIR__ . '/../models/Order.php';

$order_model = new Order($conn);
$order_id = (int)$_GET['id'];

// Get order details
$order = $order_model->getOrderById($order_id);

if (!$order) {
<<<<<<< HEAD
    header('Location: ' . APP_URL . '/admin-orders.php');
=======
    header('Location: /web_banhoa/admin-orders.php');
>>>>>>> 37c17f0dac4bb260a987b53f0f92d6e4a0c6a329
    exit;
}

// Get order items
$query = "SELECT * FROM order_items WHERE order_id = ?";
$stmt = $conn->prepare($query);
$stmt->bind_param("i", $order_id);
$stmt->execute();
$order_items = $stmt->get_result()->fetch_all(MYSQLI_ASSOC);

// Get customer info
$query = "SELECT * FROM users WHERE user_id = ?";
$stmt = $conn->prepare($query);
$stmt->bind_param("i", $order['user_id']);
$stmt->execute();
$customer = $stmt->get_result()->fetch_assoc();

$message = '';
$error = '';

// Handle status update
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (isset($_POST['update_status'])) {
        $status = $_POST['status'];
        if ($order_model->updateOrderStatus($order_id, $status)) {
            $message = 'Cập nhật trạng thái thành công!';
            // Refresh order data
            $order = $order_model->getOrderById($order_id);
        } else {
            $error = 'Có lỗi xảy ra khi cập nhật trạng thái';
        }
    }
}
?>
<<<<<<< HEAD
<?php include __DIR__ . '/../views/layout/header.php'; ?>
=======
<?php include 'views/layout/header.php'; ?>

<div class="container">
    <div class="row mb-4">
        <div class="col-md-12">
            <a href="<?php echo APP_URL; ?>/admin/orders.php" class="btn btn-outline-secondary mb-3">
                <i class="fas fa-arrow-left"></i> Quay lại
            </a>
            <h2>Chi tiết đơn hàng <?php echo $order['order_code']; ?></h2>
        </div>
    </div>
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
            <div class="d-flex justify-content-between align-items-center mb-4">
                <h2>Chi tiết đơn hàng: <?php echo $order['order_code']; ?></h2>
                <a href="<?php echo APP_URL; ?>/admin-orders.php" class="btn btn-outline-secondary">
                    <i class="fas fa-arrow-left"></i> Quay lại
                </a>
            </div>

            <?php if ($message): ?>
                <div class="alert alert-success alert-dismissible fade show" role="alert">
                    <?php echo $message; ?>
                    <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                </div>
<<<<<<< HEAD
            <?php endif; ?>

            <?php if ($error): ?>
                <div class="alert alert-danger alert-dismissible fade show" role="alert">
                    <?php echo $error; ?>
                    <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                </div>
            <?php endif; ?>

            <div class="row">
                <div class="col-md-8">
                    <div class="card mb-4">
                        <div class="card-body">
                            <h5 class="card-title">Thông tin đơn hàng</h5>
                            
                            <div class="row">
                                <div class="col-md-6">
                                    <p><strong>Mã đơn hàng:</strong> <?php echo $order['order_code']; ?></p>
                                    <p><strong>Ngày đặt:</strong> <?php echo date('d/m/Y H:i', strtotime($order['order_date'])); ?></p>
                                    <p><strong>Tổng tiền:</strong> 
                                        <span class="text-danger fw-bold"><?php echo number_format($order['total_amount'], 0, ',', '.'); ?>đ</span>
                                    </p>
                                </div>
                                <div class="col-md-6">
                                    <?php if ($order['delivery_date']): ?>
                                        <p><strong>Ngày giao:</strong> <?php echo date('d/m/Y', strtotime($order['delivery_date'])); ?></p>
                                    <?php endif; ?>
                                    <?php if ($order['delivery_time_slot']): ?>
                                        <p><strong>Khung giờ:</strong> <?php echo $order['delivery_time_slot']; ?></p>
                                    <?php endif; ?>
                                    <p><strong>Phương thức thanh toán:</strong> <?php echo $order['payment_method_name'] ?? 'N/A'; ?></p>
                                </div>
                            </div>

                            <hr>
                            <h6>Thông tin khách hàng</h6>
                            <p><strong>Tên:</strong> <?php echo $customer['full_name']; ?></p>
                            <p><strong>Email:</strong> <?php echo $customer['email']; ?></p>
                            <p><strong>Điện thoại:</strong> <?php echo $customer['phone']; ?></p>

                            <hr>
                            <h6>Thông tin người nhận</h6>
                            <p><strong>Tên:</strong> <?php echo $order['recipient_name']; ?></p>
                            <p><strong>Điện thoại:</strong> <?php echo $order['recipient_phone']; ?></p>
                            <p><strong>Địa chỉ:</strong> 
                                <?php echo $order['shipping_address']; ?>
                                <?php if ($order['shipping_ward']): ?>, <?php echo $order['shipping_ward']; ?><?php endif; ?>
                                <?php if ($order['shipping_district']): ?>, <?php echo $order['shipping_district']; ?><?php endif; ?>
                                <?php if ($order['shipping_city']): ?>, <?php echo $order['shipping_city']; ?><?php endif; ?>
                            </p>

                            <?php if ($order['message_card']): ?>
                                <hr>
                                <h6>Lời nhắn trên thiệp</h6>
                                <div class="alert alert-light">
                                    <?php echo nl2br(htmlspecialchars($order['message_card'])); ?>
                                </div>
                            <?php endif; ?>

                            <?php if ($order['notes']): ?>
                                <hr>
                                <h6>Ghi chú</h6>
                                <p><?php echo nl2br(htmlspecialchars($order['notes'])); ?></p>
                            <?php endif; ?>
                        </div>
                    </div>

                    <div class="card">
                        <div class="card-body">
                            <h5 class="card-title">Sản phẩm đã đặt</h5>
                            <div class="table-responsive">
                                <table class="table">
                                    <thead>
                                        <tr>
                                            <th>Sản phẩm</th>
                                            <th>Đơn giá</th>
                                            <th>Số lượng</th>
                                            <th>Thành tiền</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <?php foreach ($order_items as $item): ?>
                                            <tr>
                                                <td><?php echo $item['product_name']; ?></td>
                                                <td><?php echo number_format($item['product_price'], 0, ',', '.'); ?>đ</td>
                                                <td><?php echo $item['quantity']; ?></td>
                                                <td><?php echo number_format($item['subtotal'], 0, ',', '.'); ?>đ</td>
                                            </tr>
                                        <?php endforeach; ?>
                                    </tbody>
                                    <tfoot>
                                        <tr>
                                            <th colspan="3">Tạm tính:</th>
                                            <th><?php echo number_format($order['subtotal'], 0, ',', '.'); ?>đ</th>
                                        </tr>
                                        <tr>
                                            <th colspan="3">Phí vận chuyển:</th>
                                            <th><?php echo number_format($order['shipping_fee'], 0, ',', '.'); ?>đ</th>
                                        </tr>
                                        <tr class="table-primary">
                                            <th colspan="3">Tổng cộng:</th>
                                            <th><?php echo number_format($order['total_amount'], 0, ',', '.'); ?>đ</th>
                                        </tr>
                                    </tfoot>
                                </table>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="col-md-4">
                    <div class="card">
                        <div class="card-body">
                            <h5 class="card-title">Cập nhật trạng thái</h5>
                            
                            <form method="POST">
                                <div class="mb-3">
                                    <label for="status" class="form-label">Trạng thái hiện tại</label>
                                    <select name="status" id="status" class="form-select">
                                        <option value="<?php echo ORDER_PENDING; ?>" <?php echo $order['status'] === ORDER_PENDING ? 'selected' : ''; ?>>Chờ xác nhận</option>
                                        <option value="<?php echo ORDER_CONFIRMED; ?>" <?php echo $order['status'] === ORDER_CONFIRMED ? 'selected' : ''; ?>>Đã xác nhận</option>
                                        <option value="<?php echo ORDER_SHIPPING; ?>" <?php echo $order['status'] === ORDER_SHIPPING ? 'selected' : ''; ?>>Đang giao</option>
                                        <option value="<?php echo ORDER_COMPLETED; ?>" <?php echo $order['status'] === ORDER_COMPLETED ? 'selected' : ''; ?>>Hoàn thành</option>
                                        <option value="<?php echo ORDER_CANCELLED; ?>" <?php echo $order['status'] === ORDER_CANCELLED ? 'selected' : ''; ?>>Đã hủy</option>
                                    </select>
                                </div>
                                <button type="submit" name="update_status" class="btn btn-primary w-100">
                                    <i class="fas fa-save"></i> Cập nhật trạng thái
                                </button>
                            </form>
                        </div>
                    </div>
=======
                <div class="card-body">
                    <form method="POST" action="/web_banhoa/admin-orders.php">
                        <input type="hidden" name="order_id" value="<?php echo $order['order_id']; ?>">
                        <select name="status" class="form-select mb-3">
                            <option value="pending" <?php echo $order['status'] === 'pending' ? 'selected' : ''; ?>>Chờ xác nhận</option>
                            <option value="confirmed" <?php echo $order['status'] === 'confirmed' ? 'selected' : ''; ?>>Đã xác nhận</option>
                            <option value="shipping" <?php echo $order['status'] === 'shipping' ? 'selected' : ''; ?>>Đang giao</option>
                            <option value="completed" <?php echo $order['status'] === 'completed' ? 'selected' : ''; ?>>Hoàn tất</option>
                            <option value="cancelled" <?php echo $order['status'] === 'cancelled' ? 'selected' : ''; ?>>Hủy</option>
                        </select>
                        <button type="submit" name="update_status" class="btn btn-primary w-100">Cập nhật</button>
                    </form>
>>>>>>> 37c17f0dac4bb260a987b53f0f92d6e4a0c6a329
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
