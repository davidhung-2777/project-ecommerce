/**
 * DecorNest E-Commerce Frontend Scripts
 * Warm Japandi & Scandinavian Healing Experience
 */

document.addEventListener('DOMContentLoaded', () => {
    // Initial fetch for cart badge count
    refreshCartCount();
});

/**
 * Fetch and update the cart badge counter
 */
async function refreshCartCount() {
    try {
        const response = await fetch(`${APP_URL}/cart/count`);
        if (response.ok) {
            const data = await response.json();
            updateCartBadge(data.count || 0);
        }
    } catch (e) {
        console.debug('Failed to fetch cart count', e);
    }
}

/**
 * Update UI cart badge numbers
 */
function updateCartBadge(count) {
    const badge = document.getElementById('cart-badge');
    const countEl = document.getElementById('cart-count');
    const miniCountEl = document.getElementById('mini-cart-count');

    if (countEl) countEl.textContent = count;
    if (miniCountEl) miniCountEl.textContent = count;

    if (badge) {
        if (count > 0) {
            badge.classList.remove('hidden');
            badge.classList.add('flex');
            // Little bounce animation
            badge.classList.add('scale-125');
            setTimeout(() => badge.classList.remove('scale-125'), 200);
        } else {
            badge.classList.add('hidden');
            badge.classList.remove('flex');
        }
    }
}

/**
 * Add product to cart via AJAX
 */
async function addToCart(productId, quantity = 1, options = {}) {
    try {
        const formData = new FormData();
        formData.append('product_id', productId);
        formData.append('quantity', quantity);
        
        if (options.size) formData.append('size_option', options.size);
        if (options.color) formData.append('color_option', options.color);

        const response = await fetch(`${APP_URL}/cart/add`, {
            method: 'POST',
            body: formData,
            headers: {
                'X-Requested-With': 'XMLHttpRequest'
            }
        });

        const data = await response.json();

        if (data.success) {
            updateCartBadge(data.cart_count);
            showToast(data.message || 'Đã thêm vào không gian ngủ của bạn ✨', 'success');
            
            // Automatically open mini cart drawer
            if (window.Alpine) {
                const root = document.querySelector('[x-data]');
                if (root && root._x_dataStack) {
                    root._x_dataStack[0].cartOpen = true;
                }
            }
            loadMiniCart();
        } else {
            showToast(data.message || 'Không thể thêm sản phẩm.', 'error');
        }
    } catch (err) {
        console.error(err);
        showToast('Có lỗi xảy ra khi thêm sản phẩm. Vui lòng thử lại.', 'error');
    }
}

/**
 * Load mini-cart panel contents
 */
async function loadMiniCart() {
    const contentEl = document.getElementById('mini-cart-content');
    const subtotalEl = document.getElementById('mini-cart-subtotal');
    if (!contentEl) return;

    try {
        const response = await fetch(`${APP_URL}/cart/mini`, {
            headers: { 'X-Requested-With': 'XMLHttpRequest' }
        });
        if (response.ok) {
            const data = await response.json();
            contentEl.innerHTML = data.html;
            if (subtotalEl) {
                subtotalEl.textContent = new Intl.NumberFormat('vi-VN').format(data.subtotal) + 'đ';
            }
            updateCartBadge(data.count);
        }
    } catch (e) {
        console.error('Failed to load mini cart', e);
    }
}

/**
 * Update quantity of item in cart (supports both Cart page and Mini-cart)
 */
async function updateCartItem(itemId, quantity) {
    if (quantity < 1) {
        return removeCartItem(itemId);
    }

    try {
        const formData = new FormData();
        formData.append('item_id', itemId);
        formData.append('quantity', quantity);

        const response = await fetch(`${APP_URL}/cart/update`, {
            method: 'POST',
            body: formData,
            headers: { 'X-Requested-With': 'XMLHttpRequest' }
        });

        const data = await response.json();
        if (data.success) {
            updateCartBadge(data.count);
            
            // If on cart page, update page elements
            const lineTotalEl = document.getElementById(`line-total-${itemId}`);
            const subtotalEl = document.getElementById('cart-subtotal');
            const totalEl = document.getElementById('cart-total');

            if (subtotalEl) {
                subtotalEl.textContent = new Intl.NumberFormat('vi-VN').format(data.subtotal) + 'đ';
            }
            if (totalEl) {
                totalEl.textContent = new Intl.NumberFormat('vi-VN').format(data.subtotal) + 'đ';
            }
            
            // Reload mini-cart if open
            loadMiniCart();
        } else {
            showToast(data.message || 'Không thể cập nhật số lượng.', 'error');
        }
    } catch (e) {
        console.error(e);
        showToast('Có lỗi xảy ra khi cập nhật giỏ hàng.', 'error');
    }
}

/**
 * Remove an item from cart
 */
