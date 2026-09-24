# 📋 KIỂM TRA TÍNH NĂNG DỰ ÁN DECORNEST

## ✅ 1. PHÂN QUYỀN / AUTH - HOÀN CHỈNH

**Trạng thái:** ✅ ĐÃ CÓ

**Files liên quan:**
- `app/core/Auth.php` - Class quản lý authentication & authorization
- `app/controllers/UserController.php` - Xử lý đăng nhập/đăng ký
- `app/models/UserModel.php` - Model người dùng

**Chức năng:**
- ✅ Đăng nhập/Đăng ký
- ✅ Phân quyền 2 roles: `admin` và `customer`
- ✅ Middleware: `requireAdmin()`, `requireAuth()`
- ✅ Permission-based access: `Auth::can('permission')`
- ✅ Session management với regenerate ID
- ✅ Kiểm tra quyền sở hữu resource (user chỉ xem đơn của mình)

**Tài khoản mặc định:**
- Admin: `admin@decornest.com` / `admin123`
- Customer: `tienhungtrinh59@gmail.com`

---

## ✅ 2. PAYMENT - HOÀN CHỈNH

**Trạng thái:** ✅ ĐÃ CÓ (VietQR + MoMo + VNPay + COD)

**Files liên quan:**
- `app/services/payment/VietQRPayment.php` - VietQR (SePay/Casso)
- `app/services/payment/MomoPayment.php` - Ví MoMo
- `app/services/payment/VnpayPayment.php` - VNPay
- `app/services/payment/CodPayment.php` - Ship COD
- `app/services/payment/BankTransferPayment.php` - Chuyển khoản thủ công
- `app/controllers/PaymentController.php` - Xử lý thanh toán
- `app/controllers/WebhookController.php` - Nhận webhook từ payment gateway

**Phương thức:**
- ✅ VietQR (QR Code chuyển khoản ngân hàng) - Giả lập
- ✅ MoMo (Ví điện tử) - Giả lập
- ✅ VNPay (Cổng thanh toán) - Giả lập
- ✅ COD (Thanh toán khi nhận hàng)
- ✅ Bank Transfer (Chuyển khoản thủ công)

**Webhook:**
- ✅ Webhook handler cho VietQR tự động cập nhật trạng thái thanh toán

**Lưu ý:** Paypal CHƯA CÓ (có thể thêm nếu cần)

---

## ✅ 3. QUẢN LÝ ĐỠN HÀNG - HOÀN CHỈNH

**Trạng thái:** ✅ ĐÃ CÓ

**Files liên quan:**
- `app/controllers/OrderController.php` - Xử lý đơn hàng
- `app/controllers/AdminController.php` - Quản lý admin
- `app/models/OrderModel.php` - Model đơn hàng
- `app/views/admin/pages/orders.php` - Danh sách đơn hàng
- `app/views/admin/pages/order-detail.php` - Chi tiết đơn hàng
- `app/views/frontend/pages/dashboard.php` - Đơn hàng của khách

**Chức năng:**
- ✅ Xem danh sách đơn hàng (admin & customer)
- ✅ Xem chi tiết đơn hàng
- ✅ Cập nhật trạng thái đơn hàng
  - `pending` - Chờ xác nhận
  - `confirmed` - Đã duyệt
  - `processing` - Đang đóng gói
  - `shipped` - Đang vận chuyển
  - `delivered` - Đã giao hàng
  - `cancelled` - Đã hủy
  - `expired` - Hết hạn
- ✅ Theo dõi trạng thái thanh toán
- ✅ Admin có thể xác nhận thanh toán thủ công
- ✅ Lọc và tìm kiếm đơn hàng

---

## ✅ 4. GIỎ HÀNG / CHECKOUT - HOÀN CHỈNH

**Trạng thái:** ✅ ĐÃ CÓ

**Files liên quan:**
- `app/controllers/CartController.php` - Xử lý giỏ hàng
- `app/controllers/CheckoutController.php` - Xử lý thanh toán
- `app/models/CartModel.php` - Model giỏ hàng
- `app/views/frontend/pages/cart.php` - Trang giỏ hàng
- `app/views/frontend/pages/checkout.php` - Trang thanh toán

