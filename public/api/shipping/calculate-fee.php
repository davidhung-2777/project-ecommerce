<?php

/**
 * API: Tính phí vận chuyển
 * POST /api/shipping/calculate-fee.php
 * Body: {
 *   "district_id": 1451,
 *   "ward_code": "20311",
 *   "weight": 5000,
 *   "order_value": 5000000
 * }
 */

require_once __DIR__ . '/../../../vendor/autoload.php';

// Load environment
$dotenv = Dotenv\Dotenv::createImmutable(dirname(dirname(dirname(__DIR__))));
$dotenv->load();

use App\Services\GhnShippingService;

header('Content-Type: application/json; charset=utf-8');

try {
    // Lấy dữ liệu từ request
    $input = json_decode(file_get_contents('php://input'), true);
    
    $districtId = (int) ($input['district_id'] ?? 0);
    $wardCode = $input['ward_code'] ?? '';
    $weight = (int) ($input['weight'] ?? 1000); // mặc định 1kg
    $orderValue = (int) ($input['order_value'] ?? 0);
    
    if (!$districtId || !$wardCode) {
        http_response_code(400);
        echo json_encode([
            'success' => false,
            'message' => 'Thiếu district_id hoặc ward_code',
        ], JSON_UNESCAPED_UNICODE);
        exit;
    }
    
    $ghn = new GhnShippingService();
    $result = $ghn->calculateShippingFee([
        'to_district_id' => $districtId,
        'to_ward_code' => $wardCode,
        'weight' => $weight,
        'order_value' => $orderValue,
    ]);
    
    if ($result['success']) {
        echo json_encode([
            'success' => true,
            'fee' => $result['fee'],
            'expected_delivery_time' => $result['expected_delivery_time'] ?? null,
        ], JSON_UNESCAPED_UNICODE);
    } else {
        // Trả về phí mặc định nếu API lỗi
        echo json_encode([
            'success' => true,
            'fee' => 50000, // Phí mặc định
            'message' => 'Sử dụng phí vận chuyển mặc định',
        ], JSON_UNESCAPED_UNICODE);
    }
} catch (Exception $e) {
    http_response_code(500);
    echo json_encode([
        'success' => false,
        'message' => $e->getMessage(),
        'fee' => 50000, // Phí mặc định khi lỗi
    ], JSON_UNESCAPED_UNICODE);
}
