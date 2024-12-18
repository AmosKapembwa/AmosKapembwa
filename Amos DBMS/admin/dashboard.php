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

// Fetch statistics
$total_upcoming = $townhalls->getUpcoming()->rowCount();
$total_past = $townhalls->getPast(5)->rowCount();
$total_registrations = $townhalls->getStats()['total_registrations'];

?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin Dashboard</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body>
    <div class="container">
        <h1 class="mt-4">Admin Dashboard</h1>
        <div class="row">
            <div class="col-md-4">
                <div class="card">
                    <div class="card-body">
                        <h5 class="card-title">Upcoming Town Halls</h5>
                        <h2 class="display-4"><?php echo $total_upcoming; ?></h2>
                    </div>
                </div>
            </div>
            <div class="col-md-4">
                <div class="card">
                    <div class="card-body">
                        <h5 class="card-title">Past Town Halls</h5>
                        <h2 class="display-4"><?php echo $total_past; ?></h2>
                    </div>
                </div>
            </div>
            <div class="col-md-4">
                <div class="card">
                    <div class="card-body">
                        <h5 class="card-title">Total Registrations</h5>
                        <h2 class="display-4"><?php echo $total_registrations; ?></h2>
                    </div>
                </div>
            </div>
        </div>
        <a href="create_townhall.php" class="btn btn-primary mt-3">Create Town Hall</a>
    </div>
</body>
</html>
