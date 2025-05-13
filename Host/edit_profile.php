<?php
session_start();
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
$userData = $profileController->viewHostProfile();

// If no user data is found, redirect to profile
if (!$userData) {
    header("Location: profile.php");
    exit;
}

// Check for success or error messages
$successMessage = isset($_SESSION['success_message']) ? $_SESSION['success_message'] : '';
$errorMessage = isset($_SESSION['error_message']) ? $_SESSION['error_message'] : '';

// Clear session messages after displaying
unset($_SESSION['success_message']);
unset($_SESSION['error_message']);
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

<div class="container-fluid py-5">
    <div class="container">
        <div class="text-center mx-auto mb-4" style="max-width: 500px;">
            <h1 class="display-5">Edit Your Profile</h1>
            <hr class="w-25 mx-auto text-primary" style="opacity: 1;">
        </div>
        
        <!-- Success and Error Messages positioned right after the title -->
        <div class="row justify-content-center">
            <div class="col-lg-8 mb-4">
                <?php if ($successMessage): ?>
                    <div class="alert alert-success alert-dismissible fade show shadow-sm" role="alert">
                        <div class="d-flex align-items-center">
                            <i class="fas fa-check-circle me-2" style="font-size: 1.25rem;"></i>
                            <strong><?= htmlspecialchars($successMessage) ?></strong>
                        </div>
                        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                    </div>
                <?php endif; ?>

                <?php if ($errorMessage): ?>
                    <div class="alert alert-danger alert-dismissible fade show shadow-sm" role="alert">
                        <div class="d-flex align-items-center">
                            <i class="fas fa-exclamation-circle me-2" style="font-size: 1.25rem;"></i>
                            <strong><?= htmlspecialchars($errorMessage) ?></strong>
                        </div>
                        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                    </div>
                <?php endif; ?>
            </div>
        </div>
        
        <div class="row justify-content-center">
            <div class="col-lg-8">
                <div class="card border-0 shadow-sm">
                    <div class="card-body p-4">
                        <form action="update_profile.php" method="post" enctype="multipart/form-data">
                            <div class="mb-4 text-center">
                                <img src="../Controllers/GetProfileImg.php?user_id=<?= $userData['host_id'] ?>"
                                     class="rounded-circle mb-3"
                                     style="width: 150px; height: 150px; object-fit: cover;"
                                     alt="Profile Picture">
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
                                    <input type="text" name="first_name" class="form-control" value="<?= htmlspecialchars($userData['first_name']) ?>" required>
                                </div>
                                <div class="col-md-6">
                                    <label class="form-label">Last Name</label>
                                    <input type="text" name="last_name" class="form-control" value="<?= htmlspecialchars($userData['last_name']) ?>" required>
                                </div>
                                <div class="col-md-6">
                                    <label class="form-label">Email</label>
                                    <input type="email" name="email" class="form-control" value="<?= htmlspecialchars($userData['email']) ?>" required>
                                </div>
                                <div class="col-md-6">
                                    <label class="form-label">Phone Number</label>
                                    <input type="text" name="phone_number" class="form-control" value="<?= htmlspecialchars($userData['phone_number']) ?>">
                                </div>
                                <div class="col-md-6">
                                    <label class="form-label">Country</label>
                                    <input type="text" name="location" class="form-control" value="<?= htmlspecialchars($userData['location']) ?>">
                                </div>
                                <div class="col-md-6">
                                    <label class="form-label">Preferred Languages</label>
                                    <input type="text" name="preferred_language" class="form-control" value="<?= htmlspecialchars($userData['preferred_language']) ?>">
                                </div>
                                <div class="col-md-6">
                                    <label class="form-label">Property Type</label>
                                    <select name="property_type" class="form-select">
                                        <option value="teaching" <?= $userData['property_type'] == 'teaching' ? 'selected' : '' ?>>Teaching</option>
                                        <option value="farming" <?= $userData['property_type'] == 'farming' ? 'selected' : '' ?>>Farming</option>
                                        <option value="cooking" <?= $userData['property_type'] == 'cooking' ? 'selected' : '' ?>>Cooking</option>
                                        <option value="childcare" <?= $userData['property_type'] == 'childcare' ? 'selected' : '' ?>>Childcare</option>
                                    </select>
                                </div>
                                <div class="col-12">
                                    <label class="form-label">Bio</label>
                                    <textarea name="bio" class="form-control" rows="4"><?= htmlspecialchars($userData['bio']) ?></textarea>
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
    // Preview profile picture before upload
    document.getElementById('profile_picture').addEventListener('change', function(e) {
        const file = e.target.files[0];
        if (file) {
            const reader = new FileReader();
            reader.onload = function(event) {
                document.querySelector('.rounded-circle').src = event.target.result;
            }
            reader.readAsDataURL(file);
        }
    });
</script>
</body>
</html>







