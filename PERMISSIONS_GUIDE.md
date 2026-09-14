# 🔐 Hướng Dẫn Phân Quyền Admin & User

## 📋 Tổng Quan

Hệ thống phân quyền rõ ràng giữa **Admin** và **Customer (User)**:

### 👨‍💼 ADMIN
- Truy cập Admin Panel
- Quản lý sản phẩm (CRUD)
- Quản lý đơn hàng
- Quản lý users
- Xem báo cáo
- Xác nhận thanh toán
- Quản lý danh mục

### 👤 CUSTOMER (USER)
- Dashboard riêng
- Xem đơn hàng của mình
- Cập nhật profile
- Yêu cầu báo giá
- Mua sắm sản phẩm

---

## 🏗️ Cấu Trúc Files Mới

```
app/
├── core/
│   ├── Auth.php                    [NEW] Authentication & Authorization helper
│   └── Controller.php              [UPDATED] Thêm requireAuth(), requireAdmin()
│
├── controllers/
│   ├── DashboardController.php     [NEW] Dashboard cho Customer
│   ├── AdminController.php         [UPDATED] Thêm phân quyền
│   └── UserController.php          [UPDATED] Redirect theo role
│
└── views/
    └── frontend/
        └── pages/
            └── dashboard.php       [NEW] Customer dashboard view
```

---

## 🔑 Auth Helper - Sử Dụng

### 1. Kiểm Tra Đăng Nhập

```php
use App\Core\Auth;

// Kiểm tra user đã đăng nhập chưa
if (Auth::check()) {
    echo "Đã đăng nhập";
}

// Kiểm tra là admin
if (Auth::isAdmin()) {
    echo "User là admin";
}

// Kiểm tra là customer
if (Auth::isCustomer()) {
    echo "User là customer";
}
```

### 2. Lấy Thông Tin User

```php
// Lấy user ID
$userId = Auth::id(); // int|null

// Lấy role
$role = Auth::role(); // 'admin' | 'customer' | null

// Lấy tên
$name = Auth::name(); // string|null

// Lấy email
$email = Auth::email(); // string|null

// Lấy full user data
$user = Auth::user(); // array|null
```

### 3. Đăng Nhập / Đăng Xuất

```php
// Đăng nhập
$user = $userModel->find($userId);
Auth::login($user);

// Đăng xuất
Auth::logout();
```

### 4. Yêu Cầu Quyền Truy Cập

```php
// Yêu cầu đăng nhập (redirect về /user/login nếu chưa login)
Auth::requireAuth();

// Yêu cầu admin (redirect về trang chủ nếu không phải admin)
Auth::requireAdmin();
```

### 5. Kiểm Tra Quyền Sở Hữu Resource

```php
// User chỉ được xem đơn hàng của chính mình
// Admin được xem tất cả
if (Auth::canAccess('order', $order['user_id'])) {
    // Show order
} else {
    // Access denied
}
```

### 6. Kiểm Tra Permission Cụ Thể

```php
// Admin: tất cả permissions
// Customer: chỉ các permissions cụ thể

if (Auth::can('orders.view_own')) {
    // Customer có thể xem đơn hàng của mình
}

if (Auth::can('products.create')) {
    // Chỉ admin mới có quyền này
}
```

---

## 🛣️ Routes & Permissions

### Public Routes (Không cần login)
```
GET  /                          Trang chủ
GET  /products                  Danh sách sản phẩm
GET  /products/:slug            Chi tiết sản phẩm
GET  /cart                      Giỏ hàng
GET  /user/login                Trang login
POST /user/login                Xử lý login
GET  /user/register             Trang đăng ký
POST /user/register             Xử lý đăng ký
```

### Customer Routes (Cần login, role = customer)
```
GET  /dashboard                 Dashboard của customer
GET  /dashboard/orders          Đơn hàng của tôi
GET  /dashboard/orders/:id      Chi tiết đơn hàng (chỉ của mình)
GET  /dashboard/profile         Thông tin tài khoản
POST /dashboard/profile/update  Cập nhật profile
GET  /dashboard/quotes          Yêu cầu báo giá của tôi
GET  /checkout                  Thanh toán
POST /checkout/process          Xử lý checkout
```

