<?php
include_once "../Models/Database.php";

class Opportunity {
    private int $id = 0;                          // Primary Key (BIGINT AUTO_INCREMENT)
    private ?string $opportunityPhoto = null;     // Path or URL to photo
    private string $title = '';
    private string $description = '';
    private string $location = '';
    private ?\DateTime $startDate = null;         // Corresponds to DATE
    private ?\DateTime $endDate = null;
    private string $category = '';                // ENUM('teaching', 'farming', 'cooking', 'childcare')
    private string $hostId = '';                  // VARCHAR(255) referencing users(user_id)
    private string $status = 'open';              // ENUM('open', 'closed', 'cancelled')
    private ?\DateTime $createdAt = null;         // TIMESTAMP
    private string $requirements = '';            // TEXT (could be JSON or comma-separated)
    private $db;

    public function _construct() {
        $this->db = new Database();
        $this->createdAt = new \DateTime();
    }

    public function initWithData(array $data): Opportunity {
        if (isset($data['title'])) $this->title = $data['title'];
        if (isset($data['description'])) $this->description = $data['description'];
        if (isset($data['location'])) $this->location = $data['location'];
        
        if (isset($data['start_date'])) {
            if ($data['start_date'] instanceof \DateTime) {
                $this->startDate = $data['start_date'];
            } else {
                $this->startDate = new \DateTime($data['start_date']);
            }
        }
        
        if (isset($data['end_date'])) {
            if ($data['end_date'] instanceof \DateTime) {
                $this->endDate = $data['end_date'];
            } else {
                $this->endDate = new \DateTime($data['end_date']);
            }
        }
        
        if (isset($data['category'])) $this->category = $data['category'];
        if (isset($data['opportunity_photo'])) $this->opportunityPhoto = $data['opportunity_photo'];
        if (isset($data['requirements'])) $this->requirements = $data['requirements'];
        if (isset($data['host_id'])) $this->hostId = $data['host_id'];
        if (isset($data['status'])) $this->status = $data['status'];
        
        return $this;
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

    public function getImagePath(): ?string {
        return $this->opportunityPhoto;
    }

    public function setImagePath(?string $imagePath): void {
        $this->opportunityPhoto = $imagePath;
    }


    public function __construct() {
        $this->db = new Database();
    }

    // Function to check if a traveler has already applied for an opportunity
    public function checkIfTravelerApplied(int $travelerId, int $opportunityId): bool {
        $sql = "SELECT * FROM applications WHERE traveler_id = ? AND opportunity_id = ?";
        $params = [$travelerId, $opportunityId];

        $this->db->openConnection();
        $result = $this->db->selectPrepared($sql, "ii", $params);
        $this->db->closeConnection();

        return !empty($result);
    }

    // Function to apply for an opportunity
    public function applyForOpportunity(array $applicationData): bool {
        $sql = "INSERT INTO applications (traveler_id, opportunity_id, message, availability, experience, status, applied_date)
                VALUES (?, ?, ?, ?, ?, ?, ?)";

        $params = [
            $applicationData['traveler_id'],
            $applicationData['opportunity_id'],
            $applicationData['message'],
            $applicationData['availability'],
            $applicationData['experience'],
            $applicationData['status'],
            $applicationData['applied_date']
        ];

        $this->db->openConnection();
        $result = $this->db->insert($sql, "iisssss", $params);
        $this->db->closeConnection();

        return $result;
    }

    // Function to get opportunity by ID
    public function getOpportunityById(int $opportunityId): ?array {
        $sql = "SELECT o.*, u.first_name, u.last_name, u.profile_picture
                FROM opportunity o
                JOIN users u ON o.host_id = u.user_id
                WHERE o.opportunity_id = ?";

        $params = [$opportunityId];

        $this->db->openConnection();
        $result = $this->db->selectPrepared($sql, "i", $params);
        $this->db->closeConnection();

        return $result[0] ?? null;
    }

    // Function to get active opportunities
    public function getActiveOpportunities(): array {
        $currentDate = date('Y-m-d');

        $sql = "SELECT o.*, u.first_name, u.last_name
                FROM opportunity o
                JOIN users u ON o.host_id = u.user_id
                WHERE o.status = 'open'
                AND o.end_date >= ?
                ORDER BY o.created_at DESC";

        $params = [$currentDate];

        $this->db->openConnection();
        $result = $this->db->selectPrepared($sql, "s", $params);
        $this->db->closeConnection();

        return $result ?: [];
    }

    // Function to get opportunities that a traveler has applied to
    /**
     * Get opportunities applied by a traveler with details
     * 
     * @param int $travelerID The traveler ID
     * @return array Array of opportunities
     */
    public function getOpportunitiesByTravelerID(int $travelerID): array {
        $sql = "SELECT a.*, 
                o.title, o.description, o.location, o.start_date, o.end_date, o.category, o.opportunity_photo, o.host_id,
                h.first_name AS host_first_name, h.last_name AS host_last_name
                FROM applications a
                JOIN opportunity o ON a.opportunity_id = o.opportunity_id
                JOIN users h ON o.host_id = h.user_id
                WHERE a.traveler_id = ?
                ORDER BY a.applied_date DESC";

        $params = [$travelerID];

        $this->db->openConnection();
        $result = $this->db->selectPrepared($sql, "i", $params);
        $this->db->closeConnection();

        return $result ?: [];
    }

    // Function to get applications for an opportunity
    public function getApplicationsByOpportunityId(int $opportunityId): array {
        $sql = "SELECT a.*, u.first_name, u.last_name, u.profile_picture
                FROM applications a
                JOIN users u ON a.traveler_id = u.user_id
                WHERE a.opportunity_id = ?
                ORDER BY a.applied_date DESC";

        $params = [$opportunityId];

        $this->db->openConnection();
        $result = $this->db->selectPrepared($sql, "i", $params);
        $this->db->closeConnection();

        return $result ?: [];
    }

    // Function to update application status
    public function updateApplicationStatus(int $applicationId, string $status): bool {
        $sql = "UPDATE applications SET status = ? WHERE application_id = ?";

        $params = [$status, $applicationId];

        $this->db->openConnection();
        $result = $this->db->update($sql, "si", $params);
        $this->db->closeConnection();

        return $result;
    }

    // Function to get opportunities by host ID
    public function getOpportunitiesByHostID(int $hostID): array {
        $sql = "SELECT * FROM opportunity WHERE host_id = ? ORDER BY created_at DESC";

        $params = [$hostID];

        $this->db->openConnection();
        $result = $this->db->selectPrepared($sql, "i", $params);
        $this->db->closeConnection();

        return $result ?: [];
    }

    // Function to delete an opportunity
    public function deleteOpportunity(int $opportunityId): bool {
        // First, delete any applications associated with this opportunity
        $sqlApplications = "DELETE FROM applications WHERE opportunity_id = ?";
        
        $this->db->openConnection();
        $this->db->delete($sqlApplications, "i", [$opportunityId]);
        
        // Then delete the opportunity itself
        $sql = "DELETE FROM opportunity WHERE opportunity_id = ?";
        
        $params = [$opportunityId];
        $result = $this->db->delete($sql, "i", $params);
        $this->db->closeConnection();
        
        return $result;
    }

    // Function to update opportunity status
    public function updateOpportunityStatus(int $opportunityId, string $status): bool {
        $sql = "UPDATE opportunity SET status = ? WHERE opportunity_id = ?";

        $params = [$status, $opportunityId];

        $this->db->openConnection();
        $result = $this->db->update($sql, "si", $params);
        $this->db->closeConnection();

        return $result;
    }

    // Function to update an opportunity
    public function updateOpportunity(array $opportunityData): bool {
        $sql = "UPDATE opportunity SET
                title = ?,
                description = ?,
                location = ?,
                start_date = ?,
                end_date = ?,
                category = ?,
                requirements = ?,
                opportunity_photo = ?,
                status = ?
                WHERE opportunity_id = ?";

        $params = [
            $opportunityData['title'],
            $opportunityData['description'],
            $opportunityData['location'],
            $opportunityData['start_date'],
            $opportunityData['end_date'],
            $opportunityData['category'],
            $opportunityData['requirements'],
            $opportunityData['image_path'],
            $opportunityData['status'] ?? 'open', // Default to 'open' if not provided
            $opportunityData['opportunity_id']
        ];

        $this->db->openConnection();
        $result = $this->db->update($sql, "sssssssssi", $params);
        $this->db->closeConnection();

        return $result;
    }

    // Function to get all opportunities
    public function getAllOpportunities(): array {
        $sql = "SELECT o.*, u.first_name, u.last_name, u.profile_picture
                FROM opportunity o
                JOIN users u ON o.host_id = u.user_id
                ORDER BY o.created_at DESC";

        $this->db->openConnection();
        $result = $this->db->select($sql);
        $this->db->closeConnection();

        return $result ?: [];
    }

    public function saveOpportunityToDB($opportunity) {
        try {
            // Ensure database connection is established
            $this->db->openConnection();

            // Get image path
            $imagePath = $opportunity->getImagePath();

            // Debug information
            error_log("Image path: " . ($imagePath ?? 'null'));

            // Prepare the SQL statement with the correct column name "opportunity_photo"
            $sql = "INSERT INTO opportunity (title, description, location, start_date, end_date, category, opportunity_photo, requirements, host_id, status, created_at)
                    VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, 'open', NOW())";

            // Format dates for MySQL
            $startDate = $opportunity->getStartDate()->format('Y-m-d');
            $endDate = $opportunity->getEndDate()->format('Y-m-d');

            // Set parameters
            $params = [
                $opportunity->getTitle(),
                $opportunity->getDescription(),
                $opportunity->getLocation(),
                $startDate,
                $endDate,
                $opportunity->getCategory(),
                $imagePath,
                $opportunity->getRequirements(),
                $opportunity->getHostId()
            ];

            // Debug information
            error_log("Saving opportunity with params: " . print_r($params, true));

            // Execute the query
            $result = $this->db->insert($sql, "ssssssssi", $params);

            // Close the connection
            $this->db->closeConnection();

            if (!$result) {
                error_log("Database insert failed in saveOpportunityToDB");
            } else {
                error_log("Opportunity saved successfully with ID: " . $result);
            }

            return $result;
        } catch (Exception $e) {
            error_log("Error saving opportunity: " . $e->getMessage());
            return false;
        }
    }

