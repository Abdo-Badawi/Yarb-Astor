<?php
require_once '../Models/Search.php';

class SearchController {
    private $searchModel;
    
    public function __construct() {
        $this->searchModel = new Search();
    }
    
    /**
     * Search for opportunities based on filters
     * 
     * @param array $filters Array of search filters
     * @return array Array of matching opportunities
     */
    public function searchOpportunities(array $filters = []): array {
        try {
            return $this->searchModel->searchOpportunities($filters);
        } catch (Exception $e) {
            error_log("Exception in searchOpportunities: " . $e->getMessage());
            return [];
        }
    }
    
    /**
     * Get all available categories for opportunities
     * 
     * @return array Array of categories
     */
    public function getAvailableCategories(): array {
        try {
            return $this->searchModel->getAvailableCategories();
        } catch (Exception $e) {
            error_log("Exception in getAvailableCategories: " . $e->getMessage());
            return [];
        }
    }
    
    /**
     * Get all available locations for opportunities
     * 
     * @return array Array of locations
     */
    public function getAvailableLocations(): array {
        try {
            return $this->searchModel->getAvailableLocations();
        } catch (Exception $e) {
            error_log("Exception in getAvailableLocations: " . $e->getMessage());
            return [];
        }
    }
}
?>