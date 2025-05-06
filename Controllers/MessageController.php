<?php
require_once 'DBController.php';

class MessageController {
    private $db;
    
    public function __construct() {
        $this->db = new DBController();
    }
    
    // Function to send a message
    public function sendMessage(array $messageData): bool {
        $sql = "INSERT INTO messages (sender_id, receiver_id, message, sent_date, is_read, sender_type, receiver_type) 
                VALUES (?, ?, ?, ?, ?, ?, ?)";
        
        $params = [
            $messageData['sender_id'],
            $messageData['receiver_id'],
            $messageData['message'],
            $messageData['sent_date'],
            $messageData['is_read'],
            $messageData['sender_type'],
            $messageData['receiver_type']
        ];
        
        $this->db->openConnection();
        $result = $this->db->insert($sql, "iississ", $params);
        $this->db->closeConnection();
        
        return $result;
    }
    
    // Function to get conversation between two users
    public function getConversation(int $userId1, int $userId2, string $userType1, string $userType2): array {
        $sql = "SELECT * FROM messages 
                WHERE (sender_id = ? AND receiver_id = ? AND sender_type = ? AND receiver_type = ?) 
                OR (sender_id = ? AND receiver_id = ? AND sender_type = ? AND receiver_type = ?) 
                ORDER BY sent_date ASC";
        
        $params = [
            $userId1, $userId2, $userType1, $userType2,
            $userId2, $userId1, $userType2, $userType1
        ];
        
        $this->db->openConnection();
        $result = $this->db->selectPrepared($sql, "ississis", $params);
        $this->db->closeConnection();
        
        return $result ?: [];
    }
    
    // Function to mark messages as read
    public function markMessagesAsRead(int $receiverId, int $senderId, string $receiverType, string $senderType): bool {
        $sql = "UPDATE messages 
                SET is_read = 1 
                WHERE receiver_id = ? AND sender_id = ? AND receiver_type = ? AND sender_type = ? AND is_read = 0";
        
        $params = [$receiverId, $senderId, $receiverType, $senderType];
        
        $this->db->openConnection();
        $result = $this->db->update($sql, "iiss", $params);
        $this->db->closeConnection();
        
        return $result;
    }
    
    // Function to get unread message count for a user
    public function getUnreadMessageCount(int $userId, string $userType): int {
        $sql = "SELECT COUNT(*) as count FROM messages 
                WHERE receiver_id = ? AND receiver_type = ? AND is_read = 0";
        
        $params = [$userId, $userType];
        
        $this->db->openConnection();
        $result = $this->db->selectPrepared($sql, "is", $params);
        $this->db->closeConnection();
        
        return $result[0]['count'] ?? 0;
    }
}
?>