<?php
namespace Models;

include_once 'User.php'; // Assuming User.php is in the same directory

class Host extends User {
    private string $hostID;
    private string $propertyType;          // Enum type ('teaching','farming','cooking','childcare')
    private string $preferredLanguage;
    private $joinedDate;                   // Mixed type to handle different date formats
    private string $bio;
    private ?float $rate;                  // Nullable float
    private string $location;
    private string $status;                // Enum ('active','reported')

    /**
     * Constructor for Host class
     * 
     * @param string $hostID Host ID
     * @param string $propertyType Property type (teaching, farming, cooking, childcare)
     * @param string $preferredLanguage Preferred language
     * @param mixed $joinedDate Joined date (can be string, DateTime, or null)
     * @param string $bio Host bio
     * @param float|null $rate Host rate (can be null)
     * @param string $location Host location
     * @param string $status Host status (active, reported)
     */
    public function __construct(
        string $hostID = '',
        string $propertyType = '',
        string $preferredLanguage = '',
        $joinedDate = null,
        string $bio = '',
        ?float $rate = null,
        string $location = '',
        string $status = 'active'
    ) {
        parent::__construct(); // Make sure parent constructor doesn't require parameters
        $this->hostID = $hostID;
        $this->propertyType = $propertyType;
        $this->preferredLanguage = $preferredLanguage;
        $this->joinedDate = $joinedDate ?? date('Y-m-d'); // Default to today if null
        $this->bio = $bio;
        $this->rate = $rate;
        $this->location = $location;
        $this->status = $status;
    }

    // Getters and Setters
    public function getHostID(): string { 
        return $this->hostID; 
    }
    
    public function setHostID(string $hostID): void {
        $this->hostID = $hostID;
    }
    
    public function getPropertyType(): string { 
        return $this->propertyType; 
    }
    
    public function setPropertyType($propertyType): void {
        $this->propertyType = $propertyType;
    }
    
    public function getPreferredLanguage(): string { 
        return $this->preferredLanguage; 
    }
    
    public function setPreferredLanguage($preferredLanguage): void {
        $this->preferredLanguage = $preferredLanguage;
    }
    
    public function getJoinedDate() { 
        return $this->joinedDate; 
    }
    
    public function setJoinedDate($joinedDate): void {
        $this->joinedDate = $joinedDate;
    }
    
    public function getBio(): string { 
        return $this->bio; 
    }
    
    /**
     * Set the host's bio
     * 
     * @param string $bio The host's bio
     * @return void
     */
    public function setBio($bio): void {
        $this->bio = $bio;
    }
    
    public function getRate(): ?float { 
        return $this->rate; 
    }
    
    public function setRate(?float $rate): void {
        $this->rate = $rate;
    }
    
    public function getLocation(): string { 
        return $this->location; 
    }
    
    public function setLocation($location): void {
        $this->location = $location;
    }
    
    public function getStatus(): string { 
        return $this->status; 
    }
    
    public function setStatus(string $status): void {
        $this->status = $status;
    }

    /**
     * Create a new opportunity
     * 
     * @param array $opportunityData Opportunity data
     * @return bool True if opportunity created successfully, false otherwise
     */
    public function addOpportunity(array $opportunityData): bool {
        // Implementation would typically call the OpportunityController
        return true;
    }

    /**
     * Update host profile
     * 
     * @param array $profileData Profile data to update
     * @return bool True if profile updated successfully, false otherwise
     */
    public function updateProfile(array $profileData): bool {
        // Update host properties
        if (isset($profileData['property_type'])) {
            $this->setPropertyType($profileData['property_type']);
        }
        
        if (isset($profileData['preferred_language'])) {
            $this->setPreferredLanguage($profileData['preferred_language']);
        }
        
        if (isset($profileData['bio'])) {
            $this->setBio($profileData['bio']);
        }
        
        if (isset($profileData['rate'])) {
            $this->setRate($profileData['rate']);
        }
        
        if (isset($profileData['location'])) {
            $this->setLocation($profileData['location']);
        }
        
        
        
        return true;
    }

    /**
     * Convert host object to array
     * 
     * @return array Host data as array
     */
    public function toArray(): array {
        // Get parent User data
        $userData = parent::toArray();
        
        // Add Host-specific data
        $hostData = [
            'host_id' => $this->hostID,
            'property_type' => $this->propertyType,
            'preferred_language' => $this->preferredLanguage,
            'joined_date' => $this->joinedDate,
            'bio' => $this->bio,
            'rate' => $this->rate,
            'location' => $this->location,
            'status' => $this->status
        ];
        
        // Merge and return
        return array_merge($userData, $hostData);
    }

    /**
     * Create host object from array
     * 
     * @param array $data Host data
     * @return Host
     */
    public static function fromArray(array $data): Host {
        $host = new Host(
            $data['host_id'] ?? $data['user_id'] ?? '',
            $data['property_type'] ?? '',
            $data['preferred_language'] ?? '',
            $data['joined_date'] ?? null,
            $data['bio'] ?? '',
            $data['rate'] ?? null,
            $data['location'] ?? '',
            $data['status'] ?? 'active'
        );
        
        // Set User properties
        if (isset($data['first_name'])) $host->setFirstName($data['first_name']);
        if (isset($data['last_name'])) $host->setLastName($data['last_name']);
        if (isset($data['email'])) $host->setEmail($data['email']);
        if (isset($data['password'])) $host->setPassword($data['password']);
        if (isset($data['phone_number'])) $host->setPhoneNumber($data['phone_number']);
        if (isset($data['date_of_birth'])) $host->setDateOfBirth($data['date_of_birth']);
        if (isset($data['gender'])) $host->setGender($data['gender']);
        if (isset($data['profile_picture'])) $host->setProfilePicture($data['profile_picture']);
        
        return $host;
    }

    /**
     * Get host opportunities
     * 
     * @return array List of opportunities
     */
    public function getOpportunities(): array {
        // This would typically call the OpportunityController
        return [];
    }

    /**
     * Get host reviews
     * 
     * @return array List of reviews
     */
    public function getReviews(): array {
        // This would typically call the ReviewController
        return [];
    }

    /**
     * Get host applications
     * 
     * @return array List of applications
     */
    public function getApplications(): array {
        // This would typically call the ApplicationController
        return [];
    }

    /**
     * Report a host
     * 
     * @param string $reason Reason for reporting
     * @return bool True if report successful, false otherwise
     */
    public function report(string $reason): bool {
        $this->setStatus('reported');
        return true;
    }
}








