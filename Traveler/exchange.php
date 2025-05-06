<?php
session_start();
require_once '../Controllers/OpportunityController.php';

// Check if user is logged in
if (!isset($_SESSION['userID'])) {
    header("Location: ../login.php");
    exit;
}

$travelerID = $_SESSION['userID'];

// Create opportunity controller
$opportunityController = new OpportunityController();

// Get active opportunities
$activeOpportunities = $opportunityController->getActiveOpportunities();

// Get opportunities the traveler has applied to
$appliedOpportunities = $opportunityController->getOpportunitiesByTravelerID($travelerID);

// Create a lookup array of applied opportunity IDs for easy checking
$appliedOpportunityIds = [];
foreach ($appliedOpportunities as $appliedOpp) {
    $appliedOpportunityIds[$appliedOpp['opportunity_id']] = $appliedOpp['status'];
}

// Display success or error messages
$successMessage = $_SESSION['success_message'] ?? null;
$errorMessage = $_SESSION['error_message'] ?? null;

// Clear session messages
unset($_SESSION['success_message']);
unset($_SESSION['error_message']);
?>

<!DOCTYPE html>
<html lang="en">

    <head>
        <meta charset="utf-8">
        <title>HomeStays - Exchange Opportunities</title>
        <meta content="width=device-width, initial-scale=1.0" name="viewport">
        <meta content="homestays, cultural exchange, local experience, authentic travel" name="keywords">
        <meta content="Explore cultural exchange opportunities and service exchanges" name="description">

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
     
        <!-- Exchange Opportunities Start -->
        <div class="container-fluid py-5">
            <div class="container py-5">
                <div class="mx-auto text-center mb-5" style="max-width: 900px;">
                    <h5 class="section-title px-3">Exchange Opportunities</h5>
                    <h1 class="mb-0">Find Your Perfect Cultural Exchange</h1>
                </div>
                
                <!-- Alert Messages -->
                <?php if ($successMessage): ?>
                <div class="alert alert-success alert-dismissible fade show" role="alert">
                    <?= htmlspecialchars($successMessage) ?>
                    <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                </div>
                <?php endif; ?>
                
                <?php if ($errorMessage): ?>
                <div class="alert alert-danger alert-dismissible fade show" role="alert">
                    <?= htmlspecialchars($errorMessage) ?>
                    <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                </div>
                <?php endif; ?>

                <!-- Filters -->
                <div class="row mb-4">
                    <div class="col-12">
                        <div class="card border-0 shadow-sm">
                            <div class="card-body">
                                <div class="row g-3">
                                    <div class="col-md-4">
                                        <select class="form-select" id="categoryFilter">
                                            <option selected value="">All Categories</option>
                                            <option value="teaching">Teaching</option>
                                            <option value="farming">Farming</option>
                                            <option value="cooking">Cooking</option>
                                            <option value="childcare">Childcare</option>
                                        </select>
                                    </div>
                                    <div class="col-md-4">
                                        <input type="text" class="form-control" id="locationFilter" placeholder="Location">
                                    </div>
                                    <div class="col-md-4">
                                        <button class="btn btn-primary w-100" id="applyFilters">Apply Filters</button>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- My Applications Section -->
                <?php if (!empty($appliedOpportunities)): ?>
                <div class="mb-5">
                    <h3 class="mb-4">My Applications</h3>
                    <div class="row g-4" id="appliedOpportunities">
                        <?php foreach ($appliedOpportunities as $opp): ?>
                            <div class="col-lg-6">
                                <div class="card border-0 shadow-sm h-100">
                                    <div class="card-body p-4">
                                        <div class="d-flex justify-content-between mb-3">
                                            <h5 class="card-title mb-0"><?= htmlspecialchars($opp['title']) ?></h5>
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
                                        <div class="d-flex align-items-center mb-3">
                                            <img src="<?= !empty($opp['opportunity_photo']) ? '../uploads/' . htmlspecialchars($opp['opportunity_photo']) : '../img/default-opportunity.jpg' ?>" 
                                                 class="rounded-circle me-3" style="width: 50px; height: 50px; object-fit: cover;" alt="Host">
                                            <div>
                                                <h6 class="mb-0">Category: <?= ucfirst(htmlspecialchars($opp['category'])) ?></h6>
                                                <small class="text-muted"><?= htmlspecialchars($opp['location']) ?></small>
                                            </div>
                                        </div>
                                        <div class="mb-3">
                                            <p class="mb-2"><i class="fa fa-calendar me-2"></i>Duration: <?= date('M d, Y', strtotime($opp['start_date'])) ?> - <?= date('M d, Y', strtotime($opp['end_date'])) ?></p>
                                            <p class="mb-2"><i class="fa fa-tasks me-2"></i>Requirements: <?= htmlspecialchars($opp['requirements']) ?></p>
                                            <p class="mb-2"><i class="fa fa-info-circle me-2"></i>Description: <?= htmlspecialchars($opp['description']) ?></p>
                                        </div>
                                        <div class="d-flex justify-content-between">
                                            <a href="view_opportunity.php?id=<?= $opp['opportunity_id'] ?>" class="btn btn-outline-primary">View Details</a>
                                            <?php if ($opp['status'] === 'accepted'): ?>
                                                <a href="contact_host.php?id=<?= $opp['host_id'] ?>" class="btn btn-primary">Contact Host</a>
                                            <?php endif; ?>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        <?php endforeach; ?>
                    </div>
                </div>
                <?php endif; ?>

                <!-- Available Opportunities Section -->
                <h3 class="mb-4">Available Opportunities</h3>
                <div class="row g-4" id="availableOpportunities">
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
                            <div class="col-lg-6 opportunity-card" 
                                 data-category="<?= htmlspecialchars($opp['category']) ?>" 
                                 data-location="<?= htmlspecialchars($opp['location']) ?>">
                                <div class="card border-0 shadow-sm h-100">
                                    <div class="card-body p-4">
                                        <div class="d-flex justify-content-between mb-3">
                                            <h5 class="card-title mb-0"><?= htmlspecialchars($opp['title']) ?></h5>
                                            <span class="badge bg-success">Available</span>
                                        </div>
                                        <div class="d-flex align-items-center mb-3">
                                            <img src="<?= !empty($opp['opportunity_photo']) ? '../uploads/' . htmlspecialchars($opp['opportunity_photo']) : '../img/default-opportunity.jpg' ?>" 
                                                 class="rounded-circle me-3" style="width: 50px; height: 50px; object-fit: cover;" alt="Host">
                                            <div>
                                                <h6 class="mb-0">Category: <?= ucfirst(htmlspecialchars($opp['category'])) ?></h6>
                                                <small class="text-muted"><?= htmlspecialchars($opp['location']) ?></small>
                                            </div>
                                        </div>
                                        <div class="mb-3">
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

                <!-- Pagination -->
                <div class="pagination mt-5 d-flex justify-content-center">
                    <button id="prevPage" class="btn btn-sm btn-outline-primary me-2">&laquo; Prev</button>
                    <div id="pageNumbers" class="d-flex"></div>
                    <button id="nextPage" class="btn btn-sm btn-outline-primary ms-2">Next &raquo;</button>
                </div>
            </div>
        </div>
        <!-- Exchange Opportunities End -->

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
            document.addEventListener("DOMContentLoaded", function() {
                // Pagination variables
                let currentPage = 1;
                const itemsPerPage = 6;
                let filteredOpportunities = document.querySelectorAll('.opportunity-card');
                
                // Filter functionality
                document.getElementById('applyFilters').addEventListener('click', function() {
                    const categoryFilter = document.getElementById('categoryFilter').value.toLowerCase();
                    const locationFilter = document.getElementById('locationFilter').value.toLowerCase();
                    
                    filteredOpportunities = Array.from(document.querySelectorAll('.opportunity-card')).filter(card => {
                        const category = card.dataset.category.toLowerCase();
                        const location = card.dataset.location.toLowerCase();
                        
                        const categoryMatch = !categoryFilter || category === categoryFilter;
                        const locationMatch = !locationFilter || location.includes(locationFilter);
                        
                        return categoryMatch && locationMatch;
                    });
                    
                    // Reset to first page and update display
                    currentPage = 1;
                    updateDisplay();
                });
                
                // Function to update display based on current page
                function updateDisplay() {
                    const startIndex = (currentPage - 1) * itemsPerPage;
                    const endIndex = startIndex + itemsPerPage;
                    
                    // Hide all cards first
                    document.querySelectorAll('.opportunity-card').forEach(card => {
                        card.style.display = 'none';
                    });
                    
                    // Show only the cards for current page
                    filteredOpportunities.forEach((card, index) => {
                        if (index >= startIndex && index < endIndex) {
                            card.style.display = 'block';
                        }
                    });
                    
                    // Update pagination
                    updatePagination();
                }
                
                // Function to update pagination controls
                function updatePagination() {
                    const totalPages = Math.ceil(filteredOpportunities.length / itemsPerPage);
                    const pageNumbers = document.getElementById('pageNumbers');
                    
                    // Clear existing page numbers
                    pageNumbers.innerHTML = '';
                    
                    // Generate page numbers
                    for (let i = 1; i <= totalPages; i++) {
                        const pageBtn = document.createElement('button');
                        pageBtn.classList.add('btn', 'btn-sm', 'mx-1');
                        pageBtn.classList.add(i === currentPage ? 'btn-primary' : 'btn-outline-primary');
                        pageBtn.textContent = i;
                        pageBtn.addEventListener('click', function() {
                            currentPage = i;
                            updateDisplay();
                        });
                        pageNumbers.appendChild(pageBtn);
                    }
                    
                    // Update prev/next buttons
                    document.getElementById('prevPage').disabled = currentPage === 1;
                    document.getElementById('nextPage').disabled = currentPage === totalPages || totalPages === 0;
                }
                
                // Previous page button
                document.getElementById('prevPage').addEventListener('click', function() {
                    if (currentPage > 1) {
                        currentPage--;
                        updateDisplay();
                    }
                });
                
                // Next page button
                document.getElementById('nextPage').addEventListener('click', function() {
                    const totalPages = Math.ceil(filteredOpportunities.length / itemsPerPage);
                    if (currentPage < totalPages) {
                        currentPage++;
                        updateDisplay();
                    }
                });
                
                // Initialize display
                updateDisplay();
            });
        </script>
    </body>
</html> 

