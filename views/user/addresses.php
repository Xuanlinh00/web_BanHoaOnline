<?php
define('ROOT_DIR', dirname(dirname(__DIR__)));

require_once ROOT_DIR . '/config/constants.php';
require_once ROOT_DIR . '/config/session.php';
requireLogin();

$page_title = 'Sổ địa chỉ';
$conn = require ROOT_DIR . '/config/database.php';
require_once ROOT_DIR . '/models/Address.php';

$address_model = new Address($conn);
$user_id = getCurrentUserId();
$addresses = $address_model->getUserAddresses($user_id);

$message = '';
$error = '';

// Handle delete
if (isset($_GET['delete'])) {
    if ($address_model->deleteAddress($_GET['delete'], $user_id)) {
        $message = 'Xóa địa chỉ thành công!';
        $addresses = $address_model->getUserAddresses($user_id);
    } else {
        $error = 'Lỗi xóa địa chỉ';
    }
}

// Handle add/update
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $data = [
        'recipient_name' => $_POST['recipient_name'],
        'recipient_phone' => $_POST['recipient_phone'],
        'address_line' => $_POST['address_line'],
        'ward' => $_POST['ward'] ?? '',
        'district' => $_POST['district'],
        'city' => $_POST['city'],
        'is_default' => isset($_POST['is_default'])
    ];

    if (isset($_POST['address_id']) && $_POST['address_id']) {
        // Update
        if ($address_model->updateAddress($_POST['address_id'], $user_id, $data)) {
            $message = 'Cập nhật địa chỉ thành công!';
        } else {
            $error = 'Lỗi cập nhật địa chỉ';
        }
    } else {
        // Add new
        if ($address_model->addAddress($user_id, $data)) {
            $message = 'Thêm địa chỉ thành công!';
        } else {
            $error = 'Lỗi thêm địa chỉ';
        }
    }

    $addresses = $address_model->getUserAddresses($user_id);
}

$edit_address = null;
if (isset($_GET['edit'])) {
    $edit_address = $address_model->getAddressById($_GET['edit'], $user_id);
}
?>
<?php include ROOT_DIR . '/views/layout/header.php'; ?>

