<?php
namespace Models;
require_once '../Models/Database.php';

class Card {
    public int $cardId;
    public string $cardNumber;
    public string $expiryDate;
    public string $cvv;
    public string $cardHolderName;
    public string $billingAddress;
    public int $travelerId;
    private $db;

    public function __construct($data = null) {
        $this->db = new \Database();
        
        if ($data) {
            $this->cardId = $data['card_id'] ?? 0;
            $this->cardNumber = $data['card_number'] ?? '';
            $this->expiryDate = $data['expiry_date'] ?? '';
            $this->cvv = $data['cvv'] ?? '';
            $this->cardHolderName = $data['card_holder_name'] ?? '';
            $this->billingAddress = $data['billing_address'] ?? '';
            $this->travelerId = $data['traveler_id'] ?? 0;
        }
    }

    // Get all cards
    public function getAllCards(): array {
        $sql = "SELECT * FROM card ORDER BY card_id DESC";
        
        $this->db->openConnection();
        $result = $this->db->select($sql);
        $this->db->closeConnection();
        
        return $this->formatCardResults($result);
    }
    
    // Get card by ID
    public function getCardById(int $cardId): ?array {
        $sql = "SELECT * FROM card WHERE card_id = ?";
        
        $this->db->openConnection();
        $result = $this->db->selectPrepared($sql, "i", [$cardId]);
        $this->db->closeConnection();
        
        if (is_array($result) && !empty($result)) {
            $cards = $this->formatCardResults($result);
            return $cards[0];
        }
        
        return null;
    }
    
    // Get cards by traveler ID
    public function getCardsByTravelerId(int $travelerId): array {
        $sql = "SELECT * FROM card WHERE traveler_id = ? ORDER BY card_id DESC";
        
        $this->db->openConnection();
        $result = $this->db->selectPrepared($sql, "i", [$travelerId]);
        $this->db->closeConnection();
        
        return $this->formatCardResults($result);
    }
    
    // Create a new card
    public function createCard(array $cardData): int {
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
        $insertId = $result ? $this->db->getInsertId() : 0;
        $this->db->closeConnection();
        
        return $insertId;
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
        $result = $this->db->delete($sql, "i", [$cardId]);
        $this->db->closeConnection();
        
        return $result;
    }
    
    // Helper method to mask card number
    public function maskCardNumber(string $cardNumber): string {
        // Show only the last 4 digits of the card number
        $length = strlen($cardNumber);
        if ($length <= 4) {
            return $cardNumber;
        }
        
        $lastFourDigits = substr($cardNumber, -4);
        $maskedPart = str_repeat('*', $length - 4);
        
        return $maskedPart . $lastFourDigits;
    }
    
    // Helper method to format card results
    private function formatCardResults($result): array {
        if (!$result) {
            return [];
        }
        
        $formattedResults = [];
        foreach ($result as $row) {
            // Mask sensitive data for security
            $row['masked_card_number'] = $this->maskCardNumber($row['card_number']);
            
            // Format expiry date for display (MM/YY)
            if (isset($row['expiry_date']) && !empty($row['expiry_date'])) {
                $expiryDate = new \DateTime($row['expiry_date']);
                $row['formatted_expiry'] = $expiryDate->format('m/y');
            } else {
                $row['formatted_expiry'] = 'N/A';
            }
            
            $formattedResults[] = $row;
        }
        
        return $formattedResults;
    }
    
    // Convert object to array
    public function toArray(): array {
        return [
            'card_id' => $this->cardId,
            'card_number' => $this->cardNumber,
            'masked_card_number' => $this->maskCardNumber($this->cardNumber),
            'expiry_date' => $this->expiryDate,
            'formatted_expiry' => (new \DateTime($this->expiryDate))->format('m/y'),
            'cvv' => $this->cvv,
            'card_holder_name' => $this->cardHolderName,
            'billing_address' => $this->billingAddress,
            'traveler_id' => $this->travelerId
        ];
    }
}

