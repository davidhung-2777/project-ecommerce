<?php
$baseUrl   = $_ENV['APP_URL'] ?? 'http://localhost/project-ecommerce/public';
$pageTitle = isset($activeCategory) ? htmlspecialchars($activeCategory['name']) . ' - DecorNest' : 'Bộ Sưu Tập Phòng Ngủ - DecorNest';
?>

<!-- Breadcrumb & Sibling Category Banner -->
<div class="bg-cream/60 border-b border-beige/80 py-6 sm:py-8">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        
        <!-- Breadcrumb -->
        <nav class="flex items-center gap-2 text-xs text-muted mb-4" aria-label="Breadcrumb">
            <a href="<?= $baseUrl ?>" class="hover:text-wood transition">Trang chủ</a>
            <span class="text-sand">/</span>
            <?php if ($activeCategory): ?>
            <a href="<?= $baseUrl ?>/products" class="hover:text-wood transition">Sản phẩm</a>
            <span class="text-sand">/</span>
            <span class="text-charcoal font-semibold"><?= htmlspecialchars($activeCategory['name']) ?></span>
            <?php else: ?>
            <span class="text-charcoal font-semibold">Tất cả sản phẩm phòng ngủ</span>
            <?php endif; ?>
        </nav>

        <!-- Category Title & Intro -->
        <div class="max-w-2xl">
            <h1 class="text-2xl sm:text-3xl font-serif font-bold text-charcoal tracking-tight">
                <?= $activeCategory ? htmlspecialchars($activeCategory['name']) : 'Bộ Sưu Tập Đồ Decor Phòng Ngủ' ?>
            </h1>
            <p class="text-xs sm:text-sm text-muted mt-2 leading-relaxed">
                <?= $activeCategory && !empty($activeCategory['description']) 
                    ? htmlspecialchars($activeCategory['description']) 
                    : 'Tất cả các sản phẩm decor phong cách Japandi & Bắc Âu được tuyển chọn để mang lại sự thư thái, an yên cho phòng ngủ của bạn.' ?>
            </p>
        </div>

        <!-- Sibling / Quick Subcategory Tabs -->
        <?php if (!empty($siblings)): ?>
        <div class="flex gap-2 overflow-x-auto pt-6 pb-1 scrollbar-hide">
            <a href="<?= $baseUrl ?>/products"
               class="px-4 py-2 rounded-full text-xs font-semibold whitespace-nowrap transition-all shadow-sm flex-shrink-0 <?= !$activeCategory ? 'bg-charcoal text-white shadow-warm' : 'bg-white border border-beige text-charcoal hover:border-wood hover:text-wood' ?>">
                Tất cả sản phẩm
            </a>
            <?php foreach ($siblings as $sib): ?>
            <a href="<?= $baseUrl ?>/category/<?= $sib['slug'] ?>"
               class="px-4 py-2 rounded-full text-xs font-semibold whitespace-nowrap transition-all shadow-sm flex-shrink-0 <?= ($activeCategory['id'] ?? null) == $sib['id'] ? 'bg-charcoal text-white shadow-warm' : 'bg-white border border-beige text-charcoal hover:border-wood hover:text-wood' ?>">
                <?= htmlspecialchars($sib['name']) ?>
            </a>
            <?php endforeach; ?>
        </div>
        <?php endif; ?>

    </div>
</div>

