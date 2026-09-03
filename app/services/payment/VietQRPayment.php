<?php

namespace App\Services\Payment;

/**
 * VietQR Payment Service
 * Tích hợp với SePay hoặc Casso để tự động nhận webhook khi có giao dịch
 * 
 * API Documentation:
 * - SePay: https://my.sepay.vn/userapi/docs/
 * - Casso: https://docs.casso.vn/
 */
class VietQRPayment implements PaymentInterface
{
    private string $provider; // 'sepay' hoặc 'casso'
    private string $apiKey;
    private string $accountNumber;
    private string $accountName;
    private string $bankCode;
    private string $webhookSecret;
    
    public function __construct()
    {
        // Load config từ .env hoặc config file
        $this->provider = $_ENV['VIETQR_PROVIDER'] ?? 'sepay'; // sepay | casso
        $this->apiKey = $_ENV['VIETQR_API_KEY'] ?? '';
        $this->accountNumber = $_ENV['BANK_ACCOUNT_NUMBER'] ?? '';
        $this->accountName = $_ENV['BANK_ACCOUNT_NAME'] ?? '';
        $this->bankCode = $_ENV['BANK_CODE'] ?? 'VCB'; // VCB, TCB, MB, etc.
        $this->webhookSecret = $_ENV['VIETQR_WEBHOOK_SECRET'] ?? '';
    }
    
    /**
     * Tạo giao dịch thanh toán VietQR
     * 
     * @param array $order Order data từ database
     * @return array ['success' => bool, 'qr_url' => string, 'qr_data' => string]
     */
    public function createTransaction(array $order): array
    {
        // Generate order_code unique (dùng làm nội dung chuyển khoản)
        $orderCode = $this->generateOrderCode($order);
        
        // Tạo QR code VietQR chuẩn
        $qrData = $this->generateVietQRString(
            $this->bankCode,
            $this->accountNumber,
            $this->accountName,
            (int) $order['total_amount'],
            $orderCode
        );
        
        // Generate QR image URL
        $qrImageUrl = $this->generateQRImageUrl($qrData);
        
        // Lưu thông tin vào database
        $this->updateOrderWithQR($order['id'], $orderCode, $qrImageUrl);
        
        return [
            'success' => true,
            'order_code' => $orderCode,
            'qr_url' => $qrImageUrl,
            'qr_data' => $qrData,
            'amount' => $order['total_amount'],
            'account_number' => $this->accountNumber,
            'account_name' => $this->accountName,
            'bank_code' => $this->bankCode,
            'content' => $orderCode,
            'expires_at' => date('Y-m-d H:i:s', strtotime('+15 minutes')),
        ];
    }
    
    /**
     * Xử lý webhook callback từ SePay/Casso
     * 
     * @param array $payload Raw webhook data
     * @return array ['success' => bool, 'order_code' => string, 'amount' => float]
     */
    public function handleCallback(array $payload): array
    {
        // Log webhook nhận được
        $this->logWebhook($payload);
        
        // Xác thực chữ ký
        if (!$this->verifyWebhookSignature($payload)) {
            return [
                'success' => false,
                'message' => 'Invalid signature',
                'signature_valid' => false
            ];
        }
        
        // Parse data theo provider
        $parsedData = $this->provider === 'sepay' 
            ? $this->parseSePay($payload)
            : $this->parseCasso($payload);
        
        if (!$parsedData['success']) {
            return $parsedData;
        }
        
        return [
            'success' => true,
            'signature_valid' => true,
            'order_code' => $parsedData['order_code'],
            'amount' => $parsedData['amount'],
            'transaction_id' => $parsedData['transaction_id'],
            'raw' => $payload
        ];
    }
    
    /**
     * Verify webhook signature
     */
    public function verifyWebhookSignature(array $payload): bool
    {
        if ($this->provider === 'sepay') {
            // SePay gửi signature trong header X-Sepay-Signature
            $receivedSignature = $_SERVER['HTTP_X_SEPAY_SIGNATURE'] ?? '';
            $calculatedSignature = hash_hmac('sha256', json_encode($payload), $this->webhookSecret);
            return hash_equals($calculatedSignature, $receivedSignature);
        }
        
        if ($this->provider === 'casso') {
            // Casso gửi secure_token trong payload
            $receivedToken = $payload['secure_token'] ?? '';
            return hash_equals($this->webhookSecret, $receivedToken);
        }
        
        return false;
    }
    
