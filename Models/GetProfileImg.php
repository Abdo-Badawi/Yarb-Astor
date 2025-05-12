<?php
include_once '../Models/Database.php';

// Get user_id from query parameter
$userId = isset($_GET['user_id']) ? intval($_GET['user_id']) : 0;

if ($userId <= 0) {
    header("HTTP/1.0 400 Bad Request");
    exit;
}

// Connect to database
$db = new Database();
if (!$db->openConnection()) {
    header("HTTP/1.0 500 Internal Server Error");
    exit;
}

// Get profile picture filename
$query = "SELECT profile_picture FROM users WHERE user_id = ?";
$params = [$userId];
$result = $db->selectPrepared($query, "i", $params);

$db->closeConnection();

if (!$result || empty($result[0]['profile_picture'])) {
    // Return default profile image
    $defaultImage = "../img/default-profile.jpg";

    // Check if default image exists, if not use a fallback
    if (!file_exists($defaultImage)) {
        $defaultImage = "assets/images/user/avatar-2.jpg";
    }

    header("Content-Type: image/jpeg");
    readfile($defaultImage);
    exit;
}

// Check if the profile_picture is a BLOB or a filename
$profilePicture = $result[0]['profile_picture'];

// If it's a string shorter than 255 characters, assume it's a filename
if (is_string($profilePicture) && strlen($profilePicture) < 255) {
    // Get the profile picture path
    $imagePath = "../uploads/" . $profilePicture;

    // Check if file exists
    if (file_exists($imagePath)) {
        $imageInfo = getimagesize($imagePath);
        header("Content-Type: " . $imageInfo['mime']);
        readfile($imagePath);
        exit;
    }
}

// If we get here, either it's a BLOB or the file doesn't exist
// Try to output it as a BLOB
if ($profilePicture) {
    // For binary data, we'll assume it's a JPEG image
    header("Content-Type: image/jpeg");
    echo $profilePicture;
    exit;
}

// If all else fails, return the default image
$defaultImage = "../img/default-profile.jpg";

// Check if default image exists, if not use a fallback
if (!file_exists($defaultImage)) {
    $defaultImage = "assets/images/user/avatar-2.jpg";
}

header("Content-Type: image/jpeg");
readfile($defaultImage);
?>

