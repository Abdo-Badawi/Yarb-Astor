<?php
require_once '../Controllers/TravelerFeeController.php';
session_start();

// Check if user is logged in and is a traveler
if (!isset($_SESSION['userID']) || $_SESSION['userType'] !== 'traveler') {
    header("Location: ../Common/login.php");
    exit;
}

if (!isset($_SESSION['auth_token'])) {
    $_SESSION['auth_token'] = bin2hex(random_bytes(32));
}

$travelerID = $_SESSION['userID'];
$feeController = new TravelerFeeController();

// Get all published fees and transaction history
$publishedFees = $feeController->getAllPublishedFees();
$applicableFees = $feeController->getApplicableFees($travelerID);
$transactions = $feeController->getFeeTransactions($travelerID);

// Debug information
error_log("Published Fees: " . print_r($publishedFees, true));
error_log("Applicable Fees: " . print_r($applicableFees, true));

// Create a map of fee_id => is_paid for quick lookup
$paidFeesMap = [];
foreach ($applicableFees as $fee) {
    $paidFeesMap[$fee['fee_id']] = $fee['is_paid'] ?? false;
}

// Process payment if form is submitted
$paymentResult = null;
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['pay_fee'])) {
    // Validate CSRF token
    if (!isset($_POST['token']) || $_POST['token'] !== $_SESSION['auth_token']) {
        $paymentResult = [
            'success' => false,
            'message' => 'Invalid security token. Please try again.'
        ];
    } else {
        // Prepare payment data
        $paymentData = [
            'traveler_id' => $travelerID,
            'fee_id' => (int)$_POST['fee_id'],
            'amount' => (float)$_POST['amount'],
            'payment_method' => $_POST['payment_method']
        ];
        
        // Add card details if payment method is credit card
        if ($_POST['payment_method'] === 'credit_card') {
            if (isset($_POST['use_saved_card']) && $_POST['use_saved_card'] === 'yes' && !empty($_POST['saved_card_id'])) {
                $paymentData['card_id'] = (int)$_POST['saved_card_id'];
            } else {
                $paymentData['card_number'] = $_POST['card_number'];
                $paymentData['card_holder'] = $_POST['card_holder'];
                $paymentData['expiry_date'] = $_POST['expiry_month'] . '/' . $_POST['expiry_year'];
                $paymentData['cvv'] = $_POST['cvv'];
                $paymentData['save_card'] = isset($_POST['save_card']) && $_POST['save_card'] === 'yes';
            }
        }
        
        // Process the payment
        $paymentResult = $feeController->processPayment($paymentData);
        
        // Refresh data if payment was successful
        if ($paymentResult['success']) {
            $publishedFees = $feeController->getAllPublishedFees();
            $applicableFees = $feeController->getApplicableFees($travelerID);
            $transactions = $feeController->getFeeTransactions($travelerID);
            
            // Update the paid fees map
            $paidFeesMap = [];
            foreach ($applicableFees as $fee) {
                $paidFeesMap[$fee['fee_id']] = $fee['is_paid'] ?? false;
            }
        }
    }
}

