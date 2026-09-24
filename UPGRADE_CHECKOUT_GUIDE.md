# 🚀 NÂNG CẤP FORM CHECKOUT CHUYÊN NGHIỆP

## ❌ VẤN ĐỀ HIỆN TẠI:

Khách hàng phải **TỰ GÕ TAY** địa chỉ → Dễ sai, không chuyên nghiệp!

## ✅ GIẢI PHÁP:

Dùng **DROPDOWN CÓ TÌM KIẾM** với API GHN → Giống Shopee, Tiki, Lazada!

---

## 📝 CÁCH NÂNG CẤP:

### **Bước 1: Thay thế INPUT TEXT bằng SELECT DROPDOWN**

**Cũ (không tốt):**
```html
<input type="text" name="shipping_city" placeholder="Hà Nội / TP. HCM...">
<input type="text" name="shipping_district" placeholder="Đống Đa, Ba Đình...">
```

**Mới (chuyên nghiệp):**
```html
<select name="province_id" id="province-select" required>
    <option value="">Chọn Tỉnh/Thành phố</option>
</select>

<select name="district_id" id="district-select" required>
    <option value="">Chọn Quận/Huyện</option>
</select>

<select name="ward_code" id="ward-select" required>
    <option value="">Chọn Phường/Xã</option>
</select>
```

### **Bước 2: Thêm thư viện Select2 (dropdown có tìm kiếm)**

Thêm vào `<head>` của trang checkout:

```html
<!-- Select2 CSS -->
<link href="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/css/select2.min.css" rel="stylesheet" />

<!-- jQuery (Select2 cần jQuery) -->
<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>

<!-- Select2 JS -->
<script src="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/select2.min.js"></script>
```

### **Bước 3: Thêm JavaScript để load địa chỉ**

```javascript
<script>
$(document).ready(function() {
    const BASE_URL = '<?= $baseUrl ?>';
    
    // 1. Khởi tạo Select2
    $('#province-select, #district-select, #ward-select').select2({
        placeholder: "Tìm kiếm...",
        language: {
            noResults: () => "Không tìm thấy kết quả",
            searching: () => "Đang tìm kiếm..."
        }
    });
    
    // 2. Load danh sách tỉnh/thành
    loadProvinces();
    
    function loadProvinces() {
        $.get(BASE_URL + '/api/shipping/provinces.php', function(response) {
            if (response.success) {
                const select = $('#province-select');
                select.empty().append('<option value="">Chọn Tỉnh/Thành phố</option>');
                
                response.data.forEach(province => {
                    select.append(new Option(province.ProvinceName, province.ProvinceID));
                });
            }
        });
    }
    
    // 3. Khi chọn tỉnh → Load quận/huyện
    $('#province-select').on('change', function() {
        const provinceId = $(this).val();
        if (!provinceId) return;
        
        // Reset quận và phường
        $('#district-select').empty().append('<option value="">Chọn Quận/Huyện</option>').prop('disabled', true);
        $('#ward-select').empty().append('<option value="">Chọn Phường/Xã</option>').prop('disabled', true);
        
        // Load quận/huyện
        $.get(BASE_URL + '/api/shipping/districts.php?province_id=' + provinceId, function(response) {
            if (response.success) {
                const select = $('#district-select');
                select.empty().append('<option value="">Chọn Quận/Huyện</option>');
                
                response.data.forEach(district => {
                    select.append(new Option(district.DistrictName, district.DistrictID));
                });
                
                select.prop('disabled', false);
            }
        });
    });
    
    // 4. Khi chọn quận → Load phường/xã
    $('#district-select').on('change', function() {
        const districtId = $(this).val();
        if (!districtId) return;
        
        // Reset phường
        $('#ward-select').empty().append('<option value="">Chọn Phường/Xã</option>').prop('disabled', true);
        
        // Load phường/xã
        $.get(BASE_URL + '/api/shipping/wards.php?district_id=' + districtId, function(response) {
            if (response.success) {
                const select = $('#ward-select');
                select.empty().append('<option value="">Chọn Phường/Xã</option>');
                
                response.data.forEach(ward => {
                    select.append(new Option(ward.WardName, ward.WardCode));
                });
                
                select.prop('disabled', false);
            }
        });
    });
    
    // 5. Khi chọn phường → Tính phí ship
    $('#ward-select').on('change', function() {
        const wardCode = $(this).val();
        const districtId = $('#district-select').val();
        
        if (!wardCode || !districtId) return;
        
        // Tính phí vận chuyển
        $.ajax({
            url: BASE_URL + '/api/shipping/calculate-fee.php',
            method: 'POST',
            contentType: 'application/json',
            data: JSON.stringify({
                district_id: parseInt(districtId),
                ward_code: wardCode,
                weight: 5000, // 5kg mặc định
                order_value: <?= (int)$subtotal ?>
            }),
            success: function(response) {
                if (response.success) {
                    // Hiển thị phí ship
                    $('#shipping-fee-display').text(formatMoney(response.fee) + 'đ');
                    $('#shipping-fee-input').val(response.fee);
                }
            }
        });
    });
    
    function formatMoney(amount) {
        return new Intl.NumberFormat('vi-VN').format(amount);
    }
});
</script>
```

