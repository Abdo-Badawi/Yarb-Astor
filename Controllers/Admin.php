<?php
namespace Models;

require_once 'User.php';


class Admin implements User {
    public string $AdminID;
    public string $Username;
    public string $Password;
    public string $Role; // Enum
    public \DateTime $CreatedAt;
    public \DateTime $LastLogin;

    public function login(): bool {}
    public function manageFees(): bool {}
    public function approveOpportunity(): bool {}
    public function suspendUser(): bool {}
    public function manageReports(): bool {}
    public function faqSupport(): bool {}
    public function resetPassword(): bool {}
    public function updateProfile(): bool {}
} 