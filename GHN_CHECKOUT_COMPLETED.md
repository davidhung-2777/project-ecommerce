# ✅ HOÀN THÀNH TÍCH HỢP GHN VÀO CHECKOUT

## 📝 ĐÃ HOÀN THÀNH:

### 1. Frontend (Giao diện Checkout) ✅
- ✅ Thay input text bằng dropdown có search (Select2)
- ✅ Dropdown tỉnh/quận/phường cascade (chọn tỉnh → load quận → load phường)
- ✅ Tự động tính phí ship khi chọn xong địa chỉ
- ✅ Lưu cả tên và ID của địa chỉ (để gửi lên backend)

### 2. Backend (CheckoutController) ✅
- ✅ Import `GhnShippingService` vào controller
- ✅ Khởi tạo `$ghnService` trong constructor
- ✅ Cập nhật phương thức `calculateShipping()`:
  - Nhận `$districtId` và `$wardCode` từ form
  - Gọi GHN API để tính phí chính xác
  - Fallback về phí mặc định 50k nếu API lỗi
  - Vẫn giữ logic free ship >= 5 triệu
- ✅ Cập nhật phương thức `process()`:
  - Lấy thông tin địa chỉ GHN từ POST data
  - Lưu `district_id`, `ward_code`, `province_id` vào database
  - Gọi `createGhnShippingOrder()` để tạo đơn vận chuyển
  - Lưu `shipping_code` và `expected_delivery` từ GHN
- ✅ Thêm phương thức `createGhnShippingOrder()`:
  - Chuẩn bị danh sách sản phẩm
  - Tính tổng trọng lượng
  - Xử lý COD vs Non-COD
  - Gọi GHN API tạo đơn vận chuyển

### 3. Database Migration ⚠️
- ✅ File SQL đã tạo: `config/migrations/add_shipping_columns.sql`
- ⚠️ **USER CẦN CHẠY FILE NÀY TRONG phpMyAdmin**

---

## 🎯 CÁC BƯỚC TIẾP THEO:

### BƯỚC 1: Chạy Migration Database (BẮT BUỘC)

Mở phpMyAdmin → Chọn database `decornest` → Vào tab SQL → Copy và chạy:

```sql
-- File: config/migrations/add_shipping_columns.sql

ALTER TABLE `orders` 
ADD COLUMN `shipping_code` VARCHAR(50) NULL COMMENT 'Mã vận đơn GHN' AFTER `shipping_fee`,
ADD COLUMN `shipping_status` VARCHAR(50) NULL COMMENT 'Trạng thái vận chuyển' AFTER `shipping_code`,
ADD COLUMN `expected_delivery` DATETIME NULL COMMENT 'Thời gian giao hàng dự kiến' AFTER `shipping_status`,
ADD COLUMN `shipping_service_id` INT NULL COMMENT 'Loại dịch vụ GHN' AFTER `expected_delivery`,
ADD COLUMN `shipping_province_id` INT NULL COMMENT 'Mã tỉnh/thành GHN' AFTER `shipping_city`,
ADD COLUMN `shipping_district_id` INT NULL COMMENT 'Mã quận/huyện GHN' AFTER `shipping_province_id`,
ADD COLUMN `shipping_ward_code` VARCHAR(20) NULL COMMENT 'Mã phường/xã GHN' AFTER `shipping_district_id`,
ADD INDEX `idx_shipping_code` (`shipping_code`),
ADD INDEX `idx_shipping_status` (`shipping_status`);
```

### BƯỚC 2: Test Tích Hợp

Mở trình duyệt và truy cập:

```
http://localhost/project-ecommerce/public/test-ghn-integration.php
```

File này sẽ kiểm tra:
- ✅ Database đã có đủ cột chưa
- ✅ GHN API hoạt động không
- ✅ CheckoutController khởi tạo thành công không

### BƯỚC 3: Test Checkout Thực Tế

1. Thêm sản phẩm vào giỏ hàng
2. Vào trang checkout: `/checkout`
3. Điền thông tin:
   - Chọn **Tỉnh/Thành phố** từ dropdown
   - Chọn **Quận/Huyện** (sẽ load tự động sau khi chọn tỉnh)
   - Chọn **Phường/Xã** (sẽ load tự động sau khi chọn quận)
   - Phí ship sẽ hiển thị tự động
4. Hoàn tất đơn hàng
5. Kiểm tra trong database bảng `orders`:
   - Cột `shipping_code` có mã vận đơn GHN chưa?
   - Cột `shipping_province_id`, `shipping_district_id`, `shipping_ward_code` có giá trị chưa?

---

## 🔍 CÁC TÍNH NĂNG ĐÃ TÍCH HỢP:

### 1. Tính Phí Vận Chuyển Tự Động
```
- Phí được tính từ GHN API dựa trên:
  ✓ Địa chỉ giao hàng (tỉnh/quận/phường)
  ✓ Trọng lượng ước tính (5kg)
  ✓ Giá trị đơn hàng
- Vẫn áp dụng chính sách free ship >= 5 triệu
- Fallback về phí 50k nếu API lỗi
```

### 2. Tạo Đơn Vận Chuyển Tự Động
```
- Tự động tạo đơn với GHN sau khi khách đặt hàng
- Phân biệt COD và Non-COD:
  ✓ COD: Khách trả phí ship + tiền hàng khi nhận
  ✓ Non-COD: Shop trả phí ship
- Lưu mã vận đơn vào database
- Lưu thời gian giao hàng dự kiến
```

