<?php
require_once 'DBController.php';
require_once '../Models/Review.php';
use Models\Review;

class ReviewController {
    private $db;
    private $reviewModel;
    
    public function __construct() {
        $this->db = new DBController();
        $this->reviewModel = new Review();
    }
    
    /**
     * Create a new review
     * 
     * @param array $reviewData Review data
     * @return int|bool Review ID if successful, false otherwise
     */
    public function createReview(array $reviewData): int|bool {
        if (!isset($_SESSION['userID'])) {
            return ['error' => 'User not logged in'];
        }
        
        // Check if user has already reviewed this opportunity
        if ($this->reviewModel->hasReviewed($reviewData['sender_id'], $reviewData['opportunity_id'])) {
            return ['error' => 'You have already reviewed this opportunity'];
        }
        
        $this->reviewModel->SenderID = $reviewData['sender_id'];
        $this->reviewModel->ReceiverID = $reviewData['receiver_id'];
        $this->reviewModel->OpportunityID = $reviewData['opportunity_id'];
        $this->reviewModel->Rating = $reviewData['rating'];
        $this->reviewModel->Comment = $reviewData['comment'];
        
        return $this->reviewModel->save();
    }
    
    /**
     * Get reviews by user ID (either as sender or receiver)
     * 
     * @param int $userId User ID
     * @return array List of reviews with user and opportunity details
     */
    public function getReviewsByUser($userId) {
        if (!$this->db->openConnection()) {
            return ['error' => 'Database connection failed'];
        }

        $query = "SELECT 
                    r.*,
                    sender.first_name as sender_name,
                    sender.profile_picture as sender_picture,
                    receiver.first_name as receiver_name,
                    receiver.profile_picture as receiver_picture,
                    o.title as opportunity_title,
                    o.location,
                    o.category
                 FROM review r
                 LEFT JOIN users sender ON r.sender_id = sender.user_id
                 LEFT JOIN users receiver ON r.receiver_id = receiver.user_id
                 LEFT JOIN opportunity o ON r.opportunity_id = o.opportunity_id
                 WHERE r.sender_id = ? OR r.receiver_id = ?
                 ORDER BY r.created_at DESC";

        $result = $this->db->selectPrepared($query, "ii", [$userId, $userId]);
        $this->db->closeConnection();

        if ($result === false) {
            return ['error' => 'Failed to fetch user reviews'];
        }

        return $result;
    }
    
    /**
     * Get review by ID
     * 
     * @param int $reviewId Review ID
     * @return array Review details with user and opportunity information
     */
    public function getReviewById($reviewId) {
        if (!$this->db->openConnection()) {
            return ['error' => 'Database connection failed'];
        }

        try {
            $query = "SELECT 
                        r.*,
                        sender.first_name as sender_name,
                        sender.profile_picture as sender_picture,
                        receiver.first_name as receiver_name,
                        receiver.profile_picture as receiver_picture,
                        o.title as opportunity_title,
                        o.location,
                        o.category
                     FROM review r
                     LEFT JOIN users sender ON r.sender_id = sender.user_id
                     LEFT JOIN users receiver ON r.receiver_id = receiver.user_id
                     LEFT JOIN opportunity o ON r.opportunity_id = o.opportunity_id
                     WHERE r.review_id = ?";

            $result = $this->db->selectPrepared($query, "i", [$reviewId]);
            $this->db->closeConnection();

            if ($result === false) {
                return ['error' => 'Failed to fetch review'];
            }

            return $result[0] ?? ['error' => 'Review not found'];
        } catch (Exception $e) {
            $this->db->closeConnection();
            return ['error' => 'An error occurred: ' . $e->getMessage()];
        }
    }
    
    /**
     * Get all reviews
     * 
     * @return array List of all reviews with user and opportunity details
     */
    public function getAllReviews() {
        if (!$this->db->openConnection()) {
            return ['error' => 'Database connection failed'];
        }

        $query = "SELECT 
                    r.*,
                    sender.first_name as sender_name,
                    sender.profile_picture as sender_picture,
                    receiver.first_name as receiver_name,
                    receiver.profile_picture as receiver_picture,
                    o.title as opportunity_title,
                    o.location,
                    o.category
                 FROM review r
                 LEFT JOIN users sender ON r.sender_id = sender.user_id
                 LEFT JOIN users receiver ON r.receiver_id = receiver.user_id
                 LEFT JOIN opportunity o ON r.opportunity_id = o.opportunity_id
                 ORDER BY r.created_at DESC";

        $result = $this->db->select($query);
        $this->db->closeConnection();

        if ($result === false) {
            return ['error' => 'Failed to fetch reviews'];
        }

        return $result;
    }
    
