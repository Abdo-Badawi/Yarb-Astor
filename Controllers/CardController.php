<?php
require_once 'DBController.php';
require_once '../Models/Card.php';

class CardController {
    private $db;
    
    public function __construct() {
        $this->db = new DBController();
    }
    
    // Get all cards for a traveler
    public function getCardsByTravelerId(int $travelerId): array {
        $sql = "SELECT * FROM card WHERE traveler_id = ? ORDER BY card_id DESC";
        
        $this->db->openConnection();
        $result = $this->db->selectPrepared($sql, "i", [$travelerId]);
        $this->db->closeConnection();
        
        // Process the results
        if ($result) {
            foreach ($result as &$row) {
                // Mask the card number for security
                $row['masked_card_number'] = $this->maskCardNumber($row['card_number']);
                
                // Format expiry date
                if (isset($row['expiry_date'])) {
                    $expiryDate = new \DateTime($row['expiry_date']);
                    $row['formatted_expiry_date'] = $expiryDate->format('m/Y');
                }
                
                // Determine card type
                $row['card_type'] = $this->getCardType($row['card_number']);
            }
        }
        
        return $result ?: [];
    }
    
    // Get card by ID
    public function getCardById(int $cardId): ?array {
        $sql = "SELECT * FROM card WHERE card_id = ?";
        
        $this->db->openConnection();
        $result = $this->db->selectPrepared($sql, "i", [$cardId]);
        $this->db->closeConnection();
        
        if (is_array($result) && !empty($result)) {
            $card = $result[0];
            
            // Mask the card number for security
            $card['masked_card_number'] = $this->maskCardNumber($card['card_number']);
            
            // Format expiry date
            if (isset($card['expiry_date'])) {
                $expiryDate = new \DateTime($card['expiry_date']);
                $card['formatted_expiry_date'] = $expiryDate->format('m/Y');
            }
            
            // Determine card type
            $card['card_type'] = $this->getCardType($card['card_number']);
            
            return $card;
        }
        
        return null;
    }
    
    // Create a new card
    public function createCard(array $cardData): bool {
        // Prepare data for insertion
        $dbData = [
            'card_number' => $cardData['card_number'] ?? '',
            'expiry_date' => $cardData['expiry_date'] ?? null,
            'cvv' => $cardData['cvv'] ?? '',
            'card_holder_name' => $cardData['card_holder_name'] ?? '',
            'billing_address' => $cardData['billing_address'] ?? '',
            'traveler_id' => $cardData['traveler_id'] ?? null
        ];
        
        $columns = implode(', ', array_keys($dbData));
        $placeholders = implode(', ', array_fill(0, count($dbData), '?'));
        
        $sql = "INSERT INTO card ($columns) VALUES ($placeholders)";
        
        // Prepare types string
        $types = '';
        foreach ($dbData as $value) {
            if (is_int($value)) {
                $types .= 'i';
            } elseif (is_float($value)) {
                $types .= 'd';
            } else {
                $types .= 's';
            }
        }
        
        $this->db->openConnection();
        $result = $this->db->insert($sql, $types, array_values($dbData));
        $this->db->closeConnection();
        
        return $result;
    }
    
    // Update an existing card
    public function updateCard(int $cardId, array $cardData): bool {
        // Prepare data for update
        $dbData = [];
        
        if (isset($cardData['card_number'])) {
            $dbData['card_number'] = $cardData['card_number'];
        }
        
        if (isset($cardData['expiry_date'])) {
            $dbData['expiry_date'] = $cardData['expiry_date'];
        }
        
        if (isset($cardData['cvv'])) {
            $dbData['cvv'] = $cardData['cvv'];
        }
        
        if (isset($cardData['card_holder_name'])) {
            $dbData['card_holder_name'] = $cardData['card_holder_name'];
        }
        
        if (isset($cardData['billing_address'])) {
            $dbData['billing_address'] = $cardData['billing_address'];
        }
        
        if (empty($dbData)) {
            return false; // No data to update
        }
        
        // Build SET part of the query
        $setParts = [];
        foreach ($dbData as $key => $value) {
            $setParts[] = "$key = ?";
        }
        $setClause = implode(', ', $setParts);
        
        $sql = "UPDATE card SET $setClause WHERE card_id = ?";
        
        // Prepare types string
        $types = '';
        foreach ($dbData as $value) {
            if (is_int($value)) {
                $types .= 'i';
            } elseif (is_float($value)) {
                $types .= 'd';
            } else {
                $types .= 's';
            }
        }
        $types .= 'i'; // For the card_id in WHERE clause
        
        // Add card_id to the parameters array
        $params = array_values($dbData);
        $params[] = $cardId;
        
        $this->db->openConnection();
        $result = $this->db->update($sql, $types, $params);
        $this->db->closeConnection();
        
        return $result;
    }
    
    // Delete a card
    public function deleteCard(int $cardId): bool {
        $sql = "DELETE FROM card WHERE card_id = ?";
        
        $this->db->openConnection();
        $result = $this->db->insert($sql, "i", [$cardId]); // Using insert method for DELETE query
        $this->db->closeConnection();
        
        return $result;
    }
    
    // Helper method to mask card number
    private function maskCardNumber(string $cardNumber): string {
        // Show only the last 4 digits of the card number
        $length = strlen($cardNumber);
        if ($length <= 4) {
            return $cardNumber;
        }
        
        $lastFourDigits = substr($cardNumber, -4);
        $maskedPart = str_repeat('*', $length - 4);
        
        return $maskedPart . $lastFourDigits;
    }
    
    // Helper method to determine card type
    private function getCardType(string $cardNumber): string {
        // Determine card type based on the first digit
        if (empty($cardNumber)) {
            return 'Unknown';
        }
        
        $firstDigit = substr($cardNumber, 0, 1);
        
        switch ($firstDigit) {
            case '4':
                return 'Visa';
            case '5':
                return 'MasterCard';
            case '3':
                // Check for American Express (starts with 34 or 37)
                $firstTwoDigits = substr($cardNumber, 0, 2);
                if ($firstTwoDigits == '34' || $firstTwoDigits == '37') {
                    return 'American Express';
                }
                return 'Unknown';
            case '6':
                return 'Discover';
            default:
                return 'Unknown';
        }
    }
}
