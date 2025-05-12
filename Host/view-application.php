<?php
session_start();
// Check if user is logged in and is a host
if (!isset($_SESSION['userID']) || $_SESSION['userType'] !== 'host') {
    header("Location: ../Common/login.php");
    exit;
}

require_once '../Controllers/ApplicationController.php';
require_once '../Controllers/MessageController.php';

// Ensure user is logged in and is a host
if (!isset($_SESSION['userID']) || $_SESSION['userType'] !== 'host') {
    header('Location: ../login.php');
    exit;
}

// Check if application ID is provided
if (!isset($_GET['id']) || !is_numeric($_GET['id'])) {
    header('Location: applications.php');
    exit;
}

$applicationId = (int)$_GET['id'];
$hostID = $_SESSION['userID'];

// Initialize controllers
$applicationController = new ApplicationController();
$messageController = new MessageController();

// Get application details
$application = $applicationController->getApplicationByID($applicationId);

// Check if application exists and belongs to the host
if (!$application || $application['host_id'] != $hostID) {
    header('Location: applications.php');
    exit;
}

// Process application status updates if form submitted
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['action'])) {
    $action = $_POST['action'];
    
    if ($action === 'accept') {
        $applicationController->updateApplicationStatus($applicationId, 'accepted');
        
        // Send notification message to traveler
        $messageData = [
            'sender_id' => $hostID,
            'receiver_id' => $application['traveler_id'],
            'content' => "Good news! Your application for '{$application['title']}' has been accepted. Please check your email for further details.",
            'timestamp' => date('Y-m-d H:i:s'),
            'is_read' => 0,
            'sender_type' => 'host',
            'receiver_type' => 'traveler'
        ];
        $messageController->sendMessage($messageData);
        
        $_SESSION['success_message'] = "Application has been accepted successfully.";
    } elseif ($action === 'reject') {
        $applicationController->updateApplicationStatus($applicationId, 'rejected');
        
        // Send notification message to traveler
        $messageData = [
            'sender_id' => $hostID,
            'receiver_id' => $application['traveler_id'],
            'content' => "We regret to inform you that your application for '{$application['title']}' has not been accepted at this time. Thank you for your interest.",
            'timestamp' => date('Y-m-d H:i:s'),
            'is_read' => 0,
            'sender_type' => 'host',
            'receiver_type' => 'traveler'
        ];
        $messageController->sendMessage($messageData);
        
        $_SESSION['success_message'] = "Application has been rejected.";
    }
    
    // Refresh the page to show updated status
    header("Location: view-application.php?id=$applicationId");
    exit;
}

// Calculate traveler age from date of birth
$dob = new DateTime($application['date_of_birth']);
$now = new DateTime();
$age = $now->diff($dob)->y;

