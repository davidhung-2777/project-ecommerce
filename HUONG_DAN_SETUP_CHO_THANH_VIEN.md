# 🚀 HƯỚNG DẪN SETUP PROJECT CHO THÀNH VIÊN NHÓM

## 📦 YÊU CẦU:
- XAMPP (Apache + MySQL + PHP)
- Git
- Trình duyệt (Chrome/Firefox)

---

## 🔧 BƯỚC 1: Clone Project từ GitHub

```bash
# Vào thư mục htdocs (XAMPP)
cd C:\xampp\htdocs

# Clone repository
git clone https://github.com/davidhung-2777/project-ecommerce.git

# Vào thư mục project
cd project-ecommerce
```

---

## 📝 BƯỚC 2: Tạo file .env

File `.env` chứa thông tin bảo mật (database, API keys) và **KHÔNG** được commit lên GitHub.

### Cách tạo:

```bash
# Copy file mẫu
copy .env.example .env
```

**Hoặc**: Tạo thủ công file `.env` trong thư mục gốc project, copy nội dung từ `.env.example`

### ⚠️ QUAN TRỌNG: Token GHN

File `.env.example` đã có token chung cho cả nhóm:
```env
GHN_API_TOKEN=dcb0c22f-ccd5-4d90-9d5c-720ead34f902
GHN_SHOP_ID=6686325
```

**Token này hoạt động cho tất cả thành viên** (không cần cố định IP).

---

## 🗄️ BƯỚC 3: Setup Database

### 3.1. Tạo Database

1. Mở **phpMyAdmin**: `http://localhost/phpmyadmin`
2. Click **"New"** (Tạo database mới)
3. Tên database: `decornest`
4. Collation: `utf8mb4_unicode_ci`
5. Click **"Create"**

### 3.2. Import SQL

Chạy các file SQL theo thứ tự:

#### ① File chính (Tables + Data):
```sql
-- File: config/decornest.sql
-- Vào phpMyAdmin → Database decornest → Tab "Import"
-- Chọn file config/decornest.sql → Click "Go"
```

#### ② Migration GHN Shipping:
```sql
-- File: config/migrations/add_shipping_columns.sql
-- Vào phpMyAdmin → Database decornest → Tab "SQL"
-- Copy nội dung file → Paste → Click "Go"
```

#### ③ Migration Voucher System:
```sql
-- File: config/migrations/add_voucher_features.sql
-- Vào phpMyAdmin → Database decornest → Tab "SQL"
-- Copy nội dung file → Paste → Click "Go"
```

### 3.3. Kiểm tra Database

Vào phpMyAdmin → Database `decornest` → Xem có các bảng sau không:

**Tables chính:**
- ✅ `users` - Tài khoản user
- ✅ `products` - Sản phẩm
- ✅ `categories` - Danh mục
- ✅ `orders` - Đơn hàng
- ✅ `carts` - Giỏ hàng
- ✅ `payments` - Thanh toán

**Tables mới (sau migration):**
- ✅ `vouchers` - Mã giảm giá
- ✅ `voucher_usages` - Lịch sử sử dụng voucher

**Cột mới trong `orders`:**
- ✅ `shipping_code` - Mã vận đơn GHN
- ✅ `voucher_id` - ID voucher đã dùng
- ✅ `discount_amount` - Số tiền giảm giá

---

## 🎯 BƯỚC 4: Cấu hình .env

Mở file `.env` và kiểm tra các thông tin sau:

```env
# Database (local)
DB_HOST=localhost
DB_DATABASE=decornest
DB_USERNAME=root
DB_PASSWORD=
# ↑ Password để trống (XAMPP mặc định)

# App URL (theo máy của bạn)
APP_URL=http://localhost/project-ecommerce/public
# ↑ Thay đổi nếu project ở thư mục khác

# GHN Token (đã có sẵn, KHÔNG SỬA)
GHN_API_TOKEN=dcb0c22f-ccd5-4d90-9d5c-720ead34f902
GHN_SHOP_ID=6686325

# Bank Info (thông tin ngân hàng - đã có sẵn)
BANK_ACCOUNT_NAME=TRINH TIEN HUNG
BANK_ACCOUNT_NUMBER=1058081721
BANK_NAME=Vietcombank
```

**⚠️ KHÔNG thay đổi**: `GHN_API_TOKEN`, `BANK_ACCOUNT_*` (trừ khi leader yêu cầu)

---

## ✅ BƯỚC 5: Test Project

### Test 1: Trang chủ
```
http://localhost/project-ecommerce/public
```
→ Xem có hiển thị trang chủ không

### Test 2: Database Connection
```
http://localhost/project-ecommerce/public/test-db.php
```
→ Kết quả: "✅ Kết nối database thành công"

### Test 3: GHN Token
```
http://localhost/project-ecommerce/public/test-ghn-token.php
```
→ Tất cả test màu xanh ✅

### Test 4: Migrations
```
http://localhost/project-ecommerce/public/check-migrations.php
```
→ Tất cả cột/bảng màu xanh ✅

