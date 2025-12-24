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

<div class="container-fluid">
    <div class="row mb-4">
        <div class="col-md-12">
            <h2>Bảng điều khiển</h2>
        </div>
    </div>

    <!-- Statistics -->
    <div class="row mb-4">
        <div class="col-md-3">
            <div class="card text-white bg-primary">
                <div class="card-body">
                    <h5 class="card-title">Tổng đơn hàng</h5>
                    <h2><?php echo $stats['total_orders']; ?></h2>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="card text-white bg-success">
                <div class="card-body">
                    <h5 class="card-title">Doanh thu</h5>
                    <h2><?php echo number_format($stats['total_revenue'], 0, ',', '.'); ?>đ</h2>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="card text-white bg-info">
                <div class="card-body">
                    <h5 class="card-title">Sản phẩm</h5>
                    <h2><?php echo $stats['total_products']; ?></h2>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="card text-white bg-warning">
                <div class="card-body">
                    <h5 class="card-title">Khách hàng</h5>
                    <h2><?php echo $stats['total_users']; ?></h2>
                </div>
            </div>
        </div>
    </div>

    <!-- Recent Orders -->
    <div class="row mb-4">
        <div class="col-md-8">
            <div class="card">
                <div class="card-header">
                    <h5 class="mb-0">Đơn hàng gần đây</h5>
                </div>
                <div class="card-body">
                    <div class="table-responsive">
                        <table class="table table-sm">
                            <thead>
                                <tr>
                                    <th>Mã đơn</th>
                                    <th>Khách hàng</th>
                                    <th>Ngày đặt</th>
                                    <th>Tổng tiền</th>
                                    <th>Trạng thái</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php foreach ($recent_orders as $order): ?>
                                    <tr>
                                        <td><strong><?php echo $order['order_code']; ?></strong></td>
                                        <td><?php echo $order['full_name']; ?></td>
                                        <td><?php echo date('d/m/Y', strtotime($order['order_date'])); ?></td>
                                        <td><?php echo number_format($order['total_amount'], 0, ',', '.'); ?>đ</td>
                                        <td>
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
                                        </td>
                                    </tr>
                                <?php endforeach; ?>
                            </tbody>
                        </table>
                    </div>
                    <a href="/web_banhoa/admin-orders.php" class="btn btn-sm btn-primary">Xem tất cả</a>
                </div>
            </div>
        </div>

        <!-- Quick Links -->
        <div class="col-md-4">
            <div class="card">
                <div class="card-header">
                    <h5 class="mb-0">Quản lý</h5>
                </div>
                <div class="card-body">
                    <div class="list-group">
                        <a href="/web_banhoa/admin-products.php" class="list-group-item list-group-item-action">
                            <i class="fas fa-box"></i> Sản phẩm
                        </a>
                        <a href="/web_banhoa/admin-orders.php" class="list-group-item list-group-item-action">
                            <i class="fas fa-shopping-bag"></i> Đơn hàng
                        </a>
                        <a href="/web_banhoa/admin-reviews.php" class="list-group-item list-group-item-action">
                            <i class="fas fa-star"></i> Đánh giá
                            <?php if ($pending_reviews > 0): ?>
                                <span class="badge bg-danger float-end"><?php echo $pending_reviews; ?></span>
                            <?php endif; ?>
                        </a>
                        <a href="/web_banhoa/admin-users.php" class="list-group-item list-group-item-action">
                            <i class="fas fa-users"></i> Khách hàng
                        </a>
                        <a href="/web_banhoa/admin-categories.php" class="list-group-item list-group-item-action">
                            <i class="fas fa-list"></i> Danh mục
                        </a>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<?php include 'views/layout/footer.php'; ?>