<div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-10">
    <div class="flex flex-col lg:flex-row gap-10">

        <!-- Sidebar Filter (Desktop) -->
        <aside class="hidden lg:block w-64 flex-shrink-0" id="filter-sidebar">
            <div class="sticky top-28 space-y-6">
                <?php require ROOT_PATH . '/app/views/frontend/partials/filter-sidebar.php'; ?>
            </div>
        </aside>

        <!-- Main Product Listing Content -->
        <div class="flex-1">

            <!-- Controls Bar: Count, Mobile Filter, Sort, Per Page -->
            <div class="bg-cream/40 border border-beige rounded-2xl p-4 sm:p-5 flex flex-col sm:flex-row sm:items-center justify-between gap-4 mb-8">
                <div>
                    <p class="text-xs sm:text-sm font-semibold text-charcoal">
                        Hiển thị <span class="text-wood font-bold"><?= number_format($total ?? 0) ?></span> sản phẩm
                    </p>
                </div>

                <div class="flex items-center gap-3">
                    <!-- Mobile filter button -->
                    <button onclick="document.getElementById('mobile-filter-drawer').classList.remove('hidden')"
                            class="lg:hidden flex items-center gap-2 bg-white border border-beige rounded-xl px-3.5 py-2 text-xs font-semibold text-charcoal hover:bg-cream transition shadow-sm">
                        <svg class="w-4 h-4 text-wood" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 4a1 1 0 011-1h16a1 1 0 011 1v2a1 1 0 01-.293.707L13 13.414V19a1 1 0 01-.553.894l-4 2A1 1 0 017 21v-7.586L3.293 6.707A1 1 0 013 6V4z"/>
                        </svg>
                        <span>Bộ lọc</span>
                    </button>

                    <!-- Sort dropdown -->
                    <div class="flex items-center gap-1.5 text-xs text-muted">
                        <span class="hidden sm:inline">Sắp xếp:</span>
                        <select onchange="applySort(this.value)"
                                class="bg-white border border-beige rounded-xl px-3 py-2 text-xs font-medium text-charcoal focus:outline-none focus:border-wood shadow-sm">
                            <option value="newest"    <?= ($sort ?? '') === 'newest'     ? 'selected' : '' ?>>Mới nhất</option>
                            <option value="price_asc" <?= ($sort ?? '') === 'price_asc'  ? 'selected' : '' ?>>Giá: Thấp → Cao</option>
                            <option value="price_desc"<?= ($sort ?? '') === 'price_desc' ? 'selected' : '' ?>>Giá: Cao → Thấp</option>
                            <option value="popular"   <?= ($sort ?? '') === 'popular'    ? 'selected' : '' ?>>Phổ biến nhất</option>
                        </select>
                    </div>

                    <!-- Per Page dropdown -->
                    <select onchange="applyPerPage(this.value)"
                            class="bg-white border border-beige rounded-xl px-3 py-2 text-xs font-medium text-charcoal focus:outline-none focus:border-wood shadow-sm">
                        <option value="8"  <?= ($perPage ?? 16) == 8  ? 'selected' : '' ?>>8 / trang</option>
                        <option value="16" <?= ($perPage ?? 16) == 16 ? 'selected' : '' ?>>16 / trang</option>
                        <option value="24" <?= ($perPage ?? 16) == 24 ? 'selected' : '' ?>>24 / trang</option>
                    </select>
                </div>
            </div>

            <!-- Product Grid -->
            <div id="product-grid" class="grid grid-cols-2 sm:grid-cols-3 xl:grid-cols-3 gap-4 sm:gap-6">
                <?php require ROOT_PATH . '/app/views/frontend/partials/product-grid.php'; ?>
            </div>

            <!-- Pagination -->
            <?php if (($last_page ?? 1) > 1): ?>
            <div class="mt-12 flex justify-center">
                <?php require ROOT_PATH . '/app/views/frontend/partials/pagination.php'; ?>
            </div>
            <?php endif; ?>

        </div>
    </div>
</div>

<!-- Mobile Filter Drawer Modal -->
<div id="mobile-filter-drawer" class="hidden fixed inset-0 z-50 lg:hidden">
    <div class="absolute inset-0 bg-charcoal/50 backdrop-blur-sm" onclick="document.getElementById('mobile-filter-drawer').classList.add('hidden')"></div>
    <div class="absolute left-0 top-0 bottom-0 w-80 bg-warmwhite shadow-2xl overflow-y-auto flex flex-col z-10 border-r border-beige">
        <div class="flex items-center justify-between p-5 border-b border-beige bg-white">
            <h3 class="font-serif font-bold text-base text-charcoal">Bộ lọc sản phẩm</h3>
            <button onclick="document.getElementById('mobile-filter-drawer').classList.add('hidden')" class="p-2 text-muted hover:text-charcoal transition rounded-full">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.8" d="M6 18L18 6M6 6l12 12"/>
                </svg>
            </button>
        </div>
        <div class="p-5 flex-1">
            <?php require ROOT_PATH . '/app/views/frontend/partials/filter-sidebar.php'; ?>
        </div>
    </div>
</div>

<!-- SEO Content (If available) -->
<?php if ($activeCategory && !empty($activeCategory['seo_desc'])): ?>
<div class="border-t border-beige/80 mt-16 py-12 bg-cream/30">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 prose prose-sm max-w-none text-muted leading-relaxed">
        <h2 class="text-base font-serif font-bold text-charcoal"><?= htmlspecialchars($activeCategory['seo_title'] ?? $activeCategory['name']) ?></h2>
        <p><?= htmlspecialchars($activeCategory['seo_desc']) ?></p>
    </div>
</div>
<?php endif; ?>
