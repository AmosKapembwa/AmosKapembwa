<?php
header('Content-Type: application/json');
$input = json_decode(file_get_contents('php://input'), true);

if (isset($input['id'])) {
    $townhallId = $input['id'];
    
    // Database connection (replace with your own connection code)
    $conn = new PDO('mysql:host=localhost;dbname=your_database', 'username', 'password');
    
    // Prepare and execute registration query
    $stmt = $conn->prepare("INSERT INTO registrations (townhall_id) VALUES (:townhall_id)");
    $stmt->bindParam(':townhall_id', $townhallId);
    
    if ($stmt->execute()) {
        echo json_encode(['success' => true]);
    } else {
        echo json_encode(['success' => false, 'message' => 'Database error']);
    }
} else {
    echo json_encode(['success' => false, 'message' => 'Invalid input']);
}
?>
