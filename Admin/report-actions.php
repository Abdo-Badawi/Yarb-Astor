<?php
/**
 * report-actions.php
 * 
 * Handles AJAX requests for user reports
 */

require_once '../Controllers/ReportController.php';

// Initialize the controller
$reportController = new ReportController();

// Check if the request is AJAX
if (!empty($_SERVER['HTTP_X_REQUESTED_WITH']) && strtolower($_SERVER['HTTP_X_REQUESTED_WITH']) == 'xmlhttprequest') {
    // Get the action from the request
    $action = isset($_POST['action']) ? $_POST['action'] : '';
    
    // Process the action
    switch ($action) {
        case 'get_report':
            // Get report details
            if (isset($_POST['report_id'])) {
                $reportId = intval($_POST['report_id']);
                $report = $reportController->getReportById($reportId);
                
                if ($report) {
                    echo json_encode([
                        'success' => true,
                        'report' => $report
                    ]);
                } else {
                    echo json_encode([
                        'success' => false,
                        'message' => 'Report not found'
                    ]);
                }
            } else {
                echo json_encode([
                    'success' => false,
                    'message' => 'Report ID is required'
                ]);
            }
            break;
            
        case 'mark_reviewed':
            // Mark a report as reviewed
            if (isset($_POST['report_id'])) {
                $reportId = intval($_POST['report_id']);
                $result = $reportController->markAsReviewed($reportId);
                
                if ($result) {
                    echo json_encode([
                        'success' => true,
                        'message' => 'Report marked as reviewed'
                    ]);
                } else {
                    echo json_encode([
                        'success' => false,
                        'message' => 'Failed to update report status'
                    ]);
                }
            } else {
                echo json_encode([
                    'success' => false,
                    'message' => 'Report ID is required'
                ]);
            }
            break;
            
        case 'resolve_report':
            // Resolve a report
            if (isset($_POST['report_id']) && isset($_POST['admin_response'])) {
                $reportId = intval($_POST['report_id']);
                $adminResponse = $_POST['admin_response'];
                
                $result = $reportController->resolveReport($reportId, $adminResponse);
                
                if ($result) {
                    echo json_encode([
                        'success' => true,
                        'message' => 'Report resolved successfully'
                    ]);
                } else {
                    echo json_encode([
                        'success' => false,
                        'message' => 'Failed to resolve report'
                    ]);
                }
            } else {
                echo json_encode([
                    'success' => false,
                    'message' => 'Report ID and admin response are required'
                ]);
            }
            break;
            
        case 'get_user_history':
            // Get report history for a user
            if (isset($_POST['user_id'])) {
                $userId = intval($_POST['user_id']);
                $history = $reportController->getUserReportHistory($userId);
                
                if ($history) {
                    echo json_encode([
                        'success' => true,
                        'history' => $history
                    ]);
                } else {
                    echo json_encode([
                        'success' => false,
                        'message' => 'No history found or error occurred'
                    ]);
                }
            } else {
                echo json_encode([
                    'success' => false,
                    'message' => 'User ID is required'
                ]);
            }
            break;
            
        case 'filter_reports':
            // Filter reports
            $filters = [];
            
            if (isset($_POST['status']) && !empty($_POST['status'])) {
                $filters['status'] = $_POST['status'];
            }
            
            if (isset($_POST['report_type']) && !empty($_POST['report_type'])) {
                $filters['report_type'] = $_POST['report_type'];
            }
            
            if (isset($_POST['user_type']) && !empty($_POST['user_type'])) {
                $filters['user_type'] = $_POST['user_type'];
            }
            
            if (isset($_POST['date']) && !empty($_POST['date'])) {
                $filters['date'] = $_POST['date'];
            }
            
            $reports = $reportController->getAllReports($filters);
            
            if ($reports) {
                echo json_encode([
                    'success' => true,
                    'reports' => $reports
                ]);
            } else {
                echo json_encode([
                    'success' => false,
                    'message' => 'No reports found or error occurred'
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
} else {
    // Not an AJAX request
    echo json_encode([
        'success' => false,
        'message' => 'Invalid request'
    ]);
}
