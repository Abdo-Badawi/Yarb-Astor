<?php
    class DBController {
        public $conn; // Ensure this property is public so it can be accessed in AuthController

        private $dbHost = "localhost";
        private $dbUser = "root";
        private $dbPass = "";
        private $dbName = "homestay";

        public function openConnection() {
             $this->conn = new mysqli($this->dbHost, $this->dbUser, $this->dbPass, $this->dbName);
             if ($this->conn->connect_error) {
                // Use error logging instead of die in production
                error_log("Connection failed: " . $this->conn->connect_error);
                return false; // Indicate failure
             }
             return true; // Indicate success
        }
        
        public function closeConnection() {
            if ($this->conn) {
                $this->conn->close();
                $this->conn = null; // Clear the connection property
            }
        }
        public function getLastError(): string {
            return $this->conn->error ?? 'Unknown error';
        }
        
        // Original select method (kept for potential existing usage, but less safe)
        public function select($query) {
            if (!$this->conn) {
                error_log("DB Error: No connection available for select.");
                return false;
            }
            $result = $this->conn->query($query);
            if ($result) {
                return $result->fetch_all(MYSQLI_ASSOC);
            } else {
                error_log("DB Error: " . $this->conn->error . " | Query: " . $query);
                return false;
            }
        }

        // --- Added method for prepared SELECT statements --- 
        public function selectPrepared(string $query, string $types = "", array $params = []) {
            if (!$this->conn) {
                error_log("DB Error: No connection available for prepared select.");
                return false;
            }
            $stmt = $this->conn->prepare($query);
            if (!$stmt) {
                error_log("DB Error: Failed to prepare statement. " . $this->conn->error . " | Query: " . $query);
                return false;
            }

            if ($types !== "" && !empty($params)) {
                // Dynamically bind parameters
                if (!$stmt->bind_param($types, ...$params)) {
                     error_log("DB Error: Failed to bind parameters. " . $stmt->error);
                     $stmt->close();
                     return false;
                }
            }

            if (!$stmt->execute()) {
                error_log("DB Error: Failed to execute statement. " . $stmt->error);
                $stmt->close();
                return false;
            }

            $result = $stmt->get_result();
            if (!$result) {
                 error_log("DB Error: Failed to get result set. " . $stmt->error);
                 $stmt->close();
                 return false;
            }
            
            $data = $result->fetch_all(MYSQLI_ASSOC);
            $stmt->close();
            return $data;
        }
        // --- End of added method ---
        
        public function insert(string $query, string $types, array $params): bool {
             if (!$this->conn) {
                error_log("DB Error: No connection available for insert.");
                return false;
            }
            $stmt = $this->conn->prepare($query);
            if (!$stmt) { 
                error_log("DB Error: Failed to prepare insert statement. " . $this->conn->error . " | Query: " . $query);
                return false;
            }
            
            if (!$stmt->bind_param($types, ...$params)) {
                 error_log("DB Error: Failed to bind insert parameters. " . $stmt->error);
                 $stmt->close();
                 return false;
            }
            
            $result = $stmt->execute();
             if (!$result) {
                error_log("DB Error: Failed to execute insert statement. " . $stmt->error);
            }
            $stmt->close();
            return $result;
        }
        
        // Function to update records
        public function update(string $query, string $types, array $params): bool {
            if (!$this->conn) {
                error_log("DB Error: No connection available for update.");
                return false;
            }
            
            $stmt = $this->conn->prepare($query);
            if (!$stmt) {
                error_log("DB Error: Failed to prepare update statement. " . $this->conn->error . " | Query: " . $query);
                return false;
            }
            
            if (!$stmt->bind_param($types, ...$params)) {
                error_log("DB Error: Failed to bind update parameters. " . $stmt->error);
                $stmt->close();
                return false;
            }
            
            $result = $stmt->execute();
            if (!$result) {
                error_log("DB Error: Failed to execute update statement. " . $stmt->error);
            }
            
            $stmt->close();
            return $result;
        }
                
    }
?>
