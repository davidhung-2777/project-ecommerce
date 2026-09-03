<?php

namespace App\Controllers;

use App\Core\Controller;
use App\Models\OrderModel;
use App\Models\PaymentModel;
use App\Services\Payment\CodPayment;
use App\Services\Payment\BankTransferPayment;
use App\Services\Payment\MomoPayment;
use App\Services\Payment\VnpayPayment;
use App\Services\Payment\PaymentInterface;

class PaymentController extends Controller
{
    protected string $viewPath = 'frontend';

    private OrderModel $orderModel;
    private PaymentModel $paymentModel;

    public function __construct()
    {
        $this->orderModel   = new OrderModel();
        $this->paymentModel = new PaymentModel();
    }

    /**
     * Initiate payment for a pending order.
     */
    public function initiate(): void
    {
        $orderNumber = $this->get('order', '');
        $order       = $this->orderModel->findByOrderNumber($orderNumber);

        if (!$order || $order['payment_status'] !== 'pending') {
            $this->redirect($this->baseUrl());
        }

        $gateway = $this->getGateway($order['payment_method']);
        $result  = $gateway->createTransaction($order);

        if (!$result['success']) {
            $this->setFlash('error', $result['message'] ?? 'Lỗi khởi tạo thanh toán.');
            $this->redirect($this->baseUrl('checkout'));
        }

        if (!empty($result['redirect_url'])) {
            $this->redirect($result['redirect_url']);
        }

        $this->redirect($this->baseUrl('checkout/success/' . $orderNumber));
    }

    // ── MoMo ─────────────────────────────────────────────────────────────────

    public function momoReturn(): void
    {
        $params = $_GET;
        $momo   = new MomoPayment();

        // Verify signature
        if (!$momo->verifySignature($params)) {
            $this->redirect($this->baseUrl('?error=invalid_signature'));
            return;
        }

        $resultCode  = (int) ($params['resultCode'] ?? -1);
        $parts       = explode('_', $params['orderId'] ?? '');
        $orderNumber = $parts[0] ?? '';
        $order       = $this->orderModel->findByOrderNumber($orderNumber);

        if ($resultCode === 0 && $order) {
            $this->redirect($this->baseUrl('checkout/success/' . $orderNumber));
        } else {
            $this->setFlash('error', 'Thanh toán MoMo thất bại hoặc bị hủy.');
            $this->redirect($this->baseUrl('checkout'));
        }
    }

    /**
     * MoMo IPN (server-to-server) - ONLY this updates payment status.
     */
    public function momoIpn(): void
    {
        $params = json_decode(file_get_contents('php://input'), true) ?? [];
        $momo   = new MomoPayment();
        $result = $momo->handleCallback($params);

        if (!$result['success']) {
            echo json_encode(['resultCode' => 1, 'message' => $result['message']]);
            return;
        }

        $order = $this->orderModel->findByOrderNumber($result['order_number']);
        if ($order && $order['payment_status'] === 'pending') {
            $payment = $this->paymentModel->findByOrder($order['id']);
            if ($payment) {
                $this->paymentModel->markPaid($payment['id'], $result['raw'] ?? []);
            }
            $this->orderModel->update($order['id'], [
                'payment_status' => 'paid',
                'status'         => 'confirmed',
                'confirmed_at'   => date('Y-m-d H:i:s'),
            ]);
        }

        echo json_encode(['resultCode' => 0, 'message' => 'Xác nhận thành công.']);
    }

    // ── VNPay ────────────────────────────────────────────────────────────────

    public function vnpayReturn(): void
    {
        $params = $_GET;
        $vnpay  = new VnpayPayment();

        if (!$vnpay->verifySignature($params)) {
            $this->redirect($this->baseUrl('?error=invalid_signature'));
            return;
        }

        $responseCode = $params['vnp_ResponseCode'] ?? '99';
        $parts        = explode('_', $params['vnp_TxnRef'] ?? '');
        $orderNumber  = $parts[0] ?? '';
        $order        = $this->orderModel->findByOrderNumber($orderNumber);

        if ($responseCode === '00' && $order) {
            $this->redirect($this->baseUrl('checkout/success/' . $orderNumber));
        } else {
            $this->setFlash('error', 'Thanh toán VNPay thất bại hoặc bị hủy.');
            $this->redirect($this->baseUrl('checkout'));
        }
    }

    /**
     * VNPay IPN (server-to-server) - ONLY this updates payment status.
     */
    public function vnpayIpn(): void
    {
        $params = $_GET;
        $vnpay  = new VnpayPayment();
        $result = $vnpay->handleCallback($params);

        if (!$result['success']) {
            echo json_encode(['RspCode' => '97', 'Message' => 'Invalid signature']);
            return;
        }

        $order = $this->orderModel->findByOrderNumber($result['order_number']);
        if (!$order) {
            echo json_encode(['RspCode' => '01', 'Message' => 'Order not found']);
            return;
        }

        if ($order['payment_status'] !== 'pending') {
            echo json_encode(['RspCode' => '02', 'Message' => 'Order already updated']);
            return;
        }

        $payment = $this->paymentModel->findByOrder($order['id']);
        if ($payment) {
            $this->paymentModel->markPaid($payment['id'], $result['raw'] ?? []);
        }

        $this->orderModel->update($order['id'], [
            'payment_status' => 'paid',
            'status'         => 'confirmed',
            'confirmed_at'   => date('Y-m-d H:i:s'),
        ]);

        echo json_encode(['RspCode' => '00', 'Message' => 'Confirm Success']);
    }

    public function bankInfo(string $orderNumber): void
    {
        $order = $this->orderModel->findByOrderNumber($orderNumber);
        if (!$order) {
            $this->json(['error' => 'Order not found'], 404);
        }
        $gateway = new BankTransferPayment();
        $info    = $gateway->createTransaction($order);
        $this->json($info);
    }

    // ── Gateway factory ───────────────────────────────────────────────────────

    private function getGateway(string $method): PaymentInterface
    {
        return match ($method) {
            'cod'           => new CodPayment(),
            'bank_transfer' => new BankTransferPayment(),
            'momo'          => new MomoPayment(),
            'vnpay'         => new VnpayPayment(),
            default         => throw new \InvalidArgumentException("Unknown payment method: {$method}"),
        };
    }
}