### Admin Routes (Cần login, role = admin)
```
GET  /admin                     Admin dashboard
GET  /admin/products            Quản lý sản phẩm
GET  /admin/products/create     Tạo sản phẩm mới
POST /admin/products/create     Lưu sản phẩm mới
GET  /admin/products/:id/edit   Sửa sản phẩm
POST /admin/products/:id/edit   Cập nhật sản phẩm
POST /admin/products/:id/delete Xóa sản phẩm
GET  /admin/orders              Tất cả đơn hàng
GET  /admin/orders/:id          Chi tiết đơn hàng (bất kỳ)
POST /admin/orders/:id/status   Cập nhật trạng thái đơn
GET  /admin/users               Quản lý users
GET  /admin/payments            Xác nhận thanh toán
POST /admin/payments/:id/confirm Xác nhận đã nhận tiền
GET  /admin/categories          Quản lý danh mục
GET  /admin/reports             Báo cáo doanh thu
```

---

## 🔒 Bảo Mật Controller

### Trong Controller, sử dụng như sau:

```php
<?php
namespace App\Controllers;

use App\Core\Controller;
use App\Core\Auth;

class DashboardController extends Controller
{
    public function index(): void
    {
        // Yêu cầu đăng nhập
        Auth::requireAuth();
        
        // Nếu là admin, redirect về admin dashboard
        if (Auth::isAdmin()) {
            $this->redirect($this->baseUrl('admin'));
        }
        
        // Logic cho customer dashboard
        $userId = Auth::id();
        // ...
    }
    
    public function orderDetail(string $id): void
    {
        Auth::requireAuth();
        
        $order = $this->orderModel->find($id);
        
        // Kiểm tra quyền sở hữu
        if (!Auth::canAccess('order', $order['user_id'])) {
            $this->setFlash('error', 'Bạn không có quyền xem đơn hàng này.');
            $this->redirect($this->baseUrl('dashboard'));
        }
        
        // Show order
    }
}
```

### Admin Controller:

```php
<?php
namespace App\Controllers;

use App\Core\Controller;
use App\Core\Auth;

class AdminController extends Controller
{
    public function dashboard(): void
    {
        // Yêu cầu admin role
        Auth::requireAdmin();
        
        // Logic admin dashboard
    }
    
    public function deleteProduct(string $id): void
    {
        Auth::requireAdmin();
        
        // Xóa sản phẩm
    }
}
```

---

## 🎭 Luồng Đăng Nhập & Redirect

### 1. User Login Flow

```
User nhập email/password
    ↓
POST /user/login
    ↓
Validate credentials
    ↓
Auth::login($user)
    ↓
Check role:
  - Admin → Redirect /admin
  - Customer → Redirect /dashboard (hoặc trang trước đó)
```

### 2. Protected Page Access

```
User truy cập /dashboard/orders
    ↓
DashboardController::orders()
    ↓
Auth::requireAuth()
    ↓
Chưa login? → Redirect /user/login (lưu URL gốc)
    ↓
Đã login → Hiển thị trang
```

### 3. Admin Panel Access

```
User truy cập /admin
    ↓
AdminController::dashboard()
    ↓
Auth::requireAdmin()
    ↓
Chưa login? → Redirect /user/login
Không phải admin? → Redirect / (trang chủ)
    ↓
Là admin → Hiển thị admin panel
```

---

## 📊 Permission Matrix

| Feature | Admin | Customer | Guest |
|---------|-------|----------|-------|
| Xem trang chủ | ✅ | ✅ | ✅ |
| Xem sản phẩm | ✅ | ✅ | ✅ |
| Thêm vào giỏ | ✅ | ✅ | ✅ |
| Checkout | ✅ | ✅ | ❌ |
| Xem đơn hàng của mình | ✅ | ✅ | ❌ |
| Xem TẤT CẢ đơn hàng | ✅ | ❌ | ❌ |
| Tạo sản phẩm | ✅ | ❌ | ❌ |
| Sửa sản phẩm | ✅ | ❌ | ❌ |
| Xóa sản phẩm | ✅ | ❌ | ❌ |
| Quản lý users | ✅ | ❌ | ❌ |
| Xác nhận thanh toán | ✅ | ❌ | ❌ |
| Xem báo cáo | ✅ | ❌ | ❌ |
| Dashboard riêng | ✅ (Admin) | ✅ (Customer) | ❌ |

---

## 🧪 Test Phân Quyền

### Test 1: Admin Access

```bash
# Login as admin
Email: admin@decornest.com
Password: admin123

# Kiểm tra:
✅ Có thể truy cập /admin
✅ Có thể tạo/sửa/xóa sản phẩm
✅ Có thể xem tất cả đơn hàng
✅ Có thể xác nhận thanh toán
```

### Test 2: Customer Access

