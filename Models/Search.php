<?php
require_once '../Models/Database.php';

class SearchController {
    private $db;
    
    public function __construct() {
        $this->db = new Database();
    }
    
    /**
     * Search for opportunities based on various filters
     * 
     * @param array $filters Array of filter parameters
     * @return array List of matching opportunities
     */
    public function searchOpportunities(array $filters = []): array {
        $this->db->openConnection();
        
        // Start building the query
        $sql = "SELECT o.*, u.first_name, u.last_name, u.profile_picture 
                FROM opportunity o 
                JOIN users u ON o.host_id = u.user_id 
                WHERE o.status = 'open'";
        
        $params = [];
        $types = "";
        
        // Add location filter
        if (!empty($filters['location'])) {
            $sql .= " AND o.location LIKE ?";
            $params[] = "%" . $filters['location'] . "%";
            $types .= "s";
        }
        
        // Add category filter
        if (!empty($filters['category'])) {
            $sql .= " AND o.category = ?";
            $params[] = $filters['category'];
            $types .= "s";
        }
        
        // Add date range filter
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
        
        // Add accommodation type filter
        if (!empty($filters['accommodation_type'])) {
            $sql .= " AND o.accommodation_type = ?";
            $params[] = $filters['accommodation_type'];
            $types .= "s";
        }
        
        // Add duration type filter
        if (!empty($filters['duration_type'])) {
            $sql .= " AND o.duration_type = ?";
            $params[] = $filters['duration_type'];
            $types .= "s";
        }
        
        // Add order by clause
        $sql .= " ORDER BY o.created_at DESC";
        
        // Execute the query
        $result = empty($params) ? $this->db->select($sql) : $this->db->selectPrepared($sql, $types, $params);
        
        $this->db->closeConnection();
        
        return $result ?: [];
    }
    
    /**
     * Get all available locations for filtering
     * 
     * @return array List of unique locations
     */
    public function getAvailableLocations(): array {
        $this->db->openConnection();
        
        $sql = "SELECT DISTINCT location FROM opportunity WHERE status = 'open' ORDER BY location";
        $result = $this->db->select($sql);
        
        $this->db->closeConnection();
        
        return $result ?: [];
    }
    
    /**
     * Get all available categories for filtering
     * 
     * @return array List of unique categories
     */
    public function getAvailableCategories(): array {
        $this->db->openConnection();
        
        $sql = "SELECT DISTINCT category FROM opportunity WHERE status = 'open' ORDER BY category";
        $result = $this->db->select($sql);
        
        $this->db->closeConnection();
        
        return $result ?: [];
    }
}
?>