# 📦 Hướng Dẫn Quản Lý Sản Phẩm - Admin Panel

## 🚀 Chuẩn Bị

### 1. Khởi động XAMPP
```
✅ Apache: Running
✅ MySQL: Running
```

### 2. Chạy Script Tạo Database
Truy cập:
```
http://localhost/project-ecommerce/public/fix-all-now.php
```

Script này sẽ:
- ✅ Sửa encoding UTF-8 cho tất cả bảng
- ✅ Tạo 16 sản phẩm mẫu
- ✅ Tạo 6 danh mục
- ✅ Tạo admin account

### 3. Test Hệ Thống
```
http://localhost/project-ecommerce/public/test-product-crud.php
```

---

## 🔐 Đăng Nhập Admin

**URL:** `http://localhost/project-ecommerce/public/user/login`

**Thông tin đăng nhập:**
- Email: `admin@decornest.com`
- Password: `admin123`

Sau khi đăng nhập thành công → redirect về `/admin/dashboard`

---

## 📋 Quản Lý Sản Phẩm

### 📦 Danh Sách Sản Phẩm
**URL:** `/admin/products`

**Chức năng:**
- ✅ Xem danh sách tất cả sản phẩm
- ✅ Tìm kiếm sản phẩm theo tên/SKU
- ✅ Phân trang (20 sản phẩm/trang)
- ✅ Sửa sản phẩm (nút "Sửa")
- ✅ Xóa sản phẩm (nút "Xóa" - soft delete)

### ➕ Thêm Sản Phẩm Mới
**URL:** `/admin/products/create`

**Các trường bắt buộc:**
- Tên sản phẩm *
- Giá niêm yết *
- Số lượng tồn kho *

**Các trường tùy chọn:**
- SKU
- Danh mục
- Mô tả ngắn
- Mô tả chi tiết
- Giá khuyến mãi
- Phí lắp đặt
- Xuất xứ
- Chất liệu
- Kích thước
- Biến thể kích thước (phân cách bằng dấu phẩy)
- Biến thể màu sắc (phân cách bằng dấu phẩy)
- Ảnh đại diện
- Bộ sưu tập ảnh
- Meta Title (SEO)
- Meta Description (SEO)

**Trạng thái:**
- ☑️ Hiển thị trên website (is_active)
- ☑️ Sản phẩm nổi bật (is_featured)
- ☑️ Hàng mới về (is_new)

**Giá sỉ theo số lượng:**
- Thêm bậc giá (min_qty, max_qty, price)
- Ví dụ: 10-49 SP = 90,000đ | 50-99 SP = 85,000đ

**Quy tắc upload ảnh:**
- Định dạng: JPG, JPEG, PNG, WebP
- Kích thước tối đa: 5MB
- Lưu tại: `/public/uploads/products/`

### ✏️ Sửa Sản Phẩm
**URL:** `/admin/products/{id}/edit`

**Cách thực hiện:**
1. Vào danh sách sản phẩm
2. Click nút "Sửa" trên dòng sản phẩm cần sửa
3. Cập nhật thông tin
4. Upload ảnh mới (nếu cần thay đổi)
5. Click "Cập nhật sản phẩm"

### 🗑️ Xóa Sản Phẩm
**URL:** `/admin/products/{id}/delete` (POST)

**Cách thực hiện:**
1. Click nút "Xóa" trên dòng sản phẩm
2. Xác nhận trong popup
3. Sản phẩm sẽ ẩn khỏi danh sách

**Lưu ý:** 
- Đây là **soft delete** (set `is_active = 0`)
- Sản phẩm vẫn còn trong database
- Không xóa hoàn toàn để giữ lịch sử đơn hàng

---

## 🎯 Test Cases

### TEST 1: Thêm Sản Phẩm
```
✅ Bước 1: Vào /admin/products/create
✅ Bước 2: Điền thông tin:
   - Tên: "Đèn Bàn Gỗ Oak"
   - SKU: "DEN-001"
   - Giá: 450000
   - Tồn kho: 100
   - Danh mục: "Đèn Trang Trí"
✅ Bước 3: Upload ảnh (optional)
✅ Bước 4: Click "Tạo sản phẩm"
✅ Kết quả: Redirect về /admin/products, thấy sản phẩm mới
```

### TEST 2: Sửa Sản Phẩm
```
✅ Bước 1: Click "Sửa" ở sản phẩm vừa tạo
✅ Bước 2: Thay đổi giá: 450000 → 420000
✅ Bước 3: Thay đổi tồn kho: 100 → 50
✅ Bước 4: Click "Cập nhật sản phẩm"
✅ Kết quả: Thông tin đã được update
```

