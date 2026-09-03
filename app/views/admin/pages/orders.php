<?php
$pageTitle = 'Quản lý đơn hàng';
$base = $_ENV['APP_URL'] ?? '';
$statusLabels = ['pending' => 'Chờ xác nhận', 'confirmed' => 'Đã xác nhận', 'processing' => 'Đang xử lý', 'shipped' => 'Đang giao', 'delivered' => 'Đã giao', 'cancelled' => 'Đã hủy'];
$statusColors = ['pending' => 'bg-yellow-50 text-yellow-700', 'confirmed' => 'bg-blue-50 text-blue-700', 'processing' => 'bg-purple-50 text-purple-700', 'shipped' => 'bg-indigo-50 text-indigo-700', 'delivered' => 'bg-green-50 text-green-700', 'cancelled' => 'bg-red-50 text-red-700'];
?>
<div class="space-y-5">
    <div class="flex items-center justify-between">
        <div>
            <h1 class="text-xl font-bold">Đơn hàng</h1>
            <p class="text-sm text-muted">Tổng <?= number_format($total ?? 0) ?> đơn hàng</p>
        </div>
    </div>

    <!-- Filters -->
    <form class="flex flex-wrap gap-3">
        <input type="text" name="search" value="<?= htmlspecialchars($filters['search'] ?? '') ?>"
               placeholder="Tìm mã ĐH, tên, SĐT..."
               class="border border-gray-200 rounded-lg px-3 py-2 text-sm focus:outline-none focus:border-wood">
        <select name="status" class="border border-gray-200 rounded-lg px-3 py-2 text-sm focus:outline-none focus:border-wood">
            <option value="">Tất cả trạng thái</option>
            <?php foreach ($statusLabels as $v => $l): ?>
            <option value="<?= $v ?>" <?= ($filters['status'] ?? '') === $v ? 'selected' : '' ?>><?= $l ?></option>
            <?php endforeach; ?>
        </select>
        <select name="payment_method" class="border border-gray-200 rounded-lg px-3 py-2 text-sm focus:outline-none focus:border-wood">
            <option value="">Tất cả PTTT</option>
            <option value="cod" <?= ($filters['payment_method'] ?? '') === 'cod' ? 'selected' : '' ?>>COD</option>
            <option value="bank_transfer" <?= ($filters['payment_method'] ?? '') === 'bank_transfer' ? 'selected' : '' ?>>Chuyển khoản</option>
            <option value="momo" <?= ($filters['payment_method'] ?? '') === 'momo' ? 'selected' : '' ?>>MoMo</option>
            <option value="vnpay" <?= ($filters['payment_method'] ?? '') === 'vnpay' ? 'selected' : '' ?>>VNPay</option>
        </select>
        <button type="submit" class="bg-charcoal text-white px-4 py-2 rounded-lg text-sm hover:bg-wooddk transition">Lọc</button>
    </form>

    <div class="bg-white rounded-xl border border-gray-200 overflow-hidden">
        <div class="overflow-x-auto">
            <table class="w-full text-sm">
                <thead class="bg-gray-50 border-b border-gray-100">
                    <tr>
                        <th class="text-left px-5 py-3 text-xs font-medium text-muted">Mã ĐH</th>
                        <th class="text-left px-5 py-3 text-xs font-medium text-muted">Khách hàng</th>
                        <th class="text-right px-5 py-3 text-xs font-medium text-muted">Tổng tiền</th>
                        <th class="text-center px-5 py-3 text-xs font-medium text-muted">PTTT</th>
                        <th class="text-center px-5 py-3 text-xs font-medium text-muted">Thanh toán</th>
                        <th class="text-center px-5 py-3 text-xs font-medium text-muted">Trạng thái</th>
                        <th class="text-left px-5 py-3 text-xs font-medium text-muted">Ngày</th>
                        <th class="px-5 py-3"></th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-100">
                    <?php foreach (($data ?? []) as $order): ?>
                    <tr class="hover:bg-gray-50">
                        <td class="px-5 py-3 font-medium">
                            <a href="<?= $base ?>/admin/orders/<?= $order['id'] ?>" class="text-wood hover:underline">
                                <?= htmlspecialchars($order['order_number']) ?>
                            </a>
                        </td>
                        <td class="px-5 py-3">
                            <p class="font-medium"><?= htmlspecialchars($order['customer_name'] ?? $order['shipping_name']) ?></p>
                            <p class="text-xs text-muted"><?= htmlspecialchars($order['shipping_phone']) ?></p>
                        </td>
                        <td class="px-5 py-3 text-right font-medium"><?= number_format($order['total_amount']) ?>đ</td>
                        <td class="px-5 py-3 text-center text-xs text-muted">
                            <?php $pm = ['cod' => 'COD', 'bank_transfer' => 'CK', 'momo' => 'MoMo', 'vnpay' => 'VNPay']; ?>
                            <?= $pm[$order['payment_method']] ?? '-' ?>
                        </td>
                        <td class="px-5 py-3 text-center">
                            <span class="px-2 py-0.5 rounded text-xs <?= $order['payment_status'] === 'paid' ? 'bg-green-50 text-green-700' : 'bg-gray-100 text-gray-500' ?>">
                                <?= $order['payment_status'] === 'paid' ? 'Đã TT' : 'Chưa TT' ?>
                            </span>
                        </td>
                        <td class="px-5 py-3 text-center">
                            <span class="px-2 py-0.5 rounded-full text-xs font-medium <?= $statusColors[$order['status']] ?? 'bg-gray-100' ?>">
                                <?= $statusLabels[$order['status']] ?? $order['status'] ?>
                            </span>
                        </td>
                        <td class="px-5 py-3 text-muted text-xs"><?= date('d/m H:i', strtotime($order['created_at'])) ?></td>
                        <td class="px-5 py-3">
                            <div class="flex items-center gap-2 justify-end">
                                <a href="<?= $base ?>/admin/orders/<?= $order['id'] ?>" class="text-xs text-blue-600 hover:underline">Chi tiết</a>
                                <?php if ($order['status'] === 'pending'): ?>
                                <button onclick="quickConfirm(<?= $order['id'] ?>)" class="text-xs text-green-600 hover:underline">Xác nhận</button>
                                <button onclick="quickCancel(<?= $order['id'] ?>)" class="text-xs text-red-500 hover:underline">Hủy</button>
                                <?php endif; ?>
                            </div>
                        </td>
                    </tr>
                    <?php endforeach; ?>
                    <?php if (empty($data)): ?>
                    <tr><td colspan="8" class="px-5 py-10 text-center text-muted">Không có đơn hàng nào.</td></tr>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>
    </div>
</div>

<script>
function quickConfirm(id) {
    if (!confirm('Xác nhận đơn hàng này?')) return;
    
    fetch(APP_URL + '/admin/orders/' + id + '/status', {
        method: 'POST',
        headers: {
            'Content-Type': 'application/x-www-form-urlencoded',
            'X-Requested-With': 'XMLHttpRequest'
        },
        body: 'status=confirmed'
    })
    .then(r => r.json())
    .then(d => { if (d.success) location.reload(); });
}

function quickCancel(id) {
    if (!confirm('Hủy đơn hàng này?')) return;
    
    fetch(APP_URL + '/admin/orders/' + id + '/status', {
        method: 'POST',
        headers: {
            'Content-Type': 'application/x-www-form-urlencoded',
            'X-Requested-With': 'XMLHttpRequest'
        },
        body: 'status=cancelled'
    })
    .then(r => r.json())
    .then(d => { if (d.success) location.reload(); });
}
</script>
