<?php
session_start();
require_once '../Controllers/ApplicationController.php';

// Ensure user is logged in and is a host
if (!isset($_SESSION['userID']) || $_SESSION['userType'] !== 'host') {
    header('Location: ../login.php');
    exit;
}

// Get the host ID from session
$hostID = $_SESSION['userID'];

// Initialize controller and get applications
$applicationController = new ApplicationController();
$applications = $applicationController->getApplicationByOpportunityID($hostID);

// Process application status updates if form submitted
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['action']) && isset($_POST['application_id'])) {
    $applicationId = (int)$_POST['application_id'];
    $action = $_POST['action'];
    
    if ($action === 'accept') {
        $applicationController->updateApplicationStatus($applicationId, 'accepted');
        $_SESSION['success_message'] = "Application has been accepted successfully.";
    } elseif ($action === 'reject') {
        $applicationController->updateApplicationStatus($applicationId, 'rejected');
        $_SESSION['success_message'] = "Application has been rejected.";
    }
    
    // Redirect to refresh the page
    header('Location: applications.php');
    exit;
}
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="utf-8">
    <title>HomeStays - Applications</title>
    <meta content="width=device-width, initial-scale=1.0" name="viewport">
    <meta content="homestays, cultural exchange, volunteer applications, host management" name="keywords">
    <meta content="Manage applications for your homestay opportunities" name="description">

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

    <!-- Applications Start -->
    <div class="container-fluid py-5">
        <div class="container py-5">
            <!-- Page Header -->
            <div class="text-center mb-5">
                <h1 class="mb-3">Manage Applications</h1>
                <p class="mb-0">Review and respond to applications for your homestay opportunities</p>
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

            <!-- Filter Options -->
            <div class="row mb-4">
                <div class="col-md-6 mb-3 mb-md-0">
                    <div class="input-group">
                        <input type="text" class="form-control" id="searchInput" placeholder="Search applications...">
                        <button class="btn btn-primary" type="button">
                            <i class="fas fa-search"></i>
                        </button>
                    </div>
                </div>
                <div class="col-md-6 d-flex justify-content-md-end">
                    <select class="form-select" id="statusFilter" style="max-width: 200px;">
                        <option value="all">All Applications</option>
                        <option value="pending">Pending</option>
                        <option value="accepted">Accepted</option>
                        <option value="rejected">Rejected</option>
                    </select>
                </div>
            </div>

            <!-- Applications List -->
            <div class="row g-4">
                <?php if (empty($applications)): ?>
                    <div class="col-12">
                        <div class="card border-0 shadow-sm">
                            <div class="card-body text-center py-5">
                                <i class="fas fa-inbox text-muted mb-3" style="font-size: 3rem;"></i>
                                <h4 class="mb-3">No Applications Yet</h4>
                                <p class="mb-4">You haven't received any applications for your opportunities yet.</p>
                                <a href="create-opportunity.php" class="btn btn-primary">Create New Opportunity</a>
                            </div>
                        </div>
                    </div>
                <?php else: ?>
                    <?php foreach ($applications as $application): ?>
                        <div class="col-lg-6 application-item" data-status="<?php echo $application['status']; ?>">
                            <div class="card border-0 shadow-sm h-100">
                                <div class="card-body">
                                    <div class="d-flex align-items-center mb-3">
                                        <?php if (!empty($application['profile_picture'])): ?>
                                            <img src="../uploads/profiles/<?php echo htmlspecialchars($application['profile_picture']); ?>" 
                                                 class="rounded-circle me-3" style="width: 50px; height: 50px; object-fit: cover;" alt="Profile Picture">
                                        <?php else: ?>
                                            <div class="bg-light rounded-circle d-flex align-items-center justify-content-center me-3" 
                                                 style="width: 50px; height: 50px;">
                                                <i class="fas fa-user text-primary"></i>
                                            </div>
                                        <?php endif; ?>
                                        <div>
                                            <h5 class="mb-0"><?php echo htmlspecialchars($application['first_name'] . ' ' . $application['last_name']); ?></h5>
                                            <p class="text-muted mb-0"><?php echo htmlspecialchars($application['traveler_location']); ?></p>
                                        </div>
                                        <span class="ms-auto badge bg-<?php 
                                            echo $application['status'] === 'pending' ? 'warning text-dark' : 
                                                ($application['status'] === 'accepted' ? 'success' : 'danger'); 
                                        ?>">
                                            <?php echo ucfirst(htmlspecialchars($application['status'])); ?>
                                        </span>
                                    </div>
                                    
                                    <div class="mb-3">
                                        <h6 class="border-bottom pb-2 mb-2">Applied For</h6>
                                        <p class="mb-1"><strong><?php echo htmlspecialchars($application['title']); ?></strong></p>
                                        <p class="mb-1">
                                            <span class="badge bg-primary"><?php echo ucfirst(htmlspecialchars($application['category'])); ?></span>
                                            <small class="text-muted ms-2"><?php echo htmlspecialchars($application['opportunity_location']); ?></small>
                                        </p>
                                        <p class="mb-0">
                                            <small class="text-muted">
                                                <i class="fas fa-calendar-alt me-1"></i> 
                                                <?php 
                                                    echo date('M d, Y', strtotime($application['start_date'])) . ' - ' . 
                                                         date('M d, Y', strtotime($application['end_date'])); 
                                                ?>
                                            </small>
                                        </p>
                                    </div>
                                    
                                    <div class="mb-3">
                                        <h6 class="border-bottom pb-2 mb-2">Application Message</h6>
                                        <p class="mb-0 text-truncate"><?php echo htmlspecialchars($application['comment']); ?></p>
                                    </div>
                                    
                                    <div class="d-flex justify-content-between align-items-center">
                                        <small class="text-muted">
                                            <i class="fas fa-clock me-1"></i> 
                                            Applied <?php echo date('M d, Y', strtotime($application['applied_date'])); ?>
                                        </small>
                                        
                                        <div>
                                            <a href="view-application.php?id=<?php echo $application['application_id']; ?>" class="btn btn-sm btn-outline-primary me-2">
                                                <i class="fas fa-eye me-1"></i> View Details
                                            </a>
                                            
                                            <?php if ($application['status'] === 'pending'): ?>
                                                <div class="btn-group">
                                                    <button type="button" class="btn btn-sm btn-primary dropdown-toggle" data-bs-toggle="dropdown" aria-expanded="false">
                                                        Action
                                                    </button>
                                                    <ul class="dropdown-menu dropdown-menu-end">
                                                        <li>
                                                            <form method="post">
                                                                <input type="hidden" name="application_id" value="<?php echo $application['application_id']; ?>">
                                                                <input type="hidden" name="action" value="accept">
                                                                <button type="submit" class="dropdown-item text-success">
                                                                    <i class="fas fa-check me-1"></i> Accept
                                                                </button>
                                                            </form>
                                                        </li>
                                                        <li>
                                                            <form method="post">
                                                                <input type="hidden" name="application_id" value="<?php echo $application['application_id']; ?>">
                                                                <input type="hidden" name="action" value="reject">
                                                                <button type="submit" class="dropdown-item text-danger">
                                                                    <i class="fas fa-times me-1"></i> Reject
                                                                </button>
                                                            </form>
                                                        </li>
                                                    </ul>
                                                </div>
                                            <?php endif; ?>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    <?php endforeach; ?>
                <?php endif; ?>
            </div>
        </div>
    </div>
    <!-- Applications End -->

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
        // Filter applications by status
        document.addEventListener('DOMContentLoaded', function() {
            const statusFilter = document.getElementById('statusFilter');
            const searchInput = document.getElementById('searchInput');
            const applicationItems = document.querySelectorAll('.application-item');
            
            // Status filter
            statusFilter.addEventListener('change', function() {
                const selectedStatus = this.value;
                
                applicationItems.forEach(item => {
                    if (selectedStatus === 'all' || item.dataset.status === selectedStatus) {
                        item.style.display = 'block';
                    } else {
                        item.style.display = 'none';
                    }
                });
            });
            
            // Search filter
            searchInput.addEventListener('input', function() {
                const searchTerm = this.value.toLowerCase();
                
                applicationItems.forEach(item => {
                    const text = item.textContent.toLowerCase();
                    if (text.includes(searchTerm)) {
                        item.style.display = 'block';
                    } else {
                        item.style.display = 'none';
                    }
                });
            });
            
            // Confirmation for accept/reject actions
            const forms = document.querySelectorAll('form');
            forms.forEach(form => {
                form.addEventListener('submit', function(e) {
                    const action = this.querySelector('input[name="action"]').value;
                    const confirmMessage = action === 'accept' ? 
                        'Are you sure you want to accept this application?' : 
                        'Are you sure you want to reject this application?';
                    
                    if (!confirm(confirmMessage)) {
                        e.preventDefault();
                    }
                });
            });
        });
    </script>
</body>

</html>

