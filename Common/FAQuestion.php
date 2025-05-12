<?php
session_start();
require_once '../Controllers/SupportController.php';

// Initialize the controller
$controller = new SupportController();

// Get all FAQs for display
$faqsResult = $controller->getAllFAQs();
$faqs = $faqsResult['success'] ? $faqsResult['data'] : [];

// Filter to show only active FAQs for public view
$activeFaqs = array_filter($faqs, function($faq) {
    return $faq['status'] === 'active';
});
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <title>FAQ - HomeStays</title>
    <meta content="width=device-width, initial-scale=1.0" name="viewport">
    <meta content="HomeStays, Cultural Exchange, Local Experience, Homestays, FAQ" name="keywords">
    <meta content="Frequently Asked Questions about HomeStays services and experiences" name="description">

    <!-- Favicon -->
    <link href="../img/favicon.ico" rel="icon">

    <!-- Google Web Fonts -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Roboto:wght@400;500;700&display=swap" rel="stylesheet">

    <!-- Icon Font Stylesheet -->
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.10.0/css/all.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.4.1/font/bootstrap-icons.css" rel="stylesheet">

    <!-- Libraries Stylesheet -->
    <link href="../lib/animate/animate.min.css" rel="stylesheet">
    <link href="../lib/owlcarousel/assets/owl.carousel.min.css" rel="stylesheet">

    <!-- Customized Bootstrap Stylesheet -->
    <link href="../css/bootstrap.min.css" rel="stylesheet">

    <!-- Template Stylesheet -->
    <link href="../css/style.css" rel="stylesheet">
    
    <style>
        .faq-section {
            padding: 80px 0;
        }
        
        .faq-card {
            margin-bottom: 1.5rem;
            border-radius: 0.5rem;
            box-shadow: 0 2px 6px rgba(0, 0, 0, 0.08);
            transition: all 0.3s ease;
        }

        .faq-card:hover {
            box-shadow: 0 4px 12px rgba(0, 0, 0, 0.12);
            transform: translateY(-2px);
        }

        .faq-card .card-header {
            padding: 1.25rem;
            background-color: #f8f9fa;
            border-bottom: 1px solid rgba(0, 0, 0, 0.05);
        }

        .faq-card .card-body {
            padding: 1.5rem;
        }

        .faq-card .btn-link {
            color: #333;
            font-weight: 500;
            text-decoration: none;
            padding: 0;
            line-height: 1.5;
            display: flex;
            align-items: center;
            flex-wrap: wrap;
            text-align: left;
            justify-content: flex-start;
        }

        .faq-card .btn-link:hover {
            color: #007bff;
            text-decoration: none;
        }
        
        .category-filter {
            margin-bottom: 30px;
        }
        
        .category-filter .btn {
            margin-right: 10px;
            margin-bottom: 10px;
        }
        
        .category-filter .btn.active {
            background-color: #007bff;
            color: white;
        }
    </style>
</head>

