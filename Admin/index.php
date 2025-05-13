<!DOCTYPE html>
<html lang="en">

<head>
    <title>HomeStay Admin Dashboard</title>
    <!-- HTML5 Shim and Respond.js IE11 support of HTML5 elements and media queries -->
    <!-- WARNING: Respond.js doesn't work if you view the page via file:// -->
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
</head>
<body class="">

<?php include 'navCommon.php'; ?>

<!-- [ Main Content ] start -->
<div class="pcoded-main-container">
    <div class="pcoded-content">
        <!-- [ breadcrumb ] start -->
        <div class="page-header">
            <div class="page-block">
                <div class="row align-items-center">
                    <div class="col-md-12">
                        <div class="page-header-title">
                            <h5 class="m-b-10">Admin Dashboard</h5>
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
                    <div class="stat-value">248</div>
                    <div class="stat-change">+12% from last month</div>
                </div>
            </div>
            <div class="col-xl-3 col-md-6">
                <div class="stat-card">
                    <div class="stat-icon">
                        <i class="feather icon-user"></i>
                    </div>
                    <div class="stat-title">Total Travelers</div>
                    <div class="stat-value">1,524</div>
                    <div class="stat-change">+8% from last month</div>
                </div>
            </div>
            <div class="col-xl-3 col-md-6">
                <div class="stat-card">
                    <div class="stat-icon">
                        <i class="feather icon-home"></i>
                    </div>
                    <div class="stat-title">Active Homestays</div>
                    <div class="stat-value">186</div>
                    <div class="stat-change">+5% from last month</div>
                </div>
            </div>
            <div class="col-xl-3 col-md-6">
                <div class="stat-card">
                    <div class="stat-icon">
                        <i class="feather icon-dollar-sign"></i>
                    </div>
                    <div class="stat-title">Revenue</div>
                    <div class="stat-value">$24,521</div>
                    <div class="stat-change">+15% from last month</div>
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
                            <div class="activity-item d-flex">
                                <div class="activity-icon">
                                    <i class="feather icon-user-plus"></i>
                                </div>
                                <div class="activity-content">
                                    <div class="activity-title">New Host Registration</div>
                                    <div class="activity-time">2 hours ago</div>
                                </div>
                            </div>
                            <div class="activity-item d-flex">
                                <div class="activity-icon">
                                    <i class="feather icon-home"></i>
                                </div>
                                <div class="activity-content">
                                    <div class="activity-title">New Homestay Listed</div>
                                    <div class="activity-time">5 hours ago</div>
                                </div>
                            </div>
                            <div class="activity-item d-flex">
                                <div class="activity-icon">
                                    <i class="feather icon-check-circle"></i>
                                </div>
                                <div class="activity-content">
                                    <div class="activity-title">Payment Verified</div>
                                    <div class="activity-time">1 day ago</div>
                                </div>
                            </div>
                            <div class="activity-item d-flex">
                                <div class="activity-icon">
                                    <i class="feather icon-message-square"></i>
                                </div>
                                <div class="activity-content">
                                    <div class="activity-title">New Support Ticket</div>
                                    <div class="activity-time">2 days ago</div>
                                </div>
                            </div>
                            <div class="activity-item d-flex">
                                <div class="activity-icon">
                                    <i class="feather icon-star"></i>
                                </div>
                                <div class="activity-content">
                                    <div class="activity-title">New Review Posted</div>
                                    <div class="activity-time">3 days ago</div>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="card-footer">
                        <a href="#!" class="btn btn-primary btn-sm">View All Activity</a>
                    </div>
                </div>
            </div>
            
            <!-- Quick Links -->
            <div class="col-xl-4 col-md-12">
                <div class="card dashboard-card">
                    <div class="card-header">
                        <h5>Quick Links</h5>
                    </div>
                    <div class="card-body">
                        <div class="row">
                            <div class="col-md-6">
                                <a href="faq-management.html" class="btn btn-outline-primary btn-block mb-3">
                                    <i class="feather icon-help-circle mr-2"></i> Manage FAQs
                                </a>
                            </div>
                            <div class="col-md-6">
                                <a href="support-content.html" class="btn btn-outline-primary btn-block mb-3">
                                    <i class="feather icon-message-square mr-2"></i> Support Content
                                </a>
                            </div>
                            <div class="col-md-6">
                                <a href="traveler-fees.html" class="btn btn-outline-primary btn-block mb-3">
                                    <i class="feather icon-dollar-sign mr-2"></i> Traveler Fees
                                </a>
                            </div>
                            <div class="col-md-6">
                                <a href="opportunity-list.html" class="btn btn-outline-primary btn-block mb-3">
                                    <i class="feather icon-list mr-2"></i> Opportunities
                                </a>
                            </div>
                            <div class="col-md-6">
                                <a href="payment-verification.html" class="btn btn-outline-primary btn-block mb-3">
                                    <i class="feather icon-check-circle mr-2"></i> Payment Verification
                                </a>
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
    <script src="assets/js/ripple.js"></script>
    <script src="assets/js/pcoded.min.js"></script>
    <!-- Custom Js -->
    <script src="assets/js/custom.js"></script>
</body>

</html>