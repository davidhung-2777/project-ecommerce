<?php
$pageTitle = 'Quản lý báo giá';
$base      = $_ENV['APP_URL'] ?? '';
$statusLabels = ['pending' => 'Chờ xử lý', 'reviewing' => 'Đang xem', 'responded' => 'Đã phản hồi', 'accepted' => 'Đã chấp nhận', 'rejected' => 'Đã từ chối', 'converted' => 'Đã chuyển ĐH'];
$statusColors = ['pending' => 'bg-yellow-50 text-yellow-700', 'reviewing' => 'bg-blue-50 text-blue-700', 'responded' => 'bg-purple-50 text-purple-700', 'accepted' => 'bg-green-50 text-green-700', 'rejected' => 'bg-red-50 text-red-700', 'converted' => 'bg-gray-100 text-gray-600'];
?>
<div class="space-y-5">
    <div class="flex items-center justify-between">
        <div>
            <h1 class="text-xl font-bold">Báo giá B2B</h1>
            <p class="text-sm text-muted">Tổng <?= number_format($total ?? 0) ?> yêu cầu</p>
        </div>
    </div>

    <!-- Status filter tabs -->
    <div class="flex gap-2 flex-wrap">
        <a href="?" class="px-3 py-1.5 rounded-full text-xs font-medium <?= !$status ? 'bg-charcoal text-white' : 'border border-gray-200 text-muted hover:bg-gray-50' ?>">Tất cả</a>
        <?php foreach ($statusLabels as $v => $l): ?>
        <a href="?status=<?= $v ?>" class="px-3 py-1.5 rounded-full text-xs font-medium <?= $status === $v ? 'bg-charcoal text-white' : 'border border-gray-200 text-muted hover:bg-gray-50' ?>"><?= $l ?></a>
        <?php endforeach; ?>
    </div>

    <div class="bg-white rounded-xl border border-gray-200 overflow-hidden">
        <div class="overflow-x-auto">
            <table class="w-full text-sm">
                <thead class="bg-gray-50 border-b border-gray-100">
                    <tr>
                        <th class="text-left px-5 py-3 text-xs font-medium text-muted">Mã báo giá</th>
                        <th class="text-left px-5 py-3 text-xs font-medium text-muted">Khách hàng</th>
                        <th class="text-left px-5 py-3 text-xs font-medium text-muted">Công ty</th>
                        <th class="text-right px-5 py-3 text-xs font-medium text-muted">Tổng tiền</th>
                        <th class="text-center px-5 py-3 text-xs font-medium text-muted">Trạng thái</th>
                        <th class="text-left px-5 py-3 text-xs font-medium text-muted">Ngày gửi</th>
                        <th class="px-5 py-3"></th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-100">
                    <?php foreach (($data ?? []) as $quote): ?>
                    <tr class="hover:bg-gray-50">
                        <td class="px-5 py-3 font-medium">
                            <a href="<?= $base ?>/admin/quotes/<?= $quote['id'] ?? 0 ?>" class="text-wood hover:underline">
                                <?= htmlspecialchars($quote['quote_number'] ?? 'N/A') ?>
                            </a>
                        </td>
                        <td class="px-5 py-3">
                            <p class="font-medium"><?= htmlspecialchars($quote['customer_name'] ?? 'N/A') ?></p>
                            <p class="text-xs text-muted"><?= htmlspecialchars($quote['customer_email'] ?? '') ?></p>
                        </td>
                        <td class="px-5 py-3 text-muted"><?= htmlspecialchars($quote['company_name'] ?? '-') ?></td>
                        <td class="px-5 py-3 text-right font-medium"><?= !empty($quote['total_amount']) ? number_format($quote['total_amount']) . 'đ' : '-' ?></td>
                        <td class="px-5 py-3 text-center">
                            <span class="px-2 py-0.5 rounded-full text-xs font-medium <?= $statusColors[$quote['status'] ?? ''] ?? 'bg-gray-100' ?>">
                                <?= $statusLabels[$quote['status'] ?? ''] ?? ($quote['status'] ?? 'N/A') ?>
                            </span>
                        </td>
                        <td class="px-5 py-3 text-muted text-xs"><?= !empty($quote['created_at']) ? date('d/m/Y', strtotime($quote['created_at'])) : '-' ?></td>
                        <td class="px-5 py-3">
                            <div class="flex items-center gap-2 justify-end">
                                <a href="<?= $base ?>/admin/quotes/<?= $quote['id'] ?? 0 ?>" class="text-xs text-blue-600 hover:underline">Chi tiết</a>
                                <?php if (($quote['status'] ?? '') === 'pending'): ?>
                                <button onclick="quickReject(<?= $quote['id'] ?? 0 ?>)" class="text-xs text-red-500 hover:underline">Từ chối</button>
                                <?php endif; ?>
                            </div>
                        </td>
                    </tr>
                    <?php endforeach; ?>
                    <?php if (empty($data)): ?>
                    <tr><td colspan="7" class="px-5 py-10 text-center text-muted">Không có yêu cầu báo giá nào.</td></tr>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>
    </div>
</div>

<script>
function quickReject(id) {
    const reason = prompt('Lý do từ chối:');
    if (!reason) return;
    
    fetch(APP_URL + '/admin/quotes/' + id + '/reject', {
        method: 'POST',
        headers: {
            'Content-Type': 'application/x-www-form-urlencoded',
            'X-Requested-With': 'XMLHttpRequest'
        },
        body: 'reason=' + encodeURIComponent(reason)
    })
    .then(r => r.json())
    .then(d => { if (d.success) location.reload(); });
}
</script>
