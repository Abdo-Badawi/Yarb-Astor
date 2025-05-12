<?php
namespace Models;

require_once __DIR__ . '/../Controllers/DBController.php';

/**
 * Base Model class for database operations
 */
abstract class Model {
    protected $db;
    protected $tableName;
    protected $primaryKey;
    
    /**
     * Constructor for Model class
     * 
     * @param string $tableName Table name
     * @param string $primaryKey Primary key column name
     */
    public function __construct(string $tableName, string $primaryKey = 'id') {
        $this->db = new \DBController();
        $this->tableName = $tableName;
        $this->primaryKey = $primaryKey;
    }
    
    /**
     * Find record by ID
     * 
     * @param int $id Record ID
     * @return array|null Record data if found, null otherwise
     */
    public function findById(int $id): ?array {
        $this->db->openConnection();
        
        $sql = "SELECT * FROM {$this->tableName} WHERE {$this->primaryKey} = ?";
        $params = [$id];
        
        $result = $this->db->selectPrepared($sql, "i", $params);
        $this->db->closeConnection();
        
        return $result ? $result[0] : null;
    }
    
    /**
     * Find all records
     * 
     * @param array $conditions Optional conditions for WHERE clause
     * @param array $orderBy Optional ORDER BY clause
     * @param int $limit Optional LIMIT clause
     * @param int $offset Optional OFFSET clause
     * @return array Records data
     */
    public function findAll(array $conditions = [], array $orderBy = [], int $limit = 0, int $offset = 0): array {
        $this->db->openConnection();
        
        $sql = "SELECT * FROM {$this->tableName}";
        $params = [];
        $types = "";
        
        // Add WHERE conditions if provided
        if (!empty($conditions)) {
            $whereClauses = [];
            foreach ($conditions as $column => $value) {
                $whereClauses[] = "$column = ?";
                $params[] = $value;
                
                if (is_int($value)) {
                    $types .= "i";
                } elseif (is_float($value)) {
                    $types .= "d";
                } else {
                    $types .= "s";
                }
            }
            
            $sql .= " WHERE " . implode(" AND ", $whereClauses);
        }
        
        // Add ORDER BY if provided
        if (!empty($orderBy)) {
            $orderClauses = [];
            foreach ($orderBy as $column => $direction) {
                $orderClauses[] = "$column $direction";
            }
            
            $sql .= " ORDER BY " . implode(", ", $orderClauses);
        }
        
        // Add LIMIT and OFFSET if provided
        if ($limit > 0) {
            $sql .= " LIMIT $limit";
            
            if ($offset > 0) {
                $sql .= " OFFSET $offset";
            }
        }
        
        // Execute query
        $result = empty($params) ? $this->db->select($sql) : $this->db->selectPrepared($sql, $types, $params);
        $this->db->closeConnection();
        
        return $result ?: [];
    }
    
    /**
     * Create a new record
     * 
     * @param array $data Record data
     * @return int|bool ID of the new record if successful, false otherwise
     */
    public function create(array $data): int|bool {
        $this->db->openConnection();
        
        $columns = array_keys($data);
        $placeholders = array_fill(0, count($columns), "?");
        
        $sql = "INSERT INTO {$this->tableName} (" . implode(", ", $columns) . ") VALUES (" . implode(", ", $placeholders) . ")";
        
        $params = array_values($data);
        $types = "";
        
        foreach ($params as $param) {
            if (is_int($param)) {
                $types .= "i";
            } elseif (is_float($param)) {
                $types .= "d";
            } else {
                $types .= "s";
            }
        }
        
        $result = $this->db->insert($sql, $types, $params);
        
        if ($result) {
            $id = $this->db->getInsertId();
            $this->db->closeConnection();
            return $id;
        }
        
        $this->db->closeConnection();
        return false;
    }
    
    /**
     * Update a record
     * 
     * @param int $id Record ID
     * @param array $data Record data
     * @return bool True if update successful, false otherwise
     */
    public function update(int $id, array $data): bool {
        $this->db->openConnection();
        
        $setClauses = [];
        foreach (array_keys($data) as $column) {
            $setClauses[] = "$column = ?";
        }
        
        $sql = "UPDATE {$this->tableName} SET " . implode(", ", $setClauses) . " WHERE {$this->primaryKey} = ?";
        
        $params = array_values($data);
        $params[] = $id;
        
        $types = "";
        foreach ($params as $param) {
            if (is_int($param)) {
                $types .= "i";
            } elseif (is_float($param)) {
                $types .= "d";
            } else {
                $types .= "s";
            }
        }
        
        $result = $this->db->update($sql, $types, $params);
        $this->db->closeConnection();
        
        return $result;
    }
    
    /**
     * Delete a record
     * 
     * @param int $id Record ID
     * @return bool True if deletion successful, false otherwise
     */
    public function delete(int $id): bool {
        $this->db->openConnection();
        
        $sql = "DELETE FROM {$this->tableName} WHERE {$this->primaryKey} = ?";
        $params = [$id];
        
        $result = $this->db->delete($sql, "i", $params);
        $this->db->closeConnection();
        
        return $result;
    }
    
    /**
     * Execute a custom query
     * 
     * @param string $sql SQL query
     * @param string $types Parameter types
     * @param array $params Query parameters
     * @return array|bool Query result
     */
    public function query(string $sql, string $types = "", array $params = []): array|bool {
        $this->db->openConnection();
        
        if (empty($params)) {
            $result = $this->db->select($sql);
        } else {
            $result = $this->db->selectPrepared($sql, $types, $params);
        }
        
        $this->db->closeConnection();
        
        return $result;
    }
    
    /**
     * Execute a custom insert query
     * 
     * @param string $sql SQL query
     * @param string $types Parameter types
     * @param array $params Query parameters
     * @return int|bool ID of the new record if successful, false otherwise
     */
    public function customInsert(string $sql, string $types, array $params): int|bool {
        $this->db->openConnection();
        
        $result = $this->db->insert($sql, $types, $params);
        
        if ($result) {
            $id = $this->db->getInsertId();
            $this->db->closeConnection();
            return $id;
        }
        
        $this->db->closeConnection();
        return false;
    }
    
    /**
     * Execute a custom update query
     * 
     * @param string $sql SQL query
     * @param string $types Parameter types
     * @param array $params Query parameters
     * @return bool True if update successful, false otherwise
     */
    public function customUpdate(string $sql, string $types, array $params): bool {
        $this->db->openConnection();
        
        $result = $this->db->update($sql, $types, $params);
        $this->db->closeConnection();
        
        return $result;
    }
    
    /**
     * Begin a transaction
     * 
     * @return bool True if transaction started successfully, false otherwise
     */
    public function beginTransaction(): bool {
        $this->db->openConnection();
        return $this->db->beginTransaction();
    }
    
    /**
     * Commit a transaction
     * 
     * @return bool True if transaction committed successfully, false otherwise
     */
    public function commitTransaction(): bool {
        return $this->db->commitTransaction();
    }
    
    /**
     * Rollback a transaction
     * 
     * @return bool True if transaction rolled back successfully, false otherwise
     */
    public function rollbackTransaction(): bool {
        return $this->db->rollbackTransaction();
    }
    
    /**
     * Close database connection
     */
    public function closeConnection(): void {
        $this->db->closeConnection();
    }
}
?>
