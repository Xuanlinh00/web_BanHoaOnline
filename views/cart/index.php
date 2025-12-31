<?php
define('ROOT_DIR', dirname(dirname(__DIR__)));
require_once ROOT_DIR . '/config/constants.php';
require_once ROOT_DIR . '/config/session.php';

$page_title = 'Giỏ hàng';

// Require login
requireLogin();

// Admin cannot access cart
if (isAdmin()) {
    header('Location: ' . APP_URL . '/admin/dashboard.php');
    exit;
}

$conn = require ROOT_DIR . '/config/database.php';
require_once ROOT_DIR . '/models/Cart.php';

$cart = new Cart($conn);
$user_id = getCurrentUserId();

$message = '';
$error = '';

// Handle cart actions
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (isset($_POST['update_cart'])) {
        // Update quantities
        foreach ($_POST['quantity'] as $cart_id => $quantity) {
            $cart->updateQuantity($cart_id, $user_id, (int)$quantity);
        }
        $message = 'Cập nhật giỏ hàng thành công!';
    } elseif (isset($_POST['remove_item'])) {
        // Remove item
        if ($cart->removeItem($_POST['cart_id'], $user_id)) {
            $message = 'Đã xóa sản phẩm khỏi giỏ hàng!';
        }
    } elseif (isset($_POST['clear_cart'])) {
        // Clear cart
        if ($cart->clearCart($user_id)) {
            $message = 'Đã xóa toàn bộ giỏ hàng!';
        }
    }
}

// Get cart items
$cart_items = $cart->getCartItems($user_id);
$total = 0;
?>
<?php include ROOT_DIR . '/views/layout/header.php'; ?>

<div class="container">
    <h2 class="mb-4 gradient-text">Giỏ hàng của bạn</h2>

    <?php if ($message): ?>
        <div class="alert alert-success alert-dismissible fade show" role="alert">
            <i class="fas fa-check-circle"></i> <?php echo $message; ?>
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
    <?php endif; ?>

    <?php if ($error): ?>
        <div class="alert alert-danger alert-dismissible fade show" role="alert">
            <i class="fas fa-exclamation-circle"></i> <?php echo $error; ?>
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
    <?php endif; ?>

    <?php if (empty($cart_items)): ?>
        <div class="alert alert-info text-center">
            <i class="fas fa-shopping-cart fa-3x mb-3"></i>
            <h4>Giỏ hàng trống</h4>
            <p>Bạn chưa có sản phẩm nào trong giỏ hàng.</p>
            <a href="<?php echo APP_URL; ?>/views/products/index.php" class="btn btn-primary rounded-pill">
                <i class="fas fa-shopping-bag"></i> Mua sắm ngay
            </a>
        </div>
    <?php else: ?>
        <div class="row">
            <div class="col-md-8">
                <div class="card">
                    <div class="card-body">
                        <form method="POST" id="cartForm">
                            <div class="table-responsive">
                                <table class="table align-middle">
                                    <thead>
                                        <tr>
                                            <th style="width: 40px;">
                                                <input type="checkbox" id="selectAll" class="form-check-input">
                                            </th>
                                            <th>Sản phẩm</th>
                                            <th>Đơn giá</th>
                                            <th>Số lượng</th>
                                            <th>Thành tiền</th>
                                            <th></th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <?php foreach ($cart_items as $item): ?>
                                            <?php 
                                            $subtotal = $item['price'] * $item['quantity'];
                                            $total += $subtotal;
                                            ?>
                                            <tr>
                                                <td>
                                                    <input type="checkbox" name="selected_items[]" value="<?php echo $item['cart_id']; ?>" 
                                                           class="form-check-input item-checkbox">
                                                </td>
                                                <td>
                                                    <div class="d-flex align-items-center">
                                                        <?php if ($item['image_url']): ?>
                                                            <img src="<?php echo $item['image_url']; ?>" 
                                                                 alt="<?php echo $item['product_name']; ?>" 
                                                                 style="width: 60px; height: 60px; object-fit: cover; margin-right: 15px; border-radius: 8px;"
                                                                 onerror="this.onerror=null; this.style.display='none';">
                                                        <?php endif; ?>
                                                        <div>
                                                            <h6 class="mb-0">
                                                                <a href="<?php echo APP_URL; ?>/views/products/detail.php?id=<?php echo $item['product_id']; ?>" 
                                                                   class="text-decoration-none text-dark">
                                                                    <?php echo $item['product_name']; ?>
                                                                </a>
                                                            </h6>
                                                            <small class="text-muted">Còn lại: <?php echo $item['stock']; ?></small>
                                                        </div>
                                                    </div>
                                                </td>
                                                <td>
                                                    <span class="text-primary fw-bold">
                                                        <?php echo number_format($item['price'], 0, ',', '.'); ?>đ
                                                    </span>
                                                </td>
                                                <td>
                                                    <input type="number" 
                                                           name="quantity[<?php echo $item['cart_id']; ?>]" 
                                                           value="<?php echo $item['quantity']; ?>" 
                                                           min="1" 
                                                           max="<?php echo $item['stock']; ?>" 
                                                           class="form-control" 
                                                           style="width: 80px;">
                                                </td>
                                                <td>
                                                    <strong class="text-danger">
                                                        <?php echo number_format($subtotal, 0, ',', '.'); ?>đ
                                                    </strong>
                                                </td>
                                                <td>
                                                    <form method="POST" style="display: inline;">
                                                        <input type="hidden" name="cart_id" value="<?php echo $item['cart_id']; ?>">
                                                        <button type="submit" name="remove_item" class="btn btn-sm btn-outline-danger" 
                                                                onclick="return confirm('Xóa sản phẩm này khỏi giỏ hàng?')">
                                                            <i class="fas fa-trash"></i>
                                                        </button>
                                                    </form>
                                                </td>
                                            </tr>
                                        <?php endforeach; ?>
                                    </tbody>
                                </table>
                            </div>

                            <div class="d-flex justify-content-between mt-3">
                                <div>
                                    <button type="submit" name="update_cart" class="btn btn-primary">
                                        <i class="fas fa-sync"></i> Cập nhật giỏ hàng
                                    </button>
                                    <button type="submit" name="clear_cart" class="btn btn-outline-danger" 
                                            onclick="return confirm('Xóa toàn bộ giỏ hàng?')">
                                        <i class="fas fa-trash"></i> Xóa giỏ hàng
                                    </button>
                                </div>
                                <button type="button" class="btn btn-success" id="checkoutSelected" disabled>
                                    <i class="fas fa-credit-card"></i> Thanh toán sản phẩm chọn
                                </button>
                            </div>
                        </form>
                    </div>
                </div>
            </div>

            <div class="col-md-4">
                <div class="card">
                    <div class="card-body">
                        <h5 class="card-title">Tổng đơn hàng</h5>
                        <hr>
                        <div class="d-flex justify-content-between mb-2">
                            <span>Tạm tính:</span>
                            <strong id="totalAmount"><?php echo number_format($total, 0, ',', '.'); ?>đ</strong>
                        </div>
                        <div class="d-flex justify-content-between mb-2">
                            <span>Sản phẩm chọn:</span>
                            <strong id="selectedCount">0</strong>
                        </div>
                        <div class="d-flex justify-content-between mb-2">
                            <span>Phí vận chuyển:</span>
                            <strong class="text-muted">Tính sau</strong>
                        </div>
                        <hr>
                        <div class="d-flex justify-content-between mb-3">
                            <strong>Tổng cộng:</strong>
                            <strong class="text-danger h5"><?php echo number_format($total, 0, ',', '.'); ?>đ</strong>
                        </div>
                        <a href="<?php echo APP_URL; ?>/views/checkout/index.php" class="btn btn-primary w-100 rounded-pill">
                            <i class="fas fa-credit-card"></i> Thanh toán tất cả
                        </a>
                        <a href="<?php echo APP_URL; ?>/views/products/index.php" class="btn btn-outline-secondary w-100 rounded-pill mt-2">
                            <i class="fas fa-arrow-left"></i> Tiếp tục mua sắm
                        </a>
                    </div>
                </div>

                <div class="card mt-3">
                    <div class="card-body">
                        <h6 class="card-title"><i class="fas fa-info-circle"></i> Thông tin</h6>
                        <ul class="list-unstyled small mb-0">
                            <li class="mb-2"><i class="fas fa-check text-success"></i> Miễn phí giao hàng cho đơn từ 500.000đ</li>
                            <li class="mb-2"><i class="fas fa-check text-success"></i> Đổi trả trong 7 ngày</li>
                            <li class="mb-2"><i class="fas fa-check text-success"></i> Thanh toán an toàn</li>
                        </ul>
                    </div>
                </div>
            </div>
        </div>
    <?php endif; ?>
