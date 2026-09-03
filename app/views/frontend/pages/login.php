<?php
$baseUrl   = $_ENV['APP_URL'] ?? 'http://localhost/project-ecommerce/public';
$pageTitle = 'Đăng Nhập - DecorNest';
$error     = $_SESSION['login_error'] ?? null;
unset($_SESSION['login_error']);
?>

<div class="min-h-[80vh] bg-cream/40 flex items-center justify-center px-4 py-16">
    <div class="w-full max-w-md">
        
        <!-- Header -->
        <div class="text-center mb-8">
            <a href="<?= $baseUrl ?>" class="inline-block">
                <span class="text-3xl font-bold font-serif text-charcoal tracking-tight">Decor<span class="text-wood font-editorial italic">Nest</span></span>
            </a>
            <p class="mt-2 text-xs sm:text-sm text-muted">Đăng nhập vào không gian an yên của bạn</p>
        </div>

        <div class="bg-white rounded-3xl shadow-warm-lg border border-beige p-8 sm:p-10">
            <?php if ($error): ?>
            <div class="mb-6 p-4 bg-red-50 border border-red-200 rounded-2xl text-xs text-red-700 leading-relaxed">
                <?= htmlspecialchars($error) ?>
            </div>
            <?php endif; ?>

            <form action="<?= $baseUrl ?>/user/login" method="POST" x-data="{ showPass: false }">
                <div class="space-y-4">
                    <div>
                        <label class="block text-[11px] font-bold text-muted uppercase tracking-wider mb-1.5">Email hoặc Số điện thoại</label>
                        <input type="text" name="credential" required placeholder="name@example.com"
                               class="w-full bg-cream/40 border border-beige rounded-2xl px-4 py-3 text-xs sm:text-sm focus:bg-white focus:outline-none focus:border-wood focus:ring-1 focus:ring-wood transition">
                    </div>
                    <div class="relative">
                        <div class="flex items-center justify-between mb-1.5">
                            <label class="block text-[11px] font-bold text-muted uppercase tracking-wider">Mật khẩu</label>
                        </div>
                        <input :type="showPass ? 'text' : 'password'" name="password" required placeholder="••••••••"
                               class="w-full bg-cream/40 border border-beige rounded-2xl px-4 py-3 pr-10 text-xs sm:text-sm focus:bg-white focus:outline-none focus:border-wood focus:ring-1 focus:ring-wood transition">
                        <button type="button" @click="showPass = !showPass" class="absolute right-3 top-[34px] text-muted hover:text-charcoal p-1">
                            <svg x-show="!showPass" class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.8" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/>
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.8" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"/>
                            </svg>
                            <svg x-show="showPass" class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.8" d="M13.875 18.825A10.05 10.05 0 0112 19c-4.478 0-8.268-2.943-9.543-7a9.97 9.97 0 011.563-3.029m5.858.908a3 3 0 114.243 4.243M9.878 9.878l4.242 4.242M9.88 9.88l-3.29-3.29m7.532 7.532l3.29 3.29M3 3l3.59 3.59m0 0A9.953 9.953 0 0112 5c4.478 0 8.268 2.943 9.543 7a10.025 10.025 0 01-4.132 5.411m0 0L21 21"/>
                            </svg>
                        </button>
                    </div>
                    <div class="flex items-center justify-between text-xs pt-1">
                        <label class="flex items-center gap-2 cursor-pointer text-muted">
                            <input type="checkbox" name="remember" class="rounded border-beige text-charcoal accent-wood">
                            <span>Ghi nhớ đăng nhập</span>
                        </label>
                        <a href="<?= $baseUrl ?>/user/forgot-password" class="text-wood hover:underline font-medium">Quên mật khẩu?</a>
                    </div>
                </div>

                <button type="submit" class="mt-6 w-full bg-charcoal text-white py-3.5 rounded-2xl text-xs font-bold uppercase tracking-wider hover:bg-wooddk transition shadow-warm">
                    Đăng nhập vào tài khoản
                </button>
            </form>

            <div class="mt-6 pt-6 border-t border-beige text-center text-xs text-muted">
                Bạn chưa có tài khoản?
                <a href="<?= $baseUrl ?>/user/register" class="text-wood font-bold hover:underline ml-1">Đăng ký ngay</a>
            </div>
        </div>

    </div>
</div>
