<?php
require_once 'config/constants.php';
require_once 'config/session.php';
requireAdmin();

$page_title = 'Quản lý đơn hàng';
$conn = require 'config/database.php';
require_once 'models/Order.php';

$order_model = new Order($conn);
$page = isset($_GET['page']) ? (int)$_GET['page'] : 1;
$status_filter = isset($_GET['status']) ? $_GET['status'] : '';

// Get orders
if ($status_filter) {
    $query = "SELECT o.*, u.full_name, u.email FROM orders o 
              JOIN users u ON o.user_id = u.user_id 
              WHERE o.status = ? 
              ORDER BY o.order_date DESC 
              LIMIT 20 OFFSET ?";
    $stmt = $conn->prepare($query);
    $offset = ($page - 1) * 20;
    $stmt->bind_param("si", $status_filter, $offset);
    $stmt->execute();
    $orders = $stmt->get_result()->fetch_all(MYSQLI_ASSOC);
} else {
    $orders = $order_model->getAllOrders($page);
}

// Handle status update
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['update_status'])) {
    $order_model->updateOrderStatus($_POST['order_id'], $_POST['status']);
    header('Location: ' . APP_URL . '/admin/orders.php');
    exit;
}
?>
<?php include 'views/layout/header.php'; ?>

<div class="container-fluid">
    <div class="row mb-4">
        <div class="col-md-12">
            <h2>Quản lý đơn hàng</h2>
        </div>
    </div>

    <!-- Filter -->
    <div class="row mb-3">
        <div class="col-md-12">
            <div class="btn-group" role="group">
                <a href="<?php echo APP_URL; ?>/admin/orders.php" class="btn btn-outline-primary <?php echo !$status_filter ? 'active' : ''; ?>">
                    Tất cả
                </a>
                <a href="<?php echo APP_URL; ?>/admin/orders.php?status=pending" class="btn btn-outline-warning <?php echo $status_filter === 'pending' ? 'active' : ''; ?>">
                    Chờ xác nhận
                </a>
                <a href="<?php echo APP_URL; ?>/admin/orders.php?status=confirmed" class="btn btn-outline-info <?php echo $status_filter === 'confirmed' ? 'active' : ''; ?>">
                    Đã xác nhận
                </a>
                <a href="<?php echo APP_URL; ?>/admin/orders.php?status=shipping" class="btn btn-outline-info <?php echo $status_filter === 'shipping' ? 'active' : ''; ?>">
                    Đang giao
                </a>
                <a href="<?php echo APP_URL; ?>/admin/orders.php?status=completed" class="btn btn-outline-success <?php echo $status_filter === 'completed' ? 'active' : ''; ?>">
                    Hoàn tất
                </a>
            </div>
        </div>
    </div>

    <!-- Orders Table -->
    <div class="card">
        <div class="table-responsive">
            <table class="table table-hover mb-0">
                <thead class="table-light">
                    <tr>
                        <th>Mã đơn</th>
                        <th>Khách hàng</th>
                        <th>Ngày đặt</th>
                        <th>Giao hàng</th>
                        <th>Tổng tiền</th>
                        <th>Trạng thái</th>
                        <th>Hành động</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($orders as $order): ?>
                        <tr>
                            <td><strong><?php echo $order['order_code']; ?></strong></td>
                            <td>
                                <?php echo $order['full_name']; ?><br>
                                <small class="text-muted"><?php echo $order['email']; ?></small>
                            </td>
                            <td><?php echo date('d/m/Y', strtotime($order['order_date'])); ?></td>
                            <td>
                                <?php echo date('d/m/Y', strtotime($order['delivery_date'])); ?><br>
                                <small><?php echo $order['delivery_time_slot']; ?></small>
                            </td>
                            <td><?php echo number_format($order['total_amount'], 0, ',', '.'); ?>đ</td>
                            <td>
                                <form method="POST" class="d-inline">
                                    <input type="hidden" name="order_id" value="<?php echo $order['order_id']; ?>">
                                    <select name="status" class="form-select form-select-sm" onchange="this.form.submit()">
                                        <option value="pending" <?php echo $order['status'] === 'pending' ? 'selected' : ''; ?>>Chờ xác nhận</option>
                                        <option value="confirmed" <?php echo $order['status'] === 'confirmed' ? 'selected' : ''; ?>>Đã xác nhận</option>
                                        <option value="shipping" <?php echo $order['status'] === 'shipping' ? 'selected' : ''; ?>>Đang giao</option>
                                        <option value="completed" <?php echo $order['status'] === 'completed' ? 'selected' : ''; ?>>Hoàn tất</option>
                                        <option value="cancelled" <?php echo $order['status'] === 'cancelled' ? 'selected' : ''; ?>>Hủy</option>
                                    </select>
                                    <input type="hidden" name="update_status" value="1">
                                </form>
                            </td>
                            <td>
                                <a href="<?php echo APP_URL; ?>/admin/order-detail.php?id=<?php echo $order['order_id']; ?>" 
                                   class="btn btn-sm btn-primary">
                                    <i class="fas fa-eye"></i>
                                </a>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>
    </div>
</div>

<?php include 'views/layout/footer.php'; ?>
