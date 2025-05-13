<?php
    include_once '../Controllers/OpportunityController.php';

    // Instantiate the controller
    $controller = new OpportunityController();

    // Call the function to get all opportunities
    $opportunities = $controller->getAllOpportunities();  // This should return an array of opportunities

    // Check if opportunities is null or empty
    if (!$opportunities) {
        $opportunities = []; // Initialize as empty array to avoid errors in the foreach loop
    }
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <title>Homestay Opportunities - HomeStay Admin</title>
    <style>
        /* Card Styles */
        .opportunity-card {
            background: #fff;
            border-radius: 8px;
             ;
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

        .opportunity-description,
        .opportunity-requirements {
            margin-bottom: 15px;
            line-height: 1.6;
            color: #555;
            overflow: hidden;
            display: -webkit-box;
            -webkit-box-orient: vertical;
        }

        .opportunity-description {
            -webkit-line-clamp: 3;
            flex-grow: 1;
        }

        .opportunity-requirements {
            -webkit-line-clamp: 2;
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
            min-width: 100px;
        }

        .filter-row {
            margin-bottom: 25px;
            padding: 15px;
            background-color: #f8f9fa;
            border-radius: 5px;
        }

        .col-xl-6 {
            padding: 15px;
        }

        .row#opportunityList {
            margin: -15px;
        }

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
    </style>
