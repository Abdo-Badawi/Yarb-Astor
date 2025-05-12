<?php
require_once '../Models/Database.php';

class FeeController {
    public int $feeId;
    public string $feeName;
    public string $feeType;
    public float $amount;
    public string $currency;
    public string $description;
    public string $applicability;
    public bool $isMandatory;
    public string $status;
    public int $createdBy;
    public string $createdByName;
    public \DateTime $createdAt;
    public ?\DateTime $updatedAt;
    private $db;

    public function __construct() {
        $this->db = new Database();
    }

    // Get all fees
    public function getAllFees(): array {
        $sql = "SELECT f.*, u.first_name, u.last_name, CONCAT(u.first_name, ' ', u.last_name) as created_by_name
                FROM fee f
                JOIN users u ON f.created_by = u.user_id
                ORDER BY f.fee_id DESC";

        $this->db->openConnection();
        $result = $this->db->select($sql);
        $this->db->closeConnection();

        return $result ?: [];
    }

    // Get fee by ID
    public function getFeeById(int $feeId): ?array {
        $sql = "SELECT f.*, u.first_name, u.last_name, CONCAT(u.first_name, ' ', u.last_name) as created_by_name
                FROM fee f
                JOIN users u ON f.created_by = u.user_id
                WHERE f.fee_id = ?";

        $this->db->openConnection();
        $result = $this->db->selectPrepared($sql, "i", [$feeId]);
        $this->db->closeConnection();

        if (is_array($result) && !empty($result)) {
            return $result[0];
        }

        return null;
    }

    // Create a new fee
    public function createFee(array $feeData): int {
        // Map fields to match the database schema
        $dbData = [
            'fee_name' => $feeData['fee_name'] ?? '',
            'fee_type' => $feeData['fee_type'] ?? 'fixed',
            'amount' => $feeData['amount'] ?? 0,
            'currency' => $feeData['currency'] ?? 'USD',
            'description' => $feeData['description'] ?? '',
            'applicability' => $feeData['applicability'] ?? 'all',
            'is_mandatory' => isset($feeData['is_mandatory']) ? (int)$feeData['is_mandatory'] : 0,
            'status' => $feeData['status'] ?? 'active',
            'created_by' => $feeData['created_by'] ?? 1
        ];

        $columns = implode(', ', array_keys($dbData));
        $placeholders = implode(', ', array_fill(0, count($dbData), '?'));

        $sql = "INSERT INTO fee ($columns) VALUES ($placeholders)";

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
        $insertId = $this->db->getInsertId();
        $this->db->closeConnection();

        return $insertId;
    }

    // Update a fee
    public function updateFee(int $feeId, array $feeData): bool {
        // Map fields to match the database schema
        $dbData = [];
        
        if (isset($feeData['fee_name'])) $dbData['fee_name'] = $feeData['fee_name'];
        if (isset($feeData['fee_type'])) $dbData['fee_type'] = $feeData['fee_type'];
        if (isset($feeData['amount'])) $dbData['amount'] = $feeData['amount'];
        if (isset($feeData['currency'])) $dbData['currency'] = $feeData['currency'];
        if (isset($feeData['description'])) $dbData['description'] = $feeData['description'];
        if (isset($feeData['applicability'])) $dbData['applicability'] = $feeData['applicability'];
        if (isset($feeData['is_mandatory'])) $dbData['is_mandatory'] = (int)$feeData['is_mandatory'];
        if (isset($feeData['status'])) $dbData['status'] = $feeData['status'];
        
        if (empty($dbData)) {
            return false;
        }

        // Build SET clause
        $setClauses = [];
        $params = [];
        $types = '';

        foreach ($dbData as $key => $value) {
            $setClauses[] = "$key = ?";
            $params[] = $value;
            
            if (is_int($value)) {
                $types .= 'i';
            } elseif (is_float($value)) {
                $types .= 'd';
            } else {
                $types .= 's';
            }
        }

        // Add fee_id to params
        $params[] = $feeId;
        $types .= 'i';

        $sql = "UPDATE fee SET " . implode(', ', $setClauses) . " WHERE fee_id = ?";

        $this->db->openConnection();
        $result = $this->db->update($sql, $types, $params);
        $this->db->closeConnection();

        return $result;
    }

    // Delete a fee
    public function deleteFee(int $feeId): bool {
        $sql = "DELETE FROM fee WHERE fee_id = ?";

        $this->db->openConnection();
        $result = $this->db->delete($sql, "i", [$feeId]);
        $this->db->closeConnection();

        return $result;
    }

    // Assign fee to traveler
    public function assignFeeToTraveler(int $feeId, int $travelerId, ?string $dueDate = null): int {
        $dbData = [
            'fee_id' => $feeId,
            'traveler_id' => $travelerId,
            'status' => 'pending',
            'due_date' => $dueDate
        ];

        $columns = implode(', ', array_keys($dbData));
        $placeholders = implode(', ', array_fill(0, count($dbData), '?'));

        $sql = "INSERT INTO fee_assignment ($columns) VALUES ($placeholders)";

        // Prepare types string
        $types = 'iis' . ($dueDate ? 's' : 's'); // fee_id, traveler_id, status, due_date

        $this->db->openConnection();
        $result = $this->db->insert($sql, $types, array_values($dbData));
        $insertId = $this->db->getInsertId();
        $this->db->closeConnection();

        return $insertId;
    }

    // Get fees assigned to a traveler
    public function getFeesByTravelerId(int $travelerId): array {
        $sql = "SELECT f.*, fa.status as assignment_status, fa.assignment_id, fa.due_date, fa.assigned_at
                FROM fee f
                JOIN fee_assignment fa ON f.fee_id = fa.fee_id
                WHERE fa.traveler_id = ?
                ORDER BY fa.assigned_at DESC";

        $this->db->openConnection();
        $result = $this->db->selectPrepared($sql, "i", [$travelerId]);
        $this->db->closeConnection();

        return $result ?: [];
    }

    // Get fees by status
    public function getFeesByStatus(string $status): array {
        $sql = "SELECT f.*, u.first_name, u.last_name, CONCAT(u.first_name, ' ', u.last_name) as created_by_name
                FROM fee f
                JOIN users u ON f.created_by = u.user_id
                WHERE f.status = ?
                ORDER BY f.fee_id DESC";

        $this->db->openConnection();
        $result = $this->db->selectPrepared($sql, "s", [$status]);
        $this->db->closeConnection();

        return $result ?: [];
    }
}
