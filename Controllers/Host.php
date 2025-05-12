<?php
namespace Models;

include_once 'User.php'; // Assuming User.php is in the same directory

class Host extends User {
    private string $hostID;
    private string $propertyType;          // Enum type
    private string $preferredLanguage;
    // Changed type hint to allow null or string from DB initially
    private $joinedDate; 
    private string $bio;
    private float $rate;
    private string $location;
    private \DateTime $createdAt;            // TIMESTAMP
    private string $status;                // Enum (e.g., "reported")

    // Constructor - Adjusted to accept mixed type for joinedDate
    public function __construct(
        string $hostID,
        string $propertyType,
        string $preferredLanguage,
        $joinedDate, // Accept mixed type
        string $bio,
        float $rate,
        string $location,
        string $status
    ) {
        // Note: The parent User constructor is not called here. This might need adjustment
        // depending on how User objects are intended to be fully initialized.
        $this->hostID = $hostID;
        $this->propertyType = $propertyType;
        $this->preferredLanguage = $preferredLanguage;
        $this->joinedDate = $joinedDate; // Store the raw value from DB
        $this->bio = $bio;
        $this->rate = $rate;
        $this->location = $location;
        $this->status = $status;
    }

    // --- End of updated method ---

    // Example methods (Original methods retained)
    public function addOpportunities(): bool { return true; }
    public function login(): bool { return true; }
    public function resetPassword(): bool { return true; }
    public function updateProfile(): bool { return true; }

    // Getters (Original getters retained, except getJoinedDate)
    public function getHostID(): string { return $this->hostID; }
    public function getPropertyType(): string { return $this->propertyType; }
    public function getPreferredLanguage(): string { return $this->preferredLanguage; }
    public function getBio(): string { return $this->bio; }
    public function getRate(): float { return $this->rate; }
    public function getLocation(): string { return $this->location; }
    public function getStatus(): string { return $this->status; }

}

