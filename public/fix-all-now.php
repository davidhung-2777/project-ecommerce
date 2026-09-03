<?php
/**
 * FIX ALL - Sửa toàn bộ lỗi font + database + dữ liệu
 * Chạy 1 lần duy nhất: http://localhost/project-ecommerce/public/fix-all-now.php
 */

error_reporting(E_ALL);
ini_set('display_errors', 1);
header('Content-Type: text/html; charset=UTF-8');

echo "<!DOCTYPE html>
<html lang='vi'>
<head>
    <meta charset='UTF-8'>
    <meta name='viewport' content='width=device-width, initial-scale=1.0'>
    <title>Fix All - DecorNest</title>
    <style>
        * { margin: 0; padding: 0; box-sizing: border-box; }
        body { 
            font-family: -apple-system, BlinkMacSystemFont, 'Segoe UI', Arial, sans-serif;
            padding: 20px; 
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            min-height: 100vh;
        }
        .container {
            max-width: 900px;
            margin: 0 auto;
            background: white;
            border-radius: 12px;
            padding: 30px;
            box-shadow: 0 20px 60px rgba(0,0,0,0.3);
        }
        h1 { color: #333; margin-bottom: 10px; font-size: 28px; }
        h2 { color: #667eea; margin: 25px 0 15px; font-size: 18px; border-bottom: 2px solid #e0e0e0; padding-bottom: 8px; }
        .log { 
            padding: 12px 16px; 
            margin: 8px 0; 
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
    </style>
</head>
<body>
    <div class='container'>
        <h1>🔧 Fix All - Sửa Toàn Bộ Lỗi</h1>
        <p style='color: #666; margin-bottom: 30px;'>Sửa lỗi font chữ + database encoding + tạo dữ liệu mẫu</p>";

try {
    $pdo = new PDO('mysql:host=localhost;charset=utf8mb4', 'root', '', [
        PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
        PDO::MYSQL_ATTR_INIT_COMMAND => "SET NAMES utf8mb4 COLLATE utf8mb4_unicode_ci"
    ]);
    
    echo "<div class='log success'>✅ Kết nối MySQL thành công!</div>";
    
    // ========================================
    // 1. TẠO/CHỌN DATABASE
    // ========================================
    echo "<h2>1️⃣ Kiểm tra Database</h2>";
    
    $pdo->exec("CREATE DATABASE IF NOT EXISTS decornest CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci");
    $pdo->exec("USE decornest");
    echo "<div class='log success'>✅ Database <strong>decornest</strong> sẵn sàng (UTF-8)</div>";
    
    // ========================================
    // 2. SỬA ENCODING TẤT CẢ BẢNG
    // ========================================
    echo "<h2>2️⃣ Sửa Encoding Tất Cả Bảng</h2>";
    
    $tables = $pdo->query("SHOW TABLES")->fetchAll(PDO::FETCH_COLUMN);
    
    foreach ($tables as $table) {
        try {
            $pdo->exec("ALTER TABLE `{$table}` CONVERT TO CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci");
            echo "<div class='log success'>✅ Đã sửa encoding: <strong>{$table}</strong></div>";
        } catch (Exception $e) {
            echo "<div class='log error'>⚠️ Bỏ qua: {$table} - " . $e->getMessage() . "</div>";
        }
    }
    
    // ========================================
    // 3. XÓA DỮ LIỆU CŨ (LỖI ENCODING)
    // ========================================
    echo "<h2>3️⃣ Xóa Dữ Liệu Cũ (Lỗi Font)</h2>";
    
    $pdo->exec("SET FOREIGN_KEY_CHECKS=0");
    $pdo->exec("TRUNCATE TABLE cart_items");
    $pdo->exec("TRUNCATE TABLE product_price_tiers");
    $pdo->exec("TRUNCATE TABLE products");
    $pdo->exec("TRUNCATE TABLE categories");
    $pdo->exec("DELETE FROM users WHERE id > 0");
    $pdo->exec("SET FOREIGN_KEY_CHECKS=1");
    
    echo "<div class='log info'>🗑️ Đã xóa dữ liệu cũ (lỗi encoding)</div>";
    
    // ========================================
    // 4. TẠO USERS MỚI
    // ========================================
    echo "<h2>4️⃣ Tạo Tài Khoản</h2>";
    
    $users = [
        [
            'name' => 'Admin DecorNest',
            'email' => 'admin@decornest.com',
            'phone' => '0987654321',
            'password' => password_hash('admin123', PASSWORD_BCRYPT),
            'role' => 'admin'
        ],
        [
            'name' => 'Trịnh Tiến Hưng',
            'email' => 'tienhungtrinh59@gmail.com',
            'phone' => '1058081721',
            'password' => password_hash('12345678', PASSWORD_BCRYPT),
            'role' => 'customer'
        ]
    ];
    
    foreach ($users as $user) {
        $pdo->prepare("INSERT INTO users (name, email, phone, password, role, account_type, is_active, created_at) 
                       VALUES (?, ?, ?, ?, ?, 'individual', 1, NOW())")
            ->execute([$user['name'], $user['email'], $user['phone'], $user['password'], $user['role']]);
        
        echo "<div class='log success'>✅ Tạo user: <strong>{$user['name']}</strong> ({$user['role']})</div>";
    }
    
    // ========================================
    // 5. TẠO CATEGORIES
    // ========================================
    echo "<h2>5️⃣ Tạo Danh Mục</h2>";
    
    $categories = [
        ['Đèn Trang Trí', 'den-trang-tri', 'Đèn bàn, đèn ngủ, đèn treo tường'],
        ['Gối & Nệm', 'goi-nem', 'Gối trang trí, gối ôm cao cấp'],
        ['Tranh & Khung', 'tranh-khung', 'Tranh canvas, khung ảnh'],
        ['Đồ Gốm Sứ', 'do-gom-su', 'Bình hoa, chậu cây'],
        ['Đồng Hồ', 'dong-ho', 'Đồng hồ treo tường, để bàn'],
        ['Kệ & Giá', 'ke-gia', 'Kệ sách, giá treo'],
    ];
    
    foreach ($categories as $idx => $cat) {
        $pdo->prepare("INSERT INTO categories (id, name, slug, description, is_active, sort_order, created_at) 
                       VALUES (?, ?, ?, ?, 1, ?, NOW())")
            ->execute([$idx + 1, $cat[0], $cat[1], $cat[2], ($idx + 1) * 10]);
        
        echo "<div class='log success'>✅ {$cat[0]}</div>";
    }
    
    // ========================================
    // 6. TẠO PRODUCTS
    // ========================================
    echo "<h2>6️⃣ Tạo Sản Phẩm (UTF-8 Chuẩn)</h2>";
    
    $products = [
        ['Đèn Bàn Gỗ Nordic Oak', 'den-ban-go-nordic-oak', 'Đèn bàn gỗ sồi Bắc Âu ánh sáng ấm', 450000, 1, 'product-1.jpg', 100],
        ['Đèn Ngủ Minimalist Sven', 'den-ngu-minimalist-sven', 'Đèn ngủ tối giản ánh sáng dịu', 195000, 1, 'product-2.jpg', 150],
        ['Đèn Treo Nordic Pendant', 'den-treo-nordic-pendant', 'Đèn treo trần kim loại cao cấp', 680000, 1, 'product-3.jpg', 50],
        
        ['Gối Trang Trí Linen Helga', 'goi-trang-tri-linen-helga', 'Gối vải lanh họa tiết hình học', 85000, 2, 'product-4.jpg', 200],
        ['Gối Ôm Cotton Organic Sofia', 'goi-om-cotton-organic-sofia', 'Gối ôm cotton màu pastel', 145000, 2, 'product-5.jpg', 120],
        ['Nệm Ngồi Velvet Freya', 'nem-ngoi-velvet-freya', 'Nệm nhung thiết kế vuông', 220000, 2, 'product-6.jpg', 80],
        
        ['Tranh Canvas Abstract Mist', 'tranh-canvas-abstract-mist', 'Tranh trừu tượng tối giản', 320000, 3, 'product-7.jpg', 90],
        ['Bộ 3 Tranh Nordic Nature', 'bo-3-tranh-nordic-nature', 'Bộ tranh phong cách Bắc Âu', 780000, 3, 'product-8.jpg', 60],
        ['Khung Ảnh Gỗ A4 Simple', 'khung-anh-go-a4-simple', 'Khung ảnh gỗ tự nhiên A4', 125000, 3, 'product-9.jpg', 150],
        
        ['Bình Hoa Gốm Fjord', 'binh-hoa-gom-fjord', 'Bình gốm men matte sang trọng', 180000, 4, 'product-10.jpg', 110],
        ['Chậu Cây Ceramic Beige', 'chau-cay-ceramic-beige', 'Chậu gốm màu be với đế gỗ', 95000, 4, 'product-11.jpg', 200],
        ['Bình Trang Trí Matte Black', 'binh-trang-tri-matte-black', 'Bình gốm đen hiện đại', 210000, 4, 'product-12.jpg', 85],
        
        ['Đồng Hồ Treo Tường Lena', 'dong-ho-treo-tuong-lena', 'Đồng hồ kim loại tối giản', 280000, 5, 'product-13.jpg', 75],
        ['Đồng Hồ Để Bàn Minimalist', 'dong-ho-de-ban-minimalist', 'Đồng hồ báo thức LED', 165000, 5, 'product-14.jpg', 130],
        
        ['Kệ Sách Gỗ Thông Astrid', 'ke-sach-go-thong-astrid', 'Kệ gỗ thông 3 tầng', 890000, 6, 'product-15.jpg', 40],
        ['Kệ Treo Tường Metal Lars', 'ke-treo-tuong-metal-lars', 'Kệ kim loại đen kết hợp gỗ', 425000, 6, 'product-16.jpg', 65],
    ];
    
    foreach ($products as $idx => $prod) {
        $pdo->prepare("INSERT INTO products (name, slug, short_desc, price, base_price, category_id, image_url, thumbnail, is_active, stock, is_featured, created_at) 
                       VALUES (?, ?, ?, ?, ?, ?, ?, ?, 1, ?, ?, NOW())")
            ->execute([
                $prod[0], $prod[1], $prod[2], $prod[3], $prod[3],
                $prod[4], 'assets/images/products/' . $prod[5], 'assets/images/products/' . $prod[5],
                $prod[6], ($idx < 4) ? 1 : 0
            ]);
        
        echo "<div class='log success'>✅ {$prod[0]} - " . number_format($prod[3]) . "đ</div>";
    }
    
    // ========================================
    // 7. THÊM GIÁ SỈ
    // ========================================
    echo "<h2>7️⃣ Tạo Bảng Giá Sỉ</h2>";
    
    for ($i = 1; $i <= 5; $i++) {
        $basePrice = $products[$i-1][3];
        $pdo->prepare("INSERT INTO product_price_tiers (product_id, min_qty, max_qty, price, discount_pct, label, created_at) VALUES
                       (?, 10, 49, ?, 10, 'Giá sỉ 10-49 sản phẩm', NOW()),
                       (?, 50, 99, ?, 15, 'Giá sỉ 50-99 sản phẩm', NOW()),
                       (?, 100, NULL, ?, 20, 'Giá sỉ từ 100 sản phẩm', NOW())")
            ->execute([
                $i, $basePrice * 0.9,
                $i, $basePrice * 0.85,
                $i, $basePrice * 0.8
            ]);
    }
    
    echo "<div class='log success'>✅ Đã tạo bảng giá sỉ cho 5 sản phẩm</div>";
    
    // ========================================
    // THỐNG KÊ
    // ========================================
    echo "<h2>🎉 Hoàn Tất!</h2>";
    
    $stats = [
        'Users' => $pdo->query("SELECT COUNT(*) FROM users")->fetchColumn(),
        'Categories' => $pdo->query("SELECT COUNT(*) FROM categories")->fetchColumn(),
        'Products' => $pdo->query("SELECT COUNT(*) FROM products")->fetchColumn(),
        'Price Tiers' => $pdo->query("SELECT COUNT(*) FROM product_price_tiers")->fetchColumn(),
    ];
    
    echo "<div class='log info' style='font-size: 16px;'>";
    echo "<strong>📊 Dữ liệu đã tạo:</strong><br>";
    foreach ($stats as $key => $value) {
        echo "• {$key}: <strong>{$value}</strong><br>";
    }
    echo "</div>";
    
    echo "<div style='background: #d4edda; padding: 20px; border-radius: 8px; margin-top: 20px; border-left: 4px solid #28a745;'>";
    echo "<h3 style='color: #155724; margin-bottom: 10px;'>✅ ĐÃ SỬA XONG!</h3>";
    echo "<p style='color: #155724;'>Tất cả dữ liệu giờ đã dùng <strong>UTF-8 chuẩn</strong>. Font chữ hiển thị đúng!</p>";
    echo "</div>";
    
    echo "<div style='text-align: center; margin-top: 30px;'>";
    echo "<a href='/project-ecommerce/public' class='btn'>🏠 Vào Trang Chủ</a>";
    echo "<a href='/project-ecommerce/public/products' class='btn btn-blue'>🛍️ Xem Sản Phẩm</a>";
    echo "<a href='/project-ecommerce/public/generate-product-images.php' class='btn' style='background:#6c757d;'>🖼️ Tạo Ảnh</a>";
    echo "</div>";
    
    echo "<div class='log info' style='margin-top: 20px;'>";
    echo "<strong>🔐 Thông tin đăng nhập:</strong><br><br>";
    echo "👨‍💼 <strong>Admin:</strong> admin@decornest.com / admin123<br>";
    echo "👤 <strong>Customer:</strong> tienhungtrinh59@gmail.com / 12345678";
    echo "</div>";
    
} catch (PDOException $e) {
    echo "<div class='log error'><strong>❌ Lỗi:</strong> " . htmlspecialchars($e->getMessage()) . "</div>";
    echo "<p style='color: #721c24; margin-top: 20px;'>Kiểm tra:</p>";
    echo "<ul style='color: #721c24;'>";
    echo "<li>XAMPP đang chạy?</li>";
    echo "<li>MySQL đã khởi động?</li>";
    echo "<li>Port 3306 có bị chiếm?</li>";
    echo "</ul>";
}

echo "</div></body></html>";
?>
