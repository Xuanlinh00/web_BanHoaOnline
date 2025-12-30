<?php
echo "<h2>Kiểm tra ảnh có tồn tại</h2>";

$test_images = [
    'assets/images/hoasn/z7381588660007_6b681535387273719fddd4c61ab65a6d.jpg',
    'assets/images/hoasn/z7381588665014_5c8becb3c02461df35bf7d26b46b7921.jpg',
    'assets/images/hoatinhyeu/z7381670109021_6ae68533520f3279fe93ce5e14883de6.jpg',
    'assets/images/slide/z7381766425359_59890343ace576fda6504690a52bad7d.jpg',
    'assets/images/slide/z7381768017715_f62e4e9cf6e2e193d8f8a0e5b877647c.jpg',
    'assets/images/slide/z7381769713056_91a548234d64bc9c4e2dc9f3453bb9a5.jpg'
];

echo "<h3>Kiểm tra file ảnh:</h3>";
foreach ($test_images as $img) {
    $exists = file_exists($img);
    $color = $exists ? 'green' : 'red';
    $status = $exists ? '✓ EXISTS' : '✗ NOT FOUND';
    
    echo "<div style='margin: 10px 0; padding: 10px; border: 1px solid #ccc;'>";
    echo "<p><strong>File:</strong> $img</p>";
    echo "<p style='color: $color;'><strong>Status:</strong> $status</p>";
    
    if ($exists) {
        echo "<img src='$img' style='width: 150px; height: 150px; object-fit: cover; border: 1px solid #ddd;'>";
    }
    echo "</div>";
}

echo "<h3>Kiểm tra thư mục:</h3>";
$folders = ['assets/images/hoasn', 'assets/images/hoatinhyeu', 'assets/images/slide'];

foreach ($folders as $folder) {
    echo "<h4>$folder:</h4>";
    if (is_dir($folder)) {
        $files = scandir($folder);
        $image_files = array_filter($files, function($file) {
            return in_array(strtolower(pathinfo($file, PATHINFO_EXTENSION)), ['jpg', 'jpeg', 'png', 'gif']);
        });
        
        echo "<p>Tìm thấy " . count($image_files) . " ảnh:</p>";
        foreach ($image_files as $file) {
            echo "<p>- $file</p>";
        }
    } else {
        echo "<p style='color: red;'>Thư mục không tồn tại!</p>";
    }
}
?>

<style>
body { font-family: Arial, sans-serif; margin: 20px; }
img { margin: 10px 0; }
</style>