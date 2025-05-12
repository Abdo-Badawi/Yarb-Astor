<?php
require_once 'DBController.php';
require_once '../Models/Host.php';
use Models\Host;

class HostController {
    private $db;
    
    public function __construct() {
        $this->db = new DBController();
    }
    
    /**
     * Get host by ID
     * 
     * @param int $hostId Host ID
     * @return array|null Host data if found, null otherwise
     */
    public function getHostById(int $hostId): ?array {
        $sql = "SELECT u.*, h.* 
                FROM users u
                JOIN hosts h ON u.user_id = h.host_id
                WHERE u.user_id = ? AND u.user_type = 'host'";
        
        $this->db->openConnection();
        $result = $this->db->selectPrepared($sql, "i", [$hostId]);
        $this->db->closeConnection();
        
        return $result ? $result[0] : null;
    }
    
    /**
     * Get host by ID as Host object
     * 
     * @param int $hostId Host ID
     * @return Host|null Host object if found, null otherwise
     */
    public function getHostByIdAsObject(int $hostId): ?Host {
        $hostData = $this->getHostById($hostId);
        
        if (!$hostData) {
            return null;
        }
        
        return Host::fromArray($hostData);
    }
    
    /**
     * Get all hosts
     * 
     * @return array List of all hosts
     */
    public function getAllHosts(): array {
        $sql = "SELECT u.*, h.* 
                FROM users u
                JOIN hosts h ON u.user_id = h.host_id
                WHERE u.user_type = 'host'";
        
        $this->db->openConnection();
        $result = $this->db->select($sql);
        $this->db->closeConnection();
        
        return $result ?: [];
    }
    
    /**
     * Get all hosts as Host objects
     * 
     * @return array List of all hosts as Host objects
     */
    public function getAllHostsAsObjects(): array {
        $hostsData = $this->getAllHosts();
        $hosts = [];
        
        foreach ($hostsData as $hostData) {
            $hosts[] = Host::fromArray($hostData);
        }
        
        return $hosts;
    }
    
    /**
     * Update host profile
     * 
     * @param Host $host Host object with updated data
     * @return bool True if update successful, false otherwise
     */
    public function updateHost(Host $host): bool {
        // Convert Host object to array
        $data = $host->toArray();
        $hostId = $host->getHostID();
        
        // Start transaction
        $this->db->openConnection();
        $this->db->getConnection()->begin_transaction();
        
        try {
            // Update users table
            $userSql = "UPDATE users SET 
                    first_name = ?, 
                    last_name = ?, 
                    email = ?, 
                    phone_number = ?, 
                    date_of_birth = ?, 
                    gender = ?,
                    profile_picture = ?
                    WHERE user_id = ? AND user_type = 'host'";
            
            $userParams = [
                $data['first_name'],
                $data['last_name'],
                $data['email'],
                $data['phone_number'],
                $data['date_of_birth'],
                $data['gender'],
                $data['profile_picture'],
                $hostId
            ];
            
            $userResult = $this->db->update($userSql, "ssssssi", $userParams);
            
            // Update hosts table
            $hostSql = "UPDATE hosts SET 
                    property_type = ?, 
                    preferred_language = ?, 
                    bio = ?, 
                    location = ?, 
                    rate = ?,
                    status = ?
                    WHERE host_id = ?";
            
            $hostParams = [
                $data['property_type'],
                $data['preferred_language'],
                $data['bio'],
                $data['location'],
                $data['rate'],
                $data['status'],
                $hostId
            ];
            
            $hostResult = $this->db->update($hostSql, "ssssdsi", $hostParams);
            
            // If both updates were successful, commit the transaction
            if ($userResult && $hostResult) {
                $this->db->getConnection()->commit();
                $this->db->closeConnection();
                return true;
            } else {
                // If either update failed, roll back the transaction
                $this->db->getConnection()->rollback();
                $this->db->closeConnection();
                return false;
            }
        } catch (\Exception $e) {
            // If an exception occurred, roll back the transaction
            $this->db->getConnection()->rollback();
            $this->db->closeConnection();
            error_log("Error updating host: " . $e->getMessage());
            return false;
        }
    }
    
    /**
     * Create new host
     * 
     * @param Host $host Host object with data
     * @return int|bool Host ID if successful, false otherwise
     */
    public function createHost(Host $host): int|bool {
        // Convert Host object to array
        $data = $host->toArray();
        
        // Start transaction
        $this->db->openConnection();
        $this->db->getConnection()->begin_transaction();
        
        try {
            // Insert into users table
            $userSql = "INSERT INTO users (user_type, first_name, last_name, email, password, phone_number, date_of_birth, gender, profile_picture, created_at) 
                    VALUES ('host', ?, ?, ?, ?, ?, ?, ?, ?, NOW())";
            
            $userParams = [
                $data['first_name'],
                $data['last_name'],
                $data['email'],
                password_hash($data['password'], PASSWORD_DEFAULT),
                $data['phone_number'] ?? null,
                $data['date_of_birth'] ?? null,
                $data['gender'] ?? null,
                $data['profile_picture'] ?? null
            ];
            
            $userResult = $this->db->insert($userSql, "ssssssss", $userParams);
            
            if (!$userResult) {
                $this->db->getConnection()->rollback();
                $this->db->closeConnection();
                return false;
            }
            
            // Get the inserted user ID
            $hostId = $this->db->getInsertId();
            
            // Insert into hosts table
            $hostSql = "INSERT INTO hosts (host_id, property_type, preferred_language, bio, location, rate, joined_date, status) 
                    VALUES (?, ?, ?, ?, ?, ?, NOW(), ?)";
            
            $hostParams = [
                $hostId,
                $data['property_type'] ?? null,
                $data['preferred_language'] ?? null,
                $data['bio'] ?? null,
                $data['location'] ?? null,
                $data['rate'] ?? null,
                $data['status'] ?? 'active'
            ];
            
            $hostResult = $this->db->insert($hostSql, "issssds", $hostParams);
            
            if (!$hostResult) {
                $this->db->getConnection()->rollback();
                $this->db->closeConnection();
                return false;
            }
            
            // Commit the transaction
            $this->db->getConnection()->commit();
            $this->db->closeConnection();
            
            // Update the host object with the new ID
            $host->setHostID((string)$hostId);
            
            return $hostId;
        } catch (\Exception $e) {
            // If an exception occurred, roll back the transaction
            $this->db->getConnection()->rollback();
            $this->db->closeConnection();
            error_log("Error creating host: " . $e->getMessage());
            return false;
        }
    }
    
