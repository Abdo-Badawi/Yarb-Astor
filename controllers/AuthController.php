<?php
namespace Controllers;

require_once __DIR__ . '/../models/Database.php';
require_once __DIR__ . '/../models/User.php';
require_once __DIR__ . '/../models/Traveler.php';
require_once __DIR__ . '/../models/Host.php';

use Models\User;
use Models\Traveler;
use Models\Host;

class AuthController {
    
    public function login(User $user): bool {
        // Get authentication data from the User model
        $userData = $user->authenticate();
        
        if ($userData) {
            // Start session if not already started
            if (session_status() == PHP_SESSION_NONE) {
                session_start();
            }
            
            // Set session variables
            $_SESSION['email'] = $userData['email'];
            $_SESSION['userID'] = $userData['userID'];
            $_SESSION['userType'] = $userData['userType'];
            
            // Add a session token for additional security
            if (!isset($_SESSION['auth_token'])) {
                $_SESSION['auth_token'] = bin2hex(random_bytes(32));
            }
            
            return true;
        }
        
        // Set error message
        if (session_status() == PHP_SESSION_NONE) {
            session_start();
        }
        $_SESSION['errMsg'] = "Invalid email or password";
        return false;
    }
    
    public function logout(): void {
        if (session_status() == PHP_SESSION_NONE) {
            session_start();
        }
        
        session_unset();
        session_destroy();
        
        // Redirect to login page
        header("Location: ../views/shared/login.php");
        exit();
    }

    public function register(User $user): bool {
        // Call the register method from the User model
        $userId = $user->register();
        
        if ($userId) {
            // Start session if not already started
            if (session_status() == PHP_SESSION_NONE) {
                session_start();
            }
            
            // Set session variables
            $_SESSION['email'] = $user->getEmail();
            $_SESSION['userID'] = $userId;
            $_SESSION['userType'] = $user->getUserType();
            
            // Add a session token for additional security
            if (!isset($_SESSION['auth_token'])) {
                $_SESSION['auth_token'] = bin2hex(random_bytes(32));
            }
            
            return true;
        }
        
        // Log the failure
        error_log("User registration failed in AuthController::register");
        return false;
    }
    
    /**
     * Process registration form data and create a user based on role
     * 
     * @param array $formData The form data from the registration form
     * @return bool True if registration was successful, false otherwise
     */
    public function processRegistration($formData): bool {
        // Debug: Log the start of processRegistration
        error_log("Starting processRegistration with data: " . json_encode($formData));
        
        // Validate required fields
        $requiredFields = ['firstName', 'lastName', 'email', 'password', 'confirmPassword', 'phone', 'gender', 'birthday', 'nationalId', 'role'];
        foreach ($requiredFields as $field) {
            if (empty($formData[$field])) {
                if (session_status() == PHP_SESSION_NONE) {
                    session_start();
                }
                $_SESSION['errMsg'] = "Please fill in all required fields.";
                error_log("Missing required field: " . $field);
                return false;
            }
        }

        // Validate email format
        if (!filter_var($formData['email'], FILTER_VALIDATE_EMAIL)) {
            if (session_status() == PHP_SESSION_NONE) {
                session_start();
            }
            $_SESSION['errMsg'] = "Invalid email format.";
            return false;
        }

        // Validate password confirmation
        if ($formData['password'] !== $formData['confirmPassword']) {
            if (session_status() == PHP_SESSION_NONE) {
                session_start();
            }
            $_SESSION['errMsg'] = "Passwords do not match.";
            return false;
        }

        // Check if email already exists
        $db = new \Database();
        if (!$db->openConnection()) {
            error_log("Failed to connect to database in processRegistration");
            if (session_status() == PHP_SESSION_NONE) {
                session_start();
            }
            $_SESSION['errMsg'] = "Database connection error. Please try again later.";
            return false;
        }
        $query = "SELECT COUNT(*) AS count FROM users WHERE email = ?";
        $params = [$formData['email']];
        $result = $db->selectPrepared($query, "s", $params);

        if ($result && $result[0]['count'] > 0) {
            if (session_status() == PHP_SESSION_NONE) {
                session_start();
            }
            $_SESSION['errMsg'] = "An account with this email already exists.";
            $db->closeConnection();
            return false;
        }
        $db->closeConnection();

        // Hash the password
        $hashedPassword = password_hash($formData['password'], PASSWORD_BCRYPT);
        
        // Create the appropriate user type based on role
        if ($formData['role'] === 'traveler') {
            $user = new \Models\Traveler($formData['email'], $hashedPassword);
            $user->setSkills($formData['skills'] ?? null);
            $user->setLanguageSpoken($formData['languageSpoken'] ?? null);
            $user->setPreferredLanguage($formData['preferredLanguageTraveler'] ?? null);
            $user->setBio($formData['bioTraveler'] ?? null);
            $user->setLocation($formData['locationTraveler'] ?? null);
        } elseif ($formData['role'] === 'host') {
            $user = new \Models\Host($formData['email'], $hashedPassword);
            $user->setPropertyType($formData['propertyType'] ?? null);
            $user->setPreferredLanguage($formData['preferredLanguageHost'] ?? null);
            $user->setBio($formData['bioHost'] ?? null);
            $user->setLocation($formData['locationHost'] ?? null);
        } else {
            // Default to base User if role is not recognized
            $user = new \Models\User($formData['email'], $hashedPassword);
        }
        
        // Set common user properties
        $user->setFirstName($formData['firstName']);
        $user->setLastName($formData['lastName']);
        $user->setPhoneNumber($formData['phone']);
        $user->setGender($formData['gender']);
        $user->setBirthday($formData['birthday']);
        $user->setNationalID($formData['nationalId']);
        $user->setUserType($formData['role']);
        $user->setProfilePicture($formData['profilePicturePath'] ?? null);
        
        // Register the user
        return $this->register($user);
    }
}
?>



