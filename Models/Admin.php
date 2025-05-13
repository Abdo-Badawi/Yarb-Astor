<?php
// Make sure we're not using namespace if the other models aren't using it
// If you want to use namespaces, make sure all files are consistent
// Remove the namespace line if other models don't use it
// namespace Models;

require_once __DIR__ . '/Database.php';
require_once __DIR__ . '/User.php';

class Admin extends User {
    protected $db;

    public function __construct() {
        $this->db = new Database();
    }

    /**
     * Get admin user data
     * 
     * @param int $userId Admin user ID
     * @return array|bool Admin data or false on failure
     */
    public function getUserData($userId) {
        if (!$this->db->openConnection()) {
            return false;
        }
        
        $query = "SELECT u.user_id, u.first_name, u.last_name, u.email, u.phone_number, u.profile_picture, 
                 u.created_at, u.last_login, u.user_type, u.gender, u.date_of_birth, u.national_id
                 FROM users u 
                 WHERE u.user_id = ? AND u.user_type = 'admin'";
        $params = [$userId];
        
        $result = $this->db->selectPrepared($query, "i", $params);
        
        $this->db->closeConnection();
        
        return $result && count($result) > 0 ? $result[0] : false;
    }

    /**
     * Update admin user profile
     * 
     * @param int $userId Admin user ID
     * @param array $userData Updated profile data
     * @return bool Success status
     */
    public function updateUserProfile($userId, $userData) {
        if (!$this->db->openConnection()) {
            return false;
        }
        
        // Update users table
        $userQuery = "UPDATE users SET 
                     first_name = ?,
                     last_name = ?,
                     email = ?,
                     phone_number = ? 
                     WHERE user_id = ? AND user_type = 'admin'";
        $userParams = [
            $userData['first_name'],
            $userData['last_name'],
            $userData['email'],
            $userData['phone_number'],
            $userId
        ];
        
        $result = $this->db->update($userQuery, "ssssi", $userParams);
        
        $this->db->closeConnection();
        
        return $result;
    }
    
    /**
     * Get dashboard data for admin
     *
     * @return array Dashboard data including stats, activities, and pending items
     */
    public function getDashboardData() {
        if (!$this->db->openConnection()) {
            return [
                'stats' => [],
                'recentActivity' => [],
                'pendingReports' => [],
                'pendingVerifications' => [],
                'recentOpportunities' => []
            ];
        }
        
        try {
            // Get stats
            $stats = $this->getStats();
            
            // Get recent activity
            $recentActivity = $this->getRecentActivity();
            
            // Get pending reports
            $pendingReports = $this->getPendingReports();
            
            // Get pending verifications
            $pendingVerifications = $this->getPendingVerifications();
            
            // Get recent opportunities
            $recentOpportunities = $this->getRecentOpportunities();
            
            return [
                'stats' => $stats,
                'recentActivity' => $recentActivity,
                'pendingReports' => $pendingReports,
                'pendingVerifications' => $pendingVerifications,
                'recentOpportunities' => $recentOpportunities
            ];
        } catch (Exception $e) {
            error_log("Error getting admin dashboard data: " . $e->getMessage());
            return [
                'stats' => [],
                'recentActivity' => [],
                'pendingReports' => [],
                'pendingVerifications' => [],
                'recentOpportunities' => []
            ];
        } finally {
            $this->db->closeConnection();
        }
    }
    
    /**
     * Get system statistics
     *
     * @return array System statistics
     */
    public function getStats() {
        // Get total users
        $usersQuery = "SELECT 
                      (SELECT COUNT(*) FROM users WHERE user_type = 'traveler') as totalTravelers,
                      (SELECT COUNT(*) FROM users WHERE user_type = 'host') as totalHosts,
                      (SELECT COUNT(*) FROM opportunity) as totalOpportunities,
                      (SELECT COUNT(*) FROM applications) as totalApplications";
        $statsResult = $this->db->select($usersQuery);
        
        return $statsResult ? $statsResult[0] : [
            'totalTravelers' => 0,
            'totalHosts' => 0,
            'totalOpportunities' => 0,
            'totalApplications' => 0
        ];
    }
    
