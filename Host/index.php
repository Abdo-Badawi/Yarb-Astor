<?php
session_start();
// Check if user is logged in and is a host
if (!isset($_SESSION['userID']) || $_SESSION['userType'] !== 'host') {
    header("Location: ../Common/login.php");
    exit;
}

// Add a session token for additional security
if (!isset($_SESSION['auth_token'])) {
    $_SESSION['auth_token'] = bin2hex(random_bytes(32));
}

require_once '../Controllers/DashboardController.php';

$hostID = $_SESSION['userID'];
$dashboardController = new DashboardController();
$dashboardData = $dashboardController->getHostDash($hostID);

// Extract dashboard data
$stats = $dashboardData['stats'];
$recentApplications = $dashboardData['recentApplications'];
$recentMessages = $dashboardData['recentMessages'];
$activeOpportunities = $dashboardData['activeOpportunities'];
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="utf-8">
    <title>HomeStays - Host Dashboard</title>
    <meta content="width=device-width, initial-scale=1.0" name="viewport">
    <meta content="homestays, cultural exchange, host dashboard, volunteer management" name="keywords">
    <meta content="Manage your homestay opportunities and cultural exchange programs" name="description">

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

    <!-- Dashboard Start -->
    <div class="container-fluid py-5">
        <div class="container py-5">
            <div class="text-center mb-5">
                <h1 class="mb-3">Host Dashboard</h1>
                <p class="mb-0">Welcome back! Here's an overview of your homestay activities</p>
            </div>

            <!-- Analytics Overview -->
            <div class="row g-4 mb-4">
                <div class="col-lg-3 col-md-6">
                    <div class="card border-0 shadow-sm">
                        <div class="card-body text-center">
                            <h3 class="text-primary mb-2"><?php echo $stats['profileViews']; ?></h3>
                            <p class="mb-0">Profile Views</p>
                        </div>
                    </div>
                </div>
                <div class="col-lg-3 col-md-6">
                    <div class="card border-0 shadow-sm">
                        <div class="card-body text-center">
                            <h3 class="text-primary mb-2"><?php echo $stats['activeApplications']; ?></h3>
                            <p class="mb-0">Active Applications</p>
                        </div>
                    </div>
                </div>
                <div class="col-lg-3 col-md-6">
                    <div class="card border-0 shadow-sm">
                        <div class="card-body text-center">
                            <h3 class="text-primary mb-2"><?php echo $stats['unreadMessages']; ?></h3>
                            <p class="mb-0">Unread Messages</p>
                        </div>
                    </div>
                </div>
                <div class="col-lg-3 col-md-6">
                    <div class="card border-0 shadow-sm">
                        <div class="card-body text-center">
                            <h3 class="text-primary mb-2"><?php echo $stats['activeOpportunities']; ?></h3>
                            <p class="mb-0">Active Opportunities</p>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Main Dashboard Content -->
            <div class="row g-4">
                <!-- Active Opportunities -->
                <div class="col-lg-6 col-md-12">
                    <div class="card border-0 shadow-sm h-100">
                        <div class="card-header bg-white">
                            <div class="d-flex justify-content-between align-items-center">
                                <h5 class="mb-0">Active Opportunities</h5>
                                <a href="opportunities.php" class="btn btn-sm btn-outline-primary">View All</a>
                            </div>
                        </div>
                        <div class="card-body">
                            <?php if (empty($activeOpportunities)): ?>
                                <p class="text-center">No active opportunities found.</p>
                                <div class="text-center mt-3">
                                    <a href="create-opportunity.php" class="btn btn-primary">Create New Opportunity</a>
                                </div>
                            <?php else: ?>
                                <?php foreach ($activeOpportunities as $opportunity): ?>
                                    <div class="d-flex mb-3 pb-3 border-bottom">
                                        <div class="flex-grow-1">
                                            <h6 class="mb-1"><?php echo htmlspecialchars($opportunity['title']); ?></h6>
                                            <p class="small mb-1">
                                                <i class="fas fa-map-marker-alt me-1"></i> 
                                                <?php echo htmlspecialchars($opportunity['location']); ?>
                                            </p>
                                            <p class="small mb-0">
                                                <i class="fas fa-users me-1"></i> 
                                                <?php echo $opportunity['application_count']; ?> applications
                                            </p>
                                        </div>
                                        <div>
                                            <a href="view-opportunity.php?id=<?php echo $opportunity['opportunity_id']; ?>" class="btn btn-sm btn-outline-primary">
                                                <i class="fas fa-eye"></i>
                                            </a>
                                        </div>
                                    </div>
                                <?php endforeach; ?>
                            <?php endif; ?>
                        </div>
                    </div>
                </div>

                <!-- Recent Applications -->
                <div class="col-lg-6 col-md-12">
                    <div class="card border-0 shadow-sm h-100">
                        <div class="card-header bg-white">
                            <div class="d-flex justify-content-between align-items-center">
                                <h5 class="mb-0">Recent Applications</h5>
                                <a href="applications.php" class="btn btn-sm btn-outline-primary">View All</a>
                            </div>
                        </div>
                        <div class="card-body">
                            <?php if (empty($recentApplications)): ?>
                                <p class="text-center">No recent applications found.</p>
                            <?php else: ?>
                                <?php foreach ($recentApplications as $application): ?>
                                    <div class="d-flex mb-3 pb-3 border-bottom">
                                        <div class="flex-shrink-0">
                                            <?php if (!empty($application['profile_picture'])): ?>
                                                <img src="../uploads/profiles/<?php echo $application['profile_picture']; ?>" class="rounded-circle" width="50" height="50" alt="Applicant">
                                            <?php else: ?>
                                                <div class="bg-light rounded-circle d-flex align-items-center justify-content-center" style="width: 50px; height: 50px;">
                                                    <i class="fas fa-user text-primary"></i>
                                                </div>
                                            <?php endif; ?>
                                        </div>
                                        <div class="ms-3">
                                            <h6 class="mb-1"><?php echo htmlspecialchars($application['first_name'] . ' ' . $application['last_name']); ?></h6>
                                            <p class="small mb-1">Applied for: <?php echo htmlspecialchars($application['opportunity_title']); ?></p>
                                            <p class="small mb-0">
                                                Status: 
                                                <span class="badge <?php echo $application['status'] == 'pending' ? 'bg-warning' : ($application['status'] == 'accepted' ? 'bg-success' : 'bg-danger'); ?>">
                                                    <?php echo ucfirst($application['status']); ?>
                                                </span>
                                            </p>
                                        </div>
                                    </div>
                                <?php endforeach; ?>
                            <?php endif; ?>
                        </div>
                    </div>
                </div>

                <!-- Recent Messages -->
                <div class="col-lg-6 col-md-12">
                    <div class="card border-0 shadow-sm h-100">
                        <div class="card-header bg-white">
                            <div class="d-flex justify-content-between align-items-center">
                                <h5 class="mb-0">Recent Messages</h5>
                                <a href="messages.php" class="btn btn-sm btn-outline-primary">View All</a>
                            </div>
                        </div>
                        <div class="card-body">
                            <?php if (empty($recentMessages)): ?>
                                <p class="text-center">No recent messages found.</p>
                            <?php else: ?>
                                <div class="list-group">
                                    <?php foreach ($recentMessages as $message): ?>
                                        <a href="messages.php?conversation=<?php echo $message['sender_id']; ?>" class="list-group-item list-group-item-action <?php echo $message['is_read'] ? '' : 'bg-light'; ?>">
                                            <div class="d-flex w-100 justify-content-between">
                                                <div class="d-flex align-items-center">
                                                    <?php if (!empty($message['profile_picture'])): ?>
                                                        <img src="../uploads/profiles/<?php echo $message['profile_picture']; ?>" class="rounded-circle me-2" width="40" height="40" alt="Sender">
                                                    <?php else: ?>
                                                        <div class="bg-light rounded-circle d-flex align-items-center justify-content-center me-2" style="width: 40px; height: 40px;">
                                                            <i class="fas fa-user text-primary"></i>
                                                        </div>
                                                    <?php endif; ?>
                                                    <div>
                                                        <h6 class="mb-0"><?php echo htmlspecialchars($message['first_name'] . ' ' . $message['last_name']); ?></h6>
                                                        <p class="mb-0 text-truncate" style="max-width: 200px;"><?php echo htmlspecialchars($message['content']); ?></p>
                                                    </div>
                                                </div>
                                                <small class="text-muted">
                                                    <?php 
                                                    $timestamp = strtotime($message['timestamp']);
                                                    $now = time();
                                                    $diff = $now - $timestamp;
                                                    
                                                    if ($diff < 60) {
                                                        echo "Just now";
                                                    } elseif ($diff < 3600) {
                                                        echo floor($diff / 60) . "m ago";
                                                    } elseif ($diff < 86400) {
                                                        echo floor($diff / 3600) . "h ago";
                                                    } else {
                                                        echo date("M d", $timestamp);
                                                    }
                                                    ?>
                                                </small>
                                            </div>
                                        </a>
                                    <?php endforeach; ?>
                                </div>
                            <?php endif; ?>
                        </div>
                    </div>
                </div>

                <!-- Quick Actions -->
                <div class="col-lg-6 col-md-12">
                    <div class="card border-0 shadow-sm h-100">
                        <div class="card-header bg-white">
                            <h5 class="mb-0">Quick Actions</h5>
                        </div>
                        <div class="card-body">
                            <div class="row g-3">
                                <div class="col-md-6">
                                    <a href="create-opportunity.php" class="btn btn-primary w-100 mb-3">
                                        <i class="fas fa-plus-circle me-2"></i> Create Opportunity
                                    </a>
                                </div>
                                <div class="col-md-6">
                                    <a href="profile.php" class="btn btn-outline-primary w-100 mb-3">
                                        <i class="fas fa-user-edit me-2"></i> Edit Profile
                                    </a>
                                </div>
                                <div class="col-md-6">
                                    <a href="applications.php" class="btn btn-outline-primary w-100 mb-3">
                                        <i class="fas fa-clipboard-list me-2"></i> Review Applications
                                    </a>
                                </div>
                                <div class="col-md-6">
                                    <a href="messages.php" class="btn btn-outline-primary w-100 mb-3">
                                        <i class="fas fa-envelope me-2"></i> Check Messages
                                    </a>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <!-- Dashboard End -->

    <!-- Footer Start -->
    <?php include '../Common/footer.php'; ?>

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

