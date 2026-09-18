<?php

namespace App\Controllers;

use App\Core\Controller;
use App\Models\ProductModel;
use App\Models\ProductPriceTierModel;
use App\Models\CategoryModel;
use App\Models\OrderModel;
use App\Models\PaymentModel;
use App\Models\QuoteModel;
use App\Models\QuoteItemModel;
use App\Models\UserModel;

class AdminController extends Controller
{
    protected string $viewPath = 'admin';

    private ProductModel $productModel;
    private ProductPriceTierModel $tierModel;
    private CategoryModel $categoryModel;
    private OrderModel $orderModel;
    private PaymentModel $paymentModel;
    private QuoteModel $quoteModel;
    private QuoteItemModel $quoteItemModel;
    private UserModel $userModel;

    public function __construct()
    {
        $this->productModel   = new ProductModel();
        $this->tierModel      = new ProductPriceTierModel();
        $this->categoryModel  = new CategoryModel();
        $this->orderModel     = new OrderModel();
        $this->paymentModel   = new PaymentModel();
        $this->quoteModel     = new QuoteModel();
        $this->quoteItemModel = new QuoteItemModel();
        $this->userModel      = new UserModel();
    }

    public function dashboard(): void
    {
        $this->requireAdmin();

        $stats = [
            'orders_today'    => $this->orderModel->count("DATE(created_at) = CURDATE()"),
            'orders_pending'  => $this->orderModel->count("status = 'pending'"),
            'quotes_pending'  => $this->quoteModel->count("status = 'pending'"),
            'total_customers' => $this->userModel->count("role = 'customer'"),
            'revenue'         => $this->orderModel->getRevenueStats(),
            'recent_orders'   => $this->orderModel->getRecentOrders(10),
            'product'         => $this->productModel->getAdminStats(),
            'recent_products' => $this->productModel->getRecentAdmin(5),
        ];

        $this->view('pages/dashboard', compact('stats'));
    }

    // ── Products ──────────────────────────────────────────────────────────────
 
    public function products(): void
    {
        $this->requireAdmin();
        $page     = max(1, (int) $this->get('page', 1));
        $search   = trim((string) $this->get('search', ''));
        $category = $this->get('category_id', '');
        $status   = $this->get('status', '');

        $filters = [];
        if ($search !== '') {
            $filters['search'] = $search;
        }
        if ($category !== '') {
            $filters['category_id'] = (int) $category;
        }
        if ($status !== '') {
            $filters['status'] = (int) $status;
        }

        $result     = $this->productModel->getFilteredAdmin($filters, $page, 20);
        $products   = $result['data'] ?? [];
        $categories = $this->categoryModel->getAllActive();
        $stats      = $this->productModel->getAdminStats();

        $this->view('pages/products', array_merge($result, [
            'products'    => $products,
            'categories'  => $categories,
            'stats'       => $stats,
            'search'      => $search,
            'category_id' => $category,
            'status'      => $status,
        ]), 'main');
    }

    public function createProduct(): void
    {
        $this->requireAdmin();
        $categories = $this->categoryModel->getAllActive();
        $tiers = []; // Empty tiers for new product
        $product = []; // No product for create
        $this->view('pages/product-form', compact('categories', 'tiers', 'product'));
    }

    public function storeProduct(): void
    {
        $this->requireAdmin();
        if (!$this->isPost()) {
            $this->redirect($this->baseUrl('admin/products'));
        }

        $data = $this->buildProductData($_POST);
        
        // Upload thumbnail
        $thumbnail = $this->handleImageUpload('thumbnail');
        if ($thumbnail) {
            $data['thumbnail'] = $thumbnail;
        }

        // Handle gallery images
        $gallery = $this->handleGalleryUpload('images');
        if (!empty($gallery)) {
            $data['images'] = json_encode($gallery);
        }

        try {
            $productId = $this->productModel->create($data);

            // Save price tiers
            if (!empty($_POST['tiers']) && is_array($_POST['tiers'])) {
                $this->tierModel->saveForProduct($productId, $_POST['tiers']);
            }

            $this->setFlash('success', 'Sản phẩm đã được tạo thành công.');
            $this->redirect($this->baseUrl('admin/products'));
        } catch (\Exception $e) {
            $this->setFlash('error', 'Lỗi khi tạo sản phẩm: ' . $e->getMessage());
            $this->redirect($this->baseUrl('admin/products/create'));
        }
    }

