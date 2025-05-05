<?php
session_start();

// Check if the user has successfully registered
if (!isset($_SESSION['registration_success'])) {
    header("Location: register.php"); // Redirect to the registration page if accessed directly
    exit();
}

// Get the success message and user role
$successMessage = $_SESSION['registration_success'];
$userRole = $_SESSION['user_role'] ?? null; // Assuming the role is stored in the session during registration

unset($_SESSION['registration_success']); // Clear the success message after displaying it

// Redirect based on user role
if (!in_array($userRole, ['host', 'traveler'])) {
    header("Location: ../index.php");
    exit();
}

if ($userRole === 'host') {
    header("Location: ../Host/index.php"); // Redirect to the host home page
    exit();
} elseif ($userRole === 'traveler') {
    header("Location: ../Traveler/index.php"); // Redirect to the traveler home page
    exit();
} else {
    // If the role is not set or invalid, redirect to a default page
    header("Location: register.php");
    exit();
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Registration Successful</title>
    <link href="../css/bootstrap.min.css" rel="stylesheet">
    <link href="../css/style.css" rel="stylesheet">
</head>
<body>
    <!-- Navbar -->
    <?php include 'navCommon.php'; ?>

    <div class="container text-center py-5">
        <h1 class="text-success">Registration Successful!</h1>
        <p class="lead">Thank you for joining HomeStays. Your account has been successfully created.</p>
        <a href="login.php" class="btn btn-primary">Go to Login</a>
    </div>

    <!-- Footer -->
    <?php include 'footer.php'; ?>

    <script src="../js/main.js"></script>
</body>
</html>