    /**
     * Parse SePay webhook data
     */
    private function parseSePay(array $payload): array
    {
        try {
            $transaction = $payload['data'] ?? [];
            
            // Trích order_code từ nội dung chuyển khoản
            $content = $transaction['transaction_content'] ?? '';
            $orderCode = $this->extractOrderCode($content);
            
            if (!$orderCode) {
                return ['success' => false, 'message' => 'Order code not found in transaction content'];
            }
            
            return [
                'success' => true,
                'order_code' => $orderCode,
                'amount' => (float) ($transaction['amount_in'] ?? 0),
                'transaction_id' => $transaction['id'] ?? '',
                'transaction_date' => $transaction['transaction_date'] ?? '',
            ];
        } catch (\Exception $e) {
            return ['success' => false, 'message' => $e->getMessage()];
        }
    }
    
    /**
     * Parse Casso webhook data
     */
    private function parseCasso(array $payload): array
    {
        try {
            $data = $payload['data'] ?? [];
            
            // Trích order_code từ description
            $description = $data['description'] ?? '';
            $orderCode = $this->extractOrderCode($description);
            
            if (!$orderCode) {
                return ['success' => false, 'message' => 'Order code not found'];
            }
            
            return [
                'success' => true,
                'order_code' => $orderCode,
                'amount' => (float) ($data['amount'] ?? 0),
                'transaction_id' => $data['id'] ?? '',
                'transaction_date' => $data['when'] ?? '',
            ];
        } catch (\Exception $e) {
            return ['success' => false, 'message' => $e->getMessage()];
        }
    }
    
    /**
     * Trích order_code từ nội dung chuyển khoản
     * Format: DH123456 hoặc bất kỳ text nào chứa mã đơn
     */
    private function extractOrderCode(string $content): ?string
    {
        // Remove diacritics và normalize
        $content = strtoupper($this->removeDiacritics($content));
        $content = preg_replace('/\s+/', '', $content); // Remove spaces
        
        // Pattern: DH + 6-10 số hoặc chữ
        if (preg_match('/DH[A-Z0-9]{6,10}/', $content, $matches)) {
            return $matches[0];
        }
        
        return null;
    }
    
    /**
     * Generate order code unique
     */
    private function generateOrderCode(array $order): string
    {
        // Format: DH + YYYYMMDD + Random 4 ký tự
        return 'DH' . date('Ymd') . strtoupper(substr(md5(uniqid($order['id'], true)), 0, 4));
    }
    
    /**
     * Generate VietQR string theo chuẩn EMVCo
     * Spec: https://www.emvco.com/emv-technologies/qrcodes/
     */
    private function generateVietQRString(
        string $bankCode,
        string $accountNumber,
        string $accountName,
        int $amount,
        string $content
    ): string {
        $qr = '';
        
        // Payload Format Indicator
        $qr .= $this->qrField('00', '01');
        
        // Point of Initiation Method
        $qr .= $this->qrField('01', '12'); // Dynamic QR
        
        // Merchant Account Information
        $guid = '970454'; // VietQR GUID
        $bankData = $this->qrField('00', $guid) // GUID
                  . $this->qrField('01', $bankCode) // Bank code
                  . $this->qrField('02', $accountNumber); // Account number
        
        $qr .= $this->qrField('38', $bankData);
        
        // Transaction Currency (VND = 704)
        $qr .= $this->qrField('53', '704');
        
        // Transaction Amount
        $qr .= $this->qrField('54', (string) $amount);
        
        // Country Code
        $qr .= $this->qrField('58', 'VN');
        
        // Merchant Name
        $qr .= $this->qrField('59', $this->removeDiacritics($accountName));
        
        // Additional Data Field
        $addData = $this->qrField('08', $content); // Bill number/Order code
        $qr .= $this->qrField('62', $addData);
        
        // CRC (placeholder, sẽ tính sau)
        $qr .= '6304';
        
        // Calculate CRC-16 CCITT
        $crc = $this->crc16($qr);
        $qr .= strtoupper($crc);
        
        return $qr;
    }
    
    /**
     * Generate QR field theo format: ID(2) + Length(2) + Value
     */
    private function qrField(string $id, string $value): string
    {
        $length = str_pad((string) strlen($value), 2, '0', STR_PAD_LEFT);
        return $id . $length . $value;
    }
    
