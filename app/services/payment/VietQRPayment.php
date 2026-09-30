<?php

namespace App\Services\Payment;

/**
 * VietQR Payment Service - Simplified Version
 * Sử dụng VietQR.io API (FREE) để tạo QR Code
 * 
 * API Documentation: https://www.vietqr.io/
 */
class VietQRPayment implements PaymentInterface
{
    private string $accountNumber;
    private string $accountName;
    private string $bankCode;
    
    public function __construct()
    {
        // Load config từ .env
        $this->accountNumber = $_ENV['BANK_ACCOUNT_NUMBER'] ?? '';
        $this->accountName = $_ENV['BANK_ACCOUNT_NAME'] ?? '';
        $this->bankCode = $_ENV['BANK_CODE'] ?? 'VCB';
    }
    
    /**
     * Tạo giao dịch thanh toán VietQR
     */
    public function createTransaction(array $order): array
    {
        // Generate order_code unique (dùng làm nội dung chuyển khoản)
        $orderCode = $this->generateOrderCode($order);
        
        // Tạo QR Code URL sử dụng VietQR.io API (FREE)
        $qrImageUrl = $this->generateQRCodeUrl(
            $this->bankCode,
            $this->accountNumber,
            $this->accountName,
            (int) $order['total_amount'],
            $orderCode
        );
        
        // Lưu thông tin vào database
        $this->updateOrderWithQR($order['id'], $orderCode, $qrImageUrl);
        
        return [
            'success' => true,
            'order_code' => $orderCode,
            'qr_url' => $qrImageUrl,
            'amount' => $order['total_amount'],
            'account_number' => $this->accountNumber,
            'account_name' => $this->accountName,
            'bank_code' => $this->bankCode,
            'bank_name' => $this->getBankName($this->bankCode),
            'content' => $orderCode,
            'transfer_content' => $orderCode,
            'expires_at' => date('Y-m-d H:i:s', strtotime('+15 minutes')),
        ];
    }
    
    /**
     * Xử lý webhook callback (giả lập - không dùng webhook thật)
     */
    public function handleCallback(array $payload): array
    {
        // Đơn giản hóa - không dùng webhook thật
        // Admin sẽ xác nhận thủ công hoặc dùng simulate-payment.php để test
        return [
            'success' => false,
            'message' => 'Webhook not implemented - use manual confirmation',
        ];
    }
    
    /**
     * Check payment status
     */
    public function checkStatus(array $order): array
    {
        $status = $order['payment_status'] ?? 'pending';
        
        return [
            'status' => $status,
            'message' => $status === 'paid' ? 'Đã thanh toán' : 'Chờ thanh toán',
        ];
    }
    
    /**
     * Get method name
     */
    public function getMethodName(): string
    {
        return 'Chuyển khoản VietQR';
    }
    
    // ──────────────────────────────────────────────────────────────────────────
    // PRIVATE HELPERS
    // ──────────────────────────────────────────────────────────────────────────
    
    /**
     * Generate order code unique
     * Format: DH + YYYYMMDD + Random 4 ký tự
     */
    private function generateOrderCode(array $order): string
    {
        return 'DH' . date('Ymd') . strtoupper(substr(md5(uniqid($order['id'], true)), 0, 4));
    }
    
    /**
     * Generate QR Code URL using VietQR.io API
     * 
     * API Doc: https://www.vietqr.io/danh-sach-api/create-qr-code
     * 
     * @param string $bankCode Mã ngân hàng (VCB, TCB, MB, ...)
     * @param string $accountNumber Số tài khoản
     * @param string $accountName Tên chủ tài khoản
     * @param int $amount Số tiền (VNĐ)
     * @param string $content Nội dung chuyển khoản
     * @return string QR Code image URL
     */
    private function generateQRCodeUrl(
        string $bankCode,
        string $accountNumber,
        string $accountName,
        int $amount,
        string $content
    ): string {
        // VietQR.io format: https://img.vietqr.io/image/{BANK_ID}-{ACCOUNT_NUMBER}-{TEMPLATE}.png
        // 
        // Templates:
        // - compact: Nhỏ gọn, không có logo
        // - compact2: Nhỏ gọn, có logo ngân hàng + thông tin TK (khuyên dùng)
        // - qr_only: Chỉ có mã QR
        // - print: Dạng in ấn, đầy đủ thông tin
        
        $template = 'compact2';
        
        // Build query parameters
        $params = [
            'amount' => $amount,
            'addInfo' => $content,
            'accountName' => $accountName,
        ];
        
        $queryString = http_build_query($params);
        
        // Final URL
        return "https://img.vietqr.io/image/{$bankCode}-{$accountNumber}-{$template}.png?{$queryString}";
    }
    
    /**
     * Get bank name from bank code
     */
    private function getBankName(string $bankCode): string
    {
        $banks = [
            'VCB' => 'Vietcombank',
            'TCB' => 'Techcombank',
            'MB' => 'MB Bank',
            'VBA' => 'Agribank',
            'BIDV' => 'BIDV',
            'VTB' => 'Vietinbank',
            'ACB' => 'ACB',
            'STB' => 'Sacombank',
            'VPB' => 'VPBank',
            'TPB' => 'TPBank',
            'MSB' => 'MSB',
            'OCB' => 'OCB',
            'SHB' => 'SHB',
            'EIB' => 'Eximbank',
            'HDB' => 'HDBank',
            'SCB' => 'SCB',
            'VIB' => 'VIB',
            'SeABank' => 'SeABank',
            'CAKE' => 'CAKE by VPBank',
        ];
        
        return $banks[$bankCode] ?? $bankCode;
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
        ], 'id = ?', [$orderId]);
    }
}
