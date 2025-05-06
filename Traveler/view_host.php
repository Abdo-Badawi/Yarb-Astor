<?php
session_start();
require_once '../Controllers/HostController.php';
require_once '../Controllers/OpportunityController.php';

// Check if user is logged in
if (!isset($_SESSION['userID'])) {
    header("Location: ../Common/login.php");
    exit;
}

// Check if host ID is provided
if (!isset($_GET['id']) || !is_numeric($_GET['id'])) {
    header("Location: exchange.php");
    exit;
}

$hostId = (int)$_GET['id'];
$travelerId = $_SESSION['userID'];

// Create controllers
$hostController = new HostController();
$opportunityController = new OpportunityController();

// Get host details
$hostData = $hostController->getHostById($hostId);

// Check if host exists
if (!$hostData) {
    header("Location: exchange.php");
    exit;
}

// Get host's active opportunities
$hostOpportunities = $opportunityController->getOpportunitiesByHostId($hostId, 'active');

// Get host's reviews
$hostReviews = $hostController->getHostReviews($hostId);

// Calculate average rating
$averageRating = 0;
$totalReviews = count($hostReviews);
if ($totalReviews > 0) {
    $ratingSum = array_sum(array_column($hostReviews, 'rating'));
    $averageRating = $ratingSum / $totalReviews;
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <title>HomeStays - Host Profile</title>
    <meta content="width=device-width, initial-scale=1.0" name="viewport">
    <meta content="homestays, cultural exchange, host profile, volunteer opportunities" name="keywords">
    <meta content="View host profile and available cultural exchange opportunities" name="description">

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

    <!-- Host Profile Start -->
    <div class="container-fluid py-5">
        <div class="container py-5">
            <div class="row g-5">
                <!-- Host Information -->
                <div class="col-lg-4">
                    <div class="card border-0 shadow-sm mb-4">
                        <div class="card-body text-center p-4">
                            <img src="../Controllers/GetProfileImg.php?user_id=<?= $hostId ?>" 
                                 class="rounded-circle mx-auto mb-3" style="width: 150px; height: 150px; object-fit: cover;" alt="Host">
                            <h3 class="mb-1"><?= htmlspecialchars($hostData['first_name'] . ' ' . $hostData['last_name']) ?></h3>
                            <p class="text-muted mb-3">
                                <i class="fas fa-map-marker-alt me-2"></i><?= htmlspecialchars($hostData['location']) ?>
                            </p>
                            
                            <!-- Rating -->
                            <div class="mb-3">
                                <?php for ($i = 1; $i <= 5; $i++): ?>
                                    <?php if ($i <= round($averageRating)): ?>
                                        <i class="fas fa-star text-warning"></i>
                                    <?php else: ?>
                                        <i class="far fa-star text-warning"></i>
                                    <?php endif; ?>
                                <?php endfor; ?>
                                <span class="ms-2">(<?= $totalReviews ?> reviews)</span>
                            </div>
                            
                            <div class="d-grid gap-2">
                                <a href="contact_host.php?id=<?= $hostId ?>" class="btn btn-primary">
                                    <i class="fas fa-envelope me-2"></i>Contact Host
                                </a>
                            </div>
                        </div>
                    </div>
                    
                    <!-- Host Details -->
                    <div class="card border-0 shadow-sm mb-4">
                        <div class="card-body p-4">
                            <h5 class="card-title mb-3">Host Details</h5>
                            
                            <div class="mb-3">
                                <p class="mb-2"><strong><i class="fas fa-language me-2"></i>Languages:</strong></p>
                                <p class="mb-0"><?= htmlspecialchars($hostData['language_spoken'] ?? 'Not specified') ?></p>
                            </div>
                            
                            <div class="mb-3">
                                <p class="mb-2"><strong><i class="fas fa-home me-2"></i>Accommodation:</strong></p>
                                <p class="mb-0"><?= htmlspecialchars($hostData['accommodation_type'] ?? 'Not specified') ?></p>
                            </div>
                            
                            <div class="mb-3">
                                <p class="mb-2"><strong><i class="fas fa-calendar-alt me-2"></i>Member Since:</strong></p>
                                <p class="mb-0"><?= $hostData['joined_date'] ? date('F Y', strtotime($hostData['joined_date'])) : 'Not specified' ?></p>
                            </div>
                        </div>
                    </div>
                </div>
                
                <!-- Host Bio and Opportunities -->
                <div class="col-lg-8">
                    <!-- Host Bio -->
                    <div class="card border-0 shadow-sm mb-4">
                        <div class="card-body p-4">
                            <h5 class="card-title mb-3">About the Host</h5>
                            <p><?= nl2br(htmlspecialchars($hostData['bio'] ?? 'No information provided.')) ?></p>
                        </div>
                    </div>
                    
                    <!-- Host Opportunities -->
                    <div class="card border-0 shadow-sm mb-4">
                        <div class="card-body p-4">
                            <h5 class="card-title mb-3">Available Opportunities</h5>
                            
                            <?php if (empty($hostOpportunities)): ?>
                                <p class="text-muted">No active opportunities available from this host.</p>
                            <?php else: ?>
                                <div class="row g-4">
                                    <?php foreach ($hostOpportunities as $opp): ?>
                                        <div class="col-md-6">
                                            <div class="card h-100">
                                                <div class="card-body">
                                                    <h5 class="card-title"><?= htmlspecialchars($opp['title']) ?></h5>
                                                    <p class="card-text text-muted mb-2">
                                                        <i class="fas fa-map-marker-alt me-1"></i><?= htmlspecialchars($opp['location']) ?>
                                                    </p>
                                                    <p class="card-text text-muted mb-2">
                                                        <i class="fas fa-calendar me-1"></i><?= date('M d, Y', strtotime($opp['start_date'])) ?> - <?= date('M d, Y', strtotime($opp['end_date'])) ?>
                                                    </p>
                                                    <p class="card-text"><?= strlen($opp['description']) > 100 ? htmlspecialchars(substr($opp['description'], 0, 100)) . '...' : htmlspecialchars($opp['description']) ?></p>
                                                    <a href="view_opportunity.php?id=<?= $opp['opportunity_id'] ?>" class="btn btn-sm btn-outline-primary">View Details</a>
                                                </div>
                                            </div>
                                        </div>
                                    <?php endforeach; ?>
                                </div>
                            <?php endif; ?>
                        </div>
                    </div>
                    
                    <!-- Host Reviews -->
                    <div class="card border-0 shadow-sm">
                        <div class="card-body p-4">
                            <h5 class="card-title mb-3">Reviews</h5>
                            
                            <?php if (empty($hostReviews)): ?>
                                <p class="text-muted">No reviews yet for this host.</p>
                            <?php else: ?>
                                <?php foreach ($hostReviews as $review): ?>
                                    <div class="border-bottom pb-3 mb-3">
                                        <div class="d-flex align-items-center mb-2">
                                            <img src="../Controllers/GetProfileImg.php?user_id=<?= $review['traveler_id'] ?>" 
                                                 class="rounded-circle me-2" style="width: 40px; height: 40px; object-fit: cover;" alt="Reviewer">
                                            <div>
                                                <h6 class="mb-0"><?= htmlspecialchars($review['traveler_name']) ?></h6>
                                                <small class="text-muted"><?= date('M d, Y', strtotime($review['review_date'])) ?></small>
                                            </div>
                                            <div class="ms-auto">
                                                <?php for ($i = 1; $i <= 5; $i++): ?>
                                                    <?php if ($i <= $review['rating']): ?>
                                                        <i class="fas fa-star text-warning"></i>
                                                    <?php else: ?>
                                                        <i class="far fa-star text-warning"></i>
                                                    <?php endif; ?>
                                                <?php endfor; ?>
                                            </div>
                                        </div>
                                        <p class="mb-0"><?= nl2br(htmlspecialchars($review['comment'])) ?></p>
                                    </div>
                                <?php endforeach; ?>
                            <?php endif; ?>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <!-- Host Profile End -->

    <!-- Footer Start -->
    <?php include '../Common/footer.php'; ?>
    <!-- Footer End -->

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
</body>
</html>


