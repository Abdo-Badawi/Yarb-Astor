<?php
// Start the session at the very beginning of the file
session_start();

// Check if user is logged in and is an admin
if (!isset($_SESSION['userID']) || $_SESSION['userType'] !== 'admin') {
    header("Location: ../Common/login.php");
    exit;
}

// Load required controllers
require_once '../Controllers/PaymentVerificationRequestController.php';
require_once '../Controllers/FeeTransactionController.php';
require_once '../Controllers/CardController.php';

// Instantiate the controllers
$verificationController = new PaymentVerificationRequestController();
$feeController = new FeeTransactionController();
$cardController = new CardController();

// Get all transactions
$transactions = $feeController->getAllTransactions();

// Get all verification requests using the controller
$verificationRequests = $verificationController->getAllRequests(['status' => ['new', 'pending', 'in_progress']]);

// Helper functions for status and priority badges
function getStatusBadgeClass($status) {
    switch ($status) {
        case 'new':
            return 'bg-secondary';
        case 'pending':
            return 'bg-warning';
        case 'in_progress':
            return 'bg-info';
        case 'resolved':
            return 'bg-success';
        case 'closed':
            return 'bg-dark';
        default:
            return 'bg-secondary';
    }
}

function getPriorityBadgeClass($priority) {
    switch ($priority) {
        case 'low':
            return 'bg-secondary';
        case 'normal':
            return 'bg-info';
        case 'high':
            return 'bg-warning';
        case 'urgent':
            return 'bg-danger';
        default:
            return 'bg-secondary';
    }
}

function getIssueTypeLabel($issueType) {
    switch ($issueType) {
        case 'double_payment':
            return 'Double Payment Issue';
        case 'payment_not_received':
            return 'Payment Not Received';
        case 'refund_request':
            return 'Refund Request';
        case 'payment_method_change':
            return 'Payment Method Change';
        case 'other':
            return 'Other Payment Issue';
        default:
            return 'Unknown Issue';
    }
}
?>
<!DOCTYPE html>
<html lang="en">

