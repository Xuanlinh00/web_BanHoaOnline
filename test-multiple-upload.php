<!DOCTYPE html>
<html>
<head>
    <title>Test Multiple Upload</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body>
    <div class="container mt-5">
        <h2>Test Multiple Image Upload</h2>
        
        <?php if ($_SERVER['REQUEST_METHOD'] === 'POST'): ?>
            <div class="alert alert-info">
                <h4>Upload Results:</h4>
                <?php
                echo "<p>POST data:</p><pre>";
                print_r($_POST);
                echo "</pre>";
                
                echo "<p>FILES data:</p><pre>";
                print_r($_FILES);
                echo "</pre>";
                
                if (isset($_FILES['images'])) {
                    echo "<p>Number of files selected: " . count($_FILES['images']['name']) . "</p>";
                    
                    for ($i = 0; $i < count($_FILES['images']['name']); $i++) {
                        echo "<p>File $i: {$_FILES['images']['name'][$i]} (Error: {$_FILES['images']['error'][$i]})</p>";
                    }
                }
                ?>
            </div>
        <?php endif; ?>
        
        <form method="POST" enctype="multipart/form-data">
            <div class="mb-3">
                <label for="images" class="form-label">Select Multiple Images</label>
                <input type="file" class="form-control" id="images" name="images[]" multiple accept="image/*">
                <div class="form-text">Hold Ctrl/Cmd to select multiple files</div>
            </div>
            
            <div class="mb-3">
                <label for="library_images" class="form-label">Library Images (for testing)</label>
                <select class="form-select" name="library_images[]" multiple>
                    <option value="assets/images/hoasn/z7381588660007_6b681535387273719fddd4c61ab65a6d.jpg">Hoa SN 1</option>
                    <option value="assets/images/hoasn/z7381588665014_5c8becb3c02461df35bf7d26b46b7921.jpg">Hoa SN 2</option>
                    <option value="assets/images/hoatinhyeu/z7381670109021_6ae68533520f3279fe93ce5e14883de6.jpg">Hoa TY 1</option>
                </select>
                <div class="form-text">Hold Ctrl/Cmd to select multiple options</div>
            </div>
            
            <button type="submit" class="btn btn-primary">Test Upload</button>
        </form>
        
        <div id="preview" class="mt-4"></div>
    </div>

    <script>
    document.getElementById('images').addEventListener('change', function(e) {
        const files = e.target.files;
        const preview = document.getElementById('preview');
        preview.innerHTML = '';
        
        if (files.length > 0) {
            preview.innerHTML = '<h4>Selected Files (' + files.length + '):</h4>';
            
            Array.from(files).forEach((file, index) => {
                const div = document.createElement('div');
                div.className = 'mb-2';
                div.innerHTML = `<strong>File ${index + 1}:</strong> ${file.name} (${file.size} bytes)`;
                preview.appendChild(div);
                
                if (file.type.startsWith('image/')) {
                    const reader = new FileReader();
                    reader.onload = function(e) {
                        const img = document.createElement('img');
                        img.src = e.target.result;
                        img.style.width = '100px';
                        img.style.height = '100px';
                        img.style.objectFit = 'cover';
                        img.style.margin = '5px';
                        img.className = 'border';
                        div.appendChild(img);
                    };
                    reader.readAsDataURL(file);
                }
            });
        }
    });
    </script>
</body>
</html>