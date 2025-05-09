<?php
namespace Models;

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

    // Card information
    public int $cardId;
    public string $cardNumber;
    public string $cardHolderName;

    public function __construct($data) {
        $this->transactionId = $data['transaction_id'] ?? 0;
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
        return true;
    }

    public function markAsFailed(): bool {
        $this->status = 'failed';
        $this->updatedAt = new \DateTime();
        return true;
    }
}