<div class="container">
    <div class="row">
        <div class="col-md-3">
            <div class="list-group">
                <a href="<?php echo APP_URL; ?>/views/user/profile.php" class="list-group-item list-group-item-action">
                    <i class="fas fa-user"></i> Hồ sơ
                </a>
                <a href="<?php echo APP_URL; ?>/views/user/orders.php" class="list-group-item list-group-item-action">
                    <i class="fas fa-shopping-bag"></i> Đơn hàng
                </a>
                <a href="<?php echo APP_URL; ?>/views/user/addresses.php" class="list-group-item list-group-item-action active">
                    <i class="fas fa-map-marker-alt"></i> Địa chỉ
                </a>
            </div>
        </div>

        <div class="col-md-9">
            <h3 class="mb-4">Sổ địa chỉ</h3>

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

            <div class="row">
                <div class="col-md-6">
                    <div class="card">
                        <div class="card-header">
                            <h5 class="mb-0"><?php echo $edit_address ? 'Sửa địa chỉ' : 'Thêm địa chỉ mới'; ?></h5>
                        </div>
                        <div class="card-body">
                            <form method="POST">
                                <?php if ($edit_address): ?>
                                    <input type="hidden" name="address_id" value="<?php echo $edit_address['address_id']; ?>">
                                <?php endif; ?>

                                <div class="mb-3">
                                    <label for="recipient_name" class="form-label">Tên người nhận *</label>
                                    <input type="text" class="form-control" id="recipient_name" name="recipient_name" 
                                           value="<?php echo $edit_address['recipient_name'] ?? ''; ?>" required>
                                </div>

                                <div class="mb-3">
                                    <label for="recipient_phone" class="form-label">Số điện thoại *</label>
                                    <input type="tel" class="form-control" id="recipient_phone" name="recipient_phone" 
                                           value="<?php echo $edit_address['recipient_phone'] ?? ''; ?>" required>
                                </div>

                                <div class="mb-3">
                                    <label for="address_line" class="form-label">Địa chỉ *</label>
                                    <input type="text" class="form-control" id="address_line" name="address_line" 
                                           value="<?php echo $edit_address['address_line'] ?? ''; ?>" required>
                                </div>

                                <div class="mb-3">
                                    <label for="ward" class="form-label">Phường/Xã</label>
                                    <input type="text" class="form-control" id="ward" name="ward" 
                                           value="<?php echo $edit_address['ward'] ?? ''; ?>">
                                </div>

                                <div class="mb-3">
                                    <label for="district" class="form-label">Quận/Huyện *</label>
                                    <input type="text" class="form-control" id="district" name="district" 
                                           value="<?php echo $edit_address['district'] ?? ''; ?>" required>
                                </div>

                                <div class="mb-3">
                                    <label for="city" class="form-label">Thành phố *</label>
                                    <input type="text" class="form-control" id="city" name="city" 
                                           value="<?php echo $edit_address['city'] ?? ''; ?>" required>
                                </div>

                                <div class="form-check mb-3">
                                    <input class="form-check-input" type="checkbox" id="is_default" name="is_default" 
                                           <?php echo ($edit_address && $edit_address['is_default']) ? 'checked' : ''; ?>>
                                    <label class="form-check-label" for="is_default">
                                        Đặt làm địa chỉ mặc định
                                    </label>
                                </div>

                                <button type="submit" class="btn btn-primary w-100">
                                    <?php echo $edit_address ? 'Cập nhật' : 'Thêm'; ?>
                                </button>
                                <?php if ($edit_address): ?>
                                    <a href="<?php echo APP_URL; ?>/views/user/addresses.php" class="btn btn-outline-secondary w-100 mt-2">
                                        Hủy
                                    </a>
                                <?php endif; ?>
                            </form>
                        </div>
                    </div>
                </div>

                <div class="col-md-6">
                    <h5>Danh sách địa chỉ</h5>
                    <?php if (empty($addresses)): ?>
                        <div class="alert alert-info">Chưa có địa chỉ nào</div>
                    <?php else: ?>
                        <?php foreach ($addresses as $addr): ?>
                            <div class="card mb-2">
                                <div class="card-body">
                                    <div class="d-flex justify-content-between align-items-start">
                                        <div>
                                            <h6 class="mb-1"><?php echo $addr['recipient_name']; ?></h6>
                                            <p class="mb-1 small"><?php echo $addr['address_line']; ?></p>
                                            <p class="mb-1 small"><?php echo $addr['ward']; ?> <?php echo $addr['district']; ?>, <?php echo $addr['city']; ?></p>
                                            <p class="mb-0 small text-muted"><?php echo $addr['recipient_phone']; ?></p>
                                            <?php if ($addr['is_default']): ?>
                                                <span class="badge bg-success mt-2">Mặc định</span>
                                            <?php endif; ?>
                                        </div>
                                        <div>
                                            <a href="<?php echo APP_URL; ?>/views/user/addresses.php?edit=<?php echo $addr['address_id']; ?>" 
                                               class="btn btn-sm btn-outline-primary">
                                                <i class="fas fa-edit"></i>
                                            </a>
                                            <a href="<?php echo APP_URL; ?>/views/user/addresses.php?delete=<?php echo $addr['address_id']; ?>" 
                                               class="btn btn-sm btn-outline-danger" onclick="return confirm('Xóa địa chỉ này?')">
                                                <i class="fas fa-trash"></i>
                                            </a>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        <?php endforeach; ?>
                    <?php endif; ?>
                </div>
            </div>
        </div>
    </div>
</div>

<?php include ROOT_DIR . '/views/layout/footer.php'; ?>
