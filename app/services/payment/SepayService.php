<?php

/**
 * SePay API Integration Service
 * 
 * SePay là dịch vụ cung cấp API để nhận thông báo real-time 
 * khi có giao dịch chuyển khoản vào tài khoản ngân hàng
 * 
 * Documentation: https://docs.sepay.vn/
 * Portal: https://my.sepay.vn/
 * 
 * Features:
 * - Webhook real-time khi có giao dịch mới
 * - Lấy lịch sử giao dịch
 * - Kiểm tra số dư tài khoản
 * - Hỗ trợ đa ngân hàng (Vietcombank, VietinBank, BIDV, etc.)
 * 
 * @package App\Services\Payment
 * @author Kiro AI
 * @version 1.0
 */
class SepayService
{
    private string $apiKey;
    private string $accountNumber;
    private string $webhookSecret;
    private string $baseUrl = 'https://my.sepay.vn/userapi';
    
    /**
     * Constructor
     */
    public function __construct()
    {
        $this->apiKey = $_ENV['SEPAY_API_KEY'] ?? '';
        $this->accountNumber = $_ENV['SEPAY_ACCOUNT_NUMBER'] ?? '';
        $this->webhookSecret = $_ENV['SEPAY_WEBHOOK_SECRET'] ?? '';
    }

    /**
     * Lấy lịch sử giao dịch
     * 
     * @param string $fromDate Format: YYYY-MM-DD (optional)
     * @param string $toDate Format: YYYY-MM-DD (optional)
     * @param int $limit Number of records (default: 100, max: 1000)
     * @return array
     */
    public function getTransactionHistory(string $fromDate = '', string $toDate = '', int $limit = 100): array
    {
        if (empty($this->apiKey)) {
            return ['success' => false, 'message' => 'SePay API Key chưa được cấu hình'];
        }

        $params = [
            'account_number' => $this->accountNumber,
            'limit' => min($limit, 1000), // Max 1000
        ];

        if ($fromDate) {
            $params['from_date'] = $fromDate;
        }

        if ($toDate) {
            $params['to_date'] = $toDate;
        }

        $url = $this->baseUrl . '/transactions?' . http_build_query($params);

        try {
            $response = $this->makeRequest($url);
            
            if ($response['success']) {
                return [
                    'success' => true,
                    'data' => $response['data']['transactions'] ?? [],
                    'total' => count($response['data']['transactions'] ?? [])
                ];
            }

            return $response;
        } catch (Exception $e) {
            return [
                'success' => false,
                'message' => 'Lỗi kết nối SePay API: ' . $e->getMessage()
            ];
        }
    }

    /**
     * Kiểm tra số dư tài khoản
     * 
     * @return array
     */
    public function getBalance(): array
    {
        if (empty($this->apiKey)) {
            return ['success' => false, 'message' => 'SePay API Key chưa được cấu hình'];
        }

        $url = $this->baseUrl . '/balance?account_number=' . $this->accountNumber;

        try {
            $response = $this->makeRequest($url);
            
            if ($response['success']) {
                return [
                    'success' => true,
                    'balance' => $response['data']['balance'] ?? 0,
                    'account_number' => $response['data']['account_number'] ?? '',
                    'account_name' => $response['data']['account_name'] ?? '',
                    'bank_name' => $response['data']['bank_name'] ?? ''
                ];
            }

            return $response;
        } catch (Exception $e) {
            return [
                'success' => false,
                'message' => 'Lỗi kết nối SePay API: ' . $e->getMessage()
            ];
        }
    }

    /**
     * Lấy thông tin tài khoản ngân hàng đã liên kết
     * 
     * @return array
     */
    public function getLinkedAccounts(): array
    {
        if (empty($this->apiKey)) {
            return ['success' => false, 'message' => 'SePay API Key chưa được cấu hình'];
        }

        $url = $this->baseUrl . '/bank-accounts';

        try {
            $response = $this->makeRequest($url);
            
            if ($response['success']) {
                return [
                    'success' => true,
                    'accounts' => $response['data']['accounts'] ?? []
                ];
            }

            return $response;
        } catch (Exception $e) {
            return [
                'success' => false,
                'message' => 'Lỗi kết nối SePay API: ' . $e->getMessage()
            ];
        }
    }

    /**
     * Xác thực webhook signature từ SePay
     * 
     * @param array $payload Webhook payload
     * @param string $signature Signature từ header
     * @return bool
     */
    public function verifyWebhookSignature(array $payload, string $signature): bool
    {
        if (empty($this->webhookSecret)) {
            error_log('SePay Webhook Secret chưa được cấu hình');
            return false;
        }

        // SePay signature format: sha256(webhook_secret + json_payload)
        $jsonPayload = json_encode($payload, JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES);
        $expectedSignature = hash_hmac('sha256', $jsonPayload, $this->webhookSecret);

        return hash_equals($expectedSignature, $signature);
    }

