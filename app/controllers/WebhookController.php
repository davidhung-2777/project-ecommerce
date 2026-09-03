<?php

namespace App\Controllers;

use App\Core\Controller;
use App\Core\Database;
use App\Models\OrderModel;
use App\Models\OrderDetailModel;
use App\Models\ProductModel;
use App\Services\Payment\VietQRPayment;

/**
 * Webhook Controller
 * Xử lý webhook từ cổng thanh toán (SePay/Casso)
 * 
 * QUAN TRỌNG VỀ BẢO MẬT:
 * - Luôn xác thực chữ ký (signature)
 * - Idempotent: không xử lý lại webhook đã processed
 * - Log tất cả webhook vào database
 * - Không trust dữ liệu từ client
 */
class WebhookController extends Controller
{
    private OrderModel $orderModel;
    private OrderDetailModel $orderDetailModel;
    private ProductModel $productModel;
    private VietQRPayment $vietQR;
    private Database $db;
    
    public function __construct()
    {
        $this->orderModel = new OrderModel();
        $this->orderDetailModel = new OrderDetailModel();
        $this->productModel = new ProductModel();
        $this->vietQR = new VietQRPayment();
        $this->db = Database::getInstance();
    }
    
    /**
     * Endpoint nhận webhook từ SePay/Casso
     * POST /api/webhook/payment
     */
    public function handlePayment(): void
    {
        // Get raw payload
        $rawPayload = file_get_contents('php://input');
        $payload = json_decode($rawPayload, true);
        
        if (!$payload) {
            $this->respondWebhook(400, ['error' => 'Invalid JSON payload']);
            return;
        }
        
        // Log webhook vào database NGAY LẬP TỨC
        $logId = $this->logWebhook($payload, $rawPayload);
        
        try {
            // ===== BƯỚC 1: XÁC THỰC CHỮ KÝ =====
            if (!$this->vietQR->verifyWebhookSignature($payload)) {
                $this->updateWebhookLog($logId, [
                    'signature_valid' => 0,
                    'error_message' => 'Invalid signature',
                    'processed' => 1
                ]);
                
                // Vẫn trả 200 để tránh bị gọi lại liên tục
                $this->respondWebhook(200, ['status' => 'rejected', 'reason' => 'Invalid signature']);
                return;
            }
            
            // ===== BƯỚC 2: PARSE DỮ LIỆU =====
            $result = $this->vietQR->handleCallback($payload);
            
            if (!$result['success']) {
                $this->updateWebhookLog($logId, [
                    'signature_valid' => 1,
                    'error_message' => $result['message'] ?? 'Parse failed',
                    'processed' => 1
                ]);
                
                $this->respondWebhook(200, ['status' => 'error', 'message' => $result['message']]);
                return;
            }
            
            $orderCode = $result['order_code'];
            $amount = $result['amount'];
            $transactionId = $result['transaction_id'] ?? '';
            
            // ===== BƯỚC 3: TÌM ORDER =====
            $order = $this->orderModel->findOneWhere('order_code = ?', [$orderCode]);
            
            if (!$order) {
                // Không tìm thấy order → log lại nhưng VẪN TRẢ 200
                $this->updateWebhookLog($logId, [
                    'signature_valid' => 1,
                    'order_code' => $orderCode,
                    'error_message' => 'Order not found',
                    'processed' => 1
                ]);
                
                $this->respondWebhook(200, ['status' => 'ok', 'message' => 'Order not found, logged']);
                return;
            }
            
            // Update log với order_id
            $this->updateWebhookLog($logId, [
                'signature_valid' => 1,
                'order_id' => $order['id'],
                'order_code' => $orderCode
            ]);
            
            // ===== BƯỚC 4: KIỂM TRA IDEMPOTENT =====
            if ($order['payment_status'] === 'paid') {
                // Đã thanh toán rồi → bỏ qua, trả 200
                $this->updateWebhookLog($logId, [
                    'processed' => 1,
                    'error_message' => 'Already paid (idempotent)'
                ]);
                
                $this->respondWebhook(200, ['status' => 'ok', 'message' => 'Already processed']);
                return;
            }
            
            // ===== BƯỚC 5: KIỂM TRA HẾT HẠN =====
            if ($order['expires_at'] && strtotime($order['expires_at']) < time()) {
                // Hết hạn → set expired
                $this->db->beginTransaction();
                
                $this->orderModel->update($order['id'], [
                    'status' => 'expired',
                    'admin_note' => "Đơn hết hạn nhưng vẫn nhận được webhook. Transaction: {$transactionId}"
                ]);
                
                $this->updateWebhookLog($logId, [
                    'processed' => 1,
                    'error_message' => 'Order expired'
                ]);
                
                $this->db->commit();
                
                $this->respondWebhook(200, ['status' => 'expired', 'message' => 'Order expired']);
                return;
            }
            
            // ===== BƯỚC 6: SO SÁNH SỐ TIỀN =====
            $expectedAmount = (float) $order['total_amount'];
            $receivedAmount = (float) $amount;
            
            // Cho phép sai số ±1000 VND do làm tròn
            $amountDiff = abs($expectedAmount - $receivedAmount);
            
            if ($amountDiff > 1000) {
                // Số tiền KHÔNG KHỚP → set needs_review
                $this->db->beginTransaction();
                
                $this->orderModel->update($order['id'], [
                    'status' => 'needs_review',
                    'admin_note' => "Số tiền không khớp. Mong đợi: {$expectedAmount}, nhận được: {$receivedAmount}. Transaction: {$transactionId}"
                ]);
                
                $this->updateWebhookLog($logId, [
                    'processed' => 1,
                    'error_message' => "Amount mismatch: expected {$expectedAmount}, got {$receivedAmount}"
                ]);
                
                $this->db->commit();
                
                $this->respondWebhook(200, [
                    'status' => 'needs_review',
                    'message' => 'Amount mismatch',
                    'expected' => $expectedAmount,
                    'received' => $receivedAmount
                ]);
                return;
            }
            
            // ===== BƯỚC 7: SET PAID & TRỪ TỒN KHO =====
            $this->db->beginTransaction();
            
            try {
                // Update order status
                $this->orderModel->update($order['id'], [
                    'status' => 'confirmed',
                    'payment_status' => 'paid',
                    'paid_at' => date('Y-m-d H:i:s'),
                    'confirmed_at' => date('Y-m-d H:i:s'),
                    'admin_note' => "Thanh toán tự động qua VietQR. Transaction: {$transactionId}"
                ]);
                
                // Trừ tồn kho
                $orderDetails = $this->orderDetailModel->getByOrderId($order['id']);
                foreach ($orderDetails as $detail) {
                    $this->productModel->decrementStock($detail['product_id'], $detail['quantity']);
                }
                
                // Update webhook log
                $this->updateWebhookLog($logId, [
                    'processed' => 1,
                    'processed_at' => date('Y-m-d H:i:s')
                ]);
                
                $this->db->commit();
                
                // TODO: Gửi email xác nhận đơn hàng
                // TODO: Gửi notification cho admin
                
                $this->respondWebhook(200, [
                    'status' => 'success',
                    'message' => 'Payment confirmed',
                    'order_code' => $orderCode
                ]);
                
            } catch (\Exception $e) {
                $this->db->rollback();
                
                $this->updateWebhookLog($logId, [
                    'error_message' => $e->getMessage()
                ]);
                
                // Trả 500 để provider retry
                $this->respondWebhook(500, ['status' => 'error', 'message' => 'Internal error']);
            }
            
        } catch (\Exception $e) {
            // Log exception
            error_log("Webhook error: " . $e->getMessage());
            
            if (isset($logId)) {
                $this->updateWebhookLog($logId, [
                    'error_message' => $e->getMessage()
                ]);
            }
            
            $this->respondWebhook(500, ['status' => 'error', 'message' => 'Internal server error']);
        }
    }
    
    /**
     * Log webhook vào database
     */
    private function logWebhook(array $payload, string $rawPayload): int
    {
        return (int) $this->db->insert('payment_webhook_logs', [
            'raw_payload' => $rawPayload,
            'signature_valid' => 0,
            'processed' => 0,
            'received_at' => date('Y-m-d H:i:s')
        ]);
    }
    
    /**
     * Update webhook log
     */
    private function updateWebhookLog(int $logId, array $data): void
    {
        $this->db->update('payment_webhook_logs', $data, 'id = ?', [$logId]);
    }
    
    /**
     * Trả response cho webhook provider
     */
    private function respondWebhook(int $statusCode, array $data): void
    {
        http_response_code($statusCode);
        header('Content-Type: application/json; charset=utf-8');
        echo json_encode($data, JSON_UNESCAPED_UNICODE);
        exit;
    }
}
