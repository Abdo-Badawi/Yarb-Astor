<!DOCTYPE html>
<html lang="en">

    <head>
        <meta charset="utf-8">
        <title>HomeStays - My Exchange Opportunities</title>
        <meta content="width=device-width, initial-scale=1.0" name="viewport">
        <meta content="homestays, cultural exchange, local experience, authentic travel" name="keywords">
        <meta content="Manage your cultural exchange opportunities and service exchanges" name="description">

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

        <!-- Topbar Start -->
        
        <!-- Topbar End -->

        <!-- Navbar Start -->
        <?php include 'navTraveler.php'; ?>
        <!-- Navbar End -->
     
        <!-- Exchange Opportunities Start -->
        <div class="container-fluid py-5">
            <div class="container py-5">
                <div class="mx-auto text-center mb-5" style="max-width: 900px;">
                    <h5 class="section-title px-3">My Exchange Opportunities</h5>
                    <h1 class="mb-0">Current and Upcoming Cultural Exchanges</h1>
                </div>
                <div class="row g-4">
                    <!-- Upcoming Exchange -->
                    <div class="col-lg-6">
                        <div class="card border-0 shadow-sm">
                            <div class="card-body p-4">
                                <div class="d-flex justify-content-between mb-3">
                                    <h5 class="card-title mb-0">Upcoming Exchange</h5>
                                    <span class="badge bg-primary">Confirmed</span>
                                </div>
                                <div class="d-flex align-items-center mb-3">
                                    <img src="../img/host-1.jpg" class="rounded-circle me-3" style="width: 50px; height: 50px;" alt="Host">
                                    <div>
                                        <h6 class="mb-0">Maria's Family</h6>
                                        <small class="text-muted">Barcelona, Spain</small>
                                    </div>
                                </div>
                                <div class="mb-3">
                                    <p class="mb-2"><i class="fa fa-calendar me-2"></i>Duration: 2 weeks</p>
                                    <p class="mb-2"><i class="fa fa-clock me-2"></i>Start Date: June 1, 2024</p>
                                    <p class="mb-2"><i class="fa fa-tasks me-2"></i>Service: Teaching English</p>
                                    <p class="mb-2"><i class="fa fa-language me-2"></i>Languages: Spanish (Host), English (You)</p>
                                    <p class="mb-2"><i class="fa fa-users me-2"></i>Family: Parents + 2 children (8 & 10)</p>
                                    <div class="mt-3">
                                        <h6 class="mb-2">Cultural Activities:</h6>
                                        <ul class="list-unstyled">
                                            <li><i class="fa fa-check text-primary me-2"></i>Local cooking classes</li>
                                            <li><i class="fa fa-check text-primary me-2"></i>Flamenco dance lessons</li>
                                            <li><i class="fa fa-check text-primary me-2"></i>Weekend family trips</li>
                                        </ul>
                                    </div>
                                </div>
                                <div class="d-flex justify-content-between">
                                    <button class="btn btn-outline-primary">View Details</button>
                                    <button class="btn btn-primary">Contact Host</button>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Pending Exchange -->
                    <div class="col-lg-6">
                        <div class="card border-0 shadow-sm">
                            <div class="card-body p-4">
                                <div class="d-flex justify-content-between mb-3">
                                    <h5 class="card-title mb-0">Pending Exchange</h5>
                                    <span class="badge bg-warning">Under Review</span>
                                </div>
                                <div class="d-flex align-items-center mb-3">
                                    <img src="../img/host-2.jpg" class="rounded-circle me-3" style="width: 50px; height: 50px;" alt="Host">
                                    <div>
                                        <h6 class="mb-0">Yuki's Family</h6>
                                        <small class="text-muted">Tokyo, Japan</small>
                                    </div>
                                </div>
                                <div class="mb-3">
                                    <p class="mb-2"><i class="fa fa-calendar me-2"></i>Duration: 1 month</p>
                                    <p class="mb-2"><i class="fa fa-clock me-2"></i>Start Date: July 15, 2024</p>
                                    <p class="mb-2"><i class="fa fa-tasks me-2"></i>Service: Childcare & Language Exchange</p>
                                    <p class="mb-2"><i class="fa fa-language me-2"></i>Languages: Japanese (Host), English (You)</p>
                                    <p class="mb-2"><i class="fa fa-users me-2"></i>Family: Single parent + 1 child (6)</p>
                                    <div class="mt-3">
                                        <h6 class="mb-2">Cultural Activities:</h6>
                                        <ul class="list-unstyled">
                                            <li><i class="fa fa-check text-primary me-2"></i>Traditional tea ceremonies</li>
                                            <li><i class="fa fa-check text-primary me-2"></i>Japanese calligraphy</li>
                                            <li><i class="fa fa-check text-primary me-2"></i>Local festivals</li>
                                        </ul>
                                    </div>
                                </div>
                                <div class="d-flex justify-content-between">
                                    <button class="btn btn-outline-primary">View Details</button>
                                    <button class="btn btn-primary">Contact Host</button>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Past Exchange -->
                    <div class="col-lg-6">
                        <div class="card border-0 shadow-sm">
                            <div class="card-body p-4">
                                <div class="d-flex justify-content-between mb-3">
                                    <h5 class="card-title mb-0">Past Exchange</h5>
                                    <span class="badge bg-success">Completed</span>
                                </div>
                                <div class="d-flex align-items-center mb-3">
                                    <img src="../img/host-3.jpg" class="rounded-circle me-3" style="width: 50px; height: 50px;" alt="Host">
                                    <div>
                                        <h6 class="mb-0">Pierre's Family</h6>
                                        <small class="text-muted">Paris, France</small>
                                    </div>
                                </div>
                                <div class="mb-3">
                                    <p class="mb-2"><i class="fa fa-calendar me-2"></i>Duration: 3 weeks</p>
                                    <p class="mb-2"><i class="fa fa-clock me-2"></i>Completed: March 2024</p>
                                    <p class="mb-2"><i class="fa fa-tasks me-2"></i>Service: Gardening & Cultural Exchange</p>
                                    <p class="mb-2"><i class="fa fa-language me-2"></i>Languages: French (Host), English (You)</p>
                                    <p class="mb-2"><i class="fa fa-users me-2"></i>Family: Retired couple</p>
                                    <div class="mt-3">
                                        <h6 class="mb-2">Cultural Activities:</h6>
                                        <ul class="list-unstyled">
                                            <li><i class="fa fa-check text-primary me-2"></i>French cooking classes</li>
                                            <li><i class="fa fa-check text-primary me-2"></i>Wine tasting</li>
                                            <li><i class="fa fa-check text-primary me-2"></i>Art gallery visits</li>
                                        </ul>
                                    </div>
                                </div>
                                <div class="d-flex justify-content-between">
                                    <button class="btn btn-outline-primary">View Details</button>
                                    <button class="btn btn-primary">Leave Review</button>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <!-- Exchange Opportunities End -->

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