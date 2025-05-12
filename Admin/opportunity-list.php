<?php
    session_start();
    
    // Check if user is logged in and is an admin
    if (!isset($_SESSION['userID']) || $_SESSION['userType'] !== 'admin') {
        header("Location: ../Common/login.php");
        exit;
    }
    
    require_once '../Controllers/OpportunityController.php';
    $controller = new OpportunityController();

    // Call the function to get all opportunities
    $opportunities = $controller->getAllOpportunities();  // This should return an array of opportunities
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
                                <li class="breadcrumb-item"><a href="index.php"><i class="feather icon-home"></i></a></li>
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
                            <!-- Success/Error Messages -->
                            <div id="messageContainer"></div>
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
                                        <select class="form-control" id="filterWorkType" onchange="searchOpportunities()">
                                            <option value="">All Work Types</option>
                                            <option value="farming">Farming</option>
                                            <option value="teaching">Teaching</option>
                                            <option value="childcare">Childcare</option>
                                            <option value="cooking">Cooking</option>
                                            <option value="housekeeping">Housekeeping</option>
                                            <option value="construction">Construction</option>
                                        </select>
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="form-group">
                                        <label for="filterStatus">Filter by Status</label>
                                        <select class="form-control" id="filterStatus" onchange="searchOpportunities()">
                                            <option value="">All Status</option>
                                            <option value="open">Open</option>
                                            <option value="closed">Closed</option>
                                            <option value="cancelled">Cancelled</option>
                                            <option value="reported">Reported</option>
                                        </select>
                                    </div>
                                </div>
                            </div>

                            <!-- Opportunity Cards -->
                            <div class="row" id="opportunityList">
                                <?php foreach ($opportunities as $opportunity): ?>
                                    <div class="col-xl-6 col-md-6">
                                        <div class="opportunity-card" data-id="<?= $opportunity['opportunity_id'] ?>">
                                            <div class="opportunity-header">
                                                <img src="<?= isset($opportunity['opportunity_photo']) ? htmlspecialchars($opportunity['opportunity_photo']) : '../assets/images/default-opportunity.jpg' ?>" alt="Opportunity Image" class="img-fluid rounded-circle" style="width: 100px; height: 100px;">
                                                <h6 class="opportunity-title"><?= htmlspecialchars($opportunity['title']) ?></h6>
                                                <?php
                                                    $statusClass = '';
                                                    switch(strtolower($opportunity['status'])) {
                                                        case 'open':
                                                            $statusClass = 'badge-success';
                                                            break;
                                                        case 'closed':
                                                            $statusClass = 'badge-danger';
                                                            break;
                                                        case 'cancelled':
                                                            $statusClass = 'badge-warning';
                                                            break;
                                                        case 'reported':
                                                            $statusClass = 'badge-secondary';
                                                            break;
                                                        default:
                                                            $statusClass = 'badge-info';
                                                    }
                                                ?>
                                                <span class="badge <?= $statusClass ?>"><?= ucfirst(htmlspecialchars($opportunity['status'])) ?></span>
                                            </div>
                                            <div class="opportunity-location">
                                                <i class="feather icon-map-pin"></i> <?= htmlspecialchars($opportunity['location']) ?>
                                            </div>
                                            <div class="opportunity-details">
                                                <div class="detail-item">
                                                    <div class="detail-label">Work Type:</div>
                                                    <div class="detail-value"><?= htmlspecialchars($opportunity['category']) ?></div>
                                                </div>
                                                <div class="detail-item">
                                                    <div class="detail-label">Host:</div>
                                                    <div class="detail-value">
                                                        <?= isset($opportunity['first_name']) ? htmlspecialchars($opportunity['first_name'] . ' ' . $opportunity['last_name']) : 'Unknown Host' ?>
                                                    </div>
                                                </div>
                                                <div class="detail-item">
                                                    <div class="detail-label">Duration:</div>
                                                    <div class="detail-value">
                                                        <?= htmlspecialchars($opportunity['start_date']) ?> to <?= htmlspecialchars($opportunity['end_date']) ?>
                                                    </div>
                                                </div>
                                            </div>
                                            <div class="opportunity-description">
                                                <strong>Description:</strong> <?= isset($opportunity['description']) ? htmlspecialchars($opportunity['description']) : 'No description available' ?>
                                            </div>
                                            <div class="opportunity-requirements">
                                                <strong>Requirements:</strong> <?= isset($opportunity['requirements']) ? htmlspecialchars($opportunity['requirements']) : 'No specific requirements' ?>
                                            </div>
                                            <div class="opportunity-actions">
                                                <button class="btn btn-info" onclick="viewOpportunity(<?= $opportunity['opportunity_id'] ?>)">View Details</button>
                                                <button class="btn btn-warning" onclick="updateStatus(<?= $opportunity['opportunity_id'] ?>)">Update Status</button>
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

    <!-- Status Update Modal -->
    <div class="modal fade" id="statusModal" tabindex="-1" role="dialog" aria-labelledby="statusModalLabel" aria-hidden="true">
        <div class="modal-dialog" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="statusModalLabel">Update Opportunity Status</h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div class="modal-body">
                    <input type="hidden" id="opportunityId">
                    <div class="form-group">
                        <label for="opportunityStatus">Status</label>
                        <select class="form-control" id="opportunityStatus">
                            <option value="open">Open</option>
                            <option value="closed">Closed</option>
                            <option value="cancelled">Cancelled</option>
                            <option value="reported">Reported</option>
                        </select>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-dismiss="modal">Cancel</button>
                    <button type="button" class="btn btn-primary" onclick="saveStatus()">Save Changes</button>
                </div>
            </div>
        </div>
    </div>

    <!-- JS -->
    <script src="assets/js/vendor-all.min.js"></script>
    <script src="assets/js/plugins/bootstrap.min.js"></script>
    <script src="assets/js/pcoded.min.js"></script>
    <script>
        // Show notification message
        function showNotification(type, message) {
            const messageContainer = document.getElementById('messageContainer');
            const alertClass = type === 'success' ? 'alert-success' : 'alert-danger';
            
            messageContainer.innerHTML = `
                <div class="alert ${alertClass} alert-dismissible fade show" role="alert">
                    ${message}
                    <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
            `;
            
            // Auto-dismiss after 5 seconds
            setTimeout(() => {
                const alert = messageContainer.querySelector('.alert');
                if (alert) {
                    $(alert).alert('close');
                }
            }, 5000);
        }

        function searchOpportunities() {
            const searchTerm = document.getElementById('searchOpportunity').value.toLowerCase();
            const workType = document.getElementById('filterWorkType').value.toLowerCase();
            const status = document.getElementById('filterStatus').value.toLowerCase();
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

        function viewOpportunity(opportunityId) {
            // Redirect to a detailed view page
            window.location.href = `opportunity-detail.php?id=${opportunityId}`;
        }

        function updateStatus(opportunityId) {
            // Set the opportunity ID in the modal
            document.getElementById('opportunityId').value = opportunityId;
            
            // Get the current status from the card
            const card = document.querySelector(`.opportunity-card[data-id="${opportunityId}"]`);
            const currentStatus = card.querySelector('.badge').textContent.toLowerCase();
            
            // Set the current status in the dropdown
            document.getElementById('opportunityStatus').value = currentStatus;
            
            // Show the modal
            $('#statusModal').modal('show');
        }

        function saveStatus() {
            const opportunityId = document.getElementById('opportunityId').value;
            const newStatus = document.getElementById('opportunityStatus').value;

            fetch('opportunity-update-status.php', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/x-www-form-urlencoded'
                },
                body: `id=${opportunityId}&status=${newStatus}`
            })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    showNotification('success', 'Opportunity status updated successfully!');

                    // Update the status badge in the card
                    const card = document.querySelector(`.opportunity-card[data-id="${opportunityId}"]`);
                    const statusBadge = card.querySelector('.badge');
                    statusBadge.textContent = ucfirst(newStatus);
                    statusBadge.className = `badge ${getStatusClass(newStatus)}`;
                } else {
                    showNotification('error', 'Error updating opportunity status: ' + data.message);
                }
            })
            .catch(error => {
                console.error('Error:', error);
                showNotification('error', 'An error occurred while updating the opportunity status.');
            });

            // Close the modal
            $('#statusModal').modal('hide');
        }

        function getStatusClass(status) {
            switch(status.toLowerCase()) {
                case 'open':
                    return 'badge-success';
                case 'closed':
                    return 'badge-danger';
                case 'cancelled':
                    return 'badge-warning';
                case 'reported':
                    return 'badge-secondary';
                default:
                    return 'badge-info';
            }
        }

        function ucfirst(str) {
            return str.charAt(0).toUpperCase() + str.slice(1);
        }

        function deleteOpportunity(opportunityId) {
            if (confirm('Are you sure you want to delete this opportunity? This action cannot be undone.')) {
                fetch('opportunity-delete.php?id=' + opportunityId, {
                    method: 'POST'
                })
                .then(response => response.json())
                .then(data => {
                    if (data.success) {
                        showNotification('success', 'Opportunity deleted successfully!');

                        // Remove the card from the DOM
                        const card = document.querySelector(`button[onclick="deleteOpportunity(${opportunityId})"]`).closest('.col-xl-6');
                        card.remove();
                    } else {
                        showNotification('error', 'Error deleting opportunity: ' + data.message);
                    }
                })
                .catch(error => {
                    console.error('Error:', error);
                    showNotification('error', 'An error occurred while deleting the opportunity.');
                });
            }
        }
    </script>
</body>
</html>





