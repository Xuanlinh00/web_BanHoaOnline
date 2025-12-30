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

<<<<<<< HEAD
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
=======
<div class="admin-dashboard">
    <div class="container-fluid">
        <div class="row mb-4">
            <div class="col-md-12">
                <h2><i class="fas fa-tachometer-alt me-3"></i>Bảng Điều Khiển Quản Trị</h2>
            </div>
        </div>

        <!-- Statistics -->
        <div class="row mb-5">
            <div class="col-md-3 mb-4">
                <div class="card admin-stat-card">
                    <div class="card-body">
                        <h5 class="card-title">📦 Tổng Đơn Hàng</h5>
                        <h2><?php echo $stats['total_orders']; ?></h2>
                        <small>Tất cả đơn hàng trong hệ thống</small>
                    </div>
                </div>
            </div>
            <div class="col-md-3 mb-4">
                <div class="card admin-stat-card">
                    <div class="card-body">
                        <h5 class="card-title">💰 Tổng Doanh Thu</h5>
                        <h2><?php echo number_format($stats['total_revenue'], 0, ',', '.'); ?>đ</h2>
                        <small>Từ các đơn hàng hoàn tất</small>
                    </div>
                </div>
            </div>
            <div class="col-md-3 mb-4">
                <div class="card admin-stat-card">
                    <div class="card-body">
                        <h5 class="card-title">🌸 Tổng Sản Phẩm</h5>
                        <h2><?php echo $stats['total_products']; ?></h2>
                        <small>Sản phẩm đang bán</small>
                    </div>
                </div>
            </div>
            <div class="col-md-3 mb-4">
                <div class="card admin-stat-card">
                    <div class="card-body">
                        <h5 class="card-title">👥 Tổng Khách Hàng</h5>
                        <h2><?php echo $stats['total_users']; ?></h2>
                        <small>Khách hàng đã đăng ký</small>
                    </div>
                </div>
            </div>
        </div>

        <!-- Recent Orders -->
        <div class="row mb-4">
            <div class="col-md-8">
                <div class="card admin-orders-table">
                    <div class="card-header">
                        <i class="fas fa-shopping-bag me-2"></i>Đơn Hàng Gần Đây
                    </div>
                    <div class="card-body p-0">
                        <div class="table-responsive">
                            <table class="table table-sm mb-0">
                                <thead>
                                    <tr>
                                        <th>Mã Đơn</th>
                                        <th>Khách Hàng</th>
                                        <th>Ngày Đặt</th>
                                        <th>Tổng Tiền</th>
                                        <th>Trạng Thái</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php foreach ($recent_orders as $order): ?>
                                        <tr>
                                            <td><strong><?php echo $order['order_code']; ?></strong></td>
                                            <td><?php echo $order['full_name']; ?></td>
                                            <td><?php echo date('d/m/Y', strtotime($order['order_date'])); ?></td>
                                            <td><strong><?php echo number_format($order['total_amount'], 0, ',', '.'); ?>đ</strong></td>
                                            <td>
                                                <span class="badge status-badge bg-<?php 
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
                                            </td>
                                        </tr>
                                    <?php endforeach; ?>
                                </tbody>
                            </table>
                        </div>
                        <div class="p-3">
                            <a href="/web_banhoa/admin-orders.php" class="btn btn-primary admin-btn">
                                <i class="fas fa-eye me-2"></i>Xem Tất Cả Đơn Hàng
                            </a>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Quick Management Links -->
            <div class="col-md-4">
                <div class="card admin-management-card">
                    <div class="card-header">
                        <i class="fas fa-cogs me-2"></i>Quản Lý Hệ Thống
                    </div>
                    <div class="card-body p-0">
                        <div class="list-group list-group-flush">
                            <a href="/web_banhoa/admin-products.php" class="list-group-item list-group-item-action">
                                <i class="fas fa-box"></i> Quản Lý Sản Phẩm
                                <i class="fas fa-chevron-right float-end mt-1"></i>
                            </a>
                            <a href="/web_banhoa/admin-orders.php" class="list-group-item list-group-item-action">
                                <i class="fas fa-shopping-bag"></i> Quản Lý Đơn Hàng
                                <i class="fas fa-chevron-right float-end mt-1"></i>
                            </a>
                            <a href="/web_banhoa/admin-reviews.php" class="list-group-item list-group-item-action">
                                <i class="fas fa-star"></i> Quản Lý Đánh Giá
                                <?php if ($pending_reviews > 0): ?>
                                    <span class="badge bg-danger float-end"><?php echo $pending_reviews; ?></span>
                                <?php else: ?>
                                    <i class="fas fa-chevron-right float-end mt-1"></i>
                                <?php endif; ?>
                            </a>
                            <a href="/web_banhoa/admin-users.php" class="list-group-item list-group-item-action">
                                <i class="fas fa-users"></i> Quản Lý Khách Hàng
                                <i class="fas fa-chevron-right float-end mt-1"></i>
                            </a>
                            <a href="/web_banhoa/admin-categories.php" class="list-group-item list-group-item-action">
                                <i class="fas fa-list"></i> Quản Lý Danh Mục
                                <i class="fas fa-chevron-right float-end mt-1"></i>
                            </a>
                        </div>
>>>>>>> 37c17f0dac4bb260a987b53f0f92d6e4a0c6a329
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
