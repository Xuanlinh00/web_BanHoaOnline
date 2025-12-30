<?php
require_once 'config/constants.php';
require_once 'config/session.php';
<<<<<<< HEAD
=======
require_once 'models/Cart.php';

requireLogin();
>>>>>>> 37c17f0dac4bb260a987b53f0f92d6e4a0c6a329

$page_title = 'Giỏ hàng';

// Require login
requireLogin();

$conn = require 'config/database.php';
require_once 'models/Cart.php';

$cart = new Cart($conn);
$user_id = getCurrentUserId();

<<<<<<< HEAD
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
=======
// Handle update quantity
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['update_quantity'])) {
    $cart->updateItemQuantity($_POST['cart_item_id'], $_POST['quantity']);
    header('Location: /web_banhoa/views/cart/index.php');
    exit;
}

// Handle remove item
if (isset($_GET['remove'])) {
    $cart->removeItem($_GET['remove']);
    header('Location: /web_banhoa/views/cart/index.php');
    exit;
}

$total = $cart->getCartTotal($user_id);
$item_count = count($items);
?>
<?php include 'views/layout/header.php'; ?>

<div class="cart-page">
    <div class="container">
        <!-- Breadcrumb -->
        <nav aria-label="breadcrumb" class="mb-4">
            <ol class="breadcrumb">
                <li class="breadcrumb-item"><a href="/web_banhoa/">Trang chủ</a></li>
                <li class="breadcrumb-item active" aria-current="page">Giỏ hàng</li>
            </ol>
        </nav>

        <!-- Header -->
        <div class="cart-header mb-4">
            <h1 class="cart-title">Giỏ hàng của bạn</h1>
            <span class="cart-count">(<?php echo $item_count; ?> sản phẩm)</span>
        </div>

        <?php if (empty($items)): ?>
            <!-- Empty Cart State -->
            <div class="empty-cart">
                <div class="empty-cart-icon">
                    <i class="fas fa-shopping-cart"></i>
                </div>
                <h3>Giỏ hàng của bạn đang trống</h3>
                <p class="text-muted">Hãy khám phá các sản phẩm tuyệt vời của chúng tôi!</p>
                <a href="/web_banhoa/views/products/index.php" class="btn btn-primary btn-lg">
                    <i class="fas fa-shopping-bag me-2"></i>Mua sắm ngay
                </a>
            </div>
        <?php else: ?>
            <form method="POST" action="/web_banhoa/views/checkout/index.php" id="cartForm">
                <div class="row">
                    <!-- Product List - Left Column (70%) -->
                    <div class="col-lg-8 col-md-7">
                        <div class="cart-items">
                            <!-- Select All -->
                            <div class="select-all-section">
                                <div class="form-check">
                                    <input class="form-check-input" type="checkbox" id="selectAll">
                                    <label class="form-check-label fw-bold" for="selectAll">
                                        Chọn tất cả (<?php echo $item_count; ?> sản phẩm)
                                    </label>
                                </div>
                            </div>

                            <!-- Product Items -->
                            <?php foreach ($items as $item): ?>
                                <div class="cart-item">
                                    <div class="item-checkbox">
                                        <input type="checkbox" name="selected_items[]" value="<?php echo $item['cart_item_id']; ?>" 
                                               class="form-check-input item-checkbox" id="item_<?php echo $item['cart_item_id']; ?>">
                                    </div>
                                    
                                    <div class="item-image">
                                        <img src="<?php echo $item['image_url']; ?>" alt="<?php echo $item['name']; ?>">
                                    </div>
                                    
                                    <div class="item-details">
                                        <h5 class="item-name">
                                            <a href="/web_banhoa/views/products/detail.php?id=<?php echo $item['product_id']; ?>">
                                                <?php echo $item['name']; ?>
                                            </a>
                                        </h5>
                                        <div class="item-variants text-muted">
                                            <small>Loại: Hoa tươi</small>
                                        </div>
                                        <div class="item-price">
                                            <span class="current-price"><?php echo number_format($item['price'], 0, ',', '.'); ?>đ</span>
                                        </div>
                                    </div>
                                    
                                    <div class="item-quantity">
                                        <div class="quantity-controls">
                                            <button type="button" class="qty-btn qty-minus" data-id="<?php echo $item['cart_item_id']; ?>">
                                                <i class="fas fa-minus"></i>
                                            </button>
                                            <input type="number" class="qty-input" value="<?php echo $item['quantity']; ?>" 
                                                   min="1" max="<?php echo $item['stock']; ?>" 
                                                   data-id="<?php echo $item['cart_item_id']; ?>" 
                                                   data-price="<?php echo $item['price']; ?>">
                                            <button type="button" class="qty-btn qty-plus" data-id="<?php echo $item['cart_item_id']; ?>">
                                                <i class="fas fa-plus"></i>
                                            </button>
                                        </div>
                                        <small class="stock-info text-muted">Còn <?php echo $item['stock']; ?> sản phẩm</small>
                                    </div>
                                    
                                    <div class="item-total">
                                        <span class="total-price" data-id="<?php echo $item['cart_item_id']; ?>">
                                            <?php echo number_format($item['price'] * $item['quantity'], 0, ',', '.'); ?>đ
                                        </span>
                                    </div>
                                    
                                    <div class="item-actions">
                                        <button type="button" class="btn-remove" data-id="<?php echo $item['cart_item_id']; ?>" 
                                                title="Xóa sản phẩm">
                                            <i class="fas fa-trash-alt"></i>
                                        </button>
                                    </div>
                                </div>
                            <?php endforeach; ?>
                        </div>

                        <!-- Continue Shopping -->
                        <div class="continue-shopping mt-4">
                            <a href="/web_banhoa/views/products/index.php" class="btn btn-outline-primary">
                                <i class="fas fa-arrow-left me-2"></i>Tiếp tục mua sắm
                            </a>
                        </div>
                    </div>

                    <!-- Order Summary - Right Column (30%) -->
                    <div class="col-lg-4 col-md-5">
                        <div class="order-summary sticky-top">
                            <div class="summary-card">
                                <h4 class="summary-title">Tóm tắt đơn hàng</h4>
                                
                                <!-- Price Breakdown -->
                                <div class="price-breakdown">
                                    <div class="price-row">
                                        <span>Tạm tính (<span id="selectedCount">0</span> sản phẩm):</span>
                                        <span id="subtotal">0đ</span>
                                    </div>
                                    <div class="price-row">
                                        <span>Phí vận chuyển:</span>
                                        <span class="shipping-fee">30.000đ</span>
                                    </div>
                                    <hr>
                                    <div class="price-row total-row">
                                        <strong>Tổng cộng:</strong>
                                        <strong class="total-amount" id="totalAmount">0đ</strong>
                                    </div>
                                </div>

                                <!-- Checkout Button -->
                                <button type="submit" class="btn-checkout" id="checkoutBtn" disabled>
                                    <i class="fas fa-lock me-2"></i>
                                    Thanh toán an toàn
                                </button>

                                <!-- Trust Badges -->
                                <div class="trust-badges">
                                    <div class="badges-row">
                                        <img src="/web_banhoa/assets/images/ssl-badge.png" alt="SSL" class="trust-badge">
                                        <img src="/web_banhoa/assets/images/visa-badge.png" alt="Visa" class="trust-badge">
                                        <img src="/web_banhoa/assets/images/mastercard-badge.png" alt="Mastercard" class="trust-badge">
                                    </div>
                                    <p class="trust-text">
                                        <i class="fas fa-shield-alt text-success me-1"></i>
                                        Thanh toán được bảo mật 100%
                                    </p>
                                </div>
                            </div>
                        </div>
