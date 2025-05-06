<?php
require_once '../Controllers/DBController.php';

// Set content type to JSON
header('Content-Type: application/json');

// Check if action is provided
if (!isset($_GET['action'])) {
    echo json_encode([
        'success' => false,
        'message' => 'No action specified'
    ]);
    exit;
}

// Create database controller
$db = new DBController();

// Get the action
$action = $_GET['action'];

// Handle different actions
switch ($action) {
    case 'get':
        // Get card by ID
        if (!isset($_GET['id'])) {
            echo json_encode([
                'success' => false,
                'message' => 'Card ID is required'
            ]);
            exit;
        }

        $cardId = (int)$_GET['id'];
        $card = $controller->getCardById($cardId);

        if ($card) {
            echo json_encode([
                'success' => true,
                'card' => $card
            ]);
        } else {
            echo json_encode([
                'success' => false,
                'message' => 'Card not found'
            ]);
        }
        break;

    case 'get-by-traveler':
        // Get cards by traveler ID
        if (!isset($_GET['traveler_id'])) {
            echo json_encode([
                'success' => false,
                'message' => 'Traveler ID is required'
            ]);
            exit;
        }

        $travelerId = (int)$_GET['traveler_id'];
        $cards = $controller->getCardsByTravelerId($travelerId);

        echo json_encode([
            'success' => true,
            'cards' => $cards
        ]);
        break;

    case 'create':
        // Create a new card
        $data = json_decode(file_get_contents('php://input'), true);

        if (!$data) {
            echo json_encode([
                'success' => false,
                'message' => 'Invalid data provided'
            ]);
            exit;
        }

        // Validate required fields
        if (empty($data['card_number']) || empty($data['expiry_date']) || empty($data['cvv']) || empty($data['card_holder_name']) || empty($data['traveler_id'])) {
            echo json_encode([
                'success' => false,
                'message' => 'Card number, expiry date, CVV, card holder name, and traveler ID are required'
            ]);
            exit;
        }

        $result = $controller->createCard($data);

        if ($result) {
            echo json_encode([
                'success' => true,
                'message' => 'Card created successfully'
            ]);
        } else {
            echo json_encode([
                'success' => false,
                'message' => 'Failed to create card'
            ]);
        }
        break;

    case 'update':
        // Update an existing card
        if (!isset($_GET['id'])) {
            echo json_encode([
                'success' => false,
                'message' => 'Card ID is required'
            ]);
            exit;
        }

        $cardId = (int)$_GET['id'];
        $data = json_decode(file_get_contents('php://input'), true);

        if (!$data) {
            echo json_encode([
                'success' => false,
                'message' => 'Invalid data provided'
            ]);
            exit;
        }

        $result = $controller->updateCard($cardId, $data);

        if ($result) {
            echo json_encode([
                'success' => true,
                'message' => 'Card updated successfully'
            ]);
        } else {
            echo json_encode([
                'success' => false,
                'message' => 'Failed to update card'
            ]);
        }
        break;

    case 'delete':
        // Delete a card
        if (!isset($_GET['id'])) {
            echo json_encode([
                'success' => false,
                'message' => 'Card ID is required'
            ]);
            exit;
        }

        $cardId = (int)$_GET['id'];
        $result = $controller->deleteCard($cardId);

        if ($result) {
            echo json_encode([
                'success' => true,
                'message' => 'Card deleted successfully'
            ]);
        } else {
            echo json_encode([
                'success' => false,
                'message' => 'Failed to delete card'
            ]);
        }
        break;

    default:
        echo json_encode([
            'success' => false,
            'message' => 'Invalid action'
        ]);
        break;
}
