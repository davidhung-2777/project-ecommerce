<?php $baseUrl = $_ENV['APP_URL'] ?? 'http://localhost/project-ecommerce/public'; ?>
<!-- Auth Modal -->
<div id="auth-modal" class="hidden fixed inset-0 z-50 flex items-center justify-center p-4"
     x-data="{ tab: 'login', accountType: 'individual', showPass: false }">
    <!-- Backdrop Blur -->
    <div class="absolute inset-0 bg-charcoal/60 backdrop-blur-sm transition-opacity" 
         onclick="document.getElementById('auth-modal').classList.add('hidden')"></div>

    <!-- Modal Box -->
    <div class="relative bg-warmwhite rounded-3xl w-full max-w-md shadow-warm-xl border border-beige overflow-hidden z-10">

        <!-- Close Button -->
        <button onclick="document.getElementById('auth-modal').classList.add('hidden')"
                class="absolute top-4 right-4 p-2 text-muted hover:text-charcoal transition rounded-full hover:bg-cream z-20">
            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.8" d="M6 18L18 6M6 6l12 12"/>
            </svg>
        </button>

        <!-- Header -->
        <div class="pt-8 pb-4 px-8 text-center bg-cream/40 border-b border-beige/60">
            <span class="text-2xl font-bold font-serif text-charcoal">Decor<span class="text-wood font-editorial italic font-normal">Nest</span></span>
            <p class="text-xs text-muted mt-1 font-light">Chốn nghỉ ngơi & thư giãn cho tâm trí</p>

            <!-- Tabs -->
            <div class="flex bg-warmwhite border border-beige rounded-2xl p-1 mt-5 shadow-sm">
                <button @click="tab = 'login'" 
                        :class="tab === 'login' ? 'bg-charcoal text-white shadow-sm' : 'text-muted hover:text-charcoal'"
                        class="flex-1 py-2 text-xs font-semibold rounded-xl transition-all duration-200">
                    Đăng nhập
                </button>
                <button @click="tab = 'register'" 
                        :class="tab === 'register' ? 'bg-charcoal text-white shadow-sm' : 'text-muted hover:text-charcoal'"
                        class="flex-1 py-2 text-xs font-semibold rounded-xl transition-all duration-200">
                    Tạo tài khoản
                </button>
            </div>
        </div>

        <!-- Login Tab -->
        <div x-show="tab === 'login'" class="p-6 sm:p-8">
            <form id="login-form" onsubmit="submitLogin(event)">
                <div class="space-y-4">
                    <div>
                        <label class="block text-[11px] font-semibold mb-1.5 text-muted uppercase tracking-wider">Email hoặc Số điện thoại</label>
                        <input type="text" name="credential" required placeholder="name@example.com"
                               class="w-full bg-cream/50 border border-beige rounded-xl px-4 py-3 text-xs sm:text-sm focus:bg-white focus:outline-none focus:border-wood focus:ring-1 focus:ring-wood transition">
                    </div>
                    <div class="relative">
                        <div class="flex items-center justify-between mb-1.5">
                            <label class="block text-[11px] font-semibold text-muted uppercase tracking-wider">Mật khẩu</label>
                        </div>
                        <input :type="showPass ? 'text' : 'password'" name="password" required placeholder="••••••••"
                               class="w-full bg-cream/50 border border-beige rounded-xl px-4 py-3 pr-10 text-xs sm:text-sm focus:bg-white focus:outline-none focus:border-wood focus:ring-1 focus:ring-wood transition">
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
                        <button type="button" onclick="document.getElementById('auth-modal').classList.add('hidden'); document.getElementById('forgot-modal').classList.remove('hidden')"
                                class="text-wood hover:underline font-medium">Quên mật khẩu?</button>
                    </div>
                </div>

                <div id="login-error" class="hidden mt-4 p-3 bg-red-50 border border-red-200 rounded-xl text-xs text-red-700"></div>

                <button type="submit" class="mt-6 w-full bg-charcoal text-white py-3.5 rounded-2xl text-xs font-bold uppercase tracking-wider hover:bg-wooddk transition shadow-warm">
                    Đăng nhập
                </button>
            </form>
        </div>

        <!-- Register Tab -->
        <div x-show="tab === 'register'" class="p-6 sm:p-8 overflow-y-auto max-h-[70vh]">
            <!-- Account Type Choice -->
            <div class="mb-5">
                <label class="block text-[11px] font-semibold mb-2 text-muted uppercase tracking-wider">Loại tài khoản</label>
                <div class="grid grid-cols-2 gap-2.5">
                    <label :class="accountType === 'individual' ? 'border-charcoal bg-cream font-bold text-charcoal' : 'border-beige text-muted'"
                           class="flex items-center justify-center gap-2 p-2.5 border rounded-xl cursor-pointer transition text-xs">
                        <input type="radio" name="account_type" value="individual" x-model="accountType" class="hidden">
                        <span>👤 Cá nhân</span>
                    </label>
                    <label :class="accountType === 'business' ? 'border-charcoal bg-cream font-bold text-charcoal' : 'border-beige text-muted'"
                           class="flex items-center justify-center gap-2 p-2.5 border rounded-xl cursor-pointer transition text-xs">
                        <input type="radio" name="account_type" value="business" x-model="accountType" class="hidden">
                        <span>🏢 Doanh nghiệp (B2B)</span>
                    </label>
                </div>
            </div>

            <form id="register-form" onsubmit="submitRegister(event)">
                <input type="hidden" name="account_type" :value="accountType">
                <div class="space-y-3.5">
                    <div>
                        <label class="block text-[11px] font-semibold mb-1 text-muted uppercase tracking-wider">Họ và tên *</label>
                        <input type="text" name="name" required placeholder="Nguyễn Văn A"
                               class="w-full bg-cream/50 border border-beige rounded-xl px-4 py-2.5 text-xs sm:text-sm focus:bg-white focus:outline-none focus:border-wood">
                    </div>
                    <div>
                        <label class="block text-[11px] font-semibold mb-1 text-muted uppercase tracking-wider">Email *</label>
                        <input type="email" name="email" required placeholder="email@example.com"
                               class="w-full bg-cream/50 border border-beige rounded-xl px-4 py-2.5 text-xs sm:text-sm focus:bg-white focus:outline-none focus:border-wood">
                    </div>
                    <div>
                        <label class="block text-[11px] font-semibold mb-1 text-muted uppercase tracking-wider">Số điện thoại *</label>
                        <input type="tel" name="phone" required placeholder="0901 234 567"
                               class="w-full bg-cream/50 border border-beige rounded-xl px-4 py-2.5 text-xs sm:text-sm focus:bg-white focus:outline-none focus:border-wood">
                    </div>
                    <div>
                        <label class="block text-[11px] font-semibold mb-1 text-muted uppercase tracking-wider">Mật khẩu *</label>
                        <input type="password" name="password" required placeholder="Tối thiểu 8 ký tự"
                               class="w-full bg-cream/50 border border-beige rounded-xl px-4 py-2.5 text-xs sm:text-sm focus:bg-white focus:outline-none focus:border-wood">
                    </div>

                    <!-- Business Additional Fields -->
                    <div x-show="accountType === 'business'" class="space-y-3 p-4 bg-cream/80 border border-sand rounded-2xl">
                        <p class="text-xs font-bold text-charcoal flex items-center gap-1.5">
                            <span>🏢</span> Thông tin đối tác B2B & Xuất hóa đơn
                        </p>
                        <div>
                            <label class="block text-[10px] font-semibold text-muted uppercase">Tên công ty / Khách sạn / Homestay *</label>
                            <input type="text" name="company_name" placeholder="Công ty TNHH..."
                                   class="w-full bg-white border border-beige rounded-lg px-3 py-2 text-xs focus:outline-none focus:border-wood mt-0.5">
                        </div>
                        <div>
                            <label class="block text-[10px] font-semibold text-muted uppercase">Mã số thuế *</label>
                            <input type="text" name="tax_code" placeholder="0123456789"
                                   class="w-full bg-white border border-beige rounded-lg px-3 py-2 text-xs focus:outline-none focus:border-wood mt-0.5">
                        </div>
                    </div>
                </div>

                <div id="register-error" class="hidden mt-4 p-3 bg-red-50 border border-red-200 rounded-xl text-xs text-red-700"></div>

                <button type="submit" class="mt-5 w-full bg-charcoal text-white py-3.5 rounded-2xl text-xs font-bold uppercase tracking-wider hover:bg-wooddk transition shadow-warm">
                    Đăng ký tài khoản
                </button>
            </form>
        </div>
    </div>
