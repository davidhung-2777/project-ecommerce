<?php
/**
 * Script sửa database encoding và thêm dữ liệu mẫu
 * Truy cập: http://localhost/project-ecommerce/public/fix-database.php
 */

error_reporting(E_ALL);
ini_set('display_errors', 1);
header('Content-Type: text/html; charset=UTF-8');

echo "<!DOCTYPE html>
<html lang='vi'>
<head>
    <meta charset='UTF-8'>
    <title>Sửa Database</title>
    <style>
        body { font-family: Arial, sans-serif; padding: 20px; background: #f5f5f5; }
        .log { padding: 10px; margin: 5px 0; border-radius: 5px; }
        .success { background: #d4edda; color: #155724; }
        .error { background: #f8d7da; color: #721c24; }
        .info { background: #d1ecf1; color: #0c5460; }
    </style>
</head>
<body>
    <h1>🔧 Sửa Database Encoding</h1>";

try {
    $pdo = new PDO('mysql:host=localhost;dbname=decornest;charset=utf8mb4', 'root', '', [
        PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
        PDO::MYSQL_ATTR_INIT_COMMAND => "SET NAMES utf8mb4 COLLATE utf8mb4_unicode_ci"
    ]);
    
    echo "<div class='log success'>✅ Kết nối database thành công!</div>";
    
    // 1. Sửa encoding các bảng
    echo "<h2>1️⃣ Sửa encoding bảng</h2>";
    $tables = ['products', 'categories', 'users'];
    foreach ($tables as $table) {
        $sql = "ALTER TABLE `{$table}` CONVERT TO CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci";
        $pdo->exec($sql);
        echo "<div class='log success'>✅ Đã sửa encoding bảng: {$table}</div>";
    }
    
    // 2. Xóa dữ liệu cũ (nếu có)
    echo "<h2>2️⃣ Xóa dữ liệu cũ</h2>";
    $pdo->exec("DELETE FROM products");
    $pdo->exec("DELETE FROM categories WHERE id > 0");
    echo "<div class='log info'>🗑️ Đã xóa dữ liệu cũ</div>";
    
    // 3. Thêm categories mới
    echo "<h2>3️⃣ Thêm danh mục</h2>";
    $categories = [
        ['Đèn trang trí', 'Đèn bàn, đèn ngủ, đèn treo tường phong cách Scandinavian', 1],
        ['Gối & Nệm', 'Gối trang trí, gối ôm, nệm ngồi', 1],
        ['Tranh & Khung', 'Tranh canvas, khung ảnh, tranh treo tường', 1],
        ['Đồ gốm', 'Bình hoa, chậu cây, đồ trang trí gốm sứ', 1],
        ['Đồng hồ', 'Đồng hồ treo tường, đồng hồ để bàn', 1],
        ['Kệ & Giá', 'Kệ sách, giá treo, kệ trang trí', 1],
    ];
    
    foreach ($categories as $idx => $cat) {
        $sql = "INSERT INTO categories (id, name, description, is_active, created_at) VALUES (?, ?, ?, ?, NOW())";
        $pdo->prepare($sql)->execute([$idx + 1, $cat[0], $cat[1], $cat[2]]);
        echo "<div class='log success'>✅ Đã thêm: {$cat[0]}</div>";
    }
    
    // 4. Thêm products mới
    echo "<h2>4️⃣ Thêm sản phẩm mẫu</h2>";
    $products = [
        ['Đèn Bàn Gỗ Nordic Oak', 'Đèn bàn gỗ sồi phong cách Bắc Âu với ánh sáng ấm áp', 450000, 1, 'product-1.jpg'],
        ['Gối Trang Trí Helga', 'Gối trang trí vải lanh cao cấp, họa tiết hình học', 85000, 2, 'product-2.jpg'],
        ['Tranh Canvas Mist', 'Tranh canvas trừu tượng phong cách tối giản', 320000, 3, 'product-3.jpg'],
        ['Bình Hoa Gốm Fjord', 'Bình hoa gốm sứ men matte màu trung tính', 120000, 4, 'product-4.jpg'],
        ['Đồng Hồ Treo Lena', 'Đồng hồ treo tường kim loại, mặt số tối giản', 280000, 5, 'product-5.jpg'],
        ['Kệ Sách Gỗ Astrid', 'Kệ sách gỗ thông 3 tầng phong cách Scandinavia', 890000, 6, 'product-6.jpg'],
        ['Đèn Ngủ Sven', 'Đèn ngủ để bàn thiết kế tối giản, ánh sáng dịu', 195000, 1, 'product-7.jpg'],
        ['Gối Ôm Cotton Sofia', 'Gối ôm cotton organic màu pastel nhẹ nhàng', 145000, 2, 'product-8.jpg'],
        ['Tranh Trừu Tượng Nordic', 'Bộ 3 tranh treo tường phong cách Bắc Âu', 680000, 3, 'product-9.jpg'],
        ['Chậu Cây Gốm Beige', 'Chậu cây gốm màu be với đế gỗ', 95000, 4, 'product-10.jpg'],
        ['Đồng Hồ Để Bàn Minimalist', 'Đồng hồ báo thức thiết kế tối giản', 165000, 5, 'product-11.jpg'],
        ['Kệ Treo Tường Lars', 'Kệ treo tường kim loại đen kết hợp gỗ', 425000, 6, 'product-12.jpg'],
    ];
    
    foreach ($products as $idx => $prod) {
        $sql = "INSERT INTO products (name, short_desc, price, base_price, category_id, image_url, is_active, stock, created_at) 
                VALUES (?, ?, ?, ?, ?, ?, 1, ?, NOW())";
        $pdo->prepare($sql)->execute([
            $prod[0],
            $prod[1],
            $prod[2],
            $prod[2],
            $prod[3],
            $prod[4],
            rand(50, 200)
        ]);
        echo "<div class='log success'>✅ Đã thêm: {$prod[0]} - " . number_format($prod[2]) . "đ</div>";
    }
    
    echo "<h2>✅ Hoàn thành!</h2>";
    echo "<p><a href='/project-ecommerce/public' style='display: inline-block; padding: 10px 20px; background: #28a745; color: white; text-decoration: none; border-radius: 5px;'>← Quay về trang chủ</a></p>";
    
} catch (PDOException $e) {
    echo "<div class='log error'>❌ Lỗi: " . $e->getMessage() . "</div>";
}

echo "</body></html>";
?>
