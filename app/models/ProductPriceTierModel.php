<?php

namespace App\Models;

use App\Core\Model;

class ProductPriceTierModel extends Model
{
    protected string $table = 'product_price_tiers';

    public function getByProduct(int $productId): array
    {
        return $this->findWhere('product_id = ?', [$productId], 'min_qty ASC');
    }

    public function saveForProduct(int $productId, array $tiers): void
    {
        // Delete existing tiers
        $this->db->delete($this->table, 'product_id = ?', [$productId]);

        // Insert new tiers
        foreach ($tiers as $tier) {
            if (empty($tier['min_qty']) || empty($tier['price'])) continue;
            $this->create([
                'product_id'   => $productId,
                'min_qty'      => (int) $tier['min_qty'],
                'max_qty'      => isset($tier['max_qty']) && $tier['max_qty'] !== '' ? (int) $tier['max_qty'] : null,
                'price'        => (float) $tier['price'],
                'discount_pct' => $tier['discount_pct'] ?? null,
                'label'        => $tier['label'] ?? null,
            ]);
        }
    }
}
