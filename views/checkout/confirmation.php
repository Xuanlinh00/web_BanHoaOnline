<?php
require_once 'config/constants.php';
require_once 'config/session.php';
requireLogin();

$page_title = 'Xác nhận đơn hàng';
$conn = require 'config/database.php';
require_once 'models/Order.php';

if (!isset($_GET['order_id'])) {
    header('Location: /web_banhoa/');
    exit;
}

$order_model = new Order($conn);
$order = $order_model->getOrderById($_GET['order_id']);

if (!$order || $order['user_id'] != getCurrentUserId()) {
    header('Location: /web_banhoa/');
    exit;
}

// Get order items
$query = "SELECT * FROM order_items WHERE order_id = ?";
$stmt = $conn->prepare($query);
$stmt->bind_param("i", $order['order_id']);
$stmt->execute();
$items = $stmt->get_result()->fetch_all(MYSQLI_ASSOC);
?>
<?php include 'views/layout/header.php'; ?>

<div class="container">
    <div class="row justify-content-center">
        <div class="col-md-8">
            <div class="card border-success">
                <div class="card-body text-center py-5">
                    <i class="fas fa-check-circle text-success" style="font-size: 3rem;"></i>
                    <h2 class="mt-3">Đặt hàng thành công!</h2>
                    <p class="text-muted">Cảm ơn bạn đã mua sắm tại Web Bán Hoa</p>
                </div>
            </div>

            <div class="card mt-4">
                <div class="card-header">
                    <h5 class="mb-0">Thông tin đơn hàng</h5>
                </div>
                <div class="card-body">
                    <div class="row mb-3">
                        <div class="col-md-6">
                            <strong>Mã đơn hàng:</strong><br>
                            <span class="text-primary"><?php echo $order['order_code']; ?></span>
                        </div>
                        <div class="col-md-6">
                            <strong>Ngày đặt:</strong><br>
                            <?php echo date('d/m/Y H:i', strtotime($order['order_date'])); ?>
                        </div>
                    </div>

                    <div class="row mb-3">
                        <div class="col-md-6">
                            <strong>Ngày giao hàng:</strong><br>
                            <?php echo date('d/m/Y', strtotime($order['delivery_date'])); ?>
                        </div>
                        <div class="col-md-6">
                            <strong>Khung giờ:</strong><br>
                            <?php echo $order['delivery_time_slot']; ?>
                        </div>
                    </div>

                    <hr>

                    <h6>Sản phẩm:</h6>
                    <table class="table table-sm">
                        <tbody>
                            <?php foreach ($items as $item): ?>
                                <tr>
                                    <td><?php echo $item['product_name']; ?></td>
                                    <td class="text-end">x<?php echo $item['quantity']; ?></td>
                                    <td class="text-end"><?php echo number_format($item['subtotal'], 0, ',', '.'); ?>đ</td>
                                </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>

                    <hr>

                    <div class="row">
                        <div class="col-md-6">
                            <strong>Địa chỉ giao hàng:</strong><br>
                            <?php echo $order['recipient_name']; ?><br>
                            <?php echo $order['shipping_address']; ?><br>
                            <?php echo $order['shipping_ward']; ?> <?php echo $order['shipping_district']; ?><br>
                            <?php echo $order['shipping_city']; ?><br>
                            <?php echo $order['recipient_phone']; ?>
                        </div>
                        <div class="col-md-6 text-end">
                            <div class="mb-2">
                                <span>Tạm tính:</span><br>
                                <strong><?php echo number_format($order['subtotal'], 0, ',', '.'); ?>đ</strong>
                            </div>
                            <div class="mb-2">
                                <span>Phí vận chuyển:</span><br>
                                <strong><?php echo number_format($order['shipping_fee'], 0, ',', '.'); ?>đ</strong>
                            </div>
                            <div class="border-top pt-2">
                                <span>Tổng cộng:</span><br>
                                <strong class="text-danger h5"><?php echo number_format($order['total_amount'], 0, ',', '.'); ?>đ</strong>
                            </div>
                        </div>
                    </div>

                    <?php if ($order['message_card']): ?>
                        <hr>
                        <div class="alert alert-info">
                            <strong>Thiệp chúc mừng:</strong><br>
                            <?php echo nl2br($order['message_card']); ?>
                        </div>
                    <?php endif; ?>
                </div>
            </div>

            <div class="mt-4">
                <a href="/web_banhoa/orders.php" class="btn btn-primary">
                    Xem đơn hàng của tôi
                </a>
                <a href="/web_banhoa/products.php" class="btn btn-outline-primary">
                    Tiếp tục mua sắm
                </a>
            </div>
        </div>
    </div>
</div>

<?php include 'views/layout/footer.php'; ?>
