<?php
session_start();

include("connection.php");

if (!isset($_SESSION['username'])) {
    header("location:index.php");
}


?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Homepage</title>

    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.1/dist/css/bootstrap.min.css" rel="stylesheet"
        integrity="sha384-4bw+/aepP/YC94hEpVNVgiZdgIC5+VKNBQNGCHeKRQN+PtmoHDEXuppvnDJzQIu9" crossorigin="anonymous">

    <link rel="stylesheet"
        href="https://cdnjs.cloudflare.com/ajax/libs/bootstrap-icons/1.10.5/font/bootstrap-icons.min.css">

    <link rel="stylesheet" href="css/style.css">
    <link rel="stylesheet" href="css/hero.css">
</head>

<body>

    <!-- navbar section   -->
    <header class="navbar-section">
        <nav class="navbar navbar-expand-lg">
            <div class="container-fluid">
                <a class="navbar-brand" href="#"><i class="bi bi-chat"></i> Myooye community</a>
                <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav"
                    aria-controls="navbarNav" aria-expanded="false" aria-label="Toggle navigation">
                    <span class="navbar-toggler-icon"></span>
                </button>
                <div class="collapse navbar-collapse" id="navbarNav">
                    <ul class="navbar-nav ms-auto">
                        <li class="nav-item">
                            <a class="nav-link" href="#home">Home</a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link" href="#services">Services</a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link" href="polls.php">Polls</a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link" href="#about">About Us</a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link" href="contact.php">Contact</a>
                        </li>
                        <li class="nav-item">
                            <div class="dropdown">
                                <a class='nav-link dropdown-toggle' href='edit.php?id=$res_id' id='dropdownMenuLink'
                                    data-bs-toggle='dropdown' aria-expanded='false'>
                                    <i class='bi bi-person'></i>
                                </a>


                                <ul class="dropdown-menu mt-2 mr-0" aria-labelledby="dropdownMenuLink">

                                    <li>
                                        <?php

                                        $id = $_SESSION['id'];
                                        $query = mysqli_query($conn, "SELECT * FROM users WHERE id = $id");

                                        while ($result = mysqli_fetch_assoc($query)) {
                                            $res_username = $result['username'];
                                            $res_email = $result['email'];
                                            $res_id = $result['id'];
                                        }


                                        echo "<a class='dropdown-item' href='edit.php?id=$res_id'>Change Profile</a>";


                                        ?>
                                    </li>
                                    <li><a class="dropdown-item" href="logout.php">Logout</a></li>
                                </ul>
                            </div>

                        </li>
                    </ul>
                </div>
            </div>
        </nav>
    </header>


    <div class="name">
        <center>Welcome
            <?php
            // echo $_SESSION['valid'];
            
            echo $_SESSION['username'];
            ?>
            !
        </center>
    </div>

    <!-- hero section  -->
    <section id="home" class="hero-section">
        <div class="container">
            <div class="row align-items-center">
                <div class="col-lg-5 col-md-12 col-sm-12 text-content">
                    <span class="badge bg-primary mb-3">Your Voice Matters</span>
                    <h1 class="display-4 fw-bold mb-4">Citizen Participation Platform</h1>
                    <p class="lead mb-4">Empowering citizens to actively shape their community through digital democracy. Join us in making local governance more transparent, inclusive, and responsive.</p>
                    <div class="hero-buttons">
                        <button class="btn btn-primary btn-lg me-3"><a href="#about" class="text-white text-decoration-none">Learn More</a></button>
                        <button class="btn btn-outline-primary btn-lg"><a href="townhalls.php" class="text-decoration-none">Join Town Hall</a></button>
                    </div>
                    <div class="hero-stats mt-5 d-flex justify-content-start">
                        <div class="me-4">
                            <h3 class="fw-bold">1000+</h3>
                            <p class="text-muted">Active Citizens</p>
                        </div>
                        <div class="me-4">
                            <h3 class="fw-bold">50+</h3>
                            <p class="text-muted">Town Halls</p>
                        </div>
                        <div>
                            <h3 class="fw-bold">200+</h3>
                            <p class="text-muted">Initiatives</p>
                        </div>
                    </div>
                </div>
                <div class="col-lg-7 col-md-12 col-sm-12 position-relative">
                    <div class="hero-image-container">
                        <img src="images/citzn.png" alt="Citizen Participation" class="img-fluid rounded-3 shadow-lg">
                        <div class="floating-card bg-white p-3 rounded shadow-lg position-absolute" style="bottom: 30px; right: 30px;">
                            <div class="d-flex align-items-center">
                                <i class="bi bi-megaphone-fill text-primary me-2"></i>
                                <div>
                                    <h6 class="mb-0">Next Town Hall</h6>
                                    <small class="text-muted">Join us tomorrow!</small>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <!-- about section  -->
    <section class="about-section" id="about">
        <div class="container">
            <div class="row">
                <div class="col-lg-6 col-md-12 col-sm-12">
                    <img src="images/citizen.jpg" alt="" class="img-fluid">
                </div>
                <div class="col-lg-6 col-md-12 col-sm-12 text-content">
                    <h3>About Our Platform</h3>
                    <h1>Empowering Citizens Through Digital Democracy</h1>

                    <p>Our Citizen Participation Platform is a digital initiative designed to strengthen democratic engagement at the local level. We provide a secure and accessible space for citizens to actively participate in community decision-making, attend virtual town halls, submit suggestions, and engage with local government officials. Our mission is to bridge the gap between citizens and their representatives, fostering transparency and collaborative governance.</p>
                    <button>Learn More</button>
                </div>
            </div>
        </div>
    </section>

    <!-- project section  -->
    <section class="project-section" id="projects">
        <div class="container">
            <div class="row text">
                <div class="col-lg-6 col-md-12">
                    <h3>Our Initiatives</h3>
                    <h1>Citizen Participation Project</h1>
                    <hr>
                </div>
                <div class="col-lg-6 col-md-12">
                    <p>Our Citizen Participation Project empowers citizens to engage in public discourse, provide input
                        on key issues, and actively contribute to their communities. Discover our innovative features and
                        platforms that bring communities and leaders together.</p>
                </div>
            </div>
            <div class="row project">

                <div class="col-lg-3 col-md-6 col-sm-12">
                    <div class="card">
                        <img src="images/project1.jpg" alt="..." class="img-fluid">
                        <div class="card-body">
                            <div class="text">
                                <h4 class="card-title">Community Forum</h4>
                                <p class="card-text">Engagement Platform. July 15, 2024</p>
                                <button>See Details</button>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="col-lg-3 col-md-6 col-sm-12">
                    <div class="card">
                        <img src="images/project2.jpg" alt="..." class="img-fluid">
                        <div class="card-body">
                            <div class="text">
                                <h4 class="card-title">Poll & Surveys</h4>
                                <p class="card-text">Public Feedback. March 3, 2024</p>
                                <button>See Details</button>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="col-lg-3 col-md-6 col-sm-12">
                    <div class="card">
                        <img src="images/project3.jpg" alt="..." class="img-fluid">
                        <div class="card-body">
                            <div class="text">
                                <h4 class="card-title">News & Updates</h4>
                                <p class="card-text">Real-Time Information. September 20, 2024</p>
                                <button>See Details</button>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="col-lg-3 col-md-6 col-sm-12">
                    <div class="card">
                        <img src="images/project4.jpg" alt="..." class="img-fluid">
                        <div class="card-body">
                            <div class="text">
                                <h4 class="card-title">Issue Reporting</h4>
                                <p class="card-text">Direct Feedback. November 12, 2024</p>
                                <button>See Details</button>
                            </div>
                        </div>
                    </div>
                </div>

            </div>

        </div>
    </section>

    <!-- services section  -->
    <section class="services-section" id="services">
        <div class="container">
            <div class="row">
                <div class="col-12 text-content">
                    <h1>Services</h1>
                    <h2>Empowering Community Engagement through Digital Participation</h2>
                    <p>Our platform offers comprehensive digital solutions for citizen participation, enabling transparent communication between citizens and local government through various engagement channels.</p>
                </div>
            </div>
            <div class="row services">
                <div class="col-lg-4 col-md-6 col-sm-12">
                    <div class="card">
                        <img src="images/research.png" class="card-img-top" alt="Virtual Town Halls">
                        <div class="card-body">
                            <h4 class="card-title">Virtual Town Halls</h4>
                            <p class="card-text">Participate in interactive town hall meetings, engage with local officials, and contribute to community discussions in real-time.</p>
                            <a href="townhalls.php" class="btn btn-primary">Join Meetings</a>
                        </div>
                    </div>
                </div>

                <div class="col-lg-4 col-md-6 col-sm-12">
                    <div class="card">
                        <img src="images/brand.png" class="card-img-top" alt="Community Initiatives">
                        <div class="card-body">
                            <h4 class="card-title">Community Initiatives</h4>
                            <p class="card-text">Propose, support, and track community initiatives. Get updates on ongoing projects and their impact on the community.</p>
                            <a href="initiatives.php" class="btn btn-primary">View Initiatives</a>
                        </div>
                    </div>
                </div>

                <div class="col-lg-4 col-md-6 col-sm-12">
                    <div class="card">
                        <img src="images/ux.png" class="card-img-top" alt="Citizen Feedback">
                        <div class="card-body">
                            <h4 class="card-title">Citizen Feedback</h4>
                            <p class="card-text">Share your thoughts, vote on proposals, and participate in community surveys to shape local policies and decisions.</p>
                            <a href="suggestion.php" class="btn btn-primary">Give Feedback</a>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>
   
    <!-- footer section  -->
    <footer clss="footer-section">
        <div class="container">
            <div class="row">
                <!-- Company Information -->
                <div class="col-lg-3 col-md-6">
                    <h4>Myooye community</h4>
                    <p>
                        Myooye Street<br>
                        Mumbwa, plot 535022<br><br>
                        <strong>Phone:</strong> +260 9736 50518<br>
                        <strong>Email:</strong> kapekaps18@gmail.com<br>
                    </p>
                </div>

                <!-- Useful Links -->
                <div class="col-lg-3 col-md-6">
                    <h4>Useful Links</h4>
                    <ul>
                        <li><i class="bi bi-chevron-right"></i> <a href="#">Home</a></li>
                        <li><i class="bi bi-chevron-right"></i> <a href="#">About us</a></li>
                        <li><i class="bi bi-chevron-right"></i> <a href="#">Services</a></li>
                        <li><i class="bi bi-chevron-right"></i> <a href="#">Terms of service</a></li>
                    </ul>
                </div>

                <!-- Our Services -->
                <div class="col-lg-3 col-md-6">
                    <h4>Our Services</h4>
                    <ul>
                        <li><i class="bi bi-chevron-right"></i> <a href="#">Virtual Town Halls</a></li>
                        <li><i class="bi bi-chevron-right"></i> <a href="#">Community Initiatives</a></li>
                        <li><i class="bi bi-chevron-right"></i> <a href="#">Citizen Feedback</a></li>
                        <li><i class="bi bi-chevron-right"></i> <a href="#">Virtual Polls</a></li>
                    </ul>
                </div>

                <!-- Social Links -->
                <div class="col-lg-3 col-md-6">
                    <h4>Follow Us</h4>
                    <p>
                    Click on the links bellow to follow us on our different social media platforms.
                    </p>
                    <div class="social-links">
                        <a href="#" class="twitter"><i class="bi bi-twitter"></i></a>
                        <a href="#" class="facebook"><i class="bi bi-facebook"></i></a>
                        <a href="#" class="instagram"><i class="bi bi-instagram"></i></a>
                        <a href="#" class="linkedin"><i class="bi bi-linkedin"></i></a>
                    </div>
                </div>
            </div>
            <hr>
            <div class="footer-bottom text-center">
                <p>
                     All Rights Reserved<br>
                    Designed by Amos Kapembwa  <br> SIN: 2305335056<br>
                    <a target="_blank" href="http://www.icuzambia.net">ICU Zambia</a> <br>
                    <a target="_blank" href="https://www.zrdc.org">ZRDC</a> <br>
                </p>

            </div>
        </div>
    </footer>


    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.1/dist/js/bootstrap.bundle.min.js"
        integrity="sha384-HwwvtgBNo3bZJJLYd8oVXjrBZt8cqVSpeBNS5n7C8IVInixGAoxmnlMuBnhbgrkm"
        crossorigin="anonymous"></script>
</body>

</html>