<?php
require_once 'config/constants.php';
require_once 'config/session.php';

$page_title = 'Giỏ hàng';

// Require login
requireLogin();

$conn = require 'config/database.php';
require_once 'models/Cart.php';

$cart = new Cart($conn);
$user_id = getCurrentUserId();

// Handle cart actions
$message = '';
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (isset($_POST['update_quantity'])) {
        $cart_item_id = (int)$_POST['cart_item_id'];
        $quantity = (int)$_POST['quantity'];
        
        if ($cart->updateItemQuantity($cart_item_id, $quantity)) {
            $message = 'Cập nhật giỏ hàng thành công!';
        }
    } elseif (isset($_POST['remove_item'])) {
        $cart_item_id = (int)$_POST['cart_item_id'];
        
        if ($cart->removeItem($cart_item_id)) {
            $message = 'Đã xóa sản phẩm khỏi giỏ hàng!';
        }
    } elseif (isset($_POST['clear_cart'])) {
        if ($cart->clearCart($user_id)) {
            $message = 'Đã xóa tất cả sản phẩm khỏi giỏ hàng!';
        }
    }
}

$cart_items = $cart->getCartItems($user_id);
$cart_total = $cart->getCartTotal($user_id);
?>
<?php include 'views/layout/header.php'; ?>

<div class="container">
    <h2 class="mb-4">Giỏ hàng của bạn</h2>

    <?php if ($message): ?>
        <div class="alert alert-success alert-dismissible fade show" role="alert">
            <?php echo $message; ?>
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
    <?php endif; ?>

    <?php if (empty($cart_items)): ?>
        <div class="text-center py-5">
            <i class="fas fa-shopping-cart fa-3x text-muted mb-3"></i>
            <h4>Giỏ hàng trống</h4>
            <p class="text-muted">Bạn chưa có sản phẩm nào trong giỏ hàng</p>
            <a href="<?php echo APP_URL; ?>/products.php" class="btn btn-primary">Tiếp tục mua sắm</a>
        </div>
    <?php else: ?>
        <div class="row">
            <div class="col-md-8">
                <div class="card">
                    <div class="card-body">
                        <?php foreach ($cart_items as $item): ?>
                            <div class="row align-items-center border-bottom py-3">
                                <div class="col-md-2">
                                    <img src="<?php echo $item['image_url'] ?? APP_URL . '/assets/images/placeholder.jpg'; ?>" 
                                         class="img-fluid rounded" alt="<?php echo $item['name']; ?>">
                                </div>
                                <div class="col-md-4">
                                    <h6><?php echo $item['name']; ?></h6>
                                    <p class="text-muted small">Tồn kho: <?php echo $item['stock']; ?></p>
                                </div>
                                <div class="col-md-2">
                                    <span class="fw-bold text-danger"><?php echo number_format($item['price'], 0, ',', '.'); ?>đ</span>
                                </div>
                                <div class="col-md-2">
                                    <form method="POST" class="d-inline">
                                        <input type="hidden" name="cart_item_id" value="<?php echo $item['cart_item_id']; ?>">
                                        <div class="input-group input-group-sm">
                                            <input type="number" class="form-control" name="quantity" 
                                                   value="<?php echo $item['quantity']; ?>" min="1" max="<?php echo $item['stock']; ?>">
                                            <button type="submit" name="update_quantity" class="btn btn-outline-primary">
                                                <i class="fas fa-sync"></i>
                                            </button>
                                        </div>
                                    </form>
                                </div>
                                <div class="col-md-2 text-end">
                                    <div class="fw-bold"><?php echo number_format($item['price'] * $item['quantity'], 0, ',', '.'); ?>đ</div>
                                    <form method="POST" class="d-inline mt-1">
                                        <input type="hidden" name="cart_item_id" value="<?php echo $item['cart_item_id']; ?>">
                                        <button type="submit" name="remove_item" class="btn btn-sm btn-outline-danger">
                                            <i class="fas fa-trash"></i>
                                        </button>
                                    </form>
                                </div>
                            </div>
                        <?php endforeach; ?>
                    </div>
                </div>

                <div class="mt-3">
                    <form method="POST" class="d-inline">
                        <button type="submit" name="clear_cart" class="btn btn-outline-danger" 
                                onclick="return confirm('Bạn có chắc muốn xóa tất cả sản phẩm?')">
                            <i class="fas fa-trash"></i> Xóa tất cả
                        </button>
                    </form>
                    <a href="<?php echo APP_URL; ?>/products.php" class="btn btn-outline-primary ms-2">
                        <i class="fas fa-arrow-left"></i> Tiếp tục mua sắm
                    </a>
                </div>
            </div>

            <div class="col-md-4">
                <div class="card">
                    <div class="card-body">
                        <h5 class="card-title">Tóm tắt đơn hàng</h5>
                        <hr>
                        <div class="d-flex justify-content-between">
                            <span>Tạm tính:</span>
                            <span><?php echo number_format($cart_total, 0, ',', '.'); ?>đ</span>
                        </div>
                        <div class="d-flex justify-content-between">
                            <span>Phí vận chuyển:</span>
                            <span class="text-muted">Tính khi thanh toán</span>
                        </div>
                        <hr>
                        <div class="d-flex justify-content-between fw-bold">
                            <span>Tổng cộng:</span>
                            <span class="text-danger"><?php echo number_format($cart_total, 0, ',', '.'); ?>đ</span>
                        </div>
                        <a href="<?php echo APP_URL; ?>/checkout.php" class="btn btn-primary w-100 mt-3">
                            <i class="fas fa-credit-card"></i> Thanh toán
                        </a>
                    </div>
                </div>
            </div>
        </div>
    <?php endif; ?>
</div>

<?php include 'views/layout/footer.php'; ?>