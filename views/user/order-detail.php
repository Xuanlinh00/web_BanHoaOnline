<?php
require_once 'config/constants.php';
require_once 'config/session.php';
requireLogin();

$page_title = 'Chi tiết đơn hàng';
$conn = require 'config/database.php';
require_once 'models/Order.php';

if (!isset($_GET['id'])) {
    header('Location: /web_banhoa/orders.php');
    exit;
}

$order_model = new Order($conn);
$order_id = (int)$_GET['id'];
$user_id = getCurrentUserId();

// Get order details
$order = $order_model->getOrderById($order_id);

// Check if order belongs to current user
if (!$order || $order['user_id'] != $user_id) {
    header('Location: /web_banhoa/orders.php');
    exit;
}

// Get order items
$query = "SELECT oi.*, p.image_url 
          FROM order_items oi 
          LEFT JOIN products p ON oi.product_id = p.product_id 
          WHERE oi.order_id = ?";
$stmt = $conn->prepare($query);
$stmt->bind_param("i", $order_id);
$stmt->execute();
$items = $stmt->get_result()->fetch_all(MYSQLI_ASSOC);

$status_text = [
    'pending' => 'Chờ xác nhận',
    'confirmed' => 'Đã xác nhận', 
    'shipping' => 'Đang giao',
    'completed' => 'Hoàn tất',
    'cancelled' => 'Đã hủy',
    'returned' => 'Trả lại'
];

$status_color = [
    'pending' => 'warning',
    'confirmed' => 'info',
    'shipping' => 'primary', 
    'completed' => 'success',
    'cancelled' => 'danger',
    'returned' => 'secondary'
];
?>
<?php include 'views/layout/header.php'; ?>

