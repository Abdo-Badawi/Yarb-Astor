<?php
require_once '../Models/Message.php';

class MessageController {
    private $messageModel;
    
    public function __construct() {
        $this->messageModel = new Message();
    }
    
    public function sendMessage($messageData) {
        return $this->messageModel->sendMessage($messageData);
    }
    
    public function getConversation($userId1, $userId2, $userType1, $userType2) {
        return $this->messageModel->getConversation($userId1, $userId2, $userType1, $userType2);
    }
    
    public function markMessagesAsRead($receiverId, $senderId, $receiverType, $senderType) {
        return $this->messageModel->markMessagesAsRead($receiverId, $senderId, $receiverType, $senderType);
    }
    
    public function getRecentConversations($userId, $userType) {
        return $this->messageModel->getRecentConversations($userId, $userType);
    }
    
    public function getUnreadMessageCount($userId, $userType) {
        return $this->messageModel->getUnreadMessageCount($userId, $userType);
    }
    
    public function deleteMessage($messageId) {
        return $this->messageModel->deleteMessage($messageId);
    }
    
    public function deleteConversation($userId1, $userId2, $userType1, $userType2) {
        return $this->messageModel->deleteConversation($userId1, $userId2, $userType1, $userType2);
    }
}
?>

