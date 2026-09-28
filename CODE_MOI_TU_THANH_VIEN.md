# 📦 CODE MỚI TỪ THÀNH VIÊN NHÓM

## 👤 Thông tin commit:
- **Người commit**: 2311063262-tech (2311063262@hunre.edu.vn)
- **Thời gian**: Wed Sep 23 23:36:25 2026
- **Commit message**: "Add voucher management and order history"
- **Commit ID**: ec5799b

---

## ✨ TÍNH NĂNG MỚI: HỆ THỐNG VOUCHER/MÃ GIẢM GIÁ

### 1. Files mới được tạo:

#### **Backend - Services & Models:**
- `app/services/VoucherService.php` - Service xử lý logic voucher
- `app/models/VoucherModel.php` - Model quản lý voucher
- `app/models/VoucherUsageModel.php` - Model theo dõi lượt sử dụng

#### **Controllers:**
- `app/controllers/VoucherController.php` - API cho frontend apply voucher

#### **Admin Views (Quản lý voucher):**
- `app/views/admin/pages/vouchers/index.php` - Danh sách voucher
- `app/views/admin/pages/vouchers/form.php` - Form tạo/sửa voucher

#### **Frontend Views:**
- `app/views/frontend/pages/cart.php` - Cập nhật: Thêm form nhập voucher
- `app/views/frontend/pages/account-orders.php` - Lịch sử đơn hàng
- `app/views/frontend/pages/order-detail.php` - Chi tiết đơn hàng
- `app/views/frontend/pages/user-detail.php` - Chi tiết user (admin)
- `app/views/frontend/pages/forbidden.php` - Trang 403

#### **Database Migration:**
- `config/migrations/add_voucher_features.sql` - Migration database voucher

---

## 🎯 TÍNH NĂNG VOUCHER CHI TIẾT:

### Các loại voucher hỗ trợ:
1. **Giảm giá theo phần trăm** (`percent`)
   - Ví dụ: Giảm 10%, 20%, 50%
   - Có thể giới hạn giảm tối đa (max_discount_amount)

2. **Giảm giá cố định** (`fixed`)
   - Ví dụ: Giảm 50,000đ, 100,000đ

### Phạm vi áp dụng:
- **Toàn shop**: Áp dụng tất cả sản phẩm
- **Theo sản phẩm**: Chỉ áp dụng sản phẩm cụ thể
- **Theo danh mục**: Áp dụng cả danh mục sản phẩm

### Điều kiện sử dụng:
- Giá trị đơn hàng tối thiểu (`min_order_value`)
- Thời gian hiệu lực (`start_date` - `end_date`)
- Giới hạn số lượt sử dụng tổng (`usage_limit`)
- Giới hạn số lượt/user (`usage_limit_per_user`)
- Trạng thái active/inactive

### Flow hoạt động:
```
1. User nhập mã voucher vào giỏ hàng
   ↓
2. Frontend gọi API: POST /voucher/apply
   ↓
3. VoucherService validate:
   - Voucher có tồn tại không?
   - Còn hiệu lực không?
   - User đã dùng hết lượt chưa?
   - Đơn hàng đủ điều kiện không?
   - Sản phẩm có áp dụng không?
   ↓
4. Trả về discount amount
   ↓
5. Checkout: Lưu voucher_id và discount_amount vào orders
   ↓
6. Tăng used_count trong bảng vouchers
   ↓
7. Lưu lịch sử vào voucher_usages
```

---

## 📊 DATABASE MỚI:

### Bảng `vouchers` (Thông tin voucher):
```sql
- id: Mã voucher
- code: Mã nhập (VD: SALE50, NEWUSER)
- description: Mô tả voucher
- discount_type: 'percent' hoặc 'fixed'
- discount_value: Giá trị giảm
- max_discount_amount: Giảm tối đa (cho percent)
- min_order_value: Đơn tối thiểu
- usage_limit: Số lượt dùng tổng
- usage_limit_per_user: Số lượt/user
- used_count: Đã dùng bao nhiêu lượt
- start_date, end_date: Thời gian hiệu lực
- is_active: Trạng thái
```

### Bảng `voucher_products` (Voucher theo sản phẩm):
```sql
- voucher_id
- product_id
```

### Bảng `voucher_categories` (Voucher theo danh mục):
```sql
- voucher_id
- category_id
```

### Bảng `voucher_usages` (Lịch sử sử dụng):
```sql
- id
- voucher_id
- user_id
- order_id
- discount_amount
- used_at
```

### Cập nhật bảng `orders`:
```sql
+ voucher_id (FK → vouchers)
+ discount_amount (DECIMAL)
```

---

## 🔄 FILES BỊ THAY ĐỔI:

