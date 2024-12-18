<?php
include 'db/config.php';

// Initialize an empty array to hold voting messages
$votingMessages = [];

// Handle voting submission
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $policy_id = $_POST['policy_id'];
    $user_id = 1; // Replace with actual user ID from session

    // Prepare and execute the update statement to increment votes
    $stmt = $pdo->prepare("UPDATE policies SET votes = votes + 1 WHERE id = ?");
    if ($stmt->execute([$policy_id])) {
        $votingMessages[] = "Vote submitted successfully!";
    } else {
        $votingMessages[] = "Error submitting vote. Please try again.";
    }
}

// Retrieve all policies from the database
$stmt = $pdo->query("SELECT id, title, description, votes FROM policies ORDER BY created_at DESC");
$allPolicies = $stmt->fetchAll(PDO::FETCH_ASSOC);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Vote on Policies</title>
    <link rel="stylesheet" href="css/styles.css">
</head>
<body>
    <h1>Vote on Policies</h1>
    
    <!-- Display voting messages -->
    <?php foreach ($votingMessages as $message): ?>
        <p><?php echo htmlspecialchars($message); ?></p>
    <?php endforeach; ?>

    <h2>Available Policies</h2>
    <ul>
        <?php foreach ($allPolicies as $policy): ?>
            <li>
                <strong><?php echo htmlspecialchars($policy['title']); ?></strong>
                <p><?php echo htmlspecialchars($policy['description']); ?></p>
                <p>Votes: <?php echo htmlspecialchars($policy['votes']); ?></p>
                <form method="post">
                    <input type="hidden" name="policy_id" value="<?php echo $policy['id']; ?>">
                    <button type="submit">Vote</button>
                </form>
            </li>
        <?php endforeach; ?>
    </ul>
</body>
</html>