# 🔧 FIX: Không hiển thị QR Code VietQR

## ❌ VẤN ĐỀ:

Khi chọn "Chuyển khoản ngân hàng" và bấm "Hoàn tất đặt hàng" → Không thấy mã QR hiển thị.

## 🔍 NGUYÊN NHÂN:

File `.env` **thiếu các biến** cần thiết để tạo QR Code:
- `BANK_ACCOUNT_NAME` - Tên chủ tài khoản
- `BANK_ACCOUNT_NUMBER` - Số tài khoản
- `BANK_NAME` - Tên ngân hàng

Khi thiếu các biến này, `BankTransferPayment::createTransaction()` không tạo được QR code.

---

## ✅ CÁCH FIX (2 PHÚT):

### BƯỚC 1: Cập nhật file `.env`

Mở file `.env`, tìm dòng:
```env
# Payment Gateway - VietQR (SePay or Casso)
```

**TRƯỚC KHI SỬA** (thiếu thông tin):
```env
VIETQR_ACCOUNT_NUMBER=your_bank_account_number
VIETQR_ACCOUNT_NAME=YOUR_ACCOUNT_NAME
```

**SAU KHI SỬA** (đã điền đầy đủ):
```env
# Payment Gateway - VietQR (SePay or Casso)
VIETQR_PROVIDER=sepay
VIETQR_API_KEY=your_api_key_here
VIETQR_ACCOUNT_NUMBER=1058081721
VIETQR_ACCOUNT_NAME=TRINH TIEN HUNG
VIETQR_BANK_CODE=VCB
VIETQR_TEMPLATE=compact
VIETQR_WEBHOOK_SECRET=your_webhook_secret_here

# Bank Transfer Info (Alias cho VietQR - dùng cho hiển thị)
BANK_ACCOUNT_NAME=TRINH TIEN HUNG
BANK_ACCOUNT_NUMBER=1058081721
BANK_NAME=Vietcombank
BANK_CODE=VCB
BANK_BRANCH=Chi nhánh Hà Nội
```

**⚠️ QUAN TRỌNG**: Thay thế thông tin trên bằng **TÀI KHOẢN THẬT** của bạn:
- `1058081721` → Số tài khoản ngân hàng của bạn
- `TRINH TIEN HUNG` → Tên chủ tài khoản (IN HOA, KHÔNG DẤU)
- `VCB` → Mã ngân hàng (VCB=Vietcombank, BIDV, TCB, MB, ACB...)
- `Vietcombank` → Tên ngân hàng đầy đủ

### BƯỚC 2: Restart Server (nếu đang chạy)

Nếu đang chạy XAMPP/WAMP, không cần restart.

Nếu chạy built-in PHP server:
```bash
# Tắt server (Ctrl+C)
# Chạy lại
php -S localhost:8000 -t public
```

### BƯỚC 3: Test ngay

Chạy file test:
```
http://localhost/project-ecommerce/public/test-bank-qr.php
```

Kết quả mong đợi:
- ✅ Tất cả biến .env: màu xanh
- ✅ Hiển thị QR Code
- ✅ Thông tin tài khoản đầy đủ

### BƯỚC 4: Test Checkout thực tế

1. Thêm sản phẩm vào giỏ
2. Vào giỏ hàng → Thanh toán
3. Chọn "Chuyển khoản ngân hàng"
4. Bấm "Hoàn tất đặt hàng"
5. **Kiểm tra**: QR Code có hiển thị không?

---

## 🎯 KẾT QUẢ SAU KHI FIX:

Trang checkout-success sẽ hiển thị:

```
┌─────────────────────────────────────────┐
│  🏦 Hướng dẫn chuyển khoản VietQR       │
├─────────────────────────────────────────┤
│  Ngân hàng:      Vietcombank            │
│  Số TK:          1058081721             │
│  Chủ TK:         TRINH TIEN HUNG        │
│  Số tiền:        150,000đ               │
│  Nội dung:       DN20260924XXXX         │
├─────────────────────────────────────────┤
│        ┌───────────────┐                │
│        │               │                │
│        │   [QR CODE]   │  ← QR hiển thị│
│        │               │                │
│        └───────────────┘                │
│    Mở app ngân hàng quét mã QR          │
└─────────────────────────────────────────┘
```

---

## 🔧 TROUBLESHOOTING:

### Vẫn không hiển thị QR sau khi sửa .env

**Nguyên nhân**: Server cache biến môi trường

**Giải pháp**:
1. Xóa cache PHP (nếu có):
   ```bash
   php -r "opcache_reset();"
   ```
2. Restart Apache/Nginx
3. Xóa session:
   - Thoát đăng nhập
   - Xóa cookie trên browser
   - Đăng nhập lại

### QR Code bị lỗi 404

**Nguyên nhân**: URL VietQR API không đúng

**Giải pháp**: Kiểm tra URL trong `BankTransferPayment.php`:
```php
return "https://img.vietqr.io/image/{$bankId}-{$accountNo}-compact2.png"
```

Test URL thủ công:
```
https://img.vietqr.io/image/VCB-1058081721-compact2.png?amount=150000&addInfo=TEST
```

### Thông tin tài khoản hiển thị rỗng

**Nguyên nhân**: Biến .env không load đúng

**Giải pháp**: Kiểm tra `vendor/dotenv.php`:
```php
// File: vendor/dotenv.php
class Dotenv {
    public function load() {
        // Đảm bảo load đúng file .env
        $envFile = $this->path . '/.env';
        if (file_exists($envFile)) {
            $lines = file($envFile, FILE_IGNORE_NEW_LINES | FILE_SKIP_EMPTY_LINES);
            foreach ($lines as $line) {
                // Parse và set $_ENV
            }
        }
    }
}
```

---

## 📋 CHECKLIST:

- [ ] Cập nhật `.env` với thông tin tài khoản thật
- [ ] Restart server (nếu cần)
- [ ] Test: `test-bank-qr.php` → Thấy QR màu xanh
- [ ] Test checkout → Chọn Bank Transfer → Thấy QR
- [ ] Quét QR bằng app ngân hàng → Xem có tự động điền không

---

## ✨ SAU KHI FIX:

**Đã có:**
- ✅ QR Code VietQR hiển thị đầy đủ
- ✅ Thông tin tài khoản chính xác
- ✅ Số tiền + nội dung tự động điền

**Chưa có (cần setup thêm):**
- ⚠️ Webhook tự động xác nhận (cần đăng ký SePay)
- ⚠️ Polling real-time update (cần webhook)

Để có tự động xác nhận, xem: `VIETQR_SETUP_GUIDE.md`

---

## 📞 HỖ TRỢ:

Nếu vẫn lỗi, chạy file debug:
```
http://localhost/project-ecommerce/public/test-bank-qr.php
```

Copy kết quả và gửi cho team.
