<?php
// Promo bar - shown until expiry date
$promoExpiry = strtotime('2026-12-31');
if (time() < $promoExpiry):
?>
<div class="bg-charcoal text-cream text-center text-xs py-2 px-4 tracking-wide relative overflow-hidden transition-all duration-300" id="promo-bar">
    <div class="max-w-7xl mx-auto flex items-center justify-center gap-2">
        <span class="inline-flex items-center gap-1.5 font-light text-cream/90 text-[11px] sm:text-xs">
            <span class="text-amber-warm">✦</span>
            <span>Không gian chữa lành tâm trí</span>
            <span class="text-white/30 hidden md:inline">|</span>
            <span class="hidden md:inline text-cream/80">Miễn phí giao hàng & lắp đặt phòng ngủ cho đơn từ 5.000.000đ</span>
            <span class="text-white/30">|</span>
            <span class="text-amber-warm font-medium">Bảo hành 2 năm</span>
        </span>
        <button onclick="document.getElementById('promo-bar').remove()" class="absolute right-4 top-1/2 -translate-y-1/2 text-cream/60 hover:text-white text-base leading-none p-1 transition" title="Đóng">&times;</button>
    </div>
</div>
<?php endif; ?>