### 3. Dropdown Địa Chỉ Chuyên Nghiệp
```
- Giống Shopee/Tiki
- Có chức năng tìm kiếm (Select2)
- Load dữ liệu cascade (tỉnh → quận → phường)
- Validate đầy đủ
```

---

## 🚀 CÁC TÍNH NĂNG NÂNG CAO (TÙY CHỌN):

### 1. Hiển thị Tracking trong Admin
File: `app/views/admin/pages/order-detail.php`

Thêm section hiển thị thông tin vận chuyển:

```php
<?php if (!empty($order['shipping_code'])): ?>
<div class="shipping-info">
    <h3>🚚 Thông tin vận chuyển</h3>
    <p><strong>Mã vận đơn:</strong> <?= $order['shipping_code'] ?></p>
    <p><strong>Trạng thái:</strong> <?= $order['shipping_status'] ?? 'Đang xử lý' ?></p>
    <?php if (!empty($order['expected_delivery'])): ?>
    <p><strong>Dự kiến giao:</strong> <?= date('d/m/Y H:i', strtotime($order['expected_delivery'])) ?></p>
    <?php endif; ?>
    
    <a href="https://donhang.ghn.vn/?order_code=<?= $order['shipping_code'] ?>" 
       target="_blank" class="btn btn-info">
        🔍 Tracking đơn hàng
    </a>
</div>
<?php endif; ?>
```

### 2. In Phiếu Giao Hàng
File: `app/controllers/AdminController.php`

Thêm method:

```php
public function printShippingLabel(int $orderId): void
{
    $order = $this->orderModel->find($orderId);
    if (!$order || empty($order['shipping_code'])) {
        $this->setFlash('error', 'Không tìm thấy mã vận đơn');
        $this->redirect($this->baseUrl('admin/orders'));
    }
    
    $ghn = new \App\Services\GhnShippingService();
    $printUrl = $ghn->printOrder([$order['shipping_code']]);
    
    $this->redirect($printUrl);
}
```

### 3. Webhook GHN (Cập nhật trạng thái tự động)
File: `public/api/ghn-webhook.php`

```php
<?php
// Nhận webhook từ GHN khi trạng thái đơn hàng thay đổi
$data = json_decode(file_get_contents('php://input'), true);

if (!empty($data['OrderCode'])) {
    $db = \App\Core\Database::getInstance();
    
    $stmt = $db->prepare("
        UPDATE orders 
        SET shipping_status = :status 
        WHERE shipping_code = :code
    ");
    
    $stmt->execute([
        'status' => $data['Status'] ?? 'unknown',
        'code' => $data['OrderCode'],
    ]);
    
    http_response_code(200);
    echo json_encode(['success' => true]);
}
```

---

## 🧪 TROUBLESHOOTING:

### Lỗi: "Token is not valid"
→ Kiểm tra file `.env`:
```
GHN_API_TOKEN=6b686231-46ec-4a77-9b04-12df97863021
GHN_SHOP_ID=6686325
GHN_API_URL=https://online-gateway.ghn.vn/shiip/public-api
```

### Lỗi: "Column 'shipping_code' not found"
→ Bạn chưa chạy migration database. Xem BƯỚC 1 ở trên.

### Dropdown không load dữ liệu
→ Kiểm tra các file API:
- `public/api/shipping/provinces.php`
- `public/api/shipping/districts.php`
- `public/api/shipping/wards.php`

### Phí ship luôn là 50,000đ
→ Kiểm tra:
1. User đã chọn đầy đủ tỉnh/quận/phường chưa?
2. API `calculate-fee.php` có hoạt động không?
3. Mở Developer Tools → Network → Xem response của API call

---

## 📊 FLOW HOẠT ĐỘNG:

```
1. User vào /checkout
   ↓
2. Chọn Tỉnh → Load danh sách Quận
   ↓
3. Chọn Quận → Load danh sách Phường
   ↓
4. Chọn Phường → Tính phí ship từ GHN API
   ↓
5. User điền thông tin khác → Submit form
   ↓
6. CheckoutController::process()
   ├─ Validate input
   ├─ Tính phí ship (GHN API)
   ├─ Tạo đơn hàng (database)
   ├─ Tạo đơn vận chuyển (GHN API)
   └─ Lưu shipping_code
   ↓
7. Redirect đến trang success
```

---

## 🎉 KẾT LUẬN:

**✅ Backend tích hợp GHN đã hoàn tất!**

Các file đã được cập nhật:
- ✅ `app/controllers/CheckoutController.php` - Logic tính phí và tạo đơn
- ✅ `app/services/GhnShippingService.php` - Service GHN API
- ✅ `app/views/frontend/pages/checkout.php` - Form với dropdown địa chỉ
- ✅ `config/migrations/add_shipping_columns.sql` - Database migration
- ✅ `public/test-ghn-integration.php` - File test tích hợp

**Bước tiếp theo của bạn:**
1. Chạy migration SQL trong phpMyAdmin (BƯỚC 1)
2. Test checkout thực tế
3. Kiểm tra mã vận đơn có được tạo không

Nếu có lỗi, chạy file `test-ghn-integration.php` để debug!
