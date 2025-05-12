<?php
require_once '../Controllers/OpportunityController.php';

// Set the content type to JSON
header('Content-Type: application/json');

// Check if the ID parameter is provided
if (!isset($_GET['id']) || empty($_GET['id'])) {
    echo json_encode([
        'success' => false,
        'message' => 'Opportunity ID is required'
    ]);
    exit;
}

// Get the opportunity ID
$opportunityId = (int)$_GET['id'];

// Instantiate the controller
$controller = new OpportunityController();

// Delete the opportunity
$result = $controller->deleteOpportunity($opportunityId);

// Return the result
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
?>
