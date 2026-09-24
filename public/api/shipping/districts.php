<?php

/**
 * API: Lấy danh sách quận/huyện theo tỉnh
 * GET /api/shipping/districts.php?province_id=201
 */

require_once __DIR__ . '/../../../vendor/autoload.php';

// Load environment
$dotenv = Dotenv\Dotenv::createImmutable(dirname(dirname(dirname(__DIR__))));
$dotenv->load();

use App\Services\GhnShippingService;

header('Content-Type: application/json; charset=utf-8');

try {
    $provinceId = (int) ($_GET['province_id'] ?? 0);
    
    if (!$provinceId) {
        http_response_code(400);
        echo json_encode([
            'success' => false,
            'message' => 'Thiếu province_id',
        ], JSON_UNESCAPED_UNICODE);
        exit;
    }
    
    $ghn = new GhnShippingService();
    $result = $ghn->getDistricts($provinceId);
    
    if ($result['success']) {
        echo json_encode([
            'success' => true,
            'data' => $result['data'],
        ], JSON_UNESCAPED_UNICODE);
    } else {
        http_response_code(500);
        echo json_encode([
            'success' => false,
            'message' => $result['message'] ?? 'Lỗi khi lấy danh sách quận/huyện',
        ], JSON_UNESCAPED_UNICODE);
    }
} catch (Exception $e) {
    http_response_code(500);
    echo json_encode([
        'success' => false,
        'message' => $e->getMessage(),
    ], JSON_UNESCAPED_UNICODE);
}
