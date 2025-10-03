-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1
-- Generation Time: Oct 03, 2025 at 10:36 AM
-- Server version: 10.11.14-MariaDB
-- PHP Version: 8.2.12

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Database: `transit_manager`
--

-- --------------------------------------------------------

--
-- Table structure for table `admins`
--

CREATE TABLE `admins` (
  `id` int(11) NOT NULL,
  `full_name` varchar(255) NOT NULL,
  `email` varchar(255) NOT NULL,
  `password` varchar(255) NOT NULL,
  `dob` date NOT NULL,
  `role` varchar(50) NOT NULL,
  `id_card_no` varchar(255) NOT NULL,
  `phone` varchar(20) NOT NULL,
  `address` text NOT NULL,
  `status` varchar(20) NOT NULL DEFAULT 'active',
  `last_login` datetime DEFAULT NULL,
  `created_at` datetime NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `admins`
--

INSERT INTO `admins` (`id`, `full_name`, `email`, `password`, `dob`, `role`, `id_card_no`, `phone`, `address`, `status`, `last_login`, `created_at`) VALUES
(6, 'Siva Balaji SM', 'sivathetechie24@gmail.com', '$2y$10$gVeXQ9VgOl6.a2S1NYTC4ugB47tbgthunoCsJVFJBjXwVFPVEKseC', '2006-03-01', 'Shed Manager', 'TN60UID21101', '7904538655', '', 'active', '2025-09-30 12:57:49', '2025-09-13 17:32:43'),
(7, 'Santhoshkumar V', 'santhoshkumarsk@gmail.com', '$2y$10$fV15T5plNbe0GCQFjFGGguPhxRWvwDhoueRsq4Fkhesp/UbVpJ46u', '2006-03-01', 'Shed Manager', 'TN60UID21093', '969958856', '', 'active', NULL, '2025-09-13 17:33:32'),
(8, 'Syed Abzal A', 'syedabzal@gmail.com', '$2y$10$O6SC3RaWYSeDlyLb7GYonuLsym.8xLWNuxYr5Q9IldeaOAN/xNVrS', '2005-07-21', 'Bus Driver', 'TN60UID21113', '9966644788', '', 'active', '2025-09-28 00:19:37', '2025-09-13 23:04:19'),
(9, 'Nishantha Moorthy', 'moorthy@gmail.com', '$2y$10$DXLM9ypWfkNETcaid/zL6eDUKGrAhmPLN8fgH6AbGmMSWkwPLFBDq', '2006-05-05', 'Bus Conductor', 'TN60UID21068', '8610029165', '', 'active', NULL, '2025-09-14 18:15:58'),
(10, 'Sanjay R', 'sanjayr@gmail.com', '$2y$10$ITkJ6.xW3c2D4TmfIWkcne8OIunucnq71Qw4lmSzz.auXHL8wcQR.', '2005-08-02', 'Bus Driver', 'TN60UID21089', '63852148574', '', 'active', '2025-09-28 00:20:14', '2025-09-15 11:42:53'),
(11, 'Sasi R', 'sasir@gmail.com', '$2y$10$dGWinZSA2bizQ4/p456aA.A0ni1uke8Ni7U2mwn4W7Tuzw9ldipQ2', '2005-08-02', 'Bus Conductor', 'TN60UID21095', '6354785447', '', 'active', NULL, '2025-09-15 11:48:56'),
(12, 'Joseph Kuruvila ', 'josephkuruvila@stm.com', '$2y$10$OdPTi//VWlYr8EeS.MKC.OFs9irx0mBxiX2r/ZyLHCcSuE3WQ5eh.', '1998-03-01', 'Bus Driver', 'TN60UID22010', '8655645584', '', 'active', '2025-09-30 13:46:07', '2025-09-28 00:14:15'),
(13, 'Parthiban', 'parthiban@stm.com', '$2y$10$qSD/NS7sQth0QDumuwXHFO.IjR7FVV6gQaxZuA2TBRrkYZx0Kvg0a', '1997-08-05', 'Bus Conductor', 'TN60UID22011', '6354785212', '', 'active', NULL, '2025-09-28 00:15:13'),
(14, 'Suresh Kumar B', 'sivabalaji2335@gmail.coms', '$2y$10$rPFSdR2YyHON8enlBRonUuHtDHK31D9bYFVGNgd0zFKUfMFYoJzTm', '2222-05-14', 'Shed Manager', 'wewed', '5119819', '', 'active', NULL, '2025-09-28 00:17:42'),
(15, 'Rakesh Kumar V', 'rakeshkumar@stm.com', '$2y$10$dITfK5J/QnPpf/2rfSRE/ucy92e6MDIAd5Gb6zBdvR8NeX0zwmuwm', '2006-03-01', 'Bus Driver', 'TN60UID664433', '9621461475', '', 'active', '2025-09-30 13:45:33', '2025-09-30 13:39:20'),
(16, 'Raguvaran S', 'ragu@stm.com', '$2y$10$4L0iC13/MquD8QM25PPBje3go2FOa4rZBloL9u4P5l1IN7zpn5lDO', '2006-04-20', 'Bus Conductor', 'TN60UID843234', '8478465132', '', 'active', NULL, '2025-09-30 13:40:07'),
(17, 'Sujeet Kumar RV', 'sujeet@stm.com', '$2y$10$Up2MvAnO4qRbC6OPVC4Tq.MseVWaOChWH40Mp7RMnOm4HOvp7itu6', '2006-05-03', 'Bus Driver', 'TN60UID543223', '9714545113', '', 'active', '2025-09-30 13:47:03', '2025-09-30 13:40:52'),
(18, 'Joseph John Edwin S', 'johnjoseph@stm.com', '$2y$10$QgXoFcQSKw09x2JcwJyi0.Lk4tEf8BVPgIMKrgmdA9v1q1c2nUI82', '2006-07-06', 'Bus Conductor', 'TN60UID654323', '6741482456', '', 'active', NULL, '2025-09-30 13:42:03');

-- --------------------------------------------------------

--
-- Table structure for table `buses`
--

CREATE TABLE `buses` (
  `bus_id` int(11) NOT NULL,
  `bus_number` varchar(50) NOT NULL,
  `bus_name` varchar(255) DEFAULT NULL,
  `route_id` int(11) NOT NULL,
  `driver_id` int(11) NOT NULL,
  `conductor_id` int(11) NOT NULL,
  `capacity` int(11) NOT NULL,
  `year_of_manufacture` int(11) NOT NULL,
  `bus_type` varchar(50) NOT NULL,
  `notes` text DEFAULT NULL,
  `created_at` datetime NOT NULL,
  `current_lat` varchar(50) DEFAULT NULL,
  `current_lon` varchar(50) DEFAULT NULL,
  `location_last_updated` datetime DEFAULT NULL,
  `status` varchar(20) NOT NULL DEFAULT 'Inactive'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `buses`
--

INSERT INTO `buses` (`bus_id`, `bus_number`, `bus_name`, `route_id`, `driver_id`, `conductor_id`, `capacity`, `year_of_manufacture`, `bus_type`, `notes`, `created_at`, `current_lat`, `current_lon`, `location_last_updated`, `status`) VALUES
(3, 'TN 60 AB 3142', 'Hill Rider', 9, 8, 9, 60, 2022, 'AC', 'Great Bus\r\n', '2025-09-14 22:14:08', '11.313825', '77.550796', '2025-09-28 00:19:44', 'Active'),
(4, 'TN 45 AC 2211', 'KATHIJA', 11, 10, 11, 60, 2025, 'AC', 'Nice Bus', '2025-09-15 11:49:52', '0.0', '0.0', '2025-09-28 00:26:07', 'Inactive'),
(5, 'TN 60 SM 6379', 'Vaigai Rider', 12, 12, 13, 59, 2025, 'AC', 'Vaigai Darling\r\n', '2025-09-28 00:16:46', '11.313825', '77.550796', '2025-09-30 13:46:56', 'Active'),
(6, 'TN 80 DC 4213', 'Kujili Kumba', 9, 15, 16, 60, 2022, 'AC', 'Very Nice Bus...', '2025-09-30 13:43:09', '11.313825', '77.550796', '2025-09-30 13:45:58', 'Active'),
(7, 'TN 60 AF 5543', 'Ghost Rider', 9, 17, 18, 45, 2021, 'Non-AC', 'Very Good BS5 Coach.', '2025-09-30 13:44:02', '11.313825', '77.550796', '2025-09-30 13:47:09', 'Active');

-- --------------------------------------------------------

--
-- Table structure for table `routes`
--

CREATE TABLE `routes` (
  `route_id` int(11) NOT NULL,
  `route_number` varchar(50) NOT NULL,
  `start_point` varchar(255) NOT NULL,
  `end_point` varchar(255) NOT NULL,
  `created_at` datetime NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `routes`
--

INSERT INTO `routes` (`route_id`, `route_number`, `start_point`, `end_point`, `created_at`) VALUES
(9, 'THENI - TIRUPPUR', 'THENI', 'TIRUPPUR', '2025-09-14 17:40:00'),
(10, 'PERUNDURAI -  GOBI', 'PERUNDURAI', 'GOBI', '2025-09-14 17:54:49'),
(11, 'THENI - ERODE', 'THENI', 'ERODE', '2025-09-15 11:46:29'),
(12, 'PERIYAKULAM - ANDIPATTI', 'PERIYAKULAM', 'ANDIPATTI', '2025-09-28 00:12:12');

-- --------------------------------------------------------

--
-- Table structure for table `route_stops`
--

CREATE TABLE `route_stops` (
  `stop_id` int(11) NOT NULL,
  `route_id` int(11) NOT NULL,
  `stop_name` varchar(255) NOT NULL,
  `sequence_number` int(11) NOT NULL,
  `latitude` varchar(255) DEFAULT NULL,
  `longitude` varchar(255) DEFAULT NULL,
  `timestamp` time DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `route_stops`
--

INSERT INTO `route_stops` (`stop_id`, `route_id`, `stop_name`, `sequence_number`, `latitude`, `longitude`, `timestamp`) VALUES
(18, 9, 'THENI', 1, '9.8692558', '77.4222974', '10:00:00'),
(19, 9, 'PERIYAKULAM', 2, '10.1198663', '77.5467069', '10:30:00'),
(20, 9, 'KOVIL VAZHI TIRUPPUR', 3, '11.0582456', '77.3883476', '14:00:00'),
(21, 10, 'PERUNDURAI', 1, '11.2539029', '77.6288316', '16:30:00'),
(22, 10, 'SANITORIUM', 2, '11.2847295', '77.565506', '16:40:00'),
(23, 10, 'SLATTER NAGAR', 3, '11.27564', '77.58794', '16:50:00'),
(24, 10, 'THUDUPATHI', 4, '11.302577', '77.5498457', '17:00:00'),
(25, 10, 'GOBI', 5, '11.4551821', '77.4350666', '18:00:00'),
(26, 11, 'THENI', 1, '9.869256', '77.422297', '14:00:00'),
(27, 11, 'KARUR', 2, '10.8217671', '78.3828654', '17:00:00'),
(28, 11, 'ERODE', 3, '11.4905281', '11.4905281', '19:00:00'),
(29, 12, 'Periyakulam', 1, '10.124916505276305', '77.54613489743417', '22:00:00'),
(30, 12, 'Vadugapatti', 2, '10.105688227796803', '77.57201458594145', '10:10:00'),
(31, 12, 'Melmangalam', 3, '10.101374826252595', '77.58130912338474', '10:15:00'),
(32, 12, 'Jeyamangalam', 4, '10.101374826252595', '77.60937520379343', '10:25:00'),
(33, 12, 'Rajshree Sugar Factory', 5, '10.059632759198726', '77.61032708896596', '10:35:00'),
(34, 12, 'Vaigai Dam', 6, '10.059632759198726', '77.60044259842181', '10:40:00'),
(35, 12, 'Andipatti', 7, '9.999116897981503', '77.6208347766179', '10:55:00');

--
-- Indexes for dumped tables
--

--
-- Indexes for table `admins`
--
ALTER TABLE `admins`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `email` (`email`),
  ADD UNIQUE KEY `id_card_no` (`id_card_no`);

--
-- Indexes for table `buses`
--
ALTER TABLE `buses`
  ADD PRIMARY KEY (`bus_id`),
  ADD UNIQUE KEY `bus_number` (`bus_number`),
  ADD KEY `route_id` (`route_id`),
  ADD KEY `driver_id` (`driver_id`),
  ADD KEY `conductor_id` (`conductor_id`);

--
-- Indexes for table `routes`
--
ALTER TABLE `routes`
  ADD PRIMARY KEY (`route_id`),
  ADD UNIQUE KEY `route_number` (`route_number`);

--
-- Indexes for table `route_stops`
--
ALTER TABLE `route_stops`
  ADD PRIMARY KEY (`stop_id`),
  ADD KEY `route_id` (`route_id`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `admins`
--
ALTER TABLE `admins`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=19;

--
-- AUTO_INCREMENT for table `buses`
--
ALTER TABLE `buses`
  MODIFY `bus_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=8;

--
-- AUTO_INCREMENT for table `routes`
--
ALTER TABLE `routes`
  MODIFY `route_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=13;

--
-- AUTO_INCREMENT for table `route_stops`
--
ALTER TABLE `route_stops`
  MODIFY `stop_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=36;

--
-- Constraints for dumped tables
--

--
-- Constraints for table `buses`
--
ALTER TABLE `buses`
  ADD CONSTRAINT `buses_ibfk_1` FOREIGN KEY (`route_id`) REFERENCES `routes` (`route_id`),
  ADD CONSTRAINT `buses_ibfk_2` FOREIGN KEY (`driver_id`) REFERENCES `admins` (`id`),
  ADD CONSTRAINT `buses_ibfk_3` FOREIGN KEY (`conductor_id`) REFERENCES `admins` (`id`);

--
-- Constraints for table `route_stops`
--
ALTER TABLE `route_stops`
  ADD CONSTRAINT `route_stops_ibfk_1` FOREIGN KEY (`route_id`) REFERENCES `routes` (`route_id`);
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
