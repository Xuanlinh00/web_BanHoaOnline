<?php
require_once __DIR__ . '/../config/constants.php';
require_once __DIR__ . '/../config/session.php';

$page_title = 'Sửa sản phẩm';

// Require admin
requireAdmin();

if (!isset($_GET['id'])) {
    header('Location: ' . APP_URL . '/admin-products.php');
    exit;
}

$conn = require __DIR__ . '/../config/database.php';
require_once __DIR__ . '/../models/Product.php';
require_once __DIR__ . '/../models/Category.php';

$product_model = new Product($conn);
$category_model = new Category($conn);

$product_id = (int)$_GET['id'];
$product = $product_model->getProductById($product_id);

if (!$product) {
    header('Location: ' . APP_URL . '/admin-products.php');
    exit;
}

$categories = $category_model->getAllCategories();
$message = '';
$error = '';

// Handle delete image
if (isset($_POST['delete_image'])) {
    $image_id = (int)$_POST['image_id'];
    $delete_query = "DELETE FROM product_images WHERE image_id = ? AND product_id = ?";
    $delete_stmt = $conn->prepare($delete_query);
    $delete_stmt->bind_param("ii", $image_id, $product_id);
    
    if ($delete_stmt->execute()) {
        // Update main image if needed
        $remaining_query = "SELECT image_url FROM product_images WHERE product_id = ? ORDER BY sort_order ASC LIMIT 1";
        $remaining_stmt = $conn->prepare($remaining_query);
        $remaining_stmt->bind_param("i", $product_id);
        $remaining_stmt->execute();
        $remaining_result = $remaining_stmt->get_result();
        
        if ($remaining_result->num_rows > 0) {
            $new_main = $remaining_result->fetch_assoc();
            $update_main_query = "UPDATE products SET image_url = ? WHERE product_id = ?";
            $update_main_stmt = $conn->prepare($update_main_query);
            $update_main_stmt->bind_param("si", $new_main['image_url'], $product_id);
            $update_main_stmt->execute();
        } else {
            // No images left, set to null
            $update_main_query = "UPDATE products SET image_url = NULL WHERE product_id = ?";
            $update_main_stmt = $conn->prepare($update_main_query);
            $update_main_stmt->bind_param("i", $product_id);
            $update_main_stmt->execute();
        }
        
        $message = 'Đã xóa ảnh thành công!';
        // Refresh product data
        $product = $product_model->getProductById($product_id);
    }
}

