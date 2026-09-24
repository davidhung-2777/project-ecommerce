<?php

namespace App\Services;

/**
 * Giao Hàng Nhanh (GHN) Shipping Service
 * 
 * API Documentation: https://api.ghn.vn/home/docs/detail
 */
class GhnShippingService
{
    private string $apiToken;
    private string $shopId;
    private string $apiUrl;
    private int $fromDistrictId;
    private string $fromWardCode;
    
    public function __construct()
    {
        $this->apiToken = $_ENV['GHN_API_TOKEN'] ?? '';
        $this->shopId = $_ENV['GHN_SHOP_ID'] ?? '';
        $this->apiUrl = $_ENV['GHN_API_URL'] ?? 'https://dev-online-gateway.ghn.vn/shiip/public-api';
        $this->fromDistrictId = (int) ($_ENV['GHN_FROM_DISTRICT_ID'] ?? 1542);
        $this->fromWardCode = $_ENV['GHN_FROM_WARD_CODE'] ?? '20308';
    }
    
    /**
     * Tính phí vận chuyển
     * 
     * @param array $data [
     *   'to_district_id' => int,
     *   'to_ward_code' => string,
     *   'weight' => int (gram),
     *   'order_value' => int (đồng),
     *   'service_type_id' => int (optional, default 2)
     * ]
     */
    public function calculateShippingFee(array $data): array
    {
        try {
            $endpoint = $this->apiUrl . '/v2/shipping-order/fee';
            
            $payload = [
                'from_district_id' => $this->fromDistrictId,
                'to_district_id' => (int) $data['to_district_id'],
                'to_ward_code' => (string) $data['to_ward_code'],
                'weight' => (int) ($data['weight'] ?? 1000), // gram
                'service_type_id' => (int) ($data['service_type_id'] ?? 2), // 2: E-commerce Delivery
                'insurance_value' => (int) ($data['order_value'] ?? 0),
            ];
            
            $response = $this->makeRequest('POST', $endpoint, $payload);
            
            return [
                'success' => true,
                'fee' => $response['data']['total'] ?? 0,
                'service_fee' => $response['data']['service_fee'] ?? 0,
                'expected_delivery_time' => $response['data']['expected_delivery_time'] ?? null,
            ];
        } catch (\Exception $e) {
            return [
                'success' => false,
                'message' => $e->getMessage(),
                'fee' => 50000, // Phí mặc định nếu API lỗi
            ];
        }
    }
    
    /**
     * Tạo đơn hàng vận chuyển
     */
    public function createShippingOrder(array $data): array
    {
        try {
            $endpoint = $this->apiUrl . '/v2/shipping-order/create';
            
            $payload = [
                'to_name' => $data['to_name'],
                'to_phone' => $data['to_phone'],
                'to_address' => $data['to_address'],
                'to_ward_code' => $data['to_ward_code'],
                'to_district_id' => (int) $data['to_district_id'],
                'weight' => (int) ($data['weight'] ?? 1000),
                'length' => (int) ($data['length'] ?? 20),
                'width' => (int) ($data['width'] ?? 20),
                'height' => (int) ($data['height'] ?? 10),
                'service_type_id' => 2, // E-commerce Delivery
                'payment_type_id' => (int) ($data['payment_type_id'] ?? 1), // 1: Người gửi trả, 2: Người nhận trả (COD)
                'required_note' => $data['required_note'] ?? 'CHOXEMHANGKHONGTHU',
                'items' => $data['items'] ?? [],
                'cod_amount' => (int) ($data['cod_amount'] ?? 0),
            ];
            
            $response = $this->makeRequest('POST', $endpoint, $payload);
            
            return [
                'success' => true,
                'order_code' => $response['data']['order_code'] ?? null,
                'expected_delivery_time' => $response['data']['expected_delivery_time'] ?? null,
            ];
        } catch (\Exception $e) {
            return [
                'success' => false,
                'message' => $e->getMessage(),
            ];
        }
    }
    
    /**
     * Tracking đơn hàng
     */
    public function trackOrder(string $orderCode): array
    {
        try {
            $endpoint = $this->apiUrl . '/v2/shipping-order/detail';
            
            $payload = ['order_code' => $orderCode];
            
            $response = $this->makeRequest('POST', $endpoint, $payload);
            
            return [
                'success' => true,
                'data' => $response['data'] ?? [],
            ];
        } catch (\Exception $e) {
            return [
                'success' => false,
                'message' => $e->getMessage(),
            ];
        }
    }
    
    /**
     * Lấy danh sách tỉnh/thành
     */
    public function getProvinces(): array
    {
        try {
            $endpoint = $this->apiUrl . '/master-data/province';
            $response = $this->makeRequest('GET', $endpoint);
            
            return [
                'success' => true,
                'data' => $response['data'] ?? [],
            ];
        } catch (\Exception $e) {
            return [
                'success' => false,
                'message' => $e->getMessage(),
                'data' => [],
            ];
        }
    }
    
