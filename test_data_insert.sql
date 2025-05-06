-- Insert test admin user
INSERT INTO users (national_id, user_type, password, email, gender, date_of_birth, first_name, last_name, phone_number)
VALUES ('ADMIN123', 'admin', '$2y$10$ViiphTti/32rIhyT8qLLPe5cIokOyod32nxnpUf1dvYF89fo75KIy', 'admin@homestay.com', 'male', '1990-01-01', 'Admin', 'User', '1234567890');

-- Insert test host users
INSERT INTO users (national_id, user_type, password, email, gender, date_of_birth, first_name, last_name, phone_number)
VALUES 
('HOST001', 'host', '$2y$10$ViiphTti/32rIhyT8qLLPe5cIokOyod32nxnpUf1dvYF89fo75KIy', 'host1@example.com', 'female', '1985-05-15', 'Sarah', 'Johnson', '5551234567'),
('HOST002', 'host', '$2y$10$ViiphTti/32rIhyT8qLLPe5cIokOyod32nxnpUf1dvYF89fo75KIy', 'host2@example.com', 'male', '1978-09-23', 'Michael', 'Brown', '5552345678'),
('HOST003', 'host', '$2y$10$ViiphTti/32rIhyT8qLLPe5cIokOyod32nxnpUf1dvYF89fo75KIy', 'host3@example.com', 'female', '1990-03-10', 'Emma', 'Davis', '5553456789'),
('HOST004', 'host', '$2y$10$ViiphTti/32rIhyT8qLLPe5cIokOyod32nxnpUf1dvYF89fo75KIy', 'host4@example.com', 'male', '1982-11-05', 'James', 'Wilson', '5554567890');

-- Insert test traveler users
INSERT INTO users (national_id, user_type, password, email, gender, date_of_birth, first_name, last_name, phone_number)
VALUES 
('TRAV001', 'traveler', '$2y$10$ViiphTti/32rIhyT8qLLPe5cIokOyod32nxnpUf1dvYF89fo75KIy', 'traveler1@example.com', 'male', '1995-07-20', 'David', 'Lee', '5555678901'),
('TRAV002', 'traveler', '$2y$10$ViiphTti/32rIhyT8qLLPe5cIokOyod32nxnpUf1dvYF89fo75KIy', 'traveler2@example.com', 'female', '1992-02-14', 'Sophia', 'Martinez', '5556789012'),
('TRAV003', 'traveler', '$2y$10$ViiphTti/32rIhyT8qLLPe5cIokOyod32nxnpUf1dvYF89fo75KIy', 'traveler3@example.com', 'male', '1988-12-30', 'Daniel', 'Taylor', '5557890123'),
('TRAV004', 'traveler', '$2y$10$ViiphTti/32rIhyT8qLLPe5cIokOyod32nxnpUf1dvYF89fo75KIy', 'traveler4@example.com', 'female', '1993-08-17', 'Olivia', 'Anderson', '5558901234');

-- Insert host profiles
INSERT INTO hosts (host_id, property_type, preferred_language, joined_date, bio, location, status)
VALUES 
(2, 'teaching', 'English', CURDATE(), 'I am a retired English teacher with 30 years of experience. I love sharing my knowledge with travelers from around the world.', 'London, UK', 'active'),
(3, 'farming', 'Spanish', CURDATE(), 'Our family has been running this organic farm for three generations. We grow vegetables and raise free-range chickens.', 'Valencia, Spain', 'active'),
(4, 'cooking', 'Italian', CURDATE(), 'I run a small restaurant specializing in traditional Italian cuisine. I can teach you authentic recipes passed down through generations.', 'Florence, Italy', 'active'),
(5, 'childcare', 'French', CURDATE(), 'We have a lovely home with three young children. We are looking for help with childcare and light housework.', 'Paris, France', 'active');

-- Insert traveler profiles
INSERT INTO traveler (traveler_id, skill, language_spoken, preferred_language, joined_date, bio, location, status)
VALUES 
(6, 'Teaching, Gardening', 'English, Spanish', 'English', CURDATE(), 'I am a college student taking a gap year to travel and learn new skills. I have experience in teaching and gardening.', 'New York, USA', 'active'),
(7, 'Cooking, Childcare', 'English, French', 'English', CURDATE(), 'I love cooking and working with children. I have worked as a nanny for 3 years and have a culinary arts certificate.', 'Toronto, Canada', 'active'),
(8, 'Farming, Construction', 'English, German', 'English', CURDATE(), 'I grew up on a farm and have experience with various farming tasks. I also have skills in basic construction and repairs.', 'Sydney, Australia', 'active'),
(9, 'Teaching, Music', 'English, Japanese', 'English', CURDATE(), 'I am a music teacher looking to travel and share my knowledge. I can teach piano, guitar, and voice.', 'Tokyo, Japan', 'active');

