-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1
-- Generation Time: May 10, 2026 at 05:13 PM
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
-- Database: `fitzone`
--

-- --------------------------------------------------------

--
-- Table structure for table `queries`
--

CREATE TABLE `queries` (
  `id` int(11) NOT NULL,
  `customer_id` int(11) DEFAULT NULL,
  `recipient_id` int(11) DEFAULT NULL,
  `query_text` text NOT NULL,
  `reply_text` text DEFAULT NULL,
  `status` varchar(20) NOT NULL DEFAULT 'pending',
  `created_at` timestamp NULL DEFAULT current_timestamp()
) ENGINE=MyISAM DEFAULT CHARSET=utf8 COLLATE=utf8_general_ci;

--
-- Dumping data for table `queries`
--

INSERT INTO `queries` (`id`, `customer_id`, `recipient_id`, `query_text`, `reply_text`, `status`, `created_at`) VALUES
(1, 14, NULL, 'One of the best gyms i have ever gone to.', NULL, 'pending', '2024-11-13 13:27:17'),
(2, 14, NULL, 'One of the best gyms i have ever gone to.', 'ok', 'replied', '2024-11-13 13:27:24'),
(3, 14, NULL, 'Good equipment available', 'Thank You.', 'replied', '2024-11-17 09:30:08'),
(4, 15, NULL, 'Feels like home.', NULL, 'pending', '2024-11-17 13:24:58'),
(6, 14, 1, 'feeling Strong', 'keep the good work on', 'replied', '2025-09-10 08:02:24'),
(7, 17, 2, 'good coaching', 'thx', 'replied', '2025-09-10 08:06:39');

-- --------------------------------------------------------

--
-- Table structure for table `class_sessions`
--

CREATE TABLE `class_sessions` (
  `id` int(11) NOT NULL,
  `title` varchar(120) NOT NULL,
  `trainer` varchar(100) NOT NULL,
  `session_day` varchar(20) NOT NULL,
  `session_time` varchar(20) NOT NULL,
  `capacity` int(11) NOT NULL DEFAULT 15,
  `is_active` tinyint(1) NOT NULL DEFAULT 1
) ENGINE=MyISAM DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `class_sessions`
--

INSERT INTO `class_sessions` (`id`, `title`, `trainer`, `session_day`, `session_time`, `capacity`, `is_active`) VALUES
(1, 'Strength Training', 'David Laid', 'Monday', '6:00 AM', 20, 1),
(2, 'Yoga & Flexibility', 'Anna Kaiser', 'Tuesday', '6:00 AM', 18, 1),
(3, 'High-Intensity Cardio', 'Sam Sulek', 'Wednesday', '8:00 AM', 16, 1),
(4, 'Pilates & Mobility', 'Emily Chen', 'Thursday', '2:00 PM', 14, 1),
(5, 'Nutrition Coaching', 'Sarah Lee', 'Friday', '12:00 PM', 12, 1),
(6, 'Sports Conditioning', 'Alex Wilson', 'Saturday', '10:00 AM', 18, 1);

-- --------------------------------------------------------

--
-- Table structure for table `class_bookings`
--

CREATE TABLE `class_bookings` (
  `id` int(11) NOT NULL,
  `user_id` int(11) NOT NULL,
  `session_id` int(11) NOT NULL,
  `status` varchar(20) NOT NULL DEFAULT 'booked',
  `created_at` timestamp NULL DEFAULT current_timestamp()
) ENGINE=MyISAM DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `membership_requests`
--

