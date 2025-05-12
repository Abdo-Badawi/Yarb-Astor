<?php
session_start();
// Check if user is logged in and is a host
if (!isset($_SESSION['userID']) || $_SESSION['userType'] !== 'host') {
    header("Location: ../Common/login.php");
    exit;
}

include_once '../Controllers/Validation.php';
include_once '../Controllers/OpportunityController.php';
Validation::session();

$errMsg = null;
$successMsg = null;

if (isset($_SESSION['opportunity_success'])) {
    $successMsg = $_SESSION['opportunity_success'];
    unset($_SESSION['opportunity_success']); // Clear the message after displaying
}

if (isset($_SESSION['opportunity_error'])) {
    $errMsg = $_SESSION['opportunity_error'];
    unset($_SESSION['opportunity_error']); // Clear the message after displaying
}

// Process the form submission
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Handle file upload first
    $targetDir = "../uploads/opportunities/";
    $imagePath = null;

    // Create directory if it doesn't exist
    if (!file_exists($targetDir)) {
        mkdir($targetDir, 0777, true);
    }

    if (isset($_FILES["image"]) && $_FILES["image"]["error"] == 0) {
        $fileName = time() . '_' . basename($_FILES["image"]["name"]);
        $targetFilePath = $targetDir . $fileName;
        $fileType = pathinfo($targetFilePath, PATHINFO_EXTENSION);
        
        // Allow certain file formats
        $allowTypes = array('jpg', 'png', 'jpeg', 'gif');
        if (in_array(strtolower($fileType), $allowTypes)) {
            // Upload file to server
            if (move_uploaded_file($_FILES["image"]["tmp_name"], $targetFilePath)) {
                // Store the path that will be saved to the database
                $imagePath = $fileName; // Just store the filename, not the full path
                error_log("Image uploaded successfully: " . $imagePath);
            } else {
                $errorMessage = "Sorry, there was an error uploading your file.";
                $_SESSION['opportunity_error'] = $errorMessage;
                header("Location: create-opportunity.php");
                exit();
            }
        } else {
            $errorMessage = "Sorry, only JPG, JPEG, PNG & GIF files are allowed.";
            $_SESSION['opportunity_error'] = $errorMessage;
            header("Location: create-opportunity.php");
            exit();
        }
    }
    
    // Continue with form processing
    // Collect form data
    $fields = [
        'title' => $_POST['title'] ?? null,
        'description' => $_POST['description'] ?? null,
        'location' => $_POST['location'] ?? null,
        'start_date' => $_POST['start_date'] ?? null,
        'end_date' => $_POST['end_date'] ?? null,
        'category' => $_POST['category'] ?? null,
        'requirements' => $_POST['requirements'] ?? null,
        'image_path' => $imagePath
    ];

    // Validate form data
    if (!Validation::areFieldsSet($fields) || !Validation::areFieldsNotEmpty($fields)) {
        $errorMessage = "Please fill in all fields correctly.";
        $_SESSION['opportunity_error'] = $errorMessage;
        header("Location: create-opportunity.php");
        exit();
    } else {
        try {
            // Get host ID from session
            $hostId = $_SESSION['userID'] ?? null;
            if (!$hostId) {
                $_SESSION['opportunity_error'] = "You must be logged in to create an opportunity.";
                header("Location: create-opportunity.php");
                exit();
            }
            
            // Add host ID to fields
            $fields['host_id'] = $hostId;
            
            // Use the controller to create the opportunity
            $controller = new OpportunityController();
            if ($controller->createOpportunity($fields)) {
                // Success - redirect to prevent form resubmission
                $_SESSION['opportunity_success'] = "Opportunity created successfully.";
                header("Location: opportunity-success.php");
                exit();
            } else {
                $errorMessage = "Error saving opportunity to database.";
                $_SESSION['opportunity_error'] = $errorMessage;
                header("Location: create-opportunity.php");
                exit();
            }
        } catch (Exception $e) {
            $errorMessage = "Error: " . $e->getMessage();
            $_SESSION['opportunity_error'] = $errorMessage;
            header("Location: create-opportunity.php");
            exit();
        }
    }
}
?>


<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="utf-8">
    <title>HomeStays - Create Opportunity</title>
    <meta content="width=device-width, initial-scale=1.0" name="viewport">
    <meta content="homestays, cultural exchange, create opportunity, host management" name="keywords">
    <meta content="Create a new opportunity for cultural exchange and volunteering" name="description">

    <!-- Google Web Fonts -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Jost:wght@500;600&family=Roboto&display=swap" rel="stylesheet">

    <!-- Icon Font Stylesheet -->
    <link rel="stylesheet" href="https://use.fontawesome.com/releases/v5.15.4/css/all.css"/>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.4.1/font/bootstrap-icons.css" rel="stylesheet">

    <!-- Libraries Stylesheet -->
    <link href="../lib/owlcarousel/assets/owl.carousel.min.css" rel="stylesheet">
    <link href="../lib/lightbox/css/lightbox.min.css" rel="stylesheet">

    <!-- Customized Bootstrap Stylesheet -->
    <link href="../css/bootstrap.min.css" rel="stylesheet">

    <!-- Template Stylesheet -->
    <link href="../css/style.css" rel="stylesheet">