</div>

<!-- Forgot Password Modal -->
<div id="forgot-modal" class="hidden fixed inset-0 z-50 flex items-center justify-center p-4">
    <div class="absolute inset-0 bg-charcoal/60 backdrop-blur-sm" onclick="document.getElementById('forgot-modal').classList.add('hidden')"></div>
    <div class="relative bg-warmwhite rounded-3xl w-full max-w-sm shadow-warm-xl p-6 sm:p-8 border border-beige z-10">
        <h3 class="text-lg font-serif font-bold text-charcoal mb-2">Khôi phục mật khẩu</h3>
        <p class="text-xs text-muted mb-5 leading-relaxed">Nhập email đăng ký của bạn. Chúng tôi sẽ gửi liên kết tạo lại mật khẩu mới.</p>
        <form action="<?= $baseUrl ?>/user/forgot-password" method="POST">
            <input type="email" name="email" required placeholder="email@example.com"
                   class="w-full bg-cream/50 border border-beige rounded-xl px-4 py-3 text-xs sm:text-sm focus:bg-white focus:outline-none focus:border-wood mb-4">
            <button type="submit" class="w-full bg-charcoal text-white py-3 rounded-2xl text-xs font-bold uppercase tracking-wider hover:bg-wooddk transition">
                Gửi yêu cầu
            </button>
        </form>
        <button onclick="document.getElementById('forgot-modal').classList.add('hidden'); document.getElementById('auth-modal').classList.remove('hidden')" 
                class="mt-4 w-full text-center text-xs text-muted hover:text-charcoal">
            ← Quay lại đăng nhập
        </button>
    </div>
</div>
