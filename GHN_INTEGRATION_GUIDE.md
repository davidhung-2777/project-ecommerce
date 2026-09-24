# 🚚 HƯỚNG DẪN TÍCH HỢP GIAO HÀNG NHANH (GHN)

## 📋 BƯỚC 1: LẤY API TOKEN & SHOP ID

### Truy cập GHN Developer Portal:
1. Đăng nhập: https://khachhang.ghn.vn/
2. Vào menu **"Cài đặt"** → **"Tích hợp API"**
3. Hoặc truy cập trực tiếp: https://khachhang.ghn.vn/?page=api_management

### Lấy Token:
1. Click **"Tạo Token"** hoặc copy token có sẵn
2. Token có dạng: `xxxxxxxx-xxxx-xxxx-xxxx-xxxxxxxxxxxx`

### Lấy Shop ID:
1. Vào **"Quản lý Shop"**
2. Copy **Shop ID** (dạng số: `123456`)

### Lấy mã kho hàng (quan trọng!):
1. Trong phần **"Quản lý Shop"**, xem địa chỉ kho
2. Note lại:
   - **Province ID** (Tỉnh/Thành)
   - **District ID** (Quận/Huyện)  
   - **Ward Code** (Phường/Xã)

---

## ⚙️ BƯỚC 2: CẤU HÌNH DỰ ÁN

### Cập nhật file `.env`:

```env
# Shipping - Giao Hàng Nhanh (GHN)
GHN_API_TOKEN=a1b2c3d4-e5f6-g7h8-i9j0-k1l2m3n4o5p6
GHN_SHOP_ID=123456
GHN_API_URL=https://dev-online-gateway.ghn.vn/shiip/public-api

# Thông tin kho hàng
GHN_FROM_DISTRICT_ID=1542
# 1542 = Quận Ba Đình, Hà Nội
GHN_FROM_WARD_CODE=20308
# Mã phường/xã kho hàng
```

**Lưu ý:** 
- Môi trường TEST: `https://dev-online-gateway.ghn.vn/shiip/public-api`
- Môi trường PRODUCTION: `https://online-gateway.ghn.vn/shiip/public-api`

---

## 🗄️ BƯỚC 3: CẬP NHẬT DATABASE

Chạy migration để thêm cột vận chuyển:

```bash
mysql -u root -p decornest < config/migrations/add_shipping_columns.sql
```

Hoặc import thủ công trong phpMyAdmin:

```sql
ALTER TABLE `orders` 
ADD COLUMN `shipping_code` VARCHAR(50) NULL AFTER `shipping_fee`,
ADD COLUMN `shipping_status` VARCHAR(50) NULL AFTER `shipping_code`,
ADD COLUMN `expected_delivery` DATETIME NULL AFTER `shipping_status`,
ADD COLUMN `shipping_service_id` INT NULL AFTER `expected_delivery`,
ADD COLUMN `shipping_province_id` INT NULL AFTER `shipping_city`,
ADD COLUMN `shipping_district_id` INT NULL AFTER `shipping_province_id`,
ADD COLUMN `shipping_ward_code` VARCHAR(20) NULL AFTER `shipping_district_id`;
```

---

## 🧪 BƯỚC 4: TEST API

### Test cơ bản:
```
http://localhost/project-ecommerce/public/test-ghn.php
```

Trang này sẽ kiểm tra:
- ✅ Kết nối API
- ✅ Lấy danh sách tỉnh/thành
- ✅ Lấy danh sách quận/huyện
- ✅ Tính phí vận chuyển
- ✅ Lấy dịch vụ khả dụng

### Test API endpoints:

1. **Lấy tỉnh/thành:**
```
GET http://localhost/project-ecommerce/public/api/shipping/provinces.php
```

2. **Lấy quận/huyện:**
```
GET http://localhost/project-ecommerce/public/api/shipping/districts.php?province_id=201
```

3. **Lấy phường/xã:**
```
GET http://localhost/project-ecommerce/public/api/shipping/wards.php?district_id=1542
```

4. **Tính phí ship:**
```
POST http://localhost/project-ecommerce/public/api/shipping/calculate-fee.php
Content-Type: application/json

{
  "district_id": 1451,
  "ward_code": "20311",
  "weight": 5000,
  "order_value": 5000000
}
```

---

## 🎨 BƯỚC 5: TÍCH HỢP VÀO CHECKOUT

### A. Cập nhật form checkout với dropdown địa chỉ:

File: `app/views/frontend/pages/checkout.php`

Thay thế input text địa chỉ bằng dropdown:

