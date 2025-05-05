<?php
// Include necessary files
include_once 'DBController.php';

class ProfileController {
    private $db;
    
    public function __construct() {
        // Initialize DBController instance
        $this->db = new DBController();
    }
    
    // Method to fetch user data by user_id
    public function getUserData($userId) {
        if (!$this->db->openConnection()) {
            return null; // If DB connection fails, return null
        }

        // Query to select user and host data
        $query = "
            SELECT users.*, hosts.* 
            FROM users 
            JOIN hosts ON users.user_id = hosts.host_id 
            WHERE users.user_id = ?
        ";

        // Prepare parameters
        $params = [$userId];
        
        // Fetch user data
        $userData = $this->db->selectPrepared($query, "i", $params);

        // Close the connection after fetching the data
        $this->db->closeConnection();

        // If no user data found, return null
        return $userData ? $userData[0] : null;
    }
}
?>
