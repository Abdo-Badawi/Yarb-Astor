<!DOCTYPE html>
<html lang="en">

<head>
    <title>FAQ Management - HomeStay Admin</title>
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
    <meta name="description" content="FAQ Management for HomeStay Admin Dashboard" />
    <meta name="keywords" content="admin, dashboard, faq, management">
    <meta name="author" content="HomeStay" />
    <!-- Favicon icon -->
    <link rel="icon" href="assets/images/favicon.ico" type="image/x-icon">

    <!-- vendor css -->
    <link rel="stylesheet" href="assets/css/style.css">
    <!-- custom css -->
    <link rel="stylesheet" href="assets/css/custom.css">
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
        }
        
        .faq-card .btn-link:hover {
            color: #007bff;
            text-decoration: none;
        }
        
        .faq-card .badge {
            font-size: 0.75rem;
            padding: 0.35em 0.65em;
            margin-right: 0.5rem;
            font-weight: 600;
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
                            <h5 class="m-b-10">FAQ Management</h5>
                        </div>
                        <ul class="breadcrumb">
                            <li class="breadcrumb-item"><a href="index.html"><i class="feather icon-home"></i></a></li>
                            <li class="breadcrumb-item"><a href="#!">FAQ Management</a></li>
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
                        <h5><i class="feather icon-help-circle mr-2"></i>FAQ Management</h5>
                        <span class="d-block m-t-5">Create and manage frequently asked questions for the public website</span>
                    </div>
                    <div class="card-body">
                        <form id="faqForm">
                            <input type="hidden" id="faqId">
                            <div class="row">
                                <div class="col-md-6">
                                    <div class="form-group">
                                        <label for="faqQuestion">Question</label>
                                        <input type="text" class="form-control" id="faqQuestion" placeholder="Enter the question">
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="form-group">
                                        <label for="faqCategory">Category</label>
                                        <select class="form-control" id="faqCategory">
                                            <option value="">Select Category</option>
                                            <option value="host">Host</option>
                                            <option value="traveler">Traveler</option>
                                            <option value="general">General</option>
                                            <option value="payment">Payment</option>
                                            <option value="safety">Safety</option>
                                        </select>
                                    </div>
                                </div>
                            </div>
                            <div class="form-group">
                                <label for="faqAnswer">Answer</label>
                                <textarea class="form-control" id="faqAnswer" rows="4" placeholder="Enter the answer"></textarea>
                            </div>
                            <div class="row">
                                <div class="col-md-4">
                                    <div class="form-group">
                                        <label for="faqStatus">Status</label>
                                        <select class="form-control" id="faqStatus">
                                            <option value="published">Published</option>
                                            <option value="draft">Draft</option>
                                            <option value="archived">Archived</option>
                                        </select>
                                    </div>
                                </div>
                                <div class="col-md-4">
                                    <div class="form-group">
                                        <label for="faqDisplayOrder">Display Order</label>
                                        <input type="number" class="form-control" id="faqDisplayOrder" placeholder="Enter display order">
                                    </div>
                                </div>
                                <div class="col-md-4">
                                    <div class="form-group">
                                        <label for="faqTags">Tags</label>
                                        <input type="text" class="form-control" id="faqTags" placeholder="Enter tags (comma separated)">
                                    </div>
                                </div>
                            </div>
                            <div class="form-group">
                                <div class="custom-control custom-checkbox">
                                    <input type="checkbox" class="custom-control-input" id="faqFeatured">
                                    <label class="custom-control-label" for="faqFeatured">Feature this FAQ on the homepage</label>
                                </div>
                            </div>
                            <div class="text-right">
                                <button type="button" class="btn btn-secondary" onclick="resetFaqForm()">Reset</button>
                                <button type="button" class="btn btn-primary" onclick="saveFaq()">Save FAQ</button>
                            </div>
                        </form>
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
                                        <option value="host">Host</option>
                                        <option value="traveler">Traveler</option>
                                        <option value="general">General</option>
                                        <option value="payment">Payment</option>
                                        <option value="safety">Safety</option>
                                    </select>
                                </div>
                            </div>
                            <div class="col-md-4">
                                <div class="form-group">
                                    <label for="filterFaqStatus">Filter by Status</label>
                                    <select class="form-control" id="filterFaqStatus">
                                        <option value="">All Status</option>
                                        <option value="published">Published</option>
                                        <option value="draft">Draft</option>
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
                            <!-- FAQ Item 1 -->
                            <div class="card faq-card">
                                <div class="card-header" id="heading1">
                                    <div class="d-flex justify-content-between align-items-center">
                                        <h2 class="mb-0">
                                            <button class="btn btn-link btn-block text-left" type="button" data-toggle="collapse" data-target="#collapse1" aria-expanded="true" aria-controls="collapse1">
                                                <i class="feather icon-help-circle mr-2"></i> How do I become a host?
                                            </button>
                                        </h2>
                                        <div>
                                            <span class="badge badge-success mr-2">Published</span>
                                            <span class="badge badge-primary mr-2">Host</span>
                                        </div>
                                    </div>
                                </div>
                                <div id="collapse1" class="collapse show" aria-labelledby="heading1" data-parent="#faqAccordion">
                                    <div class="card-body">
                                        <p>To become a host on HomeStay, you need to:</p>
                                        <ol>
                                            <li>Create an account on our platform</li>
                                            <li>Complete your profile with accurate information</li>
                                            <li>Submit your property details and photos</li>
                                            <li>Pass our verification process</li>
                                            <li>Set your availability and pricing</li>
                                        </ol>
                                        <p>Once approved, you can start accepting travelers and sharing your culture!</p>
                                        <div class="mt-3">
                                            <strong>Tags:</strong> <span class="badge badge-light">hosting</span> <span class="badge badge-light">verification</span> <span class="badge badge-light">profile</span>
                                        </div>
                                        <div class="mt-3 text-right">
                                            <button class="btn btn-sm btn-primary" onclick="editFaq(1)">Edit</button>
                                            <button class="btn btn-sm btn-danger" onclick="deleteFaq(1)">Delete</button>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            
                            <!-- FAQ Item 2 -->
                            <div class="card faq-card">
                                <div class="card-header" id="heading2">
                                    <div class="d-flex justify-content-between align-items-center">
                                        <h2 class="mb-0">
                                            <button class="btn btn-link btn-block text-left collapsed" type="button" data-toggle="collapse" data-target="#collapse2" aria-expanded="false" aria-controls="collapse2">
                                                <i class="feather icon-help-circle mr-2"></i> What should I pack for my homestay?
                                            </button>
                                        </h2>
                                        <div>
                                            <span class="badge badge-success mr-2">Published</span>
                                            <span class="badge badge-warning mr-2">Traveler</span>
                                        </div>
                                    </div>
                                </div>
                                <div id="collapse2" class="collapse" aria-labelledby="heading2" data-parent="#faqAccordion">
                                    <div class="card-body">
                                        <p>When packing for your homestay experience, consider the following:</p>
                                        <ul>
                                            <li>Weather-appropriate clothing for your destination</li>
                                            <li>Personal toiletries and medications</li>
                                            <li>Travel documents (passport, visa, etc.)</li>
                                            <li>Small gifts from your home country for your host family</li>
                                            <li>Adapter plugs for electronic devices</li>
                                            <li>Books or other entertainment for downtime</li>
                                        </ul>
                                        <p>Remember to check with your host about specific items you might need for your stay.</p>
                                        <div class="mt-3">
                                            <strong>Tags:</strong> <span class="badge badge-light">packing</span> <span class="badge badge-light">travel</span> <span class="badge badge-light">preparation</span>
                                        </div>
                                        <div class="mt-3 text-right">
                                            <button class="btn btn-sm btn-primary" onclick="editFaq(2)">Edit</button>
                                            <button class="btn btn-sm btn-danger" onclick="deleteFaq(2)">Delete</button>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            
                            <!-- FAQ Item 3 -->
                            <div class="card faq-card">
                                <div class="card-header" id="heading3">
                                    <div class="d-flex justify-content-between align-items-center">
                                        <h2 class="mb-0">
                                            <button class="btn btn-link btn-block text-left collapsed" type="button" data-toggle="collapse" data-target="#collapse3" aria-expanded="false" aria-controls="collapse3">
                                                <i class="feather icon-help-circle mr-2"></i> How are payments processed?
                                            </button>
                                        </h2>
                                        <div>
                                            <span class="badge badge-warning mr-2">Draft</span>
                                            <span class="badge badge-primary mr-2">Payment</span>
                                        </div>
                                    </div>
                                </div>
                                <div id="collapse3" class="collapse" aria-labelledby="heading3" data-parent="#faqAccordion">
                                    <div class="card-body">
                                        <p>Payments on HomeStay are processed securely through our platform:</p>
                                        <ol>
                                            <li>Travelers make payments through our secure payment system</li>
                                            <li>Funds are held in escrow until the stay is completed</li>
                                            <li>Hosts receive payment 24 hours after check-out</li>
                                            <li>We support various payment methods including credit cards and PayPal</li>
                                        </ol>
                                        <p>Our secure payment system ensures both hosts and travelers are protected.</p>
                                        <div class="mt-3">
                                            <strong>Tags:</strong> <span class="badge badge-light">payment</span> <span class="badge badge-light">security</span> <span class="badge badge-light">escrow</span>
                                        </div>
                                        <div class="mt-3 text-right">
                                            <button class="btn btn-sm btn-primary" onclick="editFaq(3)">Edit</button>
                                            <button class="btn btn-sm btn-danger" onclick="deleteFaq(3)">Delete</button>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            
                            <!-- FAQ Item 4 -->
                            <div class="card faq-card">
                                <div class="card-header" id="heading4">
                                    <div class="d-flex justify-content-between align-items-center">
                                        <h2 class="mb-0">
                                            <button class="btn btn-link btn-block text-left collapsed" type="button" data-toggle="collapse" data-target="#collapse4" aria-expanded="false" aria-controls="collapse4">
                                                <i class="feather icon-help-circle mr-2"></i> What safety measures are in place?
                                            </button>
                                        </h2>
                                        <div>
                                            <span class="badge badge-success mr-2">Published</span>
                                            <span class="badge badge-primary mr-2">Safety</span>
                                            <span class="badge badge-info">Featured</span>
                                        </div>
                                    </div>
                                </div>
                                <div id="collapse4" class="collapse" aria-labelledby="heading4" data-parent="#faqAccordion">
                                    <div class="card-body">
                                        <p>HomeStay takes safety seriously and implements several measures:</p>
                                        <ul>
                                            <li>Identity verification for all users</li>
                                            <li>Secure messaging system within the platform</li>
                                            <li>24/7 customer support for emergencies</li>
                                            <li>Host and traveler reviews and ratings</li>
                                            <li>Secure payment system with escrow protection</li>
                                            <li>Safety tips and guidelines for both hosts and travelers</li>
                                        </ul>
                                        <p>We recommend always communicating through our platform and following our safety guidelines.</p>
                                        <div class="mt-3">
                                            <strong>Tags:</strong> <span class="badge badge-light">safety</span> <span class="badge badge-light">verification</span> <span class="badge badge-light">security</span>
                                        </div>
                                        <div class="mt-3 text-right">
                                            <button class="btn btn-sm btn-primary" onclick="editFaq(4)">Edit</button>
                                            <button class="btn btn-sm btn-danger" onclick="deleteFaq(4)">Delete</button>
                                        </div>
                                    </div>
                                </div>
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
            const displayOrder = document.getElementById('faqDisplayOrder').value;
            const tags = document.getElementById('faqTags').value;
            const featured = document.getElementById('faqFeatured').checked;
            
            if (!question || !category || !answer) {
                alert('Please fill in all required fields');
                return;
            }
            
            // Here you would typically make an API call to save the FAQ
            // For now, we'll just show a success message
            alert('FAQ saved successfully!');
            resetFaqForm();
        }
        
        // Function to reset FAQ form
        function resetFaqForm() {
            document.getElementById('faqForm').reset();
            document.getElementById('faqId').value = '';
        }
        
        // Function to edit FAQ
        function editFaq(id) {
            // Here you would typically fetch the FAQ data from an API
            // For now, we'll just populate the form with sample data
            document.getElementById('faqId').value = id;
            document.getElementById('faqQuestion').value = 'Sample Question ' + id;
            document.getElementById('faqCategory').value = 'general';
            document.getElementById('faqAnswer').value = 'Sample Answer ' + id;
            document.getElementById('faqStatus').value = 'published';
            document.getElementById('faqDisplayOrder').value = id;
            document.getElementById('faqTags').value = 'tag1, tag2, tag3';
            document.getElementById('faqFeatured').checked = id % 2 === 0;
            
            // Scroll to the form
            document.getElementById('faqForm').scrollIntoView({ behavior: 'smooth' });
        }
        
        // Function to delete FAQ
        function deleteFaq(id) {
            if (confirm('Are you sure you want to delete this FAQ?')) {
                // Here you would typically make an API call to delete the FAQ
                // For now, we'll just remove the card from the DOM
                const card = document.querySelector(`#heading${id}`).closest('.faq-card');
                if (card) {
                    card.remove();
                }
                alert('FAQ deleted successfully!');
            }
        }
        
        // Function to search FAQs
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
                
                // Show or hide the card
                card.style.display = showCard ? 'block' : 'none';
            });
        }
        
        // Add event listeners to filter dropdowns
        document.getElementById('filterFaqCategory').addEventListener('change', searchFaqs);
        document.getElementById('filterFaqStatus').addEventListener('change', searchFaqs);
        document.getElementById('filterFaqFeatured').addEventListener('change', searchFaqs);
        
        // Add event listener to search input
        document.getElementById('searchFaq').addEventListener('keyup', function(event) {
            if (event.key === 'Enter') {
                searchFaqs();
            }
        });
    </script>
</body>

</html> 