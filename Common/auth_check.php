<?php
// Start session if not already started
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

// Function to check if user is logged in with specific role
function checkUserAuth($requiredRole = null) {
    // Check if user is logged in
    if (!isset($_SESSION['userID'])) {
        // Redirect to login page with return URL
        $currentPage = urlencode($_SERVER['REQUEST_URI']);
        header("Location: ../Common/login.php?redirect=$currentPage");
        exit;
    }
    
    // If role is specified, check if user has that role
    if ($requiredRole !== null && $_SESSION['userType'] !== $requiredRole) {
        // User doesn't have required role - redirect to appropriate dashboard
        switch ($_SESSION['userType']) {
            case 'host':
                header("Location: ../Host/index.php");
                break;
            case 'traveler':
                header("Location: ../Traveler/index.php");
                break;
            case 'admin':
                header("Location: ../Admin/index.php");
                break;
            default:
                header("Location: ../Common/login.php");
                break;
        }
        exit;
    }
    
    // Add a session token for additional security
    if (!isset($_SESSION['auth_token'])) {
        $_SESSION['auth_token'] = bin2hex(random_bytes(32));
    }
    
    return true;
}
?>