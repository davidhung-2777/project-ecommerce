<?php

namespace App\Models;

use App\Core\Model;

class CartModel extends Model
{
    protected string $table = 'carts';

    /**
     * Get or create cart for user.
     */
    public function getOrCreateForUser(int $userId): array
    {
        $cart = $this->findOneWhere('user_id = ?', [$userId]);
        if (!$cart) {
            $id   = $this->create(['user_id' => $userId]);
            $cart = $this->find($id);
        }
        return $cart;
    }

    /**
     * Get or create cart for guest (session-based).
     */
    public function getOrCreateForGuest(string $sessionId): array
    {
        $cart = $this->findOneWhere('session_id = ? AND user_id IS NULL', [$sessionId]);
        if (!$cart) {
            $id   = $this->create(['session_id' => $sessionId]);
            $cart = $this->find($id);
        }
        return $cart;
    }

    /**
     * Merge guest cart into user cart on login.
     */
    public function mergeGuestCart(string $sessionId, int $userId): void
    {
        $guestCart = $this->findOneWhere('session_id = ? AND user_id IS NULL', [$sessionId]);
        if (!$guestCart) return;

        $userCart = $this->getOrCreateForUser($userId);

        $itemModel = new CartItemModel();
        $guestItems = $itemModel->getByCart($guestCart['id']);

        foreach ($guestItems as $item) {
            $existing = $itemModel->findOneWhere(
                'cart_id = ? AND product_id = ? AND (size_option = ? OR (size_option IS NULL AND ? IS NULL)) AND (color_option = ? OR (color_option IS NULL AND ? IS NULL))',
                [$userCart['id'], $item['product_id'], $item['size_option'], $item['size_option'], $item['color_option'], $item['color_option']]
            );

            if ($existing) {
                $itemModel->update($existing['id'], ['quantity' => $existing['quantity'] + $item['quantity']]);
            } else {
                $item['cart_id'] = $userCart['id'];
                unset($item['id']);
                $itemModel->create($item);
            }
        }

        // Delete guest cart
        $this->delete($guestCart['id']);
    }

    /**
     * Get full cart with items and product details.
     */
    public function getCartWithItems(int $cartId): array
    {
        $cart  = $this->find($cartId);
        if (!$cart) return [];

        $items = $this->db->fetchAll(
            'SELECT ci.*, p.name AS product_name, p.thumbnail, p.slug AS product_slug,
                    p.stock, p.sku
             FROM cart_items ci
             JOIN products p ON ci.product_id = p.id
             WHERE ci.cart_id = ?
             ORDER BY ci.created_at ASC',
            [$cartId]
        );

        $subtotal = 0;
        foreach ($items as &$item) {
            $item['line_total'] = $item['unit_price'] * $item['quantity'];
            $subtotal += $item['line_total'];
        }

        $cart['items']    = $items;
        $cart['subtotal'] = $subtotal;
        $cart['count']    = array_sum(array_column($items, 'quantity'));

        return $cart;
    }
}