```html
<!-- Tỉnh/Thành -->
<div>
    <label>Tỉnh/Thành phố *</label>
    <select name="province_id" required 
            @change="loadDistricts($event.target.value)">
        <option value="">Chọn tỉnh/thành phố</option>
    </select>
</div>

<!-- Quận/Huyện -->
<div>
    <label>Quận/Huyện *</label>
    <select name="district_id" required 
            @change="loadWards($event.target.value); calculateShipping()">
        <option value="">Chọn quận/huyện</option>
    </select>
</div>

<!-- Phường/Xã -->
<div>
    <label>Phường/Xã *</label>
    <select name="ward_code" required 
            @change="calculateShipping()">
        <option value="">Chọn phường/xã</option>
    </select>
</div>
```

### B. Thêm JavaScript để load địa chỉ:

```javascript
<script>
function checkoutData() {
    return {
        provinces: [],
        districts: [],
        wards: [],
        shippingFee: 50000,
        
        async init() {
            // Load danh sách tỉnh/thành
            await this.loadProvinces();
        },
        
        async loadProvinces() {
            const response = await fetch('/project-ecommerce/public/api/shipping/provinces.php');
            const data = await response.json();
            if (data.success) {
                this.provinces = data.data;
            }
        },
        
        async loadDistricts(provinceId) {
            if (!provinceId) return;
            
            const response = await fetch(`/project-ecommerce/public/api/shipping/districts.php?province_id=${provinceId}`);
            const data = await response.json();
            if (data.success) {
                this.districts = data.data;
                this.wards = []; // Reset wards
            }
        },
        
        async loadWards(districtId) {
            if (!districtId) return;
            
            const response = await fetch(`/project-ecommerce/public/api/shipping/wards.php?district_id=${districtId}`);
            const data = await response.json();
            if (data.success) {
                this.wards = data.data;
            }
        },
        
        async calculateShipping() {
            const districtId = document.querySelector('[name="district_id"]').value;
            const wardCode = document.querySelector('[name="ward_code"]').value;
            
            if (!districtId || !wardCode) return;
            
            const response = await fetch('/project-ecommerce/public/api/shipping/calculate-fee.php', {
                method: 'POST',
                headers: { 'Content-Type': 'application/json' },
                body: JSON.stringify({
                    district_id: parseInt(districtId),
                    ward_code: wardCode,
                    weight: 5000, // 5kg mặc định
                    order_value: this.subtotal
                })
            });
            
            const data = await response.json();
            if (data.success) {
                this.shippingFee = data.fee;
            }
        }
    };
}
</script>
```

---

## 📦 BƯỚC 6: TẠO ĐƠN VẬN CHUYỂN

### Cập nhật `CheckoutController.php`:

```php
use App\Services\GhnShippingService;

public function process()
{
    // ... code xử lý checkout hiện tại
    
    // Tạo đơn hàng trong database
    $orderId = $this->orderModel->create($orderData);
    
    // Tạo đơn vận chuyển trên GHN
    $ghn = new GhnShippingService();
    
    $shippingResult = $ghn->createShippingOrder([
        'to_name' => $shippingName,
        'to_phone' => $shippingPhone,
        'to_address' => $shippingAddress,
        'to_ward_code' => $wardCode,
        'to_district_id' => $districtId,
        'weight' => $this->calculateTotalWeight($items), // Tính tổng cân nặng
        'payment_type_id' => $paymentMethod === 'cod' ? 2 : 1,
        'cod_amount' => $paymentMethod === 'cod' ? $totalAmount : 0,
        'items' => $this->formatItemsForGhn($items),
    ]);
    
    // Lưu mã vận đơn
    if ($shippingResult['success']) {
        $this->orderModel->update($orderId, [
            'shipping_code' => $shippingResult['order_code'],
            'expected_delivery' => $shippingResult['expected_delivery_time'],
            'shipping_status' => 'ready_to_pick',
        ]);
    }
    
    // ... redirect về trang success
}

private function calculateTotalWeight($items): int
{
    // Tính tổng cân nặng (gram)
    $totalWeight = 0;
    foreach ($items as $item) {
        $product = $this->productModel->find($item['product_id']);
        $weight = $product['weight'] ?? 1000; // mặc định 1kg nếu không có
        $totalWeight += $weight * $item['quantity'];
    }
    return $totalWeight;
}

private function formatItemsForGhn($items): array
{
    $ghnItems = [];
    foreach ($items as $item) {
        $product = $this->productModel->find($item['product_id']);
        $ghnItems[] = [
            'name' => $product['name'],
            'quantity' => $item['quantity'],
            'price' => (int) $product['price'],
        ];
    }
    return $ghnItems;
}
```

