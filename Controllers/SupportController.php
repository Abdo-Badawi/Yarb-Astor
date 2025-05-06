<?php
require_once '../Models/SupportContent.php';
require_once '../Controllers/DBController.php';

class SupportController {
    private $db;

    public function __construct() {
        $this->db = new DBController();
        $this->db->openConnection();
    }

    public function __destruct() {
        $this->db->closeConnection();
    }

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

            if ($result) {
                return ['success' => true, 'message' => 'FAQ created successfully'];
            } else {
                return ['success' => false, 'error' => 'Failed to create FAQ: ' . $this->db->getLastError()];
            }
        }
    }

    public function getAllFAQs() {
        // Display order has been removed, so we only sort by created_at
        $query = "SELECT * FROM support_content ORDER BY created_at DESC";

        $result = $this->db->select($query);

        if ($result) {
            return ['success' => true, 'data' => $result];
        } else {
            return ['success' => false, 'error' => 'Failed to retrieve FAQs'];
        }
    }

    public function getFAQById($id) {
        $query = "SELECT * FROM support_content WHERE content_id = ?";
        $types = "i";
        $params = [$id];

        $result = $this->db->selectPrepared($query, $types, $params);

        if ($result && count($result) > 0) {
            return ['success' => true, 'data' => $result[0]];
        } else {
            return ['success' => false, 'error' => 'FAQ not found'];
        }
    }

    public function deleteFAQ($id) {
        $query = "DELETE FROM support_content WHERE content_id = ?";
        $types = "i";
        $params = [$id];

        $result = $this->db->insert($query, $types, $params);

        if ($result) {
            return ['success' => true];
        } else {
            return ['success' => false, 'error' => 'Failed to delete FAQ'];
        }
    }
}

// Handle direct API requests
if (basename($_SERVER['PHP_SELF']) === 'SupportController.php') {
    header('Content-Type: application/json');
    $controller = new SupportController();

    $action = $_GET['action'] ?? '';

    if ($action === 'save') {
        $data = json_decode(file_get_contents('php://input'), true);
        echo json_encode($controller->saveFAQ($data));
    } elseif ($action === 'getAll') {
        echo json_encode($controller->getAllFAQs());
    } elseif ($action === 'getById' && isset($_GET['id'])) {
        echo json_encode($controller->getFAQById($_GET['id']));
    } elseif ($action === 'delete' && isset($_GET['id'])) {
        echo json_encode($controller->deleteFAQ($_GET['id']));
    } else {
        http_response_code(400);
        echo json_encode(['success' => false, 'error' => 'Invalid action']);
    }
}
