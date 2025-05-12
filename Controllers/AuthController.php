<?php
include_once '../Models/User.php';
include_once '../Models/Traveler.php';
include_once '../Models/Host.php';
use Models\User;
use Models\Traveler;
use Models\Host;
include_once '../Controllers/DBController.php';

class AuthController {
    protected $db;

    public function __construct() {
        $this->db = new DBController();
    }

    /**
     * Login a user
     * 
     * @param string $email User email
     * @param string $password User password
     * @return array|bool User data if login successful, false otherwise
     */
    public function login(string $email, string $password) {
        if ($this->db->openConnection()) {
            // Query to fetch the user by email
            $query = "SELECT user_id, password, user_type FROM users WHERE email = ?";
            $params = [$email];
            $result = $this->db->selectPrepared($query, "s", $params);
            $this->db->closeConnection();

            if ($result && count($result) > 0) {
                $userID = $result[0]['user_id'];
                $dbPassword = $result[0]['password'];
                $userType = $result[0]['user_type'];

                // Verify the entered password with the hashed password
                if (password_verify($password, $dbPassword)) {
                    // Start the session and set session variables
                    session_start();
                    $_SESSION['email'] = $email;
                    $_SESSION['userType'] = $userType;
                    $_SESSION['userID'] = $userID;

                    // Add a session token for additional security
                    if (!isset($_SESSION['auth_token'])) {
                        $_SESSION['auth_token'] = bin2hex(random_bytes(32));
                    }
                    
                    // Update last login timestamp
                    $this->updateLastLogin($userID);

                    return [
                        'user_id' => $userID,
                        'user_type' => $userType,
                        'email' => $email
                    ]; // Login successful
                } else {
                    // Invalid password
                    session_start();
                    $_SESSION['errMsg'] = "Invalid email or password.";
                    return false;
                }
            } else {
                // User not found
                session_start();
                $_SESSION['errMsg'] = "Invalid email or password.";
                return false;
            }
        } else {
            error_log("Database connection failed.");
            return false;
        }
    }

    /**
     * Logout a user
     */
    public function logout() {
        session_start(); // Always start the session first

        // Unset all session variables
        $_SESSION = array();

        // Destroy the session cookie
        if (isset($_COOKIE[session_name()])) {
            setcookie(session_name(), '', time() - 3600, '/');
        }

        // Destroy the session
        session_destroy();

        // Redirect to login page
        header("Location: ../Common/login.php");
        exit();
    }

