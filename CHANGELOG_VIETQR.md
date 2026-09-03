# 📝 Changelog - Tích hợp VietQR + Webhook

## ✨ Các Tính Năng Mới

### 1. Module Giỏ Hàng - Giới hạn 1-100 sản phẩm
**Files đã sửa:**
- `app/controllers/CartController.php`
  - ✅ Validate quantity MIN=1, MAX=100 trong `add()` method
  - ✅ Validate khi cộng dồn sản phẩm đã có trong giỏ
  - ✅ Validate trong `update()` method
  - ✅ Kiểm tra stock trước khi thêm/cập nhật
  - ✅ Error messages chi tiết (current_quantity, max_can_add, available_stock)

**Business Rules được implement:**
- quantity < 1 → trả lỗi
- quantity > 100 → trả lỗi với thông báo rõ ràng
- quantity > stock → trả lỗi với số lượng còn lại
- Khi thêm sản phẩm đã có: kiểm tra tổng không vượt 100
- Validate ở server-side, không tin client

### 2. Thanh Toán VietQR
**Files mới:**
- `app/services/payment/VietQRPayment.php`
  - ✅ Generate QR VietQR chuẩn EMVCo
  - ✅ Tích hợp SePay/Casso (switch được qua config)
  - ✅ Parse webhook từ SePay/Casso
  - ✅ Xác thực chữ ký webhook
  - ✅ Generate order_code unique (DH + YYYYMMDD + Random)
  - ✅ Extract order_code từ nội dung chuyển khoản
  - ✅ CRC-16 checksum cho QR code
  - ✅ Remove Vietnamese diacritics

**Tính năng:**
- Generate QR động với order_code
- Hết hạn sau 15 phút
- Tự động nhận diện giao dịch qua webhook
- Support cả SePay lẫn Casso

### 3. Webhook Endpoint
**Files mới:**
- `app/controllers/WebhookController.php`
  - ✅ Xác thực signature TRƯỚC KHI xử lý
  - ✅ Log TẤT CẢ webhook vào database
  - ✅ Idempotent processing (không xử lý lại nếu đã paid)
  - ✅ So sánh số tiền (cho phép sai số ±1000đ)
  - ✅ Xử lý các trường hợp:
    - Số tiền khớp → set PAID + trừ tồn kho
    - Số tiền sai → set NEEDS_REVIEW
    - Hết hạn → set EXPIRED
    - Không tìm thấy order → log nhưng trả 200
  - ✅ Transaction database cho toàn bộ quá trình
  - ✅ Error handling và retry logic

**Bảo mật:**
- Signature validation bắt buộc
- Idempotent để tránh xử lý 2 lần
- Log chi tiết để audit
- Trả 200 ngay cả khi reject để tránh retry vô hạn

### 4. Real-time Polling
**Files mới:**
- `public/api/order-status.php`
  - ✅ API endpoint trả trạng thái đơn hàng
  - ✅ Tính time_remaining_seconds
  - ✅ Map status cho frontend (pending/paid/expired/needs_review)
  
- `public/assets/js/checkout-polling.js`
  - ✅ Poll mỗi 2.5 giây
  - ✅ Tự động dừng khi paid/expired
  - ✅ Countdown timer
  - ✅ Update UI không cần F5:
    - Ẩn QR khi paid
    - Hiện "Thanh toán thành công"
    - Hiện nút "Tạo lại QR" khi expired
  - ✅ Max retries để tránh poll vô hạn
  - ✅ Play sound khi thanh toán thành công

### 5. Cron Job - Expire Orders
**Files mới:**
- `cron/expire-orders.php`
  - ✅ Chạy mỗi phút để check đơn hết hạn
  - ✅ Set status = 'expired' cho đơn quá hạn
  - ✅ Log chi tiết
  - ✅ Error handling
  - ✅ Chỉ chạy được từ CLI

**Cài đặt:**
- Linux/Mac: crontab
- Windows: Task Scheduler

### 6. Database Migration
**Files mới:**
- `config/migrations/add_webhook_and_qr_features.sql`
  - ✅ Thêm columns: order_code, qr_image_url, expires_at, paid_at
  - ✅ Tạo bảng payment_webhook_logs
  - ✅ CHECK constraint cho quantity (1-100)
  - ✅ UNIQUE constraint (cart_id, product_id)
  - ✅ Update enum cho status và payment_method
  - ✅ Indexes cho query performance

