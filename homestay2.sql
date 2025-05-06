-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1
-- Generation Time: May 05, 2025 at 07:41 PM
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
-- Database: `homestay2`
--

-- --------------------------------------------------------

--
-- Table structure for table `applications`
--

CREATE TABLE `applications` (
  `application_id` bigint(20) NOT NULL,
  `opportunity_id` bigint(20) NOT NULL,
  `traveler_id` bigint(20) NOT NULL,
  `status` enum('pending','accepted','rejected') NOT NULL DEFAULT 'pending',
  `comment` varchar(255) NOT NULL,
  `applied_date` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `card`
--

CREATE TABLE `card` (
  `card_id` bigint(20) NOT NULL,
  `card_number` varchar(255) NOT NULL,
  `expiry_date` date DEFAULT NULL,
  `cvv` varchar(255) DEFAULT NULL,
  `card_holder_name` varchar(255) DEFAULT NULL,
  `billing_address` varchar(255) DEFAULT NULL,
  `traveler_id` bigint(20) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `fee_transaction`
--

CREATE TABLE `fee_transaction` (
  `fee_id` bigint(20) NOT NULL,
  `transaction_reference` varchar(255) DEFAULT NULL,
  `traveler_id` bigint(20) DEFAULT NULL,
  `payment_method` enum('credit_card','paypal','bank_transfer') DEFAULT NULL,
  `amount` float DEFAULT NULL,
  `date` date DEFAULT NULL,
  `status` enum('pending','completed','failed') DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `hosts`
--

CREATE TABLE `hosts` (
  `host_id` bigint(20) NOT NULL,
  `property_type` enum('teaching','farming','cooking','childcare') DEFAULT NULL,
  `preferred_language` varchar(255) DEFAULT NULL,
  `joined_date` date DEFAULT NULL,
  `bio` text DEFAULT NULL,
  `rate` float DEFAULT NULL,
  `location` varchar(255) DEFAULT NULL,
  `status` enum('active','reported') DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `hosts`
--

INSERT INTO `hosts` (`host_id`, `property_type`, `preferred_language`, `joined_date`, `bio`, `rate`, `location`, `status`) VALUES
(1, 'teaching', 'en', '2025-05-04', 'iiiiiiii', NULL, 'cairo', NULL),
(8, 'teaching', 'en', '2025-05-04', 'asassas', NULL, 'cairo', NULL),
(9, 'teaching', 'en', '2025-05-04', '222222', NULL, 'cairo', NULL),
(10, 'farming', 'en', '2025-05-04', '1234', NULL, 'cairo', NULL),
(11, 'farming', 'en', '2025-05-05', '1111', NULL, 'cairo', NULL);

-- --------------------------------------------------------

--
-- Table structure for table `message`
--

CREATE TABLE `message` (
  `message_id` bigint(20) NOT NULL,
  `sender_id` bigint(20) DEFAULT NULL,
  `receiver_id` bigint(20) DEFAULT NULL,
  `content` text DEFAULT NULL,
  `timestamp` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp(),
  `status` varchar(255) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `notification`
--

CREATE TABLE `notification` (
  `notification_id` bigint(20) NOT NULL,
  `receiver_id` bigint(20) DEFAULT NULL,
  `content` text DEFAULT NULL,
  `timestamp` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `opportunity`
--

CREATE TABLE `opportunity` (
  `opportunity_id` bigint(20) NOT NULL,
  `opportunity_photo` longblob DEFAULT NULL,
  `title` varchar(255) DEFAULT NULL,
  `description` text DEFAULT NULL,
  `location` varchar(255) DEFAULT NULL,
  `start_date` date DEFAULT NULL,
  `end_date` date DEFAULT NULL,
  `category` enum('teaching','farming','cooking','childcare') DEFAULT NULL,
  `host_id` bigint(20) DEFAULT NULL,
  `status` enum('open','closed','cancelled','deleted','reported') DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp(),
  `requirements` text DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `opportunity`
--

INSERT INTO `opportunity` (`opportunity_id`, `opportunity_photo`, `title`, `description`, `location`, `start_date`, `end_date`, `category`, `host_id`, `status`, `created_at`, `requirements`) VALUES
(1, 0x2e2e2f75706c6f6164732f6f70706f7274756e69746965732f313734363337323636365f626c6f672d332e6a7067, 'aaaa', 'sdaffdfdadsf', 'cairo', '2222-02-22', '2222-02-22', 'teaching', 1, 'open', '2025-05-04 15:31:06', 'sfsasfdsdaadfs'),
(2, 0x2e2e2f75706c6f6164732f6f70706f7274756e69746965732f313734363338363838335f62726561646372756d622d62672e6a7067, 'aaaaaaaa', 'assad', 'cairo', '2025-05-16', '2025-06-03', 'farming', 10, 'open', '2025-05-04 19:28:03', 'safdsafsda'),
(3, 0x2e2e2f75706c6f6164732f6f70706f7274756e69746965732f313734363436353137375f333033383734312e6a7067, 'ssss', 'aaaaaaaaaaaaaaaa', 'aaaaaa', '2111-11-11', '2222-02-22', 'teaching', 1, 'open', '2025-05-05 17:12:57', 'aaaaaaaaaa'),
(4, 0x2e2e2f75706c6f6164732f6f70706f7274756e69746965732f313734363436353933335f53637265656e73686f7420323032352d30332d3233203233353835372e706e67, 'dddddddd', 'ddddddddddd', 'ddd', '1111-11-11', '2222-12-22', 'childcare', 11, 'open', '2025-05-05 17:25:33', 'dddddddddddddddddd');

-- --------------------------------------------------------

--
-- Table structure for table `report`
--

CREATE TABLE `report` (
  `report_id` bigint(20) NOT NULL,
  `reported_by_id` bigint(20) DEFAULT NULL,
  `target_user_id` bigint(20) DEFAULT NULL,
  `report_content` text DEFAULT NULL,
  `status` enum('open','reviewed','resolved') DEFAULT NULL,
  `report_type` enum('user','opportunity','message') DEFAULT NULL,
  `admin_response` text DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `review`
--

CREATE TABLE `review` (
  `review_id` bigint(20) NOT NULL,
  `sender_id` bigint(20) DEFAULT NULL,
  `receiver_id` bigint(20) DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp(),
  `opportunity_id` bigint(20) DEFAULT NULL,
  `rating` float DEFAULT NULL,
  `comment` text DEFAULT NULL,
  `is_reported` tinyint(1) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `support_content`
--

CREATE TABLE `support_content` (
  `content_id` bigint(20) NOT NULL,
  `title` varchar(255) NOT NULL,
  `content` text DEFAULT NULL,
  `category` enum('account','safety','opportunity','other') DEFAULT NULL,
  `status` enum('active','archived') DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp(),
  `last_updated` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `traveler`
--

CREATE TABLE `traveler` (
  `traveler_id` bigint(20) NOT NULL,
  `skill` text DEFAULT NULL,
  `language_spoken` text DEFAULT NULL,
  `preferred_language` varchar(255) DEFAULT NULL,
  `joined_date` date DEFAULT NULL,
  `bio` text DEFAULT NULL,
  `rate` float DEFAULT NULL,
  `location` varchar(255) DEFAULT NULL,
  `created_at` date DEFAULT NULL,
  `status` enum('active','reported') DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `traveler`
--

INSERT INTO `traveler` (`traveler_id`, `skill`, `language_spoken`, `preferred_language`, `joined_date`, `bio`, `rate`, `location`, `created_at`, `status`) VALUES
(3, 'qqqq', 'qqqq', 'qqqqqq', '2025-05-04', 'qqqqq', NULL, 'cairo', NULL, NULL),
(12, 'gamer', 'arabic', 'a', '2025-05-05', 'ssseqeq', NULL, 'cairo', NULL, NULL);

-- --------------------------------------------------------

--
-- Table structure for table `users`
--

CREATE TABLE `users` (
  `user_id` bigint(20) NOT NULL,
  `national_id` varchar(255) DEFAULT NULL,
  `user_type` enum('host','traveler','admin') DEFAULT NULL,
  `password` varchar(255) DEFAULT NULL,
  `email` varchar(255) DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp(),
  `gender` enum('male','female') DEFAULT NULL,
  `date_of_birth` date DEFAULT NULL,
  `first_name` varchar(255) DEFAULT NULL,
  `last_name` varchar(255) DEFAULT NULL,
  `phone_number` varchar(255) DEFAULT NULL,
  `profile_picture` longblob DEFAULT NULL,
  `last_login` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `users`
--

INSERT INTO `users` (`user_id`, `national_id`, `user_type`, `password`, `email`, `created_at`, `gender`, `date_of_birth`, `first_name`, `last_name`, `phone_number`, `profile_picture`, `last_login`) VALUES
(1, '30506210104157', 'host', '$2y$10$ViiphTti/32rIhyT8qLLPe5cIokOyod32nxnpUf1dvYF89fo75KIy', 'abdo.badawi50@gmail.com', '2025-05-04 13:37:19', 'male', '2000-02-22', 'abdoo', 'badawi', '01120148262', 0x2e2e2f75706c6f6164732f70726f66696c655f70696374757265732f313734363336353833395f6261646177692e6a7067, '2025-05-04 13:37:19'),
(3, '1111', 'traveler', '$2y$10$LQhCrs18xhK3J01J9v.RJOQiDW/Xm6Ba9CvihviHFXxthnon0.xoe', 'malak@gmail.com', '2025-05-04 13:40:52', 'male', '2000-02-22', 'abdoo', 'haaa', '011201481111111', 0x2e2e2f75706c6f6164732f70726f66696c655f70696374757265732f313734363336363035325f6261646177692e6a7067, '2025-05-04 13:40:52'),
(8, '1234', 'host', '$2y$10$ZijGGZ7ozrxWdiO/L0ca1u6Dj8zgzCehCn/.7R.5rk95yqGRJ83S2', 'faisal@gmail.com', '2025-05-04 17:54:35', 'male', '2222-02-22', 'faisal', 'badawi', '111111111111', 0x2e2e2f75706c6f6164732f70726f66696c655f70696374757265732f313734363338313237355f6164656c73686b616c2e6a7067, '2025-05-04 17:54:35'),
(9, '99999999', 'host', '$2y$10$BqC7FvqOshoSNADVA79Ib.iXu4mV5S1.JoQeE/5jzCKTiUQvLQ4IO', 'fff@gmail.com', '2025-05-04 17:59:23', 'male', '2222-02-22', 'aaaa', 'fffff', '555', 0x2e2e2f75706c6f6164732f70726f66696c655f70696374757265732f313734363338313536335f6164656c73686b616c2e6a7067, '2025-05-04 17:59:23'),
(10, '777777', 'host', '$2y$10$.ATZRJs.izKJVwwKPVLkUOtGqbVSHPGANW3F8dAnrYedgoxq6GDyq', 'saaaa@gmail.com', '2025-05-04 19:27:26', 'female', '2025-05-11', 'saa', 'sa', '11112211', 0x2e2e2f75706c6f6164732f70726f66696c655f70696374757265732f313734363338363834365f6578706c6f72652d746f75722d312e6a7067, '2025-05-04 19:27:26'),
(11, 'a1a1', 'host', '$2y$10$T/YupG1qRuhKCioO0KwY1u5alAmGRk2SzIiu43ZzwWT84bx/D9Ab.', 'a1@gmail.com', '2025-05-05 17:25:02', 'male', '2002-02-22', 'a1', 'a1', '12121212', 0x2e2e2f75706c6f6164732f70726f66696c655f70696374757265732f313734363436353930325f53637265656e73686f7420323032352d30352d3031203030333530352e706e67, '2025-05-05 17:25:02'),
(12, '55554', 'traveler', '$2y$10$X/.cLmUd3NoTE4bi0bzOWegyFf3K5L0UzyHQF0W3vcLeyEUkTmSV6', 'e1@gmail.com', '2025-05-05 17:39:03', 'male', '2222-02-22', 'e111', 'a2e111', '1919191', 0x2e2e2f75706c6f6164732f70726f66696c655f70696374757265732f313734363436363734335f53637265656e73686f7420323032352d30332d3138203030343534322e706e67, '2025-05-05 17:39:03');

--
-- Indexes for dumped tables
--

--
-- Indexes for table `applications`
--
ALTER TABLE `applications`
  ADD PRIMARY KEY (`application_id`),
  ADD KEY `opportunity_id` (`opportunity_id`),
  ADD KEY `traveler_id` (`traveler_id`);

--
-- Indexes for table `card`
--
ALTER TABLE `card`
  ADD PRIMARY KEY (`card_id`),
  ADD KEY `traveler_id` (`traveler_id`);

--
-- Indexes for table `fee_transaction`
--
ALTER TABLE `fee_transaction`
  ADD PRIMARY KEY (`fee_id`),
  ADD KEY `traveler_id` (`traveler_id`);

--
-- Indexes for table `hosts`
--
ALTER TABLE `hosts`
  ADD PRIMARY KEY (`host_id`);

--
-- Indexes for table `message`
--
ALTER TABLE `message`
  ADD PRIMARY KEY (`message_id`),
  ADD KEY `sender_id` (`sender_id`),
  ADD KEY `receiver_id` (`receiver_id`);

--
-- Indexes for table `notification`
--
ALTER TABLE `notification`
  ADD PRIMARY KEY (`notification_id`),
  ADD KEY `receiver_id` (`receiver_id`);

--
-- Indexes for table `opportunity`
--
ALTER TABLE `opportunity`
  ADD PRIMARY KEY (`opportunity_id`),
  ADD KEY `host_id` (`host_id`);

--
-- Indexes for table `report`
--
ALTER TABLE `report`
  ADD PRIMARY KEY (`report_id`),
  ADD KEY `reported_by_id` (`reported_by_id`),
  ADD KEY `target_user_id` (`target_user_id`);

--
-- Indexes for table `review`
--
ALTER TABLE `review`
  ADD PRIMARY KEY (`review_id`),
  ADD KEY `receiver_id` (`receiver_id`),
  ADD KEY `opportunity_id` (`opportunity_id`),
  ADD KEY `review_ibfk_2` (`sender_id`);

--
-- Indexes for table `support_content`
--
ALTER TABLE `support_content`
  ADD PRIMARY KEY (`content_id`);

--
-- Indexes for table `traveler`
--
ALTER TABLE `traveler`
  ADD PRIMARY KEY (`traveler_id`);

--
-- Indexes for table `users`
--
ALTER TABLE `users`
  ADD PRIMARY KEY (`user_id`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `applications`
--
ALTER TABLE `applications`
  MODIFY `application_id` bigint(20) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `card`
--
ALTER TABLE `card`
  MODIFY `card_id` bigint(20) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `fee_transaction`
--
ALTER TABLE `fee_transaction`
  MODIFY `fee_id` bigint(20) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `message`
--
ALTER TABLE `message`
  MODIFY `message_id` bigint(20) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `notification`
--
ALTER TABLE `notification`
  MODIFY `notification_id` bigint(20) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `opportunity`
--
ALTER TABLE `opportunity`
  MODIFY `opportunity_id` bigint(20) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=5;

--
-- AUTO_INCREMENT for table `report`
--
ALTER TABLE `report`
  MODIFY `report_id` bigint(20) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `review`
--
ALTER TABLE `review`
  MODIFY `review_id` bigint(20) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `support_content`
--
ALTER TABLE `support_content`
  MODIFY `content_id` bigint(20) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `users`
--
ALTER TABLE `users`
  MODIFY `user_id` bigint(20) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=13;

--
-- Constraints for dumped tables
--

--
-- Constraints for table `applications`
--
ALTER TABLE `applications`
  ADD CONSTRAINT `applications_ibfk_1` FOREIGN KEY (`opportunity_id`) REFERENCES `opportunity` (`opportunity_id`),
  ADD CONSTRAINT `applications_ibfk_2` FOREIGN KEY (`traveler_id`) REFERENCES `users` (`user_id`);

--
-- Constraints for table `card`
--
ALTER TABLE `card`
  ADD CONSTRAINT `card_ibfk_1` FOREIGN KEY (`traveler_id`) REFERENCES `users` (`user_id`);

--
-- Constraints for table `fee_transaction`
--
ALTER TABLE `fee_transaction`
  ADD CONSTRAINT `fee_transaction_ibfk_1` FOREIGN KEY (`traveler_id`) REFERENCES `users` (`user_id`);

--
-- Constraints for table `hosts`
--
ALTER TABLE `hosts`
  ADD CONSTRAINT `hosts_ibfk_1` FOREIGN KEY (`host_id`) REFERENCES `users` (`user_id`) ON DELETE CASCADE ON UPDATE CASCADE;

--
-- Constraints for table `message`
--
ALTER TABLE `message`
  ADD CONSTRAINT `message_ibfk_1` FOREIGN KEY (`sender_id`) REFERENCES `users` (`user_id`),
  ADD CONSTRAINT `message_ibfk_2` FOREIGN KEY (`receiver_id`) REFERENCES `users` (`user_id`);

--
-- Constraints for table `notification`
--
ALTER TABLE `notification`
  ADD CONSTRAINT `notification_ibfk_1` FOREIGN KEY (`receiver_id`) REFERENCES `users` (`user_id`);

--
-- Constraints for table `opportunity`
--
ALTER TABLE `opportunity`
  ADD CONSTRAINT `opportunity_ibfk_1` FOREIGN KEY (`host_id`) REFERENCES `users` (`user_id`);

--
-- Constraints for table `report`
--
ALTER TABLE `report`
  ADD CONSTRAINT `report_ibfk_1` FOREIGN KEY (`reported_by_id`) REFERENCES `users` (`user_id`),
  ADD CONSTRAINT `report_ibfk_2` FOREIGN KEY (`target_user_id`) REFERENCES `users` (`user_id`);

--
-- Constraints for table `review`
--
ALTER TABLE `review`
  ADD CONSTRAINT `review_ibfk_1` FOREIGN KEY (`receiver_id`) REFERENCES `users` (`user_id`),
  ADD CONSTRAINT `review_ibfk_2` FOREIGN KEY (`sender_id`) REFERENCES `users` (`user_id`),
  ADD CONSTRAINT `review_ibfk_3` FOREIGN KEY (`opportunity_id`) REFERENCES `opportunity` (`opportunity_id`);

--
-- Constraints for table `traveler`
--
ALTER TABLE `traveler`
  ADD CONSTRAINT `traveler_ibfk_1` FOREIGN KEY (`traveler_id`) REFERENCES `users` (`user_id`) ON DELETE CASCADE ON UPDATE CASCADE;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
