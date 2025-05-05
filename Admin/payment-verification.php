<!DOCTYPE html>
<html lang="en">

<head>
    <title>Payment Verification - HomeStay Admin</title>
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
    <meta name="description" content="Payment Verification for HomeStay Admin Dashboard" />
    <meta name="keywords" content="admin, dashboard, payment, verification">
    <meta name="author" content="HomeStay" />
    <!-- Favicon icon -->
    <link rel="icon" href="assets/images/favicon.ico" type="image/x-icon">

    <!-- vendor css -->
    <link rel="stylesheet" href="assets/css/style.css">
    <!-- custom css -->
    <link rel="stylesheet" href="assets/css/custom.css">
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
                        <!-- Search Section -->
                        <div class="card mb-4">
                            <div class="card-header bg-light">
                                <h5 class="mb-0"><i class="feather icon-search mr-2"></i>Search Transactions</h5>
                        </div>
                        <div class="card-body">
                                <form>
                                    <div class="row">
                                        <div class="col-md-4">
                                            <div class="form-group">
                                                <label for="transactionId">Transaction ID</label>
                                                <input type="text" class="form-control" id="transactionId" placeholder="Enter transaction ID">
                                            </div>
                                        </div>
                                        <div class="col-md-4">
                                            <div class="form-group">
                                                <label for="travelerName">Traveler Name</label>
                                                <input type="text" class="form-control" id="travelerName" placeholder="Enter traveler name">
                                            </div>
                                        </div>
                                        <div class="col-md-4">
                                            <div class="form-group">
                                                <label for="bookingId">Booking ID</label>
                                                <input type="text" class="form-control" id="bookingId" placeholder="Enter booking ID">
                                            </div>
                                        </div>
                                    </div>
                                    <div class="row mt-2">
                                        <div class="col-md-4">
                                            <div class="form-group">
                                                <label for="paymentStatus">Payment Status</label>
                                                <select class="form-control" id="paymentStatus">
                                        <option value="">All Statuses</option>
                                        <option value="pending">Pending</option>
                                        <option value="verified">Verified</option>
                                        <option value="rejected">Rejected</option>
                                    </select>
                                </div>
                                        </div>
                                        <div class="col-md-4">
                                            <div class="form-group">
                                                <label for="dateFrom">Date From</label>
                                                <input type="date" class="form-control" id="dateFrom">
                                            </div>
                                        </div>
                                        <div class="col-md-4">
                                            <div class="form-group">
                                                <label for="dateTo">Date To</label>
                                                <input type="date" class="form-control" id="dateTo">
                                            </div>
                                        </div>
                                    </div>
                                    <div class="text-right mt-3">
                                        <button type="button" class="btn btn-primary"><i class="feather icon-search mr-1"></i> Search</button>
                                        <button type="reset" class="btn btn-secondary"><i class="feather icon-refresh-cw mr-1"></i> Reset</button>
                                    </div>
                                </form>
                            </div>
                        </div>
                        
                        <div class="verification-examples">
                            <div class="card mb-3">
                                <div class="card-header bg-light">
                                    <div class="d-flex justify-content-between align-items-center">
                                        <h6 class="mb-0">Request #1: Double Payment Issue</h6>
                                        <span class="badge bg-warning">Pending</span>
                                    </div>
                                </div>
                                <div class="card-body">
                                    <p><strong>Traveler:</strong> Sarah Johnson</p>
                                    <p><strong>Booking ID:</strong> BK-2023-45678</p>
                                    <p><strong>Issue:</strong> Traveler reports being charged twice for the same booking. First payment of ₹5,000 on 15/05/2023 and second payment of ₹5,000 on 16/05/2023.</p>
                                    <p><strong>Action Required:</strong> Verify both transactions in the database and process refund if confirmed.</p>
                                    <div class="mt-2">
                                        <a href="#" class="btn btn-sm btn-outline-primary"><i class="feather icon-message-square mr-1"></i> View Chat</a>
                                        <a href="traveler-fees.html" class="btn btn-sm btn-outline-info"><i class="feather icon-search mr-1"></i> Check Transaction</a>
                                        <a href="#" class="btn btn-sm btn-outline-success"><i class="feather icon-send mr-1"></i> Respond</a>
                                    </div>
                                </div>
                            </div>
                            
                            <div class="card mb-3">
                                <div class="card-header bg-light">
                                    <div class="d-flex justify-content-between align-items-center">
                                        <h6 class="mb-0">Request #2: Payment Not Received</h6>
                                        <span class="badge bg-danger">Urgent</span>
                                    </div>
                                </div>
                                <div class="card-body">
                                    <p><strong>Traveler:</strong> Michael Chen</p>
                                    <p><strong>Booking ID:</strong> BK-2023-12345</p>
                                    <p><strong>Issue:</strong> Traveler made payment of ₹7,500 on 10/06/2023 but host reports not receiving the payment. Transaction ID provided: TXN-789012</p>
                                    <p><strong>Action Required:</strong> Check payment status in the system and verify with payment gateway.</p>
                                    <div class="mt-2">
                                        <a href="#" class="btn btn-sm btn-outline-primary"><i class="feather icon-message-square mr-1"></i> View Chat</a>
                                        <a href="traveler-fees.html" class="btn btn-sm btn-outline-info"><i class="feather icon-search mr-1"></i> Check Transaction</a>
                                        <a href="#" class="btn btn-sm btn-outline-success"><i class="feather icon-send mr-1"></i> Respond</a>
                                    </div>
                                </div>
                            </div>
                            
                            <div class="card mb-3">
                                <div class="card-header bg-light">
                                    <div class="d-flex justify-content-between align-items-center">
                                        <h6 class="mb-0">Request #3: Refund Request</h6>
                                        <span class="badge bg-info">In Progress</span>
                                    </div>
                                </div>
                                        <div class="card-body">
                                    <p><strong>Traveler:</strong> Emma Rodriguez</p>
                                    <p><strong>Booking ID:</strong> BK-2023-78901</p>
                                    <p><strong>Issue:</strong> Traveler requesting refund for cancelled booking. Original payment of ₹12,000 made on 01/04/2023. Cancellation policy allows for 80% refund.</p>
                                    <p><strong>Action Required:</strong> Calculate refund amount and process according to cancellation policy.</p>
                                    <div class="mt-2">
                                        <a href="#" class="btn btn-sm btn-outline-primary"><i class="feather icon-message-square mr-1"></i> View Chat</a>
                                        <a href="traveler-fees.html" class="btn btn-sm btn-outline-info"><i class="feather icon-search mr-1"></i> Check Transaction</a>
                                        <a href="#" class="btn btn-sm btn-outline-success"><i class="feather icon-send mr-1"></i> Respond</a>
                                    </div>
                                </div>
                            </div>
                            
                            <div class="card mb-3">
                                <div class="card-header bg-light">
                                    <div class="d-flex justify-content-between align-items-center">
                                        <h6 class="mb-0">Request #4: Payment Method Change</h6>
                                        <span class="badge bg-secondary">New</span>
                                            </div>
                                        </div>
                                <div class="card-body">
                                    <p><strong>Traveler:</strong> David Kim</p>
                                    <p><strong>Booking ID:</strong> BK-2023-34567</p>
                                    <p><strong>Issue:</strong> Traveler wants to change payment method from credit card to UPI for upcoming booking. Original payment not yet processed.</p>
                                    <p><strong>Action Required:</strong> Update payment method in the system and send confirmation to traveler.</p>
                                    <div class="mt-2">
                                        <a href="#" class="btn btn-sm btn-outline-primary"><i class="feather icon-message-square mr-1"></i> View Chat</a>
                                        <a href="traveler-fees.html" class="btn btn-sm btn-outline-info"><i class="feather icon-search mr-1"></i> Check Transaction</a>
                                        <a href="#" class="btn btn-sm btn-outline-success"><i class="feather icon-send mr-1"></i> Respond</a>
                                    </div>
                                </div>
                            </div>
                        </div>
                        
                        <!-- New Payment Verification Card -->
                        <div class="card mt-4">
                            <div class="card-header bg-light">
                                <h5 class="mb-0">Payment Verification Request</h5>
                            </div>
                                        <div class="card-body">
                                <h5>Payment #1</h5>
                                <div class="payment-info mb-3">
                                    <div class="row">
                                        <div class="col-md-6">
                                            <p><strong>Traveler:</strong> James Wilson</p>
                                            <p><strong>Booking ID:</strong> BK-2023-98765</p>
                                            <p><strong>Amount:</strong> ₹8,500</p>
                                            </div>
                                        <div class="col-md-6">
                                            <p><strong>Payment Date:</strong> 22/07/2023</p>
                                            <p><strong>Payment Method:</strong> Credit Card</p>
                                            <p><strong>Transaction ID:</strong> TXN-456789</p>
                                        </div>
                                    </div>
                                    <div class="mt-2">
                                        <p><strong>Status:</strong> <span class="badge bg-warning">Pending Verification</span></p>
                                        <p><strong>Notes:</strong> Traveler has uploaded payment receipt. Please verify the transaction details with the payment gateway.</p>
                                    </div>
                                </div>
                                <div class="button-group">
                                    <a href="traveler-fees.html" class="btn btn-info btn-sm"><i class="feather icon-search mr-1"></i> Check Transaction</a>
                                    <button class="btn btn-primary btn-sm"><i class="feather icon-check mr-1"></i> Approve</button>
                                    <button class="btn btn-danger btn-sm"><i class="feather icon-x mr-1"></i> Reject</button>
                                    <button class="btn btn-secondary btn-sm"><i class="feather icon-message-square mr-1"></i> View Chat</button>
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
</body>

</html> 