### 7. Configuration
**Files đã sửa:**
- `.env`
  - ✅ Thêm VIETQR_PROVIDER
  - ✅ Thêm VIETQR_API_KEY
  - ✅ Thêm VIETQR_WEBHOOK_SECRET
  - ✅ BANK_CODE

- `app/core/App.php` (Router)
  - ✅ Route: POST /api/webhook/payment

---

## 📁 Cấu Trúc Files Mới

```
project-ecommerce/
├── app/
│   ├── controllers/
│   │   ├── CartController.php        [UPDATED] Thêm validate 1-100
│   │   └── WebhookController.php     [NEW] Xử lý webhook
│   └── services/
│       └── payment/
│           └── VietQRPayment.php     [NEW] VietQR service
│
├── config/
│   └── migrations/
│       └── add_webhook_and_qr_features.sql [NEW]
│
├── cron/
│   └── expire-orders.php             [NEW] Cron job
│
├── public/
│   ├── api/
│   │   └── order-status.php          [NEW] Polling API
│   └── assets/
│       └── js/
│           └── checkout-polling.js   [NEW] Frontend polling
│
├── .env                              [UPDATED] Thêm VietQR config
├── README_VIETQR_WEBHOOK.md          [NEW] Hướng dẫn đầy đủ
└── CHANGELOG_VIETQR.md               [NEW] File này
```

---

## 🔄 Luồng Hoạt Động Hoàn Chỉnh

### 1. Khách hàng thêm sản phẩm vào giỏ
```
User clicks "Thêm vào giỏ"
  ↓
POST /cart/add {product_id, quantity}
  ↓
CartController validates:
  - quantity >= 1 && quantity <= 100 ✅
  - quantity <= stock ✅
  - Nếu product đã có: (current + new) <= 100 ✅
  ↓
Success → Update giỏ hàng
```

### 2. Checkout với VietQR
```
User clicks "Đặt hàng"
  ↓
POST /checkout/process
  ↓
CheckoutController:
  - Validate thông tin
  - Tạo order với order_code unique
  - Snapshot order_items (giá tại thời điểm mua)
  ↓
VietQRPayment::createTransaction()
  - Generate VietQR string (EMVCo format)
  - Tạo QR image URL
  - Set expires_at = +15 phút
  ↓
Redirect đến trang thanh toán
  - Hiển thị QR code
  - Hiển thị countdown timer
  - Bắt đầu polling mỗi 2.5s
```

### 3. Khách quét QR và chuyển tiền
```
User quét QR → Mở banking app
  ↓
Chuyển khoản với nội dung = order_code (DH20250827XXXX)
  ↓
Ngân hàng nhận tiền
  ↓
SePay/Casso nhận thông báo SMS Banking
  ↓
SePay/Casso gọi webhook của hệ thống
```

### 4. Webhook xử lý giao dịch
```
POST /api/webhook/payment
  ↓
WebhookController::handlePayment()
  ↓
1. Log webhook vào database
2. Verify signature ✅
3. Parse transaction data
4. Extract order_code từ nội dung CK
5. Tìm order trong database
6. Check idempotent (đã paid chưa)
7. Check hết hạn chưa
8. So sánh số tiền:
   - Khớp → Set PAID + Trừ stock ✅
   - Sai → Set NEEDS_REVIEW ❓
   - Hết hạn → Set EXPIRED ⏰
9. Update webhook log: processed = 1
  ↓
Response 200 OK
```

### 5. Frontend tự động cập nhật
```
Polling mỗi 2.5s
  ↓
GET /api/order-status.php?order_code=DH...
  ↓
Nhận status mới:
  - status = 'paid' → Ẩn QR, hiện "Thành công" ✅
  - status = 'expired' → Hiện nút "Tạo lại QR" ⏰
  - status = 'needs_review' → Hiện thông báo cần xác minh ❓
  ↓
Update UI KHÔNG CẦN F5!
```

### 6. Cron job tự động hủy đơn hết hạn
```
Cron chạy mỗi phút
  ↓
Query: SELECT * FROM orders 
       WHERE payment_status = 'pending' 
         AND expires_at < NOW()
  ↓
Update status = 'expired'
  ↓
Log chi tiết
```

---

## ✅ Test Cases