### 1. `app/controllers/CheckoutController.php`
**Thay đổi:**
- Thêm `VoucherService`
- Xử lý voucher trong `process()`:
  ```php
  // Lấy voucher từ session
  $voucherCode = $_SESSION['applied_voucher_code'] ?? '';
  
  // Validate voucher
  $voucherResult = $this->voucherService->validateCode(...)
  
  // Tính tổng tiền sau giảm giá
  $totalAmount = $subtotal - $voucherDiscount + $shippingFee + $taxAmount
  
  // Lưu voucher_id và discount_amount vào orders
  $orderData['voucher_id'] = $voucher['id'] ?? null;
  $orderData['discount_amount'] = $voucherDiscount;
  
  // Commit usage (tăng used_count)
  $this->voucherService->commitUsage(...)
  ```

### 2. `app/controllers/AdminController.php`
**Thay đổi:**
- Thêm routes quản lý voucher (CRUD)

### 3. `app/controllers/UserController.php`
**Thay đổi:**
- Thêm trang lịch sử đơn hàng
- Thêm trang chi tiết đơn hàng

### 4. `app/views/frontend/pages/cart.php`
**Thay đổi:**
- Thêm form nhập mã voucher
- JavaScript gọi API `/voucher/apply`
- Hiển thị discount amount

### 5. `app/views/admin/layouts/main.php`
**Thay đổi:**
- Thêm menu "Quản lý Voucher"

### 6. `app/core/App.php`
**Thay đổi:**
- Thêm routes mới cho voucher

---

## ⚠️ CẦN LÀM GÌ TIẾP?

### BƯỚC 1: Chạy Migration Database (BẮT BUỘC)

Mở phpMyAdmin → Database `decornest` → SQL → Paste:

```sql
-- File: config/migrations/add_voucher_features.sql
-- (Copy toàn bộ nội dung file này)
```

Click **Go**.

### BƯỚC 2: Test tính năng Voucher

#### A. Tạo voucher mới (Admin):
1. Đăng nhập admin
2. Vào menu "Quản lý Voucher"
3. Tạo voucher test:
   - Code: `TEST50`
   - Giảm: 50%
   - Đơn tối thiểu: 100,000đ
   - Thời gian: Hôm nay → 1 tuần sau

#### B. Test trên giỏ hàng (User):
1. Thêm sản phẩm vào giỏ
2. Nhập mã: `TEST50`
3. Xem giảm giá tự động
4. Checkout → Xem discount_amount trong orders

### BƯỚC 3: Kiểm tra tích hợp với GHN

Sau khi merge code, giỏ hàng sẽ có:
- ✅ Voucher discount (từ thành viên khác)
- ✅ Shipping fee từ GHN (từ bạn)
- ✅ Tổng tiền = Subtotal - Discount + Shipping + Tax

---

## 🔗 TÍCH HỢP VỚI CODE CỦA BẠN:

### CheckoutController đã merge thành công:
```php
class CheckoutController {
    // Cả 2 service
    private GhnShippingService $ghnService;    // Của bạn
    private VoucherService $voucherService;     // Của thành viên

    public function process() {
        // 1. Tính shipping từ GHN
        $shippingFee = $this->calculateShipping($subtotal, $districtId, $wardCode);
        
        // 2. Áp dụng voucher
        if ($voucherCode) {
            $voucherResult = $this->voucherService->validateCode(...);
            $voucherDiscount = $voucherResult['discount'];
        }
        
        // 3. Tính tổng
        $totalAmount = $subtotal - $voucherDiscount + $shippingFee + $taxAmount;
        
        // 4. Lưu order
        $orderData = [
            'subtotal' => $subtotal,
            'discount_amount' => $voucherDiscount,
            'voucher_id' => $voucher['id'],
            'shipping_fee' => $shippingFee,
            'shipping_province_id' => $provinceId,  // GHN
            'shipping_district_id' => $districtId,   // GHN
            'shipping_ward_code' => $wardCode,       // GHN
            'total_amount' => $totalAmount,
        ];
        
        // 5. Tạo đơn vận chuyển GHN
        $this->createGhnShippingOrder(...);
    }
}
```

---

## 📋 TÓM TẮT:

| Tính năng | Người làm | Status |
|-----------|-----------|--------|
| **Voucher System** | Thành viên 2311063262 | ✅ Done |
| **GHN Shipping** | Bạn | ✅ Done |
| **Tích hợp cả 2** | Đã merge | ✅ Done |

**Cần làm:**
1. Chạy migration `add_voucher_features.sql`
2. Test tạo voucher
3. Test apply voucher trong giỏ hàng
4. Test checkout với cả voucher + GHN shipping

---

## 🎉 KẾT QUẢ:

Giờ dự án có đầy đủ:
- ✅ Voucher/Mã giảm giá
- ✅ Tính phí ship tự động (GHN)
- ✅ Dropdown địa chỉ chuyên nghiệp
- ✅ Tạo đơn vận chuyển tự động
- ✅ Lịch sử đơn hàng
- ✅ Quản lý voucher (Admin)

Hoàn toàn đủ tiêu chuẩn thương mại điện tử! 🚀
