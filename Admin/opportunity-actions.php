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
require_once '../Controllers/OpportunityController.php';

// Instantiate controllers
$opportunityController = new OpportunityController();

// Get the action from the request
$action = $_GET['action'] ?? '';

// Process the action
switch ($action) {
    case 'get-all':
        // Get all opportunities
        $opportunities = $opportunityController->getAllOpportunities();
        
        echo json_encode([
            'success' => true,
            'opportunities' => $opportunities
        ]);
        break;

    case 'get':
        // Get opportunity ID from request
        $opportunityId = $_GET['id'] ?? 0;

        if (empty($opportunityId)) {
            echo json_encode([
                'success' => false,
                'message' => 'Opportunity ID is required'
            ]);
            break;
        }

        // Get the opportunity using the controller
        $opportunity = $opportunityController->getOpportunityById($opportunityId);

        if ($opportunity) {
            echo json_encode([
                'success' => true,
                'opportunity' => $opportunity
            ]);
        } else {
            echo json_encode([
                'success' => false,
                'message' => 'Opportunity not found'
            ]);
        }
        break;

    case 'delete':
        // Get opportunity ID from request
        $opportunityId = $_GET['id'] ?? 0;

        if (empty($opportunityId)) {
            echo json_encode([
                'success' => false,
                'message' => 'Opportunity ID is required'
            ]);
            break;
        }

        // Delete the opportunity using the controller
        $result = $opportunityController->deleteOpportunity($opportunityId);

        if ($result) {
            echo json_encode([
                'success' => true,
                'message' => 'Opportunity deleted successfully'
            ]);
        } else {
            echo json_encode([
                'success' => false,
                'message' => 'Failed to delete opportunity'
            ]);
        }
        break;

    case 'update-status':
        // Get opportunity ID and status from request
        $opportunityId = $_GET['id'] ?? 0;
        $status = $_GET['status'] ?? '';

        if (empty($opportunityId) || empty($status)) {
            echo json_encode([
                'success' => false,
                'message' => 'Opportunity ID and status are required'
            ]);
            break;
        }

        // Update the opportunity status
        $result = $opportunityController->updateOpportunityStatus($opportunityId, $status);

        if ($result) {
            echo json_encode([
                'success' => true,
                'message' => 'Opportunity status updated successfully'
            ]);
        } else {
            echo json_encode([
                'success' => false,
                'message' => 'Failed to update opportunity status'
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