### Test 1: Giới hạn giỏ hàng
- [x] Thêm 150 sản phẩm → Error "Tối đa 100"
- [x] Thêm 50, sau đó thêm 60 → Error "Vượt quá 100"
- [x] Thêm 100 sản phẩm → Success
- [x] Update quantity = 0 → Xóa khỏi giỏ
- [x] Thêm quá stock → Error với số lượng còn lại

### Test 2: VietQR
- [x] Generate QR code với order_code
- [x] QR tuân thủ chuẩn EMVCo
- [x] Nội dung chuyển khoản chứa order_code
- [x] Expires sau 15 phút

### Test 3: Webhook
- [x] Webhook sai signature → Reject (trả 200)
- [x] Webhook đúng signature → Process
- [x] Số tiền khớp → Set PAID
- [x] Số tiền sai → Set NEEDS_REVIEW
- [x] Đơn đã paid → Idempotent, không xử lý lại
- [x] Không tìm thấy order → Log và trả 200

### Test 4: Polling
- [x] Poll mỗi 2.5s
- [x] Khi paid → Tự động hiện "Thành công"
- [x] Khi expired → Hiện nút "Tạo lại QR"
- [x] Countdown timer chạy đúng
- [x] Dừng polling sau khi paid/expired

### Test 5: Cron Job
- [x] Tìm đúng đơn hết hạn
- [x] Set status = 'expired'
- [x] Log chi tiết
- [x] Không ảnh hưởng đơn đã paid

---

## 🚀 Triển Khai Production

### Checklist
1. [x] Chạy migration SQL
2. [ ] Cập nhật `.env` với API keys thật
3. [ ] Test webhook trên môi trường staging
4. [ ] Cài đặt cron job trên server
5. [ ] Setup monitoring logs
6. [ ] Backup database trước khi deploy
7. [ ] Test toàn bộ flow trên staging
8. [ ] Deploy lên production
9. [ ] Monitor webhook logs 24h đầu
10. [ ] Setup alerting cho errors

### Environment Variables Cần Thiết
```env
VIETQR_PROVIDER=sepay
VIETQR_API_KEY=<your_api_key>
VIETQR_WEBHOOK_SECRET=<your_secret>
BANK_CODE=VCB
BANK_ACCOUNT_NUMBER=1058081721
BANK_ACCOUNT_NAME=TRINH TIEN HUNG
```

---

## 📊 Monitoring

### Queries Hữu Ích

**1. Webhook logs gần nhất**
```sql
SELECT * FROM payment_webhook_logs 
ORDER BY received_at DESC 
LIMIT 20;
```

**2. Orders cần review**
```sql
SELECT order_code, total_amount, admin_note 
FROM orders 
WHERE status = 'needs_review';
```

**3. Tỷ lệ thành công thanh toán**
```sql
SELECT 
  payment_method,
  COUNT(*) as total,
  SUM(CASE WHEN payment_status = 'paid' THEN 1 ELSE 0 END) as paid,
  ROUND(SUM(CASE WHEN payment_status = 'paid' THEN 1 ELSE 0 END) * 100.0 / COUNT(*), 2) as success_rate
FROM orders
WHERE DATE(created_at) = CURDATE()
GROUP BY payment_method;
```

**4. Đơn hàng hết hạn hôm nay**
```sql
SELECT COUNT(*) as expired_today
FROM orders
WHERE status = 'expired'
  AND DATE(expires_at) = CURDATE();
```

---

## 🐛 Known Issues & Limitations

1. **Polling timeout**: Sau 10 phút (240 lần * 2.5s) sẽ dừng
   - Solution: Reload trang để tiếp tục poll
   
2. **Webhook retry**: Nếu server down, SePay/Casso sẽ retry
   - Đã implement idempotent để tránh xử lý 2 lần
   
3. **QR code caching**: Browser có thể cache QR image
   - Solution: Thêm timestamp vào URL

4. **Cron job dependency**: Cần cài đặt thủ công
   - Cân nhắc dùng Laravel Schedule hoặc Supervisor

---

## 📚 References

- VietQR Standard: https://www.emvco.com/emv-technologies/qrcodes/
- SePay API Docs: https://my.sepay.vn/userapi/docs/
- Casso API Docs: https://docs.casso.vn/
- CRC-16 CCITT: https://en.wikipedia.org/wiki/Cyclic_redundancy_check

---

**Version**: 1.0.0  
**Date**: 2025-08-27  
**Author**: DecorNest Development Team
