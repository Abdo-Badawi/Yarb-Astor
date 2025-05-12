<?php
session_start();

// Check if user is logged in and is an admin
if (!isset($_SESSION['userID']) || $_SESSION['userType'] !== 'admin') {
    echo json_encode([
        'success' => false,
        'message' => 'Unauthorized access'
    ]);
    exit;
}

// Load required controllers
require_once '../Controllers/PaymentVerificationRequestController.php';
require_once '../Controllers/FeeTransactionController.php';
require_once '../Controllers/DBController.php';

// Instantiate controllers
$verificationController = new PaymentVerificationRequestController();
$feeController = new FeeTransactionController();
$db = new DBController();

// Get the action from the request
$action = $_GET['action'] ?? '';

// Process the action
switch ($action) {
    case 'get':
        // Get request ID from request
        $requestId = $_GET['id'] ?? 0;

        if (empty($requestId)) {
            echo json_encode([
                'success' => false,
                'message' => 'Request ID is required'
            ]);
            break;
        }

        // Get the request using the controller
        $request = $verificationController->getRequestById($requestId);

        if ($request) {
            echo json_encode([
                'success' => true,
                'request' => $request
            ]);
        } else {
            echo json_encode([
                'success' => false,
                'message' => 'Request not found'
            ]);
        }
        break;

    case 'create':
        // Get request data from POST
        $data = json_decode(file_get_contents('php://input'), true);

        if (empty($data)) {
            echo json_encode([
                'success' => false,
                'message' => 'No data provided'
            ]);
            break;
        }

        // Create the request using the controller
        $requestId = $verificationController->createRequest($data);

        if ($requestId) {
            echo json_encode([
                'success' => true,
                'request_id' => $requestId,
                'message' => 'Request created successfully'
            ]);
        } else {
            echo json_encode([
                'success' => false,
                'message' => 'Failed to create request'
            ]);
        }
        break;

    case 'update':
        // Get request ID from request
        $requestId = $_GET['id'] ?? 0;

        if (empty($requestId)) {
            echo json_encode([
                'success' => false,
                'message' => 'Request ID is required'
            ]);
            break;
        }

        // Get request data from POST
        $data = json_decode(file_get_contents('php://input'), true);

        if (empty($data)) {
            echo json_encode([
                'success' => false,
                'message' => 'No data provided'
            ]);
            break;
        }

        // Update the request using the controller
        $result = $verificationController->updateRequest($requestId, $data);

        if ($result) {
            // Get the updated request to return
            $request = $verificationController->getRequestById($requestId);
            
            echo json_encode([
                'success' => true,
                'request' => $request,
                'message' => 'Request updated successfully'
            ]);
        } else {
            echo json_encode([
                'success' => false,
                'message' => 'Failed to update request'
            ]);
        }
        break;

    case 'delete':
        // Get request ID from request
        $requestId = $_GET['id'] ?? 0;

        if (empty($requestId)) {
            echo json_encode([
                'success' => false,
                'message' => 'Request ID is required'
            ]);
            break;
        }

        // Delete the request using the controller
        $result = $verificationController->deleteRequest($requestId);

        if ($result) {
            echo json_encode([
                'success' => true,
                'message' => 'Request deleted successfully'
            ]);
        } else {
            echo json_encode([
                'success' => false,
                'message' => 'Failed to delete request'
            ]);
        }
        break;

    case 'update-status':
        // Get request ID and status from request
        $requestId = $_GET['id'] ?? 0;
        $status = $_GET['status'] ?? '';

        if (empty($requestId) || empty($status)) {
            echo json_encode([
                'success' => false,
                'message' => 'Request ID and status are required'
            ]);
            break;
        }

        // Update the request status
        $data = ['status' => $status];
        $result = $verificationController->updateRequest($requestId, $data);

        if ($result) {
            // Get the updated request
            $request = $verificationController->getRequestById($requestId);
            
            echo json_encode([
                'success' => true,
                'request' => $request,
                'message' => 'Request status updated successfully'
            ]);
        } else {
            echo json_encode([
                'success' => false,
                'message' => 'Failed to update request status'
            ]);
        }
        break;

    default:
        echo json_encode([
            'success' => false,
            'message' => 'Invalid action'
        ]);
        break;
}

