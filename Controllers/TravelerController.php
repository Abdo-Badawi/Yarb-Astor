<?php
require_once '../Models/Traveler.php';

class TravelerController {
    private $travelerModel;
    
    public function __construct() {
        $this->travelerModel = new Traveler();
    }
    
    /**
     * Get traveler by ID
     * 
     * @param int $travelerId Traveler ID
     * @return array|null Traveler data or null if not found
     */
    public function getTravelerById(int $travelerId) {
        if ($travelerId <= 0) {
            error_log("Invalid traveler ID in getTravelerById");
            return null;
        }
        
        return $this->travelerModel->getTravelerById($travelerId);
    }
    
    /**
     * Get all travelers
     * 
     * @return array Array of travelers
     */
    public function getAllTravelers(): array {
        return $this->travelerModel->getAllTravelers();
    }
    
    /**
     * Update traveler profile
     * 
     * @param array $travelerData Traveler data
     * @return bool True if update was successful, false otherwise
     */
    public function updateTravelerProfile(array $travelerData): bool {
        if (empty($travelerData['traveler_id'])) {
            error_log("Missing traveler ID in updateTravelerProfile");
            return false;
        }
        
        return $this->travelerModel->updateTravelerProfile($travelerData);
    }
    
    /**
     * Search travelers by criteria
     * 
     * @param array $criteria Search criteria
     * @return array Array of matching travelers
     */
    public function searchTravelers(array $criteria): array {
        return $this->travelerModel->searchTravelers($criteria);
    }
}
?>
