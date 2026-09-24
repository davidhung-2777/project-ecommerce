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
use App\Services\GhnShippingService;

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
    private GhnShippingService $ghnService;

    public function __construct()
    {
        $this->cartModel        = new CartModel();
        $this->cartItemModel    = new CartItemModel();
        $this->orderModel       = new OrderModel();
        $this->orderDetailModel = new OrderDetailModel();
        $this->paymentModel     = new PaymentModel();
        $this->userModel        = new UserModel();
        $this->bpModel          = new BusinessProfileModel();
        $this->ghnService       = new GhnShippingService();
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

        // Get GHN address data
        $districtId = (int) $this->post('district_id', 0);
        $wardCode = $this->post('ward_code', '');

        // Calculate totals
        $subtotal = $cartData['subtotal'];
        
        // Calculate shipping fee từ GHN API
        $shippingFee = $this->calculateShipping($subtotal, $districtId, $wardCode);
        
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
                // GHN address IDs
                'shipping_province_id' => $this->post('province_id'),
                'shipping_district_id' => $this->post('district_id'),
                'shipping_ward_code'   => $this->post('ward_code'),
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

            // Tạo đơn vận chuyển với GHN (chỉ tạo khi có đầy đủ thông tin địa chỉ)
            if ($districtId > 0 && !empty($wardCode)) {
                $shippingResult = $this->createGhnShippingOrder($orderId, $orderData, $cartData['items'], $paymentMethod);
                
                // Cập nhật shipping_code và expected_delivery nếu tạo đơn thành công
                if (!empty($shippingResult['order_code'])) {
                    $this->orderModel->update($orderId, [
                        'shipping_code' => $shippingResult['order_code'],
                        'expected_delivery' => $shippingResult['expected_delivery_time'] ?? null,
                        'shipping_status' => 'ready_to_pick',
                    ]);
                }
            }

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

    /**
     * Tính phí vận chuyển từ GHN API
     * @param float $subtotal Tổng tiền hàng
     * @param int $districtId Mã quận/huyện từ GHN
     * @param string $wardCode Mã phường/xã từ GHN
     * @return float Phí ship (VND)
     */
    private function calculateShipping(float $subtotal, int $districtId, string $wardCode): float
    {
        // Free shipping nếu đơn hàng >= 5 triệu
        if ($subtotal >= 5000000) {
            return 0;
        }

        // Nếu không có thông tin địa chỉ GHN, dùng phí mặc định
        if ($districtId <= 0 || empty($wardCode)) {
            return 50000;
        }

        // Tính phí từ GHN API
        $result = $this->ghnService->calculateShippingFee([
            'to_district_id' => $districtId,
            'to_ward_code' => $wardCode,
            'weight' => 5000, // Giả định trung bình 5kg
            'order_value' => (int) $subtotal,
        ]);

        // Nếu API lỗi, dùng phí mặc định
        if (!$result['success']) {
            return 50000;
        }

        return (float) ($result['fee'] ?? 50000);
    }

    /**
     * Tạo đơn vận chuyển với GHN
     */
    private function createGhnShippingOrder(int $orderId, array $orderData, array $cartItems, string $paymentMethod): array
    {
        // Chuẩn bị danh sách sản phẩm
        $items = [];
        $totalWeight = 0;

        foreach ($cartItems as $item) {
            $items[] = [
                'name' => $item['product_name'],
                'quantity' => (int) $item['quantity'],
                'price' => (int) $item['unit_price'],
            ];
            // Giả định mỗi sản phẩm nặng 1kg
            $totalWeight += (int) $item['quantity'] * 1000;
        }

        // Đảm bảo trọng lượng tối thiểu 1kg
        if ($totalWeight < 1000) {
            $totalWeight = 1000;
        }

        // Payment type: 1 = Shop trả phí ship, 2 = Khách trả phí ship (COD)
        $paymentTypeId = ($paymentMethod === 'cod') ? 2 : 1;

        // COD amount: chỉ có giá trị khi thanh toán COD
        $codAmount = ($paymentMethod === 'cod') ? (int) $orderData['total_amount'] : 0;

        // Tạo đơn vận chuyển
        $result = $this->ghnService->createShippingOrder([
            'to_name' => $orderData['shipping_name'],
            'to_phone' => $orderData['shipping_phone'],
            'to_address' => $orderData['shipping_address'],
            'to_ward_code' => $orderData['shipping_ward_code'],
            'to_district_id' => (int) $orderData['shipping_district_id'],
            'weight' => $totalWeight,
            'payment_type_id' => $paymentTypeId,
            'cod_amount' => $codAmount,
            'required_note' => 'CHOXEMHANGKHONGTHU', // Cho xem hàng không thử
            'items' => $items,
        ]);

        return $result;
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
