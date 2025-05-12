<?php
namespace Models;

class Opportunity {
    private int $id;
    private ?string $opportunityPhoto;
    private string $title;
    private string $description;
    private string $location;
    private \DateTime $startDate;
    private \DateTime $endDate;
    private string $category;
    private string $hostId;
    private string $status;
    private \DateTime $createdAt;
    private string $requirements;
    private int $maxVolunteers;
    private $db;

    public function __construct(string $title = '', string $description = '', string $location = '', 
                               \DateTime $startDate = null, \DateTime $endDate = null, 
                               string $category = '', ?string $opportunityPhoto = null, 
                               string $requirements = '', int $maxVolunteers = 1) {
        $this->title = $title;
        $this->description = $description;
        $this->location = $location;
        $this->startDate = $startDate ?? new \DateTime();
        $this->endDate = $endDate ?? new \DateTime('+1 month');
        $this->category = $category;
        $this->hostId = $_SESSION['userID'] ?? '0';
        $this->status = "open";
        $this->opportunityPhoto = $opportunityPhoto;
        $this->requirements = $requirements;
        $this->maxVolunteers = $maxVolunteers;
        $this->createdAt = new \DateTime();
        
        // Initialize database connection
        require_once __DIR__ . '/../Controllers/DBController.php';
        $this->db = new \DBController();
    }

    // Getters and Setters

    public function getId(): int {
        return $this->id;
    }

    public function setId(int $id): void {
        $this->id = $id;
    }

    public function getOpportunityPhoto(): ?string {
        return $this->opportunityPhoto;
    }

    public function setOpportunityPhoto(?string $opportunityPhoto): void {
        $this->opportunityPhoto = $opportunityPhoto;
    }

    public function getTitle(): string {
        return $this->title;
    }

    public function setTitle(string $title): void {
        $this->title = $title;
    }

    public function getDescription(): string {
        return $this->description;
    }

    public function setDescription(string $description): void {
        $this->description = $description;
    }

    public function getLocation(): string {
        return $this->location;
    }

    public function setLocation(string $location): void {
        $this->location = $location;
    }

    public function getStartDate(): \DateTime {
        return $this->startDate;
    }

    public function setStartDate(\DateTime $startDate): void {
        $this->startDate = $startDate;
    }

    public function getEndDate(): \DateTime {
        return $this->endDate;
    }

    public function setEndDate(\DateTime $endDate): void {
        $this->endDate = $endDate;
    }

    public function getCategory(): string {
        return $this->category;
    }

    public function setCategory(string $category): void {
        $this->category = $category;
    }

    public function getHostId(): string {
        return $this->hostId;
    }

    public function setHostId(string $hostId): void {
        $this->hostId = $hostId;
    }

    public function getStatus(): string {
        return $this->status;
    }

    public function setStatus(string $status): void {
        $this->status = $status;
    }

    public function getCreatedAt(): \DateTime {
        return $this->createdAt;
    }

    public function setCreatedAt(\DateTime $createdAt): void {
        $this->createdAt = $createdAt;
    }

    public function getRequirements(): string {
        return $this->requirements;
    }

    public function setRequirements(string $requirements): void {
        $this->requirements = $requirements;
    }
    
    public function getMaxVolunteers(): int {
        return $this->maxVolunteers;
    }
    
    public function setMaxVolunteers(int $maxVolunteers): void {
        $this->maxVolunteers = $maxVolunteers;
    }

    // Methods to manipulate the Opportunity status

    public function closeOpportunity(): bool {
        $this->status = 'closed';
        return true;
    }

    public function reopenOpportunity(): bool {
        $this->status = 'open';
        return true;
    }

    public function markAsCancelled(): bool {
        $this->status = 'cancelled';
        return true;
    }

    public function editDetails(array $data): bool {
        foreach ($data as $key => $value) {
            if (property_exists($this, $key)) {
                $this->$key = $value;
            }
        }
        return true;
    }

    public function markAsFilled(): bool {
        $this->status = 'closed';  // Assuming "filled" means no longer open
        return true;
    }

    public function isAvailable(): bool {
        $today = new \DateTime();
        return $this->status === 'open' && $today >= $this->startDate && $today <= $this->endDate;
    }

    public function addRequirement(string $requirement): bool {
        $this->requirements .= ($this->requirements ? ', ' : '') . $requirement;
        return true;
    }

    /**
     * Get the image path for the opportunity
     * 
     * @return string|null The image path
     */
    public function getImagePath(): ?string {
        return $this->opportunityPhoto;
    }
    
