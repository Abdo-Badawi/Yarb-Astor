<?php
session_start();
// Check if user is logged in and is an admin
if (!isset($_SESSION['userID']) || $_SESSION['userType'] !== 'admin') {
    header("Location: ../Common/login.php");
    exit;
}

// Add a session token for additional security
if (!isset($_SESSION['auth_token'])) {
    $_SESSION['auth_token'] = bin2hex(random_bytes(32));
}

// Fix the require statements - use absolute paths with __DIR__
require_once __DIR__ . '/../Models/SupportContent.php';
require_once __DIR__ . '/../Controllers/SupportContent.php';

// Initialize the controller
$controller = new SupportController();

// Handle AJAX requests
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_GET['action'])) {
    header('Content-Type: application/json');

    if ($_GET['action'] === 'save') {
        $data = json_decode(file_get_contents('php://input'), true);
        echo json_encode($controller->saveFAQ($data));
        exit;
    } elseif ($_GET['action'] === 'delete' && isset($_GET['id'])) {
        echo json_encode($controller->deleteFAQ($_GET['id']));
        exit;
    } elseif ($_GET['action'] === 'getById' && isset($_GET['id'])) {
        echo json_encode($controller->getFAQById($_GET['id']));
        exit;
    }
}

// Get all FAQs for display
$faqsResult = $controller->getAllFAQs();
$faqs = $faqsResult['success'] ? $faqsResult['data'] : [];
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <title>FAQ List - HomeStay Admin</title>
    <style>
        /* Custom styles for FAQ Management */
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

        .faq-card .btn-link .faq-question-text {
            margin-right: 10px;
        }

        .faq-card .btn-link .badge {
            display: inline-block;
            font-size: 0.7rem;
            font-weight: 500;
            padding: 0.25em 0.6em;
            margin-left: 5px;
            border-radius: 0.25rem;
            color: #fff;
        }

        .faq-card .badge-primary {
            background-color: #007bff !important;
            color: #fff !important;
        }

        .faq-card .badge-secondary {
            background-color: #6c757d !important;
            color: #fff !important;
        }

        .faq-card .badge-success {
            background-color: #28a745 !important;
            color: #fff !important;
        }

        .faq-card .badge-danger {
            background-color: #dc3545 !important;
            color: #fff !important;
        }

        .faq-card .badge-warning {
            background-color: #ffc107 !important;
            color: #212529 !important;
        }

        .faq-card .badge-info {
            background-color: #17a2b8 !important;
            color: #fff !important;
        }

        .faq-card .badge {
            font-size: 0.75rem;
            padding: 0.35em 0.65em;
            margin-right: 0.5rem;
            font-weight: 600;
            display: inline-block;
        }

        .faq-card .badge-light {
            background-color: #f1f3f5;
            color: #495057;
        }

        .faq-card .badge-success {
            background-color: #28a745;
            color: #fff;
            font-weight: 600;
        }

        .faq-card .badge-primary {
            background-color: #007bff;
            color: #fff;
            font-weight: 600;
        }

        .faq-card .badge-warning {
            background-color: #ffc107;
            color: #212529;
            font-weight: 600;
        }

        .faq-card .badge-info {
            background-color: #17a2b8;
            color: #fff;
            font-weight: 600;
        }

        .faq-card .mt-3 {
            margin-top: 1.5rem !important;
        }

        .faq-card .text-right {
            margin-top: 1.5rem;
        }

        .faq-card .btn-sm {
            padding: 0.25rem 0.75rem;
            font-size: 0.875rem;
            border-radius: 0.25rem;
            margin-left: 0.5rem;
        }

        .filter-row {
            margin-bottom: 2rem;
            padding: 1.25rem;
            background-color: #f8f9fa;
            border-radius: 0.5rem;
        }

        .form-group {
            margin-bottom: 1.5rem;
        }

        .form-control {
            padding: 0.75rem 1rem;
            border-radius: 0.375rem;
            border: 1px solid #ced4da;
            line-height: 1.5;
        }

        .form-control:focus {
            border-color: #80bdff;
            box-shadow: 0 0 0 0.2rem rgba(0, 123, 255, 0.25);
        }

        .btn {
            padding: 0.5rem 1.25rem;
            border-radius: 0.375rem;
            font-weight: 500;
            line-height: 1.5;
        }

        .btn-primary {
            background-color: #007bff;
            border-color: #007bff;
        }

        .btn-primary:hover {
            background-color: #0069d9;
            border-color: #0062cc;
        }

        .btn-secondary {
            background-color: #6c757d;
            border-color: #6c757d;
        }

        .btn-secondary:hover {
            background-color: #5a6268;
            border-color: #545b62;
        }

        .btn-danger {
            background-color: #dc3545;
            border-color: #dc3545;
        }

        .btn-danger:hover {
            background-color: #c82333;
            border-color: #bd2130;
        }

        .custom-control {
            padding-left: 2rem;
            margin-bottom: 1rem;
        }

        .custom-control-input {
            width: 1.25rem;
            height: 1.25rem;
            margin-top: 0.25rem;
            margin-left: -2rem;
        }

        .custom-control-label {
            padding-top: 0.25rem;
            line-height: 1.5;
        }

        .card-header h5 {
            margin-bottom: 0.5rem;
            font-weight: 600;
            line-height: 1.5;
        }

        .card-header span {
            color: #6c757d;
            font-size: 0.875rem;
            line-height: 1.5;
        }

        .nav-tabs {
            border-bottom: 1px solid #dee2e6;
            margin-bottom: 1.5rem;
        }

        .nav-tabs .nav-item {
            margin-bottom: -1px;
        }

        .nav-tabs .nav-link {
            padding: 0.75rem 1.25rem;
            border: 1px solid transparent;
            border-top-left-radius: 0.375rem;
            border-top-right-radius: 0.375rem;
            color: #495057;
            font-weight: 500;
            line-height: 1.5;
        }

        .nav-tabs .nav-link:hover {
            border-color: #e9ecef #e9ecef #dee2e6;
        }

        .nav-tabs .nav-link.active {
            color: #007bff;
            background-color: #fff;
            border-color: #dee2e6 #dee2e6 #fff;
        }

        .accordion .card {
            margin-bottom: 1rem;
            border-radius: 0.5rem;
            overflow: hidden;
        }

        .accordion .card-header {
            padding: 1.25rem;
            background-color: #f8f9fa;
            border-bottom: 1px solid rgba(0, 0, 0, 0.05);
        }

        .accordion .card-body {
            padding: 1.5rem;
        }

        .accordion .btn-link {
            color: #333;
            font-weight: 500;
            text-decoration: none;
            padding: 0;
            line-height: 1.5;
        }

        .accordion .btn-link:hover {
            color: #007bff;
            text-decoration: none;
        }

        .accordion .badge {
            font-size: 0.75rem;
            padding: 0.35em 0.65em;
            margin-right: 0.5rem;
        }

        .accordion .mt-3 {
            margin-top: 1.5rem !important;
        }

        .accordion .text-right {
            margin-top: 1.5rem;
        }

        .accordion .btn-sm {
            padding: 0.25rem 0.75rem;
            font-size: 0.875rem;
            border-radius: 0.25rem;
            margin-left: 0.5rem;
        }

        /* Responsive styles */
        @media (max-width: 768px) {
            .faq-card .card-header,
            .accordion .card-header {
                padding: 1rem;
            }

            .faq-card .card-body,
            .accordion .card-body {
                padding: 1.25rem;
            }

            .filter-row {
                padding: 1rem;
            }

            .form-group {
                margin-bottom: 1.25rem;
            }

            .btn {
                padding: 0.5rem 1rem;
            }
        }

        @media (max-width: 576px) {
            .faq-card .card-header,
            .accordion .card-header {
                padding: 0.75rem;
            }

            .faq-card .card-body,
            .accordion .card-body {
                padding: 1rem;
            }

            .filter-row {
                padding: 0.75rem;
            }

            .form-group {
                margin-bottom: 1rem;
            }

            .btn {
                padding: 0.375rem 0.75rem;
                font-size: 0.875rem;
            }

            .faq-card .btn-sm,
            .accordion .btn-sm {
                padding: 0.2rem 0.5rem;
                font-size: 0.75rem;
            }
        }
    </style>
