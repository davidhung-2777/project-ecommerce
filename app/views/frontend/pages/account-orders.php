<?php
$baseUrl = $_ENV['APP_URL'] ?? 'http://localhost/project-ecommerce/public';
$pageTitle = 'Lịch sử mua hàng - DecorNest';
$statusLabels = ['pending' => 'Chờ duyệt', 'confirmed' => 'Đã duyệt', 'processing' => 'Đang đóng gói', 'shipped' => 'Đang vận chuyển', 'delivered' => 'Đã giao thành công', 'cancelled' => 'Đã hủy', 'refunded' => 'Đã hoàn tiền'];
$statusColors = ['pending' => 'bg-amber-50 text-amber-800 border-amber-200', 'confirmed' => 'bg-blue-50 text-blue-800 border-blue-200', 'processing' => 'bg-purple-50 text-purple-800 border-purple-200', 'shipped' => 'bg-indigo-50 text-indigo-800 border-indigo-200', 'delivered' => 'bg-sage-light text-sage border-sage/20', 'cancelled' => 'bg-red-50 text-red-800 border-red-200', 'refunded' => 'bg-gray-100 text-gray-700 border-gray-200'];
?>
<div class="max-w-5xl mx-auto px-4 sm:px-6 lg:px-8 py-10 lg:py-16">
    <div class="flex items-center justify-between pb-6 border-b border-beige mb-8">
        <div><h1 class="text-2xl sm:text-3xl font-serif font-bold text-charcoal">Lịch sử mua hàng</h1><p class="text-xs sm:text-sm text-muted mt-1">Theo dõi các đơn hàng bạn đã đặt</p></div>
        <a href="<?= $baseUrl ?>/products" class="text-xs font-semibold text-wood hover:underline">← Tiếp tục mua sắm</a>
    </div>
    <?php if (empty($data)): ?>
        <div class="text-center py-20 bg-cream/40 rounded-3xl border border-dashed border-beige"><p class="text-lg font-serif font-bold">Chưa có đơn hàng nào</p><a href="<?= $baseUrl ?>/products" class="mt-5 inline-block px-6 py-3 bg-charcoal text-white rounded-full text-xs font-bold">Khám phá sản phẩm</a></div>
    <?php else: ?>
        <div class="space-y-4">
        <?php foreach ($data as $order): ?>
            <a href="<?= $baseUrl ?>/account/orders/<?= (int) $order['id'] ?>" class="block bg-white rounded-3xl border border-beige p-6 shadow-warm hover:border-sand transition">
                <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4">
                    <div><div class="flex items-center gap-3"><span class="font-serif font-bold">#<?= htmlspecialchars($order['order_number']) ?></span><span class="px-3 py-0.5 text-[10px] font-semibold rounded-full border <?= $statusColors[$order['status']] ?? 'bg-cream text-muted border-sand' ?>"><?= $statusLabels[$order['status']] ?? htmlspecialchars($order['status']) ?></span></div><p class="text-xs text-muted mt-2">Đặt ngày: <?= date('d/m/Y H:i', strtotime($order['created_at'])) ?> · <?= (int) $order['item_count'] ?> sản phẩm</p></div>
                    <div class="text-right"><p class="text-lg font-serif font-bold text-wood"><?= number_format($order['total_amount']) ?>đ</p><span class="text-xs text-wood">Xem chi tiết →</span></div>
                </div>
            </a>
        <?php endforeach; ?>
        </div>
        <?php if (($last_page ?? 1) > 1): ?><div class="mt-10 flex justify-center"><?php require ROOT_PATH . '/app/views/frontend/partials/pagination.php'; ?></div><?php endif; ?>
    <?php endif; ?>
</div>