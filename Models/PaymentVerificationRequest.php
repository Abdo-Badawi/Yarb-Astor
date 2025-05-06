<?php
require_once __DIR__ . '/Database.php';

class PaymentVerificationRequest {
    private $db;

    public function __construct() {
        $this->db = new Database();
    }

    /**
     * Get all payment verification requests
     *
     * @param array $filters Optional filters for the requests
     * @return array Array of payment verification requests
     */
    public function getAllRequests($filters = []) {
        $sql = "SELECT pvr.*, u.name as traveler_name
                FROM payment_verification_requests pvr
                LEFT JOIN users u ON pvr.traveler_id = u.user_id";

        $whereConditions = [];
        $params = [];

        // Apply filters if provided
        if (!empty($filters)) {
            if (isset($filters['status']) && $filters['status']) {
                $whereConditions[] = "pvr.status = ?";
                $params[] = $filters['status'];
            }

            if (isset($filters['priority']) && $filters['priority']) {
                $whereConditions[] = "pvr.priority = ?";
                $params[] = $filters['priority'];
            }

            if (isset($filters['issue_type']) && $filters['issue_type']) {
                $whereConditions[] = "pvr.issue_type = ?";
                $params[] = $filters['issue_type'];
            }

            if (isset($filters['traveler_id']) && $filters['traveler_id']) {
                $whereConditions[] = "pvr.traveler_id = ?";
                $params[] = $filters['traveler_id'];
            }

            if (isset($filters['booking_id']) && $filters['booking_id']) {
                $whereConditions[] = "pvr.booking_id LIKE ?";
                $params[] = '%' . $filters['booking_id'] . '%';
            }

            if (isset($filters['transaction_id']) && $filters['transaction_id']) {
                $whereConditions[] = "pvr.transaction_id LIKE ?";
                $params[] = '%' . $filters['transaction_id'] . '%';
            }
        }

        // Add WHERE clause if there are conditions
        if (!empty($whereConditions)) {
            $sql .= " WHERE " . implode(" AND ", $whereConditions);
        }

        // Add order by
        $sql .= " ORDER BY
                  CASE pvr.priority
                    WHEN 'urgent' THEN 1
                    WHEN 'high' THEN 2
                    WHEN 'normal' THEN 3
                    WHEN 'low' THEN 4
                  END,
                  CASE pvr.status
                    WHEN 'new' THEN 1
                    WHEN 'pending' THEN 2
                    WHEN 'in_progress' THEN 3
                    WHEN 'resolved' THEN 4
                    WHEN 'closed' THEN 5
                  END,
                  pvr.created_at DESC";

        return $this->db->select($sql, $params);
    }

    /**
     * Get a single payment verification request by ID
     *
     * @param int $requestId The request ID
     * @return array|false The request data or false if not found
     */
    public function getRequestById($requestId) {
        $sql = "SELECT pvr.*, u.name as traveler_name
                FROM payment_verification_requests pvr
                LEFT JOIN users u ON pvr.traveler_id = u.user_id
                WHERE pvr.request_id = ?";

        $result = $this->db->select($sql, [$requestId]);

        return !empty($result) ? $result[0] : false;
    }

    /**
     * Create a new payment verification request
     *
     * @param array $data The request data
     * @return int|false The new request ID or false on failure
     */
    public function createRequest($data) {
        $sql = "INSERT INTO payment_verification_requests
                (traveler_id, booking_id, transaction_id, issue_type, issue_description, action_required, status, priority)
                VALUES (?, ?, ?, ?, ?, ?, ?, ?)";

        $params = [
            $data['traveler_id'],
            $data['booking_id'] ?? null,
            $data['transaction_id'] ?? null,
            $data['issue_type'],
            $data['issue_description'],
            $data['action_required'],
            $data['status'] ?? 'new',
            $data['priority'] ?? 'normal'
        ];

        return $this->db->insert($sql, $params);
    }

    /**
     * Update a payment verification request
     *
     * @param int $requestId The request ID
     * @param array $data The updated data
     * @return bool True on success, false on failure
     */
    public function updateRequest($requestId, $data) {
        $updateFields = [];
        $params = [];

        // Build the update fields and parameters
        foreach ($data as $field => $value) {
            if (in_array($field, ['traveler_id', 'booking_id', 'transaction_id', 'issue_type', 'issue_description', 'action_required', 'status', 'priority'])) {
                $updateFields[] = "$field = ?";
                $params[] = $value;
            }
        }

        if (empty($updateFields)) {
            return false;
        }

        $sql = "UPDATE payment_verification_requests SET " . implode(", ", $updateFields) . " WHERE request_id = ?";
        $params[] = $requestId;

        return $this->db->update($sql, $params);
    }

    /**
     * Delete a payment verification request
     *
     * @param int $requestId The request ID
     * @return bool True on success, false on failure
     */
    public function deleteRequest($requestId) {
        $sql = "DELETE FROM payment_verification_requests WHERE request_id = ?";

        return $this->db->delete($sql, [$requestId]);
    }
}
