<?php
$baseUrl   = $_ENV['APP_URL'] ?? 'http://localhost/project-ecommerce/public';
$pageTitle = 'Chi Tiết Đơn Hàng #' . htmlspecialchars($order['order_number'] ?? '') . ' - DecorNest';
?>

<div class="max-w-4xl mx-auto px-4 sm:px-6 lg:px-8 py-10 lg:py-16">
    
    <!-- Header -->
    <div class="flex items-center justify-between pb-6 border-b border-beige mb-8">
        <div>
            <div class="flex items-center gap-3">
                <h1 class="text-2xl sm:text-3xl font-serif font-bold text-charcoal">Đơn hàng #<?= htmlspecialchars($order['order_number'] ?? '') ?></h1>
                <?php
                $statusColors = [
                    'pending'    => 'bg-amber-50 text-amber-800 border-amber-200',
                    'confirmed'  => 'bg-blue-50 text-blue-800 border-blue-200',
                    'processing' => 'bg-purple-50 text-purple-800 border-purple-200',
                    'shipped'    => 'bg-indigo-50 text-indigo-800 border-indigo-200',
                    'delivered'  => 'bg-sage-light text-sage border-sage/20',
                    'cancelled'  => 'bg-red-50 text-red-800 border-red-200',
                ];
                $statusLabels = [
                    'pending'    => 'Chờ duyệt',
                    'confirmed'  => 'Đã duyệt',
                    'processing' => 'Đang đóng gói',
                    'shipped'    => 'Đang vận chuyển',
                    'delivered'  => 'Đã giao thành công',
                    'cancelled'  => 'Đã hủy',
                ];
                ?>
                <span class="px-3 py-1 text-xs font-semibold rounded-full border <?= $statusColors[$order['status']] ?? 'bg-cream text-muted' ?>">
                    <?= $statusLabels[$order['status']] ?? $order['status'] ?>
                </span>
            </div>
            <p class="text-xs text-muted mt-1">Ngày đặt: <?= date('d/m/Y H:i', strtotime($order['created_at'])) ?></p>
        </div>
        <a href="<?= $baseUrl ?>/account/orders" class="text-xs font-semibold text-wood hover:underline">
            ← Quay lại danh sách
        </a>
    </div>

    <!-- Items List -->
    <div class="bg-white rounded-3xl border border-beige overflow-hidden shadow-warm mb-8">
        <div class="px-6 py-4 bg-cream/60 border-b border-beige">
            <h3 class="font-serif font-bold text-sm text-charcoal">Danh sách sản phẩm decor</h3>
        </div>
        <div class="divide-y divide-beige/60 p-6 space-y-4">
            <?php foreach (($order['items'] ?? []) as $item): ?>
            <div class="pt-4 first:pt-0 flex items-center gap-4">
                <div class="w-16 h-16 rounded-2xl overflow-hidden bg-cream border border-beige/60 flex-shrink-0">
                    <?php 
                        $img = !empty($item['product_image']) ? $item['product_image'] : '/assets/images/product-placeholder.jpg';
                        if (!str_starts_with($img, 'http') && !str_starts_with($img, '/')) {
                            $img = $baseUrl . '/' . ltrim($img, '/');
                        }
                    ?>
                    <img src="<?= htmlspecialchars($img) ?>" class="w-full h-full object-cover">
                </div>
                <div class="flex-1 min-w-0">
                    <p class="font-serif font-bold text-sm text-charcoal"><?= htmlspecialchars($item['product_name']) ?></p>
                    <p class="text-xs text-muted mt-0.5">Số lượng: <?= $item['quantity'] ?> × <?= number_format($item['unit_price']) ?>đ</p>
                </div>
                <span class="font-bold text-sm sm:text-base text-charcoal font-serif"><?= number_format($item['subtotal']) ?>đ</span>
            </div>
            <?php endforeach; ?>
        </div>
        <div class="px-6 py-4 bg-cream/40 border-t border-beige flex justify-between items-center">
            <span class="font-serif font-bold text-sm text-charcoal">Tổng thanh toán:</span>
            <span class="font-serif font-bold text-xl text-wood"><?= number_format($order['total_amount']) ?>đ</span>
        </div>
    </div>

    <!-- Info Cards Grid -->
    <div class="grid sm:grid-cols-2 gap-6 mb-8">
        <div class="bg-white rounded-3xl border border-beige p-6 shadow-warm text-xs sm:text-sm space-y-2">
            <h4 class="font-serif font-bold text-sm text-charcoal pb-2 border-b border-beige mb-3 flex items-center gap-2">
                <span>📍</span> Địa chỉ giao hàng & Lắp đặt
            </h4>
            <p class="font-semibold text-charcoal"><?= htmlspecialchars($order['shipping_name'] ?? '') ?></p>
            <p class="text-muted"><?= htmlspecialchars($order['shipping_phone'] ?? '') ?></p>
            <p class="text-muted"><?= htmlspecialchars($order['shipping_address'] ?? '') ?>, <?= htmlspecialchars($order['shipping_district'] ?? '') ?>, <?= htmlspecialchars($order['shipping_city'] ?? '') ?></p>
            <?php if (!empty($order['customer_note'])): ?>
            <p class="pt-2 text-xs text-muted italic">"<?= htmlspecialchars($order['customer_note']) ?>"</p>
            <?php endif; ?>
        </div>

        <div class="bg-white rounded-3xl border border-beige p-6 shadow-warm text-xs sm:text-sm space-y-2">
            <h4 class="font-serif font-bold text-sm text-charcoal pb-2 border-b border-beige mb-3 flex items-center gap-2">
                <span>💳</span> Phương thức thanh toán
            </h4>
            <p class="font-semibold text-charcoal uppercase"><?= htmlspecialchars($order['payment_method'] ?? 'bank_transfer') ?></p>
            <p class="text-muted">
                Trạng thái: 
                <span class="font-semibold <?= ($order['payment_status'] ?? '') === 'paid' ? 'text-sage' : 'text-amber-warm' ?>">
                    <?= ($order['payment_status'] ?? '') === 'paid' ? 'Đã thanh toán' : 'Chờ thanh toán' ?>
                </span>
            </p>
        </div>
    </div>

    <!-- Shipping Tracking -->
    <?php if (!empty($order['shipping_code'])): ?>
    <div class="bg-gradient-to-br from-blue-50 to-indigo-50 rounded-3xl border-2 border-indigo-200 p-6 shadow-warm mb-8">
        <div class="flex items-center justify-between mb-4">
            <h4 class="font-serif font-bold text-lg text-charcoal flex items-center gap-2">
                <span class="text-2xl">🚚</span> Vận chuyển
            </h4>
            <button onclick="refreshTracking()" class="text-xs text-indigo-600 hover:text-indigo-800 font-semibold">
                🔄 Làm mới
            </button>
        </div>

        <div class="bg-white/80 backdrop-blur rounded-2xl p-4 mb-4">
            <div class="flex items-center gap-3 mb-2">
                <span class="text-2xl">📦</span>
                <div>
                    <p class="text-xs text-muted">Mã vận đơn GHN</p>
                    <p class="font-mono font-bold text-charcoal"><?= htmlspecialchars($order['shipping_code']) ?></p>
                </div>
            </div>
            <p class="text-xs text-muted mt-2">
                <?php if (str_starts_with($order['shipping_code'], 'DEMO')): ?>
                ⚠️ Đây là mã vận đơn DEMO - Tracking đồng bộ với trạng thái đơn hàng
                <?php else: ?>
                Đơn vị vận chuyển: <span class="font-semibold">Giao Hàng Nhanh (GHN)</span>
                <?php endif; ?>
            </p>
        </div>

        <div id="tracking-info" class="bg-white/60 rounded-2xl p-4 text-sm">
            <div class="flex items-center justify-center py-8">
                <div class="animate-spin rounded-full h-8 w-8 border-b-2 border-indigo-600"></div>
            </div>
        </div>

        <p class="text-[10px] text-muted text-center mt-3">
            💡 Bấm "Làm mới" để cập nhật tracking mới nhất
        </p>
    </div>
    <?php endif; ?>

