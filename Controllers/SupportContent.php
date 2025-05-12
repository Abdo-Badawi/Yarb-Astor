<?php

namespace Models;

class SupportContent {
    public int $ContentId;
    public string $Title;
    public string $Content;
    public string $Category;
    public string $Status;
    public ?string $UserType = null;
    public ?bool $Featured = null;
    public \DateTime $CreatedAt;
    public ?\DateTime $LastUpdated = null;

    public function __construct($data) {
        $this->ContentId = $data['content_id'] ?? 0;
        $this->Title = $data['title'] ?? '';
        $this->Content = $data['content'] ?? '';
        $this->Category = $data['category'] ?? '';
        $this->Status = $data['status'] ?? 'draft';

        // Optional fields that might not exist in the database
        if (isset($data['user_type'])) {
            $this->UserType = $data['user_type'];
        }

        if (isset($data['featured'])) {
            $this->Featured = (bool)$data['featured'];
        }

        $this->CreatedAt = isset($data['created_at']) ? new \DateTime($data['created_at']) : new \DateTime();

        if (isset($data['last_updated'])) {
            $this->LastUpdated = new \DateTime($data['last_updated']);
        }
    }

    public function toArray(): array {
        $result = [
            'content_id' => $this->ContentId,
            'title' => $this->Title,
            'content' => $this->Content,
            'category' => $this->Category,
            'status' => $this->Status,
            'created_at' => $this->CreatedAt->format('Y-m-d H:i:s')
        ];

        // Add optional fields only if they exist
        if ($this->UserType !== null) {
            $result['user_type'] = $this->UserType;
        }

        if ($this->Featured !== null) {
            $result['featured'] = $this->Featured;
        }

        if ($this->LastUpdated !== null) {
            $result['last_updated'] = $this->LastUpdated->format('Y-m-d H:i:s');
        }

        return $result;
    }
}