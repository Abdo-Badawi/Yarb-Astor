<?php
require_once '../Models/Database.php';
require_once '../Models/User.php';
use Models\User;
class Admin extends User{
    private $db;

    public function __construct() {
        $this->db = new Database();
    }

    public function getDashboardData(): array {
        $dashboardData = [
            'stats' => $this->getStats(),
            'recentActivity' => $this->getRecentActivity(),
            'pendingReports' => $this->getPendingReports(),
            'pendingVerifications' => $this->getPendingVerifications(),
            'recentOpportunities' => $this->getRecentOpportunities()
        ];

        return $dashboardData;
    }

    public function getStats(): array {
        $this->db->openConnection();

        // Get total hosts count
        $hostsQuery = "SELECT COUNT(*) as total_hosts FROM users WHERE user_type = 'host'";
        $hostsResult = $this->db->select($hostsQuery);
        $totalHosts = $hostsResult[0]['total_hosts'] ?? 0;

        // Get hosts growth (last 30 days)
        $hostsGrowthQuery = "SELECT
            COUNT(*) as new_hosts,
            (SELECT COUNT(*) FROM users WHERE user_type = 'host' AND created_at < DATE_SUB(NOW(), INTERVAL 30 DAY)) as old_hosts
            FROM users
            WHERE user_type = 'host' AND created_at >= DATE_SUB(NOW(), INTERVAL 30 DAY)";
        $hostsGrowthResult = $this->db->select($hostsGrowthQuery);
        $newHosts = $hostsGrowthResult[0]['new_hosts'] ?? 0;
        $oldHosts = $hostsGrowthResult[0]['old_hosts'] ?? 1; // Prevent division by zero
        $hostsGrowth = ($oldHosts > 0) ? round(($newHosts / $oldHosts) * 100) : 0;

        // Get total travelers count
        $travelersQuery = "SELECT COUNT(*) as total_travelers FROM users WHERE user_type = 'traveler'";
        $travelersResult = $this->db->select($travelersQuery);
        $totalTravelers = $travelersResult[0]['total_travelers'] ?? 0;

        // Get travelers growth (last 30 days)
        $travelersGrowthQuery = "SELECT
            COUNT(*) as new_travelers,
            (SELECT COUNT(*) FROM users WHERE user_type = 'traveler' AND created_at < DATE_SUB(NOW(), INTERVAL 30 DAY)) as old_travelers
            FROM users
            WHERE user_type = 'traveler' AND created_at >= DATE_SUB(NOW(), INTERVAL 30 DAY)";
        $travelersGrowthResult = $this->db->select($travelersGrowthQuery);
        $newTravelers = $travelersGrowthResult[0]['new_travelers'] ?? 0;
        $oldTravelers = $travelersGrowthResult[0]['old_travelers'] ?? 1; // Prevent division by zero
        $travelersGrowth = ($oldTravelers > 0) ? round(($newTravelers / $oldTravelers) * 100) : 0;

        // Get active homestays count
        $homestaysQuery = "SELECT COUNT(*) as active_homestays FROM opportunity WHERE status = 'open'";
        $homestaysResult = $this->db->select($homestaysQuery);
        $activeHomestays = $homestaysResult[0]['active_homestays'] ?? 0;

        // Get homestays growth (last 30 days)
        $homestaysGrowthQuery = "SELECT
            COUNT(*) as new_homestays,
            (SELECT COUNT(*) FROM opportunity WHERE created_at < DATE_SUB(NOW(), INTERVAL 30 DAY)) as old_homestays
            FROM opportunity
            WHERE created_at >= DATE_SUB(NOW(), INTERVAL 30 DAY)";
        $homestaysGrowthResult = $this->db->select($homestaysGrowthQuery);
        $newHomestays = $homestaysGrowthResult[0]['new_homestays'] ?? 0;
        $oldHomestays = $homestaysGrowthResult[0]['old_homestays'] ?? 1; // Prevent division by zero
        $homestaysGrowth = ($oldHomestays > 0) ? round(($newHomestays / $oldHomestays) * 100) : 0;

        // Get pending applications count
        $applicationsQuery = "SELECT COUNT(*) as pending_applications FROM applications WHERE status = 'pending'";
        $applicationsResult = $this->db->select($applicationsQuery);
        $pendingApplications = $applicationsResult[0]['pending_applications'] ?? 0;

        // Get applications growth (last 30 days)
        $applicationsGrowthQuery = "SELECT
            COUNT(*) as new_applications,
            (SELECT COUNT(*) FROM applications WHERE applied_date < DATE_SUB(NOW(), INTERVAL 30 DAY)) as old_applications
            FROM applications
            WHERE applied_date >= DATE_SUB(NOW(), INTERVAL 30 DAY)";
        $applicationsGrowthResult = $this->db->select($applicationsGrowthQuery);
        $newApplications = $applicationsGrowthResult[0]['new_applications'] ?? 0;
        $oldApplications = $applicationsGrowthResult[0]['old_applications'] ?? 1; // Prevent division by zero
        $applicationsGrowth = ($oldApplications > 0) ? round(($newApplications / $oldApplications) * 100) : 0;

        $this->db->closeConnection();

        return [
            'totalHosts' => $totalHosts,
            'hostsGrowth' => $hostsGrowth,
            'totalTravelers' => $totalTravelers,
            'travelersGrowth' => $travelersGrowth,
            'activeHomestays' => $activeHomestays,
            'homestaysGrowth' => $homestaysGrowth,
            'pendingApplications' => $pendingApplications,
            'applicationsGrowth' => $applicationsGrowth
        ];
    }

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
}
?>