---

## 🔍 BƯỚC 7: TRACKING ĐƠN HÀNG

### Tạo trang tracking:

File: `app/views/frontend/pages/order-tracking.php`

```php
<?php
use App\Services\GhnShippingService;

$orderCode = $_GET['code'] ?? '';

if ($orderCode) {
    $ghn = new GhnShippingService();
    $tracking = $ghn->trackOrder($orderCode);
}
?>

<div class="tracking-container">
    <h2>Theo dõi đơn hàng: <?= htmlspecialchars($orderCode) ?></h2>
    
    <?php if ($tracking['success']): ?>
        <div class="tracking-status">
            <p><strong>Trạng thái:</strong> <?= $tracking['data']['status'] ?? 'N/A' ?></p>
            <p><strong>Thời gian dự kiến:</strong> <?= $tracking['data']['expected_delivery_time'] ?? 'N/A' ?></p>
        </div>
        
        <div class="tracking-timeline">
            <h3>Lịch sử vận chuyển</h3>
            <?php foreach ($tracking['data']['log'] ?? [] as $log): ?>
                <div class="timeline-item">
                    <p class="time"><?= $log['updated_date'] ?? '' ?></p>
                    <p class="status"><?= $log['status_name'] ?? '' ?></p>
                </div>
            <?php endforeach; ?>
        </div>
    <?php else: ?>
        <div class="error">Không tìm thấy thông tin vận chuyển</div>
    <?php endif; ?>
</div>
```

---

## 📊 BƯỚC 8: QUẢN LÝ ADMIN

### Thêm vào trang order detail của admin:

```php
<!-- Thông tin vận chuyển -->
<?php if ($order['shipping_code']): ?>
    <div class="box">
        <h3>🚚 Thông tin vận chuyển</h3>
        <p><strong>Mã vận đơn:</strong> <?= htmlspecialchars($order['shipping_code']) ?></p>
        <p><strong>Trạng thái:</strong> 
            <span class="badge"><?= htmlspecialchars($order['shipping_status'] ?? 'N/A') ?></span>
        </p>
        <p><strong>Dự kiến giao:</strong> <?= $order['expected_delivery'] ?? 'N/A' ?></p>
        
        <div class="actions">
            <a href="/admin/orders/tracking/<?= $order['id'] ?>" class="btn">
                Xem chi tiết vận chuyển
            </a>
            <a href="/admin/orders/print-shipping/<?= $order['id'] ?>" class="btn" target="_blank">
                In phiếu giao hàng
            </a>
        </div>
    </div>
<?php endif; ?>
```

---

## 🎯 TÓM TẮT CÁC API ĐÃ IMPLEMENT

| API | Endpoint | Mô tả | Status |
|-----|----------|-------|--------|
| Lấy tỉnh/thành | `GET /api/shipping/provinces.php` | Danh sách tỉnh | ✅ |
| Lấy quận/huyện | `GET /api/shipping/districts.php` | Danh sách quận | ✅ |
| Lấy phường/xã | `GET /api/shipping/wards.php` | Danh sách phường | ✅ |
| Tính phí ship | `POST /api/shipping/calculate-fee.php` | Tính phí vận chuyển | ✅ |
| Tạo đơn | `GhnShippingService::createShippingOrder()` | Tạo đơn GHN | ✅ |
| Tracking | `GhnShippingService::trackOrder()` | Theo dõi đơn | ✅ |
| Hủy đơn | `GhnShippingService::cancelOrder()` | Hủy vận đơn | ✅ |
| Cập nhật COD | `GhnShippingService::updateCOD()` | Sửa số tiền COD | ✅ |
| In phiếu | `GhnShippingService::printOrder()` | In phiếu giao hàng | ✅ |

---

## 🚀 CHECKLIST HOÀN THÀNH

- [ ] Lấy Token và Shop ID từ GHN
- [ ] Cập nhật file `.env`
- [ ] Chạy migration database
- [ ] Test API qua `test-ghn.php`
- [ ] Cập nhật form checkout với dropdown địa chỉ
- [ ] Thêm tính phí ship tự động
- [ ] Tạo đơn vận chuyển khi checkout
- [ ] Thêm trang tracking cho khách hàng
- [ ] Thêm quản lý vận chuyển cho admin
- [ ] Test end-to-end

---

## 📚 TÀI LIỆU THAM KHẢO

- API Documentation: https://api.ghn.vn/home/docs/detail
- Developer Portal: https://khachhang.ghn.vn/
- Support: https://ghn.vn/

---

**Tạo bởi:** Kiro AI  
**Ngày:** 24/09/2026
