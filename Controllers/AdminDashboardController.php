<?php
/**
 * AdminDashboardController
 *
 * Controller for handling admin dashboard data and functionality
 */
require_once 'DBController.php';
require_once '../Models/Admin.php';

class AdminDashboardController {
    private $db;
    private $adminModel;

    public function __construct() {
        $this->db = new DBController();
        $this->adminModel = new Models\Admin();
    }

    /**
     * Get all dashboard data for admin
     *
     * @param int $adminId Admin ID
     * @return array Dashboard data including stats and recent activities
     */
    public function getDashboardData(int $adminId = 0): array {
        // Check if admin has permission to view dashboard
        if ($adminId > 0) {
            $admin = $this->adminModel->getAdminById($adminId);
            if (!$admin) {
                return ['error' => 'Admin not found'];
            }
        }
        
        $dashboardData = [
            'stats' => $this->getStats(),
            'recentActivity' => $this->getRecentActivity(),
            'pendingReports' => $this->getPendingReports(),
            'pendingVerifications' => $this->getPendingVerifications(),
            'recentOpportunities' => $this->getRecentOpportunities()
        ];

        return $dashboardData;
    }

    /**
     * Get admin statistics
     *
     * @return array Statistics data
     */
    public function getStats(): array {
        $this->db->openConnection();

        // Get total hosts count
        $hostsQuery = "SELECT COUNT(*) as total_hosts FROM users WHERE user_type = 'host'";
        $hostsResult = $this->db->select($hostsQuery);
        $totalHosts = $hostsResult[0]['total_hosts'] ?? 0;
        
        // Get total travelers count
        $travelersQuery = "SELECT COUNT(*) as total_travelers FROM users WHERE user_type = 'traveler'";
        $travelersResult = $this->db->select($travelersQuery);
        $totalTravelers = $travelersResult[0]['total_travelers'] ?? 0;
        
        // Get total opportunities count
        $opportunitiesQuery = "SELECT COUNT(*) as total_opportunities FROM opportunity";
        $opportunitiesResult = $this->db->select($opportunitiesQuery);
        $totalOpportunities = $opportunitiesResult[0]['total_opportunities'] ?? 0;
        
        // Get total bookings count
        $bookingsQuery = "SELECT COUNT(*) as total_bookings FROM booking";
        $bookingsResult = $this->db->select($bookingsQuery);
        $totalBookings = $bookingsResult[0]['total_bookings'] ?? 0;
        
        // Get total revenue
        $revenueQuery = "SELECT SUM(amount) as total_revenue FROM fee_transaction WHERE status = 'completed'";
        $revenueResult = $this->db->select($revenueQuery);
        $totalRevenue = $revenueResult[0]['total_revenue'] ?? 0;
        
        $this->db->closeConnection();
        
        return [
            'totalHosts' => $totalHosts,
            'totalTravelers' => $totalTravelers,
            'totalOpportunities' => $totalOpportunities,
            'totalBookings' => $totalBookings,
            'totalRevenue' => $totalRevenue
        ];
    }

    /**
     * Get recent activity from user_activity_log
     *
     * @param int $limit Number of activities to return
     * @return array Recent activities
     */
    public function getRecentActivity(int $limit = 30): array {
        $this->db->openConnection();

        $query = "SELECT ual.*, u.first_name, u.last_name, u.user_type
                 FROM user_activity_log ual
                 LEFT JOIN users u ON ual.user_id = u.user_id
                 ORDER BY ual.created_at DESC
                 LIMIT ?";

        $params = [$limit];
        $result = $this->db->selectPrepared($query, "i", $params);

        $this->db->closeConnection();

        return $result ?: [];
    }

    /**
     * Get pending reports
     *
     * @param int $limit Number of reports to return
     * @return array Pending reports
     */
    public function getPendingReports(int $limit = 5): array {
        $this->db->openConnection();

        $query = "SELECT r.*,
                 u1.first_name as reporter_first_name, u1.last_name as reporter_last_name,
                 u2.first_name as target_first_name, u2.last_name as target_last_name
                 FROM report r
                 LEFT JOIN users u1 ON r.reported_by_id = u1.user_id
                 LEFT JOIN users u2 ON r.target_user_id = u2.user_id
                 WHERE r.status = 'open'
                 ORDER BY r.created_at DESC
                 LIMIT ?";

        $params = [$limit];
        $result = $this->db->selectPrepared($query, "i", $params);

        $this->db->closeConnection();

        return $result ?: [];
    }

    /**
     * Get pending payment verifications
     *
     * @param int $limit Number of verifications to return
     * @return array Pending verifications
     */
    public function getPendingVerifications(int $limit = 5): array {
        $this->db->openConnection();

        $query = "SELECT pvr.*, u.first_name, u.last_name
                 FROM payment_verification_requests pvr
                 LEFT JOIN users u ON pvr.traveler_id = u.user_id
                 WHERE pvr.status != 'resolved'
                 ORDER BY
                 CASE pvr.priority
                   WHEN 'urgent' THEN 1
                   WHEN 'high' THEN 2
                   WHEN 'normal' THEN 3
                   WHEN 'low' THEN 4
                 END,
                 pvr.created_at DESC
                 LIMIT ?";

        $params = [$limit];
        $result = $this->db->selectPrepared($query, "i", $params);

        $this->db->closeConnection();

        return $result ?: [];
    }
    
    /**
     * Get recent opportunities
     *
     * @param int $limit Number of opportunities to return
     * @return array Recent opportunities
     */
    public function getRecentOpportunities(int $limit = 5): array {
        $this->db->openConnection();

        $query = "SELECT o.*, u.first_name, u.last_name
                 FROM opportunity o
                 LEFT JOIN users u ON o.host_id = u.user_id
                 ORDER BY o.created_at DESC
                 LIMIT ?";

        $params = [$limit];
        $result = $this->db->selectPrepared($query, "i", $params);

        $this->db->closeConnection();

        return $result ?: [];
    }
    
    /**
     * Log admin activity
     *
     * @param int $adminId Admin ID
     * @param string $action Action performed
     * @param string $details Additional details
     * @return bool True if logged successfully, false otherwise
     */
    public function logAdminActivity(int $adminId, string $action, string $details = ''): bool {
        $this->db->openConnection();
        
        $query = "INSERT INTO admin_activity_log (admin_id, action, details, created_at) 
                 VALUES (?, ?, ?, NOW())";
        
        $params = [$adminId, $action, $details];
        $result = $this->db->insert($query, "iss", $params);
        
        $this->db->closeConnection();
        
        return $result;
    }
}
?>


