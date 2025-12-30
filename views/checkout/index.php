<?php
require_once 'config/constants.php';
require_once 'config/session.php';
requireLogin();

$page_title = 'Thanh toán';
$conn = require 'config/database.php';
require_once 'models/Cart.php';
require_once 'models/Address.php';
require_once 'models/Order.php';

$cart_model = new Cart($conn);
$address_model = new Address($conn);
$order_model = new Order($conn);

$user_id = getCurrentUserId();
$all_items = $cart_model->getCartItems($user_id);

// Get selected items from POST data
$selected_item_ids = $_POST['checkout_selected_items'] ?? [];
$items = [];

if (!empty($selected_item_ids)) {
    // Filter only selected items
    foreach ($all_items as $item) {
        if (in_array($item['cart_item_id'], $selected_item_ids)) {
            $items[] = $item;
        }
    }
} else {
    // If no selection, use all items (fallback)
    $items = $all_items;
}

$addresses = $address_model->getUserAddresses($user_id);

// Calculate subtotal for selected items only
$subtotal = 0;
foreach ($items as $item) {
    $subtotal += $item['price'] * $item['quantity'];
}

if (empty($items)) {
    header('Location: /web_banhoa/cart.php');
    exit;
}

$error = '';
$success = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Validate required fields
    if (empty($_POST['delivery_date']) || empty($_POST['delivery_time_slot']) || 
        empty($_POST['recipient_name']) || empty($_POST['recipient_phone']) || 
        empty($_POST['shipping_address']) || empty($_POST['shipping_district']) || 
        empty($_POST['shipping_city'])) {
        $error = 'Vui lòng điền đầy đủ thông tin giao hàng';
    } else {
        // Create order
        $order_data = [
            'delivery_date' => $_POST['delivery_date'],
            'delivery_time_slot' => $_POST['delivery_time_slot'],
            'message_card' => $_POST['message_card'] ?? '',
            'is_anonymous' => isset($_POST['is_anonymous']),
            'recipient_name' => $_POST['recipient_name'],
            'recipient_phone' => $_POST['recipient_phone'],
            'shipping_address' => $_POST['shipping_address'],
            'shipping_ward' => $_POST['shipping_ward'] ?? '',
            'shipping_district' => $_POST['shipping_district'],
            'shipping_city' => $_POST['shipping_city'],
            'subtotal' => $subtotal,
            'shipping_fee' => 30000, // Fixed fee for now
            'shipping_method_id' => 1,
            'payment_method_id' => $_POST['payment_method'] ?? 1,
            'notes' => $_POST['notes'] ?? ''
        ];

        $result = $order_model->createOrder($user_id, $order_data);

        if ($result['success']) {
            // Add only selected items to order
            $order_model->addOrderItems($result['order_id'], $items);
            
            // Remove only selected items from cart
            if (!empty($selected_item_ids)) {
                foreach ($selected_item_ids as $cart_item_id) {
                    $cart_model->removeItem($cart_item_id);
                }
            } else {
                // Clear entire cart if no specific selection
                $cart_model->clearCart($user_id);
            }

            // Redirect to order confirmation
            header('Location: /web_banhoa/checkout-confirmation.php?order_id=' . $result['order_id']);
            exit;
        } else {
            $error = $result['message'];
        }
    }
}

// Get time slots
$time_slots = [
    '08:00 - 10:00',
    '10:00 - 12:00',
    '14:00 - 16:00',
    '16:00 - 18:00',
    '18:00 - 20:00'
];

// Get minimum delivery date (tomorrow)
$min_date = date('Y-m-d', strtotime('+1 day'));
$max_date = date('Y-m-d', strtotime('+30 days'));
?>
<?php include 'views/layout/header.php'; ?>

