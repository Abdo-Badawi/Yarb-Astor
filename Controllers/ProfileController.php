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

    public function updateUserProfile($userId, $userData) {
        if (!$this->db->openConnection() || !$this->db->conn) {
            return false;
        }
        
        try {
            // Start transaction
            $this->db->conn->begin_transaction();
            
            // Update users table
            $userQuery = "UPDATE users SET 
                         first_name = ?,
                         last_name = ?,
                         email = ?,
                         phone_number = ?, 
                         profile_picture = ? 
                         WHERE user_id = ?";
            $userParams = [
                $userData['first_name'],
                $userData['last_name'],
                $userData['email'],
                $userData['phone_number'],
                $userData['profile_picture'] ?? null,
                $userId
            ];
            
            $userResult = $this->db->insert($userQuery, "sssssi", $userParams);
            
            // Update hosts table
            $hostQuery = "UPDATE hosts SET 
                         preferred_language = ?, 
                         bio = ?, 
                         location = ?, 
                         property_type = ? 
                         WHERE host_id = ?";
            $hostParams = [
                $userData['preferred_language'],
                $userData['bio'],
                $userData['location'],
                $userData['property_type'],
                $userId
            ];
            
            $hostResult = $this->db->insert($hostQuery, "ssssi", $hostParams);
            
            if ($userResult && $hostResult) {
                $this->db->conn->commit();
                return true;
            } else {
                throw new Exception("Failed to update profile");
            }
        } catch (Exception $e) {
            $this->db->conn->rollback();
            return false;
        } finally {
            $this->db->closeConnection();
        }
    }
}
?>



