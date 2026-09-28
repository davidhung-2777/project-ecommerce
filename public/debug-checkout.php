<?php
/**
 * Debug Checkout Issues
 */

// Enable error reporting
error_reporting(E_ALL);
ini_set('display_errors', 1);

require_once __DIR__ . '/../vendor/autoload.php';

$dotenv = new Dotenv(__DIR__ . '/..');
$dotenv->load();

echo "<h1>🔍 Debug Checkout Process</h1>";
echo "<hr>";

// Test 1: Database connection
echo "<h2>1️⃣ Kiểm tra Database</h2>";
try {
    $db = \App\Core\Database::getInstance();
    echo "✅ Database connected<br>";
    
    // Check tables
    $tables = ['orders', 'order_details', 'vouchers', 'voucher_usages'];
    foreach ($tables as $table) {
        $result = $db->query("SHOW TABLES LIKE '{$table}'");
        if ($result->rowCount() > 0) {
            echo "✅ Table <strong>{$table}</strong> exists<br>";
        } else {
            echo "❌ Table <strong>{$table}</strong> NOT exists<br>";
        }
    }
    
    // Check columns in orders table
    echo "<br><strong>Cột trong bảng 'orders':</strong><br>";
    $columns = $db->query("DESCRIBE orders")->fetchAll(PDO::FETCH_COLUMN);
    $requiredColumns = ['voucher_id', 'discount_amount', 'shipping_code', 'shipping_province_id'];
    
    foreach ($requiredColumns as $col) {
        if (in_array($col, $columns)) {
            echo "✅ Column <strong>{$col}</strong> exists<br>";
        } else {
            echo "❌ Column <strong>{$col}</strong> MISSING<br>";
        }
    }
    
} catch (Exception $e) {
    echo "❌ Database error: " . htmlspecialchars($e->getMessage()) . "<br>";
}

echo "<hr>";

// Test 2: Check required classes
echo "<h2>2️⃣ Kiểm tra Classes</h2>";

$classes = [
    'App\Services\VoucherService',
    'App\Services\GhnShippingService',
    'App\Services\Payment\BankTransferPayment',
    'App\Controllers\CheckoutController',
    'App\Models\VoucherModel',
];

foreach ($classes as $class) {
    if (class_exists($class)) {
        echo "✅ <strong>{$class}</strong> exists<br>";
    } else {
        echo "❌ <strong>{$class}</strong> NOT found<br>";
    }
}

echo "<hr>";

// Test 3: Check .env variables
echo "<h2>3️⃣ Kiểm tra .env Variables</h2>";

$envVars = [
    'DB_DATABASE',
    'GHN_API_TOKEN',
    'GHN_SHOP_ID',
    'BANK_ACCOUNT_NAME',
    'BANK_ACCOUNT_NUMBER',
    'BANK_NAME',
];

$allSet = true;
foreach ($envVars as $var) {
    $value = $_ENV[$var] ?? '';
    if (!empty($value) && $value !== 'your_api_key_here') {
        echo "✅ <strong>{$var}</strong>: " . htmlspecialchars(substr($value, 0, 30)) . "...<br>";
    } else {
        echo "❌ <strong>{$var}</strong>: NOT SET<br>";
        $allSet = false;
    }
}

echo "<hr>";

// Test 4: Simulate checkout process
echo "<h2>4️⃣ Simulate Checkout Process</h2>";

try {
    session_start();
    $_SESSION['user_id'] = 1; // Test user
    
    // Test cart
    $cartModel = new \App\Models\CartModel();
    $cart = $cartModel->getOrCreateForUser(1);
    echo "✅ Cart ID: {$cart['id']}<br>";
    
    $cartData = $cartModel->getCartWithItems($cart['id']);
    echo "✅ Cart items: " . count($cartData['items'] ?? []) . "<br>";
    
    if (empty($cartData['items'])) {
        echo "⚠️ <strong>Cart is empty!</strong> Add products first.<br>";
    } else {
        echo "✅ Cart subtotal: " . number_format($cartData['subtotal']) . "đ<br>";
    }
    
    // Test VoucherService
    echo "<br><strong>Test VoucherService:</strong><br>";
    $voucherService = new \App\Services\VoucherService();
    echo "✅ VoucherService initialized<br>";
    
    // Test GhnShippingService
    echo "<br><strong>Test GhnShippingService:</strong><br>";
    $ghnService = new \App\Services\GhnShippingService();
    echo "✅ GhnShippingService initialized<br>";
    
    // Test BankTransferPayment
    echo "<br><strong>Test BankTransferPayment:</strong><br>";
    $bankTransfer = new \App\Services\Payment\BankTransferPayment();
    $testOrder = [
        'id' => 1,
        'order_number' => 'TEST123',
        'total_amount' => 100000
    ];
    $bankInfo = $bankTransfer->createTransaction($testOrder);
    
    if ($bankInfo['success']) {
        echo "✅ Bank transfer QR generated<br>";
        echo "✅ QR URL: " . htmlspecialchars(substr($bankInfo['qr_url'], 0, 50)) . "...<br>";
    } else {
        echo "❌ Failed to generate QR<br>";
    }
    
} catch (Exception $e) {
    echo "❌ Error: " . htmlspecialchars($e->getMessage()) . "<br>";
    echo "<pre>" . htmlspecialchars($e->getTraceAsString()) . "</pre>";
}

echo "<hr>";

// Test 5: Check routes
echo "<h2>5️⃣ Kiểm tra Routes</h2>";

try {
    $router = new \App\Core\Router();
    echo "✅ Router initialized<br>";
    echo "<small>Routes are loaded from App.php</small><br>";
} catch (Exception $e) {
    echo "❌ Router error: " . htmlspecialchars($e->getMessage()) . "<br>";
}

echo "<hr>";

// Summary
echo "<h2>📋 Tóm tắt</h2>";

if ($allSet && !empty($cartData['items'] ?? [])) {
    echo "<div style='background: #d4edda; padding: 20px; border-left: 4px solid #28a745;'>";
    echo "<strong>✅ HỆ THỐNG SẴN SÀNG!</strong><br>";
    echo "Bạn có thể thử checkout ngay.";
    echo "</div>";
} else {
    echo "<div style='background: #fff3cd; padding: 20px; border-left: 4px solid #ffc107;'>";
    echo "<strong>⚠️ CẦN KHẮC PHỤC:</strong><br>";
    if (!$allSet) {
        echo "- Cấu hình .env chưa đầy đủ<br>";
    }
    if (empty($cartData['items'] ?? [])) {
        echo "- Giỏ hàng trống (thêm sản phẩm trước)<br>";
    }
    echo "</div>";
}

echo "<hr>";
echo "<p><small>Debug completed at " . date('Y-m-d H:i:s') . "</small></p>";

?>

<style>
body {
    font-family: system-ui, -apple-system, sans-serif;
    max-width: 900px;
    margin: 40px auto;
    padding: 20px;
    background: #f5f5f5;
}
h1, h2 { color: #333; }
pre {
    background: #f8f8f8;
    padding: 15px;
    border: 1px solid #ddd;
    border-radius: 4px;
    overflow-x: auto;
    font-size: 12px;
}
</style>
