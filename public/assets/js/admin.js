/**
 * DecorNest Admin JavaScript
 * Comprehensive interactive handlers for Products, Categories, Orders, etc.
 */

// ─── Products ───────────────────────────────────────────────────────────────

function deleteProduct(id, button) {
    if (!confirm('Bạn có chắc muốn xóa sản phẩm này?\n\n- Nếu sản phẩm chưa có đơn hàng: Hệ thống sẽ xóa vĩnh viễn.\n- Nếu sản phẩm đã có đơn hàng: Hệ thống sẽ tự động ẩn sản phẩm để bảo vệ lịch sử đơn hàng.')) {
        return;
    }

    const btn = button || document.querySelector(`[data-product-id="${id}"]`);
    const originalContent = btn ? btn.innerHTML : 'Xóa';
    if (btn) {
        btn.disabled = true;
        btn.innerHTML = `<span class="inline-block animate-spin">⌛</span> Đang xử lý...`;
    }

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
            const row = document.getElementById('product-row-' + id) || (btn ? btn.closest('tr') : null);
            if (data.action === 'deleted') {
                if (row) {
                    row.style.transition = 'all 0.4s ease-out';
                    row.style.opacity = '0';
                    row.style.transform = 'translateX(-20px)';
                    setTimeout(() => row.remove(), 400);
                } else {
                    location.reload();
                }
            } else {
                // Soft deleted (hidden)
                if (row) {
                    const statusBtn = row.querySelector('.status-btn');
                    if (statusBtn) {
                        statusBtn.className = 'status-btn inline-flex items-center gap-1.5 px-3 py-1 rounded-full text-xs font-semibold cursor-pointer transition-all border bg-gray-100 text-gray-500 border-gray-200 hover:bg-gray-200';
                        statusBtn.innerHTML = '<span class="w-1.5 h-1.5 rounded-full bg-gray-400"></span><span>Ẩn</span>';
                    }
                } else {
                    location.reload();
                }
            }
            showToast(data.message || 'Thao tác xóa sản phẩm thành công.', 'success');
        } else {
            showToast(data.message || 'Không thể xóa sản phẩm.', 'error');
            if (btn) {
                btn.disabled = false;
                btn.innerHTML = originalContent;
            }
        }
    })
    .catch(err => {
        showToast('Không thể kết nối đến máy chủ. Vui lòng thử lại.', 'error');
        if (btn) {
            btn.disabled = false;
            btn.innerHTML = originalContent;
        }
    });
}

function toggleProductStatus(id, button) {
    const btn = button;
    if (!btn) return;
    
    btn.style.opacity = '0.6';
    btn.style.pointerEvents = 'none';

    fetch(APP_URL + '/admin/products/' + id + '/toggle-status', {
        method: 'POST',
        headers: {
            'X-Requested-With': 'XMLHttpRequest',
            'Content-Type': 'application/x-www-form-urlencoded'
        }
    })
    .then(response => response.json())
    .then(data => {
        btn.style.opacity = '1';
        btn.style.pointerEvents = 'auto';

        if (data.success) {
            if (data.is_active) {
                btn.className = 'status-btn inline-flex items-center gap-1.5 px-3 py-1 rounded-full text-xs font-semibold cursor-pointer transition-all border bg-emerald-50 text-emerald-700 border-emerald-200 hover:bg-emerald-100';
                btn.innerHTML = '<span class="w-1.5 h-1.5 rounded-full bg-emerald-500"></span><span>Hiện</span>';
            } else {
                btn.className = 'status-btn inline-flex items-center gap-1.5 px-3 py-1 rounded-full text-xs font-semibold cursor-pointer transition-all border bg-gray-100 text-gray-500 border-gray-200 hover:bg-gray-200';
                btn.innerHTML = '<span class="w-1.5 h-1.5 rounded-full bg-gray-400"></span><span>Ẩn</span>';
            }
            showToast(data.message, 'success');
        } else {
            showToast(data.message || 'Không thể cập nhật trạng thái.', 'error');
        }
    })
    .catch(() => {
        btn.style.opacity = '1';
        btn.style.pointerEvents = 'auto';
        showToast('Lỗi kết nối máy chủ.', 'error');
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
    if (reason === null) return;
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
    document.querySelectorAll('.admin-toast').forEach(el => el.remove());

    const styles = {
        success: 'bg-emerald-600 text-white shadow-emerald-900/20',
        error:   'bg-red-600 text-white shadow-red-900/20',
        info:    'bg-charcoal text-white shadow-charcoal/20',
    };
    const icons = {
        success: '✓',
        error:   '✕',
        info:    'ℹ'
    };

    const toast = document.createElement('div');
    toast.className = `admin-toast fixed top-5 right-5 z-50 flex items-center gap-3 px-4 py-3 rounded-2xl shadow-xl text-xs font-semibold tracking-wide transition-all duration-300 transform translate-y-[-10px] opacity-0 ${styles[type] || styles.info}`;
    toast.innerHTML = `<span class="w-5 h-5 rounded-full bg-white/20 flex items-center justify-center text-xs font-bold">${icons[type] || icons.info}</span> <span>${message}</span>`;
    document.body.appendChild(toast);

    requestAnimationFrame(() => {
        toast.classList.remove('translate-y-[-10px]', 'opacity-0');
        toast.classList.add('translate-y-0', 'opacity-100');
    });

    setTimeout(() => {
        toast.classList.remove('translate-y-0', 'opacity-100');
        toast.classList.add('translate-y-[-10px]', 'opacity-0');
        setTimeout(() => toast.remove(), 300);
    }, 3500);
}
