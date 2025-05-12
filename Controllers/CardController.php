<?php
require_once '../Models/Card.php';

class CardController {
    private $model;
    
    public function __construct() {
        $this->model = new Models\Card(null);
    }
    
    // Get all cards
    public function getAllCards(): array {
        return $this->model->getAllCards();
    }
    
    // Get card by ID
    public function getCardById(int $cardId): ?array {
        return $this->model->getCardById($cardId);
    }
    
    // Get cards by traveler ID
    public function getCardsByTravelerId(int $travelerId): array {
        return $this->model->getCardsByTravelerId($travelerId);
    }
    
    // Create a new card
    public function createCard(array $cardData): int {
        // Validate required fields
        if (empty($cardData['card_number']) || empty($cardData['expiry_date']) || 
            empty($cardData['cvv']) || empty($cardData['card_holder_name']) || 
            empty($cardData['traveler_id'])) {
            return 0;
        }
        
        // Validate card number format (basic check)
        if (!$this->validateCardNumberFormat($cardData['card_number'])) {
            return 0;
        }
        
        // Validate expiry date (must be in the future)
        if (!$this->validateExpiryDate($cardData['expiry_date'])) {
            return 0;
        }
        
        // Validate CVV (must be 3-4 digits)
        if (!$this->validateCVV($cardData['cvv'])) {
            return 0;
        }
        
        return $this->model->createCard($cardData);
    }
    
    // Update an existing card
    public function updateCard(int $cardId, array $cardData): bool {
        if ($cardId <= 0 || empty($cardData)) {
            return false;
        }
        
        // Validate card number format if provided
        if (isset($cardData['card_number']) && !$this->validateCardNumberFormat($cardData['card_number'])) {
            return false;
        }
        
        // Validate expiry date if provided
        if (isset($cardData['expiry_date']) && !$this->validateExpiryDate($cardData['expiry_date'])) {
            return false;
        }
        
        // Validate CVV if provided
        if (isset($cardData['cvv']) && !$this->validateCVV($cardData['cvv'])) {
            return false;
        }
        
        return $this->model->updateCard($cardId, $cardData);
    }
    
    // Delete a card
    public function deleteCard(int $cardId): bool {
        if ($cardId <= 0) {
            return false;
        }
        
        return $this->model->deleteCard($cardId);
    }
    
    // Helper method to validate card number format
    private function validateCardNumberFormat(string $cardNumber): bool {
        // Remove spaces and dashes
        $cardNumber = str_replace([' ', '-'], '', $cardNumber);
        
        // Check if card number contains only digits
        if (!ctype_digit($cardNumber)) {
            return false;
        }
        
        // Check if card number length is valid (13-19 digits)
        if (strlen($cardNumber) < 13 || strlen($cardNumber) > 19) {
            return false;
        }
        
        return true;
    }
    
    // Helper method to validate expiry date
    private function validateExpiryDate(string $expiryDate): bool {
        try {
            $expiry = new \DateTime($expiryDate);
            $now = new \DateTime();
            
            // Set now to the first day of the current month
            $now->setDate($now->format('Y'), $now->format('m'), 1);
            
            // Set expiry to the first day of the expiry month
            $expiry->setDate($expiry->format('Y'), $expiry->format('m'), 1);
            
            // Check if expiry date is in the future
            return ($expiry >= $now);
        } catch (\Exception $e) {
            return false;
        }
    }
    
    // Helper method to validate CVV
    private function validateCVV(string $cvv): bool {
        // Check if CVV contains only digits
        if (!ctype_digit($cvv)) {
            return false;
        }
        
        // Check if CVV length is valid (3-4 digits)
        if (strlen($cvv) < 3 || strlen($cvv) > 4) {
            return false;
        }
        
        return true;
    }
    
    // Create a card object from array data
    public function createCardObject(array $data): Models\Card {
        return new Models\Card($data);
    }
}
 

