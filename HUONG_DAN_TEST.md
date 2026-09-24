# 🎯 HƯỚNG DẪN TEST TÍCH HỢP GHN - CHECKOUT

## ✅ ĐÃ XONG:
- Frontend: Dropdown địa chỉ có search (giống Shopee)
- Backend: Tính phí + tạo đơn vận chuyển tự động
- API: Các endpoint shipping đầy đủ

---

## 🚀 BẮT ĐẦU TEST:

### BƯỚC 1: Chạy SQL trong phpMyAdmin
Mở phpMyAdmin → Database `decornest` → Tab SQL → Paste code này:

```sql
ALTER TABLE `orders` 
ADD COLUMN `shipping_code` VARCHAR(50) NULL AFTER `shipping_fee`,
ADD COLUMN `shipping_status` VARCHAR(50) NULL AFTER `shipping_code`,
ADD COLUMN `expected_delivery` DATETIME NULL AFTER `shipping_status`,
ADD COLUMN `shipping_service_id` INT NULL AFTER `expected_delivery`,
ADD COLUMN `shipping_province_id` INT NULL AFTER `shipping_city`,
ADD COLUMN `shipping_district_id` INT NULL AFTER `shipping_province_id`,
ADD COLUMN `shipping_ward_code` VARCHAR(20) NULL AFTER `shipping_district_id`,
ADD INDEX `idx_shipping_code` (`shipping_code`),
ADD INDEX `idx_shipping_status` (`shipping_status`);
```

Click **Go** để chạy.

---

### BƯỚC 2: Test kiểm tra

Truy cập:
```
http://localhost/project-ecommerce/public/test-ghn-integration.php
```

Xem kết quả:
- ✅ Tất cả xanh → OK, qua bước 3
- ❌ Có đỏ → Xem lỗi và fix

---

### BƯỚC 3: Test checkout thực tế

1. Thêm sản phẩm vào giỏ → Checkout
2. Chọn địa chỉ từ dropdown:
   - Chọn Tỉnh → Quận → Phường
   - Phí ship tự động hiện ra
3. Điền đầy đủ thông tin → Đặt hàng
4. Kiểm tra database bảng `orders`:
   - Có `shipping_code` không?
   - Có `shipping_province_id`, `shipping_district_id`, `shipping_ward_code` không?

---

## ❓ NẾU CÓ LỖI:

### Lỗi "Column not found"
→ Bạn chưa chạy SQL ở BƯỚC 1

### Dropdown không load
→ Kiểm tra file:
- `public/api/shipping/provinces.php`
- `public/api/shipping/districts.php`
- `public/api/shipping/wards.php`

### Phí ship luôn 50k
→ Chạy file test ở BƯỚC 2 xem API GHN có hoạt động không

---

## 📞 HỖ TRỢ:

File test chi tiết: `public/test-ghn-integration.php`
File hướng dẫn đầy đủ: `GHN_CHECKOUT_COMPLETED.md`
