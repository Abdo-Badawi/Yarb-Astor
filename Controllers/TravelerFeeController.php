<?php
require_once '../Models/TravelerFee.php';

class TravelerFeeController {
    public $travelerFeeModel;
    
    public function __construct() {
        $this->travelerFeeModel = new TravelerFee();
    }
    
    /**
     * Get all fees applicable to a traveler
     * 
     * @param int $travelerId The traveler ID
     * @return array Array of fees
     */
    public function getApplicableFees(int $travelerId): array {
        try {
            // Get traveler info to determine applicability
            $travelerInfo = $this->travelerFeeModel->getTravelerInfo($travelerId);
            
            if (!$travelerInfo) {
                error_log("Failed to get traveler info for ID: $travelerId");
                return [];
            }
            
            // Get all active fees
            $allFees = $this->travelerFeeModel->getAllActiveFees();
            
            // Filter fees based on applicability
            $applicableFees = [];
            foreach ($allFees as $fee) {
                // Check if fee is applicable to this traveler
                if ($this->isFeeApplicable($fee, $travelerInfo)) {
                    // Check if fee has already been paid
                    $fee['is_paid'] = $this->travelerFeeModel->hasPaidFee($travelerId, $fee['fee_id']);
                    $applicableFees[] = $fee;
                }
            }
            
            return $applicableFees;
        } catch (Exception $e) {
            error_log("Exception in getApplicableFees: " . $e->getMessage());
            return [];
        }
    }
    
    /**
     * Get all fee transactions for a traveler
     * 
     * @param int $travelerId The traveler ID
     * @return array Array of fee transactions
     */
    public function getFeeTransactions(int $travelerId): array {
        try {
            return $this->travelerFeeModel->getFeeTransactions($travelerId);
        } catch (Exception $e) {
            error_log("Exception in getFeeTransactions: " . $e->getMessage());
            return [];
        }
    }
    
    /**
     * Process a fee payment
     * 
     * @param array $paymentData Payment data
     * @return array Result with success status and message
     */
    public function processPayment(array $paymentData): array {
        try {
            // Validate required fields
            $requiredFields = ['traveler_id', 'fee_id', 'payment_method', 'amount'];
            foreach ($requiredFields as $field) {
                if (!isset($paymentData[$field]) || empty($paymentData[$field])) {
                    return [
                        'success' => false,
                        'message' => "Missing required field: $field"
                    ];
                }
            }
            
            // Validate amount
            if (!is_numeric($paymentData['amount']) || $paymentData['amount'] <= 0) {
                return [
                    'success' => false,
                    'message' => "Invalid payment amount"
                ];
            }
            
            // Validate payment method
            $validMethods = ['credit_card', 'paypal', 'bank_transfer'];
            if (!in_array($paymentData['payment_method'], $validMethods)) {
                return [
                    'success' => false,
                    'message' => "Invalid payment method"
                ];
            }
            
            // Check if fee exists and is active
            $fee = $this->travelerFeeModel->getFeeById($paymentData['fee_id']);
            if (!$fee || $fee['status'] !== 'active') {
                return [
                    'success' => false,
                    'message' => "Fee not found or inactive"
                ];
            }
            
            // Check if fee has already been paid
            if ($this->travelerFeeModel->hasPaidFee($paymentData['traveler_id'], $paymentData['fee_id'])) {
                return [
                    'success' => false,
                    'message' => "This fee has already been paid"
                ];
            }
            
            // Process the payment based on the method
            $paymentResult = $this->processPaymentByMethod($paymentData);
            
            if (!$paymentResult['success']) {
                return $paymentResult;
            }
            
            // Record the transaction
            $transactionData = [
                'traveler_id' => $paymentData['traveler_id'],
                'fee_id' => $paymentData['fee_id'],
                'amount' => $paymentData['amount'],
                'payment_method' => $paymentData['payment_method'],
                'transaction_reference' => $paymentResult['transaction_reference'] ?? null,
                'status' => 'completed',
                'payment_date' => date('Y-m-d H:i:s')
            ];
            
            $transactionId = $this->travelerFeeModel->recordTransaction($transactionData);
            
            if ($transactionId > 0) {
                return [
                    'success' => true,
                    'message' => "Payment processed successfully",
                    'transaction_id' => $transactionId
                ];
            } else {
                return [
                    'success' => false,
                    'message' => "Failed to record transaction"
                ];
            }
        } catch (Exception $e) {
            error_log("Exception in processPayment: " . $e->getMessage());
            return [
                'success' => false,
                'message' => "An error occurred while processing payment: " . $e->getMessage()
            ];
        }
    }
    
