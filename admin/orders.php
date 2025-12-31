<?php
// ============================================
// FILE: admin/orders.php - QUẢN LÝ ĐƠN HÀNG
// ============================================

// 1. IMPORT CÁC FILE CẤU HÌNH
require_once __DIR__ . '/../config/constants.php';  // Hằng số (APP_URL, ORDER_PENDING, v.v.)
require_once __DIR__ . '/../config/session.php';    // Quản lý session

$page_title = 'Quản lý đơn hàng';

// 2. KIỂM TRA QUYỀN ADMIN
// Nếu không phải admin, sẽ redirect về trang chủ
requireAdmin();

// 3. KẾT NỐI DATABASE
$conn = require __DIR__ . '/../config/database.php';
require_once __DIR__ . '/../models/Order.php';

// 4. KHỞI TẠO MODEL ORDER
$order_model = new Order($conn);

// 5. BIẾN LƯU THÔNG BÁO
$message = '';  // Thông báo thành công
$error = '';    // Thông báo lỗi

// ============================================
// XỬ LÝ CẬP NHẬT TRẠNG THÁI ĐƠN HÀNG
// ============================================
// Khi admin chọn trạng thái mới từ dropdown
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['update_status'])) {
    $order_id = (int)$_POST['order_id'];           // Lấy ID đơn hàng
    $new_status = $_POST['status'];                // Lấy trạng thái mới
    
    // Gọi hàm cập nhật trạng thái từ model
    if ($order_model->updateOrderStatus($order_id, $new_status)) {
        $message = 'Cập nhật trạng thái đơn hàng thành công!';
    } else {
        $error = 'Có lỗi xảy ra khi cập nhật trạng thái';
    }
}

// ============================================
// LẤY DANH SÁCH ĐƠN HÀNG (CÓ PHÂN TRANG)
// ============================================
$page = isset($_GET['page']) ? (int)$_GET['page'] : 1;  // Trang hiện tại (mặc định trang 1)
$orders = $order_model->getAllOrders($page, 20);        // Lấy 20 đơn hàng mỗi trang
?>
<?php include __DIR__ . '/../views/layout/header.php'; ?>

<div class="container-fluid">
    <div class="row">
        <div class="col-md-2">
            <div class="card">
                <div class="card-body">
                    <h6 class="card-title">Menu Admin</h6>
                    <div class="list-group list-group-flush">
                        <a href="<?php echo APP_URL; ?>/admin-dashboard.php" class="list-group-item list-group-item-action">
                            <i class="fas fa-tachometer-alt"></i> Dashboard
                        </a>
                        <a href="<?php echo APP_URL; ?>/admin-products.php" class="list-group-item list-group-item-action">
                            <i class="fas fa-box"></i> Sản phẩm
                        </a>
                        <a href="<?php echo APP_URL; ?>/admin-orders.php" class="list-group-item list-group-item-action active">
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
            <h2 class="mb-4">Quản lý đơn hàng</h2>

            <?php if ($message): ?>
                <div class="alert alert-success alert-dismissible fade show" role="alert">
                    <?php echo $message; ?>
                    <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                </div>
            <?php endif; ?>

            <?php if ($error): ?>
                <div class="alert alert-danger alert-dismissible fade show" role="alert">
                    <?php echo $error; ?>
                    <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                </div>
            <?php endif; ?>

            <div class="card">
                <div class="card-body">
                    <?php if (empty($orders)): ?>
                        <div class="text-center py-5">
                            <i class="fas fa-shopping-cart fa-3x text-muted mb-3"></i>
                            <h5>Chưa có đơn hàng nào</h5>
                            <p class="text-muted">Các đơn hàng sẽ hiển thị ở đây</p>
                        </div>
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
                                    <?php foreach ($orders as $order): ?>
                                        <tr>
                                            <td>
                                                <strong><?php echo $order['order_code']; ?></strong>
                                            </td>
                                            <td>
                                                <?php echo $order['full_name']; ?>
                                                <br>
                                                <small class="text-muted"><?php echo $order['email']; ?></small>
                                            </td>
                                            <td><?php echo date('d/m/Y H:i', strtotime($order['order_date'])); ?></td>
                                            <td><?php echo number_format($order['total_amount'], 0, ',', '.'); ?>đ</td>
                                            <td>
                                                <form method="POST" class="d-inline">
                                                    <input type="hidden" name="order_id" value="<?php echo $order['order_id']; ?>">
                                                    <select name="status" class="form-select form-select-sm" onchange="this.form.submit()">
                                                        <option value="<?php echo ORDER_PENDING; ?>" <?php echo $order['status'] === ORDER_PENDING ? 'selected' : ''; ?>>Chờ xác nhận</option>
                                                        <option value="<?php echo ORDER_CONFIRMED; ?>" <?php echo $order['status'] === ORDER_CONFIRMED ? 'selected' : ''; ?>>Đã xác nhận</option>
                                                        <option value="<?php echo ORDER_SHIPPING; ?>" <?php echo $order['status'] === ORDER_SHIPPING ? 'selected' : ''; ?>>Đang giao</option>
                                                        <option value="<?php echo ORDER_COMPLETED; ?>" <?php echo $order['status'] === ORDER_COMPLETED ? 'selected' : ''; ?>>Hoàn thành</option>
                                                        <option value="<?php echo ORDER_CANCELLED; ?>" <?php echo $order['status'] === ORDER_CANCELLED ? 'selected' : ''; ?>>Đã hủy</option>
                                                    </select>
                                                    <input type="hidden" name="update_status" value="1">
                                                </form>
                                            </td>
                                            <td>
                                                <a href="<?php echo APP_URL; ?>/admin-order-detail.php?id=<?php echo $order['order_id']; ?>" 
                                                   class="btn btn-sm btn-outline-primary">
                                                    <i class="fas fa-eye"></i> Chi tiết
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
