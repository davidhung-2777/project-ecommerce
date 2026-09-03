<?php

namespace App\Services\Payment;

class VnpayPayment implements PaymentInterface
{
    private array $config;

    public function __construct()
    {
        $paymentConfig = require ROOT_PATH . '/config/payment.php';
        $this->config  = $paymentConfig['vnpay'];
    }

    public function createTransaction(array $order): array
    {
        $tmnCode    = $this->config['tmn_code'];
        $hashSecret = $this->config['hash_secret'];
        $txnRef     = $order['order_number'] . '_' . time();
        $amount     = (int) round($order['total_amount']) * 100; // VNPay uses smallest unit (×100)
        $createDate = date('YmdHis');
        $ipAddr     = $_SERVER['REMOTE_ADDR'] ?? '127.0.0.1';

        $params = [
            'vnp_Version'    => '2.1.0',
            'vnp_Command'    => 'pay',
            'vnp_TmnCode'    => $tmnCode,
            'vnp_Amount'     => $amount,
            'vnp_CreateDate' => $createDate,
            'vnp_CurrCode'   => 'VND',
            'vnp_IpAddr'     => $ipAddr,
            'vnp_Locale'     => 'vn',
            'vnp_OrderInfo'  => 'Thanh toan don hang ' . $order['order_number'],
            'vnp_OrderType'  => 'other',
            'vnp_ReturnUrl'  => $this->config['return_url'],
            'vnp_TxnRef'     => $txnRef,
            'vnp_ExpireDate' => date('YmdHis', strtotime('+15 minutes')),
        ];

        ksort($params);
        $hashData  = http_build_query($params, '', '&', PHP_QUERY_RFC3986);
        $signature = hash_hmac('sha512', $hashData, $hashSecret);

        $params['vnp_SecureHash'] = $signature;

        $payUrl = $this->config['url'] . '?' . http_build_query($params, '', '&', PHP_QUERY_RFC3986);

        return [
            'success'      => true,
            'method'       => 'vnpay',
            'redirect_url' => $payUrl,
            'txn_ref'      => $txnRef,
            'message'      => 'Chuyển hướng đến VNPay...',
        ];
    }

    /**
     * Handle IPN from VNPay server.
     * Security: verify HMAC-SHA512 before updating order status.
     */
    public function handleCallback(array $params): array
    {
        if (!$this->verifySignature($params)) {
            return ['success' => false, 'order_id' => 0, 'message' => 'Chữ ký không hợp lệ.'];
        }

        $responseCode = $params['vnp_ResponseCode'] ?? '99';
        $txnRef       = $params['vnp_TxnRef'] ?? '';

        // Extract order number (format: ORDER_NUMBER_timestamp)
        $parts       = explode('_', $txnRef);
        $orderNumber = $parts[0] ?? '';

        if ($responseCode !== '00') {
            return [
                'success'      => false,
                'order_number' => $orderNumber,
                'order_id'     => 0,
                'message'      => 'Thanh toán VNPay thất bại. Mã lỗi: ' . $responseCode,
                'raw'          => $params,
            ];
        }

        return [
            'success'        => true,
            'order_number'   => $orderNumber,
            'order_id'       => 0, // Resolved by controller
            'transaction_id' => $params['vnp_TransactionNo'] ?? '',
            'message'        => 'Thanh toán VNPay thành công.',
            'raw'            => $params,
        ];
    }

    public function checkStatus(array $order): array
    {
        $status = $order['payment_status'] ?? 'pending';
        return [
            'status'  => $status === 'paid' ? 'paid' : 'pending',
            'message' => $status === 'paid' ? 'Đã thanh toán qua VNPay' : 'Chờ thanh toán VNPay',
        ];
    }

    public function getMethodName(): string
    {
        return 'VNPay';
    }

    /**
     * Verify HMAC-SHA512 signature from VNPay.
     */
    public function verifySignature(array $params): bool
    {
        $hashSecret     = $this->config['hash_secret'];
        $receivedHash   = $params['vnp_SecureHash'] ?? '';

        // Remove signature fields before hashing
        $filtered = array_filter($params, fn($k) => !in_array($k, ['vnp_SecureHash', 'vnp_SecureHashType']), ARRAY_FILTER_USE_KEY);
        ksort($filtered);

        $hashData = http_build_query($filtered, '', '&', PHP_QUERY_RFC3986);
        $expected = hash_hmac('sha512', $hashData, $hashSecret);

        return hash_equals($expected, $receivedHash);
    }
}