// Format dates
$startDate = date('M d, Y', strtotime($application['start_date']));
$endDate = date('M d, Y', strtotime($application['end_date']));
$appliedDate = date('M d, Y', strtotime($application['applied_date']));
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="utf-8">
    <title>HomeStays - Application Details</title>
    <meta content="width=device-width, initial-scale=1.0" name="viewport">
    <meta content="homestays, cultural exchange, volunteer applications, host management" name="keywords">
    <meta content="View detailed application information for your homestay opportunity" name="description">

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

    <!-- Application Details Start -->
    <div class="container-fluid py-5">
        <div class="container py-5">
            <!-- Page Header -->
            <div class="text-center mb-5">
                <h1 class="mb-3">Application Details</h1>
                <p class="mb-0">
                    Reviewing application for: <strong><?php echo htmlspecialchars($application['title']); ?></strong>
                </p>
            </div>

            <!-- Success Message -->
            <?php if (isset($_SESSION['success_message'])): ?>
                <div class="alert alert-success alert-dismissible fade show" role="alert">
                    <?php 
                        echo $_SESSION['success_message'];
                        unset($_SESSION['success_message']);
                    ?>
                    <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                </div>
            <?php endif; ?>

            <!-- Application Status Banner -->
            <div class="row mb-4">
                <div class="col-12">
                    <div class="card border-0 shadow-sm">
                        <div class="card-body d-flex justify-content-between align-items-center">
                            <div>
                                <h5 class="mb-0">Application Status: 
                                    <span class="badge bg-<?php 
                                        echo $application['status'] === 'pending' ? 'warning text-dark' : 
                                            ($application['status'] === 'accepted' ? 'success' : 'danger'); 
                                    ?>">
                                        <?php echo ucfirst(htmlspecialchars($application['status'])); ?>
                                    </span>
                                </h5>
                                <p class="text-muted mb-0">Applied on: <?php echo $appliedDate; ?></p>
                            </div>
                            
                            <?php if ($application['status'] === 'pending'): ?>
                                <div>
                                    <form method="post" class="d-inline">
                                        <input type="hidden" name="action" value="accept">
                                        <button type="submit" class="btn btn-success me-2">
                                            <i class="fas fa-check me-1"></i> Accept Application
                                        </button>
                                    </form>
                                    <form method="post" class="d-inline">
                                        <input type="hidden" name="action" value="reject">
                                        <button type="submit" class="btn btn-danger">
                                            <i class="fas fa-times me-1"></i> Reject Application
                                        </button>
                                    </form>
                                </div>
                            <?php endif; ?>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Main Content -->
            <div class="row g-4">
                <!-- Traveler Profile -->
                <div class="col-lg-4">
                    <div class="card border-0 shadow-sm h-100">
                        <div class="card-header bg-white">
                            <h5 class="mb-0">Traveler Profile</h5>
                        </div>
                        <div class="card-body">
                            <div class="text-center mb-4">
                                <?php if (!empty($application['profile_picture'])): ?>
                                    <img src="../uploads/profiles/<?php echo htmlspecialchars($application['profile_picture']); ?>" 
                                         class="rounded-circle mb-3" style="width: 120px; height: 120px; object-fit: cover;" alt="Profile Picture">
                                <?php else: ?>
                                    <div class="bg-light rounded-circle d-flex align-items-center justify-content-center mx-auto mb-3" 
                                         style="width: 120px; height: 120px;">
                                        <i class="fas fa-user text-primary" style="font-size: 3rem;"></i>
                                    </div>
                                <?php endif; ?>
                                <h4 class="mb-0"><?php echo htmlspecialchars($application['first_name'] . ' ' . $application['last_name']); ?></h4>
                                <p class="text-muted"><?php echo $age; ?> years old, <?php echo htmlspecialchars($application['gender']); ?></p>
                            </div>
                            
                            <div class="mb-4">
                                <h6 class="border-bottom pb-2 mb-3">Contact Information</h6>
                                <p class="mb-2"><i class="fas fa-envelope me-2"></i> <?php echo htmlspecialchars($application['email']); ?></p>
                                <p class="mb-2"><i class="fas fa-phone me-2"></i> <?php echo htmlspecialchars($application['phone_number']); ?></p>
                                <p class="mb-2"><i class="fas fa-map-marker-alt me-2"></i> <?php echo htmlspecialchars($application['traveler_location']); ?></p>
                                <p class="mb-2"><i class="fas fa-language me-2"></i> <?php echo htmlspecialchars($application['language_spoken']); ?></p>
                            </div>
                            
                            <div class="mb-4">
                                <h6 class="border-bottom pb-2 mb-3">About</h6>
                                <p><?php echo htmlspecialchars($application['bio']); ?></p>
                            </div>
                            
                            <div class="mb-4">
                                <h6 class="border-bottom pb-2 mb-3">Skills & Interests</h6>
                                <p class="mb-2"><strong>Skills:</strong> <?php echo htmlspecialchars($application['skills']); ?></p>
                                <p class="mb-2"><strong>Interests:</strong> <?php echo htmlspecialchars($application['interests']); ?></p>
                                <p class="mb-2"><strong>Experience Level:</strong> <?php echo htmlspecialchars($application['experience_level']); ?></p>
                            </div>
                            
                            <div class="text-center mt-4">
                                <a href="messages.php?traveler_id=<?php echo $application['traveler_id']; ?>" class="btn btn-primary">
                                    <i class="fas fa-envelope me-1"></i> Message Traveler
                                </a>
                            </div>
                        </div>
                    </div>
                </div>
                
                <!-- Application Details -->
                <div class="col-lg-8">
                    <div class="card border-0 shadow-sm mb-4">
                        <div class="card-header bg-white">
                            <h5 class="mb-0">Application Details</h5>
                        </div>
                        <div class="card-body">
                            <div class="mb-4">
                                <h6 class="border-bottom pb-2 mb-3">Message from Applicant</h6>
                                <div class="p-3 bg-light rounded">
                                    <p><?php echo nl2br(htmlspecialchars($application['comment'])); ?></p>
                                </div>
                            </div>
                            
                            <div class="mb-4">
                                <h6 class="border-bottom pb-2 mb-3">Availability</h6>
                                <p><?php echo nl2br(htmlspecialchars($application['availability'])); ?></p>
                            </div>
                            
                            <div class="mb-4">
                                <h6 class="border-bottom pb-2 mb-3">Experience</h6>
                                <p><?php echo nl2br(htmlspecialchars($application['experience'])); ?></p>
                            </div>
                        </div>
                    </div>
                    
                    <div class="card border-0 shadow-sm">
                        <div class="card-header bg-white">
                            <h5 class="mb-0">Opportunity Details</h5>
                        </div>
                        <div class="card-body">
                            <div class="row mb-4">
                                <div class="col-md-4">
                                    <?php if (!empty($application['opportunity_photo'])): ?>
                                        <img src="../uploads/opportunities/<?php echo htmlspecialchars($application['opportunity_photo']); ?>" 
                                             class="img-fluid rounded" alt="Opportunity">
                                    <?php else: ?>
                                        <div class="bg-light rounded d-flex align-items-center justify-content-center" 
                                             style="height: 200px;">
                                            <i class="fas fa-home text-primary" style="font-size: 3rem;"></i>
                                        </div>
                                    <?php endif; ?>
                                </div>
                                <div class="col-md-8">
                                    <h4><?php echo htmlspecialchars($application['title']); ?></h4>
                                    <p class="mb-2">
                                        <span class="badge bg-primary"><?php echo ucfirst(htmlspecialchars($application['category'])); ?></span>
                                    </p>
                                    <p class="mb-2"><i class="fas fa-map-marker-alt me-2"></i> <?php echo htmlspecialchars($application['opportunity_location']); ?></p>
                                    <p class="mb-2"><i class="fas fa-calendar me-2"></i> <?php echo $startDate; ?> - <?php echo $endDate; ?></p>
                                </div>
                            </div>
                            
                            <div class="mb-4">
                                <h6 class="border-bottom pb-2 mb-3">Description</h6>
                                <p><?php echo nl2br(htmlspecialchars($application['description'])); ?></p>
                            </div>
                            
                            <div class="mb-4">
                                <h6 class="border-bottom pb-2 mb-3">Requirements</h6>
                                <p><?php echo nl2br(htmlspecialchars($application['requirements'])); ?></p>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            
            <!-- Action Buttons -->
            <div class="row mt-4">
                <div class="col-12 text-center">
                    <a href="applications.php" class="btn btn-outline-primary me-2">
                        <i class="fas fa-arrow-left me-1"></i> Back to Applications
                    </a>
                    
                    <?php if ($application['status'] === 'pending'): ?>
                        <form method="post" class="d-inline">
                            <input type="hidden" name="action" value="accept">
                            <button type="submit" class="btn btn-success me-2">
                                <i class="fas fa-check me-1"></i> Accept Application
                            </button>
                        </form>
                        <form method="post" class="d-inline">
                            <input type="hidden" name="action" value="reject">
                            <button type="submit" class="btn btn-danger">
                                <i class="fas fa-times me-1"></i> Reject Application
                            </button>
                        </form>
                    <?php endif; ?>
                </div>
            </div>
        </div>
    </div>
    <!-- Application Details End -->

    <!-- Footer Start -->
    <?php include '../Common/footer.php'; ?>
    <!-- Footer End -->

    <!-- Back to Top -->
    <a href="#" class="btn btn-lg btn-primary btn-lg-square rounded-circle back-to-top"><i class="bi bi-arrow-up"></i></a>

    <!-- JavaScript Libraries -->
    <script src="https://code.jquery.com/jquery-3.4.1.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.0.0/dist/js/bootstrap.bundle.min.js"></script>
    <script src="../lib/wow/wow.min.js"></script>
    <script src="../lib/easing/easing.min.js"></script>
    <script src="../lib/waypoints/waypoints.min.js"></script>
    <script src="../lib/owlcarousel/owl.carousel.min.js"></script>
    <script src="../lib/counterup/counterup.min.js"></script>
    <script src="../lib/parallax/parallax.min.js"></script>
    <script src="../lib/isotope/isotope.pkgd.min.js"></script>
    <script src="../lib/lightbox/js/lightbox.min.js"></script>

    <!-- Template Javascript -->
    <script src="../js/main.js"></script>
    
    <script>
        // Confirmation for accept/reject actions
        document.addEventListener('DOMContentLoaded', function() {
            const acceptForms = document.querySelectorAll('form[action="accept"]');
            const rejectForms = document.querySelectorAll('form[action="reject"]');
            
            acceptForms.forEach(form => {
                form.addEventListener('submit', function(e) {
                    if (!confirm('Are you sure you want to accept this application?')) {
                        e.preventDefault();
                    }
                });
            });
            
            rejectForms.forEach(form => {
                form.addEventListener('submit', function(e) {
                    if (!confirm('Are you sure you want to reject this application?')) {
                        e.preventDefault();
                    }
                });
            });
        });
    </script>
</body>

</html>



