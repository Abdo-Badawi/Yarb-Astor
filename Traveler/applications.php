<?php
session_start();
require_once '../Controllers/OpportunityController.php';

// Check if user is logged in and is a traveler
if (!isset($_SESSION['userID']) || $_SESSION['userType'] !== 'traveler') {
    header("Location: ../Common/login.php");
    exit;
}

$travelerID = $_SESSION['userID'];

// Initialize controller
$opportunityController = new OpportunityController();

// Get opportunities the traveler has applied to
$appliedOpportunities = $opportunityController->getOpportunitiesByTravelerID($travelerID);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <title>HomeStays - My Applications</title>
    <meta content="width=device-width, initial-scale=1.0" name="viewport">
    <!-- Bootstrap CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <!-- Font Awesome -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    <!-- Custom CSS -->
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

    <!-- Applications Header -->
    <div class="container-fluid page-header">
        <div class="container">
            <div class="d-flex flex-column align-items-center justify-content-center" style="min-height: 300px">
                <h3 class="display-4 text-white text-uppercase">My Applications</h3>
                <div class="d-inline-flex text-white">
                    <p class="m-0 text-uppercase"><a class="text-white" href="index.php">Home</a></p>
                    <i class="fa fa-angle-double-right pt-1 px-3"></i>
                    <p class="m-0 text-uppercase">Applications</p>
                </div>
            </div>
        </div>
    </div>
    <!-- Applications Header End -->

    <!-- Applications List Start -->
    <div class="container-fluid py-5">
        <div class="container py-5">
            <?php if (isset($_SESSION['success_message'])): ?>
                <div class="alert alert-success alert-dismissible fade show" role="alert">
                    <?= $_SESSION['success_message'] ?>
                    <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                </div>
                <?php unset($_SESSION['success_message']); ?>
            <?php endif; ?>

            <?php if (isset($_SESSION['error_message'])): ?>
                <div class="alert alert-danger alert-dismissible fade show" role="alert">
                    <?= $_SESSION['error_message'] ?>
                    <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                </div>
                <?php unset($_SESSION['error_message']); ?>
            <?php endif; ?>

            <!-- Filter and Search -->
            <div class="row mb-4">
                <div class="col-md-6">
                    <div class="input-group">
                        <input type="text" id="searchApplications" class="form-control" placeholder="Search applications...">
                        <button class="btn btn-primary"><i class="fa fa-search"></i></button>
                    </div>
                </div>
                <div class="col-md-6">
                    <div class="d-flex justify-content-md-end">
                        <select id="statusFilter" class="form-select" style="max-width: 200px;">
                            <option value="all">All Applications</option>
                            <option value="pending">Pending</option>
                            <option value="accepted">Accepted</option>
                            <option value="rejected">Rejected</option>
                        </select>
                    </div>
                </div>
            </div>

            <!-- Applications List -->
            <div class="row g-4" id="applicationsList">
                <?php if (empty($appliedOpportunities)): ?>
                    <div class="col-12">
                        <div class="card border-0 shadow-sm">
                            <div class="card-body text-center py-5">
                                <i class="fas fa-file-alt text-muted mb-3" style="font-size: 3rem;"></i>
                                <h4 class="mb-3">No Applications Yet</h4>
                                <p class="mb-4">You haven't applied to any opportunities yet.</p>
                                <a href="exchange.php" class="btn btn-primary">Browse Opportunities</a>
                            </div>
                        </div>
                    </div>
                <?php else: ?>
                    <?php foreach ($appliedOpportunities as $application): ?>
                        <div class="col-lg-6 application-item" data-status="<?= $application['status'] ?>">
                            <div class="card border-0 shadow-sm h-100">
                                <div class="card-body">
                                    <div class="d-flex justify-content-between mb-3">
                                        <h5 class="card-title"><?= htmlspecialchars($application['title']) ?></h5>
                                        <span class="badge bg-<?php 
                                            echo $application['status'] === 'pending' ? 'warning text-dark' : 
                                                ($application['status'] === 'accepted' ? 'success' : 'danger'); 
                                        ?>">
                                            <?= ucfirst(htmlspecialchars($application['status'])) ?>
                                        </span>
                                    </div>
                                    
                                    <div class="mb-3">
                                        <p><i class="fas fa-map-marker-alt me-2"></i><?= htmlspecialchars($application['location']) ?></p>
                                        <p><i class="fas fa-calendar-alt me-2"></i><?= date('M d, Y', strtotime($application['start_date'])) ?> - <?= date('M d, Y', strtotime($application['end_date'])) ?></p>
                                        <p><i class="fas fa-tag me-2"></i><?= htmlspecialchars($application['category']) ?></p>
                                    </div>
                                    
                                    <div class="mb-3">
                                        <h6 class="border-bottom pb-2 mb-2">Your Application</h6>
                                        <p class="mb-0"><strong>Applied:</strong> <?= date('M d, Y', strtotime($application['applied_date'])) ?></p>
                                    </div>
                                    
                                    <div class="text-end">
                                        <a href="view_opportunity.php?id=<?= $application['opportunity_id'] ?>" class="btn btn-outline-primary">
                                            <i class="fas fa-eye me-1"></i> View Opportunity
                                        </a>
                                    </div>
                                </div>
                            </div>
                        </div>
                    <?php endforeach; ?>
                <?php endif; ?>
            </div>
        </div>
    </div>
    <!-- Applications List End -->

    <!-- Footer Start -->
    <?php include '../Common/footer.php'; ?>
    <!-- Footer End -->

    <!-- Back to Top -->
    <a href="#" class="btn btn-lg btn-primary btn-lg-square back-to-top"><i class="fa fa-angle-double-up"></i></a>

    <!-- JavaScript Libraries -->
    <script src="https://code.jquery.com/jquery-3.4.1.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script src="../lib/easing/easing.min.js"></script>
    <script src="../lib/waypoints/waypoints.min.js"></script>
    <script src="../lib/owlcarousel/owl.carousel.min.js"></script>
    <script src="../lib/tempusdominus/js/moment.min.js"></script>
    <script src="../lib/tempusdominus/js/moment-timezone.min.js"></script>
    <script src="../lib/tempusdominus/js/tempusdominus-bootstrap-4.min.js"></script>

    <!-- Template Javascript -->
    <script src="../js/main.js"></script>
    
    <script>
        $(document).ready(function() {
            // Search functionality
            $("#searchApplications").on("keyup", function() {
                var value = $(this).val().toLowerCase();
                $(".application-item").filter(function() {
                    $(this).toggle($(this).text().toLowerCase().indexOf(value) > -1)
                });
            });
            
            // Status filter
            $("#statusFilter").on("change", function() {
                var value = $(this).val();
                if (value === "all") {
                    $(".application-item").show();
                } else {
                    $(".application-item").hide();
                    $(".application-item[data-status='" + value + "']").show();
                }
            });
            
            // Hide spinner after page load
            setTimeout(function() {
                $("#spinner").removeClass("show");
            }, 500);
        });
    </script>
</body>
</html>
