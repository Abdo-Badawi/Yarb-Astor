<?php
namespace Models;
require_once '../Models/Database.php';

class FeeTransaction {
    public int $transactionId;
    public int $travelerId;
    public string $travelerName;
    public string $feeType;
    public float $amount;
    public string $currency;
    public string $paymentMethod;
    public string $status;
    public string $description;
    public \DateTime $transactionDate;
    public \DateTime $createdAt;
    public \DateTime $updatedAt;
    private $db;

    // Card information
    public int $cardId;
    public string $cardNumber;
    public string $cardHolderName;

    public function __construct($data = null) {
        $this->db = new \Database();
        
        if ($data) {
            $this->transactionId = $data['transaction_id'] ?? $data['fee_id'] ?? 0;
            $this->travelerId = $data['traveler_id'] ?? 0;
            $this->travelerName = $data['traveler_name'] ?? '';
            $this->feeType = $data['fee_type'] ?? '';
            $this->amount = $data['amount'] ?? 0.0;
            $this->currency = $data['currency'] ?? 'USD';
            $this->paymentMethod = $data['payment_method'] ?? '';
            $this->status = $data['status'] ?? 'pending';
            $this->description = $data['description'] ?? '';
            $this->transactionDate = isset($data['transaction_date']) ? new \DateTime($data['transaction_date']) : new \DateTime();
            $this->createdAt = isset($data['created_at']) ? new \DateTime($data['created_at']) : new \DateTime();
            $this->updatedAt = isset($data['updated_at']) ? new \DateTime($data['updated_at']) : new \DateTime();

            // Card information
            $this->cardId = $data['card_id'] ?? 0;
            $this->cardNumber = $data['card_number'] ?? '';
            $this->cardHolderName = $data['card_holder_name'] ?? '';
        }
    }

    // Database operations
    public function getAllTransactions(): array {
        $sql = "SELECT ft.*, u.first_name, u.last_name
                FROM fee_transaction ft
                JOIN users u ON ft.traveler_id = u.user_id
                ORDER BY ft.fee_id DESC";

        $this->db->openConnection();
        $result = $this->db->select($sql);
        $this->db->closeConnection();

        return $this->formatTransactionResults($result);
    }

    public function getTransactionById(int $transactionId): ?array {
        $sql = "SELECT ft.*, u.first_name, u.last_name
                FROM fee_transaction ft
                JOIN users u ON ft.traveler_id = u.user_id
                WHERE ft.fee_id = ?";

        $this->db->openConnection();
        $result = $this->db->selectPrepared($sql, "i", [$transactionId]);
        $this->db->closeConnection();

        if (is_array($result) && !empty($result)) {
            $transactions = $this->formatTransactionResults($result);
            return $transactions[0];
        }
        
        return null;
    }

    public function getTransactionsByTravelerId(int $travelerId): array {
        $sql = "SELECT ft.*, u.first_name, u.last_name
                FROM fee_transaction ft
                JOIN users u ON ft.traveler_id = u.user_id
                WHERE ft.traveler_id = ?
                ORDER BY ft.fee_id DESC";

        $this->db->openConnection();
        $result = $this->db->selectPrepared($sql, "i", [$travelerId]);
        $this->db->closeConnection();

        return $this->formatTransactionResults($result);
    }

    public function getTransactionsByStatus(string $status): array {
        $sql = "SELECT ft.*, u.first_name, u.last_name
                FROM fee_transaction ft
                JOIN users u ON ft.traveler_id = u.user_id
                WHERE ft.status = ?
                ORDER BY ft.fee_id DESC";

        $this->db->openConnection();
        $result = $this->db->selectPrepared($sql, "s", [$status]);
        $this->db->closeConnection();

        return $this->formatTransactionResults($result);
    }

