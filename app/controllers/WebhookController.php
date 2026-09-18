<?php

require_once __DIR__ . '/../core/Controller.php';
require_once __DIR__ . '/../services/payment/SepayService.php';
require_once __DIR__ . '/../models/OrderModel.php';
require_once __DIR__ . '/../models/PaymentModel.php';

/**
 * Webhook Controller
 * 
 * Xử lý webhook callbacks từ các payment gateway:
 * - SePay: Thông báo giao dịch chuyển khoản ngân hàng
 * - MoMo: IPN callback
 * - VNPay: IPN callback
 * 
 * @package App\Controllers
 */
class WebhookController extends Controller
{
    private SepayService $sepayService;
    private OrderModel $orderModel;
    private PaymentModel $paymentModel;

    public function __construct()
    {
        parent::__construct();
        $this->sepayService = new SepayService();
        $this->orderModel = new OrderModel();
        $this->paymentModel = new PaymentModel();
    }

    /**
     * SePay Webhook Handler
     * 
     * URL: /webhook/sepay
     * Method: POST
     * 
     * Nhận thông báo real-time từ SePay khi có giao dịch chuyển khoản mới
     * 
     * Expected payload:
     * {
     *   "id": "12345",
     *   "reference_number": "FT21234567890",
     *   "amount_in": 15000000,
     *   "transaction_content": "DH12345 Thanh toan don hang",
     *   "transaction_date": "2024-01-15 10:30:45",
     *   "account_number": "1058081721",
     *   "bank_brand_name": "VCB",
     *   "gateway": "VCB",
     *   "accumulated": 150000000
     * }
     */
    public function sepay(): void
    {
        // Log webhook request
        $this->logWebhook('sepay', 'Received webhook request');

        // Only accept POST requests
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            $this->jsonResponse([
                'success' => false,
                'message' => 'Method not allowed'
            ], 405);
            return;
        }

        // Get raw POST body
        $rawBody = file_get_contents('php://input');
        
        // Log raw body
        $this->logWebhook('sepay', 'Raw body: ' . $rawBody);

        // Parse JSON payload
        $payload = json_decode($rawBody, true);

        if (json_last_error() !== JSON_ERROR_NONE) {
            $this->logWebhook('sepay', 'Invalid JSON: ' . json_last_error_msg());
            $this->jsonResponse([
                'success' => false,
                'message' => 'Invalid JSON payload'
            ], 400);
            return;
        }

        // Verify webhook signature (if configured)
        $signature = $_SERVER['HTTP_X_SEPAY_SIGNATURE'] ?? '';
        
        if (!empty($_ENV['SEPAY_WEBHOOK_SECRET']) && !empty($signature)) {
            if (!$this->sepayService->verifyWebhookSignature($payload, $signature)) {
                $this->logWebhook('sepay', 'Invalid signature');
                $this->jsonResponse([
                    'success' => false,
                    'message' => 'Invalid signature'
                ], 401);
                return;
            }
        }

        // Parse transaction data
        $transaction = $this->sepayService->parseWebhookPayload($payload);

        $this->logWebhook('sepay', 'Parsed transaction: ' . json_encode($transaction));

        // Extract order code from description
        $orderCode = $this->extractOrderCode($transaction['description']);

        if (!$orderCode) {
            $this->logWebhook('sepay', 'No order code found in description: ' . $transaction['description']);
            $this->jsonResponse([
                'success' => false,
                'message' => 'Order code not found in transaction description'
            ], 400);
            return;
        }

        // Find order by code
        $order = $this->orderModel->findByOrderCode($orderCode);

        if (!$order) {
            $this->logWebhook('sepay', 'Order not found: ' . $orderCode);
            $this->jsonResponse([
                'success' => false,
                'message' => 'Order not found'
            ], 404);
            return;
        }

        $this->logWebhook('sepay', 'Found order: ' . $order['id']);

        // Check if payment already processed
        $existingPayment = $this->paymentModel->findByTransactionId($transaction['transaction_id']);

        if ($existingPayment) {
            $this->logWebhook('sepay', 'Transaction already processed: ' . $transaction['transaction_id']);
            $this->jsonResponse([
                'success' => true,
                'message' => 'Transaction already processed'
            ], 200);
            return;
        }

