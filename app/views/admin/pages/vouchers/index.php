<?php
$pageTitle = 'Quản lý voucher';
$base = $_ENV['APP_URL'] ?? '';
$statusLabels = ['active' => 'Đang hoạt động', 'inactive' => 'Đã tắt'];
?>
<div class="max-w-6xl space-y-5">
    <div class="flex items-center justify-between">
        <div><h1 class="text-xl font-bold">Voucher</h1><p class="text-sm text-muted mt-1">Quản lý mã giảm giá và giới hạn sử dụng</p></div>
        <a href="<?= $base ?>/admin/vouchers/create" class="bg-charcoal text-white px-4 py-2 rounded-lg text-sm">+ Tạo voucher</a>
    </div>
    <form class="bg-white border border-gray-200 rounded-xl p-4 flex flex-wrap gap-3">
        <input name="search" value="<?= htmlspecialchars($search ?? '') ?>" placeholder="Tìm theo mã" class="border border-gray-200 rounded-lg px-3 py-2 text-sm flex-1 min-w-48">
        <select name="status" class="border border-gray-200 rounded-lg px-3 py-2 text-sm"><option value="">Tất cả trạng thái</option><option value="active" <?= ($status ?? '') === 'active' ? 'selected' : '' ?>>Đang hoạt động</option><option value="expired" <?= ($status ?? '') === 'expired' ? 'selected' : '' ?>>Đã hết hạn</option><option value="inactive" <?= ($status ?? '') === 'inactive' ? 'selected' : '' ?>>Đã tắt</option></select>
        <button class="bg-cream text-charcoal px-4 py-2 rounded-lg text-sm">Lọc</button>
    </form>
    <div class="bg-white border border-gray-200 rounded-xl overflow-hidden">
        <div class="overflow-x-auto"><table class="w-full text-sm"><thead class="bg-gray-50 text-xs text-muted"><tr><th class="text-left px-5 py-3">Mã</th><th class="text-left px-5 py-3">Giảm</th><th class="text-left px-5 py-3">Hiệu lực</th><th class="text-center px-5 py-3">Đã dùng</th><th class="text-left px-5 py-3">Trạng thái</th><th class="px-5 py-3"></th></tr></thead>
        <tbody class="divide-y divide-gray-100">
        <?php foreach (($data ?? []) as $voucher): ?>
            <tr><td class="px-5 py-3"><strong><?= htmlspecialchars($voucher['code']) ?></strong><p class="text-xs text-muted"><?= htmlspecialchars($voucher['description'] ?? '') ?></p></td><td class="px-5 py-3"><?= $voucher['discount_type'] === 'percent' ? rtrim(rtrim(number_format($voucher['discount_value'], 2), '0'), '.') . '%' : number_format($voucher['discount_value']) . 'đ' ?></td><td class="px-5 py-3 text-xs"><?= date('d/m/Y H:i', strtotime($voucher['start_date'])) ?><br>đến <?= date('d/m/Y H:i', strtotime($voucher['end_date'])) ?></td><td class="px-5 py-3 text-center"><?= (int) $voucher['used_count'] ?><?= $voucher['usage_limit'] !== null ? ' / ' . (int) $voucher['usage_limit'] : '' ?></td><td class="px-5 py-3"><span class="px-2 py-1 rounded text-xs <?= $voucher['is_active'] ? 'bg-green-50 text-green-700' : 'bg-gray-100 text-gray-600' ?>"><?= $voucher['is_active'] ? 'Đang hoạt động' : 'Đã tắt' ?></span></td><td class="px-5 py-3 text-right whitespace-nowrap"><a href="<?= $base ?>/admin/vouchers/<?= (int) $voucher['id'] ?>/edit" class="text-blue-600 hover:underline mr-3">Sửa</a><?php if ($voucher['is_active']): ?><form class="inline" method="post" action="<?= $base ?>/admin/vouchers/<?= (int) $voucher['id'] ?>/delete"><button class="text-red-600 hover:underline" onclick="return confirm('Tắt voucher này?')">Tắt</button></form><?php endif; ?></td></tr>
        <?php endforeach; ?>
        </tbody></table></div>
        <?php if (empty($data)): ?><p class="text-center text-sm text-muted py-10">Chưa có voucher.</p><?php endif; ?>
    </div>
</div>
