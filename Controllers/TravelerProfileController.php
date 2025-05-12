<?php
// Include necessary files
require_once 'DBController.php';
require_once '../Models/User.php';

class TravelerProfileController {
    private $db;
    
    public function __construct() {
        // Initialize DBController instance
        $this->db = new DBController();
    }
    
    /**
     * Get user data by user ID
     * 
     * @param int $userId User ID
     * @return array|null User data as array if found, null otherwise
     */
    public function getUserData($userId) {
        if (!$this->db->openConnection()) {
            return null;
        }
        
        try {
            // First, get the user type
            $userTypeQuery = "SELECT user_type FROM users WHERE user_id = ?";
            $userTypeResult = $this->db->selectPrepared($userTypeQuery, "i", [$userId]);
            
            if (!$userTypeResult || count($userTypeResult) === 0) {
                $this->db->closeConnection();
                return null;
            }
            
            $userType = $userTypeResult[0]['user_type'];
            
            // Get user data based on user type
            if ($userType === 'traveler') {
                $query = "SELECT u.*, t.* 
                          FROM users u
                          LEFT JOIN traveler t ON u.user_id = t.traveler_id
                          WHERE u.user_id = ?";
            } else {
                // For other user types
                $query = "SELECT * FROM users WHERE user_id = ?";
            }
            
            $result = $this->db->selectPrepared($query, "i", [$userId]);
            $this->db->closeConnection();
            
            if (!$result || count($result) === 0) {
                return null;
            }
            
            return $result[0];
        } catch (\Exception $e) {
            error_log("Error getting user data: " . $e->getMessage());
            $this->db->closeConnection();
            return null;
        }
    }

    /**
     * Update user profile
     * 
     * @param int $userId User ID
     * @param array $userData User data to update
     * @return bool True if update successful, false otherwise
     */
    public function updateUserProfile($userId, $userData) {
        if (!$this->db->openConnection()) {
            return false;
        }
        
        try {
            $this->db->getConnection()->begin_transaction();
            
            // Update users table
            $userUpdateQuery = "UPDATE users SET 
                               first_name = ?, 
                               last_name = ?, 
                               email = ?, 
                               phone_number = ?";
            
            $userParams = [
                $userData['first_name'],
                $userData['last_name'],
                $userData['email'],
                $userData['phone_number']
            ];
            
            // Add profile picture to update if provided
            if (isset($userData['profile_picture'])) {
                $userUpdateQuery .= ", profile_picture = ?";
                $userParams[] = $userData['profile_picture'];
                $userTypes = "sssss";
            } else {
                $userTypes = "ssss";
            }
            
            $userUpdateQuery .= " WHERE user_id = ?";
            $userParams[] = $userId;
            $userTypes .= "i";
            
            $userUpdateResult = $this->db->update($userUpdateQuery, $userTypes, $userParams);
            
            if (!$userUpdateResult) {
                $this->db->getConnection()->rollback();
                $this->db->closeConnection();
                return false;
            }
            
            // Check if traveler record exists
            $checkQuery = "SELECT COUNT(*) as count FROM traveler WHERE traveler_id = ?";
            $checkResult = $this->db->selectPrepared($checkQuery, "i", [$userId]);
            
            if ($checkResult[0]['count'] > 0) {
                // Update existing traveler record
                $travelerUpdateQuery = "UPDATE traveler SET 
                                       skill = ?, 
                                       language_spoken = ?, 
                                       preferred_language = ?, 
                                       bio = ?, 
                                       location = ? 
                                       WHERE traveler_id = ?";
                
                $travelerParams = [
                    $userData['skill'] ?? '',
                    $userData['language_spoken'] ?? '',
                    $userData['preferred_language'] ?? '',
                    $userData['bio'] ?? '',
                    $userData['location'] ?? '',
                    $userId
                ];
                
                $travelerUpdateResult = $this->db->update($travelerUpdateQuery, "sssssi", $travelerParams);
            } else {
                // Insert new traveler record
                $travelerInsertQuery = "INSERT INTO traveler 
                                       (traveler_id, skill, language_spoken, preferred_language, bio, location, joined_date) 
                                       VALUES (?, ?, ?, ?, ?, ?, NOW())";
                
                $travelerParams = [
                    $userId,
                    $userData['skill'] ?? '',
                    $userData['language_spoken'] ?? '',
                    $userData['preferred_language'] ?? '',
                    $userData['bio'] ?? '',
                    $userData['location'] ?? ''
                ];
                
                $travelerUpdateResult = $this->db->insert($travelerInsertQuery, "isssss", $travelerParams);
            }
            
            if (!$travelerUpdateResult) {
                $this->db->getConnection()->rollback();
                $this->db->closeConnection();
                return false;
            }
            
            $this->db->getConnection()->commit();
            $this->db->closeConnection();
            return true;
            
        } catch (\Exception $e) {
            error_log("Error updating user profile: " . $e->getMessage());
            $this->db->getConnection()->rollback();
            $this->db->closeConnection();
            return false;
        }
    }
    
