-- ============================================================
-- DecorNest E-commerce Database Migration
-- ============================================================

CREATE DATABASE IF NOT EXISTS decornest CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
USE decornest;

-- ------------------------------------------------------------
-- users
-- ------------------------------------------------------------
CREATE TABLE IF NOT EXISTS users (
    id            INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    name          VARCHAR(150) NOT NULL,
    email         VARCHAR(191) UNIQUE NOT NULL,
    phone         VARCHAR(20),
    password      VARCHAR(255) NOT NULL,
    account_type  ENUM('individual','business') NOT NULL DEFAULT 'individual',
    role          ENUM('customer','admin') NOT NULL DEFAULT 'customer',
    avatar        VARCHAR(255),
    is_active     TINYINT(1) NOT NULL DEFAULT 1,
    email_verified_at DATETIME,
    remember_token VARCHAR(100),
    created_at    DATETIME DEFAULT CURRENT_TIMESTAMP,
    updated_at    DATETIME DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- ------------------------------------------------------------
-- business_profiles
-- ------------------------------------------------------------
CREATE TABLE IF NOT EXISTS business_profiles (
    id                  INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    user_id             INT UNSIGNED NOT NULL UNIQUE,
    company_name        VARCHAR(255) NOT NULL,
    tax_code            VARCHAR(50) NOT NULL,
    representative_name VARCHAR(150) NOT NULL,
    invoice_address     TEXT NOT NULL,
    invoice_email       VARCHAR(191),
    is_verified         TINYINT(1) NOT NULL DEFAULT 0,
    created_at          DATETIME DEFAULT CURRENT_TIMESTAMP,
    updated_at          DATETIME DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- ------------------------------------------------------------
-- categories
-- ------------------------------------------------------------
CREATE TABLE IF NOT EXISTS categories (
    id          INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    parent_id   INT UNSIGNED,
    name        VARCHAR(150) NOT NULL,
    slug        VARCHAR(191) UNIQUE NOT NULL,
    description TEXT,
    image       VARCHAR(255),
    sort_order  INT NOT NULL DEFAULT 0,
    is_active   TINYINT(1) NOT NULL DEFAULT 1,
    seo_title   VARCHAR(255),
    seo_desc    TEXT,
    created_at  DATETIME DEFAULT CURRENT_TIMESTAMP,
    updated_at  DATETIME DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    FOREIGN KEY (parent_id) REFERENCES categories(id) ON DELETE SET NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- ------------------------------------------------------------
-- products
-- ------------------------------------------------------------
CREATE TABLE IF NOT EXISTS products (
    id              INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    category_id     INT UNSIGNED,
    name            VARCHAR(255) NOT NULL,
    slug            VARCHAR(191) UNIQUE NOT NULL,
    sku             VARCHAR(100) UNIQUE,
    short_desc      TEXT,
    description     LONGTEXT,
    price           DECIMAL(15,2) NOT NULL DEFAULT 0,
    sale_price      DECIMAL(15,2),
    cost_price      DECIMAL(15,2),
    install_fee     DECIMAL(15,2) DEFAULT 0,
    stock           INT NOT NULL DEFAULT 0,
    weight          DECIMAL(8,2),
    dimensions      VARCHAR(100),
    origin          VARCHAR(100),
    material        VARCHAR(100),
    color           VARCHAR(100),
    size_options    TEXT COMMENT 'JSON array of size variants',
    color_options   TEXT COMMENT 'JSON array of color variants',
    images          TEXT COMMENT 'JSON array of image paths',
    thumbnail       VARCHAR(255),
    is_featured     TINYINT(1) NOT NULL DEFAULT 0,
    is_new          TINYINT(1) NOT NULL DEFAULT 0,
    is_active       TINYINT(1) NOT NULL DEFAULT 1,
    views           INT NOT NULL DEFAULT 0,
    seo_title       VARCHAR(255),
    seo_desc        TEXT,
    created_at      DATETIME DEFAULT CURRENT_TIMESTAMP,
    updated_at      DATETIME DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    FOREIGN KEY (category_id) REFERENCES categories(id) ON DELETE SET NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- ------------------------------------------------------------
-- product_price_tiers (Bulk / Wholesale Pricing)
-- ------------------------------------------------------------
CREATE TABLE IF NOT EXISTS product_price_tiers (
    id          INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    product_id  INT UNSIGNED NOT NULL,
    min_qty     INT NOT NULL,
    max_qty     INT COMMENT 'NULL = unlimited',
    price       DECIMAL(15,2) NOT NULL,
    discount_pct DECIMAL(5,2) COMMENT 'Percentage discount for display',
    label       VARCHAR(100) COMMENT 'e.g. Giá sỉ 10-49',
    created_at  DATETIME DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (product_id) REFERENCES products(id) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- ------------------------------------------------------------
-- carts
-- ------------------------------------------------------------
CREATE TABLE IF NOT EXISTS carts (
    id          INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    user_id     INT UNSIGNED COMMENT 'NULL for guest',
    session_id  VARCHAR(128) COMMENT 'For guest carts',
    created_at  DATETIME DEFAULT CURRENT_TIMESTAMP,
    updated_at  DATETIME DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- ------------------------------------------------------------
-- cart_items
-- ------------------------------------------------------------
CREATE TABLE IF NOT EXISTS cart_items (
    id          INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    cart_id     INT UNSIGNED NOT NULL,
    product_id  INT UNSIGNED NOT NULL,
    quantity    INT NOT NULL DEFAULT 1,
    unit_price  DECIMAL(15,2) NOT NULL COMMENT 'Price captured at time of adding',
    size_option VARCHAR(100),
    color_option VARCHAR(100),
    created_at  DATETIME DEFAULT CURRENT_TIMESTAMP,
    updated_at  DATETIME DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    FOREIGN KEY (cart_id)    REFERENCES carts(id)    ON DELETE CASCADE,
    FOREIGN KEY (product_id) REFERENCES products(id) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- ------------------------------------------------------------
-- quotes (B2B Quote Requests)
-- ------------------------------------------------------------
CREATE TABLE IF NOT EXISTS quotes (
    id              INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    user_id         INT UNSIGNED NOT NULL,
    quote_number    VARCHAR(50) UNIQUE NOT NULL,
    status          ENUM('pending','reviewing','responded','accepted','rejected','converted') NOT NULL DEFAULT 'pending',
    customer_note   TEXT,
    admin_note      TEXT,
    admin_response  TEXT,
    total_amount    DECIMAL(15,2),
    responded_at    DATETIME,
    expires_at      DATETIME,
    converted_order_id INT UNSIGNED,
    created_at      DATETIME DEFAULT CURRENT_TIMESTAMP,
    updated_at      DATETIME DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- ------------------------------------------------------------
-- quote_items
-- ------------------------------------------------------------
CREATE TABLE IF NOT EXISTS quote_items (
    id          INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    quote_id    INT UNSIGNED NOT NULL,
    product_id  INT UNSIGNED NOT NULL,
    quantity    INT NOT NULL DEFAULT 1,
    unit_price  DECIMAL(15,2) COMMENT 'Admin-set price after review',
    note        TEXT,
    FOREIGN KEY (quote_id)   REFERENCES quotes(id)   ON DELETE CASCADE,
    FOREIGN KEY (product_id) REFERENCES products(id) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- ------------------------------------------------------------
-- vouchers
-- ------------------------------------------------------------
CREATE TABLE IF NOT EXISTS vouchers (
    id INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    code VARCHAR(50) NOT NULL UNIQUE,
    description VARCHAR(500),
    discount_type ENUM('percent', 'fixed') NOT NULL,
    discount_value DECIMAL(15,2) NOT NULL,
    max_discount_amount DECIMAL(15,2),
    min_order_value DECIMAL(15,2) NOT NULL DEFAULT 0,
    usage_limit INT UNSIGNED,
    usage_limit_per_user INT UNSIGNED,
    used_count INT UNSIGNED NOT NULL DEFAULT 0,
    start_date DATETIME NOT NULL,
    end_date DATETIME NOT NULL,
    is_active TINYINT(1) NOT NULL DEFAULT 1,
    created_by INT UNSIGNED,
    created_at DATETIME DEFAULT CURRENT_TIMESTAMP,
    updated_at DATETIME DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    INDEX idx_vouchers_listing (is_active, start_date, end_date),
    FOREIGN KEY (created_by) REFERENCES users(id) ON DELETE SET NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE IF NOT EXISTS voucher_products (
    voucher_id INT UNSIGNED NOT NULL,
    product_id INT UNSIGNED NOT NULL,
    PRIMARY KEY (voucher_id, product_id),
    FOREIGN KEY (voucher_id) REFERENCES vouchers(id) ON DELETE CASCADE,
    FOREIGN KEY (product_id) REFERENCES products(id) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE IF NOT EXISTS voucher_categories (
    voucher_id INT UNSIGNED NOT NULL,
    category_id INT UNSIGNED NOT NULL,
    PRIMARY KEY (voucher_id, category_id),
    FOREIGN KEY (voucher_id) REFERENCES vouchers(id) ON DELETE CASCADE,
    FOREIGN KEY (category_id) REFERENCES categories(id) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ------------------------------------------------------------
-- orders
-- ------------------------------------------------------------
CREATE TABLE IF NOT EXISTS orders (
    id                  INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    user_id             INT UNSIGNED COMMENT 'NULL for guest',
    quote_id            INT UNSIGNED COMMENT 'Source quote if converted',
    voucher_id          INT UNSIGNED,
    voucher_code        VARCHAR(50),
    order_number        VARCHAR(50) UNIQUE NOT NULL,
    status              ENUM('pending','confirmed','processing','shipped','delivered','cancelled','refunded') NOT NULL DEFAULT 'pending',
    invoice_type        ENUM('retail','vat') NOT NULL DEFAULT 'retail',
    -- Shipping address
    shipping_name       VARCHAR(150) NOT NULL,
    shipping_phone      VARCHAR(20) NOT NULL,
    shipping_address    TEXT NOT NULL,
    shipping_city       VARCHAR(100),
    shipping_district   VARCHAR(100),
    shipping_ward       VARCHAR(100),
    -- VAT info (when invoice_type = vat)
    vat_company_name    VARCHAR(255),
    vat_tax_code        VARCHAR(50),
    vat_address         TEXT,
    vat_email           VARCHAR(191),
    -- Totals
    subtotal            DECIMAL(15,2) NOT NULL DEFAULT 0,
    shipping_fee        DECIMAL(15,2) NOT NULL DEFAULT 0,
    discount_amount     DECIMAL(15,2) NOT NULL DEFAULT 0,
    tax_amount          DECIMAL(15,2) NOT NULL DEFAULT 0,
    total_amount        DECIMAL(15,2) NOT NULL DEFAULT 0,
    -- Other
    payment_method      ENUM('cod','bank_transfer','momo','vnpay') NOT NULL,
    payment_status      ENUM('pending','paid','failed','refunded') NOT NULL DEFAULT 'pending',
    customer_note       TEXT,
    admin_note          TEXT,
    confirmed_at        DATETIME,
    shipped_at          DATETIME,
    delivered_at        DATETIME,
    cancelled_at        DATETIME,
    created_at          DATETIME DEFAULT CURRENT_TIMESTAMP,
    updated_at          DATETIME DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    FOREIGN KEY (user_id)  REFERENCES users(id)  ON DELETE SET NULL,
    FOREIGN KEY (quote_id) REFERENCES quotes(id) ON DELETE SET NULL,
    FOREIGN KEY (voucher_id) REFERENCES vouchers(id) ON DELETE SET NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- ------------------------------------------------------------
-- voucher_usages (created after orders because it references orders)
-- ------------------------------------------------------------
CREATE TABLE IF NOT EXISTS voucher_usages (
    id              INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    voucher_id      INT UNSIGNED NOT NULL,
    user_id         INT UNSIGNED NOT NULL,
    order_id        INT UNSIGNED NOT NULL,
    discount_amount DECIMAL(15,2) NOT NULL DEFAULT 0,
    used_at         DATETIME DEFAULT CURRENT_TIMESTAMP,
    UNIQUE KEY uq_voucher_order (voucher_id, order_id),
    FOREIGN KEY (voucher_id) REFERENCES vouchers(id) ON DELETE RESTRICT,
    FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE RESTRICT,
    FOREIGN KEY (order_id) REFERENCES orders(id) ON DELETE RESTRICT,
    INDEX idx_voucher_user (voucher_id, user_id)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ------------------------------------------------------------
-- order_details
-- ------------------------------------------------------------
CREATE TABLE IF NOT EXISTS order_details (
    id              INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    order_id        INT UNSIGNED NOT NULL,
    product_id      INT UNSIGNED,
    product_name    VARCHAR(255) NOT NULL COMMENT 'Snapshot at order time',
    product_sku     VARCHAR(100),
    product_image   VARCHAR(255),
    size_option     VARCHAR(100),
    color_option    VARCHAR(100),
    quantity        INT NOT NULL DEFAULT 1,
    unit_price      DECIMAL(15,2) NOT NULL,
    subtotal        DECIMAL(15,2) NOT NULL,
    FOREIGN KEY (order_id)   REFERENCES orders(id)   ON DELETE CASCADE,
    FOREIGN KEY (product_id) REFERENCES products(id) ON DELETE SET NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- ------------------------------------------------------------
-- payments
-- ------------------------------------------------------------
CREATE TABLE IF NOT EXISTS payments (
    id               INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    order_id         INT UNSIGNED NOT NULL,
    transaction_id   VARCHAR(191) UNIQUE,
    method           ENUM('cod','bank_transfer','momo','vnpay') NOT NULL,
    amount           DECIMAL(15,2) NOT NULL,
    status           ENUM('pending','processing','paid','failed','refunded') NOT NULL DEFAULT 'pending',
    gateway_response JSON COMMENT 'Raw response from payment gateway',
    paid_at          DATETIME,
    created_at       DATETIME DEFAULT CURRENT_TIMESTAMP,
    updated_at       DATETIME DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    FOREIGN KEY (order_id) REFERENCES orders(id) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- ------------------------------------------------------------
-- Seed: default admin user (password: Admin@123456)
-- ------------------------------------------------------------
INSERT INTO users (name, email, phone, password, account_type, role) VALUES
('Administrator', 'admin@decornest.vn', '0901234567',
 '$2y$12$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', -- Admin@123456
 'individual', 'admin');

-- ------------------------------------------------------------
-- Seed: categories
-- ------------------------------------------------------------
INSERT INTO categories (name, slug, description, sort_order) VALUES
('Phòng ngủ', 'phong-ngu', 'Tất cả sản phẩm trang trí phòng ngủ', 1),
('Giường', 'giuong', 'Khung giường, đầu giường', 2),
('Tủ & Kệ', 'tu-ke', 'Tủ quần áo, kệ trang trí', 3),
('Đèn & Ánh sáng', 'den-anh-sang', 'Đèn ngủ, đèn bàn', 4),
('Gối & Chăn', 'goi-chan', 'Gối trang trí, chăn mền', 5),
('Thảm', 'tham', 'Thảm phòng ngủ các loại', 6),
('Tranh & Gương', 'tranh-guong', 'Tranh treo tường, gương trang trí', 7);

-- Update parent categories
UPDATE categories SET parent_id = (SELECT id FROM (SELECT id FROM categories WHERE slug='phong-ngu') t) WHERE slug IN ('giuong','tu-ke','den-anh-sang','goi-chan','tham','tranh-guong');

-- ------------------------------------------------------------
-- Seed: sample products
-- ------------------------------------------------------------
INSERT INTO products (category_id, name, slug, sku, short_desc, price, install_fee, stock, origin, material, color, thumbnail, is_featured, is_new) VALUES
(2, 'Giường Đôi Nordic Oak', 'giuong-doi-nordic-oak', 'GN-001', 'Khung giường gỗ sồi phong cách Bắc Âu tối giản', 4500000, 500000, 15, 'Việt Nam', 'Gỗ sồi MDF', 'Nâu gỗ', '/assets/images/product-placeholder.jpg', 1, 1),
(3, 'Tủ Đầu Giường Helga', 'tu-dau-giuong-helga', 'TK-001', 'Tủ đầu giường nhỏ gọn với ngăn kéo mở', 850000, 150000, 30, 'Việt Nam', 'MDF phủ melamine', 'Trắng kem', '/assets/images/product-placeholder.jpg', 1, 0),
(4, 'Đèn Ngủ Linen Shade', 'den-ngu-linen-shade', 'DN-001', 'Đèn bàn chụp vải linen ấm áp phong cách Scandinavian', 650000, 0, 50, 'Việt Nam', 'Khung kim loại, chụp vải linen', 'Kem trắng', '/assets/images/product-placeholder.jpg', 0, 1),
(5, 'Bộ Gối Trang Trí Mist', 'bo-goi-trang-tri-mist', 'GT-001', 'Bộ 2 gối trang trí vải cotton texture tự nhiên', 320000, 0, 100, 'Việt Nam', 'Cotton 100%', 'Be nhạt', '/assets/images/product-placeholder.jpg', 1, 0),
(6, 'Thảm Len Bắc Âu Fjord', 'tham-len-bac-au-fjord', 'TH-001', 'Thảm len dệt thủ công họa tiết hình học Scandinavian', 1200000, 0, 25, 'Ấn Độ', 'Len tự nhiên 80%', 'Xám trắng', '/assets/images/product-placeholder.jpg', 1, 1);

-- Bulk pricing for Giường Đôi Nordic Oak
INSERT INTO product_price_tiers (product_id, min_qty, max_qty, price, discount_pct, label) VALUES
(1, 1, 4, 4500000, 0, 'Giá lẻ'),
(1, 5, 9, 4050000, 10, 'Mua 5-9 giảm 10%'),
(1, 10, NULL, 3600000, 20, 'Mua 10+ giảm 20%');

-- Bulk pricing for Tủ Đầu Giường
INSERT INTO product_price_tiers (product_id, min_qty, max_qty, price, discount_pct, label) VALUES
(2, 1, 9, 850000, 0, 'Giá lẻ'),
(2, 10, 49, 765000, 10, 'Mua 10-49 giảm 10%'),
(2, 50, NULL, 680000, 20, 'Mua 50+ giảm 20%');
