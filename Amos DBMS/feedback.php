<?php
include 'db/config.php';

// Initialize an empty array to hold feedback messages
$feedbackMessages = [];

// Handle form submission
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $feedback_text = $_POST['feedback_text'];
    $user_id = 1; // Replace with actual user ID from session

    // Prepare and execute the insert statement
    $stmt = $pdo->prepare("INSERT INTO feedback (user_id, feedback_text) VALUES (?, ?)");
    if ($stmt->execute([$user_id, $feedback_text])) {
        $feedbackMessages[] = "Feedback submitted successfully!";
    } else {
        $feedbackMessages[] = "Error submitting feedback. Please try again.";
    }
}

// Retrieve all feedback from the database
$stmt = $pdo->query("SELECT feedback_text, created_at FROM feedback ORDER BY created_at DESC");
$allFeedback = $stmt->fetchAll(PDO::FETCH_ASSOC);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Feedback</title>
    <link rel="stylesheet" href="css/styles.css">
</head>
<body>
    <h1>Submit Feedback</h1>
    
    <!-- Display feedback messages -->
    <?php foreach ($feedbackMessages as $message): ?>
        <p><?php echo htmlspecialchars($message); ?></p>
    <?php endforeach; ?>
    
    <!-- Feedback submission form -->
    <form method="post">
        <textarea name="feedback_text" required></textarea>
        <button type="submit">Submit</button>
    </form>

    <h2>Previous Feedback</h2>
    <ul>
        <?php foreach ($allFeedback as $feedback): ?>
            <li>
                <strong><?php echo htmlspecialchars($feedback['created_at']); ?>:</strong>
                <?php echo htmlspecialchars($feedback['feedback_text']); ?>
            </li>
        <?php endforeach; ?>
    </ul>
</body>
</html>