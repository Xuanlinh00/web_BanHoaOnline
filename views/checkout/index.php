<?php
define('ROOT_DIR', dirname(dirname(__DIR__)));

require_once ROOT_DIR . '/config/constants.php';
require_once ROOT_DIR . '/config/session.php';

$page_title = 'Thanh toán';

// Require login
requireLogin();

// Admin cannot checkout
if (isAdmin()) {
    header('Location: ' . APP_URL . '/admin/dashboard.php');
    exit;
}

$conn = require ROOT_DIR . '/config/database.php';
require_once ROOT_DIR . '/models/Cart.php';
require_once ROOT_DIR . '/models/Order.php';
require_once ROOT_DIR . '/models/Address.php';

$cart = new Cart($conn);
$order = new Order($conn);
$address = new Address($conn);

$user_id = getCurrentUserId();

// Get cart items
$all_cart_items = $cart->getCartItems($user_id);
$cart_total = $cart->getCartTotal($user_id);

// Check if specific items are selected
$selected_items = $_POST['selected_items'] ?? [];
if (!empty($selected_items)) {
    // Filter cart items to only include selected ones
    $cart_items = array_filter($all_cart_items, function($item) use ($selected_items) {
        return in_array($item['cart_id'], $selected_items);
    });
    
    // Recalculate total for selected items
    $cart_total = 0;
    foreach ($cart_items as $item) {
        $cart_total += $item['price'] * $item['quantity'];
    }
    $cart_items = array_values($cart_items); // Re-index array
} else {
    $cart_items = $all_cart_items;
}

if (empty($cart_items)) {
    header('Location: ' . APP_URL . '/views/cart/index.php');
    exit;
}

$user_addresses = $address->getUserAddresses($user_id);
$error = '';
$success = '';

// Handle order submission
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['delivery_date'])) {
    $delivery_date = $_POST['delivery_date'] ?? '';
    $delivery_time_slot = $_POST['delivery_time_slot'] ?? '';
    $message_card = $_POST['message_card'] ?? '';
    $recipient_name = $_POST['recipient_name'] ?? '';
    $recipient_phone = $_POST['recipient_phone'] ?? '';
    $shipping_address = $_POST['shipping_address'] ?? '';
    $shipping_ward = $_POST['shipping_ward'] ?? '';
    $shipping_district = $_POST['shipping_district'] ?? '';
    $shipping_city = $_POST['shipping_city'] ?? '';
    $payment_method_id = $_POST['payment_method_id'] ?? 1;
    $notes = $_POST['notes'] ?? '';

    if (empty($delivery_date) || empty($recipient_name) || empty($recipient_phone) || empty($shipping_address)) {
        $error = 'Vui lòng điền đầy đủ thông tin bắt buộc';
    } else {
        $order_data = [
            'delivery_date' => $delivery_date,
            'delivery_time_slot' => $delivery_time_slot,
            'message_card' => $message_card,
            'is_anonymous' => isset($_POST['is_anonymous']),
            'recipient_name' => $recipient_name,
            'recipient_phone' => $recipient_phone,
            'shipping_address' => $shipping_address,
            'shipping_ward' => $shipping_ward,
            'shipping_district' => $shipping_district,
            'shipping_city' => $shipping_city,
            'subtotal' => $cart_total,
            'shipping_fee' => 30000, // Fixed shipping fee
            'shipping_method_id' => 1,
            'payment_method_id' => $payment_method_id,
            'notes' => $notes
        ];

        $result = $order->createOrder($user_id, $order_data);
        
        if ($result['success']) {
            header('Location: ' . APP_URL . '/views/checkout/confirmation.php?order_id=' . $result['order_id']);
            exit;
        } else {
            $error = $result['message'];
        }
    }
}
?>
<?php include ROOT_DIR . '/views/layout/header.php'; ?>