    /**
     * Set the image path for the opportunity
     * 
     * @param string|null $imagePath The image path
     */
    public function setImagePath(?string $imagePath): void {
        $this->opportunityPhoto = $imagePath;
    }

    /**
     * Convert opportunity object to array
     * 
     * @return array Opportunity data as array
     */
    public function toArray(): array {
        return [
            'opportunity_id' => $this->id ?? 0,
            'title' => $this->title,
            'description' => $this->description,
            'location' => $this->location,
            'start_date' => $this->startDate->format('Y-m-d'),
            'end_date' => $this->endDate->format('Y-m-d'),
            'category' => $this->category,
            'host_id' => $this->hostId,
            'status' => $this->status,
            'created_at' => $this->createdAt->format('Y-m-d H:i:s'),
            'requirements' => $this->requirements,
            'opportunity_photo' => $this->opportunityPhoto,
            'max_volunteers' => $this->maxVolunteers
        ];
    }

    /**
     * Create opportunity object from array
     * 
     * @param array $data Opportunity data
     * @return Opportunity
     */
    public static function fromArray(array $data): Opportunity {
        $opportunity = new Opportunity();
        
        if (isset($data['opportunity_id'])) {
            $opportunity->setId($data['opportunity_id']);
        }
        
        if (isset($data['title'])) {
            $opportunity->setTitle($data['title']);
        }
        
        if (isset($data['description'])) {
            $opportunity->setDescription($data['description']);
        }
        
        if (isset($data['location'])) {
            $opportunity->setLocation($data['location']);
        }
        
        if (isset($data['start_date'])) {
            $opportunity->setStartDate(new \DateTime($data['start_date']));
        }
        
        if (isset($data['end_date'])) {
            $opportunity->setEndDate(new \DateTime($data['end_date']));
        }
        
        if (isset($data['category'])) {
            $opportunity->setCategory($data['category']);
        }
        
        if (isset($data['host_id'])) {
            $opportunity->setHostId($data['host_id']);
        }
        
        if (isset($data['status'])) {
            $opportunity->setStatus($data['status']);
        }
        
        if (isset($data['created_at'])) {
            $opportunity->setCreatedAt(new \DateTime($data['created_at']));
        }
        
        if (isset($data['requirements'])) {
            $opportunity->setRequirements($data['requirements']);
        }
        
        if (isset($data['opportunity_photo'])) {
            $opportunity->setOpportunityPhoto($data['opportunity_photo']);
        }
        
        if (isset($data['max_volunteers'])) {
            $opportunity->setMaxVolunteers($data['max_volunteers']);
        }
        
        return $opportunity;
    }

    /**
     * Print opportunities as HTML cards
     * 
     * @param array $opportunities Array of opportunity data
     * @return void
     */
    public static function printOpportunities($opportunities) {
        // Check if the opportunities array is empty
        if (empty($opportunities)) {
            echo "<p>No opportunities found.</p>";
            return;
        }
    
        // Start the card-style layout
        echo "<div class='row g-4'>";
    
        // Loop through each opportunity and display it as a card
        foreach ($opportunities as $opp) {
            $statusText = '';  // To hold the status name
            $statusColor = ''; // To hold the background color
    
            // Set status name and background color based on status value
            switch (strtolower(htmlspecialchars($opp['status']))) {
                case 'open':
                    $statusText = "Open";
                    $statusColor = "bg-success text-white";  // Green background for open
                    break;
                case 'closed':
                    $statusText = "Closed";
                    $statusColor = "bg-danger text-white";  // Red background for closed
                    break;
                case 'cancelled':
                    $statusText = "Cancelled";
                    $statusColor = "bg-warning text-dark";  // Yellow background for cancelled
                    break;
                default:
                    $statusText = ucfirst(htmlspecialchars($opp['status']));
                    $statusColor = "bg-secondary text-white";  // Gray background for other statuses
            }
    
            echo "<div class='col-lg-6'>
                    <div class='card border-0 shadow-sm'>
                        <div class='card-body'>
                            <div class='d-flex justify-content-between align-items-center mb-3'>
                                <img src='" . htmlspecialchars($opp['opportunity_photo'] ?? '../assets/img/default-opportunity.jpg') . "' alt='Opportunity Image' class='img-fluid rounded-circle' style='width: 100px; height: 100px;'>
                                <h5 class='card-title mb-0'>" . htmlspecialchars($opp['title']) . "</h5>
                                <span class='badge $statusColor'>
                                    $statusText
                                </span>
                            </div>
                            <div class='mb-3'>
                                <p class='mb-2'><i class='fa fa-clock me-2'></i>Created At: " . htmlspecialchars($opp['created_at']) . "</p>
                                <p class='mb-2'><i class='bi bi-tags-fill me-2'></i> Category: " . htmlspecialchars($opp['category']) ."</p>
                                <p class='mb-2'><i class='fa fa-map-marker-alt me-2'></i> Location: " . htmlspecialchars($opp['location']) . "</p>
                                <p class='mb-2'><i class='fa fa-calendar me-2'></i> Duration: " . htmlspecialchars($opp['start_date']) . " to " . htmlspecialchars($opp['end_date']) . "</p>
                            </div>
                            <div class='d-flex justify-content-between'>
                                <a href='view-opportunity.php?id=" . htmlspecialchars($opp['opportunity_id']) . "' class='btn btn-sm btn-primary'>View Details</a>
                                <a href='edit-opportunity.php?id=" . htmlspecialchars($opp['opportunity_id']) . "' class='btn btn-sm btn-outline-primary'>Edit</a>
                                <a href='delete-opportunity.php?id=" . htmlspecialchars($opp['opportunity_id']) . "' class='btn btn-sm btn-outline-danger' onclick='return confirm(\"Are you sure you want to delete this opportunity?\")'>Delete</a>
                            </div>
                        </div>
                    </div>
                </div>";
        }
    
        // End the card-style layout
        echo "</div>";
    }

