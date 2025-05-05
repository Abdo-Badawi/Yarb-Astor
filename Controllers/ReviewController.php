<?php
// Start session at the very beginning
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

// Enable error reporting for development
ini_set('display_errors', 1);
ini_set('log_errors', 1);
error_reporting(E_ALL);

include_once '../Controllers/DBController.php';
include_once '../Controllers/AuthController.php';


class ReviewController {
    private $db;

    public function __construct() {
        $this->db = new DBController();
    }

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

            return count($result) > 0 ? $result[0] : null;
        } catch (Exception $e) {
            error_log("Error in getReviewById: " . $e->getMessage());
            $this->db->closeConnection();
            return ['error' => 'An error occurred while fetching the review'];
        }
    }

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
            $checkStmt = $this->db->conn->prepare($checkQuery);
            if (!$checkStmt) {
                $this->db->closeConnection();
                return ['error' => 'Failed to prepare check statement: ' . $this->db->conn->error];
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
            $updateStmt = $this->db->conn->prepare($updateQuery);
            if (!$updateStmt) {
                $this->db->closeConnection();
                return ['error' => 'Failed to prepare update statement: ' . $this->db->conn->error];
            }

            $updateStmt->bind_param("isi", $rating, $comment, $reviewId);
            
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

    public function deleteReview($reviewId) {
        if (!isset($_SESSION['userID'])) {
            return ['error' => 'User not logged in'];
        }

        if (!$this->db->openConnection()) {
            return ['error' => 'Database connection failed'];
        }

        try {
            // First check if the review exists and belongs to the current user
            $checkQuery = "SELECT sender_id, status FROM review WHERE review_id = ?";
            $checkStmt = $this->db->conn->prepare($checkQuery);

            if (!$checkStmt) {
                $this->db->closeConnection();
                return ['error' => 'Failed to prepare check statement: ' . $this->db->conn->error];
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
            if ($review['status'] === 'reported') {
                $this->db->closeConnection();
                return ['error' => 'Cannot delete a reported review'];
            }

            // Delete the review
            $deleteQuery = "DELETE FROM review WHERE review_id = ?";
            $deleteStmt = $this->db->conn->prepare($deleteQuery);
            if (!$deleteStmt) {
                $this->db->closeConnection();
                return ['error' => 'Failed to prepare delete statement: ' . $this->db->conn->error];
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