async function removeCartItem(itemId) {
    try {
        const formData = new FormData();
        formData.append('item_id', itemId);

        const response = await fetch(`${APP_URL}/cart/remove`, {
            method: 'POST',
            body: formData,
            headers: { 'X-Requested-With': 'XMLHttpRequest' }
        });

        const data = await response.json();
        if (data.success) {
            updateCartBadge(data.count);
            showToast('Đã bỏ sản phẩm khỏi giỏ hàng', 'info');

            // If on cart page, remove row
            const row = document.getElementById(`cart-row-${itemId}`);
            if (row) {
                row.remove();
                const subtotalEl = document.getElementById('cart-subtotal');
                const totalEl = document.getElementById('cart-total');
                if (subtotalEl) {
                    subtotalEl.textContent = new Intl.NumberFormat('vi-VN').format(data.subtotal) + 'đ';
                }
                if (totalEl) {
                    totalEl.textContent = new Intl.NumberFormat('vi-VN').format(data.subtotal) + 'đ';
                }
                if (data.count === 0) {
                    window.location.reload();
                }
            }

            loadMiniCart();
        } else {
            showToast(data.message || 'Không thể xóa sản phẩm.', 'error');
        }
    } catch (e) {
        console.error(e);
        showToast('Có lỗi xảy ra khi xóa sản phẩm.', 'error');
    }
}

/**
 * Toast notification system with warm styling
 */
function showToast(message, type = 'success', title = '') {
    const container = document.getElementById('toast-container');
    if (!container) return;

    const toast = document.createElement('div');
    toast.className = `pointer-events-auto flex items-center gap-3 p-4 rounded-2xl shadow-warm-lg border text-sm max-w-sm toast-enter transition-all duration-300 ${
        type === 'success' ? 'bg-white border-[#5F7565]/30 text-[#26211E]' :
        type === 'error'   ? 'bg-white border-red-200 text-red-700' :
                             'bg-white border-[#E4D9C8] text-[#7D736A]'
    }`;

    const icon = type === 'success' 
        ? '<div class="w-8 h-8 rounded-full bg-[#EBF2ED] text-[#5F7565] flex items-center justify-center flex-shrink-0 text-sm font-bold">🌿</div>'
        : type === 'error'
        ? '<div class="w-8 h-8 rounded-full bg-red-50 text-red-600 flex items-center justify-center flex-shrink-0 text-sm font-bold">✕</div>'
        : '<div class="w-8 h-8 rounded-full bg-[#FAF7F2] text-[#B07B52] flex items-center justify-center flex-shrink-0 text-sm font-bold">🕯️</div>';

    toast.innerHTML = `
        ${icon}
        <div class="flex-1 min-w-0">
            ${title ? `<p class="font-semibold text-xs mb-0.5">${title}</p>` : ''}
            <p class="text-xs leading-relaxed">${message}</p>
        </div>
        <button onclick="this.parentElement.remove()" class="text-muted hover:text-charcoal p-1 text-sm leading-none">&times;</button>
    `;

    container.appendChild(toast);

    setTimeout(() => {
        toast.classList.remove('toast-enter');
        toast.classList.add('toast-exit');
        setTimeout(() => toast.remove(), 300);
    }, 3800);
}

/**
 * Submit login from Modal
 */
async function submitLogin(e) {
    e.preventDefault();
    const form = e.target;
    const errorEl = document.getElementById('login-error');
    if (errorEl) errorEl.classList.add('hidden');

    const formData = new FormData(form);

    try {
        const response = await fetch(`${APP_URL}/user/login`, {
            method: 'POST',
            body: formData,
            headers: { 'X-Requested-With': 'XMLHttpRequest' }
        });

        if (response.redirected) {
            window.location.href = response.url;
            return;
        }

        const data = await response.json();
        if (data.success) {
            showToast('Đăng nhập thành công! Đang chuyển trang...', 'success');
            setTimeout(() => {
                window.location.href = data.redirect || `${APP_URL}`;
            }, 800);
        } else {
            if (errorEl) {
                errorEl.textContent = data.message || 'Email hoặc mật khẩu không chính xác.';
                errorEl.classList.remove('hidden');
            } else {
                showToast(data.message || 'Đăng nhập thất bại.', 'error');
            }
        }
    } catch (err) {
        // If regular form post is required
        form.action = `${APP_URL}/user/login`;
        form.method = 'POST';
        form.submit();
    }
}

/**
 * Submit register from Modal
 */
async function submitRegister(e) {
    e.preventDefault();
    const form = e.target;
    const errorEl = document.getElementById('register-error');
    if (errorEl) errorEl.classList.add('hidden');

    const formData = new FormData(form);

    try {
        const response = await fetch(`${APP_URL}/user/register`, {
            method: 'POST',
            body: formData,
            headers: { 'X-Requested-With': 'XMLHttpRequest' }
        });

        if (response.redirected) {
            window.location.href = response.url;
            return;
        }

        const data = await response.json();
        if (data.success) {
            showToast('Đăng ký tài khoản thành công!', 'success');
            setTimeout(() => {
                window.location.href = data.redirect || `${APP_URL}`;
            }, 800);
        } else {
            if (errorEl) {
                errorEl.textContent = data.message || 'Đăng ký thất bại. Vui lòng kiểm tra lại.';
                errorEl.classList.remove('hidden');
            } else {
                showToast(data.message || 'Đăng ký thất bại.', 'error');
            }
        }
    } catch (err) {
        form.action = `${APP_URL}/user/register`;
        form.method = 'POST';
        form.submit();
    }
}

/**
 * Apply sorting on product catalog
 */
function applySort(val) {
    const url = new URL(window.location.href);
    url.searchParams.set('sort', val);
    url.searchParams.set('page', '1');
    window.location.href = url.toString();
}

/**
 * Apply items per page on product catalog
 */
function applyPerPage(val) {
    const url = new URL(window.location.href);
    url.searchParams.set('per_page', val);
    url.searchParams.set('page', '1');
    window.location.href = url.toString();
}
