-- Create the database structure for Homestay application with all numerical auto-increment primary keys

-- Table structure for table `users`
CREATE TABLE `users` (
  `user_id` bigint(20) NOT NULL AUTO_INCREMENT,
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
  `last_login` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp(),
  PRIMARY KEY (`user_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- Table structure for table `hosts`
CREATE TABLE `hosts` (
  `host_id` bigint(20) NOT NULL AUTO_INCREMENT,
  `property_type` enum('teaching','farming','cooking','childcare') DEFAULT NULL,
  `preferred_language` varchar(255) DEFAULT NULL,
  `joined_date` date DEFAULT NULL,
  `bio` text DEFAULT NULL,
  `rate` float DEFAULT NULL,
  `location` varchar(255) DEFAULT NULL,
  `status` enum('active','reported') DEFAULT NULL,
  PRIMARY KEY (`host_id`),
  CONSTRAINT `hosts_ibfk_1` FOREIGN KEY (`host_id`) REFERENCES `users` (`user_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- Table structure for table `traveler`
CREATE TABLE `traveler` (
  `traveler_id` bigint(20) NOT NULL AUTO_INCREMENT,
  `skill` text DEFAULT NULL,
  `language_spoken` text DEFAULT NULL,
  `preferred_language` varchar(255) DEFAULT NULL,
  `joined_date` date DEFAULT NULL,
  `bio` text DEFAULT NULL,
  `rate` float DEFAULT NULL,
  `location` varchar(255) DEFAULT NULL,
  `created_at` date DEFAULT NULL,
  `status` enum('active','reported') DEFAULT NULL,
  PRIMARY KEY (`traveler_id`),
  CONSTRAINT `traveler_ibfk_1` FOREIGN KEY (`traveler_id`) REFERENCES `users` (`user_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- Table structure for table `opportunity`
CREATE TABLE `opportunity` (
  `opportunity_id` bigint(20) NOT NULL AUTO_INCREMENT,
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
  `requirements` text DEFAULT NULL,
  PRIMARY KEY (`opportunity_id`),
  KEY `host_id` (`host_id`),
  CONSTRAINT `opportunity_ibfk_1` FOREIGN KEY (`host_id`) REFERENCES `users` (`user_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- Table structure for table `applications`
CREATE TABLE `applications` (
  `application_id` bigint(20) NOT NULL AUTO_INCREMENT,
  `opportunity_id` bigint(20) NOT NULL,
  `traveler_id` bigint(20) NOT NULL,
  `status` enum('pending','accepted','rejected') NOT NULL DEFAULT 'pending',
  `comment` varchar(255) NOT NULL,
  `applied_date` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp(),
  PRIMARY KEY (`application_id`),
  KEY `opportunity_id` (`opportunity_id`),
  KEY `traveler_id` (`traveler_id`),
  CONSTRAINT `applications_ibfk_1` FOREIGN KEY (`opportunity_id`) REFERENCES `opportunity` (`opportunity_id`),
  CONSTRAINT `applications_ibfk_2` FOREIGN KEY (`traveler_id`) REFERENCES `users` (`user_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- Table structure for table `card`
CREATE TABLE `card` (
  `card_id` bigint(20) NOT NULL AUTO_INCREMENT,
  `card_number` varchar(255) NOT NULL,
  `expiry_date` date DEFAULT NULL,
  `cvv` varchar(255) DEFAULT NULL,
  `card_holder_name` varchar(255) DEFAULT NULL,
  `billing_address` varchar(255) DEFAULT NULL,
  `traveler_id` bigint(20) DEFAULT NULL,
  PRIMARY KEY (`card_id`),
  KEY `traveler_id` (`traveler_id`),
  CONSTRAINT `card_ibfk_1` FOREIGN KEY (`traveler_id`) REFERENCES `users` (`user_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- Table structure for table `fee_transaction`
CREATE TABLE `fee_transaction` (
  `fee_id` bigint(20) NOT NULL AUTO_INCREMENT,
  `transaction_reference` varchar(255) DEFAULT NULL,
  `traveler_id` bigint(20) DEFAULT NULL,
  `payment_method` enum('credit_card','paypal','bank_transfer') DEFAULT NULL,
  `amount` float DEFAULT NULL,
  `date` date DEFAULT NULL,
  `status` enum('pending','completed','failed') DEFAULT NULL,
  PRIMARY KEY (`fee_id`),
  KEY `traveler_id` (`traveler_id`),
  CONSTRAINT `fee_transaction_ibfk_1` FOREIGN KEY (`traveler_id`) REFERENCES `users` (`user_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- Table structure for table `message`
CREATE TABLE `message` (
  `message_id` bigint(20) NOT NULL AUTO_INCREMENT,
  `sender_id` bigint(20) DEFAULT NULL,
  `receiver_id` bigint(20) DEFAULT NULL,
  `content` text DEFAULT NULL,
  `timestamp` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp(),
  `status` varchar(255) DEFAULT NULL,
  PRIMARY KEY (`message_id`),
  KEY `sender_id` (`sender_id`),
  KEY `receiver_id` (`receiver_id`),
  CONSTRAINT `message_ibfk_1` FOREIGN KEY (`sender_id`) REFERENCES `users` (`user_id`),
  CONSTRAINT `message_ibfk_2` FOREIGN KEY (`receiver_id`) REFERENCES `users` (`user_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- Table structure for table `notification`
CREATE TABLE `notification` (
  `notification_id` bigint(20) NOT NULL AUTO_INCREMENT,
  `receiver_id` bigint(20) DEFAULT NULL,
  `content` text DEFAULT NULL,
  `timestamp` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp(),
  PRIMARY KEY (`notification_id`),
  KEY `receiver_id` (`receiver_id`),
  CONSTRAINT `notification_ibfk_1` FOREIGN KEY (`receiver_id`) REFERENCES `users` (`user_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- Table structure for table `report`
CREATE TABLE `report` (
  `report_id` bigint(20) NOT NULL AUTO_INCREMENT,
  `reported_by_id` bigint(20) DEFAULT NULL,
  `target_user_id` bigint(20) DEFAULT NULL,
  `report_content` text DEFAULT NULL,
  `status` enum('open','reviewed','resolved') DEFAULT NULL,
  `report_type` enum('user','opportunity','message') DEFAULT NULL,
  `admin_response` text DEFAULT NULL,
  PRIMARY KEY (`report_id`),
  KEY `reported_by_id` (`reported_by_id`),
  KEY `target_user_id` (`target_user_id`),
  CONSTRAINT `report_ibfk_1` FOREIGN KEY (`reported_by_id`) REFERENCES `users` (`user_id`),
  CONSTRAINT `report_ibfk_2` FOREIGN KEY (`target_user_id`) REFERENCES `users` (`user_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- Table structure for table `review`
CREATE TABLE `review` (
  `review_id` bigint(20) NOT NULL AUTO_INCREMENT,
  `sender_id` bigint(20) DEFAULT NULL,
  `receiver_id` bigint(20) DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp(),
  `opportunity_id` bigint(20) DEFAULT NULL,
  `rating` float DEFAULT NULL,
  `comment` text DEFAULT NULL,
  `is_reported` tinyint(1) DEFAULT NULL,
  PRIMARY KEY (`review_id`),
  KEY `receiver_id` (`receiver_id`),
  KEY `opportunity_id` (`opportunity_id`),
  CONSTRAINT `review_ibfk_1` FOREIGN KEY (`receiver_id`) REFERENCES `users` (`user_id`),
  CONSTRAINT `review_ibfk_2` FOREIGN KEY (`sender_id`) REFERENCES `users` (`user_id`),
  CONSTRAINT `review_ibfk_3` FOREIGN KEY (`opportunity_id`) REFERENCES `opportunity` (`opportunity_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- Table structure for table `support_content`
CREATE TABLE `support_content` (
  `content_id` bigint(20) NOT NULL AUTO_INCREMENT,
  `title` varchar(255) NOT NULL,
  `content` text DEFAULT NULL,
  `category` enum('account','safety','opportunity','other') DEFAULT NULL,
  `status` enum('active','archived') DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp(),
  `last_updated` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`content_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;