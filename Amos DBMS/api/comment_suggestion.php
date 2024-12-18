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

    if (!isset($data['suggestion_id']) || !isset($data['comment'])) {
        http_response_code(400);
        echo json_encode(['error' => 'Missing required fields']);
        exit();
    }

    $commentData = [
        'suggestion_id' => $data['suggestion_id'],
        'user_id' => $_SESSION['user']['username'],
        'comment' => $data['comment'],
        'parent_id' => isset($data['parent_id']) ? $data['parent_id'] : null
    ];

    $result = $suggestion->addComment($commentData);

    if ($result) {
        http_response_code(201);
        echo json_encode([
            'message' => 'Comment added successfully'
        ]);
    } else {
        http_response_code(500);
        echo json_encode(['error' => 'Failed to add comment']);
    }

} catch (Exception $e) {
    http_response_code(500);
    echo json_encode(['error' => 'Server error: ' . $e->getMessage()]);
}
?>
