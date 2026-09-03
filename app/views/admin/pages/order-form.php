<?php
$pageTitle = isset($order) ? 'Sửa Đơn Hàng' : 'Thêm Đơn Hàng';
$base      = $_ENV['APP_URL'] ?? '';
$isEdit    = isset($order);
$action    = $isEdit ? $base . '/admin/orders/' . $order['id'] . '/update' : $base . '/admin/orders/store';

$statusOptions = [
    'pending' => 'Chờ xác nhận',
    'confirmed' => 'Đã xác nhận',
    'processing' => 'Đang xử lý',
    'shipped' => 'Đang giao',
    'delivered' => 'Đã giao',
    'cancelled' => 'Đã hủy'
];

$paymentMethods = [
    'cod' => 'COD - Thanh toán khi nhận hàng',
    'bank_transfer' => 'Chuyển khoản ngân hàng',
    'momo' => 'MoMo',
    'vnpay' => 'VNPay'
];
?>
<div class="max-w-5xl space-y-5">
    <div class="flex items-center gap-3">
        <a href="<?= $base ?>/admin/orders" class="text-muted hover:text-charcoal text-sm">← Đơn hàng</a>
        <span class="text-muted">/</span>
        <h1 class="text-xl font-bold text-charcoal"><?= $pageTitle ?></h1>
    </div>

    <?php if ($isEdit): ?>
    <div class="bg-blue-50 border border-blue-200 rounded-lg p-4">
        <p class="text-sm text-blue-800">
            <strong>Mã đơn:</strong> <?= htmlspecialchars($order['order_number'] ?? 'N/A') ?> | 
            <strong>Ngày tạo:</strong> <?= date('d/m/Y H:i', strtotime($order['created_at'])) ?>
        </p>
    </div>
    <?php endif; ?>

    <form action="<?= $action ?>" method="POST" class="grid lg:grid-cols-3 gap-6" x-data="orderForm()">

        <!-- Main Info -->
        <div class="lg:col-span-2 space-y-5">

            <!-- Customer Info -->
            <div class="bg-white rounded-xl border border-gray-200 p-5 space-y-4">
                <h2 class="font-semibold text-sm text-charcoal border-b pb-3">Thông Tin Khách Hàng</h2>
                
                <?php if (!$isEdit): ?>
                <div>
                    <label class="block text-xs font-medium text-muted mb-1.5">Khách hàng *</label>
                    <select name="user_id" class="w-full border border-gray-200 rounded-lg px-4 py-2.5 text-sm focus:outline-none focus:border-wood">
                        <option value="">-- Khách lẻ (không có tài khoản) --</option>
                        <?php foreach ($users ?? [] as $user): ?>
                        <option value="<?= $user['id'] ?>"><?= htmlspecialchars($user['name']) ?> (<?= htmlspecialchars($user['email']) ?>)</option>
                        <?php endforeach; ?>
                    </select>
                </div>
                <?php endif; ?>

                <div>
                    <label class="block text-xs font-medium text-muted mb-1.5">Tên người nhận *</label>
                    <input type="text" name="shipping_name" required value="<?= htmlspecialchars($order['shipping_name'] ?? '') ?>"
                           class="w-full border border-gray-200 rounded-lg px-4 py-2.5 text-sm focus:outline-none focus:border-wood">
                </div>

                <div class="grid grid-cols-2 gap-4">
                    <div>
                        <label class="block text-xs font-medium text-muted mb-1.5">Số điện thoại *</label>
                        <input type="tel" name="shipping_phone" required value="<?= htmlspecialchars($order['shipping_phone'] ?? '') ?>"
                               class="w-full border border-gray-200 rounded-lg px-4 py-2.5 text-sm focus:outline-none focus:border-wood">
                    </div>
                    <div>
                        <label class="block text-xs font-medium text-muted mb-1.5">Email</label>
                        <input type="email" name="shipping_email" value="<?= htmlspecialchars($order['shipping_email'] ?? '') ?>"
                               class="w-full border border-gray-200 rounded-lg px-4 py-2.5 text-sm focus:outline-none focus:border-wood">
                    </div>
                </div>

                <div>
                    <label class="block text-xs font-medium text-muted mb-1.5">Địa chỉ giao hàng *</label>
                    <textarea name="shipping_address" required rows="3"
                              class="w-full border border-gray-200 rounded-lg px-4 py-2.5 text-sm focus:outline-none focus:border-wood resize-none"><?= htmlspecialchars($order['shipping_address'] ?? '') ?></textarea>
                </div>

                <div>
                    <label class="block text-xs font-medium text-muted mb-1.5">Ghi chú</label>
                    <textarea name="notes" rows="2"
                              class="w-full border border-gray-200 rounded-lg px-4 py-2.5 text-sm focus:outline-none focus:border-wood resize-none"><?= htmlspecialchars($order['notes'] ?? '') ?></textarea>
                </div>
            </div>

            <!-- Order Items -->
            <div class="bg-white rounded-xl border border-gray-200 p-5">
                <div class="flex items-center justify-between mb-4">
                    <h2 class="font-semibold text-sm text-charcoal">Sản Phẩm</h2>
                    <button type="button" @click="addItem()" class="text-xs text-wood hover:underline">+ Thêm sản phẩm</button>
                </div>

                <template x-for="(item, index) in items" :key="index">
                    <div class="grid grid-cols-12 gap-3 mb-3 items-end border-b pb-3">
                        <div class="col-span-5">
                            <label class="block text-xs font-medium text-muted mb-1">Sản phẩm *</label>
                            <select :name="'items[' + index + '][product_id]'" x-model="item.product_id" required
                                    @change="updateItemPrice(index)"
                                    class="w-full border border-gray-200 rounded-lg px-3 py-2 text-sm focus:outline-none focus:border-wood">
                                <option value="">-- Chọn --</option>
                                <?php foreach ($products ?? [] as $p): ?>
                                <option value="<?= $p['id'] ?>" data-price="<?= $p['price'] ?>" data-name="<?= htmlspecialchars($p['name']) ?>">
                                    <?= htmlspecialchars($p['name']) ?> (<?= number_format($p['price']) ?>đ)
                                </option>
                                <?php endforeach; ?>
                            </select>
                        </div>
                        <div class="col-span-2">
                            <label class="block text-xs font-medium text-muted mb-1">SL *</label>
                            <input type="number" :name="'items[' + index + '][quantity]'" x-model="item.quantity" required min="1" max="100"
                                   @input="calculateTotal()"
                                   class="w-full border border-gray-200 rounded-lg px-3 py-2 text-sm focus:outline-none focus:border-wood">
                        </div>
                        <div class="col-span-2">
                            <label class="block text-xs font-medium text-muted mb-1">Đơn giá *</label>
                            <input type="number" :name="'items[' + index + '][unit_price]'" x-model="item.unit_price" required min="0" step="1000"
                                   @input="calculateTotal()"
                                   class="w-full border border-gray-200 rounded-lg px-3 py-2 text-sm focus:outline-none focus:border-wood">
                        </div>
                        <div class="col-span-2">
                            <label class="block text-xs font-medium text-muted mb-1">Thành tiền</label>
                            <input type="text" :value="formatMoney(item.quantity * item.unit_price)" disabled
                                   class="w-full border border-gray-200 rounded-lg px-3 py-2 text-sm bg-gray-50">
                        </div>
                        <div class="col-span-1">
                            <button type="button" @click="items.splice(index, 1); calculateTotal()" class="text-red-500 hover:text-red-700">
                                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/>
                                </svg>
                            </button>
                        </div>
                    </div>
                </template>

                <div class="text-right mt-4 p-3 bg-gray-50 rounded-lg">
                    <p class="text-sm text-muted">Tổng tiền sản phẩm:</p>
                    <p class="text-xl font-bold text-charcoal" x-text="formatMoney(subtotal)"></p>
                </div>
            </div>

            <!-- Shipping & Fees -->
            <div class="bg-white rounded-xl border border-gray-200 p-5 space-y-4">
                <h2 class="font-semibold text-sm text-charcoal border-b pb-3">Phí & Chi Phí Khác</h2>
                
                <div class="grid grid-cols-2 gap-4">
                    <div>
                        <label class="block text-xs font-medium text-muted mb-1.5">Phí vận chuyển (đ)</label>
                        <input type="number" name="shipping_fee" x-model="shippingFee" min="0" step="1000"
                               @input="calculateTotal()"
                               class="w-full border border-gray-200 rounded-lg px-4 py-2.5 text-sm focus:outline-none focus:border-wood">
                    </div>
                    <div>
                        <label class="block text-xs font-medium text-muted mb-1.5">Giảm giá (đ)</label>
                        <input type="number" name="discount_amount" x-model="discountAmount" min="0" step="1000"
                               @input="calculateTotal()"
                               class="w-full border border-gray-200 rounded-lg px-4 py-2.5 text-sm focus:outline-none focus:border-wood">
                    </div>
                </div>

                <div class="bg-green-50 p-4 rounded-lg">
                    <div class="flex justify-between items-center">
                        <span class="text-sm font-medium text-green-800">TỔNG CỘNG:</span>
                        <span class="text-2xl font-bold text-green-800" x-text="formatMoney(totalAmount)"></span>
                    </div>
                </div>
                
                <input type="hidden" name="total_amount" :value="totalAmount">
            </div>
        </div>

        <!-- Right Panel -->
        <div class="space-y-5">

            <!-- Status -->
            <div class="bg-white rounded-xl border border-gray-200 p-5 space-y-4">
                <h2 class="font-semibold text-sm text-charcoal border-b pb-3">Trạng Thái</h2>
                
                <div>
                    <label class="block text-xs font-medium text-muted mb-1.5">Trạng thái đơn hàng *</label>
                    <select name="status" required class="w-full border border-gray-200 rounded-lg px-4 py-2.5 text-sm focus:outline-none focus:border-wood">
                        <?php foreach ($statusOptions as $value => $label): ?>
                        <option value="<?= $value ?>" <?= ($order['status'] ?? 'pending') === $value ? 'selected' : '' ?>>
                            <?= $label ?>
                        </option>
                        <?php endforeach; ?>
                    </select>
                </div>

                <div>
                    <label class="block text-xs font-medium text-muted mb-1.5">Phương thức thanh toán *</label>
                    <select name="payment_method" required class="w-full border border-gray-200 rounded-lg px-4 py-2.5 text-sm focus:outline-none focus:border-wood">
                        <?php foreach ($paymentMethods as $value => $label): ?>
                        <option value="<?= $value ?>" <?= ($order['payment_method'] ?? 'cod') === $value ? 'selected' : '' ?>>
                            <?= $label ?>
                        </option>
                        <?php endforeach; ?>
                    </select>
                </div>

                <div>
                    <label class="block text-xs font-medium text-muted mb-1.5">Trạng thái thanh toán *</label>
                    <select name="payment_status" required class="w-full border border-gray-200 rounded-lg px-4 py-2.5 text-sm focus:outline-none focus:border-wood">
                        <option value="pending" <?= ($order['payment_status'] ?? 'pending') === 'pending' ? 'selected' : '' ?>>Chưa thanh toán</option>
                        <option value="paid" <?= ($order['payment_status'] ?? '') === 'paid' ? 'selected' : '' ?>>Đã thanh toán</option>
                    </select>
                </div>
            </div>

            <button type="submit" class="w-full bg-charcoal text-white py-3 rounded-xl text-sm font-semibold hover:bg-wooddk transition">
                <?= $isEdit ? '💾 Cập Nhật Đơn Hàng' : '✅ Tạo Đơn Hàng' ?>
            </button>

            <?php if ($isEdit): ?>
            <button type="button" onclick="if(confirm('Xóa đơn hàng này?')) { location.href='<?= $base ?>/admin/orders/<?= $order['id'] ?>/delete'; }"
                    class="w-full bg-red-500 text-white py-3 rounded-xl text-sm font-semibold hover:bg-red-600 transition">
                🗑️ Xóa Đơn Hàng
            </button>
            <?php endif; ?>
        </div>

    </form>
