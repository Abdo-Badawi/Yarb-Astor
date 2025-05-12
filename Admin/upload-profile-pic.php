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

// Include the DBController to handle database operations
include_once '../Controllers/DBController.php';

// Initialize variables
$userId = $_SESSION['userID'];
$successMsg = '';
$errorMsg = '';

// Check if form was submitted
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['upload_profile_pic'])) {
    // Validate CSRF token
    if (!isset($_POST['csrf_token']) || $_POST['csrf_token'] !== $_SESSION['auth_token']) {
        $errorMsg = "Security validation failed. Please try again.";
    } else {
        // Check if file was uploaded without errors
        if (isset($_FILES['profile_picture']) && $_FILES['profile_picture']['error'] === UPLOAD_ERR_OK) {
            $file = $_FILES['profile_picture'];

            // Validate file type
            $allowedTypes = ['image/jpeg', 'image/png', 'image/gif'];
            $fileType = mime_content_type($file['tmp_name']);

            if (!in_array($fileType, $allowedTypes)) {
                $errorMsg = "Invalid file type. Only JPEG, PNG, and GIF images are allowed.";
            } else {
                // Validate file size (max 2MB)
                $maxSize = 2 * 1024 * 1024; // 2MB in bytes

                if ($file['size'] > $maxSize) {
                    $errorMsg = "File size exceeds the maximum limit of 2MB.";
                } else {
                    // Read file content
                    $fileContent = file_get_contents($file['tmp_name']);

                    // Update profile picture in database
                    $db = new DBController();

                    if ($db->openConnection()) {
                        // Prepare the SQL statement
                        $query = "UPDATE users SET profile_picture = ? WHERE user_id = ? AND user_type = 'admin'";

                        // Prepare the statement
                        $stmt = $db->conn->prepare($query);

                        if ($stmt) {
                            // Bind parameters
                            $stmt->bind_param("bi", $fileContent, $userId);

                            // Execute the statement
                            if ($stmt->execute()) {
                                $successMsg = "Profile picture updated successfully!";
                            } else {
                                $errorMsg = "Failed to update profile picture: " . $stmt->error;
                            }

                            // Close the statement
                            $stmt->close();
                        } else {
                            $errorMsg = "Failed to prepare statement: " . $db->conn->error;
                        }

                        $db->closeConnection();
                    } else {
                        $errorMsg = "Database connection failed.";
                    }
                }
            }
        } else {
            $errorMsg = "No file uploaded or an error occurred during upload.";
        }
    }

    // Redirect back to profile page with success or error message
    if (!empty($successMsg)) {
        $_SESSION['profile_success'] = $successMsg;
    } elseif (!empty($errorMsg)) {
        $_SESSION['profile_error'] = $errorMsg;
    }

    header("Location: admin-profile.php");
    exit;
}
?>

