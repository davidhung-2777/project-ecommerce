<?php
$baseUrl  = $_ENV['APP_URL'] ?? 'http://localhost/project-ecommerce/public';
$pageTitle = 'Đăng Ký Tài Khoản - DecorNest';
$errors    = $_SESSION['register_errors'] ?? [];
$old       = $_SESSION['register_input'] ?? [];
unset($_SESSION['register_errors'], $_SESSION['register_input']);
?>

<div class="min-h-[80vh] bg-cream/40 py-16 px-4">
    <div class="max-w-lg mx-auto">
        
        <!-- Header -->
        <div class="text-center mb-8">
            <a href="<?= $baseUrl ?>" class="inline-block">
                <span class="text-3xl font-bold font-serif text-charcoal tracking-tight">Decor<span class="text-wood font-editorial italic">Nest</span></span>
            </a>
            <p class="mt-2 text-xs sm:text-sm text-muted">Tạo tài khoản để bắt đầu hành trình xây đắp chốn về</p>
        </div>

        <div class="bg-white rounded-3xl shadow-warm-lg border border-beige p-8 sm:p-10" x-data="{ accountType: '<?= $old['account_type'] ?? 'individual' ?>' }">
            <?php if ($errors): ?>
            <div class="mb-6 p-4 bg-red-50 border border-red-200 rounded-2xl text-xs text-red-700">
                <ul class="list-disc list-inside space-y-1">
                    <?php foreach ($errors as $e): ?><li><?= htmlspecialchars($e) ?></li><?php endforeach; ?>
                </ul>
            </div>
            <?php endif; ?>

            <!-- Account type selector -->
            <div class="grid grid-cols-2 gap-3 mb-6">
                <label :class="accountType === 'individual' ? 'border-charcoal bg-cream font-bold text-charcoal' : 'border-beige text-muted'"
                       class="flex items-center justify-center gap-2 p-3 border rounded-2xl cursor-pointer transition text-xs">
                    <input type="radio" x-model="accountType" value="individual" class="hidden">
                    <span>👤 Cá nhân</span>
                </label>
                <label :class="accountType === 'business' ? 'border-charcoal bg-cream font-bold text-charcoal' : 'border-beige text-muted'"
                       class="flex items-center justify-center gap-2 p-3 border rounded-2xl cursor-pointer transition text-xs">
                    <input type="radio" x-model="accountType" value="business" class="hidden">
                    <span>🏢 Doanh nghiệp / B2B</span>
                </label>
            </div>

            <form action="<?= $baseUrl ?>/user/register" method="POST">
                <input type="hidden" name="account_type" :value="accountType">
                <div class="space-y-4">
                    <div>
                        <label class="block text-[11px] font-bold text-muted uppercase tracking-wider mb-1.5">Họ và tên *</label>
                        <input type="text" name="name" required value="<?= htmlspecialchars($old['name'] ?? '') ?>" placeholder="Nguyễn Văn A"
                               class="w-full bg-cream/40 border border-beige rounded-2xl px-4 py-3 text-xs sm:text-sm focus:bg-white focus:outline-none focus:border-wood">
                    </div>
                    <div>
                        <label class="block text-[11px] font-bold text-muted uppercase tracking-wider mb-1.5">Email *</label>
                        <input type="email" name="email" required value="<?= htmlspecialchars($old['email'] ?? '') ?>" placeholder="name@example.com"
                               class="w-full bg-cream/40 border border-beige rounded-2xl px-4 py-3 text-xs sm:text-sm focus:bg-white focus:outline-none focus:border-wood">
                    </div>
                    <div>
                        <label class="block text-[11px] font-bold text-muted uppercase tracking-wider mb-1.5">Số điện thoại *</label>
                        <input type="tel" name="phone" required value="<?= htmlspecialchars($old['phone'] ?? '') ?>" placeholder="0901 234 567"
                               class="w-full bg-cream/40 border border-beige rounded-2xl px-4 py-3 text-xs sm:text-sm focus:bg-white focus:outline-none focus:border-wood">
                    </div>
                    <div>
                        <label class="block text-[11px] font-bold text-muted uppercase tracking-wider mb-1.5">Mật khẩu *</label>
                        <input type="password" name="password" required placeholder="Tối thiểu 8 ký tự"
                               class="w-full bg-cream/40 border border-beige rounded-2xl px-4 py-3 text-xs sm:text-sm focus:bg-white focus:outline-none focus:border-wood">
                    </div>

                    <!-- Business fields -->
                    <div x-show="accountType === 'business'" class="space-y-3.5 p-5 bg-cream/80 border border-sand rounded-3xl">
                        <p class="text-xs font-bold text-charcoal flex items-center gap-1.5">
                            <span>🏢</span> Thông tin đối tác doanh nghiệp & Dự án
                        </p>
                        <div>
                            <label class="block text-[10px] uppercase font-bold text-muted mb-1">Tên công ty / Khách sạn *</label>
                            <input type="text" name="company_name" placeholder="Công ty TNHH..." value="<?= htmlspecialchars($old['company_name'] ?? '') ?>"
                                   class="w-full bg-white border border-beige rounded-xl px-3.5 py-2.5 text-xs focus:outline-none focus:border-wood">
                        </div>
                        <div>
                            <label class="block text-[10px] uppercase font-bold text-muted mb-1">Mã số thuế *</label>
                            <input type="text" name="tax_code" placeholder="0123456789" value="<?= htmlspecialchars($old['tax_code'] ?? '') ?>"
                                   class="w-full bg-white border border-beige rounded-xl px-3.5 py-2.5 text-xs focus:outline-none focus:border-wood">
                        </div>
                        <div>
                            <label class="block text-[10px] uppercase font-bold text-muted mb-1">Địa chỉ đăng ký kinh doanh *</label>
                            <textarea name="invoice_address" rows="2" placeholder="Địa chỉ xuất hóa đơn..."
                                      class="w-full bg-white border border-beige rounded-xl px-3.5 py-2.5 text-xs focus:outline-none focus:border-wood resize-none"><?= htmlspecialchars($old['invoice_address'] ?? '') ?></textarea>
                        </div>
                    </div>
                </div>

                <button type="submit" class="mt-6 w-full bg-charcoal text-white py-3.5 rounded-2xl text-xs font-bold uppercase tracking-wider hover:bg-wooddk transition shadow-warm">
                    Đăng ký tài khoản
                </button>
            </form>

            <div class="mt-6 pt-6 border-t border-beige text-center text-xs text-muted">
                Đã có tài khoản? 
                <a href="<?= $baseUrl ?>/user/login" class="text-wood font-bold hover:underline ml-1">Đăng nhập ngay</a>
            </div>
        </div>
    </div>
</div>
