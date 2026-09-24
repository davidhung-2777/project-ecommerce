-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Máy chủ: 127.0.0.1
-- Thời gian đã tạo: Th9 23, 2026 lúc 10:00 AM
-- Phiên bản máy phục vụ: 10.4.32-MariaDB
-- Phiên bản PHP: 8.0.30

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Tạo cơ sở dữ liệu `decornest` nếu chưa có
--

CREATE DATABASE IF NOT EXISTS `decornest` DEFAULT CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
USE `decornest`;

--
-- Cơ sở dữ liệu: `decornest`
--

-- --------------------------------------------------------

--
-- Cấu trúc bảng cho bảng `business_profiles`
--

CREATE TABLE `business_profiles` (
  `id` int(10) UNSIGNED NOT NULL,
  `user_id` int(10) UNSIGNED NOT NULL,
  `company_name` varchar(255) NOT NULL,
  `tax_code` varchar(50) NOT NULL,
  `representative_name` varchar(150) NOT NULL,
  `invoice_address` text NOT NULL,
  `invoice_email` varchar(191) DEFAULT NULL,
  `is_verified` tinyint(1) NOT NULL DEFAULT 0,
  `created_at` datetime DEFAULT current_timestamp(),
  `updated_at` datetime DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Cấu trúc bảng cho bảng `carts`
--

CREATE TABLE `carts` (
  `id` int(10) UNSIGNED NOT NULL,
  `user_id` int(10) UNSIGNED DEFAULT NULL COMMENT 'NULL for guest',
  `session_id` varchar(128) DEFAULT NULL COMMENT 'For guest carts',
  `created_at` datetime DEFAULT current_timestamp(),
  `updated_at` datetime DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Đang đổ dữ liệu cho bảng `carts`
--

INSERT INTO `carts` (`id`, `user_id`, `session_id`, `created_at`, `updated_at`) VALUES
(1, NULL, 'e7250653fd08fb31a4fced20e429f187', '2026-08-27 14:43:45', '2026-08-27 14:43:45'),
(3, NULL, NULL, '2026-08-30 12:59:21', '2026-08-30 12:59:21'),
(5, NULL, NULL, '2026-08-30 15:08:50', '2026-08-30 15:08:50'),
(8, 11, NULL, '2026-08-30 23:34:23', '2026-08-30 23:34:23'),
(10, 10, NULL, '2026-08-30 23:42:19', '2026-08-30 23:42:19'),
(21, NULL, '0e7d4fb51d4d46e6c254c7ca7d30aa1a', '2026-09-17 19:43:54', '2026-09-17 19:43:54'),
(27, NULL, '054d9bcca7ac7a53b96f9036552da07c', '2026-09-23 13:51:42', '2026-09-23 13:51:42');

-- --------------------------------------------------------

--
-- Cấu trúc bảng cho bảng `cart_items`
--

CREATE TABLE `cart_items` (
  `id` int(10) UNSIGNED NOT NULL,
  `cart_id` int(10) UNSIGNED NOT NULL,
  `product_id` int(10) UNSIGNED NOT NULL,
  `quantity` int(11) NOT NULL DEFAULT 1,
  `unit_price` decimal(15,2) NOT NULL COMMENT 'Price captured at time of adding',
  `size_option` varchar(100) DEFAULT NULL,
  `color_option` varchar(100) DEFAULT NULL,
  `created_at` datetime DEFAULT current_timestamp(),
  `updated_at` datetime DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Đang đổ dữ liệu cho bảng `cart_items`
--

INSERT INTO `cart_items` (`id`, `cart_id`, `product_id`, `quantity`, `unit_price`, `size_option`, `color_option`, `created_at`, `updated_at`) VALUES
(18, 10, 4, 1, 320000.00, NULL, NULL, '2026-09-18 13:54:35', '2026-09-23 13:13:40');

-- --------------------------------------------------------

--
-- Cấu trúc bảng cho bảng `categories`
--

CREATE TABLE `categories` (
  `id` int(10) UNSIGNED NOT NULL,
  `parent_id` int(10) UNSIGNED DEFAULT NULL,
  `name` varchar(150) NOT NULL,
  `slug` varchar(191) NOT NULL,
  `description` text DEFAULT NULL,
  `image` varchar(255) DEFAULT NULL,
  `sort_order` int(11) NOT NULL DEFAULT 0,
  `is_active` tinyint(1) NOT NULL DEFAULT 1,
  `seo_title` varchar(255) DEFAULT NULL,
  `seo_desc` text DEFAULT NULL,
  `created_at` datetime DEFAULT current_timestamp(),
  `updated_at` datetime DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Đang đổ dữ liệu cho bảng `categories`
--

INSERT INTO `categories` (`id`, `parent_id`, `name`, `slug`, `description`, `image`, `sort_order`, `is_active`, `seo_title`, `seo_desc`, `created_at`, `updated_at`) VALUES
(1, NULL, 'Đèn Trang Trí', 'den-trang-tri', 'Đèn bàn, đèn ngủ, đèn treo tường', NULL, 10, 1, NULL, NULL, '2026-08-30 23:31:05', '2026-08-30 23:31:05'),
(2, NULL, 'Gối & Nệm', 'goi-nem', 'Gối trang trí, gối ôm cao cấp', NULL, 20, 1, NULL, NULL, '2026-08-30 23:31:05', '2026-08-30 23:31:05'),
(3, NULL, 'Tranh & Khung', 'tranh-khung', 'Tranh canvas, khung ảnh', NULL, 30, 1, NULL, NULL, '2026-08-30 23:31:05', '2026-08-30 23:31:05'),
(4, NULL, 'Đồ Gốm Sứ', 'do-gom-su', 'Bình hoa, chậu cây', NULL, 40, 1, NULL, NULL, '2026-08-30 23:31:05', '2026-08-30 23:31:05'),
(5, NULL, 'Đồng Hồ', 'dong-ho', 'Đồng hồ treo tường, để bàn', NULL, 50, 1, NULL, NULL, '2026-08-30 23:31:05', '2026-08-30 23:31:05'),
(6, NULL, 'Kệ &amp; Giá', 'ke-gia', '', NULL, 55, 1, NULL, NULL, '2026-08-30 23:31:05', '2026-09-04 13:50:02'),
(7, NULL, 'Phòng ngủ', 'phong-ngu', 'Tất cả sản phẩm trang trí phòng ngủ', NULL, 1, 1, NULL, NULL, '2026-08-30 23:33:15', '2026-08-30 23:33:15'),
(8, 7, 'Giường', 'giuong', 'Khung giường, đầu giường', NULL, 2, 1, NULL, NULL, '2026-08-30 23:33:15', '2026-08-30 23:33:15'),
(9, 7, 'Tủ & Kệ', 'tu-ke', 'Tủ quần áo, kệ trang trí', NULL, 3, 1, NULL, NULL, '2026-08-30 23:33:15', '2026-08-30 23:33:15'),
(10, 7, 'Đèn & Ánh sáng', 'den-anh-sang', 'Đèn ngủ, đèn bàn', NULL, 4, 1, NULL, NULL, '2026-08-30 23:33:15', '2026-08-30 23:33:15'),
(11, 7, 'Gối & Chăn', 'goi-chan', 'Gối trang trí, chăn mền', NULL, 5, 1, NULL, NULL, '2026-08-30 23:33:15', '2026-08-30 23:33:15'),
(12, 7, 'Thảm', 'tham', 'Thảm phòng ngủ các loại', NULL, 6, 1, NULL, NULL, '2026-08-30 23:33:15', '2026-08-30 23:33:15'),
(13, 7, 'Tranh & Gương', 'tranh-guong', 'Tranh treo tường, gương trang trí', NULL, 7, 1, NULL, NULL, '2026-08-30 23:33:15', '2026-08-30 23:33:15'),
(14, NULL, 'Gương toàn thân', 'g-ng-to-n-th-n', '', NULL, -1, 0, NULL, NULL, '2026-09-04 09:01:30', '2026-09-04 09:25:31');

-- --------------------------------------------------------

--
-- Cấu trúc bảng cho bảng `orders`
--

CREATE TABLE `orders` (
  `id` int(10) UNSIGNED NOT NULL,
  `user_id` int(10) UNSIGNED DEFAULT NULL COMMENT 'NULL for guest',
  `quote_id` int(10) UNSIGNED DEFAULT NULL COMMENT 'Source quote if converted',
  `order_number` varchar(50) NOT NULL,
  `order_code` varchar(50) DEFAULT NULL,
  `status` enum('pending','confirmed','processing','shipped','delivered','cancelled','expired','needs_review') NOT NULL DEFAULT 'pending',
  `invoice_type` enum('retail','vat') NOT NULL DEFAULT 'retail',
  `shipping_name` varchar(150) NOT NULL,
  `shipping_phone` varchar(20) NOT NULL,
  `shipping_address` text NOT NULL,
  `shipping_city` varchar(100) DEFAULT NULL,
  `shipping_district` varchar(100) DEFAULT NULL,
  `shipping_ward` varchar(100) DEFAULT NULL,
  `vat_company_name` varchar(255) DEFAULT NULL,
  `vat_tax_code` varchar(50) DEFAULT NULL,
  `vat_address` text DEFAULT NULL,
  `vat_email` varchar(191) DEFAULT NULL,
  `subtotal` decimal(15,2) NOT NULL DEFAULT 0.00,
  `shipping_fee` decimal(15,2) NOT NULL DEFAULT 0.00,
  `discount_amount` decimal(15,2) NOT NULL DEFAULT 0.00,
  `tax_amount` decimal(15,2) NOT NULL DEFAULT 0.00,
  `total_amount` decimal(15,2) NOT NULL DEFAULT 0.00,
  `payment_method` enum('cod','bank_transfer','momo','vnpay','vietqr') NOT NULL,
  `payment_status` enum('pending','paid','failed','refunded') NOT NULL DEFAULT 'pending',
  `qr_image_url` varchar(500) DEFAULT NULL,
  `expires_at` datetime DEFAULT NULL,
  `paid_at` datetime DEFAULT NULL,
  `customer_note` text DEFAULT NULL,
  `admin_note` text DEFAULT NULL,
  `confirmed_at` datetime DEFAULT NULL,
  `shipped_at` datetime DEFAULT NULL,
  `delivered_at` datetime DEFAULT NULL,
  `cancelled_at` datetime DEFAULT NULL,
  `created_at` datetime DEFAULT current_timestamp(),
  `updated_at` datetime DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Đang đổ dữ liệu cho bảng `orders`
--

INSERT INTO `orders` (`id`, `user_id`, `quote_id`, `order_number`, `order_code`, `status`, `invoice_type`, `shipping_name`, `shipping_phone`, `shipping_address`, `shipping_city`, `shipping_district`, `shipping_ward`, `vat_company_name`, `vat_tax_code`, `vat_address`, `vat_email`, `subtotal`, `shipping_fee`, `discount_amount`, `tax_amount`, `total_amount`, `payment_method`, `payment_status`, `qr_image_url`, `expires_at`, `paid_at`, `customer_note`, `admin_note`, `confirmed_at`, `shipped_at`, `delivered_at`, `cancelled_at`, `created_at`, `updated_at`) VALUES
(1, NULL, NULL, 'DN202608307281D7', NULL, 'pending', 'retail', 'Nguyễn Văn Hưng', '0386228202', 'Láng Hạ Thành Công', 'Hà Nội', 'Ba Đình', NULL, NULL, NULL, NULL, NULL, 320000.00, 50000.00, 0.00, 0.00, 370000.00, 'momo', 'pending', NULL, NULL, NULL, '12h', NULL, NULL, NULL, NULL, NULL, '2026-08-30 23:34:15', '2026-08-30 23:34:15'),
(2, 11, NULL, 'DN20260830BA60BD', NULL, 'confirmed', 'retail', 'Trịnh Tiến Hưng', '1058081721', 'Láng Hạ Thành Công', 'Hà Nội', 'Ba Đình', NULL, NULL, NULL, NULL, NULL, 320000.00, 50000.00, 0.00, 0.00, 370000.00, 'bank_transfer', 'paid', NULL, NULL, NULL, '12h', NULL, '2026-08-30 18:42:52', NULL, NULL, NULL, '2026-08-30 23:34:51', '2026-08-30 23:42:52'),
(3, 11, NULL, 'DN2026083045AD67', NULL, 'processing', 'retail', 'Trịnh Tiến Hưng', '1058081721', 'Láng Hạ Thành Công', 'Hà Nội', 'Ba Đình', NULL, NULL, NULL, NULL, NULL, 320000.00, 50000.00, 0.00, 0.00, 370000.00, 'cod', 'pending', NULL, NULL, NULL, '12h', NULL, NULL, NULL, NULL, NULL, '2026-08-30 23:41:56', '2026-09-03 10:58:22'),
(4, 11, NULL, 'DN20260830A13D83', NULL, 'confirmed', 'retail', 'Trịnh Tiến Hưng', '1058081721', 'Láng Hạ Thành Công', 'Hà Nội', 'Ba Đình', NULL, NULL, NULL, NULL, NULL, 1170000.00, 50000.00, 0.00, 0.00, 1220000.00, 'bank_transfer', 'paid', NULL, NULL, NULL, '', NULL, '2026-09-03 05:58:37', NULL, NULL, NULL, '2026-08-30 23:49:30', '2026-09-03 10:58:37'),
(5, 11, NULL, 'DN202609032EC1D1', NULL, 'confirmed', 'vat', 'Trịnh Tiến Hưng', '1058081721', 'Thành Công', 'Hà Nội', 'Ba Đình', NULL, 'The Bad God', '012090002', 'Thành Công', NULL, 320000.00, 50000.00, 0.00, 32000.00, 402000.00, 'bank_transfer', 'paid', NULL, NULL, NULL, '', NULL, '2026-09-03 08:46:09', NULL, NULL, NULL, '2026-09-03 13:09:54', '2026-09-03 13:46:09'),
(6, 10, NULL, 'DN20260903D967D2', NULL, 'cancelled', 'retail', 'Admin DecorNest', '0987654321', 'Láng Hạ Thành Công', 'Hà Nội', '', NULL, NULL, NULL, NULL, NULL, 1200000.00, 50000.00, 0.00, 0.00, 1250000.00, 'vnpay', 'pending', NULL, NULL, NULL, '', NULL, NULL, NULL, NULL, '2026-09-18 08:53:53', '2026-09-03 13:30:21', '2026-09-18 13:53:53'),
(7, 10, NULL, 'DN20260903188E5F', NULL, 'confirmed', 'retail', 'Admin DecorNest', '0987654321', 'Láng Hạ Thành Công', 'Hà Nội', 'Ba Đình', NULL, NULL, NULL, NULL, NULL, 320000.00, 50000.00, 0.00, 0.00, 370000.00, 'bank_transfer', 'paid', NULL, NULL, NULL, '', NULL, '2026-09-03 08:47:00', NULL, NULL, NULL, '2026-09-03 13:46:41', '2026-09-03 13:47:00'),
(8, 10, NULL, 'DN2026090422C92E', NULL, 'pending', 'retail', 'Admin DecorNest', '0987654321', 'Láng Hạ Thành Công', 'Hà Nội', 'Ba Đình', NULL, NULL, NULL, NULL, NULL, 4500000.00, 50000.00, 0.00, 0.00, 4550000.00, 'momo', 'pending', NULL, NULL, NULL, '', NULL, NULL, NULL, NULL, NULL, '2026-09-04 13:36:50', '2026-09-04 13:36:50'),
(9, NULL, NULL, 'DN20260911A3D8EB', NULL, 'pending', 'retail', 'Nguyễn Văn A', '099347377', '120 Đường Láng', 'Hà Nội', 'Đống Đa', NULL, NULL, NULL, NULL, NULL, 4500000.00, 50000.00, 0.00, 0.00, 4550000.00, 'vnpay', 'pending', NULL, NULL, NULL, 'giao trước 12h', NULL, NULL, NULL, NULL, NULL, '2026-09-11 12:58:02', '2026-09-11 12:58:02'),
(10, 10, NULL, 'DN20260917C14194', NULL, 'confirmed', 'retail', 'Admin DecorNest', '0987654321', 'Láng Hạ Thành Công', 'Hà Nội', 'Ba Đình', NULL, NULL, NULL, NULL, NULL, 320000.00, 50000.00, 0.00, 0.00, 370000.00, 'bank_transfer', 'paid', NULL, NULL, NULL, '', NULL, '2026-09-17 14:37:45', NULL, NULL, NULL, '2026-09-17 19:35:08', '2026-09-17 19:37:45'),
(11, 10, NULL, 'DN20260917111115', NULL, 'pending', 'retail', 'Admin DecorNest', '0987654321', 'Thành Công', 'Hà Nội', 'Ba Đình', NULL, NULL, NULL, NULL, NULL, 320000.00, 50000.00, 0.00, 0.00, 370000.00, 'momo', 'pending', NULL, NULL, NULL, '', NULL, NULL, NULL, NULL, NULL, '2026-09-17 20:29:21', '2026-09-17 20:29:21');

-- --------------------------------------------------------

--
-- Cấu trúc bảng cho bảng `order_details`
--

CREATE TABLE `order_details` (
  `id` int(10) UNSIGNED NOT NULL,
  `order_id` int(10) UNSIGNED NOT NULL,
  `product_id` int(10) UNSIGNED DEFAULT NULL,
  `product_name` varchar(255) NOT NULL COMMENT 'Snapshot at order time',
  `product_sku` varchar(100) DEFAULT NULL,
  `product_image` varchar(255) DEFAULT NULL,
  `size_option` varchar(100) DEFAULT NULL,
  `color_option` varchar(100) DEFAULT NULL,
  `quantity` int(11) NOT NULL DEFAULT 1,
  `unit_price` decimal(15,2) NOT NULL,
  `subtotal` decimal(15,2) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Đang đổ dữ liệu cho bảng `order_details`
--

INSERT INTO `order_details` (`id`, `order_id`, `product_id`, `product_name`, `product_sku`, `product_image`, `size_option`, `color_option`, `quantity`, `unit_price`, `subtotal`) VALUES
(1, 1, 4, 'Bộ Gối Trang Trí Mist', 'GT-001', '/assets/images/product-placeholder.jpg', NULL, NULL, 1, 320000.00, 320000.00),
(2, 2, 4, 'Bộ Gối Trang Trí Mist', 'GT-001', '/assets/images/product-placeholder.jpg', NULL, NULL, 1, 320000.00, 320000.00),
(3, 3, 4, 'Bộ Gối Trang Trí Mist', 'GT-001', '/assets/images/product-placeholder.jpg', NULL, NULL, 1, 320000.00, 320000.00),
(4, 4, 2, 'Tủ Đầu Giường Helga', 'TK-001', '/assets/images/product-placeholder.jpg', NULL, NULL, 1, 850000.00, 850000.00),
(5, 4, 4, 'Bộ Gối Trang Trí Mist', 'GT-001', '/assets/images/product-placeholder.jpg', NULL, NULL, 1, 320000.00, 320000.00),
(6, 5, 4, 'Bộ Gối Trang Trí Mist', 'GT-001', '/assets/images/product-placeholder.jpg', NULL, NULL, 1, 320000.00, 320000.00),
(7, 6, 5, 'Thảm Len Bắc Âu Fjord', 'TH-001', '/assets/images/product-placeholder.jpg', NULL, NULL, 1, 1200000.00, 1200000.00),
(8, 7, 4, 'Bộ Gối Trang Trí Mist', 'GT-001', '/assets/images/product-placeholder.jpg', NULL, NULL, 1, 320000.00, 320000.00),
(9, 8, 1, 'Giường Đôi Nordic Oak', 'GN-001', '/assets/images/product-placeholder.jpg', NULL, NULL, 1, 4500000.00, 4500000.00),
(10, 9, 1, 'Giường Đôi Nordic Oak', 'GN-001', '/assets/images/product-placeholder.jpg', NULL, NULL, 1, 4500000.00, 4500000.00),
(11, 10, 4, 'Bộ Gối Trang Trí Mist', 'GT-001', '/assets/images/product-placeholder.jpg', NULL, NULL, 1, 320000.00, 320000.00),
(12, 11, 4, 'Bộ Gối Trang Trí Mist', 'GT-001', '/assets/images/product-placeholder.jpg', NULL, NULL, 1, 320000.00, 320000.00);

-- --------------------------------------------------------

--
-- Cấu trúc bảng cho bảng `payments`
--

CREATE TABLE `payments` (
  `id` int(10) UNSIGNED NOT NULL,
  `order_id` int(10) UNSIGNED NOT NULL,
  `transaction_id` varchar(191) DEFAULT NULL,
  `method` enum('cod','bank_transfer','momo','vnpay') NOT NULL,
  `amount` decimal(15,2) NOT NULL,
  `status` enum('pending','processing','paid','failed','refunded') NOT NULL DEFAULT 'pending',
  `gateway_response` longtext DEFAULT NULL COMMENT 'Raw response from payment gateway' CHECK (json_valid(`gateway_response`)),
  `paid_at` datetime DEFAULT NULL,
  `created_at` datetime DEFAULT current_timestamp(),
  `updated_at` datetime DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Đang đổ dữ liệu cho bảng `payments`
--

INSERT INTO `payments` (`id`, `order_id`, `transaction_id`, `method`, `amount`, `status`, `gateway_response`, `paid_at`, `created_at`, `updated_at`) VALUES
(1, 1, 'TXN17881076552013', 'momo', 370000.00, 'pending', NULL, NULL, '2026-08-30 23:34:15', '2026-08-30 23:34:15'),
(2, 2, 'TXN17881076914448', 'bank_transfer', 370000.00, 'paid', '{\"confirmed_by_admin\":true,\"confirmed_at\":\"2026-08-30 18:42:52\"}', '2026-08-30 18:42:52', '2026-08-30 23:34:51', '2026-08-30 23:42:52'),
(3, 3, 'TXN17881081163508', 'cod', 370000.00, 'pending', NULL, NULL, '2026-08-30 23:41:56', '2026-08-30 23:41:56'),
(4, 4, 'TXN17881085706117', 'bank_transfer', 1220000.00, 'paid', '{\"confirmed_by_admin\":true,\"confirmed_at\":\"2026-09-03 05:58:37\"}', '2026-09-03 05:58:37', '2026-08-30 23:49:30', '2026-09-03 10:58:37'),
(5, 5, 'TXN17884157944245', 'bank_transfer', 402000.00, 'paid', '{\"confirmed_by_admin\":true,\"confirmed_at\":\"2026-09-03 08:46:09\"}', '2026-09-03 08:46:09', '2026-09-03 13:09:54', '2026-09-03 13:46:09'),
(6, 6, 'TXN17884170217834', 'vnpay', 1250000.00, 'pending', NULL, NULL, '2026-09-03 13:30:21', '2026-09-03 13:30:21'),
(7, 7, 'TXN17884180019548', 'bank_transfer', 370000.00, 'paid', '{\"confirmed_by_admin\":true,\"confirmed_at\":\"2026-09-03 08:47:00\"}', '2026-09-03 08:47:00', '2026-09-03 13:46:41', '2026-09-03 13:47:00'),
(8, 8, 'TXN17885038101234', 'momo', 4550000.00, 'pending', NULL, NULL, '2026-09-04 13:36:50', '2026-09-04 13:36:50'),
(9, 9, 'TXN17891062828850', 'vnpay', 4550000.00, 'pending', NULL, NULL, '2026-09-11 12:58:02', '2026-09-11 12:58:02'),
(10, 10, 'TXN17896485083350', 'bank_transfer', 370000.00, 'paid', '{\"confirmed_by_admin\":true,\"confirmed_at\":\"2026-09-17 14:37:45\"}', '2026-09-17 14:37:45', '2026-09-17 19:35:08', '2026-09-17 19:37:45'),
(11, 11, 'TXN17896517613985', 'momo', 370000.00, 'pending', NULL, NULL, '2026-09-17 20:29:21', '2026-09-17 20:29:21');

-- --------------------------------------------------------

--
-- Cấu trúc bảng cho bảng `payment_webhook_logs`
--

CREATE TABLE `payment_webhook_logs` (
  `id` int(10) UNSIGNED NOT NULL,
  `order_id` int(10) UNSIGNED DEFAULT NULL,
  `order_code` varchar(50) DEFAULT NULL,
  `raw_payload` text NOT NULL,
  `signature_valid` tinyint(1) NOT NULL DEFAULT 0,
  `processed` tinyint(1) NOT NULL DEFAULT 0,
  `retry_count` tinyint(4) NOT NULL DEFAULT 0,
  `error_message` text DEFAULT NULL,
  `received_at` datetime DEFAULT current_timestamp(),
  `processed_at` datetime DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Cấu trúc bảng cho bảng `products`
--

CREATE TABLE `products` (
  `id` int(10) UNSIGNED NOT NULL,
  `category_id` int(10) UNSIGNED DEFAULT NULL,
  `name` varchar(255) NOT NULL,
  `slug` varchar(191) NOT NULL,
  `sku` varchar(100) DEFAULT NULL,
  `short_desc` text DEFAULT NULL,
  `description` longtext DEFAULT NULL,
  `price` decimal(15,2) NOT NULL DEFAULT 0.00,
  `sale_price` decimal(15,2) DEFAULT NULL,
  `cost_price` decimal(15,2) DEFAULT NULL,
  `install_fee` decimal(15,2) DEFAULT 0.00,
  `stock` int(11) NOT NULL DEFAULT 0,
  `weight` decimal(8,2) DEFAULT NULL,
  `dimensions` varchar(100) DEFAULT NULL,
  `origin` varchar(100) DEFAULT NULL,
  `material` varchar(100) DEFAULT NULL,
  `color` varchar(100) DEFAULT NULL,
  `size_options` text DEFAULT NULL COMMENT 'JSON array of size variants',
  `color_options` text DEFAULT NULL COMMENT 'JSON array of color variants',
  `images` text DEFAULT NULL COMMENT 'JSON array of image paths',
  `thumbnail` varchar(255) DEFAULT NULL,
  `is_featured` tinyint(1) NOT NULL DEFAULT 0,
  `is_new` tinyint(1) NOT NULL DEFAULT 0,
  `is_active` tinyint(1) NOT NULL DEFAULT 1,
  `views` int(11) NOT NULL DEFAULT 0,
  `seo_title` varchar(255) DEFAULT NULL,
  `seo_desc` text DEFAULT NULL,
  `created_at` datetime DEFAULT current_timestamp(),
  `updated_at` datetime DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Đang đổ dữ liệu cho bảng `products`
--

INSERT INTO `products` (`id`, `category_id`, `name`, `slug`, `sku`, `short_desc`, `description`, `price`, `sale_price`, `cost_price`, `install_fee`, `stock`, `weight`, `dimensions`, `origin`, `material`, `color`, `size_options`, `color_options`, `images`, `thumbnail`, `is_featured`, `is_new`, `is_active`, `views`, `seo_title`, `seo_desc`, `created_at`, `updated_at`) VALUES
(1, 2, 'Giường Đôi Nordic Oak', 'giuong-doi-nordic-oak', 'GN-001', 'Khung giường gỗ sồi phong cách Bắc Âu tối giản', '', 4500000.00, 76000.00, NULL, 500000.00, 15, NULL, '', 'Việt Nam', 'Gỗ sồi MDF', NULL, NULL, NULL, NULL, '/assets/images/product-placeholder.jpg', 1, 1, 1, 1, '', '', '2026-08-30 23:33:15', '2026-09-18 14:43:15'),
(2, 3, 'Tủ Đầu Giường Helga', 'tu-dau-giuong-helga', 'TK-001', 'Tủ đầu giường nhỏ gọn với ngăn kéo mở', NULL, 850000.00, NULL, NULL, 150000.00, 30, NULL, NULL, 'Việt Nam', 'MDF phủ melamine', 'Trắng kem', NULL, NULL, NULL, '/assets/images/product-placeholder.jpg', 1, 0, 1, 0, NULL, NULL, '2026-08-30 23:33:15', '2026-08-30 23:33:15'),
(3, 4, 'Đèn Ngủ Linen Shade', 'den-ngu-linen-shade', 'DN-001', 'Đèn bàn chụp vải linen ấm áp phong cách Scandinavian', NULL, 650000.00, NULL, NULL, 0.00, 50, NULL, NULL, 'Việt Nam', 'Khung kim loại, chụp vải linen', 'Kem trắng', NULL, NULL, NULL, '/assets/images/product-placeholder.jpg', 0, 1, 1, 0, NULL, NULL, '2026-08-30 23:33:15', '2026-08-30 23:33:15'),
(4, 5, 'Bộ Gối Trang Trí Mist', 'bo-goi-trang-tri-mist', 'GT-001', 'Bộ 2 gối trang trí vải cotton texture tự nhiên', NULL, 320000.00, NULL, NULL, 0.00, 100, NULL, NULL, 'Việt Nam', 'Cotton 100%', 'Be nhạt', NULL, NULL, NULL, '/assets/images/product-placeholder.jpg', 1, 0, 1, 2, NULL, NULL, '2026-08-30 23:33:15', '2026-09-15 20:19:48'),
(5, 6, 'Thảm Len Bắc Âu Fjord', 'tham-len-bac-au-fjord', 'TH-001', 'Thảm len dệt thủ công họa tiết hình học Scandinavian', NULL, 1200000.00, NULL, NULL, 0.00, 25, NULL, NULL, 'Ấn Độ', 'Len tự nhiên 80%', 'Xám trắng', NULL, NULL, NULL, '/assets/images/product-placeholder.jpg', 1, 1, 1, 0, NULL, NULL, '2026-08-30 23:33:15', '2026-08-30 23:33:15'),
(6, NULL, 'Gương toàn thân', 'guong-toan-than', '1233', '12323123', '1232321323', 1000000.00, 10000.00, NULL, 1228000.00, 11, NULL, '123x23x223', 'Hưng Yên', '23', NULL, '[\"123\",\"132\",\"2323\"]', '[\"Tr\\u1eafng\",\"\\u0110en\"]', '[\"\\/uploads\\/products\\/gallery_6a9a292f44963.jpg\"]', NULL, 0, 0, 1, 1, '2123', '12323', '2026-09-04 09:13:03', '2026-09-23 13:50:26');

-- --------------------------------------------------------

--
-- Cấu trúc bảng cho bảng `product_price_tiers`
--

CREATE TABLE `product_price_tiers` (
  `id` int(10) UNSIGNED NOT NULL,
  `product_id` int(10) UNSIGNED NOT NULL,
  `min_qty` int(11) NOT NULL,
  `max_qty` int(11) DEFAULT NULL COMMENT 'NULL = unlimited',
  `price` decimal(15,2) NOT NULL,
  `discount_pct` decimal(5,2) DEFAULT NULL COMMENT 'Percentage discount for display',
  `label` varchar(100) DEFAULT NULL COMMENT 'e.g. Gi?? s??? 10-49',
  `created_at` datetime DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Đang đổ dữ liệu cho bảng `product_price_tiers`
--

INSERT INTO `product_price_tiers` (`id`, `product_id`, `min_qty`, `max_qty`, `price`, `discount_pct`, `label`, `created_at`) VALUES
(4, 2, 1, 9, 850000.00, 0.00, 'Giá lẻ', '2026-08-30 23:33:15'),
(5, 2, 10, 49, 765000.00, 10.00, 'Mua 10-49 giảm 10%', '2026-08-30 23:33:15'),
(6, 2, 50, NULL, 680000.00, 20.00, 'Mua 50+ giảm 20%', '2026-08-30 23:33:15'),
(11, 1, 1, 4, 4500000.00, NULL, 'Giá lẻ', '2026-09-18 14:43:15'),
(12, 1, 5, 9, 4050000.00, NULL, 'Mua 5-9 giảm 10%', '2026-09-18 14:43:15'),
(13, 1, 10, NULL, 3600000.00, NULL, 'Mua 10+ giảm 20%', '2026-09-18 14:43:15');

-- --------------------------------------------------------

--
-- Cấu trúc bảng cho bảng `quotes`
--

CREATE TABLE `quotes` (
  `id` int(10) UNSIGNED NOT NULL,
  `user_id` int(10) UNSIGNED NOT NULL,
  `quote_number` varchar(50) NOT NULL,
  `status` enum('pending','reviewing','responded','accepted','rejected','converted') NOT NULL DEFAULT 'pending',
  `customer_note` text DEFAULT NULL,
  `admin_note` text DEFAULT NULL,
  `admin_response` text DEFAULT NULL,
  `total_amount` decimal(15,2) DEFAULT NULL,
  `responded_at` datetime DEFAULT NULL,
  `expires_at` datetime DEFAULT NULL,
  `converted_order_id` int(10) UNSIGNED DEFAULT NULL,
  `created_at` datetime DEFAULT current_timestamp(),
  `updated_at` datetime DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Cấu trúc bảng cho bảng `quote_items`
--

CREATE TABLE `quote_items` (
  `id` int(10) UNSIGNED NOT NULL,
  `quote_id` int(10) UNSIGNED NOT NULL,
  `product_id` int(10) UNSIGNED NOT NULL,
  `quantity` int(11) NOT NULL DEFAULT 1,
  `unit_price` decimal(15,2) DEFAULT NULL COMMENT 'Admin-set price after review',
  `note` text DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Cấu trúc bảng cho bảng `users`
--

CREATE TABLE `users` (
  `id` int(10) UNSIGNED NOT NULL,
  `name` varchar(150) NOT NULL,
  `email` varchar(191) NOT NULL,
  `phone` varchar(20) DEFAULT NULL,
  `password` varchar(255) NOT NULL,
  `account_type` enum('individual','business') NOT NULL DEFAULT 'individual',
  `role` enum('customer','admin') NOT NULL DEFAULT 'customer',
  `avatar` varchar(255) DEFAULT NULL,
  `is_active` tinyint(1) NOT NULL DEFAULT 1,
  `email_verified_at` datetime DEFAULT NULL,
  `remember_token` varchar(100) DEFAULT NULL,
  `created_at` datetime DEFAULT current_timestamp(),
  `updated_at` datetime DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Đang đổ dữ liệu cho bảng `users`
--

INSERT INTO `users` (`id`, `name`, `email`, `phone`, `password`, `account_type`, `role`, `avatar`, `is_active`, `email_verified_at`, `remember_token`, `created_at`, `updated_at`) VALUES
(10, 'Admin DecorNest', 'admin@decornest.com', '0987654321', '$2y$10$jm61sZlVvJOytjo/nIrSA.F3xzDsDGMqHt7KIitaLw8cyiJwBqHrm', 'individual', 'admin', NULL, 1, NULL, NULL, '2026-08-30 23:31:05', '2026-08-30 23:31:05'),
(11, 'Trịnh Tiến Hưng', 'tienhungtrinh59@gmail.com', '1058081721', '$2y$10$o.RBS2RSTtU8jRa.6iFL8.c1xyiaV/wBfHDpurk2ingr.8XbvTpGS', 'individual', 'customer', NULL, 1, NULL, NULL, '2026-08-30 23:31:05', '2026-08-30 23:31:05'),
(12, 'Administrator', 'admin@decornest.vn', '0901234567', '$2y$12$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'individual', 'admin', NULL, 1, NULL, NULL, '2026-08-30 23:33:15', '2026-08-30 23:33:15');

--
-- Chỉ mục cho các bảng đã đổ
--

--
-- Chỉ mục cho bảng `business_profiles`
--
ALTER TABLE `business_profiles`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `user_id` (`user_id`);

--
-- Chỉ mục cho bảng `carts`
--
ALTER TABLE `carts`
  ADD PRIMARY KEY (`id`),
  ADD KEY `user_id` (`user_id`);

--
-- Chỉ mục cho bảng `cart_items`
--
ALTER TABLE `cart_items`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `uq_cart_product` (`cart_id`,`product_id`),
  ADD KEY `product_id` (`product_id`);

--
-- Chỉ mục cho bảng `categories`
--
ALTER TABLE `categories`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `slug` (`slug`),
  ADD KEY `parent_id` (`parent_id`);

--
-- Chỉ mục cho bảng `orders`
--
ALTER TABLE `orders`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `order_number` (`order_number`),
  ADD UNIQUE KEY `order_code` (`order_code`),
  ADD KEY `user_id` (`user_id`),
  ADD KEY `quote_id` (`quote_id`),
  ADD KEY `idx_expires_status` (`expires_at`,`status`);

--
-- Chỉ mục cho bảng `order_details`
--
ALTER TABLE `order_details`
  ADD PRIMARY KEY (`id`),
  ADD KEY `order_id` (`order_id`),
  ADD KEY `product_id` (`product_id`);

--
-- Chỉ mục cho bảng `payments`
--
ALTER TABLE `payments`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `transaction_id` (`transaction_id`),
  ADD KEY `order_id` (`order_id`);

--
-- Chỉ mục cho bảng `payment_webhook_logs`
--
ALTER TABLE `payment_webhook_logs`
  ADD PRIMARY KEY (`id`),
  ADD KEY `order_id` (`order_id`),
  ADD KEY `idx_order_code` (`order_code`),
  ADD KEY `idx_processed` (`processed`),
  ADD KEY `idx_received_at` (`received_at`);

--
-- Chỉ mục cho bảng `products`
--
ALTER TABLE `products`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `slug` (`slug`),
  ADD UNIQUE KEY `sku` (`sku`),
  ADD KEY `category_id` (`category_id`);

--
-- Chỉ mục cho bảng `product_price_tiers`
--
ALTER TABLE `product_price_tiers`
  ADD PRIMARY KEY (`id`),
  ADD KEY `product_id` (`product_id`);

--
-- Chỉ mục cho bảng `quotes`
--
ALTER TABLE `quotes`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `quote_number` (`quote_number`),
  ADD KEY `user_id` (`user_id`);

--
-- Chỉ mục cho bảng `quote_items`
--
ALTER TABLE `quote_items`
  ADD PRIMARY KEY (`id`),
  ADD KEY `quote_id` (`quote_id`),
  ADD KEY `product_id` (`product_id`);

--
-- Chỉ mục cho bảng `users`
--
ALTER TABLE `users`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `email` (`email`);

--
-- AUTO_INCREMENT cho các bảng đã đổ
--

--
-- AUTO_INCREMENT cho bảng `business_profiles`
--
ALTER TABLE `business_profiles`
  MODIFY `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT cho bảng `carts`
--
ALTER TABLE `carts`
  MODIFY `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=28;

--
-- AUTO_INCREMENT cho bảng `cart_items`
--
ALTER TABLE `cart_items`
  MODIFY `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=19;

--
-- AUTO_INCREMENT cho bảng `categories`
--
ALTER TABLE `categories`
  MODIFY `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=17;

--
-- AUTO_INCREMENT cho bảng `orders`
--
ALTER TABLE `orders`
  MODIFY `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=12;

--
-- AUTO_INCREMENT cho bảng `order_details`
--
ALTER TABLE `order_details`
  MODIFY `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=13;

--
-- AUTO_INCREMENT cho bảng `payments`
--
ALTER TABLE `payments`
  MODIFY `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=12;

--
-- AUTO_INCREMENT cho bảng `payment_webhook_logs`
--
ALTER TABLE `payment_webhook_logs`
  MODIFY `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT cho bảng `products`
--
ALTER TABLE `products`
  MODIFY `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=11;

--
-- AUTO_INCREMENT cho bảng `product_price_tiers`
--
ALTER TABLE `product_price_tiers`
  MODIFY `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=14;

--
-- AUTO_INCREMENT cho bảng `quotes`
--
ALTER TABLE `quotes`
  MODIFY `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT cho bảng `quote_items`
--
ALTER TABLE `quote_items`
  MODIFY `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT cho bảng `users`
--
ALTER TABLE `users`
  MODIFY `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=13;

--
-- Các ràng buộc cho các bảng đã đổ
--

--
-- Các ràng buộc cho bảng `business_profiles`
--
ALTER TABLE `business_profiles`
  ADD CONSTRAINT `business_profiles_ibfk_1` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE;

--
-- Các ràng buộc cho bảng `carts`
--
ALTER TABLE `carts`
  ADD CONSTRAINT `carts_ibfk_1` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE;

--
-- Các ràng buộc cho bảng `cart_items`
--
ALTER TABLE `cart_items`
  ADD CONSTRAINT `cart_items_ibfk_1` FOREIGN KEY (`cart_id`) REFERENCES `carts` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `cart_items_ibfk_2` FOREIGN KEY (`product_id`) REFERENCES `products` (`id`) ON DELETE CASCADE;

--
-- Các ràng buộc cho bảng `categories`
--
ALTER TABLE `categories`
  ADD CONSTRAINT `categories_ibfk_1` FOREIGN KEY (`parent_id`) REFERENCES `categories` (`id`) ON DELETE SET NULL;

--
-- Các ràng buộc cho bảng `orders`
--
ALTER TABLE `orders`
  ADD CONSTRAINT `orders_ibfk_1` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE SET NULL,
  ADD CONSTRAINT `orders_ibfk_2` FOREIGN KEY (`quote_id`) REFERENCES `quotes` (`id`) ON DELETE SET NULL;

--
-- Các ràng buộc cho bảng `order_details`
--
ALTER TABLE `order_details`
  ADD CONSTRAINT `order_details_ibfk_1` FOREIGN KEY (`order_id`) REFERENCES `orders` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `order_details_ibfk_2` FOREIGN KEY (`product_id`) REFERENCES `products` (`id`) ON DELETE SET NULL;

--
-- Các ràng buộc cho bảng `payments`
--
ALTER TABLE `payments`
  ADD CONSTRAINT `payments_ibfk_1` FOREIGN KEY (`order_id`) REFERENCES `orders` (`id`) ON DELETE CASCADE;

--
-- Các ràng buộc cho bảng `payment_webhook_logs`
--
ALTER TABLE `payment_webhook_logs`
  ADD CONSTRAINT `payment_webhook_logs_ibfk_1` FOREIGN KEY (`order_id`) REFERENCES `orders` (`id`) ON DELETE SET NULL;

--
-- Các ràng buộc cho bảng `products`
--
ALTER TABLE `products`
  ADD CONSTRAINT `products_ibfk_1` FOREIGN KEY (`category_id`) REFERENCES `categories` (`id`) ON DELETE SET NULL;

--
-- Các ràng buộc cho bảng `product_price_tiers`
--
ALTER TABLE `product_price_tiers`
  ADD CONSTRAINT `product_price_tiers_ibfk_1` FOREIGN KEY (`product_id`) REFERENCES `products` (`id`) ON DELETE CASCADE;

--
-- Các ràng buộc cho bảng `quotes`
--
ALTER TABLE `quotes`
  ADD CONSTRAINT `quotes_ibfk_1` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE;

--
-- Các ràng buộc cho bảng `quote_items`
--
ALTER TABLE `quote_items`
  ADD CONSTRAINT `quote_items_ibfk_1` FOREIGN KEY (`quote_id`) REFERENCES `quotes` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `quote_items_ibfk_2` FOREIGN KEY (`product_id`) REFERENCES `products` (`id`) ON DELETE CASCADE;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
