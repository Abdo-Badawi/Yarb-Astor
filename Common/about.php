<!DOCTYPE html>
<html lang="en">

    <head>
        <meta charset="utf-8">
        <title>HomeStays - About Us</title>
        <meta content="width=device-width, initial-scale=1.0" name="viewport">
        <meta content="homestays, cultural exchange, about us, local experiences" name="keywords">
        <meta content="Learn about HomeStays - connecting travelers with local hosts for authentic cultural experiences" name="description">

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
        <link href="../css/main.css" rel="stylesheet">
    </head>

    <body>

        <!-- Spinner Start -->
        <div id="spinner" class="show bg-white position-fixed translate-middle w-100 vh-100 top-50 start-50 d-flex align-items-center justify-content-center">
            <div class="spinner-border text-primary" style="width: 3rem; height: 3rem;" role="status">
                <span class="sr-only">Loading...</span>
            </div>
        </div>

        <?php include 'navCommon.php'; ?>

        <div class="container-fluid bg-breadcrumb">
            <div class="container text-center py-5" style="max-width: 900px;">
                <h3 class="text-white display-3 mb-4">About Us</h1>   
            </div>
        </div>
        <!-- Header End -->

        <!-- About Start -->
        <div class="container-fluid about py-5">
            <div class="container py-5">
                <div class="row g-5 align-items-center">
                    <div class="col-lg-5">
                        <div class="h-100" style="border: 50px solid; border-color: transparent #13357B transparent #13357B;">
                            <img src="../img/about-img.jpg" class="img-fluid w-100 h-100" alt="">
                        </div>
                    </div>
                    <div class="col-lg-7" style="background: linear-gradient(rgba(255, 255, 255, .8), rgba(255, 255, 255, .8)), url(img/about-img-1.png);">
                        <h5 class="section-about-title pe-3">About Us</h5>
                        <h1 class="mb-4">Welcome to <span class="text-primary">HomeStays</span></h1>
                        <p class="mb-4">At HomeStays, we believe in the power of cultural exchange and authentic local experiences. Our platform connects travelers with local hosts, creating meaningful connections and fostering understanding between different cultures.</p>
                        <p class="mb-4">Founded with a vision to make travel more meaningful and accessible, HomeStays has grown into a global community of hosts and travelers who share our passion for cultural exchange.</p>
                        <div class="row gy-2 gx-4 mb-4">
                            <div class="col-sm-6">
                                <p class="mb-0"><i class="fa fa-arrow-right text-primary me-2"></i>Authentic Cultural Experiences</p>
                            </div>
                            <div class="col-sm-6">
                                <p class="mb-0"><i class="fa fa-arrow-right text-primary me-2"></i>Verified Hosts</p>
                            </div>
                            <div class="col-sm-6">
                                <p class="mb-0"><i class="fa fa-arrow-right text-primary me-2"></i>Global Community</p>
                            </div>
                            <div class="col-sm-6">
                                <p class="mb-0"><i class="fa fa-arrow-right text-primary me-2"></i>24/7 Support</p>
                            </div>
                        </div>
                        <a class="btn btn-primary rounded-pill py-3 px-5 mt-2" href="">Read More</a>
                    </div>
                </div>
            </div>
        </div>
        <!-- About End -->

   <!-- Travel Guide Start -->
