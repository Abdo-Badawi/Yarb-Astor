<?php
namespace Models;

// No dependencies
class Notification {
    public string $NotificationID;
    public int $ReceiverID;
    public string $Content;
    public \DateTime $Timestamp;
} 