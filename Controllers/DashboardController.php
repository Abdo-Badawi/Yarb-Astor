<?php
require_once 'DBController.php';

class DashboardController {
    private $db;

    public function __construct() {
        $this->db = new DBController();
    }

    /**
     * Get dashboard data for a host
     * 
     * @param int $hostID The host ID
     * @return array Dashboard data including stats and recent activities
     */
    public function getHostDashboardData(int $hostID): array {
        $dashboardData = [
            'stats' => $this->getHostStats($hostID),
            'recentApplications' => $this->getRecentApplications($hostID),
            'recentMessages' => $this->getRecentMessages($hostID),
            'activeOpportunities' => $this->getActiveOpportunities($hostID)
        ];
        
        return $dashboardData;
    }
    
    /**
     * Get host statistics
     */
    private function getHostStats(int $hostID): array {
        $this->db->openConnection();
        
        // Get profile views count
        $viewsQuery = "SELECT COUNT(*) as profile_views FROM user_activity_log 
                      WHERE activity_type = 'view_profile' AND activity_details LIKE ?";
        $viewsParams = ["%host_id=$hostID%"];
        $viewsResult = $this->db->selectPrepared($viewsQuery, "s", $viewsParams);
        $profileViews = $viewsResult[0]['profile_views'] ?? 0;
        
        // Get active applications count
        $applicationsQuery = "SELECT COUNT(*) as active_applications FROM applications a 
                             JOIN opportunity o ON a.opportunity_id = o.opportunity_id 
                             WHERE o.host_id = ? AND a.status = 'pending'";
        $applicationsParams = [$hostID];
        $applicationsResult = $this->db->selectPrepared($applicationsQuery, "i", $applicationsParams);
        $activeApplications = $applicationsResult[0]['active_applications'] ?? 0;
        
        // Get unread messages count
        $messagesQuery = "SELECT COUNT(*) as unread_messages FROM message 
                         WHERE receiver_id = ? AND receiver_type = 'host' AND is_read = 0";
        $messagesParams = [$hostID];
        $messagesResult = $this->db->selectPrepared($messagesQuery, "i", $messagesParams);
        $unreadMessages = $messagesResult[0]['unread_messages'] ?? 0;
        
        // Get active opportunities count
        $opportunitiesQuery = "SELECT COUNT(*) as active_opportunities FROM opportunity 
                              WHERE host_id = ? AND status = 'open'";
        $opportunitiesParams = [$hostID];
        $opportunitiesResult = $this->db->selectPrepared($opportunitiesQuery, "i", $opportunitiesParams);
        $activeOpportunities = $opportunitiesResult[0]['active_opportunities'] ?? 0;
        
        $this->db->closeConnection();
        
        return [
            'profileViews' => $profileViews,
            'activeApplications' => $activeApplications,
            'unreadMessages' => $unreadMessages,
            'activeOpportunities' => $activeOpportunities
        ];
    }
    
    /**
     * Get recent applications for host opportunities
     */
    private function getRecentApplications(int $hostID): array {
        $this->db->openConnection();
        
        $query = "SELECT a.*, o.title as opportunity_title, u.first_name, u.last_name, u.profile_picture 
                 FROM applications a 
                 JOIN opportunity o ON a.opportunity_id = o.opportunity_id 
                 JOIN users u ON a.traveler_id = u.user_id 
                 WHERE o.host_id = ? 
                 ORDER BY a.applied_date DESC LIMIT 5";
        $params = [$hostID];
        $result = $this->db->selectPrepared($query, "i", $params);
        
        $this->db->closeConnection();
        
        return $result ?: [];
    }
    
    /**
     * Get recent messages for a host
     */
    private function getRecentMessages(int $hostID): array {
        $this->db->openConnection();
        
        $query = "SELECT m.*, u.first_name, u.last_name, u.profile_picture 
                 FROM message m 
                 JOIN users u ON m.sender_id = u.user_id 
                 WHERE m.receiver_id = ? AND m.receiver_type = 'host' 
                 ORDER BY m.timestamp DESC LIMIT 5";
        $params = [$hostID];
        $result = $this->db->selectPrepared($query, "i", $params);
        
        $this->db->closeConnection();
        
        return $result ?: [];
    }
    
    /**
     * Get active opportunities for a host
     */
    private function getActiveOpportunities(int $hostID): array {
        $this->db->openConnection();
        
        $query = "SELECT o.*, 
                 (SELECT COUNT(*) FROM applications a WHERE a.opportunity_id = o.opportunity_id) as application_count 
                 FROM opportunity o 
                 WHERE o.host_id = ? AND o.status = 'open' 
                 ORDER BY o.start_date ASC LIMIT 3";
        $params = [$hostID];
        $result = $this->db->selectPrepared($query, "i", $params);
        
        $this->db->closeConnection();
        
        return $result ?: [];
    }
}