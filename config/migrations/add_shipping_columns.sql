-- Cập nhật bảng orders để lưu thông tin vận chuyển GHN

ALTER TABLE `orders` 
ADD COLUMN `shipping_code` VARCHAR(50) NULL COMMENT 'Mã vận đơn GHN' AFTER `shipping_fee`,
ADD COLUMN `shipping_status` VARCHAR(50) NULL COMMENT 'Trạng thái vận chuyển' AFTER `shipping_code`,
ADD COLUMN `expected_delivery` DATETIME NULL COMMENT 'Thời gian giao hàng dự kiến' AFTER `shipping_status`,
ADD COLUMN `shipping_service_id` INT NULL COMMENT 'ID dịch vụ vận chuyển' AFTER `expected_delivery`;

-- Cập nhật bảng orders để lưu mã địa chỉ GHN
ALTER TABLE `orders`
ADD COLUMN `shipping_province_id` INT NULL COMMENT 'GHN Province ID' AFTER `shipping_city`,
ADD COLUMN `shipping_district_id` INT NULL COMMENT 'GHN District ID' AFTER `shipping_province_id`,
ADD COLUMN `shipping_ward_code` VARCHAR(20) NULL COMMENT 'GHN Ward Code' AFTER `shipping_district_id`;

-- Thêm index cho tracking
ALTER TABLE `orders`
ADD INDEX `idx_shipping_code` (`shipping_code`),
ADD INDEX `idx_shipping_status` (`shipping_status`);
