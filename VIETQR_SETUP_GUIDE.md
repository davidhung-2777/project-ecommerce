# ✅ VIETQR API ĐÃ CÓ SẴN - HƯỚNG DẪN SETUP

## 📋 KIỂM TRA:

### ✅ Code VietQR đã có:
- ✅ `app/services/payment/VietQRPayment.php` - Service đầy đủ
- ✅ `app/services/payment/SepayService.php` - Tích hợp SePay
- ✅ `app/controllers/WebhookController.php` - Webhook handler
- ✅ `public/api/order-status.php` - Polling API
- ✅ `public/assets/js/checkout-polling.js` - Frontend polling

### ⚠️ Chưa cấu hình:
File `.env` có các biến nhưng **chưa điền giá trị thực**:
```env
VIETQR_API_KEY=your_api_key_here              # ❌ Chưa có
VIETQR_ACCOUNT_NUMBER=your_bank_account_number # ❌ Chưa có
VIETQR_ACCOUNT_NAME=YOUR_ACCOUNT_NAME          # ❌ Chưa có
VIETQR_WEBHOOK_SECRET=your_webhook_secret_here # ❌ Chưa có
```

---

## 🎯 2 PROVIDER HỖ TRỢ:

Bạn có thể chọn **1 trong 2** provider:

### 1️⃣ SePay (Đề xuất - Dễ setup)
- **Website**: https://my.sepay.vn/
- **Ưu điểm**: 
  - Miễn phí gói cơ bản
  - Setup nhanh chóng
  - Webhook real-time (1-3s)
  - Hỗ trợ nhiều ngân hàng (VCB, VietinBank, BIDV, MB, ACB...)
- **Nhược điểm**: 
  - API key có giới hạn request
  - Cần xác minh tài khoản

### 2️⃣ Casso
- **Website**: https://casso.vn/
- **Ưu điểm**:
  - Webhook nhanh
  - Dashboard đẹp
  - Báo cáo chi tiết
- **Nhược điểm**:
  - Có phí (từ 50k/tháng)
  - Setup phức tạp hơn

---

## 🚀 HƯỚNG DẪN SETUP SEPAY (KHUYẾN NGHỊ):

### BƯỚC 1: Đăng ký SePay

1. Truy cập: https://my.sepay.vn/register
2. Đăng ký tài khoản (Email + Password)
3. Xác thực email
4. Đăng nhập vào dashboard

### BƯỚC 2: Liên kết tài khoản ngân hàng

1. Vào menu **"Tài khoản ngân hàng"** (Bank Accounts)
2. Click **"Thêm tài khoản"**
3. Chọn ngân hàng: Vietcombank, VietinBank, BIDV, etc.
4. Nhập:
   - **Số tài khoản**: 1058081721
   - **Tên chủ tài khoản**: NGUYEN VAN A
5. Xác thực bằng cách:
   - Chuyển khoản 1,000đ với nội dung xác thực
   - Hoặc gửi ảnh CCCD + sao kê
6. Đợi SePay duyệt (5-10 phút)

### BƯỚC 3: Lấy API Key

1. Vào menu **"Cài đặt"** → **"API"**
2. Click **"Tạo API Key mới"**
3. Copy API Key (dạng: `sp_live_xxxxxxxxxxxx`)
4. **LƯU LẠI** - Chỉ hiển thị 1 lần!

### BƯỚC 4: Setup Webhook

1. Vào menu **"Webhook"**
2. Click **"Thêm webhook"**
3. Nhập:
   - **URL**: `https://your-domain.com/webhook/sepay`
   - **Events**: Chọn "Transaction received"
   - **Secret**: Tự động generate (copy lại)
4. Click **"Lưu"**
5. Test webhook bằng nút **"Test"**

### BƯỚC 5: Cấu hình `.env`

Mở file `.env`, cập nhật:

```env
# VietQR/SePay Configuration
VIETQR_PROVIDER=sepay
VIETQR_API_KEY=sp_live_xxxxxxxxxxxxxxxxxxxxxx
VIETQR_ACCOUNT_NUMBER=1058081721
VIETQR_ACCOUNT_NAME=NGUYEN VAN A
VIETQR_BANK_CODE=VCB
VIETQR_TEMPLATE=compact
VIETQR_WEBHOOK_SECRET=whsec_xxxxxxxxxxxxxxxxxxxxxx

# Bank Info (hiển thị cho user)
BANK_NAME=Vietcombank
```

