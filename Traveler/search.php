<?php
session_start();
require_once '../Controllers/OpportunityController.php';

// Check if user is logged in and is a traveler
if (!isset($_SESSION['userID']) || $_SESSION['userType'] !== 'traveler') {
    header("Location: ../Common/login.php");
    exit;
}

$travelerID = $_SESSION['userID'];

// Create controllers
$opportunityController = new OpportunityController();

// Initialize filters array
$filters = [];

// Process search form submission
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Collect filter values
    if (!empty($_POST['location'])) {
        $filters['location'] = $_POST['location'];
    }
    
    if (!empty($_POST['category'])) {
        $filters['category'] = $_POST['category'];
    }
    
    if (!empty($_POST['start_date'])) {
        $filters['start_date'] = $_POST['start_date'];
    }
    
    if (!empty($_POST['end_date'])) {
        $filters['end_date'] = $_POST['end_date'];
    }
    
    if (!empty($_POST['accommodation_type'])) {
        $filters['accommodation_type'] = $_POST['accommodation_type'];
    }
    
    if (!empty($_POST['duration_type'])) {
        $filters['duration_type'] = $_POST['duration_type'];
    }
}

// Get search results
$searchResults = $opportunityController->searchOpportunities($filters);

// Get opportunities the traveler has applied to
$appliedOpportunities = $opportunityController->getOppByTravelerID($travelerID);

// Create a lookup array of applied opportunity IDs for easy checking
$appliedOpportunityIds = [];
foreach ($appliedOpportunities as $appliedOpp) {
    $appliedOpportunityIds[$appliedOpp['opportunity_id']] = $appliedOpp['status'];
}