</div>

<?php include ROOT_DIR . '/views/layout/footer.php'; ?>

<script>
document.addEventListener('DOMContentLoaded', function() {
    const selectAllCheckbox = document.getElementById('selectAll');
    const itemCheckboxes = document.querySelectorAll('.item-checkbox');
    const checkoutSelectedBtn = document.getElementById('checkoutSelected');
    const selectedCountSpan = document.getElementById('selectedCount');

    // Select all functionality
    selectAllCheckbox.addEventListener('change', function() {
        itemCheckboxes.forEach(checkbox => {
            checkbox.checked = this.checked;
        });
        updateSelectedCount();
    });

    // Individual checkbox change
    itemCheckboxes.forEach(checkbox => {
        checkbox.addEventListener('change', function() {
            updateSelectAllCheckbox();
            updateSelectedCount();
        });
    });

    // Update select all checkbox state
    function updateSelectAllCheckbox() {
        const allChecked = Array.from(itemCheckboxes).every(cb => cb.checked);
        const someChecked = Array.from(itemCheckboxes).some(cb => cb.checked);
        selectAllCheckbox.checked = allChecked;
        selectAllCheckbox.indeterminate = someChecked && !allChecked;
    }

    // Update selected count and button state
    function updateSelectedCount() {
        const selectedCount = Array.from(itemCheckboxes).filter(cb => cb.checked).length;
        selectedCountSpan.textContent = selectedCount;
        checkoutSelectedBtn.disabled = selectedCount === 0;
    }

    // Checkout selected items
    checkoutSelectedBtn.addEventListener('click', function() {
        const selectedItems = Array.from(itemCheckboxes)
            .filter(cb => cb.checked)
            .map(cb => cb.value);

        if (selectedItems.length === 0) {
            alert('Vui lòng chọn ít nhất một sản phẩm');
            return;
        }

        // Create form and submit
        const form = document.createElement('form');
        form.method = 'POST';
        form.action = '<?php echo APP_URL; ?>/views/checkout/index.php';

        selectedItems.forEach(itemId => {
            const input = document.createElement('input');
            input.type = 'hidden';
            input.name = 'selected_items[]';
            input.value = itemId;
            form.appendChild(input);
        });

        document.body.appendChild(form);
        form.submit();
    });
});
</script>
