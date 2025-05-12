<?php
namespace Models;

require_once 'User.php';

class Message {
    public string $MessageID;
    public User $Sender;
    public User $Receiver;
    public string $Content;
    public \DateTime $Timestamp;

    public function send(): bool {}
    public function markAsRead(): bool {}
    public function delete(): bool {}
} 