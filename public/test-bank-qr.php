<?php
/**
 * Test Bank Transfer QR Code Generation
 */

require_once __DIR__ . '/../vendor/autoload.php';

$dotenv = new Dotenv(__DIR__ . '/..');
$dotenv->load();

// Test order data
$testOrder = [
    'id' => 1,
    'order_number' => 'DN20260924TEST',
    'total_amount' => 150000,
    'payment_method' => 'bank_transfer',
];

echo "<h1>🧪 Test Bank Transfer QR Code</h1>";
echo "<hr>";

// Test 1: Check .env variables
echo "<h2>1️⃣ Kiểm tra biến .env</h2>";

$vars = [
    'BANK_ACCOUNT_NAME',
    'BANK_ACCOUNT_NUMBER',
    'BANK_NAME',
    'BANK_CODE',
    'VIETQR_ACCOUNT_NAME',
    'VIETQR_ACCOUNT_NUMBER',
    'VIETQR_BANK_CODE',
];

$allSet = true;
foreach ($vars as $var) {
    $value = $_ENV[$var] ?? '';
    $status = !empty($value) && $value !== 'your_api_key_here' && $value !== 'YOUR_ACCOUNT_NAME' 
        ? '✅' : '❌';
    
    if ($status === '❌') $allSet = false;
    
    echo "{$status} <strong>{$var}</strong>: ";
    
    if (!empty($value) && $value !== 'your_api_key_here') {
        echo htmlspecialchars($value);
    } else {
        echo "<span style='color: red;'>CHƯA CÓ</span>";
    }
    echo "<br>";
}

echo "<hr>";

// Test 2: Load payment config
echo "<h2>2️⃣ Kiểm tra config payment.php</h2>";

try {
    $config = require __DIR__ . '/../config/payment.php';
    $bankConfig = $config['bank_transfer'];
    
    echo "✅ Load config thành công<br>";
    echo "<pre>";
    print_r($bankConfig);
    echo "</pre>";
} catch (Exception $e) {
    echo "❌ Lỗi load config: " . htmlspecialchars($e->getMessage());
}

echo "<hr>";

// Test 3: Generate QR Code
echo "<h2>3️⃣ Tạo QR Code</h2>";

try {
    $bankTransfer = new \App\Services\Payment\BankTransferPayment();
    $bankInfo = $bankTransfer->createTransaction($testOrder);
    
    if ($bankInfo['success']) {
        echo "✅ Tạo QR thành công<br><br>";
        
        echo "<div style='border: 1px solid #ddd; padding: 20px; border-radius: 8px; background: #f9f9f9;'>";
        echo "<h3>Thông tin chuyển khoản:</h3>";
        echo "<p><strong>Ngân hàng:</strong> " . htmlspecialchars($bankInfo['bank_name']) . "</p>";
        echo "<p><strong>Số TK:</strong> " . htmlspecialchars($bankInfo['account_number']) . "</p>";
        echo "<p><strong>Chủ TK:</strong> " . htmlspecialchars($bankInfo['account_name']) . "</p>";
        echo "<p><strong>Số tiền:</strong> " . number_format($bankInfo['amount']) . "đ</p>";
        echo "<p><strong>Nội dung:</strong> <code>" . htmlspecialchars($bankInfo['transfer_content']) . "</code></p>";
        echo "</div>";
        
        echo "<br>";
        echo "<div style='text-align: center; padding: 20px; background: white; border: 2px solid #ccc; border-radius: 8px;'>";
        echo "<h3>QR Code VietQR:</h3>";
        echo "<img src='" . htmlspecialchars($bankInfo['qr_url']) . "' alt='QR Code' style='width: 300px; height: 300px; border: 4px solid white; box-shadow: 0 2px 8px rgba(0,0,0,0.1);'>";
        echo "<br><br>";
        echo "<p style='font-size: 12px; color: #666;'>Mở app ngân hàng để quét mã QR này</p>";
        echo "</div>";
        
        echo "<br>";
        echo "<div style='background: #e8f5e9; padding: 15px; border-left: 4px solid #4caf50; border-radius: 4px;'>";
        echo "<strong>✅ QR Code URL:</strong><br>";
        echo "<code style='word-break: break-all;'>" . htmlspecialchars($bankInfo['qr_url']) . "</code>";
        echo "</div>";
        
    } else {
        echo "❌ Tạo QR thất bại<br>";
        echo "<pre>";
        print_r($bankInfo);
        echo "</pre>";
    }
    
} catch (Exception $e) {
    echo "❌ Lỗi: " . htmlspecialchars($e->getMessage()) . "<br>";
    echo "<pre>" . htmlspecialchars($e->getTraceAsString()) . "</pre>";
}

echo "<hr>";
echo "<h2>📝 Kết luận:</h2>";

if ($allSet) {
    echo "<div style='background: #d4edda; padding: 20px; border-left: 4px solid #28a745; border-radius: 4px;'>";
    echo "<strong>✅ CẤU HÌNH HOÀN TẤT!</strong><br>";
    echo "Bạn có thể test checkout với phương thức Bank Transfer.<br>";
    echo "QR Code sẽ hiển thị tự động sau khi đặt hàng.";
    echo "</div>";
} else {
    echo "<div style='background: #fff3cd; padding: 20px; border-left: 4px solid #ffc107; border-radius: 4px;'>";
    echo "<strong>⚠️ CẦN CẤU HÌNH:</strong><br>";
    echo "Vui lòng cập nhật các biến môi trường trong file <code>.env</code><br>";
    echo "Đặc biệt là: BANK_ACCOUNT_NAME, BANK_ACCOUNT_NUMBER, BANK_NAME";
    echo "</div>";
}

?>

<style>
body {
    font-family: system-ui, -apple-system, sans-serif;
    max-width: 900px;
    margin: 40px auto;
    padding: 20px;
    background: #f5f5f5;
}
h1, h2, h3 {
    color: #333;
}
code {
    background: #f0f0f0;
    padding: 2px 6px;
    border-radius: 3px;
    font-family: 'Courier New', monospace;
}
pre {
    background: #f8f8f8;
    padding: 15px;
    border: 1px solid #ddd;
    border-radius: 4px;
    overflow-x: auto;
}
</style>
