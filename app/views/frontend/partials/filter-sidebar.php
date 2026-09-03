<?php $baseUrl = $_ENV['APP_URL'] ?? 'http://localhost/project-ecommerce/public'; ?>
<div class="space-y-4">

    <!-- Filter Header -->
    <div class="flex items-center justify-between pb-3 border-b border-beige">
        <span class="font-serif font-bold text-sm text-charcoal">Bộ lọc không gian</span>
        <a href="?" class="text-xs text-wood hover:underline">Đặt lại tất cả</a>
    </div>

    <!-- Quick Checkboxes -->
    <div class="bg-cream/60 border border-beige rounded-2xl p-4 space-y-2.5">
        <p class="text-xs font-semibold text-charcoal mb-2">Ưu tiên hiển thị</p>
        <label class="flex items-center gap-2.5 cursor-pointer text-xs text-charcoal select-none">
            <input type="checkbox" id="filter-new" onchange="applyFilters()" <?= isset($_GET['is_new']) ? 'checked' : '' ?>
                   class="rounded border-sand text-wood focus:ring-wood accent-wood">
            <span>✨ Hàng mới về</span>
        </label>
        <label class="flex items-center gap-2.5 cursor-pointer text-xs text-charcoal select-none">
            <input type="checkbox" id="filter-sale" onchange="applyFilters()" <?= isset($_GET['on_sale']) ? 'checked' : '' ?>
                   class="rounded border-sand text-wood focus:ring-wood accent-wood">
            <span>🏷️ Đang có ưu đãi giá</span>
        </label>
    </div>

    <!-- Price Filter Accordion -->
    <div x-data="{ open: true }" class="border border-beige rounded-2xl overflow-hidden bg-white">
        <button @click="open = !open" class="flex items-center justify-between w-full px-4 py-3.5 text-xs font-bold text-charcoal bg-cream/40 hover:bg-cream/70 transition">
            <span>Khoảng giá (VNĐ)</span>
            <svg :class="open ? 'rotate-180' : ''" class="w-4 h-4 text-muted transition-transform duration-200" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.8" d="M19 9l-7 7-7-7"/>
            </svg>
        </button>
        <div x-show="open" class="p-4 space-y-3">
            <div class="grid grid-cols-2 gap-2">
                <div>
                    <label class="block text-[10px] uppercase tracking-wider text-muted mb-1">Từ</label>
                    <input type="number" name="min_price" id="filter-min-price"
                           value="<?= htmlspecialchars($_GET['min_price'] ?? '') ?>"
                           placeholder="0đ"
                           class="w-full bg-cream/40 border border-beige rounded-xl px-3 py-2 text-xs focus:bg-white focus:outline-none focus:border-wood">
                </div>
                <div>
                    <label class="block text-[10px] uppercase tracking-wider text-muted mb-1">Đến</label>
                    <input type="number" name="max_price" id="filter-max-price"
                           value="<?= htmlspecialchars($_GET['max_price'] ?? '') ?>"
                           placeholder="10.000.000đ"
                           class="w-full bg-cream/40 border border-beige rounded-xl px-3 py-2 text-xs focus:bg-white focus:outline-none focus:border-wood">
                </div>
            </div>

            <!-- Quick range shortcuts -->
            <div class="space-y-1 pt-1 border-t border-beige/60">
                <?php $ranges = [['0', '500000', 'Dưới 500.000đ'], ['500000', '2000000', '500.000đ – 2.000.000đ'], ['2000000', '5000000', '2.000.000đ – 5.000.000đ'], ['5000000', '', 'Trên 5.000.000đ']]; ?>
                <?php foreach ($ranges as [$min, $max, $label]): ?>
                <button type="button" onclick="setPrice('<?= $min ?>', '<?= $max ?>')"
                        class="block w-full text-left text-xs text-muted hover:text-wood py-1.5 px-2.5 rounded-lg hover:bg-cream transition">
                    <?= $label ?>
                </button>
                <?php endforeach; ?>
            </div>
        </div>
    </div>

    <!-- Apply Button -->
    <button onclick="applyFilters(); document.getElementById('mobile-filter-drawer')?.classList.add('hidden')"
            class="w-full bg-charcoal text-white py-3 rounded-xl text-xs font-bold uppercase tracking-wider hover:bg-wooddk transition shadow-warm">
        Áp dụng bộ lọc
    </button>

</div>

<script>
function setPrice(min, max) {
    const minInput = document.getElementById('filter-min-price');
    const maxInput = document.getElementById('filter-max-price');
    if (minInput) minInput.value = min;
    if (maxInput) maxInput.value = max;
    applyFilters();
}

function applyFilters() {
    const url = new URL(window.location.href);
    const minPrice = document.getElementById('filter-min-price')?.value;
    const maxPrice = document.getElementById('filter-max-price')?.value;
    const isNew = document.getElementById('filter-new')?.checked;
    const onSale = document.getElementById('filter-sale')?.checked;

    if (minPrice) url.searchParams.set('min_price', minPrice); else url.searchParams.delete('min_price');
    if (maxPrice) url.searchParams.set('max_price', maxPrice); else url.searchParams.delete('max_price');
    if (isNew) url.searchParams.set('is_new', '1'); else url.searchParams.delete('is_new');
    if (onSale) url.searchParams.set('on_sale', '1'); else url.searchParams.delete('on_sale');
    url.searchParams.set('page', '1');

    window.location.href = url.toString();
}
</script>
