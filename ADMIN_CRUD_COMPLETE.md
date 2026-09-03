# 🎯 Hệ Thống Admin CRUD - DecorNest

## ✅ Tổng Quan Chức Năng

| Module | Thêm | Sửa | Xóa | Xem | Trạng thái |
|--------|------|-----|-----|-----|------------|
| 📊 Dashboard | ❌ | ❌ | ❌ | ✅ | Chỉ xem thống kê |
| 📦 Sản phẩm | ✅ | ✅ | ✅ | ✅ | **Đầy đủ CRUD** |
| 🏷️ Danh mục | ✅ | ✅ | ✅ | ✅ | **Đầy đủ CRUD** |
| 🛒 Đơn hàng | ❌ | ✅ | ❌ | ✅ | Xác nhận/Hủy nhanh |
| 📋 Báo giá | ❌ | ✅ | ✅ | ✅ | Phản hồi/Từ chối |
| 💳 Thanh toán | ❌ | ✅ | ❌ | ✅ | Xác nhận thanh toán |
| 👥 Khách hàng | ❌ | ✅ | ✅ | ✅ | Khóa/Mở khóa |
| 📈 Báo cáo | ❌ | ❌ | ❌ | ✅ | Chỉ xem báo cáo |

---

## 📦 1. SẢN PHẨM (Products)

### ➕ Thêm Sản Phẩm
**URL:** `/admin/products/create`

**Các bước:**
1. Click nút "Thêm sản phẩm"
2. Điền thông tin:
   - Tên sản phẩm *
   - SKU (tùy chọn)
   - Giá niêm yết *
   - Giá khuyến mãi (tùy chọn)
   - Số lượng tồn kho *
   - Danh mục
   - Mô tả
3. Upload ảnh (tùy chọn)
4. Chọn trạng thái (hiển thị, nổi bật, mới)
5. Thêm bậc giá sỉ (optional)
6. Click "Tạo sản phẩm"

### ✏️ Sửa Sản Phẩm
**URL:** `/admin/products/{id}/edit`

**Các bước:**
1. Trong danh sách, click nút "Sửa"
2. Cập nhật thông tin cần thay đổi
3. Upload ảnh mới (nếu cần)
4. Click "Cập nhật sản phẩm"

### 🗑️ Xóa Sản Phẩm
**Method:** POST `/admin/products/{id}/delete`

**Các bước:**
1. Click nút "Xóa" trong danh sách
2. Xác nhận trong popup
3. ✅ Sản phẩm ẩn (soft delete, `is_active=0`)

**Lưu ý:** Soft delete - không xóa khỏi database, chỉ ẩn đi.

---

## 🏷️ 2. DANH MỤC (Categories)

### ➕ Thêm Danh Mục
**URL:** `/admin/categories` (inline form)

**Các bước:**
1. Click nút "+ Thêm danh mục"
2. Điền:
   - Tên danh mục *
   - Danh mục cha (tùy chọn)
   - Thứ tự sắp xếp
3. Click "Tạo"

### ✏️ Sửa Danh Mục
**Method:** Inline editing

**Các bước:**
1. Click nút "Sửa" ở dòng danh mục
2. Chỉnh sửa tên hoặc thứ tự
3. Click "Lưu"

### 🗑️ Xóa Danh Mục
**Method:** POST `/admin/categories/{id}/delete`

**Các bước:**
1. Click nút "Xóa"
2. Xác nhận
3. ✅ Danh mục ẩn (`is_active=0`)

**Lưu ý:** Nếu có sản phẩm trong danh mục, chúng vẫn giữ nguyên.

---

## 🛒 3. ĐỐN HÀNG (Orders)

### ✏️ Xác Nhận/Hủy Đơn Hàng
**URL:** `/admin/orders`

**Các bước:**
1. Trong danh sách đơn hàng `pending`:
   - Click "Xác nhận" → chuyển sang `confirmed`
   - Click "Hủy" → chuyển sang `cancelled`
2. Xem chi tiết: `/admin/orders/{id}`
3. Thay đổi trạng thái thủ công:
   - Chọn trạng thái mới
   - Click "Cập nhật"

