<?php
// Rendered via AJAX - cart items listing in mini cart
$baseUrl = $_ENV['APP_URL'] ?? 'http://localhost/project-ecommerce/public';
?>
<?php if (empty($cartData['items'])): ?>
<div class="flex flex-col items-center justify-center gap-3 text-muted py-12 text-center">
    <div class="w-16 h-16 rounded-full bg-cream flex items-center justify-center text-2xl">
        🕯️
    </div>
    <p class="text-sm font-semibold text-charcoal">Giỏ hàng của bạn đang trống</p>
    <p class="text-xs text-muted max-w-xs">Hãy chọn những món đồ dịu êm để vỗ về giấc ngủ và không gian nghỉ ngơi.</p>
</div>
<?php else: ?>
<div class="space-y-4 divide-y divide-beige/60">
    <?php foreach ($cartData['items'] as $item): ?>
    <div class="pt-4 first:pt-0 flex gap-3.5 group" data-item-id="<?= $item['id'] ?>">
        <!-- Product Thumbnail -->
        <div class="w-18 h-18 sm:w-20 sm:h-20 rounded-xl overflow-hidden bg-cream border border-beige/60 flex-shrink-0">
            <?php 
                $img = !empty($item['thumbnail']) ? $item['thumbnail'] : (!empty($item['image_url']) ? $item['image_url'] : '/assets/images/product-placeholder.jpg');
                if (!str_starts_with($img, 'http') && !str_starts_with($img, '/')) {
                    $img = $baseUrl . '/' . ltrim($img, '/');
                }
            ?>
            <img src="<?= htmlspecialchars($img) ?>"
                 onerror="this.src='<?= $baseUrl ?>/assets/images/product-placeholder.jpg'"
                 alt="<?= htmlspecialchars($item['product_name']) ?>"
                 class="w-full h-full object-cover group-hover:scale-105 transition-transform duration-300">
        </div>

        <!-- Product Details -->
        <div class="flex-1 min-w-0 flex flex-col justify-between">
            <div>
                <div class="flex items-start justify-between gap-2">
                    <a href="<?= $baseUrl ?>/products/<?= $item['product_slug'] ?>"
                       class="text-xs sm:text-sm font-serif font-bold text-charcoal hover:text-wood line-clamp-2 leading-snug">
                        <?= htmlspecialchars($item['product_name']) ?>
                    </a>
                    <button onclick="removeCartItem(<?= $item['id'] ?>)"
                            class="text-muted/60 hover:text-red-500 p-1 transition"
                            title="Xóa">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.8" d="M6 18L18 6M6 6l12 12"/>
                        </svg>
                    </button>
                </div>

                <?php if (!empty($item['size_option']) || !empty($item['color_option'])): ?>
                <p class="text-[11px] text-muted mt-0.5">
                    <?= !empty($item['size_option']) ? 'KT: ' . htmlspecialchars($item['size_option']) : '' ?>
                    <?= !empty($item['color_option']) ? ' • Màu: ' . htmlspecialchars($item['color_option']) : '' ?>
                </p>
                <?php endif; ?>
            </div>

            <!-- Price & Quantity Control -->
            <div class="flex items-center justify-between mt-3 pt-2">
                <span class="text-xs sm:text-sm font-bold text-charcoal">
                    <?= number_format($item['unit_price']) ?>đ
                </span>

                <div class="flex items-center border border-beige rounded-lg bg-cream/50 overflow-hidden">
                    <button onclick="updateCartItem(<?= $item['id'] ?>, <?= $item['quantity'] - 1 ?>)"
                            class="w-6 h-6 flex items-center justify-center text-xs hover:bg-cream transition text-muted hover:text-charcoal">−</button>
                    <span class="w-7 text-center text-xs font-semibold text-charcoal"><?= $item['quantity'] ?></span>
                    <button onclick="updateCartItem(<?= $item['id'] ?>, <?= $item['quantity'] + 1 ?>)"
                            class="w-6 h-6 flex items-center justify-center text-xs hover:bg-cream transition text-muted hover:text-charcoal">+</button>
                </div>
            </div>
        </div>
    </div>
    <?php endforeach; ?>
</div>
<?php endif; ?>
