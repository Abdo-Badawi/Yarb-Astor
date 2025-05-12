<?php
require_once '../Controllers/ReportController.php';

// Initialize the controller
$reportController = new ReportController();

// Check if report ID is provided
if (isset($_GET['id'])) {
    $reportId = intval($_GET['id']);

    // Get the report details before updating
    $report = $reportController->getReportById($reportId);

    if ($report) {
        // Mark the report as reviewed
        $result = $reportController->markAsReviewed($reportId);

        if ($result) {
            // Success message
            $message = "Report has been successfully marked as reviewed.";
            $status = "success";
        } else {
            // Error message
            $message = "Failed to update report status.";
            $status = "error";
        }
    } else {
        // Report not found
        $message = "Report not found.";
        $status = "error";
    }
} else {
    // No report ID provided
    $message = "Report ID is required.";
    $status = "error";
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Mark Report as Reviewed - Admin Dashboard</title>
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
                                    <h5 class="m-b-10">Mark Report as Reviewed</h5>
                                </div>
                                <ul class="breadcrumb">
                                    <li class="breadcrumb-item"><a href="index.html"><i class="feather icon-home"></i></a></li>
                                    <li class="breadcrumb-item"><a href="user-reports.php">User Reports</a></li>
                                    <li class="breadcrumb-item">Mark as Reviewed</li>
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
                                <div class="card">
                                    <div class="card-header">
                                        <h5>Action Result</h5>
                                    </div>
                                    <div class="card-body">
                                        <?php if ($status === "success"): ?>
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
                                                                <th>New Status</th>
                                                                <td><span class="badge badge-info">Under Review</span></td>
                                                            </tr>
                                                        </table>
                                                    </div>
                                                </div>
                                            <?php endif; ?>

                                            <div class="mt-4">
                                                <p>The report has been marked as "Under Review". You can now proceed with investigating the report.</p>
                                                <p>When you're ready to resolve this report, click the "Resolve Report" button and provide your response.</p>
                                            </div>
                                        <?php else: ?>
                                            <div class="alert alert-danger">
                                                <i class="feather icon-alert-circle mr-2"></i>
                                                <?php echo $message; ?>
                                            </div>
                                        <?php endif; ?>

                                        <div class="mt-4">
                                            <a href="user-reports.php" class="btn btn-primary">
                                                <i class="feather icon-arrow-left mr-2"></i>Back to Reports
                                            </a>
                                        </div>
                                    </div>
                                </div>
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
