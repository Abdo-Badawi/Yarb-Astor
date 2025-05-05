<?php
namespace Models;

// No dependencies
class Report {
    public string $ReportID;
    public string $ReportedByID;
    public int $TargetUserID;
    public string $ReportContent;
    public string $Status; // Enum
    public string $AdminResponse;

    public function assignToAdmin(): bool {}
} 