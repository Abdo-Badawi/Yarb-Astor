<?php
namespace Models;

require_once __DIR__ . '/../Controllers/DBController.php';

class Application {
    public int $ApplicationID;
    public int $OpportunityID;
    public int $TravelerID;
    public string $Status; // Enum: 'pending', 'accepted', 'rejected'
    public string $Comment;
    public string $AppliedDate;
    public string $Message;
    public string $Availability;
    public string $Experience;
    private $db;

    public function __construct() {
        $this->db = new \DBController();
        $this->Status = 'pending';
        $this->Comment = '';
        $this->AppliedDate = date('Y-m-d H:i:s');
        $this->Message = '';
        $this->Availability = '';
        $this->Experience = '';
    }

    /**
     * Save application to database
     * 
     * @return int|bool Application ID if successful, false otherwise
     */
    public function save(): int|bool {
        // Check if application already exists
        if (isset($this->ApplicationID) && $this->ApplicationID > 0) {
            return $this->update();
        }
        
        // Create new application
        $this->db->openConnection();
        
        $sql = "INSERT INTO applications (opportunity_id, traveler_id, status, comment, applied_date, message, availability, experience) 
                VALUES (?, ?, ?, ?, ?, ?, ?, ?)";
        
        $params = [
            $this->OpportunityID,
            $this->TravelerID,
            $this->Status,
            $this->Comment,
            $this->AppliedDate,
            $this->Message,
            $this->Availability,
            $this->Experience
        ];
        
        $result = $this->db->insert($sql, "iissssss", $params);
        
        if ($result) {
            // Get the last insert ID using mysqli's last_insert_id function
            $this->ApplicationID = $this->db->getConnection()->insert_id;
            $this->db->closeConnection();
            return $this->ApplicationID;
        }
        
        $this->db->closeConnection();
        return false;
    }
    
    /**
     * Update existing application
     * 
     * @return bool True if update successful, false otherwise
     */
    public function update(): bool {
        if (!isset($this->ApplicationID) || $this->ApplicationID <= 0) {
            return false;
        }
        
        $this->db->openConnection();
        
        $sql = "UPDATE applications 
                SET opportunity_id = ?, traveler_id = ?, status = ?, comment = ?, 
                    message = ?, availability = ?, experience = ? 
                WHERE application_id = ?";
        
        $params = [
            $this->OpportunityID,
            $this->TravelerID,
            $this->Status,
            $this->Comment,
            $this->Message,
            $this->Availability,
            $this->Experience,
            $this->ApplicationID
        ];
        
        $result = $this->db->update($sql, "iisssssi", $params);
        $this->db->closeConnection();
        
        return $result;
    }
    
    /**
     * Delete application
     * 
     * @return bool True if deletion successful, false otherwise
     */
    public function delete(): bool {
        if (!isset($this->ApplicationID) || $this->ApplicationID <= 0) {
            return false;
        }
        
        $this->db->openConnection();
        
        $sql = "DELETE FROM applications WHERE application_id = ?";
        $params = [$this->ApplicationID];
        
        $result = $this->db->delete($sql, "i", $params);
        $this->db->closeConnection();
        
        return $result;
    }
    
    /**
     * Load application by ID
     * 
     * @param int $applicationID Application ID
     * @return bool True if application found and loaded, false otherwise
     */
    public function loadById(int $applicationID): bool {
        $this->db->openConnection();
        
        $sql = "SELECT * FROM applications WHERE application_id = ?";
        $params = [$applicationID];
        
        $result = $this->db->selectPrepared($sql, "i", $params);
        $this->db->closeConnection();
        
        if ($result && count($result) > 0) {
            $this->ApplicationID = $result[0]['application_id'];
            $this->OpportunityID = $result[0]['opportunity_id'];
            $this->TravelerID = $result[0]['traveler_id'];
            $this->Status = $result[0]['status'];
            $this->Comment = $result[0]['comment'];
            $this->AppliedDate = $result[0]['applied_date'];
            $this->Message = $result[0]['message'];
            $this->Availability = $result[0]['availability'];
            $this->Experience = $result[0]['experience'];
            
            return true;
        }
        
        return false;
    }
    
    /**
     * Check if application exists
     * 
     * @param int $travelerID Traveler ID
     * @param int $opportunityID Opportunity ID
     * @return bool True if application exists, false otherwise
     */
    public function exists(int $travelerID, int $opportunityID): bool {
        $this->db->openConnection();
        
        $sql = "SELECT COUNT(*) as count FROM applications 
                WHERE traveler_id = ? AND opportunity_id = ?";
        
        $params = [$travelerID, $opportunityID];
        
        $result = $this->db->selectPrepared($sql, "ii", $params);
        $this->db->closeConnection();
        
        return ($result[0]['count'] ?? 0) > 0;
    }
    
    /**
     * Print opportunities in a card layout
     * 
     * @param array $opportunities Array of opportunities
     * @return void
     */
    public static function printOpportunities($opportunities): void {
        // Check if the opportunities array is empty
        if (empty($opportunities)) {
            echo "<p>No opportunities found for this host.</p>";
            return;
        }
    
        // Start the card-style layout
        echo "<div class='row g-4'>";
        
        foreach ($opportunities as $opp) {
            echo "<div class='col-md-6 col-lg-4'>
                    <div class='card h-100 shadow-sm'>
                        <div class='position-relative'>
                            <img src='" . htmlspecialchars($opp['opportunity_photo'] ?? '../assets/img/default-opportunity.jpg') . "' class='card-img-top' alt='Opportunity Image' style='height: 200px; object-fit: cover;'>
                            <div class='position-absolute top-0 end-0 p-2'>
                                <span class='badge bg-primary'>" . htmlspecialchars($opp['category']) . "</span>
                            </div>
                        </div>
                        <div class='card-body'>
                            <h5 class='card-title'>" . htmlspecialchars($opp['title']) . "</h5>
                            <div class='mb-3'>
                                <p class='mb-1'><i class='fa fa-map-marker-alt me-2'></i>" . htmlspecialchars($opp['opportunity_location']) . "</p>
                                <p class='mb-1'><i class='fa fa-calendar me-2'></i>" . htmlspecialchars(date('M d, Y', strtotime($opp['start_date']))) . " - " . htmlspecialchars(date('M d, Y', strtotime($opp['end_date']))) . "</p>
                                <p class='mb-2'><i class='fa fa-comment me-2'></i> Comment: " . htmlspecialchars($opp['comment']) . "</p>
                            </div>
                            <div class='d-flex justify-content-between'>
                                <div>
                                    <button class='btn btn-primary me-2 px-3'>Edit</button>
                                </div>
                                <button class='btn btn-sm btn-danger'>Mark Filled</button>
                            </div>
                        </div>
                    </div>
                </div>";
        }
    
        // End the card layout
        echo "</div>";
    }
} 



