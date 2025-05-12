<?php
session_start();
// Check if user is logged in and is an admin
if (!isset($_SESSION['userID']) || $_SESSION['userType'] !== 'admin') {
    header("Location: ../Common/login.php");
    exit;
}

// Add a session token for additional security
if (!isset($_SESSION['auth_token'])) {
    $_SESSION['auth_token'] = bin2hex(random_bytes(32));
}

// Include the ProfileController to handle profile operations
include_once '../Controllers/profileController.php';

// Get admin data using the ProfileController
$adminData = viewAdminProfile();

// If no admin data found, display an error
if (!$adminData) {
    echo "Error: No admin data found.";
    exit;
}

// Handle profile update
$successMsg = '';
$errorMsg = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['update_profile'])) {
    // Validate CSRF token
    if (!isset($_POST['csrf_token']) || $_POST['csrf_token'] !== $_SESSION['auth_token']) {
        $errorMsg = "Security validation failed. Please try again.";
    } else {
        // Validate and sanitize input
        $firstName = htmlspecialchars(trim($_POST['first_name']));
        $lastName = htmlspecialchars(trim($_POST['last_name']));
        $email = filter_var(trim($_POST['email']), FILTER_SANITIZE_EMAIL);
        $phone = htmlspecialchars(trim($_POST['phone_number']));

        // Validate email
        if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
            $errorMsg = "Invalid email format.";
        } else {
            // Prepare user data for update
            $userData = [
                'first_name' => $firstName,
                'last_name' => $lastName,
                'email' => $email,
                'phone_number' => $phone
            ];
            
            // Update profile using ProfileController
            $result = updateAdminProfile($userData);
            
            if ($result) {
                $successMsg = "Profile updated successfully!";
                
                // Refresh admin data
                $adminData = viewAdminProfile();
            } else {
                $errorMsg = "Failed to update profile. Please try again.";
            }
        }
    }
}

