<?php
$pageTitle = 'Chi tiết khách hàng';
$base = $_ENV['APP_URL'] ?? '';
$statusLabels = [
    'pending' => 'Chờ xác nhận', 'confirmed' => 'Đã xác nhận', 'processing' => 'Đang xử lý',
    'shipped' => 'Đang giao', 'delivered' => 'Đã giao', 'cancelled' => 'Đã hủy', 'refunded' => 'Đã hoàn tiền',
];
$statusColors = [
    'pending' => 'bg-yellow-50 text-yellow-700', 'confirmed' => 'bg-blue-50 text-blue-700',
    'processing' => 'bg-indigo-50 text-indigo-700', 'shipped' => 'bg-purple-50 text-purple-700',
    'delivered' => 'bg-green-50 text-green-700', 'cancelled' => 'bg-red-50 text-red-700',
    'refunded' => 'bg-gray-100 text-gray-700',
];
?>
<div class="max-w-6xl space-y-6">
    <div class="flex items-center gap-3">
        <a href="<?= $base ?>/admin/users" class="text-muted hover:text-charcoal text-sm">← Khách hàng</a>
        <span class="text-muted">/</span>
        <h1 class="text-xl font-bold">Chi tiết khách hàng</h1>
    </div>

    <div class="grid lg:grid-cols-3 gap-5">
        <section class="lg:col-span-1 bg-white rounded-xl border border-gray-200 p-5">
            <div class="flex items-center gap-3 mb-5">
                <span class="w-12 h-12 rounded-full bg-cream text-wooddk flex items-center justify-center text-lg font-bold">
                    <?= strtoupper(substr($user['name'] ?? 'K', 0, 1)) ?>
                </span>
                <div>
                    <h2 class="font-semibold"><?= htmlspecialchars($user['name'] ?? '') ?></h2>
                    <p class="text-xs text-muted"><?= htmlspecialchars($user['email'] ?? '') ?></p>
                </div>
            </div>
            <dl class="space-y-3 text-sm">
                <div class="flex justify-between gap-3"><dt class="text-muted">Số điện thoại</dt><dd><?= htmlspecialchars($user['phone'] ?? 'Chưa cập nhật') ?></dd></div>
                <div class="flex justify-between gap-3"><dt class="text-muted">Loại tài khoản</dt><dd><?= ($user['account_type'] ?? 'individual') === 'business' ? 'Doanh nghiệp' : 'Cá nhân' ?></dd></div>
                <div class="flex justify-between gap-3"><dt class="text-muted">Trạng thái</dt><dd class="<?= !empty($user['is_active']) ? 'text-green-700' : 'text-red-700' ?>"><?= !empty($user['is_active']) ? 'Hoạt động' : 'Đã khóa' ?></dd></div>
                <div class="flex justify-between gap-3"><dt class="text-muted">Ngày đăng ký</dt><dd><?= !empty($user['created_at']) ? date('d/m/Y H:i', strtotime($user['created_at'])) : '-' ?></dd></div>
            </dl>
        </section>

        <section class="lg:col-span-2 grid sm:grid-cols-3 gap-4">
            <div class="bg-white rounded-xl border border-gray-200 p-5"><p class="text-xs text-muted">Tổng đơn hàng</p><p class="text-2xl font-bold mt-2"><?= $stats['total_orders'] ?></p><p class="text-xs text-muted mt-1"><?= $stats['total_orders'] > 0 ? 'Đã từng mua hàng' : 'Chưa từng mua hàng' ?></p></div>
            <div class="bg-white rounded-xl border border-gray-200 p-5"><p class="text-xs text-muted">Sản phẩm đã mua</p><p class="text-2xl font-bold mt-2"><?= number_format($stats['total_products']) ?></p><p class="text-xs text-muted mt-1">Không tính đơn đã hủy</p></div>
            <div class="bg-white rounded-xl border border-gray-200 p-5"><p class="text-xs text-muted">Đã chi tiêu</p><p class="text-2xl font-bold mt-2"><?= number_format($stats['total_spent']) ?>đ</p><p class="text-xs text-muted mt-1">Đơn đã xác nhận trở lên</p></div>
        </section>
    </div>

    <section class="bg-white rounded-xl border border-gray-200 overflow-hidden">
        <div class="px-5 py-4 border-b border-gray-100 flex items-center justify-between">
            <h2 class="font-semibold">Lịch sử đơn hàng</h2>
            <span class="text-xs text-muted"><?= $orders['total'] ?> đơn hàng</span>
        </div>
        <?php if (empty($orders['data'])): ?>
            <p class="px-5 py-12 text-center text-sm text-muted">Khách hàng chưa có đơn hàng.</p>
        <?php else: ?>
        <div class="overflow-x-auto">
            <table class="w-full text-sm">
                <thead class="bg-gray-50 text-xs text-muted"><tr><th class="text-left px-5 py-3">Mã đơn</th><th class="text-left px-5 py-3">Ngày đặt</th><th class="text-center px-5 py-3">Số lượng</th><th class="text-right px-5 py-3">Tổng tiền</th><th class="text-left px-5 py-3">Trạng thái</th></tr></thead>
                <tbody class="divide-y divide-gray-100">
                <?php foreach ($orders['data'] as $order): ?>
                    <tr onclick="window.location.href='<?= $base ?>/admin/orders/<?= (int) $order['id'] ?>'" class="cursor-pointer hover:bg-gray-50">
                        <td class="px-5 py-3 font-medium">#<?= htmlspecialchars($order['order_number']) ?></td>
                        <td class="px-5 py-3 text-muted"><?= date('d/m/Y H:i', strtotime($order['created_at'])) ?></td>
                        <td class="px-5 py-3 text-center"><?= (int) $order['item_count'] ?></td>
                        <td class="px-5 py-3 text-right font-medium"><?= number_format($order['total_amount']) ?>đ</td>
                        <td class="px-5 py-3"><span class="px-2 py-1 rounded text-xs <?= $statusColors[$order['status']] ?? 'bg-gray-100 text-gray-700' ?>"><?= $statusLabels[$order['status']] ?? htmlspecialchars($order['status']) ?></span></td>
                    </tr>
                <?php endforeach; ?>
                </tbody>
            </table>
        </div>
        <?php endif; ?>
    </section>
</div>