<!-- Include the common header -->
<?php include 'header-common.php'; ?>
    <style>
        /* Consistent styling with other admin pages - blue background */
        body {
            background-color: #58b4d1 !important;
            min-height: 100vh;
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
            margin-bottom: 0 !important;
            width: 100% !important;
            border-radius: 8px 8px 0 0;
        }

        .page-header-title h5 {
            color: #fff !important;
            font-weight: 600;
            letter-spacing: 0.5px;
        }

        .breadcrumb {
            background: transparent !important;
        }

        .breadcrumb-item,
        .breadcrumb-item a,
        .breadcrumb-item.active {
            color: #fff !important;
        }

        .breadcrumb-item+.breadcrumb-item::before {
            color: #fff !important;
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

        /* Upload styling */
        .upload-container {
            text-align: center;
            padding: 30px;
        }

        .preview-container {
            margin-bottom: 30px;
            position: relative;
            display: inline-block;
        }

        .preview-image {
            width: 220px;
            height: 220px;
            border-radius: 50%;
            object-fit: cover;
            border: 5px solid #fff;
            box-shadow: 0 5px 15px rgba(0, 0, 0, 0.15);
            transition: all 0.3s ease;
        }

        .preview-image:hover {
            transform: scale(1.05);
            box-shadow: 0 8px 20px rgba(0, 0, 0, 0.2);
        }

        .file-input-container {
            margin-bottom: 25px;
            max-width: 400px;
            margin-left: auto;
            margin-right: auto;
        }

        .custom-file {
            position: relative;
            display: inline-block;
            width: 100%;
            margin-bottom: 15px;
        }

        .custom-file-input {
            position: relative;
            z-index: 2;
            width: 100%;
            height: calc(1.5em + 0.75rem + 2px);
            margin: 0;
            opacity: 0;
        }

        .custom-file-label {
            position: absolute;
            top: 0;
            right: 0;
            left: 0;
            z-index: 1;
            height: calc(1.5em + 0.75rem + 2px);
            padding: 0.375rem 0.75rem;
            font-weight: 400;
            line-height: 1.5;
            color: #495057;
            background-color: #fff;
            border: 1px solid #ced4da;
            border-radius: 0.25rem;
            overflow: hidden;
            text-overflow: ellipsis;
            white-space: nowrap;
            transition: all 0.3s ease;
        }

        .custom-file-label::after {
            position: absolute;
            top: 0;
            right: 0;
            bottom: 0;
            z-index: 3;
            display: block;
            height: calc(1.5em + 0.75rem);
            padding: 0.375rem 0.75rem;
            line-height: 1.5;
            color: #fff;
            content: "Browse";
            background-color: #58b4d1;
            border-left: inherit;
            border-radius: 0 0.25rem 0.25rem 0;
        }

        .form-text {
            display: block;
            margin-top: 8px;
            font-size: 13px;
            color: #6c757d;
        }

        .button-container {
            margin-top: 20px;
        }

        .btn {
            padding: 10px 20px;
            font-weight: 600;
            border-radius: 5px;
            transition: all 0.3s ease;
        }

        .btn-primary {
            background-color: #58b4d1;
            border-color: #58b4d1;
        }

        .btn-primary:hover {
            background-color: #4ca8c5;
            border-color: #4ca8c5;
            transform: translateY(-2px);
            box-shadow: 0 4px 10px rgba(76, 168, 197, 0.3);
        }

        .btn-secondary {
            background-color: #6c757d;
            border-color: #6c757d;
        }

        .btn-secondary:hover {
            background-color: #5a6268;
            border-color: #5a6268;
            transform: translateY(-2px);
            box-shadow: 0 4px 10px rgba(108, 117, 125, 0.3);
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
    </style>
</head>

<body class="">
    <?php include 'navCommon.php'; ?>

    <div class="pcoded-main-container">
        <div class="pcoded-content">
            <div class="page-header">
                <div class="page-block">
                    <div class="row align-items-center">
                        <div class="col-md-12">
                            <div class="page-header-title">
                                <h5 class="m-b-10">Upload Profile Picture</h5>
                            </div>
                            <ul class="breadcrumb">
                                <li class="breadcrumb-item"><a href="index.php"><i class="feather icon-home"></i></a></li>
                                <li class="breadcrumb-item"><a href="admin-profile.php">Admin Profile</a></li>
                                <li class="breadcrumb-item"><a href="#!">Upload Profile Picture</a></li>
                            </ul>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Main Content -->
            <div class="row">
                <div class="col-md-12">
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
                </div>
            </div>

            <div class="row">
                <div class="col-md-8 mx-auto">
                    <div class="card">
                        <div class="card-header">
                            <h5><i class="feather icon-image"></i>Upload Profile Picture</h5>
                        </div>
                        <div class="card-body">
                            <div class="upload-container">
                                <div class="preview-container">
                                    <img id="preview" src="../Controllers/GetProfileImg.php?user_id=<?= $userId ?>" class="preview-image" alt="Profile Picture Preview">
                                </div>

                                <form action="upload-profile-pic.php" method="post" enctype="multipart/form-data">
                                    <input type="hidden" name="csrf_token" value="<?= $_SESSION['auth_token'] ?>">

                                    <div class="file-input-container">
                                        <div class="custom-file">
                                            <input type="file" class="custom-file-input" id="profile_picture" name="profile_picture" accept="image/*" onchange="previewImage(this)">
                                            <label class="custom-file-label" for="profile_picture">Choose file</label>
                                        </div>
                                        <small class="form-text text-muted">
                                            <i class="feather icon-info mr-1"></i>Max file size: 2MB. Allowed formats: JPEG, PNG, GIF.
                                        </small>
                                    </div>

                                    <div class="button-container">
                                        <button type="submit" name="upload_profile_pic" class="btn btn-primary">
                                            <i class="feather icon-upload-cloud mr-2"></i>Upload Picture
                                        </button>
                                        <a href="admin-profile.php" class="btn btn-secondary ml-2">
                                            <i class="feather icon-x mr-2"></i>Cancel
                                        </a>
                                    </div>
                                </form>
                            </div>
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
    <script>
        // Function to preview the selected image
        function previewImage(input) {
            if (input.files && input.files[0]) {
                var reader = new FileReader();

                reader.onload = function(e) {
                    document.getElementById('preview').src = e.target.result;
                }

                reader.readAsDataURL(input.files[0]);

                // Update the file input label with the selected file name
                var fileName = input.files[0].name;
                var label = input.nextElementSibling;
                label.innerHTML = fileName;
            }
        }

        // Update custom file input label with selected file name
        document.querySelector('.custom-file-input').addEventListener('change', function(e) {
            var fileName = e.target.files[0].name;
            var nextSibling = e.target.nextElementSibling;
            nextSibling.innerText = fileName;
        });
    </script>
</body>
</html>
