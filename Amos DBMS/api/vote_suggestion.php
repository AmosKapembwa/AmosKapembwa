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

    if (!isset($data['suggestion_id']) || !isset($data['vote_type'])) {
        http_response_code(400);
        echo json_encode(['error' => 'Missing required fields']);
        exit();
    }

    if (!in_array($data['vote_type'], ['upvote', 'downvote'])) {
        http_response_code(400);
        echo json_encode(['error' => 'Invalid vote type']);
        exit();
    }

    $result = $suggestion->vote(
        $data['suggestion_id'],
        $_SESSION['user']['username'],
        $data['vote_type']
    );

    if ($result) {
        $currentVote = $suggestion->getUserVote($data['suggestion_id'], $_SESSION['user']['username']);
        http_response_code(200);
        echo json_encode([
            'message' => 'Vote recorded successfully',
            'current_vote' => $currentVote
        ]);
    } else {
        http_response_code(500);
        echo json_encode(['error' => 'Failed to record vote']);
    }

} catch (Exception $e) {
    http_response_code(500);
    echo json_encode(['error' => 'Server error: ' . $e->getMessage()]);
}
?>
