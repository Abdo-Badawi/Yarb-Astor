<?php
session_start();
// Check if user is logged in and is an admin
if (!isset($_SESSION['userID']) || $_SESSION['userType'] !== 'admin') {
    header("Location: ../Common/login.php");
    exit;
}

// Add a session token for additional security
if (!isset($_SESSION['auth_token'])) {
    $_SESSION['auth_token'] = bin2hex(random_bytes(32));
}

// Load the AdminDashboardController
include '../Controllers/AdminDashboardController.php';

// Initialize the controller and get dashboard data
$dashboardController = new AdminDashboardController();
$dashboardData = $dashboardController->getDashboardData();

// Extract dashboard data
$stats = $dashboardData['stats'];
$recentActivity = $dashboardData['recentActivity'];
$pendingReports = $dashboardData['pendingReports'];
$pendingVerifications = $dashboardData['pendingVerifications'];
$recentOpportunities = $dashboardData['recentOpportunities'];

// Include the common header
include 'header-common.php';


include 'navCommon.php'; ?>

<!-- [ Main Content ] start -->
<div class="pcoded-main-container">
    <div class="pcoded-content">
        <!-- [ breadcrumb ] start -->
        <div class="page-header">
               <div class="page-block">
                    <div class="row align-items-center">
                        <div class="col-md-12">
                            <div class="page-header-title">
                                <h5 class="m-b-10">Dashboard</h5>
                            </div>
                            <ul class="breadcrumb">
                                <li class="breadcrumb-item"><a href="index.html"><i class="feather icon-home"></i></a></li>
                                <li class="breadcrumb-item"><a href="#!">Dashboard</a></li>
                            </ul>
                        </div>
                    </div>
                </div>
        </div>
        <!-- [ breadcrumb ] end -->
        <!-- [ Main Content ] start -->
        <div class="row">
            <!-- Statistics Cards -->
            <div class="col-xl-3 col-md-6">
                <div class="stat-card">
                    <div class="stat-icon">
                        <i class="feather icon-users"></i>
                    </div>
                    <div class="stat-title">Total Hosts</div>
                    <div class="stat-value"><?= number_format($stats['totalHosts']) ?></div>
                    <div class="stat-change <?= $stats['hostsGrowth'] >= 0 ? 'text-success' : 'text-danger' ?>">
                        <?= $stats['hostsGrowth'] >= 0 ? '+' : '' ?><?= $stats['hostsGrowth'] ?>% from last month
                    </div>
                </div>
            </div>
            <div class="col-xl-3 col-md-6">
                <div class="stat-card">
                    <div class="stat-icon">
                        <i class="feather icon-user"></i>
                    </div>
                    <div class="stat-title">Total Travelers</div>
                    <div class="stat-value"><?= number_format($stats['totalTravelers']) ?></div>
                    <div class="stat-change <?= $stats['travelersGrowth'] >= 0 ? 'text-success' : 'text-danger' ?>">
                        <?= $stats['travelersGrowth'] >= 0 ? '+' : '' ?><?= $stats['travelersGrowth'] ?>% from last month
                    </div>
                </div>
            </div>
            <div class="col-xl-3 col-md-6">
                <div class="stat-card">
                    <div class="stat-icon">
                        <i class="feather icon-home"></i>
                    </div>
                    <div class="stat-title">Active Homestays</div>
                    <div class="stat-value"><?= number_format($stats['activeHomestays']) ?></div>
                    <div class="stat-change <?= $stats['homestaysGrowth'] >= 0 ? 'text-success' : 'text-danger' ?>">
                        <?= $stats['homestaysGrowth'] >= 0 ? '+' : '' ?><?= $stats['homestaysGrowth'] ?>% from last month
                    </div>
                </div>
            </div>
            <div class="col-xl-3 col-md-6">
                <div class="stat-card">
                    <div class="stat-icon">
                        <i class="feather icon-file-text"></i>
                    </div>
                    <div class="stat-title">Pending Applications</div>
                    <div class="stat-value"><?= number_format($stats['pendingApplications']) ?></div>
                    <div class="stat-change <?= $stats['applicationsGrowth'] >= 0 ? 'text-success' : 'text-danger' ?>">
                        <?= $stats['applicationsGrowth'] >= 0 ? '+' : '' ?><?= $stats['applicationsGrowth'] ?>% from last month
                    </div>
                </div>
            </div>

            <!-- Recent Activity -->
            <div class="col-xl-8 col-md-12">
                <div class="card dashboard-card">
                    <div class="card-header">
                        <h5>Recent Activity</h5>
                    </div>
                    <div class="card-body">
                        <div class="recent-activity">
                            <?php if (empty($recentActivity)): ?>
                                <div class="alert alert-info">No recent activity found.</div>
                            <?php else: ?>
                                <?php foreach ($recentActivity as $activity): ?>
                                    <div class="activity-item d-flex">
                                        <div class="activity-icon">
                                            <?php
                                            // Set icon based on activity type
                                            $icon = 'activity';
                                            switch ($activity['activity_type']) {
                                                case 'login':
                                                    $icon = 'log-in';
                                                    break;
                                                case 'create_opportunity':
                                                    $icon = 'home';
                                                    break;
                                                case 'apply_opportunity':
                                                    $icon = 'file-text';
                                                    break;
                                                case 'view_profile':
                                                    $icon = 'eye';
                                                    break;
                                                case 'update_profile':
                                                    $icon = 'edit';
                                                    break;
                                                case 'payment':
                                                    $icon = 'dollar-sign';
                                                    break;
                                                case 'report':
                                                    $icon = 'alert-triangle';
                                                    break;
                                                case 'review':
                                                    $icon = 'star';
                                                    break;
                                                case 'message':
                                                    $icon = 'message-square';
                                                    break;
                                            }
                                            ?>
                                            <i class="feather icon-<?= $icon ?>"></i>
                                        </div>
                                        <div class="activity-content">
                                            <div class="activity-title">
                                                <?php
                                                // Format activity title
                                                $userName = isset($activity['first_name']) ?
                                                    htmlspecialchars($activity['first_name'] . ' ' . $activity['last_name']) :
                                                    'User';
                                                $userType = isset($activity['user_type']) ?
                                                    ucfirst(htmlspecialchars($activity['user_type'])) :
                                                    'User';

                                                $activityTitle = '';
                                                switch ($activity['activity_type']) {
                                                    case 'login':
                                                        $activityTitle = "$userType Login: $userName";
                                                        break;
                                                    case 'create_opportunity':
                                                        $activityTitle = "New Opportunity: " . htmlspecialchars($activity['activity_details'] ?? 'Created');
                                                        break;
                                                    case 'apply_opportunity':
                                                        $activityTitle = "New Application: $userName";
                                                        break;
                                                    case 'view_profile':
                                                        $activityTitle = "Profile Viewed: " . htmlspecialchars($activity['activity_details'] ?? '');
                                                        break;
                                                    case 'update_profile':
                                                        $activityTitle = "Profile Updated: $userName";
                                                        break;
                                                    case 'payment':
                                                        $activityTitle = "Payment: " . htmlspecialchars($activity['activity_details'] ?? '');
                                                        break;
                                                    case 'report':
                                                        $activityTitle = "New Report Filed";
                                                        break;
                                                    case 'review':
                                                        $activityTitle = "New Review Posted";
                                                        break;
                                                    case 'message':
                                                        $activityTitle = "New Message: $userName";
                                                        break;
                                                    default:
                                                        $activityTitle = ucwords(str_replace('_', ' ', $activity['activity_type']));
                                                }
                                                echo htmlspecialchars($activityTitle);
                                                ?>
                                            </div>
                                            <div class="activity-time">
                                                <?php
                                                // Format time difference
                                                $activityTime = strtotime($activity['created_at']);
                                                $now = time();
                                                $diff = $now - $activityTime;

                                                if ($diff < 60) {
                                                    echo "Just now";
                                                } elseif ($diff < 3600) {
                                                    $mins = floor($diff / 60);
                                                    echo $mins . " minute" . ($mins > 1 ? "s" : "") . " ago";
                                                } elseif ($diff < 86400) {
                                                    $hours = floor($diff / 3600);
                                                    echo $hours . " hour" . ($hours > 1 ? "s" : "") . " ago";
                                                } elseif ($diff < 604800) {
                                                    $days = floor($diff / 86400);
                                                    echo $days . " day" . ($days > 1 ? "s" : "") . " ago";
                                                } else {
                                                    echo date('M j, Y', $activityTime);
                                                }
                                                ?>
                                            </div>
                                        </div>
                                    </div>
                                <?php endforeach; ?>
                            <?php endif; ?>
                        </div>
                    </div>
                    <div class="card-footer">
                        <a href="#!" class="btn btn-primary btn-sm">View All Activity</a>
                    </div>
                </div>
            </div>

            <!-- Quick Links and Pending Reports -->
            <div class="col-xl-4 col-md-12">
                <!-- Quick Links -->
                <div class="card dashboard-card mb-4">
                    <div class="card-header">
                        <h5>Quick Links</h5>
                    </div>
                    <div class="card-body">
                        <div class="row">
                            <div class="col-md-6">
                                <a href="faq-management.php" class="btn btn-outline-primary btn-block mb-3">
                                    <i class="feather icon-help-circle mr-2"></i> Manage FAQs
                                </a>
                            </div>
                            <div class="col-md-6">
                                <a href="traveler-fees.php" class="btn btn-outline-primary btn-block mb-3">
                                    <i class="feather icon-dollar-sign mr-2"></i> Traveler Fees
                                </a>
                            </div>
                            <div class="col-md-6">
                                <a href="opportunity-list.php" class="btn btn-outline-primary btn-block mb-3">
                                    <i class="feather icon-list mr-2"></i> Opportunities
                                </a>
                            </div>
                            <div class="col-md-6">
                                <a href="payment-verification.php" class="btn btn-outline-primary btn-block mb-3">
                                    <i class="feather icon-check-circle mr-2"></i> Payment Verification
                                </a>
                            </div>
                            <div class="col-md-6">
                                <a href="user-reports.php" class="btn btn-outline-primary btn-block mb-3">
                                    <i class="feather icon-alert-triangle mr-2"></i> User Reports
                                </a>
                            </div>
                            <div class="col-md-6">
                                <a href="admin-profile.php" class="btn btn-outline-primary btn-block mb-3">
                                    <i class="feather icon-user mr-2"></i> Admin Profile
                                </a>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Pending Reports -->
                <div class="card dashboard-card">
                    <div class="card-header">
                        <h5>Pending Reports</h5>
                    </div>
                    <div class="card-body p-0">
                        <?php if (empty($pendingReports)): ?>
                            <div class="alert alert-info m-3">No pending reports.</div>
                        <?php else: ?>
                            <div class="table-responsive">
                                <table class="table table-hover mb-0">
                                    <thead>
                                        <tr>
                                            <th>Type</th>
                                            <th>Reporter</th>
                                            <th>Date</th>
                                            <th>Action</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <?php foreach ($pendingReports as $report): ?>
                                            <tr>
                                                <td>
                                                    <?php
                                                    $badgeClass = '';
                                                    switch ($report['report_type']) {
                                                        case 'user':
                                                            $badgeClass = 'badge-danger';
                                                            break;
                                                        case 'opportunity':
                                                            $badgeClass = 'badge-warning';
                                                            break;
                                                        case 'message':
                                                            $badgeClass = 'badge-info';
                                                            break;
                                                        default:
                                                            $badgeClass = 'badge-secondary';
                                                    }
                                                    ?>
                                                    <span class="badge <?= $badgeClass ?>">
                                                        <?= ucfirst(htmlspecialchars($report['report_type'])) ?>
                                                    </span>
                                                </td>
                                                <td>
                                                    <?= htmlspecialchars($report['reporter_first_name'] . ' ' . $report['reporter_last_name']) ?>
                                                </td>
                                                <td>
                                                    <?= date('M j', strtotime($report['created_at'])) ?>
                                                </td>
                                                <td>
                                                    <a href="user-reports.php?report_id=<?= $report['report_id'] ?>" class="btn btn-sm btn-primary">
                                                        <i class="feather icon-eye"></i>
                                                    </a>
                                                </td>
                                            </tr>
                                        <?php endforeach; ?>
                                    </tbody>
                                </table>
                            </div>
                        <?php endif; ?>
                    </div>
                    <div class="card-footer">
                        <a href="user-reports.php" class="btn btn-primary btn-sm">View All Reports</a>
                    </div>
                </div>
            </div>

            <!-- Additional Dashboard Widgets Row -->
            <div class="row">
                <!-- Payment Verification Requests -->
                <div class="col-xl-6 col-md-12">
                    <div class="card dashboard-card">
                        <div class="card-header">
                            <h5>Payment Verification Requests</h5>
                        </div>
                        <div class="card-body p-0">
                            <?php if (empty($pendingVerifications)): ?>
                                <div class="alert alert-info m-3">No pending verification requests.</div>
                            <?php else: ?>
                                <div class="table-responsive">
                                    <table class="table table-hover mb-0">
                                        <thead>
                                            <tr>
                                                <th>Traveler</th>
                                                <th>Issue</th>
                                                <th>Priority</th>
                                                <th>Status</th>
                                                <th>Action</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            <?php foreach ($pendingVerifications as $verification): ?>
                                                <tr>
                                                    <td>
                                                        <?= htmlspecialchars($verification['first_name'] . ' ' . $verification['last_name']) ?>
                                                    </td>
                                                    <td>
                                                        <?= ucwords(str_replace('_', ' ', htmlspecialchars($verification['issue_type']))) ?>
                                                    </td>
                                                    <td>
                                                        <?php
                                                        $priorityClass = '';
                                                        switch ($verification['priority']) {
                                                            case 'urgent':
                                                                $priorityClass = 'badge-danger';
                                                                break;
                                                            case 'high':
                                                                $priorityClass = 'badge-warning';
                                                                break;
                                                            case 'normal':
                                                                $priorityClass = 'badge-info';
                                                                break;
                                                            case 'low':
                                                                $priorityClass = 'badge-secondary';
                                                                break;
                                                        }
                                                        ?>
                                                        <span class="badge <?= $priorityClass ?>">
                                                            <?= ucfirst(htmlspecialchars($verification['priority'])) ?>
                                                        </span>
                                                    </td>
                                                    <td>
                                                        <?php
                                                        $statusClass = '';
                                                        switch ($verification['status']) {
                                                            case 'new':
                                                                $statusClass = 'badge-danger';
                                                                break;
                                                            case 'pending':
                                                                $statusClass = 'badge-warning';
                                                                break;
                                                            case 'in_progress':
                                                                $statusClass = 'badge-info';
                                                                break;
                                                            case 'closed':
                                                                $statusClass = 'badge-secondary';
                                                                break;
                                                        }
                                                        ?>
                                                        <span class="badge <?= $statusClass ?>">
                                                            <?= ucfirst(str_replace('_', ' ', htmlspecialchars($verification['status']))) ?>
                                                        </span>
                                                    </td>
                                                    <td>
                                                        <a href="payment-verification.php?request_id=<?= $verification['request_id'] ?>" class="btn btn-sm btn-primary">
                                                            <i class="feather icon-eye"></i>
                                                        </a>
                                                    </td>
                                                </tr>
                                            <?php endforeach; ?>
                                        </tbody>
                                    </table>
                                </div>
                            <?php endif; ?>
                        </div>
                        <div class="card-footer">
                            <a href="payment-verification.php" class="btn btn-primary btn-sm">View All Requests</a>
                        </div>
                    </div>
                </div>

                <!-- Recent Opportunities -->
                <div class="col-xl-6 col-md-12">
                    <div class="card dashboard-card">
                        <div class="card-header">
                            <h5>Recent Opportunities</h5>
                        </div>
                        <div class="card-body p-0">
                            <?php if (empty($recentOpportunities)): ?>
                                <div class="alert alert-info m-3">No recent opportunities found.</div>
                            <?php else: ?>
                                <div class="table-responsive">
                                    <table class="table table-hover mb-0">
                                        <thead>
                                            <tr>
                                                <th>Title</th>
                                                <th>Host</th>
                                                <th>Location</th>
                                                <th>Status</th>
                                                <th>Action</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            <?php foreach ($recentOpportunities as $opportunity): ?>
                                                <tr>
                                                    <td>
                                                        <?= htmlspecialchars(substr($opportunity['title'], 0, 30)) . (strlen($opportunity['title']) > 30 ? '...' : '') ?>
                                                    </td>
                                                    <td>
                                                        <?= htmlspecialchars($opportunity['first_name'] . ' ' . $opportunity['last_name']) ?>
                                                    </td>
                                                    <td>
                                                        <?= htmlspecialchars($opportunity['location']) ?>
                                                    </td>
                                                    <td>
                                                        <?php
                                                        $statusClass = '';
                                                        switch ($opportunity['status']) {
                                                            case 'open':
                                                                $statusClass = 'badge-success';
                                                                break;
                                                            case 'closed':
                                                                $statusClass = 'badge-secondary';
                                                                break;
                                                            case 'pending':
                                                                $statusClass = 'badge-warning';
                                                                break;
                                                        }
                                                        ?>
                                                        <span class="badge <?= $statusClass ?>">
                                                            <?= ucfirst(htmlspecialchars($opportunity['status'])) ?>
                                                        </span>
                                                    </td>
                                                    <td>
                                                        <a href="opportunity-list.php?id=<?= $opportunity['opportunity_id'] ?>" class="btn btn-sm btn-primary">
                                                            <i class="feather icon-eye"></i>
                                                        </a>
                                                    </td>
                                                </tr>
                                            <?php endforeach; ?>
                                        </tbody>
                                    </table>
                                </div>
                            <?php endif; ?>
                        </div>
                        <div class="card-footer">
                            <a href="opportunity-list.php" class="btn btn-primary btn-sm">View All Opportunities</a>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
    <!-- Required Js -->
    <script src="assets/js/vendor-all.min.js"></script>
    <script src="assets/js/plugins/bootstrap.min.js"></script>
    <script src="assets/js/ripple.js"></script>
    <script src="assets/js/pcoded.min.js"></script>
    <!-- Custom Js -->
    <script src="assets/js/custom.js"></script>
    <!-- Dashboard specific JS -->
    <script>
        // Refresh dashboard data every 5 minutes
        setTimeout(function() {
            location.reload();
        }, 300000);
    </script>
</body>
</html>
