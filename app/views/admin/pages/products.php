<?php
$pageTitle = 'Quản lý sản phẩm';
$base = $_ENV['APP_URL'] ?? '';

// Null safety
$data = $data ?? [];
$total = $total ?? 0;
$search = $search ?? '';
$current_page = $current_page ?? 1;
$last_page = $last_page ?? 1;

// Helper function to fix image URL
function adminImgUrl($url, $base) {
    if (empty($url)) return $base . '/assets/images/product-placeholder.jpg';
    if (str_starts_with($url, 'http://') || str_starts_with($url, 'https://')) return $url;
    return $base . '/' . ltrim($url, '/');
}
?>

<div class="w-full space-y-5">
    <!-- Header -->
    <div class="flex flex-col sm:flex-row items-start sm:items-center justify-between gap-4">
        <div>
            <h1 class="text-2xl font-bold text-charcoal">Sản phẩm</h1>
            <p class="text-sm text-muted mt-1">Quản lý <?= number_format($total) ?> sản phẩm trong hệ thống</p>
        </div>
        <a href="<?= $base ?>/admin/products/create"
           class="inline-flex items-center gap-2 bg-charcoal text-white px-5 py-2.5 rounded-lg text-sm font-medium hover:bg-wooddk transition-colors shadow-sm">
            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/>
            </svg>
            Thêm sản phẩm mới
        </a>
    </div>

    <!-- Search & Filters -->
    <div class="bg-white rounded-xl border border-gray-200 p-4">
        <form method="GET" class="flex flex-col sm:flex-row gap-3">
            <input type="text" 
                   name="search" 
                   value="<?= htmlspecialchars($search) ?>" 
                   placeholder="Tìm kiếm theo tên, SKU..." 
                   class="flex-1 border border-gray-300 rounded-lg px-4 py-2.5 text-sm focus:outline-none focus:ring-2 focus:ring-wood focus:border-transparent transition">
            <button type="submit" 
                    class="inline-flex items-center justify-center gap-2 bg-wood text-white px-5 py-2.5 rounded-lg text-sm font-medium hover:bg-wooddk transition-colors">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/>
                </svg>
                Tìm kiếm
            </button>
            <?php if ($search): ?>
            <a href="<?= $base ?>/admin/products" 
               class="inline-flex items-center justify-center px-4 py-2.5 border border-gray-300 rounded-lg text-sm text-gray-700 hover:bg-gray-50 transition-colors">
                Xóa lọc
            </a>
            <?php endif; ?>
        </form>
    </div>

    <!-- Products Table -->
    <div class="bg-white rounded-xl border border-gray-200 overflow-hidden shadow-sm">
        <?php if (!empty($data) && is_array($data)): ?>
        <div class="overflow-x-auto">
            <table class="w-full text-sm">
                <thead class="bg-gray-50 border-b border-gray-200">
                    <tr>
                        <th class="text-left px-6 py-4 text-xs font-semibold text-gray-700 uppercase tracking-wider">Sản phẩm</th>
                        <th class="text-left px-6 py-4 text-xs font-semibold text-gray-700 uppercase tracking-wider">SKU</th>
                        <th class="text-right px-6 py-4 text-xs font-semibold text-gray-700 uppercase tracking-wider">Giá bán</th>
                        <th class="text-center px-6 py-4 text-xs font-semibold text-gray-700 uppercase tracking-wider">Tồn kho</th>
                        <th class="text-center px-6 py-4 text-xs font-semibold text-gray-700 uppercase tracking-wider">Trạng thái</th>
                        <th class="px-6 py-4"></th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-100 bg-white">
                    <?php foreach ($data as $product): ?>
                    <tr class="hover:bg-gray-50 transition-colors">
                        <td class="px-6 py-4">
                            <div class="flex items-center gap-3">
                                <div class="w-12 h-12 rounded-lg overflow-hidden bg-gray-100 flex-shrink-0 border border-gray-200">
                                    <img src="<?= adminImgUrl($product['thumbnail'] ?? '', $base) ?>"
                                         alt="<?= htmlspecialchars($product['name'] ?? '') ?>"
                                         class="w-full h-full object-cover"
                                         onerror="this.src='<?= $base ?>/assets/images/product-placeholder.jpg'; this.onerror=null;">
                                </div>
                                <div class="min-w-0">
                                    <p class="font-medium text-gray-900 truncate"><?= htmlspecialchars($product['name'] ?? '') ?></p>
                                    <p class="text-xs text-gray-500 truncate"><?= htmlspecialchars($product['category_name'] ?? 'Chưa phân loại') ?></p>
                                </div>
                            </div>
                        </td>
                        <td class="px-6 py-4">
                            <span class="text-gray-600 font-mono text-xs"><?= htmlspecialchars($product['sku'] ?? '-') ?></span>
                        </td>
                        <td class="px-6 py-4 text-right">
                            <div class="flex flex-col items-end">
                                <span class="font-semibold text-gray-900"><?= number_format($product['sale_price'] ?? $product['price']) ?>đ</span>
                                <?php if (!empty($product['sale_price']) && $product['sale_price'] < $product['price']): ?>
                                <span class="text-xs text-gray-400 line-through"><?= number_format($product['price']) ?>đ</span>
                                <?php endif; ?>
                            </div>
                        </td>
                        <td class="px-6 py-4 text-center">
                            <span class="inline-flex items-center px-2.5 py-1 rounded-full text-xs font-medium <?= ($product['stock'] ?? 0) < 5 ? 'bg-red-100 text-red-700' : 'bg-green-100 text-green-700' ?>">
                                <?= number_format($product['stock'] ?? 0) ?>
                            </span>
                        </td>
                        <td class="px-6 py-4 text-center">
                            <span class="inline-flex items-center px-2.5 py-1 rounded-full text-xs font-medium <?= ($product['is_active'] ?? 0) ? 'bg-emerald-100 text-emerald-700' : 'bg-gray-100 text-gray-600' ?>">
                                <?= ($product['is_active'] ?? 0) ? '✓ Hiển thị' : 'Ẩn' ?>
                            </span>
                        </td>
                        <td class="px-6 py-4">
                            <div class="flex items-center justify-end gap-3">
                                <a href="<?= $base ?>/admin/products/<?= $product['id'] ?>/edit"
                                   class="inline-flex items-center gap-1 text-xs font-medium text-blue-600 hover:text-blue-700 hover:underline">
                                    <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/>
                                    </svg>
                                    Sửa
                                </a>
                                <button onclick="deleteProduct(<?= $product['id'] ?>)"
                                        class="inline-flex items-center gap-1 text-xs font-medium text-red-600 hover:text-red-700 hover:underline">
                                    <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/>
                                    </svg>
                                    Xóa
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
        <div class="px-6 py-4 border-t border-gray-200 bg-gray-50">
            <div class="flex items-center justify-between">
                <p class="text-sm text-gray-600">
                    Trang <span class="font-medium"><?= $current_page ?></span> / <span class="font-medium"><?= $last_page ?></span>
                </p>
                <div class="flex gap-1">
                    <?php if ($current_page > 1): ?>
                    <a href="?page=<?= $current_page - 1 ?><?= $search ? '&search=' . urlencode($search) : '' ?>"
                       class="px-3 py-1.5 rounded-md text-sm font-medium text-gray-700 bg-white border border-gray-300 hover:bg-gray-50 transition-colors">
                        ← Trước
                    </a>
                    <?php endif; ?>
                    
                    <?php for ($p = max(1, $current_page - 2); $p <= min($last_page, $current_page + 2); $p++): ?>
                    <a href="?page=<?= $p ?><?= $search ? '&search=' . urlencode($search) : '' ?>"
                       class="px-3 py-1.5 rounded-md text-sm font-medium transition-colors <?= $p == $current_page ? 'bg-charcoal text-white' : 'text-gray-700 bg-white border border-gray-300 hover:bg-gray-50' ?>">
                        <?= $p ?>
                    </a>
                    <?php endfor; ?>
                    
                    <?php if ($current_page < $last_page): ?>
                    <a href="?page=<?= $current_page + 1 ?><?= $search ? '&search=' . urlencode($search) : '' ?>"
                       class="px-3 py-1.5 rounded-md text-sm font-medium text-gray-700 bg-white border border-gray-300 hover:bg-gray-50 transition-colors">
                        Sau →
                    </a>
                    <?php endif; ?>
                </div>
            </div>
        </div>
        <?php endif; ?>
        
        <?php else: ?>
        <!-- Empty State -->
        <div class="px-6 py-16 text-center">
            <div class="inline-flex items-center justify-center w-16 h-16 rounded-full bg-gray-100 mb-4">
                <svg class="w-8 h-8 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 13V6a2 2 0 00-2-2H6a2 2 0 00-2 2v7m16 0v5a2 2 0 01-2 2H6a2 2 0 01-2-2v-5m16 0h-2.586a1 1 0 00-.707.293l-2.414 2.414a1 1 0 01-.707.293h-3.172a1 1 0 01-.707-.293l-2.414-2.414A1 1 0 006.586 13H4"/>
                </svg>
            </div>
            <h3 class="text-lg font-semibold text-gray-900 mb-2">Chưa có sản phẩm nào</h3>
            <p class="text-sm text-gray-600 mb-6">
                <?= $search ? 'Không tìm thấy sản phẩm phù hợp với từ khóa "' . htmlspecialchars($search) . '"' : 'Bắt đầu bằng cách thêm sản phẩm đầu tiên của bạn' ?>
            </p>
            <?php if (!$search): ?>
            <a href="<?= $base ?>/admin/products/create"
               class="inline-flex items-center gap-2 bg-charcoal text-white px-5 py-2.5 rounded-lg text-sm font-medium hover:bg-wooddk transition-colors">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/>
                </svg>
                Thêm sản phẩm đầu tiên
            </a>
            <?php else: ?>
            <a href="<?= $base ?>/admin/products"
               class="inline-flex items-center gap-2 border border-gray-300 text-gray-700 px-5 py-2.5 rounded-lg text-sm font-medium hover:bg-gray-50 transition-colors">
                Xem tất cả sản phẩm
            </a>
            <?php endif; ?>
        </div>
        <?php endif; ?>
    </div>
