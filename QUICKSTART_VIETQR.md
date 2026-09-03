# ⚡ Quick Start - VietQR + Webhook

## 🎯 Tóm Tắt

Tôi đã bổ sung **HOÀN CHỈNH** các module sau vào project của bạn:

✅ **Giỏ hàng**: Giới hạn 1-100 sản phẩm (strict validation)  
✅ **VietQR Payment**: Tích hợp SePay/Casso  
✅ **Webhook**: Tự động nhận giao dịch với xác thực chữ ký  
✅ **Real-time Polling**: Frontend tự động cập nhật (không cần F5)  
✅ **Cron Job**: Tự động hủy đơn hết hạn  
✅ **Database Migration**: Schema đầy đủ với logging  

---

## 🚀 Bắt Đầu Ngay (3 Bước)

### BƯỚC 1: Setup Database (2 phút)

```bash
# Mở phpMyAdmin hoặc MySQL CLI
mysql -u root -p decornest < config/migrations/add_webhook_and_qr_features.sql
```

Hoặc chạy script setup tự động:
```
http://localhost/project-ecommerce/public/setup-complete.php
```

### BƯỚC 2: Cấu Hình VietQR (5 phút)

**Option A: Dùng SePay** (Khuyến nghị - Dễ setup)
1. Đăng ký: https://my.sepay.vn/
2. Lấy API Key trong Settings
3. Cập nhật `.env`:
```env
VIETQR_PROVIDER=sepay
VIETQR_API_KEY=your_sepay_api_key_here
VIETQR_WEBHOOK_SECRET=your_webhook_secret_here
```
4. Trong SePay Dashboard → Webhook URL:
```
https://yourdomain.com/project-ecommerce/public/api/webhook/payment
```

**Option B: Dùng Casso**
1. Đăng ký: https://casso.vn/
2. Lấy API Key và Secure Token
3. Cập nhật `.env`:
```env
VIETQR_PROVIDER=casso
CASSO_API_KEY=your_api_key
VIETQR_WEBHOOK_SECRET=your_secure_token
```

### BƯỚC 3: Test Thử (2 phút)

```bash
# 1. Chạy local site
http://localhost/project-ecommerce/public

# 2. Thêm sản phẩm vào giỏ (test giới hạn 1-100)
# Thử thêm 150 sản phẩm → sẽ bị reject

# 3. Checkout → Chọn "Thanh toán VietQR"
# Sẽ thấy QR code + countdown timer

# 4. Mở Console browser → thấy polling log mỗi 2.5s
# [Polling] Status: pending
```

---

## 📁 Files Quan Trọng Đã Được Tạo

```
📦 CONTROLLERS
├── app/controllers/CartController.php        [✏️ UPDATED] Validate 1-100
├── app/controllers/WebhookController.php     [🆕 NEW] Xử lý webhook
│
📦 SERVICES  
├── app/services/payment/VietQRPayment.php    [🆕 NEW] VietQR logic
│
📦 API ENDPOINTS
├── public/api/order-status.php               [🆕 NEW] Polling API
│
📦 FRONTEND
├── public/assets/js/checkout-polling.js      [🆕 NEW] Real-time update
│
📦 CRON
├── cron/expire-orders.php                    [🆕 NEW] Auto expire
│
📦 DATABASE
├── config/migrations/add_webhook_and_qr_features.sql  [🆕 NEW]
│
📦 DOCS
├── README_VIETQR_WEBHOOK.md                  [🆕 NEW] Hướng dẫn đầy đủ
├── CHANGELOG_VIETQR.md                       [🆕 NEW] Chi tiết thay đổi
└── QUICKSTART_VIETQR.md                      [🆕 NEW] File này
```

---

## 🧪 Test Nhanh

### Test 1: Giỏ hàng giới hạn 1-100
```javascript
// Mở Console trên trang sản phẩm
fetch('/project-ecommerce/public/cart/add', {
  method: 'POST',
  headers: {'Content-Type': 'application/x-www-form-urlencoded'},
  body: 'product_id=1&quantity=150'
}).then(r => r.json()).then(console.log);

// Expected: {"success":false, "message":"Số lượng tối đa cho mỗi sản phẩm là 100"}
```

### Test 2: Webhook (Mock test)
```bash
# Test signature sai → sẽ reject
curl -X POST http://localhost/project-ecommerce/public/api/webhook/payment \
  -H "Content-Type: application/json" \
  -d '{"data":{"amount_in":500000,"transaction_content":"DH20250827TEST"}}'

# Expected: HTTP 200, {"status":"rejected","reason":"Invalid signature"}
```

### Test 3: Polling
1. Checkout một đơn hàng
2. Mở Console browser
3. Thấy log:
```
[Polling] Initialized for order: DH20250827XXXX
[Polling] Started
[Polling] Status: pending
```