    public function editProduct(string $id): void
    {
        $this->requireAdmin();
        $product = $this->productModel->find((int) $id);
        
        if (!$product) {
            $this->setFlash('error', 'Sản phẩm không tồn tại.');
            $this->redirect($this->baseUrl('admin/products'));
            return;
        }
        
        $categories = $this->categoryModel->getAllActive();
        $tiers = $this->tierModel->getByProduct((int) $id);
        
        $this->view('pages/product-form', compact('product', 'categories', 'tiers'));
    }

    public function updateProduct(string $id): void
    {
        $this->requireAdmin();
        if (!$this->isPost()) {
            $this->redirect($this->baseUrl('admin/products'));
        }

        $productId = (int) $id;
        $product   = $this->productModel->find($productId);
        if (!$product) {
            $this->setFlash('error', 'Sản phẩm không tồn tại.');
            $this->redirect($this->baseUrl('admin/products'));
            return;
        }

        $data = $this->buildProductData($_POST);

        // Upload new thumbnail if provided
        $newThumb = $this->handleImageUpload('thumbnail');
        if ($newThumb) {
            $data['thumbnail'] = $newThumb;
        }

        // Handle new gallery images
        $gallery = $this->handleGalleryUpload('images');
        if (!empty($gallery)) {
            $existingImages = json_decode($product['images'] ?? '[]', true) ?: [];
            $data['images'] = json_encode(array_values(array_unique(array_merge($existingImages, $gallery))));
        }

        try {
            $this->productModel->update($productId, $data);

            // Update price tiers
            if (isset($_POST['tiers']) && is_array($_POST['tiers'])) {
                $this->tierModel->saveForProduct($productId, $_POST['tiers']);
            }

            $this->setFlash('success', 'Cập nhật sản phẩm thành công.');
            $this->redirect($this->baseUrl('admin/products'));
        } catch (\Exception $e) {
            $this->setFlash('error', 'Lỗi khi cập nhật: ' . $e->getMessage());
            $this->redirect($this->baseUrl('admin/products/' . $productId . '/edit'));
        }
    }

    public function deleteProduct(string $id): void
    {
        $this->requireAdmin();
        $productId = (int) $id;
        $product   = $this->productModel->find($productId);

        if (!$product) {
            $this->json(['success' => false, 'message' => 'Sản phẩm không tồn tại.']);
            return;
        }

        try {
            // Check if product has been ordered
            $hasOrders = (int) (\App\Core\Database::getInstance()->fetch(
                "SELECT COUNT(*) as cnt FROM order_details WHERE product_id = ?",
                [$productId]
            )['cnt'] ?? 0);

            if ($hasOrders > 0) {
                // Soft delete to protect order history integrity
                $this->productModel->update($productId, ['is_active' => 0]);
                $this->json([
                    'success' => true, 
                    'message' => 'Sản phẩm đã có trong đơn hàng nên được chuyển sang trạng thái Ẩn để bảo toàn lịch sử.',
                    'action'  => 'hidden'
                ]);
                return;
            }

            // Remove dependencies before hard delete
            $db = \App\Core\Database::getInstance();
            $db->query("DELETE FROM product_price_tiers WHERE product_id = ?", [$productId]);
            $db->query("DELETE FROM cart_items WHERE product_id = ?", [$productId]);
            $db->query("DELETE FROM quote_items WHERE product_id = ?", [$productId]);
            $db->query("DELETE FROM wishlists WHERE product_id = ?", [$productId]);
            $db->query("DELETE FROM reviews WHERE product_id = ?", [$productId]);
            $db->query("DELETE FROM product_images WHERE product_id = ?", [$productId]);
            $db->query("DELETE FROM product_variants WHERE product_id = ?", [$productId]);

            $this->productModel->delete($productId);
            $this->json([
                'success' => true, 
                'message' => 'Đã xóa vĩnh viễn sản phẩm khỏi hệ thống.',
                'action'  => 'deleted'
            ]);
        } catch (\Exception $e) {
            $this->json(['success' => false, 'message' => 'Lỗi khi xóa sản phẩm: ' . $e->getMessage()]);
        }
    }

