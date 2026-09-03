<?php
$pageTitle = 'Danh mục';
$base      = $_ENV['APP_URL'] ?? '';
?>
<div class="space-y-5" x-data="{ showForm: false }">
    <div class="flex items-center justify-between">
        <h1 class="text-xl font-bold">Danh mục sản phẩm</h1>
        <button @click="showForm = !showForm" class="bg-charcoal text-white px-4 py-2 rounded-lg text-sm font-medium hover:bg-wooddk transition flex items-center gap-2">
            + Thêm danh mục
        </button>
    </div>

    <!-- Add Form -->
    <div x-show="showForm" class="bg-white rounded-xl border border-gray-200 p-5">
        <form action="<?= $base ?>/admin/categories/create" method="POST" class="grid sm:grid-cols-4 gap-4 items-end">
            <div>
                <label class="block text-xs font-medium text-muted mb-1">Tên danh mục *</label>
                <input type="text" name="name" required class="w-full border border-gray-200 rounded-lg px-3 py-2 text-sm focus:outline-none focus:border-wood">
            </div>
            <div>
                <label class="block text-xs font-medium text-muted mb-1">Danh mục cha</label>
                <select name="parent_id" class="w-full border border-gray-200 rounded-lg px-3 py-2 text-sm focus:outline-none focus:border-wood">
                    <option value="">-- Không có --</option>
                    <?php foreach (array_filter($categories, fn($c) => !$c['parent_id']) as $cat): ?>
                    <option value="<?= $cat['id'] ?>"><?= htmlspecialchars($cat['name']) ?></option>
                    <?php endforeach; ?>
                </select>
            </div>
            <div>
                <label class="block text-xs font-medium text-muted mb-1">Thứ tự</label>
                <input type="number" name="sort_order" value="0" class="w-full border border-gray-200 rounded-lg px-3 py-2 text-sm focus:outline-none focus:border-wood">
            </div>
            <button type="submit" class="bg-charcoal text-white px-4 py-2 rounded-lg text-sm hover:bg-wooddk transition">Tạo</button>
        </form>
    </div>

    <!-- Table -->
    <div class="bg-white rounded-xl border border-gray-200 overflow-hidden">
        <table class="w-full text-sm">
            <thead class="bg-gray-50 border-b border-gray-100">
                <tr>
                    <th class="text-left px-5 py-3 text-xs font-medium text-muted">Tên danh mục</th>
                    <th class="text-left px-5 py-3 text-xs font-medium text-muted">Slug</th>
                    <th class="text-center px-5 py-3 text-xs font-medium text-muted">Thứ tự</th>
                    <th class="text-center px-5 py-3 text-xs font-medium text-muted">Trạng thái</th>
                    <th class="px-5 py-3"></th>
                </tr>
            </thead>
            <tbody class="divide-y divide-gray-100">
                <?php foreach ($categories as $cat): ?>
                <tr class="hover:bg-gray-50" x-data="{ editMode: false }">
                    <td class="px-5 py-3">
                        <div x-show="!editMode">
                            <span class="<?= $cat['parent_id'] ? 'pl-4 text-muted' : 'font-medium' ?>">
                                <?= $cat['parent_id'] ? '└ ' : '' ?><?= htmlspecialchars($cat['name']) ?>
                            </span>
                        </div>
                        <div x-show="editMode" x-cloak>
                            <input type="text" id="edit-name-<?= $cat['id'] ?>" value="<?= htmlspecialchars($cat['name']) ?>"
                                   class="border border-gray-300 rounded px-2 py-1 text-sm w-full">
                        </div>
                    </td>
                    <td class="px-5 py-3 text-muted text-xs font-mono"><?= htmlspecialchars($cat['slug']) ?></td>
                    <td class="px-5 py-3 text-center">
                        <div x-show="!editMode" class="text-muted"><?= $cat['sort_order'] ?></div>
                        <div x-show="editMode" x-cloak>
                            <input type="number" id="edit-order-<?= $cat['id'] ?>" value="<?= $cat['sort_order'] ?>"
                                   class="border border-gray-300 rounded px-2 py-1 text-sm w-20 text-center">
                        </div>
                    </td>
                    <td class="px-5 py-3 text-center">
                        <span class="px-2 py-0.5 rounded text-xs <?= $cat['is_active'] ? 'bg-green-50 text-green-700' : 'bg-gray-100 text-gray-500' ?>">
                            <?= $cat['is_active'] ? 'Hiện' : 'Ẩn' ?>
                        </span>
                    </td>
                    <td class="px-5 py-3">
                        <div x-show="!editMode" class="flex items-center gap-2 justify-end">
                            <button @click="editMode = true" class="text-xs text-blue-600 hover:underline">Sửa</button>
                            <button onclick="deleteCategory(<?= $cat['id'] ?>)" class="text-xs text-red-500 hover:underline">Xóa</button>
                        </div>
                        <div x-show="editMode" x-cloak class="flex items-center gap-2 justify-end">
                            <button @click="saveCategory(<?= $cat['id'] ?>); editMode = false" class="text-xs text-green-600 hover:underline">Lưu</button>
                            <button @click="editMode = false" class="text-xs text-gray-500 hover:underline">Hủy</button>
                        </div>
                    </td>
                </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    </div>
</div>

<script>
function saveCategory(id) {
    const name = document.getElementById('edit-name-' + id).value;
    const sort_order = document.getElementById('edit-order-' + id).value;
    
    fetch(APP_URL + '/admin/categories/' + id + '/edit', {
        method: 'POST',
        headers: {
            'Content-Type': 'application/x-www-form-urlencoded',
            'X-Requested-With': 'XMLHttpRequest'
        },
        body: 'name=' + encodeURIComponent(name) + '&sort_order=' + sort_order + '&is_active=1'
    })
    .then(r => r.json())
    .then(d => {
        if (d.success) {
            location.reload();
        } else {
            alert('Lỗi: ' + (d.message || 'Không thể cập nhật'));
        }
    })
    .catch(e => alert('Lỗi kết nối'));
}

function deleteCategory(id) {
    if (!confirm('Bạn có chắc muốn xóa danh mục này?')) return;
    
    fetch(APP_URL + '/admin/categories/' + id + '/delete', {
        method: 'POST',
        headers: {
            'X-Requested-With': 'XMLHttpRequest'
        }
    })
    .then(r => r.json())
    .then(d => {
        if (d.success) {
            location.reload();
        } else {
            alert('Lỗi: ' + (d.message || 'Không thể xóa'));
        }
    });
}
</script>
