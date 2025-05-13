<?php
session_start();
if (!isset($_SESSION['userID']) || $_SESSION['userType'] !== 'traveler') {
    header("Location: ../Common/login.php");
    exit;
}
if (!isset($_SESSION['auth_token'])) {
    $_SESSION['auth_token'] = bin2hex(random_bytes(32));
}
?>
<!DOCTYPE html>
<html lang="en">

    <head>
        <meta charset="utf-8">
        <title>HomeStays - Connect with Local Hosts</title>
        <meta content="width=device-width, initial-scale=1.0" name="viewport">
        <link rel="stylesheet" href="https://use.fontawesome.com/releases/v5.15.4/css/all.css"/>
        <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.4.1/font/bootstrap-icons.css" rel="stylesheet">
        <link href="../css/bootstrap.min.css" rel="stylesheet">
        <link href="../css/style.css" rel="stylesheet">
        <style>
            .navbar-light {
            position: absolute !important;
            }
        </style>
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
        <div class="container-fluid bg-primary px-5 d-none d-lg-block">
            <div class="row gx-0">
                <div class="col-lg-8 text-center text-lg-start mb-2 mb-lg-0">
                    <div class="d-inline-flex align-items-center" style="height: 45px;">
                        <a class="btn btn-sm btn-outline-light btn-sm-square rounded-circle me-2" href=""><i class="fab fa-twitter fw-normal"></i></a>
                        <a class="btn btn-sm btn-outline-light btn-sm-square rounded-circle me-2" href=""><i class="fab fa-facebook-f fw-normal"></i></a>
                        <a class="btn btn-sm btn-outline-light btn-sm-square rounded-circle me-2" href=""><i class="fab fa-linkedin-in fw-normal"></i></a>
                        <a class="btn btn-sm btn-outline-light btn-sm-square rounded-circle me-2" href=""><i class="fab fa-instagram fw-normal"></i></a>
                        <a class="btn btn-sm btn-outline-light btn-sm-square rounded-circle" href=""><i class="fab fa-youtube fw-normal"></i></a>
                    </div>
                </div>
                <div class="col-lg-4 text-center text-lg-end">
                    <div class="d-inline-flex align-items-center" style="height: 45px;">
                        <a href="#"><small class="me-3 text-light"><i class="fa fa-user me-2"></i>My Profile</small></a>
                        <a href="#"><small class="me-3 text-light"><i class="fa fa-sign-in-alt me-2"></i>Logout</small></a>
                        <div class="dropdown">
                            <a href="#" class="dropdown-toggle text-light" data-bs-toggle="dropdown"><small><i class="fa fa-home me-2"></i> My Dashboard</small></a>
                            <div class="dropdown-menu rounded">
                                <a href="#" class="dropdown-item"><i class="fas fa-user-alt me-2"></i> My Profile</a>
                                <a href="#" class="dropdown-item"><i class="fas fa-comment-alt me-2"></i> Messages</a>
                                <a href="#" class="dropdown-item"><i class="fas fa-bell me-2"></i> Notifications</a>
                                <a href="#" class="dropdown-item"><i class="fas fa-cog me-2"></i> Settings</a>
                                <a href="#" class="dropdown-item"><i class="fas fa-power-off me-2"></i> Log Out</a>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <!-- Topbar End -->

        <!-- Navbar & Hero Start -->
        <div class="container-fluid position-relative p-0">
            <?php include 'navTraveler.php'; ?>
                     <!-- Carousel Start -->
            <div class="carousel-header">
                <div id="carouselId" class="carousel slide" data-bs-ride="carousel">
                    <ol class="carousel-indicators">
                        <li data-bs-target="#carouselId" data-bs-slide-to="0" class="active"></li>
                        <li data-bs-target="#carouselId" data-bs-slide-to="1"></li>
                        <li data-bs-target="#carouselId" data-bs-slide-to="2"></li>
                    </ol>
                    <div class="carousel-inner" role="listbox">
                        <div class="carousel-item active">
                            <img src="../img/carousel-2.jpg" class="img-fluid" alt="Image">
                            <div class="carousel-caption">
                                <div class="p-3" style="max-width: 900px;">
                                    <h4 class="text-white text-uppercase fw-bold mb-4" style="letter-spacing: 3px;">Connect with Local Hosts</h4>
                                    <h1 class="display-2 text-capitalize text-white mb-4">Experience Authentic Cultural Exchange</h1>
                                    <p class="mb-5 fs-5">Live with local hosts and immerse yourself in their culture, traditions, and way of life.</p>
                                    <div class="d-flex align-items-center justify-content-center">
                                        <a class="btn-hover-bg btn btn-primary rounded-pill text-white py-3 px-5" href="exchange.php">Find Homestays</a>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="carousel-item">
                            <img src="../img/carousel-1.jpg" class="img-fluid" alt="Image">
                            <div class="carousel-caption">
                                <div class="p-3" style="max-width: 900px;">
                                    <h4 class="text-white text-uppercase fw-bold mb-4" style="letter-spacing: 3px;">Local Experience</h4>
                                    <h1 class="display-2 text-capitalize text-white mb-4">Live Like a Local</h1>
                                    <p class="mb-5 fs-5">Share meals, stories, and experiences with your host family. Learn about local customs and traditions.</p>
                                    <div class="d-flex align-items-center justify-content-center">
                                        <a class="btn-hover-bg btn btn-primary rounded-pill text-white py-3 px-5" href="exchange.php">Explore Now</a>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="carousel-item">
                            <img src="../img/carousel-3.jpg" class="img-fluid" alt="Image">
                            <div class="carousel-caption">
                                <div class="p-3" style="max-width: 900px;">
                                    <h4 class="text-white text-uppercase fw-bold mb-4" style="letter-spacing: 3px;">Cultural Exchange</h4>
                                    <h1 class="display-2 text-capitalize text-white mb-4">Share Your Culture</h1>
                                    <p class="mb-5 fs-5">Exchange cultural experiences, learn new languages, and make lifelong connections with hosts worldwide.</p>
                                    <div class="d-flex align-items-center justify-content-center">
                                        <a class="btn-hover-bg btn btn-primary rounded-pill text-white py-3 px-5" href="exchange.php">Start Your Journey</a>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    <button class="carousel-control-prev" type="button" data-bs-target="#carouselId" data-bs-slide="prev">
                        <span class="carousel-control-prev-icon btn bg-primary" aria-hidden="false"></span>
                        <span class="visually-hidden">Previous</span>
                    </button>
                    <button class="carousel-control-next" type="button" data-bs-target="#carouselId" data-bs-slide="next">
                        <span class="carousel-control-next-icon btn bg-primary" aria-hidden="false"></span>
                        <span class="visually-hidden">Next</span>
                    </button>
                </div>
            </div>
            <!-- Carousel End -->
        </div>
        <div class="container-fluid search-bar position-relative" style="top: -50%; transform: translateY(-50%);">
            <div class="container">
                <div class="position-relative rounded-pill w-100 mx-auto p-5" style="background: rgba(19, 53, 123, 0.8);">
                    <div class="row g-3">
                        <div class="col-lg-4">
                            <input class="form-control border-0 rounded-pill w-100 py-3 ps-4" type="text" placeholder="Where do you want to exchange?">
                        </div>
                        <div class="col-lg-3">
                            <select class="form-select border-0 rounded-pill w-100 py-3 ps-4">
                                <option selected disabled>What can you offer?</option>
                                <option>Teaching Languages</option>
                                <option>Childcare</option>
                                <option>Household Help</option>
                                <option>Gardening</option>
                                <option>Computer Skills</option>
                                <option>Art & Music</option>
                                <option>Other Skills</option>
                            </select>
                        </div>
                        <div class="col-lg-3">
                            <select class="form-select border-0 rounded-pill w-100 py-3 ps-4">
                                <option selected disabled>Duration</option>
                                <option>1-2 Weeks</option>
                                <option>2-4 Weeks</option>
                                <option>1-3 Months</option>
                                <option>3-6 Months</option>
                                <option>6+ Months</option>
                            </select>
                        </div>
                        <div class="col-lg-2">
                            <button type="button" class="btn btn-primary rounded-pill w-100 py-3">Find Exchange</button>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <!-- Navbar & Hero End -->

        <!-- Featured Homestays Start -->
        <div class="container-fluid py-5">
            <div class="container py-5">
                <div class="mx-auto text-center mb-5" style="max-width: 900px;">
                    <h5 class="section-title px-3">Featured Homestays</h5>
                    <h1 class="mb-0">Popular Cultural Experiences</h1>
                </div>
                <div class="row g-4">
                    <div class="col-lg-4 col-md-6">
                        <div class="packages-item bg-white">
                            <div class="packages-img">
                                <img src="../img/packages-1.jpg" class="img-fluid" alt="Image">
                                <div class="packages-price">
                                    <small>Cultural Exchange</small>
                                    <h2 class="text-primary">Service Exchange</h2>
                                    <small>Stay with hosts</small>
                                </div>
                            </div>
                            <div class="packages-content p-4">
                                <h3 class="mb-2">Traditional Family Home</h3>
                                <p class="mb-3">Experience authentic local living with a welcoming family in their traditional home. Exchange cultural knowledge and skills.</p>
                                <div class="d-flex justify-content-between">
                                    <a href="#" class="btn btn-primary rounded-pill py-2 px-4">Apply Now</a>
                                    <a href="details.html" class="btn btn-outline-primary rounded-pill py-2 px-4">Details</a>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="col-lg-4 col-md-6">
                        <div class="packages-item bg-white">
                            <div class="packages-img">
                                <img src="../img/packages-2.jpg" class="img-fluid" alt="Image">
                                <div class="packages-price">
                                    <small>Cultural Exchange</small>
                                    <h2 class="text-primary">Service Exchange</h2>
                                    <small>Stay with hosts</small>
                                </div>
                            </div>
                            <div class="packages-content p-4">
                                <h3 class="mb-2">Cultural Exchange Home</h3>
                                <p class="mb-3">Join a multicultural family and learn about their traditions and customs. Share your skills and knowledge.</p>
                                <div class="d-flex justify-content-between">
                                    <a href="#" class="btn btn-primary rounded-pill py-2 px-4">Apply Now</a>
                                    <a href="details.html" class="btn btn-outline-primary rounded-pill py-2 px-4">Details</a>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="col-lg-4 col-md-6">
                        <div class="packages-item bg-white">
                            <div class="packages-img">
                                <img src="../img/packages-3.jpg" class="img-fluid" alt="Image">
                                <div class="packages-price">
                                    <small>Cultural Exchange</small>
                                    <h2 class="text-primary">Service Exchange</h2>
                                    <small>Stay with hosts</small>
                                </div>
                            </div>
                            <div class="packages-content p-4">
                                <h3 class="mb-2">Local Experience Home</h3>
                                <p class="mb-3">Immerse yourself in local culture with a family passionate about sharing their heritage. Exchange skills and experiences.</p>
                                <div class="d-flex justify-content-between">
                                    <a href="#" class="btn btn-primary rounded-pill py-2 px-4">Apply Now</a>
                                    <a href="details.html" class="btn btn-outline-primary rounded-pill py-2 px-4">Details</a>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <!-- Featured Homestays End -->

        <!-- Services Start -->
        <div class="container-fluid bg-light service py-5">
            <div class="container py-5">
                <div class="mx-auto text-center mb-5" style="max-width: 900px;">
                    <h5 class="section-title px-3">Our Services</h5>
                    <h1 class="mb-0">What We Offer</h1>
                </div>
                <div class="row g-4">
                    <div class="col-lg-6">
                        <div class="row g-4">
                            <div class="col-12">
                                <div class="service-content-inner d-flex align-items-center bg-white border border-primary rounded p-4 pe-0">
                                    <div class="service-content text-end">
                                        <h5 class="mb-4">Easy Booking</h5>
                                        <p class="mb-0">Simple and secure booking process for your homestay experience</p>
                                    </div>
                                    <div class="service-icon bg-primary rounded-circle ms-4">
                                        <i class="fa fa-calendar-check text-white"></i>
                                    </div>
                                </div>
                            </div>
                            <div class="col-12">
                                <div class="service-content-inner d-flex align-items-center bg-white border border-primary rounded p-4 pe-0">
                                    <div class="service-content text-end">
                                        <h5 class="mb-4">Cultural Activities</h5>
                                        <p class="mb-0">Participate in local activities and cultural events with your host family</p>
                                    </div>
                                    <div class="service-icon bg-primary rounded-circle ms-4">
                                        <i class="fa fa-users text-white"></i>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="col-lg-6">
                        <div class="row g-4">
                            <div class="col-12">
                                <div class="service-content-inner d-flex align-items-center bg-white border border-primary rounded p-4 ps-0">
                                    <div class="service-icon bg-primary rounded-circle me-4">
                                        <i class="fa fa-shield-alt text-white"></i>
                                    </div>
                                    <div class="service-content">
                                        <h5 class="mb-4">Verified Hosts</h5>
                                        <p class="mb-0">All our hosts are carefully verified to ensure your safety and comfort</p>
                                    </div>
                                </div>
                            </div>
                            <div class="col-12">
                                <div class="service-content-inner d-flex align-items-center bg-white border border-primary rounded p-4 ps-0">
                                    <div class="service-icon bg-primary rounded-circle me-4">
                                        <i class="fa fa-comments text-white"></i>
                                    </div>
                                    <div class="service-content">
                                        <h5 class="mb-4">Language Exchange</h5>
                                        <p class="mb-0">Practice languages and learn about different cultures through daily interactions</p>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <!-- Services End -->

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