>>>>>>> 37c17f0dac4bb260a987b53f0f92d6e4a0c6a329
                    </div>
                </div>
            </form>
        <?php endif; ?>
    </div>
</div>

<<<<<<< HEAD
<?php include 'views/layout/footer.php'; ?>
=======
<!-- Mobile Sticky Bottom Bar -->
<div class="mobile-checkout-bar d-md-none">
    <div class="mobile-total">
        <span class="mobile-total-label">Tổng:</span>
        <span class="mobile-total-amount" id="mobileTotalAmount">0đ</span>
    </div>
    <button type="button" class="mobile-checkout-btn" id="mobileCheckoutBtn" disabled>
        Thanh toán
    </button>
</div>

<script>
document.addEventListener('DOMContentLoaded', function() {
    const selectAllCheckbox = document.getElementById('selectAll');
    const itemCheckboxes = document.querySelectorAll('.item-checkbox');
    const checkoutBtn = document.getElementById('checkoutBtn');
    const mobileCheckoutBtn = document.getElementById('mobileCheckoutBtn');
    
    // Select All functionality
    selectAllCheckbox.addEventListener('change', function() {
        itemCheckboxes.forEach(checkbox => {
            checkbox.checked = this.checked;
        });
        updateOrderSummary();
    });

    // Individual checkbox change
    itemCheckboxes.forEach(checkbox => {
        checkbox.addEventListener('change', function() {
            updateSelectAllState();
            updateOrderSummary();
        });
    });

    // Quantity controls
    document.querySelectorAll('.qty-minus').forEach(btn => {
        btn.addEventListener('click', function() {
            const input = this.parentNode.querySelector('.qty-input');
            if (input.value > 1) {
                input.value = parseInt(input.value) - 1;
                updateItemTotal(input);
                updateOrderSummary();
            }
        });
    });

    document.querySelectorAll('.qty-plus').forEach(btn => {
        btn.addEventListener('click', function() {
            const input = this.parentNode.querySelector('.qty-input');
            const max = parseInt(input.getAttribute('max'));
            if (parseInt(input.value) < max) {
                input.value = parseInt(input.value) + 1;
                updateItemTotal(input);
                updateOrderSummary();
            }
        });
    });

    // Quantity input change
    document.querySelectorAll('.qty-input').forEach(input => {
        input.addEventListener('change', function() {
            updateItemTotal(this);
            updateOrderSummary();
        });
    });

    // Remove item
    document.querySelectorAll('.btn-remove').forEach(btn => {
        btn.addEventListener('click', function() {
            if (confirm('Bạn có chắc chắn muốn xóa sản phẩm này?')) {
                const itemId = this.getAttribute('data-id');
                window.location.href = '/web_banhoa/views/cart/index.php?remove=' + itemId;
            }
        });
    });

    // Mobile checkout button
    if (mobileCheckoutBtn) {
        mobileCheckoutBtn.addEventListener('click', function() {
            if (!this.disabled) {
                document.getElementById('cartForm').submit();
            }
        });
    }

    function updateSelectAllState() {
        const checkedCount = document.querySelectorAll('.item-checkbox:checked').length;
        const totalCount = itemCheckboxes.length;
        
        selectAllCheckbox.checked = checkedCount === totalCount;
        selectAllCheckbox.indeterminate = checkedCount > 0 && checkedCount < totalCount;
    }

    function updateItemTotal(input) {
        const price = parseFloat(input.getAttribute('data-price'));
        const quantity = parseInt(input.value);
        const itemId = input.getAttribute('data-id');
        const totalElement = document.querySelector(`[data-id="${itemId}"]`);
        
        if (totalElement) {
            const total = price * quantity;
            totalElement.textContent = new Intl.NumberFormat('vi-VN').format(total) + 'đ';
        }
    }

    function updateOrderSummary() {
        const checkedItems = document.querySelectorAll('.item-checkbox:checked');
        let subtotal = 0;
        let selectedCount = 0;

        checkedItems.forEach(checkbox => {
            const cartItem = checkbox.closest('.cart-item');
            const qtyInput = cartItem.querySelector('.qty-input');
            const price = parseFloat(qtyInput.getAttribute('data-price'));
            const quantity = parseInt(qtyInput.value);
            
            subtotal += price * quantity;
            selectedCount += quantity;
        });

        const shippingFee = selectedCount > 0 ? 30000 : 0;
        const total = subtotal + shippingFee;

        // Update UI
        document.getElementById('selectedCount').textContent = selectedCount;
        document.getElementById('subtotal').textContent = new Intl.NumberFormat('vi-VN').format(subtotal) + 'đ';
        document.getElementById('totalAmount').textContent = new Intl.NumberFormat('vi-VN').format(total) + 'đ';
        
        if (document.getElementById('mobileTotalAmount')) {
            document.getElementById('mobileTotalAmount').textContent = new Intl.NumberFormat('vi-VN').format(total) + 'đ';
        }

        // Enable/disable checkout buttons
        const hasSelection = selectedCount > 0;
        checkoutBtn.disabled = !hasSelection;
        if (mobileCheckoutBtn) {
            mobileCheckoutBtn.disabled = !hasSelection;
        }

        // Add selected items to form
        updateHiddenInputs(checkedItems);
    }

    function updateHiddenInputs(checkedItems) {
        // Remove existing hidden inputs
        document.querySelectorAll('input[name="checkout_selected_items[]"]').forEach(input => {
            input.remove();
        });

        // Add new hidden inputs
        checkedItems.forEach(checkbox => {
            const hiddenInput = document.createElement('input');
            hiddenInput.type = 'hidden';
            hiddenInput.name = 'checkout_selected_items[]';
            hiddenInput.value = checkbox.value;
            document.getElementById('cartForm').appendChild(hiddenInput);
        });
    }

    // Initialize
    updateOrderSummary();
});
</script>

<?php include 'views/layout/footer.php'; ?>
>>>>>>> 37c17f0dac4bb260a987b53f0f92d6e4a0c6a329
