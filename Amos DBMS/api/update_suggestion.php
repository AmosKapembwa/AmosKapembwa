<?php
session_start();
require_once '../config/database.php';
require_once '../models/Suggestion.php';

header('Content-Type: application/json');

if (!isset($_SESSION['user'])) {
    http_response_code(401);
    echo json_encode(['error' => 'Unauthorized access']);
    exit();
}

if ($_SERVER['REQUEST_METHOD'] !== 'PUT' && $_SERVER['REQUEST_METHOD'] !== 'POST') {
    http_response_code(405);
    echo json_encode(['error' => 'Method not allowed']);
    exit();
}

try {
    $database = new Database();
    $db = $database->getConnection();
    $suggestion = new Suggestion($db);

    $data = json_decode(file_get_contents('php://input'), true);

    if (!isset($data['suggestion_id'])) {
        http_response_code(400);
        echo json_encode(['error' => 'Missing suggestion ID']);
        exit();
    }

    // Get the current suggestion to check permissions
    $current = $suggestion->getById($data['suggestion_id']);
    if (!$current) {
        http_response_code(404);
        echo json_encode(['error' => 'Suggestion not found']);
        exit();
    }

    // Only allow updates by the creator or admin
    if ($current['created_by'] !== $_SESSION['user']['username'] && $_SESSION['user']['role'] !== 'admin') {
        http_response_code(403);
        echo json_encode(['error' => 'Permission denied']);
        exit();
    }

    $updateData = array_intersect_key($data, array_flip(['title', 'description', 'category', 'location', 'status']));
    $updateData['updated_by'] = $_SESSION['user']['username'];

    if (empty($updateData)) {
        http_response_code(400);
        echo json_encode(['error' => 'No valid fields to update']);
        exit();
    }

    $result = $suggestion->update($data['suggestion_id'], $updateData);

    if ($result) {
        http_response_code(200);
        echo json_encode([
            'message' => 'Suggestion updated successfully',
            'suggestion_id' => $data['suggestion_id']
        ]);
    } else {
        http_response_code(500);
        echo json_encode(['error' => 'Failed to update suggestion']);
    }

} catch (Exception $e) {
    http_response_code(500);
    echo json_encode(['error' => 'Server error: ' . $e->getMessage()]);
}
?>
