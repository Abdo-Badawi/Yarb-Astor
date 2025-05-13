<?php
include_once '../Models/User.php';

class AuthController {
    public function logout() {
        // Start session if not already started
        if (session_status() == PHP_SESSION_NONE) {
            session_start();
        }
        
        // Unset all session variables
        $_SESSION = array();
        
        // If it's desired to kill the session, also delete the session cookie.
        if (isset($_COOKIE[session_name()])) {
            setcookie(session_name(), '', time() - 42000, '/');
        }
        
        // Destroy the session
        session_destroy();
        
        // Redirect to login page
        header("Location: ../Common/login.php");
        exit();
    }

    /**
     * Register a new user
     * 
     * @param array $userData User registration data
     * @return int|bool User ID on success, false on failure
     */
    public function register(array $userData) {
        // Validate required fields
        $requiredFields = ['first_name', 'last_name', 'email', 'password', 'user_type'];
        foreach ($requiredFields as $field) {
            if (empty($userData[$field])) {
                error_log("Missing required field: $field");
                return false;
            }
        }
        
        // Validate email format
        if (!filter_var($userData['email'], FILTER_VALIDATE_EMAIL)) {
            error_log("Invalid email format: " . $userData['email']);
            return false;
        }
        
        // Create a new User model instance
        $userModel = new User();
        
        // Call the register method on the model
        $userId = $userModel->register($userData);
        
        if ($userId) {
            // Start session if not already started
            if (session_status() == PHP_SESSION_NONE) {
                session_start();
            }
            
            // Set session variables
            $_SESSION['userID'] = $userId;
            $_SESSION['userType'] = $userData['user_type'];
            $_SESSION['email'] = $userData['email'];
            
            // Add a session token for additional security
            if (!isset($_SESSION['auth_token'])) {
                $_SESSION['auth_token'] = bin2hex(random_bytes(32));
            }
            
            return $userId;
        }
        
        return false;
    }

    /**
     * Check if an email is available (not already registered)
     * 
     * @param string $email Email to check
     * @return bool True if email is available, false if already exists
     */
    public function isEmailAvailable(string $email): bool {
        $userModel = new User();
        return $userModel->isEmailAvailable($email);
    }

    /**
     * Check if a national ID is available (not already registered)
     * 
     * @param string $nationalId National ID to check
     * @return bool True if national ID is available, false if already exists
     */
    public function isNationalIdAvailable(string $nationalId): bool {
        $userModel = new User();
        return $userModel->isNationalIdAvailable($nationalId);
    }
}

// Standalone login function for backward compatibility
function loginC() {
    $errMsg = "";
    if(isset($_POST['email']) && isset($_POST['password'])) {
        if(!empty($_POST['email']) && !empty($_POST['password'])){
            $user = new User($_POST['email'], $_POST['password']);
            $auth = $user->login($user->getEmail(), $user->getPassword());
            
            if(!$auth){
                // Make sure session is started
                if(session_status() == PHP_SESSION_NONE) {
                    session_start();
                }
                // Get error message from session or use default
                $errMsg = $_SESSION['errMsg'] ?? "Login failed";
                // Clear the session error message
                if(isset($_SESSION['errMsg'])) {
                    unset($_SESSION['errMsg']);
                }
                return $errMsg; // Return error message immediately
            } else {
                // Make sure session is started
                if(session_status() == PHP_SESSION_NONE) {
                    session_start();
                }
                
                // Set auth token for secure session
                $_SESSION['auth_token'] = bin2hex(random_bytes(32));
                
                // Redirect to appropriate dashboard based on user type
                if($_SESSION['userType'] == 'traveler'){
                    header("Location: ../Traveler/index.php");
                    exit();
                }
                else if($_SESSION['userType'] == 'host'){
                    header("Location: ../Host/index.php");
                    exit();
                }
                else if($_SESSION['userType'] == 'admin'){
                    header("Location: ../Admin/index.php");
                    exit();
                }
            }
        } else {
            $errMsg = "Please fill in all fields";
            return $errMsg; // Return error message immediately
        }
    }
    return $errMsg;
}
?>
