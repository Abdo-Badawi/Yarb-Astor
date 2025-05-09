-- Create payment_verification_requests table
CREATE TABLE IF NOT EXISTS `payment_verification_requests` (
  `request_id` int(11) NOT NULL AUTO_INCREMENT,
  `traveler_id` int(11) NOT NULL,
  `booking_id` varchar(50) DEFAULT NULL,
  `transaction_id` varchar(50) DEFAULT NULL,
  `issue_type` enum('double_payment', 'payment_not_received', 'refund_request', 'payment_method_change', 'other') NOT NULL,
  `issue_description` text NOT NULL,
  `action_required` text NOT NULL,
  `status` enum('new', 'pending', 'in_progress', 'resolved', 'closed') NOT NULL DEFAULT 'new',
  `priority` enum('low', 'normal', 'high', 'urgent') NOT NULL DEFAULT 'normal',
  `created_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP,
  `updated_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`request_id`),
  KEY `traveler_id` (`traveler_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- Insert sample data (using user_id 1 for all records for testing)
INSERT INTO `payment_verification_requests`
(`traveler_id`, `booking_id`, `transaction_id`, `issue_type`, `issue_description`, `action_required`, `status`, `priority`)
VALUES
(1, 'BK-2023-45678', 'TXN-10001', 'double_payment', 'Traveler reports being charged twice for the same booking. First payment of ₹5,000 on 15/05/2023 and second payment of ₹5,000 on 16/05/2023.', 'Verify both transactions in the database and process refund if confirmed.', 'pending', 'normal'),
(1, 'BK-2023-12345', 'TXN-10002', 'payment_not_received', 'Traveler made payment of ₹7,500 on 10/06/2023 but host reports not receiving the payment.', 'Check payment status in the system and verify with payment gateway.', 'new', 'urgent'),
(1, 'BK-2023-78901', 'TXN-789012', 'refund_request', 'Traveler requesting refund for cancelled booking. Original payment of ₹12,000 made on 01/04/2023. Cancellation policy allows for 80% refund.', 'Calculate refund amount and process according to cancellation policy.', 'in_progress', 'normal'),
(1, 'BK-2023-34567', 'TXN-10003', 'payment_method_change', 'Traveler wants to change payment method from credit card to UPI for upcoming booking. Original payment not yet processed.', 'Update payment method in the system and send confirmation to traveler.', 'new', 'low');
