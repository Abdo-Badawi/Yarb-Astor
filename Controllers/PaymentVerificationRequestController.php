<?php
require_once '../Models/PaymentVerificationRequest.php';

class PaymentVerificationRequest{
    private $model;
    
    public function __construct() {
        $this->model = new PaymentVerificationRequest();
    }
    
    /**
     * Get all payment verification requests
     * 
     * @param array $filters Optional filters for the requests
     * @return array Array of payment verification requests
     */
    public function getAllRequests($filters = []) {
        return $this->model->getAllRequests($filters);
    }
    
    /**
     * Get a single payment verification request by ID
     * 
     * @param int $requestId The request ID
     * @return array|false The request data or false if not found
     */
    public function getRequestById($requestId) {
        return $this->model->getRequestById($requestId);
    }
    
    /**
     * Create a new payment verification request
     * 
     * @param array $data The request data
     * @return int|false The new request ID or false on failure
     */
    public function createRequest($data) {
        // Validate required fields
        if (empty($data['traveler_id']) || empty($data['issue_type']) || 
            empty($data['issue_description']) || empty($data['action_required'])) {
            return false;
        }
        
        return $this->model->createRequest($data);
    }
    
    /**
     * Update a payment verification request
     * 
     * @param int $requestId The request ID
     * @param array $data The updated data
     * @return bool True on success, false on failure
     */
    public function updateRequest($requestId, $data) {
        if (empty($requestId) || empty($data)) {
            return false;
        }
        
        return $this->model->updateRequest($requestId, $data);
    }
    
    /**
     * Delete a payment verification request
     * 
     * @param int $requestId The request ID
     * @return bool True on success, false on failure
     */
    public function deleteRequest($requestId) {
        if (empty($requestId)) {
            return false;
        }
        
        return $this->model->deleteRequest($requestId);
    }
    
    /**
     * Get the badge class for a request status
     * 
     * @param string $status The request status
     * @return string The badge class
     */
    public function getStatusBadgeClass($status) {
        switch ($status) {
            case 'new':
                return 'bg-secondary';
            case 'pending':
                return 'bg-warning';
            case 'in_progress':
                return 'bg-info';
            case 'resolved':
                return 'bg-success';
            case 'closed':
                return 'bg-dark';
            default:
                return 'bg-secondary';
        }
    }
    
    /**
     * Get the badge class for a request priority
     * 
     * @param string $priority The request priority
     * @return string The badge class
     */
    public function getPriorityBadgeClass($priority) {
        switch ($priority) {
            case 'low':
                return 'bg-secondary';
            case 'normal':
                return 'bg-info';
            case 'high':
                return 'bg-warning';
            case 'urgent':
                return 'bg-danger';
            default:
                return 'bg-secondary';
        }
    }
    
    /**
     * Get a human-readable label for an issue type
     * 
     * @param string $issueType The issue type
     * @return string The human-readable label
     */
    public function getIssueTypeLabel($issueType) {
        switch ($issueType) {
            case 'double_payment':
                return 'Double Payment Issue';
            case 'payment_not_received':
                return 'Payment Not Received';
            case 'refund_request':
                return 'Refund Request';
            case 'payment_method_change':
                return 'Payment Method Change';
            case 'other':
                return 'Other Payment Issue';
            default:
                return 'Unknown Issue';
        }
    }
}
