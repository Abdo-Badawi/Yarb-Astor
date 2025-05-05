<?php
namespace Models;

// No dependencies
class SupportContent {
    public string $Title;
    public string $Content;
    public string $Category; // Enum
    public \DateTime $CreatedAt;
    public \DateTime $LastUpdated;

    public function updateContent(): bool {}
    public function searchContent(): array {} // List<SupportContent>
} 