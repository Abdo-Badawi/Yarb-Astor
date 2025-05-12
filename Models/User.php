<?php
include_once '../Models/Database.php';

// No dependencies
class User {
    private string $userID = ''; // Initialize with a default value
    private string $nationalID;
    private string $email;
    private string $password;
    private string $userType;
    private string $firstName;
    private string $lastName;
    private string $phoneNumber;
    private string $profilePicture; // Assuming this is a file path or URL
    private string $gender; // New property for gender
    private string $birthday; // New property for birthday
    private ?string $propertyType = null; // For hosts
    private ?string $preferredLanguage = null; // Common for both
    private ?string $bio = null; // Common for both
    private ?string $location = null; // Common for both
    private ?string $skills = null; // For travelers
    private ?string $languageSpoken = null; // For travelers
    protected $db;


    public function __construct(string $email, string $password) {
        $this->email = $email;
        $this->password = $password;
    }

    // Existing setters
    public function setEmail(string $email): void {
        $this->email = $email;
    }

    public function setPassword(string $password): void {
        $this->password = $password;
    }

    // Existing getters
    public function getEmail(): string {
        return $this->email;
    }

    public function getPassword(): string {
        return $this->password;
    }

    // New setters
    public function setUserID(string $userID): void {
        $this->userID = $userID;
    }

    public function setNationalID(string $nationalID): void {
        $this->nationalID = $nationalID;
    }

    public function setUserType(string $userType): void {
        $this->userType = $userType;
    }

    public function setFirstName(string $firstName): void {
        $this->firstName = $firstName;
    }

    public function setLastName(string $lastName): void {
        $this->lastName = $lastName;
    }

    public function setPhoneNumber(string $phoneNumber): void {
        $this->phoneNumber = $phoneNumber;
    }

    public function setProfilePicture(string $profilePicture): void {
        $this->profilePicture = $profilePicture;
    }

    public function setGender(string $gender): void { // New setter for gender
        $this->gender = $gender;
    }

    public function setBirthday(string $birthday): void { // New setter for birthday
        $this->birthday = $birthday;
    }

    public function setPropertyType(?string $propertyType): void {
        $this->propertyType = $propertyType;
    }

    public function setPreferredLanguage(?string $preferredLanguage): void {
        $this->preferredLanguage = $preferredLanguage;
    }

    public function setBio(?string $bio): void {
        $this->bio = $bio;
    }

    public function setLocation(?string $location): void {
        $this->location = $location;
    }

    public function setSkills(?string $skills): void {
        $this->skills = $skills;
    }

    public function setLanguageSpoken(?string $languageSpoken): void {
        $this->languageSpoken = $languageSpoken;
    }

    // New getters
    public function getUserID(): string {
        return $this->userID;
    }

    public function getNationalID(): string {
        return $this->nationalID;
    }

    public function getUserType(): string {
        return $this->userType;
    }

    public function getFirstName(): string {
        return $this->firstName;
    }

    public function getLastName(): string {
        return $this->lastName;
    }

    public function getPhoneNumber(): string {
        return $this->phoneNumber;
    }

    public function getProfilePicture(): string {
        return $this->profilePicture;
    }

    public function getGender(): string { // New getter for gender
        return $this->gender;
    }

    public function getBirthday(): string { // New getter for birthday
        return $this->birthday;
    }

    public function getPropertyType(): ?string {
        return $this->propertyType;
    }

    public function getPreferredLanguage(): ?string {
        return $this->preferredLanguage;
    }

    public function getBio(): ?string {
        return $this->bio;
    }

    public function getLocation(): ?string {
        return $this->location;
    }

    public function getSkills(): ?string {
        return $this->skills;
    }

    public function getLanguageSpoken(): ?string {
        return $this->languageSpoken;
    }


    public function login($email, $password) {
            $this->db = new Database();
            if ($this->db->openConnection()) {
                // Query to fetch the user by email
                $query = "SELECT user_id, password, user_type FROM users WHERE email = ?";
                $params = [$this->email];
                $result = $this->db->selectPrepared($query, "s", $params);

                if ($result && count($result) > 0) {
                    $dbPassword = $result[0]['password']; // Hashed password from the database
                    $userType = $result[0]['user_type'];
                    $userID = $result[0]['user_id'];

                    // Verify the entered password with the hashed password
                    if (password_verify($this->password, $dbPassword)) {
                        // Start the session and set session variables
                        session_start();
                        $_SESSION['email'] = $this->email; // Changed from $this->email()
                        $_SESSION['userType'] = $userType;
                        $_SESSION['userID'] = $userID;

                        // Add a session token for additional security
                        if (!isset($_SESSION['auth_token'])) {
                            $_SESSION['auth_token'] = bin2hex(random_bytes(32));
                        }

                        return true; // Login successful
                    } else {
                        // Invalid password
                        session_start();
                        $_SESSION['errMsg'] = "Invalid email or password.";
                        return false;
                    }
                } else {
                    // User not found
                    session_start();
                    $_SESSION['errMsg'] = "Invalid email or password.";
                    return false;
                }
            } else {
                error_log("Database connection failed.");
                return false;
            }
    }

    public function logout(){
            session_start(); // Always start the session first

            // Unset all session variables
            $_SESSION = array();

            // Destroy the session cookie
            if (isset($_COOKIE[session_name()])) {
                setcookie(session_name(), '', time() - 3600, '/');
            }

            // Destroy the session
            session_destroy();

            // Redirect to login page
            header("Location: login.php");
            exit();
    }
}
?>
