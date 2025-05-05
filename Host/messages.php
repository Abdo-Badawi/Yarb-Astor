<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="utf-8">
    <title>HomeStays - Messages</title>
    <meta content="width=device-width, initial-scale=1.0" name="viewport">
    <meta content="homestays, cultural exchange, messaging, host communication" name="keywords">
    <meta content="Communicate with volunteers and manage your cultural exchange messages" name="description">

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

    <!-- Messages Start -->
    <div class="container-fluid py-5">
        <div class="container py-5">
            <!-- Page Header -->
            <div class="text-center mb-5">
                <h1 class="mb-3">Messages</h1>
                <p class="mb-0">Communicate with volunteers and manage your cultural exchange conversations</p>
            </div>

            <div class="row">
                <!-- Conversations List -->
                <div class="col-lg-4">
                    <div class="card border-0 shadow-sm mb-4">
                        <div class="card-header bg-white">
                            <div class="input-group">
                                <input type="text" class="form-control" placeholder="Search conversations...">
                                <button class="btn btn-primary"><i class="fa fa-search"></i></button>
                            </div>
                        </div>
                        <div class="card-body p-0">
                            <!-- Conversation Item 1 -->
                            <div class="p-3 border-bottom conversation-item active">
                                <div class="d-flex align-items-center">
                                    <img src="../img/volunteer-1.jpg" class="rounded-circle me-3" style="width: 50px; height: 50px;" alt="Volunteer">
                                    <div class="flex-grow-1">
                                        <h6 class="mb-1">Sarah Johnson</h6>
                                        <p class="mb-0 text-muted small">English Teacher Position</p>
                                    </div>
                                    <div class="text-end">
                                        <small class="text-muted">2m ago</small>
                                        <span class="badge bg-primary rounded-pill ms-2">2</span>
                                    </div>
                                </div>
                            </div>

                            <!-- Conversation Item 2 -->
                            <div class="p-3 border-bottom conversation-item">
                                <div class="d-flex align-items-center">
                                    <img src="../img/volunteer-2.jpg" class="rounded-circle me-3" style="width: 50px; height: 50px;" alt="Volunteer">
                                    <div class="flex-grow-1">
                                        <h6 class="mb-1">Michael Chen</h6>
                                        <p class="mb-0 text-muted small">Garden Assistant Position</p>
                                    </div>
                                    <div class="text-end">
                                        <small class="text-muted">1h ago</small>
                                    </div>
                                </div>
                            </div>

                            <!-- Conversation Item 3 -->
                            <div class="p-3 border-bottom conversation-item">
                                <div class="d-flex align-items-center">
                                    <img src="../img/volunteer-3.jpg" class="rounded-circle me-3" style="width: 50px; height: 50px;" alt="Volunteer">
                                    <div class="flex-grow-1">
                                        <h6 class="mb-1">Emma Wilson</h6>
                                        <p class="mb-0 text-muted small">Childcare Helper Position</p>
                                    </div>
                                    <div class="text-end">
                                        <small class="text-muted">2h ago</small>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Chat Area -->
                <div class="col-lg-8">
                    <div class="card border-0 shadow-sm">
                        <!-- Chat Header -->
                        <div class="card-header bg-white">
                            <div class="d-flex align-items-center">
                                <img src="../img/volunteer-1.jpg" class="rounded-circle me-3" style="width: 50px; height: 50px;" alt="Volunteer">
                                <div>
                                    <h5 class="mb-1">Sarah Johnson</h5>
                                    <p class="mb-0 text-muted">English Teacher Position</p>
                                </div>
                                <div class="ms-auto">
                                    <button class="btn btn-sm btn-outline-primary me-2">View Profile</button>
                                    <button class="btn btn-sm btn-outline-danger">Block</button>
                                </div>
                            </div>
                        </div>

                        <!-- Chat Messages -->
                        <div class="card-body" style="height: 400px; overflow-y: auto;">
                            <!-- Message from Volunteer -->
                            <div class="d-flex mb-4">
                                <img src="../img/volunteer-1.jpg" class="rounded-circle me-3" style="width: 40px; height: 40px;" alt="Volunteer">
                                <div class="flex-grow-1">
                                    <div class="bg-light rounded p-3">
                                        <p class="mb-0">Hello! I'm very interested in the English teaching position. I have 3 years of experience teaching English to children and adults.</p>
                                    </div>
                                    <small class="text-muted">10:30 AM</small>
                                </div>
                            </div>

                            <!-- Message from Host -->
                            <div class="d-flex mb-4 justify-content-end">
                                <div class="flex-grow-1 text-end">
                                    <div class="bg-primary text-white rounded p-3">
                                        <p class="mb-0">Hi Sarah! Thanks for your interest. Could you tell me more about your teaching experience and what cultural activities you'd like to share?</p>
                                    </div>
                                    <small class="text-muted">10:35 AM</small>
                                </div>
                                <img src="../img/host.jpg" class="rounded-circle ms-3" style="width: 40px; height: 40px;" alt="Host">
                            </div>

                            <!-- Message from Volunteer -->
                            <div class="d-flex mb-4">
                                <img src="../img/volunteer-1.jpg" class="rounded-circle me-3" style="width: 40px; height: 40px;" alt="Volunteer">
                                <div class="flex-grow-1">
                                    <div class="bg-light rounded p-3">
                                        <p class="mb-0">I've taught English in both classroom and one-on-one settings. I'm passionate about sharing American culture through cooking, music, and outdoor activities.</p>
                                    </div>
                                    <small class="text-muted">10:40 AM</small>
                                </div>
                            </div>
                        </div>

                        <!-- Message Input -->
                        <div class="card-footer bg-white">
                            <div class="input-group">
                                <input type="text" class="form-control" placeholder="Type your message...">
                                <button class="btn btn-primary">
                                    <i class="fa fa-paper-plane"></i>
                                </button>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <!-- Messages End -->

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