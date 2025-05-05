<?php
namespace Models;

// No dependencies
class Application {
    private string $OpportunityID;
    private string $TravelerID;
    private string $Status; // Enum
    private \DateTime $AppliedDate;
    private string $comment;

    

    public function __construct(string $OpportunityID, string $TravelerID, string $Status, string $comment) {
        $this->OpportunityID = $OpportunityID;
        $this->TravelerID = $TravelerID;
        $this->Status = $Status;
        $this->comment = $comment;
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
                case 'accepted':
                    $statusText = "Accepted";
                    $statusColor = "bg-success text-white";  // Green background for open
                    break;
                case 'rejected':
                    $statusText = "Rejected";
                    $statusColor = "bg-danger text-white";  // Red background for closed
                    break;
                case 'pending':
                    $statusText = "Pending";
                    $statusColor = "bg-warning text-dark";  // Yellow background for cancelled
                    break;
                default:
                    $statusText = "Unknown";
                    $statusColor = "bg-secondary text-white";  // Gray background for unknown
                    break;
            }
    
            echo "<div class='col-lg-12'>
                    <div class='card border-0 shadow-sm'>
                        <div class='card-body'>
                            <div class='d-flex justify-content-between align-items-center mb-3'>
                                <img src='" . htmlspecialchars($opp['opportunity_photo']) . "' alt='Opportunity Image' class='img-fluid rounded-circle' style='width: 100px; height: 100px;'>
                                <h5 class='card-title mb-0'>" . htmlspecialchars($opp['title']) . "</h5>
                                <span class='badge $statusColor'>
                                    $statusText
                                </span>
                            </div>
                            <div class='row'>
                                <div class='col-md-6 mb-4'>
                                    <h5>Opportunity Details</h5>
                                    <p class='mb-2'><i class='fa fa-clock me-2'></i>Created At: " . htmlspecialchars($opp['created_at']) . "</p>
                                    <p class='mb-2'><i class='fa fa-tags me-2'></i> Category: " . htmlspecialchars($opp['category']) ."</p>
                                    <p class='mb-2'><i class='fa fa-map-marker-alt me-2'></i> Location: " . htmlspecialchars($opp['opportunity_location']) . "</p>
                                    <p class='mb-2'><i class='fa fa-calendar-plus me-2'></i> Start Date: " . htmlspecialchars($opp['start_date']) . "</p>
                                    <p class='mb-2'><i class='fa fa-calendar-check me-2'></i> End Date: " . htmlspecialchars($opp['end_date']) . "</p>
                                    <p class='mb-2'><i class='fa fa-tasks me-2'></i> Requirements: " . htmlspecialchars($opp['requirements']) . "</p>
                                    <p class='mb-2'><i class='fa fa-align-left me-2'></i> Description: " . htmlspecialchars($opp['description']) . "</p>
                                </div>
                                <div class='col-md-6 mb-4'>
                                    <h5>Traveler Details</h5>
                                    <p class='mb-2'>
                                        <i class='fa fa-user me-2'></i>
                                        Name: " . htmlspecialchars($opp['first_name'] . ' ' . $opp['last_name']) . "
                                    </p>
                                    <p class='mb-2'><i class='fa fa-venus-mars me-2'></i> Gender: " . htmlspecialchars($opp['gender']) ."</p>
                                    <p class='mb-2'><i class='fa fa-envelope me-2'></i> Email: " . htmlspecialchars($opp['email']) . "</p>
                                    <p class='mb-2'><i class='fa fa-phone me-2'></i> Phone Number: " . htmlspecialchars($opp['phone_number']) . "</p>
                                    <p class='mb-2'><i class='fa fa-language me-2'></i> Language Spoken: " . htmlspecialchars($opp['language_spoken']) . "</p>
                                    <p class='mb-2'><i class='fa fa-star me-2'></i> Rate: " . htmlspecialchars($opp['rate']) . "</p>
                                    <p class='mb-2'><i class='fa fa-map-marker-alt me-2'></i> Traveler Location: " . htmlspecialchars($opp['traveler_location']) . "</p>
                                    <p class='mb-2'><i class='fa fa-comment me-2'></i> Comment: " . htmlspecialchars($opp['comment']) . "</p>
                                </div>
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

} 