**Chức năng:**
- ✅ Thêm sản phẩm vào giỏ hàng
- ✅ Cập nhật số lượng
- ✅ Xóa sản phẩm
- ✅ Mini cart (hiển thị nhanh)
- ✅ Giỏ hàng cho cả user đăng nhập và guest (dùng session)
- ✅ Kiểm tra tồn kho khi thêm vào giỏ
- ✅ Checkout với nhiều phương thức thanh toán
- ✅ Tính phí vận chuyển (miễn phí từ 5.000.000đ)
- ✅ Hỗ trợ hóa đơn VAT (B2B)

---

## ✅ 5. DASHBOARD QUẢN LÝ - HOÀN CHỈNH

**Trạng thái:** ✅ ĐÃ CÓ

**Files liên quan:**
- `app/controllers/DashboardController.php` - Controller dashboard
- `app/views/admin/pages/dashboard.php` - Giao diện dashboard
- `app/models/OrderModel.php` - Thống kê đơn hàng
- `app/models/ProductModel.php` - Thống kê sản phẩm

**Chức năng:**
- ✅ Tổng quan doanh thu
- ✅ Thống kê đơn hàng theo trạng thái
- ✅ Thống kê sản phẩm (tổng, active, sắp hết hàng)
- ✅ Danh sách đơn hàng mới nhất
- ✅ Biểu đồ và số liệu (có thể mở rộng)

---

## ✅ 6. QUẢN LÝ TỒN KHO - HOÀN CHỈNH

**Trạng thái:** ✅ ĐÃ CÓ (Có kiểm tra nhưng CHƯA TỰ ĐỘNG TRỪ)

**Files liên quan:**
- `app/controllers/CartController.php` - Kiểm tra tồn kho khi thêm giỏ
- `app/models/ProductModel.php` - Quản lý stock
- Database: Cột `stock` trong bảng `products`

**Chức năng có:**
- ✅ Lưu trữ số lượng tồn kho trong DB
- ✅ Kiểm tra tồn kho khi thêm vào giỏ hàng
- ✅ Hiển thị cảnh báo "Sắp hết hàng" (stock < 5)
- ✅ Không cho phép thêm vào giỏ nếu hết hàng
- ✅ Hiển thị "Còn hàng" / "Hết hàng" trên sản phẩm

**Chức năng CHƯA CÓ:**
- ⚠️ **TỰ ĐỘNG TRỪ TỒN KHO** khi đơn hàng được xác nhận
- ⚠️ **HOÀN TRỢ TỒN KHO** khi đơn hàng bị hủy
- ⚠️ **LỊCH SỬ XUẤT NHẬP KHO**

**Cần bổ sung:**
```php
// Trong OrderController khi xác nhận đơn hàng
public function confirmOrder($orderId) {
    $order = $this->orderModel->find($orderId);
    $orderDetails = $this->orderDetailModel->getByOrder($orderId);
    
    // Trừ tồn kho
    foreach ($orderDetails as $item) {
        $product = $this->productModel->find($item['product_id']);
        $newStock = $product['stock'] - $item['quantity'];
        
        if ($newStock < 0) {
            throw new Exception("Không đủ tồn kho cho sản phẩm: " . $product['name']);
        }
        
        $this->productModel->update($item['product_id'], [
            'stock' => $newStock
        ]);
    }
    
    // Cập nhật trạng thái đơn hàng
    $this->orderModel->update($orderId, ['status' => 'confirmed']);
}
```

---

## ⚠️ 7. NOTIFICATION / EMAIL - CHƯA HOÀN CHỈNH

**Trạng thái:** ⚠️ CHƯA CÓ (Đã chuẩn bị config nhưng chưa implement)

**Files liên quan:**
- `.env` - Có config MAIL nhưng chưa dùng
- `app/controllers/WebhookController.php` - Có comment `// Send notification email (optional)`