</head>

<body>
    <!-- Spinner Start -->
    <div id="spinner" class="show bg-white position-fixed translate-middle w-100 vh-100 top-50 start-50 d-flex align-items-center justify-content-center">
        <div class="spinner-border text-primary" style="width: 3rem; height: 3rem;" role="status">
            <span class="sr-only">Loading...</span>
        </div>
    </div>
    <!-- Spinner End -->

    <!-- Navbar Start -->
    <?php include 'navHost.php'; ?>
    <!-- Navbar End -->

    <!-- Create Opportunity Form Start -->
    <div class="container-fluid py-5">
        <div class="container p-5">
            <div class="text-center mb-5 p-4">
                <h1 class="mb-3">Create Opportunity</h1>
                <p>Fill out the form below to create a new opportunity for cultural exchange and volunteering.</p>
            </div>
            
            <?php if (isset($errMsg)): ?>
                <div class="alert alert-danger" role="alert">
                    <?php echo $errMsg; ?>
                </div>
            <?php endif; ?>
            
            <?php if (isset($successMsg)): ?>
                <div class="alert alert-success" role="alert">
                    <?php echo $successMsg; ?>
                </div>
            <?php endif; ?>
            
            <form action="create-opportunity.php" method="post" id="createOpportunityForm" enctype="multipart/form-data">
                <div class="row g-4">
                    <div class="col-lg-6">
                        <div class="mb-3">
                            <label for="opportunityTitle" class="form-label">Opportunity Title</label>
                            <input type="text" class="form-control" id="opportunityTitle" name="title" placeholder="Enter title" required>
                        </div>
                        <div class="mb-3">
                            <label for="category" class="form-label">Category</label>
                            <select class="form-select" id="category" name="category" required>
                                <option value="teaching">Teaching</option>
                                <option value="farming">Farming</option>
                                <option value="cooking">Cooking</option>
                                <option value="childcare">Childcare</option>
                            </select>
                        </div>
                        <div class="row">
                            <div class="col-md-6 mb-3">
                                <label for="startDate" class="form-label">Start Date</label>
                                <input type="date" class="form-control" id="startDate" name="start_date" required>
                            </div>
                            <div class="col-md-6 mb-3">
                                <label for="endDate" class="form-label">End Date</label>
                                <input type="date" class="form-control" id="endDate" name="end_date" required>
                            </div>
                        </div>
                        <div class="mb-3">
                            <label for="location" class="form-label">Location</label>
                            <input type="text" class="form-control" id="location" name="location" placeholder="Enter location" required>
                        </div>
                    </div>
                    <div class="col-lg-6">
                        <div class="mb-3">
                            <label for="imageInput" class="form-label fw-bold">Select an image:</label>
                            <input type="file" class="form-control" id="imageInput" name="image" accept="image/*" required>
                        </div>
                        <div class="text-center mb-4">
                            <img id="preview" src="" alt="Image preview will appear here" class="img-fluid rounded shadow-sm mt-3" style="max-height: 300px; display: none; object-fit: contain; width: auto;">
                        </div>

                        <div class="mb-3">
                            <label for="requirements" class="form-label">Requirements</label>
                            <textarea class="form-control" id="requirements" name="requirements" rows="4" placeholder="List any requirements" required></textarea>
                        </div>

                        <div class="mb-3">
                            <label for="description" class="form-label">Description</label>
                            <textarea class="form-control" id="description" name="description" rows="4" placeholder="Describe the opportunity" required></textarea>
                        </div>
                    </div>

                    <div class="col-12 text-end">
                        <button type="submit" class="btn btn-primary">Create Opportunity</button>
                    </div>
                </div>
            </form>
        </div>
    </div>
    <!-- Create Opportunity Form End -->

    <!-- Footer Start -->
    <?php include '../Common/footer.php'; ?>
    <!-- Footer End -->

    <!-- JavaScript Libraries -->
    <script src="https://code.jquery.com/jquery-3.4.1.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.0.0/dist/js/bootstrap.bundle.min.js"></script>
    <script src="../lib/wow/wow.min.js"></script>
    <script src="../lib/easing/easing.min.js"></script>
    <script src="../lib/waypoints/waypoints.min.js"></script>
    <script src="../lib/counterup/counterup.min.js"></script>
    <script src="../lib/owlcarousel/owl.carousel.min.js"></script>

    <!-- Template Javascript -->
    <script src="../js/main.js"></script>
    <script>
        const imageInput = document.getElementById('imageInput');
        const preview = document.getElementById('preview');

        imageInput.addEventListener('change', function () {
            const file = this.files[0];
            if (file) {
                const reader = new FileReader();

                reader.onload = function () {
                    preview.src = reader.result;
                    preview.style.display = 'block';
                };

                reader.readAsDataURL(file);
            }
        });
        
        // Form validation for dates
        document.getElementById('createOpportunityForm').addEventListener('submit', function(e) {
            const startDate = new Date(document.getElementById('startDate').value);
            const endDate = new Date(document.getElementById('endDate').value);
            
            if (endDate < startDate) {
                e.preventDefault();
                alert('End date cannot be before start date');
            }
        });
    </script>
</body>

</html>




