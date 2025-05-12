<?php
namespace Models;

require_once __DIR__ . '/../Controllers/DBController.php';

class Review {
    public int $ReviewID;
    public int $SenderID;
    public int $ReceiverID;
    public int $OpportunityID;
    public float $Rating;
    public string $Comment;
    public bool $IsReported;
    public string $CreatedAt;
    private $db;

    public function __construct() {
        $this->db = new \DBController();
        $this->Rating = 0.0;
        $this->Comment = '';
        $this->IsReported = false;
        $this->CreatedAt = date('Y-m-d H:i:s');
    }

    /**
     * Save review to database
     * 
     * @return int|bool Review ID if successful, false otherwise
     */
    public function save(): int|bool {
        // Check if review already exists
        if (isset($this->ReviewID) && $this->ReviewID > 0) {
            return $this->update();
        }
        
        // Create new review
        $this->db->openConnection();
        
        $sql = "INSERT INTO review (sender_id, receiver_id, opportunity_id, rating, comment, created_at) 
                VALUES (?, ?, ?, ?, ?, ?)";
        
        $params = [
            $this->SenderID,
            $this->ReceiverID,
            $this->OpportunityID,
            $this->Rating,
            $this->Comment,
            $this->CreatedAt
        ];
        
        $result = $this->db->insert($sql, "iiidss", $params);
        
        if ($result) {
            // Get the last insert ID using the DBController's method
            $this->ReviewID = $this->db->getInsertId();
            $this->db->closeConnection();
            return $this->ReviewID;
        }
        
        $this->db->closeConnection();
        return false;
    }
    
    /**
     * Update existing review
     * 
     * @return bool True if update successful, false otherwise
     */
    public function update(): bool {
        if (!isset($this->ReviewID) || $this->ReviewID <= 0) {
            return false;
        }
        
        $this->db->openConnection();
        
        $sql = "UPDATE review 
                SET rating = ?, comment = ? 
                WHERE review_id = ?";
        
        $params = [
            $this->Rating,
            $this->Comment,
            $this->ReviewID
        ];
        
        $result = $this->db->update($sql, "dsi", $params);
        $this->db->closeConnection();
        
        return $result;
    }
    
    /**
     * Delete review
     * 
     * @return bool True if deletion successful, false otherwise
     */
    public function delete(): bool {
        if (!isset($this->ReviewID) || $this->ReviewID <= 0) {
            return false;
        }
        
        $this->db->openConnection();
        
        $sql = "DELETE FROM review WHERE review_id = ?";
        $params = [$this->ReviewID];
        
        $result = $this->db->delete($sql, "i", $params);
        $this->db->closeConnection();
        
        return $result;
    }
    
    /**
     * Load review by ID
     * 
     * @param int $reviewID Review ID
     * @return bool True if review found and loaded, false otherwise
     */
    public function loadById(int $reviewID): bool {
        $this->db->openConnection();
        
        $sql = "SELECT * FROM review WHERE review_id = ?";
        $params = [$reviewID];
        
        $result = $this->db->selectPrepared($sql, "i", $params);
        $this->db->closeConnection();
        
        if ($result && count($result) > 0) {
            $this->ReviewID = $result[0]['review_id'];
            $this->SenderID = $result[0]['sender_id'];
            $this->ReceiverID = $result[0]['receiver_id'];
            $this->OpportunityID = $result[0]['opportunity_id'];
            $this->Rating = $result[0]['rating'];
            $this->Comment = $result[0]['comment'];
            $this->CreatedAt = $result[0]['created_at'];
            $this->IsReported = isset($result[0]['is_reported']) && $result[0]['is_reported'] == 1;
            
            return true;
        }
        
        return false;
    }
    
    /**
     * Report a review
     * 
     * @return bool True if report successful, false otherwise
     */
    public function report(): bool {
        if (!isset($this->ReviewID) || $this->ReviewID <= 0) {
            return false;
        }
        
        $this->db->openConnection();
        
        $sql = "UPDATE review SET is_reported = 1 WHERE review_id = ?";
        $params = [$this->ReviewID];
        
        $result = $this->db->update($sql, "i", $params);
        $this->db->closeConnection();
        
        if ($result) {
            $this->IsReported = true;
        }
        
        return $result;
    }
    
    /**
     * Check if a user has already reviewed an opportunity
     * 
     * @param int $senderID Sender ID
     * @param int $opportunityID Opportunity ID
     * @return bool True if already reviewed, false otherwise
     */
    public function hasReviewed(int $senderID, int $opportunityID): bool {
        $this->db->openConnection();
        
        $sql = "SELECT COUNT(*) as count FROM review 
                WHERE sender_id = ? AND opportunity_id = ?";
        
        $params = [$senderID, $opportunityID];
        
        $result = $this->db->selectPrepared($sql, "ii", $params);
        $this->db->closeConnection();
        
        return ($result[0]['count'] ?? 0) > 0;
    }
}







