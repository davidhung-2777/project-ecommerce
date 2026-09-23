<?php

namespace App\Models;

use App\Core\Model;

class VoucherModel extends Model
{
    protected string $table = 'vouchers';

    public function findByCode(string $code): array|false
    {
        return $this->findOneWhere('code = ?', [strtoupper(trim($code))]);
    }

    public function getAll(string $search = '', string $status = '', int $page = 1, int $perPage = 20): array
    {
        $conditions = ['1 = 1'];
        $params = [];

        if ($search !== '') {
            $conditions[] = 'v.code LIKE ?';
            $params[] = '%' . $search . '%';
        }
        if ($status === 'active') {
            $conditions[] = 'v.is_active = 1 AND v.start_date <= NOW() AND v.end_date >= NOW()';
        } elseif ($status === 'expired') {
            $conditions[] = 'v.end_date < NOW()';
        } elseif ($status === 'inactive') {
            $conditions[] = 'v.is_active = 0';
        }

        $where = implode(' AND ', $conditions);
        $total = (int) ($this->db->fetch("SELECT COUNT(*) AS total FROM vouchers v WHERE {$where}", $params)['total'] ?? 0);
        $offset = ($page - 1) * $perPage;
        $data = $this->db->fetchAll(
            "SELECT v.* FROM vouchers v WHERE {$where} ORDER BY v.created_at DESC LIMIT {$perPage} OFFSET {$offset}", $params
        );

        return [
            'data' => $data,
            'total' => $total,
            'per_page' => $perPage,
            'current_page' => $page,
            'last_page' => max(1, (int) ceil($total / $perPage)),
        ];
    }

    public function getAdminList(string $search = '', string $status = ''): array
    {
        return $this->getAll($search, $status, 1, 1000);
    }

    public function getProducts(int $voucherId): array
    {
        return $this->db->fetchAll(
            'SELECT product_id FROM voucher_products WHERE voucher_id = ?', [$voucherId]
        );
    }

    public function getCategories(int $voucherId): array
    {
        return $this->db->fetchAll(
            'SELECT category_id FROM voucher_categories WHERE voucher_id = ?', [$voucherId]
        );
    }

    public function saveScope(int $voucherId, string $scope, array $productIds, array $categoryIds): void
    {
        $this->db->query('DELETE FROM voucher_products WHERE voucher_id = ?', [$voucherId]);
        $this->db->query('DELETE FROM voucher_categories WHERE voucher_id = ?', [$voucherId]);

        foreach (array_unique(array_map('intval', $productIds)) as $productId) {
            if ($productId > 0) {
                $this->db->insert('voucher_products', ['voucher_id' => $voucherId, 'product_id' => $productId]);
            }
        }
        foreach (array_unique(array_map('intval', $categoryIds)) as $categoryId) {
            if ($categoryId > 0) {
                $this->db->insert('voucher_categories', ['voucher_id' => $voucherId, 'category_id' => $categoryId]);
            }
        }
    }

    public function hasUsage(int $voucherId): bool
    {
        return (int) ($this->db->fetch(
            'SELECT COUNT(*) AS total FROM voucher_usages WHERE voucher_id = ?', [$voucherId]
        )['total'] ?? 0) > 0;
    }

    public function incrementUsedCountAtomic(int $voucherId): bool
    {
        $affected = $this->db->query(
            'UPDATE vouchers
             SET used_count = used_count + 1
             WHERE id = ? AND is_active = 1
               AND (usage_limit IS NULL OR used_count < usage_limit)',
            [$voucherId]
        )->rowCount();

        return $affected === 1;
    }

    public function softDelete(int $voucherId): int
    {
        return $this->update($voucherId, ['is_active' => 0]);
    }
}