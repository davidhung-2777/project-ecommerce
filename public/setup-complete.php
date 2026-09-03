<?php
/**
 * Script setup hoàn chỉnh project DecorNest
 * Chạy script này 1 lần duy nhất: http://localhost/project-ecommerce/public/setup-complete.php
 */

error_reporting(E_ALL);
ini_set('display_errors', 1);
header('Content-Type: text/html; charset=UTF-8');

echo "<!DOCTYPE html>
<html lang='vi'>
<head>
    <meta charset='UTF-8'>
    <meta name='viewport' content='width=device-width, initial-scale=1.0'>
    <title>Setup DecorNest</title>
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
        h1 { color: #333; margin-bottom: 10px; font-size: 32px; }
        h2 { color: #667eea; margin: 25px 0 15px; font-size: 20px; border-bottom: 2px solid #e0e0e0; padding-bottom: 8px; }
        .log { 
            padding: 12px 16px; 
            margin: 8px 0; 
            border-radius: 6px; 
            border-left: 4px solid;
            font-size: 14px;
            display: flex;
            align-items: center;
        }
        .success { background: #d4edda; color: #155724; border-left-color: #28a745; }
        .error { background: #f8d7da; color: #721c24; border-left-color: #dc3545; }
        .info { background: #d1ecf1; color: #0c5460; border-left-color: #17a2b8; }
        .warning { background: #fff3cd; color: #856404; border-left-color: #ffc107; }
        .icon { font-size: 18px; margin-right: 10px; }
        .btn {
            display: inline-block;
            padding: 12px 24px;
            background: #28a745;
            color: white;
            text-decoration: none;
            border-radius: 6px;
            font-weight: 600;
            margin-top: 20px;
            transition: background 0.3s;
        }
        .btn:hover { background: #218838; }
        .credentials {
            background: #f8f9fa;
            padding: 20px;
            border-radius: 8px;
            margin: 20px 0;
            border: 2px dashed #667eea;
        }
        .credentials h3 { color: #667eea; margin-bottom: 15px; }
        .credentials .account {
            background: white;
            padding: 15px;
            margin: 10px 0;
            border-radius: 6px;
            border-left: 4px solid #667eea;
        }
        .credentials strong { color: #333; }
        code { 
            background: #f4f4f4; 
            padding: 2px 6px; 
            border-radius: 3px; 
            font-family: 'Courier New', monospace;
            color: #e83e8c;
        }
    </style>
</head>
<body>
    <div class='container'>
        <h1>🚀 Setup DecorNest E-commerce</h1>
        <p style='color: #666; margin-bottom: 30px;'>Thiết lập database và dữ liệu mẫu cho hệ thống</p>";

try {
    $pdo = new PDO('mysql:host=localhost;dbname=decornest;charset=utf8mb4', 'root', '', [
        PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
        PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
        PDO::MYSQL_ATTR_INIT_COMMAND => "SET NAMES utf8mb4 COLLATE utf8mb4_unicode_ci"
    ]);
    
    echo "<div class='log success'><span class='icon'>✅</span>Kết nối database <code>decornest</code> thành công!</div>";
    
    // ========================================
    // 1. FIX DATABASE STRUCTURE
    // ========================================
    echo "<h2>1️⃣ Kiểm tra & Sửa Cấu trúc Database</h2>";
    
    // Check if products table has image_url column
    $stmt = $pdo->query("SHOW COLUMNS FROM products LIKE 'image_url'");
    if ($stmt->rowCount() == 0) {
        $pdo->exec("ALTER TABLE products ADD COLUMN image_url VARCHAR(255) AFTER thumbnail");
        echo "<div class='log success'><span class='icon'>✅</span>Đã thêm cột <code>image_url</code> vào bảng products</div>";
    } else {
        echo "<div class='log info'><span class='icon'>ℹ️</span>Cột <code>image_url</code> đã tồn tại</div>";
    }
    
    // Check if products has base_price
    $stmt = $pdo->query("SHOW COLUMNS FROM products LIKE 'base_price'");
    if ($stmt->rowCount() == 0) {
        $pdo->exec("ALTER TABLE products ADD COLUMN base_price DECIMAL(15,2) AFTER price");
        echo "<div class='log success'><span class='icon'>✅</span>Đã thêm cột <code>base_price</code> vào bảng products</div>";
    } else {
        echo "<div class='log info'><span class='icon'>ℹ️</span>Cột <code>base_price</code> đã tồn tại</div>";
    }
    
    // ========================================
    // 2. CLEAN OLD DATA
    // ========================================
    echo "<h2>2️⃣ Xóa Dữ liệu Cũ (Nếu có)</h2>";
    
    $pdo->exec("SET FOREIGN_KEY_CHECKS=0");
    $pdo->exec("TRUNCATE TABLE cart_items");
    $pdo->exec("TRUNCATE TABLE carts");
    $pdo->exec("TRUNCATE TABLE product_price_tiers");
    $pdo->exec("TRUNCATE TABLE products");
    $pdo->exec("TRUNCATE TABLE categories");
    $pdo->exec("DELETE FROM users WHERE id > 0");
    $pdo->exec("SET FOREIGN_KEY_CHECKS=1");
    
    echo "<div class='log info'><span class='icon'>🗑️</span>Đã xóa dữ liệu cũ</div>";
    
    // ========================================
    // 3. INSERT USERS
    // ========================================
    echo "<h2>3️⃣ Tạo Tài khoản</h2>";
    
    $users = [
        [
            'name' => 'Admin DecorNest',
            'email' => 'admin@decornest.com',
            'phone' => '0987654321',
            'password' => password_hash('admin123', PASSWORD_BCRYPT),
            'role' => 'admin',
            'account_type' => 'individual'
        ],
        [
            'name' => 'Trịnh Tiến Hưng',
            'email' => 'tienhungtrinh59@gmail.com',
            'phone' => '1058081721',
            'password' => password_hash('12345678', PASSWORD_BCRYPT),
            'role' => 'customer',
            'account_type' => 'individual'
        ]
    ];
    
    foreach ($users as $user) {
        $sql = "INSERT INTO users (name, email, phone, password, role, account_type, is_active, created_at) 
                VALUES (?, ?, ?, ?, ?, ?, 1, NOW())";
        $pdo->prepare($sql)->execute([
            $user['name'],
            $user['email'],
            $user['phone'],
            $user['password'],
            $user['role'],
            $user['account_type']
        ]);
        echo "<div class='log success'><span class='icon'>👤</span>Đã tạo user: <strong>{$user['name']}</strong> ({$user['role']})</div>";
    }
    
    // ========================================
    // 4. INSERT CATEGORIES
    // ========================================
    echo "<h2>4️⃣ Tạo Danh Mục Sản Phẩm</h2>";
    
    $categories = [
        ['Đèn Trang Trí', 'den-trang-tri', 'Đèn bàn, đèn ngủ, đèn treo tường phong cách Scandinavian'],
        ['Gối & Nệm', 'goi-nem', 'Gối trang trí, gối ôm, nệm ngồi cao cấp'],
        ['Tranh & Khung', 'tranh-khung', 'Tranh canvas, khung ảnh, tranh treo tường'],
        ['Đồ Gốm Sứ', 'do-gom-su', 'Bình hoa, chậu cây, đồ trang trí gốm sứ'],
        ['Đồng Hồ', 'dong-ho', 'Đồng hồ treo tường, đồng hồ để bàn'],
        ['Kệ & Giá', 'ke-gia', 'Kệ sách, giá treo, kệ trang trí đa năng'],
    ];
    
    foreach ($categories as $idx => $cat) {
        $sql = "INSERT INTO categories (id, name, slug, description, is_active, sort_order, created_at) 
                VALUES (?, ?, ?, ?, 1, ?, NOW())";
        $pdo->prepare($sql)->execute([$idx + 1, $cat[0], $cat[1], $cat[2], ($idx + 1) * 10]);
        echo "<div class='log success'><span class='icon'>📁</span>Đã tạo danh mục: <strong>{$cat[0]}</strong></div>";
    }
    
    // ========================================
    // 5. INSERT PRODUCTS
    // ========================================
    echo "<h2>5️⃣ Tạo Sản Phẩm Mẫu</h2>";
    
    $products = [
        ['Đèn Bàn Gỗ Nordic Oak', 'den-ban-go-nordic-oak', 'Đèn bàn gỗ sồi phong cách Bắc Âu với ánh sáng ấm áp, thiết kế tối giản sang trọng', 450000, 1, 'product-1.jpg', 100],
        ['Đèn Ngủ Minimalist Sven', 'den-ngu-minimalist-sven', 'Đèn ngủ để bàn thiết kế tối giản, ánh sáng dịu nhẹ thích hợp đọc sách', 195000, 1, 'product-2.jpg', 150],
        ['Đèn Treo Nordic Pendant', 'den-treo-nordic-pendant', 'Đèn treo trần phong cách Bắc Âu, chất liệu kim loại cao cấp', 680000, 1, 'product-3.jpg', 50],
        
        ['Gối Trang Trí Linen Helga', 'goi-trang-tri-linen-helga', 'Gối trang trí vải lanh cao cấp, họa tiết hình học hiện đại', 85000, 2, 'product-4.jpg', 200],
        ['Gối Ôm Cotton Organic Sofia', 'goi-om-cotton-organic-sofia', 'Gối ôm cotton organic màu pastel nhẹ nhàng, an toàn cho da', 145000, 2, 'product-5.jpg', 120],
        ['Nệm Ngồi Velvet Freya', 'nem-ngoi-velvet-freya', 'Nệm ngồi nhung cao cấp, thiết kế vuông vức thanh lịch', 220000, 2, 'product-6.jpg', 80],
        
        ['Tranh Canvas Abstract Mist', 'tranh-canvas-abstract-mist', 'Tranh canvas trừu tượng phong cách tối giản, tông màu nhẹ nhàng', 320000, 3, 'product-7.jpg', 90],
        ['Bộ 3 Tranh Nordic Nature', 'bo-3-tranh-nordic-nature', 'Bộ 3 tranh treo tường phong cách Bắc Âu, chủ đề thiên nhiên', 780000, 3, 'product-8.jpg', 60],
        ['Khung Ảnh Gỗ A4 Simple', 'khung-anh-go-a4-simple', 'Khung ảnh gỗ tự nhiên size A4, phù hợp mọi không gian', 125000, 3, 'product-9.jpg', 150],
        
        ['Bình Hoa Gốm Fjord', 'binh-hoa-gom-fjord', 'Bình hoa gốm sứ men matte màu trung tính, thiết kế sang trọng', 180000, 4, 'product-10.jpg', 110],
        ['Chậu Cây Ceramic Beige', 'chau-cay-ceramic-beige', 'Chậu cây gốm màu be với đế gỗ tự nhiên, kích thước vừa phải', 95000, 4, 'product-11.jpg', 200],
        ['Bình Trang Trí Matte Black', 'binh-trang-tri-matte-black', 'Bình trang trí gốm men matte đen, phong cách hiện đại', 210000, 4, 'product-12.jpg', 85],
        
        ['Đồng Hồ Treo Tường Lena', 'dong-ho-treo-tuong-lena', 'Đồng hồ treo tường kim loại, mặt số tối giản không số', 280000, 5, 'product-13.jpg', 75],
        ['Đồng Hồ Để Bàn Minimalist', 'dong-ho-de-ban-minimalist', 'Đồng hồ báo thức thiết kế tối giản, màn hình LED', 165000, 5, 'product-14.jpg', 130],
        
        ['Kệ Sách Gỗ Thông Astrid', 'ke-sach-go-thong-astrid', 'Kệ sách gỗ thông tự nhiên 3 tầng phong cách Scandinavia', 890000, 6, 'product-15.jpg', 40],
        ['Kệ Treo Tường Metal Lars', 'ke-treo-tuong-metal-lars', 'Kệ treo tường kim loại đen kết hợp gỗ, chịu tải tốt', 425000, 6, 'product-16.jpg', 65],
    ];
    
    foreach ($products as $idx => $prod) {
        $sql = "INSERT INTO products (name, slug, short_desc, price, base_price, category_id, image_url, thumbnail, is_active, stock, is_featured, created_at) 
                VALUES (?, ?, ?, ?, ?, ?, ?, ?, 1, ?, ?, NOW())";
        $pdo->prepare($sql)->execute([
            $prod[0],
            $prod[1],
            $prod[2],
            $prod[3],
            $prod[3],
            $prod[4],
            'assets/images/products/' . $prod[5],
            'assets/images/products/' . $prod[5],
            $prod[6],
            ($idx < 4) ? 1 : 0 // Featured: 4 sản phẩm đầu
        ]);
        echo "<div class='log success'><span class='icon'>🛍️</span>Đã tạo: <strong>{$prod[0]}</strong> - " . number_format($prod[3]) . "đ</div>";
    }
    
    // ========================================
    // 6. ADD PRICE TIERS (Wholesale)
    // ========================================
    echo "<h2>6️⃣ Tạo Bảng Giá Sỉ</h2>";
    
    // Add wholesale pricing for first 5 products
    for ($i = 1; $i <= 5; $i++) {
        $sql = "INSERT INTO product_price_tiers (product_id, min_qty, max_qty, price, discount_pct, label, created_at) VALUES
                (?, 10, 49, ?, 10, 'Giá sỉ 10-49 sản phẩm', NOW()),
                (?, 50, 99, ?, 15, 'Giá sỉ 50-99 sản phẩm', NOW()),
                (?, 100, NULL, ?, 20, 'Giá sỉ từ 100 sản phẩm', NOW())";
        
        $basePrice = $products[$i-1][3];
        $pdo->prepare($sql)->execute([
            $i, $basePrice * 0.9,
            $i, $basePrice * 0.85,
            $i, $basePrice * 0.8
        ]);
    }
    echo "<div class='log success'><span class='icon'>💰</span>Đã thêm bảng giá sỉ cho 5 sản phẩm</div>";
    
    // ========================================
    // SUMMARY
    // ========================================
    echo "<h2>🎉 Setup Hoàn Tất!</h2>";
    
    echo "<div class='credentials'>
        <h3>🔐 Thông Tin Đăng Nhập</h3>
        
        <div class='account'>
            <strong>👨‍💼 ADMIN</strong><br>
            Email: <code>admin@decornest.com</code><br>
            Mật khẩu: <code>admin123</code><br>
            URL: <code>http://localhost/project-ecommerce/public/admin</code>
        </div>
        
        <div class='account'>
            <strong>👤 CUSTOMER</strong><br>
            Email: <code>tienhungtrinh59@gmail.com</code><br>
            Mật khẩu: <code>12345678</code><br>
            URL: <code>http://localhost/project-ecommerce/public</code>
        </div>
    </div>";
    
    $stats = [
        'Users' => $pdo->query("SELECT COUNT(*) FROM users")->fetchColumn(),
        'Categories' => $pdo->query("SELECT COUNT(*) FROM categories")->fetchColumn(),
        'Products' => $pdo->query("SELECT COUNT(*) FROM products")->fetchColumn(),
        'Price Tiers' => $pdo->query("SELECT COUNT(*) FROM product_price_tiers")->fetchColumn(),
    ];
    
    echo "<div class='log info'><span class='icon'>📊</span><strong>Thống kê:</strong> ";
    foreach ($stats as $key => $value) {
        echo "$key: <strong>$value</strong> &nbsp;&nbsp;";
    }
    echo "</div>";
    
    echo "<div style='text-align: center; margin-top: 30px;'>
        <a href='/project-ecommerce/public' class='btn'>🏠 Vào Trang Chủ</a>
        <a href='/project-ecommerce/public/admin' class='btn' style='background: #667eea;'>⚙️ Vào Admin Panel</a>
    </div>";
    
    echo "<div class='log warning' style='margin-top: 20px;'>
        <span class='icon'>⚠️</span>
        <strong>Lưu ý:</strong> Bạn nên xóa file <code>setup-complete.php</code> sau khi setup xong để bảo mật.
    </div>";
    
} catch (PDOException $e) {
    echo "<div class='log error'><span class='icon'>❌</span><strong>Lỗi database:</strong> " . htmlspecialchars($e->getMessage()) . "</div>";
} catch (Exception $e) {
    echo "<div class='log error'><span class='icon'>❌</span><strong>Lỗi:</strong> " . htmlspecialchars($e->getMessage()) . "</div>";
}

echo "</div></body></html>";
?>
