<?php

namespace App\Controllers;

use App\Core\Controller;
use App\Models\ProductModel;
use App\Models\VoucherModel;
use App\Services\VoucherService;

class VoucherController extends Controller
{
    private VoucherModel $voucherModel;
    private VoucherService $voucherService;
    private ProductModel $productModel;

    public function __construct()
    {
        $this->voucherModel = new VoucherModel();
        $this->voucherService = new VoucherService();
        $this->productModel = new ProductModel();
    }

    public function available(): void
    {
        $this->requireAuth();
        $productId = (int) $this->get('product_id', 0);
        $product = $productId > 0 ? $this->productModel->findActiveById($productId) : false;
        $items = $product ? [['product_id' => $productId, 'quantity' => 1, 'unit_price' => (float) $this->productModel->getEffectivePrice($productId, 1)]] : [];
        $result = $this->voucherModel->getAll('', '', 1, 1000);
        $available = [];
        foreach ($result['data'] as $voucher) {
            $check = $this->voucherService->validateVoucher($voucher, $items, (int) $_SESSION['user_id']);
            if ($check['success']) {
                $available[] = [
                    'code' => $voucher['code'],
                    'description' => $voucher['description'],
                    'discount_type' => $voucher['discount_type'],
                    'discount_value' => $voucher['discount_value'],
                ];
            }
        }
        $this->json(['success' => true, 'vouchers' => $available]);
    }

    public function apply(): void
    {
        $this->requireAuth();
        $payload = json_decode(file_get_contents('php://input'), true) ?: $_POST;
        $rawItems = is_array($payload['cart_items'] ?? null) ? $payload['cart_items'] : [];
        $items = [];
        foreach ($rawItems as $rawItem) {
            $productId = (int) ($rawItem['product_id'] ?? 0);
            $product = $this->productModel->findActiveById($productId);
            $quantity = max(1, min(100, (int) ($rawItem['quantity'] ?? 1)));
            if ($product) {
                $items[] = [
                    'product_id' => $productId,
                    'quantity' => $quantity,
                    'unit_price' => (float) $this->productModel->getEffectivePrice($productId, $quantity),
                ];
            }
        }

        $result = $this->voucherService->validateCode((string) ($payload['code'] ?? ''), $items, (int) $_SESSION['user_id']);
        if (!$result['success']) $this->json($result, 422);

        $_SESSION['applied_voucher_code'] = $result['voucher']['code'];

        $orderValue = array_sum(array_map(
            static fn(array $item): float => $item['unit_price'] * $item['quantity'],
            $items
        ));
        $this->json([
            'success' => true,
            'discount' => $result['discount'],
            'final_total' => max(0, $orderValue - $result['discount']),
            'eligible_subtotal' => $result['eligible_subtotal'],
            'code' => $result['voucher']['code'],
        ]);
    }
}
