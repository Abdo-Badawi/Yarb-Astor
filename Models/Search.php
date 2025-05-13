<?php
require_once '../Models/Database.php';

class Search {
    private $db;
    
    public function __construct() {
        $this->db = new Database();
    }
    
    /**
     * Search for opportunities based on filters
     * 
     * @param array $filters Array of search filters
     * @return array Array of matching opportunities
     */
    public function searchOpportunities(array $filters = []): array {
        try {
            if (!$this->db->openConnection()) {
                error_log("Failed to open database connection in searchOpportunities");
                return [];
            }
            
            // Base SQL query with joins to get host information
            $sql = "SELECT o.*, u.first_name, u.last_name, u.profile_picture 
                    FROM opportunity o 
                    JOIN users u ON o.host_id = u.user_id 
                    WHERE o.status = 'open'";
            
            $params = [];
            $types = "";
            
            // Add filters to the query
            if (!empty($filters['location'])) {
                $sql .= " AND o.location LIKE ?";
                $params[] = "%" . $filters['location'] . "%";
                $types .= "s";
            }
            
            if (!empty($filters['category'])) {
                $sql .= " AND o.category = ?";
                $params[] = $filters['category'];
                $types .= "s";
            }
            
            if (!empty($filters['start_date'])) {
                $sql .= " AND o.start_date >= ?";
                $params[] = $filters['start_date'];
                $types .= "s";
            }
            
            if (!empty($filters['end_date'])) {
                $sql .= " AND o.end_date <= ?";
                $params[] = $filters['end_date'];
                $types .= "s";
            }
            
            if (!empty($filters['accommodation_type'])) {
                $sql .= " AND o.accommodation_type = ?";
                $params[] = $filters['accommodation_type'];
                $types .= "s";
            }
            
            if (!empty($filters['duration_type'])) {
                if ($filters['duration_type'] === 'short') {
                    $sql .= " AND DATEDIFF(o.end_date, o.start_date) <= 14"; // 2 weeks or less
                } else if ($filters['duration_type'] === 'medium') {
                    $sql .= " AND DATEDIFF(o.end_date, o.start_date) > 14 AND DATEDIFF(o.end_date, o.start_date) <= 30"; // 2-4 weeks
                } else if ($filters['duration_type'] === 'long') {
                    $sql .= " AND DATEDIFF(o.end_date, o.start_date) > 30"; // More than 4 weeks
                }
            }
            
            // Order by start date
            $sql .= " ORDER BY o.start_date ASC";
            
            // Execute the query
            $result = empty($params) ? 
                $this->db->select($sql) : 
                $this->db->selectPrepared($sql, $types, $params);
            
            $this->db->closeConnection();
            
            return $result ?: [];
        } catch (Exception $e) {
            error_log("Exception in searchOpportunities: " . $e->getMessage());
            return [];
        }
    }
    
    /**
     * Get all available categories for opportunities
     * 
     * @return array Array of categories
     */
    public function getAvailableCategories(): array {
        try {
            if (!$this->db->openConnection()) {
                error_log("Failed to open database connection in getAvailableCategories");
                return [];
            }
            
            $sql = "SELECT DISTINCT category FROM opportunity WHERE status = 'open' ORDER BY category";
            $result = $this->db->select($sql);
            
            $this->db->closeConnection();
            
            // Format the result as a simple array of categories
            $categories = [];
            if ($result) {
                foreach ($result as $row) {
                    $categories[] = $row['category'];
                }
            }
            
            return $categories;
        } catch (Exception $e) {
            error_log("Exception in getAvailableCategories: " . $e->getMessage());
            return [];
        }
    }
    
    /**
     * Get all available locations for opportunities
     * 
     * @return array Array of locations
     */
    public function getAvailableLocations(): array {
        try {
            if (!$this->db->openConnection()) {
                error_log("Failed to open database connection in getAvailableLocations");
                return [];
            }
            
            $sql = "SELECT DISTINCT location FROM opportunity WHERE status = 'open' ORDER BY location";
            $result = $this->db->select($sql);
            
            $this->db->closeConnection();
            
            // Format the result as a simple array of locations
            $locations = [];
            if ($result) {
                foreach ($result as $row) {
                    $locations[] = $row['location'];
                }
            }
            
            return $locations;
        } catch (Exception $e) {
            error_log("Exception in getAvailableLocations: " . $e->getMessage());
            return [];
        }
    }
}
?>
