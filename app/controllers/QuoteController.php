<?php

namespace App\Controllers;

use App\Core\Controller;
use App\Models\QuoteModel;
use App\Models\QuoteItemModel;
use App\Models\ProductModel;
use App\Models\OrderModel;
use App\Models\OrderDetailModel;

class QuoteController extends Controller
{
    protected string $viewPath = 'frontend';

    private QuoteModel $quoteModel;
    private QuoteItemModel $quoteItemModel;
    private ProductModel $productModel;
    private OrderModel $orderModel;
    private OrderDetailModel $orderDetailModel;

    public function __construct()
    {
        $this->quoteModel       = new QuoteModel();
        $this->quoteItemModel   = new QuoteItemModel();
        $this->productModel     = new ProductModel();
        $this->orderModel       = new OrderModel();
        $this->orderDetailModel = new OrderDetailModel();
    }

    public function index(): void
    {
        $this->requireAuth();

        $user = $_SESSION['user_type'] ?? 'individual';
        if ($user !== 'business') {
            $this->setFlash('error', 'Chức năng báo giá chỉ dành cho tài khoản doanh nghiệp.');
            $this->redirect($this->baseUrl());
        }

        $products = $this->productModel->findAll('name ASC', 100);
        $this->view('pages/quote-request', compact('products'));
    }

    public function submit(): void
    {
        $this->requireAuth();

        if (!$this->isPost()) {
            $this->redirect($this->baseUrl('quote'));
        }

        $items        = $_POST['items'] ?? [];
        $customerNote = $this->post('customer_note', '');

        // Validate at least one item
        $validItems = array_filter($items, fn($i) => !empty($i['product_id']) && !empty($i['quantity']));
        if (empty($validItems)) {
            $this->setFlash('error', 'Vui lòng thêm ít nhất một sản phẩm vào yêu cầu báo giá.');
            $this->redirect($this->baseUrl('quote'));
        }

        $quoteId = $this->quoteModel->create([
            'user_id'       => (int) $_SESSION['user_id'],
            'quote_number'  => $this->quoteModel->generateQuoteNumber(),
            'status'        => 'pending',
            'customer_note' => $customerNote,
        ]);

        $this->quoteItemModel->createFromArray($quoteId, $validItems);

        $this->setFlash('success', 'Yêu cầu báo giá đã được gửi thành công. Chúng tôi sẽ phản hồi trong 24 giờ.');
        $this->redirect($this->baseUrl('quote/' . $quoteId));
    }

    public function show(string $id): void
    {
        $this->requireAuth();
        $quote = $this->quoteModel->getFullQuote((int) $id);

        if (!$quote || $quote['user_id'] != $_SESSION['user_id']) {
            $this->redirect($this->baseUrl('user/quotes'));
        }

        $this->view('pages/quote-detail', compact('quote'));
    }

    /**
     * Accept quote and convert to order.
     */
    public function accept(string $id): void
    {
        $this->requireAuth();
        $quote = $this->quoteModel->getFullQuote((int) $id);

        if (!$quote || $quote['user_id'] != $_SESSION['user_id'] || $quote['status'] !== 'responded') {
            $this->json(['success' => false, 'message' => 'Yêu cầu không hợp lệ.']);
        }

        // Create order from quote
        $subtotal = 0;
        foreach ($quote['items'] as $item) {
            $subtotal += ($item['unit_price'] ?? $item['list_price']) * $item['quantity'];
        }

        $orderId = $this->orderModel->create([
            'user_id'          => $quote['user_id'],
            'quote_id'         => $quote['id'],
            'order_number'     => $this->orderModel->generateOrderNumber(),
            'status'           => 'confirmed',
            'invoice_type'     => 'vat',
            'shipping_name'    => $quote['user']['name'] ?? '',
            'shipping_phone'   => $quote['user']['phone'] ?? '',
            'shipping_address' => $quote['user']['bp']['invoice_address'] ?? '',
            'subtotal'         => $subtotal,
            'total_amount'     => $subtotal,
            'payment_method'   => 'bank_transfer',
            'payment_status'   => 'pending',
            'customer_note'    => 'Chuyển đổi từ báo giá ' . $quote['quote_number'],
        ]);

        foreach ($quote['items'] as $item) {
            $this->orderDetailModel->create([
                'order_id'     => $orderId,
                'product_id'   => $item['product_id'],
                'product_name' => $item['product_name'],
                'product_sku'  => $item['sku'] ?? null,
                'product_image'=> $item['thumbnail'] ?? null,
                'quantity'     => $item['quantity'],
                'unit_price'   => $item['unit_price'] ?? $item['list_price'],
                'subtotal'     => ($item['unit_price'] ?? $item['list_price']) * $item['quantity'],
            ]);
        }

        $this->quoteModel->update($quote['id'], [
            'status'             => 'converted',
            'converted_order_id' => $orderId,
        ]);

        $order = $this->orderModel->find($orderId);
        $this->json(['success' => true, 'order_number' => $order['order_number']]);
    }
}