        // Verify amount matches order total
        if (abs($transaction['amount'] - $order['total_amount']) > 1) {
            $this->logWebhook('sepay', sprintf(
                'Amount mismatch. Expected: %s, Got: %s',
                $order['total_amount'],
                $transaction['amount']
            ));
            
            // Still record payment but mark as pending review
            $paymentStatus = 'pending_review';
            $orderStatus = 'pending';
        } else {
            $paymentStatus = 'completed';
            $orderStatus = 'confirmed';
        }

        // Record payment
        $paymentId = $this->paymentModel->create([
            'order_id' => $order['id'],
            'payment_method' => 'bank_transfer',
            'transaction_id' => $transaction['transaction_id'],
            'amount' => $transaction['amount'],
            'status' => $paymentStatus,
            'gateway_response' => json_encode($transaction['raw_data']),
            'paid_at' => $transaction['transaction_date']
        ]);

        if (!$paymentId) {
            $this->logWebhook('sepay', 'Failed to record payment');
            $this->jsonResponse([
                'success' => false,
                'message' => 'Failed to record payment'
            ], 500);
            return;
        }

        // Update order status
        $updated = $this->orderModel->updateStatus($order['id'], $orderStatus);

        if (!$updated) {
            $this->logWebhook('sepay', 'Failed to update order status');
        }

        $this->logWebhook('sepay', sprintf(
            'Payment recorded successfully. Order: %s, Payment: %s, Status: %s',
            $order['id'],
            $paymentId,
            $paymentStatus
        ));

        // Send notification email (optional)
        // $this->sendPaymentNotification($order, $transaction);

        // Respond to webhook
        $this->jsonResponse([
            'success' => true,
            'message' => 'Webhook processed successfully',
            'order_id' => $order['id'],
            'payment_id' => $paymentId,
            'status' => $paymentStatus
        ], 200);
    }

    /**
     * Extract order code from transaction description
     * 
     * Patterns supported:
     * - DH12345
     * - ORDER-12345
     * - #12345
     * - Ma don: 12345
     * 
     * @param string $description
     * @return string|null
     */
    private function extractOrderCode(string $description): ?string
    {
        $description = strtoupper($description);

        // Pattern 1: DH followed by digits
        if (preg_match('/DH(\d+)/', $description, $matches)) {
            return 'DH' . $matches[1];
        }

        // Pattern 2: ORDER- followed by digits
        if (preg_match('/ORDER-?(\d+)/', $description, $matches)) {
            return 'DH' . $matches[1];
        }

        // Pattern 3: # followed by digits
        if (preg_match('/#(\d+)/', $description, $matches)) {
            return 'DH' . $matches[1];
        }

        // Pattern 4: "Ma don" or "Ma DH" followed by digits
        if (preg_match('/MA\s*(?:DON|DH)[\s:]*(\d+)/', $description, $matches)) {
            return 'DH' . $matches[1];
        }

        return null;
    }

    /**
     * Log webhook activity
     * 
     * @param string $gateway
     * @param string $message
     * @return void
     */
    private function logWebhook(string $gateway, string $message): void
    {
        $logDir = __DIR__ . '/../../logs';
        if (!is_dir($logDir)) {
            mkdir($logDir, 0755, true);
        }

        $logFile = $logDir . '/webhook-' . date('Y-m-d') . '.log';
        $timestamp = date('Y-m-d H:i:s');
        $logMessage = "[{$timestamp}] [{$gateway}] {$message}" . PHP_EOL;

        file_put_contents($logFile, $logMessage, FILE_APPEND);
    }

    /**
     * Send JSON response
     * 
     * @param array $data
     * @param int $statusCode
     * @return void
     */
    private function jsonResponse(array $data, int $statusCode = 200): void
    {
        http_response_code($statusCode);
        header('Content-Type: application/json');
        echo json_encode($data, JSON_UNESCAPED_UNICODE);
        exit;
    }

    /**
     * Test webhook endpoint
     * 
     * URL: /webhook/sepay/test
     * Method: GET
     */
    public function test(): void
    {
        $this->jsonResponse([
            'success' => true,
            'message' => 'SePay webhook endpoint is working',
            'timestamp' => date('Y-m-d H:i:s'),
            'server' => [
                'PHP_VERSION' => phpversion(),
                'SERVER_SOFTWARE' => $_SERVER['SERVER_SOFTWARE'] ?? 'Unknown'
            ]
        ]);
    }
}