    /**
     * Update a review
     * 
     * @param int $reviewId Review ID
     * @param float $rating New rating
     * @param string $comment New comment
     * @return array Result of the update operation
     */
    public function updateReview($reviewId, $rating, $comment) {
        if (!isset($_SESSION['userID'])) {
            return ['error' => 'User not logged in'];
        }

        if (!$this->db->openConnection()) {
            return ['error' => 'Database connection failed'];
        }

        try {
            // First check if the review exists and belongs to the current user
            $checkQuery = "SELECT sender_id FROM review WHERE review_id = ?";
            $checkStmt = $this->db->getConnection()->prepare($checkQuery);
            if (!$checkStmt) {
                $this->db->closeConnection();
                return ['error' => 'Failed to prepare check statement: ' . $this->db->getConnection()->error];
            }

            $checkStmt->bind_param("i", $reviewId);
            if (!$checkStmt->execute()) {
                $this->db->closeConnection();
                return ['error' => 'Failed to execute check statement: ' . $checkStmt->error];
            }

            $result = $checkStmt->get_result();
            if ($result->num_rows === 0) {
                $this->db->closeConnection();
                return ['error' => 'Review not found'];
            }

            $review = $result->fetch_assoc();
            if ($review['sender_id'] != $_SESSION['userID']) {
                $this->db->closeConnection();
                return ['error' => 'You are not authorized to update this review'];
            }

            // If checks pass, proceed with update
            $updateQuery = "UPDATE review SET rating = ?, comment = ? WHERE review_id = ?";
            $updateStmt = $this->db->getConnection()->prepare($updateQuery);
            if (!$updateStmt) {
                $this->db->closeConnection();
                return ['error' => 'Failed to prepare update statement: ' . $this->db->getConnection()->error];
            }

            $updateStmt->bind_param("dsi", $rating, $comment, $reviewId);
            
            if ($updateStmt->execute()) {
                $this->db->closeConnection();
                return ['success' => true];
            } else {
                $this->db->closeConnection();
                return ['error' => 'Failed to update review: ' . $updateStmt->error];
            }
        } catch (Exception $e) {
            error_log("Error updating review: " . $e->getMessage());
            $this->db->closeConnection();
            return ['error' => 'An error occurred while updating the review: ' . $e->getMessage()];
        }
    }
    
    /**
     * Delete a review
     * 
     * @param int $reviewId Review ID
     * @return array Result of the delete operation
     */
    public function deleteReview($reviewId) {
        if (!isset($_SESSION['userID'])) {
            return ['error' => 'User not logged in'];
        }

        if (!$this->db->openConnection()) {
            return ['error' => 'Database connection failed'];
        }

        try {
            // First check if the review exists and belongs to the current user
            $checkQuery = "SELECT sender_id, is_reported FROM review WHERE review_id = ?";
            $checkStmt = $this->db->getConnection()->prepare($checkQuery);

            if (!$checkStmt) {
                $this->db->closeConnection();
                return ['error' => 'Failed to prepare check statement: ' . $this->db->getConnection()->error];
            }

            $checkStmt->bind_param("i", $reviewId);
            if (!$checkStmt->execute()) {
                $this->db->closeConnection();
                return ['error' => 'Failed to execute check statement: ' . $checkStmt->error];
            }

            $result = $checkStmt->get_result();
            if ($result->num_rows === 0) {
                $this->db->closeConnection();
                return ['error' => 'Review not found'];
            }

            $review = $result->fetch_assoc();
            if ($review['sender_id'] != $_SESSION['userID']) {
                $this->db->closeConnection();
                return ['error' => 'You are not authorized to delete this review'];
            }

            // Check if review is already reported
            if (isset($review['is_reported']) && $review['is_reported'] == 1) {
                $this->db->closeConnection();
                return ['error' => 'Cannot delete a reported review'];
            }

            // Delete the review
            $deleteQuery = "DELETE FROM review WHERE review_id = ?";
            $deleteStmt = $this->db->getConnection()->prepare($deleteQuery);
            if (!$deleteStmt) {
                $this->db->closeConnection();
                return ['error' => 'Failed to prepare delete statement: ' . $this->db->getConnection()->error];
            }

            $deleteStmt->bind_param("i", $reviewId);
            
            if ($deleteStmt->execute()) {
                $this->db->closeConnection();
                return ['success' => true];
            } else {
                $this->db->closeConnection();
                return ['error' => 'Failed to delete review: ' . $deleteStmt->error];
            }
        } catch (Exception $e) {
            error_log("Error deleting review: " . $e->getMessage());
            $this->db->closeConnection();
            return ['error' => 'An error occurred while deleting the review: ' . $e->getMessage()];
        }
    }
    