</head>
<body class="">

<?php 
    include 'navCommon.php'; 
    include_once 'header-common.php'
?>

<!-- [ Main Content ] start -->
<div class="pcoded-main-container">
    <div class="pcoded-content">
        <!-- [ breadcrumb ] start -->
        <div class="page-header">
            <div class="page-block">
                <div class="row align-items-center">
                    <div class="col-md-12">
                        <div class="page-header-title">
                            <h5 class="m-b-10">FAQ List</h5>
                        </div>
                        <ul class="breadcrumb">
                            <li class="breadcrumb-item"><a href="index.html"><i class="feather icon-home"></i></a></li>
                            <li class="breadcrumb-item"><a href="#!">FAQ List</a></li>
                        </ul>
                    </div>
                </div>
            </div>
        </div>
        <!-- [ breadcrumb ] end -->
        <!-- [ Main Content ] start -->
        <div class="row">
            <!-- FAQ Management Form -->
            <div class="col-xl-12 col-md-12">
                <div class="card">
                    <div class="card-header">
                        <h5><i class="feather icon-help-circle mr-2"></i>FAQ List</h5>
                        <span class="d-block m-t-5">Manage existing FAQs for the public website</span>
                    </div>
                    <div class="card-body">
                        <form id="faqForm">
                            <input type="hidden" id="faqId">
                            <div class="row">
                                <div class="col-md-6">
                                    <div class="form-group">
                                        <label for="faqQuestion">Question</label>
                                        <input type="text" class="form-control" id="faqQuestion" placeholder="Enter the question" required>
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="form-group">
                                        <label for="faqCategory">Category</label>
                                        <select class="form-control" id="faqCategory" required>
                                            <option value="">Select Category</option>
                                            <option value="account">Account</option>
                                            <option value="safety">Safety</option>
                                            <option value="opportunity">Opportunity</option>
                                            <option value="other">Other</option>
                                        </select>
                                    </div>
                                </div>
                            </div>

                            <div class="form-group">
                                <label for="faqAnswer">Answer</label>
                                <textarea class="form-control" id="faqAnswer" rows="4" placeholder="Enter the answer" required></textarea>
                            </div>

                            <div class="row">
                                <div class="col-md-4">
                                    <div class="form-group">
                                        <label for="faqStatus">Status</label>
                                        <select class="form-control" id="faqStatus">
                                            <option value="active">Active</option>
                                            <option value="archived">Archived</option>
                                        </select>
                                    </div>
                                </div>
                                <div class="col-md-8">
                                    <!-- Display Order and Tags fields have been removed -->
                                </div>
                            </div>

                            <div class="form-group">
                                <div class="custom-control custom-checkbox">
                                    <input type="checkbox" class="custom-control-input" id="faqFeatured">
                                    <label class="custom-control-label" for="faqFeatured">Feature this FAQ on the homepage</label>
                                </div>
                            </div>

                            <div class="text-right">
                                <button type="reset" class="btn btn-secondary">Reset</button>
                                <button type="button" class="btn btn-primary" onclick="saveFaq()">Save FAQ</button>
                            </div>
                        </form>

                        <div id="faqMessage" class="mt-3"></div>
                    </div>
                </div>
            </div>
            <!-- FAQ List -->
            <div class="col-xl-12 col-md-12">
                <div class="card">
                    <div class="card-header">
                        <h5><i class="feather icon-list mr-2"></i>FAQ List</h5>
                        <span class="d-block m-t-5">Manage existing FAQs for the public website</span>
                    </div>
                    <div class="card-body">
                        <!-- Search and Filters -->
                        <div class="row mb-4 filter-row">
                            <div class="col-md-12 mb-3">
                                <div class="input-group">
                                    <input type="text" class="form-control" id="searchFaq" placeholder="Search FAQs by question, answer, or tags...">
                                    <div class="input-group-append">
                                        <button class="btn btn-primary" type="button" onclick="searchFaqs()">
                                            <i class="feather icon-search"></i> Search
                                        </button>
                                    </div>
                                </div>
                            </div>
                            <div class="col-md-4">
                                <div class="form-group">
                                    <label for="filterFaqCategory">Filter by Category</label>
                                    <select class="form-control" id="filterFaqCategory">
                                        <option value="">All Categories</option>
                                        <option value="account">Account</option>
                                        <option value="safety">Safety</option>
                                        <option value="opportunity">Opportunity</option>
                                        <option value="other">Other</option>
                                    </select>
                                </div>
                            </div>
                            <div class="col-md-4">
                                <div class="form-group">
                                    <label for="filterFaqStatus">Filter by Status</label>
                                    <select class="form-control" id="filterFaqStatus">
                                        <option value="">All Status</option>
                                        <option value="active">Active</option>
                                        <option value="archived">Archived</option>
                                    </select>
                                </div>
                            </div>
                            <div class="col-md-4">
                                <div class="form-group">
                                    <label for="filterFaqFeatured">Filter by Featured</label>
                                    <select class="form-control" id="filterFaqFeatured">
                                        <option value="">All FAQs</option>
                                        <option value="featured">Featured Only</option>
                                        <option value="not-featured">Not Featured</option>
                                    </select>
                                </div>
                            </div>
                        </div>

                        <!-- FAQ Accordion -->
                        <div class="accordion" id="faqAccordion">
                            <?php if (empty($faqs)): ?>
                                <div class="alert alert-info">
                                    <p>No FAQs found. Create your first FAQ using the form above.</p>
                                </div>
                            <?php else: ?>
                                <?php
                                // Debug: Print the first FAQ data to see what's available
                                if (!empty($faqs) && count($faqs) > 0) {
                                    echo '<!-- Debug: ';
                                    print_r($faqs[0]);
                                    echo ' -->';
                                }
                                ?>
                                <?php foreach ($faqs as $index => $faq): ?>
                                    <!-- FAQ Item -->
                                    <div class="card faq-card">
                                        <div class="card-header" id="heading<?php echo $faq['content_id']; ?>">
                                            <div class="d-flex justify-content-between align-items-center">
                                                <h2 class="mb-0">
                                                    <button class="btn btn-link btn-block text-left <?php echo $index !== 0 ? 'collapsed' : ''; ?>" type="button" data-toggle="collapse" data-target="#collapse<?php echo $faq['content_id']; ?>" aria-expanded="<?php echo $index === 0 ? 'true' : 'false'; ?>" aria-controls="collapse<?php echo $faq['content_id']; ?>">
                                                        <i class="feather icon-help-circle mr-2"></i>
                                                        <span class="faq-question-text"><?php echo htmlspecialchars($faq['title']); ?></span>&nbsp;&nbsp;
                                                        <span class="badge badge-<?php
                                                            $categoryClass = 'secondary';
                                                            if (isset($faq['category'])) {
                                                                switch($faq['category']) {
                                                                    case 'account': $categoryClass = 'primary'; break;
                                                                    case 'safety': $categoryClass = 'danger'; break;
                                                                    case 'opportunity': $categoryClass = 'warning'; break;
                                                                    case 'other': $categoryClass = 'info'; break;
                                                                    default: $categoryClass = 'secondary';
                                                                }
                                                            }
                                                            echo $categoryClass;
                                                        ?>"><?php echo isset($faq['category']) ? ucfirst($faq['category']) : 'Other'; ?></span>
                                                        <span class="badge badge-<?php
                                                            $statusClass = 'secondary';
                                                            if (isset($faq['status'])) {
                                                                $statusClass = $faq['status'] === 'active' ? 'success' : 'secondary';
                                                            }
                                                            echo $statusClass;
                                                        ?>"><?php echo isset($faq['status']) ? ucfirst($faq['status']) : 'Archived'; ?></span>
                                                        <?php if (isset($faq['featured']) && $faq['featured'] == 1): ?>
                                                            <span class="badge badge-info">Featured</span>
                                                        <?php endif; ?>
                                                    </button>
                                                </h2>
                                                <div>
                                                    <!-- Empty div to maintain layout -->
                                                </div>
                                            </div>
                                        </div>
                                        <div id="collapse<?php echo $faq['content_id']; ?>" class="collapse <?php echo $index === 0 ? 'show' : ''; ?>" aria-labelledby="heading<?php echo $faq['content_id']; ?>" data-parent="#faqAccordion">
                                            <div class="card-body">
                                                <?php echo $faq['content']; ?>

                                                <!-- Tags section has been removed -->

                                                <div class="mt-3 text-right">
                                                    <button class="btn btn-sm btn-primary" onclick="editFaq(<?php echo $faq['content_id']; ?>)">Edit</button>
                                                    <button class="btn btn-sm btn-danger" onclick="deleteFaq(<?php echo $faq['content_id']; ?>)">Delete</button>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                <?php endforeach; ?>
                            <?php endif; ?>
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
    <script src="assets/js/pcoded.min.js"></script>
    <!-- Custom Js -->
    <script src="assets/js/custom.js"></script>
    <script>

        // Function to save FAQ
        function saveFaq() {
            const question = document.getElementById('faqQuestion').value;
            const category = document.getElementById('faqCategory').value;
            const answer = document.getElementById('faqAnswer').value;
            const status = document.getElementById('faqStatus').value;
            const featured = document.getElementById('faqFeatured').checked;

            if (!question || !category || !answer) {
                alert('Please fill in all required fields');
                return;
            }

            // Get the FAQ ID if it exists (for updates)
            const faqId = document.getElementById('faqId').value;

            const formData = {
                faqQuestion: question,
                faqCategory: category,
                faqAnswer: answer,
                faqStatus: status,
                faqFeatured: featured,
                faqUserType: 'admin' // Replace with actual user type from session if needed
            };

            // Add the ID to the form data if it exists (for updates)
            if (faqId) {
                formData.faqId = faqId;
            }

            fetch('faq-management.php?action=save', {
                method: 'POST',
                headers: { 'Content-Type': 'application/json' },
                body: JSON.stringify(formData)
            })
            .then(res => res.json())
            .then(data => {
                if (data.success) {
                    const messageDiv = document.getElementById('faqMessage');
                    const successMessage = data.message || 'FAQ saved successfully!';
                    messageDiv.innerHTML = `<div class="alert alert-success">${successMessage}</div>`;
                    resetFaqForm();
                    // Reload the page to show the updated FAQ list
                    setTimeout(() => {
                        window.location.reload();
                    }, 1500);
                } else {
                    const messageDiv = document.getElementById('faqMessage');
                    messageDiv.innerHTML = `<div class="alert alert-danger">Error: ${data.error}</div>`;
                }
            })
            .catch(err => {
                console.error('Save error:', err);
                const messageDiv = document.getElementById('faqMessage');
                messageDiv.innerHTML = '<div class="alert alert-danger">An error occurred while saving the FAQ.</div>';
            });
        }

        // Function to reset FAQ form
        function resetFaqForm() {
            document.getElementById('faqForm').reset();
            document.getElementById('faqId').value = '';
        }

        // Function to edit FAQ
        function editFaq(id) {
            fetch(`faq-management.php?action=getById&id=${id}`, {
                method: 'POST',
                headers: { 'Content-Type': 'application/json' }
            })
            .then(res => res.json())
            .then(response => {
                if (response.success) {
                    const faq = response.data;
                    document.getElementById('faqId').value = faq.content_id;
                    document.getElementById('faqQuestion').value = faq.title;
                    document.getElementById('faqCategory').value = faq.category;
                    document.getElementById('faqAnswer').value = faq.content;
                    document.getElementById('faqStatus').value = faq.status;

                    // Handle optional fields that might not exist in the database
                    if (document.getElementById('faqFeatured') && faq.featured !== undefined) {
                        document.getElementById('faqFeatured').checked = faq.featured == 1;
                    }

                    document.getElementById('faqForm').scrollIntoView({ behavior: 'smooth' });
                } else {
                    alert('Error: ' + response.error);
                }
            })
            .catch(err => {
                console.error('Edit error:', err);
                alert('An error occurred while loading the FAQ data.');
            });
        }

        // Function to delete FAQ
        function deleteFaq(id) {
            if (confirm('Are you sure you want to delete this FAQ?')) {
                fetch(`faq-management.php?action=delete&id=${id}`, {
                    method: 'POST',
                    headers: { 'Content-Type': 'application/json' }
                })
                .then(res => res.json())
                .then(response => {
                    if (response.success) {
                        const card = document.querySelector(`#heading${id}`).closest('.faq-card');
                        if (card) {
                            card.remove();
                        }
                        const messageDiv = document.getElementById('faqMessage');
                        messageDiv.innerHTML = '<div class="alert alert-success">FAQ deleted successfully!</div>';

                        setTimeout(() => {
                            messageDiv.innerHTML = '';
                        }, 3000);
                    } else {
                        alert('Error: ' + response.error);
                    }
                })
                .catch(err => {
                    console.error('Delete error:', err);
                    alert('An error occurred while deleting the FAQ.');
                });
            }
        }

        // Function to search/filter FAQs
        function searchFaqs() {
            const searchTerm = document.getElementById('searchFaq').value.toLowerCase();
            const category = document.getElementById('filterFaqCategory').value;
            const status = document.getElementById('filterFaqStatus').value;
            const featured = document.getElementById('filterFaqFeatured').value;

            const cards = document.querySelectorAll('.faq-card');

            cards.forEach(card => {
                const question = card.querySelector('.btn-link').textContent.toLowerCase();
                const categoryBadge = card.querySelector('.badge-primary');
                const statusBadge = card.querySelector('.badge-success, .badge-warning');
                const featuredBadge = card.querySelector('.badge-info');

                let showCard = true;

                // Search term filter
                if (searchTerm && !question.includes(searchTerm)) {
                    showCard = false;
                }

                // Category filter
                if (category && categoryBadge && !categoryBadge.textContent.toLowerCase().includes(category)) {
                    showCard = false;
                }

                // Status filter
                if (status && statusBadge && !statusBadge.textContent.toLowerCase().includes(status)) {
                    showCard = false;
                }

                // Featured filter
                if (featured === 'featured' && !featuredBadge) {
                    showCard = false;
                } else if (featured === 'not-featured' && featuredBadge) {
                    showCard = false;
                }

                card.style.display = showCard ? 'block' : 'none';
            });
        }

        // Event listeners for filters
        document.getElementById('filterFaqCategory').addEventListener('change', searchFaqs);
        document.getElementById('filterFaqStatus').addEventListener('change', searchFaqs);
        document.getElementById('filterFaqFeatured').addEventListener('change', searchFaqs);

        // Event listener for search input
        document.getElementById('searchFaq').addEventListener('keyup', function(event) {
            if (event.key === 'Enter') {
                searchFaqs();
            }
        });
    </script>

    </script>
</body>

</html>