    /**
     * Calculate CRC-16 CCITT checksum
     */
    private function crc16(string $data): string
    {
        $crc = 0xFFFF;
        for ($i = 0; $i < strlen($data); $i++) {
            $crc ^= ord($data[$i]) << 8;
            for ($j = 0; $j < 8; $j++) {
                if ($crc & 0x8000) {
                    $crc = ($crc << 1) ^ 0x1021;
                } else {
                    $crc = $crc << 1;
                }
                $crc &= 0xFFFF;
            }
        }
        return strtoupper(dechex($crc & 0xFFFF));
    }
    
    /**
     * Remove Vietnamese diacritics
     */
    private function removeDiacritics(string $str): string
    {
        $from = ['à','á','ạ','ả','ã','â','ầ','ấ','ậ','ẩ','ẫ','ă','ằ','ắ','ặ','ẳ','ẵ',
                 'è','é','ẹ','ẻ','ẽ','ê','ề','ế','ệ','ể','ễ',
                 'ì','í','ị','ỉ','ĩ',
                 'ò','ó','ọ','ỏ','õ','ô','ồ','ố','ộ','ổ','ỗ','ơ','ờ','ớ','ợ','ở','ỡ',
                 'ù','ú','ụ','ủ','ũ','ư','ừ','ứ','ự','ử','ữ',
                 'ỳ','ý','ỵ','ỷ','ỹ',
                 'đ',
                 'À','Á','Ạ','Ả','Ã','Â','Ầ','Ấ','Ậ','Ẩ','Ẫ','Ă','Ằ','Ắ','Ặ','Ẳ','Ẵ',
                 'È','É','Ẹ','Ẻ','Ẽ','Ê','Ề','Ế','Ệ','Ể','Ễ',
                 'Ì','Í','Ị','Ỉ','Ĩ',
                 'Ò','Ó','Ọ','Ỏ','Õ','Ô','Ồ','Ố','Ộ','Ổ','Ỗ','Ơ','Ờ','Ớ','Ợ','Ở','Ỡ',
                 'Ù','Ú','Ụ','Ủ','Ũ','Ư','Ừ','Ứ','Ự','Ử','Ữ',
                 'Ỳ','Ý','Ỵ','Ỷ','Ỹ',
                 'Đ'];
        
        $to   = ['a','a','a','a','a','a','a','a','a','a','a','a','a','a','a','a','a',
                 'e','e','e','e','e','e','e','e','e','e','e',
                 'i','i','i','i','i',
                 'o','o','o','o','o','o','o','o','o','o','o','o','o','o','o','o','o',
                 'u','u','u','u','u','u','u','u','u','u','u',
                 'y','y','y','y','y',
                 'd',
                 'A','A','A','A','A','A','A','A','A','A','A','A','A','A','A','A','A',
                 'E','E','E','E','E','E','E','E','E','E','E',
                 'I','I','I','I','I',
                 'O','O','O','O','O','O','O','O','O','O','O','O','O','O','O','O','O',
                 'U','U','U','U','U','U','U','U','U','U','U',
                 'Y','Y','Y','Y','Y',
                 'D'];
        
        return str_replace($from, $to, $str);
    }
    
    /**
     * Generate QR image URL using external service
     */
    private function generateQRImageUrl(string $qrData): string
    {
        // Sử dụng API tạo QR code (Google Charts API hoặc tự host)
        $encodedData = urlencode($qrData);
        return "https://api.qrserver.com/v1/create-qr-code/?size=400x400&data={$encodedData}";
    }
    
    /**
     * Update order với QR info
     */
    private function updateOrderWithQR(int $orderId, string $orderCode, string $qrUrl): void
    {
        $db = \App\Core\Database::getInstance();
        $db->update('orders', [
            'order_code' => $orderCode,
            'qr_image_url' => $qrUrl,
            'expires_at' => date('Y-m-d H:i:s', strtotime('+15 minutes')),
            'status' => 'pending'
        ], 'id = ?', [$orderId]);
    }
    
    /**
     * Log webhook vào database
     */
    private function logWebhook(array $payload): void
    {
        $db = \App\Core\Database::getInstance();
        $db->insert('payment_webhook_logs', [
            'raw_payload' => json_encode($payload),
            'signature_valid' => 0,
            'processed' => 0,
            'received_at' => date('Y-m-d H:i:s')
        ]);
    }
    
    public function verifySignature(array $params): bool
    {
        return $this->verifyWebhookSignature($params);
    }
}