    public function toggleProductStatus(string $id): void
    {
        $this->requireAdmin();
        $productId = (int) $id;
        $product   = $this->productModel->find($productId);

        if (!$product) {
            $this->json(['success' => false, 'message' => 'Sản phẩm không tồn tại.']);
            return;
        }

        $newStatus = $product['is_active'] ? 0 : 1;
        $this->productModel->update($productId, ['is_active' => $newStatus]);
        $this->json([
            'success'   => true, 
            'is_active' => $newStatus,
            'message'   => $newStatus ? 'Đã kích hoạt hiển thị sản phẩm trên website.' : 'Đã ẩn sản phẩm khỏi website.'
        ]);
    }

    // ── Categories ────────────────────────────────────────────────────────────

    public function categories(): void
    {
        $this->requireAdmin();
        $categories = $this->categoryModel->getAllActive();
        $this->view('pages/categories', compact('categories'));
    }

    public function storeCategory(): void
    {
        $this->requireAdmin();
        $slug = strtolower(preg_replace('/[^a-z0-9]+/i', '-', $this->post('name')));
        $this->categoryModel->create([
            'parent_id'   => $this->post('parent_id') ?: null,
            'name'        => $this->post('name'),
            'slug'        => $slug,
            'description' => $this->post('description', ''),
            'sort_order'  => (int) $this->post('sort_order', 0, false),
        ]);
        $this->json(['success' => true]);
    }

    public function updateCategory(string $id): void
    {
        $this->requireAdmin();
        $this->categoryModel->update((int) $id, [
            'name'        => $this->post('name'),
            'description' => $this->post('description', ''),
            'sort_order'  => (int) $this->post('sort_order', 0, false),
            'is_active'   => (int) $this->post('is_active', 1, false),
        ]);
        $this->json(['success' => true]);
    }

    public function deleteCategory(string $id): void
    {
        $this->requireAdmin();
        // Soft delete: set is_active = 0
        $this->categoryModel->update((int) $id, ['is_active' => 0]);
        $this->json(['success' => true, 'message' => 'Danh mục đã được xóa.']);
    }

    // ── Orders ────────────────────────────────────────────────────────────────

    public function orders(): void
    {
        $this->requireAdmin();
        $page    = max(1, (int) $this->get('page', 1));
        $filters = [
            'status'         => $this->get('status', ''),
            'payment_method' => $this->get('payment_method', ''),
            'search'         => $this->get('search', ''),
        ];
        $result = $this->orderModel->getFilteredAdmin($filters, $page);
        $this->view('pages/orders', $result + compact('filters'));
    }

    public function orderDetail(string $id): void
    {
        $this->requireAdmin();
        $order = $this->orderModel->getFullOrder((int) $id);
        if (!$order) {
            $this->redirect($this->baseUrl('admin/orders'));
        }
        $this->view('pages/order-detail', compact('order'));
    }