</head>
<body class="">
    <?php 
        include 'navCommon.php'; 
        include_once 'header-common.php'
    ?>
    <div class="pcoded-main-container">
        <div class="pcoded-content">
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

            <!-- Main Content -->
            <div class="row">
                <div class="col-xl-12 col-md-12">
                    <div class="card">
                        <div class="card-header">
                            <h5><i class="feather icon-list mr-2"></i>Opportunity List</h5>
                        </div>
                        <div class="card-body">
                            <!-- Search and Filters -->
                            <div class="row mb-4 filter-row">
                                <div class="col-md-12 mb-3">
                                    <div class="input-group">
                                        <input type="text" class="form-control" id="searchOpportunity" placeholder="Search by title, work type, description or status...">
                                        <div class="input-group-append">
                                            <button class="btn btn-primary" type="button" onclick="searchOpportunities()">
                                                <i class="feather icon-search"></i> Search
                                            </button>
                                        </div>
                                    </div>
                                </div>
                                <div class="col-md-6">
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
                                <div class="col-md-6">
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
                            </div>

                            <!-- Opportunity Cards -->
                            <div class="row" id="opportunityList">
                                <?php foreach ($opportunities as $opportunity): ?>
                                    <div class="col-xl-6 col-md-6">
                                        <div class="opportunity-card">
                                            <div class="opportunity-header">
                                                <?php 
                                                // Check if opportunity_photo exists and is not empty
                                                $photoPath = '../uploads/opportunities/';
                                                $photoUrl = isset($opportunity['opportunity_photo']) && !empty($opportunity['opportunity_photo']) 
                                                    ? $photoPath . htmlspecialchars($opportunity['opportunity_photo']) 
                                                    : 'assets/images/default-opportunity.jpg';
                                                ?>
                                                <img src="<?= $photoUrl ?>" alt="Opportunity Image" class="img-fluid rounded-circle" style="width: 100px; height: 100px; object-fit: cover;">
                                                <h6 class="opportunity-title"><?= htmlspecialchars($opportunity['title']) ?></h6>
                                                <span class="badge badge-success"><?= ucfirst(htmlspecialchars($opportunity['status'])) ?></span>
                                            </div>
                                            <div class="opportunity-location">
                                                <i class="feather icon-map-pin"></i> <?= htmlspecialchars($opportunity['location']) ?>
                                            </div>
                                            <div class="opportunity-details">
                                                <div class="detail-item">
                                                    <span class="detail-label text-primary font-weight-bold">Work Type:</span>
                                                    <span class="detail-value" style="text-transform: capitalize;"><?= htmlspecialchars($opportunity['category']) ?></span>
                                                </div>
                                                <div class="detail-item">
                                                    <span class="detail-label text-primary font-weight-bold">Created At:</span>
                                                    <span class="detail-value"><?= isset($opportunity['created_at']) ? (new DateTime($opportunity['created_at']))->format('M d, Y') : 'N/A' ?></span>
                                                </div>
                                                <div class="detail-item">
                                                    <span class="detail-label text-primary font-weight-bold">Duration:</span>
                                                    <span class="detail-value">
                                                        <?php
                                                        if (isset($opportunity['start_date']) && isset($opportunity['end_date'])) {
                                                            echo date_diff(new DateTime($opportunity['start_date']), new DateTime($opportunity['end_date']))->format('%a days');
                                                        } else {
                                                            echo 'N/A';
                                                        }
                                                        ?>
                                                    </span>
                                                </div>
                                                <div class="detail-item">
                                                    <span class="detail-label text-primary font-weight-bold">Start Date:</span>
                                                    <span class="detail-value"><?= isset($opportunity['start_date']) ? (new DateTime($opportunity['start_date']))->format('M d, Y') : 'N/A' ?></span>
                                                </div>
                                                <div class="detail-item">
                                                    <span class="detail-label text-primary font-weight-bold">End Date:</span>
                                                    <span class="detail-value"><?= isset($opportunity['end_date']) ? (new DateTime($opportunity['end_date']))->format('M d, Y') : 'N/A' ?></span>
                                                </div>
                                            </div>
                                            <div class="opportunity-requirements">
                                                <strong>Requirements:</strong> <?= isset($opportunity['requirements']) ? htmlspecialchars($opportunity['requirements']) : 'No specific requirements' ?>
                                            </div>
                                            <div class="opportunity-description">
                                                <strong>Description:</strong> <?= isset($opportunity['description']) ? htmlspecialchars($opportunity['description']) : 'No description available' ?>
                                            </div>
                                            <div class="opportunity-actions">
                                                <button class="btn btn-danger" onclick="deleteOpportunity(<?= $opportunity['opportunity_id'] ?>)">Delete</button>
                                            </div>
                                        </div>
                                    </div>
                                <?php endforeach; ?>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- JS -->
    <script src="assets/js/vendor-all.min.js"></script>
    <script src="assets/js/plugins/bootstrap.min.js"></script>
    <script src="assets/js/pcoded.min.js"></script>
    <script src="assets/js/custom.js"></script>
    <script>
        function searchOpportunities() {
            const searchTerm = document.getElementById('searchOpportunity').value.toLowerCase();
            const workType = document.getElementById('filterWorkType').value;
            const status = document.getElementById('filterStatus').value;
            const cards = document.querySelectorAll('.opportunity-card');
            
            cards.forEach(card => {
                const title = card.querySelector('.opportunity-title').textContent.toLowerCase();
                const work = card.querySelector('.detail-value').textContent.toLowerCase();
                const desc = card.querySelector('.opportunity-description').textContent.toLowerCase();
                const statusText = card.querySelector('.badge').textContent.toLowerCase();
                
                let showCard = true;
                
                if (searchTerm && !(title.includes(searchTerm) || work.includes(searchTerm) || desc.includes(searchTerm) || statusText.includes(searchTerm))) {
                    showCard = false;
                }
                
                if (workType && !work.includes(workType)) {
                    showCard = false;
                }
                
                if (status && !statusText.includes(status)) {
                    showCard = false;
                }
                
                card.closest('.col-xl-6').style.display = showCard ? 'block' : 'none';
            });
        }

        document.getElementById('filterWorkType').addEventListener('change', searchOpportunities);
        document.getElementById('filterStatus').addEventListener('change', searchOpportunities);
        document.getElementById('searchOpportunity').addEventListener('keyup', function (event) {
            if (event.key === 'Enter') {
                searchOpportunities();
            }
        });

        function deleteOpportunity(opportunityId) {
            if (confirm('Are you sure you want to delete this opportunity? This action cannot be undone.')) {
                fetch('opportunity-delete.php?id=' + opportunityId, {
                    method: 'POST'
                })
                .then(response => response.json())
                .then(data => {
                    if (data.success) {
                        alert('Opportunity deleted successfully!');
                        // Remove the card from the DOM
                        const card = document.querySelector(`button[onclick="deleteOpportunity(${opportunityId})"]`).closest('.col-xl-6');
                        card.remove();
                    } else {
                        alert('Error: ' + data.message);
                    }
                })
                .catch(error => {
                    console.error('Error:', error);
                    alert('An error occurred while deleting the opportunity.');
                });
            }
        }
    </script>
</body>
</html>



