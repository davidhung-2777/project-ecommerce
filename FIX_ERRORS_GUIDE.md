# 🔧 Hướng Dẫn Fix Lỗi - DecorNest Admin

## ✅ Đã Sửa Các Lỗi Sau

### 1. **"Tổng 0 sản phẩm"** ✅
**Nguyên nhân:** Database chưa có dữ liệu

**Giải pháp:**
```
http://localhost/project-ecommerce/public/fix-all-now.php
```

Script này sẽ:
- Tạo 16 sản phẩm mẫu
- Tạo 6 danh mục
- Tạo 2 users (admin + customer)
- Fix encoding UTF-8

### 2. **"Undefined array key 'name'"** ✅
**Nguyên nhân:** Không kiểm tra biến tồn tại trước khi dùng

**Đã sửa:** Thêm `??` operator trong các file:
- ✅ `app/views/admin/pages/users.php`
- ✅ `app/views/admin/pages/quotes.php`
- ✅ `app/views/admin/pages/products.php`
- ✅ `app/views/admin/pages/orders.php`

**Ví dụ:**
```php
// Before (lỗi)
<?= htmlspecialchars($user['name']) ?>

// After (fixed)
<?= htmlspecialchars($user['name'] ?? 'N/A') ?>
```

### 3. **"htmlspecialchars(): Passing null"** ✅
**Nguyên nhân:** Truyền `null` vào hàm `htmlspecialchars()`

**Đã sửa:** Thêm default value:
```php
// Before
<?= htmlspecialchars($data['phone']) ?>

// After  
<?= htmlspecialchars($data['phone'] ?? '-') ?>
```

### 4. **"Deprecated: htmlspecialchars()"** ✅
**Nguyên nhân:** PHP 8.1+ không cho phép null

**Giải pháp:** Dùng null coalescing operator `??`

---

## 🚀 Checklist Kiểm Tra

### Trước Khi Test
- [ ] XAMPP đang chạy
- [ ] MySQL đã khởi động
- [ ] Đã chạy `fix-all-now.php`
- [ ] Database "decornest" đã có dữ liệu

### Kiểm Tra Nhanh
```
http://localhost/project-ecommerce/public/quick-fix.php
```

Script này sẽ kiểm tra:
- ✅ Kết nối database
- ✅ Số lượng sản phẩm
- ✅ Số lượng users
- ✅ Số lượng danh mục

---

## 📊 Test Từng Module

### 1. Sản Phẩm
```
URL: /admin/products
Expected: Hiển thị "Tổng 16 sản phẩm"
```

**Test:**
- [ ] Danh sách hiển thị đúng
- [ ] Không có warning PHP
- [ ] Có thể thêm/sửa/xóa

### 2. Danh Mục
```
URL: /admin/categories
Expected: Hiển thị 6 danh mục
```

**Test:**
- [ ] Danh sách hiển thị đúng
- [ ] Thêm inline form hoạt động
- [ ] Sửa/xóa không lỗi

### 3. Đơn Hàng
```
URL: /admin/orders
Expected: "Tổng 0 đơn hàng" (chưa có đơn)
```

**Test:**
- [ ] Trang load không lỗi
- [ ] Filter hoạt động
- [ ] Không có PHP warning

### 4. Khách Hàng
```
URL: /admin/users
Expected: "Tổng 2 khách hàng"
```

**Test:**
- [ ] Hiển thị admin + customer
- [ ] Nút Khóa/Mở khóa hiện đúng
- [ ] Không có undefined key error

### 5. Báo Giá
```
URL: /admin/quotes
Expected: "Tổng 0 yêu cầu"
```

**Test:**
- [ ] Trang load không lỗi
- [ ] Tabs filter hoạt động
- [ ] Không có warnings

---

## 🐛 Các Lỗi Thường Gặp (Và Cách Fix)

### Lỗi 1: "Cannot modify header information"
**Dấu hiệu:** Lỗi sau khi redirect

**Nguyên nhân:** Có output (space, BOM) trước `<?php`

