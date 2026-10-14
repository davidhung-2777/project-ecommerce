<?php
$pageTitle = 'Chi tiết đơn hàng #' . $order['order_number'];
$base = $_ENV['APP_URL'] ?? '';
$statusLabels = ['pending' => 'Chờ xác nhận', 'confirmed' => 'Đã xác nhận', 'processing' => 'Đang xử lý', 'shipped' => 'Đang giao', 'delivered' => 'Đã giao', 'cancelled' => 'Đã hủy'];
?>
<div class="max-w-4xl space-y-6">
    <div class="flex items-center justify-between">
        <div class="flex items-center gap-3">
            <a href="<?= $base ?>/admin/orders" class="text-muted hover:text-charcoal text-sm">← Đơn hàng</a>
            <span class="text-muted">/</span>
            <h1 class="text-xl font-bold"><?= htmlspecialchars($order['order_number']) ?></h1>
        </div>
        <!-- Status Update -->
        <div class="flex items-center gap-3">
            <select id="order-status" class="border border-gray-200 rounded-lg px-3 py-2 text-sm">
                <?php foreach ($statusLabels as $v => $l): ?>
                <option value="<?= $v ?>" <?= $order['status'] === $v ? 'selected' : '' ?>><?= $l ?></option>
                <?php endforeach; ?>
            </select>
            <button onclick="updateOrderStatusFromDropdown(<?= $order['id'] ?>)"
                    class="bg-charcoal text-white px-4 py-2 rounded-lg text-sm font-medium hover:bg-wooddk transition">
                Cập nhật
            </button>
            
            <?php if (empty($order['shipping_code']) && in_array($order['status'], ['confirmed', 'processing'])): ?>
            <button onclick="createGHNOrder(<?= $order['id'] ?>)"
                    class="bg-blue-600 text-white px-4 py-2 rounded-lg text-sm font-medium hover:bg-blue-700 transition">
                🚚 Tạo đơn GHN
            </button>
            <?php elseif (!empty($order['shipping_code'])): ?>
            <div class="flex items-center gap-3">
                <div class="px-3 py-2 bg-green-50 text-green-700 rounded-lg text-sm font-medium">
                    ✓ Mã vận đơn: <?= htmlspecialchars($order['shipping_code']) ?>
                </div>
                <a href="<?= $base ?>/admin/orders/<?= $order['id'] ?>/tracking"
                   class="bg-indigo-600 text-white px-4 py-2 rounded-lg text-sm font-medium hover:bg-indigo-700 transition">
                    📍 Tracking
                </a>
            </div>
            <?php endif; ?>
        </div>
    </div>

    <div class="grid sm:grid-cols-2 gap-5">
        <!-- Order Info -->
        <div class="bg-white rounded-xl border border-gray-200 p-5">
            <h2 class="font-semibold text-sm mb-4">Thông tin đơn hàng</h2>
            <dl class="space-y-2 text-sm">
                <div class="flex justify-between"><dt class="text-muted">Mã đơn</dt><dd class="font-medium"><?= $order['order_number'] ?></dd></div>
                <div class="flex justify-between"><dt class="text-muted">Loại HĐ</dt><dd><?= $order['invoice_type'] === 'vat' ? 'Hóa đơn VAT' : 'Hóa đơn bán lẻ' ?></dd></div>
                <div class="flex justify-between"><dt class="text-muted">PTTT</dt><dd><?= ['cod' => 'COD', 'bank_transfer' => 'Chuyển khoản', 'momo' => 'MoMo', 'vnpay' => 'VNPay'][$order['payment_method']] ?></dd></div>
                <div class="flex justify-between items-center"><dt class="text-muted">TT Thanh toán</dt>
                    <dd class="flex items-center gap-2">
                        <span class="px-2 py-0.5 rounded text-xs <?= $order['payment_status'] === 'paid' ? 'bg-green-50 text-green-700' : 'bg-yellow-50 text-yellow-700' ?>">
                            <?= $order['payment_status'] === 'paid' ? 'Đã thanh toán' : 'Chưa thanh toán' ?>
                        </span>
                        <?php if ($order['payment_method'] === 'cod' && $order['payment_status'] === 'pending'): ?>
                        <button onclick="updatePaymentStatus(<?= $order['id'] ?>, 'paid')"
                                class="text-xs bg-green-600 text-white px-2 py-1 rounded hover:bg-green-700">
                            ✓ Đã thu COD
                        </button>
                        <?php endif; ?>
                    </dd>
                </div>
                <div class="flex justify-between"><dt class="text-muted">Ngày tạo</dt><dd class="text-xs"><?= date('d/m/Y H:i', strtotime($order['created_at'])) ?></dd></div>
            </dl>
        </div>

        <!-- Shipping -->
        <div class="bg-white rounded-xl border border-gray-200 p-5">
            <h2 class="font-semibold text-sm mb-4">Thông tin giao hàng</h2>
            <div class="text-sm space-y-1">
                <p class="font-medium"><?= htmlspecialchars($order['shipping_name']) ?></p>
                <p class="text-muted"><?= htmlspecialchars($order['shipping_phone']) ?></p>
                <p class="text-muted"><?= htmlspecialchars($order['shipping_address']) ?></p>
                <?php if ($order['shipping_ward'] || $order['shipping_district'] || $order['shipping_city']): ?>
                <p class="text-muted">
                    <?php
                    $addressParts = array_filter([
                        $order['shipping_ward'] ?? '',
                        $order['shipping_district'] ?? '',
                        $order['shipping_city'] ?? ''
                    ]);
                    echo htmlspecialchars(implode(', ', $addressParts));
                    ?>
                </p>
                <?php endif; ?>
            </div>
            <?php if ($order['customer_note']): ?>
            <div class="mt-3 p-2 bg-yellow-50 rounded text-xs text-yellow-800">
                Ghi chú: <?= htmlspecialchars($order['customer_note']) ?>
            </div>
            <?php endif; ?>
        </div>
    </div>

    <!-- VAT Info -->
    <?php if ($order['invoice_type'] === 'vat' && $order['vat_company_name']): ?>
    <div class="bg-blue-50 border border-blue-200 rounded-xl p-5 text-sm">
        <h2 class="font-semibold mb-3">Thông tin hóa đơn VAT</h2>
        <p>Công ty: <strong><?= htmlspecialchars($order['vat_company_name']) ?></strong></p>
        <p>MST: <?= htmlspecialchars($order['vat_tax_code']) ?></p>
        <p>Địa chỉ: <?= htmlspecialchars($order['vat_address']) ?></p>
    </div>
    <?php endif; ?>

    <!-- Items -->
    <div class="bg-white rounded-xl border border-gray-200 overflow-hidden">
        <div class="px-5 py-4 border-b border-gray-100">
            <h2 class="font-semibold text-sm">Sản phẩm đặt hàng</h2>
        </div>
        <table class="w-full text-sm">
            <thead class="bg-gray-50 text-xs text-muted">
                <tr>
                    <th class="text-left px-5 py-3">Sản phẩm</th>
                    <th class="text-right px-5 py-3">Đơn giá</th>
                    <th class="text-center px-5 py-3">SL</th>
                    <th class="text-right px-5 py-3">Thành tiền</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-gray-100">
                <?php foreach ($order['items'] as $item): ?>
                <tr>
                    <td class="px-5 py-3">
                        <div class="flex items-center gap-3">
                            <div class="w-10 h-10 rounded-lg overflow-hidden bg-cream flex-shrink-0">
                                <img src="<?= htmlspecialchars($item['product_image'] ?: $base . '/assets/images/product-placeholder.jpg') ?>" class="w-full h-full object-cover">
                            </div>
                            <div>
                                <p class="font-medium"><?= htmlspecialchars($item['product_name']) ?></p>
                                <?php if ($item['size_option'] || $item['color_option']): ?>
                                <p class="text-xs text-muted"><?= $item['size_option'] ?><?= $item['color_option'] ? ' • ' . $item['color_option'] : '' ?></p>
                                <?php endif; ?>
                            </div>
                        </div>
                    </td>
                    <td class="px-5 py-3 text-right"><?= number_format($item['unit_price']) ?>đ</td>
                    <td class="px-5 py-3 text-center"><?= $item['quantity'] ?></td>
                    <td class="px-5 py-3 text-right font-medium"><?= number_format($item['subtotal']) ?>đ</td>
                </tr>
                <?php endforeach; ?>
            </tbody>
            <tfoot class="border-t border-gray-200 bg-gray-50">
                <tr>
                    <td colspan="3" class="px-5 py-3 text-right text-sm font-medium text-muted">Tạm tính</td>
                    <td class="px-5 py-3 text-right"><?= number_format($order['subtotal']) ?>đ</td>
                </tr>
                <tr>
                    <td colspan="3" class="px-5 py-3 text-right text-sm font-medium text-muted">Phí vận chuyển</td>
                    <td class="px-5 py-3 text-right"><?= number_format($order['shipping_fee']) ?>đ</td>
                </tr>
                <?php if ($order['tax_amount'] > 0): ?>
                <tr>
                    <td colspan="3" class="px-5 py-3 text-right text-sm font-medium text-muted">Thuế VAT (10%)</td>
                    <td class="px-5 py-3 text-right"><?= number_format($order['tax_amount']) ?>đ</td>
                </tr>
                <?php endif; ?>
                <tr>
                    <td colspan="3" class="px-5 py-3 text-right text-base font-bold text-charcoal">Tổng cộng</td>
                    <td class="px-5 py-3 text-right text-base font-bold text-charcoal"><?= number_format($order['total_amount']) ?>đ</td>
                </tr>
            </tfoot>
        </table>
    </div>
