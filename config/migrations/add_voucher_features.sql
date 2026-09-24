-- ============================================================
-- Voucher migration for DecorNest
-- Run after config/migration.sql (users, categories, products,
-- and orders must already exist).
-- ============================================================

USE decornest;

CREATE TABLE IF NOT EXISTS vouchers (
    id                    INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    code                  VARCHAR(50) NOT NULL,
    description           VARCHAR(500) NULL,
    discount_type         ENUM('percent', 'fixed') NOT NULL,
    discount_value        DECIMAL(15,2) NOT NULL,
    max_discount_amount   DECIMAL(15,2) NULL,
    min_order_value       DECIMAL(15,2) NOT NULL DEFAULT 0,
    usage_limit           INT UNSIGNED NULL COMMENT 'NULL means unlimited',
    usage_limit_per_user  INT UNSIGNED NULL COMMENT 'NULL means unlimited',
    used_count            INT UNSIGNED NOT NULL DEFAULT 0,
    start_date            DATETIME NOT NULL,
    end_date              DATETIME NOT NULL,
    is_active             TINYINT(1) NOT NULL DEFAULT 1,
    created_by            INT UNSIGNED NULL,
    created_at            DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
    updated_at            DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    UNIQUE KEY uq_vouchers_code (code),
    KEY idx_vouchers_validity (is_active, start_date, end_date),
    KEY idx_vouchers_created_by (created_by),
    CONSTRAINT fk_vouchers_created_by FOREIGN KEY (created_by)
        REFERENCES users(id) ON DELETE SET NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- No rows in these tables means the voucher applies to the whole shop.
CREATE TABLE IF NOT EXISTS voucher_products (
    voucher_id INT UNSIGNED NOT NULL,
    product_id INT UNSIGNED NOT NULL,
    PRIMARY KEY (voucher_id, product_id),
    CONSTRAINT fk_voucher_products_voucher FOREIGN KEY (voucher_id)
        REFERENCES vouchers(id) ON DELETE CASCADE,
    CONSTRAINT fk_voucher_products_product FOREIGN KEY (product_id)
        REFERENCES products(id) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE IF NOT EXISTS voucher_categories (
    voucher_id INT UNSIGNED NOT NULL,
    category_id INT UNSIGNED NOT NULL,
    PRIMARY KEY (voucher_id, category_id),
    CONSTRAINT fk_voucher_categories_voucher FOREIGN KEY (voucher_id)
        REFERENCES vouchers(id) ON DELETE CASCADE,
    CONSTRAINT fk_voucher_categories_category FOREIGN KEY (category_id)
        REFERENCES categories(id) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE IF NOT EXISTS voucher_usages (
    id              INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    voucher_id      INT UNSIGNED NOT NULL,
    user_id         INT UNSIGNED NOT NULL,
    order_id        INT UNSIGNED NOT NULL,
    discount_amount DECIMAL(15,2) NOT NULL DEFAULT 0,
    used_at         DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
    UNIQUE KEY uq_voucher_usages_order (voucher_id, order_id),
    KEY idx_voucher_usages_user (voucher_id, user_id),
    CONSTRAINT fk_voucher_usages_voucher FOREIGN KEY (voucher_id)
        REFERENCES vouchers(id) ON DELETE RESTRICT,
    CONSTRAINT fk_voucher_usages_user FOREIGN KEY (user_id)
        REFERENCES users(id) ON DELETE RESTRICT,
    CONSTRAINT fk_voucher_usages_order FOREIGN KEY (order_id)
        REFERENCES orders(id) ON DELETE RESTRICT
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

ALTER TABLE orders
    ADD COLUMN IF NOT EXISTS voucher_id INT UNSIGNED NULL AFTER quote_id,
    ADD COLUMN IF NOT EXISTS discount_amount DECIMAL(15,2) NOT NULL DEFAULT 0 AFTER shipping_fee;

SET @voucher_fk_exists = (
    SELECT COUNT(*) FROM information_schema.REFERENTIAL_CONSTRAINTS
    WHERE CONSTRAINT_SCHEMA = DATABASE()
      AND TABLE_NAME = 'orders'
      AND CONSTRAINT_NAME = 'fk_orders_voucher'
);
SET @voucher_fk_sql = IF(
    @voucher_fk_exists = 0,
    'ALTER TABLE orders ADD CONSTRAINT fk_orders_voucher FOREIGN KEY (voucher_id) REFERENCES vouchers(id) ON DELETE SET NULL',
    'SELECT 1'
);
PREPARE voucher_fk_statement FROM @voucher_fk_sql;
EXECUTE voucher_fk_statement;
DEALLOCATE PREPARE voucher_fk_statement;