<?php
session_start();
require_once '../Controllers/OpportunityController.php';
require_once '../Controllers/HostController.php';

// Check if user is logged in
if (!isset($_SESSION['userID'])) {
    header("Location: ../Common/login.php");
    exit;
}

// Check if opportunity ID is provided
if (!isset($_GET['id']) || !is_numeric($_GET['id'])) {
    header("Location: exchange.php");
    exit;
}

$opportunityId = (int)$_GET['id'];
$travelerID = $_SESSION['userID'];

// Create controllers
$opportunityController = new OpportunityController();
// $hostController = new HostController();

// Get opportunity details
$opportunityData = $opportunityController->getOppById($opportunityId);

// Check if opportunity exists
if (!$opportunityData) {
    header("Location: exchange.php");
    exit;
}

// Check if traveler has already applied
$hasApplied = $opportunityController->checkApplied($travelerID, $opportunityId);

// Get host details
$hostData = $hostController->getHostById($opportunityData['host_id']);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <title>HomeStays - Opportunity Details</title>
    <meta content="width=device-width, initial-scale=1.0" name="viewport">
    <!-- Bootstrap CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <!-- Font Awesome -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    <!-- Custom CSS -->
    <link href="../css/style.css" rel="stylesheet">
</head>

<body>
    <!-- Include navbar -->
    <?php include 'navTraveler.php'; ?>

    <div class="container py-5">
        <div class="row">
            <div class="col-lg-8 mx-auto">
                <div class="card border-0 shadow-sm">
                    <div class="card-body p-4">
                        <!-- Opportunity Header -->
                        <div class="d-flex justify-content-between align-items-center mb-4">
                            <h2 class="card-title"><?= htmlspecialchars($opportunityData['title']) ?></h2>
                            <span class="badge bg-<?= $opportunityData['status'] === 'open' ? 'success' : 'secondary' ?>">
                                <?= ucfirst(htmlspecialchars($opportunityData['status'])) ?>
                            </span>
                        </div>
                        
                        <!-- Opportunity Image -->
                        <div class="mb-4 text-center">
                            <img src="<?= !empty($opportunityData['opportunity_photo']) ? '../uploads/' . htmlspecialchars($opportunityData['opportunity_photo']) : '../img/default-opportunity.jpg' ?>" 
                                 class="img-fluid rounded" style="max-height: 400px; object-fit: cover;" alt="Opportunity Image">
                        </div>
                        
                        <!-- Host Information -->
                        <div class="d-flex align-items-center mb-4 p-3 bg-light rounded">
                            <img src="<?= !empty($hostData['profile_photo']) ? '../uploads/' . htmlspecialchars($hostData['profile_photo']) : '../img/default-profile.jpg' ?>" 
                                 class="rounded-circle me-3" style="width: 60px; height: 60px; object-fit: cover;" alt="Host">
                            <div>
                                <h5 class="mb-0">Hosted by <?= htmlspecialchars($hostData['first_name'] . ' ' . $hostData['last_name']) ?></h5>
                                <p class="text-muted mb-0">
                                    <i class="fa fa-map-marker-alt me-1"></i> <?= htmlspecialchars($opportunityData['location']) ?>
                                </p>
                                <a href="view_host.php?id=<?= $opportunityData['host_id'] ?>" class="btn btn-sm btn-outline-primary mt-2">
                                    View Host Profile
                                </a>
                            </div>
                        </div>
                        
                        <!-- Opportunity Details -->
                        <div class="mb-4">
                            <h5 class="border-bottom pb-2 mb-3">Details</h5>
                            <div class="row mb-3">
                                <div class="col-md-6">
                                    <p><i class="fa fa-tag me-2 text-primary"></i> <strong>Category:</strong> <?= ucfirst(htmlspecialchars($opportunityData['category'])) ?></p>
                                </div>
                                <div class="col-md-6">
                                    <p><i class="fa fa-map-marker-alt me-2 text-primary"></i> <strong>Location:</strong> <?= htmlspecialchars($opportunityData['location']) ?></p>
                                </div>
                            </div>
                            <div class="row mb-3">
                                <div class="col-md-6">
                                    <p><i class="fa fa-calendar me-2 text-primary"></i> <strong>Start Date:</strong> <?= date('M d, Y', strtotime($opportunityData['start_date'])) ?></p>
                                </div>
                                <div class="col-md-6">
                                    <p><i class="fa fa-calendar-check me-2 text-primary"></i> <strong>End Date:</strong> <?= date('M d, Y', strtotime($opportunityData['end_date'])) ?></p>
                                </div>
                            </div>
                        </div>
                        
                        <!-- Requirements -->
                        <div class="mb-4">
                            <h5 class="border-bottom pb-2 mb-3">Requirements</h5>
                            <p><?= nl2br(htmlspecialchars($opportunityData['requirements'])) ?></p>
                        </div>
                        
                        <!-- Description -->
                        <div class="mb-4">
                            <h5 class="border-bottom pb-2 mb-3">Description</h5>
                            <p><?= nl2br(htmlspecialchars($opportunityData['description'])) ?></p>
                        </div>
                        
                        <!-- Action Buttons -->
                        <div class="d-flex justify-content-between mt-4">
                            <a href="exchange.php" class="btn btn-outline-secondary">
                                <i class="fa fa-arrow-left me-1"></i> Back to Opportunities
                            </a>
                            <div>
                                <?php if ($opportunityData['status'] === 'open'): ?>
                                    <?php if ($hasApplied): ?>
                                        <button class="btn btn-success me-2" disabled>
                                            <i class="fa fa-check me-1"></i> Already Applied
                                        </button>
                                    <?php else: ?>
                                        <a href="apply_opportunity.php?id=<?= $opportunityId ?>" class="btn btn-primary me-2">
                                            <i class="fa fa-paper-plane me-1"></i> Apply Now
                                        </a>
                                    <?php endif; ?>
                                <?php else: ?>
                                    <button class="btn btn-secondary me-2" disabled>
                                        <i class="fa fa-lock me-1"></i> Not Available
                                    </button>
                                <?php endif; ?>
                                <a href="contact_host.php?id=<?= $opportunityData['host_id'] ?>" class="btn btn-outline-primary">
                                    <i class="fa fa-envelope me-1"></i> Contact Host
                                </a>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    
    <!-- Include footer -->
    <?php include '../Common/footer.php'; ?>

    <!-- Bootstrap JS -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>






