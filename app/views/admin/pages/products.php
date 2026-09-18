<?php
$pageTitle = 'Quản lý sản phẩm';
$base = $_ENV['APP_URL'] ?? '';

// Robust list normalization to prevent any type error
$productList = $products ?? ($data ?? []);
if (isset($productList['data']) && is_array($productList['data'])) {
    $productList = $productList['data'];
}
if (!is_array($productList)) {
    $productList = [];
}

$total        = (int) ($total ?? count($productList));
$current_page = (int) ($current_page ?? 1);
$last_page    = (int) ($last_page ?? 1);
$search       = htmlspecialchars((string) ($search ?? ''));
$categoryId   = (string) ($category_id ?? '');
$statusVal    = (string) ($status ?? '');
$categories   = $categories ?? [];
$stats        = $stats ?? ['total' => $total, 'active' => 0, 'low_stock' => 0];

if (!function_exists('adminImgUrl')) {
    function adminImgUrl($url, $base) {
        if (empty($url)) return $base . '/assets/images/product-placeholder.jpg';
        if (str_starts_with($url, 'http://') || str_starts_with($url, 'https://')) return $url;
        return rtrim($base, '/') . '/' . ltrim($url, '/');
    }
}
?>

<div class="w-full space-y-6">
    <!-- Header -->
    <div class="flex flex-col sm:flex-row items-start sm:items-center justify-between gap-4">
        <div>
            <div class="flex items-center gap-2 text-xs font-semibold uppercase tracking-wider text-wood">
                <span>Quản lý kho</span>
                <span>•</span>
                <span>DecorNest</span>
            </div>
            <h1 class="text-2xl font-bold tracking-tight text-charcoal sm:text-3xl mt-0.5">Sản phẩm</h1>
            <p class="mt-1 text-xs sm:text-sm text-muted">Quản lý danh sách, tồn kho, giá bán và trạng thái hiển thị của các sản phẩm decor.</p>
        </div>
        <div class="flex items-center gap-2.5 w-full sm:w-auto">
            <a href="<?= $base ?>/admin/products/create"
               class="inline-flex w-full sm:w-auto items-center justify-center gap-2 rounded-xl bg-charcoal px-5 py-2.5 text-xs font-semibold uppercase tracking-wider text-white shadow-warm transition-all duration-200 hover:bg-wooddk hover:shadow-warm-lg">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/>
                </svg>
                <span>Thêm sản phẩm mới</span>
            </a>
        </div>
    </div>

    <!-- Quick Stats Overview -->
    <div class="grid grid-cols-2 lg:grid-cols-4 gap-3.5 sm:gap-4">
        <div class="bg-white rounded-2xl border border-gray-200 p-4 shadow-sm flex items-center justify-between">
            <div>
                <p class="text-[11px] font-semibold text-muted uppercase tracking-wider">Tổng sản phẩm</p>
                <p class="text-xl sm:text-2xl font-bold text-charcoal mt-1"><?= number_format($stats['total'] ?? $total) ?></p>
            </div>
            <div class="w-10 h-10 rounded-xl bg-cream flex items-center justify-center text-wood text-lg">
                📦
            </div>
        </div>
        <div class="bg-white rounded-2xl border border-gray-200 p-4 shadow-sm flex items-center justify-between">
            <div>
                <p class="text-[11px] font-semibold text-muted uppercase tracking-wider">Đang hiển thị</p>
                <p class="text-xl sm:text-2xl font-bold text-emerald-600 mt-1"><?= number_format($stats['active'] ?? 0) ?></p>
            </div>
            <div class="w-10 h-10 rounded-xl bg-emerald-50 flex items-center justify-center text-emerald-600 text-lg">
                👁️
            </div>
        </div>
        <div class="bg-white rounded-2xl border border-gray-200 p-4 shadow-sm flex items-center justify-between">
            <div>
                <p class="text-[11px] font-semibold text-muted uppercase tracking-wider">Đang ẩn</p>
                <p class="text-xl sm:text-2xl font-bold text-gray-500 mt-1"><?= number_format(max(0, ($stats['total'] ?? 0) - ($stats['active'] ?? 0))) ?></p>
            </div>
            <div class="w-10 h-10 rounded-xl bg-gray-100 flex items-center justify-center text-gray-500 text-lg">
                🔒
            </div>
        </div>
        <div class="bg-white rounded-2xl border border-gray-200 p-4 shadow-sm flex items-center justify-between">
            <div>
                <p class="text-[11px] font-semibold text-muted uppercase tracking-wider">Tồn kho thấp (&lt;5)</p>
                <p class="text-xl sm:text-2xl font-bold text-amber-600 mt-1"><?= number_format($stats['low_stock'] ?? 0) ?></p>
            </div>
            <div class="w-10 h-10 rounded-xl bg-amber-50 flex items-center justify-center text-amber-600 text-lg">
                ⚠️
            </div>
        </div>
    </div>

    <!-- Search & Filters -->
    <div class="rounded-2xl border border-gray-200 bg-white p-4 shadow-sm">
        <form method="GET" action="<?= $base ?>/admin/products" class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-12 gap-3 items-center">
            
            <!-- Search Keyword -->
            <div class="lg:col-span-5">
                <div class="relative">
                    <span class="absolute inset-y-0 left-0 flex items-center pl-3 text-gray-400">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/>
                        </svg>
                    </span>
                    <input type="text" 
                           name="search" 
                           value="<?= $search ?>" 
                           placeholder="Tìm theo tên sản phẩm, mã SKU..." 
                           class="w-full rounded-xl border border-gray-200 bg-gray-50/50 pl-9 pr-4 py-2.5 text-xs sm:text-sm text-charcoal outline-none transition focus:bg-white focus:border-wood focus:ring-2 focus:ring-wood/20">
                </div>
            </div>

            <!-- Category filter -->
            <div class="lg:col-span-3">
                <select name="category_id" 
                        class="w-full rounded-xl border border-gray-200 bg-gray-50/50 px-3 py-2.5 text-xs sm:text-sm text-charcoal outline-none transition focus:bg-white focus:border-wood focus:ring-2 focus:ring-wood/20">
                    <option value="">-- Tất cả danh mục --</option>
                    <?php foreach ($categories as $cat): ?>
                    <option value="<?= $cat['id'] ?>" <?= ($categoryId !== '' && (int)$categoryId === (int)$cat['id']) ? 'selected' : '' ?>>
                        <?= htmlspecialchars($cat['name']) ?>
                    </option>
                    <?php endforeach; ?>
                </select>
            </div>

            <!-- Status filter -->
            <div class="lg:col-span-2">
                <select name="status" 
                        class="w-full rounded-xl border border-gray-200 bg-gray-50/50 px-3 py-2.5 text-xs sm:text-sm text-charcoal outline-none transition focus:bg-white focus:border-wood focus:ring-2 focus:ring-wood/20">
                    <option value="">-- Trạng thái --</option>
                    <option value="1" <?= ($statusVal === '1') ? 'selected' : '' ?>>Đang hiển thị</option>
                    <option value="0" <?= ($statusVal === '0') ? 'selected' : '' ?>>Đang ẩn</option>
                </select>
            </div>

            <!-- Action Buttons -->
            <div class="lg:col-span-2 flex items-center gap-2">
                <button type="submit" 
                        class="flex-1 inline-flex items-center justify-center gap-1.5 rounded-xl bg-wood px-4 py-2.5 text-xs font-bold text-white transition-colors hover:bg-wooddk shadow-sm">
                    <span>Lọc</span>
                </button>
                <?php if ($search !== '' || $categoryId !== '' || $statusVal !== ''): ?>
                <a href="<?= $base ?>/admin/products" 
                   class="inline-flex items-center justify-center rounded-xl border border-gray-200 bg-white px-3 py-2.5 text-xs text-gray-600 hover:bg-gray-50 transition-colors"
                   title="Xóa bộ lọc">
                    ✕
                </a>
                <?php endif; ?>
            </div>

        </form>
    </div>

    <!-- Products Table Card -->
    <div class="overflow-hidden rounded-2xl border border-gray-200 bg-white shadow-sm">
        <?php if (!empty($productList)): ?>
        <div class="overflow-x-auto">
            <table class="w-full min-w-[760px] text-sm">
                <thead class="bg-gray-50/80 border-b border-gray-200 text-[11px] font-bold uppercase tracking-wider text-muted">
                    <tr>
                        <th class="px-5 py-3.5 text-left">Sản phẩm</th>
                        <th class="px-4 py-3.5 text-left">SKU</th>
                        <th class="px-4 py-3.5 text-right">Giá bán</th>
                        <th class="px-4 py-3.5 text-center">Tồn kho</th>
                        <th class="px-4 py-3.5 text-center">Trạng thái</th>
                        <th class="px-5 py-3.5 text-right">Hành động</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-100 bg-white">
                    <?php foreach ($productList as $product): ?>
                    <?php if (!is_array($product)) continue; ?>
                    <tr class="transition-colors hover:bg-cream/20 group" id="product-row-<?= (int)$product['id'] ?>">
                        <!-- Product info -->
                        <td class="px-5 py-3.5">
                            <div class="flex items-center gap-3.5">
                                <div class="w-12 h-12 rounded-xl overflow-hidden bg-gray-50 flex-shrink-0 border border-gray-200/80 shadow-xs relative">
                                    <img src="<?= adminImgUrl($product['thumbnail'] ?? '', $base) ?>"
                                         alt="<?= htmlspecialchars($product['name'] ?? '') ?>"
                                         class="w-full h-full object-cover group-hover:scale-105 transition-transform duration-300"
                                         onerror="this.src='<?= $base ?>/assets/images/product-placeholder.jpg'; this.onerror=null;">
                                </div>
                                <div class="min-w-0 flex-1">
                                    <p class="font-bold text-xs sm:text-sm text-charcoal truncate">
                                        <a href="<?= $base ?>/admin/products/<?= (int)$product['id'] ?>/edit" class="hover:text-wood transition-colors">
                                            <?= htmlspecialchars($product['name'] ?? 'Không có tên') ?>
                                        </a>
                                    </p>
                                    <div class="flex items-center gap-2 mt-0.5">
                                        <span class="text-[11px] text-muted truncate">
                                            <?= htmlspecialchars($product['category_name'] ?? 'Chưa phân loại') ?>
                                        </span>
                                        <?php if (!empty($product['is_featured'])): ?>
                                        <span class="px-1.5 py-0.2 rounded bg-amber-50 text-amber-700 text-[10px] font-semibold border border-amber-200">Nổi bật</span>
                                        <?php endif; ?>
                                        <?php if (!empty($product['is_new'])): ?>
                                        <span class="px-1.5 py-0.2 rounded bg-blue-50 text-blue-700 text-[10px] font-semibold border border-blue-200">Mới</span>
                                        <?php endif; ?>
                                    </div>
                                </div>
                            </div>
                        </td>

                        <!-- SKU -->
                        <td class="px-4 py-3.5 text-xs text-muted font-mono">
                            <?= htmlspecialchars($product['sku'] ?: '-') ?>
                        </td>

                        <!-- Price -->
                        <td class="px-4 py-3.5 text-right font-medium">
                            <div class="flex flex-col items-end">
                                <?php 
                                $price = (float)($product['price'] ?? 0);
                                $salePrice = !empty($product['sale_price']) ? (float)$product['sale_price'] : null;
                                ?>
                                <?php if ($salePrice !== null && $salePrice < $price && $price > 0): ?>
                                    <span class="text-xs sm:text-sm font-bold text-wood">
                                        <?= number_format($salePrice, 0, ',', '.') ?>đ
                                    </span>
                                    <span class="text-[11px] text-gray-400 line-through">
                                        <?= number_format($price, 0, ',', '.') ?>đ
                                    </span>
                                <?php else: ?>
                                    <span class="text-xs sm:text-sm font-bold text-charcoal">
                                        <?= number_format($price, 0, ',', '.') ?>đ
                                    </span>
                                <?php endif; ?>
                            </div>
                        </td>

                        <!-- Stock -->
                        <td class="px-4 py-3.5 text-center">
                            <?php $stock = (int)($product['stock'] ?? 0); ?>
                            <span class="inline-flex items-center px-2.5 py-1 rounded-full text-xs font-bold <?= $stock <= 0 ? 'bg-red-100 text-red-700' : ($stock < 5 ? 'bg-amber-100 text-amber-800' : 'bg-green-100 text-green-800') ?>">
                                <?= number_format($stock, 0, ',', '.') ?>
                            </span>
                        </td>

                        <!-- Status with click-to-toggle -->
                        <td class="px-4 py-3.5 text-center">
                            <?php $isActive = !empty($product['is_active']); ?>
                            <button type="button" 
                                    onclick="toggleProductStatus(<?= (int)$product['id'] ?>, this)"
                                    class="status-btn inline-flex items-center gap-1.5 px-3 py-1 rounded-full text-xs font-semibold cursor-pointer transition-all border <?= $isActive ? 'bg-emerald-50 text-emerald-700 border-emerald-200 hover:bg-emerald-100' : 'bg-gray-100 text-gray-500 border-gray-200 hover:bg-gray-200' ?>"
                                    title="Nhấn để đổi trạng thái Hiển thị / Ẩn">
                                <span class="w-1.5 h-1.5 rounded-full <?= $isActive ? 'bg-emerald-500' : 'bg-gray-400' ?>"></span>
                                <span><?= $isActive ? 'Hiện' : 'Ẩn' ?></span>
                            </button>
                        </td>

                        <!-- Actions -->
                        <td class="px-5 py-3.5 text-right">
                            <div class="flex items-center justify-end gap-2">
                                <a href="<?= $base ?>/admin/products/<?= (int)$product['id'] ?>/edit"
                                   class="inline-flex items-center gap-1 px-2.5 py-1.5 rounded-lg border border-gray-200 bg-white text-xs font-semibold text-charcoal hover:bg-cream hover:border-wood transition shadow-2xs">
                                    <svg class="w-3.5 h-3.5 text-wood" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/>
                                    </svg>
                                    <span>Sửa</span>
                                </a>
                                <button type="button" 
                                        onclick="deleteProduct(<?= (int)$product['id'] ?>, this)"
                                        class="inline-flex items-center gap-1 px-2.5 py-1.5 rounded-lg border border-red-200 bg-white text-xs font-semibold text-red-600 hover:bg-red-50 hover:border-red-300 transition shadow-2xs">
                                    <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/>
                                    </svg>
                                    <span>Xóa</span>
                                </button>
                            </div>
                        </td>
                    </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>

        <!-- Pagination -->
        <?php if ($last_page > 1): ?>
        <div class="border-t border-gray-100 bg-gray-50/50 px-5 py-3.5 flex flex-col sm:flex-row items-center justify-between gap-3">
            <p class="text-xs text-muted">
                Đang hiển thị trang <strong class="text-charcoal font-semibold"><?= $current_page ?></strong> / <strong><?= $last_page ?></strong> (Tổng <?= number_format($total) ?> sản phẩm)
            </p>
            <div class="flex items-center gap-1">
                <?php 
                $queryParams = [];
                if ($search !== '')      $queryParams['search'] = $search;
                if ($categoryId !== '')  $queryParams['category_id'] = $categoryId;
                if ($statusVal !== '')   $queryParams['status'] = $statusVal;
                
                $buildUrl = function($p) use ($queryParams) {
                    $params = array_merge($queryParams, ['page' => $p]);
                    return '?' . http_build_query($params);
                };
                ?>

                <?php if ($current_page > 1): ?>
                <a href="<?= $buildUrl($current_page - 1) ?>" 
                   class="px-3 py-1.5 rounded-lg border border-gray-200 bg-white text-xs font-medium text-charcoal hover:bg-cream transition">
                    ← Trước
                </a>
                <?php endif; ?>

                <?php for ($p = max(1, $current_page - 2); $p <= min($last_page, $current_page + 2); $p++): ?>
                <a href="<?= $buildUrl($p) ?>" 
                   class="px-3 py-1.5 rounded-lg text-xs font-bold transition <?= $p === $current_page ? 'bg-charcoal text-white shadow-warm' : 'border border-gray-200 bg-white text-charcoal hover:bg-cream' ?>">
                    <?= $p ?>
                </a>
                <?php endfor; ?>

                <?php if ($current_page < $last_page): ?>
                <a href="<?= $buildUrl($current_page + 1) ?>" 
                   class="px-3 py-1.5 rounded-lg border border-gray-200 bg-white text-xs font-medium text-charcoal hover:bg-cream transition">
                    Sau →
                </a>
                <?php endif; ?>
            </div>
        </div>
        <?php endif; ?>

        <?php else: ?>
        <!-- Empty State -->
        <div class="py-16 text-center px-4">
            <div class="w-16 h-16 rounded-full bg-cream text-wood flex items-center justify-center text-3xl mx-auto mb-4 shadow-warm">
                📦
            </div>
            <h3 class="font-bold text-base text-charcoal">Không tìm thấy sản phẩm nào</h3>
            <p class="text-xs text-muted mt-1 max-w-sm mx-auto">
                <?= ($search !== '' || $categoryId !== '' || $statusVal !== '') 
                    ? 'Không có sản phẩm nào phù hợp với bộ lọc hiện tại. Vui lòng thử tìm kiếm với từ khóa khác.' 
                    : 'Hệ thống hiện chưa có sản phẩm nào trong kho. Hãy bắt đầu bằng việc thêm sản phẩm mới!' ?>
            </p>
            <div class="mt-5 flex justify-center gap-3">
                <?php if ($search !== '' || $categoryId !== '' || $statusVal !== ''): ?>
                <a href="<?= $base ?>/admin/products" 
                   class="px-4 py-2 rounded-xl border border-gray-200 text-xs font-semibold text-charcoal hover:bg-gray-50 transition">
                    Xóa bộ lọc
                </a>
                <?php endif; ?>
                <a href="<?= $base ?>/admin/products/create" 
                   class="px-5 py-2 rounded-xl bg-charcoal text-white text-xs font-semibold uppercase tracking-wider hover:bg-wooddk transition shadow-warm">
                    + Thêm sản phẩm mới
                </a>
            </div>
        </div>
        <?php endif; ?>
    </div>
</div>
