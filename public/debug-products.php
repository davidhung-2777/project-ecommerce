<?php
error_reporting(E_ALL);
ini_set('display_errors', 1);
header('Content-Type: text/html; charset=UTF-8');

echo "<!DOCTYPE html>
<html lang='vi'>
<head>
    <meta charset='UTF-8'>
    <title>Debug Products</title>
    <style>
        body { font-family: Arial, sans-serif; padding: 20px; background: #f5f5f5; }
        .box { background: white; padding: 20px; margin: 10px 0; border-radius: 5px; }
        .success { background: #d4edda; color: #155724; }
        .error { background: #f8d7da; color: #721c24; }
        .info { background: #d1ecf1; color: #0c5460; }
        pre { background: #f4f4f4; padding: 10px; overflow-x: auto; }
    </style>
</head>
<body>
    <h1>🐛 Debug Products - DecorNest</h1>";

try {
    // 1. Test database connection
    echo "<div class='box info'><h2>1️⃣ Kết Nối Database</h2>";
    $pdo = new PDO('mysql:host=localhost;dbname=decornest;charset=utf8mb4', 'root', '', [
        PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION
    ]);
    echo "✅ Kết nối thành công!<br>";
    echo "Database: <strong>decornest</strong><br>";
    echo "Charset: <strong>utf8mb4</strong></div>";

    // 2. Check if products table exists
    echo "<div class='box info'><h2>2️⃣ Kiểm Tra Bảng Products</h2>";
    $tables = $pdo->query("SHOW TABLES LIKE 'products'")->fetchAll();
    if (empty($tables)) {
        echo "<div class='error'>❌ Bảng 'products' KHÔNG TỒN TẠI!<br>Bạn cần chạy migration trước.</div>";
    } else {
        echo "✅ Bảng 'products' tồn tại<br>";
        
        // Get table structure
        $columns = $pdo->query("DESCRIBE products")->fetchAll(PDO::FETCH_ASSOC);
        echo "<details><summary>Xem cấu trúc bảng</summary><pre>";
        foreach ($columns as $col) {
            echo $col['Field'] . " | " . $col['Type'] . " | " . $col['Null'] . " | " . $col['Key'] . "\n";
        }
        echo "</pre></details>";
    }
    echo "</div>";

    // 3. Count products
    echo "<div class='box info'><h2>3️⃣ Đếm Sản Phẩm</h2>";
    $totalProducts = $pdo->query("SELECT COUNT(*) FROM products")->fetchColumn();
    $activeProducts = $pdo->query("SELECT COUNT(*) FROM products WHERE is_active = 1")->fetchColumn();
    
    echo "Tổng sản phẩm: <strong>$totalProducts</strong><br>";
    echo "Sản phẩm hiện: <strong>$activeProducts</strong><br>";
    
    if ($totalProducts == 0) {
        echo "<div class='error'>❌ <strong>DATABASE TRỐNG!</strong><br>";
        echo "Bạn cần chạy script tạo dữ liệu:<br>";
        echo "<a href='fix-all-now.php' style='display:inline-block;margin:10px 0;padding:10px 20px;background:#28a745;color:white;text-decoration:none;border-radius:5px;'>🔧 Chạy Fix All Now</a>";
        echo "</div>";
    }
    echo "</div>";

    // 4. Show sample products
    if ($totalProducts > 0) {
        echo "<div class='box info'><h2>4️⃣ Sản Phẩm Mẫu (5 đầu tiên)</h2>";
        $products = $pdo->query("SELECT id, name, price, stock, is_active FROM products LIMIT 5")->fetchAll(PDO::FETCH_ASSOC);
        echo "<table border='1' cellpadding='5' style='border-collapse:collapse;width:100%;'>";
        echo "<tr><th>ID</th><th>Tên</th><th>Giá</th><th>Tồn kho</th><th>Hiện</th></tr>";
        foreach ($products as $p) {
            echo "<tr>";
            echo "<td>{$p['id']}</td>";
            echo "<td>" . htmlspecialchars($p['name']) . "</td>";
            echo "<td>" . number_format($p['price']) . "đ</td>";
            echo "<td>{$p['stock']}</td>";
            echo "<td>" . ($p['is_active'] ? '✅' : '❌') . "</td>";
            echo "</tr>";
        }
        echo "</table></div>";
    }

    // 5. Check categories
    echo "<div class='box info'><h2>5️⃣ Kiểm Tra Danh Mục</h2>";
    $categoriesExist = $pdo->query("SHOW TABLES LIKE 'categories'")->fetchAll();
    if (empty($categoriesExist)) {
        echo "❌ Bảng 'categories' không tồn tại!<br>";
    } else {
        $totalCats = $pdo->query("SELECT COUNT(*) FROM categories")->fetchColumn();
        echo "Tổng danh mục: <strong>$totalCats</strong><br>";
        
        if ($totalCats > 0) {
            $cats = $pdo->query("SELECT id, name FROM categories LIMIT 10")->fetchAll(PDO::FETCH_ASSOC);
            echo "<ul>";
            foreach ($cats as $cat) {
                echo "<li>{$cat['id']}. " . htmlspecialchars($cat['name']) . "</li>";
            }
            echo "</ul>";
        }
    }
    echo "</div>";

    // 6. Check users
    echo "<div class='box info'><h2>6️⃣ Kiểm Tra Users</h2>";
    $usersExist = $pdo->query("SHOW TABLES LIKE 'users'")->fetchAll();
    if (empty($usersExist)) {
        echo "❌ Bảng 'users' không tồn tại!<br>";
    } else {
        $totalUsers = $pdo->query("SELECT COUNT(*) FROM users")->fetchColumn();
        $adminCount = $pdo->query("SELECT COUNT(*) FROM users WHERE role = 'admin'")->fetchColumn();
        echo "Tổng users: <strong>$totalUsers</strong><br>";
        echo "Admin: <strong>$adminCount</strong><br>";
        
        if ($totalUsers > 0) {
            $users = $pdo->query("SELECT id, name, email, role FROM users LIMIT 5")->fetchAll(PDO::FETCH_ASSOC);
            echo "<table border='1' cellpadding='5' style='border-collapse:collapse;margin-top:10px;'>";
            echo "<tr><th>ID</th><th>Tên</th><th>Email</th><th>Role</th></tr>";
            foreach ($users as $u) {
                echo "<tr>";
                echo "<td>{$u['id']}</td>";
                echo "<td>" . htmlspecialchars($u['name']) . "</td>";
                echo "<td>" . htmlspecialchars($u['email']) . "</td>";
                echo "<td><strong>{$u['role']}</strong></td>";
                echo "</tr>";
            }
            echo "</table>";
        }
    }
    echo "</div>";

    // 7. Recommendation
    echo "<div class='box'><h2>7️⃣ Kết Luận</h2>";
    if ($totalProducts == 0) {
        echo "<div class='error'>";
        echo "<h3>❌ Database chưa có dữ liệu!</h3>";
        echo "<p><strong>Nguyên nhân:</strong> Bạn chưa chạy script tạo dữ liệu mẫu.</p>";
        echo "<p><strong>Giải pháp:</strong></p>";
        echo "<ol>";
        echo "<li>Chạy script: <a href='fix-all-now.php'>fix-all-now.php</a></li>";
        echo "<li>Hoặc import file SQL migration</li>";
        echo "</ol>";
        echo "<a href='fix-all-now.php' style='display:inline-block;margin:10px 0;padding:12px 24px;background:#28a745;color:white;text-decoration:none;border-radius:5px;font-weight:bold;'>🔧 FIX NGAY</a>";
        echo "</div>";
    } else {
        echo "<div class='success'>";
        echo "<h3>✅ Database đã có dữ liệu!</h3>";
        echo "<p>Tổng {$totalProducts} sản phẩm đã sẵn sàng.</p>";
        echo "<a href='/project-ecommerce/public/admin/products' style='display:inline-block;margin:10px 5px;padding:12px 24px;background:#007bff;color:white;text-decoration:none;border-radius:5px;'>📦 Xem Admin Products</a>";
        echo "<a href='/project-ecommerce/public/products' style='display:inline-block;margin:10px 5px;padding:12px 24px;background:#6f42c1;color:white;text-decoration:none;border-radius:5px;'>🛍️ Xem Frontend</a>";
        echo "</div>";
    }
    echo "</div>";

} catch (PDOException $e) {
    echo "<div class='box error'>";
    echo "<h2>❌ Lỗi Kết Nối Database</h2>";
    echo "<p><strong>Lỗi:</strong> " . htmlspecialchars($e->getMessage()) . "</p>";
    echo "<p><strong>Kiểm tra:</strong></p>";
    echo "<ul>";
    echo "<li>XAMPP có đang chạy không?</li>";
    echo "<li>MySQL đã khởi động chưa? (Check XAMPP Control Panel)</li>";
    echo "<li>Database 'decornest' đã được tạo chưa?</li>";
    echo "<li>Username/password đúng chưa? (mặc định: root / không mật khẩu)</li>";
    echo "</ul>";
    echo "</div>";
}

echo "<div class='box'><h2>🔗 Quick Links</h2>";
echo "<a href='/project-ecommerce/public' style='display:inline-block;margin:5px;padding:10px 20px;background:#6c757d;color:white;text-decoration:none;border-radius:5px;'>🏠 Trang Chủ</a>";
echo "<a href='fix-all-now.php' style='display:inline-block;margin:5px;padding:10px 20px;background:#28a745;color:white;text-decoration:none;border-radius:5px;'>🔧 Fix Database</a>";
echo "<a href='test-admin-crud.php' style='display:inline-block;margin:5px;padding:10px 20px;background:#17a2b8;color:white;text-decoration:none;border-radius:5px;'>🧪 Test CRUD</a>";
echo "</div>";

echo "</body></html>";
?>
