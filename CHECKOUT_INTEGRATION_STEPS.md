# 🛒 CÁC BƯỚC TÍCH HỢP GHN VÀO CHECKOUT

## ✅ ĐÃ XONG:
1. ✅ API GHN hoạt động
2. ✅ Test thành công
3. ✅ Backup file checkout cũ

---

## 📝 CẦN LÀM TIẾP:

### **BƯỚC 1: Thêm Select2 vào checkout.php**

Thêm vào đầu file (sau dòng `$pageTitle`):

```php
<!-- Select2 for searchable dropdowns -->
<link href="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/css/select2.min.css" rel="stylesheet" />
<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/select2.min.js"></script>
```

### **BƯỚC 2: Thay INPUT TEXT bằng SELECT**

**TÌM (dòng ~88-103):**
```html
<div>
    <label>Tỉnh / Thành phố *</label>
    <input type="text" name="shipping_city" required ...>
</div>
<div>
    <label>Quận / Huyện</label>
    <input type="text" name="shipping_district" ...>
</div>
```

**THAY BẰNG:**
```html
<div>
    <label class="block text-[11px] font-semibold text-muted uppercase tracking-wider mb-1.5">
        Tỉnh / Thành phố *
    </label>
    <select name="province_id" id="province-select" required
            class="w-full bg-cream/40 border border-beige rounded-xl px-4 py-3 text-xs sm:text-sm">
        <option value="">Chọn Tỉnh/Thành phố</option>
    </select>
    <input type="hidden" name="shipping_city" id="shipping-city">
    <input type="hidden" name="shipping_province_id" id="shipping-province-id">
</div>

<div>
    <label class="block text-[11px] font-semibold text-muted uppercase tracking-wider mb-1.5">
        Quận / Huyện *
    </label>
    <select name="district_id" id="district-select" required disabled
            class="w-full bg-cream/40 border border-beige rounded-xl px-4 py-3 text-xs sm:text-sm">
        <option value="">Chọn Quận/Huyện</option>
    </select>
    <input type="hidden" name="shipping_district" id="shipping-district">
    <input type="hidden" name="shipping_district_id" id="shipping-district-id">
</div>

<div>
    <label class="block text-[11px] font-semibold text-muted uppercase tracking-wider mb-1.5">
        Phường / Xã *
    </label>
    <select name="ward_code" id="ward-select" required disabled
            class="w-full bg-cream/40 border border-beige rounded-xl px-4 py-3 text-xs sm:text-sm">
        <option value="">Chọn Phường/Xã</option>
    </select>
    <input type="hidden" name="shipping_ward" id="shipping-ward">
</div>
```

### **BƯỚC 3: Thêm JavaScript load địa chỉ**

Thêm VÀO CUỐI FILE (trước `</div>` cuối):

