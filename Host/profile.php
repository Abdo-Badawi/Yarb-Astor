<?php
session_start();
if (!isset($_SESSION['userID']) || $_SESSION['userType'] !== 'host') {
    header("Location: ../Common/login.php");
    exit;
}

include_once '../Controllers/ProfileController.php';

$userId = $_SESSION['userID'];

$profileController = new ProfileController();

$userData = $profileController->viewHostProfile();

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
<?php include 'navHost.php'; ?>

<!-- Add this after the header section and before the main content -->
<div class="container mt-4">
    <?php 
    // Check for success or error messages
    $successMessage = isset($_SESSION['success_message']) ? $_SESSION['success_message'] : '';
    $errorMessage = isset($_SESSION['error_message']) ? $_SESSION['error_message'] : '';

    // Clear session messages after displaying
    unset($_SESSION['success_message']);
    unset($_SESSION['error_message']);
    ?>
</div>

<!-- Profile Start -->
<div class="container-fluid py-5">
    <div class="container py-5">
        <div class="text-center mb-4">
            <h1 class="mb-3">My Profile</h1>
            <h6 style="color:#757575" class="mb-0">Manage your personal information and cultural exchange preferences</h6>
        </div>
        
        <!-- Success and Error Messages positioned right after the subtitle -->
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

        <div class="row">
            <!-- Profile Information -->
            <div class="col-lg-4">
                <div class="card border-0 shadow-sm mb-4">
                    <div class="card-body text-center">
                        <!-- Profile Picture Upload -->
                        <form action="upload_profile_pic.php" method="post" enctype="multipart/form-data">
                            <div class="d-flex flex-column align-items-center">
                                <img src="../Controllers/GetProfileImg.php?user_id=<?= $userData['host_id'] ?>"
                                     class="rounded-circle mb-3 img-fluid"
                                     style="width: 150px; height: 150px; object-fit: cover;"
                                     alt="Profile Picture">
                            </div>
                        </form>

                        <h3 class="mb-2 mt-1"><?= htmlspecialchars($userData['first_name'] . ' ' . $userData['last_name']) ?></h3>
                        <h6 style="text-transform: uppercase;" class="text-muted mb-3"><i class="bi bi-person-fill"></i> <?= htmlspecialchars($userData['user_type']) ?></h6>
                        <div class="d-flex justify-content-center mb-3">
                            <div class="me-3">
                                <h5 class="mb-3"><i class="bi bi-stars"></i> <?= htmlspecialchars($userData['rate']) ?></h5>
                                <p class="mb-0" style="text-transform:uppercase;font-weight:bold; color: <?= $userData['status'] === 'active' ? 'green' : ($userData['status'] === 'reported' ? 'red' : 'black') ?>">
                                    <?= htmlspecialchars($userData['status']) ?>
                                </p>
                            </div>
                        </div>
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
                        </ul>

                        <div class="tab-content pt-4" id="profileTabsContent">
                            <!-- Personal Information -->
                            <div class="tab-pane fade show active" id="personal">
                                <form>
                                    <div class="row g-3">
                                        <div class="col-md-6">
                                            <label class="form-label">First Name</label>
                                            <input type="text" class="form-control" value="<?= htmlspecialchars($userData['first_name']) ?>" readonly>
                                        </div>
                                        <div class="col-md-6">
                                            <label class="form-label">Last Name</label>
                                            <input type="text" class="form-control" value="<?= htmlspecialchars($userData['last_name']) ?>" readonly>
                                        </div>
                                        <div class="col-md-6">
                                            <label class="form-label">Email</label>
                                            <input type="email" class="form-control" value="<?= htmlspecialchars($userData['email']) ?>" readonly>
                                        </div>
                                        <div class="col-md-6">
                                            <label class="form-label">Phone Number</label>
                                            <input type="text" class="form-control" value="<?= htmlspecialchars($userData['phone_number']) ?>" readonly>
                                        </div>
                                        <div class="col-md-6">
                                            <label class="form-label">Location</label>
                                            <input type="text" class="form-control" value="<?= isset($userData['location']) ? htmlspecialchars($userData['location']) : '' ?>" readonly>
                                        </div>
                                        <div class="col-md-6">
                                            <label class="form-label">Status</label>
                                            <input type="text" class="form-control" value="<?= isset($userData['status']) ? htmlspecialchars($userData['status']) : '' ?>" readonly>
                                        </div>
                                        <div class="col-md-6">
                                            <label class="form-label">Rating</label>
                                            <input type="text" class="form-control" value="<?= isset($userData['rate']) ? htmlspecialchars($userData['rate']) : 'No ratings yet' ?>" readonly>
                                        </div>
                                        <div class="col-md-6">
                                            <label class="form-label">Joined Date</label>
                                            <input type="text" class="form-control" value="<?= isset($userData['joined_date']) ? htmlspecialchars($userData['joined_date']) : '' ?>" readonly>
                                        </div>
                                    </div>
                                </form>
                            </div>

                            <!-- Preferences -->
                            <div class="tab-pane fade" id="preferences">
                                <form>
                                    <div class="row g-3">
                                        <div class="col-md-6">
                                            <label class="form-label">National ID</label>
                                            <input type="text" class="form-control" value="<?= isset($userData['national_id']) ? htmlspecialchars($userData['national_id']) : '' ?>" readonly>
                                        </div>
                                        <div class="col-md-6">
                                            <label class="form-label">Bio</label>
                                            <input type="text" class="form-control" value="<?= isset($userData['bio']) ? htmlspecialchars($userData['bio']) : '' ?>" readonly>
                                        </div>
                                        <div class="col-md-6">
                                            <label class="form-label">Preferred Languages</label>
                                            <input type="text" class="form-control" value="<?= isset($userData['preferred_language']) ? htmlspecialchars($userData['preferred_language']) : '' ?>" readonly>
                                        </div>
                                        <div class="col-md-6">
                                            <label class="form-label">Property Type</label>
                                            <input type="text" class="form-control" value="<?= isset($userData['property_type']) ? htmlspecialchars($userData['property_type']) : '' ?>" readonly>
                                        </div>
                                    </div>
                                </form>
                            </div>
                        </div>
                        <div class="text-end mt-4">
                            <div class="d-flex justify-content-end gap-2">
                                <a href="messages.php" class="btn btn-info">Messages</a>
                                <a href="edit_profile.php" class="btn btn-primary">Edit Profile</a>

                                <form method="post" action="../Common/logout.php" style="text-align: right; margin-top: 0;">
                                    <button type="submit" class="btn btn-danger">
                                        <i class="fas fa-sign-out-alt me-1"></i> Logout
                                    </button>
                                </form>
                            </div>
                        </div>
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
</body>
</html>