    /**
     * Delete host
     * 
     * @param int $hostId Host ID
     * @return bool True if deletion successful, false otherwise
     */
    public function deleteHost(int $hostId): bool {
        // Start transaction
        $this->db->openConnection();
        $this->db->getConnection()->begin_transaction();
        
        try {
            // Delete from hosts table first (due to foreign key constraint)
            $hostSql = "DELETE FROM hosts WHERE host_id = ?";
            $hostResult = $this->db->delete($hostSql, "i", [$hostId]);
            
            if (!$hostResult) {
                $this->db->getConnection()->rollback();
                $this->db->closeConnection();
                return false;
            }
            
            // Delete from users table
            $userSql = "DELETE FROM users WHERE user_id = ? AND user_type = 'host'";
            $userResult = $this->db->delete($userSql, "i", [$hostId]);
            
            if (!$userResult) {
                $this->db->getConnection()->rollback();
                $this->db->closeConnection();
                return false;
            }
            
            // Commit the transaction
            $this->db->getConnection()->commit();
            $this->db->closeConnection();
            
            return true;
        } catch (\Exception $e) {
            // If an exception occurred, roll back the transaction
            $this->db->getConnection()->rollback();
            $this->db->closeConnection();
            error_log("Error deleting host: " . $e->getMessage());
            return false;
        }
    }
    
    /**
     * Get hosts by location
     * 
     * @param string $location Location to search for
     * @return array List of hosts matching the location
     */
    public function getHostsByLocation(string $location): array {
        $sql = "SELECT u.*, h.* 
                FROM users u
                JOIN hosts h ON u.user_id = h.host_id
                WHERE u.user_type = 'host' AND h.location LIKE ?";
        
        $this->db->openConnection();
        $result = $this->db->selectPrepared($sql, "s", ["%$location%"]);
        $this->db->closeConnection();
        
        return $result ?: [];
    }
    
    /**
     * Get hosts by location as Host objects
     * 
     * @param string $location Location to search for
     * @return array List of hosts matching the location as Host objects
     */
    public function getHostsByLocationAsObjects(string $location): array {
        $hostsData = $this->getHostsByLocation($location);
        $hosts = [];
        
        foreach ($hostsData as $hostData) {
            $hosts[] = Host::fromArray($hostData);
        }
        
        return $hosts;
    }
    
    /**
     * Get host reviews
     * 
     * @param int $hostId Host ID
     * @return array List of reviews for the host
     */
    public function getHostReviews(int $hostId): array {
        $sql = "SELECT r.*, u.first_name, u.last_name, u.profile_picture 
                FROM reviews r 
                JOIN users u ON r.reviewer_id = u.user_id 
                WHERE r.host_id = ? 
                ORDER BY r.created_at DESC";
        
        $this->db->openConnection();
        $result = $this->db->selectPrepared($sql, "i", [$hostId]);
        $this->db->closeConnection();
        
        return $result ?: [];
    }
    
    /**
     * Get host opportunities
     * 
     * @param int $hostId Host ID
     * @return array List of opportunities created by the host
     */
    public function getHostOpportunities(int $hostId): array {
        $sql = "SELECT * FROM opportunity WHERE host_id = ? ORDER BY created_at DESC";
        
        $this->db->openConnection();
        $result = $this->db->selectPrepared($sql, "i", [$hostId]);
        $this->db->closeConnection();
        
        return $result ?: [];
    }
    
    /**
     * Get host active opportunities
     * 
     * @param int $hostId Host ID
     * @return array List of active opportunities created by the host
     */
    public function getHostActiveOpportunities(int $hostId): array {
        $sql = "SELECT * FROM opportunity 
                WHERE host_id = ? AND status = 'open' AND end_date >= CURDATE()";
        
        $this->db->openConnection();
        $result = $this->db->selectPrepared($sql, "i", [$hostId]);
        $this->db->closeConnection();
        
        return $result ?: [];
    }
    
    /**
     * Authenticate host
     * 
     * @param string $email Host email
     * @param string $password Host password
     * @return Host|null Host object if authentication successful, null otherwise
     */
    public function authenticateHost(string $email, string $password): ?Host {
        $sql = "SELECT u.*, h.* 
                FROM users u
                LEFT JOIN hosts h ON u.user_id = h.host_id
                WHERE u.email = ? AND u.user_type = 'host'";
        
        $this->db->openConnection();
        $result = $this->db->selectPrepared($sql, "s", [$email]);
        $this->db->closeConnection();
        
        if ($result && password_verify($password, $result[0]['password'])) {
            return Host::fromArray($result[0]);
        }
        
        return null;
    }
}
?>