// Handle password change
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['change_password'])) {
    // Validate CSRF token
    if (!isset($_POST['csrf_token']) || $_POST['csrf_token'] !== $_SESSION['auth_token']) {
        $errorMsg = "Security validation failed. Please try again.";
    } else {
        // Validate and sanitize input
        $currentPassword = trim($_POST['current_password']);
        $newPassword = trim($_POST['new_password']);
        $confirmPassword = trim($_POST['confirm_password']);

        // Validate password
        if (empty($currentPassword) || empty($newPassword) || empty($confirmPassword)) {
            $errorMsg = "All password fields are required.";
        } elseif ($newPassword !== $confirmPassword) {
            $errorMsg = "New password and confirm password do not match.";
        } elseif (strlen($newPassword) < 8) {
            $errorMsg = "Password must be at least 8 characters long.";
        } else {
            // Include the DBController to handle database operations
            include_once '../Controllers/DBController.php';
            $db = new Database();
            
            // Verify current password
            if ($db->openConnection()) {
                $query = "SELECT password FROM users WHERE user_id = ? AND user_type = 'admin'";
                $params = [$_SESSION['userID']];
                $result = $db->selectPrepared($query, "i", $params);

                if ($result && count($result) > 0) {
                    $storedPassword = $result[0]['password'];

                    if (password_verify($currentPassword, $storedPassword)) {
                        // Update password
                        $hashedPassword = password_hash($newPassword, PASSWORD_DEFAULT);

                        $updateQuery = "UPDATE users SET password = ? WHERE user_id = ? AND user_type = 'admin'";
                        $updateParams = [$hashedPassword, $_SESSION['userID']];

                        $result = $db->update($updateQuery, "si", $updateParams);

                        if ($result) {
                            $successMsg = "Password changed successfully!";
                        } else {
                            $errorMsg = "Failed to change password. Please try again.";
                        }
                    } else {
                        $errorMsg = "Current password is incorrect.";
                    }
                } else {
                    $errorMsg = "User not found.";
                }

                $db->closeConnection();
            } else {
                $errorMsg = "Database connection failed.";
            }
        }
    }
}
?>

    <!-- Include custom card header styles -->
    <link rel="stylesheet" href="assets/css/card-header-custom.css">
    <style>
        /* Consistent styling with other admin pages - blue background */
        body {
            background-color: #58b4d1 !important;
            min-height: 100vh;
            font-size: 0.9rem !important;
        }

        /* Smaller font size for the entire page */
        .card, .form-control, .btn, .alert, label, p, span, div {
            font-size: 0.9rem;
        }

        /* Readonly inputs styling */
        .form-control[readonly] {
            background-color: #f8f9fa;
            padding: 10px 15px;
            border-color: #e0e0e0;
        }

        /* Main container with blue background */
        .pcoded-main-container {
            background-color: #58b4d1 !important;
            min-height: calc(100vh - 70px);
            padding-bottom: 20px;
        }

        /* Content area with off-white background */
        .pcoded-content {
            background-color: #f4f7fa !important;
            border-radius: 8px;
            margin: 20px;
            padding-bottom: 20px;
             ;
        }

        /* Page header styling - blue background */
        .page-header {
            background-color: #58b4d1 !important;
            padding: 20px 0 !important;
            margin-bottom: 20px !important;
            width: 100% !important;
            border-radius: 8px 8px 0 0;
        }

        .page-header-title h5 {
            color: #fff !important;
            font-weight: 600;
            letter-spacing: 0.5px;
            font-size: 1.1rem;
            margin-bottom: 5px;
        }

        .breadcrumb {
            background: transparent !important;
            padding: 0;
            margin: 0;
        }

        .breadcrumb-item,
        .breadcrumb-item a,
        .breadcrumb-item.active {
            color: rgba(255, 255, 255, 0.8) !important;
            font-size: 0.85rem;
        }

        .breadcrumb-item a:hover {
            color: #fff !important;
            text-decoration: none;
        }

        .breadcrumb-item+.breadcrumb-item::before {
            color: rgba(255, 255, 255, 0.6) !important;
        }

        /* Card styling */
        .card {
            border: none;
            border-radius: 10px;
            box-shadow: 0 4px 20px rgba(0, 0, 0, 0.08);
            transition: transform 0.3s ease, box-shadow 0.3s ease;
            overflow: hidden;
        }

        .card:hover {
            transform: translateY(-5px);
            box-shadow: 0 8px 25px rgba(0, 0, 0, 0.12);
        }

        .card-header {
            background: linear-gradient(to right, #58b4d1, #4ca8c5);
            color: white;
            border-bottom: none;
            padding: 15px 20px;
        }

        .card-header h5 {
            margin: 0;
            font-weight: 600;
            letter-spacing: 0.5px;
            display: flex;
            align-items: center;
        }

        .card-header h5 i {
            margin-right: 10px;
            font-size: 18px;
        }

        .card-body {
            padding: 25px;
        }

        /* Profile styling */
        .profile-header {
            text-align: center;
            margin-bottom: 30px;
            position: relative;
        }

        .profile-img {
            width: 150px;
            height: 150px;
            border-radius: 50%;
            object-fit: cover;
            margin-bottom: 15px;
            border: 4px solid #fff;
            box-shadow: 0 5px 15px rgba(0, 0, 0, 0.15);
            transition: transform 0.3s ease;
        }

        .profile-img:hover {
            transform: scale(1.05);
            box-shadow: 0 8px 20px rgba(0, 0, 0, 0.2);
        }

        .profile-details {
            padding: 15px;
            background-color: white;
            border-radius: 10px;
            box-shadow: 0 2px 10px rgba(0, 0, 0, 0.05);
            height: 100%;
            display: flex;
            flex-direction: column;
            justify-content: center;
        }

        .profile-name {
            font-size: 22px;
            font-weight: 700;
            margin-bottom: 5px;
            color: #333;
        }

        .profile-role {
            font-size: 14px;
            color: #58b4d1;
            text-transform: uppercase;
            margin-bottom: 15px;
            font-weight: 600;
            letter-spacing: 1px;
            background-color: rgba(88, 180, 209, 0.1);
            display: inline-block;
            padding: 4px 12px;
            border-radius: 20px;
        }

        .profile-stats {
            display: flex;
            justify-content: center;
            align-items: center;
            padding: 10px 0;
            margin-top: 15px;
        }

        .stat-item {
            flex: 1;
            margin: 0 5px;
            text-align: center;
            position: relative;
            padding: 8px 10px;
            background-color: #f8f9fa;
            border-radius: 8px;
            box-shadow: 0 2px 8px rgba(0,0,0,0.05);
            transition: all 0.3s ease;
        }

        .stat-item:hover {
            transform: translateY(-3px);
            box-shadow: 0 4px 12px rgba(0,0,0,0.1);
        }

        .stat-value {
            font-size: 16px;
            font-weight: 600;
            color: #333;
            margin-bottom: 3px;
        }

        .stat-label {
            font-size: 12px;
            color: #6c757d;
            text-transform: uppercase;
            letter-spacing: 0.5px;
        }

        .profile-section {
            margin-bottom: 30px;
            background-color: #f8f9fa;
            border-radius: 10px;
            padding: 20px;
        }

        .section-title {
            font-size: 18px;
            font-weight: 600;
            margin-bottom: 20px;
            padding-bottom: 10px;
            border-bottom: 1px solid #e0e0e0;
            color: #333;
            display: flex;
            align-items: center;
        }

        .section-title i {
            margin-right: 10px;
            color: #58b4d1;
        }

        /* Form styling */
        .form-group label {
            font-weight: 600;
            color: #555;
            margin-bottom: 8px;
        }

        .form-control {
            border-radius: 5px;
            border: 1px solid #e0e0e0;
            padding: 10px 15px;
            height: auto;
            transition: border-color 0.3s ease, box-shadow 0.3s ease;
        }

        .form-control:focus {
            border-color: #58b4d1;
            box-shadow: 0 0 0 0.2rem rgba(88, 180, 209, 0.25);
        }

        .btn-primary {
            background-color: #58b4d1;
            border-color: #58b4d1;
            padding: 10px 20px;
            font-weight: 600;
            border-radius: 5px;
            transition: all 0.3s ease;
        }

        .btn-primary:hover {
            background-color: #4ca8c5;
            border-color: #4ca8c5;
            transform: translateY(-2px);
            box-shadow: 0 4px 10px rgba(76, 168, 197, 0.3);
        }

        .alert {
            margin-bottom: 20px;
            border-radius: 5px;
            border: none;
             ;
        }

        .alert-success {
            background-color: #d1f7ea;
            color: #0f5132;
        }

        .alert-danger {
            background-color: #f8d7da;
            color: #842029;
        }

        /* Camera button styling */
        .camera-btn {
            position: absolute;
            bottom: 8px;
            right: 8px;
            background-color: #58b4d1;
            color: white;
            border-radius: 50%;
            width: 35px;
            height: 35px;
            display: flex;
            align-items: center;
            justify-content: center;
            box-shadow: 0 2px 10px rgba(0, 0, 0, 0.2);
            transition: all 0.3s ease;
            border: 2px solid white;
            z-index: 10;
            font-size: 0.85rem;
        }

        .camera-btn:hover {
            background-color: #4ca8c5;
            transform: scale(1.1);
            box-shadow: 0 4px 15px rgba(0, 0, 0, 0.3);
        }

        /* Contact info styling */
        .contact-info {
            display: flex;
            align-items: center;
            margin-bottom: 15px;
            padding: 10px;
            background-color: white;
            border-radius: 5px;
            box-shadow: 0 2px 5px rgba(0, 0, 0, 0.05);
        }

        .contact-info i {
            font-size: 20px;
            color: #58b4d1;
            margin-right: 15px;
            width: 25px;
            text-align: center;
        }

        .contact-info span {
            font-weight: 500;
            color: #555;
        }
    </style>
</head>

<body class="">
    <?php
        include 'header-common.php';
        include 'navCommon.php';
    ?>
    <div class="pcoded-main-container">
        <div class="pcoded-content">
            <!-- [ breadcrumb ] start -->
            <div class="page-header">
                <div class="page-block">
                    <div class="row align-items-center">
                        <div class="col-md-12">
                            <div class="page-header-title">
                                <h5 class="m-b-10">Admin Profile</h5>
                            </div>
                            <ul class="breadcrumb">
                                <li class="breadcrumb-item"><a href="index.php"><i class="feather icon-home"></i></a></li>
                                <li class="breadcrumb-item"><a href="#!">Admin Profile</a></li>
                            </ul>
                        </div>
                    </div>
                </div>
            </div>
            <!-- [ breadcrumb ] end -->

            <!-- [ Main Content ] start -->
            <div class="row">
                <div class="col-sm-12" style="margin-top: 50px;">
                    <?php if (!empty($successMsg)): ?>
                        <div class="alert alert-success alert-dismissible fade show" role="alert">
                            <?php echo $successMsg; ?>
                            <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                                <span aria-hidden="true">&times;</span>
                            </button>
                        </div>
                    <?php endif; ?>

                    <?php if (!empty($errorMsg)): ?>
                        <div class="alert alert-danger alert-dismissible fade show" role="alert">
                            <?php echo $errorMsg; ?>
                            <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                                <span aria-hidden="true">&times;</span>
                            </button>
                        </div>
                    <?php endif; ?>

                    <?php if (isset($_SESSION['profile_success'])): ?>
                        <div class="alert alert-success alert-dismissible fade show" role="alert">
                            <?php echo $_SESSION['profile_success']; unset($_SESSION['profile_success']); ?>
                            <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                                <span aria-hidden="true">&times;</span>
                            </button>
                        </div>
                    <?php endif; ?>

                    <?php if (isset($_SESSION['profile_error'])): ?>
                        <div class="alert alert-danger alert-dismissible fade show" role="alert">
                            <?php echo $_SESSION['profile_error']; unset($_SESSION['profile_error']); ?>
                            <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                                <span aria-hidden="true">&times;</span>
                            </button>
                        </div>
                    <?php endif; ?>
                </div>
            </div>

            <div class="row">
                <!-- Profile Information Card (Left Side) -->
                <div class="col-lg-4 mb-4">
                    <div class="card">
                        <div class="card-header">
                            <h5><i class="feather icon-user"></i>Profile Information</h5>
                        </div>
                        <div class="card-body text-center">
                            <div class="position-relative mb-3">
                                <img src="../Controllers/GetProfileImg.php?user_id=<?= $adminData['user_id'] ?>"
                                     class="profile-img"
                                     alt="Admin Profile Picture">
                                <a href="upload-profile-pic.php" class="camera-btn" title="Change Profile Picture">
                                    <i class="feather icon-camera"></i>
                                </a>
                            </div>

                            <h4 class="profile-name"><?= htmlspecialchars($adminData['first_name'] . ' ' . $adminData['last_name']) ?></h4>
                            <p class="profile-role"><?= htmlspecialchars($adminData['user_type']) ?></p>

                            <div class="profile-stats">
                                <div class="stat-item">
                                    <div class="stat-value"><?= date('d M Y', strtotime($adminData['created_at'])) ?></div>
                                    <div class="stat-label">Joined Date</div>
                                </div>
                                <div class="stat-item">
                                    <div class="stat-value"><?= date('d M Y', strtotime($adminData['last_login'])) ?></div>
                                    <div class="stat-label">Last Login</div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Edit Profile and Change Password Cards (Right Side) -->
                <div class="col-lg-8">
                    <!-- Edit Profile Form -->
                    <div class="card mb-4">
                        <div class="card-header">
                            <h5><i class="feather icon-edit"></i>Edit Profile</h5>
                        </div>
                        <div class="card-body">
                            <form action="admin-profile.php" method="post">
                                <input type="hidden" name="csrf_token" value="<?= $_SESSION['auth_token'] ?>">

                                <div class="row">
                                    <div class="col-md-6">
                                        <div class="form-group">
                                            <label for="first_name">First Name</label>
                                            <div class="input-group">
                                                <div class="input-group-prepend">
                                                    <span class="input-group-text"><i class="feather icon-user"></i></span>
                                                </div>
                                                <input type="text" class="form-control" id="first_name" name="first_name" value="<?= htmlspecialchars($adminData['first_name']) ?>" required>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="col-md-6">
                                        <div class="form-group">
                                            <label for="last_name">Last Name</label>
                                            <div class="input-group">
                                                <div class="input-group-prepend">
                                                    <span class="input-group-text"><i class="feather icon-user"></i></span>
                                                </div>
                                                <input type="text" class="form-control" id="last_name" name="last_name" value="<?= htmlspecialchars($adminData['last_name']) ?>" required>
                                            </div>
                                        </div>
                                    </div>
                                </div>

                                <div class="row">
                                    <div class="col-md-6">
                                        <div class="form-group">
                                            <label for="email">Email</label>
                                            <div class="input-group">
                                                <div class="input-group-prepend">
                                                    <span class="input-group-text"><i class="feather icon-mail"></i></span>
                                                </div>
                                                <input type="email" class="form-control" id="email" name="email" value="<?= htmlspecialchars($adminData['email']) ?>" required>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="col-md-6">
                                        <div class="form-group">
                                            <label for="phone_number">Phone Number</label>
                                            <div class="input-group">
                                                <div class="input-group-prepend">
                                                    <span class="input-group-text"><i class="feather icon-phone"></i></span>
                                                </div>
                                                <input type="text" class="form-control" id="phone_number" name="phone_number" value="<?= htmlspecialchars($adminData['phone_number'] ?? '') ?>">
                                            </div>
                                        </div>
                                    </div>
                                </div>

                                <button type="submit" name="update_profile" class="btn btn-primary">
                                    <i class="feather icon-save mr-2"></i>Update Profile
                                </button>
                            </form>
                        </div>
                    </div>

                    <!-- Change Password Form -->
                    <div class="card">
                        <div class="card-header">
                            <h5><i class="feather icon-lock"></i>Change Password</h5>
                        </div>
                        <div class="card-body">
                            <form action="admin-profile.php" method="post">
                                <input type="hidden" name="csrf_token" value="<?= $_SESSION['auth_token'] ?>">

                                <div class="form-group">
                                    <label for="current_password">Current Password</label>
                                    <div class="input-group">
                                        <div class="input-group-prepend">
                                            <span class="input-group-text"><i class="feather icon-key"></i></span>
                                        </div>
                                        <input type="password" class="form-control" id="current_password" name="current_password" required>
                                        <div class="input-group-append">
                                            <span class="input-group-text toggle-password" toggle="#current_password">
                                                <i class="feather icon-eye"></i>
                                            </span>
                                        </div>
                                    </div>
                                </div>

                                <div class="form-group">
                                    <label for="new_password">New Password</label>
                                    <div class="input-group">
                                        <div class="input-group-prepend">
                                            <span class="input-group-text"><i class="feather icon-lock"></i></span>
                                        </div>
                                        <input type="password" class="form-control" id="new_password" name="new_password" required>
                                        <div class="input-group-append">
                                            <span class="input-group-text toggle-password" toggle="#new_password">
                                                <i class="feather icon-eye"></i>
                                            </span>
                                        </div>
                                    </div>
                                    <small class="form-text text-muted">Password must be at least 8 characters long.</small>
                                </div>

                                <div class="form-group">
                                    <label for="confirm_password">Confirm New Password</label>
                                    <div class="input-group">
                                        <div class="input-group-prepend">
                                            <span class="input-group-text"><i class="feather icon-check-circle"></i></span>
                                        </div>
                                        <input type="password" class="form-control" id="confirm_password" name="confirm_password" required>
                                        <div class="input-group-append">
                                            <span class="input-group-text toggle-password" toggle="#confirm_password">
                                                <i class="feather icon-eye"></i>
                                            </span>
                                        </div>
                                    </div>
                                </div>

                                <button type="submit" name="change_password" class="btn btn-primary">
                                    <i class="feather icon-save mr-2"></i>Change Password
                                </button>
                            </form>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Required Js -->
    <script src="assets/js/vendor-all.min.js"></script>
    <script src="assets/js/plugins/bootstrap.min.js"></script>
    <script src="assets/js/pcoded.min.js"></script>
    <script src="assets/js/custom.js"></script>

    <!-- Custom Scripts -->
    <script>
        // Password toggle functionality
        document.addEventListener('DOMContentLoaded', function() {
            const togglePassword = document.querySelectorAll('.toggle-password');

            togglePassword.forEach(function(element) {
                element.addEventListener('click', function() {
                    const targetId = this.getAttribute('toggle');
                    const passwordInput = document.querySelector(targetId);
                    const icon = this.querySelector('i');

                    // Toggle password visibility
                    if (passwordInput.type === 'password') {
                        passwordInput.type = 'text';
                        icon.classList.remove('icon-eye');
                        icon.classList.add('icon-eye-off');
                    } else {
                        passwordInput.type = 'password';
                        icon.classList.remove('icon-eye-off');
                        icon.classList.add('icon-eye');
                    }
                });
            });

            // Form validation for password change
            const passwordForm = document.querySelector('form[name="change_password"]');
            if (passwordForm) {
                passwordForm.addEventListener('submit', function(event) {
                    const newPassword = document.getElementById('new_password').value;
                    const confirmPassword = document.getElementById('confirm_password').value;

                    if (newPassword !== confirmPassword) {
                        event.preventDefault();
                        alert('New password and confirm password do not match!');
                    }

                    if (newPassword.length < 8) {
                        event.preventDefault();
                        alert('Password must be at least 8 characters long!');
                    }
                });
            }
        });
    </script>
</body>
</html>


