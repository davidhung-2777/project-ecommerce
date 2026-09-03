<?php

namespace App\Services\Payment;

class MomoPayment implements PaymentInterface
{
    private array $config;

    public function __construct()
    {
        $paymentConfig = require ROOT_PATH . '/config/payment.php';
        $this->config  = $paymentConfig['momo'];
    }

    public function createTransaction(array $order): array
    {
        $partnerCode = $this->config['partner_code'];
        $accessKey   = $this->config['access_key'];
        $secretKey   = $this->config['secret_key'];
        $requestId   = $partnerCode . time();
        $orderId     = $order['order_number'] . '_' . time();
        $orderInfo   = 'Thanh toán đơn hàng ' . $order['order_number'];
        $amount      = (string) (int) round($order['total_amount']);
        $returnUrl   = $this->config['return_url'];
        $notifyUrl   = $this->config['ipn_url'];
        $requestType = 'payWithATM';
        $extraData   = base64_encode(json_encode(['order_id' => $order['id']]));

        // Build raw hash string
        $rawHash = "accessKey={$accessKey}"
            . "&amount={$amount}"
            . "&extraData={$extraData}"
            . "&ipnUrl={$notifyUrl}"
            . "&orderId={$orderId}"
            . "&orderInfo={$orderInfo}"
            . "&partnerCode={$partnerCode}"
            . "&redirectUrl={$returnUrl}"
            . "&requestId={$requestId}"
            . "&requestType={$requestType}";

        $signature = hash_hmac('sha256', $rawHash, $secretKey);

        $body = [
            'partnerCode' => $partnerCode,
            'accessKey'   => $accessKey,
            'requestId'   => $requestId,
            'amount'      => $amount,
            'orderId'     => $orderId,
            'orderInfo'   => $orderInfo,
            'returnUrl'   => $returnUrl,
            'ipnUrl'      => $notifyUrl,
            'extraData'   => $extraData,
            'requestType' => $requestType,
            'signature'   => $signature,
            'lang'        => 'vi',
        ];

        $response = $this->callApi($this->config['endpoint'], $body);

        if (empty($response['payUrl'])) {
            return [
                'success' => false,
                'message' => $response['message'] ?? 'Không thể kết nối MoMo.',
            ];
        }

        return [
            'success'      => true,
            'method'       => 'momo',
            'redirect_url' => $response['payUrl'],
            'momo_order_id'=> $orderId,
            'message'      => 'Chuyển hướng đến MoMo...',
        ];
    }

    /**
     * Verify HMAC-SHA256 signature and process IPN.
     */
    public function handleCallback(array $params): array
    {
        // Verify signature
        if (!$this->verifySignature($params)) {
            return ['success' => false, 'order_id' => 0, 'message' => 'Chữ ký không hợp lệ.'];
        }

        $resultCode = (int) ($params['resultCode'] ?? -1);
        $orderId    = $params['orderId'] ?? '';

        // Extract original order number (format: ORDER_NUMBER_timestamp)
        $parts       = explode('_', $orderId);
        $orderNumber = $parts[0] ?? '';

        if ($resultCode !== 0) {
            return [
                'success'      => false,
                'order_number' => $orderNumber,
                'order_id'     => 0,
                'message'      => 'Thanh toán MoMo thất bại. ResultCode: ' . $resultCode,
            ];
        }

        return [
            'success'      => true,
            'order_number' => $orderNumber,
            'order_id'     => 0, // Will be resolved by controller
            'transaction_id'=> $params['transId'] ?? '',
            'message'      => 'Thanh toán MoMo thành công.',
            'raw'          => $params,
        ];
    }

    public function checkStatus(array $order): array
    {
        $status = $order['payment_status'] ?? 'pending';
        return [
            'status'  => in_array($status, ['paid']) ? 'paid' : 'pending',
            'message' => $status === 'paid' ? 'Đã thanh toán qua MoMo' : 'Chờ thanh toán MoMo',
        ];
    }

    public function getMethodName(): string
    {
        return 'Ví MoMo';
    }

    /**
     * Verify HMAC-SHA256 signature from MoMo IPN/return.
     */
    public function verifySignature(array $params): bool
    {
        $secretKey = $this->config['secret_key'];
        $accessKey = $this->config['access_key'];

        $rawHash = "accessKey={$accessKey}"
            . "&amount={$params['amount']}"
            . "&extraData={$params['extraData']}"
            . "&message={$params['message']}"
            . "&orderId={$params['orderId']}"
            . "&orderInfo={$params['orderInfo']}"
            . "&orderType={$params['orderType']}"
            . "&partnerCode={$params['partnerCode']}"
            . "&payType={$params['payType']}"
            . "&requestId={$params['requestId']}"
            . "&responseTime={$params['responseTime']}"
            . "&resultCode={$params['resultCode']}"
            . "&transId={$params['transId']}";

        $expectedSignature = hash_hmac('sha256', $rawHash, $secretKey);
        return hash_equals($expectedSignature, $params['signature'] ?? '');
    }

    private function callApi(string $url, array $data): array
    {
        $jsonData = json_encode($data);
        $ch = curl_init($url);
        curl_setopt_array($ch, [
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_POST           => true,
            CURLOPT_POSTFIELDS     => $jsonData,
            CURLOPT_HTTPHEADER     => ['Content-Type: application/json', 'Content-Length: ' . strlen($jsonData)],
            CURLOPT_TIMEOUT        => 30,
            CURLOPT_SSL_VERIFYPEER => true,
        ]);
        $response = curl_exec($ch);
        curl_close($ch);
        return json_decode($response ?: '{}', true) ?? [];
    }
}
