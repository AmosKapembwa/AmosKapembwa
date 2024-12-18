<?php
error_reporting(E_ALL);
ini_set('display_errors', 1);

require_once 'includes/session.php';
require_once 'includes/database.php';
require_once 'models/TownHalls.php';

$townhalls = new TownHalls($db);
$upcoming_townhalls = $townhalls->getUpcoming();
$past_townhalls = $townhalls->getPast(5); // Get last 5 past town halls
$stats = $townhalls->getStats();

// Set security headers
header("X-Frame-Options: DENY");
header("X-XSS-Protection: 1; mode=block");
header("X-Content-Type-Options: nosniff");
header("Referrer-Policy: strict-origin-when-cross-origin");
?>

<?php include 'header.php'; ?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="description" content="Participate in local town hall meetings, engage with your community, and stay informed about important civic discussions.">
    <meta name="robots" content="index, follow">
    <meta name="theme-color" content="#FFA500">
    <title>Town Halls - Citizen Participation Platform</title>
    
    <!-- Favicon -->
    <link rel="icon" type="image/x-icon" href="favicon.ico">
    
    <!-- Stylesheets -->
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/flatpickr/dist/flatpickr.min.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.4/css/all.min.css">
    <style>
        :root {
            --primary-color: #FFA500; /* Orange */
            --secondary-color: #2B547E; /* Dark Blue */
            --background-color: #1c456d; /* Darker Blue */
        }

        body {
            margin: 0;
            padding: 0;
            font-family: 'Roboto', sans-serif; /* Change font */
            background-color: #f0f8ff; /* Light blue background */
            color: #fff;
        }

        .card {
            border: none;
            border-radius: 12px;
            transition: transform 0.3s, box-shadow 0.3s; /* Smooth transition */
        }

        .card:hover {
            transform: translateY(-5px); /* Lift effect */
            box-shadow: 0 4px 20px rgba(0, 0, 0, 0.3); /* Shadow on hover */
        }

        .btn-primary {
            background-color: var(--primary-color);
            border: none;
            border-radius: 25px; /* Rounded corners */
            padding: 10px 20px;
        }

        .btn-primary:hover {
            background-color: darkorange; /* Darker shade on hover */
        }

        h2, h4 {
            color: var(--secondary-color); /* Use secondary color for headings */
        }

        .card-title {
            font-weight: bold; /* Bold title */
        }

        .table th {
            background-color: #007bff; /* Primary color for table header */
            color: white; /* White text for contrast */
        }

        .table-hover tbody tr:hover {
            background-color: #e9ecef; /* Light gray on hover */
        }

        .background {
            background-image: url('../images/background.jpg');
            background-size: cover;
            background-position: center;
            height: 100vh; /* Full height */
            position: relative;
            z-index: -1;
        }

        /* Header and Footer Colors */
        .navbar {
            background-color: #2B547E; /* Dark Blue for header */
        }
        footer {
            background-color: #007bff; /* Blue for footer */
            color: white; /* White text for footer */
        }
        footer a {
            color: white; /* White links in footer */
        }
        footer a:hover {
            text-decoration: underline; /* Underline on hover for links */
        }
        .footer-content {
            display: flex;
            justify-content: center;
            align-items: center;
            padding: 10px;
        }

        /* New styles */
        .townhall-card {
            border: 1px solid #007bff; /* Blue border */
            border-radius: 10px;
            box-shadow: 0 4px 10px rgba(0, 0, 0, 0.1);
            background-color: #ffffff; /* White background for cards */
            margin-bottom: 20px;
            padding: 15px;
            transition: transform 0.2s;
        }
        .townhall-card:hover {
            transform: translateY(-5px);
            box-shadow: 0 8px 15px rgba(0, 0, 0, 0.2);
        }
        .townhall-title {
            font-size: 1.5rem;
            color: #007bff; /* Blue text for titles */
        }
        .townhall-date {
            font-size: 1rem;
            color: #495057;
        }
        .filter-section {
            background: #007bff; /* Blue background for filter section */
            border-radius: 10px;
            padding: 20px;
            margin-bottom: 20px;
            color: white; /* White text for better contrast */
        }
    </style>
