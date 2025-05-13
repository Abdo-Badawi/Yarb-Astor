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
