<?php
// Database connection
$servername = "localhost";
$username = "root";
$password = "";
$dbname = "abc";

$conn = new mysqli($servername, $username, $password, $dbname);

if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// User ID (use session or login system for real applications)
$userId = 1; // Example user ID; replace with actual user authentication

// Initialize messages
$messages = [];

// Poll questions
$polls = [
    1 => "How do you rate the current state of public transportation?",
    2 => "Should local government invest more in renewable energy?",
    3 => "Would you support stricter regulations on plastic use?",
    4 => "Should voting be mandatory in elections?",
    5 => "Do you believe the government should increase taxes for the wealthy?"
];

// Debugging: Inspect $_POST
// file_put_contents('debug.txt', print_r($_POST, true), FILE_APPEND);

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Validate poll_id and vote
    $pollId = filter_input(INPUT_POST, 'poll_id', FILTER_VALIDATE_INT);
    $vote = filter_input(INPUT_POST, 'vote', FILTER_SANITIZE_STRING);

    if (!$pollId || !$vote) {
        die("Invalid poll ID or vote value.");
    }

    // Check if the user has already voted
    $checkQuery = $conn->prepare("SELECT * FROM votes WHERE user_id = ? AND poll_id = ?");
    $checkQuery->bind_param("ii", $userId, $pollId);
    $checkQuery->execute();
    $result = $checkQuery->get_result();

    if ($result->num_rows > 0) {
        $messages[$pollId] = "You have already voted in this poll.";
    } else {
        // Insert the vote
        $insertQuery = $conn->prepare("INSERT INTO votes (user_id, poll_id, vote) VALUES (?, ?, ?)");
        $insertQuery->bind_param("iis", $userId, $pollId, $vote);
        if ($insertQuery->execute()) {
            $messages[$pollId] = "Thank you for voting!";
        } else {
            $messages[$pollId] = "An error occurred while recording your vote.";
        }
    }
}

// Fetch vote counts
$voteCounts = [];
foreach ($polls as $pollId => $question) {
    $countQuery = $conn->prepare("SELECT vote, COUNT(*) as count FROM votes WHERE poll_id = ? GROUP BY vote");
    $countQuery->bind_param("i", $pollId);
    $countQuery->execute();
    $result = $countQuery->get_result();

    $voteCounts[$pollId] = [];
    while ($row = $result->fetch_assoc()) {
        $voteCounts[$pollId][$row['vote']] = $row['count'];
    }
}
?>


<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Citizen Participation - Polls</title>
    <link rel="stylesheet" href="css/polls.css">
</head>
<body>
    <main>
        <section id="polls" class="polls section light-background">
            <center>
            <div class="container section-title">
            <button id="back2home" style="background: linear-gradient(rgba(0, 0, 50, 0.7), rgba(0, 0, 50, 0.7)); border: none; border-radius: 5px; padding: 12px 24px; font-size: 20px; font-weight: bold; ">
            <a href="home.php" style="color: yellow; text-decoration: none;">Back to Home</a>
            </button>
                <h1>Citizen Polls</h1>
                <h3>Your voice matters. Participate in our polls and help shape the future.</h3>
            </div>
            </center>

            <div class="container">
                <?php foreach ($polls as $pollId => $question): ?>
                    <div class="poll-item">
                        <h3><?= htmlspecialchars($question) ?></h3>
                        <form action="polls.php" method="POST">
                            <input type="hidden" name="poll_id" value="<?= $pollId ?>">
                            <input type="radio" name="vote" value="Excellent" required> Excellent<br>
                            <input type="radio" name="vote" value="Good"> Good<br>
                            <input type="radio" name="vote" value="Average"> Average<br>
                            <input type="radio" name="vote" value="Poor"> Poor<br>
                            <button type="submit">Submit</button>
                        </form>

                        <?php if (isset($messages[$pollId])): ?>
                            <p class="thank-you-message"><?= htmlspecialchars($messages[$pollId]) ?></p>
                        <?php endif; ?>

                        <h4>Current Results:</h4>
                        <ul>
                            <?php foreach ($voteCounts[$pollId] as $option => $count): ?>
                                <li><?= htmlspecialchars($option) ?>: <?= $count ?> votes</li>
                            <?php endforeach; ?>
                        </ul>
                    </div>
                <?php endforeach; ?>
            </div>
        </section>
    </main>
</body>
</html>
