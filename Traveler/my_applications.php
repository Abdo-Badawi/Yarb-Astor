<?php
session_start();
require_once '../Controllers/OpportunityController.php';

// Check if user is logged in as traveler
if (!isset($_SESSION['userID']) || $_SESSION['userType'] !== 'traveler') {
    header("Location: ../Common/login.php");
    exit;
}

$travelerID = $_SESSION['userID'];

// Create opportunity controller
$opportunityController = new OpportunityController();

// Get all applications for the current traveler
$appliedOpportunities = $opportunityController->getOppByTravelerID($travelerID);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <title>HomeStays - My Applications</title>
    <meta content="width=device-width, initial-scale=1.0" name="viewport">
    <link rel="stylesheet" href="https://use.fontawesome.com/releases/v5.15.4/css/all.css"/>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.4.1/font/bootstrap-icons.css" rel="stylesheet">
    <link href="../css/bootstrap.min.css" rel="stylesheet">
    <link href="../css/style.css" rel="stylesheet">
</head>

<body>
    <!-- Include navbar -->
    <?php include 'navTraveler.php'; ?>

    <!-- Profile Start -->
    <div class="container-fluid py-5">
        <div class="container py-5">
            <div class="text-center mb-4">
                <h1 class="mb-3">My Applications</h1>
                <h6 style="color:#757575" class="mb-0">Track the status of your applications for exchange opportunities</h6>
            </div>

            <!-- Applications List -->
            <div class="row g-4">
                <?php if (empty($appliedOpportunities)): ?>
                    <div class="col-12 text-center">
                        <div class="alert alert-info">
                            <h4>No applications yet</h4>
                            <p>You haven't applied to any opportunities yet. Browse available opportunities to get started!</p>
                            <a href="exchange.php" class="btn btn-primary">Browse Opportunities</a>
                        </div>
                    </div>
                <?php else: ?>
                    <?php foreach ($appliedOpportunities as $opp): ?>
                        <div class="col-lg-6 mb-4">
                            <div class="card border-0 shadow-sm h-100">
                                <div class="card-body p-4">
                                    <div class="d-flex justify-content-between mb-3">
                                        <h5 class="card-title mb-0" style="color: #13357b;"><?= htmlspecialchars($opp['title']) ?></h5>
                                        <?php
                                            $statusClass = '';
                                            switch($opp['status']) {
                                                case 'pending': $statusClass = 'bg-warning text-dark'; break;
                                                case 'accepted': $statusClass = 'bg-success'; break;
                                                case 'rejected': $statusClass = 'bg-danger'; break;
                                                default: $statusClass = 'bg-secondary';
                                            }
                                        ?>
                                        <span class="badge <?= $statusClass ?>"><?= ucfirst(htmlspecialchars($opp['status'])) ?></span>
                                    </div>
                                    
                                    <div class="mb-3">
                                        <p class="mb-2"><i class="fa fa-map-marker-alt me-2"></i><?= htmlspecialchars($opp['location']) ?></p>
                                        <p class="mb-2"><i class="fa fa-calendar me-2"></i><?= date('M d, Y', strtotime($opp['start_date'])) ?> - <?= date('M d, Y', strtotime($opp['end_date'])) ?></p>
                                        <p class="mb-2"><i class="fa fa-tag me-2"></i><?= ucfirst(htmlspecialchars($opp['category'])) ?></p>
                                    </div>
                                    
                                    <div class="mb-3">
                                        <h6 class="border-bottom pb-2 mb-2" style="color: #13357b;">Your Application</h6>
                                        <p class="mb-2"><strong>Applied on:</strong> <?= date('M d, Y', strtotime($opp['applied_date'])) ?></p>
                                        <p class="mb-2"><strong>Message:</strong> <?= htmlspecialchars(substr($opp['message'], 0, 100)) . (strlen($opp['message']) > 100 ? '...' : '') ?></p>
                                    </div>
                                    
                                    <div class="d-flex justify-content-between">
                                        <a href="view_opportunity.php?id=<?= $opp['opportunity_id'] ?>" class="btn btn-outline-primary">View Opportunity</a>
                                        
                                        <?php if ($opp['status'] === 'accepted'): ?>
                                            <a href="contact_host.php?id=<?= $opp['host_id'] ?>" class="btn btn-primary">Contact Host</a>
                                        <?php elseif ($opp['status'] === 'pending'): ?>
                                            <button class="btn btn-outline-secondary" disabled>Awaiting Response</button>
                                        <?php elseif ($opp['status'] === 'rejected'): ?>
                                            <a href="exchange.php" class="btn btn-outline-primary">Find More Opportunities</a>
                                        <?php endif; ?>
                                    </div>
                                </div>
                            </div>
                        </div>
                    <?php endforeach; ?>
                <?php endif; ?>
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