**Các trạng thái:**
- `pending` → Chờ xác nhận
- `confirmed` → Đã xác nhận
- `processing` → Đang xử lý
- `shipped` → Đang giao hàng
- `delivered` → Đã giao
- `cancelled` → Đã hủy

**Lưu ý:** Không có chức năng "Thêm đơn hàng" - đơn được tạo từ frontend.

---

## 📋 4. BÁO GIÁ (Quotes)

### ✏️ Phản Hồi Báo Giá
**URL:** `/admin/quotes/{id}`

**Các bước:**
1. Xem chi tiết báo giá
2. Nhập giá cho từng sản phẩm
3. Nhập ghi chú phản hồi
4. Click "Gửi báo giá"

### 🗑️ Từ Chối Báo Giá
**Method:** POST `/admin/quotes/{id}/reject`

**Các bước:**
1. Trong danh sách, click "Từ chối"
2. Nhập lý do
3. Xác nhận

**Các trạng thái:**
- `pending` → Chờ xử lý
- `responded` → Đã phản hồi
- `accepted` → Đã chấp nhận
- `rejected` → Đã từ chối
- `converted` → Đã chuyển đơn hàng

---

## 💳 5. THANH TOÁN (Payments)

### ✏️ Xác Nhận Thanh Toán Chuyển Khoản
**URL:** `/admin/payments`

**Các bước:**
1. Xem danh sách chuyển khoản chưa xác nhận
2. Kiểm tra thông tin
3. Click "Xác nhận"
4. ✅ Đơn hàng chuyển sang `paid`

**Lưu ý:** VietQR + webhook tự động xác nhận, chỉ cần xác nhận thủ công khi cần.

---

## 👥 6. KHÁCH HÀNG (Users)

### ✏️ Khóa/Mở Khóa Tài Khoản
**URL:** `/admin/users`

**Các bước:**

**Khóa tài khoản:**
1. Click "Khóa" ở dòng khách hàng
2. Xác nhận
3. ✅ Tài khoản `is_active=0`

**Mở khóa:**
1. Click "Mở khóa"
2. Xác nhận
3. ✅ Tài khoản `is_active=1`

**Lưu ý:** Không xóa hoàn toàn tài khoản để giữ lịch sử mua hàng.

---

## 📈 7. BÁO CÁO (Reports)

**URL:** `/admin/reports`

**Chức năng:** Chỉ xem thống kê
- Doanh thu theo tháng
- Sản phẩm bán chạy
- Khách hàng mới
- Tỷ lệ chuyển đổi

**Không có CRUD**

---

## 🎨 8. DASHBOARD

**URL:** `/admin/dashboard` hoặc `/admin`

**Chức năng:** Tổng quan
- Đơn hàng hôm nay
- Đơn chờ xử lý
- Báo giá chờ
- Tổng khách hàng
- Doanh thu
- 10 đơn hàng gần nhất

**Không có CRUD**

---

## 🔗 Routes API Summary

### Products
```
GET  /admin/products                # List
GET  /admin/products/create         # Form tạo
POST /admin/products/create         # Lưu mới
GET  /admin/products/:id/edit       # Form sửa
POST /admin/products/:id/edit       # Cập nhật
POST /admin/products/:id/delete     # Xóa (soft)
```

### Categories
```
GET  /admin/categories              # List + Form inline
POST /admin/categories/create       # Tạo mới
POST /admin/categories/:id/edit     # Cập nhật
POST /admin/categories/:id/delete   # Xóa (soft)
```

### Orders
```
GET  /admin/orders                  # List + Filter
GET  /admin/orders/:id              # Chi tiết
POST /admin/orders/:id/status       # Cập nhật trạng thái
```

### Quotes
```
GET  /admin/quotes                  # List + Filter
GET  /admin/quotes/:id              # Chi tiết
POST /admin/quotes/:id/respond      # Phản hồi báo giá
POST /admin/quotes/:id/reject       # Từ chối
POST /admin/quotes/:id/convert      # Chuyển đơn hàng
```