    /**
     * Get traveler statistics
     * 
     * @param int $userId User ID
     * @return array Traveler statistics
     */
    public function getTravelerStatistics($userId) {
        if (!$this->db->openConnection()) {
            return [];
        }
        
        $stats = [];
        
        try {
            // Get number of applications
            $applicationsQuery = "SELECT COUNT(*) as count FROM applications WHERE traveler_id = ?";
            $applicationsResult = $this->db->selectPrepared($applicationsQuery, "i", [$userId]);
            $stats['applications_count'] = $applicationsResult[0]['count'] ?? 0;
            
            // Get number of accepted applications
            $acceptedQuery = "SELECT COUNT(*) as count FROM applications WHERE traveler_id = ? AND status = 'accepted'";
            $acceptedResult = $this->db->selectPrepared($acceptedQuery, "i", [$userId]);
            $stats['accepted_count'] = $acceptedResult[0]['count'] ?? 0;
            
            // Get average rating received
            $ratingQuery = "SELECT AVG(rating) as avg_rating FROM review WHERE receiver_id = ?";
            $ratingResult = $this->db->selectPrepared($ratingQuery, "i", [$userId]);
            $stats['average_rating'] = $ratingResult[0]['avg_rating'] ?? 0;
            
            // Get number of reviews received
            $reviewsQuery = "SELECT COUNT(*) as count FROM review WHERE receiver_id = ?";
            $reviewsResult = $this->db->selectPrepared($reviewsQuery, "i", [$userId]);
            $stats['reviews_count'] = $reviewsResult[0]['count'] ?? 0;
            
            // Get join date
            $joinDateQuery = "SELECT joined_date FROM traveler WHERE traveler_id = ?";
            $joinDateResult = $this->db->selectPrepared($joinDateQuery, "i", [$userId]);
            $stats['joined_date'] = $joinDateResult[0]['joined_date'] ?? null;
            
            $this->db->closeConnection();
            return $stats;
        } catch (\Exception $e) {
            error_log("Error getting traveler statistics: " . $e->getMessage());
            $this->db->closeConnection();
            return [];
        }
    }
    
    /**
     * Get traveler reviews
     * 
     * @param int $userId User ID
     * @return array List of reviews for the traveler
     */
    public function getTravelerReviews($userId) {
        if (!$this->db->openConnection()) {
            return [];
        }
        
        try {
            // Get reviews where traveler is the receiver
            $receivedQuery = "SELECT r.*, 
                             h.first_name as host_first_name, h.last_name as host_last_name, 
                             h.profile_picture as host_profile_picture,
                             o.title as opportunity_title, o.location as opportunity_location
                             FROM review r
                             JOIN users h ON r.sender_id = h.user_id
                             JOIN opportunity o ON r.opportunity_id = o.opportunity_id
                             WHERE r.receiver_id = ?
                             ORDER BY r.created_at DESC";
            
            $receivedResult = $this->db->selectPrepared($receivedQuery, "i", [$userId]);
            
            // Get reviews where traveler is the sender
            $sentQuery = "SELECT r.*, 
                         h.first_name as host_first_name, h.last_name as host_last_name, 
                         h.profile_picture as host_profile_picture,
                         o.title as opportunity_title, o.location as opportunity_location
                         FROM review r
                         JOIN users h ON r.receiver_id = h.user_id
                         JOIN opportunity o ON r.opportunity_id = o.opportunity_id
                         WHERE r.sender_id = ?
                         ORDER BY r.created_at DESC";
            
            $sentResult = $this->db->selectPrepared($sentQuery, "i", [$userId]);
            
            $this->db->closeConnection();
            
            return [
                'received' => $receivedResult ?: [],
                'sent' => $sentResult ?: []
            ];
        } catch (\Exception $e) {
            error_log("Error getting traveler reviews: " . $e->getMessage());
            $this->db->closeConnection();
            return ['received' => [], 'sent' => []];
        }
    }
    
    /**
     * Get traveler applications
     * 
     * @param int $userId User ID
     * @return array List of applications for the traveler
     */
    public function getTravelerApplications($userId) {
        if (!$this->db->openConnection()) {
            return [];
        }
        
        try {
            $query = "SELECT a.*, 
                     o.title as opportunity_title, o.location as opportunity_location, 
                     o.start_date, o.end_date, o.category,
                     h.first_name as host_first_name, h.last_name as host_last_name,
                     h.profile_picture as host_profile_picture
                     FROM applications a
                     JOIN opportunity o ON a.opportunity_id = o.opportunity_id
                     JOIN users h ON o.host_id = h.user_id
                     WHERE a.traveler_id = ?
                     ORDER BY a.applied_date DESC";
            
            $result = $this->db->selectPrepared($query, "i", [$userId]);
            $this->db->closeConnection();
            
            return $result ?: [];
        } catch (\Exception $e) {
            error_log("Error getting traveler applications: " . $e->getMessage());
            $this->db->closeConnection();
            return [];
        }
    }
    
    /**
     * Upload profile picture
     * 
     * @param int $userId User ID
     * @param array $fileData File data from $_FILES
     * @return string|bool New profile picture path if successful, false otherwise
     */
    public function uploadProfilePicture($userId, $fileData) {
        // Check if file was uploaded without errors
        if ($fileData['error'] !== UPLOAD_ERR_OK) {
            return false;
        }
        
        // Define allowed file types and maximum file size
        $allowedTypes = ['image/jpeg', 'image/png', 'image/gif'];
        $maxSize = 5 * 1024 * 1024; // 5MB
        
        // Check file type
        $fileInfo = finfo_open(FILEINFO_MIME_TYPE);
        $fileType = finfo_file($fileInfo, $fileData['tmp_name']);
        finfo_close($fileInfo);
        
        if (!in_array($fileType, $allowedTypes)) {
            return false;
        }
        
        // Check file size
        if ($fileData['size'] > $maxSize) {
            return false;
        }
        
        // Generate a unique filename
        $extension = pathinfo($fileData['name'], PATHINFO_EXTENSION);
        $newFilename = 'profile_' . $userId . '_' . time() . '.' . $extension;
        
        // Define upload directory
        $uploadDir = '../uploads/';
        
        // Create directory if it doesn't exist
        if (!file_exists($uploadDir)) {
            mkdir($uploadDir, 0777, true);
        }
        
        $uploadPath = $uploadDir . $newFilename;
        
        // Move the uploaded file
        if (!move_uploaded_file($fileData['tmp_name'], $uploadPath)) {
            return false;
        }
        
        // Update the user's profile picture in the database
        if (!$this->db->openConnection()) {
            return false;
        }
        
        $query = "UPDATE users SET profile_picture = ? WHERE user_id = ?";
        $result = $this->db->update($query, "si", [$newFilename, $userId]);
        $this->db->closeConnection();
        
        if (!$result) {
            // If database update fails, delete the uploaded file
            unlink($uploadPath);
            return false;
        }
        
        return $newFilename;
    }
    
