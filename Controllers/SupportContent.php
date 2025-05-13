<?php
// Update the path to the model
require_once __DIR__ . '/../Models/SupportContent.php';

class SupportController {
    private $model;
    
    public function __construct() {
        $this->model = new SupportContent();
    }
    
    /**
     * Get all FAQs
     * 
     * @return array Result with success status and data
     */
    public function getAllFAQs() {
        return $this->model->getAllFAQs();
    }
    
    /**
     * Get FAQ by ID
     * 
     * @param int $id FAQ ID
     * @return array Result with success status and data
     */
    public function getFAQById($id) {
        return $this->model->getFAQById($id);
    }
    
    /**
     * Save FAQ (create or update)
     * 
     * @param array $data FAQ data
     * @return array Result with success status and message
     */
    public function saveFAQ($data) {
        return $this->model->saveFAQ($data);
    }
    
    /**
     * Delete FAQ
     * 
     * @param int $id FAQ ID
     * @return array Result with success status and message
     */
    public function deleteFAQ($id) {
        return $this->model->deleteFAQ($id);
    }
    
    /**
     * Get featured FAQs
     * 
     * @param int $limit Number of FAQs to return
     * @return array Result with success status and data
     */
    public function getFeaturedFAQs($limit = 5) {
        return $this->model->getFeaturedFAQs($limit);
    }
    
    /**
     * Get FAQs by category
     * 
     * @param string $category Category name
     * @return array Result with success status and data
     */
    public function getFAQsByCategory($category) {
        return $this->model->getFAQsByCategory($category);
    }
}
