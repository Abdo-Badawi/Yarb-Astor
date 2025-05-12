<?php
namespace Models;
require_once '../Models/Database.php';

class Fee {
    public int $feeId;
    public string $feeName;
    public float $amount;
    public string $currency;
    public ?string $description;
    public string $applicability;
    public bool $isMandatory;
    public string $status;
    public int $createdBy;
    public string $createdByName;
    public \DateTime $createdAt;
    public ?\DateTime $updatedAt;
    private $db;

    public function __construct($data = null) {
        $this->db = new \Database();
        
        if ($data) {
            $this->feeId = $data['fee_id'] ?? 0;
            $this->feeName = $data['fee_name'] ?? '';
            $this->amount = $data['amount'] ?? 0.0;
            $this->currency = $data['currency'] ?? 'USD';
            $this->description = $data['description'] ?? null;
            $this->applicability = $data['applicability'] ?? 'all';
            $this->isMandatory = isset($data['is_mandatory']) ? (bool)$data['is_mandatory'] : false;
            $this->status = $data['status'] ?? 'active';
            $this->createdBy = $data['created_by'] ?? 0;
            $this->createdByName = $data['created_by_name'] ?? '';
            $this->createdAt = isset($data['created_at']) ? new \DateTime($data['created_at']) : new \DateTime();
            $this->updatedAt = isset($data['updated_at']) && $data['updated_at'] ? new \DateTime($data['updated_at']) : null;
        }
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
            'amount' => $feeData['amount'] ?? 0,
            'currency' => $feeData['currency'] ?? 'USD',
            'description' => $feeData['description'] ?? null,
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
    
    // Get fees by applicability
    public function getFeesByApplicability(string $applicability): array {
        $sql = "SELECT f.*, u.first_name, u.last_name, CONCAT(u.first_name, ' ', u.last_name) as created_by_name
                FROM fee f
                JOIN users u ON f.created_by = u.user_id
                WHERE f.applicability = ? AND f.status = 'active'
                ORDER BY f.fee_id DESC";

        $this->db->openConnection();
        $result = $this->db->selectPrepared($sql, "s", [$applicability]);
        $this->db->closeConnection();

        return $result ?: [];
    }
    
    // Get mandatory fees
    public function getMandatoryFees(): array {
        $sql = "SELECT f.*, u.first_name, u.last_name, CONCAT(u.first_name, ' ', u.last_name) as created_by_name
                FROM fee f
                JOIN users u ON f.created_by = u.user_id
                WHERE f.is_mandatory = 1 AND f.status = 'active'
                ORDER BY f.fee_id DESC";

        $this->db->openConnection();
        $result = $this->db->select($sql);
        $this->db->closeConnection();

        return $result ?: [];
    }
    
    // Convert object to array
    public function toArray(): array {
        return [
            'fee_id' => $this->feeId,
            'fee_name' => $this->feeName,
            'amount' => $this->amount,
            'currency' => $this->currency,
            'description' => $this->description,
            'applicability' => $this->applicability,
            'is_mandatory' => $this->isMandatory ? 1 : 0,
            'status' => $this->status,
            'created_by' => $this->createdBy,
            'created_by_name' => $this->createdByName,
            'created_at' => $this->createdAt->format('Y-m-d H:i:s'),
            'updated_at' => $this->updatedAt ? $this->updatedAt->format('Y-m-d H:i:s') : null
        ];
    }

    public function getFormattedAmount(): string {
        return $this->currency . ' ' . number_format($this->amount, 2);
    }

    public function getFormattedDate(): string {
        return $this->createdAt->format('M d, Y');
    }

    public function getStatusBadgeClass(): string {
        return $this->status === 'active' ? 'badge-success' : 'badge-secondary';
    }

    public function getApplicabilityLabel(): string {
        switch ($this->applicability) {
            case 'all':
                return 'All Travelers';
            case 'new':
                return 'New Travelers Only';
            case 'returning':
                return 'Returning Travelers Only';
            case 'premium':
                return 'Premium Members Only';
            default:
                return 'Unknown';
        }
    }
}


