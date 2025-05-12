-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1
-- Generation Time: May 09, 2025 at 11:12 PM
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
-- Database: `homestay`
--

DELIMITER $$
--
-- Procedures
--
CREATE DEFINER=`root`@`localhost` PROCEDURE `GetConversation` (IN `user1Id` BIGINT, IN `user2Id` BIGINT, IN `user1Type` VARCHAR(20), IN `user2Type` VARCHAR(20))   BEGIN
  SELECT * FROM message
  WHERE (sender_id = user1Id AND receiver_id = user2Id AND sender_type = user1Type AND receiver_type = user2Type)
  OR (sender_id = user2Id AND receiver_id = user1Id AND sender_type = user2Type AND receiver_type = user1Type)
  ORDER BY timestamp ASC;
END$$

CREATE DEFINER=`root`@`localhost` PROCEDURE `GetHostOpportunitiesWithStats` (IN `hostId` BIGINT)   BEGIN
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
END$$

CREATE DEFINER=`root`@`localhost` PROCEDURE `GetTravelerApplications` (IN `travelerId` BIGINT)   BEGIN
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
END$$

CREATE DEFINER=`root`@`localhost` PROCEDURE `GetUnreadMessageCount` (IN `userId` BIGINT, IN `userType` VARCHAR(20))   BEGIN
  SELECT COUNT(*) AS unread_count 
  FROM message 
  WHERE receiver_id = userId 
  AND receiver_type = userType 
  AND is_read = 0;
END$$

CREATE DEFINER=`root`@`localhost` PROCEDURE `GetUserConversations` (IN `userId` BIGINT, IN `userType` VARCHAR(20))   BEGIN
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
END$$

CREATE DEFINER=`root`@`localhost` PROCEDURE `MarkMessagesAsRead` (IN `receiverId` BIGINT, IN `senderId` BIGINT, IN `receiverType` VARCHAR(20), IN `senderType` VARCHAR(20))   BEGIN
  UPDATE message 
  SET is_read = 1 
  WHERE receiver_id = receiverId 
  AND sender_id = senderId 
  AND receiver_type = receiverType 
  AND sender_type = senderType 
  AND is_read = 0;
END$$

CREATE DEFINER=`root`@`localhost` PROCEDURE `UpdateUserProfile` (IN `userId` BIGINT, IN `firstName` VARCHAR(255), IN `lastName` VARCHAR(255), IN `phoneNumber` VARCHAR(255), IN `bio` TEXT)   BEGIN
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
END$$

DELIMITER ;

-- --------------------------------------------------------

--
-- Stand-in structure for view `active_opportunities`
-- (See below for the actual view)
--
CREATE TABLE `active_opportunities` (
`opportunity_id` bigint(20)
,`opportunity_photo` longblob
,`title` varchar(255)
,`description` text
,`location` varchar(255)
,`start_date` date
,`end_date` date
,`category` enum('teaching','farming','cooking','childcare')
,`host_id` bigint(20)
,`status` enum('open','closed','cancelled','deleted','reported')
,`created_at` timestamp
,`requirements` text
,`max_volunteers` int(11)
,`host_property_type` enum('teaching','farming','cooking','childcare')
,`host_language` varchar(255)
,`host_first_name` varchar(255)
,`host_last_name` varchar(255)
,`host_profile_picture` longblob
);

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
  `applied_date` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp(),
  `message` text NOT NULL,
  `availability` text NOT NULL,
  `experience` text NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `applications`
--

INSERT INTO `applications` (`application_id`, `opportunity_id`, `traveler_id`, `status`, `comment`, `applied_date`, `message`, `availability`, `experience`) VALUES
(1, 1, 6, 'pending', 'I would love to help teach English to children!', '2025-04-30 21:00:00', 'I have experience teaching English to children and would love to join your program. I am available for the entire duration of the opportunity.', 'Available from June 1 to August 31', 'Taught English at a summer camp for 2 years, worked as a tutor for 3 years'),
(2, 2, 8, 'accepted', 'I have extensive farming experience and would be a great fit.', '2025-04-25 21:00:00', 'I grew up on a farm and have experience with organic farming methods. I am excited about the opportunity to learn about Spanish farming techniques.', 'Available immediately for 3 months', 'Worked on family farm for 10 years, volunteered at community gardens'),
(3, 3, 7, 'pending', 'I am passionate about cooking and eager to learn Italian cuisine.', '2025-05-02 21:00:00', 'I have a culinary arts certificate and would love to learn authentic Italian cooking techniques. I am a hard worker and quick learner.', 'Available for the entire period requested', 'Culinary arts certificate, worked in restaurant kitchen for 1 year'),
(4, 4, 7, 'rejected', 'I have extensive childcare experience and love working with children.', '2025-04-20 22:00:00', 'I have worked as a nanny for 3 years and have experience with children of all ages. I am responsible, patient, and enjoy creating fun activities.', 'Available from July through September', 'Professional nanny for 3 years, babysitting experience since age 16');

--
-- Triggers `applications`
--
DELIMITER $$
CREATE TRIGGER `application_status_change` AFTER UPDATE ON `applications` FOR EACH ROW BEGIN
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
END
$$
DELIMITER ;

-- --------------------------------------------------------

