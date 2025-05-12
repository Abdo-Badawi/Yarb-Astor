<?php
namespace Models;

class Fee {
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

    public function __construct($data) {
        $this->feeId = $data['fee_id'] ?? 0;
        $this->feeName = $data['fee_name'] ?? '';
        $this->feeType = $data['fee_type'] ?? 'fixed';
        $this->amount = $data['amount'] ?? 0.0;
        $this->currency = $data['currency'] ?? 'USD';
        $this->description = $data['description'] ?? '';
        $this->applicability = $data['applicability'] ?? 'all';
        $this->isMandatory = isset($data['is_mandatory']) ? (bool)$data['is_mandatory'] : false;
        $this->status = $data['status'] ?? 'active';
        $this->createdBy = $data['created_by'] ?? 0;
        $this->createdByName = $data['created_by_name'] ?? '';
        $this->createdAt = isset($data['created_at']) ? new \DateTime($data['created_at']) : new \DateTime();
        $this->updatedAt = isset($data['updated_at']) && $data['updated_at'] ? new \DateTime($data['updated_at']) : null;
    }

    public function toArray(): array {
        return [
            'fee_id' => $this->feeId,
            'fee_name' => $this->feeName,
            'fee_type' => $this->feeType,
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
        if ($this->feeType === 'percentage') {
            return $this->amount . '%';
        }
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

    public function getFeeTypeLabel(): string {
        switch ($this->feeType) {
            case 'fixed':
                return 'Fixed Amount';
            case 'percentage':
                return 'Percentage';
            case 'tiered':
                return 'Tiered';
            default:
                return 'Unknown';
        }
    }
}