    /**
     * Tìm kiếm giao dịch theo mã đơn hàng trong nội dung chuyển khoản
     * 
     * @param string $orderCode Mã đơn hàng (ví dụ: DH12345)
     * @param int $daysBack Tìm trong bao nhiêu ngày trước (default: 7)
     * @return array|null
     */
    public function findTransactionByOrderCode(string $orderCode, int $daysBack = 7): ?array
    {
        $fromDate = date('Y-m-d', strtotime("-{$daysBack} days"));
        $toDate = date('Y-m-d');

        $history = $this->getTransactionHistory($fromDate, $toDate, 1000);

        if (!$history['success']) {
            return null;
        }

        foreach ($history['data'] as $transaction) {
            $description = strtoupper($transaction['transaction_content'] ?? '');
            if (strpos($description, strtoupper($orderCode)) !== false) {
                return $transaction;
            }
        }

        return null;
    }

    /**
     * Parse webhook payload từ SePay
     * 
     * @param array $payload Raw webhook data
     * @return array Normalized transaction data
     */
    public function parseWebhookPayload(array $payload): array
    {
        return [
            'transaction_id' => $payload['id'] ?? '',
            'reference_number' => $payload['reference_number'] ?? '',
            'amount' => (float)($payload['amount_in'] ?? $payload['amount'] ?? 0),
            'description' => $payload['transaction_content'] ?? '',
            'transaction_date' => $payload['transaction_date'] ?? '',
            'account_number' => $payload['account_number'] ?? '',
            'bank_code' => $payload['bank_brand_name'] ?? '',
            'gateway' => $payload['gateway'] ?? '',
            'accumulated' => (float)($payload['accumulated'] ?? 0),
            'raw_data' => $payload
        ];
    }

    /**
     * Tạo QR Code thanh toán VietQR
     * 
     * @param float $amount Số tiền
     * @param string $orderCode Mã đơn hàng
     * @param string $description Nội dung chuyển khoản
     * @return array
     */
    public function generateQRCode(float $amount, string $orderCode, string $description = ''): array
    {
        $accountNumber = $this->accountNumber;
        $bankCode = $_ENV['SEPAY_BANK_CODE'] ?? 'VCB';
        
        // Format: Noi dung CK: [Ma don hang] [Mo ta]
        $content = $orderCode;
        if ($description) {
            $content .= ' ' . $description;
        }

        // VietQR format
        $qrContent = [
            'bank_code' => $bankCode,
            'account_number' => $accountNumber,
            'amount' => $amount,
            'description' => $content
        ];

        // Generate QR using VietQR API
        $qrUrl = $this->generateVietQRUrl($qrContent);

        return [
            'success' => true,
            'qr_code_url' => $qrUrl,
            'qr_data' => $qrContent,
            'account_number' => $accountNumber,
            'account_name' => $_ENV['SEPAY_ACCOUNT_NAME'] ?? '',
            'bank_name' => $_ENV['BANK_NAME'] ?? '',
            'bank_code' => $bankCode,
            'amount' => $amount,
            'content' => $content
        ];
    }

    /**
     * Generate VietQR URL
     * 
     * @param array $data
     * @return string
     */
    private function generateVietQRUrl(array $data): string
    {
        // VietQR.io API (free, no authentication required)
        $baseUrl = 'https://img.vietqr.io/image';
        $bankCode = $data['bank_code'];
        $accountNo = $data['account_number'];
        $amount = $data['amount'];
        $description = urlencode($data['description']);
        
        return "{$baseUrl}/{$bankCode}-{$accountNo}-compact2.jpg?amount={$amount}&addInfo={$description}&accountName=" . urlencode($_ENV['SEPAY_ACCOUNT_NAME'] ?? '');
    }

    /**
     * Make HTTP request to SePay API
     * 
     * @param string $url
     * @param string $method
     * @param array $data
     * @return array
     */
    private function makeRequest(string $url, string $method = 'GET', array $data = []): array
    {
        $ch = curl_init();

        curl_setopt_array($ch, [
            CURLOPT_URL => $url,
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_SSL_VERIFYPEER => false,
            CURLOPT_TIMEOUT => 30,
            CURLOPT_HTTPHEADER => [
                'Authorization: Bearer ' . $this->apiKey,
                'Content-Type: application/json',
                'Accept: application/json'
            ]
        ]);

        if ($method === 'POST') {
            curl_setopt($ch, CURLOPT_POST, true);
            curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($data));
        }

        $response = curl_exec($ch);
        $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
        $error = curl_error($ch);
        curl_close($ch);

        if ($error) {
            throw new Exception($error);
        }

        $responseData = json_decode($response, true);

        if ($httpCode !== 200) {
            return [
                'success' => false,
                'message' => $responseData['message'] ?? 'HTTP Error: ' . $httpCode,
                'http_code' => $httpCode
            ];
        }

        return [
            'success' => true,
            'data' => $responseData,
            'http_code' => $httpCode
        ];
    }

    /**
     * Test connection to SePay API
     * 
     * @return array
     */
    public function testConnection(): array
    {
        if (empty($this->apiKey)) {
            return [
                'success' => false,
                'message' => 'SePay API Key chưa được cấu hình'
            ];
        }

        $result = $this->getLinkedAccounts();

        if ($result['success']) {
            return [
                'success' => true,
                'message' => 'Kết nối SePay API thành công!',
                'accounts' => $result['accounts']
            ];
        }

        return [
            'success' => false,
            'message' => 'Không thể kết nối SePay API: ' . ($result['message'] ?? 'Unknown error')
        ];
    }
}