</div>

<?php if (!empty($order['shipping_code'])): ?>
<script>
const orderId = <?= (int) $order['id'] ?>;

function loadTracking() {
    fetch(`<?= $baseUrl ?>/api/tracking.php?id=<?= (int) $order['id'] ?>`, {
        credentials: 'same-origin' // Gửi cookie session
    })
        .then(r => {
            if (!r.ok) {
                throw new Error(`HTTP ${r.status}`);
            }
            return r.json();
        })
        .then(data => {
            if (data.success) {
                displayTracking(data.tracking);
            } else {
                document.getElementById('tracking-info').innerHTML = `
                    <p class="text-center text-red-600 text-xs">${data.message || 'Không thể tải thông tin tracking'}</p>
                `;
            }
        })
        .catch(err => {
            console.error('Tracking error:', err);
            document.getElementById('tracking-info').innerHTML = `
                <p class="text-center text-muted text-xs">Lỗi: ${err.message}. Vui lòng đăng nhập lại.</p>
            `;
        });
}

function displayTracking(tracking) {
    const trackingData = tracking.data || {};
    const logs = trackingData.log || [];

    const statusIcons = {
        'ready_to_pick': '📦',
        'picking': '🚶',
        'picked': '✅',
        'storing': '🏪',
        'delivering': '🚚',
        'delivered': '🎉',
        'cancelled': '❌'
    };

    let html = `
        <div class="mb-4">
            <div class="flex items-center gap-3">
                <span class="text-3xl">${statusIcons[trackingData.status] || '📦'}</span>
                <div>
                    <p class="font-serif font-bold text-charcoal">${trackingData.status_text || 'Đang xử lý'}</p>
                    <p class="text-xs text-muted">Dự kiến giao: ${new Date(trackingData.expected_delivery_time || Date.now() + 3*24*3600*1000).toLocaleString('vi-VN')}</p>
                </div>
            </div>
        </div>
    `;

    if (logs.length > 0) {
        html += '<div class="border-t border-gray-200 pt-4 space-y-3">';
        const lastIndex = logs.length - 1;

        logs.forEach((log, index) => {
            const isLast = index === lastIndex;
            const isPast = index < lastIndex;

            html += `
                <div class="flex gap-3 ${isPast ? 'opacity-80' : ''}">
                    <div class="flex-shrink-0 w-8 h-8 rounded-full ${isLast ? 'bg-indigo-600' : (isPast ? 'bg-green-500' : 'bg-gray-300')} flex items-center justify-center text-white text-xs font-bold">
                        ${isPast ? '✓' : (isLast ? '●' : index + 1)}
                    </div>
                    <div class="flex-1">
                        <p class="text-xs font-semibold ${isLast ? 'text-indigo-600' : 'text-charcoal'}">${log.description || ''}</p>
                        <p class="text-[10px] text-muted">${new Date(log.time).toLocaleString('vi-VN')}</p>
                    </div>
                </div>
            `;
        });
        html += '</div>';
    } else {
        html += '<p class="text-center text-muted text-xs">Chưa có thông tin tracking</p>';
    }

    document.getElementById('tracking-info').innerHTML = html;
}

function refreshTracking() {
    document.getElementById('tracking-info').innerHTML = '<div class="flex items-center justify-center py-8"><div class="animate-spin rounded-full h-8 w-8 border-b-2 border-indigo-600"></div></div>';
    loadTracking();
}

// Load tracking on page load
loadTracking();
</script>
<?php endif; ?>
