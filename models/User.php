<?php
namespace Models;
require_once __DIR__ . '/Database.php';

use Exception;
use mysqli;

class User {
    protected string $userID = '';
    protected string $email;
    protected string $password;
    protected string $userType;
    protected string $firstName;
    protected string $lastName;
    protected string $phoneNumber;
    protected string $profilePicture; 
    protected string $gender;
    protected string $birthday;
    protected string $nationalID;
    protected $db;
    
    public function __construct($email = '', $password = '') {
        $this->email = $email;
        $this->password = $password;
        $this->db = new \Database;
    }
    
    // Basic user getters and setters
    public function getEmail() {
        return $this->email;
    }
    
    public function getPassword() {
        return $this->password;
    }
    
    public function setFirstName($firstName) {
        $this->firstName = $firstName;
    }
    
    public function getFirstName() {
        return $this->firstName;
    }
    
    public function setLastName($lastName) {
        $this->lastName = $lastName;
    }
    
    public function getLastName() {
        return $this->lastName;
    }
    
    public function setPhoneNumber($phoneNumber) {
        $this->phoneNumber = $phoneNumber;
    }
    
    public function getPhoneNumber() {
        return $this->phoneNumber;
    }
    
    public function setProfilePicture($profilePicture) {
        $this->profilePicture = $profilePicture;
    }
    
    public function getProfilePicture() {
        return $this->profilePicture;
    }
    
    public function setGender($gender) {
        $this->gender = $gender;
    }
    
    public function getGender() {
        return $this->gender;
    }
    
    public function setBirthday($birthday) {
        $this->birthday = $birthday;
    }
    
    public function getBirthday() {
        return $this->birthday;
    }
    
    public function setNationalID($nationalID) {
        $this->nationalID = $nationalID;
    }
    
    public function getNationalID() {
        return $this->nationalID;
    }
    
    public function setUserType($userType) {
        $this->userType = $userType;
    }
    
    public function getUserType() {
        return $this->userType;
    }
    
    public function authenticate() {
        $query = "SELECT * FROM users WHERE email = ?";
        $result = $this->db->selectPrepared($query, "s", [$this->email]);
        
        if ($result && count($result) > 0) {
            // Verify password
            if (password_verify($this->password, $result[0]['password'])) {
                return [
                    'email' => $result[0]['email'],
                    'userID' => $result[0]['user_id'],
                    'userType' => $result[0]['user_type']
                ];
            }
        }
        
        return false;
    }

    public function registerUser() {
        return $this->register();
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
                $this->password, // Ensure this is hashed before passing
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
            
            // The specific user type registration will be handled by child classes
            
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
    
    // Method to be overridden by child classes
    protected function registerSpecificUserType($user_id) {
        // This will be implemented by child classes
        return true;
    }
}
?>

