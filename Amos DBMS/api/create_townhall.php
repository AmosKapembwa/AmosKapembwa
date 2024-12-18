<?php
require_once "../includes/session.php";
session_start();
header('Content-Type: application/json');

require_once "../includes/database.php";
require_once "../models/TownHalls.php";

// Check if user is logged in and is admin
if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'admin') {
    echo json_encode([
        'success' => false,
        'message' => 'Unauthorized access'
    ]);
    exit();
}

try {
    $database = new Database();
    $db = $database->getConnection();
    $townhalls = new TownHalls($db);

    // Validate required fields
    $required_fields = ['title', 'description', 'scheduled_date', 'duration_minutes', 'meeting_link'];
    foreach ($required_fields as $field) {
        if (!isset($_POST[$field]) || empty($_POST[$field])) {
            echo json_encode([
                'success' => false,
                'message' => 'Missing required field: ' . $field
            ]);
            exit();
        }
    }

    // Prepare data for creation
    $data = [
        'title' => $_POST['title'],
        'description' => $_POST['description'],
        'scheduled_date' => $_POST['scheduled_date'],
        'duration_minutes' => (int)$_POST['duration_minutes'],
        'meeting_link' => $_POST['meeting_link'],
        'created_by' => $_SESSION['user_id']
    ];

    $townhall_id = $townhalls->create($data);
    if ($townhall_id) {
        echo json_encode([
            'success' => true,
            'message' => 'Town hall created successfully',
            'townhall_id' => $townhall_id
        ]);
    } else {
        echo json_encode([
            'success' => false,
            'message' => 'Failed to create town hall'
        ]);
    }
} catch (Exception $e) {
    echo json_encode([
        'success' => false,
        'message' => 'An error occurred: ' . $e->getMessage()
    ]);
}
?>
