<?php

namespace App\Services;

use App\Core\Database;
use App\Models\VoucherModel;
use App\Models\VoucherUsageModel;

class VoucherService
{
    private Database $db;
    private VoucherModel $voucherModel;
    private VoucherUsageModel $usageModel;

    public function __construct()
    {
        $this->db = Database::getInstance();
        $this->voucherModel = new VoucherModel();
        $this->usageModel = new VoucherUsageModel();
    }

    public function validateCode(string $code, array $items, int $userId): array
    {
        $voucher = $this->voucherModel->findByCode($code);
        if (!$voucher) return ['success' => false, 'message' => 'Mã voucher không tồn tại.'];
        return $this->validateVoucher($voucher, $items, $userId);
    }

    public function validateVoucher(array $voucher, array $items, int $userId): array
    {
        $now = time();
        if (!(int) $voucher['is_active']) return ['success' => false, 'message' => 'Voucher đang tạm ngưng.'];
        if (strtotime($voucher['start_date']) > $now) return ['success' => false, 'message' => 'Voucher chưa đến thời gian áp dụng.'];
        if (strtotime($voucher['end_date']) < $now) return ['success' => false, 'message' => 'Voucher đã hết hạn.'];
        if ($voucher['usage_limit'] !== null && (int) $voucher['used_count'] >= (int) $voucher['usage_limit']) {
            return ['success' => false, 'message' => 'Voucher đã hết lượt sử dụng.'];
        }

        $userUsed = $this->usageModel->countByUser((int) $voucher['id'], $userId);
        if ($voucher['usage_limit_per_user'] !== null && $userUsed >= (int) $voucher['usage_limit_per_user']) {
            return ['success' => false, 'message' => 'Bạn đã sử dụng hết số lượt cho voucher này.'];
        }

        $eligibleSubtotal = 0.0;
        $cartSubtotal = 0.0;
        foreach ($items as $item) {
            $cartSubtotal += (float) $item['unit_price'] * (int) $item['quantity'];
            if ($this->isEligible($voucher, $item)) {
                $eligibleSubtotal += (float) $item['unit_price'] * (int) $item['quantity'];
            }
        }
        if ($eligibleSubtotal <= 0) return ['success' => false, 'message' => 'Voucher không áp dụng cho sản phẩm trong giỏ hàng.'];
        if ((float) $voucher['min_order_value'] > $cartSubtotal) {
            return ['success' => false, 'message' => 'Đơn hàng chưa đạt giá trị tối thiểu để áp dụng voucher.'];
        }

        $discount = $voucher['discount_type'] === 'percent'
            ? $eligibleSubtotal * ((float) $voucher['discount_value'] / 100)
            : (float) $voucher['discount_value'];
        if ($voucher['max_discount_amount'] !== null) {
            $discount = min($discount, (float) $voucher['max_discount_amount']);
        }
        $discount = min(round($discount, 2), $eligibleSubtotal);

        return ['success' => true, 'voucher' => $voucher, 'discount' => $discount, 'eligible_subtotal' => $eligibleSubtotal];
    }

    public function commitUsage(int $voucherId, int $userId, int $orderId, float $discountAmount): bool
    {
        if (!$this->voucherModel->incrementUsedCountAtomic($voucherId)) return false;
        try {
            $this->usageModel->createUsage($voucherId, $userId, $orderId, $discountAmount);
        } catch (\Throwable $exception) {
            $this->db->query(
                'UPDATE vouchers SET used_count = used_count - 1 WHERE id = ? AND used_count > 0',
                [$voucherId]
            );
            return false;
        }
        return true;
    }

    public function claimUsage(array $voucher, int $userId, int $orderId, float $discount, array $items): array
    {
        $result = $this->validateVoucher($voucher, $items, $userId);
        if (!$result['success']) return $result;
        if (!$this->commitUsage((int) $voucher['id'], $userId, $orderId, $discount)) {
            return ['success' => false, 'message' => 'Voucher vừa hết lượt sử dụng.'];
        }
        return $result;
    }

    private function isEligible(array $voucher, array $item): bool
    {
        $productId = (int) ($item['product_id'] ?? 0);
        $hasProductScope = (int) ($this->db->fetch('SELECT COUNT(*) AS total FROM voucher_products WHERE voucher_id = ?', [$voucher['id']])['total'] ?? 0) > 0;
        $hasCategoryScope = (int) ($this->db->fetch('SELECT COUNT(*) AS total FROM voucher_categories WHERE voucher_id = ?', [$voucher['id']])['total'] ?? 0) > 0;
        if (!$hasProductScope && !$hasCategoryScope) return true;
        if ($hasProductScope && $this->db->fetch('SELECT 1 FROM voucher_products WHERE voucher_id = ? AND product_id = ? LIMIT 1', [$voucher['id'], $productId])) return true;
        return $hasCategoryScope && (bool) $this->db->fetch(
            'SELECT 1 FROM voucher_categories vc JOIN products p ON p.category_id = vc.category_id WHERE vc.voucher_id = ? AND p.id = ? LIMIT 1',
            [$voucher['id'], $productId]
        );
    }
}