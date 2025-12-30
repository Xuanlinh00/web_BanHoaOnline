<?php
require_once 'config/constants.php';
require_once 'config/session.php';
requireLogin();

$page_title = 'Đơn hàng của tôi';
$conn = require 'config/database.php';
require_once 'models/Order.php';

$order_model = new Order($conn);
$page = isset($_GET['page']) ? (int)$_GET['page'] : 1;
$orders = $order_model->getUserOrders(getCurrentUserId(), $page);

// Get order items for each order
foreach ($orders as &$order) {
    $query = "SELECT * FROM order_items WHERE order_id = ?";
    $stmt = $conn->prepare($query);
    $stmt->bind_param("i", $order['order_id']);
    $stmt->execute();
    $order['items'] = $stmt->get_result()->fetch_all(MYSQLI_ASSOC);
}
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
            <h3 class="mb-4">Đơn hàng của tôi</h3>

            <?php if (empty($orders)): ?>
                <div class="alert alert-info">
                    Bạn chưa có đơn hàng nào. <a href="/web_banhoa/products/index.php">Mua sắm ngay</a>
                </div>
            <?php else: ?>
                <?php foreach ($orders as $order): ?>
                    <div class="card mb-3">
                        <div class="card-header">
                            <div class="row align-items-center">
                                <div class="col-md-6">
                                    <strong>Mã đơn: <?php echo $order['order_code']; ?></strong><br>
                                    <small class="text-muted"><?php echo date('d/m/Y H:i', strtotime($order['order_date'])); ?></small>
                                </div>
                                <div class="col-md-6 text-end">
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
                                            'cancelled' => 'Đã hủy',
                                            'returned' => 'Trả lại'
                                        ];
                                        echo $status_text[$order['status']] ?? $order['status'];
                                        ?>
                                    </span>
                                </div>
                            </div>
                        </div>
                        <div class="card-body">
                            <div class="row mb-3">
                                <div class="col-md-6">
                                    <strong>Giao hàng:</strong><br>
                                    <?php echo date('d/m/Y', strtotime($order['delivery_date'])); ?> - <?php echo $order['delivery_time_slot']; ?><br>
                                    <strong>Địa chỉ:</strong><br>
                                    <?php echo $order['recipient_name']; ?><br>
                                    <?php echo $order['shipping_address']; ?>, <?php echo $order['shipping_district']; ?>, <?php echo $order['shipping_city']; ?>
                                </div>
                                <div class="col-md-6 text-end">
                                    <strong>Sản phẩm:</strong><br>
                                    <?php foreach ($order['items'] as $item): ?>
                                        <?php echo $item['product_name']; ?> x<?php echo $item['quantity']; ?><br>
                                    <?php endforeach; ?>
                                    <hr>
                                    <strong class="text-danger">Tổng: <?php echo number_format($order['total_amount'], 0, ',', '.'); ?>đ</strong>
                                </div>
                            </div>
                        </div>
                        <div class="card-footer">
                            <a href="/web_banhoa/user-order-detail.php?id=<?php echo $order['order_id']; ?>" class="btn btn-sm btn-primary">
                                Xem chi tiết
                            </a>
                        </div>
                    </div>
                <?php endforeach; ?>
            <?php endif; ?>
        </div>
    </div>
</div>

<?php include 'views/layout/footer.php'; ?>
