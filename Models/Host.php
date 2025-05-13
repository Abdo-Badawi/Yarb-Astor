<?php
require_once __DIR__ . '/Database.php';
require_once __DIR__ . '/User.php';

class Host extends User {
    protected $db;

    public function __construct() {
        $this->db = new Database();
    }

    /**
     * Get host data by ID
     * 
     * @param int $hostId Host ID
     * @return array|bool Host data or false on failure
     */
    public function getHostData($hostId) {
        if (!$this->db->openConnection()) {
            return false;
        }
        
        // Updated query to match the actual database structure
        // Using the correct join condition and column names
        $query = "SELECT u.user_id as host_id, u.first_name, u.last_name, u.email, u.phone_number, 
                 u.profile_picture, u.created_at, u.last_login, u.user_type,
                 h.location, h.property_type, h.rate, h.bio, h.preferred_language as preferences, h.status
                 FROM users u 
                 LEFT JOIN hosts h ON u.user_id = h.host_id
                 WHERE u.user_id = ? AND u.user_type = 'host'";
        $params = [$hostId];
        
        $result = $this->db->selectPrepared($query, "i", $params);
        
        $this->db->closeConnection();
        
        return $result && count($result) > 0 ? $result[0] : false;
    }

    /**
     * Get host by ID
     * 
     * @param int $hostId Host ID
     * @return array|null Host data or null if not found
     */
    public function getHostById($hostId) {
        if (!$this->db->openConnection()) {
            return null;
        }
        
        // Updated query to match the actual database structure
        $query = "SELECT u.user_id as host_id, u.first_name, u.last_name, u.email, u.phone_number, 
                 u.profile_picture, u.created_at, u.last_login, u.user_type,
                 h.location, h.property_type, h.rate, h.bio, h.preferred_language as preferences, h.status
                 FROM users u 
                 LEFT JOIN hosts h ON u.user_id = h.host_id
                 WHERE u.user_id = ? AND u.user_type = 'host'";
        $params = [$hostId];
        
        $result = $this->db->selectPrepared($query, "i", $params);
        
        $this->db->closeConnection();
        
        return $result && count($result) > 0 ? $result[0] : null;
    }

    /**
     * Get all hosts
     * 
     * @return array Array of hosts
     */
    public function getAllHosts() {
        if (!$this->db->openConnection()) {
            return [];
        }
        
        $query = "SELECT u.user_id as host_id, u.first_name, u.last_name, u.email, u.phone_number, 
                 u.profile_picture, u.created_at, u.last_login, u.user_type,
                 h.location, h.property_type, h.rate, h.bio, h.preferred_language as preferences, h.status
                 FROM users u 
                 LEFT JOIN hosts h ON u.user_id = h.host_id
                 WHERE u.user_type = 'host'
                 ORDER BY u.last_name, u.first_name";
        
        $result = $this->db->select($query);
        
        $this->db->closeConnection();
        
        return $result ?: [];
    }

    /**
     * Update host profile
     * 
     * @param array $hostData Host data
     * @return bool True if update was successful, false otherwise
     */
    public function updateHostProfile($hostData) {
        if (!$this->db->openConnection()) {
            return false;
        }
        
        // Start transaction
        $this->db->openConnection();
        
        try {
            $hostId = $hostData['host_id'];
            
            // Update users table
            $userQuery = "UPDATE users SET 
                         first_name = ?,
                         last_name = ?,
                         email = ?,
                         phone_number = ? 
                         WHERE user_id = ? AND user_type = 'host'";
            $userParams = [
                $hostData['first_name'],
                $hostData['last_name'],
                $hostData['email'],
                $hostData['phone_number'],
                $hostId
            ];
            
            $userResult = $this->db->update($userQuery, "ssssi", $userParams);
            
            if (!$userResult) {
                throw new Exception("Failed to update user data");
            }
            
            // Update hosts table
            $hostQuery = "UPDATE hosts SET 
                         location = ?,
                         property_type = ?,
                         bio = ?,
                         preferred_language = ?
                         WHERE host_id = ?";
            $hostParams = [
                $hostData['location'],
                $hostData['property_type'],
                $hostData['bio'],
                $hostData['preferences'],
                $hostId
            ];
            
            $hostResult = $this->db->update($hostQuery, "ssssi", $hostParams);
            
            if (!$hostResult) {
                throw new Exception("Failed to update host data");
            }
            
            // If profile picture is provided
            if (isset($hostData['profile_picture']) && !empty($hostData['profile_picture'])) {
                $profileQuery = "UPDATE users SET profile_picture = ? WHERE user_id = ?";
                $profileParams = [$hostData['profile_picture'], $hostId];
                
                $profileResult = $this->db->update($profileQuery, "si", $profileParams);
                
                if (!$profileResult) {
                    throw new Exception("Failed to update profile picture");
                }
            }
            
            // Commit transaction
        
            
            return true;
        } catch (Exception $e) {
            // Rollback transaction on error
            $this->db->rollback();
            error_log("Error updating host profile: " . $e->getMessage());
            return false;
        } finally {
            $this->db->closeConnection();
        }
    }

