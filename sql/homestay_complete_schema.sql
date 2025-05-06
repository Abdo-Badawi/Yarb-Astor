-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";

/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Database: `homestay`
--
CREATE DATABASE IF NOT EXISTS `homestay` DEFAULT CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci;
USE `homestay`;

-- --------------------------------------------------------

--
-- Table structure for table `users`
--

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
  `status` enum('active','reported') DEFAULT NULL,
  PRIMARY KEY (`host_id`),
  CONSTRAINT `hosts_ibfk_1` FOREIGN KEY (`host_id`) REFERENCES `users` (`user_id`) ON DELETE CASCADE ON UPDATE CASCADE
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
  `status` enum('active','reported') DEFAULT NULL,
  PRIMARY KEY (`traveler_id`),
  CONSTRAINT `traveler_ibfk_1` FOREIGN KEY (`traveler_id`) REFERENCES `users` (`user_id`) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `opportunity`
--

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
  `max_volunteers` int DEFAULT 1,
  PRIMARY KEY (`opportunity_id`),
  KEY `host_id` (`host_id`),
  CONSTRAINT `opportunity_ibfk_1` FOREIGN KEY (`host_id`) REFERENCES `users` (`user_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `applications`
--

CREATE TABLE `applications` (
  `application_id` bigint(20) NOT NULL AUTO_INCREMENT,
  `opportunity_id` bigint(20) NOT NULL,
  `traveler_id` bigint(20) NOT NULL,
  `status` enum('pending','accepted','rejected') NOT NULL DEFAULT 'pending',
  `comment` varchar(255) NOT NULL,
  `applied_date` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp(),
  `message` text NOT NULL,
  `availability` text NOT NULL,
  `experience` text NOT NULL,
  PRIMARY KEY (`application_id`),
  KEY `opportunity_id` (`opportunity_id`),
  KEY `traveler_id` (`traveler_id`),
  KEY `idx_applications_status` (`status`),
  CONSTRAINT `applications_ibfk_1` FOREIGN KEY (`opportunity_id`) REFERENCES `opportunity` (`opportunity_id`),
  CONSTRAINT `applications_ibfk_2` FOREIGN KEY (`traveler_id`) REFERENCES `users` (`user_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `card`
--

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

-- --------------------------------------------------------

--
-- Table structure for table `fee_transaction`
--

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

-- --------------------------------------------------------

--
-- Table structure for table `message`
--

CREATE TABLE `message` (
  `message_id` bigint(20) NOT NULL AUTO_INCREMENT,
  `sender_id` bigint(20) DEFAULT NULL,
  `receiver_id` bigint(20) DEFAULT NULL,
  `content` text DEFAULT NULL,
  `timestamp` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp(),
  `status` varchar(255) DEFAULT NULL,
  `is_read` TINYINT(1) NOT NULL DEFAULT 0,
  `sender_type` VARCHAR(20) NOT NULL,
  `receiver_type` VARCHAR(20) NOT NULL,
  PRIMARY KEY (`message_id`),
  KEY `sender_id` (`sender_id`),
  KEY `receiver_id` (`receiver_id`),
  KEY `idx_message_sender` (`sender_id`, `sender_type`),
  KEY `idx_message_receiver` (`receiver_id`, `receiver_type`),
  CONSTRAINT `message_ibfk_1` FOREIGN KEY (`sender_id`) REFERENCES `users` (`user_id`),
  CONSTRAINT `message_ibfk_2` FOREIGN KEY (`receiver_id`) REFERENCES `users` (`user_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `notification`
--

CREATE TABLE `notification` (
  `notification_id` bigint(20) NOT NULL AUTO_INCREMENT,
  `receiver_id` bigint(20) DEFAULT NULL,
  `content` text DEFAULT NULL,
  `timestamp` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp(),
  `is_read` TINYINT(1) NOT NULL DEFAULT 0,
  PRIMARY KEY (`notification_id`),
  KEY `receiver_id` (`receiver_id`),
  CONSTRAINT `notification_ibfk_1` FOREIGN KEY (`receiver_id`) REFERENCES `users` (`user_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `report`
--

CREATE TABLE `report` (
  `report_id` bigint(20) NOT NULL AUTO_INCREMENT,
  `reported_by_id` bigint(20) DEFAULT NULL,
  `target_user_id` bigint(20) DEFAULT NULL,
  `report_content` text DEFAULT NULL,
  `status` enum('open','reviewed','resolved') DEFAULT NULL,
  `report_type` enum('user','opportunity','message') DEFAULT NULL,
  `admin_response` text DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NULL DEFAULT NULL ON UPDATE current_timestamp(),
  PRIMARY KEY (`report_id`),
  KEY `reported_by_id` (`reported_by_id`),
  KEY `target_user_id` (`target_user_id`),
  CONSTRAINT `report_ibfk_1` FOREIGN KEY (`reported_by_id`) REFERENCES `users` (`user_id`),
  CONSTRAINT `report_ibfk_2` FOREIGN KEY (`target_user_id`) REFERENCES `users` (`user_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `review`
--

CREATE TABLE `review` (
  `review_id` bigint(20) NOT NULL AUTO_INCREMENT,
  `sender_id` bigint(20) DEFAULT NULL,
  `receiver_id` bigint(20) DEFAULT NULL,
  `opportunity_id` bigint(20) DEFAULT NULL,
  `rating` int(11) DEFAULT NULL,
  `comment` text DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  PRIMARY KEY (`review_id`),
  KEY `sender_id` (`sender_id`),
  KEY `receiver_id` (`receiver_id`),
  KEY `opportunity_id` (`opportunity_id`),
  CONSTRAINT `review_ibfk_1` FOREIGN KEY (`receiver_id`) REFERENCES `users` (`user_id`),
  CONSTRAINT `review_ibfk_2` FOREIGN KEY (`sender_id`) REFERENCES `users` (`user_id`),
  CONSTRAINT `review_ibfk_3` FOREIGN KEY (`opportunity_id`) REFERENCES `opportunity` (`opportunity_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `support_content`
--

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

-- --------------------------------------------------------

--
-- Table structure for table `settings`
--

CREATE TABLE `settings` (
  `setting_id` bigint(20) NOT NULL AUTO_INCREMENT,
  `setting_key` varchar(255) NOT NULL,
  `setting_value` text DEFAULT NULL,
  `setting_group` varchar(255) DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NULL DEFAULT NULL ON UPDATE current_timestamp(),
  PRIMARY KEY (`setting_id`),
  UNIQUE KEY `setting_key` (`setting_key`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `user_activity_log`
--

CREATE TABLE `user_activity_log` (
  `log_id` bigint(20) NOT NULL AUTO_INCREMENT,
  `user_id` bigint(20) DEFAULT NULL,
  `activity_type` varchar(255) NOT NULL,
  `activity_details` text DEFAULT NULL,
  `ip_address` varchar(45) DEFAULT NULL,
  `user_agent` text DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  PRIMARY KEY (`log_id`),
  KEY `user_id` (`user_id`),
  KEY `activity_type` (`activity_type`),
  CONSTRAINT `user_activity_log_ibfk_1` FOREIGN KEY (`user_id`) REFERENCES `users` (`user_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Views
--

-- Create a view for user notifications
CREATE OR REPLACE VIEW `user_notifications` AS
SELECT 
  n.notification_id,
  n.receiver_id,
  n.content,
  n.timestamp,
  n.is_read,
  u.user_type,
  u.first_name,
  u.last_name
FROM notification n
JOIN users u ON n.receiver_id = u.user_id
ORDER BY n.timestamp DESC;

-- Create a view for active opportunities with host information
CREATE OR REPLACE VIEW `active_opportunities` AS
SELECT 
  o.*,
  h.property_type AS host_property_type,
  h.preferred_language AS host_language,
  u.first_name AS host_first_name,
  u.last_name AS host_last_name,
  u.profile_picture AS host_profile_picture
FROM opportunity o
JOIN hosts h ON o.host_id = h.host_id
JOIN users u ON h.host_id = u.user_id
WHERE o.status = 'open'
AND o.end_date >= CURDATE()
ORDER BY o.created_at DESC;

-- Create a view for application details with traveler and opportunity info
CREATE OR REPLACE VIEW `application_details` AS
SELECT 
  a.*,
  t.skill AS traveler_skill,
  t.language_spoken AS traveler_language,
  u.first_name AS traveler_first_name,
  u.last_name AS traveler_last_name,
  u.profile_picture AS traveler_profile_picture,
  o.title AS opportunity_title,
  o.location AS opportunity_location,
  o.category AS opportunity_category,
  o.start_date AS opportunity_start_date,
  o.end_date AS opportunity_end_date
FROM applications a
JOIN traveler t ON a.traveler_id = t.traveler_id
JOIN users u ON t.traveler_id = u.user_id
JOIN opportunity o ON a.opportunity_id = o.opportunity_id
ORDER BY a.applied_date DESC;

-- --------------------------------------------------------

--
-- Stored Procedures
--

DELIMITER //

-- Procedure to get unread message count for a user
CREATE PROCEDURE GetUnreadMessageCount(IN userId BIGINT, IN userType VARCHAR(20))
BEGIN
  SELECT COUNT(*) AS unread_count 
  FROM message 
  WHERE receiver_id = userId 
  AND receiver_type = userType 
  AND is_read = 0;
END //

-- Procedure to mark messages as read
CREATE PROCEDURE MarkMessagesAsRead(IN receiverId BIGINT, IN senderId BIGINT, IN receiverType VARCHAR(20), IN senderType VARCHAR(20))
BEGIN
  UPDATE message 
  SET is_read = 1 
  WHERE receiver_id = receiverId 
  AND sender_id = senderId 
  AND receiver_type = receiverType 
  AND sender_type = senderType 
  AND is_read = 0;
END //

-- Procedure to get conversation between two users
CREATE PROCEDURE GetConversation(IN user1Id BIGINT, IN user2Id BIGINT, IN user1Type VARCHAR(20), IN user2Type VARCHAR(20))
BEGIN
  SELECT * FROM message
  WHERE (sender_id = user1Id AND receiver_id = user2Id AND sender_type = user1Type AND receiver_type = user2Type)
  OR (sender_id = user2Id AND receiver_id = user1Id AND sender_type = user2Type AND receiver_type = user1Type)
  ORDER BY timestamp ASC;
END //

-- Procedure to get all conversations for a user
CREATE PROCEDURE GetUserConversations(IN userId BIGINT, IN userType VARCHAR(20))
BEGIN
  SELECT 
    DISTINCT 
    CASE 
      WHEN sender_id = userId THEN receiver_id 
      ELSE sender_id 
    END AS other_user_id,
    CASE 
      WHEN sender_id = userId THEN receiver_type 
      ELSE sender_type 
    END AS other_user_type,
    (SELECT content FROM message m2 
     WHERE ((m2.sender_id = userId AND m2.receiver_id = CASE WHEN m1.sender_id = userId THEN m1.receiver_id ELSE m1.sender_id END)
     OR (m2.sender_id = CASE WHEN m1.sender_id = userId THEN m1.receiver_id ELSE m1.sender_id END AND m2.receiver_id = userId))
     ORDER BY m2.timestamp DESC LIMIT 1) AS last_message,
    (SELECT timestamp FROM message m2 
     WHERE ((m2.sender_id = userId AND m2.receiver_id = CASE WHEN m1.sender_id = userId THEN m1.receiver_id ELSE m1.sender_id END)
     OR (m2.sender_id = CASE WHEN m1.sender_id = userId THEN m1.receiver_id ELSE m1.sender_id END AND m2.receiver_id = userId))
     ORDER BY m2.timestamp DESC LIMIT 1) AS last_message_time,
    (SELECT COUNT(*) FROM message m2 
     WHERE m2.sender_id = CASE WHEN m1.sender_id = userId THEN m1.receiver_id ELSE m1.sender_id END
     AND m2.receiver_id = userId
     AND m2.is_read = 0) AS unread_count
  FROM message m1
  WHERE sender_id = userId OR receiver_id = userId
  ORDER BY last_message_time DESC;
END //

-- Procedure to get host opportunities with application counts
CREATE PROCEDURE GetHostOpportunitiesWithStats(IN hostId BIGINT)
BEGIN
  SELECT 
    o.*,
    COUNT(DISTINCT a.application_id) AS total_applications,
    SUM(CASE WHEN a.status = 'pending' THEN 1 ELSE 0 END) AS pending_applications,
    SUM(CASE WHEN a.status = 'accepted' THEN 1 ELSE 0 END) AS accepted_applications,
    SUM(CASE WHEN a.status = 'rejected' THEN 1 ELSE 0 END) AS rejected_applications
  FROM opportunity o
  LEFT JOIN applications a ON o.opportunity_id = a.opportunity_id
  WHERE o.host_id = hostId
  GROUP BY o.opportunity_id
  ORDER BY o.created_at DESC;
END //

-- Procedure to get traveler applications with details
CREATE PROCEDURE GetTravelerApplications(IN travelerId BIGINT)
BEGIN
  SELECT 
    a.*,
    o.title AS opportunity_title,
    o.location AS opportunity_location,
    o.start_date,
    o.end_date,
    o.category,
    u.first_name AS host_first_name,
    u.last_name AS host_last_name
  FROM applications a
  JOIN opportunity o ON a.opportunity_id = o.opportunity_id
  JOIN users u ON o.host_id = u.user_id
  WHERE a.traveler_id = travelerId
  ORDER BY a.applied_date DESC;
END //

-- Procedure to update user profile
CREATE PROCEDURE UpdateUserProfile(
  IN userId BIGINT,
  IN firstName VARCHAR(255),
  IN lastName VARCHAR(255),
  IN phoneNumber VARCHAR(255),
  IN bio TEXT
)
BEGIN
  UPDATE users 
  SET 
    first_name = firstName,
    last_name = lastName,
    phone_number = phoneNumber
  WHERE user_id = userId;
  
  -- Update bio in appropriate table based on user type
  IF EXISTS (SELECT 1 FROM hosts WHERE host_id = userId) THEN
    UPDATE hosts SET bio = bio WHERE host_id = userId;
  ELSEIF EXISTS (SELECT 1 FROM traveler WHERE traveler_id = userId) THEN
    UPDATE traveler SET bio = bio WHERE traveler_id = userId;
  END IF;
END //

DELIMITER ;

-- --------------------------------------------------------

--
-- Triggers
--

DELIMITER //

-- Trigger to send notification when application status changes
CREATE TRIGGER application_status_change
AFTER UPDATE ON applications
FOR EACH ROW
BEGIN
  DECLARE opportunity_title VARCHAR(255);
  
  IF NEW.status != OLD.status THEN
    -- Get the opportunity title
    SELECT title INTO opportunity_title FROM opportunity WHERE opportunity_id = NEW.opportunity_id;
    
    -- Insert notification for traveler
    INSERT INTO notification (receiver_id, content, timestamp, is_read)
    VALUES (
      NEW.traveler_id, 
      CONCAT('Your application for "', opportunity_title, '" has been ', NEW.status, '.'),
      NOW(),
      0
    );
  END IF;
END //

-- Trigger to send notification when a new message is received
CREATE TRIGGER new_message_notification
AFTER INSERT ON message
FOR EACH ROW
BEGIN
  DECLARE sender_name VARCHAR(255);
  
  -- Get the sender's name
  SELECT CONCAT(first_name, ' ', last_name) INTO sender_name 
  FROM users WHERE user_id = NEW.sender_id;
  
  -- Insert notification for receiver
  INSERT INTO notification (receiver_id, content, timestamp, is_read)
  VALUES (
    NEW.receiver_id, 
    CONCAT('You have received a new message from ', sender_name, '.'),
    NOW(),
    0
  );
END //

-- Trigger to update host rating after a new review
CREATE TRIGGER update_host_rating
AFTER INSERT ON review
FOR EACH ROW
BEGIN
  DECLARE avg_rating FLOAT;
  
  -- Only update if the review is for a host
  IF EXISTS (SELECT 1 FROM hosts WHERE host_id = NEW.receiver_id) THEN
    -- Calculate the new average rating
    SELECT AVG(rating) INTO avg_rating 
    FROM review 
    WHERE receiver_id = NEW.receiver_id;
    
    -- Update the host's rating
    UPDATE hosts SET rate = avg_rating WHERE host_id = NEW.receiver_id;
  END IF;
END //

-- Trigger to update traveler rating after a new review
CREATE TRIGGER update_traveler_rating
AFTER INSERT ON review
FOR EACH ROW
BEGIN
  DECLARE avg_rating FLOAT;
  
  -- Only update if the review is for a traveler
  IF EXISTS (SELECT 1 FROM traveler WHERE traveler_id = NEW.receiver_id) THEN
    -- Calculate the new average rating
    SELECT AVG(rating) INTO avg_rating 
    FROM review 
    WHERE receiver_id = NEW.receiver_id;
    
    -- Update the traveler's rating
    UPDATE traveler SET rate = avg_rating WHERE traveler_id = NEW.receiver_id;
  END IF;
END //

-- Trigger to log user activity
CREATE TRIGGER log_user_login
AFTER UPDATE ON users
FOR EACH ROW
BEGIN
  IF NEW.last_login != OLD.last_login THEN
    INSERT INTO user_activity_log (user_id, activity_type, activity_details, created_at)
    VALUES (NEW.user_id, 'login', 'User logged in', NOW());
  END IF;
END //

DELIMITER ;

-- --------------------------------------------------------

--
-- Indexes for better performance
--

-- Add indexes for common search operations
CREATE INDEX idx_opportunity_location ON opportunity(location);
CREATE INDEX idx_opportunity_category ON opportunity(category);
CREATE INDEX idx_opportunity_dates ON opportunity(start_date, end_date);
CREATE INDEX idx_opportunity_status ON opportunity(status);
CREATE INDEX idx_users_email ON users(email);
CREATE INDEX idx_users_name ON users(first_name, last_name);
CREATE INDEX idx_hosts_location ON hosts(location);
CREATE INDEX idx_traveler_location ON traveler(location);
CREATE INDEX idx_message_timestamp ON message(timestamp);
CREATE INDEX idx_notification_timestamp ON notification(timestamp);
CREATE INDEX idx_review_rating ON review(rating);
