<?php
session_start();
require_once '../Controllers/OpportunityController.php';

// Check if user is logged in
if (!isset($_SESSION['userID'])) {
    header("Location: ../login.php");
    exit;
}

// Check if opportunity ID is provided
if (!isset($_GET['id']) || !is_numeric($_GET['id'])) {
    header("Location: exchange.php");
    exit;
}

$opportunityId = (int)$_GET['id'];
$travelerID = $_SESSION['userID'];

// Create opportunity controller
$opportunityController = new OpportunityController();

// Get opportunity details
$opportunity = $opportunityController->getOpportunityById($opportunityId);

// Check if opportunity exists
if (!$opportunity) {
    header("Location: exchange.php");
    exit;
}

// Check if traveler has already applied
$hasApplied = $opportunityController->checkIfTravelerApplied($travelerID, $opportunityId);
if ($hasApplied) {
    $_SESSION['error_message'] = "You have already applied for this opportunity.";
    header("Location: exchange.php");
    exit;
}

// Handle form submission
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $applicationData = [
        'traveler_id' => $travelerID,
        'opportunity_id' => $opportunityId,
        'message' => $_POST['message'],
        'availability' => $_POST['availability'],
        'experience' => $_POST['experience'],
        'status' => 'pending',
        'applied_date' => date('Y-m-d H:i:s')
    ];
    
    $result = $opportunityController->applyForOpportunity($applicationData);
    
    if ($result) {
        $_SESSION['success_message'] = "Your application has been submitted successfully!";
        header("Location: exchange.php");
        exit;
    } else {
        $_SESSION['error_message'] = "Failed to submit your application. Please try again.";
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <title>HomeStays - Apply for Opportunity</title>
    <meta content="width=device-width, initial-scale=1.0" name="viewport">
    <meta content="homestays, cultural exchange, local experience, authentic travel" name="keywords">
    <meta content="Apply for this cultural exchange opportunity" name="description">

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
    <?php include 'navTraveler.php'; ?>
    <!-- Navbar End -->
 
    <!-- Application Form Start -->
    <div class="container-fluid py-5">
        <div class="container py-5">
            <div class="mx-auto text-center mb-5" style="max-width: 900px;">
                <h5 class="section-title px-3">Apply for Opportunity</h5>
                <h1 class="mb-0"><?= htmlspecialchars($opportunity['title']) ?></h1>
            </div>
            
            <div class="row justify-content-center">
                <div class="col-lg-8">
                    <div class="card border-0 shadow-sm mb-4">
                        <div class="card-body p-4">
                            <h5 class="card-title mb-3">Opportunity Details</h5>
                            <div class="d-flex align-items-center mb-3">
                                <img src="<?= !empty($opportunity['opportunity_photo']) ? '../uploads/' . htmlspecialchars($opportunity['opportunity_photo']) : '../img/default-opportunity.jpg' ?>" 
                                     class="rounded-circle me-3" style="width: 50px; height: 50px; object-fit: cover;" alt="Host">
                                <div>
                                    <h6 class="mb-0">Category: <?= ucfirst(htmlspecialchars($opportunity['category'])) ?></h6>
                                    <small class="text-muted"><?= htmlspecialchars($opportunity['location']) ?></small>
                                </div>
                            </div>
                            <div class="mb-3">
                                <p class="mb-2"><i class="fa fa-calendar me-2"></i>Duration: <?= date('M d, Y', strtotime($opportunity['start_date'])) ?> - <?= date('M d, Y', strtotime($opportunity['end_date'])) ?></p>
                                <p class="mb-2"><i class="fa fa-tasks me-2"></i>Requirements: <?= htmlspecialchars($opportunity['requirements']) ?></p>
                                <p class="mb-2"><i class="fa fa-info-circle me-2"></i>Description: <?= nl2br(htmlspecialchars($opportunity['description'])) ?></p>
                            </div>
                        </div>
                    </div>
                    
                    <div class="card border-0 shadow-sm">
                        <div class="card-body p-4">
                            <h5 class="card-title mb-4">Your Application</h5>
                            
                            <form action="" method="post">
                                <div class="mb-3">
                                    <label for="message" class="form-label">Why are you interested in this opportunity?</label>
                                    <textarea class="form-control" id="message" name="message" rows="4" required></textarea>
                                    <div class="form-text">Explain why you're a good fit and what you hope to gain from this experience.</div>
                                </div>
                                
                                <div class="mb-3">
                                    <label for="experience" class="form-label">Relevant Experience</label>
                                    <textarea class="form-control" id="experience" name="experience" rows="3" required></textarea>
                                    <div class="form-text">Describe any relevant skills or experience you have related to this opportunity.</div>
                                </div>
                                
                                <div class="mb-3">
                                    <label for="availability" class="form-label">Availability</label>
                                    <textarea class="form-control" id="availability" name="availability" rows="2" required></textarea>
                                    <div class="form-text">Confirm your availability for the dates shown or suggest alternatives.</div>
                                </div>
                                
                                <div class="form-check mb-4">
                                    <input class="form-check-input" type="checkbox" id="termsCheck" required>
                                    <label class="form-check-label" for="termsCheck">
                                        I understand that this is an application only and does not guarantee placement.
                                    </label>
                                </div>
                                
                                <div class="d-flex justify-content-between">
                                    <a href="exchange.php" class="btn btn-outline-secondary">Cancel</a>
                                    <button type="submit" class="btn btn-primary">Submit Application</button>
                                </div>
                            </form>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <!-- Application Form End -->

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
</body>
</html>

