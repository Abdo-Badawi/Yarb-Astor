<?php
// No namespace declaration - this class is in the global namespace
require_once '../Models/Database.php';
require_once '../Models/Opportunity.php';

class OpportunityController {
    private $db;
    private $opportunityModel;

    public function __construct() {
        $this->db = new Database();
        $this->opportunityModel = new Models\Opportunity("", "", "", new \DateTime(), new \DateTime(), "");
    }

    /**
     * Get all opportunities
     * 
     * @return array Array of all opportunities
     */
    public function getAllOpportunities() {
        // Use select instead of selectPrepared since there are no parameters
        return $this->opportunityModel->getAllOpportunities();
    }

    /**
     * Get opportunity by ID
     * 
     * @param int $opportunityId The opportunity ID
     * @return array|false The opportunity data or false if not found
     */
    public function getOpportunityById($opportunityId) {
        return $this->opportunityModel->getOpportunityById($opportunityId);
    }

    /**
     * Get opportunities by host ID
     * 
     * @param int $hostId The host ID
     * @return array Array of opportunities for the host
     */
    public function getOpportunitiesByHostID($hostId) {
        return $this->opportunityModel->getOpportunitiesByHostID($hostId);
    }

    /**
     * Delete an opportunity
     * 
     * @param int $opportunityId The opportunity ID to delete
     * @return bool True if successful, false otherwise
     */
    public function deleteOpportunity($opportunityId) {
        return $this->opportunityModel->deleteOpportunity($opportunityId);
    }

    /**
     * Update opportunity status
     * 
     * @param int $opportunityId The opportunity ID
     * @param string $status The new status
     * @return bool True if successful, false otherwise
     */
    public function updateOpportunityStatus($opportunityId, $status) {
        return $this->opportunityModel->updateOpportunityStatus($opportunityId, $status);
    }

    /**
     * Get active opportunities
     * 
     * @return array Array of active opportunities
     */
    public function getActiveOpportunities() {
        return $this->opportunityModel->getActiveOpportunities();
    }

    /**
     * Get opportunities by traveler ID (applications)
     * 
     * @param int $travelerId The traveler ID
     * @return array Array of opportunities the traveler has applied to
     */
    public function getOpportunitiesByTravelerID($travelerId) {
        return $this->opportunityModel->getOpportunitiesByTravelerID($travelerId);
    }

    /**
     * Update an opportunity
     * 
     * @param array $data The opportunity data to update
     * @return bool True if successful, false otherwise
     */
    public function updateOpportunity($data) {
        return $this->opportunityModel->updateOpportunity($data);
    }

    /**
     * Save a new opportunity to the database
     * 
     * @param Models\Opportunity $opportunity The opportunity object
     * @return bool True if successful, false otherwise
     */
    public function saveOpportunityToDB($opportunity) {
        return $this->opportunityModel->saveOpportunityToDB($opportunity);
    }
}
?>










