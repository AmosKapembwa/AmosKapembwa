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
    $required_fields = ['title', 'description', 'category', 'location', 'start_date', 'end_date', 'budget', 'impact_area'];
    foreach ($required_fields as $field) {
        if (!isset($_POST[$field]) || empty($_POST[$field])) {
            throw new Exception("Missing required field: $field");
        }
    }

    // Prepare initiative data
    $initiative_data = [
        'title' => $_POST['title'],
        'description' => $_POST['description'],
        'category' => $_POST['category'],
        'location' => $_POST['location'],
        'start_date' => $_POST['start_date'],
        'end_date' => $_POST['end_date'],
        'budget' => $_POST['budget'],
        'impact_area' => $_POST['impact_area'],
        'status' => 'proposed',
        'progress_percentage' => 0,
        'created_by' => $_SESSION['username'],
        'created_at' => date('Y-m-d H:i:s')
    ];

    // Create initiative
    $initiative_id = $initiative->create($initiative_data);

    // Handle document uploads if any
    if (!empty($_FILES['documents']['name'][0])) {
        $upload_dir = "../uploads/initiatives/$initiative_id/";
        if (!file_exists($upload_dir)) {
            mkdir($upload_dir, 0777, true);
        }

        $allowed_types = ['pdf', 'doc', 'docx', 'xls', 'xlsx'];
        $uploaded_files = [];

        foreach ($_FILES['documents']['tmp_name'] as $key => $tmp_name) {
            $file_name = $_FILES['documents']['name'][$key];
            $file_ext = strtolower(pathinfo($file_name, PATHINFO_EXTENSION));

            if (!in_array($file_ext, $allowed_types)) {
                continue;
            }

            $new_file_name = uniqid() . '.' . $file_ext;
            $destination = $upload_dir . $new_file_name;

            if (move_uploaded_file($tmp_name, $destination)) {
                $uploaded_files[] = [
                    'initiative_id' => $initiative_id,
                    'file_name' => $file_name,
                    'file_path' => "uploads/initiatives/$initiative_id/$new_file_name",
                    'uploaded_at' => date('Y-m-d H:i:s')
                ];
            }
        }

        // Save document records to database
        if (!empty($uploaded_files)) {
            $initiative->addDocuments($initiative_id, $uploaded_files);
        }
    }

    echo json_encode([
        'success' => true,
        'message' => 'Initiative created successfully',
        'initiative_id' => $initiative_id
    ]);

} catch (Exception $e) {
    echo json_encode([
        'success' => false,
        'message' => $e->getMessage()
    ]);
}
?>
