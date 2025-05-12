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