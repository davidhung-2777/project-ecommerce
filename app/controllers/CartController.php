<?php

namespace App\Controllers;

use App\Core\Controller;
use App\Models\CartModel;
use App\Models\CartItemModel;
use App\Models\ProductModel;

class CartController extends Controller
{
    protected string $viewPath = 'frontend';

    private CartModel $cartModel;
    private CartItemModel $cartItemModel;
    private ProductModel $productModel;

    public function __construct()
    {
        $this->cartModel     = new CartModel();
        $this->cartItemModel = new CartItemModel();
        $this->productModel  = new ProductModel();
    }

    public function index(): void
    {
        $cart     = $this->getCart();
        $cartData = $this->cartModel->getCartWithItems($cart['id']);
        $this->view('pages/cart', compact('cartData'));
    }

    public function add(): void
    {
        $productId  = (int) $this->post('product_id', 0, false);
        $quantity   = (int) $this->post('quantity', 1, false);
        $sizeOption = $this->post('size_option') ?: null;
        $colorOption= $this->post('color_option') ?: null;

        // ===== VALIDATE QUANTITY: MIN=1, MAX=100 =====
        if ($quantity < 1) {
            $quantity = 1;
        }
        if ($quantity > 100) {
            $this->json([
                'success' => false, 
                'message' => 'Số lượng tối đa cho mỗi sản phẩm là 100.',
                'max_quantity' => 100
            ], 422);
        }

        $product = $this->productModel->findActiveById($productId);
        if (!$product) {
            $this->json(['success' => false, 'message' => 'Sản phẩm không tồn tại.'], 404);
        }

        // Kiểm tra tồn kho
        if ($product['stock'] < $quantity) {
            $this->json([
                'success' => false, 
                'message' => "Số lượng tồn kho chỉ còn {$product['stock']} sản phẩm.",
                'available_stock' => $product['stock']
            ], 422);
        }

        $cart = $this->getCart();
        
        // Kiểm tra nếu sản phẩm đã có trong giỏ
        $existing = $this->cartItemModel->findOneWhere(
            'cart_id = ? AND product_id = ?', 
            [$cart['id'], $productId]
        );
        
        $requestedQuantity = $quantity;

        if ($existing) {
            $newQuantity = $existing['quantity'] + $requestedQuantity;
            
            // Validate lại sau khi cộng dồn
            if ($newQuantity > 100) {
                $this->json([
                    'success' => false,
                    'message' => 'Tổng số lượng vượt quá giới hạn 100 sản phẩm.',
                    'current_quantity' => $existing['quantity'],
                    'max_can_add' => 100 - $existing['quantity']
                ], 422);
            }
            
            if ($newQuantity > $product['stock']) {
                $this->json([
                    'success' => false,
                    'message' => "Không đủ tồn kho. Bạn đã có {$existing['quantity']} trong giỏ.",
                    'available_stock' => $product['stock'],
                    'current_quantity' => $existing['quantity']
                ], 422);
            }
            
            $quantity = $newQuantity;
        }

        $unitPrice = $this->productModel->getEffectivePrice($productId, $quantity);
        $this->cartItemModel->addOrUpdate($cart['id'], $productId, $requestedQuantity, $unitPrice, $sizeOption, $colorOption);

        $count = $this->cartItemModel->countItems($cart['id']);

        $this->json([
            'success' => true,
            'message' => 'Đã thêm sản phẩm vào giỏ hàng.',
            'cart_count' => $count,
        ]);
    }

    public function update(): void
    {
        $itemId   = (int) $this->post('item_id', 0, false);
        $quantity = (int) $this->post('quantity', 0, false);

        // ===== VALIDATE QUANTITY: MIN=1, MAX=100 =====
        if ($quantity < 0) {
            $this->json(['success' => false, 'message' => 'Số lượng không hợp lệ.'], 422);
        }
        
        if ($quantity > 100) {
            $this->json([
                'success' => false, 
                'message' => 'Số lượng tối đa cho mỗi sản phẩm là 100.',
                'max_quantity' => 100
            ], 422);
        }

        $cart = $this->getCart();
        $item = $this->cartItemModel->findOneWhere('id = ? AND cart_id = ?', [$itemId, $cart['id']]);

        if (!$item) {
            $this->json(['success' => false, 'message' => 'Mục không tồn tại.'], 404);
        }
        
        // Kiểm tra stock
        $product = $this->productModel->find($item['product_id']);
        if ($quantity > 0 && $quantity > $product['stock']) {
            $this->json([
                'success' => false,
                'message' => "Chỉ còn {$product['stock']} sản phẩm trong kho.",
                'available_stock' => $product['stock']
            ], 422);
        }

        if ($quantity === 0) {
            // Xóa sản phẩm khỏi giỏ
            $this->cartItemModel->delete($itemId);
        } else {
            // Recalculate price for new quantity
            $unitPrice = $this->productModel->getEffectivePrice($item['product_id'], $quantity);
            $this->cartItemModel->update($itemId, ['quantity' => $quantity, 'unit_price' => $unitPrice]);
        }

        $cartData = $this->cartModel->getCartWithItems($cart['id']);

        $this->json([
            'success'  => true,
            'subtotal' => $cartData['subtotal'],
            'count'    => $cartData['count'],
        ]);
    }

    public function remove(): void
    {
        $itemId = (int) $this->post('item_id', 0, false);
        $cart   = $this->getCart();
        $item   = $this->cartItemModel->findOneWhere('id = ? AND cart_id = ?', [$itemId, $cart['id']]);

        if ($item) {
            $this->cartItemModel->delete($itemId);
        }

        $cartData = $this->cartModel->getCartWithItems($cart['id']);
        $this->json([
            'success'  => true,
            'subtotal' => $cartData['subtotal'],
            'count'    => $cartData['count'],
        ]);
    }

    public function count(): void
    {
        $cart  = $this->getCart();
        $count = $this->cartItemModel->countItems($cart['id']);
        $this->json(['count' => $count]);
    }

    public function mini(): void
    {
        $cart     = $this->getCart();
        $cartData = $this->cartModel->getCartWithItems($cart['id']);

        ob_start();
        require ROOT_PATH . '/app/views/frontend/partials/mini-cart.php';
        $html = ob_get_clean();

        $this->json([
            'html'     => $html,
            'count'    => $cartData['count'] ?? 0,
            'subtotal' => $cartData['subtotal'] ?? 0,
        ]);
    }

    // ── Helper ────────────────────────────────────────────────────────────────

    private function getCart(): array
    {
        if (!empty($_SESSION['user_id'])) {
            return $this->cartModel->getOrCreateForUser((int) $_SESSION['user_id']);
        }
        if (empty($_SESSION['cart_session_id'])) {
            $_SESSION['cart_session_id'] = bin2hex(random_bytes(16));
        }
        return $this->cartModel->getOrCreateForGuest($_SESSION['cart_session_id']);
    }
}