CREATE TABLE `membership_requests` (
  `id` int(11) NOT NULL,
  `user_id` int(11) NOT NULL,
  `plan_name` varchar(100) NOT NULL,
  `plan_price` varchar(40) NOT NULL,
  `status` varchar(20) NOT NULL DEFAULT 'pending',
  `created_at` timestamp NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=MyISAM DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `users`
--

CREATE TABLE `users` (
  `id` int(11) NOT NULL,
  `name` varchar(100) NOT NULL,
  `email` varchar(100) NOT NULL,
  `password` varchar(255) DEFAULT NULL,
  `role` enum('admin','management','customer') DEFAULT 'customer'
) ENGINE=MyISAM DEFAULT CHARSET=utf8 COLLATE=utf8_general_ci;

--
-- Dumping data for table `users`
--

INSERT INTO `users` (`id`, `name`, `email`, `password`, `role`) VALUES
(1, 'admin', 'admin@gmail.com', '$2y$10$5AYo/flDXA2LHSkbJT2ctO7THgKGzZohnVtH1dBrBVjFdeQtdYEVm', 'admin'),
(2, 'trainerone', 'trainerone@gmail.com', '$2y$10$1EL5Iu9N8a23KZ9EtpvhKOOlUPJdWxKoOGqiribcMTcPuD1sMl29W', 'management'),
(3, 'trainertwo', 'trainertwo@gmail.com', '$2y$10$fY6/I1n/dsTrTDFtAG0RSuNFNxHN7MBaZMiTRpKtWt4RuPmgNqKae', 'management'),
(4, 'trainerthree', 'trainerthree@gmail.com', '$2y$10$et7m7FMS/TfGhEL2FQ0x/uAF/k281YReBtGPncN9LGts1KjmIvvjC', 'management'),
(5, 'trainerfour', 'trainerfour@gmail.com', '$2y$10$rfo7o5InlF2j66GcluLmou0djTslntn2rsEziaPaCbO8VPYs7STRi', 'management'),
(6, 'staffone', 'staffone@gmail.com', '$2y$10$Th9G1LC2qeU8zsgJiHOxD.8VPI6oEylSEn4.zXisVM7b/YF3YFIAG', 'management'),
(7, 'stafftwo', 'stafftwo@gmail.com', '$2y$10$Fuqi8kmcjLSypqL4ta9P8ODlKBpIeAC6dmvl4Xyv73ifQNBjV7IW.', 'management'),
(8, 'Anuj', 'bunanu59@gmail.com', '$2y$10$ngmirtxJE77.d6T22RgDy.S2wLyMg4OVmXoJ8eLDjGqA8cIeiBIBa', 'customer'),
(17, 'nik', 'nik@gmail.com', '$2y$10$0KmeLwPyZxp56YRLuNakFOFkzJcDFbZA1ExAGjtQAb9DlNYepv022', 'customer'),
(14, 'Sam', 'sam@gmail.com', '$2y$10$ddFQ026Pic09rPhHPL9Rx.WqZBmB6pJ4H9nxGQWjHUS9D8a72sIX6', 'customer'),
(15, 'menuja', 'menuja@gmail.com', '$2y$10$JA.uoT1NIFQ9C05.VUytoe8J645v3tb1.NMkzYD2sLym.o1LsRtSm', 'customer'),
(18, 'beee', 'beee@gmail.com', '$2y$10$HMVl6BstYvChtDprpZUTE.nG4Wx/XP/AM4/Y7ZXOCkhL5Jx62aWL2', 'customer');

--
-- Indexes for dumped tables
--

--
-- Indexes for table `queries`
--
ALTER TABLE `queries`
  ADD PRIMARY KEY (`id`),
  ADD KEY `customer_id` (`customer_id`);

--
-- Indexes for table `class_sessions`
--
ALTER TABLE `class_sessions`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `class_bookings`
--
ALTER TABLE `class_bookings`
  ADD PRIMARY KEY (`id`),
  ADD KEY `user_id` (`user_id`),
  ADD KEY `session_id` (`session_id`);

--
-- Indexes for table `membership_requests`
--
ALTER TABLE `membership_requests`
  ADD PRIMARY KEY (`id`),
  ADD KEY `user_id` (`user_id`);

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
-- AUTO_INCREMENT for table `queries`
--
ALTER TABLE `queries`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=8;

--
-- AUTO_INCREMENT for table `class_sessions`
--
ALTER TABLE `class_sessions`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=7;

--
-- AUTO_INCREMENT for table `class_bookings`
--
ALTER TABLE `class_bookings`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `membership_requests`
--
ALTER TABLE `membership_requests`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `users`
--
ALTER TABLE `users`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=19;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
