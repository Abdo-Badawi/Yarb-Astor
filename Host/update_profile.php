<?php
session_start();
// Check if user is logged in and is a host
if (!isset($_SESSION['userID']) || $_SESSION['userType'] !== 'host') {
    header("Location: ../Common/login.php");
    exit;
}

include_once '../Controllers/ProfileController.php';
include_once '../Models/Database.php';

$userId = $_SESSION['userID'];

// Check if form was submitted
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Create profile controller
    $profileController = new ProfileController();
    
    // Collect form data
    $userData = [
        'first_name' => $_POST['first_name'],
        'last_name' => $_POST['last_name'],
        'email' => $_POST['email'],
        'phone_number' => $_POST['phone_number'],
        'preferred_language' => $_POST['preferred_language'],
        'bio' => $_POST['bio'],
        'location' => $_POST['location'],
        'property_type' => $_POST['property_type']
    ];
    
    // Check if email is being changed and validate it's unique
    if (isset($_SESSION['email']) && $_POST['email'] !== $_SESSION['email']) {
        $db = new Database();
        if ($db->openConnection()) {
            $query = "SELECT user_id FROM users WHERE email = ? AND user_id != ?";
            $params = [$_POST['email'], $userId];
            $result = $db->selectPrepared($query, "si", $params);
            $db->closeConnection();
            
            if ($result && count($result) > 0) {
                $_SESSION['error_message'] = "Email address is already in use by another account.";
                header("Location: edit_profile.php");
                exit;
            }
        }
    }
    
    // Handle profile picture upload if provided
    if (isset($_FILES['profile_picture']) && $_FILES['profile_picture']['error'] === 0) {
        $target_dir = "../uploads/";
        if (!file_exists($target_dir)) {
            mkdir($target_dir, 0777, true);
        }
        
        $target_file = $target_dir . basename($_FILES["profile_picture"]["name"]);
        $imageFileType = strtolower(pathinfo($target_file, PATHINFO_EXTENSION));
        
        // Generate unique filename
        $newFileName = uniqid() . '.' . $imageFileType;
        $target_file = $target_dir . $newFileName;
        
        // Check if image file is valid
        $check = getimagesize($_FILES["profile_picture"]["tmp_name"]);
        if ($check !== false) {
            if (move_uploaded_file($_FILES["profile_picture"]["tmp_name"], $target_file)) {
                $userData['profile_picture'] = $newFileName;
            }
        }
    }
    
    // Update profile
    $result = $profileController->updateHostProfile($userId, $userData);

    if ($result) {
        // Update session email if it was changed
        if (isset($_SESSION['email']) && $_SESSION['email'] !== $_POST['email']) {
            $_SESSION['email'] = $_POST['email'];
        }
        $_SESSION['success_message'] = "Your profile has been updated successfully! All changes have been saved.";
    } else {
        $_SESSION['error_message'] = "We couldn't update your profile. Please check your information and try again.";
    }
    
    // Redirect back to profile page
    header("Location: profile.php");
    exit;
}

// If not a POST request, redirect to edit profile page
header("Location: edit_profile.php");
exit;



