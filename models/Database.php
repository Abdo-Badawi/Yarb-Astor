<?php
class Database {
    public $conn;
    private $host = "localhost";
    private $user = "root";
    private $password = "";
    private $database = "homestay";
    
   
    public function openConnection() {
        $this->conn = new mysqli($this->host, $this->user, $this->password, $this->database);
        if ($this->conn->connect_error) {
            $this->conn = null;
            return false;
        }
        return true;
    }
    
    public function closeConnection() {
        if ($this->conn) {
            $this->conn->close();
            $this->conn = null;
        }
    }
    
    public function select($query) {
        if (!$this->conn) {
            if (!$this->openConnection()) {
                error_log("Connection not available");
                return false;
            }
        }
        
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
            if (!$this->openConnection()) {
                error_log("Failed to establish database connection");
                return false;
            }
        }
        
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
    
    public function selectPrepared($query, $types, $params) {
        // Ensure connection is open
        if ($this->conn === null) {
            if (!$this->openConnection()) {
                error_log("Failed to open database connection");
                return false;
            }
        }
        
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
    
    public function insert($query, $types, $params) {
        if (!$this->conn) {
            if (!$this->openConnection()) {
                error_log("Failed to establish database connection");
                return false;
            }
        }
        
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
    
    public function update($query, $types, $params) {
        if (!$this->conn) {
            if (!$this->openConnection()) {
                error_log("Failed to establish database connection");
                return false;
            }
        }
        
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
    
    public function delete($query, $types, $params) {
        if (!$this->conn) {
            if (!$this->openConnection()) {
                error_log("Failed to establish database connection");
                return false;
            }
        }
        
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
