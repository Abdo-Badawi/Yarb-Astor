<?php
namespace Models;

// No dependencies
interface Account {
    public function updateBio(): bool;
    public function updateAvailability(): bool;
    public function uploadProfilePicture(): bool;
    public function changeStatus(): bool;
} 