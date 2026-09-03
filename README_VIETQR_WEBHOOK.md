# 🚀 Hướng Dẫn Tích Hợp VietQR + Webhook

## 📋 Tổng Quan

Project này đã được tích hợp đầy đủ:
- ✅ Giỏ hàng với giới hạn 1-100 sản phẩm
- ✅ Thanh toán VietQR (SePay/Casso)
- ✅ Webhook tự động nhận giao dịch
- ✅ Real-time polling (không cần F5)
- ✅ Cron job tự động hủy đơn hết hạn

---

## 🛠️ Setup Database

### Bước 1: Chạy migration
```bash
mysql -u root -p decornest < config/migrations/add_webhook_and_qr_features.sql
```

Hoặc import qua phpMyAdmin.

### Bước 2: Kiểm tra tables mới
- `payment_webhook_logs` - Log tất cả webhook
- `orders` table có thêm: `order_code`, `qr_image_url`, `expires_at`, `paid_at`
- `cart_items` có CHECK constraint: quantity BETWEEN 1 AND 100

---

## 🔑 Cấu Hình API Keys

### Chọn Provider: SePay hoặc Casso

**Option 1: SePay** (Khuyến nghị)
1. Đăng ký tài khoản tại: https://my.sepay.vn/
2. Tạo API Key trong phần Settings
3. Copy Webhook Secret
4. Cập nhật file `.env`:

```env
VIETQR_PROVIDER=sepay
VIETQR_API_KEY=your_sepay_api_key_here
VIETQR_WEBHOOK_SECRET=your_webhook_secret_here
BANK_CODE=VCB
```

5. Cấu hình Webhook URL trong SePay Dashboard:
```
https://yourdomain.com/project-ecommerce/public/api/webhook/payment
```

**Option 2: Casso**
1. Đăng ký tại: https://casso.vn/
2. Lấy API Key và Secure Token
3. Cập nhật `.env`:

```env
VIETQR_PROVIDER=casso
CASSO_API_KEY=your_casso_api_key_here
VIETQR_WEBHOOK_SECRET=your_casso_secure_token_here
BANK_CODE=VCB
```

4. Cấu hình webhook trong Casso Dashboard

---

## 📱 Luồng Thanh Toán VietQR

```
1. Khách hàng checkout
   ↓
2. Hệ thống tạo order_code (DH20250827XXXX)
   ↓
3. Generate QR VietQR với nội dung = order_code
   ↓
4. Hiển thị QR + bắt đầu polling status mỗi 2.5s
   ↓
5. Khách quét QR bằng banking app, chuyển tiền
   ↓
6. SePay/Casso nhận giao dịch qua SMS Banking
   ↓
7. SePay/Casso gửi webhook đến server
   ↓
8. Server xác thực signature + xử lý:
   - Số tiền khớp → set PAID + trừ tồn kho
   - Số tiền sai → set NEEDS_REVIEW
   - Hết hạn → set EXPIRED
   ↓
9. Frontend polling nhận status mới → update UI tự động
   ↓
10. Hiển thị "Thanh toán thành công" (không cần F5!)
```

---

## 🔒 Bảo Mật Webhook

### Xác thực chữ ký

**SePay**: Gửi signature trong header `X-Sepay-Signature`
```php
$signature = $_SERVER['HTTP_X_SEPAY_SIGNATURE'];
$calculatedSignature = hash_hmac('sha256', $rawPayload, $webhookSecret);
return hash_equals($calculatedSignature, $signature);
```

**Casso**: Gửi `secure_token` trong payload
```php
$receivedToken = $payload['secure_token'];
return hash_equals($webhookSecret, $receivedToken);
```

### Idempotent Processing
- Kiểm tra `payment_status === 'paid'` → bỏ qua nếu đã xử lý
- Tránh trừ tồn kho 2 lần khi webhook bị gọi lại

### Logging
- Log TẤT CẢ webhook vào `payment_webhook_logs`
- Signature không hợp lệ → vẫn log + trả 200
- Không tìm thấy order → vẫn log + trả 200

---

## ⏰ Cron Job - Tự động hủy đơn hết hạn

### Cài đặt trên Linux/Mac
```bash
crontab -e
```

Thêm dòng:
```
* * * * * php /path/to/project/cron/expire-orders.php >> /path/to/logs/cron.log 2>&1
```

### Cài đặt trên Windows (Task Scheduler)
1. Mở Task Scheduler
2. Create Basic Task
3. Trigger: Repeat every 1 minute
4. Action: Start a program
   - Program: `C:\xampp\php\php.exe`
   - Arguments: `C:\xampp\htdocs\project-ecommerce\cron\expire-orders.php`

### Test thủ công
```bash
php cron/expire-orders.php
```

---

## 🧪 Test Workflow

### Test 1: Thêm sản phẩm vào giỏ (Giới hạn 1-100)

**Test case 1.1**: Thêm quá 100 sản phẩm
```javascript
// Console browser
fetch('/project-ecommerce/public/cart/add', {
  method: 'POST',
  headers: {'Content-Type': 'application/x-www-form-urlencoded'},
  body: 'product_id=1&quantity=150'
}).then(r => r.json()).then(console.log);

// Expected: {"success":false, "message":"Số lượng tối đa cho mỗi sản phẩm là 100"}
```