    /**
     * Report a review
     * 
     * @param int $reviewId Review ID
     * @param string $reason Reason for reporting
     * @return array Result of the report operation
     */
    public function reportReview($reviewId, $reason) {
        if (!isset($_SESSION['userID'])) {
            return ['error' => 'User not logged in'];
        }

        if (!$this->db->openConnection()) {
            return ['error' => 'Database connection failed'];
        }

        try {
            // First check if the review exists
            $checkQuery = "SELECT review_id FROM review WHERE review_id = ?";
            $checkStmt = $this->db->getConnection()->prepare($checkQuery);

            if (!$checkStmt) {
                $this->db->closeConnection();
                return ['error' => 'Failed to prepare check statement: ' . $this->db->getConnection()->error];
            }

            $checkStmt->bind_param("i", $reviewId);
            if (!$checkStmt->execute()) {
                $this->db->closeConnection();
                return ['error' => 'Failed to execute check statement: ' . $checkStmt->error];
            }

            $result = $checkStmt->get_result();
            if ($result->num_rows === 0) {
                $this->db->closeConnection();
                return ['error' => 'Review not found'];
            }

            // Mark the review as reported
            $updateQuery = "UPDATE review SET is_reported = 1 WHERE review_id = ?";
            $updateStmt = $this->db->getConnection()->prepare($updateQuery);
            if (!$updateStmt) {
                $this->db->closeConnection();
                return ['error' => 'Failed to prepare update statement: ' . $this->db->getConnection()->error];
            }

            $updateStmt->bind_param("i", $reviewId);
            
            if ($updateStmt->execute()) {
                $this->db->closeConnection();
                return ['success' => true];
            } else {
                $this->db->closeConnection();
                return ['error' => 'Failed to report review: ' . $updateStmt->error];
            }
        } catch (Exception $e) {
            error_log("Error reporting review: " . $e->getMessage());
            $this->db->closeConnection();
            return ['error' => 'An error occurred while reporting the review: ' . $e->getMessage()];
        }
    }
}

// Handle form submissions
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['action'])) {
    if (!isset($_SESSION['userID'])) {
        header('Location: ../Common/login.php');
        exit();
    }

    $reviewController = new ReviewController();
    $response = [];

    try {
        switch ($_POST['action']) {
            case 'update':
                if (isset($_POST['id']) && isset($_POST['rating']) && isset($_POST['comment'])) {
                    $result = $reviewController->updateReview(
                        intval($_POST['id']),
                        intval($_POST['rating']),
                        $_POST['comment']
                    );
                    if (isset($result['error'])) {
                        $_SESSION['error'] = $result['error'];
                    } else {
                        $_SESSION['success'] = 'Review updated successfully';
                    }
                } else {
                    $_SESSION['error'] = 'Missing required parameters';
                }
                header('Location: ../Traveler/reviews.php');
                exit();

            case 'delete':
                if (isset($_POST['id'])) {
                    $result = $reviewController->deleteReview(intval($_POST['id']));
                    if (isset($result['error'])) {
                        $_SESSION['error'] = $result['error'];
                    } else {
                        $_SESSION['success'] = 'Review deleted successfully';
                    }
                } else {
                    $_SESSION['error'] = 'Review ID not provided';
                }
                header('Location: ../Traveler/reviews.php');
                exit();

            default:
                $_SESSION['error'] = 'Invalid action';
                header('Location: ../Traveler/reviews.php');
                exit();
        }
    } catch (Exception $e) {
        error_log("Error in form handler: " . $e->getMessage());
        $_SESSION['error'] = 'An error occurred while processing your request';
        header('Location: ../Traveler/reviews.php');
        exit();
    }
}

// Handle AJAX requests
if (isset($_GET['action'])) {
    // Set headers to prevent caching and ensure JSON response
    header('Content-Type: application/json');
    header('Cache-Control: no-cache, no-store, must-revalidate');
    header('Pragma: no-cache');
    header('Expires: 0');

    $reviewController = new ReviewController();
    $response = [];

    try {
        switch ($_GET['action']) {
            case 'update':
                if (isset($_GET['id']) && isset($_GET['rating']) && isset($_GET['comment'])) {
                    $response = $reviewController->updateReview(
                        intval($_GET['id']),
                        intval($_GET['rating']),
                        $_GET['comment']
                    );
                } else {
                    $response = ['error' => 'Missing required parameters'];
                }
                break;

            case 'delete':
                if (isset($_GET['id'])) {
                    $response = $reviewController->deleteReview(intval($_GET['id']));
                } else {
                    $response = ['error' => 'Review ID not provided'];
                }
                break;

            default:
                $response = ['error' => 'Invalid action'];
                break;
        }
    } catch (Exception $e) {
        error_log("Error in AJAX handler: " . $e->getMessage());
        $response = ['error' => 'An error occurred while processing your request: ' . $e->getMessage()];
    }

    echo json_encode($response);
    exit;
}
?> 
