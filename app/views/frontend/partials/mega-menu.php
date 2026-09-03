<?php 
$baseUrl = $_ENV['APP_URL'] ?? 'http://localhost/project-ecommerce/public'; 
?>
<!-- Mega Menu Bar (Desktop) -->
<nav class="hidden lg:block bg-cream/70 border-b border-beige/80 transition-colors">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="flex items-center justify-between">
            <ul class="flex items-center gap-1 -ml-3">

                <!-- Home link -->
                <li>
                    <a href="<?= $baseUrl ?>" class="px-3.5 py-3 text-xs font-semibold text-charcoal hover:text-wood transition inline-flex items-center gap-1.5 uppercase tracking-wider">
                        Trang chủ
                    </a>
                </li>

                <!-- All Products -->
                <li>
                    <a href="<?= $baseUrl ?>/products" class="px-3.5 py-3 text-xs font-semibold text-charcoal hover:text-wood transition inline-flex items-center gap-1.5 uppercase tracking-wider">
                        Tất cả sản phẩm
                    </a>
                </li>

                <!-- Categories -->
                <?php if (!empty($menuTree)): ?>
                <?php foreach ($menuTree as $cat): ?>
                <li class="group relative">
                    <a href="<?= $baseUrl ?>/category/<?= $cat['slug'] ?>"
                       class="flex items-center gap-1 px-3 py-3 text-xs font-medium text-charcoal/80 hover:text-wood transition whitespace-nowrap group-hover:text-wood">
                        <?= htmlspecialchars($cat['name']) ?>
                        <?php if (!empty($cat['children'])): ?>
                        <svg class="w-3 h-3 opacity-50 group-hover:rotate-180 transition-transform duration-200" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"/>
                        </svg>
                        <?php endif; ?>
                    </a>

                    <?php if (!empty($cat['children'])): ?>
                    <div class="absolute left-0 top-full pt-1 opacity-0 invisible group-hover:opacity-100 group-hover:visible transition-all duration-200 z-50 min-w-[240px]">
                        <div class="bg-white border border-beige rounded-2xl shadow-warm-lg py-3 overflow-hidden">
                            <div class="px-4 py-1.5 text-[10px] font-semibold uppercase tracking-wider text-muted">
                                <?= htmlspecialchars($cat['name']) ?>
                            </div>
                            <?php foreach ($cat['children'] as $child): ?>
                            <a href="<?= $baseUrl ?>/category/<?= $child['slug'] ?>"
                               class="flex items-center justify-between px-4 py-2 text-xs text-charcoal hover:bg-cream hover:text-wood transition">
                                <span><?= htmlspecialchars($child['name']) ?></span>
                                <span class="text-muted/40 text-[10px]">→</span>
                            </a>
                            <?php endforeach; ?>
                            <div class="border-t border-beige/60 mt-2 pt-2 px-4">
                                <a href="<?= $baseUrl ?>/category/<?= $cat['slug'] ?>"
                                   class="text-[11px] font-semibold text-wood hover:underline flex items-center gap-1">
                                    Xem trọn bộ <?= htmlspecialchars($cat['name']) ?> →
                                </a>
                            </div>
                        </div>
                    </div>
                    <?php endif; ?>
                </li>
                <?php endforeach; ?>
                <?php else: ?>
                <!-- Default Hardcoded Categories for immediate aesthetic preview -->
                <li><a href="<?= $baseUrl ?>/category/den-trang-tri" class="px-3 py-3 text-xs font-medium text-charcoal/80 hover:text-wood transition">Đèn Trang Trí</a></li>
                <li><a href="<?= $baseUrl ?>/category/goi-nem" class="px-3 py-3 text-xs font-medium text-charcoal/80 hover:text-wood transition">Gối & Nệm</a></li>
                <li><a href="<?= $baseUrl ?>/category/tranh-khung" class="px-3 py-3 text-xs font-medium text-charcoal/80 hover:text-wood transition">Tranh & Khung</a></li>
                <li><a href="<?= $baseUrl ?>/category/do-gom-su" class="px-3 py-3 text-xs font-medium text-charcoal/80 hover:text-wood transition">Đồ Gốm Sứ</a></li>
                <li><a href="<?= $baseUrl ?>/category/dong-ho" class="px-3 py-3 text-xs font-medium text-charcoal/80 hover:text-wood transition">Đồng Hồ</a></li>
                <li><a href="<?= $baseUrl ?>/category/ke-gia" class="px-3 py-3 text-xs font-medium text-charcoal/80 hover:text-wood transition">Kệ & Giá Gỗ</a></li>
                <?php endif; ?>
            </ul>

            <!-- Mood / Quick Filter tags -->
            <div class="flex items-center gap-2">
                <a href="<?= $baseUrl ?>/products?is_new=1"
                   class="inline-flex items-center gap-1 px-3 py-1 text-[11px] font-medium rounded-full bg-charcoal text-white hover:bg-wooddk transition shadow-sm">
                    <span class="text-amber-warm text-[10px]">✨</span>
                    Hàng mới về
                </a>
                <a href="<?= $baseUrl ?>/products?on_sale=1"
                   class="inline-flex items-center gap-1 px-3 py-1 text-[11px] font-medium rounded-full bg-wood/10 text-wood hover:bg-wood hover:text-white border border-wood/30 transition">
                    <span class="text-[10px]">🏷️</span>
                    Ưu đãi tháng
                </a>
            </div>
        </div>
    </div>
</nav>
