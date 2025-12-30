<?php
require_once __DIR__ . '/../config/constants.php';
require_once __DIR__ . '/../config/session.php';

$page_title = 'Thêm sản phẩm';

// Require admin
requireAdmin();

$conn = require __DIR__ . '/../config/database.php';
require_once __DIR__ . '/../models/Category.php';

$category_model = new Category($conn);
$categories = $category_model->getAllCategories();

$message = '';
$error = '';

// Handle form submission
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $name = $_POST['name'] ?? '';
    $description = $_POST['description'] ?? '';
    $price = $_POST['price'] ?? 0;
    $stock = $_POST['stock'] ?? 0;
    $category_id = $_POST['category_id'] ?? null;
    $status = $_POST['status'] ?? 'available';
    
    if (empty($name) || empty($description) || $price <= 0) {
        $error = 'Vui lòng điền đầy đủ thông tin bắt buộc';
    } else {
        // Start transaction
        $conn->begin_transaction();
        
        try {
            // Insert product first
            $query = "INSERT INTO products (name, description, price, stock, category_id, status) 
                      VALUES (?, ?, ?, ?, ?, ?)";
            $stmt = $conn->prepare($query);
            $stmt->bind_param("ssdiis", $name, $description, $price, $stock, $category_id, $status);
            
            if (!$stmt->execute()) {
                throw new Exception('Lỗi khi thêm sản phẩm: ' . $stmt->error);
            }
            
            $product_id = $conn->insert_id;
            $main_image_url = null;
            
            // Handle multiple image uploads and library selections
            $main_image_url = null;
            
            // Handle library images first
            if (isset($_POST['library_images']) && !empty($_POST['library_images'])) {
                foreach ($_POST['library_images'] as $index => $image_path) {
                    // Validate image path is from assets/images
                    if (strpos($image_path, 'assets/images/') === 0 && file_exists($image_path)) {
                        if ($index === 0) {
                            $main_image_url = $image_path;
                        }
                        
                        // Insert into product_images table
                        $img_query = "INSERT INTO product_images (product_id, image_url, sort_order) VALUES (?, ?, ?)";
                        $img_stmt = $conn->prepare($img_query);
                        $img_stmt->bind_param("isi", $product_id, $image_path, $index);
                        
                        if (!$img_stmt->execute()) {
                            error_log("Failed to insert library image: " . $img_stmt->error);
                        } else {
                            error_log("Successfully inserted library image: " . $image_path);
                        }
                    }
                }
            }
            
            // Handle uploaded images
            if (isset($_FILES['images']) && !empty($_FILES['images']['name'][0])) {
                $upload_dir = UPLOAD_DIR . 'products/';
                if (!is_dir($upload_dir)) {
                    mkdir($upload_dir, 0755, true);
                }
                
                $files = $_FILES['images'];
                $current_sort_order = isset($_POST['library_images']) ? count($_POST['library_images']) : 0;
                
                for ($i = 0; $i < count($files['name']); $i++) {
                    if ($files['error'][$i] === UPLOAD_ERR_OK) {
                        $file_extension = pathinfo($files['name'][$i], PATHINFO_EXTENSION);
                        $filename = uniqid() . '_' . ($current_sort_order + $i) . '.' . $file_extension;
                        $upload_path = $upload_dir . $filename;
                        
                        if (move_uploaded_file($files['tmp_name'][$i], $upload_path)) {
                            $image_url = UPLOAD_URL . 'products/' . $filename;
                            
                            // If no main image from library, use first uploaded image
                            if ($main_image_url === null && $i === 0) {
                                $main_image_url = $image_url;
                            }
                            
                            // Insert into product_images table
                            $img_query = "INSERT INTO product_images (product_id, image_url, sort_order) VALUES (?, ?, ?)";
                            $img_stmt = $conn->prepare($img_query);
                            $sort_order = $current_sort_order + $i;
                            $img_stmt->bind_param("isi", $product_id, $image_url, $sort_order);
                            
                            if (!$img_stmt->execute()) {
                                error_log("Failed to insert uploaded image: " . $img_stmt->error);
                            } else {
                                error_log("Successfully inserted uploaded image: " . $image_url);
                            }
                        }
                    }
                }
            }
            
            // Update main image_url in products table
            if ($main_image_url) {
                $update_query = "UPDATE products SET image_url = ? WHERE product_id = ?";
                $update_stmt = $conn->prepare($update_query);
                $update_stmt->bind_param("si", $main_image_url, $product_id);
                $update_stmt->execute();
            }
            
            $conn->commit();
            $message = 'Thêm sản phẩm thành công!';
            
            // Clear form
            $name = $description = '';
            $price = $stock = 0;
            $category_id = null;
            
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
                <h2>Thêm sản phẩm</h2>
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
                                           value="<?php echo htmlspecialchars($name ?? ''); ?>" required>
                                </div>

                                <div class="mb-3">
                                    <label for="description" class="form-label">Mô tả *</label>
                                    <textarea class="form-control" id="description" name="description" rows="5" required><?php echo htmlspecialchars($description ?? ''); ?></textarea>
                                </div>

                                <div class="row">
                                    <div class="col-md-4">
                                        <div class="mb-3">
                                            <label for="price" class="form-label">Giá (VNĐ) *</label>
                                            <input type="number" class="form-control" id="price" name="price" 
                                                   value="<?php echo $price ?? 0; ?>" min="0" step="1000" required>
                                        </div>
                                    </div>
                                    <div class="col-md-4">
                                        <div class="mb-3">
                                            <label for="stock" class="form-label">Tồn kho</label>
                                            <input type="number" class="form-control" id="stock" name="stock" 
                                                   value="<?php echo $stock ?? 0; ?>" min="0">
                                        </div>
                                    </div>
                                    <div class="col-md-4">
                                        <div class="mb-3">
                                            <label for="category_id" class="form-label">Danh mục</label>
                                            <select class="form-select" id="category_id" name="category_id">
                                                <option value="">Chọn danh mục</option>
                                                <?php foreach ($categories as $cat): ?>
                                                    <option value="<?php echo $cat['category_id']; ?>" 
                                                            <?php echo ($category_id ?? '') == $cat['category_id'] ? 'selected' : ''; ?>>
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
                                        <option value="available" <?php echo ($status ?? 'available') === 'available' ? 'selected' : ''; ?>>Có sẵn</option>
                                        <option value="unavailable" <?php echo ($status ?? '') === 'unavailable' ? 'selected' : ''; ?>>Không có sẵn</option>
                                    </select>
                                </div>
                            </div>

                            <div class="col-md-4">
                                <div class="mb-3">
                                    <label class="form-label">Hình ảnh sản phẩm</label>
                                    <div class="d-grid gap-2">
                                        <button type="button" class="btn btn-outline-primary" onclick="openImageSelector()">
                                            <i class="fas fa-images"></i> Chọn ảnh từ thư viện
                                        </button>
                                        <button type="button" class="btn btn-outline-secondary" onclick="document.getElementById('images').click()">
                                            <i class="fas fa-upload"></i> Upload ảnh mới
                                        </button>
                                    </div>
                                    <input type="file" class="form-control d-none" id="images" name="images[]" 
                                           accept="image/*" multiple>
                                    <div class="form-text">Chọn ảnh từ thư viện có sẵn hoặc upload ảnh mới</div>
                                </div>

                                <!-- Hidden inputs for selected images -->
                                <div id="selectedImageInputs"></div>

                                <div id="image-preview-container" class="mt-3">
                                    <!-- Preview images will be shown here -->
                                </div>
                            </div>
                        </div>

                        <hr>
                        <div class="d-flex justify-content-end">
                            <a href="<?php echo APP_URL; ?>/admin-products.php" class="btn btn-secondary me-2">Hủy</a>
                            <button type="submit" class="btn btn-primary">
                                <i class="fas fa-save"></i> Lưu sản phẩm
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
document.getElementById('images').addEventListener('change', function(e) {
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
    const container = document.getElementById('image-preview-container');
    container.innerHTML = '';
    
    if ((type === 'upload' && items.length > 0) || (type === 'library' && items.length > 0)) {
        const previewGrid = document.createElement('div');
        previewGrid.className = 'row g-2';
        
        if (type === 'upload') {
            Array.from(items).forEach((file, index) => {
                if (file.type.startsWith('image/')) {
                    const reader = new FileReader();
                    reader.onload = function(e) {
                        createImagePreview(e.target.result, file.name, index, previewGrid);
                    };
                    reader.readAsDataURL(file);
                }
            });
        } else if (type === 'library') {
            items.forEach((img, index) => {
                createImagePreview('<?php echo APP_URL; ?>/' + img.path, img.filename, index, previewGrid);
            });
        }
        
        container.appendChild(previewGrid);
        
        // Show info
        const info = document.createElement('div');
        info.className = 'mt-2 text-muted small';
        info.textContent = `Đã chọn ${items.length} ảnh từ ${type === 'upload' ? 'upload' : 'thư viện'}`;
        container.appendChild(info);
    }
}

// Create image preview element
function createImagePreview(src, name, index, container) {
    const col = document.createElement('div');
    col.className = 'col-6';
    
    const imgContainer = document.createElement('div');
    imgContainer.className = 'position-relative';
    
    const img = document.createElement('img');
    img.src = src;
    img.className = 'img-fluid rounded border';
    img.style.height = '120px';
    img.style.width = '100%';
    img.style.objectFit = 'cover';
    
    // Add badge for main image
    if (index === 0) {
        const badge = document.createElement('span');
        badge.className = 'position-absolute top-0 start-0 badge bg-primary';
        badge.textContent = 'Ảnh chính';
        badge.style.fontSize = '10px';
        imgContainer.appendChild(badge);
    }
    
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
const container = document.getElementById('image-preview-container');

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
    document.getElementById('images').files = files;
    
    // Trigger change event
    const event = new Event('change', { bubbles: true });
    document.getElementById('images').dispatchEvent(event);
}
</script>

<style>
#image-preview-container {
    min-height: 100px;
    border: 2px dashed #dee2e6;
    border-radius: 0.375rem;
    padding: 1rem;
    text-align: center;
    transition: all 0.3s ease;
}

#image-preview-container:empty::before {
    content: "Chọn ảnh từ thư viện hoặc kéo thả ảnh vào đây";
    color: #6c757d;
    font-style: italic;
}

#image-preview-container.border-primary {
    border-color: #0d6efd !important;
}
</style>

<?php include __DIR__ . '/../views/layout/footer.php'; ?>