-- Insert opportunities
INSERT INTO opportunity (opportunity_photo, title, description, location, start_date, end_date, category, host_id, status, requirements, max_volunteers)
VALUES 
(NULL, 'English Teaching Assistant', 'Help teach English to local children aged 8-12. Classes are held Monday to Friday, 3 hours per day. Accommodation and meals provided.', 'London, UK', DATE_ADD(CURDATE(), INTERVAL 30 DAY), DATE_ADD(CURDATE(), INTERVAL 90 DAY), 'teaching', 2, 'open', 'Fluent English, experience with children, patient and enthusiastic.', 2),
(NULL, 'Organic Farm Helper', 'Join us on our organic farm to help with planting, harvesting, and animal care. Work 5-6 hours per day, 5 days a week. Private room in farmhouse provided.', 'Valencia, Spain', DATE_ADD(CURDATE(), INTERVAL 15 DAY), DATE_ADD(CURDATE(), INTERVAL 75 DAY), 'farming', 3, 'open', 'Physically fit, willing to work outdoors in all weather, some farming experience preferred.', 3),
(NULL, 'Kitchen Assistant & Cooking Classes', 'Help in our family restaurant kitchen and learn authentic Italian cooking. Work 6 hours per day, 5 days a week. Accommodation in apartment above restaurant.', 'Florence, Italy', DATE_ADD(CURDATE(), INTERVAL 45 DAY), DATE_ADD(CURDATE(), INTERVAL 105 DAY), 'cooking', 4, 'open', 'Interest in cooking, basic kitchen skills, clean and organized.', 1),
(NULL, 'Childcare and Light Housework', 'Help care for our three children (ages 3, 5, and 7) and assist with light housework. Work 4-5 hours per day, weekends off. Private room in family home.', 'Paris, France', DATE_ADD(CURDATE(), INTERVAL 20 DAY), DATE_ADD(CURDATE(), INTERVAL 80 DAY), 'childcare', 5, 'open', 'Experience with children, responsible, patient, non-smoker.', 1),
(NULL, 'Summer English Camp Helper', 'Assist with our summer English camp for teenagers. Activities include language games, sports, and cultural excursions. 6 hours per day, 5 days a week.', 'London, UK', DATE_ADD(CURDATE(), INTERVAL 60 DAY), DATE_ADD(CURDATE(), INTERVAL 120 DAY), 'teaching', 2, 'open', 'Fluent English, energetic, good with teenagers, knowledge of sports or arts is a plus.', 4);

-- Insert applications
INSERT INTO applications (opportunity_id, traveler_id, status, comment, applied_date, message, availability, experience)
VALUES 
(1, 6, 'pending', 'I would love to help teach English to children!', DATE_SUB(CURDATE(), INTERVAL 5 DAY), 'I have experience teaching English to children and would love to join your program. I am available for the entire duration of the opportunity.', 'Available from June 1 to August 31', 'Taught English at a summer camp for 2 years, worked as a tutor for 3 years'),
(2, 8, 'accepted', 'I have extensive farming experience and would be a great fit.', DATE_SUB(CURDATE(), INTERVAL 10 DAY), 'I grew up on a farm and have experience with organic farming methods. I am excited about the opportunity to learn about Spanish farming techniques.', 'Available immediately for 3 months', 'Worked on family farm for 10 years, volunteered at community gardens'),
(3, 7, 'pending', 'I am passionate about cooking and eager to learn Italian cuisine.', DATE_SUB(CURDATE(), INTERVAL 3 DAY), 'I have a culinary arts certificate and would love to learn authentic Italian cooking techniques. I am a hard worker and quick learner.', 'Available for the entire period requested', 'Culinary arts certificate, worked in restaurant kitchen for 1 year'),
(4, 7, 'rejected', 'I have extensive childcare experience and love working with children.', DATE_SUB(CURDATE(), INTERVAL 15 DAY), 'I have worked as a nanny for 3 years and have experience with children of all ages. I am responsible, patient, and enjoy creating fun activities.', 'Available from July through September', 'Professional nanny for 3 years, babysitting experience since age 16'),
(5, 9, 'pending', 'My background in music and teaching would be perfect for this camp.', DATE_SUB(CURDATE(), INTERVAL 2 DAY), 'As a music teacher, I can bring creative activities to the camp. I am fluent in English and have experience working with teenagers.', 'Available for the entire summer', 'Music teacher for 5 years, camp counselor for 2 summers');

