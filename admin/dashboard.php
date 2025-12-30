<?php
require_once 'config/constants.php';
require_once 'config/session.php';
requireAdmin();

$page_title = 'Bảng điều khiển';
$conn = require 'config/database.php';

// Get statistics
$stats = [];

// Total orders
$result = $conn->query("SELECT COUNT(*) as total FROM orders");
$stats['total_orders'] = $result->fetch_assoc()['total'];

// Total revenue
$result = $conn->query("SELECT SUM(total_amount) as total FROM orders WHERE status = 'completed'");
$row = $result->fetch_assoc();
$stats['total_revenue'] = $row['total'] ?? 0;

// Total products
$result = $conn->query("SELECT COUNT(*) as total FROM products");
$stats['total_products'] = $result->fetch_assoc()['total'];

// Total users
$result = $conn->query("SELECT COUNT(*) as total FROM users WHERE role = 'customer'");
$stats['total_users'] = $result->fetch_assoc()['total'];

// Recent orders
$result = $conn->query("SELECT o.*, u.full_name FROM orders o JOIN users u ON o.user_id = u.user_id ORDER BY o.order_date DESC LIMIT 5");
$recent_orders = $result->fetch_all(MYSQLI_ASSOC);

// Pending reviews
$result = $conn->query("SELECT COUNT(*) as total FROM reviews WHERE status = 'pending'");
$pending_reviews = $result->fetch_assoc()['total'];
?>
<?php include 'views/layout/header.php'; ?>

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
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<?php include 'views/layout/footer.php'; ?>
