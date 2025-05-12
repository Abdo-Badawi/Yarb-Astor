<?php

require_once '../Models/Database.php';
require_once '../Models/User.php';

class Traveler extends User{
    private $traveler_id;
    private $skill;
    private $language_spoken;
    private $preferred_language;
    private $joined_date;
    private $bio;
    private $rate;
    private $location;
    private $created_at;
    private $status;
    protected $db;
    
    public function __construct() {
        $this->db = new Database();
    }
    
    // Get traveler by ID
    public function getTravelerById(int $travelerId): ?array {
        $sql = "SELECT * FROM users WHERE user_id = ? AND user_type = 'traveler'";
        
        $this->db->openConnection();
        $result = $this->db->selectPrepared($sql, "i", [$travelerId]);
        $this->db->closeConnection();
        
        return $result ? $result[0] : null;
    }
    
    // Get all travelers
    public function getAllTravelers(): array {
        $sql = "SELECT * FROM users WHERE user_type = 'traveler'";
        
        $this->db->openConnection();
        $result = $this->db->select($sql);
        $this->db->closeConnection();
        
        return $result ?: [];
    }
    
    // Update traveler profile
    public function updateTravelerProfile(int $travelerId, array $data): bool {
        $sql = "UPDATE users SET 
                first_name = ?, 
                last_name = ?, 
                email = ?, 
                phone_number = ?, 
                date_of_birth = ?, 
                gender = ? 
                WHERE user_id = ? AND user_type = 'traveler'";
        
        $params = [
            $data['first_name'],
            $data['last_name'],
            $data['email'],
            $data['phone_number'],
            $data['date_of_birth'],
            $data['gender'],
            $travelerId
        ];
        
        $this->db->openConnection();
        $result = $this->db->update($sql, "ssssssi", $params);
        $this->db->closeConnection();
        
        return $result;
    }
    
    // Create new traveler
    public function createTraveler(array $data): int {
        $sql = "INSERT INTO users (user_type, first_name, last_name, email, password, phone_number, date_of_birth, gender, created_at) 
                VALUES ('traveler', ?, ?, ?, ?, ?, ?, ?, NOW())";
        
        $params = [
            $data['first_name'],
            $data['last_name'],
            $data['email'],
            password_hash($data['password'], PASSWORD_DEFAULT),
            $data['phone_number'] ?? null,
            $data['date_of_birth'] ?? null,
            $data['gender'] ?? null
        ];
        
        $this->db->openConnection();
        $result = $this->db->insert($sql, "sssssss", $params);
        $insertId = $this->db->getInsertId();
        $this->db->closeConnection();
        
        return $insertId;
    }
    
    // Authenticate traveler
    public function authenticateTraveler(string $email, string $password): ?array {
        $sql = "SELECT * FROM users WHERE email = ? AND user_type = 'traveler'";
        
        $this->db->openConnection();
        $result = $this->db->selectPrepared($sql, "s", [$email]);
        $this->db->closeConnection();
        
        if ($result && password_verify($password, $result[0]['password'])) {
            return $result[0];
        }
        
        return null;
    }
    
    // Delete traveler
    public function deleteTraveler(int $travelerId): bool {
        $sql = "DELETE FROM users WHERE user_id = ? AND user_type = 'traveler'";
        
        $this->db->openConnection();
        $result = $this->db->delete($sql, "i", [$travelerId]);
        $this->db->closeConnection();
        
        return $result;
    }

    public function toArray() {
        return [
            'traveler_id' => $this->traveler_id,
            'skill' => $this->skill,
            'language_spoken' => $this->language_spoken,
            'preferred_language' => $this->preferred_language,
            'joined_date' => $this->joined_date,
            'bio' => $this->bio,
            'rate' => $this->rate,
            'location' => $this->location,
            'created_at' => $this->created_at,
            'status' => $this->status,
        ];
    }

    public function getUserData($userID) {
        if (!$this->db->openConnection()) {
            return null; // If DB connection fails, return null
        }

        // First, check if the user exists in the traveler table
        $checkQuery = "SELECT * FROM traveler WHERE traveler_id = ?";
        $checkParams = [$userID];
        $travelerData = $this->db->selectPrepared($checkQuery, "i", $checkParams);
        
        // If no traveler record exists, create one
        if (!$travelerData) {
            // Insert a basic record for this user in the traveler table
            $insertQuery = "INSERT INTO traveler (traveler_id, status, rate) VALUES (?, 'active', 0)";
            $insertParams = [$userID];
            $this->db->insert($insertQuery, "i", $insertParams);
        }

        // Query to select user data
        $query = "
            SELECT users.*, traveler.* 
            FROM users 
            LEFT JOIN traveler ON users.user_id = traveler.traveler_id
            WHERE users.user_id = ?
        ";        
        // Prepare parameters
        $params = [$userID];
        
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