    /**
     * Save opportunity to database
     * 
     * @return int|bool Opportunity ID if successful, false otherwise
     */
    public function save(): int|bool {
        // Check if opportunity already exists
        if (isset($this->id) && $this->id > 0) {
            return $this->update();
        }
        
        // Create new opportunity
        $sql = "INSERT INTO opportunity (title, description, location, start_date, end_date, 
                category, host_id, status, created_at, requirements, opportunity_photo, max_volunteers)
                VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)";
        
        $params = [
            $this->title,
            $this->description,
            $this->location,
            $this->startDate->format('Y-m-d'),
            $this->endDate->format('Y-m-d'),
            $this->category,
            $this->hostId,
            $this->status,
            $this->createdAt->format('Y-m-d H:i:s'),
            $this->requirements,
            $this->opportunityPhoto,
            $this->maxVolunteers
        ];
        
        $this->db->openConnection();
        $result = $this->db->insert($sql, "sssssssssssi", $params);
        
        if ($result) {
            $this->id = $this->db->getInsertId();
        }
        
        $this->db->closeConnection();
        
        return $result ? $this->id : false;
    }
    
    /**
     * Update opportunity in database
     * 
     * @return bool True if update successful, false otherwise
     */
    public function update(): bool {
        if (!isset($this->id) || $this->id <= 0) {
            return false;
        }
        
        $sql = "UPDATE opportunity SET
                title = ?,
                description = ?,
                location = ?,
                start_date = ?,
                end_date = ?,
                category = ?,
                status = ?,
                requirements = ?,
                opportunity_photo = ?,
                max_volunteers = ?
                WHERE opportunity_id = ?";
        
        $params = [
            $this->title,
            $this->description,
            $this->location,
            $this->startDate->format('Y-m-d'),
            $this->endDate->format('Y-m-d'),
            $this->category,
            $this->status,
            $this->requirements,
            $this->opportunityPhoto,
            $this->maxVolunteers,
            $this->id
        ];
        
        $this->db->openConnection();
        $result = $this->db->update($sql, "sssssssssii", $params);
        $this->db->closeConnection();
        
        return $result;
    }
    
    /**
     * Delete opportunity from database
     * 
     * @return bool True if deletion successful, false otherwise
     */
    public function delete(): bool {
        if (!isset($this->id) || $this->id <= 0) {
            return false;
        }
        
        $this->db->openConnection();
        
        // Begin transaction
        $this->db->beginTransaction();
        
        // Delete applications
        $appSql = "DELETE FROM applications WHERE opportunity_id = ?";
        $appResult = $this->db->delete($appSql, "i", [$this->id]);
        
        if (!$appResult) {
            $this->db->rollbackTransaction();
            $this->db->closeConnection();
            return false;
        }
        
        // Delete opportunity
        $oppSql = "DELETE FROM opportunity WHERE opportunity_id = ?";
        $oppResult = $this->db->delete($oppSql, "i", [$this->id]);
        
        if (!$oppResult) {
            $this->db->rollbackTransaction();
            $this->db->closeConnection();
            return false;
        }
        
        // Commit transaction
        $this->db->commitTransaction();
        $this->db->closeConnection();
        
        return true;
    }
    
    /**
     * Load opportunity from database by ID
     * 
     * @param int $id Opportunity ID
     * @return bool True if loaded successfully, false otherwise
     */
    public function loadById(int $id): bool {
        $sql = "SELECT * FROM opportunity WHERE opportunity_id = ?";
        
        $this->db->openConnection();
        $result = $this->db->selectPrepared($sql, "i", [$id]);
        $this->db->closeConnection();
        
        if (empty($result)) {
            return false;
        }
        
        $this->mapFromDatabase($result[0]);
        return true;
    }
    
    /**
     * Map database result to object properties
     * 
     * @param array $data Database result row
     */
    private function mapFromDatabase(array $data): void {
        $this->id = $data['opportunity_id'];
        $this->title = $data['title'];
        $this->description = $data['description'];
        $this->location = $data['location'];
        $this->startDate = new \DateTime($data['start_date']);
        $this->endDate = new \DateTime($data['end_date']);
        $this->category = $data['category'];
        $this->hostId = $data['host_id'];
        $this->status = $data['status'];
        $this->createdAt = new \DateTime($data['created_at']);
        $this->requirements = $data['requirements'];
        $this->opportunityPhoto = $data['opportunity_photo'];
        $this->maxVolunteers = $data['max_volunteers'];
    }
    
    /**
     * Get all opportunities
     * 
     * @param array $filters Optional filters
     * @return array Array of Opportunity objects
     */
    public static function getAll(array $filters = []): array {
        $db = new \DBController();
        $sql = "SELECT o.*, u.first_name, u.last_name, u.profile_picture
                FROM opportunity o
                JOIN users u ON o.host_id = u.user_id";
        
        $whereConditions = [];
        $params = [];
        $types = "";
        
        // Add filters
        if (!empty($filters['category'])) {
            $whereConditions[] = "o.category = ?";
            $params[] = $filters['category'];
            $types .= "s";
        }
        
        if (!empty($filters['location'])) {
            $whereConditions[] = "o.location LIKE ?";
            $params[] = "%" . $filters['location'] . "%";
            $types .= "s";
        }
        
        if (!empty($filters['status'])) {
            $whereConditions[] = "o.status = ?";
            $params[] = $filters['status'];
            $types .= "s";
        }
        
        if (!empty($filters['start_date'])) {
            $whereConditions[] = "o.end_date >= ?";
            $params[] = $filters['start_date'];
            $types .= "s";
        }
        
        // Add WHERE clause if conditions exist
        if (!empty($whereConditions)) {
            $sql .= " WHERE " . implode(" AND ", $whereConditions);
        }
        
        // Add ORDER BY clause
        $sql .= " ORDER BY o.created_at DESC";
        
        $db->openConnection();
        $result = empty($params) ? $db->select($sql) : $db->selectPrepared($sql, $types, $params);
        $db->closeConnection();
        
        return $result ?: [];
    }
    
    /**
     * Get active opportunities
     * 
     * @param array $filters Optional filters
     * @return array Array of active opportunities
     */
    public static function getActive(array $filters = []): array {
        $filters['status'] = 'open';
        $filters['start_date'] = date('Y-m-d');
        return self::getAll($filters);
    }
    
    /**
     * Get opportunities by host ID
     * 
     * @param int $hostId Host ID
     * @return array Array of opportunities
     */
    public static function getByHostId(int $hostId): array {
        $db = new \DBController();
        $sql = "SELECT * FROM opportunity WHERE host_id = ? ORDER BY created_at DESC";
        
        $db->openConnection();
        $result = $db->selectPrepared($sql, "i", [$hostId]);
        $db->closeConnection();
        
        return $result ?: [];
    }
    
    /**
     * Search opportunities
     * 
     * @param string $searchTerm Search term
     * @return array Array of matching opportunities
     */
    public static function search(string $searchTerm): array {
        $db = new \DBController();
        $sql = "SELECT o.*, u.first_name, u.last_name, u.profile_picture
                FROM opportunity o
                JOIN users u ON o.host_id = u.user_id
                WHERE o.title LIKE ? OR o.description LIKE ? OR o.location LIKE ? OR o.category LIKE ?
                ORDER BY o.created_at DESC";
        
        $searchParam = "%" . $searchTerm . "%";
        $params = [$searchParam, $searchParam, $searchParam, $searchParam];
        
        $db->openConnection();
        $result = $db->selectPrepared($sql, "ssss", $params);
        $db->closeConnection();
        
        return $result ?: [];
    }
}


