<?php
namespace Models;

require_once __DIR__ . '/Database.php';

class Report {
    private $db;

    public $report_id;
    public $reported_by_id;
    public $target_user_id;
    public $report_content;
    public $status; // Enum: 'open', 'reviewed', 'resolved'
    public $report_type; // Enum: 'user', 'opportunity', 'message'
    public $admin_response;
    public $created_at;
    public $updated_at;

    public function __construct() {
        $this->db = new \Database();
    }

    /**
     * Get all reports
     *
     * @param array $filters Optional filters for the query
     * @return array|false Array of reports or false on failure
     */
    public function getAllReports($filters = []) {
        $sql = "SELECT r.*,
                u1.first_name as reporter_first_name, u1.last_name as reporter_last_name, u1.user_type as reporter_type,
                u2.first_name as target_first_name, u2.last_name as target_last_name, u2.user_type as target_type
                FROM report r
                LEFT JOIN users u1 ON r.reported_by_id = u1.user_id
                LEFT JOIN users u2 ON r.target_user_id = u2.user_id";

        $whereConditions = [];
        $params = [];

        // Apply filters
        if (!empty($filters['status'])) {
            $whereConditions[] = "r.status = ?";
            $params[] = $filters['status'];
        }

        if (!empty($filters['report_type'])) {
            $whereConditions[] = "r.report_type = ?";
            $params[] = $filters['report_type'];
        }

        if (!empty($filters['user_type'])) {
            $whereConditions[] = "u2.user_type = ?";
            $params[] = $filters['user_type'];
        }

        if (!empty($filters['date'])) {
            $whereConditions[] = "DATE(r.created_at) = ?";
            $params[] = $filters['date'];
        }

        // Add WHERE clause if there are conditions
        if (!empty($whereConditions)) {
            $sql .= " WHERE " . implode(" AND ", $whereConditions);
        }

        $sql .= " ORDER BY r.created_at DESC";

        return $this->db->select($sql, $params);
    }

    /**
     * Get a report by ID
     *
     * @param int $reportId The report ID
     * @return array|false The report data or false on failure
     */
    public function getReportById($reportId) {
        $sql = "SELECT r.*,
                u1.first_name as reporter_first_name, u1.last_name as reporter_last_name, u1.user_type as reporter_type, u1.email as reporter_email, u1.profile_picture as reporter_profile_picture,
                u2.first_name as target_first_name, u2.last_name as target_last_name, u2.user_type as target_type, u2.email as target_email, u2.profile_picture as target_profile_picture
                FROM report r
                LEFT JOIN users u1 ON r.reported_by_id = u1.user_id
                LEFT JOIN users u2 ON r.target_user_id = u2.user_id
                WHERE r.report_id = ?";

        $result = $this->db->select($sql, [$reportId]);

        return $result ? $result[0] : false;
    }

    /**
     * Create a new report
     *
     * @param array $data The report data
     * @return int|false The new report ID or false on failure
     */
    public function createReport($data) {
        $sql = "INSERT INTO report (reported_by_id, target_user_id, report_content, status, report_type)
                VALUES (?, ?, ?, ?, ?)";

        $params = [
            $data['reported_by_id'],
            $data['target_user_id'],
            $data['report_content'],
            $data['status'] ?? 'open',
            $data['report_type']
        ];

        return $this->db->insert($sql, $params);
    }

    /**
     * Update a report
     *
     * @param int $reportId The report ID
     * @param array $data The updated report data
     * @return bool True on success, false on failure
     */
    public function updateReport($reportId, $data) {
        $sql = "UPDATE report SET ";
        $params = [];

        // Build the SET clause dynamically based on provided data
        $setClauses = [];

        if (isset($data['status'])) {
            $setClauses[] = "status = ?";
            $params[] = $data['status'];
        }

        if (isset($data['admin_response'])) {
            $setClauses[] = "admin_response = ?";
            $params[] = $data['admin_response'];
        }

        // Add more fields as needed

        $sql .= implode(", ", $setClauses);
        $sql .= " WHERE report_id = ?";
        $params[] = $reportId;

        return $this->db->update($sql, $params);
    }

    /**
     * Get report history for a user
     *
     * @param int $userId The user ID
     * @return array|false Array of reports or false on failure
     */
    public function getUserReportHistory($userId) {
        $sql = "SELECT * FROM report WHERE target_user_id = ? ORDER BY created_at DESC";

        return $this->db->select($sql, [$userId]);
    }

    /**
     * Mark a report as reviewed
     *
     * @param int $reportId The report ID
     * @return bool True on success, false on failure
     */
    public function markAsReviewed($reportId) {
        return $this->updateReport($reportId, ['status' => 'reviewed']);
    }

    /**
     * Resolve a report
     *
     * @param int $reportId The report ID
     * @param string $adminResponse The admin response
     * @return bool True on success, false on failure
     */
    public function resolveReport($reportId, $adminResponse) {
        return $this->updateReport($reportId, [
            'status' => 'resolved',
            'admin_response' => $adminResponse
        ]);
    }
}