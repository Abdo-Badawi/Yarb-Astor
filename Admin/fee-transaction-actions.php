<?php
require_once '../Controllers/FeeTransactionController.php';

// Set content type to JSON
header('Content-Type: application/json');

// Check if action is provided
if (!isset($_GET['action'])) {
    echo json_encode([
        'success' => false,
        'message' => 'No action specified'
    ]);
    exit;
}

// Instantiate the controller
$controller = new FeeTransactionController();

// Get the action
$action = $_GET['action'];

// Handle different actions
switch ($action) {
    case 'get':
        // Get transaction by ID
        if (!isset($_GET['id'])) {
            echo json_encode([
                'success' => false,
                'message' => 'Transaction ID is required'
            ]);
            exit;
        }

        $transactionId = (int)$_GET['id'];
        $transaction = $controller->getTransactionById($transactionId);

        if ($transaction) {
            echo json_encode([
                'success' => true,
                'transaction' => $transaction
            ]);
        } else {
            echo json_encode([
                'success' => false,
                'message' => 'Transaction not found'
            ]);
        }
        break;

    case 'create':
        // Create a new transaction
        $data = json_decode(file_get_contents('php://input'), true);

        if (!$data) {
            echo json_encode([
                'success' => false,
                'message' => 'Invalid data provided'
            ]);
            exit;
        }

        // Validate required fields
        if (empty($data['traveler_id']) || !isset($data['amount'])) {
            echo json_encode([
                'success' => false,
                'message' => 'Traveler ID and amount are required'
            ]);
            exit;
        }

        // Add transaction reference if not provided
        if (!isset($data['transaction_reference'])) {
            $data['transaction_reference'] = 'TRX-' . time();
        }

        // Set date if not provided
        if (!isset($data['date'])) {
            $data['date'] = date('Y-m-d');
        }

        $result = $controller->createTransaction($data);

        if ($result) {
            echo json_encode([
                'success' => true,
                'message' => 'Transaction created successfully'
            ]);
        } else {
            echo json_encode([
                'success' => false,
                'message' => 'Failed to create transaction'
            ]);
        }
        break;

    case 'update-status':
        // Update transaction status
        if (!isset($_GET['id']) || !isset($_GET['status'])) {
            echo json_encode([
                'success' => false,
                'message' => 'Transaction ID and status are required'
            ]);
            exit;
        }

        $transactionId = (int)$_GET['id'];
        $status = $_GET['status'];

        // Validate status
        $validStatuses = ['pending', 'completed', 'failed'];
        if (!in_array($status, $validStatuses)) {
            echo json_encode([
                'success' => false,
                'message' => 'Invalid status. Valid statuses are: ' . implode(', ', $validStatuses)
            ]);
            exit;
        }

        $result = $controller->updateTransactionStatus($transactionId, $status);

        if ($result) {
            echo json_encode([
                'success' => true,
                'message' => 'Transaction status updated successfully'
            ]);
        } else {
            echo json_encode([
                'success' => false,
                'message' => 'Failed to update transaction status'
            ]);
        }
        break;

    case 'update-reference':
        // Update transaction reference
        if (!isset($_GET['id'])) {
            echo json_encode([
                'success' => false,
                'message' => 'Transaction ID is required'
            ]);
            exit;
        }

        $transactionId = (int)$_GET['id'];
        $data = json_decode(file_get_contents('php://input'), true);

        if (!$data || !isset($data['transaction_reference'])) {
            echo json_encode([
                'success' => false,
                'message' => 'Transaction reference is required'
            ]);
            exit;
        }

        $reference = $data['transaction_reference'];

        // Add a method to update transaction reference
        $sql = "UPDATE fee_transaction SET transaction_reference = ? WHERE fee_id = ?";

        $db = new DBController();
        $db->openConnection();
        $result = $db->update($sql, "si", [$reference, $transactionId]);
        $db->closeConnection();

        if ($result) {
            echo json_encode([
                'success' => true,
                'message' => 'Transaction reference updated successfully'
            ]);
        } else {
            echo json_encode([
                'success' => false,
                'message' => 'Failed to update transaction reference'
            ]);
        }
        break;

    case 'delete':
        // Delete a transaction
        if (!isset($_GET['id'])) {
            echo json_encode([
                'success' => false,
                'message' => 'Transaction ID is required'
            ]);
            exit;
        }

        $transactionId = (int)$_GET['id'];
        $result = $controller->deleteTransaction($transactionId);

        if ($result) {
            echo json_encode([
                'success' => true,
                'message' => 'Transaction deleted successfully'
            ]);
        } else {
            echo json_encode([
                'success' => false,
                'message' => 'Failed to delete transaction'
            ]);
        }
        break;

    case 'get-all':
        // Get all transactions
        $transactions = $controller->getAllTransactions();

        echo json_encode([
            'success' => true,
            'transactions' => $transactions
        ]);
        break;

    case 'get-by-traveler':
        // Get transactions by traveler ID
        if (!isset($_GET['traveler_id'])) {
            echo json_encode([
                'success' => false,
                'message' => 'Traveler ID is required'
            ]);
            exit;
        }

        $travelerId = (int)$_GET['traveler_id'];
        $transactions = $controller->getTransactionsByTravelerId($travelerId);

        echo json_encode([
            'success' => true,
            'transactions' => $transactions
        ]);
        break;

    case 'get-by-status':
        // Get transactions by status
        if (!isset($_GET['status'])) {
            echo json_encode([
                'success' => false,
                'message' => 'Status is required'
            ]);
            exit;
        }

        $status = $_GET['status'];
        $transactions = $controller->getTransactionsByStatus($status);

        echo json_encode([
            'success' => true,
            'transactions' => $transactions
        ]);
        break;

    case 'get-by-fee-type':
        // Get transactions by fee type
        if (!isset($_GET['fee_type'])) {
            echo json_encode([
                'success' => false,
                'message' => 'Fee type is required'
            ]);
            exit;
        }

        $feeType = $_GET['fee_type'];
        $transactions = $controller->getTransactionsByFeeType($feeType);

        echo json_encode([
            'success' => true,
            'transactions' => $transactions
        ]);
        break;

    case 'get-by-date-range':
        // Get transactions by date range
        if (!isset($_GET['start_date']) || !isset($_GET['end_date'])) {
            echo json_encode([
                'success' => false,
                'message' => 'Start date and end date are required'
            ]);
            exit;
        }

        $startDate = $_GET['start_date'];
        $endDate = $_GET['end_date'];
        $transactions = $controller->getTransactionsByDateRange($startDate, $endDate);

        echo json_encode([
            'success' => true,
            'transactions' => $transactions
        ]);
        break;

    case 'search':
        // Search transactions with multiple criteria
        $whereConditions = [];
        $params = [];
        $types = '';

        // Transaction ID
        if (isset($_GET['transaction_id']) && !empty($_GET['transaction_id'])) {
            $whereConditions[] = "(ft.transaction_reference LIKE ? OR ft.fee_id = ?)";
            $transactionId = trim($_GET['transaction_id']);
            $transactionId = str_replace('TRX-', '', $transactionId); // Remove TRX- prefix if present
            $params[] = '%' . $transactionId . '%';
            $params[] = (int)$transactionId;
            $types .= 'si';
        }

        // Traveler name
        if (isset($_GET['traveler_name']) && !empty($_GET['traveler_name'])) {
            $whereConditions[] = "(u.first_name LIKE ? OR u.last_name LIKE ? OR CONCAT(u.first_name, ' ', u.last_name) LIKE ?)";
            $travelerName = '%' . trim($_GET['traveler_name']) . '%';
            $params[] = $travelerName;
            $params[] = $travelerName;
            $params[] = $travelerName;
            $types .= 'sss';
        }

        // Payment method
        if (isset($_GET['payment_method']) && !empty($_GET['payment_method'])) {
            $whereConditions[] = "ft.payment_method = ?";
            $params[] = $_GET['payment_method'];
            $types .= 's';
        }

        // Status
        if (isset($_GET['status']) && !empty($_GET['status'])) {
            $whereConditions[] = "ft.status = ?";
            $params[] = $_GET['status'];
            $types .= 's';
        }

        // Card number (last 4 digits)
        if (isset($_GET['card_number']) && !empty($_GET['card_number'])) {
            $whereConditions[] = "c.card_number LIKE ?";
            $params[] = '%' . trim($_GET['card_number']);
            $types .= 's';
        }

        // Date range
        if (isset($_GET['date_range']) && !empty($_GET['date_range'])) {
            $today = date('Y-m-d');

            switch ($_GET['date_range']) {
                case 'today':
                    $whereConditions[] = "DATE(ft.date) = ?";
                    $params[] = $today;
                    $types .= 's';
                    break;
                case 'week':
                    $whereConditions[] = "DATE(ft.date) >= DATE_SUB(?, INTERVAL 7 DAY)";
                    $params[] = $today;
                    $types .= 's';
                    break;
                case 'month':
                    $whereConditions[] = "DATE(ft.date) >= DATE_SUB(?, INTERVAL 1 MONTH)";
                    $params[] = $today;
                    $types .= 's';
                    break;
                case 'quarter':
                    $whereConditions[] = "DATE(ft.date) >= DATE_SUB(?, INTERVAL 3 MONTH)";
                    $params[] = $today;
                    $types .= 's';
                    break;
                case 'year':
                    $whereConditions[] = "DATE(ft.date) >= DATE_SUB(?, INTERVAL 1 YEAR)";
                    $params[] = $today;
                    $types .= 's';
                    break;
            }
        }

        // Build the query
        $sql = "SELECT ft.*, u.first_name, u.last_name,
                CONCAT(u.first_name, ' ', u.last_name) as traveler_name
                FROM fee_transaction ft
                LEFT JOIN users u ON ft.traveler_id = u.user_id
                LEFT JOIN card c ON ft.traveler_id = c.traveler_id";

        // Add WHERE clause if there are conditions
        if (!empty($whereConditions)) {
            $sql .= " WHERE " . implode(" AND ", $whereConditions);
        }

        // Add ORDER BY
        $sql .= " ORDER BY ft.date DESC";

        // Execute the query
        require_once '../Controllers/DBController.php';
        $db = new DBController();
        $db->openConnection();

        if (empty($params)) {
            $transactions = $db->select($sql);
        } else {
            $transactions = $db->selectPrepared($sql, $types, $params);
        }

        $db->closeConnection();

        // Format the results
        $formattedTransactions = [];
        if ($transactions) {
            foreach ($transactions as $transaction) {
                // Add any additional fields or formatting here
                $transaction['transaction_id'] = $transaction['fee_id'];
                $transaction['currency'] = isset($transaction['currency']) ? $transaction['currency'] : 'USD';
                $transaction['fee_type'] = isset($transaction['fee_type']) ? $transaction['fee_type'] : 'Payment';

                $formattedTransactions[] = $transaction;
            }
        }

        echo json_encode([
            'success' => true,
            'transactions' => $formattedTransactions
        ]);
        break;

    default:
        echo json_encode([
            'success' => false,
            'message' => 'Invalid action'
        ]);
        break;
}