    /**
     * Lấy danh sách quận/huyện theo tỉnh
     */
    public function getDistricts(int $provinceId): array
    {
        try {
            $endpoint = $this->apiUrl . '/master-data/district';
            $response = $this->makeRequest('POST', $endpoint, ['province_id' => $provinceId]);
            
            return [
                'success' => true,
                'data' => $response['data'] ?? [],
            ];
        } catch (\Exception $e) {
            return [
                'success' => false,
                'message' => $e->getMessage(),
                'data' => [],
            ];
        }
    }
    
    /**
     * Lấy danh sách phường/xã theo quận
     */
    public function getWards(int $districtId): array
    {
        try {
            $endpoint = $this->apiUrl . '/master-data/ward';
            $response = $this->makeRequest('GET', $endpoint . '?district_id=' . $districtId);
            
            return [
                'success' => true,
                'data' => $response['data'] ?? [],
            ];
        } catch (\Exception $e) {
            return [
                'success' => false,
                'message' => $e->getMessage(),
                'data' => [],
            ];
        }
    }
    
    /**
     * Lấy danh sách dịch vụ vận chuyển khả dụng
     */
    public function getAvailableServices(int $toDistrictId): array
    {
        try {
            $endpoint = $this->apiUrl . '/v2/shipping-order/available-services';
            
            $payload = [
                'shop_id' => (int) $this->shopId,
                'from_district' => $this->fromDistrictId,
                'to_district' => $toDistrictId,
            ];
            
            $response = $this->makeRequest('POST', $endpoint, $payload);
            
            return [
                'success' => true,
                'data' => $response['data'] ?? [],
            ];
        } catch (\Exception $e) {
            return [
                'success' => false,
                'message' => $e->getMessage(),
                'data' => [],
            ];
        }
    }
    
    /**
     * Hủy đơn hàng vận chuyển
     */
    public function cancelOrder(string $orderCode): array
    {
        try {
            $endpoint = $this->apiUrl . '/v2/switch-status/cancel';
            
            $payload = [
                'order_codes' => [$orderCode],
            ];
            
            $response = $this->makeRequest('POST', $endpoint, $payload);
            
            return [
                'success' => true,
                'data' => $response['data'] ?? [],
            ];
        } catch (\Exception $e) {
            return [
                'success' => false,
                'message' => $e->getMessage(),
            ];
        }
    }
    
    /**
     * Cập nhật số tiền COD của đơn hàng
     */
    public function updateCOD(string $orderCode, int $codAmount): array
    {
        try {
            $endpoint = $this->apiUrl . '/v2/shipping-order/updateCOD';
            
            $payload = [
                'order_code' => $orderCode,
                'cod_amount' => $codAmount,
            ];
            
            $response = $this->makeRequest('POST', $endpoint, $payload);
            
            return [
                'success' => true,
                'data' => $response['data'] ?? [],
            ];
        } catch (\Exception $e) {
            return [
                'success' => false,
                'message' => $e->getMessage(),
            ];
        }
    }
    
    /**
     * In phiếu giao hàng
     */
    public function printOrder(array $orderCodes): string
    {
        // GHN trả về link PDF để in
        return $this->apiUrl . '/v2/a5/gen-token?order_codes=' . implode(',', $orderCodes) . '&token=' . $this->apiToken;
    }
    
    /**
     * Make HTTP request to GHN API
     */
    private function makeRequest(string $method, string $url, array $data = []): array
    {
        $ch = curl_init();
        
        $headers = [
            'Content-Type: application/json',
            'Token: ' . $this->apiToken,
        ];
        
        // Shop ID chỉ cần cho một số endpoint
        if (!empty($this->shopId)) {
            $headers[] = 'ShopId: ' . $this->shopId;
        }
        
        curl_setopt($ch, CURLOPT_URL, $url);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
        curl_setopt($ch, CURLOPT_TIMEOUT, 30);
        
        if ($method === 'POST') {
            curl_setopt($ch, CURLOPT_POST, true);
            curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($data));
        } elseif ($method === 'GET' && !empty($data)) {
            curl_setopt($ch, CURLOPT_URL, $url . '?' . http_build_query($data));
        }
        
        $response = curl_exec($ch);
        $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
        $error = curl_error($ch);
        curl_close($ch);
        
        if ($error) {
            throw new \Exception('CURL Error: ' . $error);
        }
        
        $result = json_decode($response, true);
        
        if ($httpCode !== 200) {
            throw new \Exception($result['message'] ?? 'GHN API Error (HTTP ' . $httpCode . ')');
        }
        
        if (!isset($result['code']) || $result['code'] !== 200) {
            throw new \Exception($result['message'] ?? 'GHN API returned error code');
        }
        
        return $result;
    }
}
