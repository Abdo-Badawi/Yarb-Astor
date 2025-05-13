<?php
// No namespace declaration - this class is in the global namespace
require_once '../Models/Database.php';
require_once '../Models/Opportunity.php';

class OpportunityController {
    private $db;
    private $opportunityModel;

    public function __construct() {
        $this->db = new Database();
        // Create a new Opportunity model with proper initialization
        $this->opportunityModel = new Models\Opportunity();
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
     * @return array|null The opportunity data or null if not found
     */
    public function getOpportunityById($opportunityId) {
        try {
            return $this->opportunityModel->getOpportunityById($opportunityId);
        } catch (Exception $e) {
            error_log("Exception in getOpportunityById: " . $e->getMessage());
            return null;
        }
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
        try {
            // Log the deletion attempt
            error_log("Attempting to delete opportunity ID: $opportunityId");
            
            // First, check if there are any active applications for this opportunity
            $applications = $this->getApplicationsByOpportunityId($opportunityId);
            $hasActiveApplications = false;
            
            if ($applications) {
                foreach ($applications as $application) {
                    if ($application['status'] === 'accepted') {
                        $hasActiveApplications = true;
                        error_log("Cannot delete opportunity ID: $opportunityId - has active applications");
                        break;
                    }
                }
            }
            
            // If there are active applications, don't allow deletion
            if ($hasActiveApplications) {
                return false;
            }
            
            // Proceed with deletion
            $result = $this->opportunityModel->deleteOpportunity($opportunityId);
            
            if ($result) {
                error_log("Successfully deleted opportunity ID: $opportunityId");
            } else {
                error_log("Failed to delete opportunity ID: $opportunityId in model");
            }
            
            return $result;
        } catch (Exception $e) {
            error_log("Exception in deleteOpportunity: " . $e->getMessage());
            return false;
        }
    }
    
    /**
     * Get applications by opportunity ID
     * 
     * @param int $opportunityId The opportunity ID
     * @return array|null Array of applications or null if none found
     */
    public function getApplicationsByOpportunityId($opportunityId) {
        return $this->opportunityModel->getApplicationsByOpportunityId($opportunityId);
    }

    /**
     * Update the status of an opportunity
     * 
     * @param int $opportunityId The opportunity ID
     * @param string $status The new status ('open', 'closed', 'cancelled', 'deleted', 'reported')
     * @return bool True if successful, false otherwise
     */
    public function updateOpportunityStatus($opportunityId, $status) {
        try {
            // Validate status
            $validStatuses = ['open', 'closed', 'cancelled', 'deleted', 'reported'];
            if (!in_array($status, $validStatuses)) {
                return false;
            }
            
            // Update the opportunity status
            return $this->opportunityModel->updateOpportunityStatus($opportunityId, $status);
        } catch (Exception $e) {
            error_log("Exception in updateOpportunityStatus: " . $e->getMessage());
            return false;
        }
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
     * Check if traveler has already applied for an opportunity
     * 
     * @param int $travelerId The traveler ID
     * @param int $opportunityId The opportunity ID
     * @return bool True if already applied, false otherwise
     */
    public function checkIfTravelerApplied(int $travelerId, int $opportunityId): bool {
        return $this->opportunityModel->checkIfTravelerApplied($travelerId, $opportunityId);
    }

    /**
     * Update an opportunity
     * 
     * @param array $data The opportunity data to update
     * @return bool True if successful, false otherwise
     */
    public function updateOpportunity($data) {
        try {
            // Validate required fields
            $requiredFields = ['opportunity_id', 'title', 'description', 'location', 'start_date', 'end_date', 'category', 'requirements'];
            foreach ($requiredFields as $field) {
                if (!isset($data[$field]) || empty($data[$field])) {
                    error_log("Missing required field for opportunity update: $field");
                    return false;
                }
            }
            
            // Validate dates
            $startDate = new DateTime($data['start_date']);
            $endDate = new DateTime($data['end_date']);
            
            if ($endDate < $startDate) {
                error_log("End date cannot be earlier than start date");
                return false;
            }
            
            // Format dates for database
            $data['start_date'] = $startDate->format('Y-m-d');
            $data['end_date'] = $endDate->format('Y-m-d');
            
            // Validate status if provided
            if (isset($data['status'])) {
                $validStatuses = ['open', 'closed', 'cancelled'];
                if (!in_array($data['status'], $validStatuses)) {
                    error_log("Invalid status for opportunity update: " . $data['status']);
                    return false;
                }
            } else {
                // Default to 'open' if not provided
                $data['status'] = 'open';
            }
            
            // Call the model to update the opportunity
            return $this->opportunityModel->updateOpportunity($data);
        } catch (Exception $e) {
            error_log("Exception in updateOpportunity: " . $e->getMessage());
            return false;
        }
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

    /**
     * Apply for an opportunity
     * 
     * @param array $applicationData Application data including traveler_id, opportunity_id, message, etc.
     * @return bool True if application was successful, false otherwise
     */
    public function applyForOpportunity(array $applicationData): bool {
        return $this->opportunityModel->applyForOpportunity($applicationData);
    }
}
?>
