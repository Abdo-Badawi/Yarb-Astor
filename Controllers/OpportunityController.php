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
        return $this->opportunityModel->deleteOpportunity($opportunityId);
    }
    
    /**
     * Get applications by opportunity ID
     * 
     * @param int $opportunityId The opportunity ID
     * @return array|null Array of applications or null if none found
     */
    public function getApplicationsByOpportunityId(int $opportunityId): ?array {
        return $this->opportunityModel->getApplicationsByOpportunityId($opportunityId);
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
     * Check if traveler has applied to an opportunity
     * 
     * @param int $travelerId The traveler ID
     * @param int $opportunityId The opportunity ID
     * @return bool True if applied, false otherwise
     */
    public function checkApplied($travelerId, $opportunityId) {
        return $this->opportunityModel->checkIfTravelerApplied($travelerId, $opportunityId);
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
     * Create a new opportunity
     * 
     * @param array $data Opportunity data
     * @return bool True if creation was successful, false otherwise
     */
    public function createOpportunity(array $data): bool {
        try {
            // Create a new Opportunity model
            $opportunityModel = new Models\Opportunity("", "", "", new \DateTime(), new \DateTime(), "");
            
            // Prepare opportunity data
            $opportunityData = [
                'title' => $data['title'],
                'description' => $data['description'],
                'location' => $data['location'],
                'start_date' => $data['start_date'],
                'end_date' => $data['end_date'],
                'category' => $data['category'],
                'opportunity_photo' => $data['image_path'] ?? '',
                'requirements' => $data['requirements'] ?? '',
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
     * @param Models\Opportunity $opportunity The opportunity object
     * @return bool True if save was successful, false otherwise
     */
    public function saveOpportunityToDB($opportunity): bool {
        try {
            return $this->opportunityModel->saveOpportunityToDB($opportunity);
        } catch (Exception $e) {
            error_log("Error saving opportunity: " . $e->getMessage());
            return false;
        }
    }
    
    /**
     * Get featured opportunities
     * 
     * @param int $limit Number of opportunities to return
     * @return array Array of featured opportunities
     */
    public function getFeaturedOpportunities(int $limit = 3): array {
        return $this->opportunityModel->getFeaturedOpportunities($limit);
    }
    
    /**
     * Get recent opportunities
     * 
     * @param int $limit Number of opportunities to return
     * @return array Array of recent opportunities
     */
    public function getRecentOpportunities(int $limit = 5): array {
        return $this->opportunityModel->getRecentOpportunities($limit);
    }
    
    /**
     * Apply to an opportunity
     * 
     * @param int $travelerId The traveler ID
     * @param int $opportunityId The opportunity ID
     * @param string $message Application message
     * @return bool True if application was successful, false otherwise
     */
    public function applyToOpportunity(int $travelerId, int $opportunityId, string $message = ''): bool {
        return $this->opportunityModel->applyToOpportunity($travelerId, $opportunityId, $message);
    }
    
    /**
     * Get application status
     * 
     * @param int $travelerId The traveler ID
     * @param int $opportunityId The opportunity ID
     * @return string|null The application status or null if not found
     */
    public function getApplicationStatus(int $travelerId, int $opportunityId): ?string {
        return $this->opportunityModel->getApplicationStatus($travelerId, $opportunityId);
    }
    
    /**
     * Update application status
     * 
     * @param int $applicationId The application ID
     * @param string $status The new status
     * @return bool True if successful, false otherwise
     */
    public function updateApplicationStatus(int $applicationId, string $status): bool {
        return $this->opportunityModel->updateApplicationStatus($applicationId, $status);
    }
    
    /**
     * Get applications by traveler ID
     * 
     * @param int $travelerId The traveler ID
     * @return array Array of applications
     */
    public function getApplicationsByTravelerId(int $travelerId): array {
        return $this->opportunityModel->getApplicationsByTravelerId($travelerId);
    }
    
    /**
     * Get applications for a host
     * 
     * @param int $hostId The host ID
     * @return array Array of applications for the host's opportunities
     */
    public function getApplicationsForHost(int $hostId): array {
        return $this->opportunityModel->getApplicationsForHost($hostId);
    }
}
?>
