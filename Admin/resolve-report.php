<?php
require_once '../Controllers/ReportController.php';

// Initialize the controller
$reportController = new ReportController();

// Initialize variables
$report = null;
$message = "";
$status = "";

// Check if report ID is provided
if (isset($_GET['id'])) {
    $reportId = intval($_GET['id']);

    // Get the report details
    $report = $reportController->getReportById($reportId);

    if (!$report) {
        // Report not found
        $message = "Report not found.";
        $status = "error";
    } elseif ($report['status'] === 'resolved') {
        // Report already resolved
        $message = "This report has already been resolved.";
        $status = "warning";
    }
} else {
    // No report ID provided
    $message = "Report ID is required.";
    $status = "error";
}

// Handle form submission
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['admin_response'])) {
    $reportId = intval($_POST['report_id']);
    $adminResponse = $_POST['admin_response'];

    if (empty($adminResponse)) {
        $message = "Admin response is required.";
        $status = "error";
    } else {
        // Resolve the report
        $result = $reportController->resolveReport($reportId, $adminResponse);

        if ($result) {
            // Success message
            $message = "Report has been successfully resolved.";
            $status = "success";

            // Get the updated report
            $report = $reportController->getReportById($reportId);
        } else {
            // Error message
            $message = "Failed to resolve report.";
            $status = "error";
        }
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Resolve Report - Admin Dashboard</title>
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
                                    <h5 class="m-b-10">Resolve Report</h5>
                                </div>
                                <ul class="breadcrumb">
                                    <li class="breadcrumb-item"><a href="index.html"><i class="feather icon-home"></i></a></li>
                                    <li class="breadcrumb-item"><a href="user-reports.php">User Reports</a></li>
                                    <li class="breadcrumb-item">Resolve Report</li>
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
                            <div class="col-12">
                                <?php if ($status === "success"): ?>
                                    <div class="card">
                                        <div class="card-header">
                                            <h5>Report Resolved</h5>
                                        </div>
                                        <div class="card-body">
                                            <div class="alert alert-success">
                                                <i class="feather icon-check-circle mr-2"></i>
                                                <?php echo $message; ?>
                                            </div>

                                            <?php if (isset($report)): ?>
                                                <div class="mt-4">
                                                    <h6>Report Details:</h6>
                                                    <div class="table-responsive">
                                                        <table class="table table-bordered">
                                                            <tr>
                                                                <th>Report ID</th>
                                                                <td><?php echo $report['report_id']; ?></td>
                                                            </tr>
                                                            <tr>
                                                                <th>Reported User</th>
                                                                <td><?php echo htmlspecialchars($report['target_first_name'] . ' ' . $report['target_last_name']); ?></td>
                                                            </tr>
                                                            <tr>
                                                                <th>Report Type</th>
                                                                <td><?php echo $reportController->formatReportType($report['report_type']); ?></td>
                                                            </tr>
                                                            <tr>
                                                                <th>Report Content</th>
                                                                <td><?php echo htmlspecialchars($report['report_content']); ?></td>
                                                            </tr>
                                                            <tr>
                                                                <th>Status</th>
                                                                <td><span class="badge badge-success">Resolved</span></td>
                                                            </tr>
                                                            <tr>
                                                                <th>Your Response</th>
                                                                <td><?php echo htmlspecialchars($report['admin_response']); ?></td>
                                                            </tr>
                                                        </table>
                                                    </div>
                                                </div>
                                            <?php endif; ?>

                                            <div class="mt-4">
                                                <a href="user-reports.php" class="btn btn-primary">
                                                    <i class="feather icon-arrow-left mr-2"></i>Back to Reports
                                                </a>
                                            </div>
                                        </div>
                                    </div>
                                <?php elseif ($status === "error" || $status === "warning"): ?>
                                    <div class="card">
                                        <div class="card-header">
                                            <h5>Error</h5>
                                        </div>
                                        <div class="card-body">
                                            <div class="alert alert-<?php echo $status === 'warning' ? 'warning' : 'danger'; ?>">
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
                                <?php else: ?>
                                    <div class="card">
                                        <div class="card-header">
                                            <h5>Resolve Report</h5>
                                        </div>
                                        <div class="card-body">
                                            <?php if (isset($report)): ?>
                                                <div class="mb-4">
                                                    <h6>Report Details:</h6>
                                                    <div class="table-responsive">
                                                        <table class="table table-bordered">
                                                            <tr>
                                                                <th>Report ID</th>
                                                                <td><?php echo $report['report_id']; ?></td>
                                                            </tr>
                                                            <tr>
                                                                <th>Reported User</th>
                                                                <td><?php echo htmlspecialchars($report['target_first_name'] . ' ' . $report['target_last_name']); ?></td>
                                                            </tr>
                                                            <tr>
                                                                <th>Report Type</th>
                                                                <td><?php echo $reportController->formatReportType($report['report_type']); ?></td>
                                                            </tr>
                                                            <tr>
                                                                <th>Report Content</th>
                                                                <td><?php echo htmlspecialchars($report['report_content']); ?></td>
                                                            </tr>
                                                            <tr>
                                                                <th>Current Status</th>
                                                                <td><?php echo $reportController->formatStatus($report['status']); ?></td>
                                                            </tr>
                                                        </table>
                                                    </div>
                                                </div>

                                                <form action="resolve-report.php" method="post">
                                                    <input type="hidden" name="report_id" value="<?php echo $report['report_id']; ?>">

                                                    <div class="form-group">
                                                        <label for="admin_response">Your Response</label>
                                                        <textarea class="form-control" id="admin_response" name="admin_response" rows="5" placeholder="Enter your response and resolution details" required></textarea>
                                                        <small class="form-text text-muted">This response will be recorded with the report and visible to administrators.</small>
                                                    </div>

                                                    <div class="form-group">
                                                        <button type="submit" class="btn btn-primary">
                                                            <i class="feather icon-check-square mr-2"></i>Resolve Report
                                                        </button>
                                                        <a href="user-reports.php" class="btn btn-secondary ml-2">
                                                            <i class="feather icon-x mr-2"></i>Cancel
                                                        </a>
                                                    </div>
                                                </form>
                                            <?php endif; ?>
                                        </div>
                                    </div>
                                <?php endif; ?>
                            </div>
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
