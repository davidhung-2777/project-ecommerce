<?php
/**
 * API: Get order status for polling
 * GET /api/order-status.php?order_code=DH20250827XXXX
 * 
 * Frontend sẽ poll endpoint này mỗi 2.5 giây để check trạng thái thanh toán
 */

require_once __DIR__ . '/../../vendor/autoload.php';
require_once __DIR__ . '/../../app/core/Database.php';

header('Content-Type: application/json; charset=utf-8');
header('Access-Control-Allow-Origin: *');

$orderCode = $_GET['order_code'] ?? '';

if (empty($orderCode)) {
    http_response_code(400);
    echo json_encode(['error' => 'Missing order_code parameter']);
    exit;
}

try {
    $db = \App\Core\Database::getInstance();
    
    $order = $db->fetch(
        "SELECT id, order_number, status, payment_status, total_amount, created_at
         FROM orders 
         WHERE order_number = ?",
        [$orderCode]
    );
    
    if (!$order) {
        http_response_code(404);
        echo json_encode(['error' => 'Order not found']);
        exit;
    }
    
    // Calculate time remaining
    $expiresAt = null;
    $now = time();
    $timeRemaining = $expiresAt ? max(0, $expiresAt - $now) : 0;
    
    // Determine display status
    $displayStatus = 'pending'; // pending | paid | expired | needs_review
    
    if ($order['payment_status'] === 'paid') {
        $displayStatus = 'paid';
    } elseif ($order['status'] === 'expired') {
        $displayStatus = 'expired';
    } elseif ($order['status'] === 'needs_review') {
        $displayStatus = 'needs_review';
    } elseif ($expiresAt && $timeRemaining === 0) {
        $displayStatus = 'expired';
    }
    
    echo json_encode([
        'order_code' => $order['order_number'],
        'status' => $displayStatus,
        'payment_status' => $order['payment_status'],
        'total_amount' => (float) $order['total_amount'],
        'expires_at' => null,
        'paid_at' => null,
        'time_remaining_seconds' => $timeRemaining,
        'created_at' => $order['created_at']
    ], JSON_UNESCAPED_UNICODE);
    
} catch (\Exception $e) {
    http_response_code(500);
    echo json_encode(['error' => 'Internal server error']);
    error_log("Order status API error: " . $e->getMessage());
}
