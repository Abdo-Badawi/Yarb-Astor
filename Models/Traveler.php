<?php
require_once __DIR__ . '/Database.php';
require_once __DIR__ . '/User.php';

class Traveler extends User {
    protected $db;

    public function __construct() {
        $this->db = new Database();
    }

    /**
     * Get traveler by ID
     * 
     * @param int $travelerId Traveler ID
     * @return array|null Traveler data or null if not found
     */
    public function getTravelerById($travelerId) {
        if (!$this->db->openConnection()) {
            return null;
        }
        
        // Query to get traveler data from both users and travelers tables
        $query = "SELECT u.user_id, u.first_name, u.last_name, u.email, u.phone_number, 
                 u.profile_picture, u.created_at, u.last_login, u.user_type,
                 t.nationality, t.language_spoken, t.skill, t.bio, t.status
                 FROM users u 
                 LEFT JOIN travelers t ON u.user_id = t.traveler_id
                 WHERE u.user_id = ? AND u.user_type = 'traveler'";
        $params = [$travelerId];
        
        $result = $this->db->selectPrepared($query, "i", $params);
        
        $this->db->closeConnection();
        
        return $result && count($result) > 0 ? $result[0] : null;
    }

    /**
     * Get all travelers
     * 
     * @return array Array of travelers
     */
    public function getAllTravelers() {
        if (!$this->db->openConnection()) {
            return [];
        }
        
        // Query to get all travelers
        $query = "SELECT u.user_id, u.first_name, u.last_name, u.email, u.phone_number, 
                 u.profile_picture, u.created_at, u.last_login, u.user_type,
                 t.nationality, t.language_spoken, t.skill, t.bio, t.status
                 FROM users u 
                 LEFT JOIN travelers t ON u.user_id = t.traveler_id
                 WHERE u.user_type = 'traveler'
                 ORDER BY u.last_name, u.first_name";
        
        $result = $this->db->select($query);
        
        $this->db->closeConnection();
        
        return $result ?: [];
    }

    /**
     * Update traveler profile
     * 
     * @param array $travelerData Traveler data
     * @return bool True if update was successful, false otherwise
     */
    public function updateTravelerProfile($travelerData) {
        if (!$this->db->openConnection()) {
            return false;
        }
        
        // Start transaction
        $this->db->openConnection();
        
        try {
            $travelerId = $travelerData['traveler_id'];
            
            // Update users table
            $userQuery = "UPDATE users SET 
                         first_name = ?,
                         last_name = ?,
                         email = ?,
                         phone_number = ? 
                         WHERE user_id = ? AND user_type = 'traveler'";
            $userParams = [
                $travelerData['first_name'],
                $travelerData['last_name'],
                $travelerData['email'],
                $travelerData['phone_number'],
                $travelerId
            ];
            
            $userResult = $this->db->update($userQuery, "ssssi", $userParams);
            
            if (!$userResult) {
                throw new Exception("Failed to update user data");
            }
            
            // Update travelers table
            $travelerQuery = "UPDATE travelers SET 
                             nationality = ?,
                             language_spoken = ?,
                             skill = ?
                             WHERE traveler_id = ?";
            $travelerParams = [
                $travelerData['nationality'],
                $travelerData['language_spoken'],
                $travelerData['skill'],
                $travelerId
            ];
            
            $travelerResult = $this->db->update($travelerQuery, "sssi", $travelerParams);
            
            if (!$travelerResult) {
                throw new Exception("Failed to update traveler data");
            }
            
            // If profile picture is provided
            if (isset($travelerData['profile_picture']) && !empty($travelerData['profile_picture'])) {
                $profileQuery = "UPDATE users SET profile_picture = ? WHERE user_id = ?";
                $profileParams = [$travelerData['profile_picture'], $travelerId];
                
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
            error_log("Error updating traveler profile: " . $e->getMessage());
            return false;
        } finally {
            $this->db->closeConnection();
        }
    }

    /**
     * Search travelers by criteria
     * 
     * @param array $criteria Search criteria
     * @return array Array of matching travelers
     */
    public function searchTravelers($criteria = []) {
        if (!$this->db->openConnection()) {
            return [];
        }
        
        $whereConditions = [];
        $params = [];
        $types = "";
        
        // Build WHERE clause based on criteria
        if (!empty($criteria['name'])) {
            $whereConditions[] = "(u.first_name LIKE ? OR u.last_name LIKE ?)";
            $params[] = "%" . $criteria['name'] . "%";
            $params[] = "%" . $criteria['name'] . "%";
            $types .= "ss";
        }
        
        if (!empty($criteria['nationality'])) {
            $whereConditions[] = "t.nationality = ?";
            $params[] = $criteria['nationality'];
            $types .= "s";
        }
        
        if (!empty($criteria['language'])) {
            $whereConditions[] = "t.language_spoken LIKE ?";
            $params[] = "%" . $criteria['language'] . "%";
            $types .= "s";
        }
        
        if (!empty($criteria['skill'])) {
            $whereConditions[] = "t.skill LIKE ?";
            $params[] = "%" . $criteria['skill'] . "%";
            $types .= "s";
        }
        
        // Base query
        $query = "SELECT u.user_id, u.first_name, u.last_name, u.email, u.phone_number, 
                 u.profile_picture, u.created_at, u.last_login, u.user_type,
                 t.nationality, t.language_spoken, t.skill, t.bio, t.status
                 FROM users u 
                 LEFT JOIN travelers t ON u.user_id = t.traveler_id
                 WHERE u.user_type = 'traveler'";
        
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
     * Get traveler data for profile
     * 
     * @param int $travelerId Traveler ID
     * @return array|bool Traveler data or false on failure
     */
    public function getTravelerData($travelerId) {
        if (!$this->db->openConnection()) {
            return false;
        }
        
        // Query to get traveler data from both users and travelers tables
        $query = "SELECT u.user_id, u.first_name, u.last_name, u.email, u.phone_number, 
                 u.profile_picture, u.created_at, u.last_login, u.user_type,
                 t.nationality, t.language_spoken, t.skill, t.bio, t.status
                 FROM users u 
                 LEFT JOIN travelers t ON u.user_id = t.traveler_id
                 WHERE u.user_id = ? AND u.user_type = 'traveler'";
        $params = [$travelerId];
        
        $result = $this->db->selectPrepared($query, "i", $params);
        
        $this->db->closeConnection();
        
        return $result && count($result) > 0 ? $result[0] : false;
    }
}
?>
