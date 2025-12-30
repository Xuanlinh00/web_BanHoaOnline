<?php
require_once 'config/constants.php';
require_once 'config/session.php';

$page_title = 'Xác nhận đơn hàng';

// Require login
requireLogin();

$conn = require 'config/database.php';
require_once 'models/Order.php';

if (!isset($_GET['order'])) {
    header('Location: ' . APP_URL . '/index.php');
    exit;
}

$order_model = new Order($conn);
$order_code = $_GET['order'];

// Get order by code (you might need to add this method to Order model)
$query = "SELECT * FROM orders WHERE order_code = ? AND user_id = ?";
$stmt = $conn->prepare($query);
$stmt->bind_param("si", $order_code, getCurrentUserId());
$stmt->execute();
$order = $stmt->get_result()->fetch_assoc();

if (!$order) {
    header('Location: ' . APP_URL . '/index.php');
    exit;
}

// Get order items
$query = "SELECT * FROM order_items WHERE order_id = ?";
$stmt = $conn->prepare($query);
$stmt->bind_param("i", $order['order_id']);
$stmt->execute();
$order_items = $stmt->get_result()->fetch_all(MYSQLI_ASSOC);
?>
<?php include 'views/layout/header.php'; ?>

<div class="container">
    <div class="text-center mb-5">
        <i class="fas fa-check-circle text-success" style="font-size: 4rem;"></i>
        <h2 class="mt-3">Đặt hàng thành công!</h2>
        <p class="text-muted">Cảm ơn bạn đã đặt hàng. Chúng tôi sẽ liên hệ với bạn sớm nhất.</p>
    </div>

    <div class="row justify-content-center">
        <div class="col-md-8">
            <div class="card">
                <div class="card-body">
                    <h5 class="card-title">Thông tin đơn hàng</h5>
                    
                    <div class="row">
                        <div class="col-md-6">
                            <p><strong>Mã đơn hàng:</strong> <?php echo $order['order_code']; ?></p>
                            <p><strong>Ngày đặt:</strong> <?php echo date('d/m/Y H:i', strtotime($order['order_date'])); ?></p>
                            <p><strong>Trạng thái:</strong> 
                                <span class="badge bg-warning">Chờ xác nhận</span>
                            </p>
                        </div>
                        <div class="col-md-6">
                            <p><strong>Ngày giao:</strong> <?php echo date('d/m/Y', strtotime($order['delivery_date'])); ?></p>
                            <?php if ($order['delivery_time_slot']): ?>
                                <p><strong>Khung giờ:</strong> <?php echo $order['delivery_time_slot']; ?></p>
                            <?php endif; ?>
                            <p><strong>Tổng tiền:</strong> 
                                <span class="text-danger fw-bold"><?php echo number_format($order['total_amount'], 0, ',', '.'); ?>đ</span>
                            </p>
                        </div>
                    </div>

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
                        <p><strong>Lời nhắn:</strong> <?php echo $order['message_card']; ?></p>
                    <?php endif; ?>

                    <hr>

                    <h6>Sản phẩm đã đặt</h6>
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

                    <div class="text-center mt-4">
                        <a href="<?php echo APP_URL; ?>/orders.php" class="btn btn-primary">
                            <i class="fas fa-list"></i> Xem đơn hàng của tôi
                        </a>
                        <a href="<?php echo APP_URL; ?>/products.php" class="btn btn-outline-primary ms-2">
                            <i class="fas fa-shopping-bag"></i> Tiếp tục mua sắm
                        </a>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<?php include 'views/layout/footer.php'; ?>