-- Insert messages
INSERT INTO message (sender_id, receiver_id, content, timestamp, status, is_read, sender_type, receiver_type)
VALUES 
(2, 6, 'Thank you for your application to our English Teaching opportunity. When can you start?', DATE_SUB(CURDATE(), INTERVAL 4 DAY), 'delivered', 1, 'host', 'traveler'),
(6, 2, 'I can start on June 1st as mentioned in my application. Looking forward to meeting you!', DATE_SUB(CURDATE(), INTERVAL 4 DAY), 'delivered', 1, 'traveler', 'host'),
(3, 8, 'Your application has been accepted! We are excited to have you join us on our farm.', DATE_SUB(CURDATE(), INTERVAL 9 DAY), 'delivered', 1, 'host', 'traveler'),
(8, 3, 'Thank you! I am very excited to join your farm. Do you need me to bring any specific clothing or equipment?', DATE_SUB(CURDATE(), INTERVAL 9 DAY), 'delivered', 1, 'traveler', 'host'),
(3, 8, 'Just bring sturdy work clothes, boots, and a hat for sun protection. We will provide all the tools and equipment.', DATE_SUB(CURDATE(), INTERVAL 8 DAY), 'delivered', 0, 'host', 'traveler'),
(4, 7, 'Thank you for your interest in our kitchen assistant position. Do you have any food allergies?', DATE_SUB(CURDATE(), INTERVAL 2 DAY), 'delivered', 1, 'host', 'traveler'),
(7, 4, 'No, I don\'t have any food allergies. I am excited about the possibility of learning Italian cooking!', DATE_SUB(CURDATE(), INTERVAL 2 DAY), 'delivered', 0, 'traveler', 'host'),
(5, 7, 'We have reviewed your application and unfortunately, we have found someone who better fits our needs. Thank you for your interest.', DATE_SUB(CURDATE(), INTERVAL 14 DAY), 'delivered', 1, 'host', 'traveler');

-- Insert notifications
INSERT INTO notification (receiver_id, content, timestamp, is_read)
VALUES 
(6, 'You have received a new message from Sarah Johnson.', DATE_SUB(CURDATE(), INTERVAL 4 DAY), 1),
(2, 'You have received a new message from David Lee.', DATE_SUB(CURDATE(), INTERVAL 4 DAY), 1),
(8, 'Your application for "Organic Farm Helper" has been accepted.', DATE_SUB(CURDATE(), INTERVAL 9 DAY), 1),
(8, 'You have received a new message from Michael Brown.', DATE_SUB(CURDATE(), INTERVAL 9 DAY), 1),
(8, 'You have received a new message from Michael Brown.', DATE_SUB(CURDATE(), INTERVAL 8 DAY), 0),
(7, 'You have received a new message from Emma Davis.', DATE_SUB(CURDATE(), INTERVAL 2 DAY), 1),
(4, 'You have received a new message from Sophia Martinez.', DATE_SUB(CURDATE(), INTERVAL 2 DAY), 0),
(7, 'Your application for "Childcare and Light Housework" has been rejected.', DATE_SUB(CURDATE(), INTERVAL 14 DAY), 1),
(7, 'You have received a new message from James Wilson.', DATE_SUB(CURDATE(), INTERVAL 14 DAY), 1);

-- Insert reviews
INSERT INTO review (sender_id, receiver_id, opportunity_id, rating, comment, created_at)
VALUES 
(3, 8, 2, 5, 'David was an excellent helper on our farm. He is hardworking, reliable, and quickly learned our farming methods. We would welcome him back anytime!', DATE_SUB(CURDATE(), INTERVAL 30 DAY)),
(8, 3, 2, 4, 'I had a wonderful experience at Michael\'s farm. I learned a lot about organic farming and enjoyed the beautiful Spanish countryside. The accommodation was comfortable and the family was very welcoming.', DATE_SUB(CURDATE(), INTERVAL 29 DAY));