</div>

<script>
// Delete product with improved UX
function deleteProduct(id) {
    if (!confirm('Bạn có chắc muốn ẩn sản phẩm này?\n\nSản phẩm sẽ bị ẩn khỏi website nhưng không bị xóa khỏi hệ thống.')) {
        return;
    }

    const btn = event.target;
    const originalText = btn.textContent;
    btn.disabled = true;
    btn.textContent = 'Đang xử lý...';
    btn.style.opacity = '0.6';

    fetch(APP_URL + '/admin/products/' + id + '/delete', {
        method: 'POST',
        headers: {
            'X-Requested-With': 'XMLHttpRequest',
            'Content-Type': 'application/x-www-form-urlencoded'
        }
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            const row = btn.closest('tr');
            if (row) {
                row.style.opacity = '0.4';
                row.style.transition = 'opacity 0.3s ease';
                setTimeout(() => {
                    row.remove();
                    showToast('Đã ẩn sản phẩm thành công', 'success');
                    // Reload if no products left
                    const tbody = document.querySelector('tbody');
                    if (tbody && tbody.querySelectorAll('tr').length === 0) {
                        window.location.reload();
                    }
                }, 300);
            } else {
                window.location.reload();
            }
        } else {
            showToast(data.message || 'Không thể cập nhật sản phẩm', 'error');
            btn.disabled = false;
            btn.textContent = originalText;
            btn.style.opacity = '1';
        }
    })
    .catch(error => {
        console.error('Error:', error);
        showToast('Không thể kết nối đến máy chủ', 'error');
        btn.disabled = false;
        btn.textContent = originalText;
        btn.style.opacity = '1';
    });
}

