
<?php


class Database {
    public $conn;
    
    private $host = "localhost";
    private $user = "root";
    private $password = "";
    private $database = "homestay3";
    private $lastError = "";
    
    // Open database connection
    public function openConnection() {
        // Open database connection logic
        $this->conn = new mysqli($this->host, $this->user, $this->password, $this->database);
        if ($this->conn->connect_error) {
            $this->lastError = $this->conn->connect_error;
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
                    $this->lastError = $this->conn->connect_error;
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
        
        $result = $this->conn->query($query);
        
        if ($result === false) {
            $this->lastError = $this->conn->error;
            error_log("Query failed: " . $this->conn->error);
            return false;
        }
        
        if ($result instanceof mysqli_result) {
            $data = [];
            while ($row = $result->fetch_assoc()) {
                $data[] = $row;
            }
            $result->free();
            return $data;
        }
        
        return true;
    }
    
    // Execute SELECT query with prepared statement
    public function selectPrepared($query, $types, $params) {
        if (!$this->conn) {
            if (!$this->openConnection()) {
                error_log("Connection not available");
                return false;
            }
        }
        
        $stmt = $this->conn->prepare($query);
        
        if (!$stmt) {
            $this->lastError = $this->conn->error;
            error_log("Prepare failed: " . $this->conn->error);
            return false;
        }
        
        if (!empty($params)) {
            $stmt->bind_param($types, ...$params);
        }
        
        if (!$stmt->execute()) {
            $this->lastError = $stmt->error;
            error_log("Execute failed: " . $stmt->error);
            $stmt->close();
            return false;
        }
        
        $result = $stmt->get_result();
        
        if ($result === false) {
            $this->lastError = $stmt->error;
            error_log("Get result failed: " . $stmt->error);
            $stmt->close();
            return false;
        }
        
        $data = [];
        while ($row = $result->fetch_assoc()) {
            $data[] = $row;
        }
        
        $stmt->close();
        return $data;
    }
    
    // Execute INSERT query with prepared statement
    public function insert($query, $types, $params) {
        if (!$this->conn) {
            if (!$this->openConnection()) {
                error_log("Connection not available");
                return false;
            }
        }
        
        $stmt = $this->conn->prepare($query);
        
        if (!$stmt) {
            $this->lastError = $this->conn->error;
            error_log("Prepare failed: " . $this->conn->error);
            return false;
        }
        
        if (!empty($params)) {
            $stmt->bind_param($types, ...$params);
        }
        
        if (!$stmt->execute()) {
            $this->lastError = $stmt->error;
            error_log("Execute failed: " . $stmt->error);
            $stmt->close();
            return false;
        }
        
        $insertId = $stmt->insert_id;
        $stmt->close();
        
        return $insertId ?: true;
    }
    
    // Execute UPDATE query with prepared statement
    public function update($query, $types, $params) {
        return $this->insert($query, $types, $params);
    }
    
    // Execute DELETE query with prepared statement
    public function delete($query, $types, $params) {
        return $this->insert($query, $types, $params);
    }
    
    // Get the last error message
    public function getLastError() {
        return $this->lastError;
    }

    /**
     * Get the ID generated from the last INSERT operation
     * 
     * @return int The last insert ID or 0 if no connection exists
     */
    public function getInsertId() {
        if ($this->conn) {
            return $this->conn->insert_id;
        }
        return 0;
    }
    public function rollback() {
        if ($this->conn) {
            $this->conn->rollback();
        }
    }
}
?>