--
-- Stand-in structure for view `application_details`
-- (See below for the actual view)
--
CREATE TABLE `application_details` (
`application_id` bigint(20)
,`opportunity_id` bigint(20)
,`traveler_id` bigint(20)
,`status` enum('pending','accepted','rejected')
,`comment` varchar(255)
,`applied_date` timestamp
,`message` text
,`availability` text
,`experience` text
,`traveler_skill` text
,`traveler_language` text
,`traveler_first_name` varchar(255)
,`traveler_last_name` varchar(255)
,`traveler_profile_picture` longblob
,`opportunity_title` varchar(255)
,`opportunity_location` varchar(255)
,`opportunity_category` enum('teaching','farming','cooking','childcare')
,`opportunity_start_date` date
,`opportunity_end_date` date
);

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

--
-- Dumping data for table `card`
--

INSERT INTO `card` (`card_id`, `card_number`, `expiry_date`, `cvv`, `card_holder_name`, `billing_address`, `traveler_id`) VALUES
(1, '123456789', '2027-05-31', '12345', 'om samah', 'tdfufiuftgogoy', 6),
(2, '1235432', '2027-05-31', '567', 'malak', 'uytdrxfcghj', 7),
(3, '12345654345', '2027-01-01', '567', 'shakal', 'oi8u7ytfg', 1),
(4, '4111111111111111', '2025-12-01', '123', 'David Lee', '123 Main St, New York, USA', 6),
(5, '5555555555554444', '2024-10-01', '456', 'Sophia Martinez', '456 Oak Ave, Toronto, Canada', 7),
(6, '378282246310005', '2026-05-01', '789', 'Daniel Taylor', '789 Pine Rd, Sydney, Australia', 8),
(7, '6011111111111117', '2025-08-01', '321', 'Olivia Anderson', '321 Cedar Ln, Tokyo, Japan', 9);

-- --------------------------------------------------------

--
-- Table structure for table `fee`
--

