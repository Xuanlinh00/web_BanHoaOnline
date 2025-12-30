<?php
require_once __DIR__ . '/../config/constants.php';
require_once __DIR__ . '/../config/session.php';

$page_title = 'Bảng điều khiển Admin';

// Require admin
requireAdmin();

$conn = require __DIR__ . '/../config/database.php';

// Get statistics
$stats = [];

// Total products
$result = $conn->query("SELECT COUNT(*) as count FROM products");
$stats['products'] = $result->fetch_assoc()['count'];

// Total orders
$result = $conn->query("SELECT COUNT(*) as count FROM orders");
$stats['orders'] = $result->fetch_assoc()['count'];

// Total users
$result = $conn->query("SELECT COUNT(*) as count FROM users WHERE role = 'customer'");
$stats['users'] = $result->fetch_assoc()['count'];

// Total revenue
$result = $conn->query("SELECT SUM(total_amount) as total FROM orders WHERE status = 'completed'");
$stats['revenue'] = $result->fetch_assoc()['total'] ?? 0;

// Recent orders
$recent_orders = $conn->query("
    SELECT o.*, u.full_name 
    FROM orders o 
    JOIN users u ON o.user_id = u.user_id 
    ORDER BY o.order_date DESC 
    LIMIT 5
")->fetch_all(MYSQLI_ASSOC);
?>
<?php include __DIR__ . '/../views/layout/header.php'; ?>

<div class="container-fluid">
    <div class="row">
        <div class="col-md-2">
            <div class="card">
                <div class="card-body">
                    <h6 class="card-title">Menu Admin</h6>
                    <div class="list-group list-group-flush">
                        <a href="<?php echo APP_URL; ?>/admin-dashboard.php" class="list-group-item list-group-item-action active">
                            <i class="fas fa-tachometer-alt"></i> Dashboard
                        </a>
                        <a href="<?php echo APP_URL; ?>/admin-products.php" class="list-group-item list-group-item-action">
                            <i class="fas fa-box"></i> Sản phẩm
                        </a>
                        <a href="<?php echo APP_URL; ?>/admin-orders.php" class="list-group-item list-group-item-action">
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
            <h2 class="mb-4">Bảng điều khiển</h2>

            <!-- Statistics Cards -->
            <div class="row mb-4">
                <div class="col-md-3">
                    <div class="card bg-primary text-white">
                        <div class="card-body">
                            <div class="d-flex justify-content-between">
                                <div>
                                    <h4><?php echo number_format($stats['products']); ?></h4>
                                    <p class="mb-0">Sản phẩm</p>
                                </div>
                                <div class="align-self-center">
                                    <i class="fas fa-box fa-2x"></i>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="col-md-3">
                    <div class="card bg-success text-white">
                        <div class="card-body">
                            <div class="d-flex justify-content-between">
                                <div>
                                    <h4><?php echo number_format($stats['orders']); ?></h4>
                                    <p class="mb-0">Đơn hàng</p>
                                </div>
                                <div class="align-self-center">
                                    <i class="fas fa-shopping-cart fa-2x"></i>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="col-md-3">
                    <div class="card bg-info text-white">
                        <div class="card-body">
                            <div class="d-flex justify-content-between">
                                <div>
                                    <h4><?php echo number_format($stats['users']); ?></h4>
                                    <p class="mb-0">Khách hàng</p>
                                </div>
                                <div class="align-self-center">
                                    <i class="fas fa-users fa-2x"></i>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="col-md-3">
                    <div class="card bg-warning text-white">
                        <div class="card-body">
                            <div class="d-flex justify-content-between">
                                <div>
                                    <h4><?php echo number_format($stats['revenue'], 0, ',', '.'); ?>đ</h4>
                                    <p class="mb-0">Doanh thu</p>
                                </div>
                                <div class="align-self-center">
                                    <i class="fas fa-dollar-sign fa-2x"></i>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Recent Orders -->
            <div class="card">
                <div class="card-body">
                    <h5 class="card-title">Đơn hàng gần đây</h5>
                    
                    <?php if (empty($recent_orders)): ?>
                        <p class="text-muted">Chưa có đơn hàng nào</p>
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
                                    <?php foreach ($recent_orders as $order): ?>
                                        <tr>
                                            <td><?php echo $order['order_code']; ?></td>
                                            <td><?php echo $order['full_name']; ?></td>
                                            <td><?php echo date('d/m/Y H:i', strtotime($order['order_date'])); ?></td>
                                            <td><?php echo number_format($order['total_amount'], 0, ',', '.'); ?>đ</td>
                                            <td>
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
                                            </td>
                                            <td>
                                                <a href="<?php echo APP_URL; ?>/admin-order-detail.php?id=<?php echo $order['order_id']; ?>" 
                                                   class="btn btn-sm btn-outline-primary">
                                                    <i class="fas fa-eye"></i>
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

<?php include __DIR__ . '/../views/layout/footer.php'; ?>