// Handle form submission
if ($_SERVER['REQUEST_METHOD'] === 'POST' && !isset($_POST['delete_image'])) {
    $name = $_POST['name'] ?? '';
    $description = $_POST['description'] ?? '';
    $price = $_POST['price'] ?? 0;
    $stock = $_POST['stock'] ?? 0;
    $category_id = $_POST['category_id'] ?? null;
    $status = $_POST['status'] ?? 'available';
    
    if (empty($name) || empty($description) || $price <= 0) {
        $error = 'Vui lòng điền đầy đủ thông tin bắt buộc';
    } else {
        $conn->begin_transaction();
        
        try {
            // Update basic product info
            $query = "UPDATE products SET name = ?, description = ?, price = ?, stock = ?, 
                      category_id = ?, status = ? WHERE product_id = ?";
            $stmt = $conn->prepare($query);
            $stmt->bind_param("ssdiisi", $name, $description, $price, $stock, $category_id, $status, $product_id);
            
            if (!$stmt->execute()) {
                throw new Exception('Lỗi khi cập nhật sản phẩm: ' . $stmt->error);
            }
            
            $main_image_updated = false;
            
            // Handle library images first
            if (isset($_POST['library_images']) && !empty($_POST['library_images'])) {
                // Get current max sort_order
                $sort_query = "SELECT COALESCE(MAX(sort_order), -1) + 1 as next_sort FROM product_images WHERE product_id = ?";
                $sort_stmt = $conn->prepare($sort_query);
                $sort_stmt->bind_param("i", $product_id);
                $sort_stmt->execute();
                $sort_result = $sort_stmt->get_result()->fetch_assoc();
                $next_sort = $sort_result['next_sort'];
                
                foreach ($_POST['library_images'] as $index => $image_path) {
                    // Validate image path is from assets/images
                    if (strpos($image_path, 'assets/images/') === 0 && file_exists($image_path)) {
                        // Insert into product_images table
                        $img_query = "INSERT INTO product_images (product_id, image_url, sort_order) VALUES (?, ?, ?)";
                        $img_stmt = $conn->prepare($img_query);
                        $sort_order = $next_sort + $index;
                        $img_stmt->bind_param("isi", $product_id, $image_path, $sort_order);
                        
                        if ($img_stmt->execute()) {
                            // Update main image if this is the first image and no main image exists
                            if (!$main_image_updated && empty($product['image_url'])) {
                                $update_main_query = "UPDATE products SET image_url = ? WHERE product_id = ?";
                                $update_main_stmt = $conn->prepare($update_main_query);
                                $update_main_stmt->bind_param("si", $image_path, $product_id);
                                $update_main_stmt->execute();
                                $main_image_updated = true;
                            }
                        }
                    }
                }
            }
            
            // Handle new uploaded images
            if (isset($_FILES['new_images']) && !empty($_FILES['new_images']['name'][0])) {
                $upload_dir = UPLOAD_DIR . 'products/';
                if (!is_dir($upload_dir)) {
                    mkdir($upload_dir, 0755, true);
                }
                
                // Get current max sort_order
                $sort_query = "SELECT COALESCE(MAX(sort_order), -1) + 1 as next_sort FROM product_images WHERE product_id = ?";
                $sort_stmt = $conn->prepare($sort_query);
                $sort_stmt->bind_param("i", $product_id);
                $sort_stmt->execute();
                $sort_result = $sort_stmt->get_result()->fetch_assoc();
                $current_sort_order = $sort_result['next_sort'];
                
                $files = $_FILES['new_images'];
                
                for ($i = 0; $i < count($files['name']); $i++) {
                    if ($files['error'][$i] === UPLOAD_ERR_OK) {
                        $file_extension = pathinfo($files['name'][$i], PATHINFO_EXTENSION);
                        $filename = uniqid() . '_' . ($current_sort_order + $i) . '.' . $file_extension;
                        $upload_path = $upload_dir . $filename;
                        
                        if (move_uploaded_file($files['tmp_name'][$i], $upload_path)) {
                            $image_url = UPLOAD_URL . 'products/' . $filename;
                            
                            // Insert into product_images table
                            $img_query = "INSERT INTO product_images (product_id, image_url, sort_order) VALUES (?, ?, ?)";
                            $img_stmt = $conn->prepare($img_query);
                            $sort_order = $current_sort_order + $i;
                            $img_stmt->bind_param("isi", $product_id, $image_url, $sort_order);
                            
                            if ($img_stmt->execute()) {
                                // Update main image if this is the first image and no main image exists
                                if (!$main_image_updated && empty($product['image_url'])) {
                                    $update_main_query = "UPDATE products SET image_url = ? WHERE product_id = ?";
                                    $update_main_stmt = $conn->prepare($update_main_query);
                                    $update_main_stmt->bind_param("si", $image_url, $product_id);
                                    $update_main_stmt->execute();
                                    $main_image_updated = true;
                                }
                            }
                        }
                    }
                }
            }
            
            $conn->commit();
            $message = 'Cập nhật sản phẩm thành công!';
            
            // Refresh product data
            $product = $product_model->getProductById($product_id);
            
        } catch (Exception $e) {
            $conn->rollback();
            $error = $e->getMessage();
        }
    }
}
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
                        <a href="<?php echo APP_URL; ?>/admin-products.php" class="list-group-item list-group-item-action active">
                            <i class="fas fa-box"></i> Sản phẩm
                        </a>
                        <a href="<?php echo APP_URL; ?>/admin-orders.php" class="list-group-item list-group-item-action">
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
            <div class="d-flex justify-content-between align-items-center mb-4">
                <h2>Sửa sản phẩm: <?php echo htmlspecialchars($product['name']); ?></h2>
                <a href="<?php echo APP_URL; ?>/admin-products.php" class="btn btn-secondary">
                    <i class="fas fa-arrow-left"></i> Quay lại
                </a>
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

            <div class="card">
                <div class="card-body">
                    <form method="POST" enctype="multipart/form-data">
                        <div class="row">
                            <div class="col-md-8">
                                <div class="mb-3">
                                    <label for="name" class="form-label">Tên sản phẩm *</label>
                                    <input type="text" class="form-control" id="name" name="name" 
                                           value="<?php echo htmlspecialchars($product['name']); ?>" required>
                                </div>

                                <div class="mb-3">
                                    <label for="description" class="form-label">Mô tả *</label>
                                    <textarea class="form-control" id="description" name="description" rows="5" required><?php echo htmlspecialchars($product['description']); ?></textarea>
                                </div>

                                <div class="row">
                                    <div class="col-md-4">
                                        <div class="mb-3">
                                            <label for="price" class="form-label">Giá (VNĐ) *</label>
                                            <input type="number" class="form-control" id="price" name="price" 
                                                   value="<?php echo $product['price']; ?>" min="0" step="1000" required>
                                        </div>
                                    </div>
                                    <div class="col-md-4">
                                        <div class="mb-3">
                                            <label for="stock" class="form-label">Tồn kho</label>
                                            <input type="number" class="form-control" id="stock" name="stock" 
                                                   value="<?php echo $product['stock']; ?>" min="0">
                                        </div>
                                    </div>
                                    <div class="col-md-4">
                                        <div class="mb-3">
                                            <label for="category_id" class="form-label">Danh mục</label>
                                            <select class="form-select" id="category_id" name="category_id">
                                                <option value="">Chọn danh mục</option>
                                                <?php foreach ($categories as $cat): ?>
                                                    <option value="<?php echo $cat['category_id']; ?>" 
                                                            <?php echo $product['category_id'] == $cat['category_id'] ? 'selected' : ''; ?>>
                                                        <?php echo $cat['name']; ?>
                                                    </option>
                                                <?php endforeach; ?>
                                            </select>
                                        </div>
                                    </div>
                                </div>

                                <div class="mb-3">
                                    <label for="status" class="form-label">Trạng thái</label>
                                    <select class="form-select" id="status" name="status">
                                        <option value="available" <?php echo $product['status'] === 'available' ? 'selected' : ''; ?>>Có sẵn</option>
                                        <option value="unavailable" <?php echo $product['status'] === 'unavailable' ? 'selected' : ''; ?>>Không có sẵn</option>
                                    </select>
                                </div>
                            </div>

                            <div class="col-md-4">
                                <!-- Existing Images -->
                                <?php if (!empty($product['images'])): ?>
                                <div class="mb-4">
                                    <label class="form-label">Ảnh hiện tại</label>
                                    <div class="row g-2">
                                        <?php foreach ($product['images'] as $index => $img): ?>
                                            <div class="col-6">
                                                <div class="position-relative">
                                                    <img src="<?php echo $img['image_url']; ?>" 
                                                         class="img-fluid rounded border" 
                                                         style="height: 100px; width: 100%; object-fit: cover;"
                                                         alt="Product image">
                                                    
                                                    <?php if ($index === 0): ?>
                                                        <span class="position-absolute top-0 start-0 badge bg-primary" style="font-size: 10px;">
                                                            Ảnh chính
                                                        </span>
                                                    <?php endif; ?>
                                                    
                                                    <!-- Delete button -->
                                                    <form method="POST" class="position-absolute top-0 end-0" 
                                                          onsubmit="return confirm('Bạn có chắc muốn xóa ảnh này?')">
                                                        <input type="hidden" name="image_id" value="<?php echo $img['image_id'] ?? ''; ?>">
                                                        <button type="submit" name="delete_image" 
                                                                class="btn btn-danger btn-sm" 
                                                                style="padding: 2px 6px; font-size: 10px;">
                                                            <i class="fas fa-times"></i>
                                                        </button>
                                                    </form>
                                                </div>
                                            </div>
                                        <?php endforeach; ?>
                                    </div>
                                </div>
                                <?php endif; ?>

                                <!-- Add Images from Library -->
                                <div class="mb-3">
                                    <label class="form-label">Thêm ảnh mới</label>
                                    <div class="d-grid gap-2">
                                        <button type="button" class="btn btn-outline-primary" onclick="openImageSelector()">
                                            <i class="fas fa-images"></i> Chọn từ thư viện
                                        </button>
                                        <button type="button" class="btn btn-outline-secondary" onclick="document.getElementById('new_images').click()">
                                            <i class="fas fa-upload"></i> Upload ảnh mới
                                        </button>
                                    </div>
                                    <input type="file" class="form-control d-none" id="new_images" name="new_images[]" 
                                           accept="image/*" multiple>
                                    <div class="form-text">Chọn ảnh từ thư viện hoặc upload ảnh mới</div>
                                </div>

                                <!-- Hidden inputs for selected library images -->
                                <div id="selectedImageInputs"></div>

                                <div id="new-image-preview-container" class="mt-3">
                                    <!-- Preview new images will be shown here -->
                                </div>
                            </div>
                        </div>

                        <hr>
                        <div class="d-flex justify-content-end">
                            <a href="<?php echo APP_URL; ?>/admin-products.php" class="btn btn-secondary me-2">Hủy</a>
                            <button type="submit" class="btn btn-primary">
                                <i class="fas fa-save"></i> Cập nhật sản phẩm
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
let selectedLibraryImages = [];

