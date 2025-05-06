<?php
require_once __DIR__ . '/../Controllers/DBController.php';

class Database {
    private $dbController;
    
    public function __construct() {
        $this->dbController = new DBController();
        $this->dbController->openConnection();
    }
    
    public function __destruct() {
        $this->dbController->closeConnection();
    }
    
    /**
     * Execute a SELECT query with parameters
     * 
     * @param string $sql The SQL query
     * @param array $params The parameters for the query
     * @return array|false The result set or false on failure
     */
    public function select($sql, $params = []) {
        if (empty($params)) {
            return $this->dbController->select($sql);
        }
        
        $types = '';
        foreach ($params as $param) {
            if (is_int($param)) {
                $types .= 'i';
            } elseif (is_float($param)) {
                $types .= 'd';
            } elseif (is_null($param)) {
                $types .= 's';
            } else {
                $types .= 's';
            }
        }
        
        return $this->dbController->selectPrepared($sql, $types, $params);
    }
    
    /**
     * Execute an INSERT query
     * 
     * @param string $sql The SQL query
     * @param array $params The parameters for the query
     * @return int|false The last insert ID or false on failure
     */
    public function insert($sql, $params = []) {
        if (empty($params)) {
            return false;
        }
        
        $types = '';
        foreach ($params as $param) {
            if (is_int($param)) {
                $types .= 'i';
            } elseif (is_float($param)) {
                $types .= 'd';
            } elseif (is_null($param)) {
                $types .= 's';
            } else {
                $types .= 's';
            }
        }
        
        $result = $this->dbController->insert($sql, $types, $params);
        
        if ($result) {
            return $this->dbController->conn->insert_id;
        }
        
        return false;
    }
    
    /**
     * Execute an UPDATE query
     * 
     * @param string $sql The SQL query
     * @param array $params The parameters for the query
     * @return bool True on success, false on failure
     */
    public function update($sql, $params = []) {
        if (empty($params)) {
            return false;
        }
        
        $types = '';
        foreach ($params as $param) {
            if (is_int($param)) {
                $types .= 'i';
            } elseif (is_float($param)) {
                $types .= 'd';
            } elseif (is_null($param)) {
                $types .= 's';
            } else {
                $types .= 's';
            }
        }
        
        return $this->dbController->update($sql, $types, $params);
    }
    
    /**
     * Execute a DELETE query
     * 
     * @param string $sql The SQL query
     * @param array $params The parameters for the query
     * @return bool True on success, false on failure
     */
    public function delete($sql, $params = []) {
        return $this->update($sql, $params);
    }
    
    /**
     * Get the last error message
     * 
     * @return string The last error message
     */
    public function getLastError() {
        return $this->dbController->getLastError();
    }
}