**Thay thế:**
- `sp_live_xxx...` = API Key từ BƯỚC 3
- `1058081721` = Số tài khoản ngân hàng của bạn
- `NGUYEN VAN A` = Tên chủ tài khoản (IN HOA, không dấu)
- `VCB` = Mã ngân hàng (VCB = Vietcombank, VTB = VietinBank, BIDV, TCB, MB...)
- `whsec_xxx...` = Webhook Secret từ BƯỚC 4

### BƯỚC 6: Cập nhật SepayService

File `app/services/payment/SepayService.php` đã có sẵn, nhưng cần đảm bảo đọc đúng biến môi trường:

Kiểm tra constructor:
```php
public function __construct()
{
    $this->apiKey = $_ENV['VIETQR_API_KEY'] ?? '';
    $this->accountNumber = $_ENV['VIETQR_ACCOUNT_NUMBER'] ?? '';
    $this->webhookSecret = $_ENV['VIETQR_WEBHOOK_SECRET'] ?? '';
}
```

Nếu không đúng, sửa lại thành:
```php
public function __construct()
{
    $this->apiKey = $_ENV['VIETQR_API_KEY'] ?? '';
    $this->accountNumber = $_ENV['VIETQR_ACCOUNT_NUMBER'] ?? '';
    $this->webhookSecret = $_ENV['VIETQR_WEBHOOK_SECRET'] ?? '';
    $this->apiUrl = 'https://my.sepay.vn/userapi';
}
```

---

## 🧪 TEST:

### Test 1: Kiểm tra API connection

Tạo file `public/test-sepay-connection.php`:

```php
<?php
require_once __DIR__ . '/../vendor/autoload.php';

$dotenv = new Dotenv(__DIR__ . '/..');
$dotenv->load();

$sepay = new \App\Services\Payment\SepayService();
$result = $sepay->testConnection();

header('Content-Type: application/json');
echo json_encode($result, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE);
```

Chạy:
```
http://localhost/project-ecommerce/public/test-sepay-connection.php
```

Kết quả mong đợi:
```json
{
  "success": true,
  "message": "Kết nối SePay API thành công!",
  "accounts": [
    {
      "account_number": "1058081721",
      "bank_name": "Vietcombank",
      "status": "active"
    }
  ]
}
```

### Test 2: Generate QR Code

Tạo file `public/test-qr-generate.php`:

```php
<?php
require_once __DIR__ . '/../vendor/autoload.php';

$dotenv = new Dotenv(__DIR__ . '/..');
$dotenv->load();

$sepay = new \App\Services\Payment\SepayService();
$qr = $sepay->generateQRCode(
    150000,              // Số tiền
    'DN20260924TEST',    // Mã đơn
    'Thanh toan don hang test'
);

?>
<!DOCTYPE html>
<html>
<head>
    <title>Test QR Code</title>
</head>
<body>
    <h1>QR Code Test</h1>
    <?php if ($qr['success']): ?>
        <img src="<?= $qr['qr_code_url'] ?>" alt="QR Code">
        <p>Số tiền: <?= number_format($qr['amount']) ?>đ</p>
        <p>Nội dung: <?= $qr['content'] ?></p>
        <p>Tài khoản: <?= $qr['account_number'] ?></p>
        <p>Ngân hàng: <?= $qr['bank_name'] ?></p>
    <?php else: ?>
        <p style="color: red;">Lỗi: <?= $qr['message'] ?? 'Unknown' ?></p>
    <?php endif; ?>
</body>
</html>
```

Chạy:
```
http://localhost/project-ecommerce/public/test-qr-generate.php
```

### Test 3: Test Webhook (Manual)

Dùng Postman hoặc curl:

