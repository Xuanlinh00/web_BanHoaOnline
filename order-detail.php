<?php
require_once 'config/constants.php';
require_once 'config/session.php';

$page_title = 'Chi tiết đơn hàng';

// Require login
requireLogin();

if (!isset($_GET['id'])) {
    header('Location: ' . APP_URL . '/orders.php');
    exit;
}

$conn = require 'config/database.php';
require_once 'models/Order.php';

$order_model = new Order($conn);
$order_id = (int)$_GET['id'];
$user_id = getCurrentUserId();

// Get order details
$order = $order_model->getOrderById($order_id);

// Check if order belongs to current user (or is admin)
if (!$order || ($order['user_id'] != $user_id && !isAdmin())) {
    header('Location: ' . APP_URL . '/orders.php');
    exit;
}

// Get order items
$query = "SELECT * FROM order_items WHERE order_id = ?";
$stmt = $conn->prepare($query);
$stmt->bind_param("i", $order_id);
$stmt->execute();
$order_items = $stmt->get_result()->fetch_all(MYSQLI_ASSOC);
?>
<?php include 'views/layout/header.php'; ?>

<div class="container">
    <div class="row">
        <div class="col-md-12">
            <div class="d-flex justify-content-between align-items-center mb-4">
                <h2>Chi tiết đơn hàng</h2>
                <a href="<?php echo APP_URL; ?>/orders.php" class="btn btn-outline-secondary">
                    <i class="fas fa-arrow-left"></i> Quay lại
                </a>
            </div>

            <div class="card">
                <div class="card-body">
                    <div class="row">
                        <div class="col-md-6">
                            <h5>Thông tin đơn hàng</h5>
                            <p><strong>Mã đơn hàng:</strong> <?php echo $order['order_code']; ?></p>
                            <p><strong>Ngày đặt:</strong> <?php echo date('d/m/Y H:i', strtotime($order['order_date'])); ?></p>
                            <p><strong>Trạng thái:</strong> 
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
                                }
                                ?>
                                <span class="badge <?php echo $status_class; ?>"><?php echo $status_text; ?></span>
                            </p>
                        </div>
                        <div class="col-md-6">
                            <h5>Thông tin giao hàng</h5>
                            <p><strong>Người nhận:</strong> <?php echo $order['recipient_name']; ?></p>
                            <p><strong>Điện thoại:</strong> <?php echo $order['recipient_phone']; ?></p>
                            <p><strong>Địa chỉ:</strong> 
                                <?php echo $order['shipping_address']; ?>
                                <?php if ($order['shipping_ward']): ?>, <?php echo $order['shipping_ward']; ?><?php endif; ?>
                                <?php if ($order['shipping_district']): ?>, <?php echo $order['shipping_district']; ?><?php endif; ?>
                                <?php if ($order['shipping_city']): ?>, <?php echo $order['shipping_city']; ?><?php endif; ?>
                            </p>
                            <?php if ($order['delivery_date']): ?>
                                <p><strong>Ngày giao:</strong> <?php echo date('d/m/Y', strtotime($order['delivery_date'])); ?></p>
                            <?php endif; ?>
                            <?php if ($order['delivery_time_slot']): ?>
                                <p><strong>Khung giờ:</strong> <?php echo $order['delivery_time_slot']; ?></p>
                            <?php endif; ?>
                        </div>
                    </div>

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

                    <hr>
                    <h5>Sản phẩm đã đặt</h5>
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

                    <div class="row mt-4">
                        <div class="col-md-6">
                            <h6>Phương thức thanh toán</h6>
                            <p><?php echo $order['payment_method_name'] ?? 'N/A'; ?></p>
                        </div>
                        <div class="col-md-6">
                            <h6>Phương thức vận chuyển</h6>
                            <p><?php echo $order['shipping_method_name'] ?? 'N/A'; ?></p>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<?php include 'views/layout/footer.php'; ?>