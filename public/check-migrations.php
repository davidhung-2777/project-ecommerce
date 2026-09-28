<?php
/**
 * Kiểm tra xem đã chạy migrations chưa
 */

// Load .env manually
$envFile = __DIR__ . '/../.env';
if (file_exists($envFile)) {
    $lines = file($envFile, FILE_IGNORE_NEW_LINES | FILE_SKIP_EMPTY_LINES);
    foreach ($lines as $line) {
        if (strpos(trim($line), '#') === 0) continue;
        list($name, $value) = explode('=', $line, 2);
        $_ENV[trim($name)] = trim($value);
    }
}

?>
<!DOCTYPE html>
<html>
<head>
    <title>Kiểm tra Database Migrations</title>
    <style>
        body { font-family: Arial, sans-serif; max-width: 900px; margin: 40px auto; padding: 20px; background: #f5f5f5; }
        h1 { color: #333; }
        .success { background: #d4edda; padding: 15px; border-left: 4px solid #28a745; margin: 10px 0; }
        .error { background: #f8d7da; padding: 15px; border-left: 4px solid #dc3545; margin: 10px 0; }
        .warning { background: #fff3cd; padding: 15px; border-left: 4px solid #ffc107; margin: 10px 0; }
        table { width: 100%; border-collapse: collapse; background: white; margin: 20px 0; }
        th, td { padding: 12px; text-align: left; border-bottom: 1px solid #ddd; }
        th { background: #f8f9fa; font-weight: bold; }
        .badge { display: inline-block; padding: 4px 8px; border-radius: 4px; font-size: 12px; font-weight: bold; }
        .badge-success { background: #28a745; color: white; }
        .badge-danger { background: #dc3545; color: white; }
        pre { background: #f8f8f8; padding: 15px; border: 1px solid #ddd; overflow-x: auto; }
    </style>
</head>
<body>

<h1>🔍 Kiểm tra Database Migrations</h1>

<?php
try {
    // Database connection
    $host = $_ENV['DB_HOST'] ?? 'localhost';
    $dbname = $_ENV['DB_DATABASE'] ?? 'decornest';
    $username = $_ENV['DB_USERNAME'] ?? 'root';
    $password = $_ENV['DB_PASSWORD'] ?? '';
    
    $db = new PDO("mysql:host={$host};dbname={$dbname}", $username, $password);
    $db->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    
    echo "<div class='success'>✅ Kết nối database thành công</div>";
    
    // Kiểm tra bảng orders
    echo "<h2>Bảng: orders</h2>";
    $stmt = $db->query("DESCRIBE orders");
    $columns = $stmt->fetchAll(PDO::FETCH_ASSOC);
    
    $requiredColumns = [
        // GHN Shipping columns
        'shipping_code' => 'GHN Shipping',
        'shipping_status' => 'GHN Shipping',
        'shipping_province_id' => 'GHN Shipping',
        'shipping_district_id' => 'GHN Shipping',
        'shipping_ward_code' => 'GHN Shipping',
        'expected_delivery' => 'GHN Shipping',
        
        // Voucher columns
        'voucher_id' => 'Voucher System',
        'discount_amount' => 'Voucher System',
    ];
    
    $existingColumns = array_column($columns, 'Field');
    
    $missing = [];
    
    echo "<table>";
    echo "<tr><th>Cột</th><th>Tính năng</th><th>Trạng thái</th></tr>";
    
    foreach ($requiredColumns as $col => $feature) {
        $exists = in_array($col, $existingColumns);
        $badge = $exists ? "<span class='badge badge-success'>✓ Có</span>" : "<span class='badge badge-danger'>✗ Thiếu</span>";
        
        echo "<tr>";
        echo "<td><code>{$col}</code></td>";
        echo "<td>{$feature}</td>";
        echo "<td>{$badge}</td>";
        echo "</tr>";
        
        if (!$exists) {
            $missing[] = $col;
        }
    }
    
    echo "</table>";
    
    // Kiểm tra bảng vouchers
    echo "<h2>Bảng: vouchers</h2>";
    $voucherTableExists = $db->query("SHOW TABLES LIKE 'vouchers'")->rowCount() > 0;
    
    if ($voucherTableExists) {
        echo "<div class='success'>✅ Bảng <strong>vouchers</strong> đã tồn tại</div>";
        
        // Kiểm tra các bảng liên quan
        $relatedTables = ['voucher_products', 'voucher_categories', 'voucher_usages'];
        foreach ($relatedTables as $table) {
            $exists = $db->query("SHOW TABLES LIKE '{$table}'")->rowCount() > 0;
            $status = $exists ? "✅" : "❌";
            echo "<p>{$status} Bảng <strong>{$table}</strong></p>";
        }
    } else {
        echo "<div class='error'>❌ Bảng <strong>vouchers</strong> CHƯA tồn tại</div>";
    }
    
    // Tóm tắt
    echo "<hr>";
    echo "<h2>📋 Tóm tắt</h2>";
    
    if (empty($missing) && $voucherTableExists) {
        echo "<div class='success'>";
        echo "<h3>✅ DATABASE SẴN SÀNG!</h3>";
        echo "<p>Tất cả migrations đã được chạy đầy đủ.</p>";
        echo "<p>Bạn có thể sử dụng đầy đủ các tính năng:</p>";
        echo "<ul>";
        echo "<li>✓ GHN Shipping</li>";
        echo "<li>✓ Voucher System</li>";
        echo "<li>✓ VietQR Payment</li>";
        echo "</ul>";
        echo "</div>";
    } else {
        echo "<div class='error'>";
        echo "<h3>❌ CẦN CHẠY MIGRATIONS!</h3>";
        
        if (!empty($missing)) {
            echo "<p><strong>Thiếu các cột sau trong bảng orders:</strong></p>";
            echo "<ul>";
            foreach ($missing as $col) {
                echo "<li><code>{$col}</code> ({$requiredColumns[$col]})</li>";
            }
            echo "</ul>";
            
            echo "<p><strong>Cách fix:</strong></p>";
            echo "<ol>";
            echo "<li>Mở phpMyAdmin</li>";
            echo "<li>Chọn database <code>decornest</code></li>";
            echo "<li>Vào tab SQL</li>";
            echo "<li>Chạy file: <code>config/migrations/add_shipping_columns.sql</code></li>";
            if (!$voucherTableExists) {
                echo "<li>Chạy tiếp file: <code>config/migrations/add_voucher_features.sql</code></li>";
            }
            echo "</ol>";
        }
        
        if (!$voucherTableExists) {
            echo "<p><strong>Thiếu bảng vouchers và các bảng liên quan</strong></p>";
            echo "<p><strong>Cách fix:</strong> Chạy file <code>config/migrations/add_voucher_features.sql</code></p>";
        }
        echo "</div>";
        
        // Hiển thị SQL cần chạy
        if (!empty($missing)) {
            echo "<h3>📝 SQL Migration - GHN Shipping</h3>";
            echo "<p>Copy SQL này vào phpMyAdmin:</p>";
            echo "<pre>";
            echo htmlspecialchars(file_get_contents(__DIR__ . '/../config/migrations/add_shipping_columns.sql'));
            echo "</pre>";
        }
        
        if (!$voucherTableExists) {
            echo "<h3>📝 SQL Migration - Voucher System</h3>";
            echo "<p>Copy SQL này vào phpMyAdmin:</p>";
            echo "<pre>";
            echo htmlspecialchars(file_get_contents(__DIR__ . '/../config/migrations/add_voucher_features.sql'));
            echo "</pre>";
        }
    }
    
} catch (Exception $e) {
    echo "<div class='error'>";
    echo "<h3>❌ Lỗi kết nối database</h3>";
    echo "<p>" . htmlspecialchars($e->getMessage()) . "</p>";
    echo "<p><strong>Kiểm tra:</strong></p>";
    echo "<ul>";
    echo "<li>XAMPP/MySQL đã chạy chưa?</li>";
    echo "<li>Database <code>decornest</code> đã tạo chưa?</li>";
    echo "<li>File <code>.env</code> có đúng thông tin không?</li>";
    echo "</ul>";
    echo "</div>";
}
?>

<hr>
<p><small>Kiểm tra lúc: <?= date('Y-m-d H:i:s') ?></small></p>

</body>
</html>
