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
            <!-- Analytics Overview -->
            <div class="row g-4 mb-4">
                <div class="col-lg-3 col-md-6">
                    <div class="card border-0 shadow-sm">
                        <div class="card-body text-center">
                            <h3 class="text-primary mb-2">245</h3>
                            <p class="mb-0">Profile Views</p>
                        </div>
                    </div>
                </div>
                <div class="col-lg-3 col-md-6">
                    <div class="card border-0 shadow-sm">
                        <div class="card-body text-center">
                            <h3 class="text-primary mb-2">12</h3>
                            <p class="mb-0">Active Applications</p>
                        </div>
                    </div>
                </div>
                <div class="col-lg-3 col-md-6">
                    <div class="card border-0 shadow-sm">
                        <div class="card-body text-center">
                            <h3 class="text-primary mb-2">8</h3>
                            <p class="mb-0">Unread Messages</p>
                        </div>
                    </div>
                </div>
                <div class="col-lg-3 col-md-6">
                    <div class="card border-0 shadow-sm">
                        <div class="card-body text-center">
                            <h3 class="text-primary mb-2">4.8</h3>
                            <p class="mb-0">Average Rating</p>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Current Opportunities -->
            <div class="row g-4 mb-4">
                <div class="col-12">
                    <div class="card border-0 shadow-sm">
                        <div class="card-header bg-transparent">
                            <h5 class="mb-0">Current Opportunities</h5>
                        </div>
                        <div class="card-body">
                            <div class="table-responsive">
                                <table class="table table-hover">
                                    <thead>
                                        <tr>
                                            <th>Position</th>
                                            <th>Status</th>
                                            <th>Applications</th>
                                            <th>Duration</th>
                                            <th>Actions</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <tr>
                                            <td>English Teacher</td>
                                            <td><span class="badge bg-success">Available</span></td>
                                            <td>5 pending</td>
                                            <td>2 months</td>
                                            <td>
                                                <button class="btn btn-sm btn-primary">View</button>
                                                <button class="btn btn-sm btn-danger">Mark Filled</button>
                                            </td>
                                        </tr>
                                        <tr>
                                            <td>Childcare Helper</td>
                                            <td><span class="badge bg-danger">Filled</span></td>
                                            <td>3 pending</td>
                                            <td>3 months</td>
                                            <td>
                                                <button class="btn btn-sm btn-primary">View</button>
                                                <button class="btn btn-sm btn-success">Mark Available</button>
                                            </td>
                                        </tr>
                                        <tr>
                                            <td>Garden Assistant</td>
                                            <td><span class="badge bg-success">Available</span></td>
                                            <td>2 pending</td>
                                            <td>1 month</td>
                                            <td>
                                                <button class="btn btn-sm btn-primary">View</button>
                                                <button class="btn btn-sm btn-danger">Mark Filled</button>
                                            </td>
                                        </tr>
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Recent Applications -->
            <div class="row g-4 mb-4">
                <div class="col-12">
                    <div class="card border-0 shadow-sm">
                        <div class="card-header bg-transparent">
                            <h5 class="mb-0">Recent Applications</h5>
                        </div>
                        <div class="card-body">
                            <div class="row g-4">
                                <div class="col-lg-4">
                                    <div class="card border-0 shadow-sm">
                                        <div class="card-body">
                                            <div class="d-flex align-items-center mb-3">
                                                <img src="../img/volunteer-1.jpg" class="rounded-circle me-3" style="width: 50px; height: 50px;" alt="Volunteer">
                                                <div>
                                                    <h6 class="mb-0">Sarah Johnson</h6>
                                                    <small class="text-muted">English Teacher</small>
                                                </div>
                                            </div>
                                            <p class="mb-3">Experienced English teacher looking for cultural exchange opportunity.</p>
                                            <div class="d-flex justify-content-between">
                                                <button class="btn btn-sm btn-success">Accept</button>
                                                <button class="btn btn-sm btn-danger">Decline</button>
                                                <button class="btn btn-sm btn-primary">View Profile</button>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                <div class="col-lg-4">
                                    <div class="card border-0 shadow-sm">
                                        <div class="card-body">
                                            <div class="d-flex align-items-center mb-3">
                                                <img src="../img/volunteer-2.jpg" class="rounded-circle me-3" style="width: 50px; height: 50px;" alt="Volunteer">
                                                <div>
                                                    <h6 class="mb-0">Michael Chen</h6>
                                                    <small class="text-muted">Garden Assistant</small>
                                                </div>
                                            </div>
                                            <p class="mb-3">Passionate about gardening and cultural exchange.</p>
                                            <div class="d-flex justify-content-between">
                                                <button class="btn btn-sm btn-success">Accept</button>
                                                <button class="btn btn-sm btn-danger">Decline</button>
                                                <button class="btn btn-sm btn-primary">View Profile</button>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                <div class="col-lg-4">
                                    <div class="card border-0 shadow-sm">
                                        <div class="card-body">
                                            <div class="d-flex align-items-center mb-3">
                                                <img src="../img/volunteer-3.jpg" class="rounded-circle me-3" style="width: 50px; height: 50px;" alt="Volunteer">
                                                <div>
                                                    <h6 class="mb-0">Emma Wilson</h6>
                                                    <small class="text-muted">Childcare Helper</small>
                                                </div>
                                            </div>
                                            <p class="mb-3">Experienced with children and interested in cultural exchange.</p>
                                            <div class="d-flex justify-content-between">
                                                <button class="btn btn-sm btn-success">Accept</button>
                                                <button class="btn btn-sm btn-danger">Decline</button>
                                                <button class="btn btn-sm btn-primary">View Profile</button>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Recent Messages -->
            <div class="row g-4">
                <div class="col-12">
                    <div class="card border-0 shadow-sm">
                        <div class="card-header bg-transparent">
                            <h5 class="mb-0">Recent Messages</h5>
                        </div>
                        <div class="card-body">
                            <div class="list-group">
                                <a href="#" class="list-group-item list-group-item-action">
                                    <div class="d-flex w-100 justify-content-between">
                                        <h6 class="mb-1">Sarah Johnson</h6>
                                        <small>3 mins ago</small>
                                    </div>
                                    <p class="mb-1">Hi! I'm very interested in the English teaching position...</p>
                                </a>
                                <a href="#" class="list-group-item list-group-item-action">
                                    <div class="d-flex w-100 justify-content-between">
                                        <h6 class="mb-1">Michael Chen</h6>
                                        <small>1 hour ago</small>
                                    </div>
                                    <p class="mb-1">Thank you for considering my application...</p>
                                </a>
                                <a href="#" class="list-group-item list-group-item-action">
                                    <div class="d-flex w-100 justify-content-between">
                                        <h6 class="mb-1">Emma Wilson</h6>
                                        <small>2 hours ago</small>
                                    </div>
                                    <p class="mb-1">I have some questions about the childcare position...</p>
                                </a>
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