</head>
<body>
    <center>
    <nav class="navbar navbar-expand-lg navbar-light bg-light fixed-top">
        <a class="navbar-brand" href="#">Citizen Participation</a>
        <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav" aria-controls="navbarNav" aria-expanded="false" aria-label="Toggle navigation">
            <span class="navbar-toggler-icon"></span>
        </button> <br> <br> <br>
        <div class="collapse navbar-collapse" id="navbarNav">
            <ul class="navbar-nav">
                <li class="nav-item">
                    <a class="nav-link active" aria-current="page" href="home.php">Home</a>
                </li>
                <li class="nav-item">
                    <a class="nav-link" href="contact.php">Contact us</a>
                </li>
                <li class="nav-item">
                    <a class="nav-link" href="polls.php">Polls</a>
                </li>
            </ul>
        </div>
    </nav>
    </center>

    <div class="container py-4">
        <div class="row mb-4">
            <div class="col page-header">
                <h1 class="h2">Town Halls</h1>
                <p class="text-muted">Connect with your local government through virtual and in-person town hall meetings.</p>
            </div>
            <?php if (isAdmin() || isModerator()): ?>
            <div class="col-auto">
                <button type="button" class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#createTownHallModal">
                    <i class="bi bi-plus-circle me-1"></i> Create Town Hall
                </button>
            </div>
            <?php endif; ?>
        </div>

        <!-- Statistics Cards -->
        <div class="row mb-4">
            <div class="col text-center">
                <h2 class="h4">Upcoming Town Halls</h2>
                <div class="display-4 text-warning">5</div>
                <p class="text-muted">Scheduled meetings</p>
            </div>
            <div class="col text-center">
                <h2 class="h4">Total Participants</h2>
                <div class="display-4 text-success">150</div>
                <p class="text-muted">Engaged citizens</p>
            </div>
            <div class="col text-center">
                <h2 class="h4">Completed Town Halls</h2>
                <div class="display-4 text-info">10</div>
                <p class="text-muted">Successfully held meetings</p>
            </div>
        </div>

        <!-- Upcoming Town Halls -->
        <div class="row mb-4">
            <div class="col">
                <h2 class="h4">Upcoming Town Halls</h2>
                <div class="table-responsive">
                    <table class="table table-hover">
                        <thead>
                            <tr>
                                <th>Title</th>
                                <th>Date</th>
                                <th>Location</th>
                                <th>Capacity</th>
                                <th>Registered</th>
                                <th>Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            <tr>
                                <td>Community Safety Discussion</td>
                                <td>Dec 15, 2024 6:00 PM</td>
                                <td>Main Hall</td>
                                <td>100</td>
                                <td>50</td>
                                <td><button class="btn btn-primary register-btn" data-id="1">Register</button></td>
                            </tr>
                            <tr>
                                <td>Budget Planning Meeting</td>
                                <td>Jan 10, 2025 5:00 PM</td>
                                <td>City Council Chamber</td>
                                <td>80</td>
                                <td>30</td>
                                <td><button class="btn btn-primary register-btn" data-id="2">Register</button></td>
                            </tr>
                            <tr>
                                <td>Environmental Awareness Forum</td>
                                <td>Feb 5, 2025 3:00 PM</td>
                                <td>Community Center</td>
                                <td>120</td>
                                <td>75</td>
                                <td><button class="btn btn-primary register-btn" data-id="3">Register</button></td>
                            </tr>
                            <tr>
                                <td>Transportation Improvement Meeting</td>
                                <td>Mar 20, 2025 4:00 PM</td>
                                <td>City Hall</td>
                                <td>90</td>
                                <td>40</td>
                                <td><button class="btn btn-primary register-btn" data-id="4">Register</button></td>
                            </tr>
                            <tr>
                                <td>Healthcare Access Forum</td>
                                <td>Apr 15, 2025 2:00 PM</td>
                                <td>Health Department</td>
                                <td>110</td>
                                <td>60</td>
                                <td><button class="btn btn-primary register-btn" data-id="5">Register</button></td>
                            </tr>
                            <?php while ($row = $upcoming_townhalls->fetch(PDO::FETCH_ASSOC)): ?>
                            <tr>
                                <td><?php echo htmlspecialchars($row['title']); ?></td>
                                <td><?php echo date('M j, Y g:i A', strtotime($row['scheduled_date'])); ?></td>
                                <td><?php echo htmlspecialchars($row['location']); ?></td>
                                <td><?php echo htmlspecialchars($row['capacity']); ?></td>
                                <td><?php echo htmlspecialchars($row['participant_count']); ?></td>
                                <td><button class="btn btn-primary register-btn" data-id="<?php echo $row['town_hall_id']; ?>">Register</button></td>
                            </tr>
                            <?php endwhile; ?>
                        </tbody>
                    </table>
                </div>
                <div class="pagination">
                    <button class="btn btn-secondary" id="prevPage">Previous</button>
                    <button class="btn btn-secondary" id="nextPage">Next</button>
                </div>
            </div>
        </div>

        <!-- Past Town Halls -->
        <div class="row mb-4">
            <div class="col">
                <h2 class="h4">Past Town Halls</h2>
                <div class="table-responsive">
                    <table class="table table-hover">
                        <thead>
                            <tr>
                                <th>Title</th>
                                <th>Date</th>
                                <th>Location</th>
                                <th>Participants</th>
                                <th>Documents</th>
                                <th>Description</th>
                                <th>Questions</th>
                                <th>Rating</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php while ($row = $past_townhalls->fetch(PDO::FETCH_ASSOC)): ?>
                            <tr>
                                <td><?php echo htmlspecialchars($row['title']); ?></td>
                                <td><?php echo date('M j, Y g:i A', strtotime($row['scheduled_date'])); ?></td>
                                <td><?php echo htmlspecialchars($row['location']); ?></td>
                                <td><?php echo htmlspecialchars($row['participant_count']); ?></td>
                                <td><a href="<?php echo htmlspecialchars($row['document_link']); ?>">View Documents</a></td>
                                <td><?php echo htmlspecialchars($row['description']); ?></td>
                                <td><?php echo htmlspecialchars($row['questions']); ?></td>
                                <td><?php echo htmlspecialchars($row['rating']); ?></td>
                            </tr>
                            <?php endwhile; ?>
                            <tr>
                                <td>Community Safety Discussion</td>
                                <td>Nov 15, 2023 6:00 PM</td>
                                <td>Main Hall</td>
                                <td>50</td>
                                <td><a href="#">View Documents</a></td>
                                <td>Discussed community safety measures and neighborhood watch programs.</td>
                                <td>10</td>
                                <td>4.5 / 5</td>
                            </tr>
                            <tr>
                                <td>Budget Planning Meeting</td>
                                <td>Oct 20, 2023 5:00 PM</td>
                                <td>City Council Chamber</td>
                                <td>30</td>
                                <td><a href="#">View Documents</a></td>
                                <td>Reviewed the budget for the upcoming fiscal year.</td>
                                <td>5</td>
                                <td>4.8 / 5</td>
                            </tr>
                            <tr>
                                <td>Environmental Awareness Forum</td>
                                <td>Sep 10, 2023 3:00 PM</td>
                                <td>Community Center</td>
                                <td>75</td>
                                <td><a href="#">View Documents</a></td>
                                <td>Focused on environmental issues and sustainability practices.</td>
                                <td>15</td>
                                <td>4.2 / 5</td>
                            </tr>
                            <tr>
                                <td>Transportation Improvement Meeting</td>
                                <td>Aug 5, 2023 4:00 PM</td>
                                <td>City Hall</td>
                                <td>40</td>
                                <td><a href="#">View Documents</a></td>
                                <td>Discussed improvements to local transportation infrastructure.</td>
                                <td>8</td>
                                <td>4.6 / 5</td>
                            </tr>
                            <tr>
                                <td>Healthcare Access Forum</td>
                                <td>Jul 15, 2023 2:00 PM</td>
                                <td>Health Department</td>
                                <td>60</td>
                                <td><a href="#">View Documents</a></td>
                                <td>Addressed healthcare access issues in the community.</td>
                                <td>12</td>
                                <td>4.9 / 5</td>
                            </tr>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>

    <!-- Create Town Hall Modal -->
    <?php if (isAdmin() || isModerator()): ?>
    <div class="modal fade" id="createTownHallModal" tabindex="-1">
        <div class="modal-dialog modal-lg">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">Create Town Hall</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body">
                    <form id="createTownHallForm">
                        <div class="mb-3">
                            <label class="form-label">Title</label>
                            <input type="text" class="form-control" name="title" required>
                        </div>
                        <div class="mb-3">
                            <label class="form-label">Description</label>
                            <textarea class="form-control" name="description" rows="3" required></textarea>
                        </div>
                        <div class="row">
                            <div class="col-md-6 mb-3">
                                <label class="form-label">Date & Time</label>
                                <input type="text" class="form-control" name="date" id="datePicker" required>
                            </div>
                            <div class="col-md-6 mb-3">
                                <label class="form-label">Location</label>
                                <input type="text" class="form-control" name="location" required>
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-md-6 mb-3">
                                <label class="form-label">Capacity</label>
                                <input type="number" class="form-control" name="capacity" min="1" required>
                            </div>
                            <div class="col-md-6 mb-3">
                                <label class="form-label">Meeting Link (Optional)</label>
                                <input type="url" class="form-control" name="meeting_link">
                            </div>
                        </div>
                        <div class="mb-3">
                            <label class="form-label">Agenda</label>
                            <textarea class="form-control" name="agenda" rows="3"></textarea>
                        </div>
                    </form>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                    <button type="button" class="btn btn-primary" id="createTownHallBtn">Create Town Hall</button>
                </div>
            </div>
        </div>
    </div>
    <?php endif; ?>

    <!-- View Town Hall Modal -->
    <div class="modal fade" id="viewTownHallModal" tabindex="-1">
        <div class="modal-dialog modal-lg">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">Town Hall Details</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body">
                    <div id="townhallDetails"></div>
                </div>
            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/flatpickr"></script>
    <script src="js/townhalls.js"></script>

    <script>
        document.querySelectorAll('.register-btn').forEach(button => {
            button.addEventListener('click', function() {
                // Change button text and color
                this.innerHTML = 'Registered';
                this.classList.add('btn-primary');
                this.disabled = true; // Disable the button
                alert('Registered successfully!'); // Show alert
            });
        });
    </script>

    <script>
        document.querySelectorAll('.view-townhall').forEach(item => {
            item.addEventListener('click', function(event) {
                event.preventDefault();
                const townhallId = this.dataset.id;
                // Fetch town hall details via AJAX
                fetch(`api/get_townhall_details.php?id=${townhallId}`)
                    .then(response => response.json())
                    .then(data => {
                        // Populate the modal with town hall details
                        const content = `<h4>${data.title}</h4>\n` +
                            `<p><strong>Date:</strong> ${data.scheduled_date}</p>\n` +
                            `<p><strong>Location:</strong> ${data.location}</p>\n` +
                            `<p><strong>Description:</strong> ${data.description}</p>\n` +
                            `<p><strong>Participants:</strong> ${data.participant_count}</p>`;
                        document.getElementById('townHallDetailsContent').innerHTML = content;
                        // Show the modal
                        var myModal = new bootstrap.Modal(document.getElementById('townHallDetailsModal'));
                        myModal.show();
                    });
            });
        });
    </script>

    <!-- Success Modal -->
    <div class="modal fade" id="successModal" tabindex="-1" aria-labelledby="successModalLabel" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="successModalLabel">Registration Successful</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    You have successfully registered for the town hall!
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                </div>
            </div>
        </div>
    </div>

    <!-- Error Modal -->
    <div class="modal fade" id="errorModal" tabindex="-1" aria-labelledby="errorModalLabel" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="errorModalLabel">Error</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <p id="errorModalMessage"></p>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                </div>
            </div>
        </div>
    </div>

</body>
</html>

<footer class="bg-light text-center text-lg-start mt-4">
    <div class="footer-content">
        <p class="text-muted">&copy; 2024 Citizen Participation Platform. All rights reserved.</p>
    </div>
</footer>

<script>
    let currentPage = 1;
    const itemsPerPage = 5;
    const totalItems = document.querySelectorAll('tbody tr').length;
    const totalPages = Math.ceil(totalItems / itemsPerPage);

    function renderPage(page) {
        const rows = document.querySelectorAll('tbody tr');
        rows.forEach((row, index) => {
            row.style.display = 'none'; // Hide all rows
            if (index >= (page - 1) * itemsPerPage && index < page * itemsPerPage) {
                row.style.display = ''; // Show only the rows for the current page
            }
        });
    }

    document.getElementById('prevPage').addEventListener('click', function() {
        if (currentPage > 1) {
            currentPage--;
            renderPage(currentPage);
        }
    });

    document.getElementById('nextPage').addEventListener('click', function() {
        if (currentPage < totalPages) {
            currentPage++;
            renderPage(currentPage);
        }
    });

    renderPage(currentPage); // Initial render
</script>