<div class="container">
    <div class="row">
        <div class="col-md-3">
            <div class="list-group">
                <a href="/web_banhoa/profile.php" class="list-group-item list-group-item-action">
                    <i class="fas fa-user"></i> Hồ sơ
                </a>
                <a href="/web_banhoa/orders.php" class="list-group-item list-group-item-action active">
                    <i class="fas fa-shopping-bag"></i> Đơn hàng
                </a>
                <a href="/web_banhoa/addresses.php" class="list-group-item list-group-item-action">
                    <i class="fas fa-map-marker-alt"></i> Địa chỉ
                </a>
            </div>
        </div>

        <div class="col-md-9">
            <div class="d-flex justify-content-between align-items-center mb-4">
                <h3>Chi tiết đơn hàng</h3>
                <a href="/web_banhoa/orders.php" class="btn btn-outline-secondary">
                    <i class="fas fa-arrow-left"></i> Quay lại
                </a>
            </div>

            <!-- Order Status -->
            <div class="card mb-4">
                <div class="card-body">
                    <div class="row align-items-center">
                        <div class="col-md-6">
                            <h5 class="mb-1">Mã đơn hàng: <strong><?php echo $order['order_code']; ?></strong></h5>
                            <p class="text-muted mb-0">Đặt ngày: <?php echo date('d/m/Y H:i', strtotime($order['order_date'])); ?></p>
                        </div>
                        <div class="col-md-6 text-end">
                            <span class="badge bg-<?php echo $status_color[$order['status']]; ?> fs-6 px-3 py-2">
                                <?php echo $status_text[$order['status']] ?? $order['status']; ?>
                            </span>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Delivery Information -->
            <div class="card mb-4">
                <div class="card-header">
                    <h5 class="mb-0"><i class="fas fa-truck"></i> Thông tin giao hàng</h5>
                </div>
                <div class="card-body">
                    <div class="row">
                        <div class="col-md-6">
                            <p><strong>Ngày giao:</strong> <?php echo date('d/m/Y', strtotime($order['delivery_date'])); ?></p>
                            <p><strong>Khung giờ:</strong> <?php echo $order['delivery_time_slot']; ?></p>
                            <?php if ($order['message_card']): ?>
                                <p><strong>Thiệp chúc mừng:</strong><br>
                                <em>"<?php echo nl2br(htmlspecialchars($order['message_card'])); ?>"</em></p>
                            <?php endif; ?>
                            <?php if ($order['is_anonymous']): ?>
                                <p><span class="badge bg-info">Gửi ẩn danh</span></p>
                            <?php endif; ?>
                        </div>
                        <div class="col-md-6">
                            <p><strong>Người nhận:</strong> <?php echo $order['recipient_name']; ?></p>
                            <p><strong>Số điện thoại:</strong> <?php echo $order['recipient_phone']; ?></p>
                            <p><strong>Địa chỉ:</strong><br>
                            <?php echo $order['shipping_address']; ?><br>
                            <?php if ($order['shipping_ward']): echo $order['shipping_ward'] . ', '; endif; ?>
                            <?php echo $order['shipping_district']; ?>, <?php echo $order['shipping_city']; ?></p>
                            <?php if ($order['notes']): ?>
                                <p><strong>Ghi chú:</strong> <?php echo htmlspecialchars($order['notes']); ?></p>
                            <?php endif; ?>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Order Items -->
            <div class="card mb-4">
                <div class="card-header">
                    <h5 class="mb-0"><i class="fas fa-box"></i> Sản phẩm đã đặt</h5>
                </div>
                <div class="card-body">
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
                                <?php foreach ($items as $item): ?>
                                    <tr>
                                        <td>
                                            <div class="d-flex align-items-center">
                                                <?php if ($item['image_url']): ?>
                                                    <img src="<?php echo $item['image_url']; ?>" alt="<?php echo $item['product_name']; ?>" 
                                                         style="width: 60px; height: 60px; object-fit: cover; margin-right: 15px; border-radius: 8px;">
                                                <?php endif; ?>
                                                <div>
                                                    <strong><?php echo $item['product_name']; ?></strong>
                                                </div>
                                            </div>
                                        </td>
                                        <td><?php echo number_format($item['product_price'], 0, ',', '.'); ?>đ</td>
                                        <td><?php echo $item['quantity']; ?></td>
                                        <td><strong><?php echo number_format($item['subtotal'], 0, ',', '.'); ?>đ</strong></td>
                                    </tr>
                                <?php endforeach; ?>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>

            <!-- Order Summary -->
            <div class="card mb-4">
                <div class="card-header">
                    <h5 class="mb-0"><i class="fas fa-calculator"></i> Tổng kết đơn hàng</h5>
                </div>
                <div class="card-body">
                    <div class="row">
                        <div class="col-md-6 offset-md-6">
                            <table class="table table-borderless">
                                <tr>
                                    <td>Tạm tính:</td>
                                    <td class="text-end"><?php echo number_format($order['subtotal'], 0, ',', '.'); ?>đ</td>
                                </tr>
                                <tr>
                                    <td>Phí vận chuyển:</td>
                                    <td class="text-end"><?php echo number_format($order['shipping_fee'], 0, ',', '.'); ?>đ</td>
                                </tr>
                                <?php if ($order['discount_amount'] > 0): ?>
                                    <tr>
                                        <td>Giảm giá:</td>
                                        <td class="text-end text-success">-<?php echo number_format($order['discount_amount'], 0, ',', '.'); ?>đ</td>
                                    </tr>
                                <?php endif; ?>
                                <tr class="border-top">
                                    <td><strong>Tổng cộng:</strong></td>
                                    <td class="text-end"><strong class="text-danger h5"><?php echo number_format($order['total_amount'], 0, ',', '.'); ?>đ</strong></td>
                                </tr>
                            </table>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Payment Information -->
            <div class="card mb-4">
                <div class="card-header">
                    <h5 class="mb-0"><i class="fas fa-credit-card"></i> Thông tin thanh toán</h5>
                </div>
                <div class="card-body">
                    <div class="row">
                        <div class="col-md-6">
                            <p><strong>Phương thức thanh toán:</strong> 
                            <?php echo $order['payment_method_name'] ?? 'Thanh toán khi nhận hàng (COD)'; ?></p>
                        </div>
                        <div class="col-md-6">
                            <p><strong>Trạng thái thanh toán:</strong> 
                            <span class="badge bg-<?php echo $order['payment_status'] === 'paid' ? 'success' : 'warning'; ?>">
                                <?php 
                                $payment_status_text = [
                                    'unpaid' => 'Chưa thanh toán',
                                    'paid' => 'Đã thanh toán',
                                    'refunded' => 'Đã hoàn tiền'
                                ];
                                echo $payment_status_text[$order['payment_status']] ?? $order['payment_status'];
                                ?>
                            </span></p>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Action Buttons -->
            <?php if ($order['status'] === 'pending'): ?>
                <div class="text-center mb-4">
                    <button class="btn btn-danger" onclick="cancelOrder(<?php echo $order['order_id']; ?>)">
                        <i class="fas fa-times"></i> Hủy đơn hàng
                    </button>
                </div>
            <?php endif; ?>
        </div>
    </div>
</div>

<script>
function cancelOrder(orderId) {
    if (confirm('Bạn có chắc chắn muốn hủy đơn hàng này?')) {
        // Implement cancel order functionality
        window.location.href = '/web_banhoa/cancel-order.php?id=' + orderId;
    }
}
</script>

<?php include 'views/layout/footer.php'; ?>