---

## 🎨 CUSTOM CSS (OPTIONAL - để đẹp hơn)

```css
/* Custom Select2 theo style DecorNest */
.select2-container--default .select2-selection--single {
    background-color: rgba(245, 241, 232, 0.4);
    border: 1px solid #E8DCC4;
    border-radius: 0.75rem;
    height: 46px;
    padding: 8px 12px;
}

.select2-container--default .select2-selection--single .select2-selection__rendered {
    color: #2C2C2C;
    line-height: 28px;
    font-size: 0.875rem;
}

.select2-container--default.select2-container--open .select2-selection--single {
    border-color: #8B7355;
    background-color: white;
}

.select2-dropdown {
    border: 1px solid #8B7355;
    border-radius: 0.75rem;
    box-shadow: 0 10px 25px rgba(0,0,0,0.1);
}

.select2-results__option--highlighted {
    background-color: #8B7355 !important;
}
```

---

## 📊 SO SÁNH:

| Tính năng | Cũ (Input Text) | Mới (Dropdown + API) |
|-----------|-----------------|----------------------|
| Tìm kiếm | ❌ Không | ✅ Có |
| Autocomplete | ❌ Không | ✅ Có |
| Chính xác | ⚠️ Thấp (khách gõ sai) | ✅ Cao (chọn từ danh sách) |
| Tính phí ship tự động | ❌ Không | ✅ Có |
| UX chuyên nghiệp | ❌ Không | ✅ Có |
| Giống Shopee/Tiki | ❌ Không | ✅ Có |

---

## ✅ KẾT QUẢ SAU KHI NÂNG CẤP:

1. ✅ Dropdown Tỉnh/Thành có **63 tỉnh Việt Nam**
2. ✅ Dropdown Quận/Huyện **tự động load** theo tỉnh
3. ✅ Dropdown Phường/Xã **tự động load** theo quận
4. ✅ **Tìm kiếm nhanh** trong dropdown
5. ✅ **Tính phí ship tự động** khi chọn xong địa chỉ
6. ✅ **Không thể nhập sai** địa chỉ
7. ✅ Trải nghiệm **giống Shopee, Tiki, Lazada**

---

## 🚀 CÁCH ÁP DỤNG NHANH:

### Option 1: Thay thế toàn bộ file checkout
```bash
# Backup file cũ
mv app/views/frontend/pages/checkout.php app/views/frontend/pages/checkout-old.php

# Dùng file mới
mv app/views/frontend/pages/checkout-new.php app/views/frontend/pages/checkout.php
```

### Option 2: Chỉnh sửa file hiện tại
1. Mở `app/views/frontend/pages/checkout.php`
2. Tìm phần input `shipping_city`, `shipping_district`
3. Thay bằng `<select>` như hướng dẫn trên
4. Thêm JavaScript và Select2

---

## 📝 LƯU Ý:

1. **API đã sẵn sàng:** Các file `/api/shipping/*.php` đã được tạo
2. **GHN Token:** Đã config trong `.env`
3. **Test ngay:** Sau khi cập nhật, test form checkout xem dropdown có hoạt động không

---

**Tạo bởi:** Kiro AI  
**Ngày:** 24/09/2026
