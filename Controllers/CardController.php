<?php
namespace Models;

// No dependencies
class Card {
    public string $CardNumber;
    public string $ExpiryDate;
    public string $CVV;
    public string $CardHolderName;
    public string $BillingAddress;
    public int $TravelerID;

    public function validateCard(): bool {}
    public function charge(): bool {}
    public function refund(): bool {}
} 