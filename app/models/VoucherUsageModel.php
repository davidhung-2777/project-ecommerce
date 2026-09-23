<?php

namespace App\Models;

use App\Core\Model;

class VoucherUsageModel extends Model
{
    protected string $table = 'voucher_usages';

    public function countByUser(int $voucherId, int $userId): int
    {
        return (int) ($this->db->fetch(
            'SELECT COUNT(*) AS total FROM voucher_usages WHERE voucher_id = ? AND user_id = ?',
            [$voucherId, $userId]
        )['total'] ?? 0);
    }

    public function createUsage(int $voucherId, int $userId, int $orderId, float $discountAmount): int|string
    {
        return $this->create([
            'voucher_id' => $voucherId,
            'user_id' => $userId,
            'order_id' => $orderId,
            'discount_amount' => $discountAmount,
        ]);
    }
}