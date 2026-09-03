<?php
$baseUrl   = $_ENV['APP_URL'] ?? 'http://localhost/project-ecommerce/public';
$pageTitle = 'Chi Tiết Đơn Hàng #' . htmlspecialchars($order['order_number'] ?? '') . ' - DecorNest';
?>

<div class="max-w-4xl mx-auto px-4 sm:px-6 lg:px-8 py-10 lg:py-16">
    
    <!-- Header -->
    <div class="flex items-center justify-between pb-6 border-b border-beige mb-8">
        <div>
            <div class="flex items-center gap-3">
                <h1 class="text-2xl sm:text-3xl font-serif font-bold text-charcoal">Đơn hàng #<?= htmlspecialchars($order['order_number'] ?? '') ?></h1>
                <?php
                $statusColors = [
                    'pending'    => 'bg-amber-50 text-amber-800 border-amber-200',
                    'confirmed'  => 'bg-blue-50 text-blue-800 border-blue-200',
                    'processing' => 'bg-purple-50 text-purple-800 border-purple-200',
                    'shipped'    => 'bg-indigo-50 text-indigo-800 border-indigo-200',
                    'delivered'  => 'bg-sage-light text-sage border-sage/20',
                    'cancelled'  => 'bg-red-50 text-red-800 border-red-200',
                ];
                $statusLabels = [
                    'pending'    => 'Chờ duyệt',
                    'confirmed'  => 'Đã duyệt',
                    'processing' => 'Đang đóng gói',
                    'shipped'    => 'Đang vận chuyển',
                    'delivered'  => 'Đã giao thành công',
                    'cancelled'  => 'Đã hủy',
                ];
                ?>
                <span class="px-3 py-1 text-xs font-semibold rounded-full border <?= $statusColors[$order['status']] ?? 'bg-cream text-muted' ?>">
                    <?= $statusLabels[$order['status']] ?? $order['status'] ?>
                </span>
            </div>
            <p class="text-xs text-muted mt-1">Ngày đặt: <?= date('d/m/Y H:i', strtotime($order['created_at'])) ?></p>
        </div>
        <a href="<?= $baseUrl ?>/user/orders" class="text-xs font-semibold text-wood hover:underline">
            ← Quay lại danh sách
        </a>
    </div>

    <!-- Items List -->
    <div class="bg-white rounded-3xl border border-beige overflow-hidden shadow-warm mb-8">
        <div class="px-6 py-4 bg-cream/60 border-b border-beige">
            <h3 class="font-serif font-bold text-sm text-charcoal">Danh sách sản phẩm decor</h3>
        </div>
        <div class="divide-y divide-beige/60 p-6 space-y-4">
            <?php foreach (($order['items'] ?? []) as $item): ?>
            <div class="pt-4 first:pt-0 flex items-center gap-4">
                <div class="w-16 h-16 rounded-2xl overflow-hidden bg-cream border border-beige/60 flex-shrink-0">
                    <?php 
                        $img = !empty($item['product_image']) ? $item['product_image'] : '/assets/images/product-placeholder.jpg';
                        if (!str_starts_with($img, 'http') && !str_starts_with($img, '/')) {
                            $img = $baseUrl . '/' . ltrim($img, '/');
                        }
                    ?>
                    <img src="<?= htmlspecialchars($img) ?>" class="w-full h-full object-cover">
                </div>
                <div class="flex-1 min-w-0">
                    <p class="font-serif font-bold text-sm text-charcoal"><?= htmlspecialchars($item['product_name']) ?></p>
                    <p class="text-xs text-muted mt-0.5">Số lượng: <?= $item['quantity'] ?> × <?= number_format($item['unit_price']) ?>đ</p>
                </div>
                <span class="font-bold text-sm sm:text-base text-charcoal font-serif"><?= number_format($item['subtotal']) ?>đ</span>
            </div>
            <?php endforeach; ?>
        </div>
        <div class="px-6 py-4 bg-cream/40 border-t border-beige flex justify-between items-center">
            <span class="font-serif font-bold text-sm text-charcoal">Tổng thanh toán:</span>
            <span class="font-serif font-bold text-xl text-wood"><?= number_format($order['total_amount']) ?>đ</span>
        </div>
    </div>

    <!-- Info Cards Grid -->
    <div class="grid sm:grid-cols-2 gap-6 mb-8">
        <div class="bg-white rounded-3xl border border-beige p-6 shadow-warm text-xs sm:text-sm space-y-2">
            <h4 class="font-serif font-bold text-sm text-charcoal pb-2 border-b border-beige mb-3 flex items-center gap-2">
                <span>📍</span> Địa chỉ giao hàng & Lắp đặt
            </h4>
            <p class="font-semibold text-charcoal"><?= htmlspecialchars($order['shipping_name'] ?? '') ?></p>
            <p class="text-muted"><?= htmlspecialchars($order['shipping_phone'] ?? '') ?></p>
            <p class="text-muted"><?= htmlspecialchars($order['shipping_address'] ?? '') ?>, <?= htmlspecialchars($order['shipping_district'] ?? '') ?>, <?= htmlspecialchars($order['shipping_city'] ?? '') ?></p>
            <?php if (!empty($order['customer_note'])): ?>
            <p class="pt-2 text-xs text-muted italic">"<?= htmlspecialchars($order['customer_note']) ?>"</p>
            <?php endif; ?>
        </div>

        <div class="bg-white rounded-3xl border border-beige p-6 shadow-warm text-xs sm:text-sm space-y-2">
            <h4 class="font-serif font-bold text-sm text-charcoal pb-2 border-b border-beige mb-3 flex items-center gap-2">
                <span>💳</span> Phương thức thanh toán
            </h4>
            <p class="font-semibold text-charcoal uppercase"><?= htmlspecialchars($order['payment_method'] ?? 'bank_transfer') ?></p>
            <p class="text-muted">
                Trạng thái: 
                <span class="font-semibold <?= ($order['payment_status'] ?? '') === 'paid' ? 'text-sage' : 'text-amber-warm' ?>">
                    <?= ($order['payment_status'] ?? '') === 'paid' ? 'Đã thanh toán' : 'Chờ thanh toán' ?>
                </span>
            </p>
        </div>
    </div>

</div>