// Handle file upload
document.getElementById('new_images').addEventListener('change', function(e) {
    const files = e.target.files;
    showImagePreviews(files, 'upload');
});

// Open image selector popup
function openImageSelector() {
    const popup = window.open(
        '<?php echo APP_URL; ?>/admin/image-selector.php', 
        'imageSelector', 
        'width=1200,height=800,scrollbars=yes,resizable=yes'
    );
}

// Receive selected images from popup
function receiveSelectedImages(images) {
    selectedLibraryImages = images;
    showImagePreviews(images, 'library');
    createHiddenInputs(images);
}

// Show image previews
function showImagePreviews(items, type) {
    const container = document.getElementById('new-image-preview-container');
    container.innerHTML = '';
    
    if ((type === 'upload' && items.length > 0) || (type === 'library' && items.length > 0)) {
        const previewGrid = document.createElement('div');
        previewGrid.className = 'row g-2';
        
        if (type === 'upload') {
            Array.from(items).forEach((file, index) => {
                if (file.type.startsWith('image/')) {
                    const reader = new FileReader();
                    reader.onload = function(e) {
                        createImagePreview(e.target.result, file.name, index, previewGrid, 'Mới');
                    };
                    reader.readAsDataURL(file);
                }
            });
        } else if (type === 'library') {
            items.forEach((img, index) => {
                createImagePreview('<?php echo APP_URL; ?>/' + img.path, img.filename, index, previewGrid, 'Thư viện');
            });
        }
        
        container.appendChild(previewGrid);
        
        // Show info
        const info = document.createElement('div');
        info.className = 'mt-2 text-muted small';
        info.textContent = `Sẽ thêm ${items.length} ảnh từ ${type === 'upload' ? 'upload' : 'thư viện'}`;
        container.appendChild(info);
    }
}

