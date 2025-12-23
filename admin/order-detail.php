<?php
require_once '../config/constants.php';
require_once '../config/session.php';
requireAdmin();

$page_title = 'Chi tiết đơn hàng';
$conn = require '../config/database.php';
require_once '../models/Order.php';

if (!isset($_GET['id'])) {
    header('Location: ' . APP_URL . '/admin/orders.php');
    exit;
}

$order_model = new Order($conn);
$order = $order_model->getOrderById($_GET['id']);

if (!$order) {
    header('Location: ' . APP_URL . '/admin/orders.php');
    exit;
}

// Get order items
$query = "SELECT * FROM order_items WHERE order_id = ?";
$stmt = $conn->prepare($query);
$stmt->bind_param("i", $order['order_id']);
$stmt->execute();
$items = $stmt->get_result()->fetch_all(MYSQLI_ASSOC);
?>
<?php include '../views/layout/header.php'; ?>

<div class="container">
    <div class="row mb-4">
        <div class="col-md-12">
            <a href="<?php echo APP_URL; ?>/admin/orders.php" class="btn btn-outline-secondary mb-3">
                <i class="fas fa-arrow-left"></i> Quay lại
            </a>
            <h2>Chi tiết đơn hàng <?php echo $order['order_code']; ?></h2>
        </div>
    </div>

    <div class="row">
        <div class="col-md-8">
            <!-- Order Info -->
            <div class="card mb-4">
                <div class="card-header">
                    <h5 class="mb-0">Thông tin đơn hàng</h5>
                </div>
                <div class="card-body">
                    <div class="row mb-3">
                        <div class="col-md-6">
                            <strong>Mã đơn:</strong> <?php echo $order['order_code']; ?><br>
                            <strong>Ngày đặt:</strong> <?php echo date('d/m/Y H:i', strtotime($order['order_date'])); ?><br>
                            <strong>Trạng thái:</strong> 
                            <span class="badge bg-<?php 
                                echo $order['status'] === 'completed' ? 'success' : 
                                     ($order['status'] === 'cancelled' ? 'danger' : 
                                     ($order['status'] === 'shipping' ? 'info' : 'warning'));
                            ?>">
                                <?php 
                                $status_text = [
                                    'pending' => 'Chờ xác nhận',
                                    'confirmed' => 'Đã xác nhận',
                                    'shipping' => 'Đang giao',
                                    'completed' => 'Hoàn tất',
                                    'cancelled' => 'Đã hủy'
                                ];
                                echo $status_text[$order['status']] ?? $order['status'];
                                ?>
                            </span>
                        </div>
                        <div class="col-md-6">
                            <strong>Thanh toán:</strong> 
                            <span class="badge bg-<?php echo $order['payment_status'] === 'paid' ? 'success' : 'warning'; ?>">
                                <?php 
                                $payment_text = [
                                    'unpaid' => 'Chưa thanh toán',
                                    'paid' => 'Đã thanh toán',
                                    'refunded' => 'Đã hoàn tiền'
                                ];
                                echo $payment_text[$order['payment_status']] ?? $order['payment_status'];
                                ?>
                            </span>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Delivery Info -->
            <div class="card mb-4">
                <div class="card-header">
                    <h5 class="mb-0">Thông tin giao hàng</h5>
                </div>
                <div class="card-body">
                    <strong>Ngày giao:</strong> <?php echo date('d/m/Y', strtotime($order['delivery_date'])); ?><br>
                    <strong>Khung giờ:</strong> <?php echo $order['delivery_time_slot']; ?><br>
                    <strong>Người nhận:</strong> <?php echo $order['recipient_name']; ?><br>
                    <strong>Số điện thoại:</strong> <?php echo $order['recipient_phone']; ?><br>
                    <strong>Địa chỉ:</strong> <?php echo $order['shipping_address']; ?>, <?php echo $order['shipping_ward']; ?> <?php echo $order['shipping_district']; ?>, <?php echo $order['shipping_city']; ?><br>
                    <?php if ($order['message_card']): ?>
                        <strong>Thiệp chúc mừng:</strong><br>
                        <div class="alert alert-info mt-2">
                            <?php echo nl2br($order['message_card']); ?>
                        </div>
                    <?php endif; ?>
                    <?php if ($order['is_anonymous']): ?>
                        <span class="badge bg-secondary">Gửi ẩn danh</span>
                    <?php endif; ?>
                </div>
            </div>

            <!-- Order Items -->
            <div class="card mb-4">
                <div class="card-header">
                    <h5 class="mb-0">Sản phẩm</h5>
                </div>
                <div class="card-body">
                    <table class="table">
                        <thead>
                            <tr>
                                <th>Sản phẩm</th>
                                <th>Giá</th>
                                <th>Số lượng</th>
                                <th>Thành tiền</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach ($items as $item): ?>
                                <tr>
                                    <td><?php echo $item['product_name']; ?></td>
                                    <td><?php echo number_format($item['product_price'], 0, ',', '.'); ?>đ</td>
                                    <td><?php echo $item['quantity']; ?></td>
                                    <td><?php echo number_format($item['subtotal'], 0, ',', '.'); ?>đ</td>
                                </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>

        <!-- Summary -->
        <div class="col-md-4">
            <div class="card">
                <div class="card-header">
                    <h5 class="mb-0">Tóm tắt</h5>
                </div>
                <div class="card-body">
                    <div class="d-flex justify-content-between mb-2">
                        <span>Tạm tính:</span>
                        <span><?php echo number_format($order['subtotal'], 0, ',', '.'); ?>đ</span>
                    </div>
                    <div class="d-flex justify-content-between mb-2">
                        <span>Phí vận chuyển:</span>
                        <span><?php echo number_format($order['shipping_fee'], 0, ',', '.'); ?>đ</span>
                    </div>
                    <div class="d-flex justify-content-between mb-3">
                        <span>Giảm giá:</span>
                        <span><?php echo number_format($order['discount_amount'], 0, ',', '.'); ?>đ</span>
                    </div>
                    <hr>
                    <div class="d-flex justify-content-between">
                        <strong>Tổng cộng:</strong>
                        <strong class="text-danger h5"><?php echo number_format($order['total_amount'], 0, ',', '.'); ?>đ</strong>
                    </div>
                </div>
            </div>

            <!-- Update Status -->
            <div class="card mt-3">
                <div class="card-header">
                    <h5 class="mb-0">Cập nhật trạng thái</h5>
                </div>
                <div class="card-body">
                    <form method="POST" action="<?php echo APP_URL; ?>/admin/orders.php">
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
                </div>
            </div>
        </div>
    </div>
</div>

<?php include '../views/layout/footer.php'; ?>
