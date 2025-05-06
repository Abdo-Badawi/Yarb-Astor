<?php
require_once '../Controllers/DBController.php';

// Set content type to JSON
header('Content-Type: application/json');

// Create database controller
$db = new DBController();

// Get the action from the request
$action = $_GET['action'] ?? '';

// Process the request based on the action
switch ($action) {
    case 'get-travelers':
        // Get all travelers (users with user_type = 'traveler')
        $db->openConnection();
        $query = "SELECT user_id, CONCAT(first_name, ' ', last_name) as name FROM users WHERE user_type = 'traveler'";
        $travelers = $db->select($query);
        $db->closeConnection();
        
        if ($travelers !== false) {
            echo json_encode([
                'success' => true,
                'travelers' => $travelers
            ]);
        } else {
            echo json_encode([
                'success' => false,
                'message' => 'Failed to fetch travelers'
            ]);
        }
        break;
        
    case 'get-traveler':
        // Get traveler by ID
        $travelerId = $_GET['id'] ?? 0;
        
        if (empty($travelerId)) {
            echo json_encode([
                'success' => false,
                'message' => 'Traveler ID is required'
            ]);
            break;
        }
        
        $db->openConnection();
        $query = "SELECT user_id, CONCAT(first_name, ' ', last_name) as name, email, phone_number, profile_picture FROM users WHERE user_id = ? AND user_type = 'traveler'";
        $traveler = $db->selectPrepared($query, "i", [$travelerId]);
        $db->closeConnection();
        
        if ($traveler !== false && !empty($traveler)) {
            echo json_encode([
                'success' => true,
                'traveler' => $traveler[0]
            ]);
        } else {
            echo json_encode([
                'success' => false,
                'message' => 'Traveler not found'
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