### Test 4: Cron job
```bash
# Chạy thủ công
php cron/expire-orders.php

# Expected output:
# [2025-08-27 14:30:00] Starting expire orders cron job...
# Found 2 expired order(s).
#   - Expired order: DH20250827XXXX
# Cron job completed in 45.23ms.
```

---

## 🔧 Cài Đặt Cron Job

### Windows (Task Scheduler)
1. Mở Task Scheduler
2. Create Basic Task → "Expire Orders"
3. Trigger: Daily, Repeat every 1 minute
4. Action: Start a program
   - Program: `C:\xampp\php\php.exe`
   - Arguments: `C:\xampp\htdocs\project-ecommerce\cron\expire-orders.php`

### Linux/Mac (crontab)
```bash
crontab -e

# Thêm dòng:
* * * * * php /path/to/project/cron/expire-orders.php >> /path/to/logs/cron.log 2>&1
```

---

## 📊 Monitor Logs

### Check webhook logs
```sql
SELECT id, order_code, signature_valid, processed, received_at 
FROM payment_webhook_logs 
ORDER BY received_at DESC 
LIMIT 10;
```

### Check orders cần review
```sql
SELECT order_code, total_amount, status, payment_status 
FROM orders 
WHERE status = 'needs_review';
```

---

## 🎓 Luồng Hoạt Động (Tóm Tắt)

```
1. User thêm vào giỏ → Validate 1-100 ✅
   ↓
2. Checkout → Tạo QR VietQR + order_code ✅
   ↓
3. Hiển thị QR + bắt đầu polling 2.5s ✅
   ↓
4. User quét QR → Chuyển khoản ✅
   ↓
5. SePay/Casso → Webhook đến server ✅
   ↓
6. Server verify signature + xử lý:
   - Số tiền khớp → PAID + trừ stock ✅
   - Số tiền sai → NEEDS_REVIEW ❓
   ↓
7. Frontend polling nhận status → Update UI tự động ✅
   ↓
8. Cron job: Tự động hủy đơn hết hạn mỗi phút ✅
```

---

## 📚 Đọc Thêm

- **Chi tiết đầy đủ**: `README_VIETQR_WEBHOOK.md`
- **Changelog**: `CHANGELOG_VIETQR.md`
- **Test cases**: Xem phần "Test Workflow" trong README

---

## ❓ Câu Hỏi Thường Gặp

**Q: Tôi chưa có API key SePay/Casso, có test được không?**  
A: Có! Tất cả code đã sẵn sàng. Chỉ cần đăng ký (5 phút) là dùng được ngay.

**Q: Webhook hoạt động như thế nào?**  
A: SePay/Casso nhận SMS Banking → Tự động POST đến `/api/webhook/payment` → Server xử lý → Polling nhận status → UI update.

**Q: Tại sao cần cron job?**  
A: Để tự động hủy các đơn hàng quá 15 phút không thanh toán.

**Q: Polling có tốn tài nguyên không?**  
A: Không. Chỉ poll khi user đang ở trang thanh toán. Poll 2.5s/lần, tối đa 10 phút rồi dừng.

**Q: Làm sao test webhook trên localhost?**  
A: Dùng **ngrok** để expose localhost ra internet:
```bash
ngrok http 80
# Copy URL https://xxxx.ngrok.io/project-ecommerce/public/api/webhook/payment
# Paste vào SePay/Casso Webhook URL
```

---

## ✅ Checklist Hoàn Thành

### Setup Cơ Bản
- [ ] Chạy migration SQL
- [ ] Cập nhật `.env` với VietQR config
- [ ] Test thêm sản phẩm vào giỏ (giới hạn 1-100)
- [ ] Test checkout tạo QR code

### Tích Hợp Webhook (Optional nếu có API key)
- [ ] Đăng ký SePay/Casso
- [ ] Lấy API key
- [ ] Cấu hình webhook URL
- [ ] Test webhook với giao dịch thật

### Production Ready
- [ ] Cài đặt cron job
- [ ] Setup monitoring logs
- [ ] Test toàn bộ flow trên staging
- [ ] Backup database

---

## 🎉 Kết Luận

Bạn đã có một hệ thống thanh toán VietQR **HOÀN CHỈNH**:
- ✅ Giỏ hàng với validation chặt chẽ
- ✅ QR code tự động
- ✅ Webhook bảo mật cao
- ✅ Real-time update không cần F5
- ✅ Tự động expire orders
- ✅ Logging đầy đủ

**Chỉ cần thêm API key là chạy được ngay! 🚀**

---

**Liên hệ**: tienhungtrinh59@gmail.com  
**Last Updated**: 2025-08-27
