<?php
$baseUrl   = $_ENV['APP_URL'] ?? 'http://localhost/project-ecommerce/public';
$pageTitle = 'Thanh Toán Đơn Hàng - DecorNest';
$items     = $cartData['items'] ?? [];
$subtotal  = $cartData['subtotal'] ?? 0;
$shippingFee = (float) ($cartData['installation_fee'] ?? 0);
$errors    = $_SESSION['checkout_errors'] ?? [];
$oldInput  = $_SESSION['checkout_input'] ?? [];
unset($_SESSION['checkout_errors'], $_SESSION['checkout_input']);
?>

<div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-10 lg:py-16">

    <!-- Steps Indicator -->
    <div class="flex items-center justify-center gap-3 mb-10 max-w-md mx-auto">
        <div class="flex items-center gap-2 text-xs font-semibold text-muted">
            <span class="w-7 h-7 rounded-full bg-sage-light text-sage font-bold flex items-center justify-center text-xs">✓</span>
            <span>Giỏ hàng</span>
        </div>
        <div class="w-10 h-px bg-sand"></div>
        <div class="flex items-center gap-2 text-xs font-bold text-charcoal">
            <span class="w-7 h-7 rounded-full bg-charcoal text-white flex items-center justify-center text-xs shadow-warm">2</span>
            <span>Thanh toán</span>
        </div>
        <div class="w-10 h-px bg-sand"></div>
        <div class="flex items-center gap-2 text-xs text-muted">
            <span class="w-7 h-7 rounded-full bg-cream text-muted flex items-center justify-center text-xs border border-beige">3</span>
            <span>Hoàn tất</span>
        </div>
    </div>

    <?php if ($errors): ?>
    <div class="mb-8 p-4 bg-red-50 border border-red-200 rounded-2xl max-w-4xl mx-auto">
        <ul class="list-disc list-inside space-y-1 text-xs text-red-700">
            <?php foreach ($errors as $e): ?><li><?= htmlspecialchars($e) ?></li><?php endforeach; ?>
        </ul>
    </div>
    <?php endif; ?>

    <form action="<?= $baseUrl ?>/checkout/process" method="POST" id="checkout-form"
          x-data="{
              invoiceType: '<?= $oldInput['invoice_type'] ?? 'retail' ?>',
              paymentMethod: '<?= $oldInput['payment_method'] ?? 'bank_transfer' ?>',
              subtotal: <?= (float)$subtotal ?>,
              shippingFee: <?= (float)$shippingFee ?>,
              get vatAmount() { return this.invoiceType === 'vat' ? Math.round(this.subtotal * 0.10) : 0; },
              get total() { return this.subtotal + this.shippingFee + this.vatAmount; }
          }">

        <div class="grid lg:grid-cols-12 gap-10">
            
            <!-- Left: Checkout Forms -->
            <div class="lg:col-span-8 space-y-8">

                <!-- 1. Shipping Address -->
                <div class="bg-white border border-beige rounded-3xl p-6 sm:p-8 shadow-warm">
                    <h2 class="font-serif font-bold text-lg text-charcoal mb-6 flex items-center gap-3">
                        <span class="w-7 h-7 rounded-full bg-cream text-wood font-sans text-xs font-bold flex items-center justify-center border border-sand">1</span>
                        <span>Thông tin giao hàng & Lắp đặt</span>
                    </h2>
                    
                    <div class="grid sm:grid-cols-2 gap-4">
                        <div>
                            <label class="block text-[11px] font-semibold text-muted uppercase tracking-wider mb-1.5">Họ và tên người nhận *</label>
                            <input type="text" name="shipping_name" required
                                   value="<?= htmlspecialchars($oldInput['shipping_name'] ?? $user['name'] ?? '') ?>"
                                   placeholder="Nguyễn Văn A"
                                   class="w-full bg-cream/40 border border-beige rounded-xl px-4 py-3 text-xs sm:text-sm focus:bg-white focus:outline-none focus:border-wood">
                        </div>
                        <div>
                            <label class="block text-[11px] font-semibold text-muted uppercase tracking-wider mb-1.5">Số điện thoại liên hệ *</label>
                            <input type="tel" name="shipping_phone" required
                                   value="<?= htmlspecialchars($oldInput['shipping_phone'] ?? $user['phone'] ?? '') ?>"
                                   placeholder="0901 234 567"
                                   class="w-full bg-cream/40 border border-beige rounded-xl px-4 py-3 text-xs sm:text-sm focus:bg-white focus:outline-none focus:border-wood">
                        </div>
                        <div class="sm:col-span-2">
                            <label class="block text-[11px] font-semibold text-muted uppercase tracking-wider mb-1.5">Địa chỉ giao hàng (Số nhà, tên đường, phường/xã) *</label>
                            <input type="text" name="shipping_address" required
                                   value="<?= htmlspecialchars($oldInput['shipping_address'] ?? '') ?>"
                                   placeholder="Ví dụ: 120 Đường Láng, Phường Láng Thượng"
                                   class="w-full bg-cream/40 border border-beige rounded-xl px-4 py-3 text-xs sm:text-sm focus:bg-white focus:outline-none focus:border-wood">
                        </div>
                        <div>
                            <label class="block text-[11px] font-semibold text-muted uppercase tracking-wider mb-1.5">Tỉnh / Thành phố *</label>
                            <input type="text" name="shipping_city" required
                                   value="<?= htmlspecialchars($oldInput['shipping_city'] ?? '') ?>"
                                   placeholder="Hà Nội / TP. HCM..."
                                   class="w-full bg-cream/40 border border-beige rounded-xl px-4 py-3 text-xs sm:text-sm focus:bg-white focus:outline-none focus:border-wood">
                        </div>
                        <div>
                            <label class="block text-[11px] font-semibold text-muted uppercase tracking-wider mb-1.5">Quận / Huyện</label>
                            <input type="text" name="shipping_district"
                                   value="<?= htmlspecialchars($oldInput['shipping_district'] ?? '') ?>"
                                   placeholder="Đống Đa, Ba Đình..."
                                   class="w-full bg-cream/40 border border-beige rounded-xl px-4 py-3 text-xs sm:text-sm focus:bg-white focus:outline-none focus:border-wood">
                        </div>
                    </div>

                    <div class="mt-4">
                        <label class="block text-[11px] font-semibold text-muted uppercase tracking-wider mb-1.5">Ghi chú giao hàng / Giờ nhận thuận tiện</label>
                        <textarea name="customer_note" rows="2" placeholder="Ví dụ: Giao sau 17h, gọi trước khi đến..."
                                  class="w-full bg-cream/40 border border-beige rounded-xl px-4 py-3 text-xs sm:text-sm focus:bg-white focus:outline-none focus:border-wood resize-none"><?= htmlspecialchars($oldInput['customer_note'] ?? '') ?></textarea>
                    </div>
                </div>

                <!-- 2. Invoice Option -->
                <div class="bg-white border border-beige rounded-3xl p-6 sm:p-8 shadow-warm">
                    <h2 class="font-serif font-bold text-lg text-charcoal mb-6 flex items-center gap-3">
                        <span class="w-7 h-7 rounded-full bg-cream text-wood font-sans text-xs font-bold flex items-center justify-center border border-sand">2</span>
                        <span>Loại hóa đơn</span>
                    </h2>

                    <div class="grid sm:grid-cols-2 gap-3.5">
                        <label :class="invoiceType === 'retail' ? 'border-charcoal bg-cream/70 font-semibold' : 'border-beige bg-white text-muted'"
                               class="flex items-start gap-3 p-4 border rounded-2xl cursor-pointer transition shadow-sm">
                            <input type="radio" name="invoice_type" value="retail" x-model="invoiceType" class="mt-0.5 accent-charcoal">
                            <div>
                                <span class="font-bold text-sm text-charcoal block">Hóa đơn bán lẻ thông thường</span>
                                <span class="text-xs text-muted">Dành cho khách hàng cá nhân</span>
                            </div>
                        </label>
                        <label :class="invoiceType === 'vat' ? 'border-charcoal bg-cream/70 font-semibold' : 'border-beige bg-white text-muted'"
                                class="flex items-start gap-3 p-4 border rounded-2xl cursor-pointer transition shadow-sm">
                            <input type="radio" name="invoice_type" value="vat" x-model="invoiceType" class="mt-0.5 accent-charcoal">
                            <div>
                                <span class="font-bold text-sm text-charcoal block">Hóa đơn điện tử VAT (10%)</span>
                                <span class="text-xs text-muted">Dành cho công ty / khách sạn / homestay</span>
                            </div>
                        </label>
                    </div>

                    <!-- VAT Information Form -->
                    <div x-show="invoiceType === 'vat'" class="mt-5 p-5 bg-cream/60 border border-sand rounded-2xl space-y-4">
                        <p class="text-xs font-bold text-charcoal">Thông tin xuất hóa đơn đỏ (VAT)</p>
                        <div class="grid sm:grid-cols-2 gap-4">
                            <div>
                                <label class="block text-[11px] text-muted mb-1 font-semibold uppercase">Tên công ty / Đơn vị *</label>
                                <input type="text" name="vat_company_name"
                                       value="<?= htmlspecialchars($oldInput['vat_company_name'] ?? '') ?>"
                                       placeholder="Công ty TNHH..."
                                       class="w-full bg-white border border-beige rounded-xl px-3.5 py-2 text-xs focus:outline-none focus:border-wood">
                            </div>
                            <div>
                                <label class="block text-[11px] text-muted mb-1 font-semibold uppercase">Mã số thuế *</label>
                                <input type="text" name="vat_tax_code"
                                       value="<?= htmlspecialchars($oldInput['vat_tax_code'] ?? '') ?>"
                                       placeholder="0101234567"
                                       class="w-full bg-white border border-beige rounded-xl px-3.5 py-2 text-xs focus:outline-none focus:border-wood">
                            </div>
                            <div class="sm:col-span-2">
                                <label class="block text-[11px] text-muted mb-1 font-semibold uppercase">Địa chỉ đăng ký kinh doanh *</label>
                                <input type="text" name="vat_address"
                                       value="<?= htmlspecialchars($oldInput['vat_address'] ?? '') ?>"
                                       class="w-full bg-white border border-beige rounded-xl px-3.5 py-2 text-xs focus:outline-none focus:border-wood">
                            </div>
                        </div>
                    </div>
                </div>

                <!-- 3. Payment Method -->
                <div class="bg-white border border-beige rounded-3xl p-6 sm:p-8 shadow-warm">
                    <h2 class="font-serif font-bold text-lg text-charcoal mb-6 flex items-center gap-3">
                        <span class="w-7 h-7 rounded-full bg-cream text-wood font-sans text-xs font-bold flex items-center justify-center border border-sand">3</span>
                        <span>Phương thức thanh toán</span>
                    </h2>

                    <div class="space-y-3">
                        <?php
                        $methods = [
                            'bank_transfer' => [
                                'icon' => '🏦', 
                                'name' => 'Chuyển khoản VietQR tự động (Khuyên dùng)', 
                                'desc' => 'Quét mã QR tự động duyệt trong 10 giây qua Vietcombank, Techcombank, MB...'
                            ],
                            'cod' => [
                                'icon' => '💵', 
                                'name' => 'Thanh toán tiền mặt khi nhận hàng (COD)', 
                                'desc' => 'Kiểm tra sản phẩm decor tại nhà trước khi thanh toán'
                            ],
                            'momo' => [
                                'icon' => '📱', 
                                'name' => 'Ví điện tử MoMo', 
                                'desc' => 'Thanh toán tức thì qua ứng dụng MoMo'
                            ],
                            'vnpay' => [
                                'icon' => '💳', 
                                'name' => 'Cổng thanh toán VNPay', 
                                'desc' => 'Hỗ trợ thẻ ATM nội địa, Visa, Mastercard, JCB'
                            ],
                        ];
                        ?>
                        <?php foreach ($methods as $key => $method): ?>
                        <label :class="paymentMethod === '<?= $key ?>' ? 'border-charcoal bg-cream/70 font-semibold' : 'border-beige bg-white text-muted'"
                               class="flex items-center gap-4 p-4 border rounded-2xl cursor-pointer transition shadow-sm">
                            <input type="radio" name="payment_method" value="<?= $key ?>" x-model="paymentMethod" required class="accent-charcoal">
                            <span class="text-2xl"><?= $method['icon'] ?></span>
                            <div>
                                <span class="font-bold text-sm text-charcoal block"><?= $method['name'] ?></span>
                                <span class="text-xs text-muted"><?= $method['desc'] ?></span>
                            </div>
                        </label>
                        <?php endforeach; ?>
                    </div>
                </div>

            </div>

            <!-- Right: Order Summary -->
            <div class="lg:col-span-4">
                <div class="bg-cream/60 border border-beige rounded-3xl p-6 sm:p-8 sticky top-28 space-y-6 shadow-warm">
                    <h3 class="font-serif font-bold text-lg text-charcoal pb-4 border-b border-beige">Chi tiết đơn hàng</h3>

                    <!-- Mini items preview -->
                    <div class="space-y-3.5 max-h-64 overflow-y-auto divide-y divide-beige/50">
                        <?php foreach ($items as $item): ?>
                        <div class="pt-3 first:pt-0 flex items-center gap-3">
                            <div class="w-12 h-12 rounded-xl overflow-hidden bg-white border border-beige/60 flex-shrink-0">
                                <?php 
                                    $itemThumb = !empty($item['thumbnail']) ? $item['thumbnail'] : (!empty($item['image_url']) ? $item['image_url'] : '/assets/images/product-placeholder.jpg');
                                    if (!str_starts_with($itemThumb, 'http') && !str_starts_with($itemThumb, '/')) {
                                        $itemThumb = $baseUrl . '/' . ltrim($itemThumb, '/');
                                    }
                                ?>
                                <img src="<?= htmlspecialchars($itemThumb) ?>" class="w-full h-full object-cover">
                            </div>
                            <div class="flex-1 min-w-0">
                                <p class="text-xs font-serif font-bold text-charcoal line-clamp-1"><?= htmlspecialchars($item['product_name']) ?></p>
                                <p class="text-[11px] text-muted">SL: <?= $item['quantity'] ?> × <?= number_format($item['unit_price']) ?>đ</p>
                            </div>
                            <span class="text-xs font-bold text-charcoal"><?= number_format($item['line_total']) ?>đ</span>
                        </div>
                        <?php endforeach; ?>
                    </div>

                    <!-- Price calculations -->
                    <div class="border-t border-beige pt-4 space-y-2.5 text-xs sm:text-sm">
                        <div class="flex justify-between text-muted">
                            <span>Tạm tính</span>
                            <span class="font-semibold text-charcoal"><?= number_format($subtotal) ?>đ</span>
                        </div>
                        <div class="flex justify-between text-muted">
                            <span>Phí lắp đặt</span>
                            <span class="text-sage font-medium"><?= $shippingFee === 0 ? 'Miễn phí' : number_format($shippingFee) . 'đ' ?></span>
                        </div>
                        <div x-show="invoiceType === 'vat'" class="flex justify-between text-muted">
                            <span>Thuế VAT (10%)</span>
                            <span class="font-semibold text-charcoal" x-text="new Intl.NumberFormat('vi-VN').format(vatAmount) + 'đ'"></span>
                        </div>
                        <div class="border-t border-beige pt-3 flex justify-between font-serif font-bold text-base sm:text-lg text-charcoal">
                            <span>Tổng thanh toán</span>
                            <span class="text-wood tracking-tight" x-text="new Intl.NumberFormat('vi-VN').format(total) + 'đ'"></span>
                        </div>
                    </div>

                    <!-- Submit CTA -->
                    <button type="submit" 
                            class="w-full bg-charcoal hover:bg-wooddk text-white py-4 rounded-2xl text-xs font-bold uppercase tracking-wider transition-all duration-300 shadow-warm flex items-center justify-center gap-2">
                        <span>Hoàn tất đặt hàng</span>
                        <span>→</span>
                    </button>

                    <p class="text-[11px] text-muted text-center leading-relaxed">
                        Bằng việc nhấn đặt hàng, bạn xác nhận đồng ý với các chính sách bảo hành và đổi trả của DecorNest.
                    </p>
                </div>
            </div>

        </div>

    </form>
</div>
