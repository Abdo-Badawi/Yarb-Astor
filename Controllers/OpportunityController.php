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

    // Function to delete an opportunity
    public function deleteOpportunity(int $opportunityId): bool {
        // First, delete any applications associated with this opportunity
        $sqlApplications = "DELETE FROM applications WHERE opportunity_id = ?";
        
        $this->db->openConnection();
        $this->db->delete($sqlApplications, "i", [$opportunityId]);
        
        // Then delete the opportunity itself
        $sql = "DELETE FROM opportunity WHERE opportunity_id = ?";
        
        $params = [$opportunityId];
        $result = $this->db->delete($sql, "i", $params);
        $this->db->closeConnection();
        
        return $result;
    }

    // Function to update opportunity status
    public function updateOpportunityStatus(int $opportunityId, string $status): bool {
        $sql = "UPDATE opportunity SET status = ? WHERE opportunity_id = ?";

        $params = [$status, $opportunityId];

        $this->db->openConnection();
        $result = $this->db->update($sql, "si", $params);
        $this->db->closeConnection();

        return $result;
    }

    // Function to update an opportunity
    public function updateOpportunity(array $opportunityData): bool {
        $sql = "UPDATE opportunity SET
                title = ?,
                description = ?,
                location = ?,
                start_date = ?,
                end_date = ?,
                category = ?,
                requirements = ?,
                opportunity_photo = ?
                WHERE opportunity_id = ?";

        $params = [
            $opportunityData['title'],
            $opportunityData['description'],
            $opportunityData['location'],
            $opportunityData['start_date'],
            $opportunityData['end_date'],
            $opportunityData['category'],
            $opportunityData['requirements'],
            $opportunityData['image_path'],
            $opportunityData['opportunity_id']
        ];

        $this->db->openConnection();
        $result = $this->db->update($sql, "ssssssssi", $params);
        $this->db->closeConnection();

        return $result;
    }

    // Function to get all opportunities
    public function getAllOpportunities(): array {
        $sql = "SELECT o.*, u.first_name, u.last_name, u.profile_picture
                FROM opportunity o
                JOIN users u ON o.host_id = u.user_id
                ORDER BY o.created_at DESC";

        $this->db->openConnection();
        $result = $this->db->select($sql);
        $this->db->closeConnection();

        return $result ?: [];
    }

    public function saveOpportunityToDB($opportunity) {
        try {
            // Ensure database connection is established
            $this->db->openConnection();

            // Get image path
            $imagePath = $opportunity->getImagePath();

            // Debug information
            error_log("Image path: " . ($imagePath ?? 'null'));

            // Prepare the SQL statement with the correct column name "opportunity_photo"
            $sql = "INSERT INTO opportunity (title, description, location, start_date, end_date, category, opportunity_photo, requirements, host_id, status, created_at)
                    VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, 'open', NOW())";

            // Format dates for MySQL
            $startDate = $opportunity->getStartDate()->format('Y-m-d');
            $endDate = $opportunity->getEndDate()->format('Y-m-d');

            // Set parameters
            $params = [
                $opportunity->getTitle(),
                $opportunity->getDescription(),
                $opportunity->getLocation(),
                $startDate,
                $endDate,
                $opportunity->getCategory(),
                $imagePath,
                $opportunity->getRequirements(),
                $opportunity->getHostId()
            ];

            // Debug information
            error_log("Saving opportunity with params: " . print_r($params, true));

            // Execute the query
            $result = $this->db->insert($sql, "ssssssssi", $params);

            // Close the connection
            $this->db->closeConnection();

            if (!$result) {
                error_log("Database insert failed in saveOpportunityToDB");
            } else {
                error_log("Opportunity saved successfully with ID: " . $result);
            }

            return $result;
        } catch (Exception $e) {
            error_log("Error saving opportunity: " . $e->getMessage());
            return false;
        }
    }
}
?>


