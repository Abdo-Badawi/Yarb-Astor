<?php
// Start or resume session with more secure settings
ini_set('session.use_only_cookies', 1);
ini_set('session.use_strict_mode', 1);
session_start();

// Regenerate session ID periodically to prevent session fixation
if (!isset($_SESSION['last_regeneration'])) {
    $_SESSION['last_regeneration'] = time();
} elseif (time() - $_SESSION['last_regeneration'] > 300) {
    // Regenerate session ID every 5 minutes
    session_regenerate_id(true);
    $_SESSION['last_regeneration'] = time();
}

// Update last activity time
$_SESSION['last_activity'] = time();

// Now load the controller and get reports
require_once '../Controllers/ReportController.php';

// Initialize the controller
$reportController = new ReportController();

// Get all reports
$reports = $reportController->getAllReports();
?>
    <style>
        /* Improved fix for loading bar */
        .loader-bg {
            display: none !important;
        }

        /* Consistent styling with other admin pages - blue background */
        body {
            background-color: #58b4d1 !important;
        }



        /* Navbar styling consistent with other pages */
        .pcoded-navbar {
            height: 100% !important;
        }

        /* Header styling consistent with other admin pages */
        .pcoded-header {
            background-color: #58b4d1 !important;
        }

        /* Page header styling - blue background */
        .page-header {
            background-color: #58b4d1 !important;
            padding: 20px 0 !important;
            margin-bottom: 20px !important;
            width: 100% !important;
        }

        /* Make sure the page block extends fully */
        .page-block {
            width: 100% !important;
            margin: 0 !important;
            padding: 0 20px !important;
        }

        .page-header-title h5 {
            color: #fff !important;
        }

        /* Breadcrumb styling */
        .breadcrumb {
            background: transparent !important;
        }

        .breadcrumb-item,
        .breadcrumb-item a,
        .breadcrumb-item.active {
            color: #fff !important;
        }

        /* Make breadcrumb separator white */
        .breadcrumb-item+.breadcrumb-item::before {
            color: #fff !important;
        }

        /* Card styling consistent with other admin pages */
        .card {
            margin-bottom: 30px;
            border: none;
            border-radius: 5px;
            box-shadow: 0 1px 20px 0 rgba(69, 90, 100, 0.08);
            background-color: #f4f7fa !important; /* Off-white background for content */
        }

        .card .card-header {
            background-color: transparent;
            border-bottom: 1px solid #f1f1f1;
            padding: 20px 25px;
        }

        .card .card-header h5 {
            margin-bottom: 0;
            color: #37474f;
            font-size: 15px;
            font-weight: 600;
            display: inline-block;
            margin-right: 10px;
            line-height: 1.4;
        }

        /* Report card styles - keep white background for content */
        .report-card {
            background: #fff;
            border-radius: 10px;
            box-shadow: 0 2px 4px rgba(0,0,0,0.1);
            margin-bottom: 20px;
            padding: 20px;
            transition: all 0.3s ease;
        }

        .report-card:hover {
            box-shadow: 0 4px 8px rgba(0,0,0,0.15);
        }

        .user-info {
            display: flex;
            align-items: center;
            margin-bottom: 15px;
        }

        .user-avatar {
            width: 50px;
            height: 50px;
            border-radius: 50%;
            margin-right: 15px;
            object-fit: cover;
            background-color: #e9ecef;
            display: flex;
            align-items: center;
            justify-content: center;
            color: #6c757d;
            font-size: 20px;
        }

        .user-details h5 {
            margin: 0;
            color: #333;
        }

        .report-user-role {
            font-size: 0.9em;
            color: #666;
            display: inline-block;
            padding: 2px 8px;
            border-radius: 12px;
            background-color: #e9ecef;
            margin-top: 4px;
        }

        .report-user-role.host {
            background-color: #d1e7dd;
            color: #0f5132;
        }

        .report-user-role.traveler {
            background-color: #cfe2ff;
            color: #084298;
        }

        .report-user-role.admin {
            background-color: #f8d7da;
            color: #842029;
        }

        .report-details {
            background: #f8f9fa;
            padding: 15px;
            border-radius: 8px;
            margin-bottom: 15px;
            border-left: 4px solid #6c757d;
        }

        .report-details.user {
            border-left-color: #0d6efd;
        }

        .report-details.opportunity {
            border-left-color: #198754;
        }

        .report-details.message {
            border-left-color: #dc3545;
        }

        .report-meta {
            display: flex;
            justify-content: space-between;
            margin-bottom: 10px;
            font-size: 0.9em;
            color: #666;
        }

        .report-content {
            margin-bottom: 15px;
        }

        .report-actions {
            display: flex;
            flex-wrap: wrap;
            gap: 8px;
        }

        .report-actions .btn {
            padding: 0.375rem 0.75rem;
            font-size: 0.875rem;
            border-radius: 4px;
            display: inline-flex;
            align-items: center;
            gap: 5px;
        }

        .status-badge {
            padding: 5px 10px;
            border-radius: 15px;
            font-size: 0.85em;
            font-weight: 500;
            color: white !important;
        }

        .status-pending {
            background: #ffc107;
        }

        .status-reviewed {
            background: #17a2b8;
        }

        .status-resolved {
            background: #28a745;
        }

        .search-filters {
            background: #fff !important;
            padding: 20px;
            border-radius: 10px;
            box-shadow: 0 2px 4px rgba(0,0,0,0.1);
            margin-bottom: 20px;
        }

        .filter-group {
            margin-bottom: 15px;
        }

        .filter-group label {
            font-weight: 500;
            margin-bottom: 5px;
        }

        .action-buttons {
            display: flex;
            flex-wrap: wrap;
            gap: 8px;
            margin-top: 15px;
        }

        .btn-view {
            background: #28a745;
            color: white;
        }

        .btn-deactivate {
            background: #dc3545;
            color: white;
        }

        .btn-warn {
            background: #ffc107;
            color: #000;
        }

        .btn-ignore {
            background: #6c757d;
            color: white;
        }

        .no-reports {
            text-align: center;
            padding: 40px 20px;
            background: #fff !important;
            border-radius: 10px;
            box-shadow: 0 2px 4px rgba(0,0,0,0.1);
        }

        .no-reports i {
            font-size: 48px;
            color: #6c757d;
            margin-bottom: 15px;
        }

        .admin-response-form {
            margin-top: 20px;
        }

        .admin-response-form textarea {
            border-radius: 4px;
            resize: vertical;
            min-height: 100px;
        }

        .btn-sm {
            min-width: 40px !important;
            max-width: 40px !important;
            text-align: center;
            display: inline-block;
            box-sizing: border-box;
            white-space: nowrap;
            overflow: hidden;
            text-overflow: ellipsis;
            padding: 0.2rem 0.1rem;
            font-size: 0.65rem;
            margin-right: 2px;
            border-radius: 3px;
        }
    </style>
