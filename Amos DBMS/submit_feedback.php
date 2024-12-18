<?php
// Connect to the database
$conn = new mysqli('localhost', 'root', '', 'abc');

if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// Get form data
$title = $conn->real_escape_string($_POST['title']);
$description = $conn->real_escape_string($_POST['description']);
$submission_date = date('Y-m-d H:i:s'); // Current timestamp

// Insert into Feedback/Suggestions table with default status
$sql = "INSERT INTO feedback (title, description, submission_date, status)
        VALUES ('$title', '$description', '$submission_date', 'open')";

if ($conn->query($sql) === TRUE) {
    echo "Feedback submitted successfully!";
} else {
    echo "Error: " . $sql . "<br>" . $conn->error;
}

// Close connection
$conn->close();
?>
