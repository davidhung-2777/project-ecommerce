<?php
$baseUrl   = $_ENV['APP_URL'] ?? 'http://localhost/project-ecommerce/public';
$pageTitle = htmlspecialchars($product['name']) . ' - DecorNest';
$pageDesc  = htmlspecialchars($product['short_desc'] ?? '');

$productImg = !empty($product['thumbnail']) ? $product['thumbnail'] : (!empty($product['image_url']) ? $product['image_url'] : '/assets/images/product-placeholder.jpg');
if (!str_starts_with($productImg, 'http') && !str_starts_with($productImg, '/')) {
    $productImg = $baseUrl . '/' . ltrim($productImg, '/');
}
$allImages = array_filter(array_merge([$productImg], $product['images_arr'] ?? []));
if (empty($allImages)) $allImages = [$productImg];
?>

<!-- Breadcrumb -->
<div class="bg-cream/50 border-b border-beige/80 py-4">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <nav class="flex items-center gap-2 text-xs text-muted">
            <a href="<?= $baseUrl ?>" class="hover:text-wood transition">Trang chủ</a>
            <span class="text-sand">/</span>
            <?php if (!empty($product['category_slug'])): ?>
            <a href="<?= $baseUrl ?>/category/<?= $product['category_slug'] ?>" class="hover:text-wood transition">
                <?= htmlspecialchars($product['category_name'] ?? 'Danh mục') ?>
            </a>
            <span class="text-sand">/</span>
            <?php endif; ?>
            <span class="text-charcoal font-semibold truncate max-w-xs sm:max-w-md"><?= htmlspecialchars($product['name']) ?></span>
        </nav>
    </div>
</div>

