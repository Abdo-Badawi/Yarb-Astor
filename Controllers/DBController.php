<?php
class DBController {
    private $host = "localhost";
    private $user = "root";
    private $password = "";
    private $database = "homestay3";
    public $conn;
    
    /**
     * Constructor for DBController class
     */
    public function __construct() {
        $this->conn = null;
    }
    
    /**
     * Open database connection
     * 
     * @return bool True if connection opened successfully, false otherwise
     */
    public function openConnection(): bool {
        $this->conn = new mysqli($this->host, $this->user, $this->password, $this->database);
        
        if ($this->conn->connect_error) {
            error_log("Connection failed: " . $this->conn->connect_error);
            return false;
        }
        
        return true;
    }
    
    /**
     * Close database connection
     */
    public function closeConnection(): void {
        if ($this->conn) {
            $this->conn->close();
            $this->conn = null;
        }
    }
    
    /**
     * Get database connection
     * 
     * @return mysqli|null Database connection
     */
    public function getConnection() {
        return $this->conn;
    }
    
    /**
     * Begin a transaction
     * 
     * @return bool True if transaction started successfully, false otherwise
     */
    public function beginTransaction(): bool {
        if (!$this->conn) {
            error_log("No database connection");
            return false;
        }
        return $this->conn->begin_transaction();
    }
    
    /**
     * Commit a transaction
     * 
     * @return bool True if transaction committed successfully, false otherwise
     */
    public function commitTransaction(): bool {
        if (!$this->conn) {
            error_log("No database connection");
            return false;
        }
        return $this->conn->commit();
    }
    
    /**
     * Rollback a transaction
     * 
     * @return bool True if transaction rolled back successfully, false otherwise
     */
    public function rollbackTransaction(): bool {
        if (!$this->conn) {
            error_log("No database connection");
            return false;
        }
        return $this->conn->rollback();
    }
    
    /**
     * Execute a SELECT query
     * 
     * @param string $sql SQL query
     * @return array|false Result set as associative array or false on failure
     */
    public function select(string $sql): array|false {
        if (!$this->conn) {
            error_log("No database connection");
            return false;
        }
        
        $result = $this->conn->query($sql);
        
        if (!$result) {
            error_log("Query failed: " . $this->conn->error);
            return false;
        }
        
        if ($result->num_rows > 0) {
            $data = [];
            while ($row = $result->fetch_assoc()) {
                $data[] = $row;
            }
            return $data;
        }
        
        return [];
    }
    
    /**
     * Execute a prepared SELECT query
     * 
     * @param string $sql SQL query
     * @param string $types Parameter types (i: integer, d: double, s: string, b: blob)
     * @param array $params Query parameters
     * @return array|false Result set as associative array or false on failure
     */
    public function selectPrepared(string $sql, string $types, array $params): array|false {
        if (!$this->conn) {
            error_log("No database connection");
            return false;
        }
        
        $stmt = $this->conn->prepare($sql);
        
        if (!$stmt) {
            error_log("Prepare failed: " . $this->conn->error);
            return false;
        }
        
        if (!empty($params)) {
            $stmt->bind_param($types, ...$params);
        }
        
        if (!$stmt->execute()) {
            error_log("Execute failed: " . $stmt->error);
            $stmt->close();
            return false;
        }
        
        $result = $stmt->get_result();
        
        if (!$result) {
            error_log("Get result failed: " . $stmt->error);
            $stmt->close();
            return false;
        }
        
        if ($result->num_rows > 0) {
            $data = [];
            while ($row = $result->fetch_assoc()) {
                $data[] = $row;
            }
            $stmt->close();
            return $data;
        }
        
        $stmt->close();
        return [];
    }
    
    /**
     * Execute an INSERT query
     * 
     * @param string $sql SQL query
     * @param string $types Parameter types (i: integer, d: double, s: string, b: blob)
     * @param array $params Query parameters
     * @return bool True if insert successful, false otherwise
     */
    public function insert(string $sql, string $types, array $params): bool {
        if (!$this->conn) {
            error_log("No database connection");
            return false;
        }
        
        $stmt = $this->conn->prepare($sql);
        
        if (!$stmt) {
            error_log("Prepare failed: " . $this->conn->error);
            return false;
        }
        
        if (!empty($params)) {
            $stmt->bind_param($types, ...$params);
        }
        
        $result = $stmt->execute();
        
        if (!$result) {
            error_log("Execute failed: " . $stmt->error);
        }
        
        $stmt->close();
        return $result;
    }
    
    /**
     * Execute an UPDATE query
     * 
     * @param string $sql SQL query
     * @param string $types Parameter types (i: integer, d: double, s: string, b: blob)
     * @param array $params Query parameters
     * @return bool True if update successful, false otherwise
     */
    public function update(string $sql, string $types, array $params): bool {
        return $this->insert($sql, $types, $params); // Same implementation as insert
    }
    
    /**
     * Execute a DELETE query
     * 
     * @param string $sql SQL query
     * @param string $types Parameter types (i: integer, d: double, s: string, b: blob)
     * @param array $params Query parameters
     * @return bool True if delete successful, false otherwise
     */
    public function delete(string $sql, string $types, array $params): bool {
        return $this->insert($sql, $types, $params); // Same implementation as insert
    }
    
    /**
     * Get the last inserted ID
     * 
     * @return int|string Last inserted ID
     */
    public function getInsertId() {
        return $this->conn ? $this->conn->insert_id : 0;
    }
    
    /**
     * Get the number of affected rows from the last query
     * 
     * @return int Number of affected rows
     */
    public function getAffectedRows(): int {
        return $this->conn ? $this->conn->affected_rows : 0;
    }
    
    /**
     * Get the last error message
     * 
     * @return string Last error message
     */
    public function getError(): string {
        return $this->conn ? $this->conn->error : '';
    }
    
    /**
     * Escape a string for use in a query
     * 
     * @param string $string String to escape
     * @return string Escaped string
     */
    public function escapeString(string $string): string {
        return $this->conn ? $this->conn->real_escape_string($string) : $string;
    }
}
?>
