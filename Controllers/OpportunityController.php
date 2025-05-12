<?php
require_once '../Models/Opportunity.php';

class OpportunityController {

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

