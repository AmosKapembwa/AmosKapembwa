<?php
require_once "../includes/session.php";
header('Content-Type: application/json');

require_once "../includes/database.php";
require_once "../models/TownHalls.php";

// Check if user is logged in
if (!isset($_SESSION['user_id'])) {
    echo json_encode([
        'success' => false,
        'message' => 'Please log in to register for town halls'
    ]);
    exit();
}

// Get POST data
$data = json_decode(file_get_contents('php://input'), true);

if (!isset($data['town_hall_id'])) {
    echo json_encode([
        'success' => false,
        'message' => 'Town hall ID is required'
    ]);
    exit();
}

try {
    $database = new Database();
    $db = $database->getConnection();
    $townhalls = new TownHalls($db);
    
    // Register the user
    $result = $townhalls->register($data['town_hall_id'], $_SESSION['user_id']);
    echo json_encode($result);

} catch (Exception $e) {
    echo json_encode([
        'success' => false,
        'message' => 'An error occurred: ' . $e->getMessage()
    ]);
}
?>