```bash
curl -X POST http://localhost/project-ecommerce/webhook/sepay \
  -H "Content-Type: application/json" \
  -H "X-Sepay-Signature: test_signature" \
  -H "X-Sepay-Timestamp: $(date +%s)" \
  -d '{
    "id": "12345",
    "reference_number": "FT21234567890",
    "amount_in": 150000,
    "transaction_content": "DN20260924TEST Thanh toan don hang",
    "transaction_date": "2026-09-24 10:30:45",
    "account_number": "1058081721",
    "bank_brand_name": "VCB",
    "gateway": "VCB"
  }'
```

Xem logs:
```
logs/webhook-2026-09-24.log
```

### Test 4: Test Full Flow

1. Thêm sản phẩm vào giỏ
2. Checkout → Chọn "Chuyển khoản ngân hàng"
3. Đặt hàng → Xem QR Code hiển thị
4. Quét QR bằng app ngân hàng
5. Chuyển khoản (thật hoặc test)
6. Đợi 2-5s → Trang tự động cập nhật "Đã thanh toán"

---

## 🔧 TROUBLESHOOTING:

### Lỗi: "API Key không hợp lệ"
**Nguyên nhân**: API Key sai hoặc hết hạn
**Giải pháp**:
1. Kiểm tra API Key trong `.env`
2. Tạo API Key mới trên SePay dashboard
3. Copy đúng toàn bộ key (không thừa/thiếu ký tự)

### Lỗi: "Account not found"
**Nguyên nhân**: Số tài khoản chưa được liên kết với SePay
**Giải pháp**:
1. Vào SePay → Tài khoản ngân hàng
2. Kiểm tra trạng thái tài khoản: "Active"
3. Nếu "Pending" → Đợi duyệt hoặc xác thực lại

### Webhook không nhận được
**Nguyên nhân**: 
- URL sai
- Server chặn POST request
- Firewall block

**Giải pháp**:
1. Test endpoint: `/webhook/sepay/test`
2. Kiểm tra URL trong SePay dashboard
3. Check server logs: `logs/webhook-*.log`
4. Thử disable firewall tạm thời

### QR Code không hiển thị
**Nguyên nhân**: Thiếu thông tin tài khoản
**Giải pháp**:
1. Kiểm tra `.env` có đủ:
   - VIETQR_ACCOUNT_NUMBER
   - VIETQR_ACCOUNT_NAME
   - VIETQR_BANK_CODE
2. Restart server sau khi sửa `.env`

---

## 📊 FLOW HOẠT ĐỘNG:

```
1. User chọn "Chuyển khoản ngân hàng"
   ↓
2. Hệ thống generate QR Code VietQR
   - Số tiền: 150,000đ
   - Nội dung: DN20260924TEST
   - Tài khoản: 1058081721 (VCB)
   ↓
3. User quét QR → Chuyển khoản
   ↓
4. Ngân hàng → SePay (1-3 giây)
   ↓
5. SePay → Webhook → Server
   POST /webhook/sepay
   ↓
6. WebhookController:
   - Parse transaction
   - Extract order code: DN20260924TEST
   - Verify amount: 150,000 = 150,000 ✓
   - Update order.payment_status = 'paid'
   - Update order.status = 'confirmed'
   ↓
7. Frontend polling (mỗi 2.5s):
   GET /api/order-status.php?order_code=DN20260924TEST
   → payment_status = 'paid'
   → Hiển thị "✅ Thanh toán thành công!"
```

---

## ✅ CHECKLIST HOÀN TẤT:

- [ ] Đăng ký SePay
- [ ] Liên kết tài khoản ngân hàng
- [ ] Lấy API Key
- [ ] Setup Webhook URL
- [ ] Cấu hình `.env` với thông tin thực
- [ ] Test connection: `test-sepay-connection.php`
- [ ] Test QR generate: `test-qr-generate.php`
- [ ] Test webhook manual (Postman)
- [ ] Test full flow (đặt hàng thật)

---

## 🎉 SAU KHI SETUP:

**Hệ thống sẽ có:**
- ✅ Generate QR Code tự động cho mỗi đơn hàng
- ✅ Webhook real-time từ SePay (1-3s)
- ✅ Tự động xác nhận thanh toán
- ✅ Frontend auto-refresh không cần F5
- ✅ Logs đầy đủ để debug
- ✅ Bảo mật với signature verification

**→ Thanh toán chuyển khoản tự động 100% như MoMo/VNPay!** 🚀