-- Insert support content
INSERT INTO support_content (title, content, category, status, created_at)
VALUES 
('How to Create a Great Host Profile', 'A complete profile helps travelers understand what you offer and increases your chances of finding suitable volunteers. Include clear photos of your property, detailed description of tasks, and information about accommodation and meals.', 'account', 'active', CURDATE()),
('Safety Tips for Travelers', 'Always research your host before accepting an opportunity. Read reviews from previous travelers, have a video call before committing, and share your itinerary with family or friends. Trust your instincts and have a backup plan.', 'safety', 'active', CURDATE()),
('Creating Effective Opportunity Listings', 'Be specific about the work required, hours expected, and skills needed. Include information about accommodation, meals, and any additional benefits. Clear expectations lead to better matches and happier experiences.', 'opportunity', 'active', CURDATE()),
('Resolving Conflicts During Your Stay', 'Open communication is key to resolving conflicts. Discuss issues calmly and directly with your host/traveler. If needed, contact our support team for mediation. Remember that cultural differences can sometimes lead to misunderstandings.', 'other', 'active', CURDATE());

-- Insert settings
INSERT INTO settings (setting_key, setting_value, setting_group, created_at)
VALUES 
('site_name', 'HomeStay Exchange', 'general', CURDATE()),
('contact_email', 'support@homestay.com', 'contact', CURDATE()),
('max_applications_per_traveler', '10', 'limits', CURDATE()),
('max_active_opportunities_per_host', '5', 'limits', CURDATE()),
('default_application_fee', '25.00', 'fees', CURDATE());

-- Insert user activity logs
INSERT INTO user_activity_log (user_id, activity_type, activity_details, ip_address, created_at)
VALUES 
(2, 'login', 'User logged in', '192.168.1.1', DATE_SUB(CURDATE(), INTERVAL 2 DAY)),
(3, 'login', 'User logged in', '192.168.1.2', DATE_SUB(CURDATE(), INTERVAL 3 DAY)),
(6, 'login', 'User logged in', '192.168.1.3', DATE_SUB(CURDATE(), INTERVAL 1 DAY)),
(7, 'login', 'User logged in', '192.168.1.4', DATE_SUB(CURDATE(), INTERVAL 2 DAY)),
(2, 'create_opportunity', 'Created new opportunity: English Teaching Assistant', '192.168.1.1', DATE_SUB(CURDATE(), INTERVAL 10 DAY)),
(3, 'create_opportunity', 'Created new opportunity: Organic Farm Helper', '192.168.1.2', DATE_SUB(CURDATE(), INTERVAL 15 DAY)),
(6, 'apply_opportunity', 'Applied to opportunity: English Teaching Assistant', '192.168.1.3', DATE_SUB(CURDATE(), INTERVAL 5 DAY)),
(8, 'apply_opportunity', 'Applied to opportunity: Organic Farm Helper', '192.168.1.5', DATE_SUB(CURDATE(), INTERVAL 10 DAY));

-- Insert sample data (using user_id 1 for all records for testing)
INSERT INTO `payment_verification_requests`
(`traveler_id`, `booking_id`, `transaction_id`, `issue_type`, `issue_description`, `action_required`, `status`, `priority`)
VALUES
(1, 'BK-2023-45678', 'TXN-10001', 'double_payment', 'Traveler reports being charged twice for the same booking. First payment of ₹5,000 on 15/05/2023 and second payment of ₹5,000 on 16/05/2023.', 'Verify both transactions in the database and process refund if confirmed.', 'pending', 'normal'),
(1, 'BK-2023-12345', 'TXN-10002', 'payment_not_received', 'Traveler made payment of ₹7,500 on 10/06/2023 but host reports not receiving the payment.', 'Check payment status in the system and verify with payment gateway.', 'new', 'urgent'),
(1, 'BK-2023-78901', 'TXN-789012', 'refund_request', 'Traveler requesting refund for cancelled booking. Original payment of ₹12,000 made on 01/04/2023. Cancellation policy allows for 80% refund.', 'Calculate refund amount and process according to cancellation policy.', 'in_progress', 'normal'),
(1, 'BK-2023-34567', 'TXN-10003', 'payment_method_change', 'Traveler wants to change payment method from credit card to UPI for upcoming booking. Original payment not yet processed.', 'Update payment method in the system and send confirmation to traveler.', 'new', 'low');
