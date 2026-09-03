<?php
$pageTitle = 'Quản lý thanh toán';
$base      = $_ENV['APP_URL'] ?? '';
?>
<div class="space-y-5">
    <h1 class="text-xl font-bold">Đối soát thanh toán</h1>
    <p class="text-sm text-muted">Xác nhận các giao dịch chuyển khoản ngân hàng thủ công.</p>

    <div class="bg-white rounded-xl border border-gray-200 overflow-hidden">
        <div class="px-5 py-4 border-b border-gray-100 flex items-center justify-between">
            <h2 class="font-semibold text-sm">Chuyển khoản chờ xác nhận (<?= count($pendingTransfers) ?>)</h2>
        </div>
        <?php if (empty($pendingTransfers)): ?>
        <div class="py-12 text-center text-muted text-sm">Không có giao dịch nào chờ xác nhận.</div>
        <?php else: ?>
        <table class="w-full text-sm">
            <thead class="bg-gray-50 text-xs text-muted">
                <tr>
                    <th class="text-left px-5 py-3">Mã ĐH</th>
                    <th class="text-left px-5 py-3">Khách hàng</th>
                    <th class="text-right px-5 py-3">Số tiền</th>
                    <th class="text-left px-5 py-3">Ngày tạo</th>
                    <th class="px-5 py-3"></th>
                </tr>
            </thead>
            <tbody class="divide-y divide-gray-100">
                <?php foreach ($pendingTransfers as $p): ?>
                <tr class="hover:bg-gray-50">
                    <td class="px-5 py-3 font-medium">
                        <a href="<?= $base ?>/admin/orders/<?= $p['order_id'] ?>" class="text-wood hover:underline">
                            <?= htmlspecialchars($p['order_number']) ?>
                        </a>
                    </td>
                    <td class="px-5 py-3"><?= htmlspecialchars($p['shipping_name']) ?></td>
                    <td class="px-5 py-3 text-right font-bold text-charcoal"><?= number_format($p['total_amount']) ?>đ</td>
                    <td class="px-5 py-3 text-muted text-xs"><?= date('d/m/Y H:i', strtotime($p['created_at'])) ?></td>
                    <td class="px-5 py-3">
                        <button onclick="confirmPayment(<?= $p['id'] ?>)"
                                class="bg-green-600 text-white px-3 py-1.5 rounded-lg text-xs font-medium hover:bg-green-700 transition">
                            ✓ Xác nhận đã CK
                        </button>
                    </td>
                </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
        <?php endif; ?>
    </div>
</div>

<script>
function confirmPayment(id) {
    if (!confirm('Xác nhận khách hàng đã chuyển khoản thành công?')) return;
    fetch(APP_URL + '/admin/payments/' + id + '/confirm', {
        method: 'POST',
        headers: { 'X-Requested-With': 'XMLHttpRequest', 'Content-Type': 'application/x-www-form-urlencoded' }
    }).then(r => r.json()).then(d => {
        if (d.success) { alert(d.message); location.reload(); }
        else alert(d.message);
    });
}
</script>
