-- phpMyAdmin SQL Dump
-- version 5.2.3
-- https://www.phpmyadmin.net/
--
-- Host: localhost:3306
-- Generation Time: Aug 01, 2026 at 12:46 AM
-- Server version: 8.0.30
-- PHP Version: 8.1.10

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Database: `movie_booking`
--

-- --------------------------------------------------------

--
-- Table structure for table `bookings`
--

CREATE TABLE `bookings` (
  `id` int NOT NULL,
  `booking_code` varchar(30) DEFAULT NULL,
  `user_id` int NOT NULL,
  `showtime_id` int NOT NULL,
  `payment_id` int DEFAULT NULL,
  `total_amount` decimal(10,2) DEFAULT NULL,
  `status` enum('pending','paid','cancelled') DEFAULT 'pending',
  `created_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

--
-- Dumping data for table `bookings`
--

INSERT INTO `bookings` (`id`, `booking_code`, `user_id`, `showtime_id`, `payment_id`, `total_amount`, `status`, `created_at`) VALUES
(1, 'PET202607230001', 2, 1, 1, 140000.00, 'paid', '2026-07-23 01:25:00'),
(2, 'PET202607230002', 3, 2, 2, 195000.00, 'paid', '2026-07-23 02:40:00'),
(3, 'PET202607230003', 4, 5, 3, 260000.00, 'paid', '2026-07-23 10:15:00'),
(4, 'PET202607230004', 2, 3, NULL, 90000.00, 'pending', '2026-07-23 11:00:00'),
(5, 'PET202607230005', 5, 6, 5, 320000.00, 'cancelled', '2026-07-23 13:00:00');

-- --------------------------------------------------------

--
-- Table structure for table `foods`
--

CREATE TABLE `foods` (
  `id` int NOT NULL,
  `name` varchar(255) NOT NULL,
  `description` text,
  `image` varchar(255) DEFAULT NULL,
  `status` enum('active','inactive') DEFAULT 'active'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

--
-- Dumping data for table `foods`
--

INSERT INTO `foods` (`id`, `name`, `description`, `image`, `status`) VALUES
(1, 'Bắp rang bơ', 'Bắp rang vị bơ truyền thống', 'popcorn.jpg', 'active'),
(2, 'Bắp rang phô mai', 'Bắp rang phủ phô mai', 'popcorn_cheese.jpg', 'active'),
(3, 'Pepsi', 'Nước ngọt Pepsi', 'pepsi.jpg', 'active'),
(4, 'Coca-Cola', 'Nước ngọt Coca-Cola', 'coca.jpg', 'active'),
(5, '7UP', 'Nước ngọt 7UP', '7up.jpg', 'active'),
(6, 'Aquafina', 'Nước suối Aquafina', 'aquafina.jpg', 'active'),
(7, 'Combo Solo', '1 bắp + 1 nước', 'combo_solo.jpg', 'active'),
(8, 'Combo Couple', '1 bắp lớn + 2 nước', 'combo_couple.jpg', 'active');

-- --------------------------------------------------------

--
-- Table structure for table `food_orders`
--

CREATE TABLE `food_orders` (
  `id` int NOT NULL,
  `booking_id` int NOT NULL,
  `food_variant_id` int NOT NULL,
  `quantity` int NOT NULL,
  `price_at_booking` decimal(10,2) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

--
-- Dumping data for table `food_orders`
--

INSERT INTO `food_orders` (`id`, `booking_id`, `food_variant_id`, `quantity`, `price_at_booking`) VALUES
(1, 1, 14, 1, 89000.00),
(2, 2, 2, 1, 60000.00),
(3, 2, 7, 2, 30000.00),
(4, 3, 15, 1, 159000.00),
(5, 5, 1, 2, 45000.00);

-- --------------------------------------------------------

--
-- Table structure for table `food_variants`
--

CREATE TABLE `food_variants` (
  `id` int NOT NULL,
  `food_id` int NOT NULL,
  `size` varchar(10) DEFAULT NULL,
  `price` decimal(10,2) DEFAULT NULL,
  `stock` int DEFAULT '0'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

--
-- Dumping data for table `food_variants`
--

INSERT INTO `food_variants` (`id`, `food_id`, `size`, `price`, `stock`) VALUES
(1, 1, 'S', 45000.00, 100),
(2, 1, 'M', 60000.00, 100),
(4, 2, 'S', 50000.00, 100),
(5, 2, 'M', 65000.00, 100),
(6, 2, 'L', 80000.00, 100),
(7, 3, 'M', 30000.00, 200),
(8, 3, 'L', 35000.00, 200),
(9, 4, 'M', 30000.00, 200),
(10, 4, 'L', 35000.00, 200),
(11, 5, 'M', 30000.00, 200),
(12, 5, 'L', 35000.00, 200),
(13, 6, '500ml', 20000.00, 200),
(14, 7, 'Combo', 89000.00, 100),
(15, 8, 'Combo', 159000.00, 100),
(16, 1, 'L', 70000.00, 200);

-- --------------------------------------------------------

--
-- Table structure for table `movies`
--

CREATE TABLE `movies` (
  `id` int NOT NULL,
  `title` varchar(255) NOT NULL,
  `genres` varchar(255) DEFAULT NULL,
  `duration` int NOT NULL,
  `description` text,
  `trailer` varchar(255) DEFAULT NULL,
  `poster` varchar(255) DEFAULT NULL,
  `release_date` date DEFAULT NULL,
  `language` varchar(100) DEFAULT NULL,
  `director` varchar(255) DEFAULT NULL,
  `actors` text,
  `age_rating` varchar(20) DEFAULT NULL,
  `status` enum('coming_soon','now_showing','ended') DEFAULT 'coming_soon'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

--
-- Dumping data for table `movies`
--

INSERT INTO `movies` (`id`, `title`, `genres`, `duration`, `description`, `trailer`, `poster`, `release_date`, `language`, `director`, `actors`, `age_rating`, `status`) VALUES
(1, 'Interstellar', 'Khoa học viễn tưởng, Phiêu lưu, Chính kịch', 169, 'Một nhóm phi hành gia thực hiện chuyến du hành xuyên không gian để tìm kiếm hành tinh mới cho loài người.', 'https://www.youtube.com/watch?v=zSWdZVtXT7E', 'movie/1784903834-movie_details_img.jpg', '2026-07-16', 'Tiếng Anh', 'Christopher Nolan', 'Matthew McConaughey, Anne Hathaway, Jessica Chastain, Michael Caine', 'P', 'now_showing'),
(2, 'Doraemon: Nobita và Cuộc Phiêu Lưu Mới', 'Hoạt hình, Phiêu lưu', 105, 'Bộ phim hoạt hình Doraemon mới.', 'https://youtube.com/watch?v=demo1', 'movie/1784903846-s_ucm_poster01.jpg', '2026-07-01', 'Tiếng Nhật', 'Yukiyo Teramoto', 'Wasabi Mizuta', 'P', 'now_showing'),
(3, 'Superman', 'Hành động, Viễn tưởng', 129, 'Siêu anh hùng Superman trở lại.', 'https://youtube.com/watch?v=demo2', 'movie/1784903856-ucm_poster01.jpg', '2026-07-15', 'Tiếng Anh', 'James Gunn', 'David Corenswet', 'T18', 'now_showing'),
(4, 'Avatar 3', 'Khoa học viễn tưởng', 190, 'Cuộc chiến mới trên Pandora.', 'https://youtube.com/watch?v=demo3', 'movie/1784903863-ucm_poster03.jpg', '2026-12-18', 'Tiếng Anh', 'James Cameron', 'Sam Worthington2', 'T13', 'coming_soon'),
(7, 'adslfadf', 'Khoa học viễn tưởng, Phiêu lưu, Chính kịch', 200, 'lsadkf', 'https://www.youtube.com/watch?v=ZxdDsQ6Tj6w', 'movie/1784941895-movie_details_img.jpg', '2026-07-26', 'Tiếng Anh', 'Christopher Nolan', 'Matthew McConaughey, Anne Hathaway, Jessica Chastain, Michael Caine', 'P', 'coming_soon');

-- --------------------------------------------------------

--
-- Table structure for table `payments`
--

CREATE TABLE `payments` (
  `id` int NOT NULL,
  `payment_method` varchar(100) DEFAULT NULL,
  `transaction_code` varchar(255) DEFAULT NULL,
  `amount` decimal(10,2) DEFAULT NULL,
  `status` enum('pending','completed','failed') DEFAULT 'pending',
  `payment_time` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

--
-- Dumping data for table `payments`
--

INSERT INTO `payments` (`id`, `payment_method`, `transaction_code`, `amount`, `status`, `payment_time`) VALUES
(1, 'vnpay', 'VNP00192837', 140000.00, 'completed', '2026-07-23 01:30:00'),
(2, 'momo', 'MOMO88273621', 195000.00, 'completed', '2026-07-23 02:45:00'),
(3, 'visa', 'VISA55667788', 260000.00, 'completed', '2026-07-23 10:20:00'),
(4, 'vnpay', 'VNP00998877', 90000.00, 'pending', NULL),
(5, 'momo', 'MOMO12345678', 320000.00, 'failed', '2026-07-23 13:10:00');

-- --------------------------------------------------------

--
-- Table structure for table `rooms`
--

CREATE TABLE `rooms` (
  `id` int NOT NULL,
  `room_type_id` int NOT NULL,
  `name` varchar(100) NOT NULL,
  `total_seats` int NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

--
-- Dumping data for table `rooms`
--

INSERT INTO `rooms` (`id`, `room_type_id`, `name`, `total_seats`) VALUES
(1, 1, 'Phòng 01', 120),
(2, 1, 'Phòng 02', 120),
(3, 2, 'Phòng 03', 100),
(4, 2, 'Phòng 04', 100),
(5, 3, 'Phòng IMAX', 180),
(6, 4, 'Gold Class', 60);

-- --------------------------------------------------------

--
-- Table structure for table `room_types`
--

CREATE TABLE `room_types` (
  `id` int NOT NULL,
  `name` varchar(50) NOT NULL,
  `price_modifier` decimal(10,2) DEFAULT '0.00',
  `description` text
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

--
-- Dumping data for table `room_types`
--

INSERT INTO `room_types` (`id`, `name`, `price_modifier`, `description`) VALUES
(1, '2D', 0.00, 'Phòng chiếu tiêu chuẩn 2D'),
(2, '3D', 30000.00, 'Phòng chiếu công nghệ 3D'),
(3, 'IMAX', 70000.00, 'Phòng chiếu IMAX màn hình lớn'),
(4, 'Gold Class', 120000.00, 'Phòng chiếu cao cấp với ghế VIP');

-- --------------------------------------------------------

--
-- Table structure for table `seats`
--

CREATE TABLE `seats` (
  `id` int NOT NULL,
  `room_id` int NOT NULL,
  `seat_type_id` int NOT NULL,
  `seat_number` varchar(10) NOT NULL,
  `row_char` varchar(2) DEFAULT NULL,
  `col_num` int DEFAULT NULL,
  `status` enum('available','maintenance') DEFAULT 'available'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

-- --------------------------------------------------------

--
-- Table structure for table `seat_types`
--

CREATE TABLE `seat_types` (
  `id` int NOT NULL,
  `name` varchar(50) NOT NULL,
  `surcharge` decimal(10,2) DEFAULT '0.00',
  `description` text
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

--
-- Dumping data for table `seat_types`
--

INSERT INTO `seat_types` (`id`, `name`, `surcharge`, `description`) VALUES
(1, 'Standard', 0.00, 'Ghế thường'),
(2, 'VIP', 25000.00, 'Ghế VIP rộng rãi'),
(3, 'Couple', 80000.00, 'Ghế đôi dành cho 2 người');

-- --------------------------------------------------------

--
-- Table structure for table `showtimes`
--

CREATE TABLE `showtimes` (
  `id` int NOT NULL,
  `movie_id` int NOT NULL,
  `room_id` int NOT NULL,
  `start_time` datetime NOT NULL,
  `end_time` datetime NOT NULL,
  `base_price` decimal(10,2) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

--
-- Dumping data for table `showtimes`
--

INSERT INTO `showtimes` (`id`, `movie_id`, `room_id`, `start_time`, `end_time`, `base_price`) VALUES
(1, 1, 1, '2026-07-31 09:00:00', '2026-07-31 11:49:00', 700000.00),
(2, 2, 2, '2026-07-31 10:00:00', '2026-07-31 12:05:00', 80000.00),
(3, 4, 1, '2026-07-31 13:00:00', '2026-07-31 16:30:00', 70000.00),
(5, 3, 5, '2026-07-23 18:00:00', '2026-07-23 21:10:00', 180000.00),
(6, 4, 6, '2026-07-23 19:00:00', '2026-07-23 20:55:00', 220000.00),
(7, 1, 1, '2026-07-24 09:00:00', '2026-07-24 10:45:00', 70000.00),
(8, 2, 2, '2026-07-24 10:00:00', '2026-07-24 12:10:00', 90000.00),
(9, 3, 5, '2026-07-24 18:00:00', '2026-07-24 21:10:00', 180000.00),
(10, 4, 6, '2026-07-24 19:00:00', '2026-07-24 20:55:00', 220000.00),
(11, 4, 1, '2026-07-26 01:00:00', '2026-07-31 01:00:00', 120000.00),
(13, 4, 6, '2026-07-25 08:30:00', '2026-07-25 11:40:00', 200000.00),
(14, 4, 6, '2026-07-31 02:15:00', '2026-07-31 05:45:00', 80000.00),
(16, 4, 1, '2026-08-01 02:29:00', '2026-08-01 05:59:00', 80000.00),
(17, 4, 1, '2026-08-04 14:00:00', '2026-08-04 17:30:00', 20000.00);

-- --------------------------------------------------------

--
-- Table structure for table `tickets`
--

CREATE TABLE `tickets` (
  `id` int NOT NULL,
  `booking_id` int NOT NULL,
  `seat_id` int NOT NULL,
  `ticket_code` varchar(100) DEFAULT NULL,
  `price` decimal(10,2) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

-- --------------------------------------------------------

--
-- Table structure for table `users`
--

CREATE TABLE `users` (
  `id` int NOT NULL,
  `fullname` varchar(255) NOT NULL,
  `email` varchar(255) NOT NULL,
  `password` varchar(255) NOT NULL,
  `role` enum('user','staff','admin') DEFAULT 'user',
  `status` enum('active','inactive','banned') DEFAULT 'active',
  `created_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

--
-- Dumping data for table `users`
--

INSERT INTO `users` (`id`, `fullname`, `email`, `password`, `role`, `status`, `created_at`) VALUES
(1, 'System Administrator', 'admin@petacinema.com', '123456', 'admin', 'active', '2026-07-22 15:37:57'),
(2, 'Nguyễn Văn Minh', 'staff1@petacinema.com', '123456', 'staff', 'active', '2026-07-22 15:37:57'),
(3, 'Trần Thu Trang', 'staff2@petacinema.com', '123456', 'staff', 'active', '2026-07-22 15:37:57'),
(4, 'Lê Văn An', 'an@gmail.com', '123456', 'user', 'active', '2026-07-22 15:37:57'),
(5, 'Phạm Thị Bình', 'binh@gmail.com', '123456', 'user', 'active', '2026-07-22 15:37:57'),
(6, 'Hoàng Minh Đức', 'duc@gmail.com', '123456', 'user', 'active', '2026-07-22 15:37:57');

--
-- Indexes for dumped tables
--

--
-- Indexes for table `bookings`
--
ALTER TABLE `bookings`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `booking_code` (`booking_code`),
  ADD UNIQUE KEY `payment_id` (`payment_id`),
  ADD KEY `fk_booking_user` (`user_id`),
  ADD KEY `fk_booking_showtime` (`showtime_id`);

--
-- Indexes for table `foods`
--
ALTER TABLE `foods`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `food_orders`
--
ALTER TABLE `food_orders`
  ADD PRIMARY KEY (`id`),
  ADD KEY `fk_foodorder_booking` (`booking_id`),
  ADD KEY `fk_foodorder_variant` (`food_variant_id`);

--
-- Indexes for table `food_variants`
--
ALTER TABLE `food_variants`
  ADD PRIMARY KEY (`id`),
  ADD KEY `fk_variant_food` (`food_id`);

--
-- Indexes for table `movies`
--
ALTER TABLE `movies`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `payments`
--
ALTER TABLE `payments`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `rooms`
--
ALTER TABLE `rooms`
  ADD PRIMARY KEY (`id`),
  ADD KEY `fk_room_type` (`room_type_id`);

--
-- Indexes for table `room_types`
--
ALTER TABLE `room_types`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `name` (`name`);

--
-- Indexes for table `seats`
--
ALTER TABLE `seats`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `room_id` (`room_id`,`seat_number`),
  ADD KEY `fk_seat_type` (`seat_type_id`);

--
-- Indexes for table `seat_types`
--
ALTER TABLE `seat_types`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `name` (`name`);

--
-- Indexes for table `showtimes`
--
ALTER TABLE `showtimes`
  ADD PRIMARY KEY (`id`),
  ADD KEY `idx_room_start` (`room_id`,`start_time`),
  ADD KEY `fk_show_movie` (`movie_id`);

--
-- Indexes for table `tickets`
--
ALTER TABLE `tickets`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `ticket_code` (`ticket_code`),
  ADD KEY `fk_ticket_booking` (`booking_id`),
  ADD KEY `fk_ticket_seat` (`seat_id`);

--
-- Indexes for table `users`
--
ALTER TABLE `users`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `email` (`email`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `bookings`
--
ALTER TABLE `bookings`
  MODIFY `id` int NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=6;

--
-- AUTO_INCREMENT for table `foods`
--
ALTER TABLE `foods`
  MODIFY `id` int NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=11;

--
-- AUTO_INCREMENT for table `food_orders`
--
ALTER TABLE `food_orders`
  MODIFY `id` int NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=6;

--
-- AUTO_INCREMENT for table `food_variants`
--
ALTER TABLE `food_variants`
  MODIFY `id` int NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=19;

--
-- AUTO_INCREMENT for table `movies`
--
ALTER TABLE `movies`
  MODIFY `id` int NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=8;

--
-- AUTO_INCREMENT for table `payments`
--
ALTER TABLE `payments`
  MODIFY `id` int NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=6;

--
-- AUTO_INCREMENT for table `rooms`
--
ALTER TABLE `rooms`
  MODIFY `id` int NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=7;

--
-- AUTO_INCREMENT for table `room_types`
--
ALTER TABLE `room_types`
  MODIFY `id` int NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=5;

--
-- AUTO_INCREMENT for table `seats`
--
ALTER TABLE `seats`
  MODIFY `id` int NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=12;

--
-- AUTO_INCREMENT for table `seat_types`
--
ALTER TABLE `seat_types`
  MODIFY `id` int NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=4;

--
-- AUTO_INCREMENT for table `showtimes`
--
ALTER TABLE `showtimes`
  MODIFY `id` int NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=18;

--
-- AUTO_INCREMENT for table `tickets`
--
ALTER TABLE `tickets`
  MODIFY `id` int NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=60;

--
-- AUTO_INCREMENT for table `users`
--
ALTER TABLE `users`
  MODIFY `id` int NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=9;

--
-- Constraints for dumped tables
--

--
-- Constraints for table `bookings`
--
ALTER TABLE `bookings`
  ADD CONSTRAINT `fk_booking_payment` FOREIGN KEY (`payment_id`) REFERENCES `payments` (`id`),
  ADD CONSTRAINT `fk_booking_showtime` FOREIGN KEY (`showtime_id`) REFERENCES `showtimes` (`id`),
  ADD CONSTRAINT `fk_booking_user` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`);

--
-- Constraints for table `food_orders`
--
ALTER TABLE `food_orders`
  ADD CONSTRAINT `fk_foodorder_booking` FOREIGN KEY (`booking_id`) REFERENCES `bookings` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `fk_foodorder_variant` FOREIGN KEY (`food_variant_id`) REFERENCES `food_variants` (`id`);

--
-- Constraints for table `food_variants`
--
ALTER TABLE `food_variants`
  ADD CONSTRAINT `fk_variant_food` FOREIGN KEY (`food_id`) REFERENCES `foods` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `rooms`
--
ALTER TABLE `rooms`
  ADD CONSTRAINT `fk_room_type` FOREIGN KEY (`room_type_id`) REFERENCES `room_types` (`id`);

--
-- Constraints for table `seats`
--
ALTER TABLE `seats`
  ADD CONSTRAINT `fk_seat_room` FOREIGN KEY (`room_id`) REFERENCES `rooms` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `fk_seat_type` FOREIGN KEY (`seat_type_id`) REFERENCES `seat_types` (`id`);

--
-- Constraints for table `showtimes`
--
ALTER TABLE `showtimes`
  ADD CONSTRAINT `fk_show_movie` FOREIGN KEY (`movie_id`) REFERENCES `movies` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `fk_show_room` FOREIGN KEY (`room_id`) REFERENCES `rooms` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `tickets`
--
ALTER TABLE `tickets`
  ADD CONSTRAINT `fk_ticket_booking` FOREIGN KEY (`booking_id`) REFERENCES `bookings` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `fk_ticket_seat` FOREIGN KEY (`seat_id`) REFERENCES `seats` (`id`);
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
