<?php
session_start();
// Check if user is logged in and is a host
if (!isset($_SESSION['userID']) || $_SESSION['userType'] !== 'host') {
    header("Location: ../Common/login.php");
    exit;
}

require_once '../Controllers/OpportunityController.php';

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
$opportunity = $opportunityController->getOpportunityById($opportunityId);

// Check if opportunity exists and belongs to the current host
if (!$opportunity || $opportunity['host_id'] != $hostId) {
    echo json_encode(['success' => false, 'error' => 'You do not have permission to delete this opportunity.']);
    exit;
}

// Delete the opportunity
$result = $opportunityController->deleteOpportunity($opportunityId);

if ($result) {
    echo json_encode(['success' => true]);
} else {
    echo json_encode(['success' => false, 'error' => 'Failed to delete the opportunity.']);
}
?>


