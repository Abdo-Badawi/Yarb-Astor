<?php
// Update the path to Database.php
require_once __DIR__ . '/Database.php';

class SupportContent {
    private $db;
    
    public function __construct() {
        $this->db = new Database();
    }
    
    /**
     * Get all FAQs
     * 
     * @return array Result with success status and data
     */
    public function getAllFAQs() {
        $this->db->openConnection();
        
        $query = "SELECT * FROM support_content ORDER BY created_at DESC";
        $result = $this->db->select($query);
        
        $this->db->closeConnection();
        
        if ($result) {
            return ['success' => true, 'data' => $result];
        } else {
            return ['success' => false, 'error' => 'Failed to retrieve FAQs'];
        }
    }
    
    /**
     * Get FAQ by ID
     * 
     * @param int $id FAQ ID
     * @return array Result with success status and data
     */
    public function getFAQById($id) {
        $this->db->openConnection();
        
        $query = "SELECT * FROM support_content WHERE content_id = ?";
        $types = "i";
        $params = [$id];
        
        $result = $this->db->selectPrepared($query, $types, $params);
        
        $this->db->closeConnection();
        
        if ($result && count($result) > 0) {
            return ['success' => true, 'data' => $result[0]];
        } else {
            return ['success' => false, 'error' => 'FAQ not found'];
        }
    }
    
    /**
     * Save FAQ (create or update)
     * 
     * @param array $data FAQ data
     * @return array Result with success status and message
     */
    public function saveFAQ($postData) {
        if (!$postData) {
            return ['success' => false, 'error' => 'Invalid input'];
        }

        // Required fields check
        if (empty($postData['faqQuestion']) || empty($postData['faqAnswer']) || empty($postData['faqCategory'])) {
            return ['success' => false, 'error' => 'Missing required fields'];
        }

        // Sanitize and assign
        $title = $postData['faqQuestion'];
        $content = $postData['faqAnswer'];
        $category = $postData['faqCategory'];
        $status = $postData['faqStatus'] ?? 'draft';
        $lastUpdated = date('Y-m-d H:i:s');

        $this->db->openConnection();
        
        // Check which columns exist in the table
        $tableInfo = $this->db->select("DESCRIBE support_content");
        $columns = [];
        if ($tableInfo) {
            foreach ($tableInfo as $column) {
                $columns[] = $column['Field'];
            }
        }

        // Check if this is an update (faqId is provided) or a new record
        $isUpdate = !empty($postData['faqId']);

        if ($isUpdate) {
            // This is an update to an existing FAQ
            $faqId = (int)$postData['faqId'];

            // Build the SET part of the query
            $setParts = ["title = ?", "content = ?", "category = ?", "status = ?"];
            $types = "ssss";
            $params = [$title, $content, $category, $status];

            // Add last_updated if it exists
            if (in_array('last_updated', $columns)) {
                $setParts[] = "last_updated = ?";
                $types .= "s";
                $params[] = $lastUpdated;
            }

            // Add user_type if it exists
            if (in_array('user_type', $columns)) {
                $userType = $postData['faqUserType'] ?? 'admin';
                $setParts[] = "user_type = ?";
                $types .= "s";
                $params[] = $userType;
            }

            // Add featured if it exists
            if (in_array('featured', $columns)) {
                $featured = isset($postData['faqFeatured']) && $postData['faqFeatured'] ? 1 : 0;
                $setParts[] = "featured = ?";
                $types .= "i";
                $params[] = $featured;
            }

            // Add the ID parameter for the WHERE clause
            $types .= "i";
            $params[] = $faqId;

            $query = "UPDATE support_content SET " . implode(', ', $setParts) . " WHERE content_id = ?";
            $result = $this->db->insert($query, $types, $params);

            $this->db->closeConnection();
            
            if ($result) {
                return ['success' => true, 'message' => 'FAQ updated successfully'];
            } else {
                return ['success' => false, 'error' => 'Failed to update FAQ: ' . $this->db->getLastError()];
            }
        } else {
            // This is a new FAQ
            $createdAt = $lastUpdated;

            // Build the query dynamically based on existing columns
            $queryFields = ['title', 'content', 'category', 'status', 'created_at'];
            $queryValues = ['?', '?', '?', '?', '?'];
            $types = "sssss";
            $params = [$title, $content, $category, $status, $createdAt];

            // Add last_updated if it exists
            if (in_array('last_updated', $columns)) {
                $queryFields[] = 'last_updated';
                $queryValues[] = '?';
                $types .= "s";
                $params[] = $lastUpdated;
            }

            // Add user_type if it exists
            if (in_array('user_type', $columns)) {
                $userType = $postData['faqUserType'] ?? 'admin';
                $queryFields[] = 'user_type';
                $queryValues[] = '?';
                $types .= "s";
                $params[] = $userType;
            }

            // Add featured if it exists
            if (in_array('featured', $columns)) {
                $featured = isset($postData['faqFeatured']) && $postData['faqFeatured'] ? 1 : 0;
                $queryFields[] = 'featured';
                $queryValues[] = '?';
                $types .= "i";
                $params[] = $featured;
            }

            $query = "INSERT INTO support_content (" . implode(', ', $queryFields) . ") VALUES (" . implode(', ', $queryValues) . ")";
            $result = $this->db->insert($query, $types, $params);

            $this->db->closeConnection();
            
            if ($result) {
                return ['success' => true, 'message' => 'FAQ created successfully'];
            } else {
                return ['success' => false, 'error' => 'Failed to create FAQ: ' . $this->db->getLastError()];
            }
        }
    }
    
    /**
     * Delete FAQ
     * 
     * @param int $id FAQ ID
     * @return array Result with success status and message
     */
    public function deleteFAQ($id) {
        $this->db->openConnection();
        
        $query = "DELETE FROM support_content WHERE content_id = ?";
        $types = "i";
        $params = [$id];
        
        $result = $this->db->delete($query, $types, $params);
        
        $this->db->closeConnection();
        
        if ($result) {
            return ['success' => true, 'message' => 'FAQ deleted successfully'];
        } else {
            return ['success' => false, 'error' => 'Failed to delete FAQ: ' . $this->db->getLastError()];
        }
    }
    
    /**
     * Get featured FAQs
     * 
     * @param int $limit Number of FAQs to return
     * @return array Result with success status and data
     */
    public function getFeaturedFAQs($limit = 5) {
        $this->db->openConnection();
        
        $query = "SELECT * FROM support_content WHERE featured = 1 AND status = 'active' ORDER BY created_at DESC LIMIT ?";
        $types = "i";
        $params = [$limit];
        
        $result = $this->db->selectPrepared($query, $types, $params);
        
        $this->db->closeConnection();
        
        if ($result) {
            return ['success' => true, 'data' => $result];
        } else {
            return ['success' => false, 'error' => 'Failed to retrieve featured FAQs'];
        }
    }
    
    /**
     * Get FAQs by category
     * 
     * @param string $category Category name
     * @return array Result with success status and data
     */
    public function getFAQsByCategory($category) {
        $this->db->openConnection();
        
        $query = "SELECT * FROM support_content WHERE category = ? AND status = 'active' ORDER BY created_at DESC";
        $types = "s";
        $params = [$category];
        
        $result = $this->db->selectPrepared($query, $types, $params);
        
        $this->db->closeConnection();
        
        if ($result) {
            return ['success' => true, 'data' => $result];
        } else {
            return ['success' => false, 'error' => 'Failed to retrieve FAQs by category'];
        }
    }
}


