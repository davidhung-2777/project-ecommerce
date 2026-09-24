# 🛋️ DecorNest - E-Commerce Platform

Hệ thống thương mại điện tử chuyên bán đồ nội thất (giường, tủ, bàn, ghế) với tính năng B2B/B2C, báo giá, và thanh toán đa dạng.

## ✨ Tính Năng Chính

### 🛒 Frontend (Khách hàng)
- **Danh sách sản phẩm** với lọc theo danh mục, giá, tính năng
- **Chi tiết sản phẩm** với gallery ảnh, biến thể (size/màu), giá theo số lượng
- **Giỏ hàng** thông minh với mini cart panel
- **Báo giá B2B** cho đơn hàng lớn
- **Thanh toán** đa dạng:
  - COD (Ship COD)
  - Chuyển khoản ngân hàng (VietQR)
  - MoMo
  - VNPay
- **Quản lý đơn hàng** cá nhân
- **Mega menu** với danh mục phân cấp

### 🔧 Backend (Admin)
- **Dashboard** với thống kê doanh thu, đơn hàng, sản phẩm
- **Quản lý sản phẩm** (CRUD):
  - Upload ảnh thumbnail + gallery
  - Giá theo bậc (bulk pricing tiers)
  - Biến thể (size, color)
  - Phí lắp đặt
  - SEO meta
- **Quản lý đơn hàng**:
  - Xem chi tiết
  - Cập nhật trạng thái
  - In hóa đơn
- **Quản lý báo giá** B2B
- **Quản lý danh mục** (CRUD)
- **Quản lý người dùng** (admin/customer)
- **Báo cáo & Analytics**

### 💳 Payment Gateway
- **VietQR Integration** (SePay/Casso) - Chuyển khoản QR
- **MoMo Wallet** - Ví điện tử
- **VNPay** - Cổng thanh toán
- **COD** - Thanh toán khi nhận hàng
- **Bank Transfer** - Chuyển khoản thủ công

## 🛠️ Tech Stack

- **Backend:** PHP 8.1+ (Custom MVC Framework)
- **Frontend:** TailwindCSS, Alpine.js
- **Database:** MySQL 8.0+
- **Server:** Apache (XAMPP)
- **Payment:** VietQR API, MoMo, VNPay

## 📦 Cài Đặt

### 1. Clone Repository

```bash
git clone https://github.com/yourusername/project-ecommerce.git
cd project-ecommerce
```

### 2. Cài Đặt Dependencies

**Cách 1: Sử dụng Composer (Khuyến nghị)**

Nếu chưa có Composer, tải tại: https://getcomposer.org/download/

```bash
composer install
```

**Cách 2: Không cần Composer**

Thư mục `vendor/` đã được tạo sẵn với autoloader đơn giản. Bạn có thể bỏ qua bước này và chạy trực tiếp.

### 3. Cấu Hình Database

**Import database:**

1. Mở phpMyAdmin: http://localhost/phpmyadmin
2. Click tab **Import**
3. Chọn file `config/decornest.sql`
4. Click **Go**

Database sẽ tự động được tạo với tên `decornest`.

### 4. Cấu Hình Environment

Copy file `.env.example` thành `.env`:
```bash
cp .env.example .env
```

Sửa thông tin database và API keys trong `.env`:
```env
DB_HOST=localhost
DB_DATABASE=decornest
DB_USERNAME=root
DB_PASSWORD=

VIETQR_API_KEY=your_api_key
MOMO_PARTNER_CODE=your_code
VNPAY_TMN_CODE=your_code
```

### 5. Cấu Hình Apache

**Virtual Host (recommended):**
```apache
<VirtualHost *:80>
    ServerName decornest.local
    DocumentRoot "C:/xampp/htdocs/project-ecommerce/public"
    
    <Directory "C:/xampp/htdocs/project-ecommerce/public">
        Options Indexes FollowSymLinks
        AllowOverride All
        Require all granted
    </Directory>
</VirtualHost>
```

Thêm vào `C:\Windows\System32\drivers\etc\hosts`:
```
127.0.0.1 decornest.local
```

