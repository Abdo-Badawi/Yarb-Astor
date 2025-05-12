<?php
require_once '../Models/Database.php';

class Application {
    private $db;
    
    public function __construct() {
        $this->db = new Database();
    }

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
        
        return $result ? $result[0] : null;
    }

    /**
     * Update application status
     * 
     * @param int $applicationID The application ID
     * @param string $status The new status ('pending', 'accepted', 'rejected')
     * @return bool True if update was successful, false otherwise
     */
    public function updateApplicationStatus(int $applicationID, string $status): bool {
        // Validate status
        if (!in_array($status, ['pending', 'accepted', 'rejected'])) {
            return false;
        }
        
        $sql = "UPDATE applications SET status = ? WHERE application_id = ?";
        $params = [$status, $applicationID];
        
        $this->db->openConnection();
        $result = $this->db->update($sql, "si", $params);
        $this->db->closeConnection();
        
        return $result;
    }
}
?>



