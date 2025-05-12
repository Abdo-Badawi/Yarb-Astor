<?php
namespace Models;
require_once __DIR__ . '/User.php';

use Exception;
use mysqli;

class Host extends User {
    private ?string $propertyType = null;
    private ?string $preferredLanguage = null;
    private ?string $bio = null;
    private ?string $location = null;
    
    public function __construct($email = '', $password = '') {
        parent::__construct($email, $password);
        $this->userType = 'host';
    }
    
    // Host-specific getters and setters
    public function setPropertyType($propertyType) {
        $this->propertyType = $propertyType;
    }
    
    public function getPropertyType() {
        return $this->propertyType;
    }
    
    public function setPreferredLanguage($preferredLanguage) {
        $this->preferredLanguage = $preferredLanguage;
    }
    
    public function getPreferredLanguage() {
        return $this->preferredLanguage;
    }
    
    public function setBio($bio) {
        $this->bio = $bio;
    }
    
    public function getBio() {
        return $this->bio;
    }
    
    public function setLocation($location) {
        $this->location = $location;
    }
    
    public function getLocation() {
        return $this->location;
    }
    
    public function register() {
        if (!$this->db->openConnection()) {
            error_log("Failed to establish database connection");
            return false;
        }
        
        // Check if the connection is valid before starting the transaction
        if ($this->db->conn instanceof mysqli) {
            $this->db->conn->begin_transaction();
        } else {
            error_log("Database connection is not valid.");
            return false;
        }

        try {
            // Insert into the `users` table
            $query = "INSERT INTO users
                      (first_name, last_name, email, password, phone_number, profile_picture, gender, national_id, user_type, date_of_birth, created_at)
                      VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, NOW())";
            $params = [
                $this->firstName,
                $this->lastName,
                $this->email,
                $this->password,
                $this->phoneNumber,
                $this->profilePicture,
                $this->gender,
                $this->nationalID,
                $this->userType,
                $this->birthday
            ];

            $result = $this->db->insert($query, "ssssssssss", $params);

            if (!$result) {
                throw new Exception("Failed to insert into users table.");
            }

            // Get the user ID for related tables
            $user_id = $this->db->conn->insert_id;
            $this->userID = $user_id;
            
            // Insert host-specific data
            $this->registerSpecificUserType($user_id);
            
            // Commit the transaction
            $this->db->conn->commit();
            return $user_id; // Return the user ID on success

        } catch (Exception $e) {
            // Rollback the transaction on failure
            $this->db->conn->rollback();
            error_log("Transaction failed: " . $e->getMessage());
            return false;
        } finally {
            $this->db->closeConnection();
        }
    }
    
    // Override the parent method to insert host-specific data
    protected function registerSpecificUserType($user_id) {
        $hostQuery = "INSERT INTO hosts (host_id, property_type, preferred_language, bio, location, joined_date)
                      VALUES (?, ?, ?, ?, ?, NOW())";
        $hostParams = [
            $user_id,
            $this->propertyType,
            $this->preferredLanguage,
            $this->bio,
            $this->location
        ];
        $hostResult = $this->db->insert($hostQuery, "issss", $hostParams);

        if (!$hostResult) {
            throw new Exception("Failed to insert into hosts table.");
        }
        
        return true;
    }
    
    // Host-specific methods
    public function getHostProfile($host_id) {
        $query = "SELECT u.*, h.* 
                  FROM users u 
                  JOIN hosts h ON u.user_id = h.host_id 
                  WHERE u.user_id = ?";
        $result = $this->db->selectPrepared($query, "i", [$host_id]);
        
        if ($result && count($result) > 0) {
            return $result[0];
        }
        
        return false;
    }
    
    public function updateHostProfile() {
        if (!$this->db->openConnection()) {
            return false;
        }
        
        try {
            // Update users table
            $userQuery = "UPDATE users SET 
                          first_name = ?, 
                          last_name = ?, 
                          phone_number = ?, 
                          profile_picture = ?, 
                          gender = ? 
                          WHERE user_id = ?";
            $userParams = [
                $this->firstName,
                $this->lastName,
                $this->phoneNumber,
                $this->profilePicture,
                $this->gender,
                $this->userID
            ];
            
            $userResult = $this->db->update($userQuery, "sssssi", $userParams);
            
            if (!$userResult) {
                throw new Exception("Failed to update user information.");
            }
            
            // Update host table
            $hostQuery = "UPDATE hosts SET 
                          property_type = ?, 
                          preferred_language = ?, 
                          bio = ?, 
                          location = ? 
                          WHERE host_id = ?";
            $hostParams = [
                $this->propertyType,
                $this->preferredLanguage,
                $this->bio,
                $this->location,
                $this->userID
            ];
            
            $hostResult = $this->db->update($hostQuery, "ssssi", $hostParams);
            
            if (!$hostResult) {
                throw new Exception("Failed to update host information.");
            }
            
            return true;
        } catch (Exception $e) {
            error_log("Update failed: " . $e->getMessage());
            return false;
        } finally {
            $this->db->closeConnection();
        }
    }
}
?>
