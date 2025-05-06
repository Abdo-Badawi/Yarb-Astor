<?php
require_once 'DBController.php';

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

    // Function to get opportunity by ID
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

    // Function to get active opportunities
    public function getActiveOpportunities(): array {
        $currentDate = date('Y-m-d');

        $sql = "SELECT o.*, u.first_name, u.last_name
                FROM opportunity o
                JOIN users u ON o.host_id = u.user_id
                WHERE o.status = 'open'
                AND o.end_date >= ?
                ORDER BY o.created_at DESC";

        $params = [$currentDate];

        $this->db->openConnection();
        $result = $this->db->selectPrepared($sql, "s", $params);
        $this->db->closeConnection();

        return $result ?: [];
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
        $sql = "SELECT * FROM opportunity WHERE host_id = ? ORDER BY created_at DESC";

        $params = [$hostID];

        $this->db->openConnection();
        $result = $this->db->selectPrepared($sql, "i", $params);
        $this->db->closeConnection();

        return $result ?: [];
    }

    // Function to get all opportunities for admin
    public function getAllOpportunities(): array {
        $sql = "SELECT o.*, u.first_name, u.last_name
                FROM opportunity o
                JOIN users u ON o.host_id = u.user_id
                ORDER BY o.created_at DESC";

        $this->db->openConnection();
        $result = $this->db->select($sql);
        $this->db->closeConnection();

        return $result ?: [];
    }

    // Function to delete an opportunity
    public function deleteOpportunity(int $opportunityId): bool {
        // First delete any applications for this opportunity
        $sqlDeleteApplications = "DELETE FROM applications WHERE opportunity_id = ?";

        $this->db->openConnection();
        $this->db->insert($sqlDeleteApplications, "i", [$opportunityId]);

        // Then delete the opportunity
        $sqlDeleteOpportunity = "DELETE FROM opportunity WHERE opportunity_id = ?";
        $result = $this->db->insert($sqlDeleteOpportunity, "i", [$opportunityId]);
        $this->db->closeConnection();

        return $result;
    }
}
?>
