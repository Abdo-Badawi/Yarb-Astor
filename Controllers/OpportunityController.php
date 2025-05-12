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

    /**
     * Delete an opportunity
     * 
     * @param int $opportunityId The ID of the opportunity to delete
     * @return bool True if deletion was successful, false otherwise
     */
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
    
    /**
     * Get applications for a specific opportunity
     * 
     * @param int $opportunityId The ID of the opportunity
     * @return array|null Array of applications or null if none found
     */
    public function getApplicationsByOpportunityId(int $opportunityId): ?array {
        $opportunity = new Opportunity();
        return $opportunity->getApplicationsByOpportunityId($opportunityId);
    }
}
?>





