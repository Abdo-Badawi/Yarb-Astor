<?php
namespace Models;

require_once 'Account.php';

class TravelerAccount implements Account {
    public array $Skills; // List<String>
    public array $TravelHistory; // List<String>
    public string $PreferredLanguage;
    public \DateTime $JoinedDate;

    public function addSkill(string $skill): bool {}
    public function addTravelEntry(string $location): bool {}

    public function updateBio(): bool {}
    public function updateAvailability(): bool {}
    public function uploadProfilePicture(): bool {}
    public function changeStatus(): bool {}
} 