**Hoặc chạy qua XAMPP:**
```
http://localhost/project-ecommerce/public
```

### 6. Tạo Dữ Liệu Mẫu (Optional)

```bash
http://localhost/project-ecommerce/public/auto-fix.php
```

## 🔐 Tài Khoản Mặc Định

**Admin:**
- Email: `admin@decornest.com`
- Password: `admin123`

**Customer:**
- Email: `customer@example.com`
- Password: `password`

## 📁 Cấu Trúc Thư Mục

```
project-ecommerce/
├── app/
│   ├── controllers/     # Controllers (MVC)
│   ├── models/          # Models (Database)
│   ├── views/           # Views (Frontend/Admin)
│   ├── core/            # Core Framework
│   └── services/        # Payment Services
├── config/              # Configuration files
├── public/              # Public assets + entry point
│   ├── assets/          # CSS, JS, Images
│   ├── uploads/         # User uploads
│   └── index.php        # Entry point
├── vendor/              # Composer dependencies
├── .env                 # Environment config (gitignored)
└── composer.json        # PHP dependencies
```

## 🎨 Design System

**Colors:**
- Primary: `#8B7355` (Wood)
- Secondary: `#2C2C2C` (Charcoal)
- Accent: `#F5F1E8` (Cream)
- Text: `#666666` (Muted)

**Fonts:**
- Headings: `Inter` (semibold/bold)
- Body: `Inter` (regular)

## 🔌 API Endpoints

### Public API
```
GET  /api/products              # List products
GET  /api/products/:id          # Product detail
POST /api/cart/add              # Add to cart
POST /api/quote/request         # Request B2B quote
```

### Admin API
```
GET    /admin/products          # List products
POST   /admin/products/store    # Create product
PUT    /admin/products/:id      # Update product
DELETE /admin/products/:id      # Delete product
```

## 🧪 Testing

Test form sản phẩm:
```
http://localhost/project-ecommerce/public/test-product-form.php
```

Debug database:
```
http://localhost/project-ecommerce/public/debug-products.php
```

## 🚀 Deployment

### Production Checklist

- [ ] Set `APP_ENV=production` in `.env`
- [ ] Set `APP_DEBUG=false`
- [ ] Update `APP_URL` to production domain
- [ ] Configure real payment gateway credentials
- [ ] Setup SSL certificate (HTTPS)
- [ ] Configure cron jobs for webhooks
- [ ] Setup backup strategy
- [ ] Enable error logging
- [ ] Optimize images and assets
- [ ] Test all payment methods

### Apache Production Config

```apache
<VirtualHost *:443>
    ServerName yourdomain.com
    DocumentRoot /var/www/decornest/public
    
    SSLEngine on
    SSLCertificateFile /path/to/cert.pem
    SSLCertificateKeyFile /path/to/key.pem
    
    <Directory /var/www/decornest/public>
        Options -Indexes +FollowSymLinks
        AllowOverride All
        Require all granted
    </Directory>
</VirtualHost>
```

## 📚 Documentation

- [Fix Product Form](FIX_PRODUCT_FORM.md) - Hướng dẫn fix lỗi form sản phẩm
- [Payment Integration](docs/PAYMENT.md) - Tích hợp thanh toán
- [Database Schema](config/migration.sql) - Cấu trúc database

## 🤝 Contributing

1. Fork the repository
2. Create feature branch (`git checkout -b feature/AmazingFeature`)
3. Commit changes (`git commit -m 'Add AmazingFeature'`)
4. Push to branch (`git push origin feature/AmazingFeature`)
5. Open Pull Request

## 📝 License

MIT License - see [LICENSE](LICENSE) file

## 👥 Authors

- **Your Name** - Initial work

## 🐛 Known Issues

- [ ] Image optimization needed for large uploads
- [ ] Mobile responsive improvements
- [ ] Real-time stock update via websocket
- [ ] Advanced search with Elasticsearch

## 📞 Support

- Email: support@decornest.com
- Issues: [GitHub Issues](https://github.com/yourusername/project-ecommerce/issues)

---

Made with ❤️ by DecorNest Team
