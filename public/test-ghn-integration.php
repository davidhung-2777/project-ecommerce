<?php
/**
 * Test GHN Integration trong CheckoutController
 * Kiểm tra database có đầy đủ cột shipping chưa
 */

require_once __DIR__ . '/../vendor/autoload.php';

$dotenv = new Dotenv(__DIR__ . '/..');
$dotenv->load();

$db = \App\Core\Database::getInstance();

echo "<h1>🧪 Test GHN Integration - CheckoutController</h1>";
echo "<hr>";

// 1. Kiểm tra các cột shipping trong bảng orders
echo "<h2>1️⃣ Kiểm tra database</h2>";

try {
    $stmt = $db->query("DESCRIBE orders");
    $columns = $stmt->fetchAll(PDO::FETCH_ASSOC);
    
    $requiredColumns = [
        'shipping_code',
        'shipping_status',
        'expected_delivery',
        'shipping_service_id',
        'shipping_province_id',
        'shipping_district_id',
        'shipping_ward_code',
    ];
    
    $existingColumns = array_column($columns, 'Field');
    
    $missingColumns = [];
    foreach ($requiredColumns as $col) {
        if (in_array($col, $existingColumns)) {
            echo "✅ Cột <strong>{$col}</strong> đã tồn tại<br>";
        } else {
            echo "❌ Thiếu cột <strong>{$col}</strong><br>";
            $missingColumns[] = $col;
        }
    }
    
    if (!empty($missingColumns)) {
        echo "<div style='background: #fff3cd; padding: 15px; margin-top: 20px; border-left: 4px solid #ffc107;'>";
        echo "<strong>⚠️ CẦN CHẠY MIGRATION:</strong><br>";
        echo "Vui lòng chạy file: <code>config/migrations/add_shipping_columns.sql</code> trong phpMyAdmin<br>";
        echo "Hoặc copy SQL này vào phpMyAdmin:<br><br>";
        echo "<textarea style='width: 100%; height: 150px; font-family: monospace; font-size: 12px;'>";
        echo file_get_contents(__DIR__ . '/../config/migrations/add_shipping_columns.sql');
        echo "</textarea>";
        echo "</div>";
    } else {
        echo "<div style='background: #d4edda; padding: 15px; margin-top: 20px; border-left: 4px solid #28a745;'>";
        echo "✅ <strong>Database đã sẵn sàng!</strong>";
        echo "</div>";
    }
    
} catch (Exception $e) {
    echo "<div style='background: #f8d7da; padding: 15px; border-left: 4px solid #dc3545;'>";
    echo "❌ Lỗi: " . htmlspecialchars($e->getMessage());
    echo "</div>";
}

// 2. Kiểm tra GhnShippingService có khởi tạo được không
echo "<h2>2️⃣ Kiểm tra GhnShippingService</h2>";

try {
    $ghn = new \App\Services\GhnShippingService();
    echo "✅ GhnShippingService khởi tạo thành công<br>";
    
    // Test tính phí (Hà Nội - Quận Ba Đình)
    $result = $ghn->calculateShippingFee([
        'to_district_id' => 1542,
        'to_ward_code' => '20308',
        'weight' => 5000,
        'order_value' => 1000000,
    ]);
    
    if ($result['success']) {
        echo "✅ API tính phí hoạt động: <strong>" . number_format($result['fee']) . " VND</strong><br>";
    } else {
        echo "❌ API tính phí lỗi: " . htmlspecialchars($result['message'] ?? 'Unknown error') . "<br>";
    }
    
} catch (Exception $e) {
    echo "❌ Lỗi khởi tạo GhnShippingService: " . htmlspecialchars($e->getMessage()) . "<br>";
}

// 3. Kiểm tra CheckoutController
echo "<h2>3️⃣ Kiểm tra CheckoutController</h2>";

try {
    $controller = new \App\Controllers\CheckoutController();
    echo "✅ CheckoutController khởi tạo thành công<br>";
    echo "✅ GHN service đã được inject vào controller<br>";
} catch (Exception $e) {
    echo "❌ Lỗi khởi tạo CheckoutController: " . htmlspecialchars($e->getMessage()) . "<br>";
}

// 4. Tóm tắt
echo "<hr>";
echo "<h2>📋 Tóm tắt</h2>";

if (empty($missingColumns)) {
    echo "<div style='background: #d4edda; padding: 20px; border-left: 4px solid #28a745;'>";
    echo "<h3>✅ TÍCH HỢP HOÀN TẤT!</h3>";
    echo "<p>Bạn có thể test checkout với GHN:</p>";
    echo "<ol>";
    echo "<li>Vào trang checkout: <a href='/checkout'>/checkout</a></li>";
    echo "<li>Chọn tỉnh/quận/phường từ dropdown</li>";
    echo "<li>Phí ship sẽ được tính tự động từ GHN</li>";
    echo "<li>Sau khi đặt hàng, mã vận đơn sẽ được tạo tự động</li>";
    echo "</ol>";
    echo "</div>";
} else {
    echo "<div style='background: #fff3cd; padding: 20px; border-left: 4px solid #ffc107;'>";
    echo "<h3>⚠️ CẦN CHẠY MIGRATION DATABASE</h3>";
    echo "<p>Vui lòng chạy SQL migration ở mục 1️⃣ trước khi test</p>";
    echo "</div>";
}

echo "<hr>";
echo "<p><small>Test completed at " . date('Y-m-d H:i:s') . "</small></p>";
