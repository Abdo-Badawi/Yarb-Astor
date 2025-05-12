<?php
require_once '../Controllers/ReportController.php';

// Initialize the controller
$reportController = new ReportController();

// Check if report ID is provided
if (isset($_GET['id'])) {
    $reportId = intval($_GET['id']);

    // Get the report details
    $report = $reportController->getReportById($reportId);

    if (!$report) {
        // Report not found
        $message = "Report not found.";
        $status = "error";
    }
} else {
    // No report ID provided
    $message = "Report ID is required.";
    $status = "error";
}

// Get user history if report exists
$history = [];
if (isset($report) && $report) {
    $history = $reportController->getUserReportHistory($report['target_user_id']);
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>View Report Details - Admin Dashboard</title>
    <!-- Prevent caching -->
    <meta http-equiv="Cache-Control" content="no-store, no-cache, must-revalidate, max-age=0">
    <meta http-equiv="Pragma" content="no-cache">
    <meta http-equiv="Expires" content="0">
    <!-- Meta -->
    <meta name="description" content="HomeStay Admin Dashboard" />
    <meta name="keywords" content="admin, dashboard, homestay, cultural exchange">
    <meta name="author" content="HomeStay" />
    <!-- Favicon icon -->
    <link rel="icon" href="assets/images/favicon.ico" type="image/x-icon">

    <!-- vendor css -->
    <link rel="stylesheet" href="assets/css/style.css">
    <!-- custom css -->
    <link rel="stylesheet" href="assets/css/custom.css">
    <style>
        .user-avatar {
            width: 100px;
            height: 100px;
            border-radius: 50%;
            object-fit: cover;
            background-color: #e9ecef;
            display: flex;
            align-items: center;
            justify-content: center;
            color: #6c757d;
            font-size: 40px;
            margin: 0 auto 20px;
        }

        .history-item {
            padding: 10px;
            border-left: 3px solid #007bff;
            margin-bottom: 10px;
            background: #f8f9fa;
        }

        .history-date {
            font-size: 0.85em;
            color: #666;
        }
    </style>
</head>
<body>

<?php include 'navCommon.php'; ?>

<!-- [ Main Content ] start -->
<div class="pcoded-main-container">
    <div class="pcoded-wrapper">
        <div class="pcoded-content">
            <div class="pcoded-inner-content">
                <!-- [ breadcrumb ] end -->
                <div class="page-header">
                    <div class="page-block">
                        <div class="row align-items-center">
                            <div class="col-md-12">
                                <div class="page-header-title">
                                    <h5 class="m-b-10">View Report Details</h5>
                                </div>
                                <ul class="breadcrumb">
                                    <li class="breadcrumb-item"><a href="index.html"><i class="feather icon-home"></i></a></li>
                                    <li class="breadcrumb-item"><a href="user-reports-fixed.php">User Reports</a></li>
                                    <li class="breadcrumb-item">View Report</li>
                                </ul>
                            </div>
                        </div>
                    </div>
                </div>
                <!-- [ breadcrumb ] end -->

                <div class="main-body">
                    <div class="page-wrapper">
                        <!-- [ Main Content ] start -->
                        <div class="row">
                            <?php if (isset($status) && $status === "error"): ?>
                                <div class="col-12">
                                    <div class="card">
                                        <div class="card-header">
                                            <h5>Error</h5>
                                        </div>
                                        <div class="card-body">
                                            <div class="alert alert-danger">
                                                <i class="feather icon-alert-circle mr-2"></i>
                                                <?php echo $message; ?>
                                            </div>

                                            <div class="mt-4">
                                                <a href="user-reports.php" class="btn btn-primary">
                                                    <i class="feather icon-arrow-left mr-2"></i>Back to Reports
                                                </a>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            <?php elseif (isset($report) && $report): ?>
                                <div class="col-md-4">
                                    <div class="card">
                                        <div class="card-header">
                                            <h5>Reported User</h5>
                                        </div>
                                        <div class="card-body text-center">
                                            <?php if (!empty($report['target_profile_picture'])): ?>
                                                <img src="data:image/jpeg;base64,<?php echo base64_encode($report['target_profile_picture']); ?>" alt="User Avatar" class="user-avatar">
                                            <?php else: ?>
                                                <div class="user-avatar">
                                                    <i class="feather icon-user"></i>
                                                </div>
                                            <?php endif; ?>

                                            <h4><?php echo htmlspecialchars($report['target_first_name'] . ' ' . $report['target_last_name']); ?></h4>
                                            <p class="text-muted"><?php echo ucfirst($report['target_type']); ?></p>

                                            <div class="mt-4 text-left">
                                                <p><strong>Email:</strong> <?php echo htmlspecialchars($report['target_email'] ?? 'N/A'); ?></p>
                                                <p><strong>User Type:</strong> <?php echo ucfirst($report['target_type']); ?></p>
                                            </div>

                                            <div class="mt-4">
                                                <a href="deactivate-user.php?id=<?php echo $report['target_user_id']; ?>" class="btn btn-danger">
                                                    <i class="feather icon-user-x mr-2"></i>Deactivate Account
                                                </a>
                                                <a href="warn-user.php?id=<?php echo $report['target_user_id']; ?>" class="btn btn-warning mt-2">
                                                    <i class="feather icon-alert-triangle mr-2"></i>Issue Warning
                                                </a>
                                            </div>
                                        </div>
                                    </div>
                                </div>

                                <div class="col-md-8">
                                    <div class="card">
                                        <div class="card-header">
                                            <h5>Report Details</h5>
                                        </div>
                                        <div class="card-body">
                                            <div class="table-responsive">
                                                <table class="table table-bordered">
                                                    <tr>
                                                        <th>Report ID</th>
                                                        <td><?php echo $report['report_id']; ?></td>
                                                    </tr>
                                                    <tr>
                                                        <th>Reported By</th>
                                                        <td><?php echo htmlspecialchars($report['reporter_first_name'] . ' ' . $report['reporter_last_name']); ?></td>
                                                    </tr>
                                                    <tr>
                                                        <th>Report Type</th>
                                                        <td><?php echo $reportController->formatReportType($report['report_type']); ?></td>
                                                    </tr>
                                                    <tr>
                                                        <th>Reported On</th>
                                                        <td><?php echo date('Y-m-d', strtotime($report['created_at'])); ?></td>
                                                    </tr>
                                                    <tr>
                                                        <th>Status</th>
                                                        <td><?php echo $reportController->formatStatus($report['status']); ?></td>
                                                    </tr>
                                                </table>
                                            </div>

                                            <div class="mt-4">
                                                <h6>Report Content:</h6>
                                                <div class="p-3 bg-light rounded">
                                                    <p><?php echo htmlspecialchars($report['report_content']); ?></p>
                                                </div>
                                            </div>

                                            <?php if (!empty($report['admin_response'])): ?>
                                                <div class="mt-4">
                                                    <h6>Admin Response:</h6>
                                                    <div class="p-3 bg-light rounded border-left border-success">
                                                        <p><?php echo htmlspecialchars($report['admin_response']); ?></p>
                                                    </div>
                                                </div>
                                            <?php endif; ?>

                                            <div class="mt-4">
                                                <?php if ($report['status'] === 'open'): ?>
                                                    <a href="mark-reviewed.php?id=<?php echo $report['report_id']; ?>" class="btn btn-info">
                                                        <i class="feather icon-check-circle mr-2"></i>Mark as Reviewed
                                                    </a>
                                                <?php endif; ?>

                                                <?php if ($report['status'] !== 'resolved'): ?>
                                                    <a href="resolve-report.php?id=<?php echo $report['report_id']; ?>" class="btn btn-primary ml-2">
                                                        <i class="feather icon-check-square mr-2"></i>Resolve Report
                                                    </a>
                                                <?php endif; ?>

                                                <a href="user-reports.php" class="btn btn-secondary ml-2">
                                                    <i class="feather icon-arrow-left mr-2"></i>Back to Reports
                                                </a>
                                            </div>
                                        </div>
                                    </div>

                                    <?php if ($history && count($history) > 0): ?>
                                        <div class="card mt-4">
                                            <div class="card-header">
                                                <h5>Report History</h5>
                                            </div>
                                            <div class="card-body">
                                                <?php foreach ($history as $item): ?>
                                                    <div class="history-item">
                                                        <div class="history-date"><?php echo date('Y-m-d', strtotime($item['created_at'])); ?></div>
                                                        <p><?php echo htmlspecialchars($item['report_content']); ?></p>
                                                        <div class="text-right">
                                                            <span class="badge badge-<?php echo $item['status'] === 'open' ? 'warning' : ($item['status'] === 'reviewed' ? 'info' : 'success'); ?>">
                                                                <?php echo ucfirst($item['status']); ?>
                                                            </span>
                                                        </div>
                                                    </div>
                                                <?php endforeach; ?>
                                            </div>
                                        </div>
                                    <?php endif; ?>
                                </div>
                            <?php endif; ?>
                        </div>
                        <!-- [ Main Content ] end -->
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Scripts -->
<script src="assets/js/jquery.min.js"></script>
<script src="assets/js/bootstrap.bundle.min.js"></script>
<script src="assets/js/feather.min.js"></script>
<script src="assets/js/pcoded.min.js"></script>
<script src="assets/js/custom.js"></script>
</body>
</html>
