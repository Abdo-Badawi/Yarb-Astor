<?php
require_once 'DBController.php';
require_once '../Models/FeeTransaction.php';

class FeeTransactionController {
    private $db;

    public function __construct() {
        $this->db = new DBController();
    }

    // Get all fee transactions
    public function getAllTransactions(): array {
        $sql = "SELECT ft.*, u.first_name, u.last_name
                FROM fee_transaction ft
                JOIN users u ON ft.traveler_id = u.user_id
                ORDER BY ft.fee_id DESC";

        $this->db->openConnection();
        $result = $this->db->select($sql);
        $this->db->closeConnection();

        // Process the results to add traveler name and dates
        if ($result) {
            foreach ($result as &$row) {
                $row['traveler_name'] = $row['first_name'] . ' ' . $row['last_name'];

                // Map fee_id to transaction_id for compatibility
                $row['transaction_id'] = $row['fee_id'];

                // Use date field for transaction_date if it exists
                if (isset($row['date'])) {
                    $row['transaction_date'] = $row['date'];
                } else {
                    $row['transaction_date'] = date('Y-m-d H:i:s');
                }

                // Add created_at and updated_at for compatibility
                $row['created_at'] = date('Y-m-d H:i:s');
                $row['updated_at'] = date('Y-m-d H:i:s');

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
            }
        }

        return $result ?: [];
    }

    // Get transactions by traveler ID
    public function getTransactionsByTravelerId(int $travelerId): array {
        $sql = "SELECT ft.*, u.first_name, u.last_name
                FROM fee_transaction ft
                JOIN users u ON ft.traveler_id = u.user_id
                WHERE ft.traveler_id = ?
                ORDER BY ft.fee_id DESC";

        $this->db->openConnection();
        $result = $this->db->selectPrepared($sql, "i", [$travelerId]);
        $this->db->closeConnection();

        // Process the results to add traveler name and dates
        if ($result) {
            foreach ($result as &$row) {
                $row['traveler_name'] = $row['first_name'] . ' ' . $row['last_name'];

                // Map fee_id to transaction_id for compatibility
                $row['transaction_id'] = $row['fee_id'];

                // Use date field for transaction_date if it exists
                $row['transaction_date'] = isset($row['date']) ? $row['date'] : date('Y-m-d H:i:s');

                // Add created_at and updated_at for compatibility
                $row['created_at'] = date('Y-m-d H:i:s');
                $row['updated_at'] = date('Y-m-d H:i:s');

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
            }
        }

        return $result ?: [];
    }

    // Get transaction by ID
    public function getTransactionById(int $transactionId): ?array {
        $sql = "SELECT ft.*, u.first_name, u.last_name
                FROM fee_transaction ft
                JOIN users u ON ft.traveler_id = u.user_id
                WHERE ft.fee_id = ?";

        $this->db->openConnection();
        $result = $this->db->selectPrepared($sql, "i", [$transactionId]);
        $this->db->closeConnection();

        if (is_array($result) && !empty($result)) {
            $transaction = $result[0];
            $transaction['traveler_name'] = $transaction['first_name'] . ' ' . $transaction['last_name'];

            // Map fee_id to transaction_id for compatibility
            $transaction['transaction_id'] = $transaction['fee_id'];

            // Use date field for transaction_date if it exists
            $transaction['transaction_date'] = $transaction['date'] ?? date('Y-m-d H:i:s');

            // Add created_at and updated_at for compatibility
            $transaction['created_at'] = date('Y-m-d H:i:s');
            $transaction['updated_at'] = date('Y-m-d H:i:s');

            // Add currency if it doesn't exist
            if (!isset($transaction['currency'])) {
                $transaction['currency'] = 'USD';
            }

            // Add description if it doesn't exist
            if (!isset($transaction['description'])) {
                $transaction['description'] = '';
            }

            // Add fee_type if it doesn't exist
            if (!isset($transaction['fee_type'])) {
                $transaction['fee_type'] = 'Service Fee';
            }

            return $transaction;
        }

        return null;
    }