    /**
     * Register a new user
     * 
     * @param array $userData User data
     * @return int|bool User ID if registration successful, false otherwise
     */
    public function register(array $userData) {
        if (!$this->db->openConnection()) {
            error_log("Failed to establish database connection");
            return false;
        }

        try {
            // Start transaction
            $this->db->beginTransaction();
            
            // Hash the password
            $hashedPassword = password_hash($userData['password'], PASSWORD_DEFAULT);
            
            // Insert into the `users` table
            $query = "INSERT INTO users
                      (first_name, last_name, email, password, phone_number, profile_picture, gender, national_id, user_type, date_of_birth, created_at)
                      VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, NOW())";
            $params = [
                $userData['first_name'],
                $userData['last_name'],
                $userData['email'],
                $hashedPassword,
                $userData['phone_number'] ?? null,
                $userData['profile_picture'] ?? null,
                $userData['gender'] ?? null,
                $userData['national_id'] ?? null,
                $userData['user_type'],
                $userData['date_of_birth'] ?? null
            ];

            $result = $this->db->insert($query, "ssssssssss", $params);

            if (!$result) {
                throw new \Exception("Failed to insert into users table.");
            }

            // Get the last insert ID
            $user_id = $this->db->getInsertId();
            
            // Start session and store user info
            session_start();
            $_SESSION['userID'] = $user_id; // Store the user ID in the session
            $_SESSION['userType'] = $userData['user_type']; // Store the user type in the session
            $_SESSION['email'] = $userData['email']; // Store the email in the session

            // Add a session token for additional security
            if (!isset($_SESSION['auth_token'])) {
                $_SESSION['auth_token'] = bin2hex(random_bytes(32));
            }
            
            if ($userData['user_type'] === 'traveler') {
                $travelerQuery = "INSERT INTO traveler (traveler_id, skill, language_spoken, preferred_language, bio, location, joined_date)
                                  VALUES (?, ?, ?, ?, ?, ?, NOW())";
                $travelerParams = [
                    $user_id, // Use the same user_id as a foreign key
                    $userData['skills'] ?? '',
                    $userData['language_spoken'] ?? '',
                    $userData['preferred_language'] ?? '',
                    $userData['bio'] ?? '',
                    $userData['location'] ?? ''
                ];
                $travelerResult = $this->db->insert($travelerQuery, "isssss", $travelerParams);

                if (!$travelerResult) {
                    throw new \Exception("Failed to insert into traveler table.");
                }
            } elseif ($userData['user_type'] === 'host') {
                $hostQuery = "INSERT INTO hosts (host_id, property_type, preferred_language, bio, location, joined_date)
                              VALUES (?, ?, ?, ?, ?, NOW())";
                $hostParams = [
                    $user_id, // Use the same user_id as a foreign key
                    $userData['property_type'] ?? '',
                    $userData['preferred_language'] ?? '',
                    $userData['bio'] ?? '',
                    $userData['location'] ?? ''
                ];
                $hostResult = $this->db->insert($hostQuery, "issss", $hostParams);

                if (!$hostResult) {
                    throw new \Exception("Failed to insert into hosts table.");
                }
            }

            // Commit the transaction
            $this->db->commitTransaction();
            $this->db->closeConnection();
            return $user_id; // Registration successful

        } catch (\Exception $e) {
            // Rollback the transaction on failure
            $this->db->rollbackTransaction();
            error_log("Transaction failed: " . $e->getMessage());
            $this->db->closeConnection();
            return false;
        }
    }

    /**
     * Check if email exists
     * 
     * @param string $email Email to check
     * @return bool True if email exists, false otherwise
     */
    public function emailExists(string $email): bool {
        if ($this->db->openConnection()) {
            $query = "SELECT COUNT(*) as count FROM users WHERE email = ?";
            $params = [$email];
            $result = $this->db->selectPrepared($query, "s", $params);
            $this->db->closeConnection();
            
            return $result && $result[0]['count'] > 0;
        }
        return false;
    }

    /**
     * Check if national ID exists
     * 
     * @param string $nationalID National ID to check
     * @return bool True if national ID exists, false otherwise
     */
    public function nationalIDExists(string $nationalID): bool {
        if ($this->db->openConnection()) {
            $query = "SELECT COUNT(*) as count FROM users WHERE national_id = ?";
            $params = [$nationalID];
            $result = $this->db->selectPrepared($query, "s", $params);
            $this->db->closeConnection();
            
            return $result && $result[0]['count'] > 0;
        }
        return false;
    }

    /**
     * Get user by ID
     * 
     * @param int $userId User ID
     * @return array|null User data if found, null otherwise
     */
    public function getUserById(int $userId): ?array {
        if ($this->db->openConnection()) {
            $query = "SELECT * FROM users WHERE user_id = ?";
            $params = [$userId];
            $result = $this->db->selectPrepared($query, "i", $params);
            $this->db->closeConnection();
            
            if ($result && count($result) > 0) {
                return $result[0];
            }
        }
        return null;
    }

    /**
     * Get user by email
     * 
     * @param string $email User email
     * @return array|null User data if found, null otherwise
     */
    public function getUserByEmail(string $email): ?array {
        if ($this->db->openConnection()) {
            $query = "SELECT * FROM users WHERE email = ?";
            $params = [$email];
            $result = $this->db->selectPrepared($query, "s", $params);
            $this->db->closeConnection();
            
            if ($result && count($result) > 0) {
                return $result[0];
            }
        }
        return null;
    }

    /**
     * Update last login timestamp
     * 
     * @param int $userId User ID
     * @return bool True if update successful, false otherwise
     */
    private function updateLastLogin(int $userId): bool {
        if ($this->db->openConnection()) {
            $query = "UPDATE users SET last_login = NOW() WHERE user_id = ?";
            $params = [$userId];
            $result = $this->db->update($query, "i", $params);
            $this->db->closeConnection();
            
            return $result;
        }
        return false;
    }

    /**
     * Change user password
     * 
     * @param int $userId User ID
     * @param string $currentPassword Current password
     * @param string $newPassword New password
     * @return bool True if password changed successfully, false otherwise
     */
    public function changePassword(int $userId, string $currentPassword, string $newPassword): bool {
        if ($this->db->openConnection()) {
            // First, verify the current password
            $query = "SELECT password FROM users WHERE user_id = ?";
            $params = [$userId];
            $result = $this->db->selectPrepared($query, "i", $params);
            
            if ($result && count($result) > 0) {
                $dbPassword = $result[0]['password'];
                
                // Verify the current password
                if (password_verify($currentPassword, $dbPassword)) {
                    // Hash the new password
                    $hashedPassword = password_hash($newPassword, PASSWORD_DEFAULT);
                    
                    // Update the password
                    $updateQuery = "UPDATE users SET password = ? WHERE user_id = ?";
                    $updateParams = [$hashedPassword, $userId];
                    $updateResult = $this->db->update($updateQuery, "si", $updateParams);
                    $this->db->closeConnection();
                    
                    return $updateResult;
                }
            }
            
            $this->db->closeConnection();
        }
        return false;
    }

    /**
     * Reset password (for forgotten passwords)
     * 
     * @param string $email User email
     * @param string $newPassword New password
     * @return bool True if password reset successful, false otherwise
     */
    public function resetPassword(string $email, string $newPassword): bool {
        if ($this->db->openConnection()) {
            // Hash the new password
            $hashedPassword = password_hash($newPassword, PASSWORD_DEFAULT);
            
            // Update the password
            $query = "UPDATE users SET password = ? WHERE email = ?";
            $params = [$hashedPassword, $email];
            $result = $this->db->update($query, "ss", $params);
            $this->db->closeConnection();
            
            return $result;
        }
        return false;
    }

    /**
     * Generate password reset token
     * 
     * @param string $email User email
     * @return string|null Token if generated successfully, null otherwise
     */
    public function generatePasswordResetToken(string $email): ?string {
        if ($this->db->openConnection()) {
            // Check if email exists
            $query = "SELECT user_id FROM users WHERE email = ?";
            $params = [$email];
            $result = $this->db->selectPrepared($query, "s", $params);
            
            if ($result && count($result) > 0) {
                // Generate token
                $token = bin2hex(random_bytes(32));
                $expiry = date('Y-m-d H:i:s', strtotime('+1 hour'));
                
                // Store token in database
                $tokenQuery = "INSERT INTO password_reset_tokens (email, token, expiry) VALUES (?, ?, ?)";
                $tokenParams = [$email, $token, $expiry];
                $tokenResult = $this->db->insert($tokenQuery, "sss", $tokenParams);
                $this->db->closeConnection();
                
                if ($tokenResult) {
                    return $token;
                }
            }
            
            $this->db->closeConnection();
        }
        return null;
    }

    /**
     * Verify password reset token
     * 
     * @param string $email User email
     * @param string $token Token to verify
     * @return bool True if token is valid, false otherwise
     */
    public function verifyPasswordResetToken(string $email, string $token): bool {
        if ($this->db->openConnection()) {
            $query = "SELECT * FROM password_reset_tokens WHERE email = ? AND token = ? AND expiry > NOW()";
            $params = [$email, $token];
            $result = $this->db->selectPrepared($query, "ss", $params);
            $this->db->closeConnection();
            
            return $result && count($result) > 0;
        }
        return false;
    }

    /**
     * Delete password reset token
     * 
     * @param string $email User email
     * @param string $token Token to delete
     * @return bool True if token deleted successfully, false otherwise
     */
    public function deletePasswordResetToken(string $email, string $token): bool {
        if ($this->db->openConnection()) {
            $query = "DELETE FROM password_reset_tokens WHERE email = ? AND token = ?";
            $params = [$email, $token];
            $result = $this->db->delete($query, "ss", $params);
            $this->db->closeConnection();
            
            return $result;
        }
        return false;
    }
}
?>






