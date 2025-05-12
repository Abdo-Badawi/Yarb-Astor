<?php
// Add this at the top of the file to see any errors
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

session_start();
// Check if user is logged in and is a host
if (!isset($_SESSION['userID']) || $_SESSION['userType'] !== 'host') {
    header("Location: ../Common/login.php");
    exit;
}

// Include the ProfileController to handle the logic
include_once '../Controllers/ProfileController.php';

// Assuming user_id is stored in session
$userId = $_SESSION['userID'];

// Create an instance of ProfileController
$profileController = new ProfileController();

// Fetch user data
$userData = $profileController->getUserData($userId);

// If no user data is found, redirect to profile
if (!$userData) {
    header("Location: profile.php");
    exit;
}

// Debug: Print user data to see what's available
// echo "<pre>"; print_r($userData); echo "</pre>";
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <title>HomeStays - Edit Profile</title>
    <meta content="width=device-width, initial-scale=1.0" name="viewport">
    <link rel="stylesheet" href="https://use.fontawesome.com/releases/v5.15.4/css/all.css"/>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.4.1/font/bootstrap-icons.css" rel="stylesheet">
    <link href="../css/bootstrap.min.css" rel="stylesheet">
    <link href="../css/style.css" rel="stylesheet">
</head>

<body>
<?php include 'navHost.php'; ?>

<!-- Edit Profile Start -->
<div class="container-fluid py-5">
    <div class="container py-5">
        <div class="text-center mb-5">
            <h1 class="mb-3">Edit Profile</h1>
            <h6 style="color:#757575" class="mb-0">Update your personal information and preferences</h6>
        </div>

        <div class="row justify-content-center">
            <div class="col-lg-8">
                <div class="card border-0 shadow-sm">
                    <div class="card-body p-4">
                        <form action="update_profile.php" method="post" enctype="multipart/form-data">
                            <div class="mb-4 text-center">
                                <?php if (!empty($userData['profile_picture'])): ?>
                                    <img src="../Controllers/GetProfileImg.php?user_id=<?= $userId ?>"
                                         class="rounded-circle mb-3"
                                         style="width: 150px; height: 150px; object-fit: cover;"
                                         alt="Profile Picture">
                                <?php else: ?>
                                    <img src="../img/default-profile.jpg"
                                         class="rounded-circle mb-3"
                                         style="width: 150px; height: 150px; object-fit: cover;"
                                         alt="Profile Picture">
                                <?php endif; ?>
                                <div class="mt-2">
                                    <label for="profile_picture" class="btn btn-outline-primary btn-sm">
                                        Change Profile Picture
                                    </label>
                                    <input type="file" id="profile_picture" name="profile_picture" class="d-none">
                                </div>
                            </div>

                            <div class="row g-3">
                                <div class="col-md-6">
                                    <label class="form-label">First Name</label>
                                    <input type="text" name="first_name" class="form-control" value="<?= htmlspecialchars($userData['first_name'] ?? '') ?>" required>
                                </div>
                                <div class="col-md-6">
                                    <label class="form-label">Last Name</label>
                                    <input type="text" name="last_name" class="form-control" value="<?= htmlspecialchars($userData['last_name'] ?? '') ?>" required>
                                </div>
                                <div class="col-md-6">
                                    <label class="form-label">Email</label>
                                    <input type="email" name="email" class="form-control" value="<?= htmlspecialchars($userData['email'] ?? '') ?>" required>
                                </div>
                                <div class="col-md-6">
                                    <label class="form-label">Phone Number</label>
                                    <input type="text" name="phone_number" class="form-control" value="<?= htmlspecialchars($userData['phone_number'] ?? '') ?>">
                                </div>
                                <div class="col-md-6">
                                    <label class="form-label">Location</label>
                                    <input type="text" name="location" class="form-control" value="<?= htmlspecialchars($userData['location'] ?? '') ?>">
                                </div>
                                <div class="col-md-6">
                                    <label class="form-label">Preferred Language</label>
                                    <input type="text" name="preferred_language" class="form-control" value="<?= htmlspecialchars($userData['preferred_language'] ?? '') ?>">
                                </div>
                                <div class="col-md-6">
                                    <label class="form-label">Property Type</label>
                                    <select name="property_type" class="form-select">
                                        <option value="">Select Property Type</option>
                                        <option value="Apartment" <?= ($userData['property_type'] ?? '') == 'Apartment' ? 'selected' : '' ?>>Apartment</option>
                                        <option value="House" <?= ($userData['property_type'] ?? '') == 'House' ? 'selected' : '' ?>>House</option>
                                        <option value="Villa" <?= ($userData['property_type'] ?? '') == 'Villa' ? 'selected' : '' ?>>Villa</option>
                                        <option value="Cabin" <?= ($userData['property_type'] ?? '') == 'Cabin' ? 'selected' : '' ?>>Cabin</option>
                                    </select>
                                </div>
                                <div class="col-12">
                                    <label class="form-label">Bio</label>
                                    <textarea name="bio" class="form-control" rows="4"><?= htmlspecialchars($userData['bio'] ?? '') ?></textarea>
                                </div>
                            </div>

                            <div class="text-center mt-4">
                                <button type="submit" class="btn btn-primary px-4">Save Changes</button>
                                <a href="profile.php" class="btn btn-outline-secondary px-4 ms-2">Cancel</a>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Footer Start -->