**Test case 1.2**: Thêm 50, sau đó thêm 60 nữa (tổng 110)
```javascript
// Lần 1: Thêm 50
fetch('/project-ecommerce/public/cart/add', {
  method: 'POST',
  body: 'product_id=1&quantity=50'
}).then(r => r.json()).then(console.log);
// Expected: success

// Lần 2: Thêm 60 (tổng = 110, vượt quá 100)
fetch('/project-ecommerce/public/cart/add', {
  method: 'POST',
  body: 'product_id=1&quantity=60'
}).then(r => r.json()).then(console.log);
// Expected: {"success":false, "message":"Tổng số lượng vượt quá giới hạn 100 sản phẩm"}
```

**Test case 1.3**: Update quantity = 0 (xóa sản phẩm)
```javascript
fetch('/project-ecommerce/public/cart/update', {
  method: 'POST',
  body: 'item_id=1&quantity=0'
}).then(r => r.json()).then(console.log);
// Expected: success (sản phẩm bị xóa khỏi giỏ)
```

### Test 2: Webhook với signature sai

**Test case 2.1**: POST webhook không có signature
```bash
curl -X POST http://localhost/project-ecommerce/public/api/webhook/payment \
  -H "Content-Type: application/json" \
  -d '{"data":{"amount_in":500000,"transaction_content":"DH20250827TEST"}}'
  
# Expected: HTTP 200, {"status":"rejected","reason":"Invalid signature"}
```

**Test case 2.2**: POST webhook với signature đúng (dùng secret key thật)
```bash
# Generate signature
payload='{"data":{"amount_in":500000,"transaction_content":"DH20250827TEST"}}'
secret="your_webhook_secret_here"
signature=$(echo -n "$payload" | openssl dgst -sha256 -hmac "$secret" | awk '{print $2}')

curl -X POST http://localhost/project-ecommerce/public/api/webhook/payment \
  -H "Content-Type: application/json" \
  -H "X-Sepay-Signature: $signature" \
  -d "$payload"
  
# Expected: Xử lý thành công hoặc "Order not found"
```

### Test 3: Polling real-time

1. Tạo đơn hàng mới (checkout)
2. Mở Console browser trên trang thanh toán
3. Quan sát log polling mỗi 2.5 giây:
```
[Polling] Status: pending
[Polling] Status: pending
...
```
4. Gửi webhook giả lập để set PAID
5. Quan sát UI tự động chuyển sang "Thanh toán thành công" (không cần F5)

### Test 4: Order hết hạn

1. Tạo order mới
2. Đợi 15 phút KHÔNG thanh toán
3. Chạy cron job thủ công:
```bash
php cron/expire-orders.php
```
4. Kiểm tra database: `SELECT * FROM orders WHERE status='expired'`
5. Refresh trang thanh toán → Hiện "Đơn hàng đã hết hạn"

---

## 📊 Monitoring

### Check webhook logs
```sql
SELECT 
  id, 
  order_code, 
  signature_valid, 
  processed, 
  error_message,
  received_at 
FROM payment_webhook_logs 
ORDER BY received_at DESC 
LIMIT 20;
```

### Check orders cần review
```sql
SELECT 
  order_code, 
  total_amount, 
  admin_note, 
  created_at 
FROM orders 
WHERE status = 'needs_review';
```

### Check expired orders
```sql
SELECT 
  order_code, 
  expires_at, 
  created_at 
FROM orders 
WHERE status = 'expired' 
  AND DATE(created_at) = CURDATE();
```

---

## 🐛 Troubleshooting

### Webhook không nhận được
1. Kiểm tra URL webhook trong SePay/Casso Dashboard
2. Đảm bảo server có thể truy cập từ internet (dùng ngrok nếu localhost)
3. Check logs: `tail -f /var/log/apache2/error.log`

### Signature luôn sai
1. Kiểm tra `VIETQR_WEBHOOK_SECRET` trong `.env`
2. Đảm bảo secret key giống với SePay/Casso Dashboard
3. Check header/payload format

### Polling không hoạt động
1. Mở Console browser, xem có lỗi JS không
2. Check API endpoint: `/project-ecommerce/public/api/order-status.php?order_code=DH...`
3. Đảm bảo `order_code` tồn tại trong database

### Cron job không chạy
1. Test thủ công: `php cron/expire-orders.php`
2. Kiểm tra crontab: `crontab -l`
3. Check log file: `tail -f /path/to/logs/cron.log`

---

## 📞 Support

Nếu gặp vấn đề, liên hệ:
- Email: tienhungtrinh59@gmail.com
- GitHub Issues: [Link repo]

---

## ✅ Checklist Triển Khai

- [ ] Chạy migration SQL
- [ ] Cập nhật `.env` với API keys
- [ ] Cấu hình webhook URL trong SePay/Casso
- [ ] Test thêm sản phẩm vào giỏ (giới hạn 1-100)
- [ ] Test webhook với signature đúng/sai
- [ ] Test polling real-time
- [ ] Cài đặt cron job expire orders
- [ ] Test đơn hàng hết hạn
- [ ] Setup monitoring/logging

**Chúc bạn thành công! 🎉**