<div class="container">
    <h2 class="mb-4">Thanh toán</h2>

    <?php if ($error): ?>
        <div class="alert alert-danger alert-dismissible fade show" role="alert">
            <?php echo $error; ?>
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
    <?php endif; ?>

    <div class="row">
        <!-- Order Summary -->
        <div class="col-md-4 order-md-2 mb-4">
            <div class="card">
                <div class="card-body">
                    <h5 class="card-title">Đơn hàng của bạn</h5>
                    <hr>
                    <?php foreach ($items as $item): ?>
                        <div class="d-flex justify-content-between mb-2">
                            <span><?php echo $item['name']; ?> x<?php echo $item['quantity']; ?></span>
                            <span><?php echo number_format($item['price'] * $item['quantity'], 0, ',', '.'); ?>đ</span>
                        </div>
                    <?php endforeach; ?>
                    <hr>
                    <div class="d-flex justify-content-between mb-2">
                        <span>Tạm tính:</span>
                        <span><?php echo number_format($subtotal, 0, ',', '.'); ?>đ</span>
                    </div>
                    <div class="d-flex justify-content-between mb-3">
                        <span>Phí vận chuyển:</span>
                        <span>30.000đ</span>
                    </div>
                    <hr>
                    <div class="d-flex justify-content-between">
                        <strong>Tổng cộng:</strong>
                        <strong class="text-danger h5"><?php echo number_format($subtotal + 30000, 0, ',', '.'); ?>đ</strong>
                    </div>
                </div>
            </div>
        </div>

        <!-- Checkout Form -->
        <div class="col-md-8 order-md-1">
            <form method="POST">
                <!-- Hidden inputs for selected items -->
                <?php if (!empty($selected_item_ids)): ?>
                    <?php foreach ($selected_item_ids as $item_id): ?>
                        <input type="hidden" name="checkout_selected_items[]" value="<?php echo $item_id; ?>">
                    <?php endforeach; ?>
                <?php endif; ?>
                <!-- Delivery Info -->
                <div class="card mb-4">
                    <div class="card-header">
                        <h5 class="mb-0">Thông tin giao hàng</h5>
                    </div>
                    <div class="card-body">
                        <div class="row">
                            <div class="col-md-6 mb-3">
                                <label for="delivery_date" class="form-label">Ngày giao hàng *</label>
                                <input type="date" class="form-control" id="delivery_date" name="delivery_date" 
                                       min="<?php echo $min_date; ?>" max="<?php echo $max_date; ?>" required>
                            </div>
                            <div class="col-md-6 mb-3">
                                <label for="delivery_time_slot" class="form-label">Khung giờ giao hàng *</label>
                                <select class="form-select" id="delivery_time_slot" name="delivery_time_slot" required>
                                    <option value="">-- Chọn khung giờ --</option>
                                    <?php foreach ($time_slots as $slot): ?>
                                        <option value="<?php echo $slot; ?>"><?php echo $slot; ?></option>
                                    <?php endforeach; ?>
                                </select>
                            </div>
                        </div>

                        <div class="mb-3">
                            <label for="message_card" class="form-label">Thiệp chúc mừng (tùy chọn)</label>
                            <textarea class="form-control" id="message_card" name="message_card" rows="3" 
                                      placeholder="Viết lời chúc mừng..."></textarea>
                        </div>

                        <div class="form-check">
                            <input class="form-check-input" type="checkbox" id="is_anonymous" name="is_anonymous">
                            <label class="form-check-label" for="is_anonymous">
                                Gửi ẩn danh (không hiển thị tên người gửi)
                            </label>
                        </div>
                    </div>
                </div>

                <!-- Recipient Info -->
                <div class="card mb-4">
                    <div class="card-header">
                        <h5 class="mb-0">Thông tin người nhận</h5>
                    </div>
                    <div class="card-body">
                        <?php if (!empty($addresses)): ?>
                            <div class="mb-3">
                                <label class="form-label">Chọn từ sổ địa chỉ</label>
                                <select class="form-select" id="address_select" onchange="fillAddress(this.value)">
                                    <option value="">-- Nhập địa chỉ mới --</option>
                                    <?php foreach ($addresses as $addr): ?>
                                        <option value="<?php echo htmlspecialchars(json_encode($addr)); ?>">
                                            <?php echo $addr['recipient_name']; ?> - <?php echo $addr['address_line']; ?>
                                        </option>
                                    <?php endforeach; ?>
                                </select>
                            </div>
                            <hr>
                        <?php endif; ?>

                        <div class="row">
                            <div class="col-md-6 mb-3">
                                <label for="recipient_name" class="form-label">Tên người nhận *</label>
                                <input type="text" class="form-control" id="recipient_name" name="recipient_name" required>
                            </div>
                            <div class="col-md-6 mb-3">
                                <label for="recipient_phone" class="form-label">Số điện thoại *</label>
                                <input type="tel" class="form-control" id="recipient_phone" name="recipient_phone" required>
                            </div>
                        </div>

                        <div class="mb-3">
                            <label for="shipping_address" class="form-label">Địa chỉ *</label>
                            <input type="text" class="form-control" id="shipping_address" name="shipping_address" required>
                        </div>

                        <div class="row">
                            <div class="col-md-4 mb-3">
                                <label for="shipping_ward" class="form-label">Phường/Xã</label>
                                <input type="text" class="form-control" id="shipping_ward" name="shipping_ward">
                            </div>
                            <div class="col-md-4 mb-3">
                                <label for="shipping_district" class="form-label">Quận/Huyện *</label>
                                <input type="text" class="form-control" id="shipping_district" name="shipping_district" required>
                            </div>
                            <div class="col-md-4 mb-3">
                                <label for="shipping_city" class="form-label">Thành phố *</label>
                                <input type="text" class="form-control" id="shipping_city" name="shipping_city" required>
                            </div>
                        </div>

                        <div class="mb-3">
                            <label for="notes" class="form-label">Ghi chú (tùy chọn)</label>
                            <textarea class="form-control" id="notes" name="notes" rows="2" 
                                      placeholder="VD: Gọi trước khi giao, nhà có chó dữ..."></textarea>
                        </div>
                    </div>
                </div>

                <!-- Payment Method -->
                <div class="card mb-4">
                    <div class="card-header">
                        <h5 class="mb-0">Phương thức thanh toán</h5>
                    </div>
                    <div class="card-body">
                        <div class="form-check">
                            <input class="form-check-input" type="radio" name="payment_method" id="payment_cod" value="1" checked>
                            <label class="form-check-label" for="payment_cod">
                                Thanh toán khi nhận hàng (COD)
                            </label>
                        </div>
                    </div>
                </div>

                <button type="submit" class="btn btn-primary btn-lg w-100">
                    Đặt hàng
                </button>
            </form>
        </div>
    </div>
</div>

<script>
function fillAddress(value) {
    if (!value) return;
    const addr = JSON.parse(value);
    document.getElementById('recipient_name').value = addr.recipient_name;
    document.getElementById('recipient_phone').value = addr.recipient_phone;
    document.getElementById('shipping_address').value = addr.address_line;
    document.getElementById('shipping_ward').value = addr.ward;
    document.getElementById('shipping_district').value = addr.district;
    document.getElementById('shipping_city').value = addr.city;
}
</script>

<?php include 'views/layout/footer.php'; ?>