**Chức năng cần:**
- ❌ Email xác nhận đơn hàng
- ❌ Email cập nhật trạng thái đơn hàng
- ❌ Email thông báo thanh toán thành công
- ❌ Email xác thực tài khoản
- ❌ Email reset password
- ❌ Thông báo trong hệ thống (notification bell)

**Cần bổ sung:**
1. Cài đặt PHPMailer: `composer require phpmailer/phpmailer`
2. Tạo `app/services/EmailService.php`
3. Tạo email templates trong `app/views/emails/`
4. Thêm gửi email vào các action:
   - Sau khi checkout thành công
   - Khi admin cập nhật trạng thái đơn
   - Khi thanh toán được xác nhận

---

## ❌ 8. TÍCH HỢP VẬN CHUYỂN - CHƯA CÓ

**Trạng thái:** ❌ CHƯA CÓ (Chỉ có phí ship cố định)

**Hiện tại:**
- ✅ Có thông tin giao hàng (tên, SĐT, địa chỉ, thành phố, quận/huyện)
- ✅ Có cột `shipping_fee` trong bảng `orders`
- ✅ Phí ship cố định: 50.000đ (miễn phí từ 5.000.000đ)

**Chức năng CHƯA CÓ:**
- ❌ Tích hợp API đơn vị vận chuyển (GHN, GHTK, Viettel Post, J&T)
- ❌ Tính phí ship tự động theo địa chỉ và cân nặng
- ❌ Tracking đơn hàng (mã vận đơn)
- ❌ In phiếu giao hàng
- ❌ Cập nhật trạng thái từ API vận chuyển

**Đơn vị vận chuyển phổ biến Việt Nam:**
1. **Giao Hàng Nhanh (GHN)** - API: https://api.ghn.vn/
2. **Giao Hàng Tiết Kiệm (GHTK)** - API: https://khachhang.giaohangtietkiem.vn/
3. **Viettel Post** - API: https://viettelpost.vn/
4. **J&T Express** - API: https://jtexpress.vn/

**Hướng dẫn tích hợp (Ví dụ GHN):**

### Bước 1: Đăng ký tài khoản và lấy API Key
- Đăng ký tại: https://saleor.ghn.vn/
- Lấy API Token và Shop ID

### Bước 2: Thêm vào `.env`
```env
# Shipping - GHN
GHN_API_TOKEN=your_ghn_token_here
GHN_SHOP_ID=your_shop_id
GHN_API_URL=https://dev-online-gateway.ghn.vn/shiip/public-api
```

