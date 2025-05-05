<?php
namespace Models;

// No dependencies
class FeeTransaction {
    public string $FeeID;
    public string $TravelerID;
    public float $Amount;
    public \DateTime $Date;
    public string $Status;

    public function markAsPaid(): bool {}
    public function refund(): bool {}
} 