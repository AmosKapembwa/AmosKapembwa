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

try {
    $database = new Database();
    $db = $database->getConnection();
    $suggestion = new Suggestion($db);

    // Build filters from query parameters
    $filters = array();
    if (isset($_GET['category'])) {
        $filters['category'] = $_GET['category'];
    }
    if (isset($_GET['status'])) {
        $filters['status'] = $_GET['status'];
    }
    if (isset($_GET['location'])) {
        $filters['location'] = $_GET['location'];
    }
    if (isset($_GET['search'])) {
        $filters['search'] = $_GET['search'];
    }

    // Build sort parameters
    $sort = array();
    if (isset($_GET['sort_by']) && isset($_GET['sort_direction'])) {
        $sort['column'] = $_GET['sort_by'];
        $sort['direction'] = $_GET['sort_direction'];
    }

    $stmt = $suggestion->read($filters, $sort);
    $suggestions = array();

    while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
        // Add user's vote if any
        $row['user_vote'] = $suggestion->getUserVote($row['suggestion_id'], $_SESSION['user']['username']);
        $suggestions[] = $row;
    }

    // Get statistics
    $stats = $suggestion->getStats();

    echo json_encode([
        'suggestions' => $suggestions,
        'stats' => $stats,
        'total' => count($suggestions)
    ]);

} catch (Exception $e) {
    http_response_code(500);
    echo json_encode(['error' => 'Server error: ' . $e->getMessage()]);
}
?>
