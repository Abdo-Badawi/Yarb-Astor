<?php
session_start();
// Check if user is logged in and is a host
if (!isset($_SESSION['userID']) || $_SESSION['userType'] !== 'host') {
    header("Location: ../Common/login.php");
    exit;
}

require_once '../Controllers/OpportunityController.php';
require_once '../Controllers/Validation.php';
require_once '../Models/Opportunity.php';
use Models\Opportunity;

// Check if opportunity ID is provided
if (!isset($_GET['id']) || !is_numeric($_GET['id'])) {
    header("Location: opportunities.php");
    exit;
}

$opportunityId = (int)$_GET['id'];
$hostId = $_SESSION['userID'];

// Create opportunity controller
$opportunityController = new OpportunityController();

// Get opportunity details
$opportunity = $opportunityController->getOppById($opportunityId);

// Check if opportunity exists and belongs to the current host
if (!$opportunity || $opportunity['host_id'] != $hostId) {
    header("Location: opportunities.php");
    exit;
}

// Initialize variables
$errMsg = null;
$successMsg = null;

// Check if there are success or error messages in the session (from a redirect)
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
    $imagePath = $opportunity['opportunity_photo']; // Keep existing image by default
    
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
                $imagePath = $fileName; // Just store the filename, not the full path
            } else {
                $errMsg = "Sorry, there was an error uploading your file.";
            }
        } else {
            $errMsg = "Sorry, only JPG, JPEG, PNG & GIF files are allowed.";
        }
    }
    
    // Continue with form processing if no error with file upload
    if (!$errMsg) {
        // Collect form data
        $fields = [
            'title' => $_POST['title'] ?? null,
            'description' => $_POST['description'] ?? null,
            'location' => $_POST['location'] ?? null,
            'start_date' => $_POST['start_date'] ?? null,
            'end_date' => $_POST['end_date'] ?? null,
            'category' => $_POST['category'] ?? null,
            'requirements' => $_POST['requirements'] ?? null,
            'status' => $_POST['status'] ?? $opportunity['status'], // Keep existing status if not provided
        ];

        // Validate form data
        if (!Validation::areFieldsSet($fields) || !Validation::areFieldsNotEmpty($fields)) {
            $errorMessage = "Please fill in all fields correctly.";
            $_SESSION['opportunity_error'] = $errorMessage;
            header("Location: edit-opportunity.php?id=" . $opportunityId);
            exit();
        } else {
            try {
                // Create data array for update
                $startDate = new DateTime($fields['start_date']);
                $endDate = new DateTime($fields['end_date']);
                
                $updateData = [
                    'opportunity_id' => $opportunityId,
                    'title' => $fields['title'],
                    'description' => $fields['description'],
                    'location' => $fields['location'],
                    'start_date' => $startDate->format('Y-m-d'),
                    'end_date' => $endDate->format('Y-m-d'),
                    'category' => $fields['category'],
                    'requirements' => $fields['requirements'],
                    'opportunity_photo' => $imagePath,
                    'status' => $fields['status']
                ];

                // Update opportunity in DB using OpportunityController
                if ($opportunityController->updateOpp($updateData)) {
                    // Success - redirect to opportunities.php with success message
                    $_SESSION['opportunity_success'] = "Opportunity updated successfully.";
                    header("Location: opportunities.php");
                    exit();
                } else {
                    $errorMessage = "Error updating opportunity in database.";
                    $_SESSION['opportunity_error'] = $errorMessage;
                    header("Location: edit-opportunity.php?id=" . $opportunityId);
                    exit();
                }
            } catch (Exception $e) {
                $errorMessage = "Error: " . $e->getMessage();
                $_SESSION['opportunity_error'] = $errorMessage;
                header("Location: edit-opportunity.php?id=" . $opportunityId);
                exit();
            }
        }
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <title>HomeStays - Edit Opportunity</title>
    <meta content="width=device-width, initial-scale=1.0" name="viewport">
    <meta content="homestays, cultural exchange, host opportunities, volunteer management" name="keywords">
    <meta content="Edit your homestay opportunity details" name="description">

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

    <!-- Edit Opportunity Form Start -->
    <div class="container-fluid py-5">
        <div class="container py-5">
            <div class="text-center mb-5">
                <h1 class="mb-3">Edit Opportunity</h1>
                <p class="mb-0">Update your cultural exchange opportunity details</p>
            </div>
            
            <!-- Success and Error Messages -->
            <div class="row justify-content-center mb-4">
                <div class="col-lg-8">
                    <?php if ($errMsg): ?>
                        <div class="alert alert-danger alert-dismissible fade show shadow-sm" role="alert">
                            <div class="d-flex align-items-center">
                                <i class="fas fa-exclamation-circle me-2" style="font-size: 1.25rem;"></i>
                                <strong><?= htmlspecialchars($errMsg) ?></strong>
                            </div>
                            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                        </div>
                    <?php endif; ?>
                    
                    <?php if ($successMsg): ?>
                        <div class="alert alert-success alert-dismissible fade show shadow-sm" role="alert">
                            <div class="d-flex align-items-center">
                                <i class="fas fa-check-circle me-2" style="font-size: 1.25rem;"></i>
                                <strong><?= htmlspecialchars($successMsg) ?></strong>
                            </div>
                            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                        </div>
                    <?php endif; ?>
                </div>
            </div>
            
            <form action="edit-opportunity.php?id=<?php echo $opportunityId; ?>" method="post" id="editOpportunityForm" enctype="multipart/form-data">
                <div class="row g-4 justify-content-center">
                    <div class="col-lg-8">
                        <div class="card border-0 shadow-sm">
                            <div class="card-body p-4">
                                <div class="row">
                                    <div class="col-md-6">
                                        <div class="mb-3">
                                            <label for="opportunityTitle" class="form-label">Opportunity Title</label>
                                            <input type="text" class="form-control" id="opportunityTitle" name="title" value="<?php echo htmlspecialchars($opportunity['title']); ?>" required>
                                        </div>
                                        <div class="mb-3">
                                            <label for="category" class="form-label">Category</label>
                                            <select class="form-select" id="category" name="category" required>
                                                <option value="teaching" <?php echo $opportunity['category'] == 'teaching' ? 'selected' : ''; ?>>Teaching</option>
                                                <option value="farming" <?php echo $opportunity['category'] == 'farming' ? 'selected' : ''; ?>>Farming</option>
                                                <option value="cooking" <?php echo $opportunity['category'] == 'cooking' ? 'selected' : ''; ?>>Cooking</option>
                                                <option value="childcare" <?php echo $opportunity['category'] == 'childcare' ? 'selected' : ''; ?>>Childcare</option>
                                            </select>
                                        </div>
                                        <div class="mb-3">
                                            <label for="status" class="form-label">Status</label>
                                            <select class="form-select" id="status" name="status" required>
                                                <option value="open" <?php echo $opportunity['status'] == 'open' ? 'selected' : ''; ?>>Open</option>
                                                <option value="closed" <?php echo $opportunity['status'] == 'closed' ? 'selected' : ''; ?>>Closed</option>
                                                <option value="cancelled" <?php echo $opportunity['status'] == 'cancelled' ? 'selected' : ''; ?>>Cancelled</option>
                                            </select>
                                        </div>
                                        <div class="row">
                                            <div class="col-md-6 mb-3">
                                                <label for="startDate" class="form-label">Start Date</label>
                                                <input type="date" class="form-control" id="startDate" name="start_date" value="<?php echo htmlspecialchars($opportunity['start_date']); ?>" required>
                                            </div>
                                            <div class="col-md-6 mb-3">
                                                <label for="endDate" class="form-label">End Date</label>
                                                <input type="date" class="form-control" id="endDate" name="end_date" value="<?php echo htmlspecialchars($opportunity['end_date']); ?>" required>
                                            </div>
                                        </div>
                                        <div class="mb-3">
                                            <label for="location" class="form-label">Location</label>
                                            <input type="text" class="form-control" id="location" name="location" value="<?php echo htmlspecialchars($opportunity['location']); ?>" required>
                                        </div>
                                    </div>
                                    <div class="col-md-6">
                                        <div class="mb-3">
                                            <label for="requirements" class="form-label">Requirements</label>
                                            <textarea class="form-control" id="requirements" name="requirements" rows="3" required><?php echo htmlspecialchars($opportunity['requirements']); ?></textarea>
                                        </div>
                                        <div class="mb-3">
                                            <label for="description" class="form-label">Description</label>
                                            <textarea class="form-control" id="description" name="description" rows="5" required><?php echo htmlspecialchars($opportunity['description']); ?></textarea>
                                        </div>
                                        <div class="mb-3">
                                            <label for="image" class="form-label">Opportunity Image</label>
                                            <?php if ($opportunity['opportunity_photo']): ?>
                                            <div class="mb-2 text-center">
                                                <img src="<?php echo '../uploads/opportunities/' . htmlspecialchars($opportunity['opportunity_photo']); ?>" alt="Current Image" class="img-thumbnail" style="max-height: 150px;">
                                                <p class="small text-muted mt-2">Current image. Upload a new one to replace it.</p>
                                            </div>
                                            <?php endif; ?>
                                            <input type="file" class="form-control" id="image" name="image">
                                        </div>
                                    </div>
                                </div>
                                <div class="d-flex justify-content-end mt-3">
                                    <a href="opportunities.php" class="btn btn-secondary me-2">Cancel</a>
                                    <button type="submit" class="btn btn-primary">Update Opportunity</button>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </form>
        </div>
    </div>
    <!-- Edit Opportunity Form End -->

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
        // Date validation
        document.getElementById('editOpportunityForm').addEventListener('submit', function(e) {
            const startDate = new Date(document.getElementById('startDate').value);
            const endDate = new Date(document.getElementById('endDate').value);
            
            if (endDate < startDate) {
                e.preventDefault();
                alert('End date cannot be earlier than start date.');
            }
        });
        
        // Preview image before upload
        document.getElementById('image').addEventListener('change', function() {
            const file = this.files[0];
            if (file) {
                const reader = new FileReader();
                const preview = document.querySelector('.img-thumbnail');
                
                reader.addEventListener('load', function() {
                    if (preview) {
                        preview.src = this.result;
                    } else {
                        const newPreview = document.createElement('img');
                        newPreview.src = this.result;
                        newPreview.classList.add('img-thumbnail');
                        newPreview.style.maxHeight = '150px';
                        document.querySelector('.mb-3:last-child').prepend(newPreview);
                    }
                });
                
                reader.readAsDataURL(file);
            }
        });
    </script>
</body>
</html>




