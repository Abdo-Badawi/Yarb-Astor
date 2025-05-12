<?php
namespace Models;

require_once 'User.php';
require_once __DIR__ . '/../Controllers/DBController.php';

class Admin implements User {
    public string $AdminID;
    public string $Username;
    public string $Password;
    public string $Role; // Enum
    public \DateTime $CreatedAt;
    public \DateTime $LastLogin;
    private $db;
    
    public function __construct() {
        $this->db = new \DBController();
    }
    
    public function login(): bool {
        if (empty($this->Username) || empty($this->Password)) {
            return false;
        }
        
        $this->db->openConnection();
        $query = "SELECT * FROM users WHERE username = ? AND user_type = 'admin'";
        $result = $this->db->selectPrepared($query, "s", [$this->Username]);
        
        if ($result && count($result) > 0) {
            $admin = $result[0];
            if (password_verify($this->Password, $admin['password'])) {
                // Update last login time
                $updateQuery = "UPDATE users SET last_login = NOW() WHERE user_id = ?";
                $this->db->update($updateQuery, "i", [$admin['user_id']]);
                
                // Set admin properties
                $this->AdminID = $admin['user_id'];
                $this->Role = $admin['role'] ?? 'admin';
                $this->CreatedAt = new \DateTime($admin['created_at']);
                $this->LastLogin = new \DateTime();
                
                $this->db->closeConnection();
                return true;
            }
        }
        
        $this->db->closeConnection();
        return false;
    }
    
    public function manageFees(): bool {
        // Check if admin has permission to manage fees
        if (empty($this->AdminID) || !in_array($this->Role, ['admin', 'super_admin'])) {
            return false;
        }
        
        return true;
    }
    
    public function approveOpportunity(): bool {
        // Check if admin has permission to approve opportunities
        if (empty($this->AdminID) || !in_array($this->Role, ['admin', 'super_admin', 'content_manager'])) {
            return false;
        }
        
        return true;
    }
    
    public function suspendUser(): bool {
        // Check if admin has permission to suspend users
        if (empty($this->AdminID) || !in_array($this->Role, ['admin', 'super_admin', 'user_manager'])) {
            return false;
        }
        
        return true;
    }
    
    public function manageReports(): bool {
        // Check if admin has permission to manage reports
        if (empty($this->AdminID) || !in_array($this->Role, ['admin', 'super_admin', 'support_agent'])) {
            return false;
        }
        
        return true;
    }
    
    public function faqSupport(): bool {
        // Check if admin has permission to manage FAQ
        if (empty($this->AdminID) || !in_array($this->Role, ['admin', 'super_admin', 'content_manager', 'support_agent'])) {
            return false;
        }
        
        return true;
    }
    
    public function resetPassword(): bool {
        // Check if admin has permission to reset passwords
        if (empty($this->AdminID) || !in_array($this->Role, ['admin', 'super_admin'])) {
            return false;
        }
        
        return true;
    }
    
    public function updateProfile(): bool {
        if (empty($this->AdminID)) {
            return false;
        }
        
        $this->db->openConnection();
        $query = "UPDATE users SET 
                 username = ?,
                 password = ?,
                 role = ?
                 WHERE user_id = ? AND user_type = 'admin'";
                 
        $params = [
            $this->Username,
            $this->Password, // Note: Password should be hashed before storing
            $this->Role,
            $this->AdminID
        ];
        
        $result = $this->db->update($query, "sssi", $params);
        $this->db->closeConnection();
        
        return $result;
    }
    
    // Additional methods for admin functionality
    
    public function getAdminById(int $adminId): ?Admin {
        $this->db->openConnection();
        $query = "SELECT * FROM users WHERE user_id = ? AND user_type = 'admin'";
        $result = $this->db->selectPrepared($query, "i", [$adminId]);
        $this->db->closeConnection();
        
        if ($result && count($result) > 0) {
            $admin = new Admin();
            $admin->AdminID = $result[0]['user_id'];
            $admin->Username = $result[0]['username'];
            $admin->Password = $result[0]['password']; // Note: This is the hashed password
            $admin->Role = $result[0]['role'] ?? 'admin';
            $admin->CreatedAt = new \DateTime($result[0]['created_at']);
            $admin->LastLogin = new \DateTime($result[0]['last_login'] ?? 'now');
            
            return $admin;
        }
        
        return null;
    }
    
    public function getAllAdmins(): array {
        $this->db->openConnection();
        $query = "SELECT * FROM users WHERE user_type = 'admin'";
        $result = $this->db->select($query);
        $this->db->closeConnection();
        
        $admins = [];
        if ($result) {
            foreach ($result as $row) {
                $admin = new Admin();
                $admin->AdminID = $row['user_id'];
                $admin->Username = $row['username'];
                $admin->Password = $row['password']; // Note: This is the hashed password
                $admin->Role = $row['role'] ?? 'admin';
                $admin->CreatedAt = new \DateTime($row['created_at']);
                $admin->LastLogin = new \DateTime($row['last_login'] ?? 'now');
                
                $admins[] = $admin;
            }
        }
        
        return $admins;
    }
}