</div>

<script>
function orderForm() {
    return {
        items: <?= isset($order) && !empty($orderItems) ? json_encode(array_map(fn($item) => [
            'product_id' => $item['product_id'],
            'quantity' => $item['quantity'],
            'unit_price' => $item['unit_price']
        ], $orderItems)) : '[{product_id: "", quantity: 1, unit_price: 0}]' ?>,
        shippingFee: <?= $order['shipping_fee'] ?? 0 ?>,
        discountAmount: <?= $order['discount_amount'] ?? 0 ?>,
        subtotal: 0,
        totalAmount: 0,

        addItem() {
            this.items.push({ product_id: '', quantity: 1, unit_price: 0 });
        },

        updateItemPrice(index) {
            const select = document.querySelectorAll('select[name^="items"]')[index * 3];
            const option = select.options[select.selectedIndex];
            if (option) {
                this.items[index].unit_price = parseFloat(option.dataset.price || 0);
                this.calculateTotal();
            }
        },

        calculateTotal() {
            this.subtotal = this.items.reduce((sum, item) => sum + (item.quantity * item.unit_price), 0);
            this.totalAmount = this.subtotal + parseFloat(this.shippingFee || 0) - parseFloat(this.discountAmount || 0);
        },

        formatMoney(amount) {
            return new Intl.NumberFormat('vi-VN').format(amount || 0) + 'đ';
        },

        init() {
            this.calculateTotal();
        }
    }
}
</script>
