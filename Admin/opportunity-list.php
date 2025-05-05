<!DOCTYPE html>
<html lang="en">

<head>
    <title>Homestay Opportunities - HomeStay Admin</title>
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
    <meta name="description" content="Homestay Opportunities Management for HomeStay Admin Dashboard" />
    <meta name="keywords" content="admin, dashboard, opportunities, homestay, management">
    <meta name="author" content="HomeStay" />
    <!-- Favicon icon -->
    <link rel="icon" href="assets/images/favicon.ico" type="image/x-icon">

    <!-- vendor css -->
    <link rel="stylesheet" href="assets/css/style.css">
    <!-- custom css -->
    <link rel="stylesheet" href="assets/css/custom.css">
    <style>
        /* Opportunity Card Styles */
        .opportunity-card {
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
        
        .opportunity-card:hover {
            box-shadow: 0 5px 15px rgba(0, 0, 0, 0.15);
            transform: translateY(-3px);
        }
        
        .opportunity-header {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 15px;
            padding-bottom: 10px;
            border-bottom: 1px solid #eee;
        }
        
        .opportunity-title {
            font-size: 18px;
            font-weight: 600;
            margin: 0;
            line-height: 1.4;
        }
        
        .opportunity-location {
            color: #666;
            margin-bottom: 15px;
            line-height: 1.5;
        }
        
        .opportunity-details {
            margin-bottom: 20px;
        }
        
        .detail-item {
            display: flex;
            margin-bottom: 10px;
            line-height: 1.6;
        }
        
        .detail-label {
            min-width: 120px;
            margin-right: 10px;
        }
        
        .detail-value {
            flex: 1;
        }
        
        .opportunity-description {
            margin-bottom: 15px;
            line-height: 1.6;
            color: #555;
            flex-grow: 1;
            overflow: hidden;
            display: -webkit-box;
            -webkit-line-clamp: 3;
            -webkit-box-orient: vertical;
        }
        
        .opportunity-requirements {
            margin-bottom: 20px;
            line-height: 1.6;
            color: #555;
            overflow: hidden;
            display: -webkit-box;
            -webkit-line-clamp: 2;
            -webkit-box-orient: vertical;
        }
        
        .opportunity-actions {
            display: flex;
            gap: 10px;
            margin-top: auto;
        }
        
        .opportunity-actions .btn {
            padding: 8px 20px;
            border-radius: 5px;
            font-weight: 500;
            transition: all 0.3s ease;
            min-width: 100px;
        }
        
        .opportunity-actions .btn:hover {
            transform: translateY(-2px);
            box-shadow: 0 4px 8px rgba(0, 0, 0, 0.1);
        }
        
        /* Form Styles */
        .form-group {
            margin-bottom: 20px;
        }
        
        .form-control {
            line-height: 1.5;
            padding: 10px 15px;
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
            .opportunity-card {
                padding: 15px;
            }
            
            .detail-item {
                flex-direction: column;
                margin-bottom: 15px;
            }
            
            .detail-label {
                margin-bottom: 5px;
            }
        }
        
        /* Add spacing between cards */
        .col-xl-6 {
            padding: 15px;
        }
        
        /* Add more space between rows */
        .row#opportunityList {
            margin: -15px;
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
                            <h5 class="m-b-10">Homestay Opportunities</h5>
                        </div>
                        <ul class="breadcrumb">
                            <li class="breadcrumb-item"><a href="index.html"><i class="feather icon-home"></i></a></li>
                            <li class="breadcrumb-item"><a href="#!">Homestay Opportunities</a></li>
                        </ul>
                    </div>
                </div>
            </div>
        </div>
        <!-- [ breadcrumb ] end -->
        <!-- [ Main Content ] start -->
        <div class="row">
            <!-- Opportunity List -->
            <div class="col-xl-12 col-md-12">
                <div class="card">
                    <div class="card-header">
                        <h5><i class="feather icon-list mr-2"></i>Opportunity List</h5>
                    </div>
                    <div class="card-body">
                        <!-- Enhanced Search and Filters -->
                        <div class="row mb-4 filter-row">
                            <div class="col-md-12 mb-3">
                                <div class="input-group">
                                    <input type="text" class="form-control" id="searchOpportunity" placeholder="Search opportunities by title, location, work type, or description...">
                                    <div class="input-group-append">
                                        <button class="btn btn-primary" type="button" onclick="searchOpportunities()">
                                            <i class="feather icon-search"></i> Search
                                        </button>
                                    </div>
                                </div>
                            </div>
                            <div class="col-md-3">
                                <div class="form-group">
                                    <label for="filterLocation">Filter by Location</label>
                                    <select class="form-control" id="filterLocation">
                                        <option value="">All Locations</option>
                                        <option value="tokyo">Tokyo</option>
                                        <option value="barcelona">Barcelona</option>
                                        <option value="vancouver">Vancouver</option>
                                        <option value="cape-town">Cape Town</option>
                                    </select>
                                </div>
                            </div>
                            <div class="col-md-3">
                                <div class="form-group">
                                    <label for="filterWorkType">Filter by Work Type</label>
                                    <select class="form-control" id="filterWorkType">
                                        <option value="">All Work Types</option>
                                        <option value="farming">Farming</option>
                                        <option value="teaching">Teaching</option>
                                        <option value="childcare">Childcare</option>
                                        <option value="elderly">Elderly Care</option>
                                        <option value="housekeeping">Housekeeping</option>
                                        <option value="construction">Construction</option>
                                    </select>
                                </div>
                            </div>
                            <div class="col-md-3">
                                <div class="form-group">
                                    <label for="filterStatus">Filter by Status</label>
                                    <select class="form-control" id="filterStatus">
                                        <option value="">All Status</option>
                                        <option value="active">Active</option>
                                        <option value="inactive">Inactive</option>
                                        <option value="filled">Filled</option>
                                    </select>
                                </div>
                            </div>
                            <div class="col-md-3">
                                <div class="form-group">
                                    <label for="filterDate">Filter by Start Date</label>
                                    <select class="form-control" id="filterDate">
                                        <option value="">All Dates</option>
                                        <option value="this-month">This Month</option>
                                        <option value="next-month">Next Month</option>
                                        <option value="next-3-months">Next 3 Months</option>
                                        <option value="next-6-months">Next 6 Months</option>
                                    </select>
                                </div>
                            </div>
                        </div>
                        
                        <!-- Opportunity Cards -->
                        <div class="row" id="opportunityList">
                            <!-- Opportunity Card 1 -->
                            <div class="col-xl-6 col-md-6">
                                <div class="opportunity-card">
                                    <div class="opportunity-header">
                                        <h6 class="opportunity-title">Organic Farm Assistant</h6>
                                        <span class="badge badge-success">Active</span>
                                    </div>
                                    <div class="opportunity-location">
                                        <i class="feather icon-map-pin"></i> Tokyo, Japan
                                    </div>
                                    <div class="opportunity-details">
                                        <div class="detail-item">
                                            <span class="detail-label text-primary font-weight-bold">Work Type:</span>
                                            <span class="detail-value">Farming</span>
                                        </div>
                                        <div class="detail-item">
                                            <span class="detail-label text-primary font-weight-bold">Work Hours:</span>
                                            <span class="detail-value">25 hrs/week</span>
                                        </div>
                                        <div class="detail-item">
                                            <span class="detail-label text-primary font-weight-bold">Duration:</span>
                                            <span class="detail-value">4 weeks</span>
                                        </div>
                                        <div class="detail-item">
                                            <span class="detail-label text-primary font-weight-bold">Start Date:</span>
                                            <span class="detail-value">Jun 15, 2023</span>
                                        </div>
                                    </div>
                                    <div class="opportunity-description">
                                        Help on our organic farm in the outskirts of Tokyo. Tasks include planting, harvesting, and general farm maintenance. Learn about sustainable farming practices while experiencing Japanese culture.
                                    </div>
                                    <div class="opportunity-requirements">
                                        <strong>Requirements:</strong> No prior farming experience needed, but must be physically fit and willing to work outdoors in various weather conditions.
                                    </div>
                                    <div class="opportunity-actions">
                                        <button class="btn btn-primary" onclick="editOpportunity(1)">Edit</button>
                                        <button class="btn btn-danger" onclick="deleteOpportunity(1)">Delete</button>
                                    </div>
                                </div>
                            </div>
                            
                            <!-- Opportunity Card 2 -->
                            <div class="col-xl-6 col-md-6">
                                <div class="opportunity-card">
                                    <div class="opportunity-header">
                                        <h6 class="opportunity-title">English Language Tutor</h6>
                                        <span class="badge badge-success">Active</span>
                                    </div>
                                    <div class="opportunity-location">
                                        <i class="feather icon-map-pin"></i> Barcelona, Spain
                                    </div>
                                    <div class="opportunity-details">
                                        <div class="detail-item">
                                            <span class="detail-label text-primary font-weight-bold">Work Type:</span>
                                            <span class="detail-value">Teaching</span>
                                        </div>
                                        <div class="detail-item">
                                            <span class="detail-label text-primary font-weight-bold">Work Hours:</span>
                                            <span class="detail-value">15 hrs/week</span>
                                        </div>
                                        <div class="detail-item">
                                            <span class="detail-label text-primary font-weight-bold">Duration:</span>
                                            <span class="detail-value">12 weeks</span>
                                        </div>
                                        <div class="detail-item">
                                            <span class="detail-label text-primary font-weight-bold">Start Date:</span>
                                            <span class="detail-value">Jul 1, 2023</span>
                                        </div>
                                    </div>
                                    <div class="opportunity-description">
                                        Teach English to our children (ages 8 and 10) while experiencing life in beautiful Barcelona. We are a family of four looking for a native English speaker to help our children improve their language skills.
                                    </div>
                                    <div class="opportunity-requirements">
                                        <strong>Requirements:</strong> Native English speaker, teaching experience preferred but not required. Must be patient and creative with children.
                                    </div>
                                    <div class="opportunity-actions">
                                        <button class="btn btn-primary" onclick="editOpportunity(2)">Edit</button>
                                        <button class="btn btn-danger" onclick="deleteOpportunity(2)">Delete</button>
                                    </div>
                                </div>
                            </div>
                            
                            <!-- Opportunity Card 3 -->
                            <div class="col-xl-6 col-md-6">
                                <div class="opportunity-card">
                                    <div class="opportunity-header">
                                        <h6 class="opportunity-title">Elderly Care Assistant</h6>
                                        <span class="badge badge-success">Active</span>
                                    </div>
                                    <div class="opportunity-location">
                                        <i class="feather icon-map-pin"></i> Vancouver, Canada
                                    </div>
                                    <div class="opportunity-details">
                                        <div class="detail-item">
                                            <span class="detail-label text-primary font-weight-bold">Work Type:</span>
                                            <span class="detail-value">Elderly Care</span>
                                        </div>
                                        <div class="detail-item">
                                            <span class="detail-label text-primary font-weight-bold">Work Hours:</span>
                                            <span class="detail-value">20 hrs/week</span>
                                        </div>
                                        <div class="detail-item">
                                            <span class="detail-label text-primary font-weight-bold">Duration:</span>
                                            <span class="detail-value">8 weeks</span>
                                        </div>
                                        <div class="detail-item">
                                            <span class="detail-label text-primary font-weight-bold">Start Date:</span>
                                            <span class="detail-value">Aug 10, 2023</span>
                                        </div>
                                    </div>
                                    <div class="opportunity-description">
                                        Help care for my 85-year-old mother who lives with us. Tasks include companionship, light housekeeping, meal preparation, and occasional assistance with mobility. Experience the beautiful city of Vancouver while making a difference in someone's life.
                                    </div>
                                    <div class="opportunity-requirements">
                                        <strong>Requirements:</strong> Experience with elderly care preferred. Must be patient, compassionate, and reliable. Basic first aid knowledge is a plus.
                                    </div>
                                    <div class="opportunity-actions">
                                        <button class="btn btn-primary" onclick="editOpportunity(3)">Edit</button>
                                        <button class="btn btn-danger" onclick="deleteOpportunity(3)">Delete</button>
                                    </div>
                                </div>
                            </div>
                            
                            <!-- Opportunity Card 4 -->
                            <div class="col-xl-6 col-md-6">
                                <div class="opportunity-card">
                                    <div class="opportunity-header">
                                        <h6 class="opportunity-title">Childcare Helper</h6>
                                        <span class="badge badge-success">Active</span>
                                    </div>
                                    <div class="opportunity-location">
                                        <i class="feather icon-map-pin"></i> Cape Town, South Africa
                                    </div>
                                    <div class="opportunity-details">
                                        <div class="detail-item">
                                            <span class="detail-label text-primary font-weight-bold">Work Type:</span>
                                            <span class="detail-value">Childcare</span>
                                        </div>
                                        <div class="detail-item">
                                            <span class="detail-label text-primary font-weight-bold">Work Hours:</span>
                                            <span class="detail-value">30 hrs/week</span>
                                        </div>
                                        <div class="detail-item">
                                            <span class="detail-label text-primary font-weight-bold">Duration:</span>
                                            <span class="detail-value">6 weeks</span>
                                        </div>
                                        <div class="detail-item">
                                            <span class="detail-label text-primary font-weight-bold">Start Date:</span>
                                            <span class="detail-value">Sep 5, 2023</span>
                                        </div>
                                    </div>
                                    <div class="opportunity-description">
                                        Help with our three children (ages 3, 5, and 7) while experiencing the vibrant culture of Cape Town. We are a family of five looking for someone to assist with childcare, light housekeeping, and meal preparation.
                                    </div>
                                    <div class="opportunity-requirements">
                                        <strong>Requirements:</strong> Experience with children required. Must be energetic, creative, and able to handle multiple children. First aid knowledge is a plus.
                                    </div>
                                    <div class="opportunity-actions">
                                        <button class="btn btn-primary" onclick="editOpportunity(4)">Edit</button>
                                        <button class="btn btn-danger" onclick="deleteOpportunity(4)">Delete</button>
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
        // Function to search opportunities
        function searchOpportunities() {
            const searchTerm = document.getElementById('searchOpportunity').value.toLowerCase();
            const location = document.getElementById('filterLocation').value;
            const workType = document.getElementById('filterWorkType').value;
            const status = document.getElementById('filterStatus').value;
            const date = document.getElementById('filterDate').value;
            
            const cards = document.querySelectorAll('.opportunity-card');
            
            cards.forEach(card => {
                const title = card.querySelector('.opportunity-title').textContent.toLowerCase();
                const cardLocation = card.querySelector('.opportunity-location').textContent.toLowerCase();
                const cardWorkType = card.querySelector('.detail-value').textContent.toLowerCase();
                const cardStatus = card.querySelector('.badge').textContent.toLowerCase();
                
                let showCard = true;
                
                // Search term filter
                if (searchTerm && !title.includes(searchTerm) && !cardLocation.includes(searchTerm) && !cardWorkType.includes(searchTerm)) {
                    showCard = false;
                }
                
                // Location filter
                if (location && !cardLocation.includes(location)) {
                    showCard = false;
                }
                
                // Work type filter
                if (workType && !cardWorkType.includes(workType)) {
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
        
        // Function to edit opportunity
        function editOpportunity(id) {
            // Redirect to the opportunity edit page
            window.location.href = `opportunity-edit.html?id=${id}`;
        }
        
        // Function to delete opportunity
        function deleteOpportunity(id) {
            if (confirm('Are you sure you want to delete this opportunity?')) {
                // Here you would typically make an API call to delete the opportunity
                // For now, we'll just remove the card from the DOM
                const card = document.querySelector(`.opportunity-card[data-id="${id}"]`);
                if (card) {
                    card.closest('.col-xl-6').remove();
                }
                alert('Opportunity deleted successfully!');
            }
        }
        
        // Add event listeners to filter dropdowns
        document.getElementById('filterLocation').addEventListener('change', searchOpportunities);
        document.getElementById('filterWorkType').addEventListener('change', searchOpportunities);
        document.getElementById('filterStatus').addEventListener('change', searchOpportunities);
        document.getElementById('filterDate').addEventListener('change', searchOpportunities);
        
        // Add event listener to search input
        document.getElementById('searchOpportunity').addEventListener('keyup', function(event) {
            if (event.key === 'Enter') {
                searchOpportunities();
            }
        });
    </script>
</body>

</html> 