    /**
     * Process payment based on the selected method
     * 
     * @param array $paymentData Payment data
     * @return array Result with success status and transaction reference
     */
    private function processPaymentByMethod(array $paymentData): array {
        // In a real application, this would integrate with payment gateways
        // For this demo, we'll simulate successful payments
        
        switch ($paymentData['payment_method']) {
            case 'credit_card':
                // Validate card details if provided
                if (isset($paymentData['card_id']) && !empty($paymentData['card_id'])) {
                    // Use existing card
                    $card = $this->travelerFeeModel->getCardById($paymentData['card_id']);
                    if (!$card) {
                        return [
                            'success' => false,
                            'message' => "Invalid card selected"
                        ];
                    }
                } else if (isset($paymentData['card_number'])) {
                    // Validate new card details
                    if (!isset($paymentData['card_holder']) || !isset($paymentData['expiry_date']) || !isset($paymentData['cvv'])) {
                        return [
                            'success' => false,
                            'message' => "Incomplete card details"
                        ];
                    }
                    
                    // Basic card number validation
                    if (strlen($paymentData['card_number']) < 13 || strlen($paymentData['card_number']) > 19) {
                        return [
                            'success' => false,
                            'message' => "Invalid card number"
                        ];
                    }
                    
                    // Save card if save_card is true
                    if (isset($paymentData['save_card']) && $paymentData['save_card']) {
                        $cardData = [
                            'traveler_id' => $paymentData['traveler_id'],
                            'card_number' => $paymentData['card_number'],
                            'card_holder' => $paymentData['card_holder'],
                            'expiry_date' => $paymentData['expiry_date'],
                            'card_type' => $this->detectCardType($paymentData['card_number'])
                        ];
                        
                        $this->travelerFeeModel->saveCard($cardData);
                    }
                } else {
                    return [
                        'success' => false,
                        'message' => "No card details provided"
                    ];
                }
                
                // Simulate credit card payment processing
                $transactionReference = 'CC-' . time() . '-' . rand(1000, 9999);
                break;
                
            case 'paypal':
                // Simulate PayPal payment processing
                $transactionReference = 'PP-' . time() . '-' . rand(1000, 9999);
                break;
                
            case 'bank_transfer':
                // Simulate bank transfer processing
                $transactionReference = 'BT-' . time() . '-' . rand(1000, 9999);
                break;
                
            default:
                return [
                    'success' => false,
                    'message' => "Unsupported payment method"
                ];
        }
        
        return [
            'success' => true,
            'transaction_reference' => $transactionReference
        ];
    }
    
    /**
     * Detect card type based on card number
     * 
     * @param string $cardNumber Card number
     * @return string Card type
     */
    private function detectCardType(string $cardNumber): string {
        // Remove spaces and dashes
        $cardNumber = str_replace([' ', '-'], '', $cardNumber);
        
        // Basic card type detection based on first digits
        if (preg_match('/^4/', $cardNumber)) {
            return 'visa';
        } else if (preg_match('/^5[1-5]/', $cardNumber)) {
            return 'mastercard';
        } else if (preg_match('/^3[47]/', $cardNumber)) {
            return 'amex';
        } else if (preg_match('/^6(?:011|5)/', $cardNumber)) {
            return 'discover';
        } else {
            return 'unknown';
        }
    }
    
    /**
     * Check if a fee is applicable to a traveler
     * 
     * @param array $fee Fee data
     * @param array $travelerInfo Traveler information
     * @return bool True if applicable, false otherwise
     */
    private function isFeeApplicable(array $fee, array $travelerInfo): bool {
        switch ($fee['applicability']) {
            case 'all':
                return true;
                
            case 'new':
                // Check if traveler is new (registered less than 30 days ago)
                $registrationDate = new DateTime($travelerInfo['registration_date']);
                $now = new DateTime();
                $daysSinceRegistration = $now->diff($registrationDate)->days;
                return $daysSinceRegistration <= 30;
                
            case 'returning':
                // Check if traveler is returning (registered more than 30 days ago)
                $registrationDate = new DateTime($travelerInfo['registration_date']);
                $now = new DateTime();
                $daysSinceRegistration = $now->diff($registrationDate)->days;
                return $daysSinceRegistration > 30;
                
            case 'premium':
                // Check if traveler has premium status
                return isset($travelerInfo['is_premium']) && $travelerInfo['is_premium'] == 1;
                
            default:
                return false;
        }
    }
    
    /**
     * Get all published fees
     * 
     * @return array Array of published fees
     */
    public function getAllPublishedFees(): array {
        try {
            return $this->travelerFeeModel->getAllPublishedFees();
        } catch (Exception $e) {
            error_log("Exception in getAllPublishedFees: " . $e->getMessage());
            return [];
        }
    }
}
?>

