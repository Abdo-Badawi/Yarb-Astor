<?php
include_once '../Models/User.php';
use Models\User;
include_once '../Controllers/DBController.php';
    class AuthController {
        protected $db;
        
        public function login(User $user) {
            $this->db = new DBController();
            if ($this->db->openConnection()) {
                // Query to fetch the user by email
                $query = "SELECT user_id, password, user_type FROM users WHERE email = ?";
                $params = [$user->getEmail()];
                $result = $this->db->selectPrepared($query, "s", $params);

                if ($result && count($result) > 0) {
                    $dbPassword = $result[0]['password']; // Hashed password from the database
                    $userType = $result[0]['user_type'];
                    $userID = $result[0]['user_id'];

                    // Verify the entered password with the hashed password
                    if (password_verify($user->getPassword(), $dbPassword)) {
                        // Start the session and set session variables
                        session_start();
                        $_SESSION['email'] = $user->getEmail();
                        $_SESSION['userType'] = $userType;
                        $_SESSION['userID'] = $userID;

                        return true; // Login successful
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
            $this->db = new DBController();
            if ($this->db->openConnection()) {
                // Start a transaction
                $this->db->conn->begin_transaction();

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