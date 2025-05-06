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
    case 'get-all':
        // Get filters from request
        $whereConditions = [];
        $params = [];

        if (isset($_GET['status']) && $_GET['status']) {
            $whereConditions[] = "pvr.status = ?";
            $params[] = $_GET['status'];
        }

        if (isset($_GET['priority']) && $_GET['priority']) {
            $whereConditions[] = "pvr.priority = ?";
            $params[] = $_GET['priority'];
        }

        if (isset($_GET['issue_type']) && $_GET['issue_type']) {
            $whereConditions[] = "pvr.issue_type = ?";
            $params[] = $_GET['issue_type'];
        }

        if (isset($_GET['traveler_id']) && $_GET['traveler_id']) {
            $whereConditions[] = "pvr.traveler_id = ?";
            $params[] = $_GET['traveler_id'];
        }

        if (isset($_GET['booking_id']) && $_GET['booking_id']) {
            $whereConditions[] = "pvr.booking_id LIKE ?";
            $params[] = '%' . $_GET['booking_id'] . '%';
        }

        if (isset($_GET['transaction_id']) && $_GET['transaction_id']) {
            $whereConditions[] = "pvr.transaction_id LIKE ?";
            $params[] = '%' . $_GET['transaction_id'] . '%';
        }

        // Build the query
        $query = "SELECT pvr.*, u.first_name, u.last_name
                  FROM payment_verification_requests pvr
                  LEFT JOIN users u ON pvr.traveler_id = u.user_id";

        // Add WHERE clause if there are conditions
        if (!empty($whereConditions)) {
            $query .= " WHERE " . implode(" AND ", $whereConditions);
        }

        // Add order by
        $query .= " ORDER BY
                  CASE pvr.priority
                    WHEN 'urgent' THEN 1
                    WHEN 'high' THEN 2
                    WHEN 'normal' THEN 3
                    WHEN 'low' THEN 4
                  END,
                  CASE pvr.status
                    WHEN 'new' THEN 1
                    WHEN 'pending' THEN 2
                    WHEN 'in_progress' THEN 3
                    WHEN 'resolved' THEN 4
                    WHEN 'closed' THEN 5
                  END,
                  pvr.created_at DESC";

        // Execute the query
        $db->openConnection();
        if (empty($params)) {
            $requests = $db->select($query);
        } else {
            $types = str_repeat('s', count($params));
            $requests = $db->selectPrepared($query, $types, $params);
        }
        $db->closeConnection();

        // Return the requests
        echo json_encode([
            'success' => true,
            'requests' => $requests
        ]);
        break;

    case 'get':
        // Get request ID from request
        $requestId = $_GET['id'] ?? 0;

        if (empty($requestId)) {
            echo json_encode([
                'success' => false,
                'message' => 'Request ID is required'
            ]);
            break;
        }

        // Get the request
        $db->openConnection();
        $query = "SELECT pvr.*, u.first_name, u.last_name
                  FROM payment_verification_requests pvr
                  LEFT JOIN users u ON pvr.traveler_id = u.user_id
                  WHERE pvr.request_id = ?";
        $result = $db->selectPrepared($query, "i", [$requestId]);
        $db->closeConnection();

        if ($result && !empty($result)) {
            echo json_encode([
                'success' => true,
                'request' => $result[0]
            ]);
        } else {
            echo json_encode([
                'success' => false,
                'message' => 'Request not found'
            ]);
        }
        break;

    case 'create':
        // Get request data from POST
        $data = json_decode(file_get_contents('php://input'), true);

        if (empty($data)) {
            echo json_encode([
                'success' => false,
                'message' => 'No data provided'
            ]);
            break;
        }

        // Validate required fields
        if (empty($data['traveler_id']) || empty($data['issue_type']) ||
            empty($data['issue_description']) || empty($data['action_required'])) {
            echo json_encode([
                'success' => false,
                'message' => 'Missing required fields'
            ]);
            break;
        }

        // Create the request
        $db->openConnection();
        $query = "INSERT INTO payment_verification_requests
                  (traveler_id, booking_id, transaction_id, issue_type, issue_description, action_required, status, priority)
                  VALUES (?, ?, ?, ?, ?, ?, ?, ?)";

        $params = [
            $data['traveler_id'],
            $data['booking_id'] ?? null,
            $data['transaction_id'] ?? null,
            $data['issue_type'],
            $data['issue_description'],
            $data['action_required'],
            $data['status'] ?? 'new',
            $data['priority'] ?? 'normal'
        ];

        $types = "isssssss";
        $result = $db->insert($query, $types, $params);
        $requestId = $db->conn->insert_id;
        $db->closeConnection();

        if ($result) {
            echo json_encode([
                'success' => true,
                'request_id' => $requestId,
                'message' => 'Request created successfully'
            ]);
        } else {
            echo json_encode([
                'success' => false,
                'message' => 'Failed to create request'
            ]);
        }
        break;

    case 'update':
        // Get request ID from request
        $requestId = $_GET['id'] ?? 0;

        if (empty($requestId)) {
            echo json_encode([
                'success' => false,
                'message' => 'Request ID is required'
            ]);
            break;
        }

        // Get request data from POST
        $data = json_decode(file_get_contents('php://input'), true);

        if (empty($data)) {
            echo json_encode([
                'success' => false,
                'message' => 'No data provided'
            ]);
            break;
        }

        // Build the update query
        $updateFields = [];
        $params = [];

        // Build the update fields and parameters
        foreach ($data as $field => $value) {
            if (in_array($field, ['traveler_id', 'booking_id', 'transaction_id', 'issue_type', 'issue_description', 'action_required', 'status', 'priority'])) {
                $updateFields[] = "$field = ?";
                $params[] = $value;
            }
        }

        if (empty($updateFields)) {
            echo json_encode([
                'success' => false,
                'message' => 'No valid fields to update'
            ]);
            break;
        }

        // Add the request ID to the parameters
        $params[] = $requestId;

        // Update the request
        $db->openConnection();
        $query = "UPDATE payment_verification_requests SET " . implode(", ", $updateFields) . " WHERE request_id = ?";

        // Prepare types string
        $types = '';
        foreach ($params as $value) {
            if (is_int($value)) {
                $types .= 'i';
            } elseif (is_float($value)) {
                $types .= 'd';
            } else {
                $types .= 's';
            }
        }

        $result = $db->update($query, $types, $params);
        $db->closeConnection();

        if ($result) {
            echo json_encode([
                'success' => true,
                'message' => 'Request updated successfully'
            ]);
        } else {
            echo json_encode([
                'success' => false,
                'message' => 'Failed to update request'
            ]);
        }
        break;

    case 'delete':
        // Get request ID from request
        $requestId = $_GET['id'] ?? 0;

        if (empty($requestId)) {
            echo json_encode([
                'success' => false,
                'message' => 'Request ID is required'
            ]);
            break;
        }

        // Delete the request
        $db->openConnection();
        $query = "DELETE FROM payment_verification_requests WHERE request_id = ?";
        $result = $db->update($query, "i", [$requestId]);
        $db->closeConnection();

        if ($result) {
            echo json_encode([
                'success' => true,
                'message' => 'Request deleted successfully'
            ]);
        } else {
            echo json_encode([
                'success' => false,
                'message' => 'Failed to delete request'
            ]);
        }
        break;

    case 'update-status':
        // Get request ID and status from request
        $requestId = $_GET['id'] ?? 0;
        $status = $_GET['status'] ?? '';

        if (empty($requestId) || empty($status)) {
            echo json_encode([
                'success' => false,
                'message' => 'Request ID and status are required'
            ]);
            break;
        }

        // Validate status
        $validStatuses = ['new', 'pending', 'in_progress', 'resolved', 'closed'];
        if (!in_array($status, $validStatuses)) {
            echo json_encode([
                'success' => false,
                'message' => 'Invalid status'
            ]);
            break;
        }

        // Update the request status
        $db->openConnection();
        $query = "UPDATE payment_verification_requests SET status = ? WHERE request_id = ?";
        $result = $db->update($query, "si", [$status, $requestId]);
        $db->closeConnection();

        if ($result) {
            echo json_encode([
                'success' => true,
                'message' => 'Request status updated successfully'
            ]);
        } else {
            echo json_encode([
                'success' => false,
                'message' => 'Failed to update request status'
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
