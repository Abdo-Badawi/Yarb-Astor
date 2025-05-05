<!DOCTYPE html>
<html lang="en">

<head>
    <title>Traveler Fees - HomeStay Admin</title>
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
    <meta name="description" content="Traveler Fees Management for HomeStay Admin Dashboard" />
    <meta name="keywords" content="admin, dashboard, fees, management">
    <meta name="author" content="HomeStay" />
    <!-- Favicon icon -->
    <link rel="icon" href="assets/images/favicon.ico" type="image/x-icon">

    <!-- vendor css -->
    <link rel="stylesheet" href="assets/css/style.css">
    <!-- custom css -->
    <link rel="stylesheet" href="assets/css/custom.css">
    <style>
        /* Custom styles for Traveler Fees */
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
            width: 80px !important;
            text-align: center;
            display: inline-block;
            box-sizing: border-box;
        }
        
        /* Ensure buttons display horizontally */
        td .btn-sm {
            display: inline-block;
            margin-right: 5px;
        }
        
        /* Remove margin from last button in a group */
        td .btn-sm:last-child {
            margin-right: 0;
        }
        
        /* Make all action buttons the same width and center them */
        .table td, .table th {
            text-align: center;
            vertical-align: middle;
        }
        
        .table .btn-sm {
            min-width: 90px; /* or your preferred width */
            text-align: center;
            display: inline-block;
            margin: 2px 4px;
            box-sizing: border-box;
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
                        <h5><i class="feather icon-dollar-sign mr-2"></i>Traveler Fee Management</h5>
                        <span class="d-block m-t-5">Create and manage fees for travelers</span>
                    </div>
                    <div class="card-body">
                        <form id="feeForm">
                            <input type="hidden" id="feeId">
                            <div class="row">
                                <div class="col-md-6">
                                    <div class="form-group">
                                        <label for="feeName">Fee Name</label>
                                        <input type="text" class="form-control" id="feeName" placeholder="Enter fee name">
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="form-group">
                                        <label for="feeType">Fee Type</label>
                                        <select class="form-control" id="feeType">
                                            <option value="">Select Fee Type</option>
                                            <option value="fixed">Fixed Amount</option>
                                            <option value="percentage">Percentage</option>
                                            <option value="tiered">Tiered</option>
                                        </select>
                                    </div>
                                </div>
                            </div>
                            <div class="row">
                                <div class="col-md-6">
                                    <div class="form-group">
                                        <label for="feeAmount">Amount</label>
                                        <input type="number" class="form-control" id="feeAmount" placeholder="Enter fee amount">
                                    </div>
                                </div>
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
                            </div>
                            <div class="row">
                                <div class="col-md-6">
                                    <div class="form-group">
                                        <label for="feeStatus">Status</label>
                                        <select class="form-control" id="feeStatus">
                                            <option value="active">Active</option>
                                            <option value="inactive">Inactive</option>
                                            <option value="pending">Pending</option>
                                        </select>
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="form-group">
                                        <label for="feeApplicability">Applicability</label>
                                        <select class="form-control" id="feeApplicability">
                                            <option value="all">All Travelers</option>
                                            <option value="new">New Travelers Only</option>
                                            <option value="returning">Returning Travelers Only</option>
                                            <option value="premium">Premium Members Only</option>
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
                                <button type="button" class="btn btn-primary" onclick="saveFee()">Save Fee</button>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
            
            <!-- Fee List -->
            <div class="col-xl-12 col-md-12">
                <div class="card">
                    <div class="card-header">
                        <h5><i class="feather icon-list mr-2"></i>Fee Transactions</h5>
                        <span class="d-block m-t-5">View and manage fee transactions from travelers</span>
                    </div>
                    <div class="card-body">
                        <!-- Filters -->
                        <div class="filter-row">
                            <div class="row">
                                <div class="col-md-3">
                                    <div class="form-group">
                                        <label for="filterTraveler">Traveler</label>
                                        <input type="text" class="form-control" id="filterTraveler" placeholder="Search by traveler name">
                                    </div>
                                </div>
                                <div class="col-md-3">
                                    <div class="form-group">
                                        <label for="filterFeeType">Fee Type</label>
                                        <select class="form-control" id="filterFeeType">
                                            <option value="">All Types</option>
                                            <option value="booking">Booking Fee</option>
                                            <option value="service">Service Fee</option>
                                            <option value="membership">Membership Fee</option>
                                            <option value="verification">Verification Fee</option>
                                        </select>
                                    </div>
                                </div>
                                <div class="col-md-3">
                                    <div class="form-group">
                                        <label for="filterPaymentStatus">Payment Status</label>
                                        <select class="form-control" id="filterPaymentStatus">
                                            <option value="">All Status</option>
                                            <option value="paid">Paid</option>
                                            <option value="pending">Pending</option>
                                            <option value="failed">Failed</option>
                                            <option value="refunded">Refunded</option>
                                        </select>
                                    </div>
                                </div>
                                <div class="col-md-3">
                                    <div class="form-group">
                                        <label for="filterDateRange">Date Range</label>
                                        <select class="form-control" id="filterDateRange">
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
                            <div class="row mt-2">
                                <div class="col-md-12 text-right">
                                    <button class="btn btn-primary" onclick="applyFilters()">Apply Filters</button>
                                    <button class="btn btn-secondary" onclick="resetFilters()">Reset</button>
                                    <button class="btn btn-success" onclick="exportTransactions()">Export</button>
                                </div>
                            </div>
                        </div>
                        
                        <!-- Fee Transactions Table -->
                        <div class="table-responsive">
                            <table class="table table-hover">
                                <thead>
                                    <tr>
                                        <th>Transaction ID</th>
                                        <th>Traveler</th>
                                        <th>Fee Type</th>
                                        <th>Amount</th>
                                        <th>Date</th>
                                        <th>Payment Method</th>
                                        <th>Status</th>
                                        <th>Actions</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <tr>
                                        <td>#TRX-1001</td>
                                        <td>John Smith</td>
                                        <td>Booking Fee</td>
                                        <td>$25.00</td>
                                        <td>2023-06-15</td>
                                        <td>Credit Card</td>
                                        <td><span class="badge badge-success">Paid</span></td>
                                        <td>
                                            <button class="btn btn-info btn-sm">View</button>
                                            <button class="btn btn-primary btn-sm">Receipt</button>
                                        </td>
                                    </tr>
                                    <tr>
                                        <td>#TRX-1002</td>
                                        <td>Emma Johnson</td>
                                        <td>Service Fee</td>
                                        <td>$12.50</td>
                                        <td>2023-06-14</td>
                                        <td>PayPal</td>
                                        <td><span class="badge badge-success">Paid</span></td>
                                        <td>
                                            <button class="btn btn-info btn-sm">View</button>
                                            <button class="btn btn-primary btn-sm">Receipt</button>
                                        </td>
                                    </tr>
                                    <tr>
                                        <td>#TRX-1003</td>
                                        <td>Michael Brown</td>
                                        <td>Membership Fee</td>
                                        <td>$99.00</td>
                                        <td>2023-06-13</td>
                                        <td>Bank Transfer</td>
                                        <td><span class="badge badge-warning">Pending</span></td>
                                        <td>
                                            <button class="btn btn-info btn-sm">View</button>
                                            <button class="btn btn-secondary btn-sm">Verify</button>
                                        </td>
                                    </tr>
                                    <tr>
                                        <td>#TRX-1004</td>
                                        <td>Sarah Davis</td>
                                        <td>Verification Fee</td>
                                        <td>$15.00</td>
                                        <td>2023-06-12</td>
                                        <td>Credit Card</td>
                                        <td><span class="badge badge-danger">Failed</span></td>
                                        <td>
                                            <button class="btn btn-info btn-sm">View</button>
                                            <button class="btn btn-warning btn-sm">Retry</button>
                                        </td>
                                    </tr>
                                    <tr>
                                        <td>#TRX-1005</td>
                                        <td>David Wilson</td>
                                        <td>Booking Fee</td>
                                        <td>$25.00</td>
                                        <td>2023-06-11</td>
                                        <td>Credit Card</td>
                                        <td><span class="badge badge-success">Paid</span></td>
                                        <td>
                                            <button class="btn btn-info btn-sm">View</button>
                                            <button class="btn btn-primary btn-sm">Receipt</button>
                                        </td>
                                    </tr>
                                    <tr>
                                        <td>#TRX-1006</td>
                                        <td>Jennifer Lee</td>
                                        <td>Service Fee</td>
                                        <td>$18.75</td>
                                        <td>2023-06-10</td>
                                        <td>PayPal</td>
                                        <td><span class="badge badge-success">Paid</span></td>
                                        <td>
                                            <button class="btn btn-info btn-sm">View</button>
                                            <button class="btn btn-primary btn-sm">Receipt</button>
                                        </td>
                                    </tr>
                                    <tr>
                                        <td>#TRX-1007</td>
                                        <td>Robert Taylor</td>
                                        <td>Membership Fee</td>
                                        <td>$99.00</td>
                                        <td>2023-06-09</td>
                                        <td>Credit Card</td>
                                        <td><span class="badge badge-success">Paid</span></td>
                                        <td>
                                            <button class="btn btn-info btn-sm">View</button>
                                            <button class="btn btn-primary btn-sm">Receipt</button>
                                        </td>
                                    </tr>
                                    <tr>
                                        <td>#TRX-1008</td>
                                        <td>Emily White</td>
                                        <td>Verification Fee</td>
                                        <td>$15.00</td>
                                        <td>2023-06-08</td>
                                        <td>PayPal</td>
                                        <td><span class="badge badge-success">Paid</span></td>
                                        <td>
                                            <button class="btn btn-info btn-sm">View</button>
                                            <button class="btn btn-primary btn-sm">Receipt</button>
                                        </td>
                                    </tr>
                                </tbody>
                            </table>
                        </div>
                        
                        <!-- Pagination -->
                        <div class="row mt-4">
                            <div class="col-md-6">
                                <p class="text-muted">Showing 1 to 8 of 8 entries</p>
                            </div>
                            <div class="col-md-6">
                                <nav aria-label="Page navigation">
                                    <ul class="pagination justify-content-end">
                                        <li class="page-item disabled">
                                            <a class="page-link" href="#" tabindex="-1">Previous</a>
                                        </li>
                                        <li class="page-item active"><a class="page-link" href="#">1</a></li>
                                        <li class="page-item disabled">
                                            <a class="page-link" href="#">Next</a>
                                        </li>
                                    </ul>
                                </nav>
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
        // Fee Management Functions
        function saveFee() {
            // Get form values
            const feeId = document.getElementById('feeId').value;
            const feeName = document.getElementById('feeName').value;
            const feeType = document.getElementById('feeType').value;
            const feeAmount = document.getElementById('feeAmount').value;
            const feeCurrency = document.getElementById('feeCurrency').value;
            const feeStatus = document.getElementById('feeStatus').value;
            const feeApplicability = document.getElementById('feeApplicability').value;
            const feeDescription = document.getElementById('feeDescription').value;
            const feeMandatory = document.getElementById('feeMandatory').checked;
            
            // Validate form
            if (!feeName || !feeType || !feeAmount) {
                alert('Please fill in all required fields');
                return;
            }
            
            // Here you would typically send this data to your backend
            console.log('Saving fee:', {
                feeId,
                feeName,
                feeType,
                feeAmount,
                feeCurrency,
                feeStatus,
                feeApplicability,
                feeDescription,
                feeMandatory
            });
            
            // Show success message
            alert('Fee saved successfully!');
            
            // Reset form
            resetFeeForm();
        }
        
        function resetFeeForm() {
            document.getElementById('feeForm').reset();
            document.getElementById('feeId').value = '';
        }
        
        function editFee(id) {
            // Here you would typically fetch the fee data from your backend
            console.log('Editing fee with ID:', id);
            
            // For demo purposes, we'll just populate the form with some sample data
            document.getElementById('feeId').value = id;
            document.getElementById('feeName').value = 'Sample Fee ' + id;
            document.getElementById('feeType').value = 'fixed';
            document.getElementById('feeAmount').value = '25.00';
            document.getElementById('feeCurrency').value = 'USD';
            document.getElementById('feeStatus').value = 'active';
            document.getElementById('feeApplicability').value = 'all';
            document.getElementById('feeDescription').value = 'This is a sample fee description.';
            document.getElementById('feeMandatory').checked = true;
        }
        
        function deleteFee(id) {
            if (confirm('Are you sure you want to delete this fee?')) {
                // Here you would typically send a delete request to your backend
                console.log('Deleting fee with ID:', id);
                
                // Show success message
                alert('Fee deleted successfully!');
            }
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
            console.log('Viewing transaction:', id);
            
            // Here you would typically open a modal with transaction details
        }
        
        function downloadReceipt(id) {
            console.log('Downloading receipt for transaction:', id);
            
            // Here you would typically generate a PDF receipt
            // and trigger a download
        }
        
        function verifyPayment(id) {
            console.log('Verifying payment for transaction:', id);
            
            // Here you would typically send a verification request to your backend
        }
        
        function retryPayment(id) {
            console.log('Retrying payment for transaction:', id);
            
            // Here you would typically initiate a new payment attempt
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