// Get available categories and locations for filter dropdowns
$availableCategories = $opportunityController->getAvailableCategories();
$availableLocations = $opportunityController->getAvailableLocations();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Find Exchange - Cultural Exchange Platform</title>
    <link rel="stylesheet" href="../css/bootstrap.min.css">
    <link rel="stylesheet" href="../css/style.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
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

    <!-- Search Section Start -->
    <div class="container-fluid py-5">
        <div class="container py-5">
            <div class="mx-auto text-center mb-5" style="max-width: 900px;">
                <h5 class="section-title px-3">Find Exchange</h5>
                <h1 class="mb-0">Discover Your Perfect Cultural Exchange Experience</h1>
            </div>

            <!-- Search Form -->
            <div class="row mb-5">
                <div class="col-12">
                    <div class="card border-0 shadow-sm">
                        <div class="card-body p-4">
                            <form method="POST" action="search.php">
                                <div class="row g-3">
                                    <div class="col-md-4">
                                        <label for="location" class="form-label">Location</label>
                                        <input type="text" class="form-control" id="location" name="location" 
                                               placeholder="Enter city or country" 
                                               value="<?= isset($filters['location']) ? htmlspecialchars($filters['location']) : '' ?>">
                                    </div>
                                    <div class="col-md-4">
                                        <label for="category" class="form-label">Category</label>
                                        <select class="form-select" id="category" name="category">
                                            <option value="">All Categories</option>
                                            <option value="teaching" <?= isset($filters['category']) && $filters['category'] === 'teaching' ? 'selected' : '' ?>>Teaching</option>
                                            <option value="farming" <?= isset($filters['category']) && $filters['category'] === 'farming' ? 'selected' : '' ?>>Farming</option>
                                            <option value="cooking" <?= isset($filters['category']) && $filters['category'] === 'cooking' ? 'selected' : '' ?>>Cooking</option>
                                            <option value="childcare" <?= isset($filters['category']) && $filters['category'] === 'childcare' ? 'selected' : '' ?>>Childcare</option>
                                            <option value="housekeeping" <?= isset($filters['category']) && $filters['category'] === 'housekeeping' ? 'selected' : '' ?>>Housekeeping</option>
                                            <option value="gardening" <?= isset($filters['category']) && $filters['category'] === 'gardening' ? 'selected' : '' ?>>Gardening</option>
                                            <option value="language" <?= isset($filters['category']) && $filters['category'] === 'language' ? 'selected' : '' ?>>Language Exchange</option>
                                            <option value="other" <?= isset($filters['category']) && $filters['category'] === 'other' ? 'selected' : '' ?>>Other</option>
                                        </select>
                                    </div>
                                    <div class="col-md-4">
                                        <label for="duration_type" class="form-label">Duration</label>
                                        <select class="form-select" id="duration_type" name="duration_type">
                                            <option value="">Any Duration</option>
                                            <option value="short" <?= isset($filters['duration_type']) && $filters['duration_type'] === 'short' ? 'selected' : '' ?>>Short (1-2 weeks)</option>
                                            <option value="medium" <?= isset($filters['duration_type']) && $filters['duration_type'] === 'medium' ? 'selected' : '' ?>>Medium (2-4 weeks)</option>
                                            <option value="long" <?= isset($filters['duration_type']) && $filters['duration_type'] === 'long' ? 'selected' : '' ?>>Long (1+ months)</option>
                                        </select>
                                    </div>
                                </div>
                                <div class="row g-3 mt-2">
                                    <div class="col-md-4">
                                        <label for="start_date" class="form-label">Start Date</label>
                                        <input type="date" class="form-control" id="start_date" name="start_date" 
                                               value="<?= isset($filters['start_date']) ? htmlspecialchars($filters['start_date']) : '' ?>">
                                    </div>
                                    <div class="col-md-4">
                                        <label for="end_date" class="form-label">End Date</label>
                                        <input type="date" class="form-control" id="end_date" name="end_date" 
                                               value="<?= isset($filters['end_date']) ? htmlspecialchars($filters['end_date']) : '' ?>">
                                    </div>
                                    <div class="col-md-4">
                                        <label for="accommodation_type" class="form-label">Accommodation</label>
                                        <select class="form-select" id="accommodation_type" name="accommodation_type">
                                            <option value="">Any Type</option>
                                            <option value="private" <?= isset($filters['accommodation_type']) && $filters['accommodation_type'] === 'private' ? 'selected' : '' ?>>Private Room</option>
                                            <option value="shared" <?= isset($filters['accommodation_type']) && $filters['accommodation_type'] === 'shared' ? 'selected' : '' ?>>Shared Room</option>
                                            <option value="separate" <?= isset($filters['accommodation_type']) && $filters['accommodation_type'] === 'separate' ? 'selected' : '' ?>>Separate Unit</option>
                                        </select>
                                    </div>
                                </div>
                                <div class="row mt-4">
                                    <div class="col-12 text-center">
                                        <button type="submit" class="btn btn-primary px-5 py-2">Search Exchanges</button>
                                        <a href="search.php" class="btn btn-outline-secondary px-5 py-2 ms-2">Reset Filters</a>
                                    </div>
                                </div>
                            </form>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Search Results -->
            <div class="row mb-3">
                <div class="col-12">
                    <h3>Found <?= count($searchResults) ?> exchange opportunities</h3>
                </div>
            </div>

            <!-- Results Grid -->
            <div class="row g-4">
                <?php if (empty($searchResults)): ?>
                    <div class="col-12 text-center py-5">
                        <div class="alert alert-info">
                            <h4>No exchange opportunities found</h4>
                            <p>Try adjusting your search criteria or check back later for new opportunities.</p>
                        </div>
                    </div>
                <?php else: ?>
                    <?php foreach ($searchResults as $opp): ?>
                        <?php
                            // Skip if traveler has already applied to this opportunity
                            if (isset($appliedOpportunityIds[$opp['opportunity_id']])) {
                                continue;
                            }
                        ?>
                        <div class="col-lg-6">
                            <div class="card border-0 shadow-sm h-100">
                                <div class="card-body p-4">
                                    <div class="d-flex justify-content-between mb-3">
                                        <h5 class="card-title mb-0"><?= htmlspecialchars($opp['title']) ?></h5>
                                        <span class="badge bg-success">Available</span>
                                    </div>
                                    <div class="d-flex align-items-center mb-3">
                                        <img src="<?= !empty($opp['profile_picture']) ? '../uploads/' . htmlspecialchars($opp['profile_picture']) : '../img/default-profile.jpg' ?>"
                                             class="rounded-circle me-3" style="width: 50px; height: 50px; object-fit: cover;" alt="Host">
                                        <div>
                                            <h6 class="mb-0">Host: <?= htmlspecialchars($opp['first_name']) . ' ' . htmlspecialchars($opp['last_name']) ?></h6>
                                            <small class="text-muted"><?= htmlspecialchars($opp['location']) ?></small>
                                        </div>
                                    </div>
                                    <div class="mb-3">
                                        <p class="mb-2"><i class="fa fa-tag me-2"></i>Category: <?= ucfirst(htmlspecialchars($opp['category'])) ?></p>
                                        <p class="mb-2"><i class="fa fa-calendar me-2"></i>Duration: <?= date('M d, Y', strtotime($opp['start_date'])) ?> - <?= date('M d, Y', strtotime($opp['end_date'])) ?></p>
                                        <p class="mb-2"><i class="fa fa-tasks me-2"></i>Requirements: <?= htmlspecialchars($opp['requirements']) ?></p>
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
        </div>
    </div>
    <!-- Search Section End -->

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

    <!-- Template Javascript -->
    <script src="../js/main.js"></script>
</body>
</html> 



