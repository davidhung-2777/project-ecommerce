<?php
$pageTitle = 'Quản lý sản phẩm';
$base = $_ENV['APP_URL'] ?? '';
?>
<div class="space-y-5">
    <div class="flex items-center justify-between">
        <div>
            <h1 class="text-xl font-bold text-charcoal">Sản phẩm</h1>
            <p class="text-sm text-muted">Tổng <?= number_format($total ?? 0) ?> sản phẩm</p>
        </div>
        <a href="<?= $base ?>/admin/products/create"
           class="bg-charcoal text-white px-4 py-2 rounded-lg text-sm font-medium hover:bg-wooddk transition flex items-center gap-2">
            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/>
            </svg>
            Thêm sản phẩm
        </a>
    </div>

    <!-- Search -->
    <form class="flex gap-3">
        <input type="text" name="search" value="<?= htmlspecialchars($search ?? '') ?>" placeholder="Tìm kiếm sản phẩm..."
               class="flex-1 max-w-xs border border-gray-200 rounded-lg px-4 py-2 text-sm focus:outline-none focus:border-wood">
        <button type="submit" class="border border-gray-200 rounded-lg px-4 py-2 text-sm hover:bg-gray-50 transition">Tìm</button>
    </form>

    <!-- Table -->
    <div class="bg-white rounded-xl border border-gray-200 overflow-hidden">
        <div class="overflow-x-auto">
            <table class="w-full text-sm">
                <thead class="bg-gray-50 border-b border-gray-100">
                    <tr>
                        <th class="text-left px-5 py-3 text-xs font-medium text-muted">Sản phẩm</th>
                        <th class="text-left px-5 py-3 text-xs font-medium text-muted">SKU</th>
                        <th class="text-right px-5 py-3 text-xs font-medium text-muted">Giá</th>
                        <th class="text-center px-5 py-3 text-xs font-medium text-muted">Tồn kho</th>
                        <th class="text-center px-5 py-3 text-xs font-medium text-muted">Trạng thái</th>
                        <th class="px-5 py-3"></th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-100">
                    <?php foreach (($data ?? []) as $product): ?>
                    <tr class="hover:bg-gray-50">
                        <td class="px-5 py-3">
                            <div class="flex items-center gap-3">
                                <div class="w-10 h-10 rounded-lg overflow-hidden bg-cream flex-shrink-0">
                                    <img src="<?= htmlspecialchars($product['thumbnail'] ?: $base . '/assets/images/product-placeholder.jpg') ?>"
                                         class="w-full h-full object-cover">
                                </div>
                                <div>
                                    <p class="font-medium text-charcoal"><?= htmlspecialchars($product['name']) ?></p>
                                    <p class="text-xs text-muted"><?= htmlspecialchars($product['category_name'] ?? '') ?></p>
                                </div>
                            </div>
                        </td>
                        <td class="px-5 py-3 text-muted"><?= htmlspecialchars($product['sku'] ?? '-') ?></td>
                        <td class="px-5 py-3 text-right font-medium"><?= number_format($product['sale_price'] ?? $product['price']) ?>đ</td>
                        <td class="px-5 py-3 text-center <?= $product['stock'] < 5 ? 'text-red-500 font-medium' : 'text-muted' ?>"><?= $product['stock'] ?></td>
                        <td class="px-5 py-3 text-center">
                            <span class="px-2 py-0.5 rounded-full text-xs <?= $product['is_active'] ? 'bg-green-50 text-green-700' : 'bg-gray-100 text-gray-500' ?>">
                                <?= $product['is_active'] ? 'Hiện' : 'Ẩn' ?>
                            </span>
                        </td>
                        <td class="px-5 py-3">
                            <div class="flex items-center gap-2 justify-end">
                                <a href="<?= $base ?>/admin/products/<?= $product['id'] ?>/edit"
                                   class="text-xs text-blue-600 hover:underline">Sửa</a>
                                <button onclick="deleteProduct(<?= $product['id'] ?>)"
                                        class="text-xs text-red-500 hover:underline">Xóa</button>
                            </div>
                        </td>
                    </tr>
                    <?php endforeach; ?>
                    <?php if (empty($data)): ?>
                    <tr><td colspan="6" class="px-5 py-10 text-center text-muted">Không có sản phẩm nào.</td></tr>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>
        <?php if (($last_page ?? 1) > 1): ?>
        <div class="px-5 py-4 border-t border-gray-100 flex justify-center gap-1">
            <?php for ($p = 1; $p <= $last_page; $p++): ?>
            <a href="?page=<?= $p ?><?= $search ? '&search=' . urlencode($search) : '' ?>"
               class="px-3 py-1.5 rounded text-xs <?= $p == $current_page ? 'bg-charcoal text-white' : 'hover:bg-gray-100' ?>">
                <?= $p ?>
            </a>
            <?php endfor; ?>
        </div>
        <?php endif; ?>
    </div>
</div>

<script>
function deleteProduct(id) {
    if (!confirm('Bạn có chắc muốn ẩn sản phẩm này?')) return;
    fetch(APP_URL + '/admin/products/' + id + '/delete', {
        method: 'POST',
        headers: { 'X-Requested-With': 'XMLHttpRequest', 'Content-Type': 'application/x-www-form-urlencoded' }
    }).then(r => r.json()).then(d => { if (d.success) location.reload(); });
}
</script>
