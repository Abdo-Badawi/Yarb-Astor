<?php
require_once __DIR__ . '/../Controllers/DBController.php';

class Message {
    private $db;
    
    public function __construct() {
        $this->db = new DBController();
    }
    
    // Create a new message
    public function createMessage(array $messageData): bool {
        $sql = "INSERT INTO message (sender_id, receiver_id, content, timestamp, status, is_read, sender_type, receiver_type) 
                VALUES (?, ?, ?, NOW(), ?, ?, ?, ?)";
        
        $params = [
            $messageData['sender_id'],
            $messageData['receiver_id'],
            $messageData['message'],
            $messageData['status'] ?? 'delivered',
            $messageData['is_read'] ?? 0,
            $messageData['sender_type'],
            $messageData['receiver_type']
        ];
        
        $this->db->openConnection();
        $result = $this->db->insert($sql, "iississ", $params);
        $this->db->closeConnection();
        
        return $result;
    }
    
    // Get conversation between two users
    public function getConversation(int $userId1, int $userId2, string $userType1, string $userType2): array {
        $sql = "SELECT * FROM message 
                WHERE (sender_id = ? AND receiver_id = ? AND sender_type = ? AND receiver_type = ?) 
                OR (sender_id = ? AND receiver_id = ? AND sender_type = ? AND receiver_type = ?) 
                ORDER BY timestamp ASC";
        
        $params = [
            $userId1, $userId2, $userType1, $userType2,
            $userId2, $userId1, $userType2, $userType1
        ];
        
        $this->db->openConnection();
        $result = $this->db->selectPrepared($sql, "iissiiss", $params);
        $this->db->closeConnection();
        
        return $result ?: [];
    }
    
    // Mark messages as read
    public function markAsRead(int $receiverId, int $senderId, string $receiverType, string $senderType): bool {
        $sql = "UPDATE message 
                SET is_read = 1 
                WHERE receiver_id = ? AND sender_id = ? 
                AND receiver_type = ? AND sender_type = ? 
                AND is_read = 0";
        
        $params = [$receiverId, $senderId, $receiverType, $senderType];
        
        $this->db->openConnection();
        $result = $this->db->update($sql, "iiss", $params);
        $this->db->closeConnection();
        
        return $result;
    }
    
    // Get recent conversations for a user
    public function getRecentConversations(int $userId, string $userType): array {
        $sql = "SELECT m.* 
                FROM message m
                INNER JOIN (
                    SELECT 
                        CASE 
                            WHEN sender_id = ? AND sender_type = ? THEN receiver_id 
                            ELSE sender_id 
                        END AS other_user_id,
                        CASE 
                            WHEN sender_id = ? AND sender_type = ? THEN receiver_type 
                            ELSE sender_type 
                        END AS other_user_type,
                        MAX(timestamp) as latest_time
                    FROM message
                    WHERE (sender_id = ? AND sender_type = ?) OR (receiver_id = ? AND receiver_type = ?)
                    GROUP BY other_user_id, other_user_type
                ) latest ON (
                    (m.sender_id = latest.other_user_id AND m.receiver_id = ? AND m.timestamp = latest.latest_time)
                    OR 
                    (m.receiver_id = latest.other_user_id AND m.sender_id = ? AND m.timestamp = latest.latest_time)
                )
                ORDER BY m.timestamp DESC";
        
        $params = [
            $userId, $userType, 
            $userId, $userType,
            $userId, $userType, $userId, $userType,
            $userId, $userId
        ];
        
        $this->db->openConnection();
        $result = $this->db->selectPrepared($sql, "isisisiiii", $params);
        $this->db->closeConnection();
        
        return $result ?: [];
    }
    
    // Get unread message count for a user
    public function getUnreadCount(int $userId, string $userType): int {
        $sql = "SELECT COUNT(*) as count 
                FROM message 
                WHERE receiver_id = ? AND receiver_type = ? AND is_read = 0";
        
        $params = [$userId, $userType];
        
        $this->db->openConnection();
        $result = $this->db->selectPrepared($sql, "is", $params);
        $this->db->closeConnection();
        
        return $result ? (int)$result[0]['count'] : 0;
    }
    
    // Delete a message
    public function deleteMessage(int $messageId): bool {
        $sql = "DELETE FROM message WHERE message_id = ?";
        
        $this->db->openConnection();
        $result = $this->db->delete($sql, "i", [$messageId]);
        $this->db->closeConnection();
        
        return $result;
    }
    
    // Delete a conversation
    public function deleteConversation(int $userId1, int $userId2, string $userType1, string $userType2): bool {
        $sql = "DELETE FROM message 
                WHERE (sender_id = ? AND receiver_id = ? AND sender_type = ? AND receiver_type = ?) 
                OR (sender_id = ? AND receiver_id = ? AND sender_type = ? AND receiver_type = ?)";
        
        $params = [
            $userId1, $userId2, $userType1, $userType2,
            $userId2, $userId1, $userType2, $userType1
        ];
        
        $this->db->openConnection();
        $result = $this->db->delete($sql, "iissiiss", $params);
        $this->db->closeConnection();
        
        return $result;
    }
}
?>
