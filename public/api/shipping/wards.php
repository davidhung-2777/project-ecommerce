<?php

/**
 * API: Lấy danh sách phường/xã theo quận
 * GET /api/shipping/wards.php?district_id=1542
 */

require_once __DIR__ . '/../../../vendor/autoload.php';

// Load environment
$dotenv = Dotenv\Dotenv::createImmutable(dirname(dirname(dirname(__DIR__))));
$dotenv->load();

use App\Services\GhnShippingService;

header('Content-Type: application/json; charset=utf-8');

try {
    $districtId = (int) ($_GET['district_id'] ?? 0);
    
    if (!$districtId) {
        http_response_code(400);
        echo json_encode([
            'success' => false,
            'message' => 'Thiếu district_id',
        ], JSON_UNESCAPED_UNICODE);
        exit;
    }
    
    $ghn = new GhnShippingService();
    $result = $ghn->getWards($districtId);
    
    if ($result['success']) {
        echo json_encode([
            'success' => true,
            'data' => $result['data'],
        ], JSON_UNESCAPED_UNICODE);
    } else {
        http_response_code(500);
        echo json_encode([
            'success' => false,
            'message' => $result['message'] ?? 'Lỗi khi lấy danh sách phường/xã',
        ], JSON_UNESCAPED_UNICODE);
    }
} catch (Exception $e) {
    http_response_code(500);
    echo json_encode([
        'success' => false,
        'message' => $e->getMessage(),
    ], JSON_UNESCAPED_UNICODE);
}
