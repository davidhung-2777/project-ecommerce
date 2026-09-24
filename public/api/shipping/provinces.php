<?php

/**
 * API: Lấy danh sách tỉnh/thành phố
 * GET /api/shipping/provinces.php
 */

require_once __DIR__ . '/../../../vendor/autoload.php';

// Load environment
$dotenv = Dotenv\Dotenv::createImmutable(dirname(dirname(dirname(__DIR__))));
$dotenv->load();

use App\Services\GhnShippingService;

header('Content-Type: application/json; charset=utf-8');

try {
    $ghn = new GhnShippingService();
    $result = $ghn->getProvinces();
    
    if ($result['success']) {
        echo json_encode([
            'success' => true,
            'data' => $result['data'],
        ], JSON_UNESCAPED_UNICODE);
    } else {
        http_response_code(500);
        echo json_encode([
            'success' => false,
            'message' => $result['message'] ?? 'Lỗi khi lấy danh sách tỉnh/thành',
        ], JSON_UNESCAPED_UNICODE);
    }
} catch (Exception $e) {
    http_response_code(500);
    echo json_encode([
        'success' => false,
        'message' => $e->getMessage(),
    ], JSON_UNESCAPED_UNICODE);
}