    /**
     * Create a new opportunity in the database
     * 
     * @param array $data Opportunity data
     * @return bool True if creation was successful, false otherwise
     */
    public function createOpportunity(array $data): bool {
        try {
            // Ensure database connection is established
            $this->db->openConnection();

            // Prepare the SQL statement
            $sql = "INSERT INTO opportunity (title, description, location, start_date, end_date, category, opportunity_photo, requirements, host_id, status, created_at)
                    VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, NOW())";

            // Format dates for MySQL if they're not already formatted
            $startDate = $data['start_date'];
            if ($startDate instanceof \DateTime) {
                $startDate = $startDate->format('Y-m-d');
            }
            
            $endDate = $data['end_date'];
            if ($endDate instanceof \DateTime) {
                $endDate = $endDate->format('Y-m-d');
            }

            // Set parameters
            $params = [
                $data['title'],
                $data['description'],
                $data['location'],
                $startDate,
                $endDate,
                $data['category'],
                $data['opportunity_photo'],
                $data['requirements'],
                $data['host_id'],
                $data['status']
            ];

            // Debug information
            error_log("Creating opportunity with params: " . print_r($params, true));

            // Execute the query
            $result = $this->db->insert($sql, "ssssssssss", $params);

            // Close the connection
            $this->db->closeConnection();

            if (!$result) {
                error_log("Database insert failed in createOpportunity");
                return false;
            } else {
                error_log("Opportunity created successfully with ID: " . $result);
                return true;
            }
        } catch (Exception $e) {
            error_log("Exception in createOpportunity: " . $e->getMessage());
            $this->db->closeConnection();
            return false;
        }
    }


    public static function printOpportunities($opportunities) {
        // Check if the opportunities array is empty
        if (empty($opportunities)) {
            echo "<p>No opportunities found for this host.</p>";
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
                    $statusText = "Unknown";
                    $statusColor = "bg-secondary text-white";  // Gray background for unknown
                    break;
            }
    
            echo "<div class='col-lg-6'>
                    <div class='card border-0 shadow-sm'>
                        <div class='card-body'>
                            <div class='d-flex justify-content-between align-items-center mb-3'>
                                <img src='" . htmlspecialchars($opp['opportunity_photo']) . "' alt='Opportunity Image' class='img-fluid rounded-circle' style='width: 100px; height: 100px;'>
                                <h5 class='card-title mb-0'>" . htmlspecialchars($opp['title']) . "</h5>
                                <span class='badge $statusColor'>
                                    $statusText
                                </span>
                            </div>
                            <div class='mb-3'>
                                <p class='mb-2'><i class='fa fa-clock me-2'></i>Created At: " . htmlspecialchars($opp['created_at']) . "</p>
                                <p class='mb-2'><i class='bi bi-tags-fill me-2'></i> Category: " . htmlspecialchars($opp['category']) ."</p>
                                <p class='mb-2'><i class='fa fa-location-arrow me-2'></i>Location: " . htmlspecialchars($opp['location']) . "</p>
                                <p class='mb-2'><i class='bi bi-calendar-fill me-2'></i> Start Date: " . htmlspecialchars($opp['start_date']) . "</p>
                                <p class='mb-2'><i class='bi bi-calendar-check-fill me-2'></i> End Date: " . htmlspecialchars($opp['end_date']) . "</p>
                                <p class='mb-2'><i class='fa fa-tasks me-2'></i>Requirements: " . htmlspecialchars($opp['requirements']) . "</p>
                                <p class='mb-2'><i class='fa fa-info-circle me-2'></i>Description: " . htmlspecialchars($opp['description']) . "</p>
                            </div>
                            <div class='d-flex justify-content-between'>
                                <div>
                                    <button class='btn btn-primary me-2 px-3'>Edit</button>
                                </div>
                                <button class='btn btn-sm btn-danger'>Mark Filled</button>
                            </div>
                        </div>
                    </div>
                </div>";
        }
    
        // End the card layout
        echo "</div>";
    }

    /**
     * Get opportunity with host details by ID
     * 
     * @param int $opportunityId The opportunity ID
     * @return array|null Opportunity with host data or null if not found
     */
    public function getOpportunityWithHostById(int $opportunityId): ?array {
        $sql = "SELECT o.*, 
                h.property_type, h.preferred_language, h.about, h.hosting_since,
                u.first_name, u.last_name, u.email, u.profile_picture, u.location as host_location
                FROM opportunity o
                JOIN hosts h ON o.host_id = h.host_id
                JOIN users u ON h.host_id = u.user_id
                WHERE o.opportunity_id = ?";

        $params = [$opportunityId];

        $this->db->openConnection();
        $result = $this->db->selectPrepared($sql, "i", $params);
        $this->db->closeConnection();

        return $result[0] ?? null;
    }

    /**
     * Search for opportunities based on various filters
     * 
     * @param array $filters Array of filter parameters
     * @return array List of matching opportunities
     */
    public function searchOpportunities(array $filters = []): array {
        $this->db->openConnection();
        
        // Start building the query
        $sql = "SELECT o.*, u.first_name, u.last_name, u.profile_picture 
                FROM opportunity o 
                JOIN users u ON o.host_id = u.user_id 
                WHERE o.status = 'open'";
        
        $params = [];
        $types = "";
        
        // Add location filter
        if (!empty($filters['location'])) {
            $sql .= " AND o.location LIKE ?";
            $params[] = "%" . $filters['location'] . "%";
            $types .= "s";
        }
        
        // Add category filter
        if (!empty($filters['category'])) {
            $sql .= " AND o.category = ?";
            $params[] = $filters['category'];
            $types .= "s";
        }
        
        // Add start date filter
        if (!empty($filters['start_date'])) {
            $sql .= " AND o.start_date >= ?";
            $params[] = $filters['start_date'];
            $types .= "s";
        }
        
        // Add end date filter
        if (!empty($filters['end_date'])) {
            $sql .= " AND o.end_date <= ?";
            $params[] = $filters['end_date'];
            $types .= "s";
        }
        
        // Add accommodation type filter if it exists
        if (!empty($filters['accommodation_type'])) {
            $sql .= " AND o.accommodation_type = ?";
            $params[] = $filters['accommodation_type'];
            $types .= "s";
        }
        
        // Add duration type filter
        if (!empty($filters['duration_type'])) {
            switch ($filters['duration_type']) {
                case 'short':
                    $sql .= " AND DATEDIFF(o.end_date, o.start_date) <= 14";
                    break;
                case 'medium':
                    $sql .= " AND DATEDIFF(o.end_date, o.start_date) > 14 AND DATEDIFF(o.end_date, o.start_date) <= 30";
                    break;
                case 'long':
                    $sql .= " AND DATEDIFF(o.end_date, o.start_date) > 30";
                    break;
            }
        }
        
        // Add order by clause
        $sql .= " ORDER BY o.created_at DESC";
        
        // Execute the query
        $result = empty($params) ? $this->db->select($sql) : $this->db->selectPrepared($sql, $types, $params);
        
        $this->db->closeConnection();
        
        return $result ?: [];
    }

    /**
     * Get all available locations for filtering
     * 
     * @return array List of unique locations
     */
    public function getAvailableLocations(): array {
        $this->db->openConnection();
        
        $sql = "SELECT DISTINCT location FROM opportunity WHERE status = 'open' ORDER BY location";
        $result = $this->db->select($sql);
        
        $this->db->closeConnection();
        
        return $result ?: [];
    }

    /**
     * Get all available categories for filtering
     * 
     * @return array List of unique categories
     */
    public function getAvailableCategories(): array {
        $this->db->openConnection();
        
        $sql = "SELECT DISTINCT category FROM opportunity WHERE status = 'open' ORDER BY category";
        $result = $this->db->select($sql);
        
        $this->db->closeConnection();
        
        return $result ?: [];
    }
}
