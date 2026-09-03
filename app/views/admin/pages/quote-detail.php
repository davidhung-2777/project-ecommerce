<?php
$pageTitle = 'Báo giá ' . $quote['quote_number'];
$base      = $_ENV['APP_URL'] ?? '';
?>
<div class="max-w-4xl space-y-6">
    <div class="flex items-center gap-3">
        <a href="<?= $base ?>/admin/quotes" class="text-muted hover:text-charcoal text-sm">← Báo giá</a>
        <span class="text-muted">/</span>
        <h1 class="text-xl font-bold"><?= htmlspecialchars($quote['quote_number']) ?></h1>
        <span class="px-2 py-1 rounded-full text-xs font-medium bg-yellow-50 text-yellow-700"><?= ucfirst($quote['status']) ?></span>
    </div>

    <div class="grid sm:grid-cols-2 gap-5">
        <!-- Customer Info -->
        <div class="bg-white rounded-xl border border-gray-200 p-5">
            <h2 class="font-semibold text-sm mb-4">Thông tin khách hàng</h2>
            <div class="text-sm space-y-2">
                <p><span class="text-muted">Tên:</span> <strong><?= htmlspecialchars($quote['user']['name'] ?? '') ?></strong></p>
                <p><span class="text-muted">Email:</span> <?= htmlspecialchars($quote['user']['email'] ?? '') ?></p>
                <?php if (!empty($quote['user']['company_name'])): ?>
                <p><span class="text-muted">Công ty:</span> <?= htmlspecialchars($quote['user']['company_name']) ?></p>
                <p><span class="text-muted">MST:</span> <?= htmlspecialchars($quote['user']['tax_code'] ?? '') ?></p>
                <?php endif; ?>
            </div>
        </div>
        <!-- Customer Note -->
        <div class="bg-white rounded-xl border border-gray-200 p-5">
            <h2 class="font-semibold text-sm mb-4">Ghi chú của khách</h2>
            <p class="text-sm text-muted"><?= nl2br(htmlspecialchars($quote['customer_note'] ?: 'Không có ghi chú.')) ?></p>
        </div>
    </div>

    <!-- Items + Admin Response Form -->
    <form onsubmit="respondQuote(event, <?= $quote['id'] ?>)">
        <div class="bg-white rounded-xl border border-gray-200 overflow-hidden">
            <div class="px-5 py-4 border-b border-gray-100">
                <h2 class="font-semibold text-sm">Danh sách sản phẩm & Đặt giá</h2>
                <p class="text-xs text-muted mt-0.5">Nhập đơn giá thương lượng cho từng sản phẩm</p>
            </div>
            <table class="w-full text-sm">
                <thead class="bg-gray-50 text-xs text-muted">
                    <tr>
                        <th class="text-left px-5 py-3">Sản phẩm</th>
                        <th class="text-center px-5 py-3">Số lượng</th>
                        <th class="text-right px-5 py-3">Giá niêm yết</th>
                        <th class="text-right px-5 py-3">Đơn giá báo</th>
                        <th class="text-right px-5 py-3">Thành tiền</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-100">
                    <?php foreach ($quote['items'] as $item): ?>
                    <tr>
                        <td class="px-5 py-3">
                            <div class="flex items-center gap-3">
                                <div class="w-10 h-10 rounded-lg overflow-hidden bg-cream flex-shrink-0">
                                    <img src="<?= htmlspecialchars($item['thumbnail'] ?: $base . '/assets/images/product-placeholder.jpg') ?>" class="w-full h-full object-cover">
                                </div>
                                <div>
                                    <p class="font-medium"><?= htmlspecialchars($item['product_name']) ?></p>
                                    <p class="text-xs text-muted"><?= $item['sku'] ?></p>
                                </div>
                            </div>
                        </td>
                        <td class="px-5 py-3 text-center font-bold"><?= $item['quantity'] ?></td>
                        <td class="px-5 py-3 text-right text-muted"><?= number_format($item['list_price']) ?>đ</td>
                        <td class="px-5 py-3 text-right">
                            <input type="number" name="items[<?= $item['id'] ?>][unit_price]"
                                   value="<?= $item['unit_price'] ?? $item['list_price'] ?>"
                                   step="1000" min="0"
                                   class="w-28 border border-gray-200 rounded px-2 py-1 text-xs text-right focus:outline-none focus:border-wood">
                        </td>
                        <td class="px-5 py-3 text-right font-medium">
                            <?= number_format(($item['unit_price'] ?? $item['list_price']) * $item['quantity']) ?>đ
                        </td>
                    </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>

        <!-- Admin Response -->
        <div class="mt-5 bg-white rounded-xl border border-gray-200 p-5 space-y-4">
            <h2 class="font-semibold text-sm">Phản hồi của admin</h2>
            <textarea name="admin_response" rows="4" placeholder="Nhập phản hồi / ghi chú báo giá cho khách..."
                      class="w-full border border-gray-200 rounded-lg px-4 py-3 text-sm focus:outline-none focus:border-wood resize-none"><?= htmlspecialchars($quote['admin_response'] ?? '') ?></textarea>
            <div class="flex gap-3">
                <button type="submit"
                        class="bg-charcoal text-white px-5 py-2.5 rounded-lg text-sm font-medium hover:bg-wooddk transition">
                    Gửi phản hồi báo giá
                </button>
                <?php if ($quote['status'] !== 'converted'): ?>
                <a href="<?= $base ?>/admin/quotes/<?= $quote['id'] ?>" class="border border-gray-200 px-5 py-2.5 rounded-lg text-sm hover:bg-gray-50 transition">Hủy</a>
                <?php endif; ?>
            </div>
        </div>
    </form>
</div>

<script>
function respondQuote(e, id) {
    e.preventDefault();
    const form  = e.target;
    const body  = new FormData(form);
    fetch(APP_URL + '/admin/quotes/' + id + '/respond', {
        method: 'POST',
        headers: { 'X-Requested-With': 'XMLHttpRequest' },
        body: new URLSearchParams(new FormData(form))
    }).then(r => r.json()).then(d => {
        if (d.success) { alert(d.message); location.reload(); }
        else alert(d.message || 'Lỗi!');
    });
}
</script>
