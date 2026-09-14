<?php

namespace App\Controllers;

use App\Core\Controller;
use App\Models\CartModel;
use App\Models\CartItemModel;
use App\Models\OrderModel;
use App\Models\OrderDetailModel;
use App\Models\PaymentModel;
use App\Models\UserModel;
use App\Models\BusinessProfileModel;

class CheckoutController extends Controller
{
    protected string $viewPath = 'frontend';

    private CartModel $cartModel;
    private CartItemModel $cartItemModel;
    private OrderModel $orderModel;
    private OrderDetailModel $orderDetailModel;
    private PaymentModel $paymentModel;
    private UserModel $userModel;
    private BusinessProfileModel $bpModel;

    public function __construct()
    {
        $this->cartModel        = new CartModel();
        $this->cartItemModel    = new CartItemModel();
        $this->orderModel       = new OrderModel();
        $this->orderDetailModel = new OrderDetailModel();
        $this->paymentModel     = new PaymentModel();
        $this->userModel        = new UserModel();
        $this->bpModel          = new BusinessProfileModel();
    }

    public function index(): void
    {
        $this->requireAuth();

        $cart = $this->getCart();
        if (!$cart) {
            $this->redirect($this->baseUrl('cart'));
        }

        $cartData = $this->cartModel->getCartWithItems($cart['id']);
        if (empty($cartData['items'])) {
            $this->redirect($this->baseUrl('cart'));
        }

        $user            = null;
        $businessProfile = null;

        if (!empty($_SESSION['user_id'])) {
            $user = $this->userModel->find((int) $_SESSION['user_id']);
            if ($user && $user['account_type'] === 'business') {
                $businessProfile = $this->bpModel->findByUserId($user['id']);
            }
        }

        $this->view('pages/checkout', compact('cartData', 'user', 'businessProfile'));
    }

    public function process(): void
    {
        $this->requireAuth();

        if (!$this->isPost()) {
            $this->redirect($this->baseUrl('checkout'));
        }

        $cart = $this->getCart();
        if (!$cart) {
            $this->redirect($this->baseUrl('cart'));
        }

        $cartData = $this->cartModel->getCartWithItems($cart['id']);
        if (empty($cartData['items'])) {
            $this->redirect($this->baseUrl('cart'));
        }

        // Validate input
        $errors = $this->validateCheckout($_POST);
        if ($errors) {
            $_SESSION['checkout_errors'] = $errors;
            $_SESSION['checkout_input']  = $_POST;
            $this->redirect($this->baseUrl('checkout'));
        }

        $paymentMethod = $this->post('payment_method');
        $invoiceType   = $this->post('invoice_type', 'retail');

        // Calculate totals
        $subtotal     = $cartData['subtotal'];
        $shippingFee  = $this->calculateShipping($subtotal);
        $taxAmount    = $invoiceType === 'vat' ? round($subtotal * 0.10, 2) : 0;
        $totalAmount  = $subtotal + $shippingFee + $taxAmount;

        $db = \App\Core\Database::getInstance();
        $db->beginTransaction();

        try {
            // Create order
            $orderData = [
                'user_id'         => $_SESSION['user_id'] ?? null,
                'order_number'    => $this->orderModel->generateOrderNumber(),
                'status'          => 'pending',
                'invoice_type'    => $invoiceType,
                'shipping_name'   => $this->post('shipping_name'),
                'shipping_phone'  => $this->post('shipping_phone'),
                'shipping_address'=> $this->post('shipping_address'),
                'shipping_city'   => $this->post('shipping_city'),
                'shipping_district'=> $this->post('shipping_district'),
                'shipping_ward'   => $this->post('shipping_ward'),
                'subtotal'        => $subtotal,
                'shipping_fee'    => $shippingFee,
                'tax_amount'      => $taxAmount,
                'total_amount'    => $totalAmount,
                'payment_method'  => $paymentMethod,
                'payment_status'  => 'pending',
                'customer_note'   => $this->post('customer_note', ''),
            ];

            if ($invoiceType === 'vat') {
                $orderData['vat_company_name'] = $this->post('vat_company_name');
                $orderData['vat_tax_code']     = $this->post('vat_tax_code');
                $orderData['vat_address']      = $this->post('vat_address');
                $orderData['vat_email']        = $this->post('vat_email');
            }

            $orderId = $this->orderModel->create($orderData);

            // Create order details
            $this->orderDetailModel->createFromCartItems($orderId, $cartData['items']);

            // Create payment record
            $this->paymentModel->createPayment($orderId, $paymentMethod, $totalAmount);

            // Clear cart
            foreach ($cartData['items'] as $item) {
                $this->cartItemModel->delete($item['id']);
            }

            $db->commit();

            // Store order for payment processing
            $_SESSION['pending_order_id'] = $orderId;

            $order = $this->orderModel->find($orderId);

            // For COD/bank transfer, go directly to success/info page
            if (in_array($paymentMethod, ['cod', 'bank_transfer'])) {
                $this->redirect($this->baseUrl('checkout/success/' . $order['order_number']));
            }

            // For MoMo/VNPay, redirect to payment initiate
            $this->redirect($this->baseUrl('payment/initiate?order=' . $order['order_number']));

        } catch (\Throwable $e) {
            $db->rollback();
            $this->setFlash('error', 'Có lỗi xảy ra khi tạo đơn hàng. Vui lòng thử lại.');
            $this->redirect($this->baseUrl('checkout'));
        }
    }

