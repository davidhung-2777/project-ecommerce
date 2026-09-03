<?php

namespace App\Models;

use App\Core\Model;

class OrderDetailModel extends Model
{
    protected string $table = 'order_details';

    public function getByOrder(int $orderId): array
    {
        return $this->findWhere('order_id = ?', [$orderId]);
    }

    public function createFromCartItems(int $orderId, array $cartItems): void
    {
        foreach ($cartItems as $item) {
            $this->create([
                'order_id'      => $orderId,
                'product_id'    => $item['product_id'],
                'product_name'  => $item['product_name'],
                'product_sku'   => $item['sku'] ?? null,
                'product_image' => $item['thumbnail'] ?? null,
                'size_option'   => $item['size_option'] ?? null,
                'color_option'  => $item['color_option'] ?? null,
                'quantity'      => $item['quantity'],
                'unit_price'    => $item['unit_price'],
                'subtotal'      => $item['unit_price'] * $item['quantity'],
            ]);
        }
    }
}