    public function updateOrderStatus(string $id): void
    {
        $this->requireAdmin();
        $status = $this->post('status');
        $valid  = ['pending','confirmed','processing','shipped','delivered','cancelled'];

        if (!in_array($status, $valid)) {
            $this->json(['success' => false, 'message' => 'Trạng thái không hợp lệ.']);
        }

        $data = ['status' => $status];
        if ($status === 'shipped')    $data['shipped_at']    = date('Y-m-d H:i:s');
        if ($status === 'delivered')  $data['delivered_at']  = date('Y-m-d H:i:s');
        if ($status === 'cancelled')  $data['cancelled_at']  = date('Y-m-d H:i:s');

        $this->orderModel->update((int) $id, $data);
        $this->json(['success' => true]);
    }

    // ── Quotes ────────────────────────────────────────────────────────────────

    public function quotes(): void
    {
        $this->requireAdmin();
        $page   = max(1, (int) $this->get('page', 1));
        $status = $this->get('status', '');
        $result = $this->quoteModel->getAdminList($status, $page);
        $this->view('pages/quotes', $result + compact('status'));
    }

    public function quoteDetail(string $id): void
    {
        $this->requireAdmin();
        $quote = $this->quoteModel->getFullQuote((int) $id);
        if (!$quote) {
            $this->redirect($this->baseUrl('admin/quotes'));
        }
        $this->view('pages/quote-detail', compact('quote'));
    }

    public function respondQuote(string $id): void
    {
        $this->requireAdmin();
        $quote = $this->quoteModel->find((int) $id);
        if (!$quote) {
            $this->json(['success' => false, 'message' => 'Báo giá không tồn tại.']);
        }

        // Update quote item prices from admin input
        $items = $_POST['items'] ?? [];
        foreach ($items as $itemId => $data) {
            if (!empty($data['unit_price'])) {
                \App\Core\Database::getInstance()->query(
                    'UPDATE quote_items SET unit_price = ? WHERE id = ? AND quote_id = ?',
                    [(float) $data['unit_price'], (int) $itemId, (int) $id]
                );
            }
        }

        // Calculate total
        $totalResult = \App\Core\Database::getInstance()->fetch(
            'SELECT SUM(unit_price * quantity) AS total FROM quote_items WHERE quote_id = ?',
            [(int) $id]
        );

        $this->quoteModel->update((int) $id, [
            'status'         => 'responded',
            'admin_response' => $this->post('admin_response', ''),
            'total_amount'   => $totalResult['total'] ?? 0,
            'responded_at'   => date('Y-m-d H:i:s'),
            'expires_at'     => date('Y-m-d H:i:s', strtotime('+7 days')),
        ]);

        $this->json(['success' => true, 'message' => 'Đã gửi phản hồi báo giá.']);
    }

    public function convertQuote(string $id): void
    {
        $this->requireAdmin();
        // Admin can force-convert a quote to order
        $quote = $this->quoteModel->find((int) $id);
        if (!$quote || $quote['status'] === 'converted') {
            $this->json(['success' => false, 'message' => 'Không thể chuyển đổi báo giá này.']);
        }
        $this->quoteModel->update((int) $id, ['status' => 'responded']);
        $this->json(['success' => true, 'message' => 'Trạng thái đã được cập nhật. Khách hàng cần xác nhận.']);
    }

    public function rejectQuote(string $id): void
    {
        $this->requireAdmin();
        $reason = $this->post('reason', 'Không đủ điều kiện báo giá');
        $this->quoteModel->update((int) $id, [
            'status' => 'rejected',
            'admin_response' => $reason,
            'responded_at' => date('Y-m-d H:i:s')
        ]);
        $this->json(['success' => true, 'message' => 'Đã từ chối báo giá.']);
    }

    // ── Users ─────────────────────────────────────────────────────────────────

    public function users(): void
    {
        $this->requireAdmin();
        $page   = max(1, (int) $this->get('page', 1));
        $result = $this->userModel->getActiveCustomers($page);
        $this->view('pages/users', $result);
    }

    public function userDetail(string $id): void
    {
        $this->requireAdmin();
        $user   = $this->userModel->find((int) $id);
        $orders = $this->orderModel->getByUser((int) $id, 1, 10);
        $this->view('pages/user-detail', compact('user', 'orders'));
    }

