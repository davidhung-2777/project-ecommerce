<?php 
$baseUrl = $_ENV['APP_URL'] ?? 'http://localhost/project-ecommerce/public'; 
?>
<!-- Mini Cart Slide-out Drawer -->
<div x-show="cartOpen" class="fixed inset-0 z-50 overflow-hidden" x-cloak>
    <!-- Backdrop Blur Overlay -->
    <div class="absolute inset-0 bg-charcoal/50 backdrop-blur-sm transition-opacity" 
         @click="cartOpen = false"
         x-show="cartOpen"
         x-transition:enter="ease-out duration-300"
         x-transition:enter-start="opacity-0"
         x-transition:enter-end="opacity-100"
         x-transition:leave="ease-in duration-200"
         x-transition:leave-start="opacity-100"
         x-transition:leave-end="opacity-0"></div>

    <!-- Drawer Panel -->
    <div class="fixed inset-y-0 right-0 max-w-full flex pl-10">
        <div class="w-screen max-w-md bg-warmwhite shadow-2xl flex flex-col border-l border-beige"
             x-show="cartOpen"
             x-transition:enter="transform transition ease-out duration-300"
             x-transition:enter-start="translate-x-full"
             x-transition:enter-end="translate-x-0"
             x-transition:leave="transform transition ease-in duration-200"
             x-transition:leave-start="translate-x-0"
             x-transition:leave-end="translate-x-full">

            <!-- Drawer Header -->
            <div class="flex items-center justify-between p-5 sm:p-6 border-b border-beige bg-white/70 backdrop-blur-md">
                <div class="flex items-center gap-2">
                    <span class="text-xl">🛏️</span>
                    <div>
                        <h2 class="font-serif font-bold text-base text-charcoal">Không gian đã chọn</h2>
                        <p class="text-[11px] text-muted"><span id="mini-cart-count">0</span> món đồ decor</p>
                    </div>
                </div>
                <button @click="cartOpen = false" class="p-2 text-muted hover:text-charcoal transition rounded-full hover:bg-cream" aria-label="Đóng giỏ hàng">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.8" d="M6 18L18 6M6 6l12 12"/>
                    </svg>
                </button>
            </div>

            <!-- Free shipping bar -->
            <div class="bg-sage-light/60 px-5 py-2.5 border-b border-sage/10 text-xs text-sage flex items-center gap-2">
                <span>🚚</span>
                <span>Miễn phí vận chuyển cho đơn hàng từ <strong>5.000.000đ</strong></span>
            </div>

            <!-- Cart Items Dynamic Content Area -->
            <div id="mini-cart-content" class="flex-1 overflow-y-auto p-5 sm:p-6 space-y-4">
                <!-- Content loaded via Ajax -->
                <div class="flex flex-col items-center justify-center h-full gap-4 text-muted py-12 text-center">
                    <div class="w-16 h-16 rounded-full bg-cream flex items-center justify-center text-2xl">
                        🕯️
                    </div>
                    <p class="text-sm font-medium text-charcoal">Không gian ngủ đang trống</p>
                    <p class="text-xs text-muted max-w-xs">Hãy chọn những món đồ dịu êm để hoàn thiện chốn về của bạn.</p>
                </div>
            </div>

            <!-- Drawer Footer -->
            <div class="border-t border-beige p-5 sm:p-6 bg-white space-y-3 shadow-warm-lg">
                <div class="flex items-center justify-between text-sm">
                    <span class="text-muted font-medium">Tạm tính:</span>
                    <span id="mini-cart-subtotal" class="font-bold text-lg text-charcoal tracking-tight">0đ</span>
                </div>
                <p class="text-[11px] text-muted text-center">Đã bao gồm VAT & các ưu đãi hiện có</p>

                <div class="space-y-2 pt-2">
                    <a href="<?= $baseUrl ?>/checkout"
                       class="block w-full text-center bg-charcoal text-white py-3.5 rounded-2xl text-xs font-bold uppercase tracking-wider hover:bg-wooddk transition shadow-warm flex items-center justify-center gap-2">
                        <span>Tiến hành thanh toán</span>
                        <span>→</span>
                    </a>
                    <a href="<?= $baseUrl ?>/cart"
                       class="block w-full text-center border border-beige text-charcoal py-2.5 rounded-2xl text-xs font-semibold hover:bg-cream transition">
                        Xem chi tiết giỏ hàng
                    </a>
                </div>
            </div>

        </div>
    </div>
</div>
