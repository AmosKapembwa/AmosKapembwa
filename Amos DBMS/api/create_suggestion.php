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

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    http_response_code(405);
    echo json_encode(['error' => 'Method not allowed']);
    exit();
}

try {
    $database = new Database();
    $db = $database->getConnection();
    $suggestion = new Suggestion($db);

    $data = json_decode(file_get_contents('php://input'), true);

    if (!isset($data['title']) || !isset($data['description']) || !isset($data['category']) || !isset($data['location'])) {
        http_response_code(400);
        echo json_encode(['error' => 'Missing required fields']);
        exit();
    }

    $suggestionData = [
        'title' => $data['title'],
        'description' => $data['description'],
        'category' => $data['category'],
        'location' => $data['location'],
        'created_by' => $_SESSION['user']['username']
    ];

    $result = $suggestion->create($suggestionData);

    if ($result) {
        http_response_code(201);
        echo json_encode([
            'message' => 'Suggestion created successfully',
            'suggestion_id' => $result
        ]);
    } else {
        http_response_code(500);
        echo json_encode(['error' => 'Failed to create suggestion']);
    }

} catch (Exception $e) {
    http_response_code(500);
    echo json_encode(['error' => 'Server error: ' . $e->getMessage()]);
}
?>
