<?php
$baseUrl = $_ENV['APP_URL'] ?? 'http://localhost/project-ecommerce/public';
?>
<?php if (empty($products)): ?>
<div class="col-span-full flex flex-col items-center justify-center py-20 px-4 text-center bg-cream/40 rounded-3xl border border-dashed border-beige">
    <div class="w-16 h-16 rounded-full bg-white shadow-warm flex items-center justify-center text-3xl mb-4">
        🌿
    </div>
    <h3 class="font-serif font-bold text-lg text-charcoal">Chưa tìm thấy sản phẩm phù hợp</h3>
    <p class="text-sm text-muted mt-1 max-w-sm">Hãy thử thay đổi bộ lọc, tìm kiếm từ khóa khác hoặc khám phá toàn bộ bộ sưu tập phòng ngủ của chúng tôi.</p>
    <a href="<?= $baseUrl ?>/products" class="mt-5 px-6 py-2.5 bg-charcoal text-white rounded-full text-xs font-medium hover:bg-wooddk transition">
        Xem tất cả sản phẩm
    </a>
</div>
<?php else: ?>
<?php foreach ($products as $product): ?>
<div class="group bg-white rounded-2xl border border-beige/80 overflow-hidden hover:shadow-warm-lg hover:border-sand transition-all duration-500 flex flex-col justify-between">

    <!-- Image Box -->
    <div class="relative aspect-square overflow-hidden bg-cream/60">
        <a href="<?= $baseUrl ?>/products/<?= $product['slug'] ?>" class="block w-full h-full">
            <?php 
                $imgUrl = !empty($product['thumbnail']) ? $product['thumbnail'] : (!empty($product['image_url']) ? $product['image_url'] : $baseUrl . '/assets/images/product-placeholder.jpg');
                if (!str_starts_with($imgUrl, 'http') && !str_starts_with($imgUrl, '/')) {
                    $imgUrl = $baseUrl . '/' . ltrim($imgUrl, '/');
                }
            ?>
            <img src="<?= htmlspecialchars($imgUrl) ?>"
                 onerror="this.src='<?= $baseUrl ?>/assets/images/product-placeholder.jpg'"
                 alt="<?= htmlspecialchars($product['name']) ?>"
                 loading="lazy"
                 class="w-full h-full object-cover group-hover:scale-108 transition-transform duration-700 ease-out">
        </a>

        <!-- Badges (Top Left) -->
        <div class="absolute top-3 left-3 flex flex-col gap-1.5 pointer-events-none">
            <?php if (!empty($product['is_new'])): ?>
            <span class="bg-charcoal/90 backdrop-blur-sm text-warmwhite text-[10px] font-medium tracking-wider uppercase px-2.5 py-1 rounded-full shadow-sm">
                Mới
            </span>
            <?php endif; ?>
            <?php if (!empty($product['sale_price']) && $product['sale_price'] < $product['price']): ?>
            <?php $discount = round((1 - $product['sale_price'] / $product['price']) * 100); ?>
            <span class="bg-wood text-white text-[10px] font-bold px-2.5 py-1 rounded-full shadow-sm">
                -<?= $discount ?>%
            </span>
            <?php endif; ?>
        </div>

        <!-- Quick Action (Top Right) -->
        <div class="absolute top-3 right-3 flex flex-col gap-2 opacity-0 group-hover:opacity-100 transition-opacity duration-300">
            <button onclick="showToast('Đã lưu vào danh sách yêu thích', 'info')" 
                    class="w-9 h-9 bg-white/90 backdrop-blur-md rounded-full shadow-warm flex items-center justify-center text-muted hover:text-wood hover:scale-110 transition"
                    title="Yêu thích">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.8" d="M4.318 6.318a4.5 4.5 0 000 6.364L12 20.364l7.682-7.682a4.5 4.5 0 00-6.364-6.364L12 7.636l-1.318-1.318a4.5 4.5 0 00-6.364 0z"/>
                </svg>
            </button>
            <a href="<?= $baseUrl ?>/products/<?= $product['slug'] ?>"
               class="w-9 h-9 bg-white/90 backdrop-blur-md rounded-full shadow-warm flex items-center justify-center text-muted hover:text-wood hover:scale-110 transition"
               title="Xem chi tiết">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.8" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/>
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.8" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"/>
                </svg>
            </a>
        </div>
    </div>

    <!-- Product Info -->
    <div class="p-4 sm:p-5 flex flex-col flex-1 justify-between">
        <div>
            <!-- Category / Tag -->
            <?php if (!empty($product['category_name'])): ?>
            <p class="text-[10px] uppercase font-semibold tracking-wider text-muted/80 mb-1">
                <?= htmlspecialchars($product['category_name']) ?>
            </p>
            <?php endif; ?>

            <!-- Title -->
            <a href="<?= $baseUrl ?>/products/<?= $product['slug'] ?>" class="block group-hover:text-wood transition-colors">
                <h3 class="font-bold text-sm sm:text-base text-charcoal line-clamp-2 leading-snug tracking-tight font-serif">
                    <?= htmlspecialchars($product['name']) ?>
                </h3>
            </a>

            <!-- Description summary -->
            <?php if (!empty($product['short_desc'])): ?>
            <p class="text-xs text-muted mt-1.5 line-clamp-2 leading-relaxed">
                <?= htmlspecialchars($product['short_desc']) ?>
            </p>
            <?php endif; ?>
        </div>

        <div class="mt-4 pt-3 border-t border-beige/60">
            <!-- Price Section -->
            <div class="flex items-baseline justify-between gap-2">
                <div>
                    <?php $displayPrice = !empty($product['sale_price']) ? $product['sale_price'] : $product['price']; ?>
                    <span class="text-base sm:text-lg font-bold text-charcoal tracking-tight">
                        <?= number_format($displayPrice) ?>đ
                    </span>
                    <?php if (!empty($product['sale_price']) && $product['sale_price'] < $product['price']): ?>
                    <span class="text-xs text-muted line-through ml-1.5">
                        <?= number_format($product['price']) ?>đ
                    </span>
                    <?php endif; ?>
                </div>

                <!-- Stock pill -->
                <span class="text-[10px] font-medium px-2 py-0.5 rounded-full <?= ($product['stock'] ?? 0) > 0 ? 'bg-sage-light text-sage' : 'bg-red-50 text-red-600' ?>">
                    <?= ($product['stock'] ?? 0) > 0 ? 'Còn hàng' : 'Hết hàng' ?>
                </span>
            </div>

            <!-- Add to cart CTA -->
            <button onclick="addToCart(<?= $product['id'] ?>, 1)"
                    <?= ($product['stock'] ?? 0) <= 0 ? 'disabled' : '' ?>
                    class="mt-3.5 w-full py-2.5 px-4 bg-cream hover:bg-charcoal text-charcoal hover:text-white border border-beige hover:border-charcoal rounded-xl text-xs font-semibold tracking-wide transition-all duration-300 disabled:opacity-40 disabled:cursor-not-allowed flex items-center justify-center gap-2 group/btn shadow-sm">
                <svg class="w-3.5 h-3.5 opacity-70 group-hover/btn:opacity-100 transition-opacity" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 11V7a4 4 0 00-8 0v4M5 9h14l1 7H4l1-7z"/>
                </svg>
                <span>Thêm vào không gian</span>
            </button>
        </div>

    </div>

</div>
<?php endforeach; ?>
<?php endif; ?>
