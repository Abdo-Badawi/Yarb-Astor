<?php

// Include necessary files
include_once '../Controllers/DBController.php';
include_once '../Models/Opportunity.php';
use Models\Opportunity;

class OpportunityController {

    private $db;

    // Constructor to initialize DBController
    public function __construct() {
        $this->db = new DBController(); // Instantiate DBController to manage database connections
        $this->db->openConnection();
    }

    // Function to save Opportunity to DB
    public function saveOpportunityToDB(Opportunity $opportunity): bool {
        $sql = "INSERT INTO opportunity 
                (title, description, location, start_date, end_date, category, host_id, status, opportunity_photo, requirements, created_at)
                VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, NOW())";

        $types = "ssssssssss";
        $params = [
            $opportunity->getTitle(),
            $opportunity->getDescription(),
            $opportunity->getLocation(),
            $opportunity->getStartDate()->format('Y-m-d'),
            $opportunity->getEndDate()->format('Y-m-d'),
            $opportunity->getCategory(),
            $opportunity->getHostId(),
            $opportunity->getStatus(),
            $opportunity->getOpportunityPhoto(),
            $opportunity->getRequirements()
        ];

        return $this->db->insert($sql, $types, $params);
    }

    // Function to fetch all opportunities from DB
    public function getAllOpportunities(): array {
        $sql = "SELECT * FROM opportunity ORDER BY created_at DESC";
        return $this->db->select($sql) ?: [];
    }

    // Function to get a single opportunity by ID
    public function getOpportunityById(int $opportunity_id): ?Opportunity {
        $sql = "SELECT * FROM opportunity WHERE opportunity_id = $opportunity_id";
        $result = $this->db->select($sql);
        
        if (!empty($result)) {
            $row = $result[0];
            
            try {
                $opportunity = new Opportunity(
                    $row['title'],
                    $row['description'],
                    $row['location'],
                    new \DateTime($row['start_date']),
                    new \DateTime($row['end_date']),
                    $row['category'],
                    $row['opportunity_photo'],
                    $row['requirements'],
                );
                
                $opportunity->setId($row['opportunity_id']);
                $opportunity->setHostId($row['host_id']);
                $opportunity->setStatus($row['status']);
                $opportunity->setCreatedAt(new \DateTime($row['created_at']));
                
                return $opportunity;
            } catch (\Exception $e) {
                error_log('Error creating Opportunity object: ' . $e->getMessage());
                return null;
            }
        }

        return null;
    }

    // Function to update an existing opportunity
    public function updateOpportunity(Opportunity $opportunity): bool {
        $sql = "UPDATE opportunity SET 
                title = ?, description = ?, location = ?, start_date = ?, end_date = ?, 
                category = ?, status = ?, opportunity_photo = ?, requirements = ?
                WHERE opportunity_id = ?";

        $types = "sssssssssi";
        $params = [
            $opportunity->getTitle(),
            $opportunity->getDescription(),
            $opportunity->getLocation(),
            $opportunity->getStartDate()->format('Y-m-d'),
            $opportunity->getEndDate()->format('Y-m-d'),
            $opportunity->getCategory(),
            $opportunity->getStatus(),
            $opportunity->getOpportunityPhoto(),
            $opportunity->getRequirements(),
            $opportunity->getId()
        ];

        return $this->db->insert($sql, $types, $params); // Your insert method is used for updates too
    }

    // Function to delete an opportunity
    public function deleteOpportunity(int $opportunity_id): bool {
        $sql = "DELETE FROM opportunity WHERE opportunity_id = ?";
        $types = "i";
        $params = [$opportunity_id];

        return $this->db->insert($sql, $types, $params); // Your insert method is used for deletes too
    }
    
    // Function to get opportunities by host ID
    public function getOpportunitiesByHostID($hostID) {
        $query = "SELECT * FROM opportunity WHERE host_id = ?";
        $params = [$hostID];
        $this->db->openConnection();
        $result = $this->db->selectPrepared($query, "i", $params);
        $this->db->closeConnection();
        return $result;
    }
    
    
    // Function to get opportunities by category
    public function getOpportunitiesByCategory(string $category): array {
        $category = strtolower($category);
        $sql = "SELECT * FROM opportunity WHERE category = '$category' AND status = 'open' ORDER BY created_at DESC";
        return $this->db->select($sql) ?: [];
    }
    
    // Function to get active opportunities
    public function getActiveOpportunities(): array {
        $today = date('Y-m-d');
        $sql = "SELECT * FROM opportunity WHERE status = 'open' AND end_date >= '$today' ORDER BY created_at DESC";
        return $this->db->select($sql) ?: [];
    }
}