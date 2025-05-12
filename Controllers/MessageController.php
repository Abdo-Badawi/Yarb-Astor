<?php
require_once 'DBController.php';
require_once '../Models/Message.php';

class MessageController {
    private $messageModel;
    
    public function __construct() {
        $this->messageModel = new Message();
    }
    
    // Function to send a message
    public function sendMessage(array $messageData): bool {
        return $this->messageModel->createMessage($messageData);
    }
    
    // Function to get conversation between two users
    public function getConversation(int $userId1, int $userId2, string $userType1, string $userType2): array {
        return $this->messageModel->getConversation($userId1, $userId2, $userType1, $userType2);
    }
    
    // Function to mark messages as read
    public function markMessagesAsRead(int $receiverId, int $senderId, string $receiverType, string $senderType): bool {
        return $this->messageModel->markAsRead($receiverId, $senderId, $receiverType, $senderType);
    }
    
    // Function to get recent conversations for a user
    public function getRecentConversations(int $userId, string $userType): array {
        return $this->messageModel->getRecentConversations($userId, $userType);
    }
    
    // Function to get unread message count for a user
    public function getUnreadMessageCount(int $userId, string $userType): int {
        return $this->messageModel->getUnreadCount($userId, $userType);
    }
    
    // Function to delete a message
    public function deleteMessage(int $messageId): bool {
        return $this->messageModel->deleteMessage($messageId);
    }
    
    // Function to delete a conversation
    public function deleteConversation(int $userId1, int $userId2, string $userType1, string $userType2): bool {
        return $this->messageModel->deleteConversation($userId1, $userId2, $userType1, $userType2);
    }
}
?>