</head>
<body class="">
<?php 
    include 'navCommon.php'; 
    include_once 'header-common.php'
?>
<!-- [ Main Content ] start -->
<div class="pcoded-main-container">
    <div class="pcoded-content">
        <div class="page-header">
            <div class="page-block">
                <div class="row align-items-center">
                    <div class="col-md-12">
                        <div class="page-header-title">
                            <h5 class="m-b-10">User Reports Management</h5>
                        </div>
                        <ul class="breadcrumb">
                            <li class="breadcrumb-item"><a href="index.php"><i class="feather icon-home"></i></a></li>
                            <li class="breadcrumb-item"><a href="user-reports.php">Manage Reports</a></li>
                        </ul>
                    </div>
                </div>
            </div>
        </div>

        <!-- Main Content -->
        <div class="row">
            <div class="col-xl-12 col-md-12">
                <div class="card">
                    <div class="card-header">
                        <h5><i class="feather icon-flag mr-2"></i>User Reports</h5>
                    </div>
                    <div class="card-body">
                        <!-- Search and Filters -->
                        <div class="row mb-4 filter-row">
                            <div class="col-12">
                                <div class="search-filters">
                                    <div class="row">
                                        <div class="col-md-3">
                                            <div class="filter-group">
                                                <label>User Type</label>
                                                <select class="form-control" id="userTypeFilter">
                                                    <option value="">All Users</option>
                                                    <option value="host">Hosts</option>
                                                    <option value="traveler">Travelers</option>
                                                </select>
                                            </div>
                                        </div>
                                        <div class="col-md-3">
                                            <div class="filter-group">
                                                <label>Report Status</label>
                                                <select class="form-control" id="statusFilter">
                                                    <option value="">All Status</option>
                                                    <option value="open">Open</option>
                                                    <option value="reviewed">Reviewed</option>
                                                    <option value="resolved">Resolved</option>
                                                </select>
                                            </div>
                                        </div>
                                        <div class="col-md-3">
                                            <div class="filter-group">
                                                <label>Report Type</label>
                                                <select class="form-control" id="reportTypeFilter">
                                                    <option value="">All Types</option>
                                                    <option value="user">User Behavior</option>
                                                    <option value="opportunity">Opportunity Issue</option>
                                                    <option value="message">Message Content</option>
                                                </select>
                                            </div>
                                        </div>
                                        <div class="col-md-3">
                                            <div class="filter-group">
                                                <label>Date Range</label>
                                                <input type="date" class="form-control" id="dateFilter">
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <!-- Reports List -->
                            <div class="col-12">
                                <div class="reports-list">
                                    <?php if ($reports && count($reports) > 0): ?>
                                        <?php foreach ($reports as $report): ?>
                                            <div class="report-card">
                                                <div class="user-info">
                                                    <?php if (!empty($report['target_profile_picture'])): ?>
                                                        <img src="data:image/jpeg;base64,<?php echo base64_encode($report['target_profile_picture']); ?>" alt="User Avatar" class="user-avatar">
                                                    <?php else: ?>
                                                        <div class="user-avatar">
                                                            <i class="feather icon-user"></i>
                                                        </div>
                                                    <?php endif; ?>
                                                    <div class="user-details">
                                                        <h5><?php echo htmlspecialchars($report['target_first_name'] . ' ' . $report['target_last_name']); ?></h5>
                                                        <span class="report-user-role <?php echo strtolower($report['target_type']); ?>"><?php echo ucfirst($report['target_type']); ?></span>
                                                    </div>
                                                </div>
                                                <div class="report-details <?php echo $report['report_type']; ?>">
                                                    <div class="report-meta">
                                                        <span>Reported: <?php echo date('Y-m-d', strtotime($report['created_at'])); ?></span>
                                                        <?php echo $reportController->formatStatus($report['status']); ?>
                                                    </div>
                                                    <div class="report-content">
                                                        <h6><?php echo $reportController->formatReportType($report['report_type']); ?></h6>
                                                        <p><?php echo htmlspecialchars($report['report_content']); ?></p>
                                                        <?php if (!empty($report['admin_response']) && $report['status'] === 'resolved'): ?>
                                                            <div class="admin-response mt-3 p-2 bg-light border-left border-success">
                                                                <small class="text-muted">Admin Response:</small>
                                                                <p class="mb-0"><?php echo htmlspecialchars($report['admin_response']); ?></p>
                                                            </div>
                                                        <?php endif; ?>
                                                    </div>
                                                    <div class="report-actions">
                                                        <a href="view-report.php?id=<?php echo $report['report_id']; ?>" class="btn btn-success">
                                                            <i class="feather icon-eye"></i> View Details
                                                        </a>

                                                        <?php if ($report['status'] === 'open'): ?>
                                                            <a href="mark-reviewed.php?id=<?php echo $report['report_id']; ?>" class="btn btn-info">
                                                                <i class="feather icon-check-circle"></i> Mark as Reviewed
                                                            </a>
                                                        <?php endif; ?>

                                                        <?php if ($report['status'] !== 'resolved'): ?>
                                                            <a href="resolve-report.php?id=<?php echo $report['report_id']; ?>" class="btn btn-primary">
                                                                <i class="feather icon-check-square"></i> Resolve Report
                                                            </a>
                                                        <?php endif; ?>

                                                        <a href="deactivate-user.php?id=<?php echo $report['target_user_id']; ?>" class="btn btn-danger">
                                                            <i class="feather icon-user-x"></i> Deactivate Account
                                                        </a>

                                                        <a href="warn-user.php?id=<?php echo $report['target_user_id']; ?>" class="btn btn-warning">
                                                            <i class="feather icon-alert-triangle"></i> Issue Warning
                                                        </a>
                                                    </div>
                                                </div>
                                            </div>
                                        <?php endforeach; ?>
                                    <?php else: ?>
                                        <div class="no-reports">
                                            <i class="feather icon-check-circle"></i>
                                            <h4>No Reports Found</h4>
                                            <p>There are currently no user reports in the system.</p>
                                        </div>
                                    <?php endif; ?>
                                </div>
                            </div>
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
<script src="assets/js/pcoded.min.js"></script>
<script src="assets/js/custom.js"></script>
<script>
    // Simple session refresh on activity
    (function() {
        // Refresh the page after 30 minutes of inactivity
        var inactivityTime = 1800000; // 30 minutes in milliseconds
        var inactivityTimer;

        // Function to reset the timer
        function resetTimer() {
            clearTimeout(inactivityTimer);
            inactivityTimer = setTimeout(function() {
                // Refresh the page after inactivity timeout
                window.location.reload();
            }, inactivityTime);
        }

        // Start the timer when the page loads
        resetTimer();

        // Reset the timer on user activity
        ['mousedown', 'mousemove', 'keypress', 'scroll', 'touchstart'].forEach(function(event) {
            document.addEventListener(event, resetTimer, true);
        });
    })();

    // Simple filter functionality
    document.addEventListener('DOMContentLoaded', function() {
        const filters = {
            userType: document.getElementById('userTypeFilter'),
            status: document.getElementById('statusFilter'),
            reportType: document.getElementById('reportTypeFilter'),
            date: document.getElementById('dateFilter')
        };

        // Add event listeners to filters
        Object.values(filters).forEach(filter => {
            if (filter) {
                filter.addEventListener('change', applyFilters);
            }
        });

        function applyFilters() {
            // Get filter values
            const userType = filters.userType ? filters.userType.value : '';
            const status = filters.status ? filters.status.value : '';
            const reportType = filters.reportType ? filters.reportType.value : '';
            const date = filters.date ? filters.date.value : '';

            // Get all report cards
            const reportCards = document.querySelectorAll('.report-card');

            // Apply filters to each card
            reportCards.forEach(card => {
                let show = true;

                // Check user type
                if (userType && card.querySelector('.report-user-role') &&
                    !card.querySelector('.report-user-role').textContent.toLowerCase().includes(userType)) {
                    show = false;
                }

                // Check status
                if (status && card.querySelector('.status-badge') &&
                    !card.querySelector('.status-badge').textContent.toLowerCase().includes(status)) {
                    show = false;
                }

                // Check report type
                if (reportType && card.querySelector('.report-content h6') &&
                    !card.querySelector('.report-content h6').textContent.toLowerCase().includes(reportType)) {
                    show = false;
                }

                // Check date
                if (date && card.querySelector('.report-meta span')) {
                    const reportDate = card.querySelector('.report-meta span').textContent.split(': ')[1];
                    if (reportDate !== date) {
                        show = false;
                    }
                }

                // Show/hide card based on filters
                card.style.display = show ? 'block' : 'none';
            });
        }
    });
</script>
