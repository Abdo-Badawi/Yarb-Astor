<?php
$conn = new mysqli("localhost", "root", "", "homestay");

if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

$userId = intval($_GET['user_id'] ?? 0);

$stmt = $conn->prepare("SELECT profile_picture FROM users WHERE user_id = ?");
$stmt->bind_param("i", $userId);
$stmt->execute();
$stmt->store_result();

$stmt->bind_result($imageData);
$stmt->fetch();

if (!empty($imageData)) {
    header("Content-Type: image/jpeg"); // Change if your images are PNG
    echo $imageData;
} else {
    // Optional: fallback image
    header("Content-Type: image/png");
    readfile("../img/download.png");
}

$stmt->close();
$conn->close();
?>
