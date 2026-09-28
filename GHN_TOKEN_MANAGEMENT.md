# 🔐 QUẢN LÝ GHN API TOKEN

## 📌 Token hiện tại (Production)

```
Token: dcb0c22f-ccd5-4d90-9d5c-720ead34f902
Shop ID: 6686325
```

**⚠️ QUAN TRỌNG**: 
- Token này đã được cấu hình trong file `.env`
- **KHÔNG cố định IP** vì đây là bài tập nhóm (mỗi thành viên có IP khác nhau)
- Token hoạt động từ **mọi IP** → Tất cả thành viên đều dùng được
- **KHÔNG** thay đổi token trừ khi gặp lỗi

---

## 🔍 Kiểm tra Token có hoạt động không

Chạy file test:
```
http://localhost/project-ecommerce/public/test-ghn-token.php
```

Nếu tất cả test **màu xanh** ✅ → Token đang hoạt động bình thường.

---

## 🛠️ Khi nào cần thay đổi Token?

### Dấu hiệu Token bị lỗi:

1. **Dropdown Tỉnh/Quận/Phường không hiển thị dữ liệu**
   - Chạy test: `test-ghn-token.php`
   - Nếu lỗi 401 Unauthorized → Token hết hạn

2. **Console Browser báo lỗi:**
   ```
   Lỗi load provinces: Unauthorized
   ```

3. **API trả về:**
   ```json
   {
     "success": false,
     "message": "Invalid token"
   }
   ```

---

## 🔄 Cách lấy Token mới từ GHN

### Bước 1: Đăng nhập GHN Portal
```
https://sso.ghn.vn/manage
```

### Bước 2: Vào "Quản lý Token API"
- Menu bên trái → **"Token API"**
- Hoặc truy cập: https://sso.ghn.vn/manage/token-api

### Bước 3: Copy Token
- Token hiện tại sẽ hiển thị ở đầu trang
- Format: `dcb0c22f-ccd5-4d90-9d5c-720ead34f902`
- Click icon **📋 Copy** để copy

### Bước 4: (Optional) Cố định IP
- Click **"Thêm địa chỉ IP"**
- Nhập IP server production
- Lưu lại

**Lợi ích khi cố định IP:**
- ✅ Token không tự động hết hạn
- ✅ Bảo mật: Chỉ server của bạn dùng được
- ✅ Ổn định: Không lo bị thay đổi

---

## 📝 Cách cập nhật Token mới

### Bước 1: Mở file `.env`
```bash
# Mở file .env ở thư mục gốc project
```

### Bước 2: Tìm dòng `GHN_API_TOKEN`
```env
# Shipping - Giao Hàng Nhanh (GHN)
GHN_API_TOKEN=dcb0c22f-ccd5-4d90-9d5c-720ead34f902
```

### Bước 3: Thay token mới
```env
GHN_API_TOKEN=token_moi_cua_ban_o_day
```

### Bước 4: Lưu file `.env`

### Bước 5: Test lại
```
http://localhost/project-ecommerce/public/test-ghn-token.php
```

Nếu **màu xanh** ✅ → Token mới hoạt động.

### Bước 6: Commit và Push
```bash
# ⚠️ KHÔNG commit file .env lên GitHub
# Chỉ thông báo cho team cập nhật token local

git add .
git commit -m "docs: Update GHN token management guide"
git push origin main
```

**Lưu ý**: File `.env` đã có trong `.gitignore`, nên token **KHÔNG** được push lên GitHub. Team cần cập nhật `.env` local của họ thủ công.

---

## 🔒 Bảo mật Token (Cho bài tập nhóm)

### ✅ SETUP HIỆN TẠI (Đã tối ưu cho nhóm):
- ✅ Token **KHÔNG cố định IP** → Tất cả thành viên dùng được
- ✅ Token lưu trong `.env` (local, không commit)
- ✅ File `.env.example` có token mẫu để team dễ setup

### ✅ NÊN:
- Lưu token trong file `.env`
- Thêm `.env` vào `.gitignore` (đã có ✅)
- Chia sẻ token qua kênh riêng tư (Discord DM, Zalo)
- Dùng file `.env.example` làm template cho team

### ❌ KHÔNG NÊN:
- Commit file `.env` lên GitHub
- Hardcode token trực tiếp trong code PHP
- Share token công khai (Facebook, GitHub Issues)
- Cố định IP (vì mỗi thành viên có IP khác nhau)

---

## 🚨 Troubleshooting

### Lỗi: "Dropdown không hiển thị dữ liệu"

**Nguyên nhân**: Token hết hạn hoặc sai.

**Cách fix**:
1. Chạy test: `test-ghn-token.php`
2. Nếu lỗi → Lấy token mới từ GHN Portal
3. Cập nhật vào `.env`
4. Test lại

---

### Lỗi: "401 Unauthorized"

**Nguyên nhân**:
- Token sai
- Token hết hạn
- IP bị chặn (nếu đã cố định IP trên GHN Portal)

**Cách fix**:
1. Kiểm tra token trong `.env` có đúng không
2. Vào GHN Portal → Token API → Copy token mới nhất
3. Nếu đã cố định IP: Kiểm tra IP hiện tại có trong danh sách không

---

### Lỗi: "Shop not found"

**Nguyên nhân**: `GHN_SHOP_ID` sai.

**Cách fix**:
1. Vào GHN Portal → Thông tin Shop
2. Copy Shop ID
3. Cập nhật vào `.env`:
   ```env
   GHN_SHOP_ID=6686325
   ```

---

## 📊 Monitoring

### Cách kiểm tra token thường xuyên:

1. **Setup cron job** (server production):
   ```bash
   # Chạy test mỗi ngày 8h sáng
   0 8 * * * curl https://your-domain.com/test-ghn-token.php
   ```

2. **Thêm alert email** khi token lỗi:
   ```php
   // Trong test-ghn-token.php
   if (!$allSuccess) {
       mail('admin@example.com', 'GHN Token Error', 'Token hết hạn, cần cập nhật!');
   }
   ```

---

## 📚 Tài liệu tham khảo

- [GHN API Documentation](https://api.ghn.vn/home/docs/detail)
- [GHN Developer Portal](https://sso.ghn.vn/manage)
- File test local: `public/test-ghn-token.php`
- Cấu hình: `.env` (dòng `GHN_API_TOKEN`)

---

## 👥 Liên hệ

Nếu gặp vấn đề với GHN Token:
1. Chạy test: `test-ghn-token.php`
2. Chụp màn hình kết quả
3. Báo team qua Slack/Discord

---

**Cập nhật lần cuối**: 2026-09-28  
**Người quản lý**: Team Backend

