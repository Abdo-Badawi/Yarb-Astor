<?php
class Database {
    public $conn;
    
    private $host = "localhost";
    private $user = "root";
    private $password = "";
    private $database = "homestay";
    
   
    // Open database connection
    public function openConnection() {
        // Open database connection logic
        $this->conn = new mysqli($this->host, $this->user, $this->password, $this->database);
        if ($this->conn->connect_error) {
            $this->conn = null; // Ensure conn is null on failure
            return false;
        }
        return true;
    }
    
    // Close database connection
    public function closeConnection() {
        if ($this->conn) {
            $this->conn->close();
            $this->conn = null;
        }
    }
    
    // Execute SELECT query
    public function select($query) {
        if (!$this->conn) {
            if (!$this->openConnection()) {
                error_log("Connection not available");
                return false;
            }
        }
        
        // Force a new connection for every query to ensure reliability
        if ($this->conn) {
            // If connection exists but has errors, recreate it
            if ($this->conn->connect_errno) {
                $this->conn->close();
                $this->conn = new mysqli($this->host, $this->user, $this->password, $this->database);
                
                if ($this->conn->connect_error) {
                    error_log("Connection failed: " . $this->conn->connect_error);
                    return false;
                }
            }
        } else {
            // If no connection exists, create one
            if (!$this->openConnection()) {
                error_log("Failed to establish database connection");
                return false;
            }
        }
        
        // Now we have a guaranteed valid connection
        $result = $this->conn->query($query);
        
        if (!$result) {
            error_log("Query failed: " . $this->conn->error);
            return false;
        }
        
        $resultArray = [];
        while ($row = $result->fetch_assoc()) {
            $resultArray[] = $row;
        }
        
        return $resultArray;
    }
    
    // Execute prepared SELECT query
    public function selectPrepared($query, $types, $params) {
        // Ensure connection is open
        if ($this->conn === null) {
            if (!$this->openConnection()) {
                error_log("Failed to open database connection");
                return false;
            }
        }
        
        // Verify connection is valid before proceeding
        if (!$this->conn instanceof mysqli || $this->conn->connect_errno) {
            error_log("Invalid connection object: " . ($this->conn ? $this->conn->connect_error : "null connection"));
            return false;
        }
        
        $stmt = $this->conn->prepare($query);
        
        if (!$stmt) {
            error_log("Prepare failed: " . $this->conn->error);
            return false;
        }
        
        $stmt->bind_param($types, ...$params);
        
        if (!$stmt->execute()) {
            error_log("Execute failed: " . $stmt->error);
            $stmt->close();
            return false;
        }
        
        $result = $stmt->get_result();
        $resultArray = [];
        
        while ($row = $result->fetch_assoc()) {
            $resultArray[] = $row;
        }
        
        $stmt->close();
        return $resultArray;
    }
    
    // Execute INSERT query
    public function insert($query, $types, $params) {
        // Ensure we have a valid connection before preparing statement
        if (!$this->conn) {
            if (!$this->openConnection()) {
                error_log("Failed to establish database connection");
                return false;
            }
        }
        
        // Double-check connection is valid
        if (!$this->conn || !($this->conn instanceof mysqli)) {
            error_log("Failed to establish database connection");
            return false;
        }
        
        $stmt = $this->conn->prepare($query);
        
        if (!$stmt) {
            error_log("Prepare failed: " . $this->conn->error);
            return false;
        }
        
        $stmt->bind_param($types, ...$params);
        
        if (!$stmt->execute()) {
            error_log("Execute failed: " . $stmt->error);
            $stmt->close();
            return false;
        }
        
        $affectedRows = $stmt->affected_rows;
        $stmt->close();
        
        return $affectedRows > 0;
    }
    
    // Execute UPDATE query
    public function update($query, $types, $params) {
        // Ensure we have a valid connection before preparing statement
        if (!$this->conn) {
            if (!$this->openConnection()) {
                error_log("Failed to establish database connection");
                return false;
            }
        }
        
        // Double-check connection is valid
        if (!$this->conn || !($this->conn instanceof mysqli)) {
            error_log("Failed to establish database connection");
            return false;
        }
        
        $stmt = $this->conn->prepare($query);
        
        if (!$stmt) {
            error_log("Prepare failed: " . $this->conn->error);
            return false;
        }
        
        $stmt->bind_param($types, ...$params);
        
        if (!$stmt->execute()) {
            error_log("Execute failed: " . $stmt->error);
            $stmt->close();
            return false;
        }
        
        $affectedRows = $stmt->affected_rows;
        $stmt->close();
        
        return $affectedRows >= 0;
    }
    
    // Execute DELETE query
    public function delete($query, $types, $params) {
        // Ensure we have a valid connection before preparing statement
        if (!$this->conn) {
            if (!$this->openConnection()) {
                error_log("Failed to establish database connection");
                return false;
            }
        }
        
        // Double-check connection is valid
        if (!$this->conn || !($this->conn instanceof mysqli)) {
            error_log("Failed to establish database connection");
            return false;
        }
        
        $stmt = $this->conn->prepare($query);
        
        if (!$stmt) {
            error_log("Prepare failed: " . $this->conn->error);
            return false;
        }
        
        $stmt->bind_param($types, ...$params);
        
        if (!$stmt->execute()) {
            error_log("Execute failed: " . $stmt->error);
            $stmt->close();
            return false;
        }
        
        $affectedRows = $stmt->affected_rows;
        $stmt->close();
        
        return $affectedRows > 0;
    }
    
    // Get last insert ID
    public function getInsertId() {
        if (!$this->conn) {
            if (!$this->openConnection()) {
                error_log("Failed to establish database connection");
                return null;
            }
        }
        return $this->conn ? $this->conn->insert_id : null;
    }
}
?>
