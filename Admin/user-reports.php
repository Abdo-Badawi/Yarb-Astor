<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>User Reports Management - Admin Dashboard</title>
    <!-- HTML5 Shim and Respond.js IE11 support of HTML5 elements and media queries -->
    <!--[if lt IE 11]>
    	<script src="https://oss.maxcdn.com/libs/html5shiv/3.7.0/html5shiv.js"></script>
    	<script src="https://oss.maxcdn.com/libs/respond.js/1.4.2/respond.min.js"></script>
    	<![endif]-->
    <!-- Meta -->
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0, user-scalable=0, minimal-ui">
    <meta http-equiv="X-UA-Compatible" content="IE=edge" />
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
        .report-card {
            background: #fff;
            border-radius: 10px;
            box-shadow: 0 2px 4px rgba(0,0,0,0.1);
            margin-bottom: 20px;
            padding: 20px;
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
        }

        .user-details h5 {
            margin: 0;
            color: #333;
        }

        .user-role {
            font-size: 0.9em;
            color: #666;
        }

        .report-details {
            background: #f8f9fa;
            padding: 15px;
            border-radius: 8px;
            margin-bottom: 15px;
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
            gap: 10px;
        }

        .status-badge {
            padding: 5px 10px;
            border-radius: 15px;
            font-size: 0.85em;
            font-weight: 500;
        }

        .status-pending {
            background: #fff3cd;
            color: #856404;
        }

        .status-reviewed {
            background: #d4edda;
            color: #155724;
        }

        .status-resolved {
            background: #cce5ff;
            color: #004085;
        }

        .search-filters {
            background: #fff;
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
            gap: 10px;
        }

        .btn-view {
            background: #007bff;
            color: white;
        }

        .btn-activate {
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

        .modal-content {
            border-radius: 10px;
        }

        .modal-header {
            background: #f8f9fa;
            border-radius: 10px 10px 0 0;
        }

        .modal-body {
            padding: 20px;
        }

        .user-history {
            margin-top: 20px;
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
<body class="">

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
										<h5 class="m-b-10">User Reports Management</h5>
									</div>
									<ul class="breadcrumb">
										<li class="breadcrumb-item"><a href="index.html"><i class="feather icon-home"></i></a></li>
										<li class="breadcrumb-item">User Reports</li>
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
								<!-- Search and Filters -->
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
														<option value="pending">Pending</option>
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
														<option value="inappropriate">Inappropriate Behavior</option>
														<option value="fraud">Fraud</option>
														<option value="harassment">Harassment</option>
														<option value="other">Other</option>
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
										<!-- Report Card 1 -->
										<div class="report-card">
											<div class="user-info">
												<img src="assets/images/avatar1.jpg" alt="User Avatar" class="user-avatar">
												<div class="user-details">
													<h5>John Doe</h5>
													<span class="user-role">Host</span>
												</div>
											</div>
											<div class="report-details">
												<div class="report-meta">
													<span>Reported: 2024-02-20</span>
													<span class="status-badge status-pending">Pending Review</span>
												</div>
												<div class="report-content">
													<h6>Inappropriate Behavior</h6>
													<p>Multiple reports of harassment and inappropriate comments towards travelers.</p>
												</div>
												<div class="report-actions">
													<button class="btn btn-view" data-toggle="modal" data-target="#userDetailsModal">
														<i class="feather icon-eye"></i> View Details
													</button>
													<button class="btn btn-deactivate">
														<i class="feather icon-user-x"></i> Deactivate Account
													</button>
													<button class="btn btn-warn">
														<i class="feather icon-alert-triangle"></i> Issue Warning
													</button>
													<button class="btn btn-ignore">
														<i class="feather icon-x-circle"></i> Ignore Report
													</button>
												</div>
											</div>
										</div>

										<!-- Report Card 2 -->
										<div class="report-card">
											<div class="user-info">
												<img src="assets/images/avatar2.jpg" alt="User Avatar" class="user-avatar">
												<div class="user-details">
													<h5>Jane Smith</h5>
													<span class="user-role">Traveler</span>
												</div>
											</div>
											<div class="report-details">
												<div class="report-meta">
													<span>Reported: 2024-02-19</span>
													<span class="status-badge status-reviewed">Under Review</span>
												</div>
												<div class="report-content">
													<h6>Fraud</h6>
													<p>Suspicious payment activities and multiple failed transactions.</p>
												</div>
												<div class="report-actions">
													<button class="btn btn-view" data-toggle="modal" data-target="#userDetailsModal">
														<i class="feather icon-eye"></i> View Details
													</button>
													<button class="btn btn-deactivate">
														<i class="feather icon-user-x"></i> Deactivate Account
													</button>
													<button class="btn btn-warn">
														<i class="feather icon-alert-triangle"></i> Issue Warning
													</button>
													<button class="btn btn-ignore">
														<i class="feather icon-x-circle"></i> Ignore Report
													</button>
												</div>
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
	<!-- [ Main Content ] end -->

    <!-- User Details Modal -->
    <div class="modal fade" id="userDetailsModal" tabindex="-1">
        <div class="modal-dialog modal-lg">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">User Details</h5>
                    <button type="button" class="close" data-dismiss="modal">
                        <span>&times;</span>
                    </button>
                </div>
                <div class="modal-body">
                    <div class="user-profile">
                        <div class="row">
                            <div class="col-md-4">
                                <img src="assets/images/avatar1.jpg" alt="User Avatar" class="img-fluid rounded">
                            </div>
                            <div class="col-md-8">
                                <h4>John Doe</h4>
                                <p class="text-muted">Host</p>
                                <p><strong>Email:</strong> john.doe@example.com</p>
                                <p><strong>Phone:</strong> +1 234 567 8900</p>
                                <p><strong>Member Since:</strong> January 2024</p>
                                <p><strong>Status:</strong> <span class="badge badge-warning">Under Review</span></p>
                            </div>
                        </div>
                    </div>

                    <div class="user-history mt-4">
                        <h5>Report History</h5>
                        <div class="history-item">
                            <div class="history-date">2024-02-20</div>
                            <p>Reported for inappropriate behavior</p>
                        </div>
                        <div class="history-item">
                            <div class="history-date">2024-02-15</div>
                            <p>Warning issued for late responses</p>
                        </div>
                    </div>

                    <div class="action-buttons mt-4">
                        <button class="btn btn-activate">
                            <i class="feather icon-user-check"></i> Activate Account
                        </button>
                        <button class="btn btn-deactivate">
                            <i class="feather icon-user-x"></i> Deactivate Account
                        </button>
                        <button class="btn btn-warn">
                            <i class="feather icon-alert-triangle"></i> Issue Warning
                        </button>
                        <button class="btn btn-ignore">
                            <i class="feather icon-x-circle"></i> Ignore Report
                        </button>
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
    <script>
        // Initialize filters
        document.addEventListener('DOMContentLoaded', function() {
            const filters = {
                userType: document.getElementById('userTypeFilter'),
                status: document.getElementById('statusFilter'),
                reportType: document.getElementById('reportTypeFilter'),
                date: document.getElementById('dateFilter')
            };

            // Add event listeners to filters
            Object.values(filters).forEach(filter => {
                filter.addEventListener('change', applyFilters);
            });

            function applyFilters() {
                // Get filter values
                const userType = filters.userType.value;
                const status = filters.status.value;
                const reportType = filters.reportType.value;
                const date = filters.date.value;

                // Get all report cards
                const reportCards = document.querySelectorAll('.report-card');

                // Apply filters to each card
                reportCards.forEach(card => {
                    let show = true;

                    // Check user type
                    if (userType && !card.querySelector('.user-role').textContent.toLowerCase().includes(userType)) {
                        show = false;
                    }

                    // Check status
                    if (status && !card.querySelector('.status-badge').textContent.toLowerCase().includes(status)) {
                        show = false;
                    }

                    // Check report type
                    if (reportType && !card.querySelector('.report-content h6').textContent.toLowerCase().includes(reportType)) {
                        show = false;
                    }

                    // Check date
                    if (date) {
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
</body>
</html> 