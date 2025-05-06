<?php

include_once '../Controllers/DBController.php'; // Include the DBController class
class TravelerProfileController {
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

        // Query to select user data
        $query = "
            SELECT users.*, traveler.* 
            FROM users 
            JOIN traveler ON users.user_id = traveler.traveler_id
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
        if (!$this->db->openConnection()) {
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
            
            // Update traveler table
            $travelerQuery = "UPDATE traveler SET 
                             skill = ?,
                             language_spoken = ?,
                             preferred_language = ?, 
                             bio = ?, 
                             location = ?
                             WHERE traveler_id = ?";
            $travelerParams = [
                $userData['skill'],
                $userData['language_spoken'],
                $userData['preferred_language'],
                $userData['bio'],
                $userData['location'],
                $userId
            ];
            
            $travelerResult = $this->db->insert($travelerQuery, "sssssi", $travelerParams);
            
            if ($userResult && $travelerResult) {
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