    /**
     * Search hosts by criteria
     * 
     * @param array $criteria Search criteria
     * @return array Array of matching hosts
     */
    public function searchHosts($criteria = []) {
        if (!$this->db->openConnection()) {
            return [];
        }
        
        $whereConditions = [];
        $params = [];
        $types = "";
        
        // Build WHERE clause based on criteria
        if (!empty($criteria['location'])) {
            $whereConditions[] = "h.location LIKE ?";
            $params[] = "%" . $criteria['location'] . "%";
            $types .= "s";
        }
        
        if (!empty($criteria['property_type'])) {
            $whereConditions[] = "h.property_type = ?";
            $params[] = $criteria['property_type'];
            $types .= "s";
        }
        
        if (!empty($criteria['rate_min'])) {
            $whereConditions[] = "h.rate >= ?";
            $params[] = $criteria['rate_min'];
            $types .= "d";
        }
        
        if (!empty($criteria['rate_max'])) {
            $whereConditions[] = "h.rate <= ?";
            $params[] = $criteria['rate_max'];
            $types .= "d";
        }
        
        if (!empty($criteria['language'])) {
            $whereConditions[] = "h.preferred_language = ?";
            $params[] = $criteria['language'];
            $types .= "s";
        }
        
        // Base query
        $query = "SELECT u.user_id as host_id, u.first_name, u.last_name, u.email, u.phone_number, 
                 u.profile_picture, u.created_at, u.last_login, u.user_type,
                 h.location, h.property_type, h.rate, h.bio, h.preferred_language as preferences, h.status
                 FROM users u 
                 LEFT JOIN hosts h ON u.user_id = h.host_id
                 WHERE u.user_type = 'host'";
        
        // Add WHERE conditions if any
        if (!empty($whereConditions)) {
            $query .= " AND " . implode(" AND ", $whereConditions);
        }
        
        // Add ORDER BY
        $query .= " ORDER BY u.last_name, u.first_name";
        
        // Execute query
        $result = empty($params) ? 
                  $this->db->select($query) : 
                  $this->db->selectPrepared($query, $types, $params);
        
        $this->db->closeConnection();
        
        return $result ?: [];
    }

    /**
     * Get host dashboard data
     * 
     * @param int $hostId Host ID
     * @return array Dashboard data
     */
    public function getHostDashboardData($hostId) {
        if (!$this->db->openConnection()) {
            return [
                'stats' => [],
                'recentApplications' => [],
                'recentMessages' => [],
                'activeOpportunities' => []
            ];
        }
        
        try {
            // Get stats
            $stats = $this->getHostStats($hostId);
            
            // Get recent applications
            $recentApplications = $this->getRecentApplications($hostId);
            
            // Get recent messages
            $recentMessages = $this->getRecentMessages($hostId);
            
            // Get active opportunities
            $activeOpportunities = $this->getActiveOpportunities($hostId);
            
            return [
                'stats' => $stats,
                'recentApplications' => $recentApplications,
                'recentMessages' => $recentMessages,
                'activeOpportunities' => $activeOpportunities
            ];
        } catch (Exception $e) {
            error_log("Error getting host dashboard data: " . $e->getMessage());
            return [
                'stats' => [],
                'recentApplications' => [],
                'recentMessages' => [],
                'activeOpportunities' => []
            ];
        } finally {
            $this->db->closeConnection();
        }
    }

