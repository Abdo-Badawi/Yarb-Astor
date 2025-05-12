<?php
require_once '../Models/Fee.php';

class FeeController {
    private $model;

    public function __construct() {
        $this->model = new Models\Fee(null);
    }

    // Get all fees
    public function getAllFees(): array {
        return $this->model->getAllFees();
    }

    // Get fee by ID
    public function getFeeById(int $feeId): ?array {
        if ($feeId <= 0) {
            return null;
        }
        
        return $this->model->getFeeById($feeId);
    }

    // Create a new fee
    public function createFee(array $feeData): int {
        // Validate required fields
        if (empty($feeData['fee_name']) || !isset($feeData['amount'])) {
            return 0;
        }
        
        // Validate amount
        if (isset($feeData['amount']) && (!is_numeric($feeData['amount']) || $feeData['amount'] < 0)) {
            return 0;
        }
        
        // Validate applicability
        if (isset($feeData['applicability']) && !in_array($feeData['applicability'], ['all', 'new', 'returning', 'premium'])) {
            return 0;
        }
        
        // Validate status
        if (isset($feeData['status']) && !in_array($feeData['status'], ['active', 'inactive'])) {
            return 0;
        }
        
        return $this->model->createFee($feeData);
    }

    // Update a fee
    public function updateFee(int $feeId, array $feeData): bool {
        if ($feeId <= 0 || empty($feeData)) {
            return false;
        }
        
        // Validate amount if provided
        if (isset($feeData['amount']) && (!is_numeric($feeData['amount']) || $feeData['amount'] < 0)) {
            return false;
        }
        
        // Validate applicability if provided
        if (isset($feeData['applicability']) && !in_array($feeData['applicability'], ['all', 'new', 'returning', 'premium'])) {
            return false;
        }
        
        // Validate status if provided
        if (isset($feeData['status']) && !in_array($feeData['status'], ['active', 'inactive'])) {
            return false;
        }
        
        return $this->model->updateFee($feeId, $feeData);
    }

    // Delete a fee
    public function deleteFee(int $feeId): bool {
        if ($feeId <= 0) {
            return false;
        }
        
        return $this->model->deleteFee($feeId);
    }

    // Assign fee to traveler
    public function assignFeeToTraveler(int $feeId, int $travelerId, ?string $dueDate = null): int {
        if ($feeId <= 0 || $travelerId <= 0) {
            return 0;
        }
        
        // Validate due date format if provided
        if ($dueDate !== null) {
            $dateTime = \DateTime::createFromFormat('Y-m-d', $dueDate);
            if (!$dateTime || $dateTime->format('Y-m-d') !== $dueDate) {
                return 0;
            }
        }
        
        return $this->model->assignFeeToTraveler($feeId, $travelerId, $dueDate);
    }
    
    // Get fees by traveler ID
    public function getFeesByTravelerId(int $travelerId): array {
        if ($travelerId <= 0) {
            return [];
        }
        
        return $this->model->getFeesByTravelerId($travelerId);
    }
    
    // Get fees by status
    public function getFeesByStatus(string $status): array {
        if (!in_array($status, ['active', 'inactive'])) {
            return [];
        }
        
        return $this->model->getFeesByStatus($status);
    }
    
    // Get fees by applicability
    public function getFeesByApplicability(string $applicability): array {
        if (!in_array($applicability, ['all', 'new', 'returning', 'premium'])) {
            return [];
        }
        
        return $this->model->getFeesByApplicability($applicability);
    }
    
    // Get mandatory fees
    public function getMandatoryFees(): array {
        return $this->model->getMandatoryFees();
    }
    
    // Calculate total fees for a traveler
    public function calculateTotalFeesForTraveler(int $travelerId): float {
        $fees = $this->getFeesByTravelerId($travelerId);
        $total = 0;
        
        foreach ($fees as $fee) {
            if ($fee['assignment_status'] === 'pending') {
                $total += $fee['amount'];
            }
        }
        
        return $total;
    }
    
    // Check if a fee is applicable to a traveler
    public function isFeeApplicableToTraveler(int $feeId, array $travelerData): bool {
        $fee = $this->getFeeById($feeId);
        if (!$fee || $fee['status'] !== 'active') {
            return false;
        }
        
        // Check applicability based on traveler data
        switch ($fee['applicability']) {
            case 'all':
                return true;
            case 'new':
                // Assuming traveler data has a registration_date field
                if (isset($travelerData['registration_date'])) {
                    $regDate = new \DateTime($travelerData['registration_date']);
                    $now = new \DateTime();
                    $diff = $now->diff($regDate);
                    return $diff->days <= 30; // Consider "new" if registered within last 30 days
                }
                return false;
            case 'returning':
                // Assuming traveler data has a registration_date field and booking_count
                if (isset($travelerData['registration_date']) && isset($travelerData['booking_count'])) {
                    $regDate = new \DateTime($travelerData['registration_date']);
                    $now = new \DateTime();
                    $diff = $now->diff($regDate);
                    return
                        $diff->days > 30 && // Not a new traveler
                        $travelerData['booking_count'] > 0; // Has at least one booking
                }
                return false;
            case 'premium':
                // Assuming traveler data has a premium_status field
                return isset($travelerData['premium_status']) && $travelerData['premium_status'] === true;
            default:
                return false;
        }
    }
    
    // Create a fee object from array data
    public function createFeeObject(array $data): Models\Fee {
        return new Models\Fee($data);
    }
}
