<?php
$baseUrl = $_ENV['APP_URL'] ?? 'http://localhost/project-ecommerce/public';
?>
<header class="glass-header sticky top-0 z-40 border-b border-beige/70 transition-all duration-300">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="flex items-center justify-between h-20 gap-4">

            <!-- Mobile Menu Toggle -->
            <button @click="mobileMenuOpen = !mobileMenuOpen" class="lg:hidden p-2 text-charcoal hover:text-wood transition" aria-label="Mở menu">
                <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M4 6h16M4 12h16M4 18h16"/>
                </svg>
            </button>

            <!-- Brand Logo -->
            <a href="<?= $baseUrl ?>" class="flex-shrink-0 flex flex-col group">
                <div class="flex items-center gap-1.5">
                    <span class="text-2xl sm:text-[26px] font-bold tracking-tight text-charcoal font-serif">
                        Decor<span class="text-wood font-editorial italic font-normal">Nest</span>
                    </span>
                    <span class="w-1.5 h-1.5 rounded-full bg-sage mb-1 opacity-80 group-hover:scale-125 transition-transform"></span>
                </div>
                <span class="text-[9px] uppercase tracking-[0.22em] text-muted -mt-1 font-light">Bedroom & Living Sanctuary</span>
            </a>

            <!-- Search Bar (Desktop) -->
            <div class="hidden lg:flex flex-1 max-w-lg mx-8">
                <form action="<?= $baseUrl ?>/search" method="GET" class="relative w-full">
                    <input
                        type="search"
                        name="q"
                        placeholder="Tìm kiếm đèn ngủ, gối lanh, tranh canvas, đồ gốm..."
                        value="<?= htmlspecialchars($_GET['q'] ?? '') ?>"
                        class="w-full pl-5 pr-12 py-2.5 bg-cream/70 hover:bg-cream border border-beige rounded-full text-xs sm:text-sm text-charcoal placeholder:text-muted/60 focus:bg-white focus:outline-none focus:border-wood focus:ring-1 focus:ring-wood transition-all duration-300 shadow-sm"
                    >
                    <button type="submit" class="absolute right-4 top-1/2 -translate-y-1/2 text-muted hover:text-wood transition-colors p-1" aria-label="Tìm kiếm">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.8" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/>
                        </svg>
                    </button>
                </form>
            </div>

            <!-- Right Actions -->
            <div class="flex items-center gap-2 sm:gap-4">

                <!-- Search toggle (Mobile) -->
                <button @click="searchOpen = !searchOpen" class="lg:hidden p-2 text-charcoal hover:text-wood transition rounded-full hover:bg-cream" aria-label="Tìm kiếm">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.6" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/>
                    </svg>
                </button>

                <!-- B2B Quote Shortcut -->
                <a href="<?= $baseUrl ?>/quote" 
                   class="hidden xl:inline-flex items-center gap-1.5 px-3.5 py-1.5 rounded-full border border-sand text-xs font-medium text-charcoal hover:border-wood hover:text-wood transition">
                    <span class="text-amber-warm text-[11px]">✧</span>
                    Báo giá dự án
                </a>

                <!-- Account Area -->
                <?php if (!empty($_SESSION['user_id'])): ?>
                <div class="relative" x-data="{ open: false }">
                    <button @click="open = !open" 
                            class="flex items-center gap-2 p-2 sm:px-3 sm:py-1.5 text-charcoal hover:text-wood transition rounded-full hover:bg-cream text-xs font-medium border border-transparent hover:border-beige">
                        <div class="w-7 h-7 rounded-full bg-cream border border-sand flex items-center justify-center text-xs text-wood font-semibold">
                            <?= mb_substr($_SESSION['user_name'] ?? 'U', 0, 1, 'UTF-8') ?>
                        </div>
                        <span class="hidden md:inline font-medium text-sm truncate max-w-[120px]"><?= htmlspecialchars($_SESSION['user_name'] ?? 'Tài khoản') ?></span>
                        <svg class="w-3.5 h-3.5 text-muted hidden md:block" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"/>
                        </svg>
                    </button>
                    <div x-show="open" @click.outside="open = false" x-cloak
                         class="absolute right-0 mt-2 w-52 bg-white border border-beige rounded-2xl shadow-warm-lg py-2 z-50 transition-all duration-200">
                        <div class="px-4 py-2 border-b border-beige/60">
                            <p class="text-xs text-muted">Đăng nhập với tư cách</p>
                            <p class="text-xs font-semibold text-charcoal truncate"><?= htmlspecialchars($_SESSION['user_email'] ?? '') ?></p>
                        </div>
                        <a href="<?= $baseUrl ?>/dashboard" class="flex items-center gap-2 px-4 py-2.5 text-xs text-charcoal hover:bg-cream hover:text-wood transition">
                            <span>📊</span> Bảng điều khiển
                        </a>
                        <a href="<?= $baseUrl ?>/user/orders" class="flex items-center gap-2 px-4 py-2.5 text-xs text-charcoal hover:bg-cream hover:text-wood transition">
                            <span>📦</span> Đơn hàng của tôi
                        </a>
                        <?php if (($_SESSION['user_type'] ?? '') === 'business'): ?>
                        <a href="<?= $baseUrl ?>/user/quotes" class="flex items-center gap-2 px-4 py-2.5 text-xs text-charcoal hover:bg-cream hover:text-wood transition">
                            <span>💼</span> Báo giá doanh nghiệp
                        </a>
                        <?php endif; ?>
                        <?php if (($_SESSION['user_role'] ?? '') === 'admin'): ?>
                        <div class="border-t border-beige/60 my-1"></div>
                        <a href="<?= $baseUrl ?>/admin" class="flex items-center gap-2 px-4 py-2.5 text-xs font-semibold text-wood hover:bg-cream transition">
                            <span>⚙️</span> Trang Quản trị
                        </a>
                        <?php endif; ?>
                        <div class="border-t border-beige/60 my-1"></div>
                        <a href="<?= $baseUrl ?>/user/logout" class="flex items-center gap-2 px-4 py-2.5 text-xs text-red-600 hover:bg-red-50 transition">
                            <span>🚪</span> Đăng xuất
                        </a>
                    </div>
                </div>
                <?php else: ?>
                <button onclick="document.getElementById('auth-modal').classList.remove('hidden')"
                        class="p-2 sm:px-3.5 sm:py-2 text-charcoal hover:text-wood transition rounded-full hover:bg-cream flex items-center gap-2 text-xs font-medium" 
                        title="Đăng nhập / Đăng ký">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.6" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"/>
                    </svg>
                    <span class="hidden sm:inline">Tài khoản</span>
                </button>
                <?php endif; ?>

                <!-- Cart Button with Slide-out trigger -->
                <button @click="cartOpen = true; loadMiniCart()" 
                        class="relative p-2.5 sm:px-3.5 sm:py-2 bg-cream/80 hover:bg-cream text-charcoal rounded-full border border-beige transition-all duration-300 hover:border-wood flex items-center gap-2"
                        aria-label="Giỏ hàng">
                    <svg class="w-5 h-5 text-charcoal" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.6" d="M16 11V7a4 4 0 00-8 0v4M5 9h14l1 7H4l1-7z"/>
                    </svg>
                    <span class="hidden md:inline text-xs font-medium">Giỏ hàng</span>
                    <span id="cart-badge"
                          class="absolute -top-1 -right-1 bg-wood text-white text-[10px] font-bold rounded-full min-w-[19px] h-[19px] flex items-center justify-center px-1 leading-none shadow-sm transition-transform duration-200 <?= empty($_SESSION['cart_count']) ? 'hidden' : 'flex' ?>">
                        <span id="cart-count"><?= (int)($_SESSION['cart_count'] ?? 0) ?></span>
                    </span>
                </button>

            </div>
        </div>

        <!-- Mobile Search Dropdown -->
        <div x-show="searchOpen" x-cloak class="pb-4 lg:hidden transition-all duration-200">
            <form action="<?= $baseUrl ?>/search" method="GET" class="relative">
                <input type="search" name="q" placeholder="Tìm kiếm sản phẩm decor phòng ngủ..."
                       class="w-full pl-4 pr-10 py-2.5 border border-beige rounded-full text-xs bg-cream/80 focus:bg-white focus:outline-none focus:border-wood">
                <button type="submit" class="absolute right-3 top-1/2 -translate-y-1/2 text-muted">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/>
                    </svg>
                </button>
            </form>
        </div>
    </div>
</header>

<!-- Mobile Menu Drawer -->
<div x-show="mobileMenuOpen" x-cloak
     class="fixed inset-0 z-50 lg:hidden" 
     x-transition:enter="transition ease-out duration-300"
     x-transition:enter-start="opacity-0"
     x-transition:enter-end="opacity-100"
     x-transition:leave="transition ease-in duration-200"
     x-transition:leave-start="opacity-100"
     x-transition:leave-end="opacity-0">
    <div class="absolute inset-0 bg-charcoal/50 backdrop-blur-sm" @click="mobileMenuOpen = false"></div>
    <div class="absolute left-0 top-0 bottom-0 w-80 bg-warmwhite shadow-2xl overflow-y-auto flex flex-col"
         x-transition:enter="transition ease-out duration-300"
         x-transition:enter-start="-translate-x-full"
         x-transition:enter-end="translate-x-0"
         x-transition:leave="transition ease-in duration-200"
         x-transition:leave-start="translate-x-0"
         x-transition:leave-end="-translate-x-full">
        
        <div class="flex items-center justify-between p-5 border-b border-beige">
            <div>
                <span class="text-xl font-bold font-serif text-charcoal">Decor<span class="text-wood font-editorial italic">Nest</span></span>
                <p class="text-[10px] text-muted tracking-wider uppercase">Chốn về an yên</p>
            </div>
            <button @click="mobileMenuOpen = false" class="p-2 text-muted hover:text-charcoal transition rounded-full hover:bg-cream">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.8" d="M6 18L18 6M6 6l12 12"/>
                </svg>
            </button>
        </div>

        <nav class="p-5 flex-1 space-y-1">
            <a href="<?= $baseUrl ?>" class="flex items-center gap-3 px-3 py-2.5 rounded-xl text-sm font-medium text-charcoal hover:bg-cream hover:text-wood transition">
                <span>🏠</span> Trang chủ
            </a>
            <a href="<?= $baseUrl ?>/products" class="flex items-center gap-3 px-3 py-2.5 rounded-xl text-sm font-medium text-charcoal hover:bg-cream hover:text-wood transition">
                <span>🛏️</span> Tất cả sản phẩm
            </a>
            <a href="<?= $baseUrl ?>/products?is_new=1" class="flex items-center gap-3 px-3 py-2.5 rounded-xl text-sm font-medium text-charcoal hover:bg-cream hover:text-wood transition">
                <span>✨</span> Hàng mới về
            </a>
            <a href="<?= $baseUrl ?>/products?on_sale=1" class="flex items-center gap-3 px-3 py-2.5 rounded-xl text-sm font-medium text-charcoal hover:bg-cream hover:text-wood transition">
                <span>🎁</span> Ưu đãi đặc biệt
            </a>
            <a href="<?= $baseUrl ?>/quote" class="flex items-center gap-3 px-3 py-2.5 rounded-xl text-sm font-medium text-charcoal hover:bg-cream hover:text-wood transition">
                <span>💼</span> Báo giá B2B & Dự án
            </a>

            <div class="pt-4 pb-2 border-t border-beige/70 my-2">
                <p class="text-[11px] font-semibold uppercase tracking-widest text-muted px-3 mb-2">Danh mục phòng ngủ</p>
                <div class="space-y-1">
                    <a href="<?= $baseUrl ?>/category/den-trang-tri" class="flex items-center gap-2 px-3 py-2 text-xs text-charcoal hover:bg-cream rounded-lg">
                        <span>💡</span> Đèn Trang Trí & Đèn Ngủ
                    </a>
                    <a href="<?= $baseUrl ?>/category/goi-nem" class="flex items-center gap-2 px-3 py-2 text-xs text-charcoal hover:bg-cream rounded-lg">
                        <span>🛋️</span> Gối & Nệm Tự Nhiên
                    </a>
                    <a href="<?= $baseUrl ?>/category/tranh-khung" class="flex items-center gap-2 px-3 py-2 text-xs text-charcoal hover:bg-cream rounded-lg">
                        <span>🖼️</span> Tranh Canvas & Khung Gỗ
                    </a>
                    <a href="<?= $baseUrl ?>/category/do-gom-su" class="flex items-center gap-2 px-3 py-2 text-xs text-charcoal hover:bg-cream rounded-lg">
                        <span>🏺</span> Đồ Gốm Sứ & Chậu Cây
                    </a>
                    <a href="<?= $baseUrl ?>/category/dong-ho" class="flex items-center gap-2 px-3 py-2 text-xs text-charcoal hover:bg-cream rounded-lg">
                        <span>🕐</span> Đồng Hồ Tối Giản
                    </a>
                    <a href="<?= $baseUrl ?>/category/ke-gia" class="flex items-center gap-2 px-3 py-2 text-xs text-charcoal hover:bg-cream rounded-lg">
                        <span>📦</span> Kệ & Giá Treo Gỗ Sồi
                    </a>
                </div>
            </div>
        </nav>

        <div class="p-5 border-t border-beige bg-cream/50 text-xs space-y-2 text-muted">
            <p class="flex items-center gap-2">📞 Hotline: <a href="tel:19001234" class="font-semibold text-charcoal">1900 1234</a></p>
            <p class="flex items-center gap-2">🌿 Giờ mở cửa: 8:00 - 21:30 hàng ngày</p>
        </div>
    </div>
</div>
