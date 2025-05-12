<?php
// Set headers for JSON response
header('Content-Type: application/json');

// Include required files
require_once '../Controllers/FeeController.php';

// Initialize controllers
$feeController = new FeeController();

// Get the action from the request
$action = $_GET['action'] ?? '';

// Process the request based on the action
switch ($action) {
    case 'get-all':
        // Get all fees
        $fees = $feeController->getAllFees();
        
        echo json_encode([
            'success' => true,
            'fees' => $fees
        ]);
        break;

    case 'get':
        // Get a specific fee by ID
        $feeId = isset($_GET['id']) ? (int)$_GET['id'] : 0;
        
        if ($feeId <= 0) {
            echo json_encode([
                'success' => false,
                'message' => 'Invalid fee ID'
            ]);
            break;
        }
        
        $fee = $feeController->getFeeById($feeId);
        
        if ($fee) {
            echo json_encode([
                'success' => true,
                'fee' => $fee
            ]);
        } else {
            echo json_encode([
                'success' => false,
                'message' => 'Fee not found'
            ]);
        }
        break;

    case 'create':
        // Create a new fee
        $data = json_decode(file_get_contents('php://input'), true);
        
        if (!$data) {
            echo json_encode([
                'success' => false,
                'message' => 'Invalid request data'
            ]);
            break;
        }
        
        // Validate required fields
        if (empty($data['fee_name']) || !isset($data['amount'])) {
            echo json_encode([
                'success' => false,
                'message' => 'Fee name and amount are required'
            ]);
            break;
        }
        
        // Set created_by to the current admin user (for demo, using user ID 1)
        $data['created_by'] = 1; // This would typically come from the session
        
        $feeId = $feeController->createFee($data);
        
        if ($feeId > 0) {
            $fee = $feeController->getFeeById($feeId);
            echo json_encode([
                'success' => true,
                'message' => 'Fee created successfully',
                'fee' => $fee
            ]);
        } else {
            echo json_encode([
                'success' => false,
                'message' => 'Failed to create fee'
            ]);
        }
        break;

    case 'update':
        // Update an existing fee
        $feeId = isset($_GET['id']) ? (int)$_GET['id'] : 0;
        $data = json_decode(file_get_contents('php://input'), true);
        
        if ($feeId <= 0 || !$data) {
            echo json_encode([
                'success' => false,
                'message' => 'Invalid request data'
            ]);
            break;
        }
        
        $result = $feeController->updateFee($feeId, $data);
        
        if ($result) {
            $fee = $feeController->getFeeById($feeId);
            echo json_encode([
                'success' => true,
                'message' => 'Fee updated successfully',
                'fee' => $fee
            ]);
        } else {
            echo json_encode([
                'success' => false,
                'message' => 'Failed to update fee'
            ]);
        }
        break;

    case 'delete':
        // Delete a fee
        $feeId = isset($_GET['id']) ? (int)$_GET['id'] : 0;
        
        if ($feeId <= 0) {
            echo json_encode([
                'success' => false,
                'message' => 'Invalid fee ID'
            ]);
            break;
        }
        
        $result = $feeController->deleteFee($feeId);
        
        if ($result) {
            echo json_encode([
                'success' => true,
                'message' => 'Fee deleted successfully'
            ]);
        } else {
            echo json_encode([
                'success' => false,
                'message' => 'Failed to delete fee'
            ]);
        }
        break;

    case 'assign':
        // Assign a fee to a traveler
        $data = json_decode(file_get_contents('php://input'), true);
        
        if (!$data || !isset($data['fee_id']) || !isset($data['traveler_id'])) {
            echo json_encode([
                'success' => false,
                'message' => 'Invalid request data'
            ]);
            break;
        }
        
        $feeId = (int)$data['fee_id'];
        $travelerId = (int)$data['traveler_id'];
        $dueDate = $data['due_date'] ?? null;
        
        $assignmentId = $feeController->assignFeeToTraveler($feeId, $travelerId, $dueDate);
        
        if ($assignmentId > 0) {
            echo json_encode([
                'success' => true,
                'message' => 'Fee assigned to traveler successfully',
                'assignment_id' => $assignmentId
            ]);
        } else {
            echo json_encode([
                'success' => false,
                'message' => 'Failed to assign fee to traveler'
            ]);
        }
        break;

    case 'get-by-traveler':
        // Get fees assigned to a traveler
        $travelerId = isset($_GET['traveler_id']) ? (int)$_GET['traveler_id'] : 0;
        
        if ($travelerId <= 0) {
            echo json_encode([
                'success' => false,
                'message' => 'Invalid traveler ID'
            ]);
            break;
        }
        
        $fees = $feeController->getFeesByTravelerId($travelerId);
        
        echo json_encode([
            'success' => true,
            'fees' => $fees
        ]);
        break;

    case 'get-by-status':
        // Get fees by status
        $status = isset($_GET['status']) ? $_GET['status'] : '';
        
        if (empty($status)) {
            echo json_encode([
                'success' => false,
                'message' => 'Status parameter is required'
            ]);
            break;
        }
        
        $fees = $feeController->getFeesByStatus($status);
        
        echo json_encode([
            'success' => true,
            'fees' => $fees
        ]);
        break;

    default:
        echo json_encode([
            'success' => false,
            'message' => 'Invalid action'
        ]);
        break;
}