### TEST 3: Xóa Sản Phẩm
```
✅ Bước 1: Click "Xóa" ở sản phẩm test
✅ Bước 2: Xác nhận xóa
✅ Kết quả: Sản phẩm biến mất khỏi danh sách
```

### TEST 4: Upload Ảnh
```
✅ Bước 1: Tạo/sửa sản phẩm
✅ Bước 2: Chọn file ảnh < 5MB
✅ Bước 3: Submit form
✅ Kết quả: Ảnh hiển thị trong danh sách và trang chi tiết
```

---

## 🐛 Xử Lý Lỗi Thường Gặp

### Lỗi 1: "Cannot find product"
**Nguyên nhân:** Database chưa có sản phẩm
**Giải pháp:** Chạy `fix-all-now.php`

### Lỗi 2: Upload ảnh không thành công
**Nguyên nhân:** Thư mục không có quyền ghi
**Giải pháp:**
```bash
# Windows (XAMPP)
icacls "c:\xampp\htdocs\project-ecommerce\public\uploads" /grant Everyone:F

# Linux/Mac
chmod -R 777 public/uploads
```

### Lỗi 3: "Namespace declaration error"
**Nguyên nhân:** File có ký tự lạ trước `<?php`
**Giải pháp:** Đã fix trong `Controller.php`

### Lỗi 4: Font chữ hiển thị sai
**Nguyên nhân:** Database encoding không phải UTF-8
**Giải pháp:** Chạy `fix-all-now.php` để fix encoding

### Lỗi 5: "Call to undefined method"
**Nguyên nhân:** Model không có method cần thiết
**Giải pháp:** Check Model extends từ base Model class

---

## 📁 Cấu Trúc File

```
project-ecommerce/
├── app/
│   ├── controllers/
│   │   └── AdminController.php        # CRUD products
│   ├── models/
│   │   ├── ProductModel.php           # Product queries
│   │   └── ProductPriceTierModel.php  # Bulk pricing
│   ├── views/
│   │   └── admin/
│   │       ├── layouts/
│   │       │   └── main.php           # Admin layout
│   │       └── pages/
│   │           ├── products.php       # List products
│   │           └── product-form.php   # Create/Edit form
│   └── core/
│       ├── App.php                    # Routes definition
│       ├── Router.php                 # Routing logic
│       ├── Controller.php             # Base controller
│       └── Model.php                  # Base model
├── public/
│   ├── uploads/
│   │   ├── products/                  # Product images
│   │   └── .htaccess                  # Security rules
│   ├── fix-all-now.php                # Database setup
│   └── test-product-crud.php          # Test interface
└── config/
    └── database.php                   # DB config
```

---

## 🔗 Quick Links

| Chức Năng | URL |
|-----------|-----|
| 🏠 Trang chủ | `/public` |
| 🔐 Đăng nhập | `/public/user/login` |
| 📊 Admin Dashboard | `/public/admin/dashboard` |
| 📦 Danh sách SP | `/public/admin/products` |
| ➕ Thêm SP mới | `/public/admin/products/create` |
| 🧪 Test CRUD | `/public/test-product-crud.php` |
| 🔧 Fix Database | `/public/fix-all-now.php` |

---

## 💡 Tips & Best Practices

1. **Luôn backup database** trước khi test xóa
2. **Upload ảnh nhỏ** (<1MB) để tải nhanh
3. **Dùng WebP** thay vì JPG để giảm dung lượng
4. **Đặt SKU duy nhất** để dễ quản lý
5. **Điền SEO meta** để tăng rank Google
6. **Giá sỉ:** Min qty phải < Max qty
7. **Soft delete** giúp khôi phục dễ dàng

---

## 📞 Hỗ Trợ

Nếu gặp lỗi, check:
1. ✅ XAMPP đang chạy?
2. ✅ Database đã setup chưa?
3. ✅ Đã đăng nhập admin?
4. ✅ Thư mục uploads có quyền ghi?
5. ✅ PHP version >= 8.0?

**Error Log:**
- Apache: `xampp/apache/logs/error.log`
- PHP: Bật `display_errors` trong `php.ini`

---

## ✅ Checklist Hoàn Thành

- [ ] Chạy `fix-all-now.php` thành công
- [ ] Đăng nhập admin thành công
- [ ] Xem được danh sách 16 sản phẩm
- [ ] Thêm được sản phẩm mới
- [ ] Sửa được sản phẩm
- [ ] Xóa được sản phẩm
- [ ] Upload ảnh thành công
- [ ] Font chữ hiển thị đúng tiếng Việt

---

**🎉 Chúc bạn test thành công!**
