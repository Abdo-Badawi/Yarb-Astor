<!DOCTYPE html>
<html lang="en">

    <head>
        <meta charset="utf-8">
        <title>Travela - Homestay Details</title>
        <meta content="width=device-width, initial-scale=1.0" name="viewport">
        <meta content="" name="keywords">
        <meta content="" name="description">

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
        <nav class="navbar navbar-expand-lg navbar-light px-4 px-lg-5 py-3 py-lg-0">
            <a href="" class="navbar-brand p-0">
                <h1 class="m-0"><i class="fa fa-map-marker-alt me-3"></i>Travela</h1>
            </a>
            <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarCollapse">
                <span class="fa fa-bars"></span>
            </button>
            <div class="collapse navbar-collapse" id="navbarCollapse">
                <div class="navbar-nav ms-auto py-0">
                    <a href="index.html" class="nav-item nav-link">Home</a>
                    <a href="bookings.html" class="nav-item nav-link">My Bookings</a>
                    <a href="reviews.html" class="nav-item nav-link">My Reviews</a>
                    <a href="profile.html" class="nav-item nav-link">Profile</a>
                    <a href="contact.html" class="nav-item nav-link">Contact</a>
                </div>
                <a href="search.html" class="btn btn-primary rounded-pill py-2 px-4 ms-lg-4">Search Stays</a>
            </div>
        </nav>
        <!-- Navbar End -->

        <!-- Header Start -->
        <div class="container-fluid bg-breadcrumb">
            <div class="container text-center py-5" style="max-width: 900px;">
                <h3 class="text-white display-3 mb-4">Luxury Villa</h3>
                <ol class="breadcrumb justify-content-center mb-0">
                    <li class="breadcrumb-item"><a href="index.html">Home</a></li>
                    <li class="breadcrumb-item"><a href="search.html">Search</a></li>
                    <li class="breadcrumb-item active text-white">Luxury Villa</li>
                </ol>
            </div>
        </div>
        <!-- Header End -->

        <!-- Details Start -->
        <div class="container-fluid py-5">
            <div class="container py-5">
                <div class="row g-5">
                    <!-- Left Column -->
                    <div class="col-lg-8">
                        <!-- Image Gallery -->
                        <div class="row g-3 mb-4">
                            <div class="col-12">
                                <img src="../img/packages-1.jpg" class="img-fluid rounded" alt="Main Image">
                            </div>
                            <div class="col-4">
                                <img src="../img/packages-2.jpg" class="img-fluid rounded" alt="Gallery Image">
                            </div>
                            <div class="col-4">
                                <img src="../img/packages-3.jpg" class="img-fluid rounded" alt="Gallery Image">
                            </div>
                            <div class="col-4">
                                <img src="../img/packages-4.jpg" class="img-fluid rounded" alt="Gallery Image">
                            </div>
                        </div>

                        <!-- Description -->
                        <div class="mb-4">
                            <h4 class="mb-3">About This Place</h4>
                            <p>Experience luxury living in this beautiful villa with stunning views. This spacious property offers modern amenities and comfortable living spaces perfect for your stay.</p>
                            <p>Located in a prime area, this villa provides easy access to local attractions while maintaining privacy and tranquility.</p>
                        </div>

                        <!-- Amenities -->
                        <div class="mb-4">
                            <h4 class="mb-3">Amenities</h4>
                            <div class="row g-3">
                                <div class="col-md-6">
                                    <ul class="list-unstyled">
                                        <li><i class="fa fa-check text-primary me-2"></i>Free Wi-Fi</li>
                                        <li><i class="fa fa-check text-primary me-2"></i>Air Conditioning</li>
                                        <li><i class="fa fa-check text-primary me-2"></i>Kitchen</li>
                                        <li><i class="fa fa-check text-primary me-2"></i>Swimming Pool</li>
                                    </ul>
                                </div>
                                <div class="col-md-6">
                                    <ul class="list-unstyled">
                                        <li><i class="fa fa-check text-primary me-2"></i>Parking</li>
                                        <li><i class="fa fa-check text-primary me-2"></i>Garden</li>
                                        <li><i class="fa fa-check text-primary me-2"></i>Security</li>
                                        <li><i class="fa fa-check text-primary me-2"></i>24/7 Support</li>
                                    </ul>
                                </div>
                            </div>
                        </div>

                        <!-- Location -->
                        <div class="mb-4">
                            <h4 class="mb-3">Location</h4>
                            <p><i class="fa fa-map-marker-alt text-primary me-2"></i>123 Luxury Street, City Name, Country</p>
                            <div class="ratio ratio-21x9">
                                <iframe src="https://www.google.com/maps/embed?pb=!1m18!1m12!1m3!1d387193.30591910525!2d-74.25986432970718!3d40.697149422113014!2m3!1f0!2f0!3f0!3m2!1i1024!2i768!4f13.1!3m3!1m2!1s0x89c24fa5d33f083b%3A0xc80b8f06e177fe62!2sNew%20York%2C%20NY%2C%20USA!5e0!3m2!1sen!2s!4v1645564757466!5m2!1sen!2s" allowfullscreen="" loading="lazy"></iframe>
                            </div>
                        </div>

                        <!-- Reviews -->
                        <div class="mb-4">
                            <h4 class="mb-3">Guest Reviews</h4>
                            <div class="d-flex mb-3">
                                <div class="text-warning">
                                    <i class="fas fa-star"></i>
                                    <i class="fas fa-star"></i>
                                    <i class="fas fa-star"></i>
                                    <i class="fas fa-star"></i>
                                    <i class="fas fa-star"></i>
                                </div>
                                <span class="ms-2">4.9 (120 reviews)</span>
                            </div>
                            <div class="border-bottom pb-3 mb-3">
                                <div class="d-flex justify-content-between">
                                    <h6 class="mb-1">John Doe</h6>
                                    <small>2 days ago</small>
                                </div>
                                <div class="text-warning mb-2">
                                    <i class="fas fa-star"></i>
                                    <i class="fas fa-star"></i>
                                    <i class="fas fa-star"></i>
                                    <i class="fas fa-star"></i>
                                    <i class="fas fa-star"></i>
                                </div>
                                <p>Amazing stay! The villa was exactly as described and the host was very helpful.</p>
                            </div>
                            <div class="border-bottom pb-3">
                                <div class="d-flex justify-content-between">
                                    <h6 class="mb-1">Jane Smith</h6>
                                    <small>1 week ago</small>
                                </div>
                                <div class="text-warning mb-2">
                                    <i class="fas fa-star"></i>
                                    <i class="fas fa-star"></i>
                                    <i class="fas fa-star"></i>
                                    <i class="fas fa-star"></i>
                                    <i class="fas fa-star-half-alt"></i>
                                </div>
                                <p>Beautiful property with great amenities. Would definitely stay again!</p>
                            </div>
                        </div>
                    </div>

                    <!-- Right Column -->
                    <div class="col-lg-4">
                        <!-- Booking Card -->
                        <div class="bg-white p-4 rounded shadow-sm">
                            <h4 class="mb-4">Book Your Stay</h4>
                            <div class="mb-3">
                                <h2 class="text-primary">$99<small>/night</small></h2>
                            </div>
                            <form>
                                <div class="mb-3">
                                    <label class="form-label">Check-in Date</label>
                                    <input type="date" class="form-control">
                                </div>
                                <div class="mb-3">
                                    <label class="form-label">Check-out Date</label>
                                    <input type="date" class="form-control">
                                </div>
                                <div class="mb-3">
                                    <label class="form-label">Guests</label>
                                    <select class="form-select">
                                        <option>1 Guest</option>
                                        <option>2 Guests</option>
                                        <option>3 Guests</option>
                                        <option>4 Guests</option>
                                        <option>5+ Guests</option>
                                    </select>
                                </div>
                                <div class="mb-3">
                                    <label class="form-label">Total Price</label>
                                    <h4 class="text-primary">$297</h4>
                                    <small class="text-muted">3 nights x $99</small>
                                </div>
                                <button type="submit" class="btn btn-primary w-100 py-3">Book Now</button>
                            </form>
                        </div>

                        <!-- Host Info -->
                        <div class="bg-white p-4 rounded shadow-sm mt-4">
                            <h4 class="mb-4">Host Information</h4>
                            <div class="d-flex align-items-center mb-3">
                                <img src="../img/testimonial-1.jpg" class="rounded-circle me-3" width="60" height="60" alt="Host">
                                <div>
                                    <h6 class="mb-1">Sarah Johnson</h6>
                                    <small class="text-muted">Superhost</small>
                                </div>
                            </div>
                            <p class="mb-3">Professional host with 5 years of experience. Always available to help make your stay memorable.</p>
                            <div class="d-flex justify-content-between">
                                <div>
                                    <small class="text-muted">Response time</small>
                                    <h6 class="mb-0">Within an hour</h6>
                                </div>
                                <div>
                                    <small class="text-muted">Response rate</small>
                                    <h6 class="mb-0">98%</h6>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <!-- Details End -->

        <!-- Footer Start -->
        <?php include '../Common/footer.php'; ?>

        <!-- JavaScript Libraries -->
        <script src="https://code.jquery.com/jquery-3.4.1.min.js"></script>
        <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.0.0/dist/js/bootstrap.bundle.min.js"></script>
        <script src="../lib/easing/easing.min.js"></script>
        <script src="../lib/waypoints/waypoints.min.js"></script>
        <script src="../lib/owlcarousel/owl.carousel.min.js"></script>
        <script src="../lib/lightbox/js/lightbox.min.js"></script>

        <!-- Template Javascript -->
        <script src="../js/main.js"></script>
    </body>
</html> 