    /**
     * Update traveler status
     * 
     * @param int $userId User ID
     * @param string $status New status
     * @return bool True if status updated successfully, false otherwise
     */
    public function updateTravelerStatus($userId, $status) {
        if (!$this->db->openConnection()) {
            return false;
        }
        
        try {
            // Update status in traveler table
            $query = "UPDATE traveler SET status = ? WHERE traveler_id = ?";
            $result = $this->db->update($query, "si", [$status, $userId]);
            $this->db->closeConnection();
            
            return $result;
        } catch (\Exception $e) {
            error_log("Error updating traveler status: " . $e->getMessage());
            $this->db->closeConnection();
            return false;
        }
    }
    
    /**
     * Get traveler notifications
     * 
     * @param int $userId User ID
     * @param int $limit Number of notifications to return
     * @param bool $unreadOnly Get only unread notifications
     * @return array Traveler notifications
     */
    public function getTravelerNotifications($userId, $limit = 10, $unreadOnly = false) {
        if (!$this->db->openConnection()) {
            return [];
        }
        
        try {
            $query = "SELECT * FROM notification 
                     WHERE receiver_id = ?";
            
            if ($unreadOnly) {
                $query .= " AND is_read = 0";
            }
            
            $query .= " ORDER BY timestamp DESC 
                       LIMIT ?";
            
            $result = $this->db->selectPrepared($query, "ii", [$userId, $limit]);
            $this->db->closeConnection();
            
            return $result ?: [];
        } catch (\Exception $e) {
            error_log("Error getting traveler notifications: " . $e->getMessage());
            $this->db->closeConnection();
            return [];
        }
    }
    
    /**
     * Mark notification as read
     * 
     * @param int $notificationId Notification ID
     * @return bool True if marked as read successfully, false otherwise
     */
    public function markNotificationAsRead($notificationId) {
        if (!$this->db->openConnection()) {
            return false;
        }
        
        try {
            $query = "UPDATE notification SET is_read = 1 WHERE notification_id = ?";
            $result = $this->db->update($query, "i", [$notificationId]);
            $this->db->closeConnection();
            
            return $result;
        } catch (\Exception $e) {
            error_log("Error marking notification as read: " . $e->getMessage());
            $this->db->closeConnection();
            return false;
        }
    }
    
    /**
     * Mark all notifications as read
     * 
     * @param int $userId User ID
     * @return bool True if all notifications marked as read successfully, false otherwise
     */
    public function markAllNotificationsAsRead($userId) {
        if (!$this->db->openConnection()) {
            return false;
        }
        
        try {
            $query = "UPDATE notification SET is_read = 1 WHERE receiver_id = ? AND is_read = 0";
            $result = $this->db->update($query, "i", [$userId]);
            $this->db->closeConnection();
            
            return $result;
        } catch (\Exception $e) {
            error_log("Error marking all notifications as read: " . $e->getMessage());
            $this->db->closeConnection();
            return false;
        }
    }
    
