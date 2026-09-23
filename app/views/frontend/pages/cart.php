<?php
$baseUrl   = $_ENV['APP_URL'] ?? 'http://localhost/project-ecommerce/public';
$pageTitle = 'Giỏ Hàng Của Bạn - DecorNest';
$items     = $cartData['items'] ?? [];
$subtotal  = $cartData['subtotal'] ?? 0;
?>

<div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-10 lg:py-16">
    <div class="flex items-center justify-between pb-6 border-b border-beige mb-8">
        <div>
            <h1 class="text-2xl sm:text-3xl font-serif font-bold text-charcoal">Không gian phòng ngủ đã chọn</h1>
            <p class="text-xs sm:text-sm text-muted mt-1">Kiểm tra các món đồ decor và chuẩn bị cho không gian mới</p>
        </div>
        <a href="<?= $baseUrl ?>/products" class="text-xs font-semibold text-wood hover:underline hidden sm:inline-flex items-center gap-1">
            ← Tiếp tục chọn đồ
        </a>
    </div>

    <?php if (empty($items)): ?>
    <div class="text-center py-20 px-4 bg-cream/40 rounded-3xl border border-dashed border-beige max-w-2xl mx-auto">
        <div class="w-20 h-20 rounded-full bg-white shadow-warm flex items-center justify-center text-3xl mx-auto mb-4">
            🕯️
        </div>
        <h2 class="text-xl font-serif font-bold text-charcoal">Giỏ hàng của bạn đang trống</h2>
        <p class="text-xs sm:text-sm text-muted mt-2 max-w-sm mx-auto leading-relaxed">
            Hãy khám phá bộ sưu tập đồ trang trí phòng ngủ của DecorNest để mang sự an yên, dịu êm về căn phòng của bạn.
        </p>
        <div class="mt-8">
            <a href="<?= $baseUrl ?>/products" class="inline-flex items-center gap-2 bg-charcoal text-white px-8 py-3.5 rounded-full text-xs font-bold uppercase tracking-wider hover:bg-wooddk transition shadow-warm">
                <span>Khám phá sản phẩm</span>
                <span>→</span>
            </a>
        </div>
    </div>
    <?php else: ?>

    <div class="grid lg:grid-cols-12 gap-10">
        <!-- Cart Items List (Left) -->
        <div class="lg:col-span-8 space-y-4">
            <!-- Table Header (Desktop) -->
            <div class="hidden sm:grid grid-cols-12 gap-4 pb-3 border-b border-beige text-[11px] font-bold text-muted uppercase tracking-wider">
                <div class="col-span-6">Sản phẩm decor</div>
                <div class="col-span-2 text-center">Đơn giá</div>
                <div class="col-span-2 text-center">Số lượng</div>
                <div class="col-span-2 text-right">Tổng</div>
            </div>

            <!-- Items -->
            <div class="divide-y divide-beige/80">
                <?php foreach ($items as $item): ?>
                <div class="grid grid-cols-12 gap-4 py-5 items-center group" id="cart-row-<?= $item['id'] ?>">
                    
                    <!-- Product Info -->
                    <div class="col-span-12 sm:col-span-6 flex gap-4">
                        <div class="w-20 h-20 rounded-2xl overflow-hidden bg-cream border border-beige/60 flex-shrink-0 shadow-sm">
                            <?php 
                                $thumb = !empty($item['thumbnail']) ? $item['thumbnail'] : (!empty($item['image_url']) ? $item['image_url'] : '/assets/images/product-placeholder.jpg');
                                if (!str_starts_with($thumb, 'http') && !str_starts_with($thumb, '/')) {
                                    $thumb = $baseUrl . '/' . ltrim($thumb, '/');
                                }
                            ?>
                            <img src="<?= htmlspecialchars($thumb) ?>"
                                 onerror="this.src='<?= $baseUrl ?>/assets/images/product-placeholder.jpg'"
                                 alt="<?= htmlspecialchars($item['product_name']) ?>"
                                 class="w-full h-full object-cover">
                        </div>
                        <div class="flex flex-col justify-center">
                            <a href="<?= $baseUrl ?>/products/<?= $item['product_slug'] ?>"
                               class="font-serif font-bold text-sm text-charcoal hover:text-wood transition leading-snug">
                                <?= htmlspecialchars($item['product_name']) ?>
                            </a>
                            <?php if (!empty($item['size_option']) || !empty($item['color_option'])): ?>
                            <p class="text-xs text-muted mt-1">
                                <?= !empty($item['size_option']) ? 'Kích thước: ' . htmlspecialchars($item['size_option']) : '' ?>
                                <?= !empty($item['color_option']) ? ' • Màu: ' . htmlspecialchars($item['color_option']) : '' ?>
                            </p>
                            <?php endif; ?>
                            <button onclick="removeCartItem(<?= $item['id'] ?>)" 
                                    class="mt-2 text-xs text-red-400 hover:text-red-600 transition flex items-center gap-1 w-fit">
                                <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.8" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/>
                                </svg>
                                <span>Xóa</span>
                            </button>
                        </div>
                    </div>

                    <!-- Unit Price -->
                    <div class="col-span-4 sm:col-span-2 text-left sm:text-center">
                        <span class="sm:hidden text-[10px] text-muted uppercase block">Đơn giá:</span>
                        <span class="text-xs sm:text-sm font-semibold text-charcoal"><?= number_format($item['unit_price']) ?>đ</span>
                    </div>

                    <!-- Quantity Stepper -->
                    <div class="col-span-4 sm:col-span-2 flex sm:justify-center">
                        <div class="flex items-center border border-beige rounded-xl bg-cream/40 overflow-hidden shadow-sm">
                            <button onclick="updateCartItem(<?= $item['id'] ?>, <?= $item['quantity'] - 1 ?>)"
                                    class="w-7 h-8 flex items-center justify-center hover:bg-cream transition text-xs text-charcoal">−</button>
                            <input type="number" value="<?= $item['quantity'] ?>" min="1"
                                   onchange="updateCartItem(<?= $item['id'] ?>, this.value)"
                                   class="w-9 h-8 text-center border-0 text-xs font-bold text-charcoal focus:outline-none bg-transparent">
                            <button onclick="updateCartItem(<?= $item['id'] ?>, <?= $item['quantity'] + 1 ?>)"
                                    class="w-7 h-8 flex items-center justify-center hover:bg-cream transition text-xs text-charcoal">+</button>
                        </div>
                    </div>

                    <!-- Line Total -->
                    <div class="col-span-4 sm:col-span-2 text-right">
                        <span class="sm:hidden text-[10px] text-muted uppercase block">Thành tiền:</span>
                        <span class="text-sm font-bold text-charcoal font-serif" id="line-total-<?= $item['id'] ?>">
                            <?= number_format($item['line_total']) ?>đ
                        </span>
                    </div>

                </div>
                <?php endforeach; ?>
            </div>
        </div>

        <!-- Order Summary & Checkout Card (Right) -->
        <div class="lg:col-span-4">
            <div class="bg-cream/60 border border-beige rounded-3xl p-6 sm:p-8 sticky top-28 space-y-6 shadow-warm">
                <h3 class="font-serif font-bold text-lg text-charcoal pb-4 border-b border-beige">Tóm tắt đơn hàng</h3>

                <div class="space-y-3.5 text-xs sm:text-sm">
                    <div class="flex justify-between text-muted">
                        <span>Tạm tính</span>
                        <span id="cart-subtotal" class="font-semibold text-charcoal"><?= number_format($subtotal) ?>đ</span>
                    </div>
                    <div class="flex justify-between text-muted">
                        <span>Phí lắp đặt</span>
                        <span class="text-sage font-medium"><?= ($cartData['installation_fee'] ?? 0) > 0 ? number_format($cartData['installation_fee']) . 'đ' : 'Miễn phí' ?></span>
                    </div>
                </div>

                <div class="p-3.5 rounded-2xl bg-white border border-beige space-y-2">
                    <div class="flex justify-between text-[11px] font-semibold">
                        <span class="text-sage flex items-center gap-1">🌿 Phí lắp đặt được lấy theo cấu hình sản phẩm.</span>
                    </div>
                </div>

                <div class="pt-4 border-t border-beige flex items-center justify-between">
                    <span class="font-serif font-bold text-sm sm:text-base text-charcoal">Tổng cộng:</span>
                    <span id="cart-total" class="font-serif font-bold text-xl sm:text-2xl text-charcoal tracking-tight">
                        <?= number_format($subtotal) ?>đ
                    </span>
                </div>

                <div class="space-y-3 pt-2">
                    <a href="<?= $baseUrl ?>/checkout"
                       class="block w-full text-center bg-charcoal text-white py-4 rounded-2xl text-xs font-bold uppercase tracking-wider hover:bg-wooddk transition shadow-warm">
                        Tiến hành đặt hàng →
                    </a>
                    <a href="<?= $baseUrl ?>/products"
                       class="block w-full text-center border border-beige text-charcoal py-3 rounded-2xl text-xs font-semibold hover:bg-white transition">
                        Tiếp tục mua sắm
                    </a>
                </div>

                <div class="pt-4 text-center">
                    <p class="text-[11px] text-muted flex items-center justify-center gap-1.5">
                        <span>🛡️</span> Thanh toán bảo mật với VietQR & Chuyển khoản
                    </p>
                </div>
            </div>
        </div>
    </div>
    <?php endif; ?>
</div>
