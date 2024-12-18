<?php
require_once '../includes/session.php';
require_once '../includes/database.php';
require_once '../models/TownHalls.php';

// Check if the user is logged in and is an admin
if (!isLoggedIn() || !isAdmin()) {
    header('Location: ../index.php');
    exit();
}

$database = new Database();
$db = $database->getConnection();
$townhalls = new TownHalls($db);

// Fetch upcoming and past town halls
$upcoming_townhalls = $townhalls->getUpcoming();
$past_townhalls = $townhalls->getPast(5);

?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Manage Town Halls</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body>
    <div class="container">
        <h1 class="mt-4">Manage Town Halls</h1>
        <h2 class="h4">Upcoming Town Halls</h2>
        <table class="table table-hover">
            <thead>
                <tr>
                    <th>Title</th>
                    <th>Date</th>
                    <th>Actions</th>
                </tr>
            </thead>
            <tbody>
                <?php while ($row = $upcoming_townhalls->fetch(PDO::FETCH_ASSOC)): ?>
                <tr>
                    <td><?php echo htmlspecialchars($row['title']); ?></td>
                    <td><?php echo date('M j, Y g:i A', strtotime($row['scheduled_date'])); ?></td>
                    <td>
                        <a href="edit_townhall.php?id=<?php echo $row['town_hall_id']; ?>" class="btn btn-warning btn-sm">Edit</a>
                        <button class="btn btn-danger btn-sm delete-btn" data-id="<?php echo $row['town_hall_id']; ?>">Delete</button>
                    </td>
                </tr>
                <?php endwhile; ?>
            </tbody>
        </table>

        <h2 class="h4">Past Town Halls</h2>
        <table class="table table-hover">
            <thead>
                <tr>
                    <th>Title</th>
                    <th>Date</th>
                </tr>
            </thead>
            <tbody>
                <?php while ($row = $past_townhalls->fetch(PDO::FETCH_ASSOC)): ?>
                <tr>
                    <td><?php echo htmlspecialchars($row['title']); ?></td>
                    <td><?php echo date('M j, Y g:i A', strtotime($row['scheduled_date'])); ?></td>
                </tr>
                <?php endwhile; ?>
            </tbody>
        </table>
    </div>
    <script>
        document.querySelectorAll('.delete-btn').forEach(button => {
            button.addEventListener('click', function() {
                const townhallId = this.dataset.id;
                if (confirm('Are you sure you want to delete this town hall?')) {
                    fetch('api/delete_townhall.php', {
                        method: 'POST',
                        headers: {
                            'Content-Type': 'application/json'
                        },
                        body: JSON.stringify({ id: townhallId })
                    }).then(response => response.json()).then(data => {
                        if (data.success) {
                            location.reload();
                        } else {
                            alert('Failed to delete town hall.');
                        }
                    });
                }
            });
        });
    </script>
</body>
</html>
