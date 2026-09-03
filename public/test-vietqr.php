<?php
/**
 * Test Script - VietQR Features
 * Kiểm tra xem các tính năng mới đã hoạt động chưa
 * 
 * Truy cập: http://localhost/project-ecommerce/public/test-vietqr.php
 */

error_reporting(E_ALL);
ini_set('display_errors', 1);
header('Content-Type: text/html; charset=UTF-8');

require_once __DIR__ . '/../vendor/autoload.php';
require_once __DIR__ . '/../app/core/Database.php';

// Load .env
$dotenv = Dotenv\Dotenv::createImmutable(dirname(__DIR__));
$dotenv->load();

?>
<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <title>Test VietQR Features</title>
    <style>
        * { margin: 0; padding: 0; box-sizing: border-box; }
        body { font-family: Arial, sans-serif; padding: 20px; background: #f5f5f5; }
        .container { max-width: 900px; margin: 0 auto; background: white; padding: 30px; border-radius: 12px; box-shadow: 0 2px 10px rgba(0,0,0,0.1); }
        h1 { color: #333; margin-bottom: 10px; }
        .subtitle { color: #666; margin-bottom: 30px; font-size: 14px; }
        .test-section { background: #f9f9f9; padding: 20px; border-radius: 8px; margin: 20px 0; border-left: 4px solid #007bff; }
        .test-section h2 { color: #007bff; margin-bottom: 15px; font-size: 18px; }
        .test-item { background: white; padding: 15px; margin: 10px 0; border-radius: 6px; }
        .status { padding: 4px 12px; border-radius: 4px; font-weight: bold; font-size: 12px; }
        .status.pass { background: #d4edda; color: #155724; }
        .status.fail { background: #f8d7da; color: #721c24; }
        .status.warn { background: #fff3cd; color: #856404; }
        .detail { margin-top: 10px; padding: 10px; background: #f8f9fa; border-radius: 4px; font-size: 13px; color: #666; }
        code { background: #e9ecef; padding: 2px 6px; border-radius: 3px; font-family: 'Courier New', monospace; }
        .summary { background: #007bff; color: white; padding: 20px; border-radius: 8px; margin-top: 30px; }
        .summary h3 { margin-bottom: 10px; }
        .btn { display: inline-block; padding: 10px 20px; background: #28a745; color: white; text-decoration: none; border-radius: 6px; margin-top: 15px; }
        .btn:hover { background: #218838; }
    </style>
</head>
<body>
    <div class="container">
        <h1>🧪 Test VietQR Features</h1>
        <p class="subtitle">Kiểm tra các tính năng VietQR + Webhook đã được tích hợp</p>

        <?php
        $results = [
            'pass' => 0,
            'fail' => 0,
            'warn' => 0,
            'tests' => []
        ];
        
        function testResult($name, $passed, $message = '', $detail = '') {
            global $results;
            $status = $passed ? 'pass' : ($message === 'warn' ? 'warn' : 'fail');
            $results[$status === 'warn' ? 'warn' : ($passed ? 'pass' : 'fail')]++;
            $results['tests'][] = [
                'name' => $name,
                'status' => $status,
                'message' => $message,
                'detail' => $detail
            ];
        }
        
        // ========== TEST 1: DATABASE STRUCTURE ==========
        echo "<div class='test-section'><h2>1️⃣ Database Structure</h2>";
        
        try {
            $db = \App\Core\Database::getInstance();
            
            // Test payment_webhook_logs table
            $tables = $db->fetchAll("SHOW TABLES LIKE 'payment_webhook_logs'");
            if (!empty($tables)) {
                testResult('Table payment_webhook_logs', true, 'Tồn tại ✅');
                
                // Check columns
                $columns = $db->fetchAll("SHOW COLUMNS FROM payment_webhook_logs");
                $columnNames = array_column($columns, 'Field');
                $requiredCols = ['id', 'order_id', 'order_code', 'raw_payload', 'signature_valid', 'processed'];
                $missing = array_diff($requiredCols, $columnNames);
                
                if (empty($missing)) {
                    testResult('Columns payment_webhook_logs', true, 'Đầy đủ ✅');
                } else {
                    testResult('Columns payment_webhook_logs', false, 'Thiếu: ' . implode(', ', $missing));
                }
            } else {
                testResult('Table payment_webhook_logs', false, 'Chưa tồn tại - Chạy migration!');
            }
            
            // Test orders columns
            $orderCols = $db->fetchAll("SHOW COLUMNS FROM orders");
            $orderColNames = array_column($orderCols, 'Field');
            $requiredOrderCols = ['order_code', 'qr_image_url', 'expires_at', 'paid_at'];
            $missingOrderCols = array_diff($requiredOrderCols, $orderColNames);
            
            if (empty($missingOrderCols)) {
                testResult('Orders table columns', true, 'Đầy đủ ✅');
            } else {
                testResult('Orders table columns', false, 'Thiếu: ' . implode(', ', $missingOrderCols));
            }
            
            // Test cart_items CHECK constraint
            $constraints = $db->fetchAll("SHOW CREATE TABLE cart_items");
            $createSql = $constraints[0]['Create Table'] ?? '';
            if (strpos($createSql, 'CHECK') !== false && strpos($createSql, 'quantity') !== false) {
                testResult('Cart quantity constraint', true, 'CHECK (1-100) tồn tại ✅');
            } else {
                testResult('Cart quantity constraint', false, 'Chưa có CHECK constraint', 'warn');
            }
            
        } catch (Exception $e) {
            testResult('Database connection', false, 'Lỗi: ' . $e->getMessage());
        }
        
        // Display test 1 results
        foreach ($results['tests'] as $test) {
            echo "<div class='test-item'>";
            echo "<strong>{$test['name']}</strong> ";
            echo "<span class='status {$test['status']}'>" . strtoupper($test['status']) . "</span>";
            if ($test['message']) echo "<div class='detail'>{$test['message']}</div>";
            echo "</div>";
        }
        $results['tests'] = []; // Reset for next section
        
        echo "</div>";
        
        // ========== TEST 2: FILES EXIST ==========
        echo "<div class='test-section'><h2>2️⃣ Required Files</h2>";
        
        $requiredFiles = [
            'VietQRPayment Service' => '../app/services/payment/VietQRPayment.php',
            'WebhookController' => '../app/controllers/WebhookController.php',
            'Order Status API' => 'api/order-status.php',
            'Checkout Polling JS' => 'assets/js/checkout-polling.js',
            'Expire Orders Cron' => '../cron/expire-orders.php',
            'Migration SQL' => '../config/migrations/add_webhook_and_qr_features.sql'
        ];
        
        foreach ($requiredFiles as $name => $path) {
            $fullPath = __DIR__ . '/' . $path;
            if (file_exists($fullPath)) {
                $size = filesize($fullPath);
                testResult($name, true, "Tồn tại ✅ (" . number_format($size) . " bytes)");
            } else {
                testResult($name, false, "Không tìm thấy file: {$path}");
            }
        }
        
        foreach ($results['tests'] as $test) {
            echo "<div class='test-item'>";
            echo "<strong>{$test['name']}</strong> ";
            echo "<span class='status {$test['status']}'>" . strtoupper($test['status']) . "</span>";
            if ($test['message']) echo "<div class='detail'>{$test['message']}</div>";
            echo "</div>";
        }
        $results['tests'] = [];
        
        echo "</div>";
        
        // ========== TEST 3: CONFIGURATION ==========
        echo "<div class='test-section'><h2>3️⃣ Configuration</h2>";
        
        $envVars = [
            'VIETQR_PROVIDER' => $_ENV['VIETQR_PROVIDER'] ?? null,
            'VIETQR_API_KEY' => $_ENV['VIETQR_API_KEY'] ?? null,
            'VIETQR_WEBHOOK_SECRET' => $_ENV['VIETQR_WEBHOOK_SECRET'] ?? null,
            'BANK_CODE' => $_ENV['BANK_CODE'] ?? null,
            'BANK_ACCOUNT_NUMBER' => $_ENV['BANK_ACCOUNT_NUMBER'] ?? null,
            'BANK_ACCOUNT_NAME' => $_ENV['BANK_ACCOUNT_NAME'] ?? null,
        ];
        
        foreach ($envVars as $key => $value) {
            if (!empty($value)) {
                $masked = strlen($value) > 10 ? substr($value, 0, 10) . '...' : $value;
                testResult($key, true, "Đã cấu hình: <code>{$masked}</code>");
            } else {
                $isRequired = in_array($key, ['VIETQR_PROVIDER', 'BANK_CODE', 'BANK_ACCOUNT_NUMBER']);
                if ($isRequired) {
                    testResult($key, false, "Chưa cấu hình (bắt buộc)");
                } else {
                    testResult($key, false, "Chưa cấu hình", 'warn');
                }
            }
        }
        
        foreach ($results['tests'] as $test) {
            echo "<div class='test-item'>";
            echo "<strong>{$test['name']}</strong> ";
            echo "<span class='status {$test['status']}'>" . strtoupper($test['status']) . "</span>";
            if ($test['message']) echo "<div class='detail'>{$test['message']}</div>";
            echo "</div>";
        }
        $results['tests'] = [];
        
        echo "</div>";
        
        // ========== TEST 4: API ENDPOINTS ==========
        echo "<div class='test-section'><h2>4️⃣ API Endpoints</h2>";
        
        // Test order status API
        $orderStatusUrl = 'http://localhost/project-ecommerce/public/api/order-status.php?order_code=TEST123';
        $ch = curl_init($orderStatusUrl);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_TIMEOUT, 5);
        $response = curl_exec($ch);
        $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
        curl_close($ch);
        
        if ($httpCode === 404) {
            testResult('Order Status API', true, "Endpoint hoạt động (404 = order không tồn tại) ✅");
        } elseif ($httpCode === 200 || $httpCode === 400) {
            testResult('Order Status API', true, "Endpoint hoạt động ✅");
        } else {
            testResult('Order Status API', false, "HTTP {$httpCode} - Có thể lỗi");
        }
        
        foreach ($results['tests'] as $test) {
            echo "<div class='test-item'>";
            echo "<strong>{$test['name']}</strong> ";
            echo "<span class='status {$test['status']}'>" . strtoupper($test['status']) . "</span>";
            if ($test['message']) echo "<div class='detail'>{$test['message']}</div>";
            echo "</div>";
        }
        
        echo "</div>";
        
        // ========== SUMMARY ==========
        $totalTests = $results['pass'] + $results['fail'] + $results['warn'];
        $passRate = $totalTests > 0 ? round(($results['pass'] / $totalTests) * 100, 1) : 0;
        
        echo "<div class='summary'>";
        echo "<h3>📊 Tổng Kết</h3>";
        echo "<p><strong>Tổng số tests:</strong> {$totalTests}</p>";
        echo "<p><strong>✅ Passed:</strong> {$results['pass']}</p>";
        echo "<p><strong>❌ Failed:</strong> {$results['fail']}</p>";
        echo "<p><strong>⚠️ Warnings:</strong> {$results['warn']}</p>";
        echo "<p><strong>Pass rate:</strong> {$passRate}%</p>";
        
        if ($results['fail'] === 0 && $results['warn'] <= 2) {
            echo "<p style='margin-top: 15px; font-size: 18px;'>🎉 <strong>Tuyệt vời!</strong> Hệ thống sẵn sàng sử dụng!</p>";
            echo "<a href='/project-ecommerce/public' class='btn'>🏠 Về Trang Chủ</a>";
            echo "<a href='/project-ecommerce/public/products' class='btn' style='background:#007bff;'>🛍️ Xem Sản Phẩm</a>";
        } elseif ($results['fail'] > 0) {
            echo "<p style='margin-top: 15px; color: #ffeb3b;'>⚠️ Cần khắc phục một số vấn đề trước khi sử dụng.</p>";
            echo "<a href='../README_VIETQR_WEBHOOK.md' class='btn'>📚 Xem Hướng Dẫn</a>";
        }
        
        echo "</div>";
        ?>
        
    </div>
</body>
</html>
