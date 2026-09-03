<?php
/**
 * Script tạo ảnh sản phẩm placeholder đẹp
 * Chạy sau khi setup: http://localhost/project-ecommerce/public/generate-product-images.php
 */

header('Content-Type: text/html; charset=UTF-8');

$products = [
    ['id' => 1, 'name' => 'Đèn Bàn', 'icon' => '💡', 'color' => '#FFF8DC'],
    ['id' => 2, 'name' => 'Đèn Ngủ', 'icon' => '🕯️', 'color' => '#FAEBD7'],
    ['id' => 3, 'name' => 'Đèn Treo', 'icon' => '💡', 'color' => '#F5DEB3'],
    ['id' => 4, 'name' => 'Gối Trang Trí', 'icon' => '🛋️', 'color' => '#E8DFD0'],
    ['id' => 5, 'name' => 'Gối Ôm', 'icon' => '🛏️', 'color' => '#F0E8DD'],
    ['id' => 6, 'name' => 'Nệm Ngồi', 'icon' => '🪑', 'color' => '#D4C4A8'],
    ['id' => 7, 'name' => 'Tranh Canvas', 'icon' => '🖼️', 'color' => '#E0D5C7'],
    ['id' => 8, 'name' => 'Tranh Bộ 3', 'icon' => '🎨', 'color' => '#C4B5A0'],
    ['id' => 9, 'name' => 'Khung Ảnh', 'icon' => '🖼️', 'color' => '#F5F0E8'],
    ['id' => 10, 'name' => 'Bình Hoa', 'icon' => '🏺', 'color' => '#DDD0C0'],
    ['id' => 11, 'name' => 'Chậu Cây', 'icon' => '🌿', 'color' => '#E5DDD0'],
    ['id' => 12, 'name' => 'Bình Trang Trí', 'icon' => '🏺', 'color' => '#B8A890'],
    ['id' => 13, 'name' => 'Đồng Hồ Treo', 'icon' => '🕐', 'color' => '#C0B5A5'],
    ['id' => 14, 'name' => 'Đồng Hồ Bàn', 'icon' => '⏰', 'color' => '#D8CFC0'],
    ['id' => 15, 'name' => 'Kệ Sách', 'icon' => '📚', 'color' => '#A0856A'],
    ['id' => 16, 'name' => 'Kệ Treo', 'icon' => '📦', 'color' => '#8B7355'],
    ['id' => 17, 'name' => 'Kệ Treo', 'icon' => '📦', 'color' => '#8B7355'],
];

$outputDir = __DIR__ . '/assets/images/products/';
if (!is_dir($outputDir)) {
    mkdir($outputDir, 0755, true);
}

echo "<!DOCTYPE html><html lang='vi'><head><meta charset='UTF-8'><title>Tạo ảnh sản phẩm</title>
<style>
    body { font-family: Arial, sans-serif; padding: 20px; background: #f5f5f5; }
    h1 { color: #333; }
    .status { padding: 10px; margin: 10px 0; border-radius: 5px; }
    .success { background: #d4edda; color: #155724; }
    .preview { display: inline-block; margin: 10px; text-align: center; }
    .preview img { border: 2px solid #ddd; border-radius: 8px; box-shadow: 0 2px 8px rgba(0,0,0,0.1); }
    .btn { display: inline-block; padding: 12px 24px; background: #28a745; color: white; text-decoration: none; border-radius: 6px; margin: 20px 10px; }
</style>
</head><body>
<h1>🖼️ Tạo Ảnh Sản Phẩm Placeholder</h1>";

$created = 0;
foreach ($products as $product) {
    $filename = "product-{$product['id']}.jpg";
    $filepath = $outputDir . $filename;
    
    $width = 800;
    $height = 800;
    $image = imagecreatetruecolor($width, $height);
    
    // Parse color
    $hex = ltrim($product['color'], '#');
    $r = hexdec(substr($hex, 0, 2));
    $g = hexdec(substr($hex, 2, 2));
    $b = hexdec(substr($hex, 4, 2));
    
    // Background color
    $bgColor = imagecolorallocate($image, $r, $g, $b);
    imagefill($image, 0, 0, $bgColor);
    
    // Darker border
    $borderColor = imagecolorallocate($image, max(0, $r-40), max(0, $g-40), max(0, $b-40));
    imagesetthickness($image, 3);
    imagerectangle($image, 20, 20, $width-20, $height-20, $borderColor);
    
    // Text color
    $textColor = imagecolorallocate($image, 60, 60, 60);
    $lightText = imagecolorallocate($image, 120, 120, 120);
    
    // Draw icon (large)
    $iconSize = 180;
    $iconX = ($width - strlen($product['icon']) * $iconSize/2) / 2;
    $iconY = $height / 2 - 100;
    
    // Use built-in font
    imagestring($image, 5, $width/2 - 30, $iconY, $product['icon'], $textColor);
    
    // Product ID
    $idText = "#" . $product['id'];
    imagestring($image, 5, $width/2 - 20, $height/2 + 50, $idText, $textColor);
    
    // Product name
    $nameLen = strlen($product['name']);
    $nameX = ($width - $nameLen * 10) / 2;
    imagestring($image, 4, $nameX, $height/2 + 100, $product['name'], $textColor);
    
    // Watermark
    imagestring($image, 3, $width/2 - 50, $height - 60, 'DecorNest', $lightText);
    
    // Save
    if (imagejpeg($image, $filepath, 92)) {
        echo "<div class='status success'>✅ Đã tạo: {$filename}</div>";
        echo "<div class='preview'><img src='assets/images/products/{$filename}' width='150'><br><small>{$product['name']}</small></div>";
        $created++;
    }
    
    imagedestroy($image);
}

echo "<div style='clear:both; margin-top: 30px;'>
    <h2>✅ Hoàn thành!</h2>
    <p>Đã tạo <strong>{$created}/16</strong> ảnh sản phẩm!</p>
    <a href='/project-ecommerce/public' class='btn'>← Về Trang Chủ</a>
    <a href='/project-ecommerce/public/products' class='btn' style='background:#667eea;'>🛍️ Xem Sản Phẩm</a>
</div>
</body></html>";
?>