<div class="container">
    <h2 class="mb-4 gradient-text">Thanh toán</h2>

    <?php if ($error): ?>
        <div class="alert alert-danger alert-dismissible fade show" role="alert">
            <i class="fas fa-exclamation-circle"></i> <?php echo $error; ?>
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
    <?php endif; ?>

    <form method="POST">
        <div class="row">
            <div class="col-md-8">
                <!-- Delivery Information -->
                <div class="card mb-4">
                    <div class="card-body">
                        <h5 class="card-title">Thông tin giao hàng</h5>
                        
                        <div class="row">
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label for="delivery_date" class="form-label">Ngày giao hàng *</label>
                                    <input type="date" class="form-control" id="delivery_date" name="delivery_date" 
                                           min="<?php echo date('Y-m-d', strtotime('+1 day')); ?>" required>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label for="delivery_time_slot" class="form-label">Khung giờ giao hàng</label>
                                    <select class="form-select" id="delivery_time_slot" name="delivery_time_slot">
                                        <option value="">Chọn khung giờ</option>
                                        <option value="08:00-12:00">8:00 - 12:00</option>
                                        <option value="13:00-17:00">13:00 - 17:00</option>
                                        <option value="18:00-21:00">18:00 - 21:00</option>
                                    </select>
                                </div>
                            </div>
                        </div>

                        <div class="mb-3">
                            <label for="message_card" class="form-label">Lời nhắn trên thiệp</label>
                            <textarea class="form-control" id="message_card" name="message_card" rows="3" 
                                      placeholder="Nhập lời chúc hoặc lời nhắn..."></textarea>
                        </div>

                        <div class="form-check">
                            <input class="form-check-input" type="checkbox" id="is_anonymous" name="is_anonymous">
                            <label class="form-check-label" for="is_anonymous">
                                Gửi ẩn danh (không ghi tên người gửi)
                            </label>
                        </div>
                    </div>
                </div>

                <!-- Recipient Information -->
                <div class="card mb-4">
                    <div class="card-body">
                        <h5 class="card-title">Thông tin người nhận</h5>
                        
                        <div class="row">
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label for="recipient_name" class="form-label">Tên người nhận *</label>
                                    <input type="text" class="form-control" id="recipient_name" name="recipient_name" required>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label for="recipient_phone" class="form-label">Số điện thoại *</label>
                                    <input type="tel" class="form-control" id="recipient_phone" name="recipient_phone" required>
                                </div>
                            </div>
                        </div>

                        <div class="mb-3">
                            <label for="shipping_address" class="form-label">Địa chỉ *</label>
                            <input type="text" class="form-control" id="shipping_address" name="shipping_address" 
                                   placeholder="Số nhà, tên đường..." required>
                        </div>

                        <div class="row">
                            <div class="col-md-4">
                                <div class="mb-3">
                                    <label for="shipping_ward" class="form-label">Phường/Xã</label>
                                    <input type="text" class="form-control" id="shipping_ward" name="shipping_ward">
                                </div>
                            </div>
                            <div class="col-md-4">
                                <div class="mb-3">
                                    <label for="shipping_district" class="form-label">Quận/Huyện</label>
                                    <input type="text" class="form-control" id="shipping_district" name="shipping_district">
                                </div>
                            </div>
                            <div class="col-md-4">
                                <div class="mb-3">
                                    <label for="shipping_city" class="form-label">Tỉnh/Thành phố</label>
                                    <input type="text" class="form-control" id="shipping_city" name="shipping_city">
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Payment Method -->
                <div class="card mb-4">
                    <div class="card-body">
                        <h5 class="card-title">Phương thức thanh toán</h5>
                        
                        <div class="form-check">
                            <input class="form-check-input" type="radio" name="payment_method_id" id="cod" value="1" checked>
                            <label class="form-check-label" for="cod">
                                <i class="fas fa-money-bill-wave"></i> Thanh toán khi nhận hàng (COD)
                            </label>
                        </div>
                        
                        <div class="form-check">
                            <input class="form-check-input" type="radio" name="payment_method_id" id="bank" value="2">
                            <label class="form-check-label" for="bank">
                                <i class="fas fa-university"></i> Chuyển khoản ngân hàng
                            </label>
                        </div>
                    </div>
                </div>

                <!-- Notes -->
                <div class="card mb-4">
                    <div class="card-body">
                        <h5 class="card-title">Ghi chú</h5>
                        <textarea class="form-control" name="notes" rows="3" 
                                  placeholder="Ghi chú thêm cho đơn hàng..."></textarea>
                    </div>
                </div>
            </div>

            <div class="col-md-4">
                <!-- Order Summary -->
                <div class="card">
                    <div class="card-body">
                        <h5 class="card-title">Đơn hàng của bạn</h5>
                        
                        <?php foreach ($cart_items as $item): ?>
                            <div class="d-flex justify-content-between align-items-center border-bottom py-2">
                                <div class="flex-grow-1">
                                    <h6 class="mb-0"><?php echo $item['product_name']; ?></h6>
                                    <small class="text-muted">Số lượng: <?php echo $item['quantity']; ?></small>
                                </div>
                                <span><?php echo number_format($item['price'] * $item['quantity'], 0, ',', '.'); ?>đ</span>
                            </div>
                        <?php endforeach; ?>

                        <hr>
                        <div class="d-flex justify-content-between">
                            <span>Tạm tính:</span>
                            <span><?php echo number_format($cart_total, 0, ',', '.'); ?>đ</span>
                        </div>
                        <div class="d-flex justify-content-between">
                            <span>Phí vận chuyển:</span>
                            <span>30.000đ</span>
                        </div>
                        <hr>
                        <div class="d-flex justify-content-between fw-bold">
                            <span>Tổng cộng:</span>
                            <span class="text-danger"><?php echo number_format($cart_total + 30000, 0, ',', '.'); ?>đ</span>
                        </div>

                        <button type="submit" class="btn btn-primary w-100 mt-3 rounded-pill">
                            <i class="fas fa-check"></i> Đặt hàng
                        </button>
                    </div>
                </div>
            </div>
        </div>
    </form>
</div>

<?php include ROOT_DIR . '/views/layout/footer.php'; ?>