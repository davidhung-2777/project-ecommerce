<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <title>Quick Fix - DecorNest</title>
    <style>
        body { font-family: Arial, sans-serif; padding: 20px; background: #f5f5f5; }
        .box { background: white; padding: 30px; border-radius: 8px; max-width: 800px; margin: 0 auto; box-shadow: 0 2px 10px rgba(0,0,0,0.1); }
        h1 { color: #333; }
        .status { padding: 15px; margin: 15px 0; border-radius: 5px; border-left: 4px solid; }
        .success { background: #d4edda; color: #155724; border-left-color: #28a745; }
        .error { background: #f8d7da; color: #721c24; border-left-color: #dc3545; }
        .btn { display: inline-block; padding: 12px 24px; background: #007bff; color: white; text-decoration: none; border-radius: 5px; margin: 10px 5px; }
        .btn:hover { background: #0056b3; }
    </style>
</head>
<body>
    <div class="box">
        <h1>🔧 Quick Fix - DecorNest</h1>
        
        <?php
        try {
            $pdo = new PDO('mysql:host=localhost;dbname=decornest;charset=utf8mb4', 'root', '', [
                PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION
            ]);
            
            echo '<div class="status success">✅ Kết nối database thành công</div>';
            
            // Check if we have data
            $productCount = $pdo->query("SELECT COUNT(*) FROM products WHERE is_active=1")->fetchColumn();
            $userCount = $pdo->query("SELECT COUNT(*) FROM users")->fetchColumn();
            $categoryCount = $pdo->query("SELECT COUNT(*) FROM categories WHERE is_active=1")->fetchColumn();
            
            echo '<h2>📊 Trạng Thái Database</h2>';
            echo '<ul>';
            echo '<li><strong>Sản phẩm:</strong> ' . $productCount . '</li>';
            echo '<li><strong>Danh mục:</strong> ' . $categoryCount . '</li>';
            echo '<li><strong>Users:</strong> ' . $userCount . '</li>';
            echo '</ul>';
            
            if ($productCount == 0 || $userCount == 0 || $categoryCount == 0) {
                echo '<div class="status error">⚠️ <strong>Database trống!</strong> Bạn cần chạy script tạo dữ liệu.</div>';
                echo '<a href="fix-all-now.php" class="btn">🔧 Chạy Fix All Now</a>';
            } else {
                echo '<div class="status success">✅ Database đã có dữ liệu!</div>';
                echo '<a href="/project-ecommerce/public/admin/products" class="btn">📦 Xem Sản Phẩm</a>';
                echo '<a href="/project-ecommerce/public/admin" class="btn">🎯 Admin Panel</a>';
            }
            
        } catch (PDOException $e) {
            echo '<div class="status error">❌ Lỗi: ' . htmlspecialchars($e->getMessage()) . '</div>';
            echo '<p>Kiểm tra:</p>';
            echo '<ul>';
            echo '<li>XAMPP có đang chạy không?</li>';
            echo '<li>MySQL đã khởi động chưa?</li>';
            echo '<li>Database "decornest" đã tạo chưa?</li>';
            echo '</ul>';
        }
        ?>
        
        <hr style="margin: 30px 0;">
        
        <h2>🔗 Quick Links</h2>
        <a href="/project-ecommerce/public" class="btn">🏠 Trang Chủ</a>
        <a href="/project-ecommerce/public/user/login" class="btn">🔐 Đăng Nhập</a>
        <a href="/project-ecommerce/public/test-admin-crud.php" class="btn">🧪 Test CRUD</a>
    </div>
</body>
</html>
