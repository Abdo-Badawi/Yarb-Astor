<?php
session_start();
// Check if user is logged in and is a host
if (!isset($_SESSION['userID']) || $_SESSION['userType'] !== 'host') {
    header("Location: ../Common/login.php");
    exit;
}

require_once '../Controllers/OpportunityController.php';

// Set content type to JSON
header('Content-Type: application/json');

// Check if opportunity ID is provided
if (!isset($_GET['id']) || !is_numeric($_GET['id'])) {
    echo json_encode(['success' => false, 'error' => 'Invalid opportunity ID.']);
    exit;
}

$opportunityId = (int)$_GET['id'];
$hostId = $_SESSION['userID'];

// Create opportunity controller
$opportunityController = new OpportunityController();

// Get opportunity to verify ownership
$opportunity = $opportunityController->getOppById($opportunityId);

// Check if opportunity exists and belongs to the current host
if (!$opportunity || $opportunity['host_id'] != $hostId) {
    echo json_encode(['success' => false, 'error' => 'You do not have permission to delete this opportunity.']);
    exit;
}

// Delete the opportunity
$result = $opportunityController->deleteOpportunity($opportunityId);

if ($result) {
    // Log the deletion for audit purposes
    error_log("Opportunity ID: $opportunityId deleted by Host ID: $hostId at " . date('Y-m-d H:i:s'));
    
    echo json_encode([
        'success' => true, 
        'message' => 'Opportunity deleted successfully.'
    ]);
} else {
    echo json_encode([
        'success' => false, 
        'error' => 'Failed to delete the opportunity. Please try again.'
    ]);
}
?>