<?php include '../Common/footer.php'; ?>
<!-- Footer End -->

<script src="https://code.jquery.com/jquery-3.4.1.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.0.0/dist/js/bootstrap.bundle.min.js"></script>
<script src="../js/main.js"></script>
<script>
    // Function to handle form submission
    document.addEventListener('DOMContentLoaded', function() {
        const form = document.querySelector('form');
        
        form.addEventListener('submit', function(event) {
            // Prevent default form submission
            event.preventDefault();
            
            // Validate form fields
            if (validateForm()) {
                // If validation passes, submit the form
                this.submit();
            }
        });
        
        // Function to validate form fields
        function validateForm() {
            let isValid = true;
            const firstName = document.querySelector('input[name="first_name"]');
            const lastName = document.querySelector('input[name="last_name"]');
            const email = document.querySelector('input[name="email"]');
            
            // Validate first name
            if (!firstName.value.trim()) {
                showError(firstName, 'First name is required');
                isValid = false;
            } else {
                clearError(firstName);
            }
            
            // Validate last name
            if (!lastName.value.trim()) {
                showError(lastName, 'Last name is required');
                isValid = false;
            } else {
                clearError(lastName);
            }
            
            // Validate email
            if (!email.value.trim()) {
                showError(email, 'Email is required');
                isValid = false;
            } else if (!isValidEmail(email.value.trim())) {
                showError(email, 'Please enter a valid email address');
                isValid = false;
            } else {
                clearError(email);
            }
            
            return isValid;
        }
        
        // Function to show error message
        function showError(input, message) {
            const formGroup = input.closest('.col-md-6') || input.closest('.col-12');
            const errorElement = formGroup.querySelector('.error-message') || document.createElement('div');
            
            errorElement.className = 'error-message text-danger mt-1';
            errorElement.textContent = message;
            
            if (!formGroup.querySelector('.error-message')) {
                formGroup.appendChild(errorElement);
            }
            
            input.classList.add('is-invalid');
        }
        
        // Function to clear error message
        function clearError(input) {
            const formGroup = input.closest('.col-md-6') || input.closest('.col-12');
            const errorElement = formGroup.querySelector('.error-message');
            
            if (errorElement) {
                formGroup.removeChild(errorElement);
            }
            
            input.classList.remove('is-invalid');
        }
        
        // Function to validate email format
        function isValidEmail(email) {
            const emailRegex = /^[^\s@]+@[^\s@]+\.[^\s@]+$/;
            return emailRegex.test(email);
        }
        
        // Function to preview profile picture before upload
        const profilePictureInput = document.getElementById('profile_picture');
        const profilePicturePreview = document.querySelector('.rounded-circle');
        
        if (profilePictureInput && profilePicturePreview) {
            profilePictureInput.addEventListener('change', function() {
                if (this.files && this.files[0]) {
                    const reader = new FileReader();
                    
                    reader.onload = function(e) {
                        profilePicturePreview.src = e.target.result;
                    }
                    
                    reader.readAsDataURL(this.files[0]);
                }
            });
        }
    });
</script>
</body>
</html>





