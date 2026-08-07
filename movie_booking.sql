-- phpMyAdmin SQL Dump
-- version 5.2.3
-- https://www.phpmyadmin.net/
--
-- Host: localhost:3306
-- Generation Time: Aug 07, 2026 at 08:40 PM
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
  `created_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP,
  `checkin_status` varchar(20) NOT NULL DEFAULT 'pending',
  `checked_in_at` datetime DEFAULT NULL,
  `checked_in_by` int DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

--
-- Dumping data for table `bookings`
--

INSERT INTO `bookings` (`id`, `booking_code`, `user_id`, `showtime_id`, `payment_id`, `total_amount`, `status`, `created_at`, `checkin_status`, `checked_in_at`, `checked_in_by`) VALUES
(13, 'PET202608040001', 5, 22, NULL, 136000.00, 'paid', '2026-08-04 01:13:57', 'pending', NULL, NULL),
(14, 'PET202608060001', 2, 22, NULL, 189000.00, 'paid', '2026-08-06 15:51:35', 'pending', NULL, NULL),
(15, 'PET202608060002', 1, 22, NULL, 126000.00, 'paid', '2026-08-06 16:02:50', 'pending', NULL, NULL),
(16, 'PET202608060003', 3, 25, NULL, 280000.00, 'paid', '2026-08-06 16:32:02', 'pending', NULL, NULL),
(17, 'PET202608080017', 9, 28, 6, 57000.00, 'paid', '2026-08-07 20:03:55', 'pending', NULL, NULL),
(18, 'PET202608080018', 9, 38, 7, 414000.00, 'paid', '2026-08-07 20:17:40', 'pending', NULL, NULL),
(19, 'PET202608080019', 9, 28, 8, 298000.00, 'cancelled', '2026-08-07 20:20:42', 'pending', NULL, NULL);

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
(11, 'Bắp rang bơ', 'Bắp rang vị bơ truyền thống', 'foods/1785754064-bap_rang_bo.png', 'active'),
(12, 'Bắp rang phô mai', 'Bắp rang phủ phô mai', 'foods/1785754143-bap_rang_pho_mai.png', 'active'),
(13, 'Pepsi', 'Nước ngọt Pepsi', 'foods/1785754153-pesi.png', 'active'),
(14, 'Coca-Cola', 'Nước ngọt Coca-Cola', 'foods/1785754174-Coca-Cola.png', 'active'),
(15, '7UP', 'Nước ngọt 7UP', 'foods/1785754183-7UP.png', 'active'),
(16, 'Aquafina', 'Nước suối Aquafina', 'foods/1785754194-Aquafina.png', 'active'),
(17, 'Combo Solo', '1 bắp + 1 nước coca', 'foods/1785754220-Combo_Solo69k.png', 'active'),
(18, 'Combo Couple', '1 bắp lớn + 2 nước', 'foods/1785754246-Combo_Couple95k.png', 'active');

-- --------------------------------------------------------

--
-- Table structure for table `food_orders`
--

CREATE TABLE `food_orders` (
  `id` int NOT NULL,
  `booking_id` int NOT NULL,
  `food_variant_id` int NOT NULL,
  `quantity` int NOT NULL,
  `price_at_booking` decimal(10,2) DEFAULT NULL,
  `delivered_at` datetime DEFAULT NULL,
  `delivered_by` int DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

--
-- Dumping data for table `food_orders`
--

INSERT INTO `food_orders` (`id`, `booking_id`, `food_variant_id`, `quantity`, `price_at_booking`, `delivered_at`, `delivered_by`) VALUES
(10, 13, 60, 1, 30000.00, NULL, NULL),
(11, 14, 60, 1, 30000.00, NULL, NULL),
(12, 18, 51, 1, 45000.00, NULL, NULL),
(13, 18, 56, 1, 25000.00, NULL, NULL),
(14, 19, 51, 1, 45000.00, NULL, NULL),
(15, 19, 56, 1, 25000.00, NULL, NULL);

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
(51, 11, 'S', 45000.00, 99),
(52, 11, 'M', 60000.00, 100),
(53, 12, 'S', 50000.00, 100),
(54, 12, 'M', 65000.00, 100),
(55, 12, 'L', 80000.00, 100),
(56, 13, 'S', 25000.00, 199),
(57, 13, 'M', 30000.00, 200),
(58, 14, 'M', 30000.00, 200),
(59, 14, 'L', 35000.00, 200),
(60, 15, 'M', 30000.00, 200),
(61, 15, 'L', 35000.00, 200),
(62, 16, '500ml', 20000.00, 200),
(63, 17, 'Combo', 69000.00, 100),
(64, 18, 'Combo', 95000.00, 100),
(65, 11, 'L', 70000.00, 200),
(66, 13, 'L', 35000.00, 100);

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
(8, 'Ác Tượng', 'Kinh dị', 115, 'Một thế lực tà ác thức tỉnh sau nhiều năm phong ấn.', 'https://youtu.be/YaO1QNkWFoE?si=KOBbiUDEeNqmk1Vv', 'movie/1785759433-acTuong.webp', '2026-09-12', 'Tiếng Việt', 'Đang cập nhật', 'Đang cập nhật', 'T18', 'coming_soon'),
(10, 'Ám Ảnh', 'Kinh dị', 102, 'Những hiện tượng kỳ bí liên tiếp xảy ra trong một ngôi nhà cũ.', 'https://youtu.be/gMC8kkwbIQQ?si=U5807B_CzEKD97IM', 'movie/1785758319-amAnh.webp', '2026-07-25', 'Tiếng Việt', 'Đang cập nhật', 'Đang cập nhật', 'T18', 'now_showing'),
(11, 'Âm Thanh Vượt Đại Dương', 'Tình cảm', 118, 'Câu chuyện về âm nhạc và những cuộc gặp gỡ định mệnh.', 'https://youtu.be/jmXkTbvZ6iM?si=JnKRnI8p_cFA7XYb', 'movie/1785758437-amThanhVuotDaiDuong.webp', '2026-07-18', 'Tiếng Việt', 'Đang cập nhật', 'Đang cập nhật', 'P', 'now_showing'),
(12, 'Avatar 4', 'Khoa học viễn tưởng', 185, 'Jake Sully tiếp tục hành trình bảo vệ Pandora.', 'https://youtu.be/26IJmNyirkI?si=XPP5XOsz9NcyAC0T', 'movie/1785759264-Avatar4.webp', '2026-12-18', 'Tiếng Anh', 'James Cameron', 'Sam Worthington', 'T13', 'coming_soon'),
(13, 'Avatar 5', 'Khoa học viễn tưởng', 190, 'Phần cuối của loạt phim Avatar.', 'https://youtu.be/ftQAfAuS4kg?si=zUm5giW1bOC_3azh', 'movie/1785758938-Avatar5.webp', '2027-12-17', 'Tiếng Anh', 'James Cameron', 'Sam Worthington', 'T13', 'coming_soon'),
(14, 'Avengers: Doomsday', 'Hành động, Siêu anh hùng', 150, 'Các Avengers đối đầu mối đe dọa mới.', 'https://youtu.be/irVNGjRFZGk?si=0AdTfAG0_-_gmhPc', 'movie/1785758389-avengers.webp', '2026-07-20', 'Tiếng Anh', 'Marvel Studios', 'Đang cập nhật', 'T13', 'now_showing'),
(15, 'The Backrooms', 'Kinh dị', 108, 'Lạc vào mê cung vô tận đầy bí ẩn.', 'https://youtu.be/0HjdiohVOik?si=TyPi5hSa6TkhrpZE', 'movie/1785759419-backRooms.webp', '2026-08-28', 'Tiếng Anh', 'Đang cập nhật', 'Đang cập nhật', 'T18', 'coming_soon'),
(16, 'Bò Sữa Bay', 'Hài', 96, 'Bộ phim hài gia đình vui nhộn.', 'https://youtu.be/u19T6zs4Ksk?si=QfeSswz3pliv0Dmv', 'movie/1785758493-boSuaBay.webp', '2026-07-15', 'Tiếng Việt', 'Đang cập nhật', 'Đang cập nhật', 'P', 'now_showing'),
(17, 'Cảm Ơn Người Đã Thức Cùng Tôi', 'Tình cảm', 120, 'Một câu chuyện chữa lành đầy cảm xúc.', 'https://youtu.be/NZ4o-WpnSR4?si=CmPj98fEMnVDIt1L', 'movie/1785758347-camOnNguoiDaThucCungToi.webp', '2026-07-22', 'Tiếng Việt', 'Đang cập nhật', 'Đang cập nhật', 'P', 'now_showing'),
(18, 'Clayface', 'Hành động, Siêu anh hùng', 130, 'Nguồn gốc phản diện nổi tiếng của DC.', 'https://youtu.be/NL3clCsupBw?si=98L3A76J2UQdNrQP', 'movie/1785759209-Clayface.webp', '2026-10-02', 'Tiếng Anh', 'DC Studios', 'Đang cập nhật', 'T16', 'coming_soon'),
(19, 'Detective Conan: Fallen Angel of the Highway', 'Hoạt hình, Trinh thám', 111, 'Conan điều tra vụ án bí ẩn trên cao tốc.', 'https://youtu.be/AgTzZDGYghA?si=1As6h30JcDTsXog2', 'movie/1785758570-conan.webp', '2026-07-04', 'Tiếng Nhật', 'Đang cập nhật', 'Minami Takayama', 'P', 'now_showing'),
(20, 'Cô Nàng Ngổ Ngáo', 'Tình cảm, Hài', 110, 'Phiên bản mới của câu chuyện tình hài hước.', 'https://youtu.be/Js3Yq2nfM14?si=c9QevKjeVWrlffrh', 'movie/1785758808-coNangNgoNgao.webp', '2026-08-14', 'Tiếng Hàn', 'Đang cập nhật', 'Đang cập nhật', 'P', 'coming_soon'),
(21, 'Công Viên Giải Thoát', 'Kinh dị', 105, 'Một công viên giải trí che giấu bí mật kinh hoàng.', 'https://youtu.be/sc6qMJdUY6M?si=DPe9DNOoWtPNQ7JR', 'movie/1785759614-cvgt.webp', '2026-09-01', 'Tiếng Anh', 'Đang cập nhật', 'Đang cập nhật', 'T18', 'coming_soon'),
(22, 'Chú Mèo Mang Mũ', 'Hoạt hình', 92, 'Bộ phim hoạt hình dành cho gia đình.', 'https://youtu.be/jz8pLlPhSeY?si=Rf6XUKwjhX2t-ZZX', 'movie/1785758552-chuMeoMangMu.webp', '2026-07-10', 'Tiếng Anh', 'Warner Bros.', 'Đang cập nhật', 'P', 'now_showing'),
(23, 'Đồng Dao Ma Quái', 'Kinh dị', 109, 'Một nhóm người khám phá hang động đầy quỷ dữ.', 'https://youtu.be/EU9PCW1JmLM?si=G1zwfWh45Nd0z9cU', 'movie/1785758998-dongDaoMaQuai.webp', '2026-08-25', 'Tiếng Việt', 'Đang cập nhật', 'Đang cập nhật', 'T18', 'coming_soon'),
(24, 'Dune: Part Three', 'Khoa học viễn tưởng', 170, 'Paul Atreides bước vào cuộc chiến cuối cùng.', 'https://youtu.be/NdvqHc56lE0?si=R5ys6VP7wOMFzMp-', 'movie/1785759225-Dune.webp', '2026-11-20', 'Tiếng Anh', 'Denis Villeneuve', 'Timothée Chalamet', 'T13', 'coming_soon'),
(25, 'Frozen III', 'Hoạt hình, Phiêu lưu', 110, 'Elsa và Anna trở lại trong cuộc phiêu lưu mới.', 'https://youtu.be/i_oE6WoOlLI?si=v4zaH9FNOTcMV5ZS', 'movie/1785759317-frozen.webp', '2026-12-24', 'Tiếng Anh', 'Disney', 'Đang cập nhật', 'P', 'coming_soon'),
(26, 'Hậu Duệ Thần Mặt Trời', 'Tình cảm', 120, 'Chuyện tình giữa bác sĩ và quân nhân.', 'https://youtu.be/S6H-g-8buEM?si=AKUsakwsNOaRH7-M', 'movie/1785758524-hauDueThanMatTroi.webp', '2026-07-12', 'Tiếng Hàn', 'Đang cập nhật', 'Song Joong-ki', 'P', 'now_showing'),
(27, 'Hoàng Hậu Cuối Cùng', 'Cổ trang', 130, 'Bộ phim cổ trang đầy kịch tính.', 'https://youtu.be/gV6xpwrOS6k?si=Lyc_7gM80_zCGsKT', 'movie/1785759079-hoanHauCuoiCung.webp', '2026-08-30', 'Tiếng Hàn', 'Đang cập nhật', 'Đang cập nhật', 'T13', 'coming_soon'),
(28, 'Hộ Linh Tráng Sĩ: Bí Ẩn Mộ Vua Đinh', 'Phiêu lưu', 125, 'Hành trình khám phá bí mật lịch sử Việt Nam.', 'https://youtu.be/kWTZegSlimE?si=3qxoKhdg9Stokv14', 'movie/1785758985-hoLinhTrangSi.webp', '2026-08-22', 'Tiếng Việt', 'Đang cập nhật', 'Đang cập nhật', 'T13', 'coming_soon'),
(29, 'Hòn Đảo Quên Lãng', 'Phiêu lưu', 118, 'Một hòn đảo ẩn chứa nhiều bí mật.', 'https://youtu.be/vd1wzfi8-HI?si=DyfHp-0pJaK0G9Im', 'movie/1785758849-honDaoQuenLang.webp', '2026-08-16', 'Tiếng Việt', 'Đang cập nhật', 'Đang cập nhật', 'T13', 'coming_soon'),
(30, 'Lầu Chú Hỏa', 'Kinh dị', 112, 'Những bí ẩn xảy ra trong một tòa nhà cũ.', 'https://youtu.be/4YJ4cV1dTJs?si=zaqUbNB3XqN3w5ae', 'movie/1785758422-lauChuHoa.webp', '2026-07-19', 'Tiếng Việt', 'Đang cập nhật', 'Đang cập nhật', 'T18', 'now_showing'),
(31, 'Lên Hương', 'Tâm lý', 105, 'Hành trình tìm lại giá trị bản thân.', 'https://youtu.be/ZImBtmy3XrA?si=fni8ad8DRuWCy56k', 'movie/1785758783-lenHuong.webp', '2026-08-08', 'Tiếng Việt', 'Đang cập nhật', 'Đang cập nhật', 'T13', 'coming_soon'),
(32, 'Lời Nguyền Huyết Tộc', 'Kinh dị', 108, 'Lời nguyền kéo dài qua nhiều thế hệ.', 'https://youtu.be/rMsQUXdD5zg?si=KumVKj8Rvcabgzs2', 'movie/1785758957-loi_nguyen_huyet_toc.webp', '2026-08-20', 'Tiếng Việt', 'Đang cập nhật', 'Đang cập nhật', 'T18', 'coming_soon'),
(33, 'Loving Karma', 'Tình cảm, Chính kịch', 118, 'Một câu chuyện về tình yêu và sự tha thứ.', 'https://youtu.be/wSnbB1umwEM?si=sERWc4rZvuDh2ExL', 'movie/1785759014-lovingKarma.webp', '2026-08-28', 'Tiếng Hàn', 'Đang cập nhật', 'Đang cập nhật', 'T13', 'coming_soon'),
(34, 'Ma Xưởng Hòm', 'Kinh dị', 107, 'Những bí mật kinh hoàng bị chôn vùi nhiều năm.', 'https://youtu.be/XGSBNAKNGtk?si=aHqTnGM9c-TvZBxV', 'movie/1785759473-mauXuongHom.webp', '2026-09-05', 'Tiếng Việt', 'Đang cập nhật', 'Đang cập nhật', 'T18', 'coming_soon'),
(35, 'Mẹ Ơi Về Nhà', 'Gia đình, Tâm lý', 115, 'Bộ phim cảm động về tình cảm gia đình.', 'https://youtu.be/FJDJyluXVkw?si=Iv24ZxG1JaNOuSBt', 'movie/1785758333-meOi,VeNha.webp', '2026-07-24', 'Tiếng Việt', 'Đang cập nhật', 'Đang cập nhật', 'P', 'now_showing'),
(36, 'Minions & Monsters', 'Hoạt hình, Hài', 95, 'Những Minions tiếp tục cuộc phiêu lưu đầy hài hước.', 'https://youtu.be/ZSdOwt-G49w?si=s6MAE-juzQiCn1qU', 'movie/1785758459-minions.webp', '2026-07-18', 'Tiếng Anh', 'Pierre Coffin', 'Đang cập nhật', 'P', 'now_showing'),
(37, 'Moana', 'Hoạt hình, Phiêu lưu', 108, 'Moana trở lại với chuyến hải trình mới.', 'https://youtu.be/eflOa68vl_o?si=TUv5iix-bmyfVJdU', 'movie/1785759244-moana.webp', '2026-12-12', 'Tiếng Anh', 'Disney', 'Đang cập nhật', 'P', 'coming_soon'),
(38, 'Nghỉ Hè Sợ Nghi Hưu', 'Hài', 103, 'Một kỳ nghỉ hè đầy tiếng cười và bất ngờ.', 'https://youtu.be/_gPgo3HQFGE?si=YGmSWsbRxKJwEE81', 'movie/1785757891-nghiHeSoNghiHuu.webp', '2026-08-01', 'Tiếng Việt', 'Đang cập nhật', 'Đang cập nhật', 'P', 'now_showing'),
(39, 'Người Được Chọn', 'Hành động, Viễn tưởng', 128, 'Người mang năng lực đặc biệt đứng trước vận mệnh nhân loại.', 'https://www.youtube.com/results?search_query=Người+Được+Chọn+trailer', 'movie/1785759493-nguoiDuocChon.webp', '2026-09-10', 'Tiếng Anh', 'Đang cập nhật', 'Đang cập nhật', 'T13', 'coming_soon'),
(40, 'Spider-Man: Brand New Day', 'Hành động, Phiêu lưu', 140, 'Peter Parker đối mặt với một kẻ thù hoàn toàn mới.', 'https://youtu.be/0H_mDKTRVBQ?si=ozTm233KPMUqn0bz', 'movie/1785758249-nguoinhen.webp', '2026-07-31', 'Tiếng Anh', 'Destin Daniel Cretton', 'Tom Holland, Zendaya', 'T13', 'now_showing'),
(41, 'Oak', 'Phiêu lưu, Giả tưởng', 104, 'Cuộc phiêu lưu kỳ ảo trong khu rừng cổ đại.', 'https://youtu.be/JLcRRP30tfc?si=kQHcX_BwGKqI47z2', 'movie/1785758862-oak.webp', '2026-08-18', 'Tiếng Anh', 'Đang cập nhật', 'Đang cập nhật', 'P', 'coming_soon'),
(42, 'Quỷ Bắt Hồn', 'Kinh dị', 110, 'Những linh hồn báo oán quay trở lại.', 'https://youtu.be/bOPJW8orWjc?si=YHZWVPacg6hOeejz', 'movie/1785758797-quy_bat_hon.webp', '2026-08-08', 'Tiếng Việt', 'Đang cập nhật', 'Đang cập nhật', 'T18', 'coming_soon'),
(43, 'Quý Tử Vượt Giàu', 'Hài, Gia đình', 109, 'Chàng công tử học cách sống tự lập.', 'https://youtu.be/xvev8eWB2yc?si=Hx44yn2wlXnncJiq', 'movie/1785758306-quyTuVuotGiau.webp', '2026-07-26', 'Tiếng Việt', 'Đang cập nhật', 'Đang cập nhật', 'P', 'now_showing'),
(44, 'Crayon Shin-chan the Movie', 'Hoạt hình, Hài', 97, 'Shin và gia đình bước vào chuyến phiêu lưu mới.', 'https://youtu.be/_ZvdQS9gRrw?si=g7hLxmGZ7ySefyaB', 'movie/1785758834-Shin.webp', '2026-08-15', 'Tiếng Nhật', 'Đang cập nhật', 'Đang cập nhật', 'P', 'coming_soon'),
(45, 'Sợi Chỉ Đỏ', 'Tình cảm', 114, 'Những con người được kết nối bởi định mệnh.', 'https://youtu.be/nHnI7dL57Nc?si=pVd7RIgBnvx77B9W', 'movie/1785758284-soiChiDo.webp', '2026-07-29', 'Tiếng Việt', 'Đang cập nhật', 'Đang cập nhật', 'P', 'now_showing'),
(46, 'Street Fighter', 'Hành động, Võ thuật', 130, 'Những chiến binh huyền thoại tái xuất.', 'https://youtu.be/xwnZ_uvrFs0?si=UUhcP-Fi_z3PPzQT', 'movie/1785759401-StreetFighter.webp', '2026-09-18', 'Tiếng Anh', 'Legendary Pictures', 'Đang cập nhật', 'T16', 'coming_soon'),
(47, 'Toy Story 5', 'Hoạt hình, Gia đình', 110, 'Woody và Buzz trở lại trong cuộc phiêu lưu mới.', 'https://youtu.be/GGBgf8dcgyY?si=8PlYeDZUGD5jYmmw', 'movie/1785759293-toyStory5.webp', '2026-12-20', 'Tiếng Anh', 'Pixar', 'Tom Hanks, Tim Allen', 'P', 'coming_soon'),
(48, 'Thám Tử Kiên: Lời Nguyền Hoàng Kim', 'Trinh thám', 121, 'Thám tử Kiên điều tra vụ án bí ẩn đầy nguy hiểm.', 'https://www.youtube.com/results?search_query=Thám+Tử+Kiên+trailer', 'movie/1785758475-thamTuKien.webp', '2026-07-18', 'Tiếng Việt', 'Victor Vũ', 'Quốc Huy', 'T16', 'now_showing'),
(49, 'The Odyssey', 'Phiêu lưu, Sử thi', 170, 'Hành trình huyền thoại của Odysseus sau chiến tranh thành Troy.', 'https://youtu.be/Mzw2ttJD2qQ?si=jxwSCyQzo7ImGpvk', 'movie/1785759177-TheOdyssey.webp', '2026-07-17', 'Tiếng Anh', 'Christopher Nolan', 'Matt Damon', 'T13', 'now_showing'),
(50, 'Thư Tình Gửi Ngoại', 'Tình cảm', 108, 'Những lá thư vô tình kết nối hai tâm hồn xa lạ.', 'https://youtu.be/Ta-tV7RXrgU?si=TxnUxmKU1FV5yPCM', 'movie/1785758970-thu_tinh_gui-ngoai.webp', '2026-08-21', 'Tiếng Việt', 'Đang cập nhật', 'Đang cập nhật', 'P', 'coming_soon'),
(51, 'Trại Buôn Người', 'Hành động, Tội phạm', 132, 'Cuộc giải cứu nghẹt thở khỏi đường dây buôn người.', 'https://youtu.be/nlFar2vq4Yo?si=R5apIkx-_DqdZQZ8', 'movie/1785759056-traiBuonNguoi.webp', '2026-08-29', 'Tiếng Việt', 'Đang cập nhật', 'Đang cập nhật', 'T18', 'coming_soon'),
(52, 'Trái Tim Quái Thú', 'Tâm lý, Kỳ ảo', 116, 'Một trái tim mang sức mạnh vượt ngoài trí tưởng tượng.', 'https://youtu.be/_1F2MrfQLjo?si=AHxhd3Dv0uBTa2av', 'movie/1785758750-traiTimQuaiThu.webp', '2026-08-06', 'Tiếng Việt', 'Đang cập nhật', 'Đang cập nhật', 'T13', 'coming_soon'),
(53, 'Trường Hè', 'Học đường', 102, 'Mùa hè đáng nhớ của nhóm bạn trẻ.', 'https://youtu.be/2AzjKy6jT-w?si=Ih695MOKbbvUZcAG', 'movie/1785758364-truongHe.webp', '2026-07-21', 'Tiếng Việt', 'Đang cập nhật', 'Đang cập nhật', 'P', 'now_showing'),
(54, 'Umamusume: Pretty Derby', 'Hoạt hình, Thể thao', 110, 'Những cô gái ngựa bước vào giải đấu lớn nhất.', 'https://youtu.be/oyhJGXKLH3g?si=_nPGvE6kxJQJeEvb', 'movie/1785759278-umamusume.webp', '2026-09-26', 'Tiếng Nhật', 'Cygames Pictures', 'Đang cập nhật', 'P', 'coming_soon'),
(55, 'Xứ Sở Thần Tiên', 'Hoạt Hình, Phiêu Lưu', 119, 'Adventure is just... a wish away.', 'https://youtu.be/wxGvki6LQN0?si=5eyco030fRGzKXdl', 'movie/1785759380-xstt.webp', '2026-10-08', 'Tiếng Anh', 'Đang cập nhật', 'Đang cập nhật', 'T13', 'coming_soon'),
(56, 'Colony: Bầy Xác Sống', 'Kinh dị, Hành động', 112, 'Đại dịch zombie bùng phát trên toàn thành phố.', 'https://youtu.be/NI5iE1R8HgQ?si=AoK8fhmD_fYraNIw', 'movie/1785759340-zombies.webp', '2026-10-30', 'Tiếng Anh', 'Đang cập nhật', 'Đang cập nhật', 'T18', 'coming_soon');

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
(5, 'momo', 'MOMO12345678', 320000.00, 'failed', '2026-07-23 13:10:00'),
(6, 'vnpay', '15650119', 57000.00, 'completed', '2026-08-07 20:06:16'),
(7, 'vnpay', '15650121', 414000.00, 'completed', '2026-08-07 20:18:14'),
(8, 'vnpay', '15650122', 298000.00, 'failed', '2026-08-07 20:21:15');

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
(25, 8, 'Phòng IMAX 1', 200),
(26, 8, 'Phòng IMAX 2', 200),
(27, 9, 'L\'amour', 60),
(28, 9, 'Compo Couple', 60),
(29, 9, 'Phòng VIP', 60),
(30, 6, 'Phòng 01', 100),
(31, 6, 'Phòng 02', 120),
(32, 6, 'Phòng 03', 100),
(33, 6, 'Phòng 04', 100),
(34, 7, 'Phòng 05', 120),
(35, 7, 'Phòng 06', 120),
(36, 7, 'Phòng 07', 120);

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
(6, '2D', 2000.00, 'Phòng chiếu tiêu chuẩn 2D'),
(7, '3D', 30000.00, 'Phòng chiếu công nghệ 3D'),
(8, 'IMAX', 45000.00, 'Phòng chiếu IMAX màn hình lớn'),
(9, 'Gold Class', 10000.00, 'Phòng chiếu cao cấp với ghế VIP');

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
  `couple_group` varchar(100) DEFAULT NULL,
  `status` enum('available','maintenance') NOT NULL DEFAULT 'available'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

--
-- Dumping data for table `seats`
--

INSERT INTO `seats` (`id`, `room_id`, `seat_type_id`, `seat_number`, `row_char`, `col_num`, `couple_group`, `status`) VALUES
(1512, 26, 5, 'A1', 'A', 1, NULL, 'available'),
(1513, 26, 5, 'A2', 'A', 2, NULL, 'available'),
(1514, 26, 5, 'A3', 'A', 3, NULL, 'available'),
(1515, 26, 5, 'A4', 'A', 4, NULL, 'available'),
(1516, 26, 5, 'A5', 'A', 5, NULL, 'available'),
(1517, 26, 5, 'A6', 'A', 6, NULL, 'available'),
(1518, 26, 5, 'A7', 'A', 7, NULL, 'available'),
(1519, 26, 5, 'A8', 'A', 8, NULL, 'available'),
(1520, 26, 5, 'A9', 'A', 9, NULL, 'available'),
(1521, 26, 5, 'A10', 'A', 10, NULL, 'available'),
(1522, 26, 5, 'A11', 'A', 11, NULL, 'available'),
(1523, 26, 5, 'A12', 'A', 12, NULL, 'available'),
(1524, 26, 5, 'A13', 'A', 13, NULL, 'available'),
(1525, 26, 5, 'A14', 'A', 14, NULL, 'available'),
(1526, 26, 5, 'A15', 'A', 15, NULL, 'available'),
(1527, 26, 5, 'A16', 'A', 16, NULL, 'available'),
(1528, 26, 5, 'A17', 'A', 17, NULL, 'available'),
(1529, 26, 5, 'A18', 'A', 18, NULL, 'available'),
(1530, 26, 5, 'A19', 'A', 19, NULL, 'available'),
(1531, 26, 5, 'A20', 'A', 20, NULL, 'available'),
(1532, 26, 5, 'B1', 'B', 1, NULL, 'available'),
(1533, 26, 5, 'B2', 'B', 2, NULL, 'available'),
(1534, 26, 5, 'B3', 'B', 3, NULL, 'available'),
(1535, 26, 5, 'B4', 'B', 4, NULL, 'available'),
(1536, 26, 5, 'B5', 'B', 5, NULL, 'available'),
(1537, 26, 5, 'B6', 'B', 6, NULL, 'available'),
(1538, 26, 5, 'B7', 'B', 7, NULL, 'available'),
(1539, 26, 5, 'B8', 'B', 8, NULL, 'available'),
(1540, 26, 5, 'B9', 'B', 9, NULL, 'available'),
(1541, 26, 5, 'B10', 'B', 10, NULL, 'available'),
(1542, 26, 5, 'B11', 'B', 11, NULL, 'available'),
(1543, 26, 5, 'B12', 'B', 12, NULL, 'available'),
(1544, 26, 5, 'B13', 'B', 13, NULL, 'available'),
(1545, 26, 5, 'B14', 'B', 14, NULL, 'available'),
(1546, 26, 5, 'B15', 'B', 15, NULL, 'available'),
(1547, 26, 5, 'B16', 'B', 16, NULL, 'available'),
(1548, 26, 5, 'B17', 'B', 17, NULL, 'available'),
(1549, 26, 5, 'B18', 'B', 18, NULL, 'available'),
(1550, 26, 5, 'B19', 'B', 19, NULL, 'available'),
(1551, 26, 5, 'B20', 'B', 20, NULL, 'available'),
(1552, 26, 5, 'C1', 'C', 1, NULL, 'available'),
(1553, 26, 5, 'C2', 'C', 2, NULL, 'available'),
(1554, 26, 5, 'C3', 'C', 3, NULL, 'available'),
(1555, 26, 5, 'C4', 'C', 4, NULL, 'available'),
(1556, 26, 5, 'C5', 'C', 5, NULL, 'available'),
(1557, 26, 5, 'C6', 'C', 6, NULL, 'available'),
(1558, 26, 5, 'C7', 'C', 7, NULL, 'available'),
(1559, 26, 5, 'C8', 'C', 8, NULL, 'available'),
(1560, 26, 5, 'C9', 'C', 9, NULL, 'available'),
(1561, 26, 5, 'C10', 'C', 10, NULL, 'available'),
(1562, 26, 5, 'C11', 'C', 11, NULL, 'available'),
(1563, 26, 5, 'C12', 'C', 12, NULL, 'available'),
(1564, 26, 5, 'C13', 'C', 13, NULL, 'available'),
(1565, 26, 5, 'C14', 'C', 14, NULL, 'available'),
(1566, 26, 5, 'C15', 'C', 15, NULL, 'available'),
(1567, 26, 5, 'C16', 'C', 16, NULL, 'available'),
(1568, 26, 5, 'C17', 'C', 17, NULL, 'available'),
(1569, 26, 5, 'C18', 'C', 18, NULL, 'available'),
(1570, 26, 5, 'C19', 'C', 19, NULL, 'available'),
(1571, 26, 5, 'C20', 'C', 20, NULL, 'available'),
(1572, 26, 6, 'D1', 'D', 1, NULL, 'available'),
(1573, 26, 6, 'D2', 'D', 2, NULL, 'available'),
(1574, 26, 6, 'D3', 'D', 3, NULL, 'available'),
(1575, 26, 6, 'D4', 'D', 4, NULL, 'available'),
(1576, 26, 6, 'D5', 'D', 5, NULL, 'available'),
(1577, 26, 6, 'D6', 'D', 6, NULL, 'available'),
(1578, 26, 6, 'D7', 'D', 7, NULL, 'available'),
(1579, 26, 6, 'D8', 'D', 8, NULL, 'available'),
(1580, 26, 6, 'D9', 'D', 9, NULL, 'available'),
(1581, 26, 6, 'D10', 'D', 10, NULL, 'available'),
(1582, 26, 6, 'D11', 'D', 11, NULL, 'available'),
(1583, 26, 6, 'D12', 'D', 12, NULL, 'available'),
(1584, 26, 6, 'D13', 'D', 13, NULL, 'available'),
(1585, 26, 6, 'D14', 'D', 14, NULL, 'available'),
(1586, 26, 6, 'D15', 'D', 15, NULL, 'available'),
(1587, 26, 6, 'D16', 'D', 16, NULL, 'available'),
(1588, 26, 6, 'D17', 'D', 17, NULL, 'available'),
(1589, 26, 6, 'D18', 'D', 18, NULL, 'available'),
(1590, 26, 6, 'D19', 'D', 19, NULL, 'available'),
(1591, 26, 6, 'D20', 'D', 20, NULL, 'available'),
(1592, 26, 6, 'E1', 'E', 1, NULL, 'available'),
(1593, 26, 6, 'E2', 'E', 2, NULL, 'available'),
(1594, 26, 6, 'E3', 'E', 3, NULL, 'available'),
(1595, 26, 6, 'E4', 'E', 4, NULL, 'available'),
(1596, 26, 6, 'E5', 'E', 5, NULL, 'available'),
(1597, 26, 6, 'E6', 'E', 6, NULL, 'available'),
(1598, 26, 6, 'E7', 'E', 7, NULL, 'available'),
(1599, 26, 6, 'E8', 'E', 8, NULL, 'available'),
(1600, 26, 6, 'E9', 'E', 9, NULL, 'available'),
(1601, 26, 6, 'E10', 'E', 10, NULL, 'available'),
(1602, 26, 6, 'E11', 'E', 11, NULL, 'available'),
(1603, 26, 6, 'E12', 'E', 12, NULL, 'available'),
(1604, 26, 6, 'E13', 'E', 13, NULL, 'available'),
(1605, 26, 6, 'E14', 'E', 14, NULL, 'available'),
(1606, 26, 6, 'E15', 'E', 15, NULL, 'available'),
(1607, 26, 6, 'E16', 'E', 16, NULL, 'available'),
(1608, 26, 6, 'E17', 'E', 17, NULL, 'available'),
(1609, 26, 6, 'E18', 'E', 18, NULL, 'available'),
(1610, 26, 6, 'E19', 'E', 19, NULL, 'available'),
(1611, 26, 6, 'E20', 'E', 20, NULL, 'available'),
(1612, 26, 6, 'F1', 'F', 1, NULL, 'available'),
(1613, 26, 6, 'F2', 'F', 2, NULL, 'available'),
(1614, 26, 6, 'F3', 'F', 3, NULL, 'available'),
(1615, 26, 6, 'F4', 'F', 4, NULL, 'available'),
(1616, 26, 6, 'F5', 'F', 5, NULL, 'available'),
(1617, 26, 6, 'F6', 'F', 6, NULL, 'available'),
(1618, 26, 6, 'F7', 'F', 7, NULL, 'available'),
(1619, 26, 6, 'F8', 'F', 8, NULL, 'available'),
(1620, 26, 6, 'F9', 'F', 9, NULL, 'available'),
(1621, 26, 6, 'F10', 'F', 10, NULL, 'available'),
(1622, 26, 6, 'F11', 'F', 11, NULL, 'available'),
(1623, 26, 6, 'F12', 'F', 12, NULL, 'available'),
(1624, 26, 6, 'F13', 'F', 13, NULL, 'available'),
(1625, 26, 6, 'F14', 'F', 14, NULL, 'available'),
(1626, 26, 6, 'F15', 'F', 15, NULL, 'available'),
(1627, 26, 6, 'F16', 'F', 16, NULL, 'available'),
(1628, 26, 6, 'F17', 'F', 17, NULL, 'available'),
(1629, 26, 6, 'F18', 'F', 18, NULL, 'available'),
(1630, 26, 6, 'F19', 'F', 19, NULL, 'available'),
(1631, 26, 6, 'F20', 'F', 20, NULL, 'available'),
(1632, 26, 6, 'G1', 'G', 1, NULL, 'available'),
(1633, 26, 6, 'G2', 'G', 2, NULL, 'available'),
(1634, 26, 6, 'G3', 'G', 3, NULL, 'available'),
(1635, 26, 6, 'G4', 'G', 4, NULL, 'available'),
(1636, 26, 6, 'G5', 'G', 5, NULL, 'available'),
(1637, 26, 6, 'G6', 'G', 6, NULL, 'available'),
(1638, 26, 6, 'G7', 'G', 7, NULL, 'available'),
(1639, 26, 6, 'G8', 'G', 8, NULL, 'available'),
(1640, 26, 6, 'G9', 'G', 9, NULL, 'available'),
(1641, 26, 6, 'G10', 'G', 10, NULL, 'available'),
(1642, 26, 6, 'G11', 'G', 11, NULL, 'available'),
(1643, 26, 6, 'G12', 'G', 12, NULL, 'available'),
(1644, 26, 6, 'G13', 'G', 13, NULL, 'available'),
(1645, 26, 6, 'G14', 'G', 14, NULL, 'available'),
(1646, 26, 6, 'G15', 'G', 15, NULL, 'available'),
(1647, 26, 6, 'G16', 'G', 16, NULL, 'available'),
(1648, 26, 6, 'G17', 'G', 17, NULL, 'available'),
(1649, 26, 6, 'G18', 'G', 18, NULL, 'available'),
(1650, 26, 6, 'G19', 'G', 19, NULL, 'available'),
(1651, 26, 6, 'G20', 'G', 20, NULL, 'available'),
(1652, 26, 6, 'H1', 'H', 1, NULL, 'available'),
(1653, 26, 6, 'H2', 'H', 2, NULL, 'available'),
(1654, 26, 6, 'H3', 'H', 3, NULL, 'available'),
(1655, 26, 6, 'H4', 'H', 4, NULL, 'available'),
(1656, 26, 6, 'H5', 'H', 5, NULL, 'available'),
(1657, 26, 6, 'H6', 'H', 6, NULL, 'available'),
(1658, 26, 6, 'H7', 'H', 7, NULL, 'available'),
(1659, 26, 6, 'H8', 'H', 8, NULL, 'available'),
(1660, 26, 6, 'H9', 'H', 9, NULL, 'available'),
(1661, 26, 6, 'H10', 'H', 10, NULL, 'available'),
(1662, 26, 6, 'H11', 'H', 11, NULL, 'available'),
(1663, 26, 6, 'H12', 'H', 12, NULL, 'available'),
(1664, 26, 6, 'H13', 'H', 13, NULL, 'available'),
(1665, 26, 6, 'H14', 'H', 14, NULL, 'available'),
(1666, 26, 6, 'H15', 'H', 15, NULL, 'available'),
(1667, 26, 6, 'H16', 'H', 16, NULL, 'available'),
(1668, 26, 6, 'H17', 'H', 17, NULL, 'available'),
(1669, 26, 6, 'H18', 'H', 18, NULL, 'available'),
(1670, 26, 6, 'H19', 'H', 19, NULL, 'available'),
(1671, 26, 6, 'H20', 'H', 20, NULL, 'available'),
(1672, 26, 6, 'I1', 'I', 1, NULL, 'available'),
(1673, 26, 6, 'I2', 'I', 2, NULL, 'available'),
(1674, 26, 6, 'I3', 'I', 3, NULL, 'available'),
(1675, 26, 6, 'I4', 'I', 4, NULL, 'available'),
(1676, 26, 6, 'I5', 'I', 5, NULL, 'available'),
(1677, 26, 6, 'I6', 'I', 6, NULL, 'available'),
(1678, 26, 6, 'I7', 'I', 7, NULL, 'available'),
(1679, 26, 6, 'I8', 'I', 8, NULL, 'available'),
(1680, 26, 6, 'I9', 'I', 9, NULL, 'available'),
(1681, 26, 6, 'I10', 'I', 10, NULL, 'available'),
(1682, 26, 6, 'I11', 'I', 11, NULL, 'available'),
(1683, 26, 6, 'I12', 'I', 12, NULL, 'available'),
(1684, 26, 6, 'I13', 'I', 13, NULL, 'available'),
(1685, 26, 6, 'I14', 'I', 14, NULL, 'available'),
(1686, 26, 6, 'I15', 'I', 15, NULL, 'available'),
(1687, 26, 6, 'I16', 'I', 16, NULL, 'available'),
(1688, 26, 6, 'I17', 'I', 17, NULL, 'available'),
(1689, 26, 6, 'I18', 'I', 18, NULL, 'available'),
(1690, 26, 6, 'I19', 'I', 19, NULL, 'available'),
(1691, 26, 6, 'I20', 'I', 20, NULL, 'available'),
(1692, 26, 7, 'J1', 'J', 1, 'ROOM_26_J_PAIR_1', 'available'),
(1693, 26, 7, 'J2', 'J', 2, 'ROOM_26_J_PAIR_1', 'available'),
(1694, 26, 7, 'J3', 'J', 3, 'ROOM_26_J_PAIR_2', 'available'),
(1695, 26, 7, 'J4', 'J', 4, 'ROOM_26_J_PAIR_2', 'available'),
(1696, 26, 7, 'J5', 'J', 5, 'ROOM_26_J_PAIR_3', 'available'),
(1697, 26, 7, 'J6', 'J', 6, 'ROOM_26_J_PAIR_3', 'available'),
(1698, 26, 7, 'J7', 'J', 7, 'ROOM_26_J_PAIR_4', 'available'),
(1699, 26, 7, 'J8', 'J', 8, 'ROOM_26_J_PAIR_4', 'available'),
(1700, 26, 7, 'J9', 'J', 9, 'ROOM_26_J_PAIR_5', 'available'),
(1701, 26, 7, 'J10', 'J', 10, 'ROOM_26_J_PAIR_5', 'available'),
(1702, 26, 7, 'J11', 'J', 11, 'ROOM_26_J_PAIR_6', 'available'),
(1703, 26, 7, 'J12', 'J', 12, 'ROOM_26_J_PAIR_6', 'available'),
(1704, 26, 7, 'J13', 'J', 13, 'ROOM_26_J_PAIR_7', 'available'),
(1705, 26, 7, 'J14', 'J', 14, 'ROOM_26_J_PAIR_7', 'available'),
(1706, 26, 7, 'J15', 'J', 15, 'ROOM_26_J_PAIR_8', 'available'),
(1707, 26, 7, 'J16', 'J', 16, 'ROOM_26_J_PAIR_8', 'available'),
(1708, 26, 7, 'J17', 'J', 17, 'ROOM_26_J_PAIR_9', 'available'),
(1709, 26, 7, 'J18', 'J', 18, 'ROOM_26_J_PAIR_9', 'available'),
(1710, 26, 7, 'J19', 'J', 19, 'ROOM_26_J_PAIR_10', 'available'),
(1711, 26, 7, 'J20', 'J', 20, 'ROOM_26_J_PAIR_10', 'available'),
(1712, 27, 7, 'A1', 'A', 1, 'ROOM_27_A_PAIR_1', 'available'),
(1713, 27, 7, 'A2', 'A', 2, 'ROOM_27_A_PAIR_1', 'available'),
(1714, 27, 7, 'A3', 'A', 3, 'ROOM_27_A_PAIR_2', 'available'),
(1715, 27, 7, 'A4', 'A', 4, 'ROOM_27_A_PAIR_2', 'available'),
(1716, 27, 7, 'A5', 'A', 5, 'ROOM_27_A_PAIR_3', 'available'),
(1717, 27, 7, 'A6', 'A', 6, 'ROOM_27_A_PAIR_3', 'available'),
(1718, 27, 7, 'A7', 'A', 7, 'ROOM_27_A_PAIR_4', 'available'),
(1719, 27, 7, 'A8', 'A', 8, 'ROOM_27_A_PAIR_4', 'available'),
(1720, 27, 7, 'A9', 'A', 9, 'ROOM_27_A_PAIR_5', 'available'),
(1721, 27, 7, 'A10', 'A', 10, 'ROOM_27_A_PAIR_5', 'available'),
(1722, 27, 7, 'B1', 'B', 1, 'ROOM_27_B_PAIR_1', 'available'),
(1723, 27, 7, 'B2', 'B', 2, 'ROOM_27_B_PAIR_1', 'available'),
(1724, 27, 7, 'B3', 'B', 3, 'ROOM_27_B_PAIR_2', 'available'),
(1725, 27, 7, 'B4', 'B', 4, 'ROOM_27_B_PAIR_2', 'available'),
(1726, 27, 7, 'B5', 'B', 5, 'ROOM_27_B_PAIR_3', 'available'),
(1727, 27, 7, 'B6', 'B', 6, 'ROOM_27_B_PAIR_3', 'available'),
(1728, 27, 7, 'B7', 'B', 7, 'ROOM_27_B_PAIR_4', 'available'),
(1729, 27, 7, 'B8', 'B', 8, 'ROOM_27_B_PAIR_4', 'available'),
(1730, 27, 7, 'B9', 'B', 9, 'ROOM_27_B_PAIR_5', 'available'),
(1731, 27, 7, 'B10', 'B', 10, 'ROOM_27_B_PAIR_5', 'available'),
(1732, 27, 7, 'C1', 'C', 1, 'ROOM_27_C_PAIR_1', 'available'),
(1733, 27, 7, 'C2', 'C', 2, 'ROOM_27_C_PAIR_1', 'available'),
(1734, 27, 7, 'C3', 'C', 3, 'ROOM_27_C_PAIR_2', 'available'),
(1735, 27, 7, 'C4', 'C', 4, 'ROOM_27_C_PAIR_2', 'available'),
(1736, 27, 7, 'C5', 'C', 5, 'ROOM_27_C_PAIR_3', 'available'),
(1737, 27, 7, 'C6', 'C', 6, 'ROOM_27_C_PAIR_3', 'available'),
(1738, 27, 7, 'C7', 'C', 7, 'ROOM_27_C_PAIR_4', 'available'),
(1739, 27, 7, 'C8', 'C', 8, 'ROOM_27_C_PAIR_4', 'available'),
(1740, 27, 7, 'C9', 'C', 9, 'ROOM_27_C_PAIR_5', 'available'),
(1741, 27, 7, 'C10', 'C', 10, 'ROOM_27_C_PAIR_5', 'available'),
(1742, 27, 7, 'D1', 'D', 1, 'ROOM_27_D_PAIR_1', 'available'),
(1743, 27, 7, 'D2', 'D', 2, 'ROOM_27_D_PAIR_1', 'available'),
(1744, 27, 7, 'D3', 'D', 3, 'ROOM_27_D_PAIR_2', 'available'),
(1745, 27, 7, 'D4', 'D', 4, 'ROOM_27_D_PAIR_2', 'available'),
(1746, 27, 7, 'D5', 'D', 5, 'ROOM_27_D_PAIR_3', 'available'),
(1747, 27, 7, 'D6', 'D', 6, 'ROOM_27_D_PAIR_3', 'available'),
(1748, 27, 7, 'D7', 'D', 7, 'ROOM_27_D_PAIR_4', 'available'),
(1749, 27, 7, 'D8', 'D', 8, 'ROOM_27_D_PAIR_4', 'available'),
(1750, 27, 7, 'D9', 'D', 9, 'ROOM_27_D_PAIR_5', 'available'),
(1751, 27, 7, 'D10', 'D', 10, 'ROOM_27_D_PAIR_5', 'available'),
(1752, 27, 7, 'E1', 'E', 1, 'ROOM_27_E_PAIR_1', 'available'),
(1753, 27, 7, 'E2', 'E', 2, 'ROOM_27_E_PAIR_1', 'available'),
(1754, 27, 7, 'E3', 'E', 3, 'ROOM_27_E_PAIR_2', 'available'),
(1755, 27, 7, 'E4', 'E', 4, 'ROOM_27_E_PAIR_2', 'available'),
(1756, 27, 7, 'E5', 'E', 5, 'ROOM_27_E_PAIR_3', 'available'),
(1757, 27, 7, 'E6', 'E', 6, 'ROOM_27_E_PAIR_3', 'available'),
(1758, 27, 7, 'E7', 'E', 7, 'ROOM_27_E_PAIR_4', 'available'),
(1759, 27, 7, 'E8', 'E', 8, 'ROOM_27_E_PAIR_4', 'available'),
(1760, 27, 7, 'E9', 'E', 9, 'ROOM_27_E_PAIR_5', 'available'),
(1761, 27, 7, 'E10', 'E', 10, 'ROOM_27_E_PAIR_5', 'available'),
(1762, 27, 7, 'F1', 'F', 1, 'ROOM_27_F_PAIR_1', 'available'),
(1763, 27, 7, 'F2', 'F', 2, 'ROOM_27_F_PAIR_1', 'available'),
(1764, 27, 7, 'F3', 'F', 3, 'ROOM_27_F_PAIR_2', 'available'),
(1765, 27, 7, 'F4', 'F', 4, 'ROOM_27_F_PAIR_2', 'available'),
(1766, 27, 7, 'F5', 'F', 5, 'ROOM_27_F_PAIR_3', 'available'),
(1767, 27, 7, 'F6', 'F', 6, 'ROOM_27_F_PAIR_3', 'available'),
(1768, 27, 7, 'F7', 'F', 7, 'ROOM_27_F_PAIR_4', 'available'),
(1769, 27, 7, 'F8', 'F', 8, 'ROOM_27_F_PAIR_4', 'available'),
(1770, 27, 7, 'F9', 'F', 9, 'ROOM_27_F_PAIR_5', 'available'),
(1771, 27, 7, 'F10', 'F', 10, 'ROOM_27_F_PAIR_5', 'available'),
(1772, 28, 7, 'A1', 'A', 1, 'ROOM_28_A_PAIR_1', 'available'),
(1773, 28, 7, 'A2', 'A', 2, 'ROOM_28_A_PAIR_1', 'available'),
(1774, 28, 7, 'A3', 'A', 3, 'ROOM_28_A_PAIR_2', 'available'),
(1775, 28, 7, 'A4', 'A', 4, 'ROOM_28_A_PAIR_2', 'available'),
(1776, 28, 7, 'A5', 'A', 5, 'ROOM_28_A_PAIR_3', 'available'),
(1777, 28, 7, 'A6', 'A', 6, 'ROOM_28_A_PAIR_3', 'available'),
(1778, 28, 7, 'A7', 'A', 7, 'ROOM_28_A_PAIR_4', 'available'),
(1779, 28, 7, 'A8', 'A', 8, 'ROOM_28_A_PAIR_4', 'available'),
(1780, 28, 7, 'A9', 'A', 9, 'ROOM_28_A_PAIR_5', 'available'),
(1781, 28, 7, 'A10', 'A', 10, 'ROOM_28_A_PAIR_5', 'available'),
(1782, 28, 7, 'B1', 'B', 1, 'ROOM_28_B_PAIR_1', 'available'),
(1783, 28, 7, 'B2', 'B', 2, 'ROOM_28_B_PAIR_1', 'available'),
(1784, 28, 7, 'B3', 'B', 3, 'ROOM_28_B_PAIR_2', 'available'),
(1785, 28, 7, 'B4', 'B', 4, 'ROOM_28_B_PAIR_2', 'available'),
(1786, 28, 7, 'B5', 'B', 5, 'ROOM_28_B_PAIR_3', 'available'),
(1787, 28, 7, 'B6', 'B', 6, 'ROOM_28_B_PAIR_3', 'available'),
(1788, 28, 7, 'B7', 'B', 7, 'ROOM_28_B_PAIR_4', 'available'),
(1789, 28, 7, 'B8', 'B', 8, 'ROOM_28_B_PAIR_4', 'available'),
(1790, 28, 7, 'B9', 'B', 9, 'ROOM_28_B_PAIR_5', 'available'),
(1791, 28, 7, 'B10', 'B', 10, 'ROOM_28_B_PAIR_5', 'available'),
(1792, 28, 7, 'C1', 'C', 1, 'ROOM_28_C_PAIR_1', 'available'),
(1793, 28, 7, 'C2', 'C', 2, 'ROOM_28_C_PAIR_1', 'available'),
(1794, 28, 7, 'C3', 'C', 3, 'ROOM_28_C_PAIR_2', 'available'),
(1795, 28, 7, 'C4', 'C', 4, 'ROOM_28_C_PAIR_2', 'available'),
(1796, 28, 7, 'C5', 'C', 5, 'ROOM_28_C_PAIR_3', 'available'),
(1797, 28, 7, 'C6', 'C', 6, 'ROOM_28_C_PAIR_3', 'available'),
(1798, 28, 7, 'C7', 'C', 7, 'ROOM_28_C_PAIR_4', 'available'),
(1799, 28, 7, 'C8', 'C', 8, 'ROOM_28_C_PAIR_4', 'available'),
(1800, 28, 7, 'C9', 'C', 9, 'ROOM_28_C_PAIR_5', 'available'),
(1801, 28, 7, 'C10', 'C', 10, 'ROOM_28_C_PAIR_5', 'available'),
(1802, 28, 7, 'D1', 'D', 1, 'ROOM_28_D_PAIR_1', 'available'),
(1803, 28, 7, 'D2', 'D', 2, 'ROOM_28_D_PAIR_1', 'available'),
(1804, 28, 7, 'D3', 'D', 3, 'ROOM_28_D_PAIR_2', 'available'),
(1805, 28, 7, 'D4', 'D', 4, 'ROOM_28_D_PAIR_2', 'available'),
(1806, 28, 7, 'D5', 'D', 5, 'ROOM_28_D_PAIR_3', 'available'),
(1807, 28, 7, 'D6', 'D', 6, 'ROOM_28_D_PAIR_3', 'available'),
(1808, 28, 7, 'D7', 'D', 7, 'ROOM_28_D_PAIR_4', 'available'),
(1809, 28, 7, 'D8', 'D', 8, 'ROOM_28_D_PAIR_4', 'available'),
(1810, 28, 7, 'D9', 'D', 9, 'ROOM_28_D_PAIR_5', 'available'),
(1811, 28, 7, 'D10', 'D', 10, 'ROOM_28_D_PAIR_5', 'available'),
(1812, 28, 7, 'E1', 'E', 1, 'ROOM_28_E_PAIR_1', 'available'),
(1813, 28, 7, 'E2', 'E', 2, 'ROOM_28_E_PAIR_1', 'available'),
(1814, 28, 7, 'E3', 'E', 3, 'ROOM_28_E_PAIR_2', 'available'),
(1815, 28, 7, 'E4', 'E', 4, 'ROOM_28_E_PAIR_2', 'available'),
(1816, 28, 7, 'E5', 'E', 5, 'ROOM_28_E_PAIR_3', 'available'),
(1817, 28, 7, 'E6', 'E', 6, 'ROOM_28_E_PAIR_3', 'available'),
(1818, 28, 7, 'E7', 'E', 7, 'ROOM_28_E_PAIR_4', 'available'),
(1819, 28, 7, 'E8', 'E', 8, 'ROOM_28_E_PAIR_4', 'available'),
(1820, 28, 7, 'E9', 'E', 9, 'ROOM_28_E_PAIR_5', 'available'),
(1821, 28, 7, 'E10', 'E', 10, 'ROOM_28_E_PAIR_5', 'available'),
(1822, 28, 7, 'F1', 'F', 1, 'ROOM_28_F_PAIR_1', 'available'),
(1823, 28, 7, 'F2', 'F', 2, 'ROOM_28_F_PAIR_1', 'available'),
(1824, 28, 7, 'F3', 'F', 3, 'ROOM_28_F_PAIR_2', 'available'),
(1825, 28, 7, 'F4', 'F', 4, 'ROOM_28_F_PAIR_2', 'available'),
(1826, 28, 7, 'F5', 'F', 5, 'ROOM_28_F_PAIR_3', 'available'),
(1827, 28, 7, 'F6', 'F', 6, 'ROOM_28_F_PAIR_3', 'available'),
(1828, 28, 7, 'F7', 'F', 7, 'ROOM_28_F_PAIR_4', 'available'),
(1829, 28, 7, 'F8', 'F', 8, 'ROOM_28_F_PAIR_4', 'available'),
(1830, 28, 7, 'F9', 'F', 9, 'ROOM_28_F_PAIR_5', 'available'),
(1831, 28, 7, 'F10', 'F', 10, 'ROOM_28_F_PAIR_5', 'available'),
(1832, 29, 7, 'A1', 'A', 1, 'ROOM_29_A_PAIR_1', 'available'),
(1833, 29, 7, 'A2', 'A', 2, 'ROOM_29_A_PAIR_1', 'available'),
(1834, 29, 7, 'A3', 'A', 3, 'ROOM_29_A_PAIR_2', 'available'),
(1835, 29, 7, 'A4', 'A', 4, 'ROOM_29_A_PAIR_2', 'available'),
(1836, 29, 7, 'A5', 'A', 5, 'ROOM_29_A_PAIR_3', 'available'),
(1837, 29, 7, 'A6', 'A', 6, 'ROOM_29_A_PAIR_3', 'available'),
(1838, 29, 7, 'A7', 'A', 7, 'ROOM_29_A_PAIR_4', 'available'),
(1839, 29, 7, 'A8', 'A', 8, 'ROOM_29_A_PAIR_4', 'available'),
(1840, 29, 7, 'A9', 'A', 9, 'ROOM_29_A_PAIR_5', 'available'),
(1841, 29, 7, 'A10', 'A', 10, 'ROOM_29_A_PAIR_5', 'available'),
(1842, 29, 7, 'B1', 'B', 1, 'ROOM_29_B_PAIR_1', 'available'),
(1843, 29, 7, 'B2', 'B', 2, 'ROOM_29_B_PAIR_1', 'available'),
(1844, 29, 7, 'B3', 'B', 3, 'ROOM_29_B_PAIR_2', 'available'),
(1845, 29, 7, 'B4', 'B', 4, 'ROOM_29_B_PAIR_2', 'available'),
(1846, 29, 7, 'B5', 'B', 5, 'ROOM_29_B_PAIR_3', 'available'),
(1847, 29, 7, 'B6', 'B', 6, 'ROOM_29_B_PAIR_3', 'available'),
(1848, 29, 7, 'B7', 'B', 7, 'ROOM_29_B_PAIR_4', 'available'),
(1849, 29, 7, 'B8', 'B', 8, 'ROOM_29_B_PAIR_4', 'available'),
(1850, 29, 7, 'B9', 'B', 9, 'ROOM_29_B_PAIR_5', 'available'),
(1851, 29, 7, 'B10', 'B', 10, 'ROOM_29_B_PAIR_5', 'available'),
(1852, 29, 7, 'C1', 'C', 1, 'ROOM_29_C_PAIR_1', 'available'),
(1853, 29, 7, 'C2', 'C', 2, 'ROOM_29_C_PAIR_1', 'available'),
(1854, 29, 7, 'C3', 'C', 3, 'ROOM_29_C_PAIR_2', 'available'),
(1855, 29, 7, 'C4', 'C', 4, 'ROOM_29_C_PAIR_2', 'available'),
(1856, 29, 7, 'C5', 'C', 5, 'ROOM_29_C_PAIR_3', 'available'),
(1857, 29, 7, 'C6', 'C', 6, 'ROOM_29_C_PAIR_3', 'available'),
(1858, 29, 7, 'C7', 'C', 7, 'ROOM_29_C_PAIR_4', 'available'),
(1859, 29, 7, 'C8', 'C', 8, 'ROOM_29_C_PAIR_4', 'available'),
(1860, 29, 7, 'C9', 'C', 9, 'ROOM_29_C_PAIR_5', 'available'),
(1861, 29, 7, 'C10', 'C', 10, 'ROOM_29_C_PAIR_5', 'available'),
(1862, 29, 7, 'D1', 'D', 1, 'ROOM_29_D_PAIR_1', 'available'),
(1863, 29, 7, 'D2', 'D', 2, 'ROOM_29_D_PAIR_1', 'available'),
(1864, 29, 7, 'D3', 'D', 3, 'ROOM_29_D_PAIR_2', 'available'),
(1865, 29, 7, 'D4', 'D', 4, 'ROOM_29_D_PAIR_2', 'available'),
(1866, 29, 7, 'D5', 'D', 5, 'ROOM_29_D_PAIR_3', 'available'),
(1867, 29, 7, 'D6', 'D', 6, 'ROOM_29_D_PAIR_3', 'available'),
(1868, 29, 7, 'D7', 'D', 7, 'ROOM_29_D_PAIR_4', 'available'),
(1869, 29, 7, 'D8', 'D', 8, 'ROOM_29_D_PAIR_4', 'available'),
(1870, 29, 7, 'D9', 'D', 9, 'ROOM_29_D_PAIR_5', 'available'),
(1871, 29, 7, 'D10', 'D', 10, 'ROOM_29_D_PAIR_5', 'available'),
(1872, 29, 7, 'E1', 'E', 1, 'ROOM_29_E_PAIR_1', 'available'),
(1873, 29, 7, 'E2', 'E', 2, 'ROOM_29_E_PAIR_1', 'available'),
(1874, 29, 7, 'E3', 'E', 3, 'ROOM_29_E_PAIR_2', 'available'),
(1875, 29, 7, 'E4', 'E', 4, 'ROOM_29_E_PAIR_2', 'available'),
(1876, 29, 7, 'E5', 'E', 5, 'ROOM_29_E_PAIR_3', 'available'),
(1877, 29, 7, 'E6', 'E', 6, 'ROOM_29_E_PAIR_3', 'available'),
(1878, 29, 7, 'E7', 'E', 7, 'ROOM_29_E_PAIR_4', 'available'),
(1879, 29, 7, 'E8', 'E', 8, 'ROOM_29_E_PAIR_4', 'available'),
(1880, 29, 7, 'E9', 'E', 9, 'ROOM_29_E_PAIR_5', 'available'),
(1881, 29, 7, 'E10', 'E', 10, 'ROOM_29_E_PAIR_5', 'available'),
(1882, 29, 7, 'F1', 'F', 1, 'ROOM_29_F_PAIR_1', 'available'),
(1883, 29, 7, 'F2', 'F', 2, 'ROOM_29_F_PAIR_1', 'available'),
(1884, 29, 7, 'F3', 'F', 3, 'ROOM_29_F_PAIR_2', 'available'),
(1885, 29, 7, 'F4', 'F', 4, 'ROOM_29_F_PAIR_2', 'available'),
(1886, 29, 7, 'F5', 'F', 5, 'ROOM_29_F_PAIR_3', 'available'),
(1887, 29, 7, 'F6', 'F', 6, 'ROOM_29_F_PAIR_3', 'available'),
(1888, 29, 7, 'F7', 'F', 7, 'ROOM_29_F_PAIR_4', 'available'),
(1889, 29, 7, 'F8', 'F', 8, 'ROOM_29_F_PAIR_4', 'available'),
(1890, 29, 7, 'F9', 'F', 9, 'ROOM_29_F_PAIR_5', 'available'),
(1891, 29, 7, 'F10', 'F', 10, 'ROOM_29_F_PAIR_5', 'available'),
(1892, 30, 5, 'A1', 'A', 1, NULL, 'available'),
(1893, 30, 5, 'A2', 'A', 2, NULL, 'available'),
(1894, 30, 5, 'A3', 'A', 3, NULL, 'available'),
(1895, 30, 5, 'A4', 'A', 4, NULL, 'available'),
(1896, 30, 5, 'A5', 'A', 5, NULL, 'available'),
(1897, 30, 5, 'A6', 'A', 6, NULL, 'available'),
(1898, 30, 5, 'A7', 'A', 7, NULL, 'available'),
(1899, 30, 5, 'A8', 'A', 8, NULL, 'available'),
(1900, 30, 5, 'A9', 'A', 9, NULL, 'available'),
(1901, 30, 5, 'A10', 'A', 10, NULL, 'available'),
(1902, 30, 5, 'B1', 'B', 1, NULL, 'available'),
(1903, 30, 5, 'B2', 'B', 2, NULL, 'available'),
(1904, 30, 5, 'B3', 'B', 3, NULL, 'available'),
(1905, 30, 5, 'B4', 'B', 4, NULL, 'available'),
(1906, 30, 5, 'B5', 'B', 5, NULL, 'available'),
(1907, 30, 5, 'B6', 'B', 6, NULL, 'available'),
(1908, 30, 5, 'B7', 'B', 7, NULL, 'available'),
(1909, 30, 5, 'B8', 'B', 8, NULL, 'available'),
(1910, 30, 5, 'B9', 'B', 9, NULL, 'available'),
(1911, 30, 5, 'B10', 'B', 10, NULL, 'available'),
(1912, 30, 5, 'C1', 'C', 1, NULL, 'available'),
(1913, 30, 5, 'C2', 'C', 2, NULL, 'available'),
(1914, 30, 5, 'C3', 'C', 3, NULL, 'available'),
(1915, 30, 5, 'C4', 'C', 4, NULL, 'available'),
(1916, 30, 5, 'C5', 'C', 5, NULL, 'available'),
(1917, 30, 5, 'C6', 'C', 6, NULL, 'available'),
(1918, 30, 5, 'C7', 'C', 7, NULL, 'available'),
(1919, 30, 5, 'C8', 'C', 8, NULL, 'available'),
(1920, 30, 5, 'C9', 'C', 9, NULL, 'available'),
(1921, 30, 5, 'C10', 'C', 10, NULL, 'available'),
(1922, 30, 6, 'D1', 'D', 1, NULL, 'available'),
(1923, 30, 6, 'D2', 'D', 2, NULL, 'available'),
(1924, 30, 6, 'D3', 'D', 3, NULL, 'available'),
(1925, 30, 6, 'D4', 'D', 4, NULL, 'available'),
(1926, 30, 6, 'D5', 'D', 5, NULL, 'available'),
(1927, 30, 6, 'D6', 'D', 6, NULL, 'available'),
(1928, 30, 6, 'D7', 'D', 7, NULL, 'available'),
(1929, 30, 6, 'D8', 'D', 8, NULL, 'available'),
(1930, 30, 6, 'D9', 'D', 9, NULL, 'available'),
(1931, 30, 6, 'D10', 'D', 10, NULL, 'available'),
(1932, 30, 6, 'E1', 'E', 1, NULL, 'available'),
(1933, 30, 6, 'E2', 'E', 2, NULL, 'available'),
(1934, 30, 6, 'E3', 'E', 3, NULL, 'available'),
(1935, 30, 6, 'E4', 'E', 4, NULL, 'available'),
(1936, 30, 6, 'E5', 'E', 5, NULL, 'available'),
(1937, 30, 6, 'E6', 'E', 6, NULL, 'available'),
(1938, 30, 6, 'E7', 'E', 7, NULL, 'available'),
(1939, 30, 6, 'E8', 'E', 8, NULL, 'available'),
(1940, 30, 6, 'E9', 'E', 9, NULL, 'available'),
(1941, 30, 6, 'E10', 'E', 10, NULL, 'available'),
(1942, 30, 6, 'F1', 'F', 1, NULL, 'available'),
(1943, 30, 6, 'F2', 'F', 2, NULL, 'available'),
(1944, 30, 6, 'F3', 'F', 3, NULL, 'available'),
(1945, 30, 6, 'F4', 'F', 4, NULL, 'available'),
(1946, 30, 6, 'F5', 'F', 5, NULL, 'available'),
(1947, 30, 6, 'F6', 'F', 6, NULL, 'available'),
(1948, 30, 6, 'F7', 'F', 7, NULL, 'available'),
(1949, 30, 6, 'F8', 'F', 8, NULL, 'available'),
(1950, 30, 6, 'F9', 'F', 9, NULL, 'available'),
(1951, 30, 6, 'F10', 'F', 10, NULL, 'available'),
(1952, 30, 6, 'G1', 'G', 1, NULL, 'available'),
(1953, 30, 6, 'G2', 'G', 2, NULL, 'available'),
(1954, 30, 6, 'G3', 'G', 3, NULL, 'available'),
(1955, 30, 6, 'G4', 'G', 4, NULL, 'available'),
(1956, 30, 6, 'G5', 'G', 5, NULL, 'available'),
(1957, 30, 6, 'G6', 'G', 6, NULL, 'available'),
(1958, 30, 6, 'G7', 'G', 7, NULL, 'available'),
(1959, 30, 6, 'G8', 'G', 8, NULL, 'available'),
(1960, 30, 6, 'G9', 'G', 9, NULL, 'available'),
(1961, 30, 6, 'G10', 'G', 10, NULL, 'available'),
(1962, 30, 6, 'H1', 'H', 1, NULL, 'available'),
(1963, 30, 6, 'H2', 'H', 2, NULL, 'available'),
(1964, 30, 6, 'H3', 'H', 3, NULL, 'available'),
(1965, 30, 6, 'H4', 'H', 4, NULL, 'available'),
(1966, 30, 6, 'H5', 'H', 5, NULL, 'available'),
(1967, 30, 6, 'H6', 'H', 6, NULL, 'available'),
(1968, 30, 6, 'H7', 'H', 7, NULL, 'available'),
(1969, 30, 6, 'H8', 'H', 8, NULL, 'available'),
(1970, 30, 6, 'H9', 'H', 9, NULL, 'available'),
(1971, 30, 6, 'H10', 'H', 10, NULL, 'available'),
(1972, 30, 6, 'I1', 'I', 1, NULL, 'available'),
(1973, 30, 6, 'I2', 'I', 2, NULL, 'available'),
(1974, 30, 6, 'I3', 'I', 3, NULL, 'available'),
(1975, 30, 6, 'I4', 'I', 4, NULL, 'available'),
(1976, 30, 6, 'I5', 'I', 5, NULL, 'available'),
(1977, 30, 6, 'I6', 'I', 6, NULL, 'available'),
(1978, 30, 6, 'I7', 'I', 7, NULL, 'available'),
(1979, 30, 6, 'I8', 'I', 8, NULL, 'available'),
(1980, 30, 6, 'I9', 'I', 9, NULL, 'available'),
(1981, 30, 6, 'I10', 'I', 10, NULL, 'available'),
(1982, 30, 7, 'J1', 'J', 1, 'ROOM_30_J_PAIR_1', 'available'),
(1983, 30, 7, 'J2', 'J', 2, 'ROOM_30_J_PAIR_1', 'available'),
(1984, 30, 7, 'J3', 'J', 3, 'ROOM_30_J_PAIR_2', 'available'),
(1985, 30, 7, 'J4', 'J', 4, 'ROOM_30_J_PAIR_2', 'available'),
(1986, 30, 7, 'J5', 'J', 5, 'ROOM_30_J_PAIR_3', 'available'),
(1987, 30, 7, 'J6', 'J', 6, 'ROOM_30_J_PAIR_3', 'available'),
(1988, 30, 7, 'J7', 'J', 7, 'ROOM_30_J_PAIR_4', 'available'),
(1989, 30, 7, 'J8', 'J', 8, 'ROOM_30_J_PAIR_4', 'available'),
(1990, 30, 7, 'J9', 'J', 9, 'ROOM_30_J_PAIR_5', 'available'),
(1991, 30, 7, 'J10', 'J', 10, 'ROOM_30_J_PAIR_5', 'available'),
(1992, 31, 5, 'A1', 'A', 1, NULL, 'available'),
(1993, 31, 5, 'A2', 'A', 2, NULL, 'available'),
(1994, 31, 5, 'A3', 'A', 3, NULL, 'available'),
(1995, 31, 5, 'A4', 'A', 4, NULL, 'available'),
(1996, 31, 5, 'A5', 'A', 5, NULL, 'available'),
(1997, 31, 5, 'A6', 'A', 6, NULL, 'available'),
(1998, 31, 5, 'A7', 'A', 7, NULL, 'available'),
(1999, 31, 5, 'A8', 'A', 8, NULL, 'available'),
(2000, 31, 5, 'A9', 'A', 9, NULL, 'available'),
(2001, 31, 5, 'A10', 'A', 10, NULL, 'available'),
(2002, 31, 5, 'A11', 'A', 11, NULL, 'available'),
(2003, 31, 5, 'A12', 'A', 12, NULL, 'available'),
(2004, 31, 5, 'B1', 'B', 1, NULL, 'available'),
(2005, 31, 5, 'B2', 'B', 2, NULL, 'available'),
(2006, 31, 5, 'B3', 'B', 3, NULL, 'available'),
(2007, 31, 5, 'B4', 'B', 4, NULL, 'available'),
(2008, 31, 5, 'B5', 'B', 5, NULL, 'available'),
(2009, 31, 5, 'B6', 'B', 6, NULL, 'available'),
(2010, 31, 5, 'B7', 'B', 7, NULL, 'available'),
(2011, 31, 5, 'B8', 'B', 8, NULL, 'available'),
(2012, 31, 5, 'B9', 'B', 9, NULL, 'available'),
(2013, 31, 5, 'B10', 'B', 10, NULL, 'available'),
(2014, 31, 5, 'B11', 'B', 11, NULL, 'available'),
(2015, 31, 5, 'B12', 'B', 12, NULL, 'available'),
(2016, 31, 5, 'C1', 'C', 1, NULL, 'available'),
(2017, 31, 5, 'C2', 'C', 2, NULL, 'available'),
(2018, 31, 5, 'C3', 'C', 3, NULL, 'available'),
(2019, 31, 5, 'C4', 'C', 4, NULL, 'available'),
(2020, 31, 5, 'C5', 'C', 5, NULL, 'available'),
(2021, 31, 5, 'C6', 'C', 6, NULL, 'available'),
(2022, 31, 5, 'C7', 'C', 7, NULL, 'available'),
(2023, 31, 5, 'C8', 'C', 8, NULL, 'available'),
(2024, 31, 5, 'C9', 'C', 9, NULL, 'available'),
(2025, 31, 5, 'C10', 'C', 10, NULL, 'available'),
(2026, 31, 5, 'C11', 'C', 11, NULL, 'available'),
(2027, 31, 5, 'C12', 'C', 12, NULL, 'available'),
(2028, 31, 6, 'D1', 'D', 1, NULL, 'available'),
(2029, 31, 6, 'D2', 'D', 2, NULL, 'available'),
(2030, 31, 6, 'D3', 'D', 3, NULL, 'available'),
(2031, 31, 6, 'D4', 'D', 4, NULL, 'available'),
(2032, 31, 6, 'D5', 'D', 5, NULL, 'available'),
(2033, 31, 6, 'D6', 'D', 6, NULL, 'available'),
(2034, 31, 6, 'D7', 'D', 7, NULL, 'available'),
(2035, 31, 6, 'D8', 'D', 8, NULL, 'available'),
(2036, 31, 6, 'D9', 'D', 9, NULL, 'available'),
(2037, 31, 6, 'D10', 'D', 10, NULL, 'available'),
(2038, 31, 6, 'D11', 'D', 11, NULL, 'available'),
(2039, 31, 6, 'D12', 'D', 12, NULL, 'available'),
(2040, 31, 6, 'E1', 'E', 1, NULL, 'available'),
(2041, 31, 6, 'E2', 'E', 2, NULL, 'available'),
(2042, 31, 6, 'E3', 'E', 3, NULL, 'available'),
(2043, 31, 6, 'E4', 'E', 4, NULL, 'available'),
(2044, 31, 6, 'E5', 'E', 5, NULL, 'available'),
(2045, 31, 6, 'E6', 'E', 6, NULL, 'available'),
(2046, 31, 6, 'E7', 'E', 7, NULL, 'available'),
(2047, 31, 6, 'E8', 'E', 8, NULL, 'available'),
(2048, 31, 6, 'E9', 'E', 9, NULL, 'available'),
(2049, 31, 6, 'E10', 'E', 10, NULL, 'available'),
(2050, 31, 6, 'E11', 'E', 11, NULL, 'available'),
(2051, 31, 6, 'E12', 'E', 12, NULL, 'available'),
(2052, 31, 6, 'F1', 'F', 1, NULL, 'available'),
(2053, 31, 6, 'F2', 'F', 2, NULL, 'available'),
(2054, 31, 6, 'F3', 'F', 3, NULL, 'available'),
(2055, 31, 6, 'F4', 'F', 4, NULL, 'available'),
(2056, 31, 6, 'F5', 'F', 5, NULL, 'available'),
(2057, 31, 6, 'F6', 'F', 6, NULL, 'available'),
(2058, 31, 6, 'F7', 'F', 7, NULL, 'available'),
(2059, 31, 6, 'F8', 'F', 8, NULL, 'available'),
(2060, 31, 6, 'F9', 'F', 9, NULL, 'available'),
(2061, 31, 6, 'F10', 'F', 10, NULL, 'available'),
(2062, 31, 6, 'F11', 'F', 11, NULL, 'available'),
(2063, 31, 6, 'F12', 'F', 12, NULL, 'available'),
(2064, 31, 6, 'G1', 'G', 1, NULL, 'available'),
(2065, 31, 6, 'G2', 'G', 2, NULL, 'available'),
(2066, 31, 6, 'G3', 'G', 3, NULL, 'available'),
(2067, 31, 6, 'G4', 'G', 4, NULL, 'available'),
(2068, 31, 6, 'G5', 'G', 5, NULL, 'available'),
(2069, 31, 6, 'G6', 'G', 6, NULL, 'available'),
(2070, 31, 6, 'G7', 'G', 7, NULL, 'available'),
(2071, 31, 6, 'G8', 'G', 8, NULL, 'available'),
(2072, 31, 6, 'G9', 'G', 9, NULL, 'available'),
(2073, 31, 6, 'G10', 'G', 10, NULL, 'available'),
(2074, 31, 6, 'G11', 'G', 11, NULL, 'available'),
(2075, 31, 6, 'G12', 'G', 12, NULL, 'available'),
(2076, 31, 6, 'H1', 'H', 1, NULL, 'available'),
(2077, 31, 6, 'H2', 'H', 2, NULL, 'available'),
(2078, 31, 6, 'H3', 'H', 3, NULL, 'available'),
(2079, 31, 6, 'H4', 'H', 4, NULL, 'available'),
(2080, 31, 6, 'H5', 'H', 5, NULL, 'available'),
(2081, 31, 6, 'H6', 'H', 6, NULL, 'available'),
(2082, 31, 6, 'H7', 'H', 7, NULL, 'available'),
(2083, 31, 6, 'H8', 'H', 8, NULL, 'available'),
(2084, 31, 6, 'H9', 'H', 9, NULL, 'available'),
(2085, 31, 6, 'H10', 'H', 10, NULL, 'available'),
(2086, 31, 6, 'H11', 'H', 11, NULL, 'available'),
(2087, 31, 6, 'H12', 'H', 12, NULL, 'available'),
(2088, 31, 6, 'I1', 'I', 1, NULL, 'available'),
(2089, 31, 6, 'I2', 'I', 2, NULL, 'available'),
(2090, 31, 6, 'I3', 'I', 3, NULL, 'available'),
(2091, 31, 6, 'I4', 'I', 4, NULL, 'available'),
(2092, 31, 6, 'I5', 'I', 5, NULL, 'available'),
(2093, 31, 6, 'I6', 'I', 6, NULL, 'available'),
(2094, 31, 6, 'I7', 'I', 7, NULL, 'available'),
(2095, 31, 6, 'I8', 'I', 8, NULL, 'available'),
(2096, 31, 6, 'I9', 'I', 9, NULL, 'available'),
(2097, 31, 6, 'I10', 'I', 10, NULL, 'available'),
(2098, 31, 6, 'I11', 'I', 11, NULL, 'available'),
(2099, 31, 6, 'I12', 'I', 12, NULL, 'available'),
(2100, 31, 7, 'J1', 'J', 1, 'ROOM_31_J_PAIR_1', 'available'),
(2101, 31, 7, 'J2', 'J', 2, 'ROOM_31_J_PAIR_1', 'available'),
(2102, 31, 7, 'J3', 'J', 3, 'ROOM_31_J_PAIR_2', 'available'),
(2103, 31, 7, 'J4', 'J', 4, 'ROOM_31_J_PAIR_2', 'available'),
(2104, 31, 7, 'J5', 'J', 5, 'ROOM_31_J_PAIR_3', 'available'),
(2105, 31, 7, 'J6', 'J', 6, 'ROOM_31_J_PAIR_3', 'available'),
(2106, 31, 7, 'J7', 'J', 7, 'ROOM_31_J_PAIR_4', 'available'),
(2107, 31, 7, 'J8', 'J', 8, 'ROOM_31_J_PAIR_4', 'available'),
(2108, 31, 7, 'J9', 'J', 9, 'ROOM_31_J_PAIR_5', 'available'),
(2109, 31, 7, 'J10', 'J', 10, 'ROOM_31_J_PAIR_5', 'available'),
(2110, 31, 7, 'J11', 'J', 11, 'ROOM_31_J_PAIR_6', 'available'),
(2111, 31, 7, 'J12', 'J', 12, 'ROOM_31_J_PAIR_6', 'available'),
(2112, 32, 5, 'A1', 'A', 1, NULL, 'available'),
(2113, 32, 5, 'A2', 'A', 2, NULL, 'available'),
(2114, 32, 5, 'A3', 'A', 3, NULL, 'available'),
(2115, 32, 5, 'A4', 'A', 4, NULL, 'available'),
(2116, 32, 5, 'A5', 'A', 5, NULL, 'available'),
(2117, 32, 5, 'A6', 'A', 6, NULL, 'available'),
(2118, 32, 5, 'A7', 'A', 7, NULL, 'available'),
(2119, 32, 5, 'A8', 'A', 8, NULL, 'available'),
(2120, 32, 5, 'A9', 'A', 9, NULL, 'available'),
(2121, 32, 5, 'A10', 'A', 10, NULL, 'available'),
(2122, 32, 5, 'B1', 'B', 1, NULL, 'available'),
(2123, 32, 5, 'B2', 'B', 2, NULL, 'available'),
(2124, 32, 5, 'B3', 'B', 3, NULL, 'available'),
(2125, 32, 5, 'B4', 'B', 4, NULL, 'available'),
(2126, 32, 5, 'B5', 'B', 5, NULL, 'available'),
(2127, 32, 5, 'B6', 'B', 6, NULL, 'available'),
(2128, 32, 5, 'B7', 'B', 7, NULL, 'available'),
(2129, 32, 5, 'B8', 'B', 8, NULL, 'available'),
(2130, 32, 5, 'B9', 'B', 9, NULL, 'available'),
(2131, 32, 5, 'B10', 'B', 10, NULL, 'available'),
(2132, 32, 5, 'C1', 'C', 1, NULL, 'available'),
(2133, 32, 5, 'C2', 'C', 2, NULL, 'available'),
(2134, 32, 5, 'C3', 'C', 3, NULL, 'available'),
(2135, 32, 5, 'C4', 'C', 4, NULL, 'available'),
(2136, 32, 5, 'C5', 'C', 5, NULL, 'available'),
(2137, 32, 5, 'C6', 'C', 6, NULL, 'available'),
(2138, 32, 5, 'C7', 'C', 7, NULL, 'available'),
(2139, 32, 5, 'C8', 'C', 8, NULL, 'available'),
(2140, 32, 5, 'C9', 'C', 9, NULL, 'available'),
(2141, 32, 5, 'C10', 'C', 10, NULL, 'available'),
(2142, 32, 6, 'D1', 'D', 1, NULL, 'available'),
(2143, 32, 6, 'D2', 'D', 2, NULL, 'available'),
(2144, 32, 6, 'D3', 'D', 3, NULL, 'available'),
(2145, 32, 6, 'D4', 'D', 4, NULL, 'available'),
(2146, 32, 6, 'D5', 'D', 5, NULL, 'available'),
(2147, 32, 6, 'D6', 'D', 6, NULL, 'available'),
(2148, 32, 6, 'D7', 'D', 7, NULL, 'available'),
(2149, 32, 6, 'D8', 'D', 8, NULL, 'available'),
(2150, 32, 6, 'D9', 'D', 9, NULL, 'available'),
(2151, 32, 6, 'D10', 'D', 10, NULL, 'available'),
(2152, 32, 6, 'E1', 'E', 1, NULL, 'available'),
(2153, 32, 6, 'E2', 'E', 2, NULL, 'available'),
(2154, 32, 6, 'E3', 'E', 3, NULL, 'available'),
(2155, 32, 6, 'E4', 'E', 4, NULL, 'available'),
(2156, 32, 6, 'E5', 'E', 5, NULL, 'available'),
(2157, 32, 6, 'E6', 'E', 6, NULL, 'available'),
(2158, 32, 6, 'E7', 'E', 7, NULL, 'available'),
(2159, 32, 6, 'E8', 'E', 8, NULL, 'available'),
(2160, 32, 6, 'E9', 'E', 9, NULL, 'available'),
(2161, 32, 6, 'E10', 'E', 10, NULL, 'available'),
(2162, 32, 6, 'F1', 'F', 1, NULL, 'available'),
(2163, 32, 6, 'F2', 'F', 2, NULL, 'available'),
(2164, 32, 6, 'F3', 'F', 3, NULL, 'available'),
(2165, 32, 6, 'F4', 'F', 4, NULL, 'available'),
(2166, 32, 6, 'F5', 'F', 5, NULL, 'available'),
(2167, 32, 6, 'F6', 'F', 6, NULL, 'available'),
(2168, 32, 6, 'F7', 'F', 7, NULL, 'available'),
(2169, 32, 6, 'F8', 'F', 8, NULL, 'available'),
(2170, 32, 6, 'F9', 'F', 9, NULL, 'available'),
(2171, 32, 6, 'F10', 'F', 10, NULL, 'available'),
(2172, 32, 6, 'G1', 'G', 1, NULL, 'available'),
(2173, 32, 6, 'G2', 'G', 2, NULL, 'available'),
(2174, 32, 6, 'G3', 'G', 3, NULL, 'available'),
(2175, 32, 6, 'G4', 'G', 4, NULL, 'available'),
(2176, 32, 6, 'G5', 'G', 5, NULL, 'available'),
(2177, 32, 6, 'G6', 'G', 6, NULL, 'available'),
(2178, 32, 6, 'G7', 'G', 7, NULL, 'available'),
(2179, 32, 6, 'G8', 'G', 8, NULL, 'available'),
(2180, 32, 6, 'G9', 'G', 9, NULL, 'available'),
(2181, 32, 6, 'G10', 'G', 10, NULL, 'available'),
(2182, 32, 6, 'H1', 'H', 1, NULL, 'available'),
(2183, 32, 6, 'H2', 'H', 2, NULL, 'available'),
(2184, 32, 6, 'H3', 'H', 3, NULL, 'available'),
(2185, 32, 6, 'H4', 'H', 4, NULL, 'available'),
(2186, 32, 6, 'H5', 'H', 5, NULL, 'available'),
(2187, 32, 6, 'H6', 'H', 6, NULL, 'available'),
(2188, 32, 6, 'H7', 'H', 7, NULL, 'available'),
(2189, 32, 6, 'H8', 'H', 8, NULL, 'available'),
(2190, 32, 6, 'H9', 'H', 9, NULL, 'available'),
(2191, 32, 6, 'H10', 'H', 10, NULL, 'available'),
(2192, 32, 6, 'I1', 'I', 1, NULL, 'available'),
(2193, 32, 6, 'I2', 'I', 2, NULL, 'available'),
(2194, 32, 6, 'I3', 'I', 3, NULL, 'available'),
(2195, 32, 6, 'I4', 'I', 4, NULL, 'available'),
(2196, 32, 6, 'I5', 'I', 5, NULL, 'available'),
(2197, 32, 6, 'I6', 'I', 6, NULL, 'available'),
(2198, 32, 6, 'I7', 'I', 7, NULL, 'available'),
(2199, 32, 6, 'I8', 'I', 8, NULL, 'available'),
(2200, 32, 6, 'I9', 'I', 9, NULL, 'available'),
(2201, 32, 6, 'I10', 'I', 10, NULL, 'available'),
(2202, 32, 7, 'J1', 'J', 1, 'ROOM_32_J_PAIR_1', 'available'),
(2203, 32, 7, 'J2', 'J', 2, 'ROOM_32_J_PAIR_1', 'available'),
(2204, 32, 7, 'J3', 'J', 3, 'ROOM_32_J_PAIR_2', 'available'),
(2205, 32, 7, 'J4', 'J', 4, 'ROOM_32_J_PAIR_2', 'available'),
(2206, 32, 7, 'J5', 'J', 5, 'ROOM_32_J_PAIR_3', 'available'),
(2207, 32, 7, 'J6', 'J', 6, 'ROOM_32_J_PAIR_3', 'available'),
(2208, 32, 7, 'J7', 'J', 7, 'ROOM_32_J_PAIR_4', 'available'),
(2209, 32, 7, 'J8', 'J', 8, 'ROOM_32_J_PAIR_4', 'available'),
(2210, 32, 7, 'J9', 'J', 9, 'ROOM_32_J_PAIR_5', 'available'),
(2211, 32, 7, 'J10', 'J', 10, 'ROOM_32_J_PAIR_5', 'available'),
(2212, 33, 5, 'A1', 'A', 1, NULL, 'available'),
(2213, 33, 5, 'A2', 'A', 2, NULL, 'available'),
(2214, 33, 5, 'A3', 'A', 3, NULL, 'available'),
(2215, 33, 5, 'A4', 'A', 4, NULL, 'available'),
(2216, 33, 5, 'A5', 'A', 5, NULL, 'available'),
(2217, 33, 5, 'A6', 'A', 6, NULL, 'available'),
(2218, 33, 5, 'A7', 'A', 7, NULL, 'available'),
(2219, 33, 5, 'A8', 'A', 8, NULL, 'available'),
(2220, 33, 5, 'A9', 'A', 9, NULL, 'available'),
(2221, 33, 5, 'A10', 'A', 10, NULL, 'available'),
(2222, 33, 5, 'B1', 'B', 1, NULL, 'available'),
(2223, 33, 5, 'B2', 'B', 2, NULL, 'available'),
(2224, 33, 5, 'B3', 'B', 3, NULL, 'available'),
(2225, 33, 5, 'B4', 'B', 4, NULL, 'available'),
(2226, 33, 5, 'B5', 'B', 5, NULL, 'available'),
(2227, 33, 5, 'B6', 'B', 6, NULL, 'available'),
(2228, 33, 5, 'B7', 'B', 7, NULL, 'available'),
(2229, 33, 5, 'B8', 'B', 8, NULL, 'available'),
(2230, 33, 5, 'B9', 'B', 9, NULL, 'available'),
(2231, 33, 5, 'B10', 'B', 10, NULL, 'available'),
(2232, 33, 5, 'C1', 'C', 1, NULL, 'available'),
(2233, 33, 5, 'C2', 'C', 2, NULL, 'available'),
(2234, 33, 5, 'C3', 'C', 3, NULL, 'available'),
(2235, 33, 5, 'C4', 'C', 4, NULL, 'available'),
(2236, 33, 5, 'C5', 'C', 5, NULL, 'available'),
(2237, 33, 5, 'C6', 'C', 6, NULL, 'available'),
(2238, 33, 5, 'C7', 'C', 7, NULL, 'available'),
(2239, 33, 5, 'C8', 'C', 8, NULL, 'available'),
(2240, 33, 5, 'C9', 'C', 9, NULL, 'available'),
(2241, 33, 5, 'C10', 'C', 10, NULL, 'available'),
(2242, 33, 6, 'D1', 'D', 1, NULL, 'available'),
(2243, 33, 6, 'D2', 'D', 2, NULL, 'available'),
(2244, 33, 6, 'D3', 'D', 3, NULL, 'available'),
(2245, 33, 6, 'D4', 'D', 4, NULL, 'available'),
(2246, 33, 6, 'D5', 'D', 5, NULL, 'available'),
(2247, 33, 6, 'D6', 'D', 6, NULL, 'available'),
(2248, 33, 6, 'D7', 'D', 7, NULL, 'available'),
(2249, 33, 6, 'D8', 'D', 8, NULL, 'available'),
(2250, 33, 6, 'D9', 'D', 9, NULL, 'available'),
(2251, 33, 6, 'D10', 'D', 10, NULL, 'available'),
(2252, 33, 6, 'E1', 'E', 1, NULL, 'available'),
(2253, 33, 6, 'E2', 'E', 2, NULL, 'available'),
(2254, 33, 6, 'E3', 'E', 3, NULL, 'available'),
(2255, 33, 6, 'E4', 'E', 4, NULL, 'available'),
(2256, 33, 6, 'E5', 'E', 5, NULL, 'available'),
(2257, 33, 6, 'E6', 'E', 6, NULL, 'available'),
(2258, 33, 6, 'E7', 'E', 7, NULL, 'available'),
(2259, 33, 6, 'E8', 'E', 8, NULL, 'available'),
(2260, 33, 6, 'E9', 'E', 9, NULL, 'available'),
(2261, 33, 6, 'E10', 'E', 10, NULL, 'available'),
(2262, 33, 6, 'F1', 'F', 1, NULL, 'available'),
(2263, 33, 6, 'F2', 'F', 2, NULL, 'available'),
(2264, 33, 6, 'F3', 'F', 3, NULL, 'available'),
(2265, 33, 6, 'F4', 'F', 4, NULL, 'available'),
(2266, 33, 6, 'F5', 'F', 5, NULL, 'available'),
(2267, 33, 6, 'F6', 'F', 6, NULL, 'available'),
(2268, 33, 6, 'F7', 'F', 7, NULL, 'available'),
(2269, 33, 6, 'F8', 'F', 8, NULL, 'available'),
(2270, 33, 6, 'F9', 'F', 9, NULL, 'available'),
(2271, 33, 6, 'F10', 'F', 10, NULL, 'available'),
(2272, 33, 6, 'G1', 'G', 1, NULL, 'available'),
(2273, 33, 6, 'G2', 'G', 2, NULL, 'available'),
(2274, 33, 6, 'G3', 'G', 3, NULL, 'available'),
(2275, 33, 6, 'G4', 'G', 4, NULL, 'available'),
(2276, 33, 6, 'G5', 'G', 5, NULL, 'available'),
(2277, 33, 6, 'G6', 'G', 6, NULL, 'available'),
(2278, 33, 6, 'G7', 'G', 7, NULL, 'available'),
(2279, 33, 6, 'G8', 'G', 8, NULL, 'available'),
(2280, 33, 6, 'G9', 'G', 9, NULL, 'available'),
(2281, 33, 6, 'G10', 'G', 10, NULL, 'available'),
(2282, 33, 6, 'H1', 'H', 1, NULL, 'available'),
(2283, 33, 6, 'H2', 'H', 2, NULL, 'available'),
(2284, 33, 6, 'H3', 'H', 3, NULL, 'available'),
(2285, 33, 6, 'H4', 'H', 4, NULL, 'available'),
(2286, 33, 6, 'H5', 'H', 5, NULL, 'available'),
(2287, 33, 6, 'H6', 'H', 6, NULL, 'available'),
(2288, 33, 6, 'H7', 'H', 7, NULL, 'available'),
(2289, 33, 6, 'H8', 'H', 8, NULL, 'available'),
(2290, 33, 6, 'H9', 'H', 9, NULL, 'available'),
(2291, 33, 6, 'H10', 'H', 10, NULL, 'available'),
(2292, 33, 6, 'I1', 'I', 1, NULL, 'available'),
(2293, 33, 6, 'I2', 'I', 2, NULL, 'available'),
(2294, 33, 6, 'I3', 'I', 3, NULL, 'available'),
(2295, 33, 6, 'I4', 'I', 4, NULL, 'available'),
(2296, 33, 6, 'I5', 'I', 5, NULL, 'available'),
(2297, 33, 6, 'I6', 'I', 6, NULL, 'available'),
(2298, 33, 6, 'I7', 'I', 7, NULL, 'available'),
(2299, 33, 6, 'I8', 'I', 8, NULL, 'available'),
(2300, 33, 6, 'I9', 'I', 9, NULL, 'available'),
(2301, 33, 6, 'I10', 'I', 10, NULL, 'available'),
(2302, 33, 7, 'J1', 'J', 1, 'ROOM_33_J_PAIR_1', 'available'),
(2303, 33, 7, 'J2', 'J', 2, 'ROOM_33_J_PAIR_1', 'available'),
(2304, 33, 7, 'J3', 'J', 3, 'ROOM_33_J_PAIR_2', 'available'),
(2305, 33, 7, 'J4', 'J', 4, 'ROOM_33_J_PAIR_2', 'available'),
(2306, 33, 7, 'J5', 'J', 5, 'ROOM_33_J_PAIR_3', 'available'),
(2307, 33, 7, 'J6', 'J', 6, 'ROOM_33_J_PAIR_3', 'available'),
(2308, 33, 7, 'J7', 'J', 7, 'ROOM_33_J_PAIR_4', 'available'),
(2309, 33, 7, 'J8', 'J', 8, 'ROOM_33_J_PAIR_4', 'available'),
(2310, 33, 7, 'J9', 'J', 9, 'ROOM_33_J_PAIR_5', 'available'),
(2311, 33, 7, 'J10', 'J', 10, 'ROOM_33_J_PAIR_5', 'available'),
(2312, 34, 5, 'A1', 'A', 1, NULL, 'available'),
(2313, 34, 5, 'A2', 'A', 2, NULL, 'available'),
(2314, 34, 5, 'A3', 'A', 3, NULL, 'available'),
(2315, 34, 5, 'A4', 'A', 4, NULL, 'available'),
(2316, 34, 5, 'A5', 'A', 5, NULL, 'available'),
(2317, 34, 5, 'A6', 'A', 6, NULL, 'available'),
(2318, 34, 5, 'A7', 'A', 7, NULL, 'available'),
(2319, 34, 5, 'A8', 'A', 8, NULL, 'available'),
(2320, 34, 5, 'A9', 'A', 9, NULL, 'available'),
(2321, 34, 5, 'A10', 'A', 10, NULL, 'available'),
(2322, 34, 5, 'A11', 'A', 11, NULL, 'available'),
(2323, 34, 5, 'A12', 'A', 12, NULL, 'available'),
(2324, 34, 5, 'B1', 'B', 1, NULL, 'available'),
(2325, 34, 5, 'B2', 'B', 2, NULL, 'available'),
(2326, 34, 5, 'B3', 'B', 3, NULL, 'available'),
(2327, 34, 5, 'B4', 'B', 4, NULL, 'available'),
(2328, 34, 5, 'B5', 'B', 5, NULL, 'available'),
(2329, 34, 5, 'B6', 'B', 6, NULL, 'available'),
(2330, 34, 5, 'B7', 'B', 7, NULL, 'available'),
(2331, 34, 5, 'B8', 'B', 8, NULL, 'available'),
(2332, 34, 5, 'B9', 'B', 9, NULL, 'available'),
(2333, 34, 5, 'B10', 'B', 10, NULL, 'available'),
(2334, 34, 5, 'B11', 'B', 11, NULL, 'available'),
(2335, 34, 5, 'B12', 'B', 12, NULL, 'available'),
(2336, 34, 5, 'C1', 'C', 1, NULL, 'available'),
(2337, 34, 5, 'C2', 'C', 2, NULL, 'available'),
(2338, 34, 5, 'C3', 'C', 3, NULL, 'available'),
(2339, 34, 5, 'C4', 'C', 4, NULL, 'available'),
(2340, 34, 5, 'C5', 'C', 5, NULL, 'available'),
(2341, 34, 5, 'C6', 'C', 6, NULL, 'available'),
(2342, 34, 5, 'C7', 'C', 7, NULL, 'available'),
(2343, 34, 5, 'C8', 'C', 8, NULL, 'available'),
(2344, 34, 5, 'C9', 'C', 9, NULL, 'available'),
(2345, 34, 5, 'C10', 'C', 10, NULL, 'available'),
(2346, 34, 5, 'C11', 'C', 11, NULL, 'available'),
(2347, 34, 5, 'C12', 'C', 12, NULL, 'available'),
(2348, 34, 6, 'D1', 'D', 1, NULL, 'available'),
(2349, 34, 6, 'D2', 'D', 2, NULL, 'available'),
(2350, 34, 6, 'D3', 'D', 3, NULL, 'available'),
(2351, 34, 6, 'D4', 'D', 4, NULL, 'available'),
(2352, 34, 6, 'D5', 'D', 5, NULL, 'available'),
(2353, 34, 6, 'D6', 'D', 6, NULL, 'available'),
(2354, 34, 6, 'D7', 'D', 7, NULL, 'available'),
(2355, 34, 6, 'D8', 'D', 8, NULL, 'available'),
(2356, 34, 6, 'D9', 'D', 9, NULL, 'available'),
(2357, 34, 6, 'D10', 'D', 10, NULL, 'available'),
(2358, 34, 6, 'D11', 'D', 11, NULL, 'available'),
(2359, 34, 6, 'D12', 'D', 12, NULL, 'available'),
(2360, 34, 6, 'E1', 'E', 1, NULL, 'available'),
(2361, 34, 6, 'E2', 'E', 2, NULL, 'available'),
(2362, 34, 6, 'E3', 'E', 3, NULL, 'available'),
(2363, 34, 6, 'E4', 'E', 4, NULL, 'available'),
(2364, 34, 6, 'E5', 'E', 5, NULL, 'available'),
(2365, 34, 6, 'E6', 'E', 6, NULL, 'available'),
(2366, 34, 6, 'E7', 'E', 7, NULL, 'available'),
(2367, 34, 6, 'E8', 'E', 8, NULL, 'available'),
(2368, 34, 6, 'E9', 'E', 9, NULL, 'available'),
(2369, 34, 6, 'E10', 'E', 10, NULL, 'available'),
(2370, 34, 6, 'E11', 'E', 11, NULL, 'available'),
(2371, 34, 6, 'E12', 'E', 12, NULL, 'available'),
(2372, 34, 6, 'F1', 'F', 1, NULL, 'available'),
(2373, 34, 6, 'F2', 'F', 2, NULL, 'available'),
(2374, 34, 6, 'F3', 'F', 3, NULL, 'available'),
(2375, 34, 6, 'F4', 'F', 4, NULL, 'available'),
(2376, 34, 6, 'F5', 'F', 5, NULL, 'available'),
(2377, 34, 6, 'F6', 'F', 6, NULL, 'available'),
(2378, 34, 6, 'F7', 'F', 7, NULL, 'available'),
(2379, 34, 6, 'F8', 'F', 8, NULL, 'available'),
(2380, 34, 6, 'F9', 'F', 9, NULL, 'available'),
(2381, 34, 6, 'F10', 'F', 10, NULL, 'available'),
(2382, 34, 6, 'F11', 'F', 11, NULL, 'available'),
(2383, 34, 6, 'F12', 'F', 12, NULL, 'available'),
(2384, 34, 6, 'G1', 'G', 1, NULL, 'available'),
(2385, 34, 6, 'G2', 'G', 2, NULL, 'available'),
(2386, 34, 6, 'G3', 'G', 3, NULL, 'available'),
(2387, 34, 6, 'G4', 'G', 4, NULL, 'available'),
(2388, 34, 6, 'G5', 'G', 5, NULL, 'available'),
(2389, 34, 6, 'G6', 'G', 6, NULL, 'available'),
(2390, 34, 6, 'G7', 'G', 7, NULL, 'available'),
(2391, 34, 6, 'G8', 'G', 8, NULL, 'available'),
(2392, 34, 6, 'G9', 'G', 9, NULL, 'available'),
(2393, 34, 6, 'G10', 'G', 10, NULL, 'available'),
(2394, 34, 6, 'G11', 'G', 11, NULL, 'available'),
(2395, 34, 6, 'G12', 'G', 12, NULL, 'available'),
(2396, 34, 6, 'H1', 'H', 1, NULL, 'available'),
(2397, 34, 6, 'H2', 'H', 2, NULL, 'available'),
(2398, 34, 6, 'H3', 'H', 3, NULL, 'available'),
(2399, 34, 6, 'H4', 'H', 4, NULL, 'available'),
(2400, 34, 6, 'H5', 'H', 5, NULL, 'available'),
(2401, 34, 6, 'H6', 'H', 6, NULL, 'available'),
(2402, 34, 6, 'H7', 'H', 7, NULL, 'available'),
(2403, 34, 6, 'H8', 'H', 8, NULL, 'available'),
(2404, 34, 6, 'H9', 'H', 9, NULL, 'available'),
(2405, 34, 6, 'H10', 'H', 10, NULL, 'available'),
(2406, 34, 6, 'H11', 'H', 11, NULL, 'available'),
(2407, 34, 6, 'H12', 'H', 12, NULL, 'available'),
(2408, 34, 6, 'I1', 'I', 1, NULL, 'available'),
(2409, 34, 6, 'I2', 'I', 2, NULL, 'available'),
(2410, 34, 6, 'I3', 'I', 3, NULL, 'available'),
(2411, 34, 6, 'I4', 'I', 4, NULL, 'available'),
(2412, 34, 6, 'I5', 'I', 5, NULL, 'available'),
(2413, 34, 6, 'I6', 'I', 6, NULL, 'available'),
(2414, 34, 6, 'I7', 'I', 7, NULL, 'available'),
(2415, 34, 6, 'I8', 'I', 8, NULL, 'available'),
(2416, 34, 6, 'I9', 'I', 9, NULL, 'available'),
(2417, 34, 6, 'I10', 'I', 10, NULL, 'available'),
(2418, 34, 6, 'I11', 'I', 11, NULL, 'available'),
(2419, 34, 6, 'I12', 'I', 12, NULL, 'available'),
(2420, 34, 7, 'J1', 'J', 1, 'ROOM_34_J_PAIR_1', 'available'),
(2421, 34, 7, 'J2', 'J', 2, 'ROOM_34_J_PAIR_1', 'available'),
(2422, 34, 7, 'J3', 'J', 3, 'ROOM_34_J_PAIR_2', 'available'),
(2423, 34, 7, 'J4', 'J', 4, 'ROOM_34_J_PAIR_2', 'available'),
(2424, 34, 7, 'J5', 'J', 5, 'ROOM_34_J_PAIR_3', 'available'),
(2425, 34, 7, 'J6', 'J', 6, 'ROOM_34_J_PAIR_3', 'available'),
(2426, 34, 7, 'J7', 'J', 7, 'ROOM_34_J_PAIR_4', 'available'),
(2427, 34, 7, 'J8', 'J', 8, 'ROOM_34_J_PAIR_4', 'available'),
(2428, 34, 7, 'J9', 'J', 9, 'ROOM_34_J_PAIR_5', 'available'),
(2429, 34, 7, 'J10', 'J', 10, 'ROOM_34_J_PAIR_5', 'available'),
(2430, 34, 7, 'J11', 'J', 11, 'ROOM_34_J_PAIR_6', 'available'),
(2431, 34, 7, 'J12', 'J', 12, 'ROOM_34_J_PAIR_6', 'available'),
(2432, 35, 5, 'A1', 'A', 1, NULL, 'available'),
(2433, 35, 5, 'A2', 'A', 2, NULL, 'available'),
(2434, 35, 5, 'A3', 'A', 3, NULL, 'available'),
(2435, 35, 5, 'A4', 'A', 4, NULL, 'available'),
(2436, 35, 5, 'A5', 'A', 5, NULL, 'available'),
(2437, 35, 5, 'A6', 'A', 6, NULL, 'available'),
(2438, 35, 5, 'A7', 'A', 7, NULL, 'available'),
(2439, 35, 5, 'A8', 'A', 8, NULL, 'available'),
(2440, 35, 5, 'A9', 'A', 9, NULL, 'available'),
(2441, 35, 5, 'A10', 'A', 10, NULL, 'available'),
(2442, 35, 5, 'A11', 'A', 11, NULL, 'available'),
(2443, 35, 5, 'A12', 'A', 12, NULL, 'available'),
(2444, 35, 5, 'B1', 'B', 1, NULL, 'available'),
(2445, 35, 5, 'B2', 'B', 2, NULL, 'available'),
(2446, 35, 5, 'B3', 'B', 3, NULL, 'available'),
(2447, 35, 5, 'B4', 'B', 4, NULL, 'available'),
(2448, 35, 5, 'B5', 'B', 5, NULL, 'available'),
(2449, 35, 5, 'B6', 'B', 6, NULL, 'available'),
(2450, 35, 5, 'B7', 'B', 7, NULL, 'available'),
(2451, 35, 5, 'B8', 'B', 8, NULL, 'available'),
(2452, 35, 5, 'B9', 'B', 9, NULL, 'available'),
(2453, 35, 5, 'B10', 'B', 10, NULL, 'available'),
(2454, 35, 5, 'B11', 'B', 11, NULL, 'available'),
(2455, 35, 5, 'B12', 'B', 12, NULL, 'available'),
(2456, 35, 5, 'C1', 'C', 1, NULL, 'available'),
(2457, 35, 5, 'C2', 'C', 2, NULL, 'available'),
(2458, 35, 5, 'C3', 'C', 3, NULL, 'available'),
(2459, 35, 5, 'C4', 'C', 4, NULL, 'available'),
(2460, 35, 5, 'C5', 'C', 5, NULL, 'available'),
(2461, 35, 5, 'C6', 'C', 6, NULL, 'available'),
(2462, 35, 5, 'C7', 'C', 7, NULL, 'available'),
(2463, 35, 5, 'C8', 'C', 8, NULL, 'available'),
(2464, 35, 5, 'C9', 'C', 9, NULL, 'available'),
(2465, 35, 5, 'C10', 'C', 10, NULL, 'available'),
(2466, 35, 5, 'C11', 'C', 11, NULL, 'available'),
(2467, 35, 5, 'C12', 'C', 12, NULL, 'available'),
(2468, 35, 6, 'D1', 'D', 1, NULL, 'available'),
(2469, 35, 6, 'D2', 'D', 2, NULL, 'available'),
(2470, 35, 6, 'D3', 'D', 3, NULL, 'available'),
(2471, 35, 6, 'D4', 'D', 4, NULL, 'available'),
(2472, 35, 6, 'D5', 'D', 5, NULL, 'available'),
(2473, 35, 6, 'D6', 'D', 6, NULL, 'available'),
(2474, 35, 6, 'D7', 'D', 7, NULL, 'available'),
(2475, 35, 6, 'D8', 'D', 8, NULL, 'available'),
(2476, 35, 6, 'D9', 'D', 9, NULL, 'available'),
(2477, 35, 6, 'D10', 'D', 10, NULL, 'available'),
(2478, 35, 6, 'D11', 'D', 11, NULL, 'available'),
(2479, 35, 6, 'D12', 'D', 12, NULL, 'available'),
(2480, 35, 6, 'E1', 'E', 1, NULL, 'available'),
(2481, 35, 6, 'E2', 'E', 2, NULL, 'available'),
(2482, 35, 6, 'E3', 'E', 3, NULL, 'available'),
(2483, 35, 6, 'E4', 'E', 4, NULL, 'available'),
(2484, 35, 6, 'E5', 'E', 5, NULL, 'available'),
(2485, 35, 6, 'E6', 'E', 6, NULL, 'available'),
(2486, 35, 6, 'E7', 'E', 7, NULL, 'available'),
(2487, 35, 6, 'E8', 'E', 8, NULL, 'available'),
(2488, 35, 6, 'E9', 'E', 9, NULL, 'available'),
(2489, 35, 6, 'E10', 'E', 10, NULL, 'available'),
(2490, 35, 6, 'E11', 'E', 11, NULL, 'available'),
(2491, 35, 6, 'E12', 'E', 12, NULL, 'available'),
(2492, 35, 6, 'F1', 'F', 1, NULL, 'available'),
(2493, 35, 6, 'F2', 'F', 2, NULL, 'available'),
(2494, 35, 6, 'F3', 'F', 3, NULL, 'available'),
(2495, 35, 6, 'F4', 'F', 4, NULL, 'available'),
(2496, 35, 6, 'F5', 'F', 5, NULL, 'available'),
(2497, 35, 6, 'F6', 'F', 6, NULL, 'available'),
(2498, 35, 6, 'F7', 'F', 7, NULL, 'available'),
(2499, 35, 6, 'F8', 'F', 8, NULL, 'available'),
(2500, 35, 6, 'F9', 'F', 9, NULL, 'available'),
(2501, 35, 6, 'F10', 'F', 10, NULL, 'available'),
(2502, 35, 6, 'F11', 'F', 11, NULL, 'available'),
(2503, 35, 6, 'F12', 'F', 12, NULL, 'available'),
(2504, 35, 6, 'G1', 'G', 1, NULL, 'available'),
(2505, 35, 6, 'G2', 'G', 2, NULL, 'available'),
(2506, 35, 6, 'G3', 'G', 3, NULL, 'available'),
(2507, 35, 6, 'G4', 'G', 4, NULL, 'available');
INSERT INTO `seats` (`id`, `room_id`, `seat_type_id`, `seat_number`, `row_char`, `col_num`, `couple_group`, `status`) VALUES
(2508, 35, 6, 'G5', 'G', 5, NULL, 'available'),
(2509, 35, 6, 'G6', 'G', 6, NULL, 'available'),
(2510, 35, 6, 'G7', 'G', 7, NULL, 'available'),
(2511, 35, 6, 'G8', 'G', 8, NULL, 'available'),
(2512, 35, 6, 'G9', 'G', 9, NULL, 'available'),
(2513, 35, 6, 'G10', 'G', 10, NULL, 'available'),
(2514, 35, 6, 'G11', 'G', 11, NULL, 'available'),
(2515, 35, 6, 'G12', 'G', 12, NULL, 'available'),
(2516, 35, 6, 'H1', 'H', 1, NULL, 'available'),
(2517, 35, 6, 'H2', 'H', 2, NULL, 'available'),
(2518, 35, 6, 'H3', 'H', 3, NULL, 'available'),
(2519, 35, 6, 'H4', 'H', 4, NULL, 'available'),
(2520, 35, 6, 'H5', 'H', 5, NULL, 'available'),
(2521, 35, 6, 'H6', 'H', 6, NULL, 'available'),
(2522, 35, 6, 'H7', 'H', 7, NULL, 'available'),
(2523, 35, 6, 'H8', 'H', 8, NULL, 'available'),
(2524, 35, 6, 'H9', 'H', 9, NULL, 'available'),
(2525, 35, 6, 'H10', 'H', 10, NULL, 'available'),
(2526, 35, 6, 'H11', 'H', 11, NULL, 'available'),
(2527, 35, 6, 'H12', 'H', 12, NULL, 'available'),
(2528, 35, 6, 'I1', 'I', 1, NULL, 'available'),
(2529, 35, 6, 'I2', 'I', 2, NULL, 'available'),
(2530, 35, 6, 'I3', 'I', 3, NULL, 'available'),
(2531, 35, 6, 'I4', 'I', 4, NULL, 'available'),
(2532, 35, 6, 'I5', 'I', 5, NULL, 'available'),
(2533, 35, 6, 'I6', 'I', 6, NULL, 'available'),
(2534, 35, 6, 'I7', 'I', 7, NULL, 'available'),
(2535, 35, 6, 'I8', 'I', 8, NULL, 'available'),
(2536, 35, 6, 'I9', 'I', 9, NULL, 'available'),
(2537, 35, 6, 'I10', 'I', 10, NULL, 'available'),
(2538, 35, 6, 'I11', 'I', 11, NULL, 'available'),
(2539, 35, 6, 'I12', 'I', 12, NULL, 'available'),
(2540, 35, 7, 'J1', 'J', 1, 'ROOM_35_J_PAIR_1', 'available'),
(2541, 35, 7, 'J2', 'J', 2, 'ROOM_35_J_PAIR_1', 'available'),
(2542, 35, 7, 'J3', 'J', 3, 'ROOM_35_J_PAIR_2', 'available'),
(2543, 35, 7, 'J4', 'J', 4, 'ROOM_35_J_PAIR_2', 'available'),
(2544, 35, 7, 'J5', 'J', 5, 'ROOM_35_J_PAIR_3', 'available'),
(2545, 35, 7, 'J6', 'J', 6, 'ROOM_35_J_PAIR_3', 'available'),
(2546, 35, 7, 'J7', 'J', 7, 'ROOM_35_J_PAIR_4', 'available'),
(2547, 35, 7, 'J8', 'J', 8, 'ROOM_35_J_PAIR_4', 'available'),
(2548, 35, 7, 'J9', 'J', 9, 'ROOM_35_J_PAIR_5', 'available'),
(2549, 35, 7, 'J10', 'J', 10, 'ROOM_35_J_PAIR_5', 'available'),
(2550, 35, 7, 'J11', 'J', 11, 'ROOM_35_J_PAIR_6', 'available'),
(2551, 35, 7, 'J12', 'J', 12, 'ROOM_35_J_PAIR_6', 'available'),
(2552, 36, 5, 'A1', 'A', 1, NULL, 'available'),
(2553, 36, 5, 'A2', 'A', 2, NULL, 'available'),
(2554, 36, 5, 'A3', 'A', 3, NULL, 'available'),
(2555, 36, 5, 'A4', 'A', 4, NULL, 'available'),
(2556, 36, 5, 'A5', 'A', 5, NULL, 'available'),
(2557, 36, 5, 'A6', 'A', 6, NULL, 'available'),
(2558, 36, 5, 'A7', 'A', 7, NULL, 'available'),
(2559, 36, 5, 'A8', 'A', 8, NULL, 'available'),
(2560, 36, 5, 'A9', 'A', 9, NULL, 'available'),
(2561, 36, 5, 'A10', 'A', 10, NULL, 'available'),
(2562, 36, 5, 'A11', 'A', 11, NULL, 'available'),
(2563, 36, 5, 'A12', 'A', 12, NULL, 'available'),
(2564, 36, 5, 'B1', 'B', 1, NULL, 'available'),
(2565, 36, 5, 'B2', 'B', 2, NULL, 'available'),
(2566, 36, 5, 'B3', 'B', 3, NULL, 'available'),
(2567, 36, 5, 'B4', 'B', 4, NULL, 'available'),
(2568, 36, 5, 'B5', 'B', 5, NULL, 'available'),
(2569, 36, 5, 'B6', 'B', 6, NULL, 'available'),
(2570, 36, 5, 'B7', 'B', 7, NULL, 'available'),
(2571, 36, 5, 'B8', 'B', 8, NULL, 'available'),
(2572, 36, 5, 'B9', 'B', 9, NULL, 'available'),
(2573, 36, 5, 'B10', 'B', 10, NULL, 'available'),
(2574, 36, 5, 'B11', 'B', 11, NULL, 'available'),
(2575, 36, 5, 'B12', 'B', 12, NULL, 'available'),
(2576, 36, 5, 'C1', 'C', 1, NULL, 'available'),
(2577, 36, 5, 'C2', 'C', 2, NULL, 'available'),
(2578, 36, 5, 'C3', 'C', 3, NULL, 'available'),
(2579, 36, 5, 'C4', 'C', 4, NULL, 'available'),
(2580, 36, 5, 'C5', 'C', 5, NULL, 'available'),
(2581, 36, 5, 'C6', 'C', 6, NULL, 'available'),
(2582, 36, 5, 'C7', 'C', 7, NULL, 'available'),
(2583, 36, 5, 'C8', 'C', 8, NULL, 'available'),
(2584, 36, 5, 'C9', 'C', 9, NULL, 'available'),
(2585, 36, 5, 'C10', 'C', 10, NULL, 'available'),
(2586, 36, 5, 'C11', 'C', 11, NULL, 'available'),
(2587, 36, 5, 'C12', 'C', 12, NULL, 'available'),
(2588, 36, 6, 'D1', 'D', 1, NULL, 'available'),
(2589, 36, 6, 'D2', 'D', 2, NULL, 'available'),
(2590, 36, 6, 'D3', 'D', 3, NULL, 'available'),
(2591, 36, 6, 'D4', 'D', 4, NULL, 'available'),
(2592, 36, 6, 'D5', 'D', 5, NULL, 'available'),
(2593, 36, 6, 'D6', 'D', 6, NULL, 'available'),
(2594, 36, 6, 'D7', 'D', 7, NULL, 'available'),
(2595, 36, 6, 'D8', 'D', 8, NULL, 'available'),
(2596, 36, 6, 'D9', 'D', 9, NULL, 'available'),
(2597, 36, 6, 'D10', 'D', 10, NULL, 'available'),
(2598, 36, 6, 'D11', 'D', 11, NULL, 'available'),
(2599, 36, 6, 'D12', 'D', 12, NULL, 'available'),
(2600, 36, 6, 'E1', 'E', 1, NULL, 'available'),
(2601, 36, 6, 'E2', 'E', 2, NULL, 'available'),
(2602, 36, 6, 'E3', 'E', 3, NULL, 'available'),
(2603, 36, 6, 'E4', 'E', 4, NULL, 'available'),
(2604, 36, 6, 'E5', 'E', 5, NULL, 'available'),
(2605, 36, 6, 'E6', 'E', 6, NULL, 'available'),
(2606, 36, 6, 'E7', 'E', 7, NULL, 'available'),
(2607, 36, 6, 'E8', 'E', 8, NULL, 'available'),
(2608, 36, 6, 'E9', 'E', 9, NULL, 'available'),
(2609, 36, 6, 'E10', 'E', 10, NULL, 'available'),
(2610, 36, 6, 'E11', 'E', 11, NULL, 'available'),
(2611, 36, 6, 'E12', 'E', 12, NULL, 'available'),
(2612, 36, 6, 'F1', 'F', 1, NULL, 'available'),
(2613, 36, 6, 'F2', 'F', 2, NULL, 'available'),
(2614, 36, 6, 'F3', 'F', 3, NULL, 'available'),
(2615, 36, 6, 'F4', 'F', 4, NULL, 'available'),
(2616, 36, 6, 'F5', 'F', 5, NULL, 'available'),
(2617, 36, 6, 'F6', 'F', 6, NULL, 'available'),
(2618, 36, 6, 'F7', 'F', 7, NULL, 'available'),
(2619, 36, 6, 'F8', 'F', 8, NULL, 'available'),
(2620, 36, 6, 'F9', 'F', 9, NULL, 'available'),
(2621, 36, 6, 'F10', 'F', 10, NULL, 'available'),
(2622, 36, 6, 'F11', 'F', 11, NULL, 'available'),
(2623, 36, 6, 'F12', 'F', 12, NULL, 'available'),
(2624, 36, 6, 'G1', 'G', 1, NULL, 'available'),
(2625, 36, 6, 'G2', 'G', 2, NULL, 'available'),
(2626, 36, 6, 'G3', 'G', 3, NULL, 'available'),
(2627, 36, 6, 'G4', 'G', 4, NULL, 'available'),
(2628, 36, 6, 'G5', 'G', 5, NULL, 'available'),
(2629, 36, 6, 'G6', 'G', 6, NULL, 'available'),
(2630, 36, 6, 'G7', 'G', 7, NULL, 'available'),
(2631, 36, 6, 'G8', 'G', 8, NULL, 'available'),
(2632, 36, 6, 'G9', 'G', 9, NULL, 'available'),
(2633, 36, 6, 'G10', 'G', 10, NULL, 'available'),
(2634, 36, 6, 'G11', 'G', 11, NULL, 'available'),
(2635, 36, 6, 'G12', 'G', 12, NULL, 'available'),
(2636, 36, 6, 'H1', 'H', 1, NULL, 'available'),
(2637, 36, 6, 'H2', 'H', 2, NULL, 'available'),
(2638, 36, 6, 'H3', 'H', 3, NULL, 'available'),
(2639, 36, 6, 'H4', 'H', 4, NULL, 'available'),
(2640, 36, 6, 'H5', 'H', 5, NULL, 'available'),
(2641, 36, 6, 'H6', 'H', 6, NULL, 'available'),
(2642, 36, 6, 'H7', 'H', 7, NULL, 'available'),
(2643, 36, 6, 'H8', 'H', 8, NULL, 'available'),
(2644, 36, 6, 'H9', 'H', 9, NULL, 'available'),
(2645, 36, 6, 'H10', 'H', 10, NULL, 'available'),
(2646, 36, 6, 'H11', 'H', 11, NULL, 'available'),
(2647, 36, 6, 'H12', 'H', 12, NULL, 'available'),
(2648, 36, 6, 'I1', 'I', 1, NULL, 'available'),
(2649, 36, 6, 'I2', 'I', 2, NULL, 'available'),
(2650, 36, 6, 'I3', 'I', 3, NULL, 'available'),
(2651, 36, 6, 'I4', 'I', 4, NULL, 'available'),
(2652, 36, 6, 'I5', 'I', 5, NULL, 'available'),
(2653, 36, 6, 'I6', 'I', 6, NULL, 'available'),
(2654, 36, 6, 'I7', 'I', 7, NULL, 'available'),
(2655, 36, 6, 'I8', 'I', 8, NULL, 'available'),
(2656, 36, 6, 'I9', 'I', 9, NULL, 'available'),
(2657, 36, 6, 'I10', 'I', 10, NULL, 'available'),
(2658, 36, 6, 'I11', 'I', 11, NULL, 'available'),
(2659, 36, 6, 'I12', 'I', 12, NULL, 'available'),
(2660, 36, 7, 'J1', 'J', 1, 'ROOM_36_J_PAIR_1', 'available'),
(2661, 36, 7, 'J2', 'J', 2, 'ROOM_36_J_PAIR_1', 'available'),
(2662, 36, 7, 'J3', 'J', 3, 'ROOM_36_J_PAIR_2', 'available'),
(2663, 36, 7, 'J4', 'J', 4, 'ROOM_36_J_PAIR_2', 'available'),
(2664, 36, 7, 'J5', 'J', 5, 'ROOM_36_J_PAIR_3', 'available'),
(2665, 36, 7, 'J6', 'J', 6, 'ROOM_36_J_PAIR_3', 'available'),
(2666, 36, 7, 'J7', 'J', 7, 'ROOM_36_J_PAIR_4', 'available'),
(2667, 36, 7, 'J8', 'J', 8, 'ROOM_36_J_PAIR_4', 'available'),
(2668, 36, 7, 'J9', 'J', 9, 'ROOM_36_J_PAIR_5', 'available'),
(2669, 36, 7, 'J10', 'J', 10, 'ROOM_36_J_PAIR_5', 'available'),
(2670, 36, 7, 'J11', 'J', 11, 'ROOM_36_J_PAIR_6', 'available'),
(2671, 36, 7, 'J12', 'J', 12, 'ROOM_36_J_PAIR_6', 'available'),
(2672, 25, 5, 'A1', 'A', 1, NULL, 'available'),
(2673, 25, 5, 'A2', 'A', 2, NULL, 'available'),
(2674, 25, 5, 'A3', 'A', 3, NULL, 'available'),
(2675, 25, 5, 'A4', 'A', 4, NULL, 'available'),
(2676, 25, 5, 'A5', 'A', 5, NULL, 'available'),
(2677, 25, 5, 'A6', 'A', 6, NULL, 'available'),
(2678, 25, 5, 'A7', 'A', 7, NULL, 'available'),
(2679, 25, 5, 'A8', 'A', 8, NULL, 'available'),
(2680, 25, 5, 'A9', 'A', 9, NULL, 'available'),
(2681, 25, 5, 'A10', 'A', 10, NULL, 'available'),
(2682, 25, 5, 'A11', 'A', 11, NULL, 'available'),
(2683, 25, 5, 'A12', 'A', 12, NULL, 'available'),
(2684, 25, 5, 'A13', 'A', 13, NULL, 'available'),
(2685, 25, 5, 'A14', 'A', 14, NULL, 'available'),
(2686, 25, 5, 'A15', 'A', 15, NULL, 'available'),
(2687, 25, 5, 'A16', 'A', 16, NULL, 'available'),
(2688, 25, 5, 'A17', 'A', 17, NULL, 'available'),
(2689, 25, 5, 'A18', 'A', 18, NULL, 'available'),
(2690, 25, 5, 'A19', 'A', 19, NULL, 'available'),
(2691, 25, 5, 'A20', 'A', 20, NULL, 'available'),
(2692, 25, 5, 'B1', 'B', 1, NULL, 'available'),
(2693, 25, 5, 'B2', 'B', 2, NULL, 'available'),
(2694, 25, 5, 'B3', 'B', 3, NULL, 'available'),
(2695, 25, 5, 'B4', 'B', 4, NULL, 'available'),
(2696, 25, 5, 'B5', 'B', 5, NULL, 'available'),
(2697, 25, 5, 'B6', 'B', 6, NULL, 'available'),
(2698, 25, 5, 'B7', 'B', 7, NULL, 'available'),
(2699, 25, 5, 'B8', 'B', 8, NULL, 'available'),
(2700, 25, 5, 'B9', 'B', 9, NULL, 'available'),
(2701, 25, 5, 'B10', 'B', 10, NULL, 'available'),
(2702, 25, 5, 'B11', 'B', 11, NULL, 'available'),
(2703, 25, 5, 'B12', 'B', 12, NULL, 'available'),
(2704, 25, 5, 'B13', 'B', 13, NULL, 'available'),
(2705, 25, 5, 'B14', 'B', 14, NULL, 'available'),
(2706, 25, 5, 'B15', 'B', 15, NULL, 'available'),
(2707, 25, 5, 'B16', 'B', 16, NULL, 'available'),
(2708, 25, 5, 'B17', 'B', 17, NULL, 'available'),
(2709, 25, 5, 'B18', 'B', 18, NULL, 'available'),
(2710, 25, 5, 'B19', 'B', 19, NULL, 'available'),
(2711, 25, 5, 'B20', 'B', 20, NULL, 'available'),
(2712, 25, 5, 'C1', 'C', 1, NULL, 'available'),
(2713, 25, 5, 'C2', 'C', 2, NULL, 'available'),
(2714, 25, 5, 'C3', 'C', 3, NULL, 'available'),
(2715, 25, 5, 'C4', 'C', 4, NULL, 'available'),
(2716, 25, 5, 'C5', 'C', 5, NULL, 'available'),
(2717, 25, 5, 'C6', 'C', 6, NULL, 'available'),
(2718, 25, 5, 'C7', 'C', 7, NULL, 'available'),
(2719, 25, 5, 'C8', 'C', 8, NULL, 'available'),
(2720, 25, 5, 'C9', 'C', 9, NULL, 'available'),
(2721, 25, 5, 'C10', 'C', 10, NULL, 'available'),
(2722, 25, 5, 'C11', 'C', 11, NULL, 'available'),
(2723, 25, 5, 'C12', 'C', 12, NULL, 'available'),
(2724, 25, 5, 'C13', 'C', 13, NULL, 'available'),
(2725, 25, 5, 'C14', 'C', 14, NULL, 'available'),
(2726, 25, 5, 'C15', 'C', 15, NULL, 'available'),
(2727, 25, 5, 'C16', 'C', 16, NULL, 'available'),
(2728, 25, 5, 'C17', 'C', 17, NULL, 'available'),
(2729, 25, 5, 'C18', 'C', 18, NULL, 'available'),
(2730, 25, 5, 'C19', 'C', 19, NULL, 'available'),
(2731, 25, 5, 'C20', 'C', 20, NULL, 'available'),
(2732, 25, 6, 'D1', 'D', 1, NULL, 'available'),
(2733, 25, 6, 'D2', 'D', 2, NULL, 'available'),
(2734, 25, 6, 'D3', 'D', 3, NULL, 'available'),
(2735, 25, 6, 'D4', 'D', 4, NULL, 'available'),
(2736, 25, 6, 'D5', 'D', 5, NULL, 'available'),
(2737, 25, 6, 'D6', 'D', 6, NULL, 'available'),
(2738, 25, 6, 'D7', 'D', 7, NULL, 'available'),
(2739, 25, 6, 'D8', 'D', 8, NULL, 'available'),
(2740, 25, 6, 'D9', 'D', 9, NULL, 'available'),
(2741, 25, 6, 'D10', 'D', 10, NULL, 'available'),
(2742, 25, 6, 'D11', 'D', 11, NULL, 'available'),
(2743, 25, 6, 'D12', 'D', 12, NULL, 'available'),
(2744, 25, 6, 'D13', 'D', 13, NULL, 'available'),
(2745, 25, 6, 'D14', 'D', 14, NULL, 'available'),
(2746, 25, 6, 'D15', 'D', 15, NULL, 'available'),
(2747, 25, 6, 'D16', 'D', 16, NULL, 'available'),
(2748, 25, 6, 'D17', 'D', 17, NULL, 'available'),
(2749, 25, 6, 'D18', 'D', 18, NULL, 'available'),
(2750, 25, 6, 'D19', 'D', 19, NULL, 'available'),
(2751, 25, 6, 'D20', 'D', 20, NULL, 'available'),
(2752, 25, 6, 'E1', 'E', 1, NULL, 'available'),
(2753, 25, 6, 'E2', 'E', 2, NULL, 'available'),
(2754, 25, 6, 'E3', 'E', 3, NULL, 'available'),
(2755, 25, 6, 'E4', 'E', 4, NULL, 'available'),
(2756, 25, 6, 'E5', 'E', 5, NULL, 'available'),
(2757, 25, 6, 'E6', 'E', 6, NULL, 'available'),
(2758, 25, 6, 'E7', 'E', 7, NULL, 'available'),
(2759, 25, 6, 'E8', 'E', 8, NULL, 'available'),
(2760, 25, 6, 'E9', 'E', 9, NULL, 'available'),
(2761, 25, 6, 'E10', 'E', 10, NULL, 'available'),
(2762, 25, 6, 'E11', 'E', 11, NULL, 'available'),
(2763, 25, 6, 'E12', 'E', 12, NULL, 'available'),
(2764, 25, 6, 'E13', 'E', 13, NULL, 'available'),
(2765, 25, 6, 'E14', 'E', 14, NULL, 'available'),
(2766, 25, 6, 'E15', 'E', 15, NULL, 'available'),
(2767, 25, 6, 'E16', 'E', 16, NULL, 'available'),
(2768, 25, 6, 'E17', 'E', 17, NULL, 'available'),
(2769, 25, 6, 'E18', 'E', 18, NULL, 'available'),
(2770, 25, 6, 'E19', 'E', 19, NULL, 'available'),
(2771, 25, 6, 'E20', 'E', 20, NULL, 'available'),
(2772, 25, 6, 'F1', 'F', 1, NULL, 'available'),
(2773, 25, 6, 'F2', 'F', 2, NULL, 'available'),
(2774, 25, 6, 'F3', 'F', 3, NULL, 'available'),
(2775, 25, 6, 'F4', 'F', 4, NULL, 'available'),
(2776, 25, 6, 'F5', 'F', 5, NULL, 'available'),
(2777, 25, 6, 'F6', 'F', 6, NULL, 'available'),
(2778, 25, 6, 'F7', 'F', 7, NULL, 'available'),
(2779, 25, 6, 'F8', 'F', 8, NULL, 'available'),
(2780, 25, 6, 'F9', 'F', 9, NULL, 'available'),
(2781, 25, 6, 'F10', 'F', 10, NULL, 'available'),
(2782, 25, 6, 'F11', 'F', 11, NULL, 'available'),
(2783, 25, 6, 'F12', 'F', 12, NULL, 'available'),
(2784, 25, 6, 'F13', 'F', 13, NULL, 'available'),
(2785, 25, 6, 'F14', 'F', 14, NULL, 'available'),
(2786, 25, 6, 'F15', 'F', 15, NULL, 'available'),
(2787, 25, 6, 'F16', 'F', 16, NULL, 'available'),
(2788, 25, 6, 'F17', 'F', 17, NULL, 'available'),
(2789, 25, 6, 'F18', 'F', 18, NULL, 'available'),
(2790, 25, 6, 'F19', 'F', 19, NULL, 'available'),
(2791, 25, 6, 'F20', 'F', 20, NULL, 'available'),
(2792, 25, 6, 'G1', 'G', 1, NULL, 'available'),
(2793, 25, 6, 'G2', 'G', 2, NULL, 'available'),
(2794, 25, 6, 'G3', 'G', 3, NULL, 'available'),
(2795, 25, 6, 'G4', 'G', 4, NULL, 'available'),
(2796, 25, 6, 'G5', 'G', 5, NULL, 'available'),
(2797, 25, 6, 'G6', 'G', 6, NULL, 'available'),
(2798, 25, 6, 'G7', 'G', 7, NULL, 'available'),
(2799, 25, 6, 'G8', 'G', 8, NULL, 'available'),
(2800, 25, 6, 'G9', 'G', 9, NULL, 'available'),
(2801, 25, 6, 'G10', 'G', 10, NULL, 'available'),
(2802, 25, 6, 'G11', 'G', 11, NULL, 'available'),
(2803, 25, 6, 'G12', 'G', 12, NULL, 'available'),
(2804, 25, 6, 'G13', 'G', 13, NULL, 'available'),
(2805, 25, 6, 'G14', 'G', 14, NULL, 'available'),
(2806, 25, 6, 'G15', 'G', 15, NULL, 'available'),
(2807, 25, 6, 'G16', 'G', 16, NULL, 'available'),
(2808, 25, 6, 'G17', 'G', 17, NULL, 'available'),
(2809, 25, 6, 'G18', 'G', 18, NULL, 'available'),
(2810, 25, 6, 'G19', 'G', 19, NULL, 'available'),
(2811, 25, 6, 'G20', 'G', 20, NULL, 'available'),
(2812, 25, 6, 'H1', 'H', 1, NULL, 'available'),
(2813, 25, 6, 'H2', 'H', 2, NULL, 'available'),
(2814, 25, 6, 'H3', 'H', 3, NULL, 'available'),
(2815, 25, 6, 'H4', 'H', 4, NULL, 'available'),
(2816, 25, 6, 'H5', 'H', 5, NULL, 'available'),
(2817, 25, 6, 'H6', 'H', 6, NULL, 'available'),
(2818, 25, 6, 'H7', 'H', 7, NULL, 'available'),
(2819, 25, 6, 'H8', 'H', 8, NULL, 'available'),
(2820, 25, 6, 'H9', 'H', 9, NULL, 'available'),
(2821, 25, 6, 'H10', 'H', 10, NULL, 'available'),
(2822, 25, 6, 'H11', 'H', 11, NULL, 'available'),
(2823, 25, 6, 'H12', 'H', 12, NULL, 'available'),
(2824, 25, 6, 'H13', 'H', 13, NULL, 'available'),
(2825, 25, 6, 'H14', 'H', 14, NULL, 'available'),
(2826, 25, 6, 'H15', 'H', 15, NULL, 'available'),
(2827, 25, 6, 'H16', 'H', 16, NULL, 'available'),
(2828, 25, 6, 'H17', 'H', 17, NULL, 'available'),
(2829, 25, 6, 'H18', 'H', 18, NULL, 'available'),
(2830, 25, 6, 'H19', 'H', 19, NULL, 'available'),
(2831, 25, 6, 'H20', 'H', 20, NULL, 'available'),
(2832, 25, 6, 'I1', 'I', 1, NULL, 'available'),
(2833, 25, 6, 'I2', 'I', 2, NULL, 'available'),
(2834, 25, 6, 'I3', 'I', 3, NULL, 'available'),
(2835, 25, 6, 'I4', 'I', 4, NULL, 'available'),
(2836, 25, 6, 'I5', 'I', 5, NULL, 'available'),
(2837, 25, 6, 'I6', 'I', 6, NULL, 'available'),
(2838, 25, 6, 'I7', 'I', 7, NULL, 'available'),
(2839, 25, 6, 'I8', 'I', 8, NULL, 'available'),
(2840, 25, 6, 'I9', 'I', 9, NULL, 'available'),
(2841, 25, 6, 'I10', 'I', 10, NULL, 'available'),
(2842, 25, 6, 'I11', 'I', 11, NULL, 'available'),
(2843, 25, 6, 'I12', 'I', 12, NULL, 'available'),
(2844, 25, 6, 'I13', 'I', 13, NULL, 'available'),
(2845, 25, 6, 'I14', 'I', 14, NULL, 'available'),
(2846, 25, 6, 'I15', 'I', 15, NULL, 'available'),
(2847, 25, 6, 'I16', 'I', 16, NULL, 'available'),
(2848, 25, 6, 'I17', 'I', 17, NULL, 'available'),
(2849, 25, 6, 'I18', 'I', 18, NULL, 'available'),
(2850, 25, 6, 'I19', 'I', 19, NULL, 'available'),
(2851, 25, 6, 'I20', 'I', 20, NULL, 'available'),
(2852, 25, 7, 'J1', 'J', 1, 'ROOM_25_J_PAIR_1', 'available'),
(2853, 25, 7, 'J2', 'J', 2, 'ROOM_25_J_PAIR_1', 'available'),
(2854, 25, 7, 'J3', 'J', 3, 'ROOM_25_J_PAIR_2', 'available'),
(2855, 25, 7, 'J4', 'J', 4, 'ROOM_25_J_PAIR_2', 'available'),
(2856, 25, 7, 'J5', 'J', 5, 'ROOM_25_J_PAIR_3', 'available'),
(2857, 25, 7, 'J6', 'J', 6, 'ROOM_25_J_PAIR_3', 'available'),
(2858, 25, 7, 'J7', 'J', 7, 'ROOM_25_J_PAIR_4', 'available'),
(2859, 25, 7, 'J8', 'J', 8, 'ROOM_25_J_PAIR_4', 'available'),
(2860, 25, 7, 'J9', 'J', 9, 'ROOM_25_J_PAIR_5', 'available'),
(2861, 25, 7, 'J10', 'J', 10, 'ROOM_25_J_PAIR_5', 'available'),
(2862, 25, 7, 'J11', 'J', 11, 'ROOM_25_J_PAIR_6', 'available'),
(2863, 25, 7, 'J12', 'J', 12, 'ROOM_25_J_PAIR_6', 'available'),
(2864, 25, 7, 'J13', 'J', 13, 'ROOM_25_J_PAIR_7', 'available'),
(2865, 25, 7, 'J14', 'J', 14, 'ROOM_25_J_PAIR_7', 'available'),
(2866, 25, 7, 'J15', 'J', 15, 'ROOM_25_J_PAIR_8', 'available'),
(2867, 25, 7, 'J16', 'J', 16, 'ROOM_25_J_PAIR_8', 'available'),
(2868, 25, 7, 'J17', 'J', 17, 'ROOM_25_J_PAIR_9', 'available'),
(2869, 25, 7, 'J18', 'J', 18, 'ROOM_25_J_PAIR_9', 'available'),
(2870, 25, 7, 'J19', 'J', 19, 'ROOM_25_J_PAIR_10', 'available'),
(2871, 25, 7, 'J20', 'J', 20, 'ROOM_25_J_PAIR_10', 'available');

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
(5, 'Standard', 5000.00, 'Ghế thường'),
(6, 'VIP', 15000.00, 'Ghế VIP rộng rãi'),
(7, 'Couple', 120000.00, 'Ghế đôi dành cho 2 người');

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
(22, 35, 30, '2026-08-05 08:11:00', '2026-08-05 10:26:00', 48000.00),
(23, 8, 28, '2026-08-08 13:13:00', '2026-08-08 15:28:00', 200000.00),
(24, 10, 27, '2026-08-08 14:13:00', '2026-08-08 16:15:00', 2000.00),
(25, 8, 28, '2026-08-06 23:40:00', '2026-08-07 01:55:00', 20000.00),
(26, 40, 28, '2026-08-08 07:30:00', '2026-08-08 10:10:00', 50000.00),
(27, 40, 27, '2026-08-08 07:35:00', '2026-08-08 10:15:00', 50000.00),
(28, 40, 30, '2026-08-08 08:44:00', '2026-08-08 11:24:00', 50000.00),
(29, 40, 35, '2026-08-08 09:43:00', '2026-08-08 12:23:00', 50000.00),
(30, 49, 30, '2026-08-07 21:00:00', '2026-08-08 00:10:00', 50000.00),
(31, 49, 36, '2026-08-07 21:00:00', '2026-08-08 00:10:00', 50000.00),
(32, 49, 32, '2026-08-08 08:37:00', '2026-08-08 11:47:00', 50000.00),
(33, 14, 25, '2026-08-07 22:00:00', '2026-08-08 00:50:00', 50000.00),
(34, 14, 31, '2026-08-07 22:24:00', '2026-08-08 01:14:00', 50000.00),
(35, 14, 32, '2026-08-07 22:24:00', '2026-08-08 01:14:00', 50000.00),
(36, 14, 29, '2026-08-07 22:25:00', '2026-08-08 01:15:00', 50000.00),
(37, 14, 34, '2026-08-07 22:30:00', '2026-08-08 01:20:00', 50000.00),
(38, 14, 31, '2026-08-08 21:25:00', '2026-08-09 00:15:00', 50000.00);

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

--
-- Dumping data for table `tickets`
--

INSERT INTO `tickets` (`id`, `booking_id`, `seat_id`, `ticket_code`, `price`) VALUES
(74, 13, 1892, 'PET202608040001-T01', 53000.00),
(75, 13, 1893, 'PET202608040001-T02', 53000.00),
(76, 14, 1897, 'PET202608060001-T01', 53000.00),
(77, 14, 1896, 'PET202608060001-T02', 53000.00),
(78, 14, 1895, 'PET202608060001-T03', 53000.00),
(79, 15, 1946, 'PET202608060002-T01', 63000.00),
(80, 15, 1947, 'PET202608060002-T02', 63000.00),
(81, 16, 1772, 'PET202608060003-T01', 140000.00),
(82, 16, 1773, 'PET202608060003-T02', 140000.00),
(83, 17, 1907, NULL, 57000.00),
(84, 18, 2106, NULL, 172000.00),
(85, 18, 2107, NULL, 172000.00),
(86, 19, 1905, NULL, 57000.00),
(87, 19, 1906, NULL, 57000.00),
(88, 19, 1915, NULL, 57000.00),
(89, 19, 1916, NULL, 57000.00);

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
(5, 'Phạm Thị Bình', 'binh@gmail.com', '123456', 'user', 'active', '2026-07-22 15:37:57'),
(6, 'Hoàng Minh Đức', 'duc@gmail.com', '123456', 'user', 'active', '2026-07-22 15:37:57'),
(9, 'Tiến', 'tientd.hust@gmail.com', '123456', 'user', 'active', '2026-08-01 22:40:44'),
(11, 'Tiến Trần Đình', 'tientd.hust23@gmail.com', '$2y$10$4hapKJ8ozRobKngRQv/8KOEnzjKS8F4ASmqSEm8Q8OJOgvU.Z.gLS', 'user', 'active', '2026-08-07 10:41:06');

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
  ADD KEY `fk_booking_showtime` (`showtime_id`),
  ADD KEY `fk_booking_checked_in_by` (`checked_in_by`);

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
  ADD KEY `fk_foodorder_variant` (`food_variant_id`),
  ADD KEY `fk_food_orders_delivered_by` (`delivered_by`);

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
  ADD KEY `fk_seat_type` (`seat_type_id`),
  ADD KEY `idx_seats_couple_group` (`room_id`,`couple_group`);

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
  MODIFY `id` int NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=20;

--
-- AUTO_INCREMENT for table `foods`
--
ALTER TABLE `foods`
  MODIFY `id` int NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=19;

--
-- AUTO_INCREMENT for table `food_orders`
--
ALTER TABLE `food_orders`
  MODIFY `id` int NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=16;

--
-- AUTO_INCREMENT for table `food_variants`
--
ALTER TABLE `food_variants`
  MODIFY `id` int NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=68;

--
-- AUTO_INCREMENT for table `movies`
--
ALTER TABLE `movies`
  MODIFY `id` int NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=57;

--
-- AUTO_INCREMENT for table `payments`
--
ALTER TABLE `payments`
  MODIFY `id` int NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=9;

--
-- AUTO_INCREMENT for table `rooms`
--
ALTER TABLE `rooms`
  MODIFY `id` int NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=37;

--
-- AUTO_INCREMENT for table `room_types`
--
ALTER TABLE `room_types`
  MODIFY `id` int NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=10;

--
-- AUTO_INCREMENT for table `seats`
--
ALTER TABLE `seats`
  MODIFY `id` int NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2872;

--
-- AUTO_INCREMENT for table `seat_types`
--
ALTER TABLE `seat_types`
  MODIFY `id` int NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=8;

--
-- AUTO_INCREMENT for table `showtimes`
--
ALTER TABLE `showtimes`
  MODIFY `id` int NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=39;

--
-- AUTO_INCREMENT for table `tickets`
--
ALTER TABLE `tickets`
  MODIFY `id` int NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=90;

--
-- AUTO_INCREMENT for table `users`
--
ALTER TABLE `users`
  MODIFY `id` int NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=12;

--
-- Constraints for dumped tables
--

--
-- Constraints for table `bookings`
--
ALTER TABLE `bookings`
  ADD CONSTRAINT `fk_booking_checked_in_by` FOREIGN KEY (`checked_in_by`) REFERENCES `users` (`id`),
  ADD CONSTRAINT `fk_booking_payment` FOREIGN KEY (`payment_id`) REFERENCES `payments` (`id`),
  ADD CONSTRAINT `fk_booking_showtime` FOREIGN KEY (`showtime_id`) REFERENCES `showtimes` (`id`),
  ADD CONSTRAINT `fk_booking_user` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`);

--
-- Constraints for table `food_orders`
--
ALTER TABLE `food_orders`
  ADD CONSTRAINT `fk_food_orders_delivered_by` FOREIGN KEY (`delivered_by`) REFERENCES `users` (`id`) ON DELETE SET NULL ON UPDATE CASCADE,
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
