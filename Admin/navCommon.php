<?php
// Include the DBController if not already included
if (!class_exists('DBController')) {
    include_once '../Controllers/DBController.php';
}

// Default values
$navUserId = 0;
$navAdminName = "Admin"; // Default name

// Get admin data from database if user is logged in
if (isset($_SESSION['userID'])) {
    $navUserId = $_SESSION['userID'];

    $navDb = new DBController();
    if ($navDb->openConnection()) {
        $navQuery = "SELECT first_name, last_name FROM users WHERE user_id = ? AND user_type = 'admin'";
        $navParams = [$navUserId];
        $navResult = $navDb->selectPrepared($navQuery, "i", $navParams);

        if ($navResult && count($navResult) > 0) {
            $navAdminName = $navResult[0]['first_name'] . ' ' . $navResult[0]['last_name'];
        }

        $navDb->closeConnection();
    }
}
?>
	<!-- [ Pre-loader ] start -->
	<div class="loader-bg">
		<div class="loader-track">
			<div class="loader-fill"></div>
		</div>
	</div>
	<!-- [ Pre-loader ] End -->
	<!-- [ navigation menu ] start -->
	<nav class="pcoded-navbar menu-light">
		<div class="navbar-wrapper">
			<div class="navbar-content scroll-div">
				<div class="sidebar-user">
					<div class="main-menu-header">
						<div class="user-image">
							<img class="img-radius" src="../Controllers/GetProfileImg.php?user_id=<?= $navUserId ?>" alt="Admin-Profile-Image">
							<div class="user-status online"></div>
						</div>
						<div class="user-details">
							<div class="user-name"><?= htmlspecialchars($navAdminName) ?></div>
						</div>
					</div>
					<div class="sidebar-user-actions">
						<div class="sidebar-user-links">
							<a href="admin-profile.php" class="sidebar-user-link" title="View Profile">
								<i class="feather icon-user"></i>
							</a>
							<a href="../Common/logout.php" class="sidebar-user-link" title="Logout">
								<i class="feather icon-log-out"></i>
							</a>
						</div>
					</div>
				</div>

				<ul class="nav pcoded-inner-navbar">
					<li class="nav-item pcoded-menu-caption">
						<label>Navigation</label>
					</li>
					<li class="nav-item">
						<a href="index.php" class="nav-link"><span class="pcoded-micon"><i class="feather icon-home"></i></span><span class="pcoded-mtext">Dashboard</span></a>
					</li>

					<!-- Homestay Opportunities -->
					<li class="nav-item pcoded-menu-caption">
						<label>Homestay Opportunities</label>
					</li>
					<li class="nav-item">
						<a href="opportunity-list.php" class="nav-link"><span class="pcoded-micon"><i class="feather icon-list"></i></span><span class="pcoded-mtext">View Opportunities</span></a>
					</li>



					<!-- FAQ Management -->
					<li class="nav-item pcoded-menu-caption">
						<label>FAQ Management</label>
					</li>
					<li class="nav-item">
						<a href="faq-management.php" class="nav-link"><span class="pcoded-micon"><i class="feather icon-help-circle"></i></span><span class="pcoded-mtext">Manage FAQs</span></a>
					</li>

					<!-- Fee Management -->
					<li class="nav-item pcoded-menu-caption">
						<label>Fee Management</label>
					</li>
					<li class="nav-item">
						<a href="traveler-fees.php" class="nav-link"><span class="pcoded-micon"><i class="feather icon-credit-card"></i></span><span class="pcoded-mtext">Traveler Fees</span></a>
					</li>
					<li class="nav-item">
						<a href="payment-verification.php" class="nav-link"><span class="pcoded-micon"><i class="feather icon-check-circle"></i></span><span class="pcoded-mtext">Payment Verification</span></a>
					</li>

					<!-- User Reports -->
					<li class="nav-item pcoded-menu-caption">
						<label>User Reports</label>
					</li>
					<li class="nav-item">
						<a href="user-reports.php" class="nav-link"><span class="pcoded-micon"><i class="feather icon-flag"></i></span><span class="pcoded-mtext">Manage Reports</span></a>
					</li>
				</ul>
			</div>
		</div>
	</nav>
	<!-- [ navigation menu ] end -->
	<!-- [ Header ] start -->
	<header class="navbar pcoded-header navbar-expand-lg navbar-light header-blue">


				<div class="m-header">
					<a class="mobile-menu" id="mobile-collapse" href="#!"><span></span></a>
					<a href="#!" class="b-brand">
						<!-- ========   change your logo hear   ============ -->
                        <img src="assets/images/logo_main.svg" alt="HomeStay Logo" width="120" class="logo">

					</a>
					<a href="#!" class="mob-toggler">
						<i class="feather icon-more-vertical"></i>
					</a>
				</div>
				<div class="collapse navbar-collapse">
				</div>


	</header>
	<!-- [ Header ] end -->