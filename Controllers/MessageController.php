<?php
require_once '../Models/Message.php';

class MessageController {
    private $messageModel;
    
    public function __construct() {
        $this->messageModel = new \Models\Message();
    }
    
    /**
     * Send a message
     * 
     * @param array $messageData Message data including sender_id, receiver_id, content, etc.
     * @return bool True if message was sent successfully, false otherwise
     */
    public function sendMessage(array $messageData): bool {
        // Validate required fields
        if (empty($messageData['sender_id']) || empty($messageData['receiver_id']) || 
            empty($messageData['content']) || empty($messageData['sender_type']) || 
            empty($messageData['receiver_type'])) {
            error_log("Missing required fields in sendMessage");
            return false;
        }
        
        // Ensure content is not too long
        if (strlen($messageData['content']) > 10000) {
            error_log("Message content too long");
            return false;
        }
        
        return $this->messageModel->sendMessage($messageData);
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
        if ($userId1 <= 0 || $userId2 <= 0 || empty($userType1) || empty($userType2)) {
            error_log("Invalid parameters in getConversation");
            return [];
        }
        
        return $this->messageModel->getConversation($userId1, $userId2, $userType1, $userType2);
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
        if ($receiverId <= 0 || $senderId <= 0 || empty($receiverType) || empty($senderType)) {
            error_log("Invalid parameters in markMessagesAsRead");
            return false;
        }
        
        return $this->messageModel->markMessagesAsRead($receiverId, $senderId, $receiverType, $senderType);
    }
    
    /**
     * Get recent conversations for a user
     * 
     * @param int $userId User ID
     * @param string $userType User type
     * @return array Array of recent conversations
     */
    public function getRecentConversations(int $userId, string $userType): array {
        if ($userId <= 0 || empty($userType)) {
            error_log("Invalid parameters in getRecentConversations");
            return [];
        }
        
        return $this->messageModel->getRecentConversations($userId, $userType);
    }
    
    /**
     * Get unread message count for a user
     * 
     * @param int $userId User ID
     * @param string $userType User type
     * @return int Number of unread messages
     */
    public function getUnreadMessageCount(int $userId, string $userType): int {
        if ($userId <= 0 || empty($userType)) {
            error_log("Invalid parameters in getUnreadMessageCount");
            return 0;
        }
        
        return $this->messageModel->getUnreadMessageCount($userId, $userType);
    }
    
    /**
     * Delete a message
     * 
     * @param int $messageId Message ID
     * @return bool True if message was deleted, false otherwise
     */
    public function deleteMessage(int $messageId): bool {
        if ($messageId <= 0) {
            error_log("Invalid message ID in deleteMessage");
            return false;
        }
        
        return $this->messageModel->deleteMessage($messageId);
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
        if ($userId1 <= 0 || $userId2 <= 0 || empty($userType1) || empty($userType2)) {
            error_log("Invalid parameters in deleteConversation");
            return false;
        }
        
        return $this->messageModel->deleteConversation($userId1, $userId2, $userType1, $userType2);
    }
}
?>

