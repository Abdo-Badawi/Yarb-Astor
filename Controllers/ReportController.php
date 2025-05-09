<?php
/**
 * ReportController.php
 *
 * Controller for handling user reports
 */

require_once __DIR__ . '/../Models/Report.php';

class ReportController {
    private $reportModel;

    public function __construct() {
        $this->reportModel = new Models\Report();
    }

    /**
     * Get all reports with optional filtering
     *
     * @param array $filters Optional filters for the query
     * @return array|false Array of reports or false on failure
     */
    public function getAllReports($filters = []) {
        return $this->reportModel->getAllReports($filters);
    }

    /**
     * Get a report by ID
     *
     * @param int $reportId The report ID
     * @return array|false The report data or false on failure
     */
    public function getReportById($reportId) {
        return $this->reportModel->getReportById($reportId);
    }

    /**
     * Create a new report
     *
     * @param array $data The report data
     * @return int|false The new report ID or false on failure
     */
    public function createReport($data) {
        // Validate required fields
        if (empty($data['reported_by_id']) || empty($data['target_user_id']) ||
            empty($data['report_content']) || empty($data['report_type'])) {
            return false;
        }

        // Set default status if not provided
        if (!isset($data['status'])) {
            $data['status'] = 'open';
        }

        return $this->reportModel->createReport($data);
    }

    /**
     * Update a report's status
     *
     * @param int $reportId The report ID
     * @param string $status The new status
     * @return bool True on success, false on failure
     */
    public function updateReportStatus($reportId, $status) {
        // Validate status
        $validStatuses = ['open', 'reviewed', 'resolved'];
        if (!in_array($status, $validStatuses)) {
            return false;
        }

        return $this->reportModel->updateReport($reportId, ['status' => $status]);
    }

    /**
     * Add admin response to a report
     *
     * @param int $reportId The report ID
     * @param string $response The admin response
     * @return bool True on success, false on failure
     */
    public function addAdminResponse($reportId, $response) {
        if (empty($response)) {
            return false;
        }

        return $this->reportModel->updateReport($reportId, ['admin_response' => $response]);
    }

    /**
     * Mark a report as reviewed
     *
     * @param int $reportId The report ID
     * @return bool True on success, false on failure
     */
    public function markAsReviewed($reportId) {
        return $this->reportModel->markAsReviewed($reportId);
    }

    /**
     * Resolve a report
     *
     * @param int $reportId The report ID
     * @param string $adminResponse The admin response
     * @return bool True on success, false on failure
     */
    public function resolveReport($reportId, $adminResponse) {
        if (empty($adminResponse)) {
            return false;
        }

        return $this->reportModel->resolveReport($reportId, $adminResponse);
    }

    /**
     * Get report history for a user
     *
     * @param int $userId The user ID
     * @return array|false Array of reports or false on failure
     */
    public function getUserReportHistory($userId) {
        return $this->reportModel->getUserReportHistory($userId);
    }

    /**
     * Format report status for display
     *
     * @param string $status The report status
     * @return string The formatted status with CSS class
     */
    public function formatStatus($status) {
        switch ($status) {
            case 'open':
                return '<span class="status-badge status-pending">Open</span>';
            case 'reviewed':
                return '<span class="status-badge status-reviewed">Reviewed</span>';
            case 'resolved':
                return '<span class="status-badge status-resolved">Resolved</span>';
            default:
                return '<span class="status-badge">' . ucfirst($status) . '</span>';
        }
    }

    /**
     * Format report type for display
     *
     * @param string $type The report type
     * @return string The formatted type
     */
    public function formatReportType($type) {
        switch ($type) {
            case 'user':
                return 'User Behavior';
            case 'opportunity':
                return 'Opportunity Issue';
            case 'message':
                return 'Message Content';
            default:
                return ucfirst($type);
        }
    }


}
