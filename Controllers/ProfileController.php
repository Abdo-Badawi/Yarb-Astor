<?php
// Include necessary files
require_once 'DBController.php';
require_once '../Models/User.php';
require_once '../Models/Traveler.php';
require_once '../Models/Host.php';
use Models\User;
use Models\Traveler;
use Models\Host;

class ProfileController {
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
            // First, get the user type to determine which model to use
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
            } elseif ($userType === 'host') {
                $query = "SELECT u.*, h.* 
                          FROM users u
                          LEFT JOIN hosts h ON u.user_id = h.host_id
                          WHERE u.user_id = ?";
            } else {
                // For admin or other user types
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
            // Start transaction
            $this->db->getConnection()->begin_transaction();
            
            // First, get the user type
            $userTypeQuery = "SELECT user_type FROM users WHERE user_id = ?";
            $userTypeResult = $this->db->selectPrepared($userTypeQuery, "i", [$userId]);
            
            if (!$userTypeResult || count($userTypeResult) === 0) {
                $this->db->getConnection()->rollback();
                $this->db->closeConnection();
                return false;
            }
            
            $userType = $userTypeResult[0]['user_type'];
            
            // Update basic user information in the users table
            $userUpdateQuery = "UPDATE users SET 
                                first_name = ?, 
                                last_name = ?, 
                                email = ?, 
                                phone_number = ?";
            
            $userParams = [
                $userData['first_name'],
                $userData['last_name'],
                $userData['email'],
                $userData['phone_number'] ?? null
            ];
            
            $userTypes = "ssss";
            
            // Add profile picture to update if provided
            if (isset($userData['profile_picture']) && !empty($userData['profile_picture'])) {
                $userUpdateQuery .= ", profile_picture = ?";
                $userParams[] = $userData['profile_picture'];
                $userTypes .= "s";
            }
            
            // Add date of birth to update if provided
            if (isset($userData['date_of_birth']) && !empty($userData['date_of_birth'])) {
                $userUpdateQuery .= ", date_of_birth = ?";
                $userParams[] = $userData['date_of_birth'];
                $userTypes .= "s";
            }
            
            // Add gender to update if provided
            if (isset($userData['gender']) && !empty($userData['gender'])) {
                $userUpdateQuery .= ", gender = ?";
                $userParams[] = $userData['gender'];
                $userTypes .= "s";
            }
            
            // Add national ID to update if provided
            if (isset($userData['national_id']) && !empty($userData['national_id'])) {
                $userUpdateQuery .= ", national_id = ?";
                $userParams[] = $userData['national_id'];
                $userTypes .= "s";
            }
            
            // Finish the query
            $userUpdateQuery .= " WHERE user_id = ?";
            $userParams[] = $userId;
            $userTypes .= "i";
            
            // Execute the user update query
            $userUpdateResult = $this->db->update($userUpdateQuery, $userTypes, $userParams);
            
            if (!$userUpdateResult) {
                $this->db->getConnection()->rollback();
                $this->db->closeConnection();
                return false;
            }
            
            // Update user type specific information
            if ($userType === 'traveler') {
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
            } elseif ($userType === 'host') {
                // Check if host record exists
                $checkQuery = "SELECT COUNT(*) as count FROM hosts WHERE host_id = ?";
                $checkResult = $this->db->selectPrepared($checkQuery, "i", [$userId]);
                
                if ($checkResult[0]['count'] > 0) {
                    // Update existing host record
                    $hostUpdateQuery = "UPDATE hosts SET 
                                       property_type = ?, 
                                       preferred_language = ?, 
                                       bio = ?, 
                                       location = ?";
                    
                    $hostParams = [
                        $userData['property_type'] ?? '',
                        $userData['preferred_language'] ?? '',
                        $userData['bio'] ?? '',
                        $userData['location'] ?? ''
                    ];
                    
                    $hostTypes = "ssss";
                    
                    // Add rate to update if provided
                    if (isset($userData['rate']) && !empty($userData['rate'])) {
                        $hostUpdateQuery .= ", rate = ?";
                        $hostParams[] = $userData['rate'];
                        $hostTypes .= "d";
                    }
                    
                    // Add status to update if provided
                    if (isset($userData['status']) && !empty($userData['status'])) {
                        $hostUpdateQuery .= ", status = ?";
                        $hostParams[] = $userData['status'];
                        $hostTypes .= "s";
                    }
                    
                    // Finish the query
                    $hostUpdateQuery .= " WHERE host_id = ?";
                    $hostParams[] = $userId;
                    $hostTypes .= "i";
                    
                    $hostUpdateResult = $this->db->update($hostUpdateQuery, $hostTypes, $hostParams);
                } else {
                    // Insert new host record
                    $hostInsertQuery = "INSERT INTO hosts 
                                       (host_id, property_type, preferred_language, bio, location, rate, status, joined_date) 
                                       VALUES (?, ?, ?, ?, ?, ?, ?, NOW())";
                    
                    $hostParams = [
                        $userId,
                        $userData['property_type'] ?? '',
                        $userData['preferred_language'] ?? '',
                        $userData['bio'] ?? '',
                        $userData['location'] ?? '',
                        $userData['rate'] ?? 0.0,
                        $userData['status'] ?? 'active'
                    ];
                    
                    $hostUpdateResult = $this->db->insert($hostInsertQuery, "issssds", $hostParams);
                }
                
                if (!$hostUpdateResult) {
                    $this->db->getConnection()->rollback();
                    $this->db->closeConnection();
                    return false;
                }
            }
            
            // Commit the transaction
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
     * Get user profile picture
     * 
     * @param int $userId User ID
     * @return string|null Profile picture path if found, null otherwise
     */
    public function getUserProfilePicture($userId) {
        if (!$this->db->openConnection()) {
            return null;
        }
        
        $query = "SELECT profile_picture FROM users WHERE user_id = ?";
        $result = $this->db->selectPrepared($query, "i", [$userId]);
        $this->db->closeConnection();
        
        if (!$result || count($result) === 0 || empty($result[0]['profile_picture'])) {
            return null;
        }
        
        return $result[0]['profile_picture'];
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
     * Get user reviews
     * 
     * @param int $userId User ID
     * @return array List of reviews for the user
     */
    public function getUserReviews($userId) {
        if (!$this->db->openConnection()) {
            return [];
        }
        
        // First, get the user type
        $userTypeQuery = "SELECT user_type FROM users WHERE user_id = ?";
        $userTypeResult = $this->db->selectPrepared($userTypeQuery, "i", [$userId]);
        
        if (!$userTypeResult || count($userTypeResult) === 0) {
            $this->db->closeConnection();
            return [];
        }
        
        $userType = $userTypeResult[0]['user_type'];
        
        // Get reviews based on user type
        if ($userType === 'traveler') {
            $query = "SELECT r.*, 
                      h.first_name as host_first_name, h.last_name as host_last_name, 
                      h.profile_picture as host_profile_picture,
                      o.title as opportunity_title, o.location as opportunity_location
                      FROM review r
                      JOIN users h ON r.receiver_id = h.user_id
                      JOIN opportunity o ON r.opportunity_id = o.opportunity_id
                      WHERE r.sender_id = ?
                      ORDER BY r.created_at DESC";
        } elseif ($userType === 'host') {
            $query = "SELECT r.*, 
                      t.first_name as traveler_first_name, t.last_name as traveler_last_name, 
                      t.profile_picture as traveler_profile_picture,
                      o.title as opportunity_title, o.location as opportunity_location
                      FROM review r
                      JOIN users t ON r.sender_id = t.user_id
                      JOIN opportunity o ON r.opportunity_id = o.opportunity_id
                      WHERE r.receiver_id = ?
                      ORDER BY r.created_at DESC";
        } else {
            $this->db->closeConnection();
            return [];
        }
        
        $result = $this->db->selectPrepared($query, "i", [$userId]);
        $this->db->closeConnection();
        
        return $result ?: [];
    }
    
    /**
     * Get user opportunities
     * 
     * @param int $userId User ID
     * @return array List of opportunities for the user
     */
    public function getUserOpportunities($userId) {
        if (!$this->db->openConnection()) {
            return [];
        }
        
        // First, get the user type
        $userTypeQuery = "SELECT user_type FROM users WHERE user_id = ?";
        $userTypeResult = $this->db->selectPrepared($userTypeQuery, "i", [$userId]);
        
        if (!$userTypeResult || count($userTypeResult) === 0) {
            $this->db->closeConnection();
            return [];
        }
        
        $userType = $userTypeResult[0]['user_type'];
        
        // Get opportunities based on user type
        if ($userType === 'traveler') {
            // Get opportunities the traveler has applied for
            $query = "SELECT o.*, a.status as application_status, 
                      h.first_name as host_first_name, h.last_name as host_last_name
                      FROM opportunity o
                      JOIN application a ON o.opportunity_id = a.opportunity_id
                      JOIN users h ON o.host_id = h.user_id
                      WHERE a.traveler_id = ?
                      ORDER BY a.created_at DESC";
        } elseif ($userType === 'host') {
            // Get opportunities created by the host
            $query = "SELECT o.*, 
                      (SELECT COUNT(*) FROM application WHERE opportunity_id = o.opportunity_id) as application_count
                      FROM opportunity o
                      WHERE o.host_id = ?
                      ORDER BY o.created_at DESC";
        } else {
            $this->db->closeConnection();
            return [];
        }
        
        $result = $this->db->selectPrepared($query, "i", [$userId]);
        $this->db->closeConnection();
        
        return $result ?: [];
    }
    
    /**
     * Get user statistics
     * 
     * @param int $userId User ID
     * @return array User statistics
     */
    public function getUserStatistics($userId) {
        if (!$this->db->openConnection()) {
            return [];
        }
        
        // First, get the user type
        $userTypeQuery = "SELECT user_type FROM users WHERE user_id = ?";
        $userTypeResult = $this->db->selectPrepared($userTypeQuery, "i", [$userId]);
        
        if (!$userTypeResult || count($userTypeResult) === 0) {
            $this->db->closeConnection();
            return [];
        }
        
        $userType = $userTypeResult[0]['user_type'];
        $stats = [];
        
        // Get statistics based on user type
        if ($userType === 'traveler') {
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
            
        } elseif ($userType === 'host') {
            // Get number of opportunities
            $opportunitiesQuery = "SELECT COUNT(*) as count FROM opportunity WHERE host_id = ?";
            $opportunitiesResult = $this->db->selectPrepared($opportunitiesQuery, "i", [$userId]);
            $stats['opportunities_count'] = $opportunitiesResult[0]['count'] ?? 0;
            
            // Get number of active opportunities
            $activeQuery = "SELECT COUNT(*) as count FROM opportunity WHERE host_id = ? AND status = 'open'";
            $activeResult = $this->db->selectPrepared($activeQuery, "i", [$userId]);
            $stats['active_opportunities'] = $activeResult[0]['count'] ?? 0;
            
            // Get number of applications received
            $applicationsQuery = "SELECT COUNT(*) as count FROM applications a 
                                 JOIN opportunity o ON a.opportunity_id = o.opportunity_id 
                                 WHERE o.host_id = ?";
            $applicationsResult = $this->db->selectPrepared($applicationsQuery, "i", [$userId]);
            $stats['applications_received'] = $applicationsResult[0]['count'] ?? 0;
            
            // Get average rating received
            $ratingQuery = "SELECT AVG(rating) as avg_rating FROM review WHERE receiver_id = ?";
            $ratingResult = $this->db->selectPrepared($ratingQuery, "i", [$userId]);
            $stats['average_rating'] = $ratingResult[0]['avg_rating'] ?? 0;
            
            // Get number of reviews received
            $reviewsQuery = "SELECT COUNT(*) as count FROM review WHERE receiver_id = ?";
            $reviewsResult = $this->db->selectPrepared($reviewsQuery, "i", [$userId]);
            $stats['reviews_count'] = $reviewsResult[0]['count'] ?? 0;
            
            // Get join date
            $joinDateQuery = "SELECT joined_date FROM hosts WHERE host_id = ?";
            $joinDateResult = $this->db->selectPrepared($joinDateQuery, "i", [$userId]);
            $stats['joined_date'] = $joinDateResult[0]['joined_date'] ?? null;
        }
        
        $this->db->closeConnection();
        return $stats;
    }
    
    /**
     * Add user activity log
     * 
     * @param int $userId User ID
     * @param string $activity Activity description
     * @param string $activityType Type of activity
     * @return bool True if log added successfully, false otherwise
     */
    public function addUserActivityLog($userId, $activity, $activityType = 'general') {
        if (!$this->db->openConnection()) {
            return false;
        }
        
        $query = "INSERT INTO user_activity_log (user_id, activity, activity_type, created_at) 
                  VALUES (?, ?, ?, NOW())";
        $result = $this->db->insert($query, "iss", [$userId, $activity, $activityType]);
        $this->db->closeConnection();
        
        return $result;
    }
    
    /**
     * Get user activity log
     * 
     * @param int $userId User ID
     * @param int $limit Number of activities to return
     * @return array User activity log
     */
    public function getUserActivityLog($userId, $limit = 10) {
        if (!$this->db->openConnection()) {
            return [];
        }
        
        $query = "SELECT * FROM user_activity_log 
                  WHERE user_id = ? 
                  ORDER BY created_at DESC 
                  LIMIT ?";
        $result = $this->db->selectPrepared($query, "ii", [$userId, $limit]);
        $this->db->closeConnection();
        
        return $result ?: [];
    }
    
    /**
     * Update user status
     * 
     * @param int $userId User ID
     * @param string $status New status
     * @return bool True if status updated successfully, false otherwise
     */
    public function updateUserStatus($userId, $status) {
        if (!$this->db->openConnection()) {
            return false;
        }
        
        // First, get the user type
        $userTypeQuery = "SELECT user_type FROM users WHERE user_id = ?";
        $userTypeResult = $this->db->selectPrepared($userTypeQuery, "i", [$userId]);
        
        if (!$userTypeResult || count($userTypeResult) === 0) {
            $this->db->closeConnection();
            return false;
        }
        
        $userType = $userTypeResult[0]['user_type'];
        
        // Update status based on user type
        if ($userType === 'traveler') {
            $query = "UPDATE traveler SET status = ? WHERE traveler_id = ?";
        } elseif ($userType === 'host') {
            $query = "UPDATE hosts SET status = ? WHERE host_id = ?";
        } else {
            // For admin or other user types, update the users table
            $query = "UPDATE users SET status = ? WHERE user_id = ?";
        }
        
        $result = $this->db->update($query, "si", [$status, $userId]);
        $this->db->closeConnection();
        
        return $result;
    }
    
    /**
     * Get user notifications
     * 
     * @param int $userId User ID
     * @param int $limit Number of notifications to return
     * @param bool $unreadOnly Get only unread notifications
     * @return array User notifications
     */
    public function getUserNotifications($userId, $limit = 10, $unreadOnly = false) {
        if (!$this->db->openConnection()) {
            return [];
        }
        
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
        
        $query = "UPDATE notification SET is_read = 1 WHERE notification_id = ?";
        $result = $this->db->update($query, "i", [$notificationId]);
        $this->db->closeConnection();
        
        return $result;
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
        
        $query = "UPDATE notification SET is_read = 1 WHERE receiver_id = ? AND is_read = 0";
        $result = $this->db->update($query, "i", [$userId]);
        $this->db->closeConnection();
        
        return $result;
    }
    
    /**
     * Get unread notification count
     * 
     * @param int $userId User ID
     * @return int Number of unread notifications
     */
    public function getUnreadNotificationCount($userId) {
        if (!$this->db->openConnection()) {
            return 0;
        }
        
        $query = "SELECT COUNT(*) as count FROM notification 
                  WHERE receiver_id = ? AND is_read = 0";
        $result = $this->db->selectPrepared($query, "i", [$userId]);
        $this->db->closeConnection();
        
        return $result[0]['count'] ?? 0;
    }
    
    /**
     * Add notification
     * 
     * @param int $receiverId Receiver user ID
     * @param string $content Notification content
     * @param string $type Notification type
     * @return bool True if notification added successfully, false otherwise
     */
    public function addNotification($receiverId, $content, $type = 'general') {
        if (!$this->db->openConnection()) {
            return false;
        }
        
        $query = "INSERT INTO notification (receiver_id, content, type, timestamp, is_read) 
                  VALUES (?, ?, ?, NOW(), 0)";
        $result = $this->db->insert($query, "iss", [$receiverId, $content, $type]);
        $this->db->closeConnection();
        
        return $result;
    }
    
    /**
     * Get user messages
     * 
     * @param int $userId User ID
     * @param string $userType User type (traveler, host, admin)
     * @param int $limit Number of messages to return
     * @return array User messages grouped by conversation
     */
    public function getUserMessages($userId, $userType, $limit = 10) {
        if (!$this->db->openConnection()) {
            return [];
        }
        
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
                 WHERE (m.sender_id = ? AND m.sender_type = ?) 
                    OR (m.receiver_id = ? AND m.receiver_type = ?)
                 ORDER BY m.timestamp DESC
                 LIMIT ?";
        
        $result = $this->db->selectPrepared(
            $query, 
            "iiiissi", 
            [$userId, $userId, $userId, $userId, $userType, $userId, $userType, $limit]
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
                                 AND receiver_type = ? 
                                 AND sender_id = ? 
                                 AND sender_type = ? 
                                 AND is_read = 0";
                $unreadResult = $this->db->selectPrepared(
                    $unreadQuery, 
                    "isis", 
                    [$userId, $userType, $conversation['user_id'], $conversation['user_type']]
                );
                $conversation['unread_count'] = $unreadResult[0]['count'] ?? 0;
            }
        }
        
        return array_values($conversations);
    }
    
    /**
     * Get conversation messages
     * 
     * @param int $userId User ID
     * @param string $userType User type (traveler, host, admin)
     * @param int $otherUserId Other user ID
     * @param string $otherUserType Other user type (traveler, host, admin)
     * @param int $limit Number of messages to return
     * @return array Conversation messages
     */
    public function getConversationMessages($userId, $userType, $otherUserId, $otherUserType, $limit = 50) {
        if (!$this->db->openConnection()) {
            return [];
        }
        
        $query = "SELECT m.*, 
                 u_sender.first_name as sender_first_name, 
                 u_sender.last_name as sender_last_name,
                 u_sender.profile_picture as sender_profile_picture
                 FROM message m
                 JOIN users u_sender ON m.sender_id = u_sender.user_id
                 WHERE (m.sender_id = ? AND m.sender_type = ? AND m.receiver_id = ? AND m.receiver_type = ?)
                    OR (m.sender_id = ? AND m.sender_type = ? AND m.receiver_id = ? AND m.receiver_type = ?)
                 ORDER BY m.timestamp ASC
                 LIMIT ?";
        
        $result = $this->db->selectPrepared(
            $query, 
            "isisisis", 
            [
                $userId, $userType, $otherUserId, $otherUserType,
                $otherUserId, $otherUserType, $userId, $userType,
                $limit
            ]
        );
        
        // Mark messages as read
        $this->markMessagesAsRead($userId, $userType, $otherUserId, $otherUserType);
        
        $this->db->closeConnection();
        
        return $result ?: [];
    }
    
    /**
     * Mark messages as read
     * 
     * @param int $receiverId Receiver user ID
     * @param string $receiverType Receiver user type
     * @param int $senderId Sender user ID
     * @param string $senderType Sender user type
     * @return bool True if messages marked as read successfully, false otherwise
     */
    public function markMessagesAsRead($receiverId, $receiverType, $senderId, $senderType) {
        if (!$this->db->openConnection()) {
            return false;
        }
        
        $query = "UPDATE message 
                 SET is_read = 1 
                 WHERE receiver_id = ? 
                   AND receiver_type = ? 
                   AND sender_id = ? 
                   AND sender_type = ? 
                   AND is_read = 0";
        
        $result = $this->db->update($query, "isis", [$receiverId, $receiverType, $senderId, $senderType]);
        $this->db->closeConnection();
        
        return $result;
    }
    
    /**
     * Send message
     * 
     * @param int $senderId Sender user ID
     * @param string $senderType Sender user type
     * @param int $receiverId Receiver user ID
     * @param string $receiverType Receiver user type
     * @param string $content Message content
     * @return bool True if message sent successfully, false otherwise
     */
    public function sendMessage($senderId, $senderType, $receiverId, $receiverType, $content) {
        if (!$this->db->openConnection()) {
            return false;
        }
        
        $query = "INSERT INTO message 
                 (sender_id, sender_type, receiver_id, receiver_type, content, timestamp, is_read) 
                 VALUES (?, ?, ?, ?, ?, NOW(), 0)";
        
        $result = $this->db->insert($query, "isiss", [$senderId, $senderType, $receiverId, $receiverType, $content]);
        $this->db->closeConnection();
        
        return $result;
    }
}
?>