CREATE TABLE `fee` (
  `fee_id` bigint(20) NOT NULL,
  `fee_name` varchar(255) NOT NULL,
  `amount` float NOT NULL,
  `currency` varchar(10) NOT NULL DEFAULT 'USD',
  `description` text DEFAULT NULL,
  `applicability` enum('all','new','returning','premium') NOT NULL DEFAULT 'all',
  `is_mandatory` tinyint(1) NOT NULL DEFAULT 0,
  `status` enum('active','inactive') NOT NULL DEFAULT 'active',
  `created_by` bigint(20) NOT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NULL DEFAULT NULL ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `fee`
--

INSERT INTO `fee` (`fee_id`, `fee_name`, `amount`, `currency`, `description`, `applicability`, `is_mandatory`, `status`, `created_by`, `created_at`, `updated_at`) VALUES
(1, 'Registration Fee', 25, 'USD', 'One-time registration fee for all new travelers', 'new', 1, 'active', 1, '2025-05-06 04:50:55', NULL),
(2, 'Annual Membership', 99, 'USD', 'Annual membership fee for premium services', 'all', 1, 'active', 1, '2025-05-06 04:50:55', NULL),
(3, 'Booking Service Fee', 5, 'USD', '5% service fee on all bookings', 'all', 1, 'active', 1, '2025-05-06 04:50:55', NULL),
(4, 'Verification Fee', 15, 'USD', 'One-time verification fee for identity verification', 'all', 0, 'active', 1, '2025-05-06 04:50:55', NULL);

-- --------------------------------------------------------

--
-- Table structure for table `fee_assignment`
--

CREATE TABLE `fee_assignment` (
  `assignment_id` bigint(20) NOT NULL,
  `fee_id` bigint(20) NOT NULL,
  `traveler_id` bigint(20) DEFAULT NULL,
  `status` enum('pending','paid','waived') NOT NULL DEFAULT 'pending',
  `assigned_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `due_date` date DEFAULT NULL,
  `paid_at` timestamp NULL DEFAULT NULL
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

--
-- Dumping data for table `fee_transaction`
--

INSERT INTO `fee_transaction` (`fee_id`, `transaction_reference`, `traveler_id`, `payment_method`, `amount`, `date`, `status`) VALUES
(1, 'hgfdsdfghj', 6, 'credit_card', 45, '2025-05-01', 'completed'),
(2, 'CARD-undefined-TRX-2', 7, 'credit_card', 55, '2025-05-02', 'completed'),
(3, 'TRX-1746501161', 1, 'credit_card', 70, '2025-05-06', 'completed'),
(4, 'TXN-10001', 6, 'credit_card', 5000, '2023-05-15', 'completed'),
(5, 'TXN-10002', 6, 'credit_card', 5000, '2023-05-16', 'completed'),
(6, 'TXN-10003', 7, 'bank_transfer', 7500, '2023-06-10', 'pending'),
(7, 'TXN-789012', 8, 'credit_card', 12000, '2023-04-01', 'completed'),
(8, 'TXN-10004', 9, 'credit_card', 8500, '2023-07-22', 'pending'),
(9, 'TXN-10005', 6, 'paypal', 6000, '2023-08-05', 'failed');

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
(2, 'teaching', 'English', '2025-05-06', 'I am a retired English teacher with 30 years of experience. I love sharing my knowledge with travelers from around the world.', NULL, 'London, UK', 'active'),
(3, 'farming', 'Spanish', '2025-05-06', 'Our family has been running this organic farm for three generations. We grow vegetables and raise free-range chickens.', 4, 'Valencia, Spain', 'active'),
(4, 'cooking', 'Italian', '2025-05-06', 'I run a small restaurant specializing in traditional Italian cuisine. I can teach you authentic recipes passed down through generations.', NULL, 'Florence, Italy', 'active'),
(5, 'childcare', 'French', '2025-05-06', 'We have a lovely home with three young children. We are looking for help with childcare and light housework.', NULL, 'Paris, France', 'active');

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
  `status` varchar(255) DEFAULT NULL,
  `is_read` tinyint(1) NOT NULL DEFAULT 0,
  `sender_type` varchar(20) NOT NULL,
  `receiver_type` varchar(20) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `message`
--

INSERT INTO `message` (`message_id`, `sender_id`, `receiver_id`, `content`, `timestamp`, `status`, `is_read`, `sender_type`, `receiver_type`) VALUES
(1, 2, 6, 'Thank you for your application to our English Teaching opportunity. When can you start?', '2025-05-01 21:00:00', 'delivered', 1, 'host', 'traveler'),
(2, 6, 2, 'I can start on June 1st as mentioned in my application. Looking forward to meeting you!', '2025-05-01 21:00:00', 'delivered', 1, 'traveler', 'host'),
(3, 3, 8, 'Your application has been accepted! We are excited to have you join us on our farm.', '2025-04-26 21:00:00', 'delivered', 1, 'host', 'traveler'),
(4, 8, 3, 'Thank you! I am very excited to join your farm. Do you need me to bring any specific clothing or equipment?', '2025-04-26 21:00:00', 'delivered', 1, 'traveler', 'host'),
(5, 3, 8, 'Just bring sturdy work clothes, boots, and a hat for sun protection. We will provide all the tools and equipment.', '2025-04-27 21:00:00', 'delivered', 0, 'host', 'traveler'),
(6, 4, 7, 'Thank you for your interest in our kitchen assistant position. Do you have any food allergies?', '2025-05-03 21:00:00', 'delivered', 1, 'host', 'traveler'),
(7, 7, 4, 'No, I don\'t have any food allergies. I am excited about the possibility of learning Italian cooking!', '2025-05-03 21:00:00', 'delivered', 0, 'traveler', 'host'),
(8, 5, 7, 'We have reviewed your application and unfortunately, we have found someone who better fits our needs. Thank you for your interest.', '2025-04-21 22:00:00', 'delivered', 1, 'host', 'traveler');

--
-- Triggers `message`
--
DELIMITER $$
CREATE TRIGGER `new_message_notification` AFTER INSERT ON `message` FOR EACH ROW BEGIN
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
END
$$
DELIMITER ;

-- --------------------------------------------------------

--
-- Table structure for table `notification`
--

CREATE TABLE `notification` (
  `notification_id` bigint(20) NOT NULL,
  `receiver_id` bigint(20) DEFAULT NULL,
  `content` text DEFAULT NULL,
  `timestamp` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp(),
  `is_read` tinyint(1) NOT NULL DEFAULT 0
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `notification`
--

INSERT INTO `notification` (`notification_id`, `receiver_id`, `content`, `timestamp`, `is_read`) VALUES
(1, 6, 'You have received a new message from Sarah Johnson.', '2025-05-06 00:55:29', 0),
(2, 2, 'You have received a new message from David Lee.', '2025-05-06 00:55:29', 0),
(3, 8, 'You have received a new message from Michael Brown.', '2025-05-06 00:55:29', 0),
(4, 3, 'You have received a new message from Daniel Taylor.', '2025-05-06 00:55:29', 0),
(5, 8, 'You have received a new message from Michael Brown.', '2025-05-06 00:55:29', 0),
(6, 7, 'You have received a new message from Emma Davis.', '2025-05-06 00:55:29', 0),
(7, 4, 'You have received a new message from Sophia Martinez.', '2025-05-06 00:55:29', 0),
(8, 7, 'You have received a new message from James Wilson.', '2025-05-06 00:55:29', 0),
(9, 6, 'You have received a new message from Sarah Johnson.', '2025-05-01 21:00:00', 1),
(10, 2, 'You have received a new message from David Lee.', '2025-05-01 21:00:00', 1),
(11, 8, 'Your application for \"Organic Farm Helper\" has been accepted.', '2025-04-26 21:00:00', 1),
(12, 8, 'You have received a new message from Michael Brown.', '2025-04-26 21:00:00', 1),
(13, 8, 'You have received a new message from Michael Brown.', '2025-04-27 21:00:00', 0),
(14, 7, 'You have received a new message from Emma Davis.', '2025-05-03 21:00:00', 1),
(15, 4, 'You have received a new message from Sophia Martinez.', '2025-05-03 21:00:00', 0),
(16, 7, 'Your application for \"Childcare and Light Housework\" has been rejected.', '2025-04-21 22:00:00', 1),
(17, 7, 'You have received a new message from James Wilson.', '2025-04-21 22:00:00', 1);

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
  `requirements` text DEFAULT NULL,
  `max_volunteers` int(11) DEFAULT 1
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `opportunity`
--

INSERT INTO `opportunity` (`opportunity_id`, `opportunity_photo`, `title`, `description`, `location`, `start_date`, `end_date`, `category`, `host_id`, `status`, `created_at`, `requirements`, `max_volunteers`) VALUES
(1, NULL, 'English Teaching Assistant', 'Help teach English to local children aged 8-12. Classes are held Monday to Friday, 3 hours per day. Accommodation and meals provided.', 'London, UK', '2025-06-05', '2025-08-04', 'teaching', 2, 'closed', '2025-05-09 18:24:45', 'Fluent English, experience with children, patient and enthusiastic.', 2),
(2, NULL, 'Organic Farm Helper', 'Join us on our organic farm to help with planting, harvesting, and animal care. Work 5-6 hours per day, 5 days a week. Private room in farmhouse provided.', 'Valencia, Spain', '2025-05-21', '2025-07-20', 'farming', 3, 'open', '2025-05-06 00:55:29', 'Physically fit, willing to work outdoors in all weather, some farming experience preferred.', 3),
(3, NULL, 'Kitchen Assistant & Cooking Classes', 'Help in our family restaurant kitchen and learn authentic Italian cooking. Work 6 hours per day, 5 days a week. Accommodation in apartment above restaurant.', 'Florence, Italy', '2025-06-20', '2025-08-19', 'cooking', 4, 'open', '2025-05-06 00:55:29', 'Interest in cooking, basic kitchen skills, clean and organized.', 1),
(4, NULL, 'Childcare and Light Housework', 'Help care for our three children (ages 3, 5, and 7) and assist with light housework. Work 4-5 hours per day, weekends off. Private room in family home.', 'Paris, France', '2025-05-26', '2025-07-25', 'childcare', 5, 'open', '2025-05-06 00:55:29', 'Experience with children, responsible, patient, non-smoker.', 1);

-- --------------------------------------------------------

--
-- Table structure for table `payment_verification_requests`
--

CREATE TABLE `payment_verification_requests` (
  `request_id` int(11) NOT NULL,
  `traveler_id` int(11) NOT NULL,
  `booking_id` varchar(50) DEFAULT NULL,
  `transaction_id` varchar(50) DEFAULT NULL,
  `issue_type` enum('double_payment','payment_not_received','refund_request','payment_method_change','other') NOT NULL,
  `issue_description` text NOT NULL,
  `action_required` text NOT NULL,
  `status` enum('new','pending','in_progress','resolved','closed') NOT NULL DEFAULT 'new',
  `priority` enum('low','normal','high','urgent') NOT NULL DEFAULT 'normal',
  `created_at` timestamp NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `payment_verification_requests`
--

INSERT INTO `payment_verification_requests` (`request_id`, `traveler_id`, `booking_id`, `transaction_id`, `issue_type`, `issue_description`, `action_required`, `status`, `priority`, `created_at`, `updated_at`) VALUES
(5, 6, 'BK-2023-45678', 'TXN-10001', 'double_payment', 'Traveler reports being charged twice for the same booking. First payment of $5,000 on 15/05/2023 and second payment of $5,000 on 16/05/2023.', 'Verify both transactions in the database and process refund if confirmed.', 'resolved', 'normal', '2023-05-17 07:30:00', '2025-05-06 04:05:33'),
(6, 7, 'BK-2023-12345', 'TXN-10003', 'payment_not_received', 'Traveler made payment of $7,500 on 10/06/2023 but host reports not receiving the payment.', 'Check payment status in the system and verify with payment gateway.', 'new', 'urgent', '2023-06-15 11:45:00', '2025-05-06 04:03:59'),
(7, 8, 'BK-2023-78901', 'TXN-789012', 'refund_request', 'Traveler requesting refund for cancelled booking. Original payment of $12,000 made on 01/04/2023. Cancellation policy allows for 80% refund.', 'Calculate refund amount and process according to cancellation policy.', 'in_progress', 'normal', '2023-04-10 07:15:00', '2025-05-06 04:02:08'),
(8, 9, 'BK-2023-34567', 'TXN-10004', 'payment_method_change', 'Traveler wants to change payment method from credit card to UPI for upcoming booking. Original payment not yet processed.', 'Update payment method in the system and send confirmation to traveler.', 'new', 'low', '2023-07-25 13:20:00', '2025-05-06 04:02:08'),
(9, 6, 'BK-2023-56789', 'TXN-10005', 'other', 'Payment failed multiple times. Traveler needs assistance with alternative payment method.', 'Contact traveler to provide alternative payment options.', 'resolved', 'high', '2023-08-06 08:10:00', '2025-05-06 04:35:30');

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
  `admin_response` text DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NULL DEFAULT NULL ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `report`
--

INSERT INTO `report` (`report_id`, `reported_by_id`, `target_user_id`, `report_content`, `status`, `report_type`, `admin_response`, `created_at`, `updated_at`) VALUES
(1, 2, 6, 'un polite words ', 'reviewed', 'message', NULL, '2025-05-06 21:32:19', '2025-05-06 21:43:42'),
(2, 2, 3, 'iohguiyfulfvkujgh', 'open', 'message', NULL, '2025-05-06 23:05:49', NULL),
(3, 4, 8, 'kluhytrfghj;joiuodryh;otury;jouytuj', 'open', 'opportunity', NULL, '2025-05-06 23:20:49', NULL);

-- --------------------------------------------------------

--
-- Table structure for table `review`
--

CREATE TABLE `review` (
  `review_id` bigint(20) NOT NULL,
  `sender_id` bigint(20) DEFAULT NULL,
  `receiver_id` bigint(20) DEFAULT NULL,
  `opportunity_id` bigint(20) DEFAULT NULL,
  `rating` int(11) DEFAULT NULL,
  `comment` text DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `review`
--

INSERT INTO `review` (`review_id`, `sender_id`, `receiver_id`, `opportunity_id`, `rating`, `comment`, `created_at`) VALUES
(1, 3, 8, 2, 5, 'David was an excellent helper on our farm. He is hardworking, reliable, and quickly learned our farming methods. We would welcome him back anytime!', '2025-04-05 22:00:00'),
(2, 8, 3, 2, 4, 'I had a wonderful experience at Michael\'s farm. I learned a lot about organic farming and enjoyed the beautiful Spanish countryside. The accommodation was comfortable and the family was very welcoming.', '2025-04-06 22:00:00');

--
-- Triggers `review`
--
DELIMITER $$
CREATE TRIGGER `update_host_rating` AFTER INSERT ON `review` FOR EACH ROW BEGIN
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
END
$$
DELIMITER ;
DELIMITER $$
CREATE TRIGGER `update_traveler_rating` AFTER INSERT ON `review` FOR EACH ROW BEGIN
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
END
$$
DELIMITER ;

-- --------------------------------------------------------

--
-- Table structure for table `settings`
--

CREATE TABLE `settings` (
  `setting_id` bigint(20) NOT NULL,
  `setting_key` varchar(255) NOT NULL,
  `setting_value` text DEFAULT NULL,
  `setting_group` varchar(255) DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NULL DEFAULT NULL ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `settings`
--

INSERT INTO `settings` (`setting_id`, `setting_key`, `setting_value`, `setting_group`, `created_at`, `updated_at`) VALUES
(1, 'site_name', 'HomeStay Exchange', 'general', '2025-05-05 21:00:00', NULL),
(2, 'contact_email', 'support@homestay.com', 'contact', '2025-05-05 21:00:00', NULL),
(3, 'max_applications_per_traveler', '10', 'limits', '2025-05-05 21:00:00', NULL),
(4, 'max_active_opportunities_per_host', '5', 'limits', '2025-05-05 21:00:00', NULL),
(5, 'default_application_fee', '25.00', 'fees', '2025-05-05 21:00:00', NULL);

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

--
-- Dumping data for table `support_content`
--

INSERT INTO `support_content` (`content_id`, `title`, `content`, `category`, `status`, `created_at`, `last_updated`) VALUES
(1, 'How to Create a Great Host Profile', 'A complete profile helps travelers understand what you offer and increases your chances of finding suitable volunteers. Include clear photos of your property, detailed description of tasks, and information about accommodation and meals.', 'account', 'active', '2025-05-06 01:58:37', '2025-05-06 00:58:37'),
(2, 'Safety Tips for Travelers', 'Always research your host before accepting an opportunity. Read reviews from previous travelers, have a video call before committing, and share your itinerary with family or friends. Trust your instincts and have a backup plan.', 'safety', 'active', '2025-05-05 21:00:00', NULL),
(3, 'Creating Effective Opportunity Listings', 'Be specific about the work required, hours expected, and skills needed. Include information about accommodation, meals, and any additional benefits. Clear expectations lead to better matches and happier experiences.', 'opportunity', 'active', '2025-05-05 21:00:00', NULL),
(4, 'Resolving Conflicts During Your Stay', 'Open communication is key to resolving conflicts. Discuss issues calmly and directly with your host/traveler. If needed, contact our support team for mediation. Remember that cultural differences can sometimes lead to misunderstandings.', 'other', 'active', '2025-05-05 21:00:00', NULL);

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
(6, 'Teaching, Gardening', 'English, Spanish', 'English', '2025-05-06', 'I am a college student taking a gap year to travel and learn new skills. I have experience in teaching and gardening.', NULL, 'New York, USA', NULL, 'active'),
(7, 'Cooking, Childcare', 'English, French', 'English', '2025-05-06', 'I love cooking and working with children. I have worked as a nanny for 3 years and have a culinary arts certificate.', NULL, 'Toronto, Canada', NULL, 'active'),
(8, 'Farming, Construction', 'English, German', 'English', '2025-05-06', 'I grew up on a farm and have experience with various farming tasks. I also have skills in basic construction and repairs.', 5, 'Sydney, Australia', NULL, 'active'),
(9, 'Teaching, Music', 'English, Japanese', 'English', '2025-05-06', 'I am a music teacher looking to travel and share my knowledge. I can teach piano, guitar, and voice.', NULL, 'Tokyo, Japan', NULL, 'active');

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
(1, 'ADMIN123', 'admin', '$2y$10$ViiphTti/32rIhyT8qLLPe5cIokOyod32nxnpUf1dvYF89fo75KIy', 'admin@homestay.com', '2025-05-06 00:55:28', 'male', '1990-01-01', 'Admin', 'User', '1234567890', NULL, '2025-05-06 00:55:28'),
(2, 'HOST001', 'host', '$2y$10$ViiphTti/32rIhyT8qLLPe5cIokOyod32nxnpUf1dvYF89fo75KIy', 'host1@example.com', '2025-05-06 00:55:28', 'female', '1985-05-15', 'Sarah', 'Johnson', '5551234567', NULL, '2025-05-06 00:55:28'),
(3, 'HOST002', 'host', '$2y$10$ViiphTti/32rIhyT8qLLPe5cIokOyod32nxnpUf1dvYF89fo75KIy', 'host2@example.com', '2025-05-06 00:55:28', 'male', '1978-09-23', 'Michael', 'Brown', '5552345678', NULL, '2025-05-06 00:55:28'),
(4, 'HOST003', 'host', '$2y$10$ViiphTti/32rIhyT8qLLPe5cIokOyod32nxnpUf1dvYF89fo75KIy', 'host3@example.com', '2025-05-06 00:55:28', 'female', '1990-03-10', 'Emma', 'Davis', '5553456789', NULL, '2025-05-06 00:55:28'),
(5, 'HOST004', 'host', '$2y$10$ViiphTti/32rIhyT8qLLPe5cIokOyod32nxnpUf1dvYF89fo75KIy', 'host4@example.com', '2025-05-06 00:55:28', 'male', '1982-11-05', 'James', 'Wilson', '5554567890', NULL, '2025-05-06 00:55:28'),
(6, 'TRAV001', 'traveler', '$2y$10$ViiphTti/32rIhyT8qLLPe5cIokOyod32nxnpUf1dvYF89fo75KIy', 'traveler1@example.com', '2025-05-06 00:55:28', 'male', '1995-07-20', 'David', 'Lee', '5555678901', NULL, '2025-05-06 00:55:28'),
(7, 'TRAV002', 'traveler', '$2y$10$ViiphTti/32rIhyT8qLLPe5cIokOyod32nxnpUf1dvYF89fo75KIy', 'traveler2@example.com', '2025-05-06 00:55:28', 'female', '1992-02-14', 'Sophia', 'Martinez', '5556789012', NULL, '2025-05-06 00:55:28'),
(8, 'TRAV003', 'traveler', '$2y$10$ViiphTti/32rIhyT8qLLPe5cIokOyod32nxnpUf1dvYF89fo75KIy', 'traveler3@example.com', '2025-05-06 00:55:28', 'male', '1988-12-30', 'Daniel', 'Taylor', '5557890123', NULL, '2025-05-06 00:55:28'),
(9, 'TRAV004', 'traveler', '$2y$10$ViiphTti/32rIhyT8qLLPe5cIokOyod32nxnpUf1dvYF89fo75KIy', 'traveler4@example.com', '2025-05-06 00:55:28', 'female', '1993-08-17', 'Olivia', 'Anderson', '5558901234', NULL, '2025-05-06 00:55:28'),
(10, '12345678765432', 'admin', '$2y$10$bvsBWs9YXStYkF.tvQkShOzFxE5JLFio4DBMurwH4Et8iUsibRV4C', 'host@email', '2025-05-07 02:38:51', 'male', '1995-11-25', 'Host', 'Last', '012345456', '', '2025-05-07 02:38:51');

--
-- Triggers `users`
--
DELIMITER $$
CREATE TRIGGER `log_user_login` AFTER UPDATE ON `users` FOR EACH ROW BEGIN
  IF NEW.last_login != OLD.last_login THEN
    INSERT INTO user_activity_log (user_id, activity_type, activity_details, created_at)
    VALUES (NEW.user_id, 'login', 'User logged in', NOW());
  END IF;
END
$$
DELIMITER ;

-- --------------------------------------------------------

--
-- Table structure for table `user_activity_log`
--

CREATE TABLE `user_activity_log` (
  `log_id` bigint(20) NOT NULL,
  `user_id` bigint(20) DEFAULT NULL,
  `activity_type` varchar(255) NOT NULL,
  `activity_details` text DEFAULT NULL,
  `ip_address` varchar(45) DEFAULT NULL,
  `user_agent` text DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `user_activity_log`
--

INSERT INTO `user_activity_log` (`log_id`, `user_id`, `activity_type`, `activity_details`, `ip_address`, `user_agent`, `created_at`) VALUES
(1, 2, 'login', 'User logged in', '192.168.1.1', NULL, '2025-05-03 21:00:00'),
(2, 3, 'login', 'User logged in', '192.168.1.2', NULL, '2025-05-02 21:00:00'),
(3, 6, 'login', 'User logged in', '192.168.1.3', NULL, '2025-05-04 21:00:00'),
(4, 7, 'login', 'User logged in', '192.168.1.4', NULL, '2025-05-03 21:00:00'),
(5, 2, 'create_opportunity', 'Created new opportunity: English Teaching Assistant', '192.168.1.1', NULL, '2025-04-25 21:00:00'),
(6, 3, 'create_opportunity', 'Created new opportunity: Organic Farm Helper', '192.168.1.2', NULL, '2025-04-20 22:00:00'),
(7, 6, 'apply_opportunity', 'Applied to opportunity: English Teaching Assistant', '192.168.1.3', NULL, '2025-04-30 21:00:00'),
(8, 8, 'apply_opportunity', 'Applied to opportunity: Organic Farm Helper', '192.168.1.5', NULL, '2025-04-25 21:00:00'),
(9, 10, 'login', 'User logged in', NULL, NULL, '2025-05-06 01:00:14'),
(10, 10, 'login', 'User logged in', NULL, NULL, '2025-05-06 23:37:01'),
(11, 10, 'login', 'User logged in', NULL, NULL, '2025-05-06 23:38:40'),
(12, 10, 'login', 'User logged in', NULL, NULL, '2025-05-07 02:18:32'),
(13, 10, 'login', 'User logged in', NULL, NULL, '2025-05-07 02:36:12'),
(14, 10, 'login', 'User logged in', NULL, NULL, '2025-05-07 02:38:51');

-- --------------------------------------------------------

--
-- Stand-in structure for view `user_notifications`
-- (See below for the actual view)
--
CREATE TABLE `user_notifications` (
`notification_id` bigint(20)
,`receiver_id` bigint(20)
,`content` text
,`timestamp` timestamp
,`is_read` tinyint(1)
,`user_type` enum('host','traveler','admin')
,`first_name` varchar(255)
,`last_name` varchar(255)
);

-- --------------------------------------------------------

--
-- Structure for view `active_opportunities`
--
DROP TABLE IF EXISTS `active_opportunities`;

CREATE ALGORITHM=UNDEFINED DEFINER=`root`@`localhost` SQL SECURITY DEFINER VIEW `active_opportunities`  AS SELECT `o`.`opportunity_id` AS `opportunity_id`, `o`.`opportunity_photo` AS `opportunity_photo`, `o`.`title` AS `title`, `o`.`description` AS `description`, `o`.`location` AS `location`, `o`.`start_date` AS `start_date`, `o`.`end_date` AS `end_date`, `o`.`category` AS `category`, `o`.`host_id` AS `host_id`, `o`.`status` AS `status`, `o`.`created_at` AS `created_at`, `o`.`requirements` AS `requirements`, `o`.`max_volunteers` AS `max_volunteers`, `h`.`property_type` AS `host_property_type`, `h`.`preferred_language` AS `host_language`, `u`.`first_name` AS `host_first_name`, `u`.`last_name` AS `host_last_name`, `u`.`profile_picture` AS `host_profile_picture` FROM ((`opportunity` `o` join `hosts` `h` on(`o`.`host_id` = `h`.`host_id`)) join `users` `u` on(`h`.`host_id` = `u`.`user_id`)) WHERE `o`.`status` = 'open' AND `o`.`end_date` >= curdate() ORDER BY `o`.`created_at` DESC ;

-- --------------------------------------------------------

--
-- Structure for view `application_details`
--
DROP TABLE IF EXISTS `application_details`;

CREATE ALGORITHM=UNDEFINED DEFINER=`root`@`localhost` SQL SECURITY DEFINER VIEW `application_details`  AS SELECT `a`.`application_id` AS `application_id`, `a`.`opportunity_id` AS `opportunity_id`, `a`.`traveler_id` AS `traveler_id`, `a`.`status` AS `status`, `a`.`comment` AS `comment`, `a`.`applied_date` AS `applied_date`, `a`.`message` AS `message`, `a`.`availability` AS `availability`, `a`.`experience` AS `experience`, `t`.`skill` AS `traveler_skill`, `t`.`language_spoken` AS `traveler_language`, `u`.`first_name` AS `traveler_first_name`, `u`.`last_name` AS `traveler_last_name`, `u`.`profile_picture` AS `traveler_profile_picture`, `o`.`title` AS `opportunity_title`, `o`.`location` AS `opportunity_location`, `o`.`category` AS `opportunity_category`, `o`.`start_date` AS `opportunity_start_date`, `o`.`end_date` AS `opportunity_end_date` FROM (((`applications` `a` join `traveler` `t` on(`a`.`traveler_id` = `t`.`traveler_id`)) join `users` `u` on(`t`.`traveler_id` = `u`.`user_id`)) join `opportunity` `o` on(`a`.`opportunity_id` = `o`.`opportunity_id`)) ORDER BY `a`.`applied_date` DESC ;

-- --------------------------------------------------------

--
-- Structure for view `user_notifications`
--
DROP TABLE IF EXISTS `user_notifications`;

CREATE ALGORITHM=UNDEFINED DEFINER=`root`@`localhost` SQL SECURITY DEFINER VIEW `user_notifications`  AS SELECT `n`.`notification_id` AS `notification_id`, `n`.`receiver_id` AS `receiver_id`, `n`.`content` AS `content`, `n`.`timestamp` AS `timestamp`, `n`.`is_read` AS `is_read`, `u`.`user_type` AS `user_type`, `u`.`first_name` AS `first_name`, `u`.`last_name` AS `last_name` FROM (`notification` `n` join `users` `u` on(`n`.`receiver_id` = `u`.`user_id`)) ORDER BY `n`.`timestamp` DESC ;

--
-- Indexes for dumped tables
--

--
-- Indexes for table `applications`
--
ALTER TABLE `applications`
  ADD PRIMARY KEY (`application_id`),
  ADD KEY `opportunity_id` (`opportunity_id`),
  ADD KEY `traveler_id` (`traveler_id`),
  ADD KEY `idx_applications_status` (`status`);

--
-- Indexes for table `card`
--
ALTER TABLE `card`
  ADD PRIMARY KEY (`card_id`),
  ADD KEY `traveler_id` (`traveler_id`);

--
-- Indexes for table `fee`
--
ALTER TABLE `fee`
  ADD PRIMARY KEY (`fee_id`),
  ADD KEY `created_by` (`created_by`);

--
-- Indexes for table `fee_assignment`
--
ALTER TABLE `fee_assignment`
  ADD PRIMARY KEY (`assignment_id`),
  ADD KEY `fee_id` (`fee_id`),
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
  ADD PRIMARY KEY (`host_id`),
  ADD KEY `idx_hosts_location` (`location`);

--
-- Indexes for table `message`
--
ALTER TABLE `message`
  ADD PRIMARY KEY (`message_id`),
  ADD KEY `sender_id` (`sender_id`),
  ADD KEY `receiver_id` (`receiver_id`),
  ADD KEY `idx_message_sender` (`sender_id`,`sender_type`),
  ADD KEY `idx_message_receiver` (`receiver_id`,`receiver_type`),
  ADD KEY `idx_message_timestamp` (`timestamp`);

--
-- Indexes for table `notification`
--
ALTER TABLE `notification`
  ADD PRIMARY KEY (`notification_id`),
  ADD KEY `receiver_id` (`receiver_id`),
  ADD KEY `idx_notification_timestamp` (`timestamp`);

--
-- Indexes for table `opportunity`
--
ALTER TABLE `opportunity`
  ADD PRIMARY KEY (`opportunity_id`),
  ADD KEY `host_id` (`host_id`),
  ADD KEY `idx_opportunity_location` (`location`),
  ADD KEY `idx_opportunity_category` (`category`),
  ADD KEY `idx_opportunity_dates` (`start_date`,`end_date`),
  ADD KEY `idx_opportunity_status` (`status`);

--
-- Indexes for table `payment_verification_requests`
--
ALTER TABLE `payment_verification_requests`
  ADD PRIMARY KEY (`request_id`),
  ADD KEY `traveler_id` (`traveler_id`);

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
  ADD KEY `sender_id` (`sender_id`),
  ADD KEY `receiver_id` (`receiver_id`),
  ADD KEY `opportunity_id` (`opportunity_id`),
  ADD KEY `idx_review_rating` (`rating`);

--
-- Indexes for table `settings`
--
ALTER TABLE `settings`
  ADD PRIMARY KEY (`setting_id`),
  ADD UNIQUE KEY `setting_key` (`setting_key`);

--
-- Indexes for table `support_content`
--
ALTER TABLE `support_content`
  ADD PRIMARY KEY (`content_id`);

--
-- Indexes for table `traveler`
--
ALTER TABLE `traveler`
  ADD PRIMARY KEY (`traveler_id`),
  ADD KEY `idx_traveler_location` (`location`);

--
-- Indexes for table `users`
--
ALTER TABLE `users`
  ADD PRIMARY KEY (`user_id`),
  ADD KEY `idx_users_email` (`email`),
  ADD KEY `idx_users_name` (`first_name`,`last_name`);

--
-- Indexes for table `user_activity_log`
--
ALTER TABLE `user_activity_log`
  ADD PRIMARY KEY (`log_id`),
  ADD KEY `user_id` (`user_id`),
  ADD KEY `activity_type` (`activity_type`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `applications`
--
ALTER TABLE `applications`
  MODIFY `application_id` bigint(20) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=6;

--
-- AUTO_INCREMENT for table `card`
--
ALTER TABLE `card`
  MODIFY `card_id` bigint(20) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=8;

--
-- AUTO_INCREMENT for table `fee`
--
ALTER TABLE `fee`
  MODIFY `fee_id` bigint(20) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=5;

--
-- AUTO_INCREMENT for table `fee_assignment`
--
ALTER TABLE `fee_assignment`
  MODIFY `assignment_id` bigint(20) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `fee_transaction`
--
ALTER TABLE `fee_transaction`
  MODIFY `fee_id` bigint(20) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=10;

--
-- AUTO_INCREMENT for table `message`
--
ALTER TABLE `message`
  MODIFY `message_id` bigint(20) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=9;

--
-- AUTO_INCREMENT for table `notification`
--
ALTER TABLE `notification`
  MODIFY `notification_id` bigint(20) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=18;

--
-- AUTO_INCREMENT for table `opportunity`
--
ALTER TABLE `opportunity`
  MODIFY `opportunity_id` bigint(20) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=6;

--
-- AUTO_INCREMENT for table `payment_verification_requests`
--
ALTER TABLE `payment_verification_requests`
  MODIFY `request_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=10;

--
-- AUTO_INCREMENT for table `report`
--
ALTER TABLE `report`
  MODIFY `report_id` bigint(20) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=4;

--
-- AUTO_INCREMENT for table `review`
--
ALTER TABLE `review`
  MODIFY `review_id` bigint(20) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3;

--
-- AUTO_INCREMENT for table `settings`
--
ALTER TABLE `settings`
  MODIFY `setting_id` bigint(20) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=6;

--
-- AUTO_INCREMENT for table `support_content`
--
ALTER TABLE `support_content`
  MODIFY `content_id` bigint(20) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=5;

--
-- AUTO_INCREMENT for table `users`
--
ALTER TABLE `users`
  MODIFY `user_id` bigint(20) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=11;

--
-- AUTO_INCREMENT for table `user_activity_log`
--
ALTER TABLE `user_activity_log`
  MODIFY `log_id` bigint(20) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=15;

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
-- Constraints for table `fee`
--
ALTER TABLE `fee`
  ADD CONSTRAINT `fee_ibfk_1` FOREIGN KEY (`created_by`) REFERENCES `users` (`user_id`);

--
-- Constraints for table `fee_assignment`
--
ALTER TABLE `fee_assignment`
  ADD CONSTRAINT `fee_assignment_ibfk_1` FOREIGN KEY (`fee_id`) REFERENCES `fee` (`fee_id`),
  ADD CONSTRAINT `fee_assignment_ibfk_2` FOREIGN KEY (`traveler_id`) REFERENCES `users` (`user_id`);

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

--
-- Constraints for table `user_activity_log`
--
ALTER TABLE `user_activity_log`
  ADD CONSTRAINT `user_activity_log_ibfk_1` FOREIGN KEY (`user_id`) REFERENCES `users` (`user_id`);
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
