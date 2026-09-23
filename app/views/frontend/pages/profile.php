<?php
$baseUrl   = $_ENV['APP_URL'] ?? 'http://localhost/project-ecommerce/public';
$pageTitle = 'Hồ Sơ Của Tôi - DecorNest';
?>

<div class="max-w-4xl mx-auto px-4 sm:px-6 lg:px-8 py-10 lg:py-16">
    
    <div class="pb-6 border-b border-beige mb-8">
        <h1 class="text-2xl sm:text-3xl font-serif font-bold text-charcoal">Thông tin tài khoản</h1>
        <p class="text-xs sm:text-sm text-muted mt-1">Cập nhật thông tin liên hệ và địa chỉ xuất hóa đơn doanh nghiệp</p>
    </div>

    <div class="grid md:grid-cols-12 gap-8">
        
        <!-- Profile Form (Left) -->
        <div class="md:col-span-8 bg-white rounded-3xl border border-beige p-6 sm:p-8 shadow-warm">
            <form action="<?= $baseUrl ?>/user/profile/update" method="POST" class="space-y-5">
                
                <div>
                    <label class="block text-[11px] font-bold text-muted uppercase tracking-wider mb-1.5">Họ và tên *</label>
                    <input type="text" name="name" required value="<?= htmlspecialchars($user['name'] ?? '') ?>"
                           class="w-full bg-cream/40 border border-beige rounded-2xl px-4 py-3 text-xs sm:text-sm focus:bg-white focus:outline-none focus:border-wood">
                </div>

                <div>
                    <label class="block text-[11px] font-bold text-muted uppercase tracking-wider mb-1.5">Email (Không thể thay đổi)</label>
                    <input type="email" value="<?= htmlspecialchars($user['email'] ?? '') ?>" disabled
                           class="w-full bg-cream/70 border border-beige/60 rounded-2xl px-4 py-3 text-xs sm:text-sm text-muted cursor-not-allowed">
                </div>

                <div>
                    <label class="block text-[11px] font-bold text-muted uppercase tracking-wider mb-1.5">Số điện thoại *</label>
                    <input type="tel" name="phone" value="<?= htmlspecialchars($user['phone'] ?? '') ?>"
                           class="w-full bg-cream/40 border border-beige rounded-2xl px-4 py-3 text-xs sm:text-sm focus:bg-white focus:outline-none focus:border-wood">
                </div>

                <!-- Business Profile info if business user -->
                <?php if (($user['account_type'] ?? '') === 'business'): ?>
                <div class="pt-4 border-t border-beige space-y-4">
                    <p class="text-xs font-bold text-charcoal flex items-center gap-2">
                        <span>🏢</span> Thông tin xuất hóa đơn VAT doanh nghiệp
                    </p>
                    
                    <div>
                        <label class="block text-[10px] font-bold text-muted uppercase mb-1">Tên công ty *</label>
                        <input type="text" name="company_name" value="<?= htmlspecialchars($businessProfile['company_name'] ?? '') ?>"
                               class="w-full bg-cream/40 border border-beige rounded-xl px-3.5 py-2.5 text-xs focus:bg-white focus:outline-none focus:border-wood">
                    </div>

                    <div>
                        <label class="block text-[10px] font-bold text-muted uppercase mb-1">Mã số thuế *</label>
                        <input type="text" name="tax_code" value="<?= htmlspecialchars($businessProfile['tax_code'] ?? '') ?>"
                               class="w-full bg-cream/40 border border-beige rounded-xl px-3.5 py-2.5 text-xs focus:bg-white focus:outline-none focus:border-wood">
                    </div>

                    <div>
                        <label class="block text-[10px] font-bold text-muted uppercase mb-1">Người đại diện</label>
                        <input type="text" name="representative_name" value="<?= htmlspecialchars($businessProfile['representative_name'] ?? '') ?>"
                               class="w-full bg-cream/40 border border-beige rounded-xl px-3.5 py-2.5 text-xs focus:bg-white focus:outline-none focus:border-wood">
                    </div>

                    <div>
                        <label class="block text-[10px] font-bold text-muted uppercase mb-1">Địa chỉ đăng ký kinh doanh</label>
                        <textarea name="invoice_address" rows="2"
                                  class="w-full bg-cream/40 border border-beige rounded-xl px-3.5 py-2.5 text-xs focus:bg-white focus:outline-none focus:border-wood resize-none"><?= htmlspecialchars($businessProfile['invoice_address'] ?? '') ?></textarea>
                    </div>

                    <div>
                        <label class="block text-[10px] font-bold text-muted uppercase mb-1">Email nhận hóa đơn điện tử</label>
                        <input type="email" name="invoice_email" value="<?= htmlspecialchars($businessProfile['invoice_email'] ?? '') ?>"
                               class="w-full bg-cream/40 border border-beige rounded-xl px-3.5 py-2.5 text-xs focus:bg-white focus:outline-none focus:border-wood">
                    </div>
                </div>
                <?php endif; ?>

                <button type="submit" class="w-full bg-charcoal text-white py-3.5 rounded-2xl text-xs font-bold uppercase tracking-wider hover:bg-wooddk transition shadow-warm">
                    Lưu thay đổi hồ sơ
                </button>
            </form>
        </div>

        <!-- Navigation Sidebar (Right) -->
        <div class="md:col-span-4 space-y-4">
            <div class="bg-white rounded-3xl border border-beige p-6 shadow-warm space-y-2">
                <a href="<?= $baseUrl ?>/user/orders" class="flex items-center gap-3 p-3 rounded-2xl hover:bg-cream transition text-xs font-semibold text-charcoal">
                    <span>📦</span> Đơn hàng của tôi
                </a>
                <?php if (($user['account_type'] ?? '') === 'business'): ?>
                <a href="<?= $baseUrl ?>/user/quotes" class="flex items-center gap-3 p-3 rounded-2xl hover:bg-cream transition text-xs font-semibold text-charcoal">
                    <span>💼</span> Báo giá B2B
                </a>
                <?php endif; ?>
                <a href="<?= $baseUrl ?>/user/logout" class="flex items-center gap-3 p-3 rounded-2xl hover:bg-red-50 transition text-xs font-semibold text-red-600">
                    <span>🚪</span> Đăng xuất
                </a>
            </div>
        </div>

    </div>

</div>