### Bước 3: Tạo Service
```php
// app/services/GhnShippingService.php
<?php

namespace App\Services;

class GhnShippingService
{
    private string $apiToken;
    private string $shopId;
    private string $apiUrl;
    
    public function __construct()
    {
        $this->apiToken = $_ENV['GHN_API_TOKEN'];
        $this->shopId = $_ENV['GHN_SHOP_ID'];
        $this->apiUrl = $_ENV['GHN_API_URL'];
    }
    
    /**
     * Tính phí vận chuyển
     */
    public function calculateShippingFee(array $data): array
    {
        $endpoint = $this->apiUrl . '/v2/shipping-order/fee';
        
        $payload = [
            'from_district_id' => $data['from_district_id'], // Kho hàng
            'to_district_id' => $data['to_district_id'],
            'to_ward_code' => $data['to_ward_code'],
            'weight' => $data['weight'], // gram
            'service_type_id' => 2, // 2: E-commerce Delivery
            'insurance_value' => $data['order_value'],
        ];
        
        $response = $this->makeRequest('POST', $endpoint, $payload);
        
        return [
            'success' => true,
            'fee' => $response['data']['total'] ?? 0,
            'expected_delivery_time' => $response['data']['expected_delivery_time'] ?? null,
        ];
    }
    
    /**
     * Tạo đơn hàng vận chuyển
     */
    public function createShippingOrder(array $orderData): array
    {
        $endpoint = $this->apiUrl . '/v2/shipping-order/create';
        
        $payload = [
            'to_name' => $orderData['customer_name'],
            'to_phone' => $orderData['customer_phone'],
            'to_address' => $orderData['customer_address'],
            'to_ward_code' => $orderData['ward_code'],
            'to_district_id' => $orderData['district_id'],
            'weight' => $orderData['weight'],
            'length' => $orderData['length'] ?? 20,
            'width' => $orderData['width'] ?? 20,
            'height' => $orderData['height'] ?? 10,
            'service_type_id' => 2,
            'payment_type_id' => $orderData['payment_type_id'], // 1: Người gửi trả, 2: Người nhận trả (COD)
            'required_note' => 'CHOXEMHANGKHONGTHU', // Cho xem hàng không thử
            'items' => $orderData['items'],
            'cod_amount' => $orderData['cod_amount'] ?? 0,
        ];
        
        $response = $this->makeRequest('POST', $endpoint, $payload);
        
        return [
            'success' => true,
            'order_code' => $response['data']['order_code'] ?? null,
            'expected_delivery_time' => $response['data']['expected_delivery_time'] ?? null,
        ];
    }
    
    /**
     * Tracking đơn hàng
     */
    public function trackOrder(string $orderCode): array
    {
        $endpoint = $this->apiUrl . '/v2/shipping-order/detail';
        
        $payload = ['order_code' => $orderCode];
        
        $response = $this->makeRequest('POST', $endpoint, $payload);
        
        return $response['data'] ?? [];
    }
    
    /**
     * Lấy danh sách tỉnh/thành
     */
    public function getProvinces(): array
    {
        $endpoint = $this->apiUrl . '/master-data/province';
        return $this->makeRequest('GET', $endpoint);
    }
    
    /**
     * Lấy danh sách quận/huyện
     */
    public function getDistricts(int $provinceId): array
    {
        $endpoint = $this->apiUrl . '/master-data/district';
        return $this->makeRequest('POST', $endpoint, ['province_id' => $provinceId]);
    }
    
    /**
     * Lấy danh sách phường/xã
     */
    public function getWards(int $districtId): array
    {
        $endpoint = $this->apiUrl . '/master-data/ward';
        return $this->makeRequest('GET', $endpoint, ['district_id' => $districtId]);
    }
    
    /**
     * Make HTTP request
     */
    private function makeRequest(string $method, string $url, array $data = []): array
    {
        $ch = curl_init();
        
        $headers = [
            'Content-Type: application/json',
            'Token: ' . $this->apiToken,
            'ShopId: ' . $this->shopId,
        ];
        
        curl_setopt($ch, CURLOPT_URL, $url);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
        
        if ($method === 'POST') {
            curl_setopt($ch, CURLOPT_POST, true);
            curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($data));
        } elseif ($method === 'GET' && !empty($data)) {
            curl_setopt($ch, CURLOPT_URL, $url . '?' . http_build_query($data));
        }
        
        $response = curl_exec($ch);
        $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
        curl_close($ch);
        
        $result = json_decode($response, true);
        
        if ($httpCode !== 200 || !isset($result['code']) || $result['code'] !== 200) {
            throw new \Exception($result['message'] ?? 'Shipping API error');
        }
        
        return $result;
    }
}
```

### Bước 4: Sử dụng trong CheckoutController
```php
// app/controllers/CheckoutController.php

use App\Services\GhnShippingService;

public function process()
{
    $ghn = new GhnShippingService();
    
    // Tính phí ship
    $shippingFee = $ghn->calculateShippingFee([
        'from_district_id' => 1542, // Quận Ba Đình, Hà Nội (kho hàng)
        'to_district_id' => $districtId,
        'to_ward_code' => $wardCode,
        'weight' => $totalWeight,
        'order_value' => $subtotal,
    ]);
    
    // Tạo đơn ship sau khi đặt hàng
    $shippingOrder = $ghn->createShippingOrder([
        'customer_name' => $shippingName,
        'customer_phone' => $shippingPhone,
        'customer_address' => $shippingAddress,
        'ward_code' => $wardCode,
        'district_id' => $districtId,
        'weight' => $totalWeight,
        'payment_type_id' => $paymentMethod === 'cod' ? 2 : 1,
        'cod_amount' => $paymentMethod === 'cod' ? $totalAmount : 0,
        'items' => $items,
    ]);
    
    // Lưu mã vận đơn vào database
    $this->orderModel->update($orderId, [
        'shipping_code' => $shippingOrder['order_code'],
        'expected_delivery' => $shippingOrder['expected_delivery_time'],
    ]);
}
```

