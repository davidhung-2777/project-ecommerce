<?php
$pageTitle = 'Khách hàng';
$base      = $_ENV['APP_URL'] ?? '';
?>
<div class="space-y-5">
    <div>
        <h1 class="text-xl font-bold">Khách hàng</h1>
        <p class="text-sm text-muted">Tổng <?= number_format($total ?? 0) ?> khách hàng</p>
    </div>
    <div class="bg-white rounded-xl border border-gray-200 overflow-hidden">
        <div class="overflow-x-auto">
            <table class="w-full text-sm">
                <thead class="bg-gray-50 border-b border-gray-100">
                    <tr>
                        <th class="text-left px-5 py-3 text-xs font-medium text-muted">Khách hàng</th>
                        <th class="text-left px-5 py-3 text-xs font-medium text-muted">Loại TK</th>
                        <th class="text-left px-5 py-3 text-xs font-medium text-muted">Số điện thoại</th>
                        <th class="text-center px-5 py-3 text-xs font-medium text-muted">Trạng thái</th>
                        <th class="text-left px-5 py-3 text-xs font-medium text-muted">Ngày đăng ký</th>
                        <th class="px-5 py-3"></th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-100">
                    <?php foreach (($data ?? []) as $user): ?>
                    <tr class="hover:bg-gray-50">
                        <td class="px-5 py-3">
                            <p class="font-medium"><?= htmlspecialchars($user['name'] ?? 'N/A') ?></p>
                            <p class="text-xs text-muted"><?= htmlspecialchars($user['email'] ?? '') ?></p>
                        </td>
                        <td class="px-5 py-3">
                            <span class="px-2 py-0.5 rounded text-xs <?= ($user['account_type'] ?? '') === 'business' ? 'bg-blue-50 text-blue-700' : 'bg-gray-100 text-gray-600' ?>">
                                <?= ($user['account_type'] ?? '') === 'business' ? 'Doanh nghiệp' : 'Cá nhân' ?>
                            </span>
                        </td>
                        <td class="px-5 py-3 text-muted"><?= htmlspecialchars($user['phone'] ?? '-') ?></td>
                        <td class="px-5 py-3 text-center">
                            <span class="px-2 py-0.5 rounded text-xs <?= ($user['is_active'] ?? 0) ? 'bg-green-50 text-green-700' : 'bg-red-50 text-red-700' ?>">
                                <?= ($user['is_active'] ?? 0) ? 'Hoạt động' : 'Khóa' ?>
                            </span>
                        </td>
                        <td class="px-5 py-3 text-muted text-xs"><?= !empty($user['created_at']) ? date('d/m/Y', strtotime($user['created_at'])) : '-' ?></td>
                        <td class="px-5 py-3">
                            <div class="flex items-center gap-2 justify-end">
                                <a href="<?= $base ?>/admin/users/<?= $user['id'] ?? 0 ?>" class="text-xs text-blue-600 hover:underline">Chi tiết</a>
                                <?php if ($user['is_active'] ?? 0): ?>
                                <button onclick="blockUser(<?= $user['id'] ?? 0 ?>)" class="text-xs text-red-500 hover:underline">Khóa</button>
                                <?php else: ?>
                                <button onclick="unblockUser(<?= $user['id'] ?? 0 ?>)" class="text-xs text-green-600 hover:underline">Mở khóa</button>
                                <?php endif; ?>
                            </div>
                        </td>
                    </tr>
                    <?php endforeach; ?>
                    <?php if (empty($data)): ?>
                    <tr><td colspan="6" class="px-5 py-10 text-center text-muted">Chưa có khách hàng nào.</td></tr>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>
    </div>
</div>

<script>
function blockUser(id) {
    if (!confirm('Bạn có chắc muốn khóa tài khoản này?')) return;
    
    fetch(APP_URL + '/admin/users/' + id + '/block', {
        method: 'POST',
        headers: { 'X-Requested-With': 'XMLHttpRequest' }
    })
    .then(r => r.json())
    .then(d => { if (d.success) location.reload(); });
}

function unblockUser(id) {
    if (!confirm('Bạn có chắc muốn mở khóa tài khoản này?')) return;
    
    fetch(APP_URL + '/admin/users/' + id + '/unblock', {
        method: 'POST',
        headers: { 'X-Requested-With': 'XMLHttpRequest' }
    })
    .then(r => r.json())
    .then(d => { if (d.success) location.reload(); });
}
</script>
