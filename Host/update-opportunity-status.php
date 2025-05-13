<?php
session_start();
// Check if user is logged in and is a host
if (!isset($_SESSION['userID']) || $_SESSION['userType'] !== 'host') {
    header("Location: ../Common/login.php");
    exit;
}

require_once '../Controllers/OpportunityController.php';

// Check if opportunity ID and status are provided
if (!isset($_GET['id']) || !is_numeric($_GET['id']) || !isset($_GET['status'])) {
    echo json_encode(['success' => false, 'error' => 'Invalid parameters.']);
    exit;
}

$opportunityId = (int)$_GET['id'];
$status = $_GET['status'];
$hostId = $_SESSION['userID'];

// Validate status
$validStatuses = ['open', 'closed', 'cancelled'];
if (!in_array($status, $validStatuses)) {
    echo json_encode(['success' => false, 'error' => 'Invalid status.']);
    exit;
}

// Create opportunity controller
$opportunityController = new OpportunityController();

// Get opportunity to verify ownership
$opportunity = $opportunityController->getOppById($opportunityId);

// Check if opportunity exists and belongs to the current host
if (!$opportunity || $opportunity['host_id'] != $hostId) {
    echo json_encode(['success' => false, 'error' => 'You do not have permission to update this opportunity.']);
    exit;
}

// Update the opportunity status
$result = $opportunityController->updateOppStatus($opportunityId, $status);

if ($result) {
    echo json_encode(['success' => true]);
} else {
    echo json_encode(['success' => false, 'error' => 'Failed to update the opportunity status.']);
}
?>