### Test 5: Checkout với Dropdown
1. Thêm sản phẩm vào giỏ hàng
2. Vào trang thanh toán
3. **Kiểm tra**: Dropdown Tỉnh/Quận/Phường có hiển thị dữ liệu không?

---

## 🚨 TROUBLESHOOTING

### ❌ Lỗi: "Table 'decornest.orders' doesn't exist"
**Nguyên nhân**: Chưa chạy file SQL

**Cách fix**:
1. Vào phpMyAdmin
2. Import file `config/decornest.sql`

---

### ❌ Lỗi: "Column 'shipping_code' not found"
**Nguyên nhân**: Chưa chạy migration GHN

**Cách fix**:
1. Vào phpMyAdmin → Database `decornest` → Tab SQL
2. Copy nội dung file `config/migrations/add_shipping_columns.sql`
3. Paste và chạy

---

### ❌ Lỗi: "Table 'vouchers' doesn't exist"
**Nguyên nhân**: Chưa chạy migration Voucher

**Cách fix**:
1. Vào phpMyAdmin → Database `decornest` → Tab SQL
2. Copy nội dung file `config/migrations/add_voucher_features.sql`
3. Paste và chạy

---

### ❌ Dropdown Tỉnh/Quận/Phường không hiển thị
**Nguyên nhân**: Token GHN chưa đúng trong `.env`

**Cách fix**:
1. Mở file `.env`
2. Kiểm tra dòng:
   ```env
   GHN_API_TOKEN=dcb0c22f-ccd5-4d90-9d5c-720ead34f902
   ```
3. Đảm bảo token **chính xác** (không thừa dấu cách)
4. Test lại: `test-ghn-token.php`

---

### ❌ Lỗi: "Access denied for user 'root'@'localhost'"
**Nguyên nhân**: Sai mật khẩu MySQL

**Cách fix**:
1. Mở file `.env`
2. Kiểm tra:
   ```env
   DB_USERNAME=root
   DB_PASSWORD=
   ```
3. XAMPP mặc định: `DB_PASSWORD=` (để trống)
4. Nếu bạn đặt password MySQL: điền password vào

---

## 🔄 PULL CODE MỚI TỪ GITHUB

Khi có thành viên khác push code mới:

```bash
# Vào thư mục project
cd C:\xampp\htdocs\project-ecommerce

# Pull code mới nhất
git pull origin main

# Kiểm tra file .env có thay đổi không
# Nếu có file mới (migrations), chạy SQL trong phpMyAdmin
```

**⚠️ LƯU Ý**: File `.env` **KHÔNG** được pull từ GitHub (đã có trong `.gitignore`). Nếu có thay đổi token, leader sẽ thông báo team cập nhật thủ công.

---

## 👥 LÀM VIỆC NHÓM VỚI GIT

### Quy tắc:
1. **KHÔNG commit file `.env`** lên GitHub
2. **Tạo branch riêng** cho mỗi tính năng:
   ```bash
   git checkout -b feature/ten-tinh-nang
   ```
3. **Commit thường xuyên**:
   ```bash
   git add .
   git commit -m "feat: Mô tả tính năng"
   git push origin feature/ten-tinh-nang
   ```
4. **Tạo Pull Request** trên GitHub để team review

### Khi conflict (xung đột code):
```bash
# Pull code mới nhất
git pull origin main

# Giải quyết conflict trong file
# Sau đó:
git add .
git commit -m "fix: Resolve merge conflict"
git push
```

---

## 📚 TÀI LIỆU THAM KHẢO

### Files hướng dẫn trong project:
- `README.md` - Thông tin chung về project
- `GHN_TOKEN_MANAGEMENT.md` - Quản lý token GHN
- `FEATURE_CHECKLIST.md` - Checklist tính năng
- `HUONG_DAN_TEST.md` - Hướng dẫn test

### API Documentation:
- GHN: https://api.ghn.vn/home/docs/detail
- VietQR: https://www.vietqr.io/

### Files test trong project:
- `test-db.php` - Test database connection
- `test-ghn-token.php` - Test GHN API token
- `check-migrations.php` - Kiểm tra migrations

---

## 🆘 HỖ TRỢ

### Nếu gặp vấn đề:

1. **Kiểm tra files test** (xem phần "BƯỚC 5: Test Project")
2. **Chụp màn hình lỗi** (F12 → Console trong browser)
3. **Hỏi trong group** (Discord/Zalo nhóm)
4. **Liên hệ leader**: [Tên leader]

### Thông tin liên hệ:
- Discord: [Server link]
- GitHub: https://github.com/davidhung-2777/project-ecommerce
- Leader: [Tên và contact]

---

**Cập nhật**: 2026-09-28  
**Version**: 1.0  
**Tác giả**: Team Backend - DecorNest

---

## ✨ SAU KHI SETUP XONG:

Bạn sẽ có:
- ✅ Project chạy local: `http://localhost/project-ecommerce/public`
- ✅ Admin panel: `http://localhost/project-ecommerce/public/admin`
- ✅ Database đầy đủ
- ✅ GHN Shipping hoạt động
- ✅ Voucher System hoạt động
- ✅ VietQR Payment hoạt động

**→ Sẵn sàng code tính năng mới!** 🎉

