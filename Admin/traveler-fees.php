<?php
// Start the session at the very beginning of the file
session_start();

// Check if user is logged in and is an admin
if (!isset($_SESSION['userID']) || $_SESSION['userType'] !== 'admin') {
    header("Location: ../Common/login.php");
    exit;
}

// Load required controllers
require_once '../Controllers/FeeTransactionController.php';
require_once '../Controllers/CardController.php';
require_once '../Controllers/FeeController.php';

// Instantiate the controllers
$feeTransactionController = new FeeTransactionController();
$cardController = new CardController();
$feeController = new FeeController();

// Get all transactions
$transactions = $feeTransactionController->getAllTransactions();

// Get all fees
$fees = $feeController->getAllFees();
?>
<!DOCTYPE html>
<html lang="en">

<head>
    <title>Traveler Fees - HomeStay Admin</title>
    <style>
        /* Custom styles for Traveler Fees */
        .fee-card {
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

        /* Make all badge text white */
        .badge {
            color: white !important;
            font-weight: 500;
            padding: 5px 10px;
            border-radius: 4px;
        }

        /* Action button styling */
        .btn-group-sm .btn {
            min-width: 40px !important;
            max-width: 40px !important;
            text-align: center;
            display: inline-block;
            box-sizing: border-box;
            white-space: nowrap;
            overflow: hidden;
            text-overflow: ellipsis;
            padding: 0.2rem 0.1rem;
            font-size: 0.65rem;
            margin-right: 2px;
            border-radius: 3px;
        }

        .btn-group-sm .btn:last-child {
            margin-right: 0;
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

        /* Fee card styling */
        .fee-card {
            margin-bottom: 1.5rem;
            border-radius: 0.5rem;
            box-shadow: 0 2px 6px rgba(0, 0, 0, 0.08);
            transition: all 0.3s ease;
        }

        .fee-card:hover {
            box-shadow: 0 4px 12px rgba(0, 0, 0, 0.12);
            transform: translateY(-2px);
        }

        .fee-card .card-header {
            padding: 1.25rem;
            background-color: #f8f9fa;
            border-bottom: 1px solid rgba(0, 0, 0, 0.05);
        }

        .fee-card .card-body {
            padding: 1.5rem;
        }

        .fee-card .badge {
            font-size: 0.75rem;
            padding: 0.35em 0.65em;
            margin-right: 0.5rem;
            font-weight: 600;
        }

        .fee-card .badge-light {
            background-color: #f1f3f5;
            color: #495057;
        }

        .fee-card .badge-success {
            background-color: #28a745;
            color: #fff;
            font-weight: 600;
        }

        .fee-card .badge-warning {
            background-color: #ffc107;
            color: #212529;
            font-weight: 600;
        }

        .fee-card .badge-danger {
            background-color: #dc3545;
            color: #fff;
            font-weight: 600;
        }

        .fee-card .mt-3 {
            margin-top: 1.5rem !important;
        }

        .fee-card .text-right {
            margin-top: 1.5rem;
        }

        .fee-card .btn-sm {
            padding: 0.25rem 0.75rem;
            font-size: 0.875rem;
            border-radius: 0.25rem;
            margin-left: 0.5rem;
            width: 80px;
            text-align: center;
            display: inline-block;
            box-sizing: border-box;
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
            background-color: #4dabf7;
            border-color: #4dabf7;
        }

        .btn-primary:hover {
            background-color: #3c99e6;
            border-color: #3c99e6;
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

        /* Make form smaller */
        .fee-form-card {
            max-width: 800px;
            margin: 0 auto 2rem auto;
        }

        .fee-form-card .card-body {
            padding: 1.25rem;
        }

        .fee-form-card .form-group {
            margin-bottom: 1rem;
        }

        .fee-form-card .form-control {
            padding: 0.5rem 0.75rem;
        }

        .fee-form-card .btn {
            padding: 0.375rem 1rem;
        }

        /* Responsive styles */
        @media (max-width: 768px) {
            .fee-card .card-header,
            .fee-form-card .card-header {
                padding: 1rem;
            }

            .fee-card .card-body,
            .fee-form-card .card-body {
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
            .fee-card .card-header,
            .fee-form-card .card-header {
                padding: 0.75rem;
            }

            .fee-card .card-body,
            .fee-form-card .card-body {
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

            .fee-card .btn-sm,
            .fee-form-card .btn-sm {
                padding: 0.2rem 0.5rem;
                font-size: 0.75rem;
            }
        }

        .btn-info {
            background-color: #28a745;
            border-color: #28a745;
            color: #fff;
        }

        .btn-info:hover {
            background-color: #218838;
            border-color: #1e7e34;
            color: #fff;
        }

        /* Make all action buttons the same width */
        .btn-sm {
            min-width: 40px !important;
            max-width: 40px !important;
            text-align: center;
            display: inline-block;
            box-sizing: border-box;
            white-space: nowrap;
            overflow: hidden;
            text-overflow: ellipsis;
            padding: 0.2rem 0.1rem;
            font-size: 0.65rem;
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
        .table th:nth-child(3), .table td:nth-child(3) { width: 12%; } /* Fee Type */
        .table th:nth-child(4), .table td:nth-child(4) { width: 8%; } /* Amount */
        .table th:nth-child(5), .table td:nth-child(5) { width: 8%; } /* Date */
        .table th:nth-child(6), .table td:nth-child(6) { width: 10%; } /* Payment Method */
        .table th:nth-child(7), .table td:nth-child(7) { width: 10%; } /* Status */
        .table th:nth-child(8), .table td:nth-child(8) { width: 30%; } /* Actions */

        /* Ensure text doesn't overflow */
        .table td {
            white-space: nowrap;
            overflow: hidden;
            text-overflow: ellipsis;
        }

        /* Specific style for the Fail button */
        .btn-danger.btn-sm {
            min-width: 40px !important;
            max-width: 40px !important;
            padding: 0.2rem 0.1rem;
            white-space: nowrap;
            overflow: hidden;
            text-overflow: ellipsis;
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
                            <h5 class="m-b-10">Traveler Fees</h5>
                        </div>
                        <ul class="breadcrumb">
                            <li class="breadcrumb-item"><a href="index.html"><i class="feather icon-home"></i></a></li>
                            <li class="breadcrumb-item"><a href="#!">Fee Management</a></li>
                            <li class="breadcrumb-item"><a href="#!">Traveler Fees</a></li>
                        </ul>
                    </div>
                </div>
            </div>
        </div>
        <!-- [ breadcrumb ] end -->
        <!-- [ Main Content ] start -->
        <div class="row">
            <!-- Fee Management Form -->
            <div class="col-xl-12 col-md-12">
                <div class="card fee-form-card">
                    <div class="card-header">
                        <h5><i class="feather icon-dollar-sign mr-2"></i>Create New Fee</h5>
                        <span class="d-block m-t-5">Create and publish fees for travelers</span>
                    </div>
                    <div class="card-body">
                        <form id="feeForm" action="traveler-fees.php" method="post">
                            <input type="hidden" id="feeId">
                            <div class="row">
                                <div class="col-md-6">
                                    <div class="form-group">
                                        <label for="feeName">Fee Name <span class="text-danger">*</span></label>
                                        <input type="text" class="form-control" id="feeName" placeholder="Enter fee name" required>
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="form-group">
                                        <label for="feeAmount">Amount <span class="text-danger">*</span></label>
                                        <input type="number" class="form-control" id="feeAmount" placeholder="Enter fee amount" step="0.01" min="0" required>
                                    </div>
                                </div>
                            </div>
                            <div class="row">
                            <div class="col-md-6">
                                    <div class="form-group">
                                        <label for="feeCurrency">Currency</label>
                                        <select class="form-control" id="feeCurrency">
                                            <option value="USD">USD</option>
                                            <option value="EUR">EUR</option>
                                            <option value="GBP">GBP</option>
                                            <option value="JPY">JPY</option>
                                            <option value="AUD">AUD</option>
                                            <option value="CAD">CAD</option>
                                            <option value="CHF">CHF</option>
                                            <option value="CNY">CNY</option>
                                            <option value="INR">INR</option>
                                        </select>
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="form-group">
                                        <label for="feeStatus">Status</label>
                                        <select class="form-control" id="feeStatus">
                                            <option value="active">Active</option>
                                            <option value="inactive">Inactive</option>
                                        </select>
                                    </div>
                                </div>
                            </div>
                            <div class="form-group">
                                <label for="feeDescription">Description</label>
                                <textarea class="form-control" id="feeDescription" rows="3" placeholder="Enter fee description"></textarea>
                            </div>
                            <div class="form-group">
                                <div class="custom-control custom-checkbox">
                                    <input type="checkbox" class="custom-control-input" id="feeMandatory">
                                    <label class="custom-control-label" for="feeMandatory">This fee is mandatory for all applicable travelers</label>
                                </div>
                            </div>
                            <div class="text-right">
                                <button type="button" class="btn btn-secondary" onclick="resetFeeForm()">Reset</button>
                                <button type="button" class="btn btn-primary" onclick="createFee()">
                                    <i class="feather icon-plus-circle mr-1"></i> Create Fee
                                </button>
                            </div>
                        </form>
                    </div>
                </div>
            </div>

            <!-- Published Fees List -->
            <div class="col-xl-12 col-md-12">
                <div class="card">
                    <div class="card-header">
                        <h5><i class="feather icon-list mr-2"></i>Published Fees</h5>
                        <span class="d-block m-t-5">View and manage fees published to travelers</span>
                    </div>
                    <div class="card-body">
                        <div class="table-responsive">
                            <table class="table table-hover">
                                <thead>
                                    <tr>
                                        <th>ID</th>
                                        <th>Fee Name</th>

                                        <th>Amount</th>
                                        <th>Applicability</th>
                                        <th>Mandatory</th>
                                        <th>Status</th>
                                        <th>Created By</th>
                                        <th>Actions</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php if (empty($fees)): ?>
                                    <tr>
                                        <td colspan="9" class="text-center">No fees found</td>
                                    </tr>
                                    <?php else: ?>
                                    <?php foreach ($fees as $fee): ?>
                                    <tr>
                                        <td>#FEE-<?php echo $fee['fee_id']; ?></td>
                                        <td><?php echo htmlspecialchars($fee['fee_name']); ?></td>

                                        <td>
                                            <?php echo $fee['currency'] . ' ' . number_format($fee['amount'], 2); ?>
                                        </td>
                                        <td>
                                            <?php
                                            $applicabilityLabel = '';
                                            switch ($fee['applicability']) {
                                                case 'all':
                                                    $applicabilityLabel = 'All Travelers';
                                                    break;
                                                case 'new':
                                                    $applicabilityLabel = 'New Travelers';
                                                    break;
                                                case 'returning':
                                                    $applicabilityLabel = 'Returning Travelers';
                                                    break;
                                                case 'premium':
                                                    $applicabilityLabel = 'Premium Members';
                                                    break;
                                            }
                                            echo $applicabilityLabel;
                                            ?>
                                        </td>
                                        <td>
                                            <?php if ($fee['is_mandatory']): ?>
                                            <span class="badge badge-success">Yes</span>
                                            <?php else: ?>
                                            <span class="badge badge-secondary">No</span>
                                            <?php endif; ?>
                                        </td>
                                        <td>
                                            <?php if ($fee['status'] === 'active'): ?>
                                            <span class="badge badge-success">Active</span>
                                            <?php else: ?>
                                            <span class="badge badge-secondary">Inactive</span>
                                            <?php endif; ?>
                                        </td>
                                        <td><?php echo htmlspecialchars($fee['created_by_name'] ?? 'Admin'); ?></td>
                                        <td>
                                            <div class="btn-group btn-group-sm" role="group">
                                                <button class="btn btn-success" onclick="viewFee(<?php echo $fee['fee_id']; ?>)">
                                                    <i class="feather icon-eye d-md-none"></i>
                                                    <span class="d-none d-md-inline">View</span>
                                                </button>
                                                <button class="btn btn-secondary" onclick="editFee(<?php echo $fee['fee_id']; ?>)">
                                                    <i class="feather icon-edit-2 d-md-none"></i>
                                                    <span class="d-none d-md-inline">Edit</span>
                                                </button>
                                                <button class="btn btn-danger" onclick="deleteFee(<?php echo $fee['fee_id']; ?>)">
                                                    <i class="feather icon-trash-2 d-md-none"></i>
                                                    <span class="d-none d-md-inline">Delete</span>
                                                </button>
                                                <?php if ($fee['status'] === 'active'): ?>
                                                <button class="btn btn-primary" onclick="assignFee(<?php echo $fee['fee_id']; ?>)">
                                                    <i class="feather icon-user-plus d-md-none"></i>
                                                    <span class="d-none d-md-inline">Assign</span>
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
                    </div>
                </div>
            </div>


        </div>
    </div>
</div>
    <!-- Add Card Modal -->
    <div class="modal fade" id="addCardModal" tabindex="-1" role="dialog" aria-labelledby="addCardModalLabel" aria-hidden="true">
        <div class="modal-dialog" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="addCardModalLabel">Add New Card</h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div class="modal-body">
                    <form id="cardForm">
                        <input type="hidden" id="cardTransactionId" value="">
                        <div class="form-group">
                            <label for="cardNumber">Card Number</label>
                            <input type="text" class="form-control" id="cardNumber" placeholder="Enter card number" required>
                        </div>
                        <div class="row">
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label for="expiryDate">Expiry Date</label>
                                    <input type="month" class="form-control" id="expiryDate" required>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label for="cvv">CVV</label>
                                    <input type="text" class="form-control" id="cvv" placeholder="Enter CVV" maxlength="4" required>
                                </div>
                            </div>
                        </div>
                        <div class="form-group">
                            <label for="cardHolderName">Card Holder Name</label>
                            <input type="text" class="form-control" id="cardHolderName" placeholder="Enter card holder name" required>
                        </div>
                        <div class="form-group">
                            <label for="billingAddress">Billing Address</label>
                            <textarea class="form-control" id="billingAddress" rows="3" placeholder="Enter billing address"></textarea>
                        </div>
                    </form>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-dismiss="modal">Cancel</button>
                    <button type="button" class="btn btn-primary" onclick="saveCard()">Save Card</button>
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
        // Fee Management Functions
        function createFee() {
            // Check if form elements exist
            const feeIdElement = document.getElementById('feeId');
            const feeNameElement = document.getElementById('feeName');
            const feeAmountElement = document.getElementById('feeAmount');
            const feeCurrencyElement = document.getElementById('feeCurrency');
            const feeStatusElement = document.getElementById('feeStatus');
            const feeApplicabilityElement = document.getElementById('feeApplicability');
            const feeDescriptionElement = document.getElementById('feeDescription');
            const feeMandatoryElement = document.getElementById('feeMandatory');
            
            // Log which elements are missing
            if (!feeIdElement) console.error('Element with ID "feeId" not found');
            if (!feeNameElement) console.error('Element with ID "feeName" not found');
            if (!feeAmountElement) console.error('Element with ID "feeAmount" not found');
            if (!feeCurrencyElement) console.error('Element with ID "feeCurrency" not found');
            if (!feeStatusElement) console.error('Element with ID "feeStatus" not found');
            if (!feeApplicabilityElement) console.error('Element with ID "feeApplicability" not found');
            if (!feeDescriptionElement) console.error('Element with ID "feeDescription" not found');
            if (!feeMandatoryElement) console.error('Element with ID "feeMandatory" not found');
            
            // Get form values safely
            const feeId = feeIdElement ? feeIdElement.value : '';
            const feeName = feeNameElement ? feeNameElement.value : '';
            const feeAmount = feeAmountElement ? feeAmountElement.value : '';
            const feeCurrency = feeCurrencyElement ? feeCurrencyElement.value : 'USD';
            const feeStatus = feeStatusElement ? feeStatusElement.value : 'active';
            const feeApplicability = feeApplicabilityElement ? feeApplicabilityElement.value : 'all';
            const feeDescription = feeDescriptionElement ? feeDescriptionElement.value : '';
            const feeMandatory = feeMandatoryElement ? feeMandatoryElement.checked : false;

            console.log('Form data:', { feeName, feeAmount, feeCurrency, feeStatus, feeApplicability, feeMandatory });

            // Validate form
            if (!feeName || !feeAmount) {
                alert('Please fill in all required fields');
                return;
            }

            // Create fee data
            const feeData = {
                fee_name: feeName,
                fee_type: 'fixed', // Default to fixed type
                amount: parseFloat(feeAmount),
                currency: feeCurrency,
                status: feeStatus,
                applicability: feeApplicability,
                description: feeDescription,
                is_mandatory: feeMandatory
            };

            console.log('Sending fee data:', feeData);

            // If editing an existing fee, update it
            if (feeId) {
                updateFee(feeId, feeData);
                return;
            }

            // Send data to backend to create a new fee
            fetch('fee-actions.php?action=create', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json'
                },
                body: JSON.stringify(feeData)
            })
            .then(response => {
                console.log('Response status:', response.status);
                console.log('Response headers:', response.headers);
                return response.text().then(text => {
                    try {
                        // Try to parse as JSON
                        console.log('Raw response:', text);
                        return JSON.parse(text);
                    } catch (e) {
                        // If not valid JSON, log the raw response and throw error
                        console.error('Invalid JSON response:', text);
                        throw new Error('Server returned invalid JSON: ' + text);
                    }
                });
            })
            .then(data => {
                console.log('Response data:', data);
                if (data.success) {
                    showNotification('success', 'Fee created successfully!');
                    resetFeeForm();
                    window.location.reload();
                } else {
                    console.error('Error from server:', data.message);
                    showNotification('error', 'Error: ' + data.message);
                }
            })
            .catch(error => {
                console.error('Error details:', error);
                showNotification('error', 'An error occurred while creating the fee: ' + error.message);
            });
        }

        function updateFee(feeId, feeData) {
            // Send data to backend to update an existing fee
            fetch(`fee-actions.php?action=update&id=${feeId}`, {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json'
                },
                body: JSON.stringify(feeData)
            })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    showNotification('success', 'Fee updated successfully!');
                    resetFeeForm();
                    window.location.reload();
                } else {
                    showNotification('error', 'Error: ' + data.message);
                }
            })
            .catch(error => {
                console.error('Error:', error);
                showNotification('error', 'An error occurred while updating the fee.');
            });
        }

        function resetFeeForm() {
            document.getElementById('feeForm').reset();
            document.getElementById('feeId').value = '';
        }

        function viewFee(id) {
            // Fetch fee data from backend
            fetch(`fee-actions.php?action=get&id=${id}`)
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    const fee = data.fee;

                    // Create a modal to display fee details
                    let modalHtml = `
                    <div class="modal fade" id="feeModal" tabindex="-1" role="dialog" aria-labelledby="feeModalLabel" aria-hidden="true">
                        <div class="modal-dialog modal-lg" role="document">
                            <div class="modal-content">
                                <div class="modal-header">
                                    <h5 class="modal-title" id="feeModalLabel">Fee Details: ${fee.fee_name}</h5>
                                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                                        <span aria-hidden="true">&times;</span>
                                    </button>
                                </div>
                                <div class="modal-body">
                                    <div class="row">
                                        <div class="col-md-6">
                                            <div class="card mb-4">
                                                <div class="card-header bg-primary text-white">
                                                    <h6 class="mb-0">Basic Information</h6>
                                                </div>
                                                <div class="card-body">
                                                    <p><strong>Fee ID:</strong> #FEE-${fee.fee_id}</p>
                                                    <p><strong>Fee Name:</strong> ${fee.fee_name}</p>
                                                    <p><strong>Amount:</strong> ${fee.currency + ' ' + parseFloat(fee.amount).toFixed(2)}</p>
                                                    <p><strong>Status:</strong> <span class="badge ${fee.status === 'active' ? 'badge-success' : 'badge-secondary'}">${fee.status === 'active' ? 'Active' : 'Inactive'}</span></p>
                                                </div>
                                            </div>
                                        </div>
                                        <div class="col-md-6">
                                            <div class="card mb-4">
                                                <div class="card-header bg-info text-white">
                                                    <h6 class="mb-0">Additional Details</h6>
                                                </div>
                                                <div class="card-body">
                                                    <p><strong>Applicability:</strong> ${fee.applicability === 'all' ? 'All Travelers' : fee.applicability === 'new' ? 'New Travelers Only' : fee.applicability === 'returning' ? 'Returning Travelers Only' : 'Premium Members Only'}</p>
                                                    <p><strong>Mandatory:</strong> ${fee.is_mandatory ? 'Yes' : 'No'}</p>
                                                    <p><strong>Created By:</strong> ${fee.created_by_name || 'Admin'}</p>
                                                    <p><strong>Created At:</strong> ${new Date(fee.created_at).toLocaleString()}</p>
                                                    <p><strong>Last Updated:</strong> ${fee.updated_at ? new Date(fee.updated_at).toLocaleString() : 'Never'}</p>
                                                </div>
                                            </div>
                                        </div>
                                    </div>

                                    <div class="card">
                                        <div class="card-header bg-light">
                                            <h6 class="mb-0">Description</h6>
                                        </div>
                                        <div class="card-body">
                                            <p>${fee.description || 'No description available.'}</p>
                                        </div>
                                    </div>
                                </div>
                                <div class="modal-footer">
                                    <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
                                    <button type="button" class="btn btn-primary" onclick="editFee(${fee.fee_id})">Edit Fee</button>
                                    ${fee.status === 'active' ? `<button type="button" class="btn btn-success" onclick="assignFee(${fee.fee_id})">Assign to Travelers</button>` : ''}
                                </div>
                            </div>
                        </div>
                    </div>`;

                    // Add modal to the page
                    const modalContainer = document.createElement('div');
                    modalContainer.innerHTML = modalHtml;
                    document.body.appendChild(modalContainer);

                    // Show the modal
                    $('#feeModal').modal('show');

                    // Remove the modal from the DOM when it's closed
                    $('#feeModal').on('hidden.bs.modal', function() {
                        document.body.removeChild(modalContainer);
                    });
                } else {
                    showNotification('error', 'Error: ' + data.message);
                }
            })
            .catch(error => {
                console.error('Error:', error);
                showNotification('error', 'An error occurred while fetching the fee data.');
            });
        }

        function editFee(id) {
            // Fetch fee data from backend
            fetch(`fee-actions.php?action=get&id=${id}`)
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    const fee = data.fee;

                    // Close any open modals
                    $('.modal').modal('hide');

                    // Populate the form with fee data
                    document.getElementById('feeId').value = fee.fee_id;
                    document.getElementById('feeName').value = fee.fee_name;
                    document.getElementById('feeAmount').value = fee.amount;
                    document.getElementById('feeCurrency').value = fee.currency;
                    document.getElementById('feeStatus').value = fee.status;
                    document.getElementById('feeApplicability').value = fee.applicability;
                    document.getElementById('feeDescription').value = fee.description;
                    document.getElementById('feeMandatory').checked = fee.is_mandatory == 1;

                    // Update button text
                    const saveButton = document.querySelector('#feeForm button.btn-primary');
                    if (saveButton) {
                        saveButton.innerHTML = '<i class="feather icon-save mr-1"></i> Update Fee';
                    }

                    // Scroll to the form
                    document.getElementById('feeForm').scrollIntoView({ behavior: 'smooth' });
                } else {
                    showNotification('error', 'Error: ' + data.message);
                }
            })
            .catch(error => {
                console.error('Error:', error);
                showNotification('error', 'An error occurred while fetching the fee data.');
            });
        }

        function deleteFee(id) {
            if (confirm('Are you sure you want to delete this fee? This action cannot be undone.')) {
                fetch(`fee-actions.php?action=delete&id=${id}`, {
                    method: 'POST'
                })
                .then(response => response.json())
                .then(data => {
                    if (data.success) {
                        showNotification('success', 'Fee deleted successfully!');
                        window.location.reload();
                    } else {
                        showNotification('error', 'Error: ' + data.message);
                    }
                })
                .catch(error => {
                    console.error('Error:', error);
                    showNotification('error', 'An error occurred while deleting the fee.');
                });
            }
        }

        function assignFee(id) {
            // Create a modal to assign the fee to travelers
            let modalHtml = `
            <div class="modal fade" id="assignFeeModal" tabindex="-1" role="dialog" aria-labelledby="assignFeeModalLabel" aria-hidden="true">
                <div class="modal-dialog" role="document">
                    <div class="modal-content">
                        <div class="modal-header">
                            <h5 class="modal-title" id="assignFeeModalLabel">Assign Fee to Travelers</h5>
                            <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                                <span aria-hidden="true">&times;</span>
                            </button>
                        </div>
                        <div class="modal-body">
                            <form id="assignFeeForm">
                                <input type="hidden" id="assignFeeId" value="${id}">
                                <div class="form-group">
                                    <label for="assignTravelerId">Select Traveler</label>
                                    <select class="form-control" id="assignTravelerId" required>
                                        <option value="">-- Select a traveler --</option>
                                        <!-- Travelers will be loaded dynamically -->
                                    </select>
                                </div>
                                <div class="form-group">
                                    <label for="assignDueDate">Due Date (Optional)</label>
                                    <input type="date" class="form-control" id="assignDueDate">
                                </div>
                            </form>
                        </div>
                        <div class="modal-footer">
                            <button type="button" class="btn btn-secondary" data-dismiss="modal">Cancel</button>
                            <button type="button" class="btn btn-primary" onclick="saveAssignment()">Assign Fee</button>
                        </div>
                    </div>
                </div>
            </div>`;

            // Add modal to the page
            const modalContainer = document.createElement('div');
            modalContainer.innerHTML = modalHtml;
            document.body.appendChild(modalContainer);

            // Show the modal
            $('#assignFeeModal').modal('show');

            // Load travelers
            loadTravelers();

            // Remove the modal from the DOM when it's closed
            $('#assignFeeModal').on('hidden.bs.modal', function() {
                document.body.removeChild(modalContainer);
            });
        }

        function loadTravelers() {
            // For demo purposes, we'll add some sample travelers
            const travelers = [
                { id: 1, name: 'John Doe' },
                { id: 2, name: 'Jane Smith' },
                { id: 3, name: 'Bob Johnson' },
                { id: 4, name: 'Alice Williams' }
            ];

            const travelerSelect = document.getElementById('assignTravelerId');

            // Clear existing options except the first one
            while (travelerSelect.options.length > 1) {
                travelerSelect.remove(1);
            }

            // Add travelers to the select
            travelers.forEach(traveler => {
                const option = document.createElement('option');
                option.value = traveler.id;
                option.textContent = traveler.name;
                travelerSelect.appendChild(option);
            });
        }

        function saveAssignment() {
            const feeId = document.getElementById('assignFeeId').value;
            const travelerId = document.getElementById('assignTravelerId').value;
            const dueDate = document.getElementById('assignDueDate').value;

            if (!travelerId) {
                showNotification('error', 'Please select a traveler');
                return;
            }

            const assignmentData = {
                fee_id: parseInt(feeId),
                traveler_id: parseInt(travelerId),
                due_date: dueDate || null
            };

            fetch('fee-actions.php?action=assign', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json'
                },
                body: JSON.stringify(assignmentData)
            })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    showNotification('success', 'Fee assigned to traveler successfully!');
                    $('#assignFeeModal').modal('hide');
                } else {
                    showNotification('error', 'Error: ' + data.message);
                }
            })
            .catch(error => {
                console.error('Error:', error);
                showNotification('error', 'An error occurred while assigning the fee.');
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

        // Fee Transaction Functions
        function applyFilters() {
            const traveler = document.getElementById('filterTraveler').value.toLowerCase();
            const feeType = document.getElementById('filterFeeType').value;
            const paymentStatus = document.getElementById('filterPaymentStatus').value;
            const dateRange = document.getElementById('filterDateRange').value;

            console.log('Applying filters:', {
                traveler,
                feeType,
                paymentStatus,
                dateRange
            });

            // Get all transaction rows
            const rows = document.querySelectorAll('table tbody tr');
            let visibleCount = 0;

            // Loop through each row and check if it matches the filters
            rows.forEach(row => {
                const travelerName = row.cells[1].textContent.toLowerCase();
                const feeTypeText = row.cells[2].textContent;
                const statusBadge = row.cells[6].querySelector('.badge');
                const statusText = statusBadge ? statusBadge.textContent.toLowerCase() : '';
                const dateText = row.cells[4].textContent;

                // Check if the row matches all filters
                const matchesTraveler = !traveler || travelerName.includes(traveler);
                const matchesFeeType = !feeType || feeTypeText === feeType;
                const matchesStatus = !paymentStatus || statusText === paymentStatus;
                const matchesDate = !dateRange || isInDateRange(dateText, dateRange);

                // Show or hide the row based on filter matches
                if (matchesTraveler && matchesFeeType && matchesStatus && matchesDate) {
                    row.style.display = '';
                    visibleCount++;
                } else {
                    row.style.display = 'none';
                }
            });

            // Update the pagination info
            updatePaginationInfo(visibleCount);
        }

        function isInDateRange(dateStr, range) {
            const date = new Date(dateStr);
            const today = new Date();

            switch(range) {
                case 'today':
                    return date.toDateString() === today.toDateString();
                case 'week':
                    const weekAgo = new Date(today);
                    weekAgo.setDate(today.getDate() - 7);
                    return date >= weekAgo;
                case 'month':
                    const monthAgo = new Date(today);
                    monthAgo.setMonth(today.getMonth() - 1);
                    return date >= monthAgo;
                case 'quarter':
                    const quarterAgo = new Date(today);
                    quarterAgo.setMonth(today.getMonth() - 3);
                    return date >= quarterAgo;
                case 'year':
                    const yearAgo = new Date(today);
                    yearAgo.setFullYear(today.getFullYear() - 1);
                    return date >= yearAgo;
                default:
                    return true;
            }
        }

        function updatePaginationInfo(visibleCount) {
            const paginationInfo = document.querySelector('.text-muted');
            if (paginationInfo) {
                paginationInfo.textContent = `Showing 1 to ${visibleCount} of ${visibleCount} entries`;
            }
        }

        function resetFilters() {
            document.getElementById('filterTraveler').value = '';
            document.getElementById('filterFeeType').value = '';
            document.getElementById('filterPaymentStatus').value = '';
            document.getElementById('filterDateRange').value = '';

            // Show all rows
            const rows = document.querySelectorAll('table tbody tr');
            rows.forEach(row => {
                row.style.display = '';
            });

            // Update the pagination info
            updatePaginationInfo(rows.length);
        }

        function exportTransactions() {
            console.log('Exporting transactions');

            // Here you would typically generate a CSV or Excel file
            // and trigger a download
        }

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
                        <div class="modal-dialog" role="document">
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
                                            <p><strong>Traveler:</strong> ${transaction.traveler_name}</p>
                                            <p><strong>Fee Type:</strong> ${transaction.fee_type}</p>
                                            <p><strong>Amount:</strong> ${transaction.currency} ${parseFloat(transaction.amount).toFixed(2)}</p>
                                            <p><strong>Status:</strong> <span class="badge ${getStatusBadgeClass(transaction.status)}">${transaction.status}</span></p>
                                        </div>
                                        <div class="col-md-6">
                                            <p><strong>Transaction Date:</strong> ${new Date(transaction.date || transaction.transaction_date || new Date()).toLocaleDateString()}</p>
                                            <p><strong>Payment Method:</strong> ${transaction.payment_method || 'N/A'}</p>
                                            <p><strong>Created At:</strong> ${transaction.created_at ? new Date(transaction.created_at).toLocaleString() : 'N/A'}</p>
                                            <p><strong>Updated At:</strong> ${transaction.updated_at ? new Date(transaction.updated_at).toLocaleString() : 'N/A'}</p>
                                        </div>
                                    </div>
                                    <div class="row mt-3">
                                        <div class="col-md-12">
                                            <p><strong>Description:</strong></p>
                                            <p>${transaction.description || 'No description available'}</p>
                                        </div>
                                    </div>

                                    ${transaction.payment_method === 'credit_card' ?
                                    `<div class="row mt-4">
                                        <div class="col-md-12">
                                            <div class="alert alert-info">
                                                <div class="d-flex align-items-center mb-2">
                                                    <i class="feather icon-credit-card mr-2" style="font-size: 1.5rem;"></i>
                                                    <h5 class="m-0">Payment Card Information</h5>
                                                </div>
                                                <div id="cardDetails-${transaction.transaction_id}" class="mt-3">
                                                    <div class="text-center py-3">
                                                        <div class="spinner-border text-primary" role="status">
                                                            <span class="sr-only">Loading...</span>
                                                        </div>
                                                        <p class="mt-2 mb-0">Loading card details...</p>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>` : ''}
                                </div>
                                <div class="modal-footer">
                                    <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
                                    ${transaction.status === 'pending' ?
                                        `<button type="button" class="btn btn-success mr-2" onclick="verifyPayment(${transaction.transaction_id})">Verify</button>
                                         <button type="button" class="btn btn-danger" onclick="markAsFailed(${transaction.transaction_id})">Fail</button>` : ''}
                                    ${transaction.status === 'failed' ?
                                        `<button type="button" class="btn btn-warning" onclick="retryPayment(${transaction.transaction_id})">Retry</button>` : ''}
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

                    // If this is a credit card transaction, load the card details
                    if (transaction.payment_method === 'credit_card') {
                        loadCardDetailsForTransaction(transaction);
                    }

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

        // Card Management Functions
        function toggleCardSelection() {
            const paymentMethod = document.getElementById('paymentMethod').value;
            const cardSelectionRow = document.getElementById('cardSelectionRow');

            if (paymentMethod === 'credit_card') {
                cardSelectionRow.style.display = 'block';
                loadCards();
            } else {
                cardSelectionRow.style.display = 'none';
            }
        }

        function loadCards() {
            // For demo purposes, we'll use traveler ID 1
            const travelerId = 1;

            fetch(`card-actions.php?action=get-by-traveler&traveler_id=${travelerId}`)
                .then(response => response.json())
                .then(data => {
                    if (data.success) {
                        const cardSelect = document.getElementById('cardId');

                        // Clear existing options except the first one
                        while (cardSelect.options.length > 1) {
                            cardSelect.remove(1);
                        }

                        // Add cards to the select
                        data.cards.forEach(card => {
                            const option = document.createElement('option');
                            option.value = card.card_id;
                            option.textContent = `${card.card_type} **** **** **** ${card.masked_card_number.slice(-4)}`;
                            cardSelect.appendChild(option);
                        });
                    } else {
                        console.error('Error loading cards:', data.message);
                    }
                })
                .catch(error => {
                    console.error('Error:', error);
                });
        }

        function showAddCardModal() {
            // Reset the form
            document.getElementById('cardForm').reset();

            // Clear the transaction ID field
            document.getElementById('cardTransactionId').value = '';

            // Show the modal
            $('#addCardModal').modal('show');
        }

        function showAddCardModalForTransaction(transactionId) {
            // Reset the form
            document.getElementById('cardForm').reset();

            // Set the transaction ID field
            document.getElementById('cardTransactionId').value = transactionId;

            // Show the modal
            $('#addCardModal').modal('show');
        }

        function saveCard() {
            // Get form values
            const cardNumber = document.getElementById('cardNumber').value;
            const expiryDate = document.getElementById('expiryDate').value;
            const cvv = document.getElementById('cvv').value;
            const cardHolderName = document.getElementById('cardHolderName').value;
            const billingAddress = document.getElementById('billingAddress').value;

            // Validate form
            if (!cardNumber || !expiryDate || !cvv || !cardHolderName) {
                alert('Please fill in all required fields');
                return;
            }

            // Format expiry date for database (YYYY-MM-DD)
            const formattedExpiryDate = expiryDate + '-01';

            // Create card data
            const cardData = {
                card_number: cardNumber,
                expiry_date: formattedExpiryDate,
                cvv: cvv,
                card_holder_name: cardHolderName,
                billing_address: billingAddress,
                traveler_id: 1 // For demo purposes, we'll use traveler ID 1
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
                        loadCards();
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

        function downloadReceipt(id) {
            alert('Receipt download functionality would be implemented here.');
            // In a real implementation, you would make an AJAX request to generate a PDF receipt
            // and trigger a download
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

        // Add event listeners for real-time filtering
        document.addEventListener('DOMContentLoaded', function() {
            const filterInputs = [
                document.getElementById('filterTraveler'),
                document.getElementById('filterFeeType'),
                document.getElementById('filterPaymentStatus'),
                document.getElementById('filterDateRange')
            ];

            filterInputs.forEach(input => {
                if (input) {
                    input.addEventListener('change', applyFilters);
                    if (input.tagName === 'INPUT') {
                        input.addEventListener('keyup', applyFilters);
                    }
                }
            });
        });
    </script>
</body>
</html>



