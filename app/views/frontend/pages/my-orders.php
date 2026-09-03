<?php
$baseUrl   = $_ENV['APP_URL'] ?? 'http://localhost/project-ecommerce/public';
$pageTitle = 'Đơn Hàng Của Tôi - DecorNest';
$orders    = $orders ?? $items ?? [];
?>

<div class="max-w-5xl mx-auto px-4 sm:px-6 lg:px-8 py-10 lg:py-16">

    <div class="flex items-center justify-between pb-6 border-b border-beige mb-8">
        <div>
            <h1 class="text-2xl sm:text-3xl font-serif font-bold text-charcoal">Đơn hàng của tôi</h1>
            <p class="text-xs sm:text-sm text-muted mt-1">Theo dõi trạng thái giao hàng và lịch sử mua sắm decor</p>
        </div>
        <a href="<?= $baseUrl ?>/products" class="text-xs font-semibold text-wood hover:underline">
            ← Tiếp tục mua sắm
        </a>
    </div>

    <?php if (empty($orders)): ?>
    <div class="text-center py-20 px-4 bg-cream/40 rounded-3xl border border-dashed border-beige max-w-xl mx-auto">
        <div class="w-16 h-16 rounded-full bg-white shadow-warm flex items-center justify-center text-3xl mx-auto mb-4">
            📦
        </div>
        <h2 class="text-lg font-serif font-bold text-charcoal">Chưa có đơn hàng nào</h2>
        <p class="text-xs text-muted mt-1 max-w-xs mx-auto">Bạn chưa đặt đơn hàng nào tại DecorNest. Hãy khám phá ngay các sản phẩm decor phòng ngủ tuyệt đẹp nhé.</p>
        <a href="<?= $baseUrl ?>/products" class="mt-6 inline-block px-7 py-3 bg-charcoal text-white rounded-full text-xs font-bold uppercase tracking-wider hover:bg-wooddk transition shadow-warm">
            Khám phá ngay
        </a>
    </div>
    <?php else: ?>
    
    <div class="space-y-4">
        <?php foreach ($orders as $order): ?>
        <div class="bg-white rounded-3xl border border-beige p-6 sm:p-7 shadow-warm hover:border-sand transition flex flex-col sm:flex-row items-start sm:items-center justify-between gap-4">
            <div class="space-y-2">
                <div class="flex items-center gap-3">
                    <span class="font-serif font-bold text-base text-charcoal">#<?= htmlspecialchars($order['order_number']) ?></span>
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
                    <span class="px-3 py-0.5 text-[10px] font-semibold rounded-full border <?= $statusColors[$order['status']] ?? 'bg-cream text-muted border-sand' ?>">
                        <?= $statusLabels[$order['status']] ?? $order['status'] ?>
                    </span>
                </div>
                <p class="text-xs text-muted">
                    Đặt ngày: <?= date('d/m/Y H:i', strtotime($order['created_at'])) ?>
                </p>
                <p class="text-xs text-muted">
                    Người nhận: <strong class="text-charcoal"><?= htmlspecialchars($order['shipping_name'] ?? '') ?></strong> • <?= htmlspecialchars($order['shipping_phone'] ?? '') ?>
                </p>
            </div>

            <div class="flex sm:flex-col items-center sm:items-end justify-between w-full sm:w-auto gap-4 pt-3 sm:pt-0 border-t sm:border-t-0 border-beige/60">
                <p class="text-base sm:text-lg font-serif font-bold text-wood">
                    <?= number_format($order['total_amount']) ?>đ
                </p>
                <a href="<?= $baseUrl ?>/user/orders/<?= $order['id'] ?>"
                   class="px-5 py-2 rounded-xl bg-cream hover:bg-charcoal text-charcoal hover:text-white border border-beige transition text-xs font-semibold">
                    Xem chi tiết →
                </a>
            </div>
        </div>
        <?php endforeach; ?>
    </div>

    <!-- Pagination -->
    <?php if (($last_page ?? 1) > 1): ?>
    <div class="mt-10 flex justify-center">
        <?php require ROOT_PATH . '/app/views/frontend/partials/pagination.php'; ?>
    </div>
    <?php endif; ?>

    <?php endif; ?>

</div>
