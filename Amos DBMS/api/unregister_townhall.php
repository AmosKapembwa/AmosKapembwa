<?php
require_once '../includes/session.php';
require_once '../includes/session.php';
session_start();
header('Content-Type: application/json');

require_once "../includes/database.php";
require_once "../models/TownHalls.php";

// Check if user is logged in
if (!isset($_SESSION['user_id'])) {
    echo json_encode([
        'success' => false,
        'message' => 'Please log in to manage your registrations'
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
    $townhalls = new TownHalls($db);
    
    // Check if town hall exists
    $townhall_data = $townhalls->getById($data['town_hall_id']);
    if (!$townhall_data) {
        echo json_encode([
            'success' => false,
            'message' => 'Town hall not found'
        ]);
        exit();
    }

    // Check if user is registered
    $registration = $townhalls->getUserRegistrationStatus($data['town_hall_id'], $_SESSION['user_id']);
    if (!$registration) {
        echo json_encode([
            'success' => false,
            'message' => 'You are not registered for this town hall'
        ]);
        exit();
    }

    // Check if town hall has already started
    if ($townhall_data['status'] === 'ongoing' || $townhall_data['status'] === 'completed') {
        echo json_encode([
            'success' => false,
            'message' => 'Cannot unregister from a town hall that has already started or completed'
        ]);
        exit();
    }

    // Unregister the user
    if ($townhalls->unregister($data['town_hall_id'], $_SESSION['user_id'])) {
        echo json_encode([
            'success' => true,
            'message' => 'Successfully unregistered from the town hall'
        ]);
    } else {
        echo json_encode([
            'success' => false,
            'message' => 'Failed to unregister from the town hall'
        ]);
    }
} catch (Exception $e) {
    echo json_encode([
        'success' => false,
        'message' => 'An error occurred: ' . $e->getMessage()
    ]);
}