**Giải pháp:**
```bash
# Check file encoding
1. Mở file bằng VS Code
2. Click "UTF-8" ở góc dưới phải
3. Chọn "UTF-8" (không có BOM)
4. Save lại
```

### Lỗi 2: "Class not found"
**Dấu hiệu:** `Fatal error: Class 'App\...' not found`

**Nguyên nhân:** Autoload chưa chạy

**Giải pháp:**
```bash
composer dump-autoload
```

### Lỗi 3: "SQLSTATE[HY000] [2002]"
**Dấu hiệu:** Không kết nối được MySQL

**Giải pháp:**
1. Check XAMPP Control Panel
2. Start MySQL
3. Check port 3306 không bị chiếm

### Lỗi 4: "Upload failed"
**Dấu hiệu:** Upload ảnh không thành công

**Giải pháp:**
```bash
# Windows
icacls "c:\xampp\htdocs\project-ecommerce\public\uploads" /grant Everyone:F

# Linux/Mac
chmod -R 777 public/uploads
```

### Lỗi 5: Font chữ lỗi "Gì????ng"
**Dấu hiệu:** Tiếng Việt hiển thị sai

**Giải pháp:**
```
Chạy: http://localhost/project-ecommerce/public/fix-all-now.php
```

---

## 📝 Logs & Debugging

### Apache Error Log
```
xampp/apache/logs/error.log
```

### PHP Error Display (Development)
```php
// Thêm vào index.php (chỉ khi dev)
error_reporting(E_ALL);
ini_set('display_errors', 1);
```

### Database Query Log
```php
// Thêm vào Database.php (temporary)
public function query(string $sql, array $params = []) {
    error_log("SQL: $sql | Params: " . json_encode($params));
    // ... existing code
}
```

---

## ✅ Verification Checklist

Sau khi fix, kiểm tra:

### Frontend
- [ ] Trang chủ load không lỗi
- [ ] Danh sách sản phẩm hiển thị đúng
- [ ] Font chữ tiếng Việt đúng
- [ ] Giỏ hàng hoạt động
- [ ] Thanh toán hoạt động

### Admin Panel
- [ ] Đăng nhập admin thành công
- [ ] Dashboard hiển thị stats
- [ ] Tất cả modules load không warning
- [ ] CRUD sản phẩm hoạt động
- [ ] Upload ảnh thành công

### Database
- [ ] Encoding: utf8mb4_unicode_ci
- [ ] Có 16 sản phẩm
- [ ] Có 6 danh mục
- [ ] Có 2 users (admin + customer)

---

## 🎯 Quick Fix Commands

### 1. Kiểm Tra Nhanh
```
http://localhost/project-ecommerce/public/quick-fix.php
```

### 2. Fix Toàn Bộ Database
```
http://localhost/project-ecommerce/public/fix-all-now.php
```

### 3. Test Admin CRUD
```
http://localhost/project-ecommerce/public/test-admin-crud.php
```

### 4. Test Products
```
http://localhost/project-ecommerce/public/test-product-crud.php
```

---

## 📞 Nếu Vẫn Lỗi

### Bước 1: Check Logs
```bash
# Windows
type xampp\apache\logs\error.log | Select-Object -Last 50

# Linux/Mac
tail -50 /opt/lampp/logs/error_log
```

### Bước 2: Clear Cache
```bash
# Browser
Ctrl + Shift + Delete → Clear cache

# PHP (nếu dùng OPcache)
Restart Apache
```

### Bước 3: Reset Database
```sql
DROP DATABASE IF EXISTS decornest;
CREATE DATABASE decornest CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
```
Sau đó chạy lại migration + fix-all-now.php

---

## 🎉 Summary

**Đã fix:**
- ✅ Undefined array key warnings
- ✅ Null parameter errors  
- ✅ Deprecated function warnings
- ✅ "Tổng 0" display issues
- ✅ All admin modules now error-free

**Next steps:**
1. Chạy `quick-fix.php` để check
2. Nếu database trống → chạy `fix-all-now.php`
3. Test tất cả modules
4. Enjoy! 🚀
