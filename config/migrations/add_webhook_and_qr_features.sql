-- ============================================================
-- Migration: Thêm tính năng VietQR + Webhook
-- ============================================================

USE decornest;

-- 1. Thêm columns cho orders
ALTER TABLE orders 
ADD COLUMN IF NOT EXISTS order_code VARCHAR(50) UNIQUE AFTER order_number,
ADD COLUMN IF NOT EXISTS qr_image_url VARCHAR(500) AFTER payment_status,
ADD COLUMN IF NOT EXISTS expires_at DATETIME AFTER qr_image_url,
ADD COLUMN IF NOT EXISTS paid_at DATETIME AFTER expires_at;

-- Update status enum để thêm needs_review
ALTER TABLE orders 
MODIFY COLUMN status ENUM('pending','confirmed','processing','shipped','delivered','cancelled','expired','needs_review') NOT NULL DEFAULT 'pending';

-- 2. Tạo bảng payment_webhook_logs
CREATE TABLE IF NOT EXISTS payment_webhook_logs (
    id INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    order_id INT UNSIGNED,
    order_code VARCHAR(50),
    raw_payload TEXT NOT NULL,
    signature_valid TINYINT(1) NOT NULL DEFAULT 0,
    processed TINYINT(1) NOT NULL DEFAULT 0,
    error_message TEXT,
    received_at DATETIME DEFAULT CURRENT_TIMESTAMP,
    processed_at DATETIME,
    FOREIGN KEY (order_id) REFERENCES orders(id) ON DELETE SET NULL,
    INDEX idx_order_code (order_code),
    INDEX idx_processed (processed),
    INDEX idx_received_at (received_at)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- 3. Thêm CHECK constraint cho cart_items quantity (1-100)
-- MySQL 8.0.16+ hỗ trợ CHECK constraints
ALTER TABLE cart_items 
ADD CONSTRAINT chk_quantity_range CHECK (quantity >= 1 AND quantity <= 100);

-- 4. Thêm unique constraint cho cart_items (tránh trùng product trong cùng cart)
ALTER TABLE cart_items
ADD CONSTRAINT uq_cart_product UNIQUE (cart_id, product_id);

-- 5. Update payment_method enum để thêm 'vietqr'
ALTER TABLE orders
MODIFY COLUMN payment_method ENUM('cod','bank_transfer','momo','vnpay','vietqr') NOT NULL;

-- 6. Tạo index cho việc query orders hết hạn
ALTER TABLE orders
ADD INDEX idx_expires_status (expires_at, status);

-- 7. Thêm column để track số lần retry webhook
ALTER TABLE payment_webhook_logs
ADD COLUMN retry_count TINYINT NOT NULL DEFAULT 0 AFTER processed;

COMMIT;