    // Create a new fee transaction
    public function createTransaction(array $transactionData): bool {
        // Map fields to match the database schema
        $dbData = [
            'traveler_id' => $transactionData['traveler_id'] ?? null,
            'payment_method' => $transactionData['payment_method'] ?? 'credit_card',
            'amount' => $transactionData['amount'] ?? 0,
            'date' => date('Y-m-d'),
            'status' => $transactionData['status'] ?? 'pending',
            'transaction_reference' => $transactionData['transaction_reference'] ?? 'TRX-' . time()
        ];

        // If this is a credit card payment and we have a card_id, save it to a separate table
        if ($dbData['payment_method'] === 'credit_card' && isset($transactionData['card_id']) && $transactionData['card_id'] > 0) {
            // We could create a transaction_card table to link transactions with cards
            // For now, we'll just include the card info in the transaction reference
            $dbData['transaction_reference'] = 'CARD-' . $transactionData['card_id'] . '-' . $dbData['transaction_reference'];
        }

        $columns = implode(', ', array_keys($dbData));
        $placeholders = implode(', ', array_fill(0, count($dbData), '?'));

        $sql = "INSERT INTO fee_transaction ($columns) VALUES ($placeholders)";

        // Prepare types string (s for string, d for float, i for integer)
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

    // Update transaction status
    public function updateTransactionStatus(int $transactionId, string $status): bool {
        $sql = "UPDATE fee_transaction SET status = ? WHERE fee_id = ?";

        $this->db->openConnection();
        $result = $this->db->update($sql, "si", [$status, $transactionId]);
        $this->db->closeConnection();

        return $result;
    }

    // Delete a transaction
    public function deleteTransaction(int $transactionId): bool {
        $sql = "DELETE FROM fee_transaction WHERE fee_id = ?";

        $this->db->openConnection();
        $result = $this->db->insert($sql, "i", [$transactionId]); // Using insert method for DELETE query
        $this->db->closeConnection();

        return $result;
    }

    // Get transactions by status
    public function getTransactionsByStatus(string $status): array {
        $sql = "SELECT ft.*, u.first_name, u.last_name
                FROM fee_transaction ft
                JOIN users u ON ft.traveler_id = u.user_id
                WHERE ft.status = ?
                ORDER BY ft.fee_id DESC";

        $this->db->openConnection();
        $result = $this->db->selectPrepared($sql, "s", [$status]);
        $this->db->closeConnection();

        // Process the results to add traveler name and dates
        if ($result) {
            foreach ($result as &$row) {
                $row['traveler_name'] = $row['first_name'] . ' ' . $row['last_name'];

                // Map fee_id to transaction_id for compatibility
                $row['transaction_id'] = $row['fee_id'];

                // Use date field for transaction_date if it exists
                $row['transaction_date'] = $row['date'] ?? date('Y-m-d H:i:s');

                // Add created_at and updated_at for compatibility
                $row['created_at'] = date('Y-m-d H:i:s');
                $row['updated_at'] = date('Y-m-d H:i:s');

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
            }
        }

        return $result ?: [];
    }

    // Get transactions by fee type - Note: fee_type doesn't exist in the table
    public function getTransactionsByFeeType(string $feeType): array {
        // Since fee_type doesn't exist in the table, we'll just return all transactions
        return $this->getAllTransactions();
    }

    // Get transactions by date range
    public function getTransactionsByDateRange(string $startDate, string $endDate): array {
        $sql = "SELECT ft.*, u.first_name, u.last_name
                FROM fee_transaction ft
                JOIN users u ON ft.traveler_id = u.user_id
                WHERE ft.date BETWEEN ? AND ?
                ORDER BY ft.fee_id DESC";

        $this->db->openConnection();
        $result = $this->db->selectPrepared($sql, "ss", [$startDate, $endDate]);
        $this->db->closeConnection();

        // Process the results to add traveler name and dates
        if ($result) {
            foreach ($result as &$row) {
                $row['traveler_name'] = $row['first_name'] . ' ' . $row['last_name'];

                // Map fee_id to transaction_id for compatibility
                $row['transaction_id'] = $row['fee_id'];

                // Use date field for transaction_date if it exists
                $row['transaction_date'] = $row['date'] ?? date('Y-m-d H:i:s');

                // Add created_at and updated_at for compatibility
                $row['created_at'] = date('Y-m-d H:i:s');
                $row['updated_at'] = date('Y-m-d H:i:s');

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
            }
        }

        return $result ?: [];
    }
}
