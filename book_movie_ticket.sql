-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Máy chủ: 127.0.0.1
-- Thời gian đã tạo: Th10 26, 2025 lúc 03:31 AM
-- Phiên bản máy phục vụ: 10.4.32-MariaDB
-- Phiên bản PHP: 8.2.12

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Cơ sở dữ liệu: `book_movie_ticket`
--

-- --------------------------------------------------------

--
-- Cấu trúc bảng cho bảng `bookings`
--

CREATE TABLE `bookings` (
  `booking_id` bigint(20) UNSIGNED NOT NULL,
  `user_id` bigint(20) UNSIGNED NOT NULL,
  `customer_name` varchar(191) DEFAULT NULL,
  `customer_phone` varchar(20) DEFAULT NULL,
  `customer_email` varchar(100) DEFAULT NULL,
  `showtime_id` bigint(20) UNSIGNED NOT NULL,
  `booking_code` varchar(20) NOT NULL,
  `idempotency_key` varchar(64) DEFAULT NULL,
  `total_amount` decimal(10,2) NOT NULL,
  `promotion_code` varchar(20) DEFAULT NULL,
  `discount_amount` decimal(10,2) NOT NULL DEFAULT 0.00,
  `final_amount` decimal(10,2) DEFAULT NULL,
  `payment_method` varchar(20) DEFAULT NULL,
  `payment_status` char(10) NOT NULL,
  `booking_status` enum('Pending','Confirmed','Cancelled','Used','Expired') NOT NULL DEFAULT 'Pending',
  `booking_date` timestamp NOT NULL DEFAULT current_timestamp(),
  `payment_date` timestamp NULL DEFAULT NULL,
  `expires_at` timestamp NULL DEFAULT NULL,
  `notes` text DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Đang đổ dữ liệu cho bảng `bookings`
--

INSERT INTO `bookings` (`booking_id`, `user_id`, `customer_name`, `customer_phone`, `customer_email`, `showtime_id`, `booking_code`, `idempotency_key`, `total_amount`, `promotion_code`, `discount_amount`, `final_amount`, `payment_method`, `payment_status`, `booking_status`, `booking_date`, `payment_date`, `expires_at`, `notes`) VALUES
(62, 2, 'Admin User', '0987654321', 'admin@example.com', 1, 'BK20251021D6321C', NULL, 760000.00, 'WELCOME50K', 50000.00, 710000.00, 'VNPAY', 'Pending', 'Pending', '2025-10-21 08:06:53', NULL, '2025-10-21 08:21:53', NULL),
(63, 2, 'Admin User', '0987654321', 'admin@example.com', 1, 'BK20251021C2F3B6', NULL, 285000.00, 'WELCOME50K', 50000.00, 235000.00, 'Cash', 'Pending', 'Confirmed', '2025-10-21 08:07:24', NULL, '2025-10-21 08:22:24', NULL),
(64, 2, 'Admin User', '0987654321', 'admin@example.com', 1, 'BK202510216D2C85', NULL, 340000.00, 'WELCOME50K', 50000.00, 290000.00, 'Cash', 'Pending', 'Confirmed', '2025-10-21 08:49:42', NULL, '2025-10-21 09:04:42', NULL),
(65, 4, 'Nam Hoài', '0326684220', 'namblue2909@gmail.com', 1, 'BK2025102358A0BF', NULL, 295000.00, 'MORNING20', 30000.00, 265000.00, 'Cash', 'Pending', 'Confirmed', '2025-10-23 07:28:21', NULL, '2025-10-23 07:43:21', NULL);

-- --------------------------------------------------------

--
-- Cấu trúc bảng cho bảng `booking_food`
--

CREATE TABLE `booking_food` (
  `booking_food_id` bigint(20) UNSIGNED NOT NULL,
  `booking_id` bigint(20) UNSIGNED NOT NULL,
  `item_id` bigint(20) UNSIGNED NOT NULL,
  `quantity` int(11) NOT NULL,
  `unit_price` decimal(10,2) NOT NULL,
  `total_price` decimal(10,2) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Đang đổ dữ liệu cho bảng `booking_food`
--

INSERT INTO `booking_food` (`booking_food_id`, `booking_id`, `item_id`, `quantity`, `unit_price`, `total_price`) VALUES
(80, 62, 2, 1, 55000.00, 55000.00),
(81, 62, 6, 1, 40000.00, 40000.00),
(82, 62, 7, 1, 45000.00, 45000.00),
(83, 62, 12, 1, 220000.00, 220000.00),
(84, 63, 6, 1, 40000.00, 40000.00),
(85, 63, 7, 1, 45000.00, 45000.00),
(86, 64, 2, 1, 55000.00, 55000.00),
(87, 64, 6, 1, 40000.00, 40000.00),
(88, 64, 7, 1, 45000.00, 45000.00),
(89, 65, 2, 1, 55000.00, 55000.00),
(90, 65, 6, 1, 40000.00, 40000.00);

-- --------------------------------------------------------

--
-- Cấu trúc bảng cho bảng `booking_seats`
--

CREATE TABLE `booking_seats` (
  `booking_seat_id` bigint(20) UNSIGNED NOT NULL,
  `booking_id` bigint(20) UNSIGNED NOT NULL,
  `seat_id` bigint(20) UNSIGNED NOT NULL,
  `seat_price` decimal(10,2) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Đang đổ dữ liệu cho bảng `booking_seats`
--

INSERT INTO `booking_seats` (`booking_seat_id`, `booking_id`, `seat_id`, `seat_price`) VALUES
(89, 62, 674, 100000.00),
(90, 62, 648, 100000.00),
(91, 62, 661, 100000.00),
(92, 62, 675, 100000.00),
(93, 63, 695, 100000.00),
(94, 63, 709, 100000.00),
(95, 64, 644, 100000.00),
(96, 64, 645, 100000.00),
(97, 65, 655, 100000.00),
(98, 65, 669, 100000.00);

-- --------------------------------------------------------

--
-- Cấu trúc bảng cho bảng `cinemas`
--

CREATE TABLE `cinemas` (
  `cinema_id` bigint(20) UNSIGNED NOT NULL,
  `name` varchar(100) NOT NULL,
  `address` text NOT NULL,
  `city` varchar(50) NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Đang đổ dữ liệu cho bảng `cinemas`
--

INSERT INTO `cinemas` (`cinema_id`, `name`, `address`, `city`, `created_at`, `updated_at`) VALUES
(2, 'CineStar', 'Số 3 Hùng Vương, Quận 5, TP.HCM', 'Hồ Chí Minh', '2025-08-26 08:57:25', '2025-08-26 08:57:25'),
(3, 'CGV Golden Plaza', 'Số 1 Lê Duẩn, Quận 1, TP.HCM', 'Hồ Chí Minh', '2025-08-26 08:57:25', '2025-08-26 08:57:25'),
(4, 'Lotte Cinema', 'Lotte Mart, 469 Nguyễn Hữu Cảnh, Bình Thạnh, TP.HCM', 'Hồ Chí Minh', '2025-08-26 08:57:25', '2025-08-26 08:57:25'),
(5, 'BHD Star Cineplex', 'Tầng 6, Vincom Bà Triệu, 191 Bà Triệu, Hà Nội', 'Hà Nội', '2025-08-26 08:57:25', '2025-08-26 08:57:25'),
(6, 'Galaxy Nguyễn Du', '116 Nguyễn Du, Quận 1, TP.HCM', 'Hồ Chí Minh', '2025-08-26 08:57:25', '2025-08-26 08:57:25'),
(7, 'Cineplex Lê Quang Đạo', 'Số 1 Lê Quang Đạo, Hà Nội', 'Hà Nội', '2025-08-26 08:57:25', '2025-08-26 08:57:25'),
(8, 'TCL Cinema', 'Số 12 Trần Duy Hưng, Quận Cầu Giấy, Hà Nội', 'Hà Nội', '2025-08-26 08:57:25', '2025-08-26 08:57:25'),
(9, 'Rạp chiếu phim Quốc gia', '45 Tràng Tiền, Quận Hoàn Kiếm, Hà Nội', 'Hà Nội', '2025-08-26 08:57:25', '2025-08-26 08:57:25'),
(10, 'CGV Vincom Royal City', 'Tầng 3 Vincom Royal City, 72A Nguyễn Trãi, Hà Nội', 'Hà Nội', '2025-08-26 08:57:25', '2025-08-26 08:57:25'),
(11, 'Galaxy Nguyễn Thị Minh Khai', '137 Nguyễn Thị Minh Khai, Quận 3, TP.HCM', 'Hồ Chí Minh', '2025-08-26 08:57:25', '2025-08-26 08:57:25'),
(12, 'Galaxy Tân Bình', '236 Hoàng Văn Thụ, Quận Tân Bình, TP.HCM', 'Hồ Chí Minh', '2025-08-26 08:57:36', '2025-08-26 08:57:36'),
(13, 'CGV Aeon Mall Bình Tân', '1 Đường 17A, Bình Tân, TP.HCM', 'Hồ Chí Minh', '2025-08-26 08:57:36', '2025-08-26 08:57:36'),
(14, 'Lotte Cinema Gò Vấp', '236 Phan Văn Trị, Quận Gò Vấp, TP.HCM', 'Hồ Chí Minh', '2025-08-26 08:57:36', '2025-08-26 08:57:36'),
(15, 'BHD Star Mega Mall', 'Tầng 6, Vincom Mega Mall, Quận 9, TP.HCM', 'Hồ Chí Minh', '2025-08-26 08:57:36', '2025-08-26 08:57:36'),
(16, 'Cineplex Thái Hà', 'Tầng 6, Vincom Thái Hà, Hà Nội', 'Hà Nội', '2025-08-26 08:57:36', '2025-08-26 08:57:36'),
(17, 'CGV Tràng Tiền', 'Tầng 2, 191 Tràng Tiền, Hà Nội', 'Hà Nội', '2025-08-26 08:57:36', '2025-08-26 08:57:36'),
(18, 'BHD Star Cineplex Phạm Ngọc Thạch', '37 Phạm Ngọc Thạch, Quận 3, TP.HCM', 'Hồ Chí Minh', '2025-08-26 08:57:36', '2025-08-26 08:57:36'),
(19, 'Lotte Cinema Royal City', 'Tầng 5 Vincom Royal City, 72A Nguyễn Trãi, Hà Nội', 'Hà Nội', '2025-08-26 08:57:36', '2025-08-26 08:57:36'),
(20, 'Galaxy Hồ Xuân Hương', '65 Hồ Xuân Hương, Quận 3, TP.HCM', 'Hồ Chí Minh', '2025-08-26 08:57:36', '2025-08-26 08:57:36'),
(21, 'Rạp chiếu phim Hòa Bình', '240 Nguyễn Trãi, Quận 5, TP.HCM', 'Hồ Chí Minh', '2025-08-26 08:57:36', '2025-08-26 08:57:36'),
(22, 'CGV Star City', 'Star City, 345 Trường Chinh, TP.HCM', 'Hồ Chí Minh', '2025-08-26 08:57:36', '2025-08-26 08:57:36'),
(23, 'Cineplex Láng Hạ', 'Số 74 Láng Hạ, Hà Nội', 'Hà Nội', '2025-08-26 08:57:36', '2025-08-26 08:57:36'),
(24, 'CGV Sài Gòn', '2 Hàn Thuyên, Quận 1, TP.HCM', 'Hồ Chí Minh', '2025-08-26 08:57:36', '2025-08-26 08:57:36'),
(25, 'BHD Star Cinema Vincom', 'Tầng 5 Vincom, 190 Phạm Ngọc Thạch, Hà Nội', 'Hà Nội', '2025-08-26 08:57:36', '2025-08-26 08:57:36'),
(26, 'Lotte Cinema Cộng Hòa', '130 Cộng Hòa, Quận Tân Bình, TP.HCM', 'Hồ Chí Minh', '2025-08-26 08:57:36', '2025-08-26 08:57:36'),
(27, 'Cineplex Lê Đại Hành', '20 Lê Đại Hành, Quận 11, TP.HCM', 'Hồ Chí Minh', '2025-08-26 08:57:36', '2025-08-26 08:57:36'),
(28, 'CGV Nguyễn Trãi', '106 Nguyễn Trãi, Quận 1, TP.HCM', 'Hồ Chí Minh', '2025-08-26 08:57:36', '2025-08-26 08:57:36'),
(29, 'Galaxy Lê Văn Lương', '234 Lê Văn Lương, TP.HCM', 'Hồ Chí Minh', '2025-08-26 08:57:36', '2025-08-26 08:57:36'),
(30, 'BHD Star Cineplex Vincom Bà Triệu', 'Tầng 6, Vincom Bà Triệu, Hà Nội', 'Hà Nội', '2025-08-26 08:57:36', '2025-08-26 08:57:36'),
(31, 'Lotte Cinema Thủ Đức', 'Lotte Thủ Đức, Quận Thủ Đức, TP.HCM', 'Hồ Chí Minh', '2025-08-26 08:57:36', '2025-08-26 08:57:36'),
(32, 'CineStar Hà Nội', 'Số 1 Tràng Tiền, Quận Hoàn Kiếm, Hà Nội', 'Hà Nội', '2025-08-26 09:18:25', '2025-08-26 09:18:25'),
(33, 'CGV Vincom Bà Triệu', '191 Bà Triệu, Quận Hai Bà Trưng, Hà Nội', 'Hà Nội', '2025-08-26 09:18:25', '2025-08-26 09:18:25'),
(34, 'Lotte Cinema Hà Đông', 'Tầng 5, Vincom Hà Đông, Hà Nội', 'Hà Nội', '2025-08-26 09:18:25', '2025-08-26 09:18:25'),
(35, 'BHD Star Cineplex Hà Nội', 'Tầng 6, Vincom Bà Triệu, Hà Nội', 'Hà Nội', '2025-08-26 09:18:25', '2025-08-26 09:18:25'),
(36, 'Galaxy Nguyễn Du', '116 Nguyễn Du, Quận 1, TP.HCM', 'Hồ Chí Minh', '2025-08-26 09:18:25', '2025-08-26 09:18:25'),
(37, 'Cineplex Phạm Ngọc Thạch', '37 Phạm Ngọc Thạch, Quận 3, TP.HCM', 'Hồ Chí Minh', '2025-08-26 09:18:25', '2025-08-26 09:18:25'),
(38, 'CGV Aeon Mall Tân Phú', 'Số 30, Khu phố 6, Quận Tân Phú, TP.HCM', 'Hồ Chí Minh', '2025-08-26 09:18:25', '2025-08-26 09:18:25'),
(39, 'Lotte Cinema Gò Vấp', '236 Phan Văn Trị, Quận Gò Vấp, TP.HCM', 'Hồ Chí Minh', '2025-08-26 09:18:25', '2025-08-26 09:18:25'),
(40, 'Cineplex Đà Nẵng', '17-19 Phan Châu Trinh, Quận Hải Châu, Đà Nẵng', 'Đà Nẵng', '2025-08-26 09:18:25', '2025-08-26 09:18:25'),
(41, 'CGV Vincom Đà Nẵng', 'Tầng 5, Vincom, Quận Hải Châu, Đà Nẵng', 'Đà Nẵng', '2025-08-26 09:18:25', '2025-08-26 09:18:25'),
(42, 'Lotte Cinema Hải Phòng', 'Tầng 4, Lotte Mall Hải Phòng', 'Hải Phòng', '2025-08-26 09:18:25', '2025-08-26 09:18:25'),
(43, 'Galaxy Hải Phòng', 'Tầng 5, Vincom Lê Lợi, Hải Phòng', 'Hải Phòng', '2025-08-26 09:18:25', '2025-08-26 09:18:25'),
(44, 'CGV Vincom Nghệ An', 'Tầng 5, Vincom Nghệ An, TP Vinh', 'Nghệ An', '2025-08-26 09:18:25', '2025-08-26 09:18:25'),
(45, 'Lotte Cinema Nghệ An', 'Tầng 3, Lotte Mart TP Vinh', 'Nghệ An', '2025-08-26 09:18:25', '2025-08-26 09:18:25'),
(46, 'Cineplex Bình Dương', 'Số 6, Đại lộ Bình Dương, TP Thủ Dầu Một', 'Bình Dương', '2025-08-26 09:18:25', '2025-08-26 09:18:25'),
(47, 'CGV Bình Dương', 'Tầng 3, Vincom Thủ Dầu Một', 'Bình Dương', '2025-08-26 09:18:25', '2025-08-26 09:18:25'),
(48, 'Lotte Cinema Quảng Ninh', 'Tầng 3, Lotte Mart Hạ Long', 'Quảng Ninh', '2025-08-26 09:18:25', '2025-08-26 09:18:25'),
(49, 'BHD Star Cineplex Quảng Ninh', 'Tầng 5, Vincom Hạ Long', 'Quảng Ninh', '2025-08-26 09:18:25', '2025-08-26 09:18:25'),
(50, 'CGV Kiên Giang', 'Tầng 2, Vincom Rạch Giá', 'Kiên Giang', '2025-08-26 09:18:25', '2025-08-26 09:18:25'),
(51, 'Lotte Cinema Kiên Giang', 'Tầng 2, Lotte Mart Rạch Giá', 'Kiên Giang', '2025-08-26 09:18:25', '2025-08-26 09:18:25'),
(52, 'Galaxy Bình Định', 'Tầng 3, Vincom Quy Nhơn', 'Bình Định', '2025-08-26 09:18:25', '2025-08-26 09:18:25'),
(53, 'CGV Quy Nhơn', 'Tầng 3, Vincom Quy Nhơn', 'Bình Định', '2025-08-26 09:18:25', '2025-08-26 09:18:25'),
(54, 'CineStar Cần Thơ', 'Số 2 Hòa Bình, Quận Ninh Kiều, Cần Thơ', 'Cần Thơ', '2025-08-26 09:18:25', '2025-08-26 09:18:25'),
(55, 'BHD Star Cineplex Cần Thơ', 'Tầng 6, Vincom Xuân Khánh, Cần Thơ', 'Cần Thơ', '2025-08-26 09:18:25', '2025-08-26 09:18:25'),
(56, 'CGV An Giang', 'Tầng 3, Vincom Long Xuyên', 'An Giang', '2025-08-26 09:18:25', '2025-08-26 09:18:25'),
(57, 'Lotte Cinema An Giang', 'Tầng 2, Lotte Mart Long Xuyên', 'An Giang', '2025-08-26 09:18:25', '2025-08-26 09:18:25'),
(58, 'Galaxy Tây Ninh', 'Tầng 2, Vincom Tây Ninh', 'Tây Ninh', '2025-08-26 09:18:25', '2025-08-26 09:18:25'),
(59, 'CGV Tây Ninh', 'Tầng 3, Vincom Tây Ninh', 'Tây Ninh', '2025-08-26 09:18:25', '2025-08-26 09:18:25'),
(60, 'Cineplex Long An', 'Tầng 2, Vincom Tân An', 'Long An', '2025-08-26 09:18:25', '2025-08-26 09:18:25'),
(61, 'Lotte Cinema Long An', 'Tầng 3, Lotte Mart Tân An', 'Long An', '2025-08-26 09:18:25', '2025-08-26 09:18:25'),
(62, 'CGV Bắc Ninh', 'Tầng 2, Vincom Bắc Ninh', 'Bắc Ninh', '2025-08-26 09:18:25', '2025-08-26 09:18:25'),
(63, 'CineStar Bắc Ninh', 'Số 56 Lý Thái Tổ, TP Bắc Ninh', 'Bắc Ninh', '2025-08-26 09:18:25', '2025-08-26 09:18:25'),
(64, 'Galaxy Vĩnh Long', 'Tầng 2, Vincom Vĩnh Long', 'Vĩnh Long', '2025-08-26 09:18:25', '2025-08-26 09:18:25'),
(65, 'CGV Vĩnh Long', 'Tầng 3, Vincom Vĩnh Long', 'Vĩnh Long', '2025-08-26 09:18:25', '2025-08-26 09:18:25');

-- --------------------------------------------------------

--
-- Cấu trúc bảng cho bảng `failed_jobs`
--

CREATE TABLE `failed_jobs` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `uuid` varchar(255) NOT NULL,
  `connection` text NOT NULL,
  `queue` text NOT NULL,
  `payload` longtext NOT NULL,
  `exception` longtext NOT NULL,
  `failed_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Cấu trúc bảng cho bảng `food_items`
--

CREATE TABLE `food_items` (
  `item_id` bigint(20) UNSIGNED NOT NULL,
  `name` varchar(100) NOT NULL,
  `description` text DEFAULT NULL,
  `price` decimal(10,2) NOT NULL,
  `category` enum('Popcorn','Drinks','Snacks','Combo') NOT NULL,
  `image_url` varchar(500) DEFAULT NULL,
  `is_available` tinyint(1) NOT NULL DEFAULT 1,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Đang đổ dữ liệu cho bảng `food_items`
--

INSERT INTO `food_items` (`item_id`, `name`, `description`, `price`, `category`, `image_url`, `is_available`, `created_at`) VALUES
(1, 'Bắp rang bơ nhỏ', 'Bắp rang vị bơ size nhỏ', 35000.00, 'Popcorn', '/storage/food_items/food_1756955474_eJ536UDMFh.jfif', 1, '2025-09-04 02:15:52'),
(2, 'Bắp rang phô mai lớn', 'Bắp rang vị phô mai size lớn', 55000.00, 'Popcorn', '/storage/food_items/food_1756955441_iJzEW7xhY5.png', 1, '2025-09-04 02:15:52'),
(3, 'Bắp caramel', 'Bắp rang vị caramel ngọt ngào', 60000.00, 'Popcorn', '/storage/food_items/food_1756955414_olTzOCSJTW.jpg', 1, '2025-09-04 02:15:52'),
(4, 'Coca-Cola (M)', 'Nước ngọt Coca-Cola size vừa', 30000.00, 'Drinks', '/storage/food_items/food_1756955366_x74SFdtfzy.jfif', 1, '2025-09-04 02:15:52'),
(5, 'Pepsi (L)', 'Nước ngọt Pepsi size lớn', 35000.00, 'Drinks', '/storage/food_items/food_1756955315_0bawDzDv9j.jpg', 1, '2025-09-04 02:15:52'),
(6, 'Trà đào cam sả', 'Trà đào thanh mát', 40000.00, 'Drinks', '/storage/food_items/food_1756955212_CoeBRalSYH.jfif', 1, '2025-09-04 02:15:52'),
(7, 'Xúc xích nướng', 'Xúc xích Đức nướng', 45000.00, 'Snacks', '/storage/food_items/food_1756955186_ImIR4fX1Ba.png', 1, '2025-09-04 02:15:52'),
(8, 'Khoai tây chiên', 'Khoai tây chiên giòn rụm', 40000.00, 'Snacks', '/storage/food_items/food_1756955084_bRiNxdtfHZ.jfif', 1, '2025-09-04 02:15:52'),
(9, 'Gà rán miếng', 'Miếng gà rán giòn cay', 50000.00, 'Snacks', '/storage/food_items/food_1756955032_u88YGdOfMd.jpg', 1, '2025-09-04 02:15:52'),
(10, 'Combo 1 người', '1 bắp nhỏ + 1 nước M', 60000.00, 'Combo', '/storage/food_items/food_1756955005_6Ip1VGvfx6.jpg', 1, '2025-09-04 02:15:52'),
(11, 'Combo đôi', '1 bắp lớn + 2 nước L', 110000.00, 'Combo', '/storage/food_items/food_1756954926_YjOv8h2GD0.jpg', 1, '2025-09-04 02:15:52'),
(12, 'Combo gia đình', '2 bắp lớn + 4 nước L + 2 snacks', 220000.00, 'Combo', '/storage/food_items/food_1756954872_WBqDVPCPjj.jpg', 1, '2025-09-04 02:15:52');

-- --------------------------------------------------------

--
-- Cấu trúc bảng cho bảng `migrations`
--

CREATE TABLE `migrations` (
  `id` int(10) UNSIGNED NOT NULL,
  `migration` varchar(255) NOT NULL,
  `batch` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Đang đổ dữ liệu cho bảng `migrations`
--

INSERT INTO `migrations` (`id`, `migration`, `batch`) VALUES
(1, '2014_10_12_000000_create_users_table', 1),
(2, '2014_10_12_100000_create_password_reset_tokens_table', 1),
(3, '2019_08_19_000000_create_failed_jobs_table', 1),
(4, '2019_12_14_000001_create_personal_access_tokens_table', 1),
(7, '2025_08_08_074300_create_movies_table', 2),
(8, '2025_08_09_082822_create_cinemas_table', 2),
(9, '2025_08_10_040611_create_screens_table', 3),
(10, '2025_08_10_044932_create_seats_table', 4),
(11, '2025_08_11_042526_create_showtimes_table', 5),
(12, '2025_08_29_020038_create_pricing_table', 6),
(13, '2025_09_01_105536_create_food_items_table', 7),
(15, '2025_09_04_015626_create_promotions_table', 8),
(16, '2025_09_08_084649_create_bookings_table', 8),
(17, '2025_09_08_084658_create_booking_seats_table', 8),
(18, '2025_09_08_084704_create_booking_food_table', 8),
(19, '2025_09_09_035209_add_social_columns_to_users_table', 9),
(20, '2025_09_09_085224_update_showtimes_table', 10),
(21, '2025_10_07_065730_alter_bookings_add_name_phone_drop_amounts', 11),
(22, '2025_10_07_112431_drop_pricing_table', 12),
(23, '2025_01_10_000000_add_promotion_fields_to_bookings_table', 13),
(24, '2025_10_18_024244_create_sepay_table', 14),
(25, '2025_10_18_073500_add_remember_token_to_users_table', 15),
(26, '2025_10_18_010101_create_reviews_table', 16),
(27, '2025_11_24_041259_add_email_verified_at_to_users_table', 17);

-- --------------------------------------------------------

--
-- Cấu trúc bảng cho bảng `movies`
--

CREATE TABLE `movies` (
  `movie_id` bigint(20) UNSIGNED NOT NULL,
  `title` varchar(200) NOT NULL,
  `original_title` varchar(200) DEFAULT NULL,
  `description` text DEFAULT NULL,
  `duration` int(11) NOT NULL,
  `release_date` date DEFAULT NULL,
  `director` varchar(100) DEFAULT NULL,
  `cast` text DEFAULT NULL,
  `genre` varchar(100) DEFAULT NULL,
  `language` varchar(50) DEFAULT NULL,
  `country` varchar(50) DEFAULT NULL,
  `rating` decimal(2,1) DEFAULT NULL,
  `age_rating` varchar(10) DEFAULT NULL,
  `poster_url` varchar(500) DEFAULT NULL,
  `trailer_url` varchar(500) DEFAULT NULL,
  `status` enum('Coming Soon','Now Showing','Ended') NOT NULL DEFAULT 'Coming Soon',
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Đang đổ dữ liệu cho bảng `movies`
--

INSERT INTO `movies` (`movie_id`, `title`, `original_title`, `description`, `duration`, `release_date`, `director`, `cast`, `genre`, `language`, `country`, `rating`, `age_rating`, `poster_url`, `trailer_url`, `status`, `created_at`, `updated_at`) VALUES
(1, 'Avengers: Endgame', 'Avengers: Endgame', 'Cuộc chiến cuối cùng để cứu vũ trụ khỏi tay Thanos.', 181, '2019-04-26', 'Anthony Russo, Joe Russo', 'Robert Downey Jr., Chris Evans, Scarlett Johansson', NULL, 'Tiếng Anh', 'Mỹ', 6.0, 'T13', 'posters/1756197298_Avengers_Endgame_bia_teaser.jpg', 'https://example.com/trailer1.mp4', 'Now Showing', '2025-08-26 07:55:53', '2025-08-26 01:34:58'),
(2, 'The Lion King', 'The Lion King', 'Cuộc phiêu lưu của Simba để trở thành vua của savanna.', 118, '2019-07-19', 'Jon Favreau', 'Donald Glover, Beyoncé, Seth Rogen', 'Hoạt hình, Gia đình', 'Tiếng Anh', 'Mỹ', 7.0, 'P', '', 'https://example.com/trailer2.mp4', 'Now Showing', '2025-08-26 07:55:53', '2025-08-26 07:55:53'),
(3, 'Inception', 'Inception', 'Một nhóm đi vào những giấc mơ để ăn trộm thông tin quan trọng.', 148, '2010-07-16', 'Christopher Nolan', 'Leonardo DiCaprio, Joseph Gordon-Levitt, Ellen Page', NULL, 'Tiếng Anh', 'Mỹ', 5.0, 'T16', 'posters/1756197946_unnamed.png', 'https://example.com/trailer3.mp4', 'Now Showing', '2025-08-26 07:55:53', '2025-08-26 01:45:46'),
(4, 'Frozen II', 'Frozen II', 'Elsa và Anna phải đối mặt với một bí ẩn từ quá khứ của vương quốc.', 103, '2019-11-22', 'Chris Buck, Jennifer Lee', 'Idina Menzel, Kristen Bell, Josh Gad', NULL, 'Tiếng Anh', 'Mỹ', 4.0, 'P', 'posters/1756197900_Frozen_II_(2019_animated_film).jpg', 'https://example.com/trailer4.mp4', 'Ended', '2025-08-26 07:55:53', '2025-08-26 01:45:00'),
(5, 'Spider-Man: No Way Home', 'Spider-Man: No Way Home', 'Peter Parker phải đối mặt với những thử thách mới khi lỗ hổng không gian thời gian mở ra.', 148, '2021-12-17', 'Jon Watts', 'Tom Holland, Zendaya, Benedict Cumberbatch', 'Hành động, Khoa học viễn tưởng', 'Tiếng Anh', 'Mỹ', 8.6, 'T13', '', 'https://example.com/trailer5.mp4', 'Now Showing', '2025-08-26 07:57:18', '2025-08-26 07:57:18'),
(6, 'The Dark Knight', 'The Dark Knight', 'Batman đấu tranh với Joker, kẻ tâm thần muốn hủy hoại Gotham City.', 152, '2008-07-18', 'Christopher Nolan', 'Christian Bale, Heath Ledger, Aaron Eckhart', 'Hành động, Tội phạm', 'Tiếng Anh', 'Mỹ', 9.0, 'T16', '', 'https://example.com/trailer6.mp4', 'Now Showing', '2025-08-26 07:57:18', '2025-08-26 07:57:18'),
(7, 'Toy Story 4', 'Toy Story 4', 'Woody và Buzz Lightyear phải đối mặt với những cuộc phiêu lưu mới khi gặp một món đồ chơi mới.', 100, '2019-06-21', 'Josh Cooley', 'Tom Hanks, Tim Allen, Tony Hale', 'Hoạt hình, Gia đình', 'Tiếng Anh', 'Mỹ', 7.8, 'P', '', 'https://example.com/trailer7.mp4', 'Now Showing', '2025-08-26 07:57:18', '2025-08-26 07:57:18'),
(8, 'Jurassic World: Dominion', 'Jurassic World: Dominion', 'Dinosaur và con người phải sống chung trong một thế giới hỗn loạn.', 147, '2022-06-10', 'Colin Trevorrow', 'Chris Pratt, Bryce Dallas Howard, Sam Neill', 'Hành động, Phiêu lưu, Khoa học viễn tưởng', 'Tiếng Anh', 'Mỹ', 6.8, 'T13', '', 'https://example.com/trailer8.mp4', 'Coming Soon', '2025-08-26 07:57:18', '2025-08-26 07:57:18'),
(9, 'Guardians of the Galaxy Vol. 3', 'Guardians of the Galaxy Vol. 3', 'Các Guardian phải đối mặt với thử thách lớn để cứu người bạn Rocket của mình.', 150, '2023-05-05', 'James Gunn', 'Chris Pratt, Zoe Saldana, Dave Bautista', NULL, 'Tiếng Anh', 'Mỹ', NULL, 'T13', 'posters/1756197925_p17845781_v_v13_ar.jpg', 'https://example.com/trailer9.mp4', 'Now Showing', '2025-08-26 07:57:18', '2025-08-26 01:45:25'),
(10, 'Shang-Chi and the Legend of the Ten Rings', 'Shang-Chi and the Legend of the Ten Rings', 'Shang-Chi khám phá bí mật gia đình mình và đấu với những thế lực siêu nhiên.', 132, '2021-09-03', 'Destin Daniel Cretton', 'Simu Liu, Awkwafina, Tony Leung', 'Hành động, Khoa học viễn tưởng', 'Tiếng Anh', 'Mỹ', 7.9, 'T13', '', 'https://example.com/trailer10.mp4', 'Now Showing', '2025-08-26 07:57:18', '2025-08-26 07:57:18'),
(11, 'Fast & Furious 9', 'Fast & Furious 9', 'Dominic Toretto và đội của mình đối đầu với kẻ thù mới, là em trai của Toretto.', 145, '2021-06-25', 'Justin Lin', 'Vin Diesel, Michelle Rodriguez, John Cena', NULL, 'Tiếng Anh', 'Mỹ', 9.0, 'T13', 'posters/1756197834_unnamed.jpg', 'https://example.com/trailer11.mp4', 'Ended', '2025-08-26 07:57:18', '2025-08-26 01:43:54'),
(12, 'Black Panther: Wakanda Forever', 'Black Panther: Wakanda Forever', 'Wakanda phải tìm cách bảo vệ quốc gia sau cái chết của Black Panther.', 161, '2022-11-11', 'Ryan Coogler', 'Letitia Wright, Angela Bassett, Lupita Nyong\'o', NULL, 'Tiếng Anh', 'Mỹ', 6.8, 'T13', 'posters/1756197371_pp_disney_blackpanther_wakandaforever_1289_d3419b8f.jpeg', 'https://example.com/trailer12.mp4', 'Now Showing', '2025-08-26 07:57:18', '2025-08-26 01:36:11'),
(13, 'Doctor Strange in the Multiverse of Madness', 'Doctor Strange in the Multiverse of Madness', 'Doctor Strange đối mặt với những mối đe dọa từ vũ trụ đa chiều.', 126, '2022-05-06', 'Sam Raimi', 'Benedict Cumberbatch, Elizabeth Olsen, Benedict Wong', NULL, 'Tiếng Anh', 'Mỹ', NULL, 'T16', 'posters/1756197803_p_drstrangeinthemultiverseofmadness_245_476cabb1.jpeg', 'https://example.com/trailer13.mp4', 'Now Showing', '2025-08-26 07:57:18', '2025-08-26 01:43:23'),
(14, 'Avengers: Infinity War', 'Avengers: Infinity War', 'Avengers hợp sức để ngừng Thanos thu thập các viên đá vô cực.', 149, '2018-04-27', 'Anthony Russo, Joe Russo', 'Robert Downey Jr., Chris Hemsworth, Mark Ruffalo', NULL, 'Tiếng Anh', 'Mỹ', NULL, 'T13', 'posters/1756197341_Avengers_Infinity_war_poster.webp', 'https://example.com/trailer14.mp4', 'Ended', '2025-08-26 07:57:18', '2025-08-26 01:35:41'),
(15, 'The Matrix Resurrections', 'The Matrix Resurrections', 'Neo trở lại trong một vũ trụ mới đầy nguy hiểm và bất ngờ.', 148, '2021-12-22', 'Lana Wachowski', 'Keanu Reeves, Carrie-Anne Moss, Yahya Abdul-Mateen II', 'Hành động, Khoa học viễn tưởng', 'Tiếng Anh', 'Mỹ', 5.7, 'T16', '', 'https://example.com/trailer15.mp4', 'Ended', '2025-08-26 07:57:18', '2025-08-26 07:57:18'),
(16, 'Avatar', 'Avatar', 'Một thế giới Pandora tuyệt đẹp, nơi con người và người bản địa giao tranh vì tài nguyên.', 162, '2009-12-18', 'James Cameron', 'Sam Worthington, Zoe Saldana, Sigourney Weaver', 'Hoạt hình', 'Tiếng Anh', 'Mỹ', 3.7, 'T13', 'posters/1756197189_avatar.jfif', 'https://example.com/trailer16.mp4', 'Now Showing', '2025-08-26 07:57:27', '2025-08-26 01:41:58'),
(17, 'The Shawshank Redemption', 'The Shawshank Redemption', 'Câu chuyện về tình bạn và sự hy vọng trong một nhà tù khắc nghiệt.', 142, '1994-09-22', 'Frank Darabont', 'Tim Robbins, Morgan Freeman, Bob Gunton', 'Tội phạm, Chính kịch', 'Tiếng Anh', 'Mỹ', 9.3, 'T16', '', 'https://example.com/trailer17.mp4', 'Now Showing', '2025-08-26 07:57:27', '2025-08-26 07:57:27'),
(18, 'Forrest Gump', 'Forrest Gump', 'Một người đàn ông đơn giản nhưng đã có cuộc đời đầy ắp những sự kiện lịch sử.', 142, '1994-07-06', 'Robert Zemeckis', 'Tom Hanks, Robin Wright, Gary Sinise', NULL, 'Tiếng Anh', 'Mỹ', 4.8, 'T13', 'posters/1756197871_gump.jpg', 'https://example.com/trailer18.mp4', 'Now Showing', '2025-08-26 07:57:27', '2025-08-26 01:44:31'),
(19, 'The Godfather', 'The Godfather', 'Một gia đình mafia và cuộc chiến nội bộ dẫn đến sự trả thù đẫm máu.', 175, '1972-03-24', 'Francis Ford Coppola', 'Marlon Brando, Al Pacino, James Caan', 'Tội phạm, Chính kịch', 'Tiếng Anh', 'Mỹ', 9.2, 'T16', '', 'https://example.com/trailer19.mp4', 'Ended', '2025-08-26 07:57:27', '2025-08-26 07:57:27'),
(20, 'Pulp Fiction', 'Pulp Fiction', 'Một loạt câu chuyện giao nhau về tội ác và cuộc sống ngoài vòng pháp luật.', 154, '1994-10-14', 'Quentin Tarantino', 'John Travolta, Uma Thurman, Samuel L. Jackson', 'Tội phạm, Chính kịch', 'Tiếng Anh', 'Mỹ', 8.9, 'T16', '', 'https://example.com/trailer20.mp4', 'Now Showing', '2025-08-26 07:57:27', '2025-08-26 07:57:27'),
(21, 'The Dark Knight Rises', 'The Dark Knight Rises', 'Batman quay lại Gotham để đối đầu với Bane, kẻ muốn phá hủy thành phố.', 164, '2012-07-20', 'Christopher Nolan', 'Christian Bale, Tom Hardy, Anne Hathaway', 'Hành động, Tội phạm', 'Tiếng Anh', 'Mỹ', 8.4, 'T13', '', 'https://example.com/trailer21.mp4', 'Now Showing', '2025-08-26 07:57:27', '2025-08-26 07:57:27'),
(22, 'The Lord of the Rings: The Fellowship of the Ring', 'The Lord of the Rings: The Fellowship of the Ring', 'Cuộc hành trình của Frodo và những người bạn để tiêu diệt chiếc nhẫn quyền lực.', 178, '2001-12-19', 'Peter Jackson', 'Elijah Wood, Ian McKellen, Viggo Mortensen', 'Hành động, Phiêu lưu, Fantasty', 'Tiếng Anh', 'New Zealand', 8.8, 'T13', '', 'https://example.com/trailer22.mp4', 'Now Showing', '2025-08-26 07:57:27', '2025-08-26 07:57:27'),
(23, 'The Lord of the Rings: The Two Towers', 'The Lord of the Rings: The Two Towers', 'Frodo và Sam tiếp tục hành trình, trong khi Gandalf và Aragorn chuẩn bị chiến đấu chống lại Sauron.', 179, '2002-12-18', 'Peter Jackson', 'Elijah Wood, Ian McKellen, Viggo Mortensen', 'Hành động, Phiêu lưu, Fantasty', 'Tiếng Anh', 'New Zealand', 8.7, 'T13', '', 'https://example.com/trailer23.mp4', 'Now Showing', '2025-08-26 07:57:27', '2025-08-26 07:57:27'),
(24, 'The Lord of the Rings: The Return of the King', 'The Lord of the Rings: The Return of the King', 'Cuộc chiến cuối cùng giữa các lực lượng thiện và ác để giành lấy chiếc nhẫn quyền lực.', 201, '2003-12-17', 'Peter Jackson', 'Elijah Wood, Ian McKellen, Viggo Mortensen', 'Hành động, Phiêu lưu, Fantasty', 'Tiếng Anh', 'New Zealand', 8.9, 'T13', '', 'https://example.com/trailer24.mp4', 'Now Showing', '2025-08-26 07:57:27', '2025-08-26 07:57:27'),
(25, 'Schindler\'s List', 'Schindler\'s List', 'Một câu chuyện có thật về Oskar Schindler, người đã cứu sống hàng nghìn người Do Thái trong cuộc diệt chủng Holocaust.', 195, '1993-12-15', 'Steven Spielberg', 'Liam Neeson, Ben Kingsley, Ralph Fiennes', 'Chính kịch, Lịch sử', 'Tiếng Anh', 'Mỹ', 9.0, 'T16', '', 'https://example.com/trailer25.mp4', 'Ended', '2025-08-26 07:57:27', '2025-08-26 07:57:27');

-- --------------------------------------------------------

--
-- Cấu trúc bảng cho bảng `password_reset_tokens`
--

CREATE TABLE `password_reset_tokens` (
  `email` varchar(255) NOT NULL,
  `token` varchar(255) NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Cấu trúc bảng cho bảng `personal_access_tokens`
--

CREATE TABLE `personal_access_tokens` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `tokenable_type` varchar(255) NOT NULL,
  `tokenable_id` bigint(20) UNSIGNED NOT NULL,
  `name` varchar(255) NOT NULL,
  `token` varchar(64) NOT NULL,
  `abilities` text DEFAULT NULL,
  `last_used_at` timestamp NULL DEFAULT NULL,
  `expires_at` timestamp NULL DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Cấu trúc bảng cho bảng `promotions`
--

CREATE TABLE `promotions` (
  `promotion_id` bigint(20) UNSIGNED NOT NULL,
  `code` varchar(20) NOT NULL,
  `name` varchar(100) NOT NULL,
  `description` text DEFAULT NULL,
  `discount_type` enum('Percentage','Fixed Amount') NOT NULL,
  `discount_value` decimal(10,2) NOT NULL,
  `min_amount` decimal(10,2) NOT NULL DEFAULT 0.00,
  `max_discount` decimal(10,2) DEFAULT NULL,
  `usage_limit` int(11) DEFAULT NULL,
  `used_count` int(11) NOT NULL DEFAULT 0,
  `start_date` date NOT NULL,
  `end_date` date NOT NULL,
  `is_active` tinyint(1) NOT NULL DEFAULT 1,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Đang đổ dữ liệu cho bảng `promotions`
--

INSERT INTO `promotions` (`promotion_id`, `code`, `name`, `description`, `discount_type`, `discount_value`, `min_amount`, `max_discount`, `usage_limit`, `used_count`, `start_date`, `end_date`, `is_active`, `created_at`, `updated_at`) VALUES
(1, 'WEEKEND15', 'Cuối tuần -15%', 'Giảm 15% cho mọi vé vào Thứ 7/CN', 'Percentage', 15.00, 0.00, 40000.00, 1000, 0, '2025-09-06', '2025-10-06', 1, '2025-09-08 08:51:56', '2025-09-08 08:51:56'),
(2, 'NEW50K', 'Tân khách -50K', 'Giảm 50.000đ cho đơn đầu tiên', 'Fixed Amount', 50000.00, 100000.00, NULL, 1, 1, '2025-09-01', '2025-12-31', 1, '2025-09-08 08:51:56', '2025-10-17 07:20:50'),
(3, 'MORNING20', 'Sáng vui -20%', 'Giảm 20% cho suất trước 11:00', 'Percentage', 20.00, 0.00, 30000.00, 500, 0, '2025-09-01', '2025-10-31', 1, '2025-09-08 08:51:56', '2025-09-08 08:51:56'),
(4, 'STUDENT10', 'Sinh viên -10%', 'Nhập mã kèm thẻ SV tại quầy', 'Percentage', 10.00, 0.00, 25000.00, 2000, 0, '2025-09-08', '2026-03-31', 1, '2025-09-08 08:51:56', '2025-09-08 08:51:56'),
(5, 'COUPLE80K', 'Cặp đôi -80K', 'Áp dụng khi đặt 2 ghế Couple', 'Fixed Amount', 80000.00, 200000.00, NULL, 1000, 0, '2025-09-08', '2025-11-30', 1, '2025-09-08 08:51:56', '2025-09-08 08:51:56'),
(6, 'COMBO15', 'Combo bắp nước -15%', 'Giảm 15% tổng combo khi mua kèm vé', 'Percentage', 15.00, 50000.00, 30000.00, 1500, 0, '2025-09-08', '2025-12-31', 1, '2025-09-08 08:51:56', '2025-09-08 08:51:56'),
(7, 'MIDFEST25', 'Trung Thu -25%', 'Ưu đãi Trung Thu', 'Percentage', 25.00, 0.00, 50000.00, 800, 0, '2025-08-30', '2025-09-07', 0, '2025-09-08 08:51:56', '2025-09-08 08:51:56'),
(8, 'EVENING10', 'Buổi tối -10%', 'Áp dụng sau 18:00', 'Percentage', 10.00, 0.00, 35000.00, 3000, 0, '2025-09-08', '2026-01-31', 1, '2025-09-08 08:51:56', '2025-09-08 08:51:56'),
(9, 'LOYAL30K', 'Thành viên -30K', 'Cho khách có ≥200 điểm', 'Fixed Amount', 30000.00, 80000.00, NULL, 5000, 0, '2025-09-08', '2026-09-08', 0, '2025-09-08 08:51:56', '2025-09-08 01:52:51'),
(10, 'NATDAY24', 'Lễ Quốc Khánh -24%', 'Áp dụng dịp 2/9', 'Percentage', 24.00, 0.00, 60000.00, 500, 0, '2025-08-31', '2025-09-03', 0, '2025-09-08 08:51:56', '2025-09-08 08:51:56'),
(11, 'BANKZALOPAY15', 'Ví/ZaloPay -15%', 'Giảm khi thanh toán qua ví', 'Percentage', 15.00, 100000.00, 50000.00, 3000, 0, '2025-09-08', '2025-12-31', 0, '2025-09-08 08:51:56', '2025-09-08 01:52:54'),
(12, 'FLASH40', 'Flash Sale -40%', 'Số lượng có hạn, không hoàn hủy', 'Percentage', 40.00, 0.00, 70000.00, 200, 0, '2025-09-10', '2025-09-12', 1, '2025-09-08 08:51:56', '2025-09-08 08:51:56');

-- --------------------------------------------------------

--
-- Cấu trúc bảng cho bảng `reviews`
--

CREATE TABLE `reviews` (
  `review_id` bigint(20) UNSIGNED NOT NULL,
  `user_id` bigint(20) UNSIGNED NOT NULL,
  `movie_id` bigint(20) UNSIGNED NOT NULL,
  `rating` tinyint(3) UNSIGNED NOT NULL,
  `comment` text DEFAULT NULL,
  `is_approved` tinyint(1) NOT NULL DEFAULT 0,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Cấu trúc bảng cho bảng `screens`
--

CREATE TABLE `screens` (
  `screen_id` bigint(20) UNSIGNED NOT NULL,
  `cinema_id` bigint(20) UNSIGNED NOT NULL,
  `screen_name` varchar(50) NOT NULL,
  `total_seats` int(11) NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Đang đổ dữ liệu cho bảng `screens`
--

INSERT INTO `screens` (`screen_id`, `cinema_id`, `screen_name`, `total_seats`, `created_at`, `updated_at`) VALUES
(1, 33, 'Phòng 1', 108, '2025-08-28 01:48:57', '2025-08-28 01:59:57'),
(2, 30, 'Phòng 2', 108, '2025-09-03 20:51:46', '2025-09-03 20:51:46'),
(3, 33, 'Phòng 2', 50, '2025-09-03 20:53:13', '2025-09-03 20:53:13'),
(4, 33, 'Phòng 3', 50, '2025-09-04 20:46:28', '2025-09-04 20:46:40'),
(5, 35, 'DEMO123', 50, '2025-10-21 07:56:52', '2025-10-21 07:57:07');

-- --------------------------------------------------------

--
-- Cấu trúc bảng cho bảng `seats`
--

CREATE TABLE `seats` (
  `seat_id` bigint(20) UNSIGNED NOT NULL,
  `screen_id` bigint(20) UNSIGNED NOT NULL,
  `row_name` varchar(5) NOT NULL,
  `seat_number` int(11) NOT NULL,
  `seat_type` enum('Normal','VIP','Couple','Disabled') NOT NULL DEFAULT 'Normal',
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Đang đổ dữ liệu cho bảng `seats`
--

INSERT INTO `seats` (`seat_id`, `screen_id`, `row_name`, `seat_number`, `seat_type`, `created_at`, `updated_at`) VALUES
(644, 1, 'A', 1, 'Normal', '2025-08-28 02:00:03', '2025-08-28 02:00:03'),
(645, 1, 'A', 2, 'Normal', '2025-08-28 02:00:03', '2025-08-28 02:00:03'),
(646, 1, 'A', 3, 'Normal', '2025-08-28 02:00:03', '2025-08-28 02:00:03'),
(647, 1, 'A', 4, 'Normal', '2025-08-28 02:00:03', '2025-08-28 02:00:03'),
(648, 1, 'A', 5, 'Normal', '2025-08-28 02:00:03', '2025-08-28 02:00:03'),
(649, 1, 'A', 6, 'Normal', '2025-08-28 02:00:03', '2025-08-28 02:00:03'),
(650, 1, 'A', 7, 'Normal', '2025-08-28 02:00:03', '2025-08-28 02:00:03'),
(651, 1, 'A', 8, 'Normal', '2025-08-28 02:00:03', '2025-08-28 02:00:03'),
(652, 1, 'A', 9, 'Normal', '2025-08-28 02:00:03', '2025-08-28 02:00:03'),
(653, 1, 'A', 10, 'Normal', '2025-08-28 02:00:03', '2025-08-28 02:00:03'),
(654, 1, 'A', 11, 'Normal', '2025-08-28 02:00:03', '2025-08-28 02:00:03'),
(655, 1, 'A', 12, 'Normal', '2025-08-28 02:00:03', '2025-08-28 02:00:03'),
(656, 1, 'B', 1, 'Normal', '2025-08-28 02:00:03', '2025-08-28 02:00:03'),
(657, 1, 'B', 2, 'Normal', '2025-08-28 02:00:03', '2025-08-28 02:00:03'),
(658, 1, 'B', 3, 'Normal', '2025-08-28 02:00:03', '2025-08-28 02:00:03'),
(659, 1, 'B', 4, 'Normal', '2025-08-28 02:00:03', '2025-08-28 02:00:03'),
(660, 1, 'B', 5, 'Normal', '2025-08-28 02:00:03', '2025-08-28 02:00:03'),
(661, 1, 'B', 6, 'Normal', '2025-08-28 02:00:03', '2025-08-28 02:00:03'),
(662, 1, 'B', 7, 'Normal', '2025-08-28 02:00:03', '2025-08-28 02:00:03'),
(663, 1, 'B', 8, 'Normal', '2025-08-28 02:00:03', '2025-08-28 02:00:03'),
(664, 1, 'B', 9, 'Normal', '2025-08-28 02:00:03', '2025-08-28 02:00:03'),
(665, 1, 'B', 10, 'Normal', '2025-08-28 02:00:03', '2025-08-28 02:00:03'),
(666, 1, 'B', 11, 'Normal', '2025-08-28 02:00:03', '2025-08-28 02:00:03'),
(667, 1, 'B', 12, 'Normal', '2025-08-28 02:00:03', '2025-08-28 02:00:03'),
(668, 1, 'B', 13, 'Normal', '2025-08-28 02:00:03', '2025-08-28 02:00:03'),
(669, 1, 'B', 14, 'Normal', '2025-08-28 02:00:03', '2025-08-28 02:00:03'),
(670, 1, 'C', 1, 'Normal', '2025-08-28 02:00:03', '2025-08-28 02:00:03'),
(671, 1, 'C', 2, 'Normal', '2025-08-28 02:00:03', '2025-08-28 02:00:03'),
(672, 1, 'C', 3, 'Normal', '2025-08-28 02:00:03', '2025-08-28 02:00:03'),
(673, 1, 'C', 4, 'Normal', '2025-08-28 02:00:03', '2025-08-28 02:00:03'),
(674, 1, 'C', 5, 'Normal', '2025-08-28 02:00:03', '2025-08-28 02:00:03'),
(675, 1, 'C', 6, 'Normal', '2025-08-28 02:00:03', '2025-08-28 02:00:03'),
(676, 1, 'C', 7, 'Normal', '2025-08-28 02:00:03', '2025-08-28 02:00:03'),
(677, 1, 'C', 8, 'Normal', '2025-08-28 02:00:03', '2025-08-28 02:00:03'),
(678, 1, 'C', 9, 'Normal', '2025-08-28 02:00:03', '2025-08-28 02:00:03'),
(679, 1, 'C', 10, 'Normal', '2025-08-28 02:00:03', '2025-08-28 02:00:03'),
(680, 1, 'C', 11, 'Normal', '2025-08-28 02:00:03', '2025-08-28 02:00:03'),
(681, 1, 'C', 12, 'Normal', '2025-08-28 02:00:03', '2025-08-28 02:00:03'),
(682, 1, 'C', 13, 'Normal', '2025-08-28 02:00:03', '2025-08-28 02:00:03'),
(683, 1, 'C', 14, 'Normal', '2025-08-28 02:00:03', '2025-08-28 02:00:03'),
(684, 1, 'D', 1, 'Normal', '2025-08-28 02:00:03', '2025-08-28 02:00:03'),
(685, 1, 'D', 2, 'Normal', '2025-08-28 02:00:03', '2025-08-28 02:00:03'),
(686, 1, 'D', 3, 'Normal', '2025-08-28 02:00:03', '2025-08-28 02:00:03'),
(687, 1, 'D', 4, 'Normal', '2025-08-28 02:00:03', '2025-08-28 02:00:03'),
(688, 1, 'D', 5, 'VIP', '2025-08-28 02:00:03', '2025-08-28 02:00:03'),
(689, 1, 'D', 6, 'VIP', '2025-08-28 02:00:03', '2025-08-28 02:00:03'),
(690, 1, 'D', 7, 'VIP', '2025-08-28 02:00:03', '2025-08-28 02:00:03'),
(691, 1, 'D', 8, 'VIP', '2025-08-28 02:00:03', '2025-08-28 02:00:03'),
(692, 1, 'D', 9, 'VIP', '2025-08-28 02:00:03', '2025-08-28 02:00:03'),
(693, 1, 'D', 10, 'VIP', '2025-08-28 02:00:03', '2025-08-28 02:00:03'),
(694, 1, 'D', 11, 'Normal', '2025-08-28 02:00:03', '2025-08-28 02:00:03'),
(695, 1, 'D', 12, 'Normal', '2025-08-28 02:00:03', '2025-08-28 02:00:03'),
(696, 1, 'D', 13, 'Normal', '2025-08-28 02:00:03', '2025-08-28 02:00:03'),
(697, 1, 'D', 14, 'Normal', '2025-08-28 02:00:03', '2025-08-28 02:00:03'),
(698, 1, 'E', 1, 'Normal', '2025-08-28 02:00:03', '2025-08-28 02:00:03'),
(699, 1, 'E', 2, 'Normal', '2025-08-28 02:00:03', '2025-08-28 02:00:03'),
(700, 1, 'E', 3, 'Normal', '2025-08-28 02:00:03', '2025-08-28 02:00:03'),
(701, 1, 'E', 4, 'Normal', '2025-08-28 02:00:03', '2025-08-28 02:00:03'),
(702, 1, 'E', 5, 'Normal', '2025-08-28 02:00:03', '2025-08-28 02:00:03'),
(703, 1, 'E', 6, 'Normal', '2025-08-28 02:00:03', '2025-08-28 02:00:03'),
(704, 1, 'E', 7, 'Normal', '2025-08-28 02:00:03', '2025-08-28 02:00:03'),
(705, 1, 'E', 8, 'Normal', '2025-08-28 02:00:03', '2025-08-28 02:00:03'),
(706, 1, 'E', 9, 'Normal', '2025-08-28 02:00:03', '2025-08-28 02:00:03'),
(707, 1, 'E', 10, 'Normal', '2025-08-28 02:00:03', '2025-08-28 02:00:03'),
(708, 1, 'E', 11, 'Normal', '2025-08-28 02:00:03', '2025-08-28 02:00:03'),
(709, 1, 'E', 12, 'Normal', '2025-08-28 02:00:03', '2025-08-28 02:00:03'),
(710, 1, 'E', 13, 'Normal', '2025-08-28 02:00:03', '2025-08-28 02:00:03'),
(711, 1, 'E', 14, 'Normal', '2025-08-28 02:00:03', '2025-08-28 02:00:03'),
(712, 1, 'F', 1, 'Normal', '2025-08-28 02:00:03', '2025-08-28 02:00:03'),
(713, 1, 'F', 2, 'Normal', '2025-08-28 02:00:03', '2025-08-28 02:00:03'),
(714, 1, 'F', 3, 'Normal', '2025-08-28 02:00:03', '2025-08-28 02:00:03'),
(715, 1, 'F', 4, 'Normal', '2025-08-28 02:00:03', '2025-08-28 02:00:03'),
(716, 1, 'F', 5, 'Normal', '2025-08-28 02:00:03', '2025-08-28 02:00:03'),
(717, 1, 'F', 6, 'Normal', '2025-08-28 02:00:03', '2025-08-28 02:00:03'),
(718, 1, 'F', 7, 'Normal', '2025-08-28 02:00:03', '2025-08-28 02:00:03'),
(719, 1, 'F', 8, 'Normal', '2025-08-28 02:00:03', '2025-08-28 02:00:03'),
(720, 1, 'F', 9, 'Normal', '2025-08-28 02:00:03', '2025-08-28 02:00:03'),
(721, 1, 'F', 10, 'Normal', '2025-08-28 02:00:03', '2025-08-28 02:00:03'),
(722, 1, 'F', 11, 'Normal', '2025-08-28 02:00:03', '2025-08-28 02:00:03'),
(723, 1, 'F', 12, 'Normal', '2025-08-28 02:00:03', '2025-08-28 02:00:03'),
(724, 1, 'F', 13, 'Normal', '2025-08-28 02:00:03', '2025-08-28 02:00:03'),
(725, 1, 'F', 14, 'Normal', '2025-08-28 02:00:03', '2025-08-28 02:00:03'),
(726, 1, 'G', 1, 'Normal', '2025-08-28 02:00:03', '2025-08-28 02:00:03'),
(727, 1, 'G', 2, 'Normal', '2025-08-28 02:00:03', '2025-08-28 02:00:03'),
(728, 1, 'G', 3, 'Normal', '2025-08-28 02:00:03', '2025-08-28 02:00:03'),
(729, 1, 'G', 4, 'Normal', '2025-08-28 02:00:03', '2025-08-28 02:00:03'),
(730, 1, 'G', 5, 'Normal', '2025-08-28 02:00:03', '2025-08-28 02:00:03'),
(731, 1, 'G', 6, 'Normal', '2025-08-28 02:00:03', '2025-08-28 02:00:03'),
(732, 1, 'G', 7, 'Normal', '2025-08-28 02:00:03', '2025-08-28 02:00:03'),
(733, 1, 'G', 8, 'Normal', '2025-08-28 02:00:03', '2025-08-28 02:00:03'),
(734, 1, 'G', 9, 'Normal', '2025-08-28 02:00:03', '2025-08-28 02:00:03'),
(735, 1, 'G', 10, 'Normal', '2025-08-28 02:00:03', '2025-08-28 02:00:03'),
(736, 1, 'G', 11, 'Normal', '2025-08-28 02:00:03', '2025-08-28 02:00:03'),
(737, 1, 'G', 12, 'Normal', '2025-08-28 02:00:03', '2025-08-28 02:00:03'),
(738, 1, 'G', 13, 'Normal', '2025-08-28 02:00:03', '2025-08-28 02:00:03'),
(739, 1, 'G', 14, 'Normal', '2025-08-28 02:00:03', '2025-08-28 02:00:03'),
(740, 1, 'H', 1, 'Normal', '2025-08-28 02:00:03', '2025-08-28 02:00:03'),
(741, 1, 'H', 2, 'Normal', '2025-08-28 02:00:03', '2025-08-28 02:00:03'),
(742, 1, 'H', 3, 'Normal', '2025-08-28 02:00:03', '2025-08-28 02:00:03'),
(743, 1, 'H', 4, 'Normal', '2025-08-28 02:00:03', '2025-08-28 02:00:03'),
(744, 1, 'H', 5, 'Normal', '2025-08-28 02:00:03', '2025-08-28 02:00:03'),
(745, 1, 'H', 6, 'Normal', '2025-08-28 02:00:03', '2025-08-28 02:00:03'),
(746, 1, 'H', 7, 'Normal', '2025-08-28 02:00:03', '2025-08-28 02:00:03'),
(747, 1, 'H', 8, 'Normal', '2025-08-28 02:00:03', '2025-08-28 02:00:03'),
(748, 1, 'H', 9, 'Normal', '2025-08-28 02:00:03', '2025-08-28 02:00:03'),
(749, 1, 'H', 10, 'Normal', '2025-08-28 02:00:03', '2025-08-28 02:00:03'),
(750, 1, 'H', 11, 'Normal', '2025-08-28 02:00:03', '2025-08-28 02:00:03'),
(751, 1, 'H', 12, 'Normal', '2025-08-28 02:00:03', '2025-08-28 02:00:03'),
(960, 4, 'A', 1, 'Normal', '2025-09-04 20:46:40', '2025-09-04 20:46:40'),
(961, 4, 'A', 2, 'Normal', '2025-09-04 20:46:40', '2025-09-04 20:46:40'),
(962, 4, 'A', 3, 'Normal', '2025-09-04 20:46:40', '2025-09-04 20:46:40'),
(963, 4, 'A', 4, 'Normal', '2025-09-04 20:46:40', '2025-09-04 20:46:40'),
(964, 4, 'A', 5, 'Normal', '2025-09-04 20:46:40', '2025-09-04 20:46:40'),
(965, 4, 'A', 6, 'Normal', '2025-09-04 20:46:40', '2025-09-04 20:46:40'),
(966, 4, 'A', 7, 'Normal', '2025-09-04 20:46:40', '2025-09-04 20:46:40'),
(967, 4, 'A', 8, 'Normal', '2025-09-04 20:46:40', '2025-09-04 20:46:40'),
(968, 4, 'A', 9, 'Normal', '2025-09-04 20:46:40', '2025-09-04 20:46:40'),
(969, 4, 'A', 10, 'Normal', '2025-09-04 20:46:40', '2025-09-04 20:46:40'),
(970, 4, 'B', 1, 'Normal', '2025-09-04 20:46:40', '2025-09-04 20:46:40'),
(971, 4, 'B', 2, 'Normal', '2025-09-04 20:46:40', '2025-09-04 20:46:40'),
(972, 4, 'B', 3, 'Normal', '2025-09-04 20:46:40', '2025-09-04 20:46:40'),
(973, 4, 'B', 4, 'Normal', '2025-09-04 20:46:40', '2025-09-04 20:46:40'),
(974, 4, 'B', 5, 'Normal', '2025-09-04 20:46:40', '2025-09-04 20:46:40'),
(975, 4, 'B', 6, 'Normal', '2025-09-04 20:46:40', '2025-09-04 20:46:40'),
(976, 4, 'B', 7, 'Normal', '2025-09-04 20:46:40', '2025-09-04 20:46:40'),
(977, 4, 'B', 8, 'Normal', '2025-09-04 20:46:40', '2025-09-04 20:46:40'),
(978, 4, 'B', 9, 'Normal', '2025-09-04 20:46:40', '2025-09-04 20:46:40'),
(979, 4, 'B', 10, 'Normal', '2025-09-04 20:46:40', '2025-09-04 20:46:40'),
(980, 4, 'C', 1, 'Normal', '2025-09-04 20:46:40', '2025-09-04 20:46:40'),
(981, 4, 'C', 2, 'Normal', '2025-09-04 20:46:40', '2025-09-04 20:46:40'),
(982, 4, 'C', 3, 'Normal', '2025-09-04 20:46:40', '2025-09-04 20:46:40'),
(983, 4, 'C', 4, 'VIP', '2025-09-04 20:46:40', '2025-09-04 20:46:40'),
(984, 4, 'C', 5, 'VIP', '2025-09-04 20:46:40', '2025-09-04 20:46:40'),
(985, 4, 'C', 6, 'VIP', '2025-09-04 20:46:40', '2025-09-04 20:46:40'),
(986, 4, 'C', 7, 'VIP', '2025-09-04 20:46:40', '2025-09-04 20:46:40'),
(987, 4, 'C', 8, 'Normal', '2025-09-04 20:46:40', '2025-09-04 20:46:40'),
(988, 4, 'C', 9, 'Normal', '2025-09-04 20:46:40', '2025-09-04 20:46:40'),
(989, 4, 'C', 10, 'Normal', '2025-09-04 20:46:40', '2025-09-04 20:46:40'),
(990, 4, 'D', 1, 'Normal', '2025-09-04 20:46:40', '2025-09-04 20:46:40'),
(991, 4, 'D', 2, 'Normal', '2025-09-04 20:46:40', '2025-09-04 20:46:40'),
(992, 4, 'D', 3, 'Normal', '2025-09-04 20:46:40', '2025-09-04 20:46:40'),
(993, 4, 'D', 4, 'Normal', '2025-09-04 20:46:40', '2025-09-04 20:46:40'),
(994, 4, 'D', 5, 'Normal', '2025-09-04 20:46:40', '2025-09-04 20:46:40'),
(995, 4, 'D', 6, 'Normal', '2025-09-04 20:46:40', '2025-09-04 20:46:40'),
(996, 4, 'D', 7, 'Normal', '2025-09-04 20:46:40', '2025-09-04 20:46:40'),
(997, 4, 'D', 8, 'Normal', '2025-09-04 20:46:40', '2025-09-04 20:46:40'),
(998, 4, 'D', 9, 'Normal', '2025-09-04 20:46:40', '2025-09-04 20:46:40'),
(999, 4, 'D', 10, 'Normal', '2025-09-04 20:46:40', '2025-09-04 20:46:40'),
(1000, 4, 'E', 1, 'Normal', '2025-09-04 20:46:40', '2025-09-04 20:46:40'),
(1001, 4, 'E', 2, 'Normal', '2025-09-04 20:46:40', '2025-09-04 20:46:40'),
(1002, 4, 'E', 3, 'Normal', '2025-09-04 20:46:40', '2025-09-04 20:46:40'),
(1003, 4, 'E', 4, 'Normal', '2025-09-04 20:46:40', '2025-09-04 20:46:40'),
(1004, 4, 'E', 5, 'Normal', '2025-09-04 20:46:40', '2025-09-04 20:46:40'),
(1005, 4, 'E', 6, 'Normal', '2025-09-04 20:46:40', '2025-09-04 20:46:40'),
(1006, 4, 'E', 7, 'Normal', '2025-09-04 20:46:40', '2025-09-04 20:46:40'),
(1007, 4, 'E', 8, 'Normal', '2025-09-04 20:46:40', '2025-09-04 20:46:40'),
(1008, 4, 'E', 9, 'Normal', '2025-09-04 20:46:40', '2025-09-04 20:46:40'),
(1009, 4, 'E', 10, 'Normal', '2025-09-04 20:46:40', '2025-09-04 20:46:40'),
(1384, 3, 'A', 1, 'Normal', '2025-10-18 09:04:56', '2025-10-18 09:04:56'),
(1385, 3, 'A', 2, 'Normal', '2025-10-18 09:04:56', '2025-10-18 09:04:56'),
(1386, 3, 'A', 3, 'Normal', '2025-10-18 09:04:56', '2025-10-18 09:04:56'),
(1387, 3, 'A', 4, 'Normal', '2025-10-18 09:04:56', '2025-10-18 09:04:56'),
(1388, 3, 'A', 5, 'Normal', '2025-10-18 09:04:56', '2025-10-18 09:04:56'),
(1389, 3, 'A', 6, 'Normal', '2025-10-18 09:04:56', '2025-10-18 09:04:56'),
(1390, 3, 'A', 7, 'Normal', '2025-10-18 09:04:56', '2025-10-18 09:04:56'),
(1391, 3, 'A', 8, 'Normal', '2025-10-18 09:04:56', '2025-10-18 09:04:56'),
(1392, 3, 'A', 9, 'Normal', '2025-10-18 09:04:56', '2025-10-18 09:04:56'),
(1393, 3, 'A', 10, 'Normal', '2025-10-18 09:04:56', '2025-10-18 09:04:56'),
(1394, 3, 'B', 1, 'Normal', '2025-10-18 09:04:56', '2025-10-18 09:04:56'),
(1395, 3, 'B', 2, 'Normal', '2025-10-18 09:04:56', '2025-10-18 09:04:56'),
(1396, 3, 'B', 3, 'Normal', '2025-10-18 09:04:56', '2025-10-18 09:04:56'),
(1397, 3, 'B', 4, 'Normal', '2025-10-18 09:04:56', '2025-10-18 09:04:56'),
(1398, 3, 'B', 5, 'Normal', '2025-10-18 09:04:56', '2025-10-18 09:04:56'),
(1399, 3, 'B', 6, 'Normal', '2025-10-18 09:04:56', '2025-10-18 09:04:56'),
(1400, 3, 'B', 7, 'Normal', '2025-10-18 09:04:56', '2025-10-18 09:04:56'),
(1401, 3, 'B', 8, 'Normal', '2025-10-18 09:04:56', '2025-10-18 09:04:56'),
(1402, 3, 'B', 9, 'Normal', '2025-10-18 09:04:56', '2025-10-18 09:04:56'),
(1403, 3, 'B', 10, 'Normal', '2025-10-18 09:04:56', '2025-10-18 09:04:56'),
(1404, 3, 'C', 1, 'Normal', '2025-10-18 09:04:56', '2025-10-18 09:04:56'),
(1405, 3, 'C', 2, 'Normal', '2025-10-18 09:04:56', '2025-10-18 09:04:56'),
(1406, 3, 'C', 3, 'Normal', '2025-10-18 09:04:56', '2025-10-18 09:04:56'),
(1407, 3, 'C', 4, 'VIP', '2025-10-18 09:04:56', '2025-10-18 09:04:56'),
(1408, 3, 'C', 5, 'VIP', '2025-10-18 09:04:56', '2025-10-18 09:04:56'),
(1409, 3, 'C', 6, 'VIP', '2025-10-18 09:04:56', '2025-10-18 09:04:56'),
(1410, 3, 'C', 7, 'VIP', '2025-10-18 09:04:56', '2025-10-18 09:04:56'),
(1411, 3, 'C', 8, 'Normal', '2025-10-18 09:04:56', '2025-10-18 09:04:56'),
(1412, 3, 'C', 9, 'Normal', '2025-10-18 09:04:56', '2025-10-18 09:04:56'),
(1413, 3, 'C', 10, 'Normal', '2025-10-18 09:04:56', '2025-10-18 09:04:56'),
(1414, 3, 'D', 1, 'Normal', '2025-10-18 09:04:56', '2025-10-18 09:04:56'),
(1415, 3, 'D', 2, 'Normal', '2025-10-18 09:04:56', '2025-10-18 09:04:56'),
(1416, 3, 'D', 3, 'Normal', '2025-10-18 09:04:56', '2025-10-18 09:04:56'),
(1417, 3, 'D', 4, 'Normal', '2025-10-18 09:04:56', '2025-10-18 09:04:56'),
(1418, 3, 'D', 5, 'Normal', '2025-10-18 09:04:56', '2025-10-18 09:04:56'),
(1419, 3, 'D', 6, 'Normal', '2025-10-18 09:04:56', '2025-10-18 09:04:56'),
(1420, 3, 'D', 7, 'Normal', '2025-10-18 09:04:56', '2025-10-18 09:04:56'),
(1421, 3, 'D', 8, 'Normal', '2025-10-18 09:04:56', '2025-10-18 09:04:56'),
(1422, 3, 'D', 9, 'Normal', '2025-10-18 09:04:56', '2025-10-18 09:04:56'),
(1423, 3, 'D', 10, 'Normal', '2025-10-18 09:04:56', '2025-10-18 09:04:56'),
(1424, 3, 'E', 1, 'Couple', '2025-10-18 09:04:56', '2025-10-18 09:04:56'),
(1425, 3, 'E', 2, 'Disabled', '2025-10-18 09:04:56', '2025-10-18 09:04:56'),
(1426, 3, 'E', 3, 'Normal', '2025-10-18 09:04:56', '2025-10-18 09:04:56'),
(1427, 3, 'E', 4, 'Normal', '2025-10-18 09:04:56', '2025-10-18 09:04:56'),
(1428, 3, 'E', 5, 'Normal', '2025-10-18 09:04:56', '2025-10-18 09:04:56'),
(1429, 3, 'E', 6, 'Normal', '2025-10-18 09:04:56', '2025-10-18 09:04:56'),
(1430, 3, 'E', 7, 'Normal', '2025-10-18 09:04:56', '2025-10-18 09:04:56'),
(1431, 3, 'E', 8, 'Normal', '2025-10-18 09:04:56', '2025-10-18 09:04:56'),
(1432, 3, 'E', 9, 'Disabled', '2025-10-18 09:04:56', '2025-10-18 09:04:56'),
(1433, 3, 'E', 10, 'Couple', '2025-10-18 09:04:56', '2025-10-18 09:04:56'),
(1434, 2, 'A', 1, 'Normal', '2025-10-18 09:05:04', '2025-10-18 09:05:04'),
(1435, 2, 'A', 2, 'Normal', '2025-10-18 09:05:04', '2025-10-18 09:05:04'),
(1436, 2, 'A', 3, 'Normal', '2025-10-18 09:05:04', '2025-10-18 09:05:04'),
(1437, 2, 'A', 4, 'Normal', '2025-10-18 09:05:04', '2025-10-18 09:05:04'),
(1438, 2, 'A', 5, 'Normal', '2025-10-18 09:05:04', '2025-10-18 09:05:04'),
(1439, 2, 'A', 6, 'Normal', '2025-10-18 09:05:04', '2025-10-18 09:05:04'),
(1440, 2, 'A', 7, 'Normal', '2025-10-18 09:05:04', '2025-10-18 09:05:04'),
(1441, 2, 'A', 8, 'Normal', '2025-10-18 09:05:04', '2025-10-18 09:05:04'),
(1442, 2, 'A', 9, 'Normal', '2025-10-18 09:05:04', '2025-10-18 09:05:04'),
(1443, 2, 'A', 10, 'Normal', '2025-10-18 09:05:04', '2025-10-18 09:05:04'),
(1444, 2, 'A', 11, 'Normal', '2025-10-18 09:05:04', '2025-10-18 09:05:04'),
(1445, 2, 'A', 12, 'Normal', '2025-10-18 09:05:04', '2025-10-18 09:05:04'),
(1446, 2, 'B', 1, 'Normal', '2025-10-18 09:05:04', '2025-10-18 09:05:04'),
(1447, 2, 'B', 2, 'Normal', '2025-10-18 09:05:04', '2025-10-18 09:05:04'),
(1448, 2, 'B', 3, 'Normal', '2025-10-18 09:05:04', '2025-10-18 09:05:04'),
(1449, 2, 'B', 4, 'Normal', '2025-10-18 09:05:04', '2025-10-18 09:05:04'),
(1450, 2, 'B', 5, 'Normal', '2025-10-18 09:05:04', '2025-10-18 09:05:04'),
(1451, 2, 'B', 6, 'Normal', '2025-10-18 09:05:04', '2025-10-18 09:05:04'),
(1452, 2, 'B', 7, 'Normal', '2025-10-18 09:05:04', '2025-10-18 09:05:04'),
(1453, 2, 'B', 8, 'Normal', '2025-10-18 09:05:04', '2025-10-18 09:05:04'),
(1454, 2, 'B', 9, 'Normal', '2025-10-18 09:05:04', '2025-10-18 09:05:04'),
(1455, 2, 'B', 10, 'Normal', '2025-10-18 09:05:04', '2025-10-18 09:05:04'),
(1456, 2, 'B', 11, 'Normal', '2025-10-18 09:05:04', '2025-10-18 09:05:04'),
(1457, 2, 'B', 12, 'Normal', '2025-10-18 09:05:04', '2025-10-18 09:05:04'),
(1458, 2, 'B', 13, 'Normal', '2025-10-18 09:05:04', '2025-10-18 09:05:04'),
(1459, 2, 'B', 14, 'Normal', '2025-10-18 09:05:04', '2025-10-18 09:05:04'),
(1460, 2, 'C', 1, 'Normal', '2025-10-18 09:05:04', '2025-10-18 09:05:04'),
(1461, 2, 'C', 2, 'Normal', '2025-10-18 09:05:04', '2025-10-18 09:05:04'),
(1462, 2, 'C', 3, 'Normal', '2025-10-18 09:05:04', '2025-10-18 09:05:04'),
(1463, 2, 'C', 4, 'Normal', '2025-10-18 09:05:04', '2025-10-18 09:05:04'),
(1464, 2, 'C', 5, 'Normal', '2025-10-18 09:05:04', '2025-10-18 09:05:04'),
(1465, 2, 'C', 6, 'Normal', '2025-10-18 09:05:04', '2025-10-18 09:05:04'),
(1466, 2, 'C', 7, 'Normal', '2025-10-18 09:05:04', '2025-10-18 09:05:04'),
(1467, 2, 'C', 8, 'Normal', '2025-10-18 09:05:04', '2025-10-18 09:05:04'),
(1468, 2, 'C', 9, 'Normal', '2025-10-18 09:05:04', '2025-10-18 09:05:04'),
(1469, 2, 'C', 10, 'Normal', '2025-10-18 09:05:04', '2025-10-18 09:05:04'),
(1470, 2, 'C', 11, 'Normal', '2025-10-18 09:05:04', '2025-10-18 09:05:04'),
(1471, 2, 'C', 12, 'Normal', '2025-10-18 09:05:04', '2025-10-18 09:05:04'),
(1472, 2, 'C', 13, 'Normal', '2025-10-18 09:05:04', '2025-10-18 09:05:04'),
(1473, 2, 'C', 14, 'Normal', '2025-10-18 09:05:04', '2025-10-18 09:05:04'),
(1474, 2, 'D', 1, 'Normal', '2025-10-18 09:05:04', '2025-10-18 09:05:04'),
(1475, 2, 'D', 2, 'Normal', '2025-10-18 09:05:04', '2025-10-18 09:05:04'),
(1476, 2, 'D', 3, 'Normal', '2025-10-18 09:05:04', '2025-10-18 09:05:04'),
(1477, 2, 'D', 4, 'Normal', '2025-10-18 09:05:04', '2025-10-18 09:05:04'),
(1478, 2, 'D', 5, 'VIP', '2025-10-18 09:05:04', '2025-10-18 09:05:04'),
(1479, 2, 'D', 6, 'VIP', '2025-10-18 09:05:04', '2025-10-18 09:05:04'),
(1480, 2, 'D', 7, 'VIP', '2025-10-18 09:05:04', '2025-10-18 09:05:04'),
(1481, 2, 'D', 8, 'VIP', '2025-10-18 09:05:04', '2025-10-18 09:05:04'),
(1482, 2, 'D', 9, 'VIP', '2025-10-18 09:05:04', '2025-10-18 09:05:04'),
(1483, 2, 'D', 10, 'VIP', '2025-10-18 09:05:04', '2025-10-18 09:05:04'),
(1484, 2, 'D', 11, 'Normal', '2025-10-18 09:05:04', '2025-10-18 09:05:04'),
(1485, 2, 'D', 12, 'Normal', '2025-10-18 09:05:04', '2025-10-18 09:05:04'),
(1486, 2, 'D', 13, 'Normal', '2025-10-18 09:05:04', '2025-10-18 09:05:04'),
(1487, 2, 'D', 14, 'Normal', '2025-10-18 09:05:04', '2025-10-18 09:05:04'),
(1488, 2, 'E', 1, 'Normal', '2025-10-18 09:05:04', '2025-10-18 09:05:04'),
(1489, 2, 'E', 2, 'Normal', '2025-10-18 09:05:04', '2025-10-18 09:05:04'),
(1490, 2, 'E', 3, 'Normal', '2025-10-18 09:05:04', '2025-10-18 09:05:04'),
(1491, 2, 'E', 4, 'Normal', '2025-10-18 09:05:04', '2025-10-18 09:05:04'),
(1492, 2, 'E', 5, 'Normal', '2025-10-18 09:05:04', '2025-10-18 09:05:04'),
(1493, 2, 'E', 6, 'Normal', '2025-10-18 09:05:04', '2025-10-18 09:05:04'),
(1494, 2, 'E', 7, 'Normal', '2025-10-18 09:05:04', '2025-10-18 09:05:04'),
(1495, 2, 'E', 8, 'Normal', '2025-10-18 09:05:04', '2025-10-18 09:05:04'),
(1496, 2, 'E', 9, 'Normal', '2025-10-18 09:05:04', '2025-10-18 09:05:04'),
(1497, 2, 'E', 10, 'Normal', '2025-10-18 09:05:04', '2025-10-18 09:05:04'),
(1498, 2, 'E', 11, 'Normal', '2025-10-18 09:05:04', '2025-10-18 09:05:04'),
(1499, 2, 'E', 12, 'Normal', '2025-10-18 09:05:04', '2025-10-18 09:05:04'),
(1500, 2, 'E', 13, 'Normal', '2025-10-18 09:05:04', '2025-10-18 09:05:04'),
(1501, 2, 'E', 14, 'Normal', '2025-10-18 09:05:04', '2025-10-18 09:05:04'),
(1502, 2, 'F', 1, 'Normal', '2025-10-18 09:05:04', '2025-10-18 09:05:04'),
(1503, 2, 'F', 2, 'Normal', '2025-10-18 09:05:04', '2025-10-18 09:05:04'),
(1504, 2, 'F', 3, 'Normal', '2025-10-18 09:05:04', '2025-10-18 09:05:04'),
(1505, 2, 'F', 4, 'Normal', '2025-10-18 09:05:04', '2025-10-18 09:05:04'),
(1506, 2, 'F', 5, 'Normal', '2025-10-18 09:05:04', '2025-10-18 09:05:04'),
(1507, 2, 'F', 6, 'Normal', '2025-10-18 09:05:04', '2025-10-18 09:05:04'),
(1508, 2, 'F', 7, 'Normal', '2025-10-18 09:05:04', '2025-10-18 09:05:04'),
(1509, 2, 'F', 8, 'Normal', '2025-10-18 09:05:04', '2025-10-18 09:05:04'),
(1510, 2, 'F', 9, 'Normal', '2025-10-18 09:05:04', '2025-10-18 09:05:04'),
(1511, 2, 'F', 10, 'Normal', '2025-10-18 09:05:04', '2025-10-18 09:05:04'),
(1512, 2, 'F', 11, 'Normal', '2025-10-18 09:05:04', '2025-10-18 09:05:04'),
(1513, 2, 'F', 12, 'Normal', '2025-10-18 09:05:04', '2025-10-18 09:05:04'),
(1514, 2, 'F', 13, 'Normal', '2025-10-18 09:05:04', '2025-10-18 09:05:04'),
(1515, 2, 'F', 14, 'Normal', '2025-10-18 09:05:04', '2025-10-18 09:05:04'),
(1516, 2, 'G', 1, 'Normal', '2025-10-18 09:05:04', '2025-10-18 09:05:04'),
(1517, 2, 'G', 2, 'Normal', '2025-10-18 09:05:04', '2025-10-18 09:05:04'),
(1518, 2, 'G', 3, 'Normal', '2025-10-18 09:05:04', '2025-10-18 09:05:04'),
(1519, 2, 'G', 4, 'Normal', '2025-10-18 09:05:04', '2025-10-18 09:05:04'),
(1520, 2, 'G', 5, 'Normal', '2025-10-18 09:05:04', '2025-10-18 09:05:04'),
(1521, 2, 'G', 6, 'Normal', '2025-10-18 09:05:04', '2025-10-18 09:05:04'),
(1522, 2, 'G', 7, 'Normal', '2025-10-18 09:05:04', '2025-10-18 09:05:04'),
(1523, 2, 'G', 8, 'Normal', '2025-10-18 09:05:04', '2025-10-18 09:05:04'),
(1524, 2, 'G', 9, 'Normal', '2025-10-18 09:05:04', '2025-10-18 09:05:04'),
(1525, 2, 'G', 10, 'Normal', '2025-10-18 09:05:04', '2025-10-18 09:05:04'),
(1526, 2, 'G', 11, 'Normal', '2025-10-18 09:05:04', '2025-10-18 09:05:04'),
(1527, 2, 'G', 12, 'Normal', '2025-10-18 09:05:04', '2025-10-18 09:05:04'),
(1528, 2, 'G', 13, 'Normal', '2025-10-18 09:05:04', '2025-10-18 09:05:04'),
(1529, 2, 'G', 14, 'Normal', '2025-10-18 09:05:04', '2025-10-18 09:05:04'),
(1530, 2, 'H', 1, 'Couple', '2025-10-18 09:05:04', '2025-10-18 09:05:04'),
(1531, 2, 'H', 2, 'Disabled', '2025-10-18 09:05:04', '2025-10-18 09:05:04'),
(1532, 2, 'H', 3, 'Normal', '2025-10-18 09:05:04', '2025-10-18 09:05:04'),
(1533, 2, 'H', 4, 'Normal', '2025-10-18 09:05:04', '2025-10-18 09:05:04'),
(1534, 2, 'H', 5, 'Normal', '2025-10-18 09:05:04', '2025-10-18 09:05:04'),
(1535, 2, 'H', 6, 'Normal', '2025-10-18 09:05:04', '2025-10-18 09:05:04'),
(1536, 2, 'H', 7, 'Normal', '2025-10-18 09:05:04', '2025-10-18 09:05:04'),
(1537, 2, 'H', 8, 'Normal', '2025-10-18 09:05:04', '2025-10-18 09:05:04'),
(1538, 2, 'H', 9, 'Normal', '2025-10-18 09:05:04', '2025-10-18 09:05:04'),
(1539, 2, 'H', 10, 'Normal', '2025-10-18 09:05:04', '2025-10-18 09:05:04'),
(1540, 2, 'H', 11, 'Disabled', '2025-10-18 09:05:04', '2025-10-18 09:05:04'),
(1541, 2, 'H', 12, 'Couple', '2025-10-18 09:05:04', '2025-10-18 09:05:04'),
(1652, 5, 'A', 1, 'Normal', '2025-10-21 07:57:16', '2025-10-21 07:57:16'),
(1653, 5, 'A', 2, 'Normal', '2025-10-21 07:57:16', '2025-10-21 07:57:16'),
(1654, 5, 'A', 3, 'Normal', '2025-10-21 07:57:16', '2025-10-21 07:57:16'),
(1655, 5, 'A', 4, 'Normal', '2025-10-21 07:57:16', '2025-10-21 07:57:16'),
(1656, 5, 'A', 5, 'Normal', '2025-10-21 07:57:16', '2025-10-21 07:57:16'),
(1657, 5, 'A', 6, 'Normal', '2025-10-21 07:57:16', '2025-10-21 07:57:16'),
(1658, 5, 'A', 7, 'Normal', '2025-10-21 07:57:16', '2025-10-21 07:57:16'),
(1659, 5, 'A', 8, 'Normal', '2025-10-21 07:57:16', '2025-10-21 07:57:16'),
(1660, 5, 'A', 9, 'Normal', '2025-10-21 07:57:16', '2025-10-21 07:57:16'),
(1661, 5, 'A', 10, 'Normal', '2025-10-21 07:57:16', '2025-10-21 07:57:16'),
(1662, 5, 'B', 1, 'Normal', '2025-10-21 07:57:16', '2025-10-21 07:57:16'),
(1663, 5, 'B', 2, 'Normal', '2025-10-21 07:57:16', '2025-10-21 07:57:16'),
(1664, 5, 'B', 3, 'Normal', '2025-10-21 07:57:16', '2025-10-21 07:57:16'),
(1665, 5, 'B', 4, 'Normal', '2025-10-21 07:57:16', '2025-10-21 07:57:16'),
(1666, 5, 'B', 5, 'Normal', '2025-10-21 07:57:16', '2025-10-21 07:57:16'),
(1667, 5, 'B', 6, 'Normal', '2025-10-21 07:57:16', '2025-10-21 07:57:16'),
(1668, 5, 'B', 7, 'Normal', '2025-10-21 07:57:16', '2025-10-21 07:57:16'),
(1669, 5, 'B', 8, 'Normal', '2025-10-21 07:57:16', '2025-10-21 07:57:16'),
(1670, 5, 'B', 9, 'Normal', '2025-10-21 07:57:16', '2025-10-21 07:57:16'),
(1671, 5, 'B', 10, 'Normal', '2025-10-21 07:57:16', '2025-10-21 07:57:16'),
(1672, 5, 'C', 1, 'VIP', '2025-10-21 07:57:16', '2025-10-21 07:57:16'),
(1673, 5, 'C', 2, 'VIP', '2025-10-21 07:57:16', '2025-10-21 07:57:16'),
(1674, 5, 'C', 3, 'VIP', '2025-10-21 07:57:16', '2025-10-21 07:57:16'),
(1675, 5, 'C', 4, 'VIP', '2025-10-21 07:57:16', '2025-10-21 07:57:16'),
(1676, 5, 'C', 5, 'VIP', '2025-10-21 07:57:16', '2025-10-21 07:57:16'),
(1677, 5, 'C', 6, 'VIP', '2025-10-21 07:57:16', '2025-10-21 07:57:16'),
(1678, 5, 'C', 7, 'VIP', '2025-10-21 07:57:16', '2025-10-21 07:57:16'),
(1679, 5, 'C', 8, 'VIP', '2025-10-21 07:57:16', '2025-10-21 07:57:16'),
(1680, 5, 'C', 9, 'VIP', '2025-10-21 07:57:16', '2025-10-21 07:57:16'),
(1681, 5, 'C', 10, 'VIP', '2025-10-21 07:57:16', '2025-10-21 07:57:16'),
(1682, 5, 'D', 1, 'Normal', '2025-10-21 07:57:16', '2025-10-21 07:57:16'),
(1683, 5, 'D', 2, 'Normal', '2025-10-21 07:57:16', '2025-10-21 07:57:16'),
(1684, 5, 'D', 3, 'Normal', '2025-10-21 07:57:16', '2025-10-21 07:57:16'),
(1685, 5, 'D', 4, 'Normal', '2025-10-21 07:57:16', '2025-10-21 07:57:16'),
(1686, 5, 'D', 5, 'Normal', '2025-10-21 07:57:16', '2025-10-21 07:57:16'),
(1687, 5, 'D', 6, 'Normal', '2025-10-21 07:57:16', '2025-10-21 07:57:16'),
(1688, 5, 'D', 7, 'Normal', '2025-10-21 07:57:16', '2025-10-21 07:57:16'),
(1689, 5, 'D', 8, 'Normal', '2025-10-21 07:57:16', '2025-10-21 07:57:16'),
(1690, 5, 'D', 9, 'Normal', '2025-10-21 07:57:16', '2025-10-21 07:57:16'),
(1691, 5, 'D', 10, 'Normal', '2025-10-21 07:57:16', '2025-10-21 07:57:16'),
(1692, 5, 'F', 1, 'Normal', '2025-10-21 07:57:16', '2025-10-21 07:57:16'),
(1693, 5, 'F', 2, 'Normal', '2025-10-21 07:57:16', '2025-10-21 07:57:16'),
(1694, 5, 'F', 3, 'Normal', '2025-10-21 07:57:16', '2025-10-21 07:57:16'),
(1695, 5, 'F', 4, 'Normal', '2025-10-21 07:57:16', '2025-10-21 07:57:16'),
(1696, 5, 'F', 5, 'Normal', '2025-10-21 07:57:16', '2025-10-21 07:57:16'),
(1697, 5, 'F', 6, 'Normal', '2025-10-21 07:57:16', '2025-10-21 07:57:16'),
(1698, 5, 'F', 7, 'Normal', '2025-10-21 07:57:16', '2025-10-21 07:57:16'),
(1699, 5, 'F', 8, 'Normal', '2025-10-21 07:57:16', '2025-10-21 07:57:16'),
(1700, 5, 'F', 9, 'Normal', '2025-10-21 07:57:16', '2025-10-21 07:57:16'),
(1701, 5, 'F', 10, 'Normal', '2025-10-21 07:57:16', '2025-10-21 07:57:16');

-- --------------------------------------------------------

--
-- Cấu trúc bảng cho bảng `showtimes`
--

CREATE TABLE `showtimes` (
  `showtime_id` bigint(20) UNSIGNED NOT NULL,
  `movie_id` bigint(20) UNSIGNED NOT NULL,
  `screen_id` bigint(20) UNSIGNED NOT NULL,
  `show_date` date NOT NULL,
  `show_time` time NOT NULL,
  `end_time` time NOT NULL,
  `available_seats` int(11) NOT NULL,
  `status` enum('Active','Cancelled','Full','over') NOT NULL DEFAULT 'Active',
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  `price_seat_normal` decimal(10,2) NOT NULL DEFAULT 0.00,
  `price_seat_vip` decimal(10,2) NOT NULL DEFAULT 0.00,
  `price_seat_couple` decimal(10,2) NOT NULL DEFAULT 0.00
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Đang đổ dữ liệu cho bảng `showtimes`
--

INSERT INTO `showtimes` (`showtime_id`, `movie_id`, `screen_id`, `show_date`, `show_time`, `end_time`, `available_seats`, `status`, `created_at`, `updated_at`, `price_seat_normal`, `price_seat_vip`, `price_seat_couple`) VALUES
(1, 16, 1, '2025-11-27', '14:30:00', '17:12:00', 39, 'Active', '2025-08-28 02:34:55', '2025-10-23 07:28:21', 100000.00, 120000.00, 130000.00),
(2, 16, 3, '2025-10-05', '14:45:00', '17:27:00', 50, 'Active', '2025-08-28 18:52:11', '2025-09-08 01:17:44', 100000.00, 120000.00, 130000.00),
(3, 16, 2, '2025-10-05', '09:00:00', '11:42:00', 108, 'Active', '2025-09-04 20:24:56', '2025-09-08 01:21:42', 100000.00, 120000.00, 130000.00),
(4, 16, 2, '2025-10-05', '10:00:00', '12:42:00', 108, 'Active', '2025-09-04 20:26:42', '2025-09-16 01:52:55', 100000.00, 120000.00, 130000.00),
(5, 16, 1, '2025-09-24', '01:49:00', '04:31:00', 108, 'Active', '2025-09-04 20:27:10', '2025-09-16 01:53:26', 100000.00, 120000.00, 130000.00),
(6, 16, 2, '2025-09-24', '06:00:00', '08:42:00', 108, 'Active', '2025-09-04 20:28:08', '2025-09-08 01:10:47', 0.00, 0.00, 0.00),
(7, 16, 1, '2025-09-24', '10:57:00', '13:39:00', 108, 'Active', '2025-09-10 20:58:02', '2025-09-10 20:58:02', 100000.00, 120000.00, 130000.00),
(8, 16, 2, '2025-09-24', '06:44:00', '09:45:00', 108, 'Active', '2025-09-11 01:17:00', '2025-09-16 01:52:07', 50000.00, 100000.00, 120000.00),
(9, 16, 4, '2025-09-24', '09:00:00', '11:42:00', 50, 'Active', '2025-09-11 01:18:12', '2025-09-11 01:18:12', 70000.00, 80000.00, 100000.00),
(10, 16, 5, '2025-10-23', '10:00:00', '12:42:00', 50, 'Active', '2025-10-21 08:01:27', '2025-10-21 08:01:27', 100000.00, 120000.00, 140000.00);

-- --------------------------------------------------------

--
-- Cấu trúc bảng cho bảng `users`
--

CREATE TABLE `users` (
  `user_id` bigint(20) UNSIGNED NOT NULL,
  `email` varchar(100) NOT NULL,
  `email_verified_at` timestamp NULL DEFAULT NULL,
  `password` varchar(255) DEFAULT NULL,
  `remember_token` varchar(100) DEFAULT NULL,
  `full_name` varchar(100) NOT NULL,
  `phone` varchar(15) DEFAULT NULL,
  `date_of_birth` date DEFAULT NULL,
  `gender` enum('Male','Female') DEFAULT NULL,
  `address` text DEFAULT NULL,
  `loyalty_points` int(11) NOT NULL DEFAULT 0,
  `user_type` enum('Customer','Admin','Staff') NOT NULL DEFAULT 'Customer',
  `is_active` tinyint(1) NOT NULL DEFAULT 1,
  `provider` varchar(255) DEFAULT NULL,
  `provider_id` varchar(255) DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Đang đổ dữ liệu cho bảng `users`
--

INSERT INTO `users` (`user_id`, `email`, `email_verified_at`, `password`, `remember_token`, `full_name`, `phone`, `date_of_birth`, `gender`, `address`, `loyalty_points`, `user_type`, `is_active`, `provider`, `provider_id`, `created_at`, `updated_at`) VALUES
(1, 'customer@example.com', NULL, '$2y$12$lZLQe5HOl4Vk3P3A8k67CeOAsXA5oyBmqGSvVxdJoaCJOU0eqSoWq', NULL, 'John Doe', '0123456789', '1990-01-01', 'Male', '123 Main Street, City, Country', 0, 'Customer', 0, NULL, NULL, '2025-08-06 21:14:41', '2025-08-06 21:14:41'),
(2, 'admin@example.com', NULL, '$2y$12$TM1udSXkz2MyOVgRhyR2nO90yKu0WPB07UMKmIxkPk2jGXFmQq8Ha', NULL, 'Admin User', '0987654321', '1985-05-10', 'Female', '456 Admin Ave, City, Country', 0, 'Admin', 1, NULL, NULL, '2025-08-06 21:14:42', '2025-08-06 21:14:42'),
(3, 'staff@example.com', NULL, '$2y$12$I4/Z8H5LFXYiuj27nNG7xu6aYsCwhw9y0wR.2eZi3omGQjh1T5HBG', NULL, 'Staff Member', '0222333444', '1995-12-25', 'Male', '789 Staff Road, City, Country', 0, 'Staff', 1, NULL, NULL, '2025-08-06 21:14:42', '2025-08-06 21:14:42'),
(4, 'namblue2909@gmail.com', NULL, NULL, 'T62ObO1NtFf5abtlRJTTn9oReyV6goc8TQ6TNgniJuiM2M85uQUbaGU4hQpK', 'Nam Hoài', NULL, NULL, NULL, NULL, 605, 'Customer', 1, 'google', '102578549864023559403', '2025-09-08 21:03:42', '2025-09-08 21:03:42'),
(5, 'buihoainam2.vn@gmail.com', NULL, NULL, NULL, 'Nam Bùi Hoài', NULL, NULL, NULL, NULL, 0, 'Customer', 1, 'google', '102359496558502341111', '2025-09-09 00:23:14', '2025-09-09 00:23:14'),
(6, 'buihoainam2002.vn@gmail.com', NULL, NULL, NULL, 'Nam Bùi Hoài', NULL, NULL, NULL, NULL, 0, 'Customer', 1, 'google', '116147379371202979584', '2025-10-11 21:39:14', '2025-10-11 21:39:14');

--
-- Chỉ mục cho các bảng đã đổ
--

--
-- Chỉ mục cho bảng `bookings`
--
ALTER TABLE `bookings`
  ADD PRIMARY KEY (`booking_id`),
  ADD UNIQUE KEY `bookings_booking_code_unique` (`booking_code`),
  ADD UNIQUE KEY `bookings_idempotency_key_unique` (`idempotency_key`),
  ADD KEY `bookings_user_id_foreign` (`user_id`),
  ADD KEY `bookings_showtime_id_foreign` (`showtime_id`);

--
-- Chỉ mục cho bảng `booking_food`
--
ALTER TABLE `booking_food`
  ADD PRIMARY KEY (`booking_food_id`),
  ADD KEY `booking_food_booking_id_item_id_index` (`booking_id`,`item_id`),
  ADD KEY `booking_food_item_id_foreign` (`item_id`);

--
-- Chỉ mục cho bảng `booking_seats`
--
ALTER TABLE `booking_seats`
  ADD PRIMARY KEY (`booking_seat_id`),
  ADD UNIQUE KEY `unique_booking_seat` (`booking_id`,`seat_id`),
  ADD KEY `booking_seats_seat_id_foreign` (`seat_id`);

--
-- Chỉ mục cho bảng `cinemas`
--
ALTER TABLE `cinemas`
  ADD PRIMARY KEY (`cinema_id`);

--
-- Chỉ mục cho bảng `failed_jobs`
--
ALTER TABLE `failed_jobs`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `failed_jobs_uuid_unique` (`uuid`);

--
-- Chỉ mục cho bảng `food_items`
--
ALTER TABLE `food_items`
  ADD PRIMARY KEY (`item_id`);

--
-- Chỉ mục cho bảng `migrations`
--
ALTER TABLE `migrations`
  ADD PRIMARY KEY (`id`);

--
-- Chỉ mục cho bảng `movies`
--
ALTER TABLE `movies`
  ADD PRIMARY KEY (`movie_id`);

--
-- Chỉ mục cho bảng `password_reset_tokens`
--
ALTER TABLE `password_reset_tokens`
  ADD PRIMARY KEY (`email`);

--
-- Chỉ mục cho bảng `personal_access_tokens`
--
ALTER TABLE `personal_access_tokens`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `personal_access_tokens_token_unique` (`token`),
  ADD KEY `personal_access_tokens_tokenable_type_tokenable_id_index` (`tokenable_type`,`tokenable_id`);

--
-- Chỉ mục cho bảng `promotions`
--
ALTER TABLE `promotions`
  ADD PRIMARY KEY (`promotion_id`),
  ADD UNIQUE KEY `promotions_code_unique` (`code`),
  ADD KEY `promotions_code_index` (`code`),
  ADD KEY `promotions_is_active_index` (`is_active`),
  ADD KEY `promotions_start_date_end_date_index` (`start_date`,`end_date`);

--
-- Chỉ mục cho bảng `reviews`
--
ALTER TABLE `reviews`
  ADD PRIMARY KEY (`review_id`),
  ADD UNIQUE KEY `unique_user_movie_review` (`user_id`,`movie_id`),
  ADD KEY `reviews_movie_id_foreign` (`movie_id`);

--
-- Chỉ mục cho bảng `screens`
--
ALTER TABLE `screens`
  ADD PRIMARY KEY (`screen_id`),
  ADD KEY `screens_cinema_id_foreign` (`cinema_id`);

--
-- Chỉ mục cho bảng `seats`
--
ALTER TABLE `seats`
  ADD PRIMARY KEY (`seat_id`),
  ADD UNIQUE KEY `unique_seat` (`screen_id`,`row_name`,`seat_number`);

--
-- Chỉ mục cho bảng `showtimes`
--
ALTER TABLE `showtimes`
  ADD PRIMARY KEY (`showtime_id`),
  ADD KEY `showtimes_movie_id_foreign` (`movie_id`),
  ADD KEY `showtimes_screen_id_foreign` (`screen_id`);

--
-- Chỉ mục cho bảng `users`
--
ALTER TABLE `users`
  ADD PRIMARY KEY (`user_id`),
  ADD UNIQUE KEY `users_email_unique` (`email`),
  ADD KEY `users_provider_id_index` (`provider_id`);

--
-- AUTO_INCREMENT cho các bảng đã đổ
--

--
-- AUTO_INCREMENT cho bảng `bookings`
--
ALTER TABLE `bookings`
  MODIFY `booking_id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=66;

--
-- AUTO_INCREMENT cho bảng `booking_food`
--
ALTER TABLE `booking_food`
  MODIFY `booking_food_id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=91;

--
-- AUTO_INCREMENT cho bảng `booking_seats`
--
ALTER TABLE `booking_seats`
  MODIFY `booking_seat_id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=99;

--
-- AUTO_INCREMENT cho bảng `cinemas`
--
ALTER TABLE `cinemas`
  MODIFY `cinema_id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=66;

--
-- AUTO_INCREMENT cho bảng `failed_jobs`
--
ALTER TABLE `failed_jobs`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT cho bảng `food_items`
--
ALTER TABLE `food_items`
  MODIFY `item_id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=14;

--
-- AUTO_INCREMENT cho bảng `migrations`
--
ALTER TABLE `migrations`
  MODIFY `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=28;

--
-- AUTO_INCREMENT cho bảng `movies`
--
ALTER TABLE `movies`
  MODIFY `movie_id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=27;

--
-- AUTO_INCREMENT cho bảng `personal_access_tokens`
--
ALTER TABLE `personal_access_tokens`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT cho bảng `promotions`
--
ALTER TABLE `promotions`
  MODIFY `promotion_id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=13;

--
-- AUTO_INCREMENT cho bảng `reviews`
--
ALTER TABLE `reviews`
  MODIFY `review_id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT cho bảng `screens`
--
ALTER TABLE `screens`
  MODIFY `screen_id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=6;

--
-- AUTO_INCREMENT cho bảng `seats`
--
ALTER TABLE `seats`
  MODIFY `seat_id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=1702;

--
-- AUTO_INCREMENT cho bảng `showtimes`
--
ALTER TABLE `showtimes`
  MODIFY `showtime_id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=11;

--
-- AUTO_INCREMENT cho bảng `users`
--
ALTER TABLE `users`
  MODIFY `user_id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=7;

--
-- Các ràng buộc cho các bảng đã đổ
--

--
-- Các ràng buộc cho bảng `bookings`
--
ALTER TABLE `bookings`
  ADD CONSTRAINT `bookings_showtime_id_foreign` FOREIGN KEY (`showtime_id`) REFERENCES `showtimes` (`showtime_id`) ON UPDATE CASCADE,
  ADD CONSTRAINT `bookings_user_id_foreign` FOREIGN KEY (`user_id`) REFERENCES `users` (`user_id`) ON UPDATE CASCADE;

--
-- Các ràng buộc cho bảng `booking_food`
--
ALTER TABLE `booking_food`
  ADD CONSTRAINT `booking_food_booking_id_foreign` FOREIGN KEY (`booking_id`) REFERENCES `bookings` (`booking_id`) ON DELETE CASCADE ON UPDATE CASCADE,
  ADD CONSTRAINT `booking_food_item_id_foreign` FOREIGN KEY (`item_id`) REFERENCES `food_items` (`item_id`) ON UPDATE CASCADE;

--
-- Các ràng buộc cho bảng `booking_seats`
--
ALTER TABLE `booking_seats`
  ADD CONSTRAINT `booking_seats_booking_id_foreign` FOREIGN KEY (`booking_id`) REFERENCES `bookings` (`booking_id`) ON DELETE CASCADE ON UPDATE CASCADE,
  ADD CONSTRAINT `booking_seats_seat_id_foreign` FOREIGN KEY (`seat_id`) REFERENCES `seats` (`seat_id`) ON UPDATE CASCADE;

--
-- Các ràng buộc cho bảng `reviews`
--
ALTER TABLE `reviews`
  ADD CONSTRAINT `reviews_movie_id_foreign` FOREIGN KEY (`movie_id`) REFERENCES `movies` (`movie_id`) ON DELETE CASCADE,
  ADD CONSTRAINT `reviews_user_id_foreign` FOREIGN KEY (`user_id`) REFERENCES `users` (`user_id`) ON DELETE CASCADE;

--
-- Các ràng buộc cho bảng `screens`
--
ALTER TABLE `screens`
  ADD CONSTRAINT `screens_cinema_id_foreign` FOREIGN KEY (`cinema_id`) REFERENCES `cinemas` (`cinema_id`) ON DELETE CASCADE;

--
-- Các ràng buộc cho bảng `seats`
--
ALTER TABLE `seats`
  ADD CONSTRAINT `seats_screen_id_foreign` FOREIGN KEY (`screen_id`) REFERENCES `screens` (`screen_id`) ON DELETE CASCADE;

--
-- Các ràng buộc cho bảng `showtimes`
--
ALTER TABLE `showtimes`
  ADD CONSTRAINT `showtimes_movie_id_foreign` FOREIGN KEY (`movie_id`) REFERENCES `movies` (`movie_id`) ON DELETE CASCADE,
  ADD CONSTRAINT `showtimes_screen_id_foreign` FOREIGN KEY (`screen_id`) REFERENCES `screens` (`screen_id`) ON DELETE CASCADE;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