```bash
# Login as customer
Email: tienhungtrinh59@gmail.com
Password: 12345678

# Kiểm tra:
✅ Redirect về /dashboard (không phải /admin)
✅ Có thể xem đơn hàng của mình
❌ KHÔNG thể truy cập /admin
❌ KHÔNG thể xem đơn hàng của người khác
✅ Có thể cập nhật profile
```

### Test 3: Guest Access

```bash
# Không đăng nhập

# Kiểm tra:
✅ Có thể xem sản phẩm
✅ Có thể thêm vào giỏ
❌ Redirect /user/login khi truy cập /dashboard
❌ Redirect /user/login khi truy cập /admin
❌ Bị redirect về /user/login khi checkout
```

### Test 4: Authorization Bypass

```bash
# Login as customer, cố truy cập admin panel
URL: http://localhost/project-ecommerce/public/admin

Expected:
❌ Bị redirect về trang chủ
✅ Hiển thị flash message: "Bạn không có quyền truy cập"
```

### Test 5: Order Ownership

```bash
# Customer A login, cố xem order của Customer B

# URL: /dashboard/orders/123 (order của user khác)

Expected:
❌ Bị chặn bởi Auth::canAccess()
✅ Redirect về /dashboard
✅ Flash message: "Bạn không có quyền xem đơn hàng này"
```

---

## 🔐 Best Practices

### 1. Luôn kiểm tra auth ở đầu method

```php
public function sensitiveAction(): void
{
    // ĐÚNG: Kiểm tra ngay đầu
    Auth::requireAdmin();
    
    // Business logic...
}
```

### 2. Kiểm tra ownership cho resource của user

```php
public function viewOrder(int $orderId): void
{
    Auth::requireAuth();
    
    $order = $this->orderModel->find($orderId);
    
    // ĐÚNG: Kiểm tra ownership
    if (!Auth::canAccess('order', $order['user_id'])) {
        throw new AccessDeniedException();
    }
    
    // Show order...
}
```

### 3. Sử dụng Auth helper thay vì $_SESSION trực tiếp

```php
// SAI
if ($_SESSION['user_role'] === 'admin') { ... }

// ĐÚNG
if (Auth::isAdmin()) { ... }
```

### 4. Redirect có ý nghĩa

```php
// Admin bị từ chối → Redirect về admin dashboard
if (!Auth::can('some_permission')) {
    $this->redirect($this->baseUrl('admin'));
}

// Customer bị từ chối → Redirect về customer dashboard
if (!Auth::can('some_permission')) {
    $this->redirect($this->baseUrl('dashboard'));
}
```

---

## 📱 Frontend - Hiển thị theo role

### Trong view, kiểm tra role:

```php
<?php use App\Core\Auth; ?>

<!-- Chỉ hiển thị cho admin -->
<?php if (Auth::isAdmin()): ?>
    <a href="/admin">Admin Panel</a>
<?php endif; ?>

<!-- Hiển thị cho customer đã login -->
<?php if (Auth::isCustomer()): ?>
    <a href="/dashboard">Dashboard của tôi</a>
<?php endif; ?>

<!-- Hiển thị cho tất cả user đã login -->
<?php if (Auth::check()): ?>
    <a href="/user/profile">Profile</a>
    <a href="/user/logout">Đăng xuất</a>
<?php else: ?>
    <a href="/user/login">Đăng nhập</a>
<?php endif; ?>
```

---

## 🚀 Triển Khai

### Các file đã tạo/sửa:

1. ✅ `app/core/Auth.php` - Auth helper
2. ✅ `app/controllers/DashboardController.php` - Customer dashboard
3. ✅ `app/views/frontend/pages/dashboard.php` - Dashboard view
4. ✅ `app/core/App.php` - Routes mới
5. ✅ `app/controllers/AdminController.php` - Đã có requireAdmin()

### Link truy cập:

**Admin Dashboard:**
```
http://localhost/project-ecommerce/public/admin
Login: admin@decornest.com / admin123
```

**Customer Dashboard:**
```
http://localhost/project-ecommerce/public/dashboard
Login: tienhungtrinh59@gmail.com / 12345678
```

---

## ✅ Checklist Hoàn Thành

- [x] Auth helper với đầy đủ methods
- [x] requireAuth() và requireAdmin() trong Controller
- [x] DashboardController cho customer
- [x] Routes riêng cho admin và customer
- [x] Dashboard view đẹp cho customer
- [x] Permission checking (canAccess, can)
- [x] Login redirect theo role
- [x] Ownership checking cho orders
- [x] Documentation đầy đủ

**Hệ thống phân quyền đã sẵn sàng! 🎉**
