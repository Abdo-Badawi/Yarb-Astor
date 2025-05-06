<?php
include_once 'DBController.php';

// Get user_id from query parameter
$userId = isset($_GET['user_id']) ? intval($_GET['user_id']) : 0;

if ($userId <= 0) {
    header("HTTP/1.0 400 Bad Request");
    exit;
}

// Connect to database
$db = new DBController();
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
    header("Content-Type: image/jpeg");
    readfile($defaultImage);
    exit;
}

// Get the profile picture path
$imagePath = "../uploads/" . $result[0]['profile_picture'];

// Check if file exists
if (file_exists($imagePath)) {
    $imageInfo = getimagesize($imagePath);
    header("Content-Type: " . $imageInfo['mime']);
    readfile($imagePath);
} else {
    // Return default profile image if file not found
    $defaultImage = "../img/default-profile.jpg";
    header("Content-Type: image/jpeg");
    readfile($defaultImage);
}
?>