    public function success(string $orderNumber): void
    {
        $order = $this->orderModel->findByOrderNumber($orderNumber);
        if (!$order) {
            $this->redirect($this->baseUrl());
        }

        // Check ownership
        if (!empty($order['user_id']) && $order['user_id'] != ($_SESSION['user_id'] ?? 0)) {
            $this->redirect($this->baseUrl());
        }

        $order = $this->orderModel->getFullOrder($order['id']);

        $bankInfo = null;
        if ($order['payment_method'] === 'bank_transfer') {
            $bankTransfer = new \App\Services\Payment\BankTransferPayment();
            $bankInfo     = $bankTransfer->createTransaction($order);
        }

        $this->view('pages/checkout-success', compact('order', 'bankInfo'));
    }

    // ── Helpers ───────────────────────────────────────────────────────────────

    private function getCart(): ?array
    {
        if (!empty($_SESSION['user_id'])) {
            return $this->cartModel->getOrCreateForUser((int) $_SESSION['user_id']);
        }
        if (!empty($_SESSION['cart_session_id'])) {
            return $this->cartModel->getOrCreateForGuest($_SESSION['cart_session_id']);
        }
        return null;
    }

    private function calculateShipping(float $subtotal): float
    {
        if ($subtotal >= 5000000) return 0; // Free shipping >= 5M VND
        return 50000;
    }

    private function validateCheckout(array $data): array
    {
        $errors = [];
        if (empty($data['shipping_name']))    $errors[] = 'Vui lòng nhập họ tên.';
        if (empty($data['shipping_phone']))   $errors[] = 'Vui lòng nhập số điện thoại.';
        if (empty($data['shipping_address'])) $errors[] = 'Vui lòng nhập địa chỉ giao hàng.';
        if (empty($data['payment_method']))   $errors[] = 'Vui lòng chọn phương thức thanh toán.';

        $validMethods = ['cod', 'bank_transfer', 'momo', 'vnpay'];
        if (!in_array($data['payment_method'] ?? '', $validMethods)) {
            $errors[] = 'Phương thức thanh toán không hợp lệ.';
        }

        if (($data['invoice_type'] ?? '') === 'vat') {
            if (empty($data['vat_company_name'])) $errors[] = 'Vui lòng nhập tên công ty xuất hóa đơn.';
            if (empty($data['vat_tax_code']))      $errors[] = 'Vui lòng nhập mã số thuế.';
        }

        return $errors;
    }
}