// Toast notification helper
function showToast(message, type = 'success') {
    const toast = document.createElement('div');
    const colors = {
        success: 'bg-green-50 border-green-200 text-green-800',
        error: 'bg-red-50 border-red-200 text-red-800'
    };
    const icons = { success: '✓', error: '✕' };
    
    toast.className = `fixed top-4 right-4 z-50 flex items-center gap-3 px-4 py-3 rounded-lg border shadow-lg text-sm font-medium ${colors[type] || colors.success}`;
    toast.innerHTML = `<span>${icons[type] || icons.success}</span> ${message}`;
    toast.style.animation = 'slideInRight 0.3s ease';
    
    document.body.appendChild(toast);
    
    setTimeout(() => {
        toast.style.opacity = '0';
        toast.style.transform = 'translateX(20px)';
        toast.style.transition = 'all 0.3s ease';
        setTimeout(() => toast.remove(), 300);
    }, 3000);
}

// Add animation keyframes
if (!document.getElementById('toast-animations')) {
    const style = document.createElement('style');
    style.id = 'toast-animations';
    style.textContent = `
        @keyframes slideInRight {
            from { transform: translateX(100%); opacity: 0; }
            to { transform: translateX(0); opacity: 1; }
        }
    `;
    document.head.appendChild(style);
}
</script>