<div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-10 lg:py-16">
    <div class="grid lg:grid-cols-12 gap-10 lg:gap-14">

        <!-- Product Gallery Column (Left) -->
        <div class="lg:col-span-6" x-data="{ activeImg: 0 }">
            <!-- Main Featured Image -->
            <div class="aspect-square rounded-3xl overflow-hidden bg-cream/70 border border-beige shadow-warm-lg mb-4 relative">
                <?php foreach (array_values($allImages) as $i => $img): ?>
                <img src="<?= htmlspecialchars($img) ?>"
                     x-show="activeImg === <?= $i ?>"
                     onerror="this.src='<?= $baseUrl ?>/assets/images/product-placeholder.jpg'"
                     alt="<?= htmlspecialchars($product['name']) ?>"
                     class="w-full h-full object-cover transition-all duration-500">
                <?php endforeach; ?>

                <!-- Badges -->
                <div class="absolute top-4 left-4 flex flex-col gap-2">
                    <?php if (!empty($product['is_new'])): ?>
                    <span class="bg-charcoal/90 backdrop-blur-sm text-warmwhite text-[10px] font-medium uppercase tracking-wider px-3 py-1 rounded-full shadow-sm">
                        Mới
                    </span>
                    <?php endif; ?>
                    <?php if (!empty($product['sale_price']) && $product['sale_price'] < $product['price']): ?>
                    <?php $discount = round((1 - $product['sale_price'] / $product['price']) * 100); ?>
                    <span class="bg-wood text-white text-xs font-bold px-3 py-1 rounded-full shadow-sm">
                        -<?= $discount ?>%
                    </span>
                    <?php endif; ?>
                </div>
            </div>

            <!-- Thumbnail Selector -->
            <?php if (count($allImages) > 1): ?>
            <div class="flex gap-3 overflow-x-auto pb-2 scrollbar-hide">
                <?php foreach (array_values($allImages) as $i => $img): ?>
                <button @click="activeImg = <?= $i ?>"
                        :class="activeImg === <?= $i ?> ? 'ring-2 ring-wood border-transparent shadow-warm' : 'border-beige hover:border-wood/60'"
                        class="w-20 h-20 rounded-2xl overflow-hidden flex-shrink-0 bg-cream border transition-all duration-200">
                    <img src="<?= htmlspecialchars($img) ?>"
                         onerror="this.src='<?= $baseUrl ?>/assets/images/product-placeholder.jpg'"
                         class="w-full h-full object-cover">
                </button>
                <?php endforeach; ?>
            </div>
            <?php endif; ?>
        </div>

        <!-- Product Purchase Information Column (Right) -->
        <div class="lg:col-span-6 flex flex-col justify-between" x-data="{ selectedSize: '', selectedColor: '', qty: 1 }">
            <div>
                <!-- Category Tag & Stock Status -->
                <div class="flex items-center justify-between gap-4 mb-2.5">
                    <?php if (!empty($product['category_name'])): ?>
                    <span class="text-xs uppercase font-bold tracking-widest text-wood">
                        <?= htmlspecialchars($product['category_name']) ?>
                    </span>
                    <?php endif; ?>

                    <span class="text-xs font-semibold px-2.5 py-1 rounded-full <?= ($product['stock'] ?? 0) > 0 ? 'bg-sage-light text-sage' : 'bg-red-50 text-red-600' ?>">
                        <?= ($product['stock'] ?? 0) > 0 ? '● Còn hàng (' . $product['stock'] . ')' : '● Tạm hết hàng' ?>
                    </span>
                </div>

                <!-- Product Name -->
                <h1 class="text-2xl sm:text-3xl lg:text-4xl font-serif font-bold text-charcoal leading-tight tracking-tight">
                    <?= htmlspecialchars($product['name']) ?>
                </h1>

                <!-- Short Description / Mood Note -->
                <?php if (!empty($product['short_desc'])): ?>
                <p class="mt-3 text-xs sm:text-sm text-muted leading-relaxed">
                    <?= htmlspecialchars($product['short_desc']) ?>
                </p>
                <?php endif; ?>

                <!-- Price Block -->
                <div class="mt-6 p-4 rounded-2xl bg-cream/50 border border-beige/80 flex items-baseline gap-3">
                    <?php $displayPrice = !empty($product['sale_price']) ? $product['sale_price'] : $product['price']; ?>
                    <span class="text-2xl sm:text-3xl font-bold font-serif text-charcoal tracking-tight" id="product-price">
                        <?= number_format($displayPrice) ?>đ
                    </span>
                    <?php if (!empty($product['sale_price']) && $product['sale_price'] < $product['price']): ?>
                    <span class="text-sm sm:text-base text-muted line-through">
                        <?= number_format($product['price']) ?>đ
                    </span>
                    <?php endif; ?>
                </div>

                <?php if (!empty($product['install_fee']) && $product['install_fee'] > 0): ?>
                <p class="text-xs text-muted mt-2 flex items-center gap-1">
                    <span>🔧</span> Phí hỗ trợ lắp đặt chuyên nghiệp tận phòng: <strong>+<?= number_format($product['install_fee']) ?>đ</strong>
                </p>
                <?php endif; ?>

                <!-- Bulk Pricing Tier Table -->
                <?php if (!empty($tiers)): ?>
                <div class="mt-6 border border-beige rounded-2xl overflow-hidden bg-white shadow-sm">
                    <div class="bg-cream px-4 py-2.5 text-[11px] font-bold uppercase tracking-wider text-charcoal flex items-center justify-between">
                        <span>🏷️ Bảng giá sỉ / Đặt số lượng lớn</span>
                        <span class="text-wood">Ưu đãi đến 20%</span>
                    </div>
                    <table class="w-full text-xs">
                        <thead>
                            <tr class="border-b border-beige/60 bg-cream/20 text-muted">
                                <th class="text-left px-4 py-2 font-medium">Số lượng</th>
                                <th class="text-right px-4 py-2 font-medium">Đơn giá</th>
                                <th class="text-right px-4 py-2 font-medium">Mức giảm</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-beige/50">
                            <?php foreach ($tiers as $tier): ?>
                            <tr class="hover:bg-cream/40 transition">
                                <td class="px-4 py-2.5 font-medium text-charcoal">
                                    <?= $tier['min_qty'] ?><?= $tier['max_qty'] ? ' – ' . $tier['max_qty'] : '+' ?> sản phẩm
                                    <?php if (!empty($tier['label'])): ?>
                                    <span class="text-muted text-[10px] ml-1">(<?= htmlspecialchars($tier['label']) ?>)</span>
                                    <?php endif; ?>
                                </td>
                                <td class="px-4 py-2.5 text-right font-bold text-charcoal"><?= number_format($tier['price']) ?>đ</td>
                                <td class="px-4 py-2.5 text-right">
                                    <?php if (!empty($tier['discount_pct']) && $tier['discount_pct'] > 0): ?>
                                    <span class="bg-wood/10 text-wood text-[10px] font-bold px-2 py-0.5 rounded-full">
                                        -<?= $tier['discount_pct'] ?>%
                                    </span>
                                    <?php else: ?>
                                    <span class="text-muted text-[11px]">Giá lẻ</span>
                                    <?php endif; ?>
                                </td>
                            </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                </div>
                <?php endif; ?>

                <!-- Size Variant Options -->
                <?php if (!empty($product['size_options_arr'])): ?>
                <div class="mt-6">
                    <label class="block text-xs font-bold uppercase tracking-wider text-charcoal mb-2.5">Kích thước</label>
                    <div class="flex flex-wrap gap-2">
                        <?php foreach ($product['size_options_arr'] as $size): ?>
                        <button type="button" @click="selectedSize = '<?= htmlspecialchars($size) ?>'"
                                :class="selectedSize === '<?= htmlspecialchars($size) ?>' ? 'bg-charcoal text-white border-charcoal shadow-sm' : 'bg-white border-beige text-charcoal hover:border-wood'"
                                class="border rounded-xl px-4 py-2.5 text-xs font-semibold transition">
                            <?= htmlspecialchars($size) ?>
                        </button>
                        <?php endforeach; ?>
                    </div>
                </div>
                <?php endif; ?>

                <!-- Color Variant Options -->
                <?php if (!empty($product['color_options_arr'])): ?>
                <div class="mt-5">
                    <label class="block text-xs font-bold uppercase tracking-wider text-charcoal mb-2.5">Tông màu</label>
                    <div class="flex flex-wrap gap-2">
                        <?php foreach ($product['color_options_arr'] as $color): ?>
                        <button type="button" @click="selectedColor = '<?= htmlspecialchars($color) ?>'"
                                :class="selectedColor === '<?= htmlspecialchars($color) ?>' ? 'bg-charcoal text-white border-charcoal shadow-sm' : 'bg-white border-beige text-charcoal hover:border-wood'"
                                class="border rounded-xl px-4 py-2.5 text-xs font-semibold transition">
                            <?= htmlspecialchars($color) ?>
                        </button>
                        <?php endforeach; ?>
                    </div>
                </div>
                <?php endif; ?>

                <!-- Quantity and Add to Cart Button -->
                <div class="mt-8 flex flex-col sm:flex-row items-stretch sm:items-center gap-3.5">
                    <!-- Quantity Stepper -->
                    <div class="flex items-center justify-between border border-beige rounded-2xl bg-white overflow-hidden shadow-sm h-12">
                        <button type="button" @click="if(qty > 1) { qty--; updateBulkPrice(qty) }"
                                class="w-12 h-full flex items-center justify-center hover:bg-cream transition text-base font-semibold text-charcoal">−</button>
                        <input type="number" x-model="qty" min="1" max="<?= $product['stock'] ?? 100 ?>"
                               @change="if(qty < 1) qty = 1; updateBulkPrice(qty)"
                               class="w-14 h-full text-center border-0 text-sm font-bold text-charcoal focus:outline-none bg-transparent">
                        <button type="button" @click="qty++; updateBulkPrice(qty)"
                                class="w-12 h-full flex items-center justify-center hover:bg-cream transition text-base font-semibold text-charcoal">+</button>
                    </div>

                    <!-- Add to Cart CTA -->
                    <button type="button" 
                            @click="addToCartWithOptions(<?= $product['id'] ?>, qty, selectedSize, selectedColor)"
                            <?= ($product['stock'] ?? 0) <= 0 ? 'disabled' : '' ?>
                            class="flex-1 bg-charcoal hover:bg-wooddk text-white h-12 px-6 rounded-2xl text-xs sm:text-sm font-bold uppercase tracking-wider transition-all duration-300 shadow-warm disabled:opacity-40 disabled:cursor-not-allowed flex items-center justify-center gap-2">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 11V7a4 4 0 00-8 0v4M5 9h14l1 7H4l1-7z"/>
                        </svg>
                        <span>Thêm vào không gian</span>
                    </button>

                    <!-- Wishlist Button -->
                    <button type="button" onclick="showToast('Đã lưu vào danh sách yêu thích ✨', 'success')" 
                            class="h-12 w-12 border border-beige rounded-2xl flex items-center justify-center text-muted hover:text-wood hover:border-wood transition bg-white shadow-sm"
                            title="Yêu thích">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.8" d="M4.318 6.318a4.5 4.5 0 000 6.364L12 20.364l7.682-7.682a4.5 4.5 0 00-6.364-6.364L12 7.636l-1.318-1.318a4.5 4.5 0 00-6.364 0z"/>
                        </svg>
                    </button>
                </div>

            </div>

            <!-- Value Guarantees Box -->
            <div class="mt-8 p-4 rounded-2xl bg-cream/40 border border-beige/80 grid grid-cols-3 gap-2 text-center text-xs text-muted">
                <div>
                    <span class="text-base block mb-0.5">🌿</span>
                    <span class="text-[11px] font-medium text-charcoal">Gỗ & Vải Tự Nhiên</span>
                </div>
                <div>
                    <span class="text-base block mb-0.5">🚚</span>
                    <span class="text-[11px] font-medium text-charcoal">Lắp Đặt Tận Phòng</span>
                </div>
                <div>
                    <span class="text-base block mb-0.5">🛡️</span>
                    <span class="text-[11px] font-medium text-charcoal">Bảo Hành 24 Tháng</span>
                </div>
            </div>

        </div>

    </div>

    <!-- Product Description & Specs Tabs -->
    <div class="mt-16 pt-10 border-t border-beige" x-data="{ tab: 'desc' }">
        <div class="flex border-b border-beige gap-8">
            <button @click="tab = 'desc'" 
                    :class="tab === 'desc' ? 'border-b-2 border-charcoal text-charcoal font-bold font-serif text-base pb-3.5' : 'text-muted hover:text-charcoal pb-3.5 text-sm font-medium'"
                    class="transition">
                Chi tiết sản phẩm & Cảm xúc
            </button>
            <button @click="tab = 'spec'" 
                    :class="tab === 'spec' ? 'border-b-2 border-charcoal text-charcoal font-bold font-serif text-base pb-3.5' : 'text-muted hover:text-charcoal pb-3.5 text-sm font-medium'"
                    class="transition">
                Thông số kỹ thuật & Chất liệu
            </button>
        </div>

        <!-- Description Tab Content -->
        <div x-show="tab === 'desc'" class="py-8 prose prose-sm max-w-none text-charcoal leading-relaxed">
            <?= !empty($product['description']) ? nl2br(htmlspecialchars($product['description'])) : '<p class="text-muted">Sản phẩm được chế tác tỉ mỉ từ vật liệu cao cấp, mang lại vẻ đẹp thanh lịch và không gian nghỉ ngơi thư thái cho gia chủ.</p>' ?>
        </div>

        <!-- Specification Tab Content -->
        <div x-show="tab === 'spec'" class="py-8 max-w-xl">
            <table class="w-full text-xs sm:text-sm border-collapse">
                <?php 
                $specs = [
                    'material'   => 'Chất liệu chế tác', 
                    'dimensions' => 'Kích thước tiêu chuẩn', 
                    'origin'     => 'Xuất xứ sản phẩm', 
                    'sku'        => 'Mã định danh (SKU)',
                    'weight'     => 'Trọng lượng'
                ]; 
                ?>
                <?php foreach ($specs as $key => $label): ?>
                <?php if (!empty($product[$key])): ?>
                <tr class="border-b border-beige">
                    <td class="py-3 pr-4 text-muted w-1/3"><?= $label ?></td>
                    <td class="py-3 font-semibold text-charcoal"><?= htmlspecialchars($product[$key]) ?></td>
                </tr>
                <?php endif; ?>
                <?php endforeach; ?>
            </table>
        </div>
    </div>

    <!-- Related Products -->
    <?php if (!empty($related)): ?>
    <div class="mt-20 pt-10 border-t border-beige/80">
        <div class="flex items-end justify-between mb-8">
            <div>
                <span class="text-xs font-semibold uppercase tracking-widest text-wood block mb-1">Gợi Ý Phối Hợp</span>
                <h2 class="text-xl sm:text-2xl font-serif font-bold text-charcoal">Sản phẩm cùng phong cách</h2>
            </div>
            <a href="<?= $baseUrl ?>/products" class="text-xs font-bold uppercase tracking-wider text-wood hover:underline">
                Xem thêm →
            </a>
        </div>

        <div class="grid grid-cols-2 sm:grid-cols-3 lg:grid-cols-4 gap-4 sm:gap-6">
            <?php $products = $related; require ROOT_PATH . '/app/views/frontend/partials/product-grid.php'; ?>
        </div>
    </div>
    <?php endif; ?>

</div>

<script>
// Bulk price tier dynamic calculation
const priceTiers = <?= json_encode($tiers ?? []) ?>;
const basePrice  = <?= (float) (!empty($product['sale_price']) ? $product['sale_price'] : $product['price']) ?>;

function updateBulkPrice(qty) {
    let price = basePrice;
    if (priceTiers && priceTiers.length > 0) {
        priceTiers.forEach(tier => {
            const minQty = parseInt(tier.min_qty);
            const maxQty = tier.max_qty ? parseInt(tier.max_qty) : Infinity;
            if (qty >= minQty && qty <= maxQty) {
                price = parseFloat(tier.price);
            }
        });
    }
    const priceEl = document.getElementById('product-price');
    if (priceEl) {
        priceEl.textContent = new Intl.NumberFormat('vi-VN').format(price) + 'đ';
    }
}

function addToCartWithOptions(productId, qty, size, color) {
    addToCart(productId, qty, { size: size, color: color });
}
</script>
