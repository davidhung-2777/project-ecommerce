<?php

namespace App\Services\Payment;

class BankTransferPayment implements PaymentInterface
{
    private array $config;

    public function __construct()
    {
        $paymentConfig  = require ROOT_PATH . '/config/payment.php';
        $this->config   = $paymentConfig['bank_transfer'];
    }

    public function createTransaction(array $order): array
    {
        $transferContent = 'SEVQR ' . $order['order_number'];
        $qrUrl = $this->generateVietQrUrl(
            $order['order_number'],
            $order['total_amount'],
            $transferContent
        );

        return [
            'success'        => true,
            'method'         => 'bank_transfer',
            'redirect_url'   => null,
            'qr_url'         => $qrUrl,
            'account_name'   => $this->config['account_name'],
            'account_number' => $this->config['account_number'],
            'bank_name'      => $this->config['bank_name'],
            'bank_branch'    => $this->config['bank_branch'],
            'transfer_content' => $transferContent,
            'amount'         => $order['total_amount'],
            'message'        => 'Vui lòng chuyển khoản với nội dung: ' . $transferContent,
        ];
    }

    /**
     * Bank transfer callback is handled manually by admin.
     * This endpoint is for when admin confirms transfer.
     */
    public function handleCallback(array $params): array
    {
        return [
            'success'  => true,
            'order_id' => (int) ($params['order_id'] ?? 0),
            'message'  => 'Xác nhận thanh toán chuyển khoản thủ công.',
        ];
    }

    public function checkStatus(array $order): array
    {
        $status = $order['payment_status'] ?? 'pending';
        return [
            'status'  => $status === 'paid' ? 'paid' : 'pending',
            'message' => $status === 'paid'
                ? 'Đã xác nhận chuyển khoản'
                : 'Chờ xác nhận chuyển khoản từ admin',
        ];
    }

    public function getMethodName(): string
    {
        return 'Chuyển khoản ngân hàng';
    }

    /**
     * Generate VietQR static QR URL.
     * Docs: https://vietqr.io/danh-sach-api/generate-qr/
     */
    private function generateVietQrUrl(string $orderNumber, float $amount, string $transferContent): string
    {
        // Using VietQR quick link format
        $bankId        = $this->config['bank_code'] ?: $this->getBankId($this->config['bank_name']);
        $accountNo     = $this->config['account_number'];
        $accountName   = urlencode($this->config['account_name']);
        $amountInt     = (int) round($amount);
        $addInfo       = urlencode($transferContent);

        return "https://img.vietqr.io/image/{$bankId}-{$accountNo}-compact2.png"
            . "?amount={$amountInt}&addInfo={$addInfo}&accountName={$accountName}";
    }

    private function getBankId(string $bankName): string
    {
        $map = [
            'Vietcombank'  => 'VCB',
            'Vietinbank'   => 'ICB',
            'BIDV'         => 'BIDV',
            'Agribank'     => 'VBA',
            'Techcombank'  => 'TCB',
            'MB Bank'      => 'MB',
            'Sacombank'    => 'STB',
            'ACB'          => 'ACB',
            'VPBank'       => 'VPB',
            'TPBank'       => 'TPB',
        ];
        return $map[$bankName] ?? 'VCB';
    }
}
