<?php
/**
 * Dashboard AJAX Handler
 * 
 * This file handles AJAX requests for the admin dashboard
 */
session_start();

// Check if user is logged in and is an admin
if (!isset($_SESSION['userID']) || $_SESSION['userType'] !== 'admin') {
    header('Content-Type: application/json');
    echo json_encode(['error' => 'Unauthorized access']);
    exit;
}

// Check for CSRF token
if (!isset($_SESSION['auth_token']) || !isset($_POST['auth_token']) || $_SESSION['auth_token'] !== $_POST['auth_token']) {
    header('Content-Type: application/json');
    echo json_encode(['error' => 'Invalid security token']);
    exit;
}

// Load the AdminDashboardController
require_once '../Controllers/AdminDashboardController.php';

// Initialize the controller
$dashboardController = new AdminDashboardController();

// Set content type to JSON
header('Content-Type: application/json');

// Process the request based on the action
$action = $_GET['action'] ?? '';

switch ($action) {
    case 'get_stats':
        // Get dashboard statistics
        $stats = $dashboardController->getStats();
        echo json_encode([
            'success' => true,
            'stats' => $stats
        ]);
        break;
        
    case 'get_recent_activity':
        // Get recent activity
        $limit = isset($_GET['limit']) ? (int)$_GET['limit'] : 10;
        $recentActivity = $dashboardController->getRecentActivity($limit);
        echo json_encode([
            'success' => true,
            'recentActivity' => $recentActivity
        ]);
        break;
        
    case 'get_pending_reports':
        // Get pending reports
        $limit = isset($_GET['limit']) ? (int)$_GET['limit'] : 5;
        $pendingReports = $dashboardController->getPendingReports($limit);
        echo json_encode([
            'success' => true,
            'pendingReports' => $pendingReports
        ]);
        break;
        
    case 'get_pending_verifications':
        // Get pending payment verifications
        $limit = isset($_GET['limit']) ? (int)$_GET['limit'] : 5;
        $pendingVerifications = $dashboardController->getPendingVerifications($limit);
        echo json_encode([
            'success' => true,
            'pendingVerifications' => $pendingVerifications
        ]);
        break;
        
    case 'get_recent_opportunities':
        // Get recent opportunities
        $limit = isset($_GET['limit']) ? (int)$_GET['limit'] : 5;
        $recentOpportunities = $dashboardController->getRecentOpportunities($limit);
        echo json_encode([
            'success' => true,
            'recentOpportunities' => $recentOpportunities
        ]);
        break;
        
    case 'get_all_dashboard_data':
        // Get all dashboard data
        $dashboardData = $dashboardController->getDashboardData();
        echo json_encode([
            'success' => true,
            'dashboardData' => $dashboardData
        ]);
        break;
        
    default:
        echo json_encode([
            'success' => false,
            'message' => 'Invalid action'
        ]);
        break;
}
?>