<head>
    <title>Payment Verification - HomeStay Admin</title>


    <style>
        /* Make all action buttons the same width */
        .btn-sm {
            min-width: 60px !important;
            max-width: 60px !important;
            text-align: center;
            display: inline-block;
            box-sizing: border-box;
            white-space: nowrap;
            overflow: hidden;
            text-overflow: ellipsis;
            padding: 0.375rem 0.25rem;
            font-size: 0.75rem;
        }

        /* Ensure buttons display horizontally */
        td .btn-sm {
            display: inline-block;
            margin-right: 3px;
        }

        /* Remove margin from last button in a group */
        td .btn-sm:last-child {
            margin-right: 0;
        }

        /* Make all action buttons the same width and center them */
        .table td, .table th {
            text-align: center;
            vertical-align: middle;
            padding: 0.5rem 0.25rem;
        }

        /* Make the table responsive */
        .table-responsive {
            overflow-x: hidden;
        }

        /* Adjust table to prevent horizontal scrolling */
        .table {
            width: 100%;
            table-layout: fixed;
        }

        /* Set column widths */
        .table th:nth-child(1), .table td:nth-child(1) { width: 10%; } /* ID */
        .table th:nth-child(2), .table td:nth-child(2) { width: 12%; } /* Traveler */
        .table th:nth-child(3), .table td:nth-child(3) { width: 12%; } /* Amount */
        .table th:nth-child(4), .table td:nth-child(4) { width: 8%; } /* Date */
        .table th:nth-child(5), .table td:nth-child(5) { width: 10%; } /* Payment Method */
        .table th:nth-child(6), .table td:nth-child(6) { width: 10%; } /* Status */
        .table th:nth-child(7), .table td:nth-child(7) { width: 30%; } /* Actions */

        /* Ensure text doesn't overflow */
        .table td {
            white-space: nowrap;
            overflow: hidden;
            text-overflow: ellipsis;
        }

        /* Enhanced button styling */
        .btn-group-sm .btn {
            border-radius: 4px;
            margin-right: 2px;
            margin-bottom: 2px;
            box-shadow: 0 2px 6px rgba(0,0,0,0.1);
            transition: all 0.2s ease;
        }

        .btn-group-sm .btn:hover {
            transform: translateY(-2px);
            box-shadow: 0 4px 8px rgba(0,0,0,0.15);
        }

        .btn-group-sm .btn:active {
            transform: translateY(0);
            box-shadow: 0 1px 3px rgba(0,0,0,0.1);
        }

        /* Styles for verification request buttons */
        .verification-btn {
            min-width: 150px !important;
            margin-right: 10px;
            margin-bottom: 10px;
            padding: 8px 15px;
            font-size: 14px;
            font-weight: 500;
            border-width: 2px;
            transition: all 0.3s ease;
            box-shadow: 0 2px 6px rgba(0,0,0,0.1);
        }

        .verification-btn:hover {
            transform: translateY(-2px);
            box-shadow: 0 4px 8px rgba(0,0,0,0.15);
        }

        .verification-btn:active {
            transform: translateY(0);
            box-shadow: 0 1px 3px rgba(0,0,0,0.1);
        }

        .verification-btn i {
            font-size: 16px;
        }

        /* Button colors */
        .btn-info {
            background-color: #4099ff;
            border-color: #4099ff;
        }

        .btn-secondary {
            background-color: #6c757d;
            border-color: #6c757d;
        }

        .btn-danger {
            background-color: #ff5370;
            border-color: #ff5370;
        }

        .btn-primary {
            background-color: #2ed8b6;
            border-color: #2ed8b6;
        }

        .btn-warning {
            background-color: #FFB64D;
            border-color: #FFB64D;
            color: white;
        }

        .btn-success {
            background-color: #2ed8b6;
            border-color: #2ed8b6;
        }

        /* Make badge text white and more prominent */
        .badge {
            color: white !important;
            font-weight: 500;
            padding: 5px 10px;
            font-size: 12px;
            letter-spacing: 0.5px;
            border-radius: 4px;
            box-shadow: 0 2px 4px rgba(0,0,0,0.1);
        }

        /* Enhance badge colors */
        .bg-secondary, .badge-secondary {
            background-color: #6c757d !important;
        }

        .bg-warning, .badge-warning {
            background-color: #FFB64D !important;
        }

        .bg-info, .badge-info {
            background-color: #4099ff !important;
        }

        .bg-success, .badge-success {
            background-color: #2ed8b6 !important;
        }

        .bg-danger, .badge-danger {
            background-color: #ff5370 !important;
        }

        .bg-dark, .badge-dark {
            background-color: #343a40 !important;
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
                                <h5 class="m-b-10">Payment Verification</h5>
                            </div>
                            <ul class="breadcrumb">
                                <li class="breadcrumb-item"><a href="index.html"><i class="feather icon-home"></i></a></li>
                            <li class="breadcrumb-item"><a href="#!">Fee Management</a></li>
                                <li class="breadcrumb-item"><a href="#!">Payment Verification</a></li>
                            </ul>
                        </div>
                    </div>
                </div>
            </div>
            <!-- [ breadcrumb ] end -->
            <!-- [ Main Content ] start -->
            <div class="row">
            <!-- Payment Verification Card -->
                <div class="col-xl-12 col-md-12">
                    <div class="card">
                        <div class="card-header">
                        <h5><i class="feather icon-check-circle mr-2"></i>Payment Verification</h5>
                        <span class="d-block m-t-5">Review and respond to payment verification requests from travelers</span>
                    </div>
                    <div class="card-body">
                        <div class="verification-examples">
                            <div class="card mb-4">
                                <div class="card-header bg-light">
                                    <h5 class="mb-0"><i class="feather icon-alert-circle mr-2"></i>Payment Verification Requests</h5>
                                </div>
                                <div class="card-body px-2 px-md-3"> <!-- Reduced padding on small screens -->
                                    <?php if (empty($verificationRequests)): ?>
                                    <div class="alert alert-info">
                                        <i class="feather icon-info mr-2"></i>
                                        No payment verification requests found.
                                    </div>
                                    <?php else: ?>
                                    <div class="row">
                                        <?php foreach ($verificationRequests as $request): ?>
                                        <div class="col-12 col-lg-6 mb-3">
                                            <div class="card h-100" data-request-id="<?php echo $request['request_id']; ?>">
                                                <div class="card-header bg-light">
                                                    <div class="d-flex justify-content-between align-items-center">
                                                        <h6 class="mb-0 text-truncate">
                                                            <span class="d-inline-block text-truncate" style="max-width: 150px;">
                                                                #<?php echo $request['request_id']; ?>: <?php echo getIssueTypeLabel($request['issue_type']); ?>
                                                            </span>
                                                        </h6>
                                                        <div>
                                                            <span class="badge <?php echo getPriorityBadgeClass($request['priority']); ?> mr-1">
                                                                <?php echo ucfirst($request['priority']); ?>
                                                            </span>
                                                            <span class="badge <?php echo getStatusBadgeClass($request['status']); ?>">
                                                                <?php echo ucfirst($request['status']); ?>
                                                            </span>
                                                        </div>
                                                    </div>
                                                </div>
                                                <div class="card-body">
                                                    <div class="row">
                                                        <div class="col-md-6">
                                                            <p class="mb-2"><strong>Traveler:</strong><br>
                                                                <span class="text-truncate d-inline-block" style="max-width: 100%;">
                                                                    <?php echo htmlspecialchars($request['first_name'] . ' ' . $request['last_name']); ?>
                                                                </span>
                                                            </p>
                                                            <?php if (!empty($request['booking_id'])): ?>
                                                            <p class="mb-2"><strong>Booking ID:</strong><br> <?php echo htmlspecialchars($request['booking_id']); ?></p>
                                                            <?php endif; ?>
                                                            <?php if (!empty($request['transaction_id'])): ?>
                                                            <p class="mb-2"><strong>Transaction ID:</strong><br> <?php echo htmlspecialchars($request['transaction_id']); ?></p>
                                                            <?php endif; ?>
                                                        </div>
                                                        <div class="col-md-6">
                                                            <p class="mb-2"><strong>Issue:</strong><br>
                                                                <span class="text-truncate d-inline-block" style="max-width: 100%;">
                                                                    <?php echo htmlspecialchars($request['issue_description']); ?>
                                                                </span>
                                                            </p>
                                                            <p class="mb-2"><strong>Action Required:</strong><br>
                                                                <span class="text-truncate d-inline-block" style="max-width: 100%;">
                                                                    <?php echo htmlspecialchars($request['action_required']); ?>
                                                                </span>
                                                            </p>
                                                        </div>
                                                    </div>
                                                    <div class="mt-3 d-flex justify-content-between">
                                                        <button class="btn btn-primary verification-btn" style="flex: 1; <?php echo $request['status'] !== 'resolved' ? 'margin-right: 10px;' : ''; ?>" onclick="checkTransaction('<?php echo $request['transaction_id']; ?>')">
                                                            <i class="feather icon-search mr-1"></i> <span class="d-none d-sm-inline">Check</span><span class="d-sm-none">Tx</span>
                                                        </button>
                                                        <?php if ($request['status'] !== 'resolved'): ?>
                                                        <button class="btn btn-secondary verification-btn" style="flex: 1;" onclick="updateRequestStatus(<?php echo $request['request_id']; ?>)">
                                                            <i class="feather icon-edit-2 mr-1"></i> <span class="d-none d-sm-inline">Update</span><span class="d-sm-none">Status</span>
                                                        </button>
                                                        <?php endif; ?>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                        <?php endforeach; ?>
                                    </div>
                                    <?php endif; ?>
                                </div>
                            </div>
                        </div>

                        <!-- Search Section -->
                        <div class="card mt-4">
                            <div class="card-header bg-light d-flex justify-content-between align-items-center">
                                <h5 class="mb-0"><i class="feather icon-search mr-2"></i>Search Transactions</h5>
                                <button class="btn btn-sm btn-outline-primary d-md-none" type="button" data-toggle="collapse" data-target="#searchFormCollapse" aria-expanded="false" aria-controls="searchFormCollapse">
                                    <i class="feather icon-filter mr-1"></i> Filters
                                </button>
                            </div>
                            <div class="card-body">
                                <div id="searchFormCollapse" class="collapse show">
                                    <form id="searchForm">
                                        <div class="row">
                                            <div class="col-md-4 col-sm-6">
                                                <div class="form-group">
                                                    <label for="transactionId">Transaction ID</label>
                                                    <input type="text" class="form-control" id="transactionId" placeholder="Enter transaction ID">
                                                </div>
                                            </div>
                                            <div class="col-md-4 col-sm-6">
                                                <div class="form-group">
                                                    <label for="travelerName">Traveler Name</label>
                                                    <input type="text" class="form-control" id="travelerName" placeholder="Enter traveler name">
                                                </div>
                                            </div>
                                            <div class="col-md-4 col-sm-6">
                                                <div class="form-group">
                                                    <label for="paymentMethod">Payment Method</label>
                                                    <select class="form-control" id="paymentMethod">
                                                        <option value="">All Methods</option>
                                                        <option value="credit_card">Credit Card</option>
                                                        <option value="paypal">PayPal</option>
                                                        <option value="bank_transfer">Bank Transfer</option>
                                                    </select>
                                                </div>
                                            </div>
                                            <div class="col-md-4 col-sm-6">
                                                <div class="form-group">
                                                    <label for="paymentStatus">Payment Status</label>
                                                    <select class="form-control" id="paymentStatus">
                                                        <option value="">All Statuses</option>
                                                        <option value="pending">Pending</option>
                                                        <option value="completed">Completed</option>
                                                        <option value="failed">Failed</option>
                                                    </select>
                                                </div>
                                            </div>
                                            <div class="col-md-4 col-sm-6">
                                                <div class="form-group">
                                                    <label for="cardNumber">Card Number (Last 4 digits)</label>
                                                    <input type="text" class="form-control" id="cardNumber" placeholder="Enter last 4 digits" maxlength="4">
                                                </div>
                                            </div>
                                            <div class="col-md-4 col-sm-6">
                                                <div class="form-group">
                                                    <label for="dateRange">Date Range</label>
                                                    <select class="form-control" id="dateRange">
                                                        <option value="">All Time</option>
                                                        <option value="today">Today</option>
                                                        <option value="week">This Week</option>
                                                        <option value="month">This Month</option>
                                                        <option value="quarter">This Quarter</option>
                                                        <option value="year">This Year</option>
                                                    </select>
                                                </div>
                                            </div>
                                        </div>
                                        <div class="d-flex justify-content-end mt-3">
                                            <button type="button" class="btn btn-secondary verification-btn mr-2" onclick="resetSearch()">
                                                <i class="feather icon-refresh-cw mr-1"></i>
                                                <span class="d-none d-sm-inline">Reset</span>
                                            </button>
                                            <button type="button" class="btn btn-primary verification-btn" onclick="searchTransactions()">
                                                <i class="feather icon-search mr-1"></i>
                                                <span class="d-none d-sm-inline">Search</span>
                                            </button>
                                        </div>
                                    </form>
                                </div>
                            </div>
                        </div>

                        <!-- Real Transactions Table -->
                        <div class="card mt-4">
                            <div class="card-header bg-light">
                                <h5 class="mb-0">Payment Transactions</h5>
                            </div>
                            <div class="card-body px-0 px-md-3"> <!-- Reduced padding on small screens -->
                                <div class="table-responsive">
                                    <table class="table table-hover table-striped">
                                        <thead class="thead-light">
                                            <tr>
                                                <th>Transaction ID</th>
                                                <th>Traveler</th>

                                                <th>Amount</th>
                                                <th class="d-none d-md-table-cell">Date</th> <!-- Hide on mobile -->
                                                <th class="d-none d-lg-table-cell">Payment Method</th> <!-- Hide on smaller screens -->
                                                <th>Status</th>
                                                <th>Actions</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            <?php if (empty($transactions)): ?>
                                            <tr>
                                                <td colspan="8" class="text-center">No transactions found</td>
                                            </tr>
                                            <?php else: ?>
                                            <?php foreach ($transactions as $transaction): ?>
                                            <tr>
                                                <td>
                                                    <span class="font-weight-bold">#TRX-<?php echo $transaction['transaction_id']; ?></span>
                                                    <!-- Mobile-only info -->
                                                    <div class="d-block d-md-none">
                                                        <small class="text-muted">
                                                            <?php echo (new DateTime($transaction['date'] ?? $transaction['transaction_date'] ?? date('Y-m-d')))->format('Y-m-d'); ?>
                                                        </small>
                                                    </div>
                                                </td>
                                                <td>
                                                    <?php echo htmlspecialchars($transaction['traveler_name']); ?>
                                                    <!-- Mobile-only payment method -->
                                                    <div class="d-block d-lg-none">
                                                        <small class="text-muted"><?php echo htmlspecialchars($transaction['payment_method']); ?></small>
                                                    </div>
                                                </td>

                                                <td><?php echo $transaction['currency'] . ' ' . number_format($transaction['amount'], 2); ?></td>
                                                <td class="d-none d-md-table-cell"><?php echo (new DateTime($transaction['date'] ?? $transaction['transaction_date'] ?? date('Y-m-d')))->format('Y-m-d'); ?></td>
                                                <td class="d-none d-lg-table-cell"><?php echo htmlspecialchars($transaction['payment_method']); ?></td>
                                                <td>
                                                    <?php
                                                    $badgeClass = 'badge-secondary';
                                                    switch ($transaction['status']) {
                                                        case 'completed':
                                                            $badgeClass = 'badge-success';
                                                            break;
                                                        case 'pending':
                                                            $badgeClass = 'badge-warning';
                                                            break;
                                                        case 'failed':
                                                            $badgeClass = 'badge-danger';
                                                            break;
                                                    }
                                                    ?>
                                                    <span class="badge <?php echo $badgeClass; ?>"><?php echo ucfirst($transaction['status']); ?></span>
                                                </td>
                                                <td>
                                                    <div class="btn-group btn-group-sm" role="group">
                                                        <button class="btn btn-info" onclick="viewTransaction(<?php echo $transaction['transaction_id']; ?>)">
                                                            <i class="feather icon-eye d-md-none"></i>
                                                            <span class="d-none d-md-inline">View</span>
                                                        </button>
                                                        <?php if ($transaction['status'] === 'pending'): ?>
                                                        <button class="btn btn-secondary" onclick="verifyPayment(<?php echo $transaction['transaction_id']; ?>)">
                                                            <i class="feather icon-check d-md-none"></i>
                                                            <span class="d-none d-md-inline">Verify</span>
                                                        </button>
                                                        <button class="btn btn-danger" onclick="markAsFailed(<?php echo $transaction['transaction_id']; ?>)">
                                                            <i class="feather icon-x d-md-none"></i>
                                                            <span class="d-none d-md-inline">Fail</span>
                                                        </button>
                                                        <?php elseif ($transaction['status'] === 'failed'): ?>
                                                        <button class="btn btn-warning" onclick="retryPayment(<?php echo $transaction['transaction_id']; ?>)">
                                                            <i class="feather icon-refresh-cw d-md-none"></i>
                                                            <span class="d-none d-md-inline">Retry</span>
                                                        </button>
                                                        <?php endif; ?>
                                                    </div>
                                                </td>
                                            </tr>
                                            <?php endforeach; ?>
                                            <?php endif; ?>
                                        </tbody>
                                    </table>
                                </div>
                                <div class="d-block d-md-none text-center mt-3 mb-2">
                                    <small class="text-muted">Swipe horizontally to see more details</small>
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
        // Initialize the page
        document.addEventListener('DOMContentLoaded', function() {
            // Load travelers for the dropdowns
            loadTravelers();
        });

        // Transaction Functions
        function checkTransaction(transactionId) {
            if (!transactionId) {
                alert('No transaction ID provided.');
                return;
            }

            // Clear all other search fields
            document.getElementById('travelerName').value = '';
            document.getElementById('paymentMethod').value = '';
            document.getElementById('paymentStatus').value = '';
            document.getElementById('cardNumber').value = '';
            document.getElementById('dateRange').value = '';

            // Set the transaction ID in the search form
            document.getElementById('transactionId').value = transactionId;

            // Scroll to the transactions table
            const transactionsTable = document.querySelector('.table-responsive');
            if (transactionsTable) {
                transactionsTable.scrollIntoView({ behavior: 'smooth', block: 'start' });
            }

            // Highlight the search form
            const searchForm = document.getElementById('searchForm');
            searchForm.classList.add('border', 'border-primary');
            setTimeout(() => {
                searchForm.classList.remove('border', 'border-primary');
            }, 2000);

            // Trigger the search
            searchTransactions();

            // Show a message to the user
            const tableBody = document.querySelector('.table tbody');
            const firstRow = tableBody.querySelector('tr');
            if (firstRow) {
                firstRow.classList.add('bg-light');
                setTimeout(() => {
                    firstRow.classList.remove('bg-light');
                }, 3000);
            }
        }



        function updateRequestStatus(requestId) {
            // First fetch the current request to get its status
            fetch(`payment-verification-actions.php?action=get&id=${requestId}`)
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    const request = data.request;
                    const currentStatus = request.status || 'new';

                    // Create a modal to update the request status
                    let modalHtml = `
                    <div class="modal fade" id="updateStatusModal" tabindex="-1" role="dialog" aria-labelledby="updateStatusModalLabel" aria-hidden="true">
                        <div class="modal-dialog" role="document">
                            <div class="modal-content">
                                <div class="modal-header">
                                    <h5 class="modal-title" id="updateStatusModalLabel">Update Request Status</h5>
                                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                                        <span aria-hidden="true">&times;</span>
                                    </button>
                                </div>
                                <div class="modal-body">
                                    <form id="updateStatusForm">
                                        <div class="form-group">
                                            <label for="requestStatus">Status</label>
                                            <select class="form-control" id="requestStatus" required>
                                                <option value="new" ${currentStatus === 'new' ? 'selected' : ''}>New</option>
                                                <option value="pending" ${currentStatus === 'pending' ? 'selected' : ''}>Pending</option>
                                                <option value="in_progress" ${currentStatus === 'in_progress' ? 'selected' : ''}>In Progress</option>
                                                <option value="resolved" ${currentStatus === 'resolved' ? 'selected' : ''}>Resolved</option>
                                                <option value="closed" ${currentStatus === 'closed' ? 'selected' : ''}>Closed</option>
                                            </select>
                                        </div>
                                        <div class="alert alert-info mt-3">
                                            <i class="feather icon-info mr-1"></i>
                                            <strong>Note:</strong> Setting status to "Resolved" will:
                                            <ul class="mb-0 mt-1">
                                                <li>Remove the "Update Status" button</li>
                                                <li>Mark any related transaction as "completed"</li>
                                            </ul>
                                        </div>
                                    </form>
                                </div>
                                <div class="modal-footer">
                                    <button type="button" class="btn btn-secondary" data-dismiss="modal">Cancel</button>
                                    <button type="button" class="btn btn-primary" onclick="saveRequestStatus(${requestId})">Save</button>
                                </div>
                            </div>
                        </div>
                    </div>`;

                    // Add modal to the page
                    const modalContainer = document.createElement('div');
                    modalContainer.innerHTML = modalHtml;
                    document.body.appendChild(modalContainer);

                    // Show the modal
                    $('#updateStatusModal').modal('show');

                    // Remove the modal from the DOM when it's closed
                    $('#updateStatusModal').on('hidden.bs.modal', function() {
                        document.body.removeChild(modalContainer);
                    });
                } else {
                    alert('Error: ' + data.message);
                }
            })
            .catch(error => {
                console.error('Error:', error);
                alert('An error occurred while fetching the request data.');
            });
        }

        function saveRequestStatus(requestId) {
            // Get form values
            const status = document.getElementById('requestStatus').value;

            // Create request data
            const requestData = {
                status: status
            };

            // Send data to backend
            fetch(`payment-verification-actions.php?action=update&id=${requestId}`, {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json'
                },
                body: JSON.stringify(requestData)
            })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    // If status is resolved, update any related transactions and remove the request card
                    if (status === 'resolved' && data.request && data.request.transaction_id) {
                        // Update the transaction status to completed if it's not already
                        fetch(`fee-transaction-actions.php?action=update-status&id=${data.request.transaction_id}&status=completed`, {
                            method: 'POST'
                        })
                        .then(response => response.json())
                        .then(transactionData => {
                            if (transactionData.success) {
                                // Close the modal
                                $('#updateStatusModal').modal('hide');

                                // Find and remove the request card from the UI
                                const requestCard = document.querySelector(`.card[data-request-id="${requestId}"]`);
                                if (requestCard) {
                                    const parentCol = requestCard.closest('.col-12');
                                    if (parentCol) {
                                        // Add fade-out animation
                                        parentCol.style.transition = 'opacity 0.5s ease';
                                        parentCol.style.opacity = '0';

                                        // Remove after animation completes
                                        setTimeout(() => {
                                            parentCol.remove();

                                            // Check if there are any requests left
                                            const remainingRequests = document.querySelectorAll('.verification-examples .col-12');
                                            if (remainingRequests.length === 0) {
                                                // If no requests left, show the "no requests" message
                                                const requestsContainer = document.querySelector('.verification-examples .row');
                                                if (requestsContainer) {
                                                    requestsContainer.innerHTML = `
                                                        <div class="col-12">
                                                            <div class="alert alert-info">
                                                                <i class="feather icon-info mr-2"></i>
                                                                No payment verification requests found.
                                                            </div>
                                                        </div>
                                                    `;
                                                }
                                            }
                                        }, 500);
                                    }
                                }

                                // Show success message
                                showNotification('success', 'Request resolved successfully and transaction marked as completed!');

                                // Refresh the transactions table
                                searchTransactions();
                            } else {
                                $('#updateStatusModal').modal('hide');
                                showNotification('warning', 'Request status updated to resolved but failed to update transaction: ' + transactionData.message);
                            }
                        })
                        .catch(error => {
                            console.error('Error updating transaction:', error);
                            $('#updateStatusModal').modal('hide');
                            showNotification('error', 'Request status updated to resolved but failed to update transaction.');
                        });
                    } else {
                        // For non-resolved statuses, just update the status badge
                        $('#updateStatusModal').modal('hide');

                        // Find and update the status badge
                        const requestCard = document.querySelector(`.card[data-request-id="${requestId}"]`);
                        if (requestCard) {
                            const statusBadge = requestCard.querySelector('.badge:last-child');
                            if (statusBadge) {
                                // Remove old status classes
                                statusBadge.classList.remove('bg-secondary', 'bg-warning', 'bg-info', 'bg-success', 'bg-dark');

                                // Add new status class
                                let badgeClass = 'bg-secondary';
                                switch (status) {
                                    case 'new': badgeClass = 'bg-secondary'; break;
                                    case 'pending': badgeClass = 'bg-warning'; break;
                                    case 'in_progress': badgeClass = 'bg-info'; break;
                                    case 'closed': badgeClass = 'bg-dark'; break;
                                }
                                statusBadge.classList.add(badgeClass);

                                // Update text
                                statusBadge.textContent = status.charAt(0).toUpperCase() + status.slice(1);
                            }
                        }

                        showNotification('success', 'Request status updated successfully!');
                    }
                } else {
                    showNotification('error', 'Error: ' + data.message);
                }
            })
            .catch(error => {
                console.error('Error:', error);
                showNotification('error', 'An error occurred while updating the request status.');
            });
        }

        // Helper function to show notifications
        function showNotification(type, message) {
            // Create notification element
            const notification = document.createElement('div');
            notification.className = `alert alert-${type === 'error' ? 'danger' : type} notification-toast`;
            notification.style.position = 'fixed';
            notification.style.top = '20px';
            notification.style.right = '20px';
            notification.style.zIndex = '9999';
            notification.style.minWidth = '300px';
            notification.style.boxShadow = '0 4px 8px rgba(0,0,0,0.1)';
            notification.style.transition = 'all 0.3s ease';
            notification.style.opacity = '0';
            notification.style.transform = 'translateY(-20px)';

            // Add icon based on type
            let icon = 'info';
            switch (type) {
                case 'success': icon = 'check-circle'; break;
                case 'warning': icon = 'alert-triangle'; break;
                case 'error': icon = 'alert-circle'; break;
            }

            notification.innerHTML = `
                <div class="d-flex align-items-center">
                    <i class="feather icon-${icon} mr-2"></i>
                    <span>${message}</span>
                </div>
                <button type="button" class="close" onclick="this.parentElement.remove()">
                    <span aria-hidden="true">&times;</span>
                </button>
            `;

            // Add to document
            document.body.appendChild(notification);

            // Trigger animation
            setTimeout(() => {
                notification.style.opacity = '1';
                notification.style.transform = 'translateY(0)';
            }, 10);

            // Auto-remove after 5 seconds
            setTimeout(() => {
                notification.style.opacity = '0';
                notification.style.transform = 'translateY(-20px)';
                setTimeout(() => notification.remove(), 300);
            }, 5000);
        }

        // Search Transactions
        function searchTransactions() {
            const transactionId = document.getElementById('transactionId').value;
            const travelerName = document.getElementById('travelerName').value;
            const paymentMethod = document.getElementById('paymentMethod').value;
            const paymentStatus = document.getElementById('paymentStatus').value;
            const cardNumber = document.getElementById('cardNumber').value;
            const dateRange = document.getElementById('dateRange').value;

            // Build query parameters
            const params = new URLSearchParams();
            if (transactionId) params.append('transaction_id', transactionId);
            if (travelerName) params.append('traveler_name', travelerName);
            if (paymentMethod) params.append('payment_method', paymentMethod);
            if (paymentStatus) params.append('status', paymentStatus);
            if (cardNumber) params.append('card_number', cardNumber);
            if (dateRange) params.append('date_range', dateRange);

            // Show loading indicator
            const tableBody = document.querySelector('.table tbody');
            tableBody.innerHTML = `
                <tr>
                    <td colspan="8" class="text-center py-4">
                        <div class="spinner-border text-primary" role="status">
                            <span class="sr-only">Loading...</span>
                        </div>
                        <p class="mt-2">Searching transactions...</p>
                    </td>
                </tr>
            `;

            // If no search parameters are provided, show all transactions
            if (params.toString() === '') {
                window.location.reload();
                return;
            }

            // Fetch filtered transactions
            fetch(`fee-transaction-actions.php?action=search&${params.toString()}`)
                .then(response => response.json())
                .then(data => {
                    if (data.success) {
                        if (data.transactions && data.transactions.length > 0) {
                            let transactionsHtml = '';

                            // Add a message showing the number of results
                            transactionsHtml += `
                                <tr>
                                    <td colspan="8" class="bg-light text-center py-2">
                                        <div class="alert alert-success mb-0">
                                            <i class="feather icon-check-circle mr-2"></i>
                                            Found ${data.transactions.length} transaction(s) matching your search criteria.
                                        </div>
                                    </td>
                                </tr>
                            `;

                            data.transactions.forEach(transaction => {
                                const badgeClass = getStatusBadgeClass(transaction.status);

                                // Format the transaction data
                                const formattedAmount = parseFloat(transaction.amount).toFixed(2);
                                const formattedDate = new Date(transaction.date || transaction.transaction_date || new Date()).toLocaleDateString();
                                const currency = transaction.currency || 'USD';

                                transactionsHtml += `
                                    <tr>
                                        <td>#TRX-${transaction.transaction_id}</td>
                                        <td>${transaction.traveler_name || 'Unknown'}</td>
                                        <td>${currency} ${formattedAmount}</td>
                                        <td>${formattedDate}</td>
                                        <td>${transaction.payment_method || 'N/A'}</td>
                                        <td><span class="badge ${badgeClass}">${transaction.status.toUpperCase()}</span></td>
                                        <td>
                                            <button class="btn btn-info btn-sm verification-btn mb-1" onclick="viewTransaction(${transaction.transaction_id})">
                                                <i class="feather icon-eye mr-1"></i> View
                                            </button>
                                            ${transaction.status === 'pending' ? `
                                            <button class="btn btn-success btn-sm verification-btn mb-1" onclick="verifyPayment(${transaction.transaction_id})">
                                                <i class="feather icon-check-circle mr-1"></i> Verify
                                            </button>
                                            <button class="btn btn-danger btn-sm verification-btn mb-1" onclick="markAsFailed(${transaction.transaction_id})">
                                                <i class="feather icon-x-circle mr-1"></i> Fail
                                            </button>
                                            ` : ''}
                                            ${transaction.status === 'failed' ? `
                                            <button class="btn btn-warning btn-sm verification-btn mb-1" onclick="retryPayment(${transaction.transaction_id})">
                                                <i class="feather icon-refresh-cw mr-1"></i> Retry
                                            </button>
                                            ` : ''}
                                        </td>
                                    </tr>
                                `;
                            });

                            tableBody.innerHTML = transactionsHtml;
                        } else {
                            tableBody.innerHTML = `
                                <tr>
                                    <td colspan="8" class="text-center py-4">
                                        <div class="alert alert-info mb-0">
                                            <i class="feather icon-info mr-2"></i>
                                            No transactions found matching your search criteria.
                                            <div class="mt-3">
                                                <button class="btn btn-outline-primary btn-sm verification-btn" onclick="resetSearch()">
                                                    <i class="feather icon-refresh-cw mr-1"></i> Show All Transactions
                                                </button>
                                            </div>
                                        </div>
                                    </td>
                                </tr>
                            `;
                        }
                    } else {
                        tableBody.innerHTML = `
                            <tr>
                                <td colspan="8" class="text-center py-4">
                                    <div class="alert alert-danger mb-0">
                                        <i class="feather icon-alert-circle mr-2"></i>
                                        Error: ${data.message}
                                        <div class="mt-3">
                                            <button class="btn btn-outline-primary btn-sm verification-btn" onclick="resetSearch()">
                                                <i class="feather icon-refresh-cw mr-1"></i> Reset Search
                                            </button>
                                        </div>
                                    </div>
                                </td>
                            </tr>
                        `;
                    }
                })
                .catch(error => {
                    console.error('Error:', error);
                    tableBody.innerHTML = `
                        <tr>
                            <td colspan="8" class="text-center py-4">
                                <div class="alert alert-danger mb-0">
                                    <i class="feather icon-alert-circle mr-2"></i>
                                    An error occurred while searching transactions.
                                    <div class="mt-3">
                                        <button class="btn btn-outline-primary btn-sm verification-btn" onclick="resetSearch()">
                                            <i class="feather icon-refresh-cw mr-1"></i> Reset Search
                                        </button>
                                    </div>
                                </div>
                            </td>
                        </tr>
                    `;
                });
        }

        // Reset search form
        function resetSearch() {
            document.getElementById('searchForm').reset();

            // Reload all transactions
            window.location.reload();
        }

        // Transaction Management Functions
        function viewTransaction(id) {
            // Fetch transaction data from backend
            fetch(`fee-transaction-actions.php?action=get&id=${id}`)
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    const transaction = data.transaction;

                    // Create a modal to display transaction details
                    let modalHtml = `
                    <div class="modal fade" id="transactionModal" tabindex="-1" role="dialog" aria-labelledby="transactionModalLabel" aria-hidden="true">
                        <div class="modal-dialog modal-lg" role="document">
                            <div class="modal-content">
                                <div class="modal-header">
                                    <h5 class="modal-title" id="transactionModalLabel">Transaction #TRX-${transaction.transaction_id}</h5>
                                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                                        <span aria-hidden="true">&times;</span>
                                    </button>
                                </div>
                                <div class="modal-body">
                                    <div class="row">
                                        <div class="col-md-6">
                                            <div class="card mb-4">
                                                <div class="card-header bg-primary text-white">
                                                    <h6 class="mb-0">Transaction Details</h6>
                                                </div>
                                                <div class="card-body">
                                                    <p><strong>Traveler:</strong> ${transaction.traveler_name}</p>
                                                    <p><strong>Amount:</strong> ${transaction.currency || 'USD'} ${parseFloat(transaction.amount).toFixed(2)}</p>
                                                    <p><strong>Status:</strong> <span class="badge ${getStatusBadgeClass(transaction.status)}">${transaction.status}</span></p>
                                                    <p><strong>Transaction Date:</strong> ${new Date(transaction.date || transaction.transaction_date || new Date()).toLocaleDateString()}</p>
                                                    <p><strong>Payment Method:</strong> ${transaction.payment_method || 'N/A'}</p>
                                                    <p><strong>Description:</strong> ${transaction.description || 'No description available'}</p>
                                                </div>
                                            </div>
                                        </div>
                                        <div class="col-md-6">
                                            <div class="card mb-4">
                                                <div class="card-header bg-info text-white">
                                                    <h6 class="mb-0">Payment Card Information</h6>
                                                </div>
                                                <div class="card-body" id="cardDetails-${transaction.transaction_id}">
                                                    <div class="text-center py-3">
                                                        <div class="spinner-border text-primary" role="status">
                                                            <span class="sr-only">Loading...</span>
                                                        </div>
                                                        <p class="mt-2 mb-0">Loading card details...</p>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>

                                    <div class="card">
                                        <div class="card-header bg-light">
                                            <h6 class="mb-0">Payment Verification</h6>
                                        </div>
                                        <div class="card-body">
                                            <div class="row">
                                                <div class="col-md-6">
                                                    <h6>Current Status</h6>
                                                    <div class="alert ${transaction.status === 'completed' ? 'alert-success' : transaction.status === 'pending' ? 'alert-warning' : 'alert-danger'}">
                                                        <i class="feather ${transaction.status === 'completed' ? 'icon-check-circle' : transaction.status === 'pending' ? 'icon-clock' : 'icon-x-circle'} mr-2"></i>
                                                        This payment is currently <strong>${transaction.status.toUpperCase()}</strong>
                                                    </div>
                                                </div>
                                                <div class="col-md-6">
                                                    <h6>Actions</h6>
                                                    <div class="d-flex flex-wrap">
                                                        ${transaction.status === 'pending' ?
                                                        `<button class="btn btn-success verification-btn mr-2 mb-2" onclick="verifyPayment(${transaction.transaction_id})">
                                                            <i class="feather icon-check-circle mr-1"></i> Verify Payment
                                                        </button>
                                                        <button class="btn btn-danger verification-btn mb-2" onclick="markAsFailed(${transaction.transaction_id})">
                                                            <i class="feather icon-x-circle mr-1"></i> Mark as Failed
                                                        </button>` : ''}
                                                        ${transaction.status === 'failed' ?
                                                        `<button class="btn btn-warning verification-btn mb-2" onclick="retryPayment(${transaction.transaction_id})">
                                                            <i class="feather icon-refresh-cw mr-1"></i> Retry Payment
                                                        </button>` : ''}
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                <div class="modal-footer">
                                    <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
                                </div>
                            </div>
                        </div>
                    </div>`;

                    // Add modal to the page
                    const modalContainer = document.createElement('div');
                    modalContainer.innerHTML = modalHtml;
                    document.body.appendChild(modalContainer);

                    // Show the modal
                    $('#transactionModal').modal('show');

                    // Load the card details
                    loadCardDetailsForTransaction(transaction);

                    // Remove the modal from the DOM when it's closed
                    $('#transactionModal').on('hidden.bs.modal', function() {
                        document.body.removeChild(modalContainer);
                    });
                } else {
                    alert('Error: ' + data.message);
                }
            })
            .catch(error => {
                console.error('Error:', error);
                alert('An error occurred while fetching the transaction data.');
            });
        }

        function getStatusBadgeClass(status) {
            switch (status) {
                case 'completed':
                    return 'badge-success';
                case 'pending':
                    return 'badge-warning';
                case 'failed':
                    return 'badge-danger';
                default:
                    return 'badge-secondary';
            }
        }

        function verifyPayment(id) {
            if (confirm('Are you sure you want to mark this payment as verified? This will change the status from "pending" to "completed".')) {
                fetch(`fee-transaction-actions.php?action=update-status&id=${id}&status=completed`, {
                    method: 'POST'
                })
                .then(response => response.json())
                .then(data => {
                    if (data.success) {
                        alert('Payment verified successfully! Status changed to "completed".');
                        window.location.reload();
                    } else {
                        alert('Error: ' + data.message);
                    }
                })
                .catch(error => {
                    console.error('Error:', error);
                    alert('An error occurred while verifying the payment.');
                });
            }
        }

        function markAsFailed(id) {
            if (confirm('Are you sure you want to mark this payment as failed? This will change the status to "failed".')) {
                fetch(`fee-transaction-actions.php?action=update-status&id=${id}&status=failed`, {
                    method: 'POST'
                })
                .then(response => response.json())
                .then(data => {
                    if (data.success) {
                        alert('Payment marked as failed. Status changed to "failed".');
                        window.location.reload();
                    } else {
                        alert('Error: ' + data.message);
                    }
                })
                .catch(error => {
                    console.error('Error:', error);
                    alert('An error occurred while updating the payment status.');
                });
            }
        }

        function retryPayment(id) {
            if (confirm('Are you sure you want to retry this payment? This will change the status from "failed" to "pending".')) {
                fetch(`fee-transaction-actions.php?action=update-status&id=${id}&status=pending`, {
                    method: 'POST'
                })
                .then(response => response.json())
                .then(data => {
                    if (data.success) {
                        alert('Payment status updated to "pending". Please process the payment manually.');
                        window.location.reload();
                    } else {
                        alert('Error: ' + data.message);
                    }
                })
                .catch(error => {
                    console.error('Error:', error);
                    alert('An error occurred while updating the payment status.');
                });
            }
        }

        // Card Management Functions
        function loadCardDetailsForTransaction(transaction) {
            const cardDetailsContainer = document.getElementById(`cardDetails-${transaction.transaction_id}`);

            // First try to extract card ID from transaction reference if available
            // Format is "CARD-{cardId}-{transactionReference}"
            let cardId = null;

            if (transaction.transaction_reference && transaction.transaction_reference.includes('CARD-')) {
                const parts = transaction.transaction_reference.split('-');
                if (parts.length >= 3) {
                    cardId = parseInt(parts[1]);
                }
            }

            // If card ID found in reference, fetch that specific card
            if (cardId) {
                fetchAndDisplayCardById(cardId, transaction, cardDetailsContainer);
            } else {
                // Otherwise, try to find the most recent card for this traveler
                fetchAndDisplayCardByTravelerId(transaction.traveler_id, transaction, cardDetailsContainer);
            }
        }

        function fetchAndDisplayCardById(cardId, transaction, cardDetailsContainer) {
            // Get card details
            fetch(`card-actions.php?action=get&id=${cardId}`)
                .then(response => response.json())
                .then(data => {
                    if (data.success) {
                        displayCardDetails(data.card, transaction, cardDetailsContainer);
                    } else {
                        // If specific card not found, try to find any card for this traveler
                        fetchAndDisplayCardByTravelerId(transaction.traveler_id, transaction, cardDetailsContainer);
                    }
                })
                .catch(error => {
                    console.error('Error:', error);
                    showCardNotFoundMessage(transaction, cardDetailsContainer);
                });
        }

        function fetchAndDisplayCardByTravelerId(travelerId, transaction, cardDetailsContainer) {
            // Get cards for this traveler
            fetch(`card-actions.php?action=get-by-traveler&traveler_id=${travelerId}`)
                .then(response => response.json())
                .then(data => {
                    if (data.success && data.cards && data.cards.length > 0) {
                        // Use the most recent card (first in the array)
                        displayCardDetails(data.cards[0], transaction, cardDetailsContainer);
                    } else {
                        showCardNotFoundMessage(transaction, cardDetailsContainer);
                    }
                })
                .catch(error => {
                    console.error('Error:', error);
                    showCardNotFoundMessage(transaction, cardDetailsContainer);
                });
        }

        function showCardNotFoundMessage(transaction, cardDetailsContainer) {
            cardDetailsContainer.innerHTML = `
                <div class="card mb-3">
                    <div class="card-header bg-secondary text-white">
                        <div class="d-flex justify-content-between align-items-center">
                            <h5 class="m-0">Credit Card</h5>
                            <div>**** **** **** ****</div>
                        </div>
                    </div>
                    <div class="card-body">
                        <div class="alert alert-warning mb-0">
                            <i class="feather icon-alert-circle mr-2"></i>
                            No card information found for this traveler.
                        </div>
                    </div>
                </div>
            `;
        }

        function displayCardDetails(card, transaction, cardDetailsContainer) {
            // Determine card background color based on card type
            let cardBgClass = 'bg-secondary';
            if (card.card_type) {
                switch(card.card_type.toLowerCase()) {
                    case 'visa':
                        cardBgClass = 'bg-primary';
                        break;
                    case 'mastercard':
                        cardBgClass = 'bg-danger';
                        break;
                    case 'american express':
                        cardBgClass = 'bg-info';
                        break;
                    case 'discover':
                        cardBgClass = 'bg-warning';
                        break;
                }
            }

            // Create card details HTML
            let cardHtml = `
                <div class="card mb-3">
                    <div class="card-header ${cardBgClass} text-white">
                        <div class="d-flex justify-content-between align-items-center">
                            <h5 class="m-0">${card.card_type || 'Credit Card'}</h5>
                            <div>${card.masked_card_number || '****'}</div>
                        </div>
                    </div>
                    <div class="card-body">
                        <div class="row">
                            <div class="col-md-6">
                                <p><strong>Card Holder:</strong><br>${card.card_holder_name || 'N/A'}</p>
                                <p><strong>Expiry Date:</strong><br>${card.formatted_expiry_date || 'N/A'}</p>
                            </div>
                            <div class="col-md-6">
                                <p><strong>Billing Address:</strong><br>${card.billing_address || 'N/A'}</p>
                            </div>
                        </div>
                    </div>
                </div>
            `;

            // Update the container
            cardDetailsContainer.innerHTML = cardHtml;
        }

        // Load travelers for dropdown
        function loadTravelers() {
            fetch('user-actions.php?action=get-travelers')
                .then(response => response.json())
                .then(data => {
                    if (data.success) {
                        // Travelers loaded successfully
                    } else {
                        console.error('Error loading travelers:', data.message);
                    }
                })
                .catch(error => {
                    console.error('Error:', error);
                });
        }



        // Load cards for a specific traveler
        function loadTravelerCards() {
            const travelerId = document.getElementById('selectTraveler').value;
            const container = document.getElementById('travelerCardsContainer');

            if (!travelerId) {
                container.innerHTML = `
                    <div class="text-center py-4">
                        <p class="text-muted">Select a traveler to view their cards</p>
                    </div>
                `;
                return;
            }

            // Show loading indicator
            container.innerHTML = `
                <div class="text-center py-4">
                    <div class="spinner-border text-primary" role="status">
                        <span class="sr-only">Loading...</span>
                    </div>
                    <p class="mt-2">Loading cards...</p>
                </div>
            `;

            // Fetch cards for the traveler
            fetch(`card-actions.php?action=get-by-traveler&traveler_id=${travelerId}`)
                .then(response => response.json())
                .then(data => {
                    if (data.success) {
                        if (data.cards && data.cards.length > 0) {
                            let cardsHtml = `
                                <div class="table-responsive">
                                    <table class="table table-hover">
                                        <thead>
                                            <tr>
                                                <th>Card Type</th>
                                                <th>Card Number</th>
                                                <th>Card Holder</th>
                                                <th>Expiry Date</th>
                                                <th>Actions</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                            `;

                            data.cards.forEach(card => {
                                cardsHtml += `
                                    <tr>
                                        <td>${card.card_type || 'Unknown'}</td>
                                        <td>${card.masked_card_number || '****'}</td>
                                        <td>${card.card_holder_name}</td>
                                        <td>${card.formatted_expiry_date}</td>
                                        <td>
                                            <button class="btn btn-info btn-sm" onclick="viewCardDetails(${card.card_id})">View</button>
                                            <button class="btn btn-danger btn-sm" onclick="deleteCard(${card.card_id})">Delete</button>
                                        </td>
                                    </tr>
                                `;
                            });

                            cardsHtml += `
                                        </tbody>
                                    </table>
                                </div>
                            `;

                            container.innerHTML = cardsHtml;
                        } else {
                            container.innerHTML = `
                                <div class="alert alert-info">
                                    <i class="feather icon-info mr-2"></i>
                                    No cards found for this traveler.
                                </div>
                                <div class="text-center">
                                    <button class="btn btn-primary" onclick="addCardForTraveler(${travelerId})">
                                        <i class="feather icon-plus mr-1"></i> Add Card for this Traveler
                                    </button>
                                </div>
                            `;
                        }
                    } else {
                        container.innerHTML = `
                            <div class="alert alert-danger">
                                <i class="feather icon-alert-circle mr-2"></i>
                                Error: ${data.message}
                            </div>
                        `;
                    }
                })
                .catch(error => {
                    console.error('Error:', error);
                    container.innerHTML = `
                        <div class="alert alert-danger">
                            <i class="feather icon-alert-circle mr-2"></i>
                            An error occurred while loading cards.
                        </div>
                    `;
                });
        }

        // Add card for a specific traveler
        function addCardForTraveler(travelerId) {
            // Reset the form
            document.getElementById('cardForm').reset();

            // Set the traveler ID
            document.getElementById('cardTravelerId').value = travelerId;

            // Close the manage cards modal
            $('#manageCardsModal').modal('hide');

            // Show the add card modal
            setTimeout(() => {
                $('#addCardModal').modal('show');
            }, 500);
        }

        // View card details
        function viewCardDetails(cardId) {
            fetch(`card-actions.php?action=get&id=${cardId}`)
                .then(response => response.json())
                .then(data => {
                    if (data.success) {
                        const card = data.card;

                        // Create a modal to display card details
                        let modalHtml = `
                        <div class="modal fade" id="cardDetailsModal" tabindex="-1" role="dialog" aria-labelledby="cardDetailsModalLabel" aria-hidden="true">
                            <div class="modal-dialog" role="document">
                                <div class="modal-content">
                                    <div class="modal-header">
                                        <h5 class="modal-title" id="cardDetailsModalLabel">Card Details</h5>
                                        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                                            <span aria-hidden="true">&times;</span>
                                        </button>
                                    </div>
                                    <div class="modal-body">
                                        <div class="card mb-3">
                                            <div class="card-header bg-${getCardBgClass(card.card_type)} text-white">
                                                <div class="d-flex justify-content-between align-items-center">
                                                    <h5 class="m-0">${card.card_type || 'Credit Card'}</h5>
                                                    <div>${card.masked_card_number || '****'}</div>
                                                </div>
                                            </div>
                                            <div class="card-body">
                                                <div class="row">
                                                    <div class="col-md-6">
                                                        <p><strong>Card Holder:</strong><br>${card.card_holder_name || 'N/A'}</p>
                                                        <p><strong>Expiry Date:</strong><br>${card.formatted_expiry_date || 'N/A'}</p>
                                                    </div>
                                                    <div class="col-md-6">
                                                        <p><strong>Billing Address:</strong><br>${card.billing_address || 'N/A'}</p>
                                                        <p><strong>Added On:</strong><br>${new Date(card.created_at).toLocaleString()}</p>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>

                                        <h6 class="mt-4 mb-3">Recent Transactions</h6>
                                        <div id="cardTransactions">
                                            <div class="text-center py-3">
                                                <div class="spinner-border text-primary" role="status">
                                                    <span class="sr-only">Loading...</span>
                                                </div>
                                                <p class="mt-2 mb-0">Loading transactions...</p>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="modal-footer">
                                        <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
                                    </div>
                                </div>
                            </div>
                        </div>`;

                        // Add modal to the page
                        const modalContainer = document.createElement('div');
                        modalContainer.innerHTML = modalHtml;
                        document.body.appendChild(modalContainer);

                        // Show the modal
                        $('#cardDetailsModal').modal('show');

                        // Load transactions for this card
                        loadCardTransactions(cardId);

                        // Remove the modal from the DOM when it's closed
                        $('#cardDetailsModal').on('hidden.bs.modal', function() {
                            document.body.removeChild(modalContainer);
                        });
                    } else {
                        alert('Error: ' + data.message);
                    }
                })
                .catch(error => {
                    console.error('Error:', error);
                    alert('An error occurred while fetching the card details.');
                });
        }

        // Load transactions for a specific card
        function loadCardTransactions(cardId) {
            fetch(`fee-transaction-actions.php?action=get-by-card&card_id=${cardId}`)
                .then(response => response.json())
                .then(data => {
                    const container = document.getElementById('cardTransactions');

                    if (data.success) {
                        if (data.transactions && data.transactions.length > 0) {
                            let transactionsHtml = `
                                <div class="table-responsive">
                                    <table class="table table-sm">
                                        <thead>
                                            <tr>
                                                <th>ID</th>
                                                <th>Date</th>
                                                <th>Amount</th>
                                                <th>Status</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                            `;

                            data.transactions.forEach(transaction => {
                                transactionsHtml += `
                                    <tr>
                                        <td>#TRX-${transaction.transaction_id}</td>
                                        <td>${new Date(transaction.date || transaction.transaction_date).toLocaleDateString()}</td>
                                        <td>${transaction.currency} ${parseFloat(transaction.amount).toFixed(2)}</td>
                                        <td><span class="badge ${getStatusBadgeClass(transaction.status)}">${transaction.status}</span></td>
                                    </tr>
                                `;
                            });

                            transactionsHtml += `
                                        </tbody>
                                    </table>
                                </div>
                            `;

                            container.innerHTML = transactionsHtml;
                        } else {
                            container.innerHTML = `
                                <div class="alert alert-info">
                                    <i class="feather icon-info mr-2"></i>
                                    No transactions found for this card.
                                </div>
                            `;
                        }
                    } else {
                        container.innerHTML = `
                            <div class="alert alert-warning">
                                <i class="feather icon-alert-triangle mr-2"></i>
                                ${data.message}
                            </div>
                        `;
                    }
                })
                .catch(error => {
                    console.error('Error:', error);
                    const container = document.getElementById('cardTransactions');
                    container.innerHTML = `
                        <div class="alert alert-danger">
                            <i class="feather icon-alert-circle mr-2"></i>
                            An error occurred while loading transactions.
                        </div>
                    `;
                });
        }

        // Delete a card
        function deleteCard(cardId) {
            if (confirm('Are you sure you want to delete this card? This action cannot be undone.')) {
                fetch(`card-actions.php?action=delete&id=${cardId}`, {
                    method: 'POST'
                })
                .then(response => response.json())
                .then(data => {
                    if (data.success) {
                        alert('Card deleted successfully!');
                        loadTravelerCards(); // Reload the cards list
                    } else {
                        alert('Error: ' + data.message);
                    }
                })
                .catch(error => {
                    console.error('Error:', error);
                    alert('An error occurred while deleting the card.');
                });
            }
        }

        // Get background class for card type
        function getCardBgClass(cardType) {
            if (!cardType) return 'secondary';

            switch(cardType.toLowerCase()) {
                case 'visa':
                    return 'primary';
                case 'mastercard':
                    return 'danger';
                case 'american express':
                    return 'info';
                case 'discover':
                    return 'warning';
                default:
                    return 'secondary';
            }
        }

        // Save a new card
        function saveCard() {
            // Get form values
            const travelerId = document.getElementById('cardTravelerId').value;
            const cardNumber = document.getElementById('cardNumber').value;
            const expiryDate = document.getElementById('expiryDate').value;
            const cvv = document.getElementById('cvv').value;
            const cardHolderName = document.getElementById('cardHolderName').value;
            const cardType = document.getElementById('cardType').value;
            const billingAddress = document.getElementById('billingAddress').value;

            // Validate form
            if (!travelerId || !cardNumber || !expiryDate || !cvv || !cardHolderName || !cardType) {
                alert('Please fill in all required fields');
                return;
            }

            // Format expiry date for database (YYYY-MM-DD)
            const formattedExpiryDate = expiryDate + '-01';

            // Create card data
            const cardData = {
                traveler_id: parseInt(travelerId),
                card_number: cardNumber,
                expiry_date: formattedExpiryDate,
                cvv: cvv,
                card_holder_name: cardHolderName,
                card_type: cardType,
                billing_address: billingAddress
            };

            // Send data to backend
            fetch('card-actions.php?action=create', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json'
                },
                body: JSON.stringify(cardData)
            })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    // Check if this card is for a specific transaction
                    const transactionId = document.getElementById('cardTransactionId').value;

                    if (transactionId) {
                        // Associate the card with the transaction
                        associateCardWithTransaction(data.card_id, transactionId)
                            .then(() => {
                                alert('Card added and associated with transaction successfully!');
                                $('#addCardModal').modal('hide');

                                // Reload the transaction details
                                viewTransaction(transactionId);
                            })
                            .catch(error => {
                                console.error('Error:', error);
                                alert('Card added but could not be associated with the transaction.');
                                $('#addCardModal').modal('hide');
                            });
                    } else {
                        alert('Card added successfully!');
                        $('#addCardModal').modal('hide');

                        // If the manage cards modal was open, reload the cards
                        if ($('#manageCardsModal').hasClass('show')) {
                            loadTravelerCards();
                            $('#manageCardsModal').modal('show');
                        }
                    }
                } else {
                    alert('Error: ' + data.message);
                }
            })
            .catch(error => {
                console.error('Error:', error);
                alert('An error occurred while saving the card.');
            });
        }

        // Function to associate a card with a transaction
        function associateCardWithTransaction(cardId, transactionId) {
            return new Promise((resolve, reject) => {
                // Get the transaction first
                fetch(`fee-transaction-actions.php?action=get&id=${transactionId}`)
                    .then(response => response.json())
                    .then(data => {
                        if (data.success) {
                            const transaction = data.transaction;

                            // Create a new transaction reference that includes the card ID
                            const newReference = `CARD-${cardId}-TRX-${transactionId}`;

                            // Update the transaction reference
                            fetch(`fee-transaction-actions.php?action=update-reference&id=${transactionId}`, {
                                method: 'POST',
                                headers: {
                                    'Content-Type': 'application/json'
                                },
                                body: JSON.stringify({
                                    transaction_reference: newReference
                                })
                            })
                            .then(response => response.json())
                            .then(data => {
                                if (data.success) {
                                    resolve();
                                } else {
                                    reject(new Error(data.message));
                                }
                            })
                            .catch(error => {
                                reject(error);
                            });
                        } else {
                            reject(new Error(data.message));
                        }
                    })
                    .catch(error => {
                        reject(error);
                    });
            });
        }
    </script>
</body>

</html>
