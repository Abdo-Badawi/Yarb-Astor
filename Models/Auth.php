<?php
include_once '../Models/User.php';
include_once '../Models/Database.php';
use Models\User;

class Auth {
        protected $db;

        public function logout(){
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
            header("Location: login.php");
            exit();
        }

        public function register(User $user) {
            $this->db = new Database();
            if ($this->db->openConnection()) {
                // Ensure we have a valid connection before starting transaction
                if (!$this->db->conn) {
                    if (!$this->db->openConnection()) {
                        error_log("Failed to establish database connection");
                        return false;
                    }
                }

                // Recheck if the connection is valid before starting the transaction
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
                              VALUES ( ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, NOW())";
                    $params = [

                        $user->getFirstName(),
                        $user->getLastName(),
                        $user->getEmail(),
                        $user->getPassword(), // Ensure this is hashed before passing
                        $user->getPhoneNumber(),
                        $user->getProfilePicture(),
                        $user->getGender(),
                        $user->getNationalID(),
                        $user->getUserType(),
                        $user->getBirthday()
                    ];

                    $result = $this->db->insert($query, "ssssssssss", $params);

                    if (!$result) {
                        throw new Exception("Failed to insert into users table.");
                    }

                    // Insert into the related table based on user type
                    $user_id = $this->db->conn->insert_id;
                    $_SESSION['userID'] = $user_id; // Store the user ID in the session
                    $_SESSION['userType'] = $user->getUserType(); // Store the user type in the session

                    // Add a session token for additional security
                    if (!isset($_SESSION['auth_token'])) {
                        $_SESSION['auth_token'] = bin2hex(random_bytes(32));
                    }
                    if ($user->getUserType() === 'traveler') {

                        $travelerQuery = "INSERT INTO traveler (traveler_id, skill, language_spoken, preferred_language, bio, location, joined_date)
                                          VALUES (?, ?, ?, ?, ?, ?, NOW())";
                        $travelerParams = [
                            $user_id, // Use the same user_id as a foreign key
                            $user->getSkills(),
                            $user->getLanguageSpoken(),
                            $user->getPreferredLanguage(),
                            $user->getBio(),
                            $user->getLocation()
                        ];
                        $travelerResult = $this->db->insert($travelerQuery, "isssss", $travelerParams);

                        if (!$travelerResult) {
                            throw new Exception("Failed to insert into traveler table.");
                        }
                    } elseif ($user->getUserType() === 'host') {
                        $hostQuery = "INSERT INTO hosts (host_id, property_type, preferred_language, bio, location, joined_date)
                                      VALUES (?, ?, ?, ?, ?, NOW())";
                        $hostParams = [
                            $user_id, // Use the same user_id as a foreign key
                            $user->getPropertyType(),
                            $user->getPreferredLanguage(),
                            $user->getBio(),
                            $user->getLocation()
                        ];
                        $hostResult = $this->db->insert($hostQuery, "issss", $hostParams);

                        if (!$hostResult) {
                            throw new Exception("Failed to insert into hosts table.");
                        }
                    }

                    // Commit the transaction
                    $this->db->conn->commit();
                    return true; // Registration successful

                } catch (Exception $e) {
                    // Rollback the transaction on failure
                    $this->db->conn->rollback();
                    error_log("Transaction failed: " . $e->getMessage());
                    return false;
                }
            } else {
                echo "Connection failed";
                return false;
            }
        }
}
?>
