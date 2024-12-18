<?php
session_start();
header('Content-Type: application/json');

require_once "../includes/database.php";
require_once "../models/TownHalls.php";

// Check if ID is provided
if (!isset($_GET['id'])) {
    echo json_encode([
        'success' => false,
        'message' => 'Town hall ID is required'
    ]);
    exit();
}

try {
    $townhalls = new TownHalls($db);
    
    // Get town hall details
    $townhall_data = $townhalls->getById($_GET['id']);
    if (!$townhall_data) {
        echo json_encode([
            'success' => false,
            'message' => 'Town hall not found'
        ]);
        exit();
    }

    // Get registration status if user is logged in
    if (isset($_SESSION['user_id'])) {
        $registration = $townhalls->getUserRegistrationStatus($_GET['id'], $_SESSION['user_id']);
        $townhall_data['user_registration'] = $registration;
    }

    // Get documents
    $documents = $townhalls->getDocuments($_GET['id']);
    $townhall_data['documents'] = [];
    while ($doc = $documents->fetch(PDO::FETCH_ASSOC)) {
        $townhall_data['documents'][] = $doc;
    }

    // Get updates
    $updates = $townhalls->getUpdates($_GET['id']);
    $townhall_data['updates'] = [];
    while ($update = $updates->fetch(PDO::FETCH_ASSOC)) {
        $townhall_data['updates'][] = $update;
    }

    // Get registrations (only for admins/moderators)
    if (isset($_SESSION['user_id']) && (isAdmin() || isModerator())) {
        $registrations = $townhalls->getRegistrations($_GET['id']);
        $townhall_data['registrations'] = [];
        while ($reg = $registrations->fetch(PDO::FETCH_ASSOC)) {
            $townhall_data['registrations'][] = $reg;
        }
    }

    echo json_encode([
        'success' => true,
        'townhall' => $townhall_data
    ]);
} catch (Exception $e) {
    echo json_encode([
        'success' => false,
        'message' => 'An error occurred: ' . $e->getMessage()
    ]);
}
