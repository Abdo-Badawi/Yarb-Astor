-- Create fee table
CREATE TABLE IF NOT EXISTS `fee` (
  `fee_id` bigint(20) NOT NULL AUTO_INCREMENT,
  `fee_name` varchar(255) NOT NULL,
  `fee_type` enum('fixed','percentage','tiered') NOT NULL DEFAULT 'fixed',
  `amount` float NOT NULL,
  `currency` varchar(10) NOT NULL DEFAULT 'USD',
  `description` text DEFAULT NULL,
  `applicability` enum('all','new','returning','premium') NOT NULL DEFAULT 'all',
  `is_mandatory` tinyint(1) NOT NULL DEFAULT 0,
  `status` enum('active','inactive') NOT NULL DEFAULT 'active',
  `created_by` bigint(20) NOT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NULL DEFAULT NULL ON UPDATE current_timestamp(),
  PRIMARY KEY (`fee_id`),
  KEY `created_by` (`created_by`),
  CONSTRAINT `fee_ibfk_1` FOREIGN KEY (`created_by`) REFERENCES `users` (`user_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- Create fee_assignment table to link fees to travelers
CREATE TABLE IF NOT EXISTS `fee_assignment` (
  `assignment_id` bigint(20) NOT NULL AUTO_INCREMENT,
  `fee_id` bigint(20) NOT NULL,
  `traveler_id` bigint(20) DEFAULT NULL,
  `status` enum('pending','paid','waived') NOT NULL DEFAULT 'pending',
  `assigned_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `due_date` date DEFAULT NULL,
  `paid_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`assignment_id`),
  KEY `fee_id` (`fee_id`),
  KEY `traveler_id` (`traveler_id`),
  CONSTRAINT `fee_assignment_ibfk_1` FOREIGN KEY (`fee_id`) REFERENCES `fee` (`fee_id`),
  CONSTRAINT `fee_assignment_ibfk_2` FOREIGN KEY (`traveler_id`) REFERENCES `users` (`user_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- Insert sample data
INSERT INTO `fee` (`fee_name`, `fee_type`, `amount`, `currency`, `description`, `applicability`, `is_mandatory`, `status`, `created_by`)
VALUES
('Registration Fee', 'fixed', 25.00, 'USD', 'One-time registration fee for all new travelers', 'new', 1, 'active', 1),
('Annual Membership', 'fixed', 99.00, 'USD', 'Annual membership fee for premium services', 'all', 1, 'active', 1),
('Booking Service Fee', 'percentage', 5.00, 'USD', '5% service fee on all bookings', 'all', 1, 'active', 1),
('Verification Fee', 'fixed', 15.00, 'USD', 'One-time verification fee for identity verification', 'all', 0, 'active', 1);
