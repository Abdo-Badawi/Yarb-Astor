<?php
require_once 'DBController.php';

class TravelerController {
    private $db;
    
    public function __construct() {
        $this->db = new DBController();
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
}
?>


