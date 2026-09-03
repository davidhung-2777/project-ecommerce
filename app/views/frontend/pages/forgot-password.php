<?php
$baseUrl   = $_ENV['APP_URL'] ?? 'http://localhost/project-ecommerce/public';
$pageTitle = 'Quên Mật Khẩu - DecorNest';
?>

<div class="min-h-[70vh] bg-cream/40 flex items-center justify-center px-4 py-16">
    <div class="w-full max-w-md">
        
        <div class="text-center mb-8">
            <a href="<?= $baseUrl ?>" class="inline-block">
                <span class="text-3xl font-bold font-serif text-charcoal tracking-tight">Decor<span class="text-wood font-editorial italic">Nest</span></span>
            </a>
            <p class="mt-2 text-xs sm:text-sm text-muted">Khôi phục mật khẩu tài khoản của bạn</p>
        </div>

        <div class="bg-white rounded-3xl shadow-warm-lg border border-beige p-8 sm:p-10">
            <p class="text-xs text-muted leading-relaxed mb-6">
                Nhập địa chỉ email đăng ký của bạn. Chúng tôi sẽ gửi hướng dẫn khôi phục mật khẩu an toàn.
            </p>

            <form action="<?= $baseUrl ?>/user/forgot-password" method="POST" class="space-y-4">
                <div>
                    <label class="block text-[11px] font-bold text-muted uppercase tracking-wider mb-1.5">Email của bạn</label>
                    <input type="email" name="email" required placeholder="name@example.com"
                           class="w-full bg-cream/40 border border-beige rounded-2xl px-4 py-3 text-xs sm:text-sm focus:bg-white focus:outline-none focus:border-wood">
                </div>

                <button type="submit" class="w-full bg-charcoal text-white py-3.5 rounded-2xl text-xs font-bold uppercase tracking-wider hover:bg-wooddk transition shadow-warm mt-2">
                    Gửi hướng dẫn khôi phục
                </button>
            </form>

            <div class="mt-6 pt-6 border-t border-beige text-center">
                <a href="<?= $baseUrl ?>/user/login" class="text-xs text-wood font-bold hover:underline">
                    ← Quay lại đăng nhập
                </a>
            </div>
        </div>

    </div>
</div>
