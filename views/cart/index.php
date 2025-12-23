<?php
require_once 'config/constants.php';
require_once 'config/session.php';
require_once 'config/database.php';
require_once 'models/Cart.php';

requireLogin();

$page_title = 'Giỏ hàng';
$conn = require 'config/database.php';

$cart = new Cart($conn);
$user_id = getCurrentUserId();
$items = $cart->getCartItems($user_id);

// Handle update quantity
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['update_quantity'])) {
    $cart->updateItemQuantity($_POST['cart_item_id'], $_POST['quantity']);
    header('Location: /web_banhoa/cart.php');
    exit;
}

// Handle remove item
if (isset($_GET['remove'])) {
    $cart->removeItem($_GET['remove']);
    header('Location: /web_banhoa/cart.php');
    exit;
}

$total = $cart->getCartTotal($user_id);
?>
<?php include 'views/layout/header.php'; ?>

<div class="container">
    <h2 class="mb-4">Giỏ hàng</h2>

    <?php if (empty($items)): ?>
        <div class="alert alert-info">
            Giỏ hàng của bạn trống. <a href="/web_banhoa/products.php">Tiếp tục mua sắm</a>
        </div>
    <?php else: ?>
        <div class="row">
            <div class="col-md-8">
                <div class="table-responsive">
                    <table class="table">
                        <thead>
                            <tr>
                                <th>Sản phẩm</th>
                                <th>Giá</th>
                                <th>Số lượng</th>
                                <th>Thành tiền</th>
                                <th></th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach ($items as $item): ?>
                                <tr>
                                    <td>
                                        <div class="d-flex align-items-center">
                                            <img src="<?php echo $item['image_url']; ?>" alt="<?php echo $item['name']; ?>" 
                                                 style="width: 60px; height: 60px; object-fit: cover; margin-right: 10px;">
                                            <a href="/web_banhoa/product-detail.php?id=<?php echo $item['product_id']; ?>">
                                                <?php echo $item['name']; ?>
                                            </a>
                                        </div>
                                    </td>
                                    <td><?php echo number_format($item['price'], 0, ',', '.'); ?>đ</td>
                                    <td>
                                        <form method="POST" class="d-flex align-items-center gap-2">
                                            <input type="hidden" name="cart_item_id" value="<?php echo $item['cart_item_id']; ?>">
                                            <input type="number" name="quantity" value="<?php echo $item['quantity']; ?>" 
                                                   min="1" max="<?php echo $item['stock']; ?>" class="form-control" style="width: 60px;">
                                            <button type="submit" name="update_quantity" class="btn btn-sm btn-outline-primary">
                                                <i class="fas fa-sync"></i>
                                            </button>
                                        </form>
                                    </td>
                                    <td><?php echo number_format($item['price'] * $item['quantity'], 0, ',', '.'); ?>đ</td>
                                    <td>
                                        <a href="/web_banhoa/cart.php?remove=<?php echo $item['cart_item_id']; ?>" 
                                           class="btn btn-sm btn-danger" onclick="return confirm('Xóa sản phẩm này?')">
                                            <i class="fas fa-trash"></i>
                                        </a>
                                    </td>
                                </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                </div>
            </div>

            <div class="col-md-4">
                <div class="card">
                    <div class="card-body">
                        <h5 class="card-title">Tóm tắt đơn hàng</h5>
                        <hr>
                        <div class="d-flex justify-content-between mb-2">
                            <span>Tạm tính:</span>
                            <span><?php echo number_format($total, 0, ',', '.'); ?>đ</span>
                        </div>
                        <div class="d-flex justify-content-between mb-3">
                            <span>Phí vận chuyển:</span>
                            <span>Tính khi thanh toán</span>
                        </div>
                        <hr>
                        <div class="d-flex justify-content-between mb-4">
                            <strong>Tổng cộng:</strong>
                            <strong class="text-danger h5"><?php echo number_format($total, 0, ',', '.'); ?>đ</strong>
                        </div>
                        <a href="/web_banhoa/checkout.php" class="btn btn-primary w-100">
                            Tiến hành thanh toán
                        </a>
                    </div>
                </div>
            </div>
        </div>
    <?php endif; ?>
</div>

<?php include 'views/layout/footer.php'; ?>
