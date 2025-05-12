<?php
require_once 'DBController.php';
require_once '../Models/Opportunity.php';
use Models\Opportunity;

class OpportunityController {
    private $db;

    public function __construct() {
        $this->db = new DBController();
    }

    // Function to check if a traveler has already applied for an opportunity
    public function checkIfTravelerApplied(int $travelerId, int $opportunityId): bool {
        $sql = "SELECT * FROM applications WHERE traveler_id = ? AND opportunity_id = ?";
        $params = [$travelerId, $opportunityId];

        $this->db->openConnection();
        $result = $this->db->selectPrepared($sql, "ii", $params);
        $this->db->closeConnection();

        return !empty($result);
    }

    // Function to apply for an opportunity
    public function applyForOpportunity(array $applicationData): bool {
        $sql = "INSERT INTO applications (traveler_id, opportunity_id, message, availability, experience, status, applied_date)
                VALUES (?, ?, ?, ?, ?, ?, ?)";

        $params = [
            $applicationData['traveler_id'],
            $applicationData['opportunity_id'],
            $applicationData['message'],
            $applicationData['availability'],
            $applicationData['experience'],
            $applicationData['status'],
            $applicationData['applied_date']
        ];

        $this->db->openConnection();
        $result = $this->db->insert($sql, "iisssss", $params);
        $this->db->closeConnection();

        return $result;
    }

    /**
     * Save opportunity to database
     * 
     * @param Opportunity $opportunity Opportunity object
     * @return int|bool Opportunity ID if successful, false otherwise
     */
    public function saveOpportunityToDB(Opportunity $opportunity): int|bool {
        return $opportunity->save();
    }

    /**
     * Get opportunity by ID
     * 
     * @param int $opportunityId Opportunity ID
     * @return array|null Opportunity data as array if found, null otherwise
     */
    public function getOpportunityById(int $opportunityId): ?array {
        $sql = "SELECT o.*, u.first_name, u.last_name, u.profile_picture
                FROM opportunity o
                JOIN users u ON o.host_id = u.user_id
                WHERE o.opportunity_id = ?";

        $params = [$opportunityId];

        $this->db->openConnection();
        $result = $this->db->selectPrepared($sql, "i", $params);
        $this->db->closeConnection();

        return $result[0] ?? null;
    }

    /**
     * Get active opportunities
     * 
     * @param array $filters Optional filters for the opportunities
     * @return array Array of active opportunities
     */
    public function getActiveOpportunities(array $filters = []): array {
        return Opportunity::getActive($filters);
    }

    // Function to get opportunities that a traveler has applied to
    public function getOpportunitiesByTravelerID(int $travelerID): array {
        $sql = "SELECT o.*, a.status as status, a.applied_date
                FROM opportunity o
                JOIN applications a ON o.opportunity_id = a.opportunity_id
                WHERE a.traveler_id = ?
                ORDER BY a.applied_date DESC";

        $params = [$travelerID];
        $this->db->openConnection();
        $result = $this->db->selectPrepared($sql, "i", $params);
        $this->db->closeConnection();

        return $result ?: [];
    }

    // Function to get applications for an opportunity
    public function getApplicationsByOpportunityId(int $opportunityId): array {
        $sql = "SELECT a.*, u.first_name, u.last_name, u.profile_picture
                FROM applications a
                JOIN users u ON a.traveler_id = u.user_id
                WHERE a.opportunity_id = ?
                ORDER BY a.applied_date DESC";

        $params = [$opportunityId];

        $this->db->openConnection();
        $result = $this->db->selectPrepared($sql, "i", $params);
        $this->db->closeConnection();

        return $result ?: [];
    }

    // Function to update application status
    public function updateApplicationStatus(int $applicationId, string $status): bool {
        $sql = "UPDATE applications SET status = ? WHERE application_id = ?";

        $params = [$status, $applicationId];

        $this->db->openConnection();
        $result = $this->db->update($sql, "si", $params);
        $this->db->closeConnection();

        return $result;
    }

    // Function to get opportunities by host ID
    public function getOpportunitiesByHostID(int $hostID): array {
        return Opportunity::getByHostId($hostID);
    }

    /**
     * Delete an opportunity
     * 
     * @param int $opportunityId Opportunity ID
     * @return bool True if deletion successful, false otherwise
     */
    public function deleteOpportunity(int $opportunityId): bool {
        $opportunity = new Opportunity();
        if ($opportunity->loadById($opportunityId)) {
            return $opportunity->delete();
        }
        return false;
    }

    /**
     * Update an opportunity
     * 
     * @param Opportunity $opportunity The opportunity object to update
     * @return bool True if update successful, false otherwise
     */
    public function updateOpportunityObject(Opportunity $opportunity): bool {
        return $opportunity->update();
    }

    /**
     * Update an opportunity
     * 
     * @param array $opportunityData Opportunity data
     * @return bool True if update successful, false otherwise
     */
    public function updateOpportunity(array $opportunityData): bool {
        $opportunity = new Opportunity();
        if ($opportunity->loadById($opportunityData['opportunity_id'])) {
            // Update properties
            $opportunity->setTitle($opportunityData['title']);
            $opportunity->setDescription($opportunityData['description']);
            $opportunity->setLocation($opportunityData['location']);
            $opportunity->setStartDate(new \DateTime($opportunityData['start_date']));
            $opportunity->setEndDate(new \DateTime($opportunityData['end_date']));
            $opportunity->setCategory($opportunityData['category']);
            $opportunity->setRequirements($opportunityData['requirements']);
            $opportunity->setOpportunityPhoto($opportunityData['opportunity_photo']);
            $opportunity->setStatus($opportunityData['status']);
            $opportunity->setMaxVolunteers($opportunityData['max_volunteers']);
            
            return $opportunity->update();
        }
        return false;
    }

    /**
     * Get all opportunities
     * 
     * @param array $filters Optional filters for the opportunities
     * @return array Array of opportunities
     */
    public function getAllOpportunities(array $filters = []): array {
        return Opportunity::getAll($filters);
    }
    
    /**
     * Get basic active opportunities (without filters)
     * 
     * @return array Array of active opportunities
     */
    public function getBasicActiveOpportunities(): array {
        return Opportunity::getActive([]);
    }
    
    /**
     * Get opportunities by location
     * 
     * @param string $location Location
     * @return array Array of opportunities in the location
     */
    public function getOpportunitiesByLocation(string $location): array {
        $filters = ['location' => $location];
        return Opportunity::getAll($filters);
    }
}

