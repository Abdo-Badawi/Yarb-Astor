<?php
include_once '../Models/Database.php';

class TravelerFee {
    private $db;
    
    public function __construct() {
        $this->db = new \Database();
    }
    
    /**
     * Get all active fees
     * 
     * @return array Array of active fees
     */
    public function getAllActiveFees(): array {
        $this->db->openConnection();
        
        $query = "SELECT * FROM fees WHERE status = 'active' ORDER BY is_mandatory DESC, fee_name ASC";
        
        $result = $this->db->select($query);
        
        $this->db->closeConnection();
        
        return $result ?: [];
    }
    
    /**
     * Get all published fees
     * 
     * @return array Array of published fees
     */
    public function getAllPublishedFees(): array {
        $this->db->openConnection();
        
        $query = "SELECT * FROM fees WHERE status = 'active' ORDER BY is_mandatory DESC, fee_name ASC";
        
        $result = $this->db->select($query);
        
        $this->db->closeConnection();
        
        return $result ?: [];
    }
    
    /**
     * Get fee by ID
     * 
     * @param int $feeId Fee ID
     * @return array|null Fee data or null if not found
     */
    public function getFeeById(int $feeId): ?array {
        $this->db->openConnection();
        
        $query = "SELECT * FROM fees WHERE fee_id = ?";
        
        $result = $this->db->selectPrepared($query, "i", [$feeId]);
        
        $this->db->closeConnection();
        
        return !empty($result) ? $result[0] : null;
    }
    
    /**
     * Get traveler information
     * 
     * @param int $travelerId Traveler ID
     * @return array|null Traveler data or null if not found
     */
    public function getTravelerInfo(int $travelerId): ?array {
        $this->db->openConnection();
        
        $query = "SELECT * FROM travelers WHERE traveler_id = ?";
        
        $result = $this->db->selectPrepared($query, "i", [$travelerId]);
        
        $this->db->closeConnection();
        
        return !empty($result) ? $result[0] : null;
    }
    
    /**
     * Check if a traveler has paid a fee
     * 
     * @param int $travelerId Traveler ID
     * @param int $feeId Fee ID
     * @return bool True if paid, false otherwise
     */
    public function hasPaidFee(int $travelerId, int $feeId): bool {
        $this->db->openConnection();
        
        $query = "SELECT COUNT(*) as count FROM fee_transactions 
                 WHERE traveler_id = ? 
                 AND fee_id = ? 
                 AND status = 'completed'";
        
        $result = $this->db->selectPrepared($query, "ii", [$travelerId, $feeId]);
        
        $this->db->closeConnection();
        
        return !empty($result) && $result[0]['count'] > 0;
    }
    
    /**
     * Get applicable fees for a traveler with payment status
     * 
     * @param int $travelerId Traveler ID
     * @return array Array of applicable fees with payment status
     */
    public function getApplicableFees(int $travelerId): array {
        $this->db->openConnection();
        
        $query = "SELECT f.*, 
                 (SELECT COUNT(*) FROM fee_transactions 
                  WHERE traveler_id = ? 
                  AND fee_id = f.fee_id 
                  AND status = 'completed') > 0 as is_paid
                 FROM fees f
                 WHERE f.status = 'active'
                 ORDER BY f.is_mandatory DESC, f.fee_name ASC";
        
        $result = $this->db->selectPrepared($query, "i", [$travelerId]);
        
        $this->db->closeConnection();
        
        return $result ?: [];
    }
    
    /**
     * Get fee transactions for a traveler
     * 
     * @param int $travelerId Traveler ID
     * @return array Array of fee transactions
     */
    public function getFeeTransactions(int $travelerId): array {
        $this->db->openConnection();
        
        $query = "SELECT t.*, f.fee_name 
                 FROM fee_transactions t
                 JOIN fees f ON t.fee_id = f.fee_id
                 WHERE t.traveler_id = ?
                 ORDER BY t.payment_date DESC";
        
        $result = $this->db->selectPrepared($query, "i", [$travelerId]);
        
        $this->db->closeConnection();
        
        return $result ?: [];
    }
    
