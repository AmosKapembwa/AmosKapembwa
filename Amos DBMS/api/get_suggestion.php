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

if (!isset($_GET['id'])) {
    http_response_code(400);
    echo json_encode(['error' => 'Missing suggestion ID']);
    exit();
}

try {
    $database = new Database();
    $db = $database->getConnection();
    $suggestion = new Suggestion($db);

    $suggestion_id = $_GET['id'];
    $suggestionData = $suggestion->getById($suggestion_id);

    if (!$suggestionData) {
        http_response_code(404);
        echo json_encode(['error' => 'Suggestion not found']);
        exit();
    }

    // Add user's vote if any
    $suggestionData['user_vote'] = $suggestion->getUserVote($suggestion_id, $_SESSION['user']['username']);

    // Get comments
    $commentsStmt = $suggestion->getComments($suggestion_id);
    $comments = array();
    while ($comment = $commentsStmt->fetch(PDO::FETCH_ASSOC)) {
        $comments[] = $comment;
    }
    $suggestionData['comments'] = $comments;

    echo json_encode($suggestionData);

} catch (Exception $e) {
    http_response_code(500);
    echo json_encode(['error' => 'Server error: ' . $e->getMessage()]);
}
?>
