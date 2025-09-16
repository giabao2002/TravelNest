-- phpMyAdmin SQL Dump
-- version 5.2.2
-- https://www.phpmyadmin.net/
--
-- Host: localhost:3306
-- Generation Time: May 04, 2025 at 11:19 AM
-- Server version: 8.4.3
-- PHP Version: 8.3.16

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Database: `travel_nest`
--

-- --------------------------------------------------------

--
-- Table structure for table `bookings`
--

CREATE TABLE `bookings` (
  `booking_id` int NOT NULL,
  `user_id` int NOT NULL,
  `tour_id` int NOT NULL,
  `date_id` int NOT NULL,
  `num_adults` int NOT NULL DEFAULT '1',
  `num_children` int NOT NULL DEFAULT '0',
  `total_price` decimal(10,2) NOT NULL,
  `notes` text,
  `status` enum('confirmed','completed','cancelled') DEFAULT 'confirmed',
  `booking_date` timestamp NULL DEFAULT CURRENT_TIMESTAMP,
  `updated_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

--
-- Dumping data for table `bookings`
--

INSERT INTO `bookings` (`booking_id`, `user_id`, `tour_id`, `date_id`, `num_adults`, `num_children`, `total_price`, `notes`, `status`, `booking_date`, `updated_at`) VALUES
(1, 2, 6, 20, 5, 2, 5450000.00, '', 'confirmed', '2025-04-28 18:27:06', '2025-05-04 10:46:19'),
(2, 3, 6, 20, 3, 1, 3170000.00, '', 'confirmed', '2025-04-28 18:27:44', '2025-04-28 18:27:44'),
(3, 3, 1, 1, 5, 0, 4000000.00, '', 'confirmed', '2025-04-28 18:32:53', '2025-04-28 18:32:53'),
(4, 4, 6, 20, 5, 2, 5450000.00, '', 'cancelled', '2025-04-28 18:33:28', '2025-04-28 18:33:34'),
(5, 4, 5, 13, 4, 0, 1600000.00, '', 'completed', '2025-04-28 18:34:53', '2025-05-04 11:07:01'),
(6, 5, 6, 17, 5, 0, 4450000.00, '', 'completed', '2025-04-28 18:35:33', '2025-05-04 11:06:57'),
(7, 5, 4, 10, 5, 0, 50000000.00, '', 'completed', '2025-04-28 18:35:50', '2025-05-04 11:07:02'),
(8, 2, 6, 19, 7, 3, 7730000.00, '', 'cancelled', '2025-05-04 10:36:05', '2025-05-04 10:36:42');

-- --------------------------------------------------------

--
-- Table structure for table `reviews`
--

CREATE TABLE `reviews` (
  `review_id` int NOT NULL,
  `user_id` int NOT NULL,
  `tour_id` int NOT NULL,
  `booking_id` int NOT NULL,
  `rating` int NOT NULL,
  `comment` text,
  `status` enum('active','deleted') DEFAULT 'active',
  `review_date` timestamp NULL DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

--
-- Dumping data for table `reviews`
--

INSERT INTO `reviews` (`review_id`, `user_id`, `tour_id`, `booking_id`, `rating`, `comment`, `status`, `review_date`) VALUES
(1, 2, 6, 1, 5, 'Dịch vụ nơi đây rất tốt', 'active', '2025-05-04 10:43:40'),
(2, 4, 5, 5, 4, 'Trải nghiệm tương đối hay', 'active', '2025-05-04 11:08:47'),
(3, 5, 4, 7, 1, 'Tôi không thích chuyến đi này', 'active', '2025-05-04 11:09:22'),
(4, 5, 6, 6, 5, 'Địa điểm cực chill', 'active', '2025-05-04 11:09:38');

-- --------------------------------------------------------

--
-- Table structure for table `tours`
--

CREATE TABLE `tours` (
  `tour_id` int NOT NULL,
  `name` varchar(255) NOT NULL,
  `description` text NOT NULL,
  `location` varchar(100) NOT NULL,
  `duration` varchar(50) NOT NULL,
  `price_adult` decimal(10,2) NOT NULL,
  `price_child` decimal(10,2) NOT NULL,
  `image1` varchar(255) DEFAULT NULL,
  `image2` varchar(255) DEFAULT NULL,
  `image3` varchar(255) DEFAULT NULL,
  `status` enum('active','inactive') DEFAULT 'active',
  `created_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP,
  `updated_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

--
-- Dumping data for table `tours`
--

INSERT INTO `tours` (`tour_id`, `name`, `description`, `location`, `duration`, `price_adult`, `price_child`, `image1`, `image2`, `image3`, `status`, `created_at`, `updated_at`) VALUES
(1, 'Hạ Long - Chùa Ba Vàng - Yên Tử', '- Xe ôtô 29,35,45 chỗ điều hòa loại tốt phục vụ suốt chương trình ( Tùy theo số lượng khách)\r\n- Ăn chính : 01 bữa trưa tại nhà hàng, tiêu chuẩn : 200.000đ/suất (không bao gồm đồ uống).\r\n- Hướng dẫn tiếng Việt kinh nghiệm, nhiệt tình theo suốt chương trình\r\n- Phí tham quan theo lịch trình.\r\n- Quà tặng của Vietravel.\r\n- Khăn ướt phục vụ trên xe 01 cái/khách\r\n- Nước suối phục vụ trên xe 01 chai 0,5l/ ngày.\r\n- Bảo hiểm du lịch mức chi trả tối đa: 120.000.000đ/ người/vụ\r\n- Thuế VAT 10% theo quy định nhà nước.', 'Hạ Long, Chùa Ba Vàng, Yên Tử', '3 ngày 2 đêm', 800000.00, 200000.00, 'uploads/tours/313be7.png', 'uploads/tours/313e23.png', 'uploads/tours/31429e.png', 'active', '2025-04-18 09:48:03', '2025-04-18 09:48:03'),
(2, 'Hòa Bình - Mộc Châu - Điện Biên - Lai Châu - Sapa - Fansipan', '- Xe tham quan (15, 25,35 chỗ tùy theo số lượng khách) theo chương trình\r\n- Vé máy bay khứ hồi\r\n- Khách sạn 2&3 sao theo tiêu chuẩn 2 khách/phòng hoặc 3 khách/phòng.\r\n- Các bữa ăn chính theo chương trình\r\n- Vé tham quan theo chương trình\r\n- Hướng dẫn viên tiếng Việt nối tuyến\r\n- Bảo hiểm du lịch với mức bồi thường cao nhất 120.000.000đ/vụ\r\n- Nón Vietravel + Nước suối + Khăn lạnh\r\n- Thuế VAT', 'Hòa Bình, Mộc Châu, Điện Biên, Lai Châu, Sapa, Fansipan', '4 ngày 3 đêm', 5000000.00, 2000000.00, 'uploads/tours/8a72d9.png', 'uploads/tours/8a76f7.png', 'uploads/tours/8a8b11.png', 'active', '2025-04-18 09:50:48', '2025-04-18 09:50:48'),
(3, 'Sapa - Bản Cát Cát - Fansipan - Lào Cai - Hà Khẩu', '- Xe tham quan (15, 25, 35, 45 chỗ tùy theo số lượng khách) theo chương trình\r\n- Vé máy bay khứ hồi\r\n- Khách sạn theo tiêu chuẩn 2 khách/phòng hoặc 3 khách/phòng\r\n- Vé tham quan theo chương trình\r\n- Hướng dẫn viên tiếng Việt nối tuyến\r\n- Bảo hiểm du lịch với mức bồi thường cao nhất 120.000.000đ/vụ\r\n- Nón Vietravel + Nước suối + Khăn lạnh\r\n- Thuế VAT', 'Sapa, Bản Cát Cát, Fansipan, Lào Cai, Hà Khẩu', '5 ngày 4 đêm', 8000000.00, 5000000.00, 'uploads/tours/03d8bd.png', 'uploads/tours/03df4f.png', 'uploads/tours/040b42.png', 'active', '2025-04-18 09:53:20', '2025-04-18 09:53:20'),
(4, 'Vân Hồ - Mộc Châu - Mai Châu', 'Trải nghiệm hệ thống dịch vụ cao cấp, mang màu sắc thiết kế riêng đặc trưng và những khám phá vô cùng thú vị tại Mộc Châu Island - điểm đến di sản thiên nhiên khu vực hàng đầu thế giới và Avana Retreat – một bản nhỏ ẩn giữa rừng nguyên sinh, vẻ đẹp vô cùng đáng yêu của Xóm Pạnh. \r\nKhám phá ẩm thực địa phương Tây Bắc, thưởng thức bữa tối BBQ ngắm view kỷ lục GUINESS -Cầu Kính Bạch Long rực rỡ về đêm. \r\nĐắm chìm trong không gian lãng mạn, ngọt ngào của bữa ăn tối riêng tư bên thác Pùng - duy nhất chỉ có tại Avana  Retreat.\r\nKhám phá vẻ đẹp Nordic Village - tựa như ngôi làng Bắc Âu độc đáo tại Vân Hồ.\r\nHành trình khám phá vẻ đẹp của khu rừng Xóm Pạnh trên chiếc xe Jeep đời 1975 “không cánh cửa, không mái che, gió thổi tung mái tóc, hương vị núi đồi thấm đẫm trên làn da. ', 'Vân Hồ, Mộc Châu, Mai Châu', '3 ngày 2 đêm', 10000000.00, 5000000.00, 'uploads/tours/7572f3.png', 'uploads/tours/757613.png', 'uploads/tours/7579a1.png', 'active', '2025-04-18 09:55:35', '2025-04-18 09:55:35'),
(5, 'Walking Tour - Hạ Long', '- Hướng dẫn viên,\r\n- Tặng nước suối, khăn lạnh, bảo hiểm cho khách (tối đa 15 khách) đăng ký tour trước ngày khởi hành', 'Hạ Long', '1 ngày 1 đêm', 400000.00, 100000.00, 'uploads/tours/604d08.png', 'uploads/tours/604fdb.png', 'uploads/tours/605260.png', 'active', '2025-04-18 09:58:46', '2025-04-18 09:58:46'),
(6, 'Hạ Long - Chùa Linh Sơn - Đền Xã Tắc - Chùa Xuân Lan', '1. Xe ôtô 29-45 chỗ đưa đón theo chương trình tại Việt Nam\r\n2. Ăn 01 bữa chính: 150.000VNĐ/suất ( Không bao gồm đồ uống)\r\n3. Ăn sáng 01 bữa tại nhà hàng, tiêu chuẩn: 35.000đ/suất.\r\n4. Hướng dẫn viên tiếng Việt kinh nghiệm, nhiệt tình theo suốt chương trình\r\n5. Nước suối: 01 chai /người/ngày\r\n6. Khăn lạnh trên xe.\r\n7. Vé tham quan theo chương trình ( 01 lần).\r\n8. Bảo hiểm du lịch mức chi trả tối đa 120.000.000đ/vụ\r\n9. Thuế VAT theo quy định của nhà nước\r\n', 'Hạ Long, Chùa Linh Sơn, Đền Xã Tắc, Chùa Xuân Lan', '3 ngày 2 đêm', 890000.00, 500000.00, 'uploads/tours/2dc610.png', 'uploads/tours/2ddafd.png', 'uploads/tours/2deea5.png', 'active', '2025-04-18 10:57:22', '2025-04-18 10:57:22');

-- --------------------------------------------------------

--
-- Table structure for table `tour_dates`
--

CREATE TABLE `tour_dates` (
  `date_id` int NOT NULL,
  `tour_id` int NOT NULL,
  `departure_date` date NOT NULL,
  `available_seats` int NOT NULL DEFAULT '20',
  `status` enum('available','full','cancelled') DEFAULT 'available'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

--
-- Dumping data for table `tour_dates`
--

INSERT INTO `tour_dates` (`date_id`, `tour_id`, `departure_date`, `available_seats`, `status`) VALUES
(1, 1, '2025-04-29', 15, 'available'),
(2, 1, '2025-05-03', 20, 'available'),
(3, 1, '2025-05-07', 20, 'available'),
(4, 2, '2025-04-29', 20, 'available'),
(5, 2, '2025-05-04', 20, 'available'),
(6, 2, '2025-05-09', 20, 'available'),
(7, 3, '2025-04-29', 20, 'available'),
(8, 3, '2025-05-05', 20, 'available'),
(9, 3, '2025-05-11', 20, 'available'),
(10, 4, '2025-04-29', 15, 'available'),
(11, 4, '2025-05-03', 20, 'available'),
(12, 4, '2025-05-07', 20, 'available'),
(13, 5, '2025-04-29', 16, 'available'),
(14, 5, '2025-05-01', 20, 'available'),
(15, 5, '2025-05-03', 20, 'available'),
(16, 5, '2025-05-05', 20, 'available'),
(17, 6, '2025-04-29', 5, 'available'),
(18, 6, '2025-05-03', 20, 'available'),
(19, 6, '2025-05-07', 20, 'available'),
(20, 6, '2025-05-11', 9, 'available'),
(21, 6, '2025-05-15', 20, 'available');

-- --------------------------------------------------------

--
-- Table structure for table `users`
--

CREATE TABLE `users` (
  `user_id` int NOT NULL,
  `full_name` varchar(255) NOT NULL,
  `email` varchar(255) NOT NULL,
  `password` varchar(255) NOT NULL,
  `phone` varchar(20) NOT NULL,
  `address` text,
  `role` enum('customer','admin') DEFAULT 'customer',
  `status` enum('active','blocked','inactive') DEFAULT 'active',
  `created_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP,
  `updated_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

--
-- Dumping data for table `users`
--

INSERT INTO `users` (`user_id`, `full_name`, `email`, `password`, `phone`, `address`, `role`, `status`, `created_at`, `updated_at`) VALUES
(1, 'Admin', 'admin@gmail.com', '$2y$10$Omai3/4irC.XgyckBrGozOj7.y2MHBdr5mS03Qmyi5mtEJdzS8QHG', '0123456789', NULL, 'admin', 'active', '2025-04-18 09:22:04', '2025-04-18 09:28:52'),
(2, 'Nguyễn Văn A', 'user@gmail.com', '$2y$10$Omai3/4irC.XgyckBrGozOj7.y2MHBdr5mS03Qmyi5mtEJdzS8QHG', '0123456789', 'Hà Nội', 'customer', 'active', '2025-04-18 09:28:20', '2025-05-04 10:32:57'),
(3, 'Nguyễn Văn B', 'user2@gmail.com', '$2y$10$Omai3/4irC.XgyckBrGozOj7.y2MHBdr5mS03Qmyi5mtEJdzS8QHG', '0123456789', 'Hà Nội', 'customer', 'active', '2025-04-18 10:53:29', '2025-04-28 12:03:56'),
(4, 'Nguyễn Văn C', 'user3@gmail.com', '$2y$10$Omai3/4irC.XgyckBrGozOj7.y2MHBdr5mS03Qmyi5mtEJdzS8QHG', '0123456789', '', 'customer', 'active', '2025-04-28 16:43:28', '2025-04-28 16:43:28'),
(5, 'Nguyễn Văn D', 'user4@gmail.com', '$2y$10$Omai3/4irC.XgyckBrGozOj7.y2MHBdr5mS03Qmyi5mtEJdzS8QHG', '0123456789', '', 'customer', 'active', '2025-04-28 16:46:40', '2025-05-04 10:42:14');

--
-- Indexes for dumped tables
--

--
-- Indexes for table `bookings`
--
ALTER TABLE `bookings`
  ADD PRIMARY KEY (`booking_id`),
  ADD KEY `user_id` (`user_id`),
  ADD KEY `tour_id` (`tour_id`),
  ADD KEY `date_id` (`date_id`);

--
-- Indexes for table `reviews`
--
ALTER TABLE `reviews`
  ADD PRIMARY KEY (`review_id`),
  ADD KEY `user_id` (`user_id`),
  ADD KEY `tour_id` (`tour_id`),
  ADD KEY `booking_id` (`booking_id`);

--
-- Indexes for table `tours`
--
ALTER TABLE `tours`
  ADD PRIMARY KEY (`tour_id`);

--
-- Indexes for table `tour_dates`
--
ALTER TABLE `tour_dates`
  ADD PRIMARY KEY (`date_id`),
  ADD KEY `tour_id` (`tour_id`);

--
-- Indexes for table `users`
--
ALTER TABLE `users`
  ADD PRIMARY KEY (`user_id`),
  ADD UNIQUE KEY `email` (`email`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `bookings`
--
ALTER TABLE `bookings`
  MODIFY `booking_id` int NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=9;

--
-- AUTO_INCREMENT for table `reviews`
--
ALTER TABLE `reviews`
  MODIFY `review_id` int NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=5;

--
-- AUTO_INCREMENT for table `tours`
--
ALTER TABLE `tours`
  MODIFY `tour_id` int NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=7;

--
-- AUTO_INCREMENT for table `tour_dates`
--
ALTER TABLE `tour_dates`
  MODIFY `date_id` int NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=22;

--
-- AUTO_INCREMENT for table `users`
--
ALTER TABLE `users`
  MODIFY `user_id` int NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=6;

--
-- Constraints for dumped tables
--

--
-- Constraints for table `bookings`
--
ALTER TABLE `bookings`
  ADD CONSTRAINT `bookings_ibfk_1` FOREIGN KEY (`user_id`) REFERENCES `users` (`user_id`) ON DELETE CASCADE,
  ADD CONSTRAINT `bookings_ibfk_2` FOREIGN KEY (`tour_id`) REFERENCES `tours` (`tour_id`) ON DELETE CASCADE,
  ADD CONSTRAINT `bookings_ibfk_3` FOREIGN KEY (`date_id`) REFERENCES `tour_dates` (`date_id`) ON DELETE CASCADE;

--
-- Constraints for table `reviews`
--
ALTER TABLE `reviews`
  ADD CONSTRAINT `reviews_ibfk_1` FOREIGN KEY (`user_id`) REFERENCES `users` (`user_id`) ON DELETE CASCADE,
  ADD CONSTRAINT `reviews_ibfk_2` FOREIGN KEY (`tour_id`) REFERENCES `tours` (`tour_id`) ON DELETE CASCADE,
  ADD CONSTRAINT `reviews_ibfk_3` FOREIGN KEY (`booking_id`) REFERENCES `bookings` (`booking_id`) ON DELETE CASCADE;

--
-- Constraints for table `tour_dates`
--
ALTER TABLE `tour_dates`
  ADD CONSTRAINT `tour_dates_ibfk_1` FOREIGN KEY (`tour_id`) REFERENCES `tours` (`tour_id`) ON DELETE CASCADE;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
