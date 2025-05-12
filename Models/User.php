<?php
namespace Models;

require_once __DIR__ . '/../Controllers/DBController.php';

/**
 * Base User class
 */
abstract class User {
    protected $db;
    protected int $userId;
    protected string $userType;
    protected string $firstName;
    protected string $lastName;
    protected string $email;
    protected string $password;
    protected ?string $phoneNumber;
    protected ?string $dateOfBirth;
    protected ?string $gender;
    protected ?string $profilePicture;
    protected ?string $nationalID;
    protected string $createdAt;
    
    /**
     * Constructor
     */
    public function __construct() {
        $this->db = new \DBController();
        $this->userId = 0;
        $this->userType = '';
        $this->firstName = '';
        $this->lastName = '';
        $this->email = '';
        $this->password = '';
        $this->phoneNumber = null;
        $this->dateOfBirth = null;
        $this->gender = null;
        $this->profilePicture = null;
        $this->nationalID = null;
        $this->createdAt = date('Y-m-d H:i:s');
    }
    
    // Getters and setters
    public function getUserId(): int {
        return $this->userId;
    }
    
    public function setUserId(int $userId): void {
        $this->userId = $userId;
    }
    
    public function getUserType(): string {
        return $this->userType;
    }
    
    public function setUserType(string $userType): void {
        $this->userType = $userType;
    }
    
    public function getFirstName(): string {
        return $this->firstName;
    }
    
    public function setFirstName(string $firstName): void {
        $this->firstName = $firstName;
    }
    
    public function getLastName(): string {
        return $this->lastName;
    }
    
    public function setLastName(string $lastName): void {
        $this->lastName = $lastName;
    }
    
    public function getEmail(): string {
        return $this->email;
    }
    
    public function setEmail(string $email): void {
        $this->email = $email;
    }
    
    public function getPassword(): string {
        return $this->password;
    }
    
    public function setPassword(string $password): void {
        $this->password = $password;
    }
    
    public function getPhoneNumber(): ?string {
        return $this->phoneNumber;
    }
    
    public function setPhoneNumber(?string $phoneNumber): void {
        $this->phoneNumber = $phoneNumber;
    }
    
    public function getDateOfBirth(): ?string {
        return $this->dateOfBirth;
    }
    
    public function setDateOfBirth(?string $dateOfBirth): void {
        $this->dateOfBirth = $dateOfBirth;
    }
    
    public function getGender(): ?string {
        return $this->gender;
    }
    
    public function setGender(?string $gender): void {
        $this->gender = $gender;
    }
    
    public function getProfilePicture(): ?string {
        return $this->profilePicture;
    }
    
    public function setProfilePicture(?string $profilePicture): void {
        $this->profilePicture = $profilePicture;
    }
    
    public function getNationalID(): ?string {
        return $this->nationalID;
    }
    
    public function setNationalID(?string $nationalID): void {
        $this->nationalID = $nationalID;
    }
    
    public function getCreatedAt(): string {
        return $this->createdAt;
    }
    
    public function setCreatedAt(string $createdAt): void {
        $this->createdAt = $createdAt;
    }
    
    /**
     * Get full name
     * 
     * @return string Full name
     */
    public function getFullName(): string {
        return $this->firstName . ' ' . $this->lastName;
    }
    
    /**
     * Map database result to user properties
     * 
     * @param array $data Database result
     */
    protected function mapFromDatabase(array $data): void {
        $this->userId = (int)($data['user_id'] ?? 0);
        $this->userType = $data['user_type'] ?? '';
        $this->firstName = $data['first_name'] ?? '';
        $this->lastName = $data['last_name'] ?? '';
        $this->email = $data['email'] ?? '';
        $this->password = $data['password'] ?? '';
        $this->phoneNumber = $data['phone_number'] ?? null;
        $this->dateOfBirth = $data['date_of_birth'] ?? null;
        $this->gender = $data['gender'] ?? null;
        $this->profilePicture = $data['profile_picture'] ?? null;
        $this->nationalID = $data['national_id'] ?? null;
        $this->createdAt = $data['created_at'] ?? date('Y-m-d H:i:s');
    }
    
    /**
     * Convert user object to array
     * 
     * @return array User data as array
     */
    public function toArray(): array {
        return [
            'user_id' => $this->userId,
            'user_type' => $this->userType,
            'first_name' => $this->firstName,
            'last_name' => $this->lastName,
            'email' => $this->email,
            'password' => $this->password,
            'phone_number' => $this->phoneNumber,
            'date_of_birth' => $this->dateOfBirth,
            'gender' => $this->gender,
            'profile_picture' => $this->profilePicture,
            'national_id' => $this->nationalID,
            'created_at' => $this->createdAt
        ];
    }
}




