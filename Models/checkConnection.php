<?php
session_start();

// Include the ProfileController to handle the logic
include_once '../Models/ProfileController.php';

// Assuming user_id is stored in session or passed as a query parameter
$userId = $_SESSION['userID']; // Or replace with actual user id logic

// Create an instance of ProfileController
$profileController = new ProfileController();

// Fetch user data
$userData = $profileController->getUserData($userId);

// If no user data is found, display an error
if (!$userData) {
    echo "Error: No user data found.";
    exit;
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <title>HomeStays - Host Profile</title>
    <meta content="width=device-width, initial-scale=1.0" name="viewport">
    <link rel="stylesheet" href="https://use.fontawesome.com/releases/v5.15.4/css/all.css"/>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.4.1/font/bootstrap-icons.css" rel="stylesheet">
    <link href="../css/bootstrap.min.css" rel="stylesheet">
    <link href="../css/style.css" rel="stylesheet">
</head>

<body>
<!-- Topbar Start -->
<?php include 'navHost.php'; ?>
<!-- Topbar End -->

<!-- Profile Start -->
<div class="container-fluid py-5">
    <div class="container py-5">
        <div class="text-center mb-5">
            <h1 class="mb-3">My Profile</h1>
            <p class="mb-0">Manage your personal information and cultural exchange preferences</p>
        </div>

        <div class="row">
            <!-- Profile Information -->
            <div class="col-lg-4">
                <div class="card border-0 shadow-sm mb-4">
                    <div class="card-body text-center">
                        <img src="../img/host.jpg" class="rounded-circle mb-3" style="width: 150px; height: 150px;" alt="Host">
                        <h4 class="mb-1"><?= htmlspecialchars($userData['first_name'] . ' ' . $userData['last_name']) ?></h4>
                        <p class="text-muted mb-3"><?= htmlspecialchars($userData['location']) ?></p>
                        <div class="d-flex justify-content-center mb-3">
                            <div class="me-3">
                                <h5 class="mb-0"><?= htmlspecialchars($userData['rating']) ?></h5>
                                <small class="text-muted">Rating</small>
                            </div>
                        </div>
                        <button class="btn btn-primary">Edit Profile</button>
                    </div>
                </div>
            </div>

            <!-- Profile Details -->
            <div class="col-lg-8">
                <div class="card border-0 shadow-sm">
                    <div class="card-body">
                        <ul class="nav nav-tabs" id="profileTabs" role="tablist">
                            <li class="nav-item">
                                <a class="nav-link active" data-bs-toggle="tab" href="#personal">Personal Info</a>
                            </li>
                            <li class="nav-item">
                                <a class="nav-link" data-bs-toggle="tab" href="#preferences">Preferences</a>
                            </li>
                            <li class="nav-item">
                                <a class="nav-link" data-bs-toggle="tab" href="#cultural">Cultural Exchange</a>
                            </li>
                        </ul>

                        <div class="tab-content pt-4" id="profileTabsContent">
                            <!-- Personal Information -->
                            <div class="tab-pane fade show active" id="personal">
                                <form>
                                    <div class="row g-3">
                                        <div class="col-md-6">
                                            <label class="form-label">Full Name</label>
                                            <input type="text" class="form-control" value="<?= htmlspecialchars($userData['first_name'] . ' ' . $userData['last_name']) ?>" readonly>
                                        </div>
                                        <div class="col-md-6">
                                            <label class="form-label">Email</label>
                                            <input type="email" class="form-control" value="<?= htmlspecialchars($userData['email']) ?>" readonly>
                                        </div>
                                        <div class="col-md-6">
                                            <label class="form-label">Phone</label>
                                            <input type="tel" class="form-control" value="<?= htmlspecialchars($userData['phone_number']) ?>" readonly>
                                        </div>
                                        <div class="col-md-6">
                                            <label class="form-label">Location</label>
                                            <input type="text" class="form-control" value="<?= htmlspecialchars($userData['location']) ?>" readonly>
                                        </div>
                                    </div>
                                </form>
                            </div>
                            <!-- Preferences and Cultural Exchange Tabs (you can extend these sections similarly) -->
                            <div class="tab-pane fade" id="preferences">
                                <form>
                                    <div class="row g-3">
                                        <div class="col-md-6">
                                            <label class="form-label">Preferred Languages</label>
                                            <input type="text" class="form-control" value="<?= htmlspecialchars($userData['preferred_language']) ?>" readonly>
                                        </div>
                                    </div>
                                </form>
                            </div>
                        </div>
                        <div class="text-end mt-4">
                            <button class="btn btn-primary" onclick="window.location.href='edit_profile.php'">Edit Profile</button>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
<!-- Profile End -->

<!-- Footer Start -->
<?php include '../Common/footer.php'; ?>
<!-- Footer End -->

<script src="https://code.jquery.com/jquery-3.4.1.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.0.0/dist/js/bootstrap.bundle.min.js"></script>
<script src="../js/main.js"></script>
</body>
</html>