    public function createTransaction(array $transactionData): int {
        // Map fields to match the database schema
        $dbData = [
            'traveler_id' => $transactionData['traveler_id'] ?? null,
            'payment_method' => $transactionData['payment_method'] ?? 'credit_card',
            'amount' => $transactionData['amount'] ?? 0,
            'date' => date('Y-m-d'),
            'status' => $transactionData['status'] ?? 'pending',
            'transaction_reference' => $transactionData['transaction_reference'] ?? 'TRX-' . time()
        ];

        $columns = implode(', ', array_keys($dbData));
        $placeholders = implode(', ', array_fill(0, count($dbData), '?'));

        $sql = "INSERT INTO fee_transaction ($columns) VALUES ($placeholders)";

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

    public function updateTransactionStatus(int $transactionId, string $status): bool {
        $sql = "UPDATE fee_transaction SET status = ? WHERE fee_id = ?";

        $this->db->openConnection();
        $result = $this->db->update($sql, "si", [$status, $transactionId]);
        $this->db->closeConnection();

        return $result;
    }

    public function updateTransactionReference(int $transactionId, string $reference): bool {
        $sql = "UPDATE fee_transaction SET transaction_reference = ? WHERE fee_id = ?";

        $this->db->openConnection();
        $result = $this->db->update($sql, "si", [$reference, $transactionId]);
        $this->db->closeConnection();

        return $result;
    }

    public function deleteTransaction(int $transactionId): bool {
        $sql = "DELETE FROM fee_transaction WHERE fee_id = ?";

        $this->db->openConnection();
        $result = $this->db->delete($sql, "i", [$transactionId]);
        $this->db->closeConnection();

        return $result;
    }

    // Helper methods
    private function formatTransactionResults($result): array {
        if (!$result) {
            return [];
        }

        $formattedResults = [];
        foreach ($result as $row) {
            // Add traveler name
            $row['traveler_name'] = $row['first_name'] . ' ' . $row['last_name'];

            // Map fee_id to transaction_id for compatibility
            $row['transaction_id'] = $row['fee_id'];

            // Use date field for transaction_date if it exists
            $row['transaction_date'] = $row['date'] ?? date('Y-m-d H:i:s');

            // Add created_at and updated_at for compatibility
            $row['created_at'] = $row['created_at'] ?? date('Y-m-d H:i:s');
            $row['updated_at'] = $row['updated_at'] ?? date('Y-m-d H:i:s');

            // Add currency if it doesn't exist
            if (!isset($row['currency'])) {
                $row['currency'] = 'USD';
            }

            // Add description if it doesn't exist
            if (!isset($row['description'])) {
                $row['description'] = '';
            }

            // Add fee_type if it doesn't exist
            if (!isset($row['fee_type'])) {
                $row['fee_type'] = 'Service Fee';
            }

            $formattedResults[] = $row;
        }

        return $formattedResults;
    }

    public function toArray(): array {
        return [
            'transaction_id' => $this->transactionId,
            'traveler_id' => $this->travelerId,
            'traveler_name' => $this->travelerName,
            'fee_type' => $this->feeType,
            'amount' => $this->amount,
            'currency' => $this->currency,
            'payment_method' => $this->paymentMethod,
            'status' => $this->status,
            'description' => $this->description,
            'transaction_date' => $this->transactionDate->format('Y-m-d H:i:s'),
            'created_at' => $this->createdAt->format('Y-m-d H:i:s'),
            'updated_at' => $this->updatedAt->format('Y-m-d H:i:s'),
            // Card information
            'card_id' => $this->cardId,
            'card_number' => $this->cardNumber,
            'card_holder_name' => $this->cardHolderName
        ];
    }

    public function getFormattedAmount(): string {
        return $this->currency . ' ' . number_format($this->amount, 2);
    }

    public function getFormattedDate(): string {
        return $this->transactionDate->format('M d, Y');
    }

    public function getStatusBadgeClass(): string {
        switch ($this->status) {
            case 'completed':
                return 'badge-success';
            case 'pending':
                return 'badge-warning';
            case 'failed':
                return 'badge-danger';
            default:
                return 'badge-secondary';
        }
    }

    public function markAsCompleted(): bool {
        $this->status = 'completed';
        $this->updatedAt = new \DateTime();
        return $this->updateTransactionStatus($this->transactionId, 'completed');
    }

    public function markAsFailed(): bool {
        $this->status = 'failed';
        $this->updatedAt = new \DateTime();
        return $this->updateTransactionStatus($this->transactionId, 'failed');
    }
}
