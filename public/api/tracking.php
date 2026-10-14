<?php
/**
 * API: Tracking đơn hàng GHN
 * GET /api/tracking/{order_id}
 */

require __DIR__ . '/../../vendor/autoload.php';

// Load .env manually - hardcode path
$envFile = __DIR__ . '/../../.env';
if (file_exists($envFile)) {
    $lines = file($envFile, FILE_IGNORE_NEW_LINES | FILE_SKIP_EMPTY_LINES);
    foreach ($lines as $line) {
        if (strpos(trim($line), '#') === 0) continue;
        if (strpos($line, '=') === false) continue;
        
        list($key, $value) = explode('=', $line, 2);
        $key = trim($key);
        $value = trim($value, " \t\n\r\0\x0B\"'");
        
        if (!array_key_exists($key, $_ENV)) {
            $_ENV[$key] = $value;
            putenv("$key=$value");
        }
    }
}

session_start();

header('Content-Type: application/json');

// Lấy order_id từ URL
$orderId = $_GET['id'] ?? null;

if (!$orderId) {
    http_response_code(400);
    echo json_encode(['success' => false, 'message' => 'Missing order ID']);
    exit;
}

try {
    // Lấy thông tin đơn hàng
    $orderModel = new \App\Models\OrderModel();
    $order = $orderModel->find((int) $orderId);

    if (!$order) {
        http_response_code(404);
        echo json_encode(['success' => false, 'message' => 'Order not found']);
        exit;
    }

    // Kiểm tra quyền (chỉ user sở hữu hoặc admin) - nếu có session
    if (!empty($_SESSION['user_id'])) {
        if ($order['user_id'] != $_SESSION['user_id'] && ($_SESSION['user_role'] ?? '') !== 'admin') {
            http_response_code(403);
            echo json_encode(['success' => false, 'message' => 'Forbidden']);
            exit;
        }
    }
    // Nếu không có session, cho phép tracking (đơn hàng công khai cho demo)
    
    // Kiểm tra có shipping_code không
    if (empty($order['shipping_code'])) {
        echo json_encode([
            'success' => false,
            'message' => 'Đơn hàng chưa có mã vận đơn'
        ]);
        exit;
    }

    // Gọi GHN tracking API với status từ database
    $ghn = new \App\Services\GhnShippingService();
    error_log("Tracking request - Order ID: $orderId, Order Status: {$order['status']}, Shipping Code: {$order['shipping_code']}");
    $tracking = $ghn->trackOrder($order['shipping_code'], $order['status']);
    error_log("Tracking response: " . json_encode($tracking));

    echo json_encode([
        'success' => true,
        'tracking' => $tracking
    ]);
    
} catch (Exception $e) {
    http_response_code(500);
    echo json_encode([
        'success' => false,
        'message' => $e->getMessage()
    ]);
}