// Get saved cards for credit card payments
$savedCards = $feeController->travelerFeeModel->getSavedCards($travelerID);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Traveler Fees - Cultural Exchange Platform</title>
    <link rel="stylesheet" href="../css/bootstrap.min.css">
    <link rel="stylesheet" href="../css/style.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    <style>
        .fee-card {
            transition: transform 0.3s;
        }
        .fee-card:hover {
            transform: translateY(-5px);
        }
        .payment-methods .nav-link {
            border: 1px solid #dee2e6;
            border-radius: 0.25rem;
            margin-right: 10px;
            padding: 10px 15px;
        }
        .payment-methods .nav-link.active {
            background-color: #7b2cbf;
            color: white;
            border-color: #7b2cbf;
        }
        .card-badge {
            position: absolute;
            top: 10px;
            right: 10px;
        }
        .fee-category {
            margin-bottom: 30px;
        }
        .fee-category-title {
            border-bottom: 2px solid #f0f0f0;
            padding-bottom: 10px;
            margin-bottom: 20px;
        }
    </style>
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
    <?php include 'navTraveler.php'; ?>
    <!-- Navbar End -->

    <!-- Fees Section Start -->
    <div class="container-fluid py-5">
        <div class="container py-5">
            <div class="mx-auto text-center mb-5" style="max-width: 900px;">
                <h5 class="section-title px-3">TRAVELER FEES</h5>
                <h1 class="mb-0">Manage Your Platform Fees</h1>
            </div>

            <?php if ($paymentResult): ?>
                <div class="row mb-4">
                    <div class="col-12">
                        <div class="alert alert-<?= $paymentResult['success'] ? 'success' : 'danger' ?> alert-dismissible fade show" role="alert">
                            <?= htmlspecialchars($paymentResult['message']) ?>
                            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                        </div>
                    </div>
                </div>
            <?php endif; ?>

            <!-- All Published Fees -->
            <div class="row mb-5">
                <div class="col-12">
                    <h3 class="mb-4">Platform Fees</h3>
                </div>
                
                <?php if (empty($publishedFees)): ?>
                    <div class="col-12">
                        <div class="alert alert-info">
                            <p class="mb-0">There are no fees available at this time.</p>
                        </div>
                    </div>
                <?php else: ?>
                    <!-- Mandatory Fees -->
                    <div class="col-12 fee-category">
                        <h4 class="fee-category-title">Mandatory Fees</h4>
                        <div class="row">
                            <?php 
                            $mandatoryCount = 0;
                            foreach ($publishedFees as $fee): 
                                if ($fee['is_mandatory'] == 1):
                                    $mandatoryCount++;
                                    $isPaid = isset($paidFeesMap[$fee['fee_id']]) ? $paidFeesMap[$fee['fee_id']] : false;
                            ?>
                            <div class="col-md-6 col-lg-4 mb-4">
                                <div class="card h-100 fee-card">
                                    <?php if ($isPaid): ?>
                                        <div class="card-badge">
                                            <span class="badge bg-success">Paid</span>
                                        </div>
                                    <?php endif; ?>
                                    <div class="card-body">
                                        <h5 class="card-title"><?= htmlspecialchars($fee['fee_name']) ?></h5>
                                        <p class="card-text"><?= htmlspecialchars($fee['description']) ?></p>
                                        <div class="d-flex justify-content-between align-items-center mb-3">
                                            <h4 class="text-primary mb-0">$<?= number_format($fee['amount'], 2) ?></h4>
                                            <span class="badge bg-danger">Mandatory</span>
                                        </div>
                                        <?php if (!$isPaid): ?>
                                            <button type="button" class="btn btn-primary w-100" 
                                                    data-bs-toggle="modal" 
                                                    data-bs-target="#paymentModal" 
                                                    data-fee-id="<?= $fee['fee_id'] ?>"
                                                    data-fee-name="<?= htmlspecialchars($fee['fee_name']) ?>"
                                                    data-fee-amount="<?= $fee['amount'] ?>">
                                                Pay Now
                                            </button>
                                        <?php else: ?>
                                            <button class="btn btn-success w-100" disabled>Already Paid</button>
                                        <?php endif; ?>
                                    </div>
                                </div>
                            </div>
                            <?php 
                                endif;
                            endforeach; 
                            
                            if ($mandatoryCount === 0):
                            ?>
                            <div class="col-12">
                                <div class="alert alert-info">
                                    <p class="mb-0">There are no mandatory fees at this time.</p>
                                </div>
                            </div>
                            <?php endif; ?>
                        </div>
                    </div>
                    
                    <!-- Optional Fees -->
                    <div class="col-12 fee-category">
                        <h4 class="fee-category-title">Optional Fees</h4>
                        <div class="row">
                            <?php 
                            $optionalCount = 0;
                            foreach ($publishedFees as $fee): 
                                if ($fee['is_mandatory'] == 0):
                                    $optionalCount++;
                                    $isPaid = isset($paidFeesMap[$fee['fee_id']]) ? $paidFeesMap[$fee['fee_id']] : false;
                            ?>
                            <div class="col-md-6 col-lg-4 mb-4">
                                <div class="card h-100 fee-card">
                                    <?php if ($isPaid): ?>
                                        <div class="card-badge">
                                            <span class="badge bg-success">Paid</span>
                                        </div>
                                    <?php endif; ?>
                                    <div class="card-body">
                                        <h5 class="card-title"><?= htmlspecialchars($fee['fee_name']) ?></h5>
                                        <p class="card-text"><?= htmlspecialchars($fee['description']) ?></p>
                                        <div class="d-flex justify-content-between align-items-center mb-3">
                                            <h4 class="text-primary mb-0">$<?= number_format($fee['amount'], 2) ?></h4>
                                            <span class="badge bg-info">Optional</span>
                                        </div>
                                        <?php if (!$isPaid): ?>
                                            <button type="button" class="btn btn-primary w-100" 
                                                    data-bs-toggle="modal" 
                                                    data-bs-target="#paymentModal" 
                                                    data-fee-id="<?= $fee['fee_id'] ?>"
                                                    data-fee-name="<?= htmlspecialchars($fee['fee_name']) ?>"
                                                    data-fee-amount="<?= $fee['amount'] ?>">
                                                Pay Now
                                            </button>
                                        <?php else: ?>
                                            <button class="btn btn-success w-100" disabled>Already Paid</button>
                                        <?php endif; ?>
                                    </div>
                                </div>
                            </div>
                            <?php 
                                endif;
                            endforeach; 
                            
                            if ($optionalCount === 0):
                            ?>
                            <div class="col-12">
                                <div class="alert alert-info">
                                    <p class="mb-0">There are no optional fees at this time.</p>
                                </div>
                            </div>
                            <?php endif; ?>
                        </div>
                    </div>
                <?php endif; ?>
            </div>

            <!-- Transaction History -->
            <div class="row">
                <div class="col-12">
                    <h3 class="mb-4">Transaction History</h3>
                    
                    <?php if (empty($transactions)): ?>
                        <div class="alert alert-info">
                            <p class="mb-0">You have no transaction history yet.</p>
                        </div>
                    <?php else: ?>
                        <div class="table-responsive">
                            <table class="table table-striped">
                                <thead>
                                    <tr>
                                        <th>Date</th>
                                        <th>Fee</th>
                                        <th>Amount</th>
                                        <th>Payment Method</th>
                                        <th>Reference</th>
                                        <th>Status</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php foreach ($transactions as $transaction): ?>
                                        <tr>
                                            <td><?= date('M d, Y', strtotime($transaction['payment_date'])) ?></td>
                                            <td><?= htmlspecialchars($transaction['fee_name']) ?></td>
                                            <td>$<?= number_format($transaction['amount'], 2) ?></td>
                                            <td><?= ucfirst(str_replace('_', ' ', $transaction['payment_method'])) ?></td>
                                            <td><?= htmlspecialchars($transaction['transaction_reference']) ?></td>
                                            <td>
                                                <span class="badge bg-<?= $transaction['status'] === 'completed' ? 'success' : ($transaction['status'] === 'pending' ? 'warning' : 'danger') ?>">
                                                    <?= ucfirst($transaction['status']) ?>
                                                </span>
                                            </td>
                                        </tr>
                                    <?php endforeach; ?>
                                </tbody>
                            </table>
                        </div>
                    <?php endif; ?>
                </div>
            </div>
        </div>
    </div>
    <!-- Fees Section End -->

    <!-- Payment Modal -->
    <div class="modal fade" id="paymentModal" tabindex="-1" aria-labelledby="paymentModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-lg">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="paymentModalLabel">Pay Fee</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <form id="paymentForm" method="post" action="fees.php">
                        <input type="hidden" name="token" value="<?= $_SESSION['auth_token'] ?>">
                        <input type="hidden" name="fee_id" id="feeId">
                        <input type="hidden" name="amount" id="feeAmount">
                        <input type="hidden" name="pay_fee" value="1">
                        
                        <div class="mb-4">
                            <h5 id="feeName" class="mb-2"></h5>
                            <h3 id="feeAmountDisplay" class="text-primary"></h3>
                        </div>
                        
                        <div class="mb-4">
                            <h6>Select Payment Method</h6>
                            <ul class="nav payment-methods" id="paymentTabs" role="tablist">
                                <li class="nav-item" role="presentation">
                                    <button class="nav-link active" id="credit-card-tab" data-bs-toggle="tab" data-bs-target="#credit-card" type="button" role="tab" aria-controls="credit-card" aria-selected="true" onclick="document.getElementById('payment_method').value='credit_card'">
                                        <i class="fas fa-credit-card me-2"></i> Credit Card
                                    </button>
                                </li>
                                <li class="nav-item" role="presentation">
                                    <button class="nav-link" id="paypal-tab" data-bs-toggle="tab" data-bs-target="#paypal" type="button" role="tab" aria-controls="paypal" aria-selected="false" onclick="document.getElementById('payment_method').value='paypal'">
                                        <i class="fab fa-paypal me-2"></i> PayPal
                                    </button>
                                </li>
                                <li class="nav-item" role="presentation">
                                    <button class="nav-link" id="bank-transfer-tab" data-bs-toggle="tab" data-bs-target="#bank-transfer" type="button" role="tab" aria-controls="bank-transfer" aria-selected="false" onclick="document.getElementById('payment_method').value='bank_transfer'">
                                        <i class="fas fa-university me-2"></i> Bank Transfer
                                    </button>
                                </li>
                            </ul>
                            <input type="hidden" name="payment_method" id="payment_method" value="credit_card">
                        </div>
                        
                        <div class="tab-content" id="paymentTabContent">
                            <!-- Credit Card Payment -->
                            <div class="tab-pane fade show active" id="credit-card" role="tabpanel" aria-labelledby="credit-card-tab">
                                <?php if (!empty($savedCards)): ?>
                                <div class="mb-3">
                                    <div class="form-check">
                                        <input class="form-check-input" type="checkbox" id="useSavedCard" name="use_saved_card" value="yes" onchange="toggleSavedCardForm()">
                                        <label class="form-check-label" for="useSavedCard">
                                            Use a saved card
                                        </label>
                                    </div>
                                </div>
                                
                                <div id="savedCardForm" style="display: none;">
                                    <div class="mb-3">
                                        <label for="savedCardId" class="form-label">Select Card</label>
                                        <select class="form-select" id="savedCardId" name="saved_card_id">
                                            <?php foreach ($savedCards as $card): ?>
                                            <option value="<?= $card['card_id'] ?>">
                                                <?= ucfirst($card['card_type']) ?> ending in <?= substr($card['card_number'], -4) ?> (<?= $card['card_holder'] ?>)
                                            </option>
                                            <?php endforeach; ?>
                                        </select>
                                    </div>
                                </div>
                                <?php endif; ?>
                                
                                <div id="newCardForm">
                                    <div class="row">
                                        <div class="col-md-6 mb-3">
                                            <label for="cardNumber" class="form-label">Card Number</label>
                                            <input type="text" class="form-control" id="cardNumber" name="card_number" placeholder="1234 5678 9012 3456">
                                        </div>
                                        <div class="col-md-6 mb-3">
                                            <label for="cardHolder" class="form-label">Card Holder Name</label>
                                            <input type="text" class="form-control" id="cardHolder" name="card_holder" placeholder="John Doe">
                                        </div>
                                    </div>
                                    <div class="row">
                                        <div class="col-md-4 mb-3">
                                            <label for="expiryMonth" class="form-label">Expiry Month</label>
                                            <select class="form-select" id="expiryMonth" name="expiry_month">
                                                <?php for ($i = 1; $i <= 12; $i++): ?>
                                                <option value="<?= sprintf('%02d', $i) ?>"><?= sprintf('%02d', $i) ?></option>
                                                <?php endfor; ?>
                                            </select>
                                        </div>
                                        <div class="col-md-4 mb-3">
                                            <label for="expiryYear" class="form-label">Expiry Year</label>
                                            <select class="form-select" id="expiryYear" name="expiry_year">
                                                <?php $currentYear = (int)date('Y'); ?>
                                                <?php for ($i = $currentYear; $i <= $currentYear + 10; $i++): ?>
                                                <option value="<?= $i ?>"><?= $i ?></option>
                                                <?php endfor; ?>
                                            </select>
                                        </div>
                                        <div class="col-md-4 mb-3">
                                            <label for="cvv" class="form-label">CVV</label>
                                            <input type="text" class="form-control" id="cvv" name="cvv" placeholder="123">
                                        </div>
                                    </div>
                                    <div class="mb-3">
                                        <div class="form-check">
                                            <input class="form-check-input" type="checkbox" id="saveCard" name="save_card" value="yes">
                                            <label class="form-check-label" for="saveCard">
                                                Save this card for future payments
                                            </label>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            
                            <!-- PayPal Payment -->
                            <div class="tab-pane fade" id="paypal" role="tabpanel" aria-labelledby="paypal-tab">
                                <div class="alert alert-info">
                                    <p class="mb-0">You will be redirected to PayPal to complete your payment after clicking "Pay Now".</p>
                                </div>
                            </div>
                            
                            <!-- Bank Transfer Payment -->
                            <div class="tab-pane fade" id="bank-transfer" role="tabpanel" aria-labelledby="bank-transfer-tab">
                                <div class="alert alert-info">
                                    <p class="mb-0">Please use the following bank details to make your transfer:</p>
                                </div>
                                <div class="mb-3">
                                    <p><strong>Bank Name:</strong> Global Exchange Bank</p>
                                    <p><strong>Account Name:</strong> Cultural Exchange Platform</p>
                                    <p><strong>Account Number:</strong> 1234567890</p>
                                    <p><strong>Routing Number:</strong> 987654321</p>
                                    <p><strong>Reference:</strong> <span id="bankReference"></span></p>
                                </div>
                                <div class="alert alert-warning">
                                    <p class="mb-0">Please note that bank transfers may take 2-3 business days to process.</p>
                                </div>
                            </div>
                        </div>
                        
                        <div class="mt-4">
                            <button type="submit" class="btn btn-primary btn-lg w-100">Pay Now</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>

    <!-- Footer Start -->
    <?php include '../Common/footer.php'; ?>
    <!-- Footer End -->

    <!-- JavaScript Libraries -->
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.0.0/dist/js/bootstrap.bundle.min.js"></script>
    <script src="../js/main.js"></script>
    
    <script>
        // Initialize payment modal
        document.addEventListener('DOMContentLoaded', function() {
            const paymentModal = document.getElementById('paymentModal');
            if (paymentModal) {
                paymentModal.addEventListener('show.bs.modal', function(event) {
                    const button = event.relatedTarget;
                    const feeId = button.getAttribute('data-fee-id');
                    const feeName = button.getAttribute('data-fee-name');
                    const feeAmount = button.getAttribute('data-fee-amount');

                    document.getElementById('feeId').value = feeId;
                    document.getElementById('feeAmount').value = feeAmount;
                    document.getElementById('feeName').textContent = feeName;
                    document.getElementById('feeAmountDisplay').textContent = '$' + parseFloat(feeAmount).toFixed(2);

                    // Generate a unique reference for bank transfer
                    const reference = 'TXN' + Date.now().toString(36).toUpperCase();
                    document.getElementById('bankReference').textContent = reference;
                });
            }
        });

        // Toggle saved card form
        function toggleSavedCardForm() {
            const useSavedCard = document.getElementById('useSavedCard');
            const savedCardForm = document.getElementById('savedCardForm');
            const newCardForm = document.getElementById('newCardForm');

            if (useSavedCard.checked) {
                savedCardForm.style.display = 'block';
                newCardForm.style.display = 'none';
            } else {
                savedCardForm.style.display = 'none';
                newCardForm.style.display = 'block';
            }
        }
    </script>
</body>
</html>


