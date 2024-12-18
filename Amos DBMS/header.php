<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo isset($page_title) ? $page_title . " - " : ""; ?>Citizen Participation Platform</title>
    
    <!-- Bootstrap CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/css/bootstrap.min.css" rel="stylesheet">
    
    <!-- Font Awesome -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    
    <!-- Custom CSS -->
    <style>
        body {
            min-height: 100vh;
            display: flex;
            flex-direction: column;
        }
        .navbar {
            padding: 1rem;
            box-shadow: 0 2px 4px rgba(0,0,0,.1);
        }
        .navbar-brand {
            font-weight: bold;
        }
        .nav-link {
            color: #333;
            font-weight: 500;
        }
        .nav-link:hover {
            color: #007bff;
        }
        .nav-link.active {
            color: #007bff;
        }
        .main-content {
            flex: 1;
        }
        .card {
            transition: transform 0.3s;
            margin-bottom: 1.5rem;
        }
        .card:hover {
            transform: translateY(-5px);
        }
        .footer {
            background-color: #f8f9fa;
            padding: 2rem 0;
            margin-top: auto;
        }
    </style>
</head>
<body>
    <nav class="navbar navbar-expand-lg navbar-light bg-light">
        <div class="container">
            <a class="navbar-brand" href="home.php">
                <img src="images/logo.png" alt="Logo" height="30" class="d-inline-block align-text-top me-2">
                Citizen Participation
            </a>
            <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav">
                <span class="navbar-toggler-icon"></span>
            </button>
            <div class="collapse navbar-collapse" id="navbarNav">
                <ul class="navbar-nav ms-auto">
                    <li class="nav-item">
                        <a class="nav-link <?php echo basename($_SERVER['PHP_SELF']) == 'home.php' ? 'active' : ''; ?>" href="home.php">
                            <i class="fas fa-home"></i> Home
                        </a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link <?php echo basename($_SERVER['PHP_SELF']) == 'initiatives.php' ? 'active' : ''; ?>" href="initiatives.php">
                            <i class="fas fa-lightbulb"></i> Initiatives
                        </a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link <?php echo in_array(basename($_SERVER['PHP_SELF']), ['townhalls.php', 'townhalls.php']) ? 'active' : ''; ?>" href="townhalls.php">
                            <i class="fas fa-users"></i> Town Halls
                        </a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link <?php echo basename($_SERVER['PHP_SELF']) == 'polls.php' ? 'active' : ''; ?>" href="polls.php">
                            <i class="fas fa-poll"></i> Polls
                        </a>
                    </li>
                    <?php if (isset($_SESSION['username'])): ?>
                        <li class="nav-item dropdown">
                            <a class="nav-link dropdown-toggle" href="#" id="navbarDropdown" role="button" data-bs-toggle="dropdown">
                                <i class="fas fa-user"></i> <?php echo htmlspecialchars($_SESSION['username']); ?>
                            </a>
                            <ul class="dropdown-menu">
                                <li><a class="dropdown-item" href="profile.php"><i class="fas fa-id-card"></i> Profile</a></li>
                                <?php if (isset($_SESSION['role']) && in_array($_SESSION['role'], ['admin', 'moderator'])): ?>
                                    <li><a class="dropdown-item" href="admin/dashboard.php"><i class="fas fa-tachometer-alt"></i> Dashboard</a></li>
                                <?php endif; ?>
                                <li><hr class="dropdown-divider"></li>
                                <li><a class="dropdown-item" href="logout.php"><i class="fas fa-sign-out-alt"></i> Logout</a></li>
                            </ul>
                        </li>
                    <?php else: ?>
                        <li class="nav-item">
                            <a class="nav-link" href="login.php"><i class="fas fa-sign-in-alt"></i> Login</a>
                        </li>
                    <?php endif; ?>
                </ul>
            </div>
        </div>
    </nav>
    <div class="main-content">
