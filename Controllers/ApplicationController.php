<?php
require_once 'DBController.php';
require_once '../Models/Application.php';
use Models\Application;

class ApplicationController {
    private $db;
    private $applicationModel;
    
    public function __construct() {
        $this->db = new DBController();
        $this->applicationModel = new Application();
    }
    
    /**
     * Get applications for opportunities created by a host
     * 
     * @param int $hostID The host ID
     * @return array List of applications with traveler and opportunity details
     */
    public function getApplicationByOpportunityID(int $hostID): array {
        $sql = "SELECT a.*, 
                o.title, o.category, o.location as opportunity_location, o.start_date, o.end_date, o.opportunity_photo,
                u.first_name, u.last_name, u.email, u.phone_number, u.profile_picture, u.gender,
                t.language_spoken, t.location as traveler_location
                FROM applications a
                JOIN opportunity o ON a.opportunity_id = o.opportunity_id
                JOIN users u ON a.traveler_id = u.user_id
                JOIN traveler t ON a.traveler_id = t.traveler_id
                WHERE o.host_id = ?
                ORDER BY a.applied_date DESC";
        
        $params = [$hostID];
        
        $this->db->openConnection();
        $result = $this->db->selectPrepared($sql, "i", $params);
        $this->db->closeConnection();
        
        return $result ?: [];
    }
    
    /**
     * Get application details by ID
     * 
     * @param int $applicationID The application ID
     * @return array|null Application details or null if not found
     */
    public function getApplicationByID(int $applicationID): ?array {
        $sql = "SELECT a.*, 
                o.title, o.category, o.description, o.location as opportunity_location, o.start_date, o.end_date, o.opportunity_photo, o.requirements, o.host_id,
                u.first_name, u.last_name, u.email, u.phone_number, u.profile_picture, u.gender, u.date_of_birth,
                t.language_spoken, t.location as traveler_location, t.bio, t.skill as skills, t.skill as interests, t.skill as experience_level
                FROM applications a
                JOIN opportunity o ON a.opportunity_id = o.opportunity_id
                JOIN users u ON a.traveler_id = u.user_id
                JOIN traveler t ON a.traveler_id = t.traveler_id
                WHERE a.application_id = ?";
        
        $params = [$applicationID];
        
        $this->db->openConnection();
        $result = $this->db->selectPrepared($sql, "i", $params);
        $this->db->closeConnection();
        
        return $result[0] ?? null;
    }
    
    /**
     * Update application status
     * 
     * @param int $applicationID The application ID
     * @param string $status The new status ('accepted', 'rejected', 'pending')
     * @return bool True if update successful, false otherwise
     */
    public function updateApplicationStatus(int $applicationID, string $status): bool {
        $sql = "UPDATE applications SET status = ? WHERE application_id = ?";
        
        $params = [$status, $applicationID];
        
        $this->db->openConnection();
        $result = $this->db->update($sql, "si", $params);
        $this->db->closeConnection();
        
        return $result;
    }
    
    /**
     * Create a new application
     * 
     * @param array $applicationData Application data
     * @return int|bool Application ID if successful, false otherwise
     */
    public function createApplication(array $applicationData): int|bool {
        $sql = "INSERT INTO applications (opportunity_id, traveler_id, status, comment, applied_date, message, availability, experience) 
                VALUES (?, ?, ?, ?, ?, ?, ?, ?)";
        
        $params = [
            $applicationData['opportunity_id'],
            $applicationData['traveler_id'],
            $applicationData['status'] ?? 'pending',
            $applicationData['comment'] ?? '',
            $applicationData['applied_date'] ?? date('Y-m-d H:i:s'),
            $applicationData['message'] ?? '',
            $applicationData['availability'] ?? '',
            $applicationData['experience'] ?? ''
        ];
        
        $this->db->openConnection();
        $result = $this->db->insert($sql, "iissssss", $params);
        
        if ($result) {
            // Get the last insert ID using the proper method
            $applicationId = $this->db->getInsertId();
            $this->db->closeConnection();
            return $applicationId;
        }
        
        $this->db->closeConnection();
        return false;
    }
    
    /**
     * Get applications by traveler ID
     * 
     * @param int $travelerID The traveler ID
     * @return array List of applications with opportunity details
     */
    public function getApplicationsByTravelerID(int $travelerID): array {
        $sql = "SELECT a.*, 
                o.title, o.category, o.location as opportunity_location, o.start_date, o.end_date, o.opportunity_photo,
                h.first_name as host_first_name, h.last_name as host_last_name
                FROM applications a
                JOIN opportunity o ON a.opportunity_id = o.opportunity_id
                JOIN users h ON o.host_id = h.user_id
                WHERE a.traveler_id = ?
                ORDER BY a.applied_date DESC";
        
        $params = [$travelerID];
        
        $this->db->openConnection();
        $result = $this->db->selectPrepared($sql, "i", $params);
        $this->db->closeConnection();
        
        return $result ?: [];
    }
    
    /**
     * Delete an application
     * 
     * @param int $applicationID The application ID
     * @return bool True if deletion successful, false otherwise
     */
    public function deleteApplication(int $applicationID): bool {
        $sql = "DELETE FROM applications WHERE application_id = ?";
        
        $params = [$applicationID];
        
        $this->db->openConnection();
        $result = $this->db->delete($sql, "i", $params);
        $this->db->closeConnection();
        
        return $result;
    }
    
    /**
     * Check if a traveler has already applied for an opportunity
     * 
     * @param int $travelerID The traveler ID
     * @param int $opportunityID The opportunity ID
     * @return bool True if already applied, false otherwise
     */
    public function hasApplied(int $travelerID, int $opportunityID): bool {
        $sql = "SELECT COUNT(*) as count FROM applications 
                WHERE traveler_id = ? AND opportunity_id = ?";
        
        $params = [$travelerID, $opportunityID];
        
        $this->db->openConnection();
        $result = $this->db->selectPrepared($sql, "ii", $params);
        $this->db->closeConnection();
        
        return ($result[0]['count'] ?? 0) > 0;
    }
    
    /**
     * Get application statistics for a host
     * 
     * @param int $hostID The host ID
     * @return array Application statistics
     */
    public function getApplicationStats(int $hostID): array {
        $sql = "SELECT 
                COUNT(*) as total_applications,
                SUM(CASE WHEN a.status = 'pending' THEN 1 ELSE 0 END) as pending_applications,
                SUM(CASE WHEN a.status = 'accepted' THEN 1 ELSE 0 END) as accepted_applications,
                SUM(CASE WHEN a.status = 'rejected' THEN 1 ELSE 0 END) as rejected_applications
                FROM applications a
                JOIN opportunity o ON a.opportunity_id = o.opportunity_id
                WHERE o.host_id = ?";
        
        $params = [$hostID];
        
        $this->db->openConnection();
        $result = $this->db->selectPrepared($sql, "i", $params);
        $this->db->closeConnection();
        
        return $result[0] ?? [
            'total_applications' => 0,
            'pending_applications' => 0,
            'accepted_applications' => 0,
            'rejected_applications' => 0
        ];
    }
}
?>





