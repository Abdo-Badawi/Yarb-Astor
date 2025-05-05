<!DOCTYPE html>
<html lang="en">

<head>
    <title>Support Content - HomeStay Admin</title>
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
    <meta name="description" content="Support Content Management for HomeStay Admin Dashboard" />
    <meta name="keywords" content="admin, dashboard, support, content, management">
    <meta name="author" content="HomeStay" />
    <!-- Favicon icon -->
    <link rel="icon" href="assets/images/favicon.ico" type="image/x-icon">

    <!-- vendor css -->
    <link rel="stylesheet" href="assets/css/style.css">
    <!-- custom css -->
    <link rel="stylesheet" href="assets/css/custom.css">
    <style>
        /* Support Card Styles */
        .support-card {
            background: #fff;
            border-radius: 8px;
            box-shadow: 0 2px 10px rgba(0, 0, 0, 0.1);
            padding: 20px;
            margin-bottom: 25px;
            transition: all 0.3s ease;
            height: 100%;
            display: flex;
            flex-direction: column;
        }
        
        .support-card:hover {
            box-shadow: 0 5px 15px rgba(0, 0, 0, 0.15);
            transform: translateY(-3px);
        }
        
        .support-header {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 15px;
            padding-bottom: 10px;
            border-bottom: 1px solid #eee;
        }
        
        .support-title {
            font-size: 18px;
            font-weight: 600;
            margin: 0;
            line-height: 1.4;
        }
        
        .support-category {
            color: #666;
            margin-bottom: 15px;
            line-height: 1.5;
        }
        
        .support-content {
            margin-bottom: 20px;
            line-height: 1.6;
            color: #555;
            flex-grow: 1;
            overflow: hidden;
            display: -webkit-box;
            -webkit-line-clamp: 8;
            -webkit-box-orient: vertical;
        }
        
        .support-actions {
            display: flex;
            gap: 10px;
            margin-top: auto;
        }
        
        .support-actions .btn {
            padding: 8px 20px;
            border-radius: 5px;
            font-weight: 500;
            transition: all 0.3s ease;
            min-width: 100px;
        }
        
        .support-actions .btn:hover {
            transform: translateY(-2px);
            box-shadow: 0 4px 8px rgba(0, 0, 0, 0.1);
        }
        
        /* Add spacing between cards */
        .col-xl-6 {
            padding: 15px;
        }
        
        /* Add more space between rows */
        .row#supportContentList {
            margin: -15px;
        }
        
        /* Filter Styles */
        .filter-row {
            margin-bottom: 25px;
            padding: 15px;
            background-color: #f8f9fa;
            border-radius: 5px;
        }
        
        /* Responsive Styles */
        @media (max-width: 768px) {
            .support-card {
                padding: 15px;
            }
            
            .support-content {
                -webkit-line-clamp: 6;
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
                            <h5 class="m-b-10">Support Content</h5>
                        </div>
                        <ul class="breadcrumb">
                            <li class="breadcrumb-item"><a href="index.html"><i class="feather icon-home"></i></a></li>
                            <li class="breadcrumb-item"><a href="#!">Support</a></li>
                            <li class="breadcrumb-item"><a href="#!">Support Content</a></li>
                        </ul>
                    </div>
                </div>
            </div>
        </div>
        <!-- [ breadcrumb ] end -->
        <!-- [ Main Content ] start -->
        <div class="row">
            <!-- Support Content List -->
            <div class="col-xl-12 col-md-12">
                <div class="card">
                    <div class="card-header">
                        <h5><i class="feather icon-message-square mr-2"></i>Support Content Management</h5>
                    </div>
                    <div class="card-body">
                        <!-- Search and Filters -->
                        <div class="row mb-4 filter-row">
                            <div class="col-md-12 mb-3">
                                <div class="input-group">
                                    <input type="text" class="form-control" id="searchSupport" placeholder="Search support content by title, category, or content...">
                                    <div class="input-group-append">
                                        <button class="btn btn-primary" type="button" onclick="searchSupportContent()">
                                            <i class="feather icon-search"></i> Search
                                        </button>
                                    </div>
                                </div>
                            </div>
                            <div class="col-md-4">
                                <div class="form-group">
                                    <label for="filterCategory">Filter by Category</label>
                                    <select class="form-control" id="filterCategory">
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
                                    <label for="filterStatus">Filter by Status</label>
                                    <select class="form-control" id="filterStatus">
                                        <option value="">All Status</option>
                                        <option value="published">Published</option>
                                        <option value="draft">Draft</option>
                                        <option value="archived">Archived</option>
                                    </select>
                                </div>
                            </div>
                            <div class="col-md-4">
                                <div class="form-group">
                                    <label for="filterDate">Filter by Date</label>
                                    <select class="form-control" id="filterDate">
                                        <option value="">All Dates</option>
                                        <option value="today">Today</option>
                                        <option value="this-week">This Week</option>
                                        <option value="this-month">This Month</option>
                                        <option value="last-3-months">Last 3 Months</option>
                                    </select>
                                </div>
                            </div>
                        </div>
                        
                        <!-- Tabs for Replied and Not Replied -->
                        <ul class="nav nav-tabs mb-4" id="supportTabs" role="tablist">
                            <li class="nav-item">
                                <a class="nav-link active" id="replied-tab" data-toggle="tab" href="#replied" role="tab" aria-controls="replied" aria-selected="true">
                                    <i class="feather icon-check-circle mr-1"></i> Replied <span class="badge badge-success">4</span>
                                </a>
                            </li>
                            <li class="nav-item">
                                <a class="nav-link" id="not-replied-tab" data-toggle="tab" href="#not-replied" role="tab" aria-controls="not-replied" aria-selected="false">
                                    <i class="feather icon-alert-circle mr-1"></i> Not Replied <span class="badge badge-danger">3</span>
                                </a>
                            </li>
                        </ul>
                        
                        <!-- Tab Content -->
                        <div class="tab-content" id="supportTabsContent">
                            <!-- Replied Tab -->
                            <div class="tab-pane fade show active" id="replied" role="tabpanel" aria-labelledby="replied-tab">
                                <div class="row" id="repliedContentList">
                                    <!-- Support Content Item 1 -->
                                    <div class="col-xl-6 col-md-6">
                                        <div class="support-card">
                                            <div class="support-header">
                                                <h6 class="support-title">How do I become a host?</h6>
                                                <span class="badge badge-success">Published</span>
                                            </div>
                                            <div class="support-category">
                                                <i class="feather icon-tag"></i> Host
                                            </div>
                                            <div class="support-content">
                                                <p>To become a host on HomeStay, you need to:</p>
                                                <ol>
                                                    <li>Create an account on our platform</li>
                                                    <li>Complete your profile with accurate information</li>
                                                    <li>Submit your property details and photos</li>
                                                    <li>Pass our verification process</li>
                                                    <li>Set your availability and pricing</li>
                                                </ol>
                                                <p>Once approved, you can start accepting travelers and sharing your culture!</p>
                                            </div>
                                            <div class="support-actions">
                                                <button class="btn btn-primary" onclick="editSupportContent(1)">Edit Response</button>
                                                <button class="btn btn-danger" onclick="deleteSupportContent(1)">Delete</button>
                                            </div>
                                        </div>
                                    </div>
                                    
                                    <!-- Support Content Item 2 -->
                                    <div class="col-xl-6 col-md-6">
                                        <div class="support-card">
                                            <div class="support-header">
                                                <h6 class="support-title">What should I pack for my homestay?</h6>
                                                <span class="badge badge-success">Published</span>
                                            </div>
                                            <div class="support-category">
                                                <i class="feather icon-tag"></i> Traveler
                                            </div>
                                            <div class="support-content">
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
                                            </div>
                                            <div class="support-actions">
                                                <button class="btn btn-primary" onclick="editSupportContent(2)">Edit Response</button>
                                                <button class="btn btn-danger" onclick="deleteSupportContent(2)">Delete</button>
                                            </div>
                                        </div>
                                    </div>
                                    
                                    <!-- Support Content Item 3 -->
                                    <div class="col-xl-6 col-md-6">
                                        <div class="support-card">
                                            <div class="support-header">
                                                <h6 class="support-title">How are payments processed?</h6>
                                                <span class="badge badge-warning">Draft</span>
                                            </div>
                                            <div class="support-category">
                                                <i class="feather icon-tag"></i> Payment
                                            </div>
                                            <div class="support-content">
                                                <p>Payments on HomeStay are processed securely through our platform:</p>
                                                <ol>
                                                    <li>Travelers make payments through our secure payment system</li>
                                                    <li>Funds are held in escrow until the stay is completed</li>
                                                    <li>Hosts receive payment 24 hours after check-out</li>
                                                    <li>We support various payment methods including credit cards and PayPal</li>
                                                </ol>
                                                <p>Our secure payment system ensures both hosts and travelers are protected.</p>
                                            </div>
                                            <div class="support-actions">
                                                <button class="btn btn-primary" onclick="editSupportContent(3)">Edit Response</button>
                                                <button class="btn btn-danger" onclick="deleteSupportContent(3)">Delete</button>
                                            </div>
                                        </div>
                                    </div>
                                    
                                    <!-- Support Content Item 4 -->
                                    <div class="col-xl-6 col-md-6">
                                        <div class="support-card">
                                            <div class="support-header">
                                                <h6 class="support-title">What safety measures are in place?</h6>
                                                <span class="badge badge-success">Published</span>
                                            </div>
                                            <div class="support-category">
                                                <i class="feather icon-tag"></i> Safety
                                            </div>
                                            <div class="support-content">
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
                                            </div>
                                            <div class="support-actions">
                                                <button class="btn btn-primary" onclick="editSupportContent(4)">Edit Response</button>
                                                <button class="btn btn-danger" onclick="deleteSupportContent(4)">Delete</button>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            
                            <!-- Not Replied Tab -->
                            <div class="tab-pane fade" id="not-replied" role="tabpanel" aria-labelledby="not-replied-tab">
                                <div class="row" id="notRepliedContentList">
                                    <!-- Not Replied Item 1 -->
                                    <div class="col-xl-6 col-md-6">
                                        <div class="support-card not-replied">
                                            <div class="support-header">
                                                <h6 class="support-title">How do I cancel a booking?</h6>
                                                <span class="badge badge-danger">Not Replied</span>
                                            </div>
                                            <div class="support-category">
                                                <i class="feather icon-tag"></i> General
                                            </div>
                                            <div class="support-content">
                                                <p>Question from user:</p>
                                                <p>I need to cancel my upcoming homestay booking due to an emergency. What is the cancellation policy and how do I go about canceling my reservation?</p>
                                            </div>
                                            <div class="support-actions">
                                                <button class="btn btn-primary" onclick="replyToQuestion(5)">Submit Response</button>
                                                <button class="btn btn-secondary" onclick="markAsReplied(5)">Mark as Replied</button>
                                            </div>
                                        </div>
                                    </div>
                                    
                                    <!-- Not Replied Item 2 -->
                                    <div class="col-xl-6 col-md-6">
                                        <div class="support-card not-replied">
                                            <div class="support-header">
                                                <h6 class="support-title">What happens if there's a dispute with my host?</h6>
                                                <span class="badge badge-danger">Not Replied</span>
                                            </div>
                                            <div class="support-category">
                                                <i class="feather icon-tag"></i> Safety
                                            </div>
                                            <div class="support-content">
                                                <p>Question from user:</p>
                                                <p>I'm concerned about what would happen if there's a disagreement or dispute with my host during my stay. How does HomeStay handle these situations and what support is available to travelers?</p>
                                            </div>
                                            <div class="support-actions">
                                                <button class="btn btn-primary" onclick="replyToQuestion(6)">Submit Response</button>
                                                <button class="btn btn-secondary" onclick="markAsReplied(6)">Mark as Replied</button>
                                            </div>
                                        </div>
                                    </div>
                                    
                                    <!-- Not Replied Item 3 -->
                                    <div class="col-xl-6 col-md-6">
                                        <div class="support-card not-replied">
                                            <div class="support-header">
                                                <h6 class="support-title">How do I report inappropriate behavior?</h6>
                                                <span class="badge badge-danger">Not Replied</span>
                                            </div>
                                            <div class="support-category">
                                                <i class="feather icon-tag"></i> Safety
                                            </div>
                                            <div class="support-content">
                                                <p>Question from user:</p>
                                                <p>What is the process for reporting inappropriate behavior from a host or traveler? How quickly will HomeStay respond to these reports and what actions are taken to ensure the safety of all users?</p>
                                            </div>
                                            <div class="support-actions">
                                                <button class="btn btn-primary" onclick="replyToQuestion(7)">Submit Response</button>
                                                <button class="btn btn-secondary" onclick="markAsReplied(7)">Mark as Replied</button>
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
</div>
    <!-- Required Js -->
    <script src="assets/js/vendor-all.min.js"></script>
    <script src="assets/js/plugins/bootstrap.min.js"></script>
    <script src="assets/js/pcoded.min.js"></script>
    <!-- Custom Js -->
    <script src="assets/js/custom.js"></script>
    <script>
        // Function to search support content
        function searchSupportContent() {
            const searchTerm = document.getElementById('searchSupport').value.toLowerCase();
            const category = document.getElementById('filterCategory').value;
            const status = document.getElementById('filterStatus').value;
            const date = document.getElementById('filterDate').value;
            
            const cards = document.querySelectorAll('.support-card');
            
            cards.forEach(card => {
                const title = card.querySelector('.support-title').textContent.toLowerCase();
                const cardCategory = card.querySelector('.support-category').textContent.toLowerCase();
                const cardStatus = card.querySelector('.badge').textContent.toLowerCase();
                
                let showCard = true;
                
                // Search term filter
                if (searchTerm && !title.includes(searchTerm) && !cardCategory.includes(searchTerm)) {
                    showCard = false;
                }
                
                // Category filter
                if (category && !cardCategory.includes(category)) {
                    showCard = false;
                }
                
                // Status filter
                if (status && !cardStatus.includes(status)) {
                    showCard = false;
                }
                
                // Show or hide the card
                card.closest('.col-xl-6').style.display = showCard ? 'block' : 'none';
            });
        }
        
        // Function to edit support content
        function editSupportContent(id) {
            // Redirect to the support content edit page
            window.location.href = `support-content-edit.html?id=${id}`;
        }
        
        // Function to delete support content
        function deleteSupportContent(id) {
            if (confirm('Are you sure you want to delete this support content?')) {
                // Here you would typically make an API call to delete the support content
                // For now, we'll just remove the card from the DOM
                const card = document.querySelector(`.support-card[data-id="${id}"]`);
                if (card) {
                    card.closest('.col-xl-6').remove();
                }
                alert('Support content deleted successfully!');
            }
        }
        
        // Function to reply to a question
        function replyToQuestion(id) {
            // Redirect to the reply page
            window.location.href = `support-content-reply.html?id=${id}`;
        }
        
        // Function to mark a question as replied
        function markAsReplied(id) {
            if (confirm('Are you sure you want to mark this question as replied?')) {
                // Here you would typically make an API call to update the status
                // For now, we'll just move the card to the replied section
                const card = document.querySelector(`.support-card[data-id="${id}"]`);
                if (card) {
                    // Clone the card
                    const clonedCard = card.cloneNode(true);
                    
                    // Update the badge and buttons
                    const badge = clonedCard.querySelector('.badge');
                    badge.className = 'badge badge-success';
                    badge.textContent = 'Published';
                    
                    const actions = clonedCard.querySelector('.support-actions');
                    actions.innerHTML = `
                        <button class="btn btn-primary" onclick="editSupportContent(${id})">Edit Response</button>
                        <button class="btn btn-danger" onclick="deleteSupportContent(${id})">Delete</button>
                    `;
                    
                    // Add to replied section
                    document.getElementById('repliedContentList').appendChild(clonedCard.closest('.col-xl-6').cloneNode(true));
                    
                    // Remove from not replied section
                    card.closest('.col-xl-6').remove();
                    
                    // Update counts
                    updateCounts();
                }
                alert('Question marked as replied successfully!');
            }
        }
        
        // Function to update the counts in the tabs
        function updateCounts() {
            const repliedCount = document.querySelectorAll('#repliedContentList .support-card').length;
            const notRepliedCount = document.querySelectorAll('#notRepliedContentList .support-card').length;
            
            document.querySelector('#replied-tab .badge').textContent = repliedCount;
            document.querySelector('#not-replied-tab .badge').textContent = notRepliedCount;
        }
        
        // Add event listeners to filter dropdowns
        document.getElementById('filterCategory').addEventListener('change', searchSupportContent);
        document.getElementById('filterStatus').addEventListener('change', searchSupportContent);
        document.getElementById('filterDate').addEventListener('change', searchSupportContent);
        
        // Add event listener to search input
        document.getElementById('searchSupport').addEventListener('keyup', function(event) {
            if (event.key === 'Enter') {
                searchSupportContent();
            }
        });
    </script>
</body>

</html> 