    public function blockUser(string $id): void
    {
        $this->requireAdmin();
        $this->userModel->update((int) $id, ['is_active' => 0]);
        $this->json(['success' => true, 'message' => 'Đã khóa tài khoản.']);
    }

    public function unblockUser(string $id): void
    {
        $this->requireAdmin();
        $this->userModel->update((int) $id, ['is_active' => 1]);
        $this->json(['success' => true, 'message' => 'Đã mở khóa tài khoản.']);
    }

    // ── Payments ──────────────────────────────────────────────────────────────

    public function payments(): void
    {
        $this->requireAdmin();
        $pendingTransfers = $this->paymentModel->getPendingBankTransfers();
        $this->view('pages/payments', compact('pendingTransfers'));
    }

    public function confirmPayment(string $id): void
    {
        $this->requireAdmin();
        $payment = $this->paymentModel->find((int) $id);
        if (!$payment) {
            $this->json(['success' => false, 'message' => 'Thanh toán không tồn tại.']);
        }

        $this->paymentModel->markPaid((int) $id, ['confirmed_by_admin' => true, 'confirmed_at' => date('Y-m-d H:i:s')]);
        $this->orderModel->update($payment['order_id'], [
            'payment_status' => 'paid',
            'status'         => 'confirmed',
            'confirmed_at'   => date('Y-m-d H:i:s'),
        ]);

        $this->json(['success' => true, 'message' => 'Đã xác nhận thanh toán.']);
    }

    public function reports(): void
    {
        $this->requireAdmin();
        $stats = $this->orderModel->getRevenueStats();
        $this->view('pages/reports', compact('stats'));
    }

    // ── Private helpers ───────────────────────────────────────────────────────

    private function buildProductData(array $post): array
    {
        return [
            'category_id'   => $post['category_id'] ?: null,
            'name'          => htmlspecialchars($post['name'] ?? '', ENT_QUOTES, 'UTF-8'),
            'slug'          => $this->productSlug($post['name'] ?? ''),
            'sku'           => htmlspecialchars($post['sku'] ?? '', ENT_QUOTES, 'UTF-8'),
            'short_desc'    => htmlspecialchars($post['short_desc'] ?? '', ENT_QUOTES, 'UTF-8'),
            'description'   => $post['description'] ?? '',
            'price'         => (float) ($post['price'] ?? 0),
            'sale_price'    => !empty($post['sale_price']) ? (float) $post['sale_price'] : null,
            'install_fee'   => (float) ($post['install_fee'] ?? 0),
            'stock'         => (int) ($post['stock'] ?? 0),
            'weight'        => !empty($post['weight']) ? (float) $post['weight'] : null,
            'dimensions'    => $post['dimensions'] ?? null,
            'origin'        => $post['origin'] ?? null,
            'material'      => $post['material'] ?? null,
            'color'         => $post['color'] ?? null,
            'size_options'  => !empty($post['size_options']) ? json_encode(array_map('trim', explode(',', $post['size_options']))) : null,
            'color_options' => !empty($post['color_options']) ? json_encode(array_map('trim', explode(',', $post['color_options']))) : null,
            'is_featured'   => isset($post['is_featured']) ? 1 : 0,
            'is_new'        => isset($post['is_new']) ? 1 : 0,
            'is_active'     => isset($post['is_active']) ? 1 : 0,
            'seo_title'     => $post['seo_title'] ?? null,
            'seo_desc'      => $post['seo_desc'] ?? null,
        ];
    }

