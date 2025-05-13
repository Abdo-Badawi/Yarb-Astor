<?php
require_once '../Controllers/OpportunityController.php';
session_start();
if (!isset($_SESSION['userID']) || $_SESSION['userType'] !== 'traveler') {
    header("Location: ../Common/login.php");
    exit;
}

if (!isset($_SESSION['auth_token'])) {
    $_SESSION['auth_token'] = bin2hex(random_bytes(32));
}
$travelerID = $_SESSION['userID'];

$opportunityController = new OpportunityController();

// Get active opportunities
$activeOpportunities = $opportunityController->getActiveOpp();

// Get applied opportunities to check if user has already applied
$appliedOpportunities = $opportunityController->getOppByTravelerID($travelerID);

// Create a lookup array of applied opportunity IDs for easy checking
$appliedOpportunityIds = [];
foreach ($appliedOpportunities as $appliedOpp) {
    $appliedOpportunityIds[$appliedOpp['opportunity_id']] = $appliedOpp['status'];
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <title>HomeStays - Exchange Opportunities</title>
    <meta content="width=device-width, initial-scale=1.0" name="viewport">
    <link rel="stylesheet" href="https://use.fontawesome.com/releases/v5.15.4/css/all.css"/>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.4.1/font/bootstrap-icons.css" rel="stylesheet">
    <link href="../css/bootstrap.min.css" rel="stylesheet">
    <link href="../css/style.css" rel="stylesheet">
</head>

<body>
    <!-- Include navbar -->
    <?php include 'navTraveler.php'; ?>
    
    <!-- Opportunities Start -->
    <div class="container-fluid py-5">
        <div class="container py-5">
            <div class="text-center mb-4">
                <h1 class="mb-3">Exchange Opportunities</h1>
                <h6 style="color:#757575" class="mb-0">Discover cultural exchange opportunities with hosts around the world</h6>
            </div>

            <!-- Filter Options -->
            <div class="row mb-5">
                <div class="col-lg-12">
                    <div class="card border-0 shadow-sm">
                        <div class="card-body p-4">
                            <h5 class="card-title mb-3" style="color: #13357b;">Filter Opportunities</h5>
                            <div class="row g-3">
                                <!-- Filter options here -->
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Available Opportunities Section -->
            <h3 class="mb-4" style="color: #13357b;">Available Opportunities</h3>
            <div class="row g-4" id="opportunities">
                <?php if (empty($activeOpportunities)): ?>
                    <div class="col-12">
                        <p>No available opportunities found at the moment. Please check back later.</p>
                    </div>
                <?php else: ?>
                    <?php foreach ($activeOpportunities as $opp): ?>
                        <?php
                            // Skip if traveler has already applied to this opportunity
                            if (isset($appliedOpportunityIds[$opp['opportunity_id']])) {
                                continue;
                            }
                        ?>
                        <div class="col-lg-4 col-md-6 mb-4">
                            <div class="card border-0 shadow-sm h-100">
                                <div class="position-relative">
                                    <img src="<?= !empty($opp['opportunity_photo']) ? '../uploads/' . htmlspecialchars($opp['opportunity_photo']) : '../img/default-opportunity.jpg' ?>" 
                                         class="card-img-top" style="height: 200px; object-fit: cover;" alt="Opportunity">
                                    <div class="position-absolute top-0 end-0 m-2">
                                        <span class="badge bg-success"><?= ucfirst(htmlspecialchars($opp['category'])) ?></span>
                                    </div>
                                </div>
                                <div class="card-body p-4">
                                    <h5 class="card-title mb-3" style="color: #13357b;"><?= htmlspecialchars($opp['title']) ?></h5>
                                    <div class="mb-3">
                                        <p class="mb-2"><i class="fa fa-map-marker-alt me-2"></i><?= htmlspecialchars($opp['location']) ?></p>
                                        <p class="mb-2"><i class="fa fa-calendar me-2"></i><?= date('M d, Y', strtotime($opp['start_date'])) ?> - <?= date('M d, Y', strtotime($opp['end_date'])) ?></p>
                                        <p class="mb-2"><i class="fa fa-info-circle me-2"></i>Description: <?= htmlspecialchars(substr($opp['description'], 0, 150)) . (strlen($opp['description']) > 150 ? '...' : '') ?></p>
                                    </div>
                                    <div class="d-flex justify-content-between">
                                        <a href="view_opportunity.php?id=<?= $opp['opportunity_id'] ?>" class="btn btn-outline-primary">View Details</a>
                                        <a href="apply_opportunity.php?id=<?= $opp['opportunity_id'] ?>" class="btn btn-primary">Apply Now</a>
                                    </div>
                                </div>
                            </div>
                        </div>
                    <?php endforeach; ?>
                <?php endif; ?>
            </div>

            <!-- Link to My Applications -->
            <div class="text-center mt-5">
                <p>Want to check your existing applications?</p>
                <a href="my_applications.php" class="btn btn-outline-primary">View My Applications</a>
            </div>
        </div>
    </div>

    <!-- Include footer -->
    <?php include '../Common/footer.php'; ?>

    <!-- JavaScript Libraries -->
    <script src="https://code.jquery.com/jquery-3.4.1.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.0.0/dist/js/bootstrap.bundle.min.js"></script>
    <script src="../lib/wow/wow.min.js"></script>
    <script src="../lib/easing/easing.min.js"></script>
    <script src="../lib/waypoints/waypoints.min.js"></script>
    <script src="../lib/owlcarousel/owl.carousel.min.js"></script>

    <!-- Template Javascript -->
    <script src="../js/main.js"></script>
</body>
</html>