</div>

<script>
function updateOrderStatusFromDropdown(orderId) {
    const status = document.getElementById('order-status').value;
    fetch(APP_URL + '/admin/orders/' + orderId + '/status', {
        method: 'POST',
        headers: { 'Content-Type': 'application/x-www-form-urlencoded', 'X-Requested-With': 'XMLHttpRequest' },
        body: 'status=' + status
    }).then(r => r.json()).then(d => {
        if (d.success) location.reload();
        else alert(d.message || 'Lỗi!');
    });
}

function createGHNOrder(orderId) {
    if (!confirm('Bạn có chắc muốn tạo đơn vận chuyển GHN cho đơn hàng này?')) {
        return;
    }
    
    const button = event.target;
    const originalText = button.innerHTML;
    button.disabled = true;
    button.innerHTML = '⏳ Đang tạo...';
    
    fetch(APP_URL + '/admin/orders/' + orderId + '/create-ghn-order', {
        method: 'POST',
        headers: { 
            'Content-Type': 'application/x-www-form-urlencoded',
            'X-Requested-With': 'XMLHttpRequest'
        }
    })
    .then(r => r.json())
    .then(d => {
        if (d.success) {
            alert('✓ Tạo đơn GHN thành công!\nMã vận đơn: ' + d.shipping_code);
            location.reload();
        } else {
            alert('❌ Lỗi: ' + (d.message || 'Không thể tạo đơn GHN'));
            button.disabled = false;
            button.innerHTML = originalText;
        }
    })
    .catch(err => {
        alert('❌ Lỗi kết nối: ' + err.message);
        button.disabled = false;
        button.innerHTML = originalText;
    });
}

function updatePaymentStatus(orderId, status) {
    if (!confirm('Bạn có chắc muốn cập nhật trạng thái thanh toán COD thành "Đã thanh toán"?')) {
        return;
    }
    
    fetch(APP_URL + '/admin/orders/' + orderId + '/payment-status', {
        method: 'POST',
        headers: { 'Content-Type': 'application/x-www-form-urlencoded', 'X-Requested-With': 'XMLHttpRequest' },
        body: 'payment_status=' + status
    }).then(r => r.json()).then(d => {
        if (d.success) {
            alert('✓ Đã cập nhật trạng thái thanh toán!');
            location.reload();
        } else {
            alert('❌ Lỗi: ' + (d.message || 'Không thể cập nhật'));
        }
    }).catch(err => {
        alert('❌ Lỗi kết nối: ' + err.message);
    });
}
</script>