    /**
     * Get recent activity
     *
     * @param int $limit Number of records to return
     * @return array Recent activity
     */
    public function getRecentActivity($limit = 10) {
        $query = "SELECT 'new_user' as activity_type, u.user_id, u.user_type, u.first_name, u.last_name, u.created_at as timestamp
                 FROM users u
                 WHERE u.created_at > DATE_SUB(NOW(), INTERVAL 30 DAY)
                 UNION
                 SELECT 'new_opportunity' as activity_type, o.host_id as user_id, 'host' as user_type, u.first_name, u.last_name, o.created_at as timestamp
                 FROM opportunity o
                 JOIN users u ON o.host_id = u.user_id
                 WHERE o.created_at > DATE_SUB(NOW(), INTERVAL 30 DAY)
                 UNION
                 SELECT 'new_application' as activity_type, a.traveler_id as user_id, 'traveler' as user_type, u.first_name, u.last_name, a.applied_date as timestamp
                 FROM applications a
                 JOIN users u ON a.traveler_id = u.user_id
                 WHERE a.applied_date > DATE_SUB(NOW(), INTERVAL 30 DAY)
                 ORDER BY timestamp DESC
                 LIMIT ?";
        
        $params = [$limit];
        $result = $this->db->selectPrepared($query, "i", $params);
        
        return $result ?: [];
    }
    
    /**
     * Get pending reports
     *
     * @param int $limit Number of records to return
     * @return array Pending reports
     */
    public function getPendingReports($limit = 5) {
        // This is a placeholder. Adjust according to your actual database schema
        $query = "SELECT r.report_id, r.reporter_id, r.reported_id, r.report_type, r.reason, r.created_at,
                 u1.first_name as reporter_first_name, u1.last_name as reporter_last_name,
                 u2.first_name as reported_first_name, u2.last_name as reported_last_name
                 FROM reports r
                 JOIN users u1 ON r.reporter_id = u1.user_id
                 JOIN users u2 ON r.reported_id = u2.user_id
                 WHERE r.status = 'pending'
                 ORDER BY r.created_at DESC
                 LIMIT ?";
        
        $params = [$limit];
        $result = $this->db->selectPrepared($query, "i", $params);
        
        // If the reports table doesn't exist yet, return empty array
        if ($result === false) {
            return [];
        }
        
        return $result;
    }
    
    /**
     * Get pending verifications
     *
     * @param int $limit Number of records to return
     * @return array Pending verifications
     */
    public function getPendingVerifications($limit = 5) {
        // This is a placeholder. Adjust according to your actual database schema
        $query = "SELECT v.verification_id, v.user_id, v.verification_type, v.submitted_at,
                 u.first_name, u.last_name, u.user_type
                 FROM verifications v
                 JOIN users u ON v.user_id = u.user_id
                 WHERE v.status = 'pending'
                 ORDER BY v.submitted_at DESC
                 LIMIT ?";
        
        $params = [$limit];
        $result = $this->db->selectPrepared($query, "i", $params);
        
        // If the verifications table doesn't exist yet, return empty array
        if ($result === false) {
            return [];
        }
        
        return $result;
    }
    
    /**
     * Get recent opportunities
     *
     * @param int $limit Number of records to return
     * @return array Recent opportunities
     */
    public function getRecentOpportunities($limit = 5) {
        $query = "SELECT o.opportunity_id, o.title, o.location, o.start_date, o.end_date, o.status, o.created_at,
                 u.user_id as host_id, u.first_name as host_first_name, u.last_name as host_last_name
                 FROM opportunity o
                 JOIN users u ON o.host_id = u.user_id
                 ORDER BY o.created_at DESC
                 LIMIT ?";
        
        $params = [$limit];
        $result = $this->db->selectPrepared($query, "i", $params);
        
        return $result ?: [];
    }
}