    /**
     * Get traveler messages
     * 
     * @param int $userId User ID
     * @param int $limit Number of messages to return
     * @return array Traveler messages grouped by conversation
     */
    public function getTravelerMessages($userId, $limit = 10) {
        if (!$this->db->openConnection()) {
            return [];
        }
        
        try {
            // Get the latest message from each conversation
            $query = "SELECT m.*, 
                     CASE 
                        WHEN m.sender_id = ? THEN m.receiver_id 
                        ELSE m.sender_id 
                     END as conversation_with,
                     CASE 
                        WHEN m.sender_id = ? THEN m.receiver_type 
                        ELSE m.sender_type 
                     END as conversation_with_type,
                     u.first_name, u.last_name, u.profile_picture
                     FROM message m
                     JOIN users u ON (
                        CASE 
                            WHEN m.sender_id = ? THEN m.receiver_id 
                            ELSE m.sender_id 
                        END = u.user_id
                     )
                     WHERE (m.sender_id = ? AND m.sender_type = 'traveler') 
                        OR (m.receiver_id = ? AND m.receiver_type = 'traveler')
                     ORDER BY m.timestamp DESC
                     LIMIT ?";
            
            $result = $this->db->selectPrepared(
                $query, 
                "iiiii", 
                [$userId, $userId, $userId, $userId, $userId, $limit]
            );
            
            $this->db->closeConnection();
            
            // Group messages by conversation
            $conversations = [];
            if ($result) {
                foreach ($result as $message) {
                    $conversationKey = $message['conversation_with'] . '_' . $message['conversation_with_type'];
                    if (!isset($conversations[$conversationKey])) {
                        $conversations[$conversationKey] = [
                            'user_id' => $message['conversation_with'],
                            'user_type' => $message['conversation_with_type'],
                            'first_name' => $message['first_name'],
                            'last_name' => $message['last_name'],
                            'profile_picture' => $message['profile_picture'],
                            'last_message' => $message['content'],
                            'last_message_time' => $message['timestamp'],
                            'unread_count' => 0
                        ];
                    }
                }
                
                // Get unread count for each conversation
                foreach ($conversations as $key => &$conversation) {
                    $unreadQuery = "SELECT COUNT(*) as count 
                                   FROM message 
                                   WHERE receiver_id = ? 
                                     AND receiver_type = 'traveler' 
                                     AND sender_id = ? 
                                     AND sender_type = ? 
                                     AND is_read = 0";
                    $unreadResult = $this->db->selectPrepared(
                        $unreadQuery, 
                        "iis", 
                        [$userId, $conversation['user_id'], $conversation['user_type']]
                    );
                    $conversation['unread_count'] = $unreadResult[0]['count'] ?? 0;
                }
            }
            
            return array_values($conversations);
        } catch (\Exception $e) {
            error_log("Error getting traveler messages: " . $e->getMessage());
            $this->db->closeConnection();
            return [];
        }
    }
    
    /**
     * Get conversation messages
     * 
     * @param int $userId User ID
     * @param int $otherUserId Other user ID
     * @param string $otherUserType Other user type (host, admin)
     * @param int $limit Number of messages to return
     * @return array Conversation messages
     */
    public function getConversationMessages($userId, $otherUserId, $otherUserType, $limit = 50) {
        if (!$this->db->openConnection()) {
            return [];
        }
        
        try {
            $query = "SELECT m.*, 
                     u_sender.first_name as sender_first_name, 
                     u_sender.last_name as sender_last_name,
                     u_sender.profile_picture as sender_profile_picture
                     FROM message m
                     JOIN users u_sender ON m.sender_id = u_sender.user_id
                     WHERE (m.sender_id = ? AND m.sender_type = 'traveler' AND m.receiver_id = ? AND m.receiver_type = ?)
                        OR (m.sender_id = ? AND m.sender_type = ? AND m.receiver_id = ? AND m.receiver_type = 'traveler')
                     ORDER BY m.timestamp ASC
                     LIMIT ?";
            
            $result = $this->db->selectPrepared(
                $query, 
                "iisisi", 
                [
                    $userId, $otherUserId, $otherUserType,
                    $otherUserId, $otherUserType, $userId,
                    $limit
                ]
            );
            
            // Mark messages as read
            $this->markMessagesAsRead($userId, $otherUserId, $otherUserType);
            
            $this->db->closeConnection();
            
            return $result ?: [];
        } catch (\Exception $e) {
            error_log("Error getting conversation messages: " . $e->getMessage());
            $this->db->closeConnection();
            return [];
        }
    }
    
    /**
     * Mark messages as read
     * 
     * @param int $receiverId Receiver user ID
     * @param int $senderId Sender user ID
     * @param string $senderType Sender user type
     * @return bool True if messages marked as read successfully, false otherwise
     */
    public function markMessagesAsRead($receiverId, $senderId, $senderType) {
        if (!$this->db->openConnection()) {
            return false;
        }
        
        try {
            $query = "UPDATE message SET is_read = 1 
                      WHERE receiver_id = ? 
                        AND receiver_type = 'traveler' 
                        AND sender_id = ? 
                        AND sender_type = ?";
            $result = $this->db->update($query, "iis", [$receiverId, $senderId, $senderType]);
            $this->db->closeConnection();
            
            return $result;
        } catch (\Exception $e) {
            error_log("Error marking messages as read: " . $e->getMessage());
            $this->db->closeConnection();
            return false;
        }
    }
}
?>