    /**
     * Get host statistics
     * 
     * @param int $hostId Host ID
     * @return array Host statistics
     */
    private function getHostStats($hostId) {
        // Get profile views
        $viewsQuery = "SELECT COUNT(*) as profileViews FROM profile_views WHERE profile_id = ? AND profile_type = 'host'";
        $viewsParams = [$hostId];
        $viewsResult = $this->db->selectPrepared($viewsQuery, "i", $viewsParams);
        $profileViews = $viewsResult ? $viewsResult[0]['profileViews'] : 0;
        
        // Get active applications
        $applicationsQuery = "SELECT COUNT(*) as activeApplications FROM applications a 
                             JOIN opportunity o ON a.opportunity_id = o.opportunity_id 
                             WHERE o.host_id = ? AND a.status = 'pending'";
        $applicationsParams = [$hostId];
        $applicationsResult = $this->db->selectPrepared($applicationsQuery, "i", $applicationsParams);
        $activeApplications = $applicationsResult ? $applicationsResult[0]['activeApplications'] : 0;
        
        // Get unread messages
        $messagesQuery = "SELECT COUNT(*) as unreadMessages FROM messages 
                         WHERE recipient_id = ? AND recipient_type = 'host' AND is_read = 0";
        $messagesParams = [$hostId];
        $messagesResult = $this->db->selectPrepared($messagesQuery, "i", $messagesParams);
        $unreadMessages = $messagesResult ? $messagesResult[0]['unreadMessages'] : 0;
        
        // Get active opportunities
        $opportunitiesQuery = "SELECT COUNT(*) as activeOpportunities FROM opportunity 
                              WHERE host_id = ? AND status = 'open'";
        $opportunitiesParams = [$hostId];
        $opportunitiesResult = $this->db->selectPrepared($opportunitiesQuery, "i", $opportunitiesParams);
        $activeOpportunities = $opportunitiesResult ? $opportunitiesResult[0]['activeOpportunities'] : 0;
        
        return [
            'profileViews' => $profileViews,
            'activeApplications' => $activeApplications,
            'unreadMessages' => $unreadMessages,
            'activeOpportunities' => $activeOpportunities
        ];
    }

    /**
     * Get recent applications for host
     * 
     * @param int $hostId Host ID
     * @return array Recent applications
     */
    private function getRecentApplications($hostId) {
        $query = "SELECT a.application_id, a.traveler_id, a.opportunity_id, a.status, a.created_at,
                 u.first_name, u.last_name, u.profile_picture,
                 o.title as opportunity_title
                 FROM applications a
                 JOIN users u ON a.traveler_id = u.user_id
                 JOIN opportunity o ON a.opportunity_id = o.opportunity_id
                 WHERE o.host_id = ?
                 ORDER BY a.created_at DESC
                 LIMIT 5";
        $params = [$hostId];
        
        $result = $this->db->selectPrepared($query, "i", $params);
        
        return $result ?: [];
    }

    /**
     * Get recent messages for host
     * 
     * @param int $hostId Host ID
     * @return array Recent messages
     */
    private function getRecentMessages($hostId) {
        $query = "SELECT m.message_id, m.sender_id, m.sender_type, m.content, m.created_at, m.is_read,
                 u.first_name, u.last_name, u.profile_picture
                 FROM messages m
                 JOIN users u ON m.sender_id = u.user_id
                 WHERE m.recipient_id = ? AND m.recipient_type = 'host'
                 ORDER BY m.created_at DESC
                 LIMIT 5";
        $params = [$hostId];
        
        $result = $this->db->selectPrepared($query, "i", $params);
        
        return $result ?: [];
    }

    /**
     * Get active opportunities for host
     * 
     * @param int $hostId Host ID
     * @return array Active opportunities
     */
    private function getActiveOpportunities($hostId) {
        $query = "SELECT o.opportunity_id, o.title, o.description, o.location, o.start_date, o.end_date,
                 o.status, o.created_at,
                 (SELECT COUNT(*) FROM applications a WHERE a.opportunity_id = o.opportunity_id) as application_count
                 FROM opportunity o
                 WHERE o.host_id = ? AND o.status = 'open'
                 ORDER BY o.created_at DESC
                 LIMIT 5";
        $params = [$hostId];
        
        $result = $this->db->selectPrepared($query, "i", $params);
        
        return $result ?: [];
    }
}
