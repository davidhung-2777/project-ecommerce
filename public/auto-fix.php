<?php
/**
 * AUTO FIX - Tự động fix tất cả lỗi
 * Chạy file này 1 lần duy nhất
 */

error_reporting(E_ALL);
ini_set('display_errors', 1);
header('Content-Type: text/html; charset=UTF-8');

echo "<!DOCTYPE html>
<html lang='vi'>
<head>
    <meta charset='UTF-8'>
    <meta name='viewport' content='width=device-width, initial-scale=1.0'>
    <title>Auto Fix - DecorNest</title>
    <style>
        * { margin: 0; padding: 0; box-sizing: border-box; }
        body { 
            font-family: -apple-system, BlinkMacSystemFont, 'Segoe UI', Arial, sans-serif;
            padding: 20px; 
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            min-height: 100vh;
        }
        .container {
            max-width: 800px;
            margin: 0 auto;
            background: white;
            border-radius: 16px;
            padding: 40px;
            box-shadow: 0 20px 60px rgba(0,0,0,0.3);
        }
        h1 { color: #333; margin-bottom: 20px; }
        .log { 
            padding: 12px 16px; 
            margin: 10px 0; 
            border-radius: 6px; 
            border-left: 4px solid;
            font-size: 14px;
        }
        .success { background: #d4edda; color: #155724; border-left-color: #28a745; }
        .error { background: #f8d7da; color: #721c24; border-left-color: #dc3545; }
        .info { background: #d1ecf1; color: #0c5460; border-left-color: #17a2b8; }
        .btn {
            display: inline-block;
            padding: 12px 24px;
            background: #28a745;
            color: white;
            text-decoration: none;
            border-radius: 6px;
            font-weight: 600;
            margin: 10px 5px;
        }
        .btn:hover { background: #218838; }
        .btn-blue { background: #007bff; }
        .btn-blue:hover { background: #0056b3; }
        code { background: #f4f4f4; padding: 2px 6px; border-radius: 3px; }
    </style>
</head>
<body>
    <div class='container'>
        <h1>🔧 Auto Fix - DecorNest</h1>
        <p style='color: #666; margin-bottom: 30px;'>Tự động sửa lỗi database và tạo dữ liệu mẫu</p>";

try {
    // Connect to MySQL
    $pdo = new PDO('mysql:host=localhost;charset=utf8mb4', 'root', '', [
        PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
        PDO::MYSQL_ATTR_INIT_COMMAND => "SET NAMES utf8mb4 COLLATE utf8mb4_unicode_ci"
    ]);
    
    echo "<div class='log success'>✅ Bước 1: Kết nối MySQL thành công</div>";
    
    // Create database if not exists
    $pdo->exec("CREATE DATABASE IF NOT EXISTS decornest CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci");
    $pdo->exec("USE decornest");
    echo "<div class='log success'>✅ Bước 2: Database <strong>decornest</strong> sẵn sàng</div>";
    
    // Check if products exist
    $productCount = 0;
    try {
        $productCount = $pdo->query("SELECT COUNT(*) FROM products")->fetchColumn();
    } catch (PDOException $e) {
        // Table doesn't exist, need to run migration
        echo "<div class='log error'>⚠️ Bảng chưa tồn tại. Cần chạy migration trước!</div>";
        echo "<div class='log info'>📌 <strong>Hướng dẫn:</strong><br>";
        echo "1. Import file: <code>config/migration.sql</code><br>";
        echo "2. Hoặc chạy qua phpMyAdmin</div>";
        echo "<a href='http://localhost/phpmyadmin' target='_blank' class='btn btn-blue'>Mở phpMyAdmin</a>";
        echo "</div></body></html>";
        exit;
    }
    
    echo "<div class='log info'>📊 Hiện có: <strong>{$productCount}</strong> sản phẩm</div>";
    
    if ($productCount > 0) {
        echo "<div class='log success'>";
        echo "<h3 style='margin-bottom: 10px;'>✅ Database đã có dữ liệu!</h3>";
        echo "<p>Không cần fix gì cả. Hệ thống đã sẵn sàng!</p>";
        echo "</div>";
        
        echo "<div style='text-align: center; margin-top: 30px;'>";
        echo "<a href='/project-ecommerce/public/admin/products' class='btn'>📦 Xem Admin Products</a>";
        echo "<a href='/project-ecommerce/public' class='btn btn-blue'>🏠 Về Trang Chủ</a>";
        echo "</div>";
        
    } else {
        echo "<div class='log info'>⚙️ Bước 3: Đang tạo dữ liệu mẫu...</div>";
        
        // Clear old data
        $pdo->exec("SET FOREIGN_KEY_CHECKS=0");
        $pdo->exec("TRUNCATE TABLE products");
        $pdo->exec("TRUNCATE TABLE categories");
        $pdo->exec("DELETE FROM users WHERE id > 0");
        $pdo->exec("SET FOREIGN_KEY_CHECKS=1");
        
        // Create users
        $users = [
            ['Admin DecorNest', 'admin@decornest.com', '0987654321', password_hash('admin123', PASSWORD_BCRYPT), 'admin'],
            ['Trịnh Tiến Hưng', 'tienhungtrinh59@gmail.com', '1058081721', password_hash('12345678', PASSWORD_BCRYPT), 'customer']
        ];
        
        foreach ($users as $user) {
            $pdo->prepare("INSERT INTO users (name, email, phone, password, role, account_type, is_active, created_at) 
                           VALUES (?, ?, ?, ?, ?, 'individual', 1, NOW())")
                ->execute($user);
        }
        echo "<div class='log success'>✅ Tạo 2 users (admin + customer)</div>";
        
        // Create categories
        $categories = [
            [1, 'Đèn Trang Trí', 'den-trang-tri', 'Đèn bàn, đèn ngủ, đèn treo tường', 10],
            [2, 'Gối & Nệm', 'goi-nem', 'Gối trang trí, gối ôm cao cấp', 20],
            [3, 'Tranh & Khung', 'tranh-khung', 'Tranh canvas, khung ảnh', 30],
            [4, 'Đồ Gốm Sứ', 'do-gom-su', 'Bình hoa, chậu cây', 40],
            [5, 'Đồng Hồ', 'dong-ho', 'Đồng hồ treo tường, để bàn', 50],
            [6, 'Kệ & Giá', 'ke-gia', 'Kệ sách, giá treo', 60],
        ];
        
        foreach ($categories as $cat) {
            $pdo->prepare("INSERT INTO categories (id, name, slug, description, is_active, sort_order, created_at) 
                           VALUES (?, ?, ?, ?, 1, ?, NOW())")
                ->execute($cat);
        }
        echo "<div class='log success'>✅ Tạo 6 danh mục</div>";
        
        // Create products
        $products = [
            ['Đèn Bàn Gỗ Nordic Oak', 'den-ban-go-nordic-oak', 'Đèn bàn gỗ sồi Bắc Âu ánh sáng ấm', 450000, 1, 100],
            ['Đèn Ngủ Minimalist Sven', 'den-ngu-minimalist-sven', 'Đèn ngủ tối giản ánh sáng dịu', 195000, 1, 150],
            ['Đèn Treo Nordic Pendant', 'den-treo-nordic-pendant', 'Đèn treo trần kim loại cao cấp', 680000, 1, 50],
            ['Gối Trang Trí Linen Helga', 'goi-trang-tri-linen-helga', 'Gối vải lanh họa tiết hình học', 85000, 2, 200],
            ['Gối Ôm Cotton Organic Sofia', 'goi-om-cotton-organic-sofia', 'Gối ôm cotton màu pastel', 145000, 2, 120],
            ['Nệm Ngồi Velvet Freya', 'nem-ngoi-velvet-freya', 'Nệm nhung thiết kế vuông', 220000, 2, 80],
            ['Tranh Canvas Abstract Mist', 'tranh-canvas-abstract-mist', 'Tranh trừu tượng tối giản', 320000, 3, 90],
            ['Bộ 3 Tranh Nordic Nature', 'bo-3-tranh-nordic-nature', 'Bộ tranh phong cách Bắc Âu', 780000, 3, 60],
            ['Khung Ảnh Gỗ A4 Simple', 'khung-anh-go-a4-simple', 'Khung ảnh gỗ tự nhiên A4', 125000, 3, 150],
            ['Bình Hoa Gốm Fjord', 'binh-hoa-gom-fjord', 'Bình gốm men matte sang trọng', 180000, 4, 110],
            ['Chậu Cây Ceramic Beige', 'chau-cay-ceramic-beige', 'Chậu gốm màu be với đế gỗ', 95000, 4, 200],
            ['Bình Trang Trí Matte Black', 'binh-trang-tri-matte-black', 'Bình gốm đen hiện đại', 210000, 4, 85],
            ['Đồng Hồ Treo Tường Lena', 'dong-ho-treo-tuong-lena', 'Đồng hồ kim loại tối giản', 280000, 5, 75],
            ['Đồng Hồ Để Bàn Minimalist', 'dong-ho-de-ban-minimalist', 'Đồng hồ báo thức LED', 165000, 5, 130],
            ['Kệ Sách Gỗ Thông Astrid', 'ke-sach-go-thong-astrid', 'Kệ gỗ thông 3 tầng', 890000, 6, 40],
            ['Kệ Treo Tường Metal Lars', 'ke-treo-tuong-metal-lars', 'Kệ kim loại đen kết hợp gỗ', 425000, 6, 65],
        ];
        
        $stmt = $pdo->prepare("INSERT INTO products (name, slug, short_desc, price, base_price, category_id, is_active, stock, is_featured, created_at) 
                               VALUES (?, ?, ?, ?, ?, ?, 1, ?, ?, NOW())");
        
        foreach ($products as $idx => $prod) {
            $stmt->execute([
                $prod[0], $prod[1], $prod[2], $prod[3], $prod[3],
                $prod[4], $prod[5], ($idx < 4) ? 1 : 0
            ]);
        }
        echo "<div class='log success'>✅ Tạo 16 sản phẩm</div>";
        
        // Summary
        echo "<div class='log success' style='margin-top: 20px;'>";
        echo "<h3>🎉 HOÀN THÀNH!</h3>";
        echo "<p style='margin-top: 10px;'><strong>Đã tạo:</strong></p>";
        echo "<ul style='margin-left: 20px; margin-top: 5px;'>";
        echo "<li>✅ 2 users (admin + customer)</li>";
        echo "<li>✅ 6 danh mục</li>";
        echo "<li>✅ 16 sản phẩm</li>";
        echo "</ul>";
        echo "</div>";
        
        echo "<div style='background: #d1ecf1; padding: 20px; border-radius: 8px; margin-top: 20px; border-left: 4px solid #17a2b8;'>";
        echo "<h3 style='color: #0c5460; margin-bottom: 10px;'>🔐 Thông tin đăng nhập:</h3>";
        echo "<p style='color: #0c5460; margin: 5px 0;'><strong>Admin:</strong> admin@decornest.com / admin123</p>";
        echo "<p style='color: #0c5460; margin: 5px 0;'><strong>Customer:</strong> tienhungtrinh59@gmail.com / 12345678</p>";
        echo "</div>";
        
        echo "<div style='text-align: center; margin-top: 30px;'>";
        echo "<a href='/project-ecommerce/public/admin/products' class='btn'>📦 Vào Admin Products</a>";
        echo "<a href='/project-ecommerce/public' class='btn btn-blue'>🏠 Về Trang Chủ</a>";
        echo "</div>";
    }
    
} catch (PDOException $e) {
    echo "<div class='log error'><strong>❌ Lỗi:</strong> " . htmlspecialchars($e->getMessage()) . "</div>";
    echo "<div class='log info'>";
    echo "<p><strong>Kiểm tra:</strong></p>";
    echo "<ul style='margin-left: 20px;'>";
    echo "<li>XAMPP có đang chạy không?</li>";
    echo "<li>MySQL đã khởi động chưa?</li>";
    echo "<li>Port 3306 có bị chiếm không?</li>";
    echo "</ul>";
    echo "</div>";
}

echo "</div></body></html>";
?>
