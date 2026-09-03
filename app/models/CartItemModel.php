<?php

namespace App\Models;

use App\Core\Model;

class CartItemModel extends Model
{
    protected string $table = 'cart_items';

    public function getByCart(int $cartId): array
    {
        return $this->findWhere('cart_id = ?', [$cartId], 'created_at ASC');
    }

    public function findExisting(int $cartId, int $productId, ?string $size, ?string $color): array|false
    {
        $sql    = 'SELECT * FROM cart_items WHERE cart_id = ? AND product_id = ?';
        $params = [$cartId, $productId];

        if ($size !== null) {
            $sql    .= ' AND size_option = ?';
            $params[] = $size;
        } else {
            $sql .= ' AND size_option IS NULL';
        }

        if ($color !== null) {
            $sql    .= ' AND color_option = ?';
            $params[] = $color;
        } else {
            $sql .= ' AND color_option IS NULL';
        }

        $sql .= ' LIMIT 1';
        return $this->db->fetch($sql, $params);
    }

    public function addOrUpdate(int $cartId, int $productId, int $quantity, float $unitPrice, ?string $size = null, ?string $color = null): int|string
    {
        $existing = $this->findExisting($cartId, $productId, $size, $color);

        if ($existing) {
            $newQty = $existing['quantity'] + $quantity;
            $this->update($existing['id'], ['quantity' => $newQty, 'unit_price' => $unitPrice]);
            return $existing['id'];
        }

        return $this->create([
            'cart_id'      => $cartId,
            'product_id'   => $productId,
            'quantity'     => $quantity,
            'unit_price'   => $unitPrice,
            'size_option'  => $size,
            'color_option' => $color,
        ]);
    }

    public function countItems(int $cartId): int
    {
        $result = $this->db->fetch(
            'SELECT SUM(quantity) AS total FROM cart_items WHERE cart_id = ?',
            [$cartId]
        );
        return (int) ($result['total'] ?? 0);
    }
}
