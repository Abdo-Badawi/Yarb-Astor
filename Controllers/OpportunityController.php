<?php
// No namespace declaration - this class is in the global namespace
require_once '../Models/Database.php';
require_once '../Models/Opportunity.php';
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










    function getActiveOpp(){
        $opportunity = new Opportunity();
        $activeOpportunities = $opportunity->getActiveOpportunities();
        return $activeOpportunities;
    }

    function getOppByTravelerID($travelerID){
        $opportunity = new Opportunity();
        $appliedOpportunities = $opportunity->getOpportunitiesByTravelerID($travelerID);
        return $appliedOpportunities;
    }

    function getOppById($opportunityId){
        $opportunity = new Opportunity();
        $opportunityData = $opportunity->getOpportunityById($opportunityId);
        return $opportunityData;
    }

    function checkApplied($travelerID, $opportunityId){
        $opportunity = new Opportunity();
        $hasApplied = $opportunity->checkIfTravelerApplied($travelerID, $opportunityId);
    }

    function getOppByHostId($hostID){
        $opportunity = new Opportunity();
        $opportunityData = $opportunity->getOpportunitiesByHostID($hostID);
        return $opportunityData;
    }    

    function updateOpp($updateData){
        $opportunity = new Opportunity();
        $result = $opportunity->updateOpportunity($updateData);
        return $result;
    }

    function updateOppStatus($opportunityId, $status){
        $opportunity = new Opportunity();
        $result = $opportunity->updateOpportunityStatus($opportunityId, $status);
        return $result;
    }

    public function deleteOpportunity(int $opportunityId): bool {
        $opportunity = new Opportunity();
        
        // First, check if there are any active applications for this opportunity
        $applications = $this->getApplicationsByOpportunityId($opportunityId);
        $hasActiveApplications = false;
        
        if ($applications) {
            foreach ($applications as $application) {
                if ($application['status'] === 'accepted') {
                    $hasActiveApplications = true;
                    break;
                }
            }
        }
        
        // If there are active applications, don't allow deletion
        if ($hasActiveApplications) {
            return false;
        }
        
        // Proceed with deletion
        return $opportunity->deleteOpportunity($opportunityId);
    }
    
    public function getApplicationsByOpportunityId(int $opportunityId): ?array {
        $opportunity = new Opportunity();
        return $opportunity->getApplicationsByOpportunityId($opportunityId);
    }

    /**
     * Create a new opportunity
     * 
     * @param array $data Opportunity data
     * @return bool True if creation was successful, false otherwise
     */
    public function createOpportunity(array $data): bool {
        try {
            // Create a new Opportunity model
            $opportunityModel = new Opportunity();
            
            // Prepare opportunity data
            $opportunityData = [
                'title' => $data['title'],
                'description' => $data['description'],
                'location' => $data['location'],
                'start_date' => $data['start_date'],
                'end_date' => $data['end_date'],
                'category' => $data['category'],
                'opportunity_photo' => $data['image_path'],
                'requirements' => $data['requirements'],
                'host_id' => $data['host_id'],
                'status' => 'open'
            ];
            
            // Save to database using the model's method
            return $opportunityModel->createOpportunity($opportunityData);
        } catch (Exception $e) {
            error_log("Error creating opportunity: " . $e->getMessage());
            return false;
        }
    }

    /**
     * Save opportunity to database
     * 
     * @param Opportunity $opportunity The opportunity object
     * @return bool True if save was successful, false otherwise
     */
    public function saveOpportunityToDB($opportunity): bool {
        try {
            $model = new Opportunity();
            return $model->saveOpportunityToDB($opportunity);
        } catch (Exception $e) {
            error_log("Error saving opportunity: " . $e->getMessage());
            return false;
        }
    }
}
?>