    /**
     * Record a fee transaction
     * 
     * @param array $transactionData Transaction data
     * @return int Transaction ID or 0 on failure
     */
    public function recordTransaction(array $transactionData): int {
        $this->db->openConnection();
        
        $query = "INSERT INTO fee_transactions 
                 (traveler_id, fee_id, amount, payment_method, transaction_reference, status, payment_date) 
                 VALUES 
                 (?, ?, ?, ?, ?, ?, ?)";
        
        $params = [
            $transactionData['traveler_id'],
            $transactionData['fee_id'],
            $transactionData['amount'],
            $transactionData['payment_method'],
            $transactionData['transaction_reference'],
            $transactionData['status'],
            $transactionData['payment_date'] ?? date('Y-m-d H:i:s')
        ];
        
        $types = "iidsss" . (isset($transactionData['payment_date']) ? 's' : 's');
        
        $result = $this->db->insert($query, $types, $params);
        $insertId = $result ? $this->db->getInsertId() : 0;
        
        $this->db->closeConnection();
        
        return $insertId;
    }
    
    /**
     * Get saved cards for a traveler
     * 
     * @param int $travelerId Traveler ID
     * @return array Array of saved cards
     */
    public function getSavedCards(int $travelerId): array {
        $this->db->openConnection();
        
        $query = "SELECT * FROM card WHERE traveler_id = ?";
        
        $result = $this->db->selectPrepared($query, "i", [$travelerId]);
        
        $this->db->closeConnection();
        
        // Mask card numbers for security
        if (!empty($result)) {
            foreach ($result as &$card) {
                $card['card_number'] = $this->maskCardNumber($card['card_number']);
            }
        }
        
        return $result ?: [];
    }
    
    /**
     * Get card by ID
     * 
     * @param int $cardId Card ID
     * @return array|null Card data or null if not found
     */
    public function getCardById(int $cardId): ?array {
        $this->db->openConnection();
        
        $query = "SELECT * FROM card WHERE card_id = ?";
        
        $result = $this->db->selectPrepared($query, "i", [$cardId]);
        
        $this->db->closeConnection();
        
        if (!empty($result)) {
            $card = $result[0];
            // Mask card number for security
            $card['card_number'] = $this->maskCardNumber($card['card_number']);
            return $card;
        }
        
        return null;
    }
    
    /**
     * Save a payment card
     * 
     * @param array $cardData Card data
     * @return int Card ID or 0 on failure
     */
    public function saveCard(array $cardData): int {
        $this->db->openConnection();
        
        $query = "INSERT INTO card 
                 (traveler_id, card_number, card_holder_name, expiry_date, card_type) 
                 VALUES 
                 (?, ?, ?, ?, ?)";
        
        $params = [
            $cardData['traveler_id'],
            $cardData['card_number'],
            $cardData['card_holder'],
            $cardData['expiry_date'],
            $cardData['card_type'] ?? 'unknown'
        ];
        
        $types = "issss";
        
        $result = $this->db->insert($query, $types, $params);
        $insertId = $result ? $this->db->getInsertId() : 0;
        
        $this->db->closeConnection();
        
        return $insertId;
    }
    
    /**
     * Mask a card number for security
     * 
     * @param string $cardNumber Full card number
     * @return string Masked card number
     */
    private function maskCardNumber(string $cardNumber): string {
        // Keep first 4 and last 4 digits visible, mask the rest
        $length = strlen($cardNumber);
        if ($length <= 8) {
            return $cardNumber; // Too short to mask effectively
        }
        
        $firstFour = substr($cardNumber, 0, 4);
        $lastFour = substr($cardNumber, -4);
        $maskedLength = $length - 8;
        $masked = str_repeat('*', $maskedLength);
        
        return $firstFour . $masked . $lastFour;
    }
}?>


