<?php
require_once '../Models/Opportunity.php';

class OpportunityController {

    function getActiveOpp(){
        $opportunity = new Opportunity();
        $activeOpportunities = $opportunity->getActiveOpportunities();
        return $activeOpportunities;
    }

    public function getOppByTravelerID(int $travelerID): array {
        try {
            $opportunity = new Opportunity();
            $applications = $opportunity->getOpportunitiesByTravelerID($travelerID);
            
            // Debug information
            error_log("Retrieved " . count($applications) . " applications for traveler ID $travelerID");
            
            return $applications;
        } catch (Exception $e) {
            error_log("Error getting opportunities by traveler ID: " . $e->getMessage());
            return [];
        }
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

    public function saveOpportunityToDB($opportunity): bool {
        try {
            $model = new Opportunity();
            return $model->saveOpportunityToDB($opportunity);
        } catch (Exception $e) {
            error_log("Error saving opportunity: " . $e->getMessage());
            return false;
        }
    }

    public function getOpportunityWithHostById(int $opportunityId): ?array {
        try {
            $opportunity = new Opportunity();
            $data = $opportunity->getOpportunityWithHostById($opportunityId);
            
            // Debug information
            if ($data) {
                error_log("Retrieved opportunity with host data for ID $opportunityId");
            } else {
                error_log("No data found for opportunity ID $opportunityId");
            }
            
            return $data;
        } catch (Exception $e) {
            error_log("Error getting opportunity with host by ID: " . $e->getMessage());
            return null;
        }
    }

    public function searchOpportunities(array $filters = []): array {
        try {
            $opportunity = new Opportunity();
            return $opportunity->searchOpportunities($filters);
        } catch (Exception $e) {
            error_log("Error searching opportunities: " . $e->getMessage());
            return [];
        }
    }

    public function getAvailableCategories(): array {
        try {
            $opportunity = new Opportunity();
            return $opportunity->getAvailableCategories();
        } catch (Exception $e) {
            error_log("Error getting available categories: " . $e->getMessage());
            return [];
        }
    }

    public function getAvailableLocations(): array {
        try {
            $opportunity = new Opportunity();
            return $opportunity->getAvailableLocations();
        } catch (Exception $e) {
            error_log("Error getting available locations: " . $e->getMessage());
            return [];
        }
    }
}
?>