<body>
    <!-- Navbar Start -->
    <?php include 'navCommon.php'; ?>
    <!-- Navbar End -->

    <!-- Page Header Start -->
    <div class="container-fluid page-header py-5 mb-5 wow fadeIn" data-wow-delay="0.1s">
        <div class="container py-5">
            <h1 class="display-4 animated slideInDown mb-4">Frequently Asked Questions</h1>
            <nav aria-label="breadcrumb animated slideInDown">
                <ol class="breadcrumb mb-0">
                    <li class="breadcrumb-item"><a href="index.php">Home</a></li>
                    <li class="breadcrumb-item active" aria-current="page">FAQ</li>
                </ol>
            </nav>
        </div>
    </div>
    <!-- Page Header End -->

    <!-- FAQ Section Start -->
    <div class="container-xxl faq-section">
        <div class="container">
            <div class="text-center mx-auto wow fadeInUp" data-wow-delay="0.1s" style="max-width: 600px;">
                <h1 class="mb-4">Frequently Asked Questions</h1>
                <p class="mb-5">Find answers to common questions about HomeStays services, opportunities, and experiences.</p>
            </div>
            
            <!-- Category Filter Buttons -->
            <div class="category-filter text-center wow fadeInUp" data-wow-delay="0.2s">
                <button class="btn btn-outline-primary active" data-filter="all">All Categories</button>
                <button class="btn btn-outline-primary" data-filter="account">Account</button>
                <button class="btn btn-outline-primary" data-filter="safety">Safety</button>
                <button class="btn btn-outline-primary" data-filter="opportunity">Opportunity</button>
                <button class="btn btn-outline-primary" data-filter="other">Other</button>
            </div>
            
            <!-- Search Box -->
            <div class="row mb-5 wow fadeInUp" data-wow-delay="0.3s">
                <div class="col-md-8 mx-auto">
                    <div class="input-group">
                        <input type="text" class="form-control" id="searchFaq" placeholder="Search FAQs...">
                        <button class="btn btn-primary" type="button" id="searchButton">
                            <i class="fas fa-search"></i> Search
                        </button>
                    </div>
                </div>
            </div>
            
            <!-- FAQ Accordion -->
            <div class="accordion wow fadeInUp" data-wow-delay="0.4s" id="faqAccordion">
                <?php if (empty($activeFaqs)): ?>
                    <div class="alert alert-info">
                        <p>No FAQs available at the moment. Please check back later.</p>
                    </div>
                <?php else: ?>
                    <?php foreach ($activeFaqs as $index => $faq): ?>
                        <!-- FAQ Item -->
                        <div class="card faq-card" data-category="<?php echo htmlspecialchars($faq['category']); ?>">
                            <div class="card-header" id="heading<?php echo $faq['content_id']; ?>">
                                <h2 class="mb-0">
                                    <button class="btn btn-link btn-block text-left <?php echo $index !== 0 ? 'collapsed' : ''; ?>" type="button" data-toggle="collapse" data-target="#collapse<?php echo $faq['content_id']; ?>" aria-expanded="<?php echo $index === 0 ? 'true' : 'false'; ?>" aria-controls="collapse<?php echo $faq['content_id']; ?>">
                                        <i class="fas fa-question-circle mr-2"></i>
                                        <span class="faq-question-text"><?php echo htmlspecialchars($faq['title']); ?></span>&nbsp;&nbsp;
                                        <span class="badge badge-<?php
                                            $categoryClass = 'secondary';
                                            switch($faq['category']) {
                                                case 'account': $categoryClass = 'primary'; break;
                                                case 'safety': $categoryClass = 'danger'; break;
                                                case 'opportunity': $categoryClass = 'warning'; break;
                                                case 'other': $categoryClass = 'info'; break;
                                                default: $categoryClass = 'secondary';
                                            }
                                            echo $categoryClass;
                                        ?>"><?php echo ucfirst($faq['category']); ?></span>
                                        <?php if (isset($faq['featured']) && $faq['featured'] == 1): ?>
                                            <span class="badge badge-info">Featured</span>
                                        <?php endif; ?>
                                    </button>
                                </h2>
                            </div>
                            <div id="collapse<?php echo $faq['content_id']; ?>" class="collapse <?php echo $index === 0 ? 'show' : ''; ?>" aria-labelledby="heading<?php echo $faq['content_id']; ?>" data-parent="#faqAccordion">
                                <div class="card-body">
                                    <?php echo $faq['content']; ?>
                                </div>
                            </div>
                        </div>
                    <?php endforeach; ?>
                <?php endif; ?>
            </div>
        </div>
    </div>
    <!-- FAQ Section End -->

    <!-- Footer Start -->
    <?php include 'footer.php'; ?>
    <!-- Footer End -->

    <!-- Back to Top -->
    <a href="#" class="btn btn-lg btn-primary btn-lg-square rounded-circle back-to-top"><i class="bi bi-arrow-up"></i></a>

    <!-- JavaScript Libraries -->
    <script src="https://code.jquery.com/jquery-3.4.1.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.0.0/dist/js/bootstrap.bundle.min.js"></script>
    <script src="../lib/wow/wow.min.js"></script>
    <script src="../lib/easing/easing.min.js"></script>
    <script src="../lib/waypoints/waypoints.min.js"></script>
    <script src="../lib/owlcarousel/owl.carousel.min.js"></script>

    <!-- Template Javascript -->
    <script src="../js/main.js"></script>
    
    <script>
        $(document).ready(function() {
            // Initialize WOW.js
            new WOW().init();
            
            // Category filter functionality
            $('.category-filter .btn').click(function() {
                $('.category-filter .btn').removeClass('active');
                $(this).addClass('active');
                
                const filter = $(this).data('filter');
                
                if (filter === 'all') {
                    $('.faq-card').show();
                } else {
                    $('.faq-card').hide();
                    $(`.faq-card[data-category="${filter}"]`).show();
                }
            });
            
            // Search functionality
            $('#searchButton, #searchFaq').on('click keyup', function(e) {
                if (e.type === 'click' || e.keyCode === 13) {
                    const searchTerm = $('#searchFaq').val().toLowerCase();
                    
                    if (searchTerm.length > 0) {
                        $('.faq-card').hide();
                        $('.faq-card').each(function() {
                            const questionText = $(this).find('.faq-question-text').text().toLowerCase();
                            const answerText = $(this).find('.card-body').text().toLowerCase();
                            
                            if (questionText.includes(searchTerm) || answerText.includes(searchTerm)) {
                                $(this).show();
                            }
                        });
                    } else {
                        // If search is empty, respect the current category filter
                        const activeFilter = $('.category-filter .btn.active').data('filter');
                        if (activeFilter === 'all') {
                            $('.faq-card').show();
                        } else {
                            $('.faq-card').hide();
                            $(`.faq-card[data-category="${activeFilter}"]`).show();
                        }
                    }
                }
            });
        });
    </script>
</body>
</html>