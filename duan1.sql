-- phpMyAdmin SQL Dump
-- version 5.2.2
-- https://www.phpmyadmin.net/
--
-- Máy chủ: localhost:3306
-- Thời gian đã tạo: Th3 29, 2026 lúc 12:08 PM
-- Phiên bản máy phục vụ: 8.0.30
-- Phiên bản PHP: 8.1.10

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Cơ sở dữ liệu: `duan1`
--

-- --------------------------------------------------------

--
-- Cấu trúc bảng cho bảng `category`
--

CREATE TABLE `category` (
  `id` int NOT NULL,
  `name` varchar(255) CHARACTER SET utf8mb3 COLLATE utf8mb3_unicode_ci NOT NULL,
  `image` varchar(255) CHARACTER SET utf8mb3 COLLATE utf8mb3_unicode_ci NOT NULL,
  `parent_id` int NOT NULL,
  `status` enum('0','1') COLLATE utf8mb3_unicode_ci NOT NULL DEFAULT '1'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb3 COLLATE=utf8mb3_unicode_ci;

--
-- Đang đổ dữ liệu cho bảng `category`
--

INSERT INTO `category` (`id`, `name`, `image`, `parent_id`, `status`) VALUES
(1, 'Áo thun', 'aothun.jpg', 0, '1'),
(2, 'Áo sơ mi', 'aosomi.jpg', 0, '1'),
(3, 'Áo khoác', 'aokhoac.jpg', 0, '1'),
(4, 'Áo len', 'aolen.jpg', 0, '1'),
(5, 'Áo polo', 'aopolo.jpg', 0, '1');

-- --------------------------------------------------------

--
-- Cấu trúc bảng cho bảng `comments`
--

CREATE TABLE `comments` (
  `id` int NOT NULL,
  `user_id` int NOT NULL,
  `product_id` int NOT NULL,
  `content` text CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL COMMENT 'Nội dung bình luận',
  `rating` tinyint DEFAULT '5' COMMENT 'Số sao đánh giá (1-5)',
  `created_at` datetime DEFAULT CURRENT_TIMESTAMP,
  `status` tinyint DEFAULT '1' COMMENT '1: Hiện, 0: Ẩn (kiểm duyệt)'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Cấu trúc bảng cho bảng `coupons`
--

CREATE TABLE `coupons` (
  `id` int NOT NULL,
  `code` varchar(20) NOT NULL,
  `discount_type` enum('percent','fixed') NOT NULL,
  `discount_value` decimal(10,2) NOT NULL,
  `start_date` date NOT NULL,
  `end_date` date NOT NULL,
  `usage_limit` int DEFAULT NULL,
  `user_count` int DEFAULT '0',
  `is_active` tinyint(1) DEFAULT '1'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

-- --------------------------------------------------------

--
-- Cấu trúc bảng cho bảng `favorites`
--

CREATE TABLE `favorites` (
  `id` int NOT NULL,
  `user_id` int NOT NULL,
  `product_id` int NOT NULL,
  `created_at` datetime DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Đang đổ dữ liệu cho bảng `favorites`
--

INSERT INTO `favorites` (`id`, `user_id`, `product_id`, `created_at`) VALUES
(1, 1, 1, '2025-12-10 15:47:14'),
(2, 8, 7, '2025-12-10 15:51:54'),
(4, 6, 4, '2025-12-13 10:48:04');

-- --------------------------------------------------------

--
-- Cấu trúc bảng cho bảng `orders`
--

CREATE TABLE `orders` (
  `id` int NOT NULL,
  `user_id` int NOT NULL,
  `name` varchar(100) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `email` varchar(100) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `phone` varchar(20) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `address` text CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `note` text CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci,
  `total_amount` decimal(15,2) NOT NULL,
  `Shipping_fee` decimal(10,2) DEFAULT '0.00',
  `pay_method_id` int DEFAULT '1',
  `payment_status` enum('unpaid','paid','refunded') CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT 'unpaid',
  `status` enum('pending','confirmed','shipping','completed','cancelled') CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT 'pending',
  `created_at` datetime DEFAULT CURRENT_TIMESTAMP,
  `updated_at` datetime DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Đang đổ dữ liệu cho bảng `orders`
--

INSERT INTO `orders` (`id`, `user_id`, `name`, `email`, `phone`, `address`, `note`, `total_amount`, `Shipping_fee`, `pay_method_id`, `payment_status`, `status`, `created_at`, `updated_at`) VALUES
(1, 1, 'Administrator', 'admin@gmail.com', '0947541167', '3rwe', 'qwrwq', 328000.00, 30000.00, 1, 'unpaid', 'pending', '2025-11-18 14:34:48', '2025-11-18 14:34:48'),
(2, 1, 'Administrator', 'admin@gmail.com', '0947541167', 'gege', 'gege', 377000.00, 30000.00, 1, 'unpaid', 'pending', '2025-11-28 13:24:40', '2025-11-28 13:24:40'),
(3, 1, 'Administrator', 'admin@gmail.com', '108196651', 'we', 'dwqd', 3010000.00, 30000.00, 1, 'unpaid', 'pending', '2025-11-28 13:42:20', '2025-11-28 13:42:20'),
(4, 6, 'Kẹo chôcc', 'dat@gmail.com', '0936715847', 'feafe', 'faeaf', 228000.00, 30000.00, 1, 'unpaid', 'pending', '2025-11-28 14:36:07', '2025-11-28 14:36:07'),
(5, 6, 'Kẹo chôcc', 'dat@gmail.com', '0354646513', 'adsadwa', 'dấddawd', 129000.00, 30000.00, 1, 'unpaid', 'pending', '2025-11-30 15:57:24', '2025-11-30 15:57:24'),
(6, 1, 'Administrator', 'admin@gmail.com', '0123456789', 'adsda', 'dsadwad', 129000.00, 30000.00, 1, 'unpaid', 'pending', '2025-11-30 17:13:25', '2025-11-30 17:13:25'),
(7, 1, 'Administrator', 'admin@gmail.com', '1315465435', 'đă agafaf', 'dagfawd', 129000.00, 30000.00, 1, 'unpaid', 'pending', '2025-11-30 17:24:57', '2025-11-30 17:24:57'),
(8, 1, 'Administrator', 'admin@gmail.com', '1234567892', 'adsadava', 'adwvwab', 189000.00, 30000.00, 1, 'unpaid', 'pending', '2025-12-01 15:12:22', '2025-12-01 15:12:22'),
(9, 1, 'Administrator', 'admin@gmail.com', '123456789', 'adwvabw', 'wadbavdbaw', 129000.00, 30000.00, 1, 'unpaid', 'pending', '2025-12-01 15:15:17', '2025-12-01 15:15:17'),
(10, 2, 'Nguyễn Văn A', 'a@gmail.com', '1234564556', 'teh teha', 'hteatea', 129000.00, 30000.00, 1, 'unpaid', 'pending', '2025-12-01 15:21:59', '2025-12-01 15:21:59'),
(11, 2, 'Nguyễn Văn A', 'a@gmail.com', '12345612345', 'f qeFGsf', 'gfSG', 129000.00, 30000.00, 1, 'unpaid', 'pending', '2025-12-01 15:24:08', '2025-12-01 15:24:08'),
(12, 2, 'Nguyễn Văn A', 'a@gmail.com', '123456789', 'wd EF A', 'E AFEFA', 129000.00, 30000.00, 1, 'unpaid', 'pending', '2025-12-01 15:27:47', '2025-12-01 15:27:47'),
(13, 2, 'Nguyễn Văn A', 'a@gmail.com', '123456789', 'dw  dWDADW', 'ADWDWADA', 129000.00, 30000.00, 1, 'unpaid', 'pending', '2025-12-01 15:28:44', '2025-12-01 15:28:44'),
(14, 2, 'Nguyễn Văn A', 'a@gmail.com', '1234567892', 'vuvuvg', 'hibihbug', 189000.00, 30000.00, 1, 'unpaid', 'pending', '2025-12-01 15:33:15', '2025-12-01 15:33:15'),
(15, 2, 'Nguyễn Văn A', 'a@gmail.com', '0936715847', 'guvuvg', 'ygv', 129000.00, 30000.00, 1, 'unpaid', 'pending', '2025-12-01 15:34:19', '2025-12-01 15:34:19'),
(16, 2, 'Nguyễn Văn A', 'a@gmail.com', '12345612345', 'huhuhu', '', 129000.00, 30000.00, 1, 'unpaid', 'pending', '2025-12-01 15:35:55', '2025-12-01 15:35:55'),
(17, 2, 'Nguyễn Văn A', 'a@gmail.com', '123', 'kkj', 'kjkj', 179000.00, 30000.00, 1, 'unpaid', 'pending', '2025-12-01 15:36:58', '2025-12-01 15:36:58'),
(18, 2, 'Nguyễn Văn A', 'a@gmail.com', '123', 'weqwq', 'eqwe', 179000.00, 30000.00, 1, 'unpaid', 'pending', '2025-12-01 15:39:04', '2025-12-01 15:39:04'),
(19, 2, 'Nguyễn Văn A', 'a@gmail.com', '0936715847', '61515', '', 129000.00, 30000.00, 1, 'unpaid', 'pending', '2025-12-01 15:42:54', '2025-12-01 15:42:54'),
(20, 2, 'Nguyễn Văn A', 'a@gmail.com', '0936715847', 'adfsgd', '', 129000.00, 30000.00, 1, 'unpaid', 'pending', '2025-12-01 15:46:18', '2025-12-01 15:46:18'),
(21, 6, 'Kẹo chôcc', 'dat@gmail.com', '0936715847', 'sadgfshdg', '', 129000.00, 30000.00, 1, 'unpaid', 'completed', '2025-12-01 15:48:41', '2025-12-13 11:13:07'),
(22, 6, 'Kẹo chôcc', 'dat@gmail.com', '0936715847', 'dwa', '', 179000.00, 30000.00, 1, 'unpaid', 'completed', '2025-12-01 15:49:03', '2025-12-13 11:13:05'),
(23, 6, 'Kẹo chôcc', 'dat@gmail.com', '0936715847', '1651546', '', 189000.00, 30000.00, 1, 'unpaid', 'completed', '2025-12-01 15:55:45', '2025-12-13 11:13:04'),
(24, 1, 'Administrator', 'admin@gmail.com', '123123123', 'huy', 'huypros1', 646000.00, 30000.00, 1, 'unpaid', 'completed', '2025-12-10 15:20:15', '2025-12-13 11:13:02'),
(25, 6, 'Kẹo chôcc', 'dat@gmail.com', '0935237185', 'duawdawda', 'dawdadw', 228000.00, 30000.00, 1, 'unpaid', 'completed', '2025-12-13 06:34:45', '2025-12-13 11:05:02'),
(26, 1, 'Administrator', 'admin@gmail.com', '0935237185', 'duawda', '', 129000.00, 30000.00, 1, 'unpaid', 'completed', '2025-12-13 11:13:49', '2025-12-13 11:14:58'),
(27, 6, 'Kẹo chôcc', 'dat@gmail.com', '0935237185', 'adsda', '', 129000.00, 30000.00, 1, 'unpaid', 'completed', '2025-12-13 11:24:47', '2025-12-13 11:25:25'),
(28, 6, 'Kẹo chôcc', 'dat@gmail.com', '0935237185', 'dawdaawds', '', 149000.00, 30000.00, 1, 'unpaid', 'completed', '2025-12-13 11:25:05', '2025-12-13 11:25:24');

-- --------------------------------------------------------

--
-- Cấu trúc bảng cho bảng `order_item`
--

CREATE TABLE `order_item` (
  `id` int NOT NULL,
  `variant_id` int NOT NULL,
  `order_id` int NOT NULL,
  `quantity` int NOT NULL,
  `price` int NOT NULL,
  `product_id` int NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb3 COLLATE=utf8mb3_unicode_ci;

-- --------------------------------------------------------

--
-- Cấu trúc bảng cho bảng `payment_method`
--

CREATE TABLE `payment_method` (
  `id` int NOT NULL,
  `name_method` varchar(100) NOT NULL,
  `description` text,
  `is_active` tinyint(1) DEFAULT '1'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

--
-- Đang đổ dữ liệu cho bảng `payment_method`
--

INSERT INTO `payment_method` (`id`, `name_method`, `description`, `is_active`) VALUES
(1, 'Thanh toán khi nhận hàng (COD)', 'Khách trả tiền khi nhận hàng', 1),
(2, 'Chuyển khoản ngân hàng', 'Thanh toán qua chuyển khoản', 1),
(3, 'Ví Momo', 'Thanh toán qua ví điện tử Momo', 0),
(4, 'Thẻ tín dụng / Ghi nợ', 'Thanh toán qua cổng VNPAY', 0);

-- --------------------------------------------------------

--
-- Cấu trúc bảng cho bảng `products`
--

CREATE TABLE `products` (
  `id` int NOT NULL,
  `name` varchar(255) CHARACTER SET utf8mb3 COLLATE utf8mb3_unicode_ci NOT NULL,
  `category_slug` varchar(100) CHARACTER SET utf8mb3 COLLATE utf8mb3_unicode_ci NOT NULL,
  `description` varchar(255) CHARACTER SET utf8mb3 COLLATE utf8mb3_unicode_ci NOT NULL,
  `image` varchar(255) CHARACTER SET utf8mb3 COLLATE utf8mb3_unicode_ci NOT NULL,
  `category_id` int NOT NULL,
  `status` tinyint NOT NULL DEFAULT '1',
  `sold` int NOT NULL,
  `created_at` timestamp NOT NULL,
  `quantity` int NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb3 COLLATE=utf8mb3_unicode_ci;

--
-- Đang đổ dữ liệu cho bảng `products`
--

INSERT INTO `products` (`id`, `name`, `category_slug`, `description`, `image`, `category_id`, `status`, `sold`, `created_at`, `quantity`) VALUES
(1, 'Áo thun basic trắng', '', 'Áo thun cotton 100%, thoáng mát, dễ phối đồ', 'aothun_trang.jpg', 1, 1, 50, '2025-11-30 09:21:45', 100),
(2, 'Áo thun đen form rộng', '', 'Phong cách streetwear, chất liệu co giãn', 'aothun_den.jpg', 1, 1, 40, '2025-11-30 09:22:06', 100),
(3, 'Áo thun in hình Tokyo', '', 'Áo thun unisex in hình thành phố Tokyo', 'aothun_tokyo.jpg', 1, 1, 30, '2025-11-30 09:22:16', 100),
(4, 'Áo thun cổ tròn xanh navy', '', 'Thiết kế đơn giản, phù hợp đi học, đi chơi', 'aothun_xanhnavy.jpg', 1, 1, 60, '2025-11-30 09:22:34', 100),
(5, 'Áo thun thể thao', '', 'Áo thun thể thao thấm hút mồ hôi tốt', 'aothun_sport.jpg', 1, 1, 35, '2025-11-30 09:23:14', 100),
(6, 'Áo thun tay lỡ Hàn Quốc', '', 'Phong cách Hàn Quốc, tay lỡ, form rộng', 'aothun_hanquoc.jpg', 1, 1, 45, '2025-11-30 09:24:21', 100),
(7, 'Áo sơ mi trắng công sở', '', 'Áo sơ mi trắng cổ điển, phù hợp đi làm', 'somi_trang.jpg', 2, 1, 50, '2025-11-30 09:24:29', 100),
(8, 'Áo sơ mi caro đỏ', '', 'Áo sơ mi caro phong cách trẻ trung', 'somi_caro.jpg', 2, 1, 40, '2025-11-30 09:24:37', 100),
(9, 'Áo sơ mi denim xanh', '', 'Chất liệu denim bền đẹp, cá tính', 'somi_denim.jpg', 2, 1, 25, '2025-11-30 09:24:46', 100),
(10, 'Áo sơ mi lụa nữ', '', 'Chất liệu lụa mềm mại, sang trọng', 'somi_lua.jpg', 2, 1, 30, '2025-11-30 09:24:55', 100),
(11, 'Áo sơ mi tay ngắn', '', 'Thiết kế tay ngắn, thoải mái mùa hè', 'somi_tayngan.jpg', 2, 1, 35, '2025-11-30 09:25:59', 100),
(12, 'Áo sơ mi họa tiết tropical', '', 'Phong cách biển, họa tiết nổi bật', 'somi_tropical.jpg', 2, 1, 20, '2025-11-30 09:26:09', 100),
(13, 'Áo khoác bomber đen', '', 'Áo khoác bomber chất liệu kaki, cá tính', 'khoac_bomber.jpg', 3, 1, 30, '2025-11-30 09:26:18', 100),
(14, 'Áo khoác jean xanh', '', 'Áo khoác jean cổ điển, unisex', 'khoac_jean.jpg', 3, 1, 25, '2025-11-30 09:26:27', 100),
(15, 'Áo khoác hoodie xám', '', 'Hoodie nỉ ấm áp, có mũ', 'khoac_hoodie.jpg', 3, 1, 40, '2025-11-30 09:26:36', 100),
(16, 'Áo khoác gió thể thao', '', 'Chống gió, nhẹ, phù hợp đi phượt', 'khoac_gio.jpg', 3, 1, 35, '2025-11-30 09:26:44', 100),
(17, 'Áo khoác dạ nữ', '', 'Áo khoác dạ dáng dài, sang trọng', 'khoac_da.jpg', 3, 1, 20, '2025-11-30 09:26:53', 100),
(18, 'Áo khoác parka lót lông', '', 'Giữ ấm tốt, phù hợp mùa đông', 'khoac_parka.jpg', 3, 1, 15, '2025-11-30 09:27:00', 100),
(19, 'Áo len cổ lọ xám', '', 'Chất liệu len mềm, giữ ấm tốt', 'aolen_xam.jpg', 4, 1, 25, '2025-11-30 09:27:08', 100),
(20, 'Áo len cổ tim nữ', '', '', 'aolen_coltim.jpg', 4, 1, 30, '2025-11-30 09:27:17', 50),
(21, 'Áo len oversize', '', 'Phong cách Hàn Quốc, form rộng', 'aolen_oversize.jpg', 4, 1, 20, '2025-11-30 09:27:25', 100),
(22, 'Áo len sọc ngang', '', 'Họa tiết sọc trẻ trung, năng động', 'aolen_soc.jpg', 4, 1, 35, '2025-11-30 09:27:33', 100),
(23, 'Áo len cardigan', '', 'Cardigan len mỏng, dễ phối đồ', 'aolen_cardigan.jpg', 4, 1, 40, '2025-11-30 09:27:41', 100),
(24, 'Áo len cổ tròn basic', '', 'Thiết kế đơn giản, dễ mặc', 'aolen_cotron.jpg', 4, 1, 28, '2025-11-30 09:27:46', 100),
(25, 'Áo polo nam trắng', '', 'Áo polo cổ bẻ, lịch sự', 'polo_trang.jpg', 5, 1, 50, '2025-11-30 09:27:58', 100),
(26, 'Áo polo nữ hồng pastel', '', 'Phong cách trẻ trung, nữ tính', 'polo_hong.jpg', 5, 1, 35, '2025-11-16 11:30:00', 0),
(27, 'Áo polo thể thao', '', 'Chất liệu thun lạnh, thấm hút tốt', 'polo_sport.jpg', 5, 1, 40, '2025-11-16 11:30:00', 0),
(28, 'Áo polo sọc caro', '', 'Họa tiết caro, cá tính', 'polo_caro.jpg', 5, 1, 30, '2025-11-16 11:30:00', 111),
(29, 'Áo polo đen basic', '', 'Thiết kế đơn giản, dễ phối đồ', 'polo_den.jpg', 5, 1, 45, '2025-11-16 11:30:00', 212);

-- --------------------------------------------------------

--
-- Cấu trúc bảng cho bảng `product_variants`
--

CREATE TABLE `product_variants` (
  `id` int NOT NULL,
  `product_id` int NOT NULL,
  `size` varchar(10) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `price` int NOT NULL,
  `stock` int NOT NULL DEFAULT '100'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Đang đổ dữ liệu cho bảng `product_variants`
--

INSERT INTO `product_variants` (`id`, `product_id`, `size`, `price`, `stock`) VALUES
(9, 1, 'S', 99000, 100),
(10, 1, 'M', 99000, 100),
(11, 1, 'L', 119000, 100),
(12, 1, 'XL', 139000, 100),
(13, 1, 'XXL', 149000, 100),
(24, 29, 'S', 1000000, 212),
(25, 28, 'S', 111111, 111);

-- --------------------------------------------------------

--
-- Cấu trúc bảng cho bảng `questions`
--

CREATE TABLE `questions` (
  `id` int NOT NULL,
  `user_id` int NOT NULL,
  `product_id` int NOT NULL,
  `content` text CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `reply` text CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci COMMENT 'Câu trả lời của Admin',
  `created_at` datetime DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Đang đổ dữ liệu cho bảng `questions`
--

INSERT INTO `questions` (`id`, `user_id`, `product_id`, `content`, `reply`, `created_at`) VALUES
(1, 1, 1, 'hêlo', NULL, '2025-12-10 14:41:31'),
(2, 8, 1, 'Hello', NULL, '2025-12-10 15:26:09');

-- --------------------------------------------------------

--
-- Cấu trúc bảng cho bảng `shipping_method`
--

CREATE TABLE `shipping_method` (
  `id` int NOT NULL,
  `name_method` varchar(100) NOT NULL,
  `description` text,
  `is_active` tinyint(1) DEFAULT '1'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

--
-- Đang đổ dữ liệu cho bảng `shipping_method`
--

INSERT INTO `shipping_method` (`id`, `name_method`, `description`, `is_active`) VALUES
(1, 'Giao hàng tiêu chuẩn', 'Giao trong 3-5 ngày', 1),
(2, 'Giao hàng nhanh', 'Giao trong 1-2 ngày', 1),
(3, 'Giao hàng hỏa tốc', 'Giao trong ngày (nội thành)', 0);

-- --------------------------------------------------------

--
-- Cấu trúc bảng cho bảng `users`
--

CREATE TABLE `users` (
  `id` int NOT NULL,
  `name` varchar(100) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `email` varchar(100) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `password` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `phone` varchar(20) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `address` text CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci,
  `birthday` datetime DEFAULT NULL,
  `role` enum('admin','teacher','student','customer') CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'customer',
  `created_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP,
  `updated_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  `gender` enum('nam','nữ','khác') CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Đang đổ dữ liệu cho bảng `users`
--

INSERT INTO `users` (`id`, `name`, `email`, `password`, `phone`, `address`, `birthday`, `role`, `created_at`, `updated_at`, `gender`) VALUES
(1, 'Administrator', 'admin@gmail.com', 'e10adc3949ba59abbe56e057f20f883e', '0909123456', 'Hà Nội', NULL, 'admin', '2025-11-18 07:25:23', '2025-11-18 07:25:23', 'nam'),
(2, 'Nguyễn Văn A', 'a@gmail.com', 'e10adc3949ba59abbe56e057f20f883e', '0909111222', NULL, NULL, 'customer', '2025-11-18 07:25:23', '2025-11-18 07:25:23', 'nam'),
(3, 'Trần Thị B', 'b@gmail.com', 'e10adc3949ba59abbe56e057f20f883e', '0909333444', NULL, NULL, 'customer', '2025-11-18 07:25:23', '2025-11-18 07:25:23', 'nam'),
(4, 'Lê Văn C', 'c@gmail.com', 'e10adc3949ba59abbe56e057f20f883e', '0909555666', NULL, NULL, 'customer', '2025-11-18 07:25:23', '2025-11-18 07:25:23', 'nam'),
(5, 'huypro', 'huy@gmail.com', 'b8dc042d8cf7deefb0ec6a264c930b02', NULL, NULL, NULL, 'customer', '2025-11-19 08:16:57', '2025-11-19 08:16:57', 'nam'),
(6, 'Kẹo chôcc', 'dat@gmail.com', 'e10adc3949ba59abbe56e057f20f883e', '0935237185', 'F7/31', NULL, 'customer', '2025-11-28 07:35:26', '2025-12-13 03:47:56', 'nam'),
(7, 'Nguyễn Khổng Đạt', 'vana@gmall.com', 'c33367701511b4f6020ec61ded352059', '0935237185', 'F7/31 ấp 6 vĩnh lộc a ,huyện Bình Chánh, TPHCM', '2026-03-18 00:00:00', 'customer', '2025-12-05 04:57:41', '2026-03-17 00:01:51', 'nam'),
(8, '124124', 'huypro@gmail.com', 'e10adc3949ba59abbe56e057f20f883e', NULL, NULL, NULL, 'customer', '2025-12-10 08:17:36', '2025-12-10 08:17:36', 'nam');

--
-- Chỉ mục cho các bảng đã đổ
--

--
-- Chỉ mục cho bảng `category`
--
ALTER TABLE `category`
  ADD PRIMARY KEY (`id`);

--
-- Chỉ mục cho bảng `comments`
--
ALTER TABLE `comments`
  ADD PRIMARY KEY (`id`),
  ADD KEY `user_id` (`user_id`),
  ADD KEY `product_id` (`product_id`);

--
-- Chỉ mục cho bảng `coupons`
--
ALTER TABLE `coupons`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `code` (`code`);

--
-- Chỉ mục cho bảng `favorites`
--
ALTER TABLE `favorites`
  ADD PRIMARY KEY (`id`),
  ADD KEY `user_id` (`user_id`),
  ADD KEY `product_id` (`product_id`);

--
-- Chỉ mục cho bảng `orders`
--
ALTER TABLE `orders`
  ADD PRIMARY KEY (`id`),
  ADD KEY `user_id` (`user_id`);

--
-- Chỉ mục cho bảng `order_item`
--
ALTER TABLE `order_item`
  ADD PRIMARY KEY (`id`),
  ADD KEY `fk_order_item_variant` (`variant_id`);

--
-- Chỉ mục cho bảng `payment_method`
--
ALTER TABLE `payment_method`
  ADD PRIMARY KEY (`id`);

--
-- Chỉ mục cho bảng `products`
--
ALTER TABLE `products`
  ADD PRIMARY KEY (`id`),
  ADD KEY `category_id` (`category_id`);

--
-- Chỉ mục cho bảng `product_variants`
--
ALTER TABLE `product_variants`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `unique_product_size` (`product_id`,`size`),
  ADD KEY `idx_product` (`product_id`);

--
-- Chỉ mục cho bảng `questions`
--
ALTER TABLE `questions`
  ADD PRIMARY KEY (`id`),
  ADD KEY `user_id` (`user_id`),
  ADD KEY `product_id` (`product_id`);

--
-- Chỉ mục cho bảng `shipping_method`
--
ALTER TABLE `shipping_method`
  ADD PRIMARY KEY (`id`);

--
-- Chỉ mục cho bảng `users`
--
ALTER TABLE `users`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `unique_email` (`email`);

--
-- AUTO_INCREMENT cho các bảng đã đổ
--

--
-- AUTO_INCREMENT cho bảng `category`
--
ALTER TABLE `category`
  MODIFY `id` int NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=8;

--
-- AUTO_INCREMENT cho bảng `comments`
--
ALTER TABLE `comments`
  MODIFY `id` int NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3;

--
-- AUTO_INCREMENT cho bảng `coupons`
--
ALTER TABLE `coupons`
  MODIFY `id` int NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT cho bảng `favorites`
--
ALTER TABLE `favorites`
  MODIFY `id` int NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=6;

--
-- AUTO_INCREMENT cho bảng `orders`
--
ALTER TABLE `orders`
  MODIFY `id` int NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=29;

--
-- AUTO_INCREMENT cho bảng `order_item`
--
ALTER TABLE `order_item`
  MODIFY `id` int NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=14;

--
-- AUTO_INCREMENT cho bảng `payment_method`
--
ALTER TABLE `payment_method`
  MODIFY `id` int NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=5;

--
-- AUTO_INCREMENT cho bảng `products`
--
ALTER TABLE `products`
  MODIFY `id` int NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=39;

--
-- AUTO_INCREMENT cho bảng `product_variants`
--
ALTER TABLE `product_variants`
  MODIFY `id` int NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=26;

--
-- AUTO_INCREMENT cho bảng `questions`
--
ALTER TABLE `questions`
  MODIFY `id` int NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3;

--
-- AUTO_INCREMENT cho bảng `shipping_method`
--
ALTER TABLE `shipping_method`
  MODIFY `id` int NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=4;

--
-- AUTO_INCREMENT cho bảng `users`
--
ALTER TABLE `users`
  MODIFY `id` int NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=9;

--
-- Ràng buộc đối với các bảng kết xuất
--

--
-- Ràng buộc cho bảng `comments`
--
ALTER TABLE `comments`
  ADD CONSTRAINT `comments_ibfk_1` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `comments_ibfk_2` FOREIGN KEY (`product_id`) REFERENCES `products` (`id`) ON DELETE CASCADE;

--
-- Ràng buộc cho bảng `favorites`
--
ALTER TABLE `favorites`
  ADD CONSTRAINT `favorites_ibfk_1` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `favorites_ibfk_2` FOREIGN KEY (`product_id`) REFERENCES `products` (`id`) ON DELETE CASCADE;

--
-- Ràng buộc cho bảng `orders`
--
ALTER TABLE `orders`
  ADD CONSTRAINT `orders_ibfk_1` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE RESTRICT ON UPDATE RESTRICT;

--
-- Ràng buộc cho bảng `order_item`
--
ALTER TABLE `order_item`
  ADD CONSTRAINT `fk_order_item_variant` FOREIGN KEY (`variant_id`) REFERENCES `product_variants` (`id`) ON DELETE CASCADE;

--
-- Ràng buộc cho bảng `products`
--
ALTER TABLE `products`
  ADD CONSTRAINT `products_ibfk_1` FOREIGN KEY (`category_id`) REFERENCES `category` (`id`) ON DELETE RESTRICT ON UPDATE RESTRICT;

--
-- Ràng buộc cho bảng `product_variants`
--
ALTER TABLE `product_variants`
  ADD CONSTRAINT `product_variants_ibfk_1` FOREIGN KEY (`product_id`) REFERENCES `products` (`id`) ON DELETE CASCADE;

--
-- Ràng buộc cho bảng `questions`
--
ALTER TABLE `questions`
  ADD CONSTRAINT `questions_ibfk_1` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `questions_ibfk_2` FOREIGN KEY (`product_id`) REFERENCES `products` (`id`) ON DELETE CASCADE;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
