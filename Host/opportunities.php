<?php
session_start();
// Check if user is logged in and is a host
if (!isset($_SESSION['userID']) || $_SESSION['userType'] !== 'host') {
    header("Location: ../Common/login.php");
    exit;
}

require_once '../Controllers/OpportunityController.php';
require_once '../Models/Opportunity.php';

// Initialize variables
$successMsg = null;
$errMsg = null;

// Check if there are success or error messages in the session
if (isset($_SESSION['opportunity_success'])) {
    $successMsg = $_SESSION['opportunity_success'];
    unset($_SESSION['opportunity_success']); // Clear the message after displaying
}

if (isset($_SESSION['opportunity_error'])) {
    $errMsg = $_SESSION['opportunity_error'];
    unset($_SESSION['opportunity_error']); // Clear the message after displaying
}

// Create opportunity controller
$opportunityController = new OpportunityController();

// Get all opportunities for the current host
$hostId = $_SESSION['userID'];
$opportunities = $opportunityController->getOppByHostId($hostId);
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="utf-8">
    <title>HomeStays - My Opportunities</title>
    <meta content="width=device-width, initial-scale=1.0" name="viewport">
    <meta content="homestays, cultural exchange, host opportunities, volunteer management" name="keywords">
    <meta content="Manage your homestay opportunities and cultural exchange programs" name="description">

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

    <!-- Opportunities Start -->
    <div class="container-fluid py-5">
        <div class="container py-5">
            <!-- Page Header -->
            <div class="text-center mb-5">
                <h1 class="mb-3">My Opportunities</h1>
                <p class="mb-0">Manage your cultural exchange opportunities and volunteer positions</p>
            </div>

            <!-- Success and Error Messages - only instance -->
            <?php if ($successMsg): ?>
                <div class="alert alert-success alert-dismissible fade show" role="alert">
                    <i class="fas fa-check-circle me-2"></i><?= htmlspecialchars($successMsg) ?>
                    <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                </div>
            <?php endif; ?>
            
            <?php if ($errMsg): ?>
                <div class="alert alert-danger alert-dismissible fade show" role="alert">
                    <i class="fas fa-exclamation-circle me-2"></i><?= htmlspecialchars($errMsg) ?>
                    <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                </div>
            <?php endif; ?>

            <!-- Filters -->
            <div class="row mb-4">
                <div class="col-12">
                    <div class="card border-0 shadow-sm">
                        <div class="card-body">
                            <div class="row g-3">
                                <div class="col-md-4">
                                    <select class="form-select" id="statusFilter">
                                        <option selected>Status</option>
                                        <option value="open">Open</option>
                                        <option value="closed">Closed</option>
                                        <option value="cancelled">Cancelled</option>
                                        <option value="reported">Reported</option>
                                    </select>
                                </div>
                                <div class="col-md-4">
                                    <select class="form-select" id="categoryFilter">
                                        <option selected>Category</option>
                                        <option value="teaching">Teaching</option>
                                        <option value="farming">Farming</option>
                                        <option value="cooking">Cooking</option>
                                        <option value="childcare">Childcare</option>
                                    </select>
                                </div>
                                <div class="col-md-4">
                                    <button class="btn btn-primary w-100" id="applyFilters">Apply Filters</button>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Opportunities List -->
            <div class="row g-4">
                <?php
                // Print the opportunities using the printOpportunities method
                if (empty($opportunities)) {
                    echo "<div class='col-12 text-center'><p class='alert alert-info'>You haven't created any opportunities yet. <a href='create-opportunity.php' class='alert-link'>Create your first opportunity</a> to start connecting with travelers!</p></div>";
                } else {
                    foreach ($opportunities as $opportunity) {
                        $statusClass = ($opportunity['status'] === 'open') ? 'bg-success' : 
                                      (($opportunity['status'] === 'closed') ? 'bg-danger' : 
                                      (($opportunity['status'] === 'cancelled') ? 'bg-warning' : 'bg-secondary'));
                        
                        echo '<div class="col-lg-6 mb-4">
                                <div class="card border-0 shadow-sm h-100">
                                    <div class="card-body">
                                        <div class="d-flex justify-content-between align-items-center mb-3">
                                            <img src="../uploads/opportunities/' . ($opportunity['opportunity_photo'] ?? 'default.jpg') . '" alt="Opportunity" class="img-fluid rounded-circle" style="width: 80px; height: 80px; object-fit: cover;">
                                            <h5 class="card-title mb-0">' . htmlspecialchars($opportunity['title']) . '</h5>
                                            <span class="badge ' . $statusClass . ' text-white">' . ucfirst($opportunity['status']) . '</span>
                                        </div>
                                        <div class="mb-3">
                                            <p class="mb-2"><i class="fa fa-clock me-2"></i>Created: ' . date('M d, Y', strtotime($opportunity['created_at'])) . '</p>
                                            <p class="mb-2"><i class="bi bi-tags-fill me-2"></i>Category: ' . htmlspecialchars($opportunity['category']) . '</p>
                                            <p class="mb-2"><i class="fa fa-map-marker-alt me-2"></i>Location: ' . htmlspecialchars($opportunity['location']) . '</p>
                                            <p class="mb-2"><i class="bi bi-calendar-fill me-2"></i>Dates: ' . date('M d', strtotime($opportunity['start_date'])) . ' - ' . date('M d, Y', strtotime($opportunity['end_date'])) . '</p>
                                        </div>
                                        <div class="d-flex justify-content-between mt-3">
                                            <div>
                                                <a href="edit-opportunity.php?id=' . $opportunity['opportunity_id'] . '" class="btn btn-primary me-2 px-3">Edit</a>
                                                <button class="btn btn-danger me-2 px-3" onclick="deleteOpportunity(' . $opportunity['opportunity_id'] . ')">Delete</button>
                                            </div>
                                            ' . (($opportunity['status'] === 'open') ? '<button class="btn btn-sm btn-warning" onclick="markAsFilled(' . $opportunity['opportunity_id'] . ')">Mark Filled</button>' : '') . '
                                        </div>
                                    </div>
                                </div>
                            </div>';
                    }
                }
                ?>
            </div>

            <!-- Pagination -->
            <div class="pagination">
                <button id="prevPage" class="btn btn-sm">&laquo; Prev</button>
                <div id="pageNumbers" class="page-numbers"></div>
                <button id="nextPage" class="btn btn-sm ">Next &raquo;</button>
            </div>


        </div>
    </div>
    <!-- Opportunities End -->

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
    <script>
    document.addEventListener("DOMContentLoaded", function () {
    const opportunities = <?php echo json_encode($opportunities); ?>; // PHP array passed to JS

    let filteredOpportunities = opportunities;
    let currentPage = 1;
    const itemsPerPage = 6;

    // Function to display opportunities
    function displayOpportunities() {
        const start = (currentPage - 1) * itemsPerPage;
        const end = currentPage * itemsPerPage;
        const paginatedOpportunities = filteredOpportunities.slice(start, end);

        // Clear existing opportunities
        const opportunitiesContainer = document.querySelector(".row.g-4");
        opportunitiesContainer.innerHTML = '';

        // Loop through and display the filtered opportunities
        paginatedOpportunities.forEach(opp => {
            let statusText = opp.status.charAt(0).toUpperCase() + opp.status.slice(1);
            let statusColor = (opp.status === "open") ? "bg-success text-white" :
                              (opp.status === "closed") ? "bg-danger text-white" : 
                              (opp.status === "cancelled") ? "bg-warning text-dark" : "bg-secondary text-white";

            let opportunityHTML = `
                <div class="col-lg-6">
                    <div class="card border-0 shadow-sm">
                        <div class="card-body">
                            <div class="d-flex justify-content-between align-items-center mb-3">
                                <img src="${opp.opportunity_photo || 'default_image.jpg'}" alt="Opportunity Image" class="img-fluid rounded-circle" style="width: 100px; height: 100px;">
                                <h5 class="card-title mb-0">${opp.title}</h5>
                                <span class="badge ${statusColor}">${statusText}</span>
                            </div>
                            <div class="mb-3">
                                <p class="mb-2"><i class="fa fa-clock me-2"></i>Created At: ${opp.created_at}</p>
                                <p class="mb-2"><i class="bi bi-tags-fill me-2"></i> Category: ${opp.category}</p>
                                <p class="mb-2"><i class="fa fa-location-arrow me-2"></i>Location: ${opp.location}</p>
                                <p class="mb-2"><i class="bi bi-calendar-fill me-2"></i> Start Date: ${opp.start_date}</p>
                                <p class="mb-2"><i class="bi bi-calendar-check-fill me-2"></i> End Date: ${opp.end_date}</p>
                                <p class="mb-2"><i class="fa fa-tasks me-2"></i>Requirements: ${opp.requirements}</p>
                                <p class="mb-2"><i class="fa fa-info-circle me-2"></i>Description: ${opp.description}</p>
                            </div>
                            <div class="d-flex justify-content-between">
                                <div>
                                    <a href="edit-opportunity.php?id=${opp.opportunity_id}" class="btn btn-primary me-2 px-3">Edit</a>
                                    <button class="btn btn-danger me-2 px-3" onclick="deleteOpportunity(${opp.opportunity_id})">Delete</button>
                                </div>
                                <button class="btn btn-sm btn-warning" onclick="markAsFilled(${opp.opportunity_id})">Mark Filled</button>
                            </div>
                        </div>
                    </div>
                </div>`;
            opportunitiesContainer.innerHTML += opportunityHTML;
        });

        // Update pagination visibility and active status
        updatePagination();
    }

    // Function to apply filters based on selected status and category
    document.getElementById("applyFilters").addEventListener("click", function () {
        const selectedStatus = document.getElementById("statusFilter").value;
        const selectedCategory = document.getElementById("categoryFilter").value;

        // Filter the opportunities based on selected status and category
        filteredOpportunities = opportunities.filter(opp => {
            const statusMatches = (selectedStatus === "Status" || opp.status === selectedStatus);
            const categoryMatches = (selectedCategory === "Category" || opp.category.toLowerCase() === selectedCategory.toLowerCase());
            return statusMatches && categoryMatches;
        });

        currentPage = 1;  // Reset to first page when filters are applied
        displayOpportunities();
    });

    // Function to generate dynamic page numbers
    function generatePageNumbers() {
        const totalPages = Math.ceil(filteredOpportunities.length / itemsPerPage);
        const pageNumbersContainer = document.getElementById("pageNumbers");
        
        // Clear existing page numbers
        pageNumbersContainer.innerHTML = '';

        // Create page number buttons dynamically
        for (let i = 1; i <= totalPages; i++) {
            const pageNumber = document.createElement("span");
            pageNumber.classList.add("page-link");
            pageNumber.innerText = i;
            pageNumber.addEventListener("click", function () {
                currentPage = i;
                displayOpportunities();
            });
            pageNumbersContainer.appendChild(pageNumber);
        }
    }

    // Update pagination links based on the filtered opportunities
    function updatePagination() {
        const totalPages = Math.ceil(filteredOpportunities.length / itemsPerPage);

        // Update Prev/Next buttons visibility
        document.getElementById("prevPage").classList.toggle("disabled", currentPage === 1);
        document.getElementById("nextPage").classList.toggle("disabled", currentPage === totalPages);

        // Generate dynamic page numbers
        generatePageNumbers();
    }

    // Pagination event listeners
    document.getElementById("prevPage").addEventListener("click", function () {
        if (currentPage > 1) {
            currentPage--;
            displayOpportunities();
        }
    });

    document.getElementById("nextPage").addEventListener("click", function () {
        const totalPages = Math.ceil(filteredOpportunities.length / itemsPerPage);
        if (currentPage < totalPages) {
            currentPage++;
            displayOpportunities();
        }
    });

    // Initialize the display with all opportunities
    displayOpportunities();
});

    function deleteOpportunity(id) {
        if (confirm('Are you sure you want to delete this opportunity? This action cannot be undone.')) {
            // Send AJAX request to delete the opportunity
            fetch(`delete-opportunity.php?id=${id}`, {
                method: 'POST'
            })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    // Show success message
                    const alertDiv = document.createElement('div');
                    alertDiv.className = 'alert alert-success alert-dismissible fade show';
                    alertDiv.innerHTML = `
                        <i class="fas fa-check-circle me-2"></i>Opportunity deleted successfully!
                        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                    `;
                    
                    // Insert the alert at the top of the opportunities container
                    const container = document.querySelector('.container.py-5');
                    const header = container.querySelector('.text-center.mb-5');
                    container.insertBefore(alertDiv, header.nextSibling);
                    
                    // Remove the opportunity card from the DOM
                    const card = document.querySelector(`.opportunity-card[data-id="${id}"]`);
                    if (card) {
                        card.closest('.col-lg-4').remove();
                    }
                    
                    // Scroll to the top to show the success message
                    window.scrollTo({top: 0, behavior: 'smooth'});
                    
                    // Auto-dismiss the alert after 3 seconds
                    setTimeout(() => {
                        alertDiv.classList.remove('show');
                        setTimeout(() => alertDiv.remove(), 150);
                    }, 3000);
                } else {
                    alert('Error: ' + (data.error || 'Unknown error'));
                }
            })
            .catch(error => {
                console.error('Error:', error);
                alert('An error occurred while deleting the opportunity.');
            });
        }
    }

    function markAsFilled(id) {
        if (confirm('Are you sure you want to mark this opportunity as filled?')) {
            // Send AJAX request to mark the opportunity as filled
            fetch(`update-opportunity-status.php?id=${id}&status=closed`, {
                method: 'POST'
            })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    // Update the status badge
                    const statusBadge = document.querySelector(`.opportunity-card[data-id="${id}"] .badge`);
                    if (statusBadge) {
                        statusBadge.textContent = 'Closed';
                        statusBadge.classList.remove('bg-success');
                        statusBadge.classList.add('bg-secondary');
                    }
                    alert('Opportunity marked as filled!');
                } else {
                    alert('Error: ' + data.error);
                }
            })
            .catch(error => {
                console.error('Error:', error);
                alert('An error occurred while updating the opportunity status.');
            });
        }
    }
    </script>
    <style>
    .pagination {
        display: flex;
        justify-content: center;
        align-items: center;
        gap: 10px;
        margin-top: 100px;
    }

    .page-link {
        padding: 8px 12px;
        background-color: #13357B;  /* Dark background to match the page theme */
        color: #fff;  /* White text */
        border: none;  /* No border for the page numbers */
        border-radius: 5px;
        cursor: pointer;
        font-size: 16px;
        transition: background-color 0.3s, color 0.3s;
    }

    .pagination button {
        padding: 8px 15px;
        background-color: #13357B;  /* Dark background to match the page theme */
        color: white;
        border: none;
        border-radius: 5px;
        cursor: pointer;
        font-size: 16px;
        transition: background-color 0.3s;
    }

    </style>

</body>
</html>
