// Create image preview element
function createImagePreview(src, name, index, container, badge_text) {
    const col = document.createElement('div');
    col.className = 'col-6';
    
    const imgContainer = document.createElement('div');
    imgContainer.className = 'position-relative';
    
    const img = document.createElement('img');
    img.src = src;
    img.className = 'img-fluid rounded border';
    img.style.height = '100px';
    img.style.width = '100%';
    img.style.objectFit = 'cover';
    
    // Add badge
    const badge = document.createElement('span');
    badge.className = 'position-absolute top-0 start-0 badge bg-success';
    badge.textContent = badge_text;
    badge.style.fontSize = '10px';
    
    // Add order number
    const orderBadge = document.createElement('span');
    orderBadge.className = 'position-absolute top-0 end-0 badge bg-secondary';
    orderBadge.textContent = index + 1;
    orderBadge.style.fontSize = '10px';
    
    // Add filename
    const filename = document.createElement('div');
    filename.className = 'small text-center mt-1';
    filename.textContent = name.length > 15 ? name.substring(0, 15) + '...' : name;
    
    imgContainer.appendChild(img);
    imgContainer.appendChild(badge);
    imgContainer.appendChild(orderBadge);
    col.appendChild(imgContainer);
    col.appendChild(filename);
    container.appendChild(col);
}

// Create hidden inputs for library images
function createHiddenInputs(images) {
    const container = document.getElementById('selectedImageInputs');
    container.innerHTML = '';
    
    images.forEach((img, index) => {
        const input = document.createElement('input');
        input.type = 'hidden';
        input.name = 'library_images[]';
        input.value = img.path;
        container.appendChild(input);
    });
}

// Drag and drop functionality
const container = document.getElementById('new-image-preview-container');

['dragenter', 'dragover', 'dragleave', 'drop'].forEach(eventName => {
    container.addEventListener(eventName, preventDefaults, false);
});

function preventDefaults(e) {
    e.preventDefault();
    e.stopPropagation();
}

['dragenter', 'dragover'].forEach(eventName => {
    container.addEventListener(eventName, highlight, false);
});

['dragleave', 'drop'].forEach(eventName => {
    container.addEventListener(eventName, unhighlight, false);
});

function highlight(e) {
    container.classList.add('border-primary', 'bg-light');
}

function unhighlight(e) {
    container.classList.remove('border-primary', 'bg-light');
}

container.addEventListener('drop', handleDrop, false);

function handleDrop(e) {
    const dt = e.dataTransfer;
    const files = dt.files;
    document.getElementById('new_images').files = files;
    
    // Trigger change event
    const event = new Event('change', { bubbles: true });
    document.getElementById('new_images').dispatchEvent(event);
}
</script>

<style>
#new-image-preview-container {
    min-height: 80px;
    border: 2px dashed #dee2e6;
    border-radius: 0.375rem;
    padding: 1rem;
    text-align: center;
    transition: all 0.3s ease;
}

#new-image-preview-container:empty::before {
    content: "Kéo thả ảnh mới vào đây hoặc chọn từ thư viện";
    color: #6c757d;
    font-style: italic;
    font-size: 14px;
}

#new-image-preview-container.border-primary {
    border-color: #0d6efd !important;
}
</style>

<?php include __DIR__ . '/../views/layout/footer.php'; ?>