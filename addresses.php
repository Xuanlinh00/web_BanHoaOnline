<?php
require_once 'config/constants.php';
require_once 'config/session.php';

$page_title = 'Địa chỉ của tôi';

// Require login
requireLogin();

$conn = require 'config/database.php';
require_once 'models/Address.php';

$address_model = new Address($conn);
$user_id = getCurrentUserId();

$message = '';
$error = '';

// Handle form submissions
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (isset($_POST['add_address'])) {
        $data = [
            'recipient_name' => $_POST['recipient_name'],
            'recipient_phone' => $_POST['recipient_phone'],
            'address_line' => $_POST['address_line'],
            'ward' => $_POST['ward'],
            'district' => $_POST['district'],
            'city' => $_POST['city'],
            'is_default' => isset($_POST['is_default'])
        ];

        if ($address_model->addAddress($user_id, $data)) {
            $message = 'Thêm địa chỉ thành công!';
        } else {
            $error = 'Có lỗi xảy ra khi thêm địa chỉ';
        }
    } elseif (isset($_POST['delete_address'])) {
        $address_id = (int)$_POST['address_id'];
        if ($address_model->deleteAddress($address_id, $user_id)) {
            $message = 'Xóa địa chỉ thành công!';
        } else {
            $error = 'Có lỗi xảy ra khi xóa địa chỉ';
        }
    }
}

$addresses = $address_model->getUserAddresses($user_id);
?>
<?php include 'views/layout/header.php'; ?>

<div class="container">
    <div class="row">
        <div class="col-md-3">
            <div class="card">
                <div class="card-body">
                    <h5 class="card-title">Tài khoản của tôi</h5>
                    <div class="list-group list-group-flush">
                        <a href="<?php echo APP_URL; ?>/profile.php" class="list-group-item list-group-item-action">
                            <i class="fas fa-user"></i> Hồ sơ
                        </a>
                        <a href="<?php echo APP_URL; ?>/orders.php" class="list-group-item list-group-item-action">
                            <i class="fas fa-shopping-bag"></i> Đơn hàng
                        </a>
                        <a href="<?php echo APP_URL; ?>/addresses.php" class="list-group-item list-group-item-action active">
                            <i class="fas fa-map-marker-alt"></i> Địa chỉ
                        </a>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-md-9">
            <div class="card">
                <div class="card-body">
                    <div class="d-flex justify-content-between align-items-center mb-4">
                        <h5 class="card-title mb-0">Địa chỉ của tôi</h5>
                        <button type="button" class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#addAddressModal">
                            <i class="fas fa-plus"></i> Thêm địa chỉ
                        </button>
                    </div>

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

                    <?php if (empty($addresses)): ?>
                        <div class="text-center py-5">
                            <i class="fas fa-map-marker-alt fa-3x text-muted mb-3"></i>
                            <h6>Bạn chưa có địa chỉ nào</h6>
                            <p class="text-muted">Thêm địa chỉ để thuận tiện cho việc đặt hàng</p>
                        </div>
                    <?php else: ?>
                        <?php foreach ($addresses as $addr): ?>
                            <div class="card mb-3">
                                <div class="card-body">
                                    <div class="row align-items-center">
                                        <div class="col-md-8">
                                            <h6 class="mb-1">
                                                <?php echo $addr['recipient_name']; ?>
                                                <?php if ($addr['is_default']): ?>
                                                    <span class="badge bg-primary ms-2">Mặc định</span>
                                                <?php endif; ?>
                                            </h6>
                                            <p class="mb-1"><?php echo $addr['recipient_phone']; ?></p>
                                            <p class="text-muted mb-0">
                                                <?php echo $addr['address_line']; ?>
                                                <?php if ($addr['ward']): ?>, <?php echo $addr['ward']; ?><?php endif; ?>
                                                <?php if ($addr['district']): ?>, <?php echo $addr['district']; ?><?php endif; ?>
                                                <?php if ($addr['city']): ?>, <?php echo $addr['city']; ?><?php endif; ?>
                                            </p>
                                        </div>
                                        <div class="col-md-4 text-end">
                                            <form method="POST" class="d-inline">
                                                <input type="hidden" name="address_id" value="<?php echo $addr['address_id']; ?>">
                                                <button type="submit" name="delete_address" class="btn btn-outline-danger btn-sm"
                                                        onclick="return confirm('Bạn có chắc muốn xóa địa chỉ này?')">
                                                    <i class="fas fa-trash"></i> Xóa
                                                </button>
                                            </form>
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

<!-- Add Address Modal -->
<div class="modal fade" id="addAddressModal" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Thêm địa chỉ mới</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <form method="POST">
                <div class="modal-body">
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
                        <label for="address_line" class="form-label">Địa chỉ *</label>
                        <input type="text" class="form-control" id="address_line" name="address_line" 
                               placeholder="Số nhà, tên đường..." required>
                    </div>

                    <div class="row">
                        <div class="col-md-4">
                            <div class="mb-3">
                                <label for="ward" class="form-label">Phường/Xã</label>
                                <input type="text" class="form-control" id="ward" name="ward">
                            </div>
                        </div>
                        <div class="col-md-4">
                            <div class="mb-3">
                                <label for="district" class="form-label">Quận/Huyện</label>
                                <input type="text" class="form-control" id="district" name="district">
                            </div>
                        </div>
                        <div class="col-md-4">
                            <div class="mb-3">
                                <label for="city" class="form-label">Tỉnh/Thành phố</label>
                                <input type="text" class="form-control" id="city" name="city">
                            </div>
                        </div>
                    </div>

                    <div class="form-check">
                        <input class="form-check-input" type="checkbox" id="is_default" name="is_default">
                        <label class="form-check-label" for="is_default">
                            Đặt làm địa chỉ mặc định
                        </label>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Hủy</button>
                    <button type="submit" name="add_address" class="btn btn-primary">Thêm địa chỉ</button>
                </div>
            </form>
        </div>
    </div>
</div>

<?php include 'views/layout/footer.php'; ?>