<div class="container-fluid guide py-5">
    <div class="container py-5">
        <div class="mx-auto text-center mb-5" style="max-width: 900px;">
            <h5 class="section-title px-3">Travel Guide</h5>
            <h1 class="mb-0">Meet Our Guide</h1>
        </div>
        <div class="row g-4 justify-content-center">
            <!-- Guide 1 - This will set the height for all others -->
            <div class="col-6 col-sm-4 col-md-3 col-lg-20p col-xl-20p">
                <div class="guide-item h-100 d-flex flex-column">
                    <div class="guide-img flex-grow-1" style="min-height: 300px;">
                        <div class="guide-img-efects h-100">
                            <img src="../img/badawi.jpg" class="img-fluid w-100 h-100 object-fit-cover rounded-top" alt="Abdelrahman">
                        </div>
                        <div class="guide-icon rounded-pill p-2">
                            <a class="btn btn-square btn-primary rounded-circle mx-1" href=""><i class="fab fa-facebook-f"></i></a>
                            <a class="btn btn-square btn-primary rounded-circle mx-1" href=""><i class="fab fa-twitter"></i></a>
                            <a class="btn btn-square btn-primary rounded-circle mx-1" href=""><i class="fab fa-instagram"></i></a>
                            <a class="btn btn-square btn-primary rounded-circle mx-1" href=""><i class="fab fa-linkedin-in"></i></a>
                        </div>
                    </div>
                    <div class="guide-title text-center rounded-bottom p-4">
                        <h4 class="mt-3">Abdelrahman</h4>
                        <p class="mb-0">Software Engineer</p>
                    </div>
                </div>
            </div>
            
            <!-- Guide 2 -->
            <div class="col-6 col-sm-4 col-md-3 col-lg-20p col-xl-20p">
                <div class="guide-item h-100 d-flex flex-column">
                    <div class="guide-img flex-grow-1" style="min-height: 300px;">
                        <div class="guide-img-efects h-100">
                            <img src="../img/malakragab.jpg" class="img-fluid w-100 h-100 object-fit-cover rounded-top" alt="Mahmoud">
                        </div>
                        <div class="guide-icon rounded-pill p-2">
                            <a class="btn btn-square btn-primary rounded-circle mx-1" href=""><i class="fab fa-facebook-f"></i></a>
                            <a class="btn btn-square btn-primary rounded-circle mx-1" href=""><i class="fab fa-twitter"></i></a>
                            <a class="btn btn-square btn-primary rounded-circle mx-1" href=""><i class="fab fa-instagram"></i></a>
                            <a class="btn btn-square btn-primary rounded-circle mx-1" href=""><i class="fab fa-linkedin-in"></i></a>
                        </div>
                    </div>
                    <div class="guide-title text-center rounded-bottom p-4">
                        <h4 class="mt-3">Malak Ragab</h4>
                        <p class="mb-0">Software Engineer</p>
                    </div>
                </div>
            </div>
            
            <!-- Guide 3 -->
            <div class="col-6 col-sm-4 col-md-3 col-lg-20p col-xl-20p">
                <div class="guide-item h-100 d-flex flex-column">
                    <div class="guide-img flex-grow-1" style="min-height: 300px;">
                        <div class="guide-img-efects h-100">
                                <img src="../img/mahmoud.jpg" class="img-fluid w-100 h-100 object-fit-cover rounded-top" alt="Adel">
                        </div>
                        <div class="guide-icon rounded-pill p-2">
                            <a class="btn btn-square btn-primary rounded-circle mx-1" href=""><i class="fab fa-facebook-f"></i></a>
                            <a class="btn btn-square btn-primary rounded-circle mx-1" href=""><i class="fab fa-twitter"></i></a>
                            <a class="btn btn-square btn-primary rounded-circle mx-1" href=""><i class="fab fa-instagram"></i></a>
                            <a class="btn btn-square btn-primary rounded-circle mx-1" href=""><i class="fab fa-linkedin-in"></i></a>
                        </div>
                    </div>
                    <div class="guide-title text-center rounded-bottom p-4">
                        <h4 class="mt-3">Mahmoud</h4>
                        <p class="mb-0">Software Engineer</p>
                    </div>
                </div>
            </div>
            
            <!-- Guide 4 -->
            <div class="col-6 col-sm-4 col-md-3 col-lg-20p col-xl-20p">
                <div class="guide-item h-100 d-flex flex-column">
                    <div class="guide-img flex-grow-1" style="min-height: 300px;">
                        <div class="guide-img-efects h-100">
                            <img src="../img/malakhany.jpg" class="img-fluid w-100 h-100 object-fit-cover rounded-top" alt="Faisal">
                        </div>
                        <div class="guide-icon rounded-pill p-2">
                            <a class="btn btn-square btn-primary rounded-circle mx-1" href=""><i class="fab fa-facebook-f"></i></a>
                            <a class="btn btn-square btn-primary rounded-circle mx-1" href=""><i class="fab fa-twitter"></i></a>
                            <a class="btn btn-square btn-primary rounded-circle mx-1" href=""><i class="fab fa-instagram"></i></a>
                            <a class="btn btn-square btn-primary rounded-circle mx-1" href=""><i class="fab fa-linkedin-in"></i></a>
                        </div>
                    </div>
                    <div class="guide-title text-center rounded-bottom p-4">
                        <h4 class="mt-3">Malak hany</h4>
                        <p class="mb-0">Software Engineer</p>
                    </div>
                </div>
            </div>
            
            <!-- Guide 5 (if needed) -->
            <div class="col-6 col-sm-4 col-md-3 col-lg-20p col-xl-20p">
                <div class="guide-item h-100 d-flex flex-column">
                    <div class="guide-img flex-grow-1" style="min-height: 300px;">
                        <div class="guide-img-efects h-100">
                            <img src="../img/faisal.jpg" class="img-fluid w-100 h-100 object-fit-cover rounded-top" alt="Guide 5">
                        </div>
                        <div class="guide-icon rounded-pill p-2">
                            <a class="btn btn-square btn-primary rounded-circle mx-1" href=""><i class="fab fa-facebook-f"></i></a>
                            <a class="btn btn-square btn-primary rounded-circle mx-1" href=""><i class="fab fa-twitter"></i></a>
                            <a class="btn btn-square btn-primary rounded-circle mx-1" href=""><i class="fab fa-instagram"></i></a>
                            <a class="btn btn-square btn-primary rounded-circle mx-1" href=""><i class="fab fa-linkedin-in"></i></a>
                        </div>
                    </div>
                    <div class="guide-title text-center rounded-bottom p-4">
                        <h4 class="mt-3">Faisal</h4>
                        <p class="mb-0">Software Engineer</p>
                    </div>
                </div>
            </div> 
        </div>
    </div>
</div>
<!-- Travel Guide End -->

<style>
    /* Custom column width for 5 items in a row (20% width each) */
    .col-lg-20p {
        width: 20%;
        flex: 0 0 20%;
        max-width: 20%;
    }
    .col-xl-20p {
        width: 20%;
        flex: 0 0 20%;
        max-width: 20%;
    }
    
    /* Make all cards equal height */
    .guide-item {
        display: flex;
        flex-direction: column;
        height: 100%;
    }
    
    .guide-img {
        flex-grow: 1;
    }
    
    /* Responsive adjustments */
    @media (max-width: 1199.98px) {
        .col-lg-20p {
            width: 25%;
            flex: 0 0 25%;
            max-width: 25%;
        }
    }
    @media (max-width: 991.98px) {
        .col-md-3 {
            width: 33.333%;
            flex: 0 0 33.333%;
            max-width: 33.333%;
        }
    }
    @media (max-width: 767.98px) {
        .col-sm-4 {
            width: 50%;
            flex: 0 0 50%;
            max-width: 50%;
        }
    }
</style>

        <!-- Subscribe Start -->
        <div class="container-fluid subscribe py-5">
            <div class="container text-center py-5">
                <div class="mx-auto text-center" style="max-width: 900px;">
                    <h5 class="subscribe-title px-3">Subscribe</h5>
                    <h1 class="text-white mb-4">Our Newsletter</h1>
                    <p class="text-white mb-5">Lorem ipsum dolor sit amet consectetur adipisicing elit. Laborum tempore nam, architecto doloremque velit explicabo? Voluptate sunt eveniet fuga eligendi! Expedita laudantium fugiat corrupti eum cum repellat a laborum quasi.
                    </p>
                    <div class="position-relative mx-auto">
                        <input class="form-control border-primary rounded-pill w-100 py-3 ps-4 pe-5" type="text" placeholder="Your email">
                        <button type="button" class="btn btn-primary rounded-pill position-absolute top-0 end-0 py-2 px-4 mt-2 me-2">Subscribe</button>
                    </div>
                </div>
            </div>
        </div>
        <!-- Subscribe End -->

        <?php include 'footer.php'; ?>
        
        <!-- JavaScript Libraries -->
        <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.6.4/jquery.min.js"></script>
        <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.0.0/dist/js/bootstrap.bundle.min.js"></script>
        <script src="lib/easing/easing.min.js"></script>
        <script src="lib/waypoints/waypoints.min.js"></script>
        <script src="lib/owlcarousel/owl.carousel.min.js"></script>
        <script src="lib/lightbox/js/lightbox.min.js"></script>
        

        <!-- Template Javascript -->
        <script src="../js/main.js"></script>
    </body>

</html>