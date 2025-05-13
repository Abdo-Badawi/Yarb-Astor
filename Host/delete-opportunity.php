<?php
// Start session and include required files
session_start();
require_once '../Controllers/OpportunityController.php';

// Set content type to JSON
header('Content-Type: application/json');

// Check if user is logged in and is a host
if (!isset($_SESSION['userID']) || $_SESSION['userType'] !== 'host') {
    echo json_encode(['success' => false, 'error' => 'Unauthorized access']);
    exit;
}

// Check if opportunity ID is provided
if (!isset($_GET['id']) || !is_numeric($_GET['id'])) {
    echo json_encode(['success' => false, 'error' => 'Invalid opportunity ID']);
    exit;
}

$opportunityId = (int)$_GET['id'];
$hostId = $_SESSION['userID'];

try {
    // Create controller instance
    $opportunityController = new OpportunityController();
    
    // Get opportunity to verify ownership
    $opportunity = $opportunityController->getOpportunityById($opportunityId);
    
    // Check if opportunity exists
    if (!$opportunity) {
        echo json_encode(['success' => false, 'error' => 'Opportunity not found']);
        exit;
    }
    
    // Check if opportunity belongs to the current host
    if ($opportunity['host_id'] != $hostId) {
        echo json_encode(['success' => false, 'error' => 'You do not have permission to delete this opportunity']);
        exit;
    }
    
    // Delete the opportunity using the controller
    $result = $opportunityController->deleteOpportunity($opportunityId);
    
    if ($result) {
        echo json_encode(['success' => true, 'message' => 'Opportunity deleted successfully']);
    } else {
        echo json_encode(['success' => false, 'error' => 'Failed to delete opportunity']);
    }
} catch (Exception $e) {
    // Log the error
    error_log("Error in delete-opportunity.php: " . $e->getMessage());
    echo json_encode(['success' => false, 'error' => 'An error occurred: ' . $e->getMessage()]);
}
?>
