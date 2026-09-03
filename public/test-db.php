<?php
// Quick database connection test
header('Content-Type: text/html; charset=UTF-8');

try {
    $pdo = new PDO('mysql:host=localhost;dbname=decornest;charset=utf8mb4', 'root', '', [
        PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
        PDO::MYSQL_ATTR_INIT_COMMAND => "SET NAMES utf8mb4 COLLATE utf8mb4_unicode_ci"
    ]);
    
    echo "<h2>✅ Kết nối database thành công!</h2>";
    
    // Test products
    $products = $pdo->query("SELECT id, name, price FROM products LIMIT 5")->fetchAll();
    
    echo "<h3>📦 Sản phẩm trong database:</h3><ul>";
    foreach ($products as $p) {
        echo "<li>ID: {$p['id']} - {$p['name']} - " . number_format($p['price']) . "đ</li>";
    }
    echo "</ul>";
    
    if (count($products) == 0) {
        echo "<p style='color:red;'>⚠️ Chưa có sản phẩm! Hãy chạy setup-complete.php</p>";
    }
    
} catch (PDOException $e) {
    echo "<h2>❌ Lỗi: " . $e->getMessage() . "</h2>";
}
?>
