<?php
session_start();
header('Content-Type: application/json');

require_once "../config/database.php";
require_once "../models/Initiative.php";

// Check if user is logged in and has appropriate role
if (!isset($_SESSION['username']) || !in_array($_SESSION['role'], ['admin', 'moderator'])) {
    echo json_encode(['success' => false, 'message' => 'Unauthorized access']);
    exit();
}

try {
    $database = new Database();
    $db = $database->getConnection();
    $initiative = new Initiative($db);

    // Validate required fields
    if (!isset($_POST['initiative_id']) || !isset($_POST['progress']) || !isset($_POST['status']) || !isset($_POST['update_notes'])) {
        throw new Exception('Missing required fields');
    }

    $initiative_id = $_POST['initiative_id'];
    $progress = intval($_POST['progress']);
    $status = $_POST['status'];
    $update_notes = $_POST['update_notes'];

    // Validate progress value
    if ($progress < 0 || $progress > 100) {
        throw new Exception('Invalid progress value. Must be between 0 and 100');
    }

    // Validate status
    $allowed_statuses = ['proposed', 'in_progress', 'completed'];
    if (!in_array($status, $allowed_statuses)) {
        throw new Exception('Invalid status value');
    }

    // Update initiative
    $update_data = [
        'progress_percentage' => $progress,
        'status' => $status,
        'updated_by' => $_SESSION['username'],
        'updated_at' => date('Y-m-d H:i:s')
    ];

    if ($initiative->update($initiative_id, $update_data)) {
        // Log the update
        $initiative->addUpdateLog($initiative_id, [
            'progress_percentage' => $progress,
            'status' => $status,
            'notes' => $update_notes,
            'updated_by' => $_SESSION['username'],
            'updated_at' => date('Y-m-d H:i:s')
        ]);

        echo json_encode([
            'success' => true,
            'message' => 'Initiative updated successfully'
        ]);
    } else {
        throw new Exception('Failed to update initiative');
    }

} catch (Exception $e) {
    echo json_encode([
        'success' => false,
        'message' => $e->getMessage()
    ]);
}
?>
