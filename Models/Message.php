<?php
namespace Models;
require_once '../Models/Database.php';

class Message {
    private $db;
    
    public function __construct() {
        $this->db = new \Database();
    }
    
    /**
     * Send a message
     * 
     * @param array $messageData Message data including sender_id, receiver_id, content, etc.
     * @return bool True if message was sent successfully, false otherwise
     */
    public function sendMessage(array $messageData): bool {
        if (!$this->db->openConnection()) {
            error_log("Failed to establish database connection in sendMessage");
            return false;
        }
        
        $sql = "INSERT INTO message (sender_id, receiver_id, content, timestamp, status, is_read, sender_type, receiver_type) 
                VALUES (?, ?, ?, NOW(), ?, ?, ?, ?)";
        
        $params = [
            $messageData['sender_id'],
            $messageData['receiver_id'],
            $messageData['content'],
            $messageData['status'] ?? 'delivered',
            $messageData['is_read'] ?? 0,
            $messageData['sender_type'],
            $messageData['receiver_type']
        ];
        
        $result = $this->db->insert($sql, "iississ", $params);
        $this->db->closeConnection();
        
        return $result ? true : false;
    }
    
    /**
     * Get conversation between two users
     * 
     * @param int $userId1 First user ID
     * @param int $userId2 Second user ID
     * @param string $userType1 First user type
     * @param string $userType2 Second user type
     * @return array Array of messages
     */
    public function getConversation(int $userId1, int $userId2, string $userType1, string $userType2): array {
        if (!$this->db->openConnection()) {
            error_log("Failed to establish database connection in getConversation");
            return [];
        }
        
        $sql = "SELECT * FROM message 
                WHERE (sender_id = ? AND receiver_id = ? AND sender_type = ? AND receiver_type = ?) 
                OR (sender_id = ? AND receiver_id = ? AND sender_type = ? AND receiver_type = ?) 
                ORDER BY timestamp ASC";
        
        $params = [
            $userId1, $userId2, $userType1, $userType2,
            $userId2, $userId1, $userType2, $userType1
        ];
        
        $result = $this->db->selectPrepared($sql, "iissiiss", $params);
        $this->db->closeConnection();
        
        return $result ?: [];
    }
    
    /**
     * Mark messages as read
     * 
     * @param int $receiverId Receiver ID
     * @param int $senderId Sender ID
     * @param string $receiverType Receiver type
     * @param string $senderType Sender type
     * @return bool True if messages were marked as read, false otherwise
     */
    public function markMessagesAsRead(int $receiverId, int $senderId, string $receiverType, string $senderType): bool {
        if (!$this->db->openConnection()) {
            error_log("Failed to establish database connection in markMessagesAsRead");
            return false;
        }
        
        $sql = "UPDATE message 
                SET is_read = 1 
                WHERE receiver_id = ? AND sender_id = ? 
                AND receiver_type = ? AND sender_type = ? 
                AND is_read = 0";
        
        $params = [$receiverId, $senderId, $receiverType, $senderType];
        
        $result = $this->db->update($sql, "iiss", $params);
        $this->db->closeConnection();
        
        return $result ? true : false;
    }
    
    /**
     * Get recent conversations for a user
     * 
     * @param int $userId User ID
     * @param string $userType User type
     * @return array Array of recent conversations
     */
    public function getRecentConversations(int $userId, string $userType): array {
        if (!$this->db->openConnection()) {
            error_log("Failed to establish database connection in getRecentConversations");
            return [];
        }
        
        $sql = "SELECT m.*, 
                u.first_name, u.last_name, u.profile_picture,
                (SELECT COUNT(*) FROM message 
                 WHERE ((sender_id = m.sender_id AND receiver_id = m.receiver_id) 
                       OR (sender_id = m.receiver_id AND receiver_id = m.sender_id))
                       AND is_read = 0 AND receiver_id = ? AND receiver_type = ?) as unread_count
                FROM message m
                JOIN users u ON (m.sender_id = u.user_id AND m.sender_id != ? AND m.sender_type != ?)
                              OR (m.receiver_id = u.user_id AND m.receiver_id != ? AND m.receiver_type != ?)
                WHERE (m.sender_id = ? AND m.sender_type = ?) 
                   OR (m.receiver_id = ? AND m.receiver_type = ?)
                GROUP BY IF(m.sender_id = ?, m.receiver_id, m.sender_id)
                ORDER BY m.timestamp DESC";
        
        $params = [
            $userId, $userType, 
            $userId, $userType,
            $userId, $userType, 
            $userId, $userType,
            $userId, $userType,
            $userId
        ];
        
        $result = $this->db->selectPrepared($sql, "isisissisii", $params);
        $this->db->closeConnection();
        
        return $result ?: [];
    }
    
    /**
     * Get unread message count for a user
     * 
     * @param int $userId User ID
     * @param string $userType User type
     * @return int Number of unread messages
     */
    public function getUnreadMessageCount(int $userId, string $userType): int {
        if (!$this->db->openConnection()) {
            error_log("Failed to establish database connection in getUnreadMessageCount");
            return 0;
        }
        
        $sql = "SELECT COUNT(*) as count 
                FROM message 
                WHERE receiver_id = ? AND receiver_type = ? AND is_read = 0";
        
        $params = [$userId, $userType];
        
        $result = $this->db->selectPrepared($sql, "is", $params);
        $this->db->closeConnection();
        
        return $result ? (int)$result[0]['count'] : 0;
    }
    
    /**
     * Delete a message
     * 
     * @param int $messageId Message ID
     * @return bool True if message was deleted, false otherwise
     */
    public function deleteMessage(int $messageId): bool {
        if (!$this->db->openConnection()) {
            error_log("Failed to establish database connection in deleteMessage");
            return false;
        }
        
        $sql = "DELETE FROM message WHERE message_id = ?";
        
        $result = $this->db->delete($sql, "i", [$messageId]);
        $this->db->closeConnection();
        
        return $result ? true : false;
    }
    
    /**
     * Delete a conversation
     * 
     * @param int $userId1 First user ID
     * @param int $userId2 Second user ID
     * @param string $userType1 First user type
     * @param string $userType2 Second user type
     * @return bool True if conversation was deleted, false otherwise
     */
    public function deleteConversation(int $userId1, int $userId2, string $userType1, string $userType2): bool {
        if (!$this->db->openConnection()) {
            error_log("Failed to establish database connection in deleteConversation");
            return false;
        }
        
        $sql = "DELETE FROM message 
                WHERE (sender_id = ? AND receiver_id = ? AND sender_type = ? AND receiver_type = ?) 
                OR (sender_id = ? AND receiver_id = ? AND sender_type = ? AND receiver_type = ?)";
        
        $params = [
            $userId1, $userId2, $userType1, $userType2,
            $userId2, $userId1, $userType2, $userType1
        ];
        
        $result = $this->db->delete($sql, "iissiiss", $params);
        $this->db->closeConnection();
        
        return $result ? true : false;
    }
}
?>