    private function productSlug(string $name): string
    {
        $unicode = [
            'a' => 'á|à|ả|ã|ạ|ă|ắ|ặ|ằ|ẳ|ẵ|â|ấ|ầ|ẩ|ẫ|ậ',
            'd' => 'đ',
            'e' => 'é|è|ẻ|ẽ|ẹ|ê|ế|ề|ể|ễ|ệ',
            'i' => 'í|ì|ỉ|ĩ|ị',
            'o' => 'ó|ò|ỏ|õ|ọ|ô|ố|ồ|ổ|ỗ|ộ|ơ|ớ|ờ|ở|ỡ|ợ',
            'u' => 'ú|ù|ủ|ũ|ụ|ư|ứ|ừ|ử|ữ|ự',
            'y' => 'ý|ỳ|ỷ|ỹ|ỵ',
            'A' => 'Á|À|Ả|Ã|Ạ|Ă|Ắ|Ặ|Ằ|Ẳ|Ẵ|Â|Ấ|Ầ|Ẩ|Ẫ|Ậ',
            'D' => 'Đ',
            'E' => 'É|È|Ẻ|Ẽ|Ẹ|Ê|Ế|Ề|Ể|Ễ|Ệ',
            'I' => 'Í|Ì|Ỉ|Ĩ|Ị',
            'O' => 'Ó|Ò|Ỏ|Õ|Ọ|Ô|Ố|Ồ|Ổ|Ỗ|Ộ|Ơ|Ớ|Ờ|Ở|Ỡ|Ợ',
            'U' => 'Ú|Ù|Ủ|Ũ|Ụ|Ư|Ứ|Ừ|Ử|Ữ|Ự',
            'Y' => 'Ý|Ỳ|Ỷ|Ỹ|Ỵ',
        ];
        foreach ($unicode as $nonAccent => $accent) {
            $name = preg_replace("/($accent)/iu", $nonAccent, $name);
        }

        $slug = trim((string) preg_replace('/[^a-z0-9]+/i', '-', strtolower($name)), '-');
        if ($slug === '') {
            $slug = 'product-' . uniqid();
        }

        return $slug;
    }

    private function handleImageUpload(string $fieldName): string
    {
        if (empty($_FILES[$fieldName]['name'])) return '';

        $file    = $_FILES[$fieldName];
        $ext     = strtolower(pathinfo($file['name'], PATHINFO_EXTENSION));
        $allowed = ['jpg', 'jpeg', 'png', 'webp', 'gif'];

        if (!in_array($ext, $allowed)) return '';
        if ($file['size'] > 5 * 1024 * 1024) return ''; // 5MB max

        $uploadDir = ROOT_PATH . '/public/uploads/products';
        if (!is_dir($uploadDir)) {
            @mkdir($uploadDir, 0777, true);
        }

        $filename = 'product_' . uniqid() . '.' . $ext;
        $dest     = $uploadDir . '/' . $filename;

        if (move_uploaded_file($file['tmp_name'], $dest)) {
            return '/uploads/products/' . $filename;
        }
        return '';
    }

    private function handleGalleryUpload(string $fieldName): array
    {
        $paths = [];
        if (empty($_FILES[$fieldName]['name']) || !is_array($_FILES[$fieldName]['name'])) return $paths;

        $uploadDir = ROOT_PATH . '/public/uploads/products';
        if (!is_dir($uploadDir)) {
            @mkdir($uploadDir, 0777, true);
        }

        foreach ($_FILES[$fieldName]['tmp_name'] as $i => $tmpName) {
            if (!$tmpName || empty($_FILES[$fieldName]['name'][$i])) continue;
            
            $ext     = strtolower(pathinfo($_FILES[$fieldName]['name'][$i], PATHINFO_EXTENSION));
            $allowed = ['jpg', 'jpeg', 'png', 'webp', 'gif'];
            if (!in_array($ext, $allowed)) continue;
            if ($_FILES[$fieldName]['size'][$i] > 5 * 1024 * 1024) continue;

            $filename = 'gallery_' . uniqid() . '_' . $i . '.' . $ext;
            $dest     = $uploadDir . '/' . $filename;
            if (move_uploaded_file($tmpName, $dest)) {
                $paths[] = '/uploads/products/' . $filename;
            }
        }

        return $paths;
    }
}
