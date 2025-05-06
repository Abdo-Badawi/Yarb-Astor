<?php
require_once 'DBController.php';

class HostController {
    private $db;
    
    public function __construct() {
        $this->db = new DBController();
    }
    
    // Function to get host by ID
    public function getHostById(int $hostId): ?array {
        $sql = "SELECT h.*, u.first_name, u.last_name, u.email, u.phone_number, u.profile_picture 
                FROM hosts h 
                JOIN users u ON h.host_id = u.user_id 
                WHERE h.host_id = ?";
        
        $params = [$hostId];
        
        $this->db->openConnection();
        $result = $this->db->selectPrepared($sql, "i", $params);
        $this->db->closeConnection();
        
        return $result[0] ?? null;
    }
    
    // Function to get all active hosts
    public function getActiveHosts(): array {
        $sql = "SELECT h.*, u.first_name, u.last_name, u.profile_picture 
                FROM hosts h 
                JOIN users u ON h.host_id = u.user_id 
                WHERE h.status = 'active'";
        
        $this->db->openConnection();
        $result = $this->db->select($sql);
        $this->db->closeConnection();
        
        return $result ?: [];
    }
    
    // Function to get host opportunities
    public function getHostOpportunities(int $hostId): array {
        $sql = "SELECT * FROM opportunity WHERE host_id = ? ORDER BY created_at DESC";
        
        $params = [$hostId];
        
        $this->db->openConnection();
        $result = $this->db->selectPrepared($sql, "i", $params);
        $this->db->closeConnection();
        
        return $result ?: [];
    }
    
    // Function to get host reviews
    public function getHostReviews(int $hostId): array {
        $sql = "SELECT r.*, u.first_name, u.last_name, u.profile_picture 
                FROM reviews r 
                JOIN users u ON r.traveler_id = u.user_id 
                WHERE r.host_id = ? 
                ORDER BY r.created_at DESC";
        
        $params = [$hostId];
        
        $this->db->openConnection();
        $result = $this->db->selectPrepared($sql, "i", $params);
        $this->db->closeConnection();
        
        return $result ?: [];
    }
    
    // Function to get host average rating
    public function getHostAverageRating(int $hostId): float {
        $sql = "SELECT AVG(rating) as avg_rating FROM reviews WHERE host_id = ?";
        
        $params = [$hostId];
        
        $this->db->openConnection();
        $result = $this->db->selectPrepared($sql, "i", $params);
        $this->db->closeConnection();
        
        return round($result[0]['avg_rating'] ?? 0, 1);
    }
}
?>