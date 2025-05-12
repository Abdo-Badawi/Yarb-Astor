<?php
require_once '../Models/FeeTransaction.php';

class FeeTransactionController {
    private $model;

    public function __construct() {
        $this->model = new Models\FeeTransaction(null);
    }

    // Get all fee transactions
    public function getAllTransactions(): array {
        return $this->model->getAllTransactions();
    }

    // Get transaction by ID
    public function getTransactionById(int $transactionId): ?array {
        return $this->model->getTransactionById($transactionId);
    }

    // Get transactions by traveler ID
    public function getTransactionsByTravelerId(int $travelerId): array {
        return $this->model->getTransactionsByTravelerId($travelerId);
    }

    // Get transactions by status
    public function getTransactionsByStatus(string $status): array {
        return $this->model->getTransactionsByStatus($status);
    }

    // Create a new fee transaction
    public function createTransaction(array $transactionData): int {
        // Validate required fields
        if (empty($transactionData['traveler_id']) || !isset($transactionData['amount'])) {
            return 0;
        }

        return $this->model->createTransaction($transactionData);
    }

    // Update transaction status
    public function updateTransactionStatus(int $transactionId, string $status): bool {
        // Validate status
        $validStatuses = ['pending', 'completed', 'failed', 'cancelled'];
        if (!in_array($status, $validStatuses)) {
            return false;
        }

        return $this->model->updateTransactionStatus($transactionId, $status);
    }

    // Update transaction reference
    public function updateTransactionReference(int $transactionId, string $reference): bool {
        if (empty($reference)) {
            return false;
        }

        return $this->model->updateTransactionReference($transactionId, $reference);
    }

    // Delete a transaction
    public function deleteTransaction(int $transactionId): bool {
        return $this->model->deleteTransaction($transactionId);
    }

    // Get transactions by fee type
    public function getTransactionsByFeeType(string $feeType): array {
        // Since fee_type doesn't exist in the table, we'll just return all transactions
        // In a real implementation, you would filter by fee_type
        return $this->getAllTransactions();
    }

    // Get transactions by date range
    public function getTransactionsByDateRange(string $startDate, string $endDate): array {
        // This would be implemented in the model
        // For now, return all transactions
        return $this->getAllTransactions();
    }

    // Create a transaction object from array data
    public function createTransactionObject(array $data): Models\FeeTransaction {
        return new Models\FeeTransaction($data);
    }
}