```javascript
<script>
$(document).ready(function() {
    const BASE_URL = '<?= $baseUrl ?>';
    let currentShippingFee = <?= (float)$shippingFee ?>;
    
    // Initialize Select2
    $('#province-select, #district-select, #ward-select').select2({
        placeholder: 'Chọn...',
        language: {
            noResults: () => "Không tìm thấy",
            searching: () => "Đang tìm..."
        }
    });
    
    // Load provinces
    $.get(BASE_URL + '/api/shipping/provinces.php', function(res) {
        if (res.success) {
            const sel = $('#province-select');
            sel.empty().append('<option value="">Chọn Tỉnh/Thành phố</option>');
            res.data.forEach(p => sel.append(new Option(p.ProvinceName, p.ProvinceID)));
        }
    });
    
    // Province change → Load districts
    $('#province-select').on('change', function() {
        const id = $(this).val();
        const name = $(this).find('option:selected').text();
        $('#shipping-city').val(name);
        $('#shipping-province-id').val(id);
        
        $('#district-select').empty().append('<option value="">Chọn Quận/Huyện</option>').prop('disabled', true);
        $('#ward-select').empty().append('<option value="">Chọn Phường/Xã</option>').prop('disabled', true);
        
        if (!id) return;
        
        $.get(BASE_URL + '/api/shipping/districts.php?province_id=' + id, function(res) {
            if (res.success) {
                const sel = $('#district-select');
                sel.empty().append('<option value="">Chọn Quận/Huyện</option>');
                res.data.forEach(d => sel.append(new Option(d.DistrictName, d.DistrictID)));
                sel.prop('disabled', false);
            }
        });
    });
    
    // District change → Load wards
    $('#district-select').on('change', function() {
        const id = $(this).val();
        const name = $(this).find('option:selected').text();
        $('#shipping-district').val(name);
        $('#shipping-district-id').val(id);
        
        $('#ward-select').empty().append('<option value="">Chọn Phường/Xã</option>').prop('disabled', true);
        
        if (!id) return;
        
        $.get(BASE_URL + '/api/shipping/wards.php?district_id=' + id, function(res) {
            if (res.success) {
                const sel = $('#ward-select');
                sel.empty().append('<option value="">Chọn Phường/Xã</option>');
                res.data.forEach(w => sel.append(new Option(w.WardName, w.WardCode)));
                sel.prop('disabled', false);
            }
        });
    });
    
    // Ward change → Calculate shipping fee
    $('#ward-select').on('change', function() {
        const code = $(this).val();
        const name = $(this).find('option:selected').text();
        const districtId = $('#district-select').val();
        
        $('#shipping-ward').val(name);
        
        if (!code || !districtId) return;
        
        // Calculate shipping fee
        $.ajax({
            url: BASE_URL + '/api/shipping/calculate-fee.php',
            method: 'POST',
            contentType: 'application/json',
            data: JSON.stringify({
                district_id: parseInt(districtId),
                ward_code: code,
                weight: 5000,
                order_value: <?= (int)$subtotal ?>
            }),
            success: function(res) {
                if (res.success && res.fee) {
                    currentShippingFee = res.fee;
                    // Update Alpine.js data
                    const form = document.querySelector('#checkout-form');
                    if (form.__x) {
                        form.__x.$data.shippingFee = currentShippingFee;
                    }
                }
            }
        });
    });
});
</script>
```

---

## 🎯 HOẶC CÁCH NHANH HƠN:

Thay file `checkout.php` bằng file demo đã test thành công:

```bash
# Đã backup rồi, giờ thay file
copy public\demo-address-dropdown.php app\views\frontend\pages\checkout-ghn-demo.php
```

Sau đó chỉnh sửa `checkout-ghn-demo.php` cho đúng với layout DecorNest.

---

## ✅ SAU KHI XONG FORM, CẦN CẬP NHẬT:

### **CheckoutController.php** - Lưu thông tin GHN vào database:

```php
// Trong CheckoutController::process()

// Lưu thông tin địa chỉ GHN
$orderData['shipping_province_id'] = $post['province_id'] ?? null;
$orderData['shipping_district_id'] = $post['district_id'] ?? null;
$orderData['shipping_ward_code'] = $post['ward_code'] ?? null;

// Tạo đơn vận chuyển (optional - sau khi đơn được xác nhận)
if ($paymentMethod !== 'cod') {
    $ghn = new \App\Services\GhnShippingService();
    $shippingOrder = $ghn->createShippingOrder([
        'to_name' => $shippingName,
        'to_phone' => $shippingPhone,
        'to_address' => $shippingAddress,
        'to_ward_code' => $post['ward_code'],
        'to_district_id' => $post['district_id'],
        'weight' => 5000,
        'payment_type_id' => 1,
        'items' => $items,
    ]);
    
    if ($shippingOrder['success']) {
        $orderData['shipping_code'] = $shippingOrder['order_code'];
        $orderData['expected_delivery'] = $shippingOrder['expected_delivery_time'];
    }
}
```

---

Bạn muốn tôi làm cách nào?
1. Tạo file checkout mới hoàn chỉnh
2. Hướng dẫn sửa từng dòng file hiện tại
