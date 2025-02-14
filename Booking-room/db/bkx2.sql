-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1
-- Generation Time: Feb 06, 2025 at 05:20 AM
-- Server version: 10.4.32-MariaDB
-- PHP Version: 8.2.12

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Database: `bkx2`
--

-- --------------------------------------------------------

--
-- Table structure for table `bookings`
--

CREATE TABLE `bookings` (
  `id` int(11) NOT NULL,
  `user_id` int(11) DEFAULT NULL,
  `room_id` int(11) NOT NULL,
  `participants` int(11) NOT NULL,
  `booker_name` varchar(255) NOT NULL,
  `phone` varchar(20) NOT NULL,
  `start_date` date NOT NULL,
  `end_date` date NOT NULL,
  `start_time` time NOT NULL,
  `end_time` time NOT NULL,
  `purpose` varchar(255) NOT NULL,
  `equipment` varchar(255) DEFAULT NULL,
  `other_details` text DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `is_confirmed` tinyint(1) NOT NULL DEFAULT 0
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `bookings`
--

INSERT INTO `bookings` (`id`, `user_id`, `room_id`, `participants`, `booker_name`, `phone`, `start_date`, `end_date`, `start_time`, `end_time`, `purpose`, `equipment`, `other_details`, `created_at`, `is_confirmed`) VALUES
(20, 6, 79, 123, 'จิรพัฒน์ หนูราช', '093', '2025-02-05', '2025-02-05', '01:16:00', '01:16:00', 'ประชุม', '', '', '2025-02-05 18:16:53', 0),
(21, 6, 79, 123123, 'จิรพัฒน์ หนูราช', '093', '2025-02-05', '2025-02-05', '01:51:00', '01:51:00', 'ประชุม', '', '', '2025-02-05 18:51:23', 0),
(22, 6, 79, 12312, 'จิรพัฒน์ หนูราช', '093', '2025-02-05', '2025-02-05', '01:52:00', '01:52:00', 'ประชุม', '', '', '2025-02-05 18:52:44', 0),
(23, 6, 79, 123123, 'จิรพัฒน์ หนูราช', '093', '2025-02-05', '2025-02-05', '01:54:00', '01:54:00', 'ประชุม', '', '', '2025-02-05 18:54:08', 0),
(24, 6, 79, 123, 'จิรพัฒน์ หนูราช', '093', '2025-02-05', '2025-02-05', '02:16:00', '02:16:00', 'ประชุม', '', '', '2025-02-05 19:16:57', 0),
(25, 6, 79, 123, 'จิรพัฒน์ หนูราช', '093', '2025-02-05', '2025-02-05', '02:16:00', '02:16:00', 'ประชุม', '', '', '2025-02-05 19:17:01', 0),
(26, 6, 79, 600, 'จิรพัฒน์ หนูราช', '093', '2025-02-05', '2025-02-05', '02:19:00', '02:19:00', 'ประชุม', '', '', '2025-02-05 19:19:23', 0),
(27, 6, 79, 123, 'จิรพัฒน์ หนูราช', '093', '2025-02-05', '2025-02-05', '02:22:00', '03:22:00', 'ประชุม', '', '', '2025-02-05 19:22:42', 0),
(28, 6, 80, 12, 'จิรพัฒน์ หนูราช', '093', '2025-02-06', '2025-02-06', '02:24:00', '03:24:00', 'ประชุม', '', '', '2025-02-05 19:24:08', 0),
(29, 6, 79, 12, 'จิรพัฒน์ หนูราช', '093', '2025-02-08', '2025-02-09', '02:28:00', '02:28:00', 'ประชุม', '', '', '2025-02-05 19:28:15', 0),
(30, 6, 79, 12, 'จิรพัฒน์ หนูราช', '093', '2025-02-09', '2025-02-08', '02:28:00', '02:28:00', 'ประชุม', '', '', '2025-02-05 19:28:25', 2),
(32, 6, 81, 1, 'จิรพัฒน์ หนูราช', '093', '2025-02-05', '2025-02-05', '02:41:00', '03:41:00', 'ประชุม', '', '', '2025-02-05 19:42:04', 0);

-- --------------------------------------------------------

--
-- Table structure for table `rooms`
--

CREATE TABLE `rooms` (
  `id` int(11) NOT NULL,
  `room_name` varchar(100) NOT NULL,
  `description` text DEFAULT NULL,
  `location` varchar(255) DEFAULT NULL,
  `capacity` int(11) NOT NULL,
  `image` varchar(255) DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `is_visible` tinyint(1) DEFAULT 1
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `rooms`
--

INSERT INTO `rooms` (`id`, `room_name`, `description`, `location`, `capacity`, `image`, `created_at`, `is_visible`) VALUES
(79, 'ห้องประชุมอินทนิล', 'ห้องประชุมอินทนิล (ชั้น3อาคารพัสดุ)\r\nความจุ: 500-600 คน\r\nอุปกรณ์:\r\nโปรเจคเตอร์ความละเอียดสูง\r\nจอแสดงผล LED\r\nระบบเสียง (ไมโครโฟนแบบไร้สาย, ลำโพง)\r\nระบบปรับอากาศ\r\nโต๊ะและเก้าอี้แบบจัดเลี้ยงหรือประชุม', 'ชั้น3อาคารพัสดุ', 600, 'uploads/1738779845_89b23d9c-e510-4687-82ad-8fd196698aa0.jpg', '2025-02-05 14:56:40', 1),
(80, 'ห้องประชุมอาคารสันตุสสโก', 'ห้องประชุมอาคารสันตุสสโก (อาคารโดมอเนกประสงค์)\r\nความจุ: 2000-3000 คน อุปกรณ์: จอแสดงผล LED ระบบเสียง (ไมโครโฟนแบบไร้สาย, ลำโพง) เก้าอี้แบบจัดเลี้ยงหรือประชุม', 'อาคารโดมอเนกประสงค์', 3000, 'uploads/1738779860_5d88dcd2-f7e9-4b15-b3df-e916b4e90a3b.jpg', '2025-02-05 15:21:38', 1),
(81, 'ห้องโสตทัศนูปกรณ์', 'ห้องโสตทัศนูปกรณ์\r\nความจุ: 85 คน อุปกรณ์: โปรเจคเตอร์ความละเอียดสูง ระบบเสียง (ไมโครโฟนแบบไร้สาย, ลำโพง) ระบบปรับอากาศ โต๊ะและเก้าอี้แบบจัดเลี้ยงหรือประชุม', 'อาคาร 3 ชั้น 2', 85, 'uploads/1738779907_163e4e88-125f-44e8-9d85-b73707bdfd1b.jpg', '2025-02-05 15:24:55', 1),
(82, 'ห้องประชุมร่มประดู่ 1', 'ห้องประชุมร่มประดู่ 1 (ห้องประชุมสถาบัน) \r\nความจุ: 30-40 คน อุปกรณ์: จอแสดงผล LED ระบบเสียง (ไมโครโฟนแบบไร้สาย, ลำโพง) ระบบปรับอากาศ โต๊ะและเก้าอี้แบบจัดเลี้ยงหรือประชุม', 'ห้องประชุมสถาบัน', 40, 'uploads/1738780612_81d13b98-c3ca-482c-8eeb-81d87901eebc.jpg', '2025-02-05 15:25:39', 1),
(83, 'ห้องประชุมร่มประดู่ 2', 'ห้องประชุมร่มประดู่ 2 (อาคารอํานวยการ ชั้น 2)\r\nความจุ: 25-30 คน อุปกรณ์: จอแสดงผล LED ระบบเสียง (ไมโครโฟนแบบไร้สาย, ลำโพง) ระบบปรับอากาศ โต๊ะและเก้าอี้แบบจัดเลี้ยงหรือประชุม', 'อาคารอํานวยการ ชั้น 2', 30, 'uploads/1738779935_10a51b3f-23a4-495d-9d8a-83f2ad7ee08a.jpg', '2025-02-05 15:26:53', 1);

-- --------------------------------------------------------

--
-- Table structure for table `users`
--

CREATE TABLE `users` (
  `id` int(11) NOT NULL,
  `name` varchar(255) NOT NULL,
  `username` varchar(50) NOT NULL,
  `password` varchar(255) NOT NULL,
  `profile` varchar(255) DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `role_id` tinyint(1) NOT NULL DEFAULT 1,
  `phone` varchar(15) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `users`
--

INSERT INTO `users` (`id`, `name`, `username`, `password`, `profile`, `created_at`, `role_id`, `phone`) VALUES
(2, 'test', 'test2', '$2y$10$PRHUMrFheweD57Sa.anxm.LrZe0a48FuvzqrG/H1PLvZejeFUlT9W', NULL, '2024-10-27 16:20:32', 1, ''),
(3, 'test', 'test3', '$2y$10$wLWa2wla2OrBOLFPRrNjGOjAENqwgIdgZi1UeoD1WyKqu/eL9tsCa', NULL, '2024-10-30 13:55:20', 1, ''),
(6, 'จิรพัฒน์ หนูราช', 'admin', '$2y$10$Rl5jn/4maFi5kq.rrztafew.bQCAWrOXLQaBj1vwusnJo9P8vslNK', 'uploads/11.jpg', '2024-12-06 10:42:22', 0, '093'),
(10, 'จิรพัฒน์ หนูราช', '66309010034', '$2y$10$2oNr9hzfvy1uw4eVaI6V0.O6hHkZ49w4sh7Bfwc6wWd9oshOmYcey', 'uploads/11.jpg', '2025-02-04 18:52:01', 1, ''),
(12, 'test', 'te1', '$2y$10$hJ/6v6Y07iXlXYLf2/W2rOCcuUw5QWtg0RG6BSzIreBDglwBeeXIe', 'uploads/6518561685168.jpg', '2025-02-04 19:23:45', 1, ''),
(13, 'test', '123', '$2y$10$VtzKU9LaAdMmsioj5mQsnusHP6L13A2B4ZQzJIWxTi/O6ZNvLpfrK', NULL, '2025-02-04 19:24:02', 0, ''),
(14, 'testttttttttttttttttttttttttttttttttttt', 'tester', '$2y$10$Xhw6cb4Eemlgo0t9n3Js3.MU9bImABFqzLBKNf.2dooUhlajIoaG2', 'uploads/gekko-looks-like-frank-ocean-v0-zo3wxwppxqla1.png', '2025-02-05 12:15:46', 1, '0999999999');

--
-- Indexes for dumped tables
--

--
-- Indexes for table `bookings`
--
ALTER TABLE `bookings`
  ADD PRIMARY KEY (`id`),
  ADD KEY `room_id` (`room_id`),
  ADD KEY `user_id` (`user_id`);

--
-- Indexes for table `rooms`
--
ALTER TABLE `rooms`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `users`
--
ALTER TABLE `users`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `username` (`username`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `bookings`
--
ALTER TABLE `bookings`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=33;

--
-- AUTO_INCREMENT for table `rooms`
--
ALTER TABLE `rooms`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=84;

--
-- AUTO_INCREMENT for table `users`
--
ALTER TABLE `users`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=15;

--
-- Constraints for dumped tables
--

--
-- Constraints for table `bookings`
--
ALTER TABLE `bookings`
  ADD CONSTRAINT `bookings_ibfk_1` FOREIGN KEY (`room_id`) REFERENCES `rooms` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `bookings_ibfk_2` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE SET NULL;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