### Bước 5: Cập nhật Database
```sql
-- Thêm cột vào bảng orders
ALTER TABLE orders 
ADD COLUMN shipping_code VARCHAR(50) NULL AFTER shipping_fee,
ADD COLUMN shipping_status VARCHAR(50) NULL AFTER shipping_code,
ADD COLUMN expected_delivery DATETIME NULL AFTER shipping_status;
```

### Bước 6: Tracking Page
Tạo trang tracking để khách hàng xem trạng thái vận chuyển:

```php
// app/views/frontend/pages/order-tracking.php
<?php
$ghn = new GhnShippingService();
$tracking = $ghn->trackOrder($order['shipping_code']);
?>

<div class="tracking-info">
    <h3>Trạng thái vận chuyển: <?= $tracking['status'] ?></h3>
    <p>Dự kiến giao: <?= $tracking['expected_delivery_time'] ?></p>
    
    <div class="tracking-history">
        <?php foreach ($tracking['log'] as $log): ?>
            <div class="tracking-step">
                <p><?= $log['status_name'] ?></p>
                <small><?= $log['updated_date'] ?></small>
            </div>
        <?php endforeach; ?>
    </div>
</div>
```

---

## 📊 TỔNG KẾT

| Tính năng | Trạng thái | Ghi chú |
|-----------|-----------|---------|
| 1. Phân quyền/Auth | ✅ Hoàn chỉnh | Admin + Customer roles |
| 2. Payment | ✅ Hoàn chỉnh | VietQR, MoMo, VNPay, COD (thiếu Paypal) |
| 3. Quản lý đơn hàng | ✅ Hoàn chỉnh | Xem, cập nhật trạng thái |
| 4. Giỏ hàng/Checkout | ✅ Hoàn chỉnh | Đầy đủ tính năng |
| 5. Dashboard QL | ✅ Hoàn chỉnh | Thống kê, báo cáo |
| 6. Quản lý tồn kho | ⚠️ Cơ bản | Có kiểm tra, chưa tự động trừ |
| 7. Notification/Email | ❌ Chưa có | Cần implement |
| 8. Vận chuyển/Tracking | ❌ Chưa có | Cần tích hợp API (GHN/GHTK) |

### Điểm mạnh ✅
- Auth & phân quyền tốt
- Payment đa dạng với webhook
- CRUD đầy đủ cho sản phẩm, đơn hàng
- Giỏ hàng & checkout mượt mà
- Dashboard admin đẹp

### Cần cải thiện ⚠️
1. **Quản lý tồn kho:** Thêm tự động trừ kho khi xác nhận đơn
2. **Email notification:** Implement gửi email
3. **Shipping:** Tích hợp GHN/GHTK cho tính phí và tracking

---

## 🚀 ƯU TIÊN PHÁT TRIỂN TIẾP

### Giai đoạn 1 (Quan trọng):
1. ✅ Tự động trừ tồn kho khi xác nhận đơn
2. ✅ Email thông báo đơn hàng

### Giai đoạn 2 (Nâng cao):
3. ✅ Tích hợp API vận chuyển (GHN hoặc GHTK)
4. ✅ Tracking đơn hàng

### Giai đoạn 3 (Tùy chọn):
5. ⭕ Thêm Paypal nếu cần bán quốc tế
6. ⭕ In hóa đơn PDF
7. ⭕ Báo cáo doanh thu chi tiết

---

**Tạo bởi:** Kiro AI  
**Ngày:** 24/09/2026
