/**
 * DecorNest Admin JavaScript
 * Handles CRUD operations for products, categories, orders, etc.
 */

// ─── Products ───────────────────────────────────────────────────────────────

function deleteProduct(id) {
    if (!confirm('Bạn có chắc muốn ẩn sản phẩm này?\n\nSản phẩm sẽ bị ẩn khỏi website nhưng không bị xóa khỏi hệ thống.')) return;

    const btn = event.target;
    btn.disabled = true;
    btn.textContent = '...';

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
            // Remove the row from table gracefully
            const row = btn.closest('tr');
            if (row) {
                row.style.opacity = '0.4';
                row.style.transition = 'opacity 0.3s';
                setTimeout(() => row.remove(), 300);
            } else {
                window.location.reload();
            }
            showToast('Đã ẩn sản phẩm thành công.', 'success');
        } else {
            showToast(data.message || 'Không thể cập nhật sản phẩm.', 'error');
            btn.disabled = false;
            btn.textContent = 'Xóa';
        }
    })
    .catch(() => {
        showToast('Không thể kết nối đến máy chủ.', 'error');
        btn.disabled = false;
        btn.textContent = 'Xóa';
    });
}

// ─── Categories ─────────────────────────────────────────────────────────────

function saveCategory(id) {
    const nameEl = document.getElementById('edit-name-' + id);
    const orderEl = document.getElementById('edit-order-' + id);
    if (!nameEl) return;

    const name = nameEl.value.trim();
    if (!name) { showToast('Tên danh mục không được để trống!', 'error'); return; }

    const sort_order = orderEl ? orderEl.value : 0;

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
            showToast('Đã lưu danh mục thành công!', 'success');
            location.reload();
        } else {
            showToast('Lỗi: ' + (d.message || 'Không thể cập nhật'), 'error');
        }
    })
    .catch(() => showToast('Lỗi kết nối máy chủ.', 'error'));
}

function deleteCategory(id) {
    if (!confirm('Bạn có chắc muốn xóa danh mục này?\n\nDanh mục sẽ bị ẩn khỏi hệ thống.')) return;

    fetch(APP_URL + '/admin/categories/' + id + '/delete', {
        method: 'POST',
        headers: { 'X-Requested-With': 'XMLHttpRequest' }
    })
    .then(r => r.json())
    .then(d => {
        if (d.success) {
            showToast('Đã xóa danh mục.', 'success');
            location.reload();
        } else {
            showToast('Lỗi: ' + (d.message || 'Không thể xóa'), 'error');
        }
    })
    .catch(() => showToast('Lỗi kết nối máy chủ.', 'error'));
}

// ─── Orders ─────────────────────────────────────────────────────────────────

function quickConfirm(id) {
    if (!confirm('Xác nhận đơn hàng này?')) return;
    updateOrderStatus(id, 'confirmed');
}

function quickCancel(id) {
    const reason = prompt('Lý do hủy đơn hàng (không bắt buộc):');
    if (reason === null) return; // User cancelled prompt
    updateOrderStatus(id, 'cancelled');
}

function updateOrderStatus(id, status) {
    fetch(APP_URL + '/admin/orders/' + id + '/status', {
        method: 'POST',
        headers: {
            'Content-Type': 'application/x-www-form-urlencoded',
            'X-Requested-With': 'XMLHttpRequest'
        },
        body: 'status=' + status
    })
    .then(r => r.json())
    .then(d => {
        if (d.success) {
            showToast('Cập nhật trạng thái đơn hàng thành công!', 'success');
            location.reload();
        } else {
            showToast(d.message || 'Không thể cập nhật trạng thái.', 'error');
        }
    })
    .catch(() => showToast('Lỗi kết nối.', 'error'));
}

// ─── Users ──────────────────────────────────────────────────────────────────

function blockUser(id) {
    if (!confirm('Khóa tài khoản người dùng này?')) return;
    fetch(APP_URL + '/admin/users/' + id + '/block', {
        method: 'POST',
        headers: { 'X-Requested-With': 'XMLHttpRequest' }
    })
    .then(r => r.json())
    .then(d => {
        if (d.success) { showToast('Đã khóa tài khoản.', 'success'); location.reload(); }
        else showToast(d.message || 'Lỗi xảy ra.', 'error');
    });
}

function unblockUser(id) {
    if (!confirm('Mở khóa tài khoản người dùng này?')) return;
    fetch(APP_URL + '/admin/users/' + id + '/unblock', {
        method: 'POST',
        headers: { 'X-Requested-With': 'XMLHttpRequest' }
    })
    .then(r => r.json())
    .then(d => {
        if (d.success) { showToast('Đã mở khóa tài khoản.', 'success'); location.reload(); }
        else showToast(d.message || 'Lỗi xảy ra.', 'error');
    });
}

// ─── Payments ───────────────────────────────────────────────────────────────

function confirmPayment(id) {
    if (!confirm('Xác nhận thanh toán này đã được nhận?')) return;
    fetch(APP_URL + '/admin/payments/' + id + '/confirm', {
        method: 'POST',
        headers: { 'X-Requested-With': 'XMLHttpRequest' }
    })
    .then(r => r.json())
    .then(d => {
        if (d.success) { showToast('Đã xác nhận thanh toán.', 'success'); location.reload(); }
        else showToast(d.message || 'Lỗi xảy ra.', 'error');
    });
}

// ─── Toast Notification ─────────────────────────────────────────────────────

function showToast(message, type = 'success') {
    // Remove existing toasts
    document.querySelectorAll('.admin-toast').forEach(el => el.remove());

    const colors = {
        success: 'bg-green-50 border-green-200 text-green-800',
        error: 'bg-red-50 border-red-200 text-red-800',
        info: 'bg-blue-50 border-blue-200 text-blue-800',
    };
    const icons = { success: '✓', error: '✕', info: 'ℹ' };

    const toast = document.createElement('div');
    toast.className = `admin-toast fixed top-4 right-4 z-50 flex items-center gap-3 px-4 py-3 rounded-xl border shadow-lg text-sm font-medium transition-all duration-300 ${colors[type] || colors.info}`;
    toast.innerHTML = `<span class="text-base">${icons[type] || icons.info}</span> <span>${message}</span>`;
    toast.style.opacity = '0';
    toast.style.transform = 'translateX(20px)';
    document.body.appendChild(toast);

    requestAnimationFrame(() => {
        toast.style.opacity = '1';
        toast.style.transform = 'translateX(0)';
    });

    setTimeout(() => {
        toast.style.opacity = '0';
        toast.style.transform = 'translateX(20px)';
        setTimeout(() => toast.remove(), 300);
    }, 3500);
}

// ─── Image preview ──────────────────────────────────────────────────────────

document.addEventListener('DOMContentLoaded', function () {
    // Preview thumbnail before upload
    const thumbInput = document.querySelector('input[name="thumbnail"]');
    if (thumbInput) {
        thumbInput.addEventListener('change', function () {
            const file = this.files[0];
            if (!file) return;
            const reader = new FileReader();
            reader.onload = function (e) {
                let preview = document.getElementById('thumb-preview');
                if (!preview) {
                    preview = document.createElement('img');
                    preview.id = 'thumb-preview';
                    preview.className = 'w-full rounded-lg object-cover mt-2';
                    preview.style.maxHeight = '200px';
                    thumbInput.parentNode.insertBefore(preview, thumbInput.nextSibling);
                }
                preview.src = e.target.result;
            };
            reader.readAsDataURL(file);
        });
    }
});
