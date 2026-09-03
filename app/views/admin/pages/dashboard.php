<?php
$pageTitle = 'Dashboard';
$base      = $_ENV['APP_URL'] ?? '';
?>
<div class="space-y-6">

    <!-- Page Header -->
    <div>
        <h1 class="text-xl font-bold text-charcoal">Dashboard</h1>
        <p class="text-sm text-muted">Tổng quan hoạt động hôm nay — <?= date('d/m/Y') ?></p>
    </div>

    <div class="grid grid-cols-3 gap-5">
        <a href="<?= $base ?>/admin/products" class="bg-white rounded-xl border border-gray-200 p-5 hover:border-wood transition">
            <p class="text-xs text-muted">Tổng sản phẩm</p>
            <p class="text-2xl font-bold text-charcoal mt-2"><?= number_format($stats['product']['total']) ?></p>
        </a>
        <a href="<?= $base ?>/admin/products" class="bg-white rounded-xl border border-gray-200 p-5 hover:border-wood transition">
            <p class="text-xs text-muted">Đang hiển thị</p>
            <p class="text-2xl font-bold text-green-700 mt-2"><?= number_format($stats['product']['active']) ?></p>
        </a>
        <a href="<?= $base ?>/admin/products" class="bg-white rounded-xl border border-gray-200 p-5 hover:border-wood transition">
            <p class="text-xs text-muted">Sắp hết hàng</p>
            <p class="text-2xl font-bold text-red-600 mt-2"><?= number_format($stats['product']['low_stock']) ?></p>
        </a>
    </div>

    <!-- Stats Cards -->
    <div class="grid grid-cols-2 lg:grid-cols-4 gap-5">
        <div class="bg-white rounded-xl border border-gray-200 p-5">
            <div class="flex items-center justify-between mb-3">
                <span class="text-2xl">🛒</span>
                <span class="text-xs text-green-600 font-medium bg-green-50 px-2 py-0.5 rounded">Hôm nay</span>
            </div>
            <p class="text-2xl font-bold text-charcoal"><?= $stats['orders_today'] ?></p>
            <p class="text-xs text-muted mt-0.5">Đơn hàng hôm nay</p>
        </div>
        <div class="bg-white rounded-xl border border-gray-200 p-5">
            <div class="flex items-center justify-between mb-3">
                <span class="text-2xl">⏳</span>
                <span class="text-xs text-yellow-600 font-medium bg-yellow-50 px-2 py-0.5 rounded">Chờ xử lý</span>
            </div>
            <p class="text-2xl font-bold text-charcoal"><?= $stats['orders_pending'] ?></p>
            <p class="text-xs text-muted mt-0.5">Đơn chờ xác nhận</p>
        </div>
        <div class="bg-white rounded-xl border border-gray-200 p-5">
            <div class="flex items-center justify-between mb-3">
                <span class="text-2xl">📋</span>
                <span class="text-xs text-blue-600 font-medium bg-blue-50 px-2 py-0.5 rounded">Mới</span>
            </div>
            <p class="text-2xl font-bold text-charcoal"><?= $stats['quotes_pending'] ?></p>
            <p class="text-xs text-muted mt-0.5">Báo giá chờ phản hồi</p>
        </div>
        <div class="bg-white rounded-xl border border-gray-200 p-5">
            <div class="flex items-center justify-between mb-3">
                <span class="text-2xl">👥</span>
            </div>
            <p class="text-2xl font-bold text-charcoal"><?= number_format($stats['total_customers']) ?></p>
            <p class="text-xs text-muted mt-0.5">Tổng khách hàng</p>
        </div>
    </div>

    <!-- Revenue Stats -->
    <div class="grid sm:grid-cols-3 gap-5">
        <?php $labels = ['today' => 'Hôm nay', 'week' => 'Tuần này', 'month' => 'Tháng này']; ?>
        <?php foreach ($stats['revenue'] as $key => $amount): ?>
        <div class="bg-white rounded-xl border border-gray-200 p-5">
            <p class="text-xs font-medium text-muted uppercase tracking-wide mb-2"><?= $labels[$key] ?? $key ?></p>
            <p class="text-xl font-bold text-charcoal"><?= number_format($amount) ?>đ</p>
            <p class="text-xs text-muted mt-1">Doanh thu đã thanh toán</p>
        </div>
        <?php endforeach; ?>
    </div>

    <!-- Recent Orders -->
    <div class="bg-white rounded-xl border border-gray-200 overflow-hidden">
        <div class="px-5 py-4 border-b border-gray-100 flex items-center justify-between">
            <h2 class="font-semibold text-charcoal">Đơn hàng gần đây</h2>
            <a href="<?= $base ?>/admin/orders" class="text-xs text-wood hover:underline">Xem tất cả →</a>
        </div>
        <div class="overflow-x-auto">
            <table class="w-full text-sm">
                <thead class="bg-gray-50 border-b border-gray-100">
                    <tr>
                        <th class="text-left px-5 py-3 text-xs font-medium text-muted">Mã ĐH</th>
                        <th class="text-left px-5 py-3 text-xs font-medium text-muted">Khách hàng</th>
                        <th class="text-left px-5 py-3 text-xs font-medium text-muted">Tổng tiền</th>
                        <th class="text-left px-5 py-3 text-xs font-medium text-muted">Thanh toán</th>
                        <th class="text-left px-5 py-3 text-xs font-medium text-muted">Trạng thái</th>
                        <th class="text-left px-5 py-3 text-xs font-medium text-muted">Ngày tạo</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-100">
                    <?php foreach ($stats['recent_orders'] as $order): ?>
                    <tr class="hover:bg-gray-50">
                        <td class="px-5 py-3 font-medium">
                            <a href="<?= $base ?>/admin/orders/<?= $order['id'] ?>" class="text-wood hover:underline">
                                <?= htmlspecialchars($order['order_number']) ?>
                            </a>
                        </td>
                        <td class="px-5 py-3 text-muted"><?= htmlspecialchars($order['customer_name'] ?? $order['shipping_name']) ?></td>
                        <td class="px-5 py-3 font-medium"><?= number_format($order['total_amount']) ?>đ</td>
                        <td class="px-5 py-3">
                            <?php $pm = ['cod' => 'COD', 'bank_transfer' => 'CK Ngân hàng', 'momo' => 'MoMo', 'vnpay' => 'VNPay']; ?>
                            <span class="text-xs"><?= $pm[$order['payment_method']] ?? $order['payment_method'] ?></span>
                        </td>
                        <td class="px-5 py-3">
                            <?php
                            $statusColors = ['pending' => 'bg-yellow-50 text-yellow-700', 'confirmed' => 'bg-blue-50 text-blue-700', 'processing' => 'bg-purple-50 text-purple-700', 'shipped' => 'bg-indigo-50 text-indigo-700', 'delivered' => 'bg-green-50 text-green-700', 'cancelled' => 'bg-red-50 text-red-700'];
                            $statusLabels = ['pending' => 'Chờ xác nhận', 'confirmed' => 'Đã xác nhận', 'processing' => 'Đang xử lý', 'shipped' => 'Đang giao', 'delivered' => 'Đã giao', 'cancelled' => 'Đã hủy'];
                            ?>
                            <span class="px-2 py-0.5 rounded-full text-xs font-medium <?= $statusColors[$order['status']] ?? 'bg-gray-100 text-gray-600' ?>">
                                <?= $statusLabels[$order['status']] ?? $order['status'] ?>
                            </span>
                        </td>
                        <td class="px-5 py-3 text-muted text-xs"><?= date('d/m/Y H:i', strtotime($order['created_at'])) ?></td>
                    </tr>
                    <?php endforeach; ?>
                    <?php if (empty($stats['recent_orders'])): ?>
                    <tr><td colspan="6" class="px-5 py-10 text-center text-muted">Chưa có đơn hàng nào.</td></tr>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>
    </div>

</div>
