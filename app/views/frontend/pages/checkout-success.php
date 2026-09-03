<?php
$baseUrl   = $_ENV['APP_URL'] ?? 'http://localhost/project-ecommerce/public';
$pageTitle = 'Đặt Hàng Thành Công - DecorNest';
?>

<div class="max-w-3xl mx-auto px-4 sm:px-6 py-12 lg:py-20">

    <!-- Celebration Header -->
    <div class="text-center mb-10">
        <div class="w-20 h-20 rounded-full bg-sage-light text-sage flex items-center justify-center text-3xl mx-auto mb-4 border border-sage/20 shadow-warm">
            🌿
        </div>
        <h1 class="text-2xl sm:text-3xl font-serif font-bold text-charcoal">Đặt hàng thành công!</h1>
        <p class="text-xs sm:text-sm text-muted mt-2">Cảm ơn bạn đã tin tưởng lựa chọn DecorNest để chăm sóc không gian ngủ của mình.</p>
        <div class="mt-3 inline-block px-4 py-1.5 rounded-full bg-cream border border-sand text-xs font-semibold text-charcoal">
            Mã đơn hàng: <strong class="text-wood font-mono"><?= htmlspecialchars($order['order_number']) ?></strong>
        </div>
    </div>

    <!-- Bank Transfer / VietQR Display -->
    <?php if ($order['payment_method'] === 'bank_transfer' && !empty($bankInfo)): ?>
    <div class="bg-white border border-sand rounded-3xl p-6 sm:p-8 shadow-warm mb-8">
        <div class="flex items-center justify-between pb-4 mb-6 border-b border-beige">
            <h2 class="font-serif font-bold text-base sm:text-lg text-charcoal flex items-center gap-2">
                <span>🏦</span> Hướng dẫn chuyển khoản VietQR tự động
            </h2>
            <span class="text-xs font-semibold text-sage bg-sage-light px-3 py-1 rounded-full">
                Xử lý tự động 24/7
            </span>
        </div>

        <div class="grid sm:grid-cols-12 gap-8 items-center">
            
            <div class="sm:col-span-7 space-y-3 text-xs sm:text-sm">
                <div class="flex justify-between py-1.5 border-b border-beige/60">
                    <span class="text-muted">Ngân hàng</span>
                    <span class="font-bold text-charcoal"><?= htmlspecialchars($bankInfo['bank_name'] ?? 'Vietcombank') ?></span>
                </div>
                <div class="flex justify-between py-1.5 border-b border-beige/60">
                    <span class="text-muted">Số tài khoản</span>
                    <span class="font-bold font-mono text-base text-charcoal tracking-wide"><?= htmlspecialchars($bankInfo['account_number'] ?? '1058081721') ?></span>
                </div>
                <div class="flex justify-between py-1.5 border-b border-beige/60">
                    <span class="text-muted">Chủ tài khoản</span>
                    <span class="font-semibold text-charcoal uppercase"><?= htmlspecialchars($bankInfo['account_name'] ?? 'TRINH TIEN HUNG') ?></span>
                </div>
                <div class="flex justify-between py-1.5 border-b border-beige/60">
                    <span class="text-muted">Số tiền cần chuyển</span>
                    <span class="font-bold font-serif text-lg text-wood"><?= number_format($order['total_amount']) ?>đ</span>
                </div>
                <div class="flex justify-between items-center py-2 bg-cream/70 rounded-xl px-3 border border-sand/50">
                    <span class="text-muted font-medium text-xs">Nội dung CK</span>
                    <span class="font-mono font-bold text-charcoal bg-white px-2.5 py-1 rounded-lg border border-beige"><?= htmlspecialchars($bankInfo['transfer_content'] ?? $order['order_number']) ?></span>
                </div>
            </div>

            <!-- QR Code display -->
            <div class="sm:col-span-5 flex flex-col items-center justify-center p-4 bg-cream/30 rounded-2xl border border-beige text-center">
                <?php 
                $qrUrl = !empty($bankInfo['qr_url']) 
                    ? $bankInfo['qr_url'] 
                    : "https://img.vietqr.io/image/VCB-1058081721-compact2.png?amount=" . $order['total_amount'] . "&addInfo=" . urlencode($order['order_number']) . "&accountName=TRINH%20TIEN%20HUNG";
                ?>
                <img src="<?= htmlspecialchars($qrUrl) ?>" 
                     alt="VietQR Code chuyển khoản" 
                     class="w-44 h-44 rounded-2xl border-4 border-white shadow-warm bg-white">
                <p class="text-[11px] font-semibold text-charcoal mt-2">Mở app ngân hàng quét mã QR</p>
                <p class="text-[10px] text-muted">Hệ thống sẽ tự động xác nhận đơn trong 10 giây</p>
            </div>

        </div>

        <div class="mt-6 p-3.5 bg-amber-50/70 rounded-2xl border border-amber-200/60 text-xs text-amber-900 flex items-start gap-2">
            <span>💡</span>
            <span>Vui lòng giữ nguyên <strong>nội dung chuyển khoản</strong> để hệ thống gạch nợ và sắp xếp giao hàng nhanh nhất có thể.</span>
        </div>
    </div>
    <?php elseif ($order['payment_method'] === 'cod'): ?>
    <div class="bg-sage-light/60 border border-sage/20 rounded-3xl p-6 mb-8 text-xs sm:text-sm text-sage-dark flex items-center gap-3">
        <span class="text-2xl">💵</span>
        <div>
            <p class="font-bold text-charcoal">Thanh toán tiền mặt khi nhận hàng (COD)</p>
            <p class="text-muted mt-0.5">Bạn vui lòng chuẩn bị số tiền <strong><?= number_format($order['total_amount']) ?>đ</strong> khi nhân viên giao hàng tới.</p>
        </div>
    </div>
    <?php endif; ?>

    <!-- Order Items Summary Box -->
    <div class="bg-white border border-beige rounded-3xl overflow-hidden shadow-warm mb-8">
        <div class="px-6 py-4 bg-cream/60 border-b border-beige flex items-center justify-between">
            <h3 class="font-serif font-bold text-sm text-charcoal">Chi tiết sản phẩm decor đã đặt</h3>
            <span class="text-xs text-muted"><?= count($order['items'] ?? []) ?> món đồ</span>
        </div>

        <div class="divide-y divide-beige/60 p-6 space-y-4">
            <?php foreach (($order['items'] ?? []) as $item): ?>
            <div class="pt-4 first:pt-0 flex items-center gap-4">
                <div class="w-14 h-14 rounded-2xl overflow-hidden bg-cream border border-beige/60 flex-shrink-0">
                    <?php 
                        $img = !empty($item['product_image']) ? $item['product_image'] : '/assets/images/product-placeholder.jpg';
                        if (!str_starts_with($img, 'http') && !str_starts_with($img, '/')) {
                            $img = $baseUrl . '/' . ltrim($img, '/');
                        }
                    ?>
                    <img src="<?= htmlspecialchars($img) ?>" class="w-full h-full object-cover">
                </div>
                <div class="flex-1 min-w-0">
                    <p class="text-xs sm:text-sm font-serif font-bold text-charcoal truncate"><?= htmlspecialchars($item['product_name']) ?></p>
                    <p class="text-xs text-muted">Số lượng: <?= $item['quantity'] ?> × <?= number_format($item['unit_price']) ?>đ</p>
                </div>
                <span class="text-xs sm:text-sm font-bold text-charcoal"><?= number_format($item['subtotal']) ?>đ</span>
            </div>
            <?php endforeach; ?>
        </div>

        <div class="px-6 py-4 bg-cream/40 border-t border-beige flex justify-between items-center">
            <span class="font-serif font-bold text-sm text-charcoal">Tổng thanh toán:</span>
            <span class="font-serif font-bold text-lg text-wood"><?= number_format($order['total_amount']) ?>đ</span>
        </div>
    </div>

    <!-- Shipping address info -->
    <div class="bg-white border border-beige rounded-3xl p-6 mb-8 text-xs sm:text-sm shadow-warm">
        <h4 class="font-serif font-bold text-sm text-charcoal mb-3 flex items-center gap-2">
            <span>📍</span> Địa chỉ nhận hàng
        </h4>
        <p class="font-semibold text-charcoal"><?= htmlspecialchars($order['shipping_name'] ?? '') ?> • <?= htmlspecialchars($order['shipping_phone'] ?? '') ?></p>
        <p class="text-muted mt-1"><?= htmlspecialchars($order['shipping_address'] ?? '') ?></p>
    </div>

    <!-- Actions -->
    <div class="flex flex-col sm:flex-row gap-3.5">
        <a href="<?= $baseUrl ?>/user/orders" 
           class="flex-1 text-center bg-charcoal text-white py-3.5 rounded-2xl text-xs font-bold uppercase tracking-wider hover:bg-wooddk transition shadow-warm">
            Theo dõi đơn hàng
        </a>
        <a href="<?= $baseUrl ?>/products" 
           class="flex-1 text-center border border-beige text-charcoal py-3.5 rounded-2xl text-xs font-semibold hover:bg-cream transition">
            Khám phá thêm sản phẩm
        </a>
    </div>

</div>

<!-- Polling script for VietQR instant update -->
<?php if ($order['payment_method'] === 'bank_transfer'): ?>
<script src="<?= $baseUrl ?>/assets/js/checkout-polling.js"></script>
<script>
document.addEventListener('DOMContentLoaded', () => {
    if (typeof CheckoutPolling !== 'undefined') {
        new CheckoutPolling('<?= htmlspecialchars($order['order_number']) ?>');
    }
});
</script>
<?php endif; ?>