### Users
```
GET  /admin/users                   # List
GET  /admin/users/:id               # Chi tiết
POST /admin/users/:id/block         # Khóa
POST /admin/users/:id/unblock       # Mở khóa
```

### Payments
```
GET  /admin/payments                # List chờ xác nhận
POST /admin/payments/:id/confirm    # Xác nhận TT
```

---

## 🧪 Test Checklist

### Sản Phẩm
- [ ] Thêm sản phẩm mới thành công
- [ ] Upload ảnh < 5MB
- [ ] Sửa thông tin sản phẩm
- [ ] Xóa sản phẩm (soft delete)
- [ ] Thêm giá sỉ theo số lượng

### Danh Mục
- [ ] Thêm danh mục cha
- [ ] Thêm danh mục con
- [ ] Sửa tên danh mục inline
- [ ] Xóa danh mục

### Đơn Hàng
- [ ] Xác nhận đơn pending
- [ ] Hủy đơn pending
- [ ] Thay đổi trạng thái thủ công
- [ ] Xem chi tiết đơn hàng

### Báo Giá
- [ ] Phản hồi báo giá với giá mới
- [ ] Từ chối báo giá
- [ ] Chuyển báo giá thành đơn hàng

### Khách Hàng
- [ ] Xem danh sách khách hàng
- [ ] Khóa tài khoản
- [ ] Mở khóa tài khoản
- [ ] Xem lịch sử mua hàng

### Thanh Toán
- [ ] Xác nhận chuyển khoản thủ công
- [ ] VietQR tự động xác nhận

---

## 💡 Best Practices

### 1. Soft Delete
- Không xóa hẳn dữ liệu quan trọng
- Set `is_active = 0` thay vì DELETE
- Giữ lịch sử cho audit trail

### 2. Validation
- Server-side validation bắt buộc
- Client-side validation để UX tốt hơn
- Sanitize input để tránh XSS

### 3. Authorization
- Mọi route admin đều có `requireAdmin()`
- Check quyền ở controller level
- Redirect về home nếu không đủ quyền

### 4. AJAX Operations
- Dùng fetch() cho thao tác nhanh
- Return JSON response
- Reload page sau khi thành công

### 5. User Experience
- Inline editing khi có thể
- Confirmation popup cho delete
- Flash message sau mỗi thao tác

---

## 🐛 Troubleshooting

### Lỗi 403 Forbidden
**Nguyên nhân:** Không có quyền admin
**Giải pháp:** Đăng nhập với `admin@decornest.com`

### Lỗi 404 Not Found
**Nguyên nhân:** Route chưa được định nghĩa
**Giải pháp:** Check `App.php` routes

### Lỗi 500 Internal Server Error
**Nguyên nhân:** Lỗi PHP
**Giải pháp:** 
- Check Apache error log
- Bật `display_errors` trong php.ini
- Xem file log tại `xampp/apache/logs/error.log`

### Upload ảnh thất bại
**Nguyên nhân:** Không có quyền ghi
**Giải pháp:**
```bash
icacls "c:\xampp\htdocs\project-ecommerce\public\uploads" /grant Everyone:F
```

---

## 📚 Tài Liệu Tham Khảo

- [ADMIN_PRODUCT_GUIDE.md](ADMIN_PRODUCT_GUIDE.md) - Chi tiết quản lý sản phẩm
- [PERMISSIONS_GUIDE.md](PERMISSIONS_GUIDE.md) - Hệ thống phân quyền
- [README.md](README.md) - Tổng quan dự án

---

## ✅ Summary

**Đã hoàn thành:**
- ✅ Sản phẩm: CRUD đầy đủ
- ✅ Danh mục: CRUD đầy đủ
- ✅ Đơn hàng: Xem, xác nhận, hủy nhanh
- ✅ Báo giá: Phản hồi, từ chối
- ✅ Khách hàng: Khóa/mở khóa
- ✅ Thanh toán: Xác nhận chuyển khoản
- ✅ Dashboard: Xem thống kê
- ✅ Báo cáo: Xem báo cáo

**Tất cả modules trong sidebar đều có đầy đủ chức năng cần thiết!** 🎉
