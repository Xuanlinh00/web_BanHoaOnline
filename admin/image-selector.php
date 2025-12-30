<?php
require_once __DIR__ . '/../config/constants.php';

// Get all images from assets/images folders
function getImagesFromFolder($folder) {
    $images = [];
    $path = __DIR__ . '/../assets/images/' . $folder;
    
    if (is_dir($path)) {
        $files = scandir($path);
        foreach ($files as $file) {
            if (in_array(strtolower(pathinfo($file, PATHINFO_EXTENSION)), ['jpg', 'jpeg', 'png', 'gif'])) {
                $images[] = [
                    'filename' => $file,
                    'path' => 'assets/images/' . $folder . '/' . $file,
                    'folder' => $folder
                ];
            }
        }
    }
    
    return $images;
}

$folders = ['hoasn', 'hoatinhyeu', 'hoacuoi', 'hoakhaitruong', 'hoachucmung', 'hoatang', 'slide'];
$all_images = [];

foreach ($folders as $folder) {
    $all_images[$folder] = getImagesFromFolder($folder);
}

// Handle AJAX request
if (isset($_GET['ajax']) && $_GET['ajax'] === 'get_images') {
    header('Content-Type: application/json');
    echo json_encode($all_images);
    exit;
}
?>

<!DOCTYPE html>
<html>
<head>
    <title>Image Selector</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <style>
        .image-item {
            cursor: pointer;
            transition: all 0.3s;
            border: 2px solid transparent;
        }
        .image-item:hover {
            transform: scale(1.05);
            border-color: #007bff;
        }
        .image-item.selected {
            border-color: #28a745;
            background-color: #d4edda;
        }
        .image-preview {
            width: 100px;
            height: 100px;
            object-fit: cover;
        }
        .selected-images {
            max-height: 200px;
            overflow-y: auto;
        }
    </style>
</head>
<body>
    <div class="container-fluid mt-3">
        <h4>Chọn ảnh từ thư viện</h4>
        
        <div class="row">
            <div class="col-md-8">
                <!-- Tabs for different folders -->
                <ul class="nav nav-tabs" id="folderTabs">
                    <?php foreach ($folders as $index => $folder): ?>
                        <li class="nav-item">
                            <a class="nav-link <?php echo $index === 0 ? 'active' : ''; ?>" 
                               data-bs-toggle="tab" href="#<?php echo $folder; ?>">
                                <?php 
                                $folder_names = [
                                    'hoasn' => 'Hoa Sinh Nhật',
                                    'hoatinhyeu' => 'Hoa Tình Yêu', 
                                    'hoacuoi' => 'Hoa Cưới',
                                    'hoakhaitruong' => 'Hoa Khai Trương',
                                    'hoachucmung' => 'Hoa Chúc Mừng',
                                    'hoatang' => 'Hoa Tang',
                                    'slide' => 'Ảnh Slide'
                                ];
                                echo $folder_names[$folder] ?? $folder;
                                ?>
                            </a>
                        </li>
                    <?php endforeach; ?>
                </ul>

                <!-- Tab content -->
                <div class="tab-content mt-3">
                    <?php foreach ($folders as $index => $folder): ?>
                        <div class="tab-pane fade <?php echo $index === 0 ? 'show active' : ''; ?>" id="<?php echo $folder; ?>">
                            <div class="row g-2">
                                <?php foreach ($all_images[$folder] as $image): ?>
                                    <div class="col-md-2 col-sm-3 col-4">
                                        <div class="image-item p-2 text-center" 
                                             onclick="selectImage('<?php echo $image['path']; ?>', '<?php echo $image['filename']; ?>')">
                                            <img src="<?php echo APP_URL . '/' . $image['path']; ?>" 
                                                 class="image-preview rounded" 
                                                 alt="<?php echo $image['filename']; ?>">
                                            <div class="small mt-1"><?php echo substr($image['filename'], 0, 15) . '...'; ?></div>
                                        </div>
                                    </div>
                                <?php endforeach; ?>
                            </div>
                        </div>
                    <?php endforeach; ?>
                </div>
            </div>

            <div class="col-md-4">
                <div class="card">
                    <div class="card-header">
                        <h6>Ảnh đã chọn (<span id="selectedCount">0</span>)</h6>
                    </div>
                    <div class="card-body selected-images" id="selectedImages">
                        <p class="text-muted">Chưa chọn ảnh nào</p>
                    </div>
                    <div class="card-footer">
                        <button type="button" class="btn btn-primary btn-sm" onclick="confirmSelection()">
                            Xác nhận chọn
                        </button>
                        <button type="button" class="btn btn-secondary btn-sm" onclick="clearSelection()">
                            Xóa tất cả
                        </button>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/js/bootstrap.bundle.min.js"></script>
    <script>
        let selectedImages = [];

        function selectImage(path, filename) {
            // Check if already selected
            const index = selectedImages.findIndex(img => img.path === path);
            
            if (index > -1) {
                // Remove if already selected
                selectedImages.splice(index, 1);
                document.querySelector(`[onclick="selectImage('${path}', '${filename}')"]`).classList.remove('selected');
            } else {
                // Add to selection
                selectedImages.push({path: path, filename: filename});
                document.querySelector(`[onclick="selectImage('${path}', '${filename}')"]`).classList.add('selected');
            }
            
            updateSelectedDisplay();
        }

        function updateSelectedDisplay() {
            const container = document.getElementById('selectedImages');
            const count = document.getElementById('selectedCount');
            
            count.textContent = selectedImages.length;
            
            if (selectedImages.length === 0) {
                container.innerHTML = '<p class="text-muted">Chưa chọn ảnh nào</p>';
            } else {
                let html = '';
                selectedImages.forEach((img, index) => {
                    html += `
                        <div class="d-flex align-items-center mb-2">
                            <img src="<?php echo APP_URL; ?>/${img.path}" class="me-2" style="width: 40px; height: 40px; object-fit: cover;">
                            <div class="flex-grow-1 small">${img.filename}</div>
                            <button type="button" class="btn btn-sm btn-outline-danger" onclick="removeImage(${index})">×</button>
                        </div>
                    `;
                });
                container.innerHTML = html;
            }
        }

        function removeImage(index) {
            const img = selectedImages[index];
            selectedImages.splice(index, 1);
            document.querySelector(`[onclick="selectImage('${img.path}', '${img.filename}')"]`).classList.remove('selected');
            updateSelectedDisplay();
        }

        function clearSelection() {
            selectedImages = [];
            document.querySelectorAll('.image-item.selected').forEach(item => {
                item.classList.remove('selected');
            });
            updateSelectedDisplay();
        }

        function confirmSelection() {
            if (selectedImages.length === 0) {
                alert('Vui lòng chọn ít nhất một ảnh');
                return;
            }
            
            // Send selected images back to parent window
            if (window.opener && window.opener.receiveSelectedImages) {
                window.opener.receiveSelectedImages(selectedImages);
                window.close();
            } else {
                // For testing - show selected images
                console.log('Selected images:', selectedImages);
                alert('Đã chọn ' + selectedImages.length + ' ảnh');
            }
        }
    </script>
</body>
</html>