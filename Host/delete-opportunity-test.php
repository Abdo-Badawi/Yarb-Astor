<?php
// Disable error output to the browser
ini_set('display_errors', 0);
error_reporting(E_ALL);

// Start output buffering to catch any unexpected output
ob_start();

session_start();
// Check if user is logged in and is a host
if (!isset($_SESSION['userID']) || $_SESSION['userType'] !== 'host') {
    // Clear any buffered output
    ob_end_clean();
    
    header('Content-Type: application/json');
    echo json_encode(['success' => false, 'error' => 'Unauthorized access']);
    exit;
}

require_once '../Controllers/OpportunityController.php';

// Set content type to JSON
header('Content-Type: application/json');

// Check if opportunity ID is provided
if (!isset($_GET['id']) || !is_numeric($_GET['id'])) {
    // Clear any buffered output
    ob_end_clean();
    
    echo json_encode(['success' => false, 'error' => 'Invalid opportunity ID.']);
    exit;
}

$opportunityId = (int)$_GET['id'];
$hostId = $_SESSION['userID'];

try {
    // Create opportunity controller
    $opportunityController = new OpportunityController();
    
    // Get opportunity to verify ownership
    $opportunity = $opportunityController->getOpportunityById($opportunityId);
    
    // Clear any buffered output
    ob_end_clean();
    
    // Return information about the opportunity for debugging
    echo json_encode([
        'success' => true,
        'message' => 'Test response with opportunity data',
        'opportunity_id' => $opportunityId,
        'host_id' => $hostId,
        'opportunity_exists' => ($opportunity ? true : false),
        'opportunity_owner' => ($opportunity ? $opportunity['host_id'] : 'unknown')
    ]);
    
} catch (Exception $e) {
    // Clear any buffered output
    ob_end_clean();
    
    echo json_encode([
        'success' => false,
        'error' => 'Exception: ' . $e->getMessage()
    ]);
}
?>