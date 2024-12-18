<?php
// Connect to the database
$conn = new mysqli('localhost', 'root', '', 'abc');

if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// Check if the form has been submitted
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // Get form data
    $title = $conn->real_escape_string($_POST['title']);
    $description = $conn->real_escape_string($_POST['description']);
    $submission_date = date('Y-m-d H:i:s'); // Current timestamp

    // Insert into Feedback/Suggestions table with default status
    $sql = "INSERT INTO feedback (title, description, submission_date, status)
            VALUES ('$title', '$description', '$submission_date', 'open')";

    if ($conn->query($sql) === TRUE) {
        $message = "Feedback submitted successfully!";
    } else {
        $message = "Error: " . $conn->error;
    }

    // Close connection
    $conn->close();
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Submit Feedback/Suggestions</title>
    <link rel="stylesheet" href="css/styles-suggestion.css">
    <link rel="stylesheet" href="css/style3.css">
    <!-- Bootstrap CSS for styling -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/bootstrap-icons/1.10.5/font/bootstrap-icons.min.css">
</head>
<body>

    <!-- Main Content Section -->
    <div class="container mt-5">
    <button id="back2home" style="background: linear-gradient(rgba(0, 0, 50, 0.7), rgba(0, 0, 50, 0.7)); border: none; border-radius: 5px; padding: 12px 24px; font-size: 20px; font-weight: bold; ">
    <a href="home.php" style="color: yellow; text-decoration: none;">Back to Home</a></button>
        <h2 class="heading text-center mb-4">Submit Feedback/Suggestions</h2>

        <!-- Display success or error message -->
        <?php if (isset($message)): ?>
            <div class="alert <?php echo ($message === "Feedback submitted successfully!") ? 'alert-success' : 'alert-danger'; ?> text-center">
                <?php echo $message; ?>
            </div>
        <?php endif; ?>

        <div class="form-wrapper mx-auto" style="max-width: 600px;">
            <form action="" method="POST">
                <!-- Title -->
                <div class="form-group mb-3">
                    <input type="text" class="form-control" name="title" placeholder="Name" required>
                </div>
                <div class="form-group mb-3">
                    <input type="text" class="form-control" name="title" placeholder="Email" required>
                </div>
                <div class="form-group mb-3">
                    <input type="text" class="form-control" name="title" placeholder="Title" required>
                </div>
                <!-- Description -->
                <div class="form-group mb-3">
                    <textarea class="form-control" name="description" placeholder="Write your description here..." rows="4" required></textarea>
                </div>
                <!-- Submit Button -->
                <div class="text-center">
                    <button type="submit" class="btn btn-warning">Submit Feedback</button>
                </div>
            </form>
        </div>
    </div>
   
    <!-- Bootstrap JS for Navbar functionality -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
