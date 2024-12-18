<?php require_once 'includes/session.php'; ?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Contact Us</title>
    <link rel="stylesheet" href="css/style3.css">
    <!-- <link rel="stylesheet" href="css/style4.css"> -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons/font/bootstrap-icons.css" rel="stylesheet">
    
</head>

<body>

  <!-- Header Section with link back to Home Page -->
    <div class="container">
        <div class="form-box box">
            <?php
            include "connection.php";

            if ($_SERVER['REQUEST_METHOD'] == 'POST') {
                $name = mysqli_real_escape_string($conn, $_POST['name']);
                $email = mysqli_real_escape_string($conn, $_POST['email']);
                $subject = mysqli_real_escape_string($conn, $_POST['subject']);
                $message = mysqli_real_escape_string($conn, $_POST['message']);

                $query = "INSERT INTO contact (name, email, subject, message) VALUES ('$name', '$email', '$subject', '$message')";

                if (mysqli_query($conn, $query)) {
                    echo "<div class='message success'>
                            <p>Message sent successfully ✨</p>
                          </div>";
                } else {
                    echo "<div class='message error'>
                            <p>Message sending failed 😔</p>
                          </div>";
                }
            }
            ?>
        </div>
    </div>

    <!-- Contact Section -->
    <section class="contact-section" id="contact">
        <div class="container">
            <button id="back2home" style="background: linear-gradient(rgba(0, 0, 50, 0.7), rgba(0, 0, 50, 0.7)); border: none; border-radius: 5px; padding: 12px 24px; font-size: 20px; font-weight: bold; ">
             <a href="home.php" style="color: yellow; text-decoration: none;">Back to Home</a>
            </button>
            <h1>Contact Us</h1>
            <div class="row gy-4">
                <!-- Contact Info -->
                <div class="col-lg-6">
                    <div class="row gy-4">
                        <div class="col-md-6">
                            <div class="info-box">
                                <i class="bi bi-geo-alt"></i>
                                <h3>Address</h3>
                                <p>123 Civic Engagement Street,<br>Your City, 56789</p>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="info-box">
                                <i class="bi bi-telephone"></i>
                                <h3>Call Us</h3>
                                <p>+260 768 759646</p>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="info-box">
                                <i class="bi bi-envelope"></i>
                                <h3>Email Us</h3>
                                <p>info@citizenparticipation.com<br>support@citizenparticipation.com</p>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="info-box">
                                <i class="bi bi-clock"></i>
                                <h3>Open Hours</h3>
                                <p>Monday - Friday<br>9:00 AM - 6:00 PM</p>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Contact Form -->
                <div class="col-lg-6 form">
                    <form action="contact.php" method="POST" class="php-email-form">
                        <div class="row gy-4">
                            <div class="col-md-6">
                                <input type="text" name="name" class="form-control" placeholder="Your Name" required>
                            </div> <br>
                            <div class="col-md-6">
                                <input type="email" name="email" class="form-control" placeholder="Your Email" required>
                            </div>
                            <div class="col-md-6">
                                <input type="text" name="subject" class="form-control" placeholder="Subject" required>
                            </div> <br> <br>
                            <div class="col-md-12">
                                <textarea name="message" class="form-control" rows="5" placeholder="Message" required></textarea>
                            </div>
                            <div class="col-md-12 text-center">
                                <button type="submit" class="btn">Send Message</button>
                            </div>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </section>

    <!-- footer section  -->
    <footer clss="footer-section">
        <div class="container">           
            <hr>
        <center>
            <div class="footer-bottom text-center">
            <p>
                     All Rights Reserved<br>
                    Designed by Amos Kapembwa  <br> SIN: 2305335056 <br>
                    <a target="_blank" href="http://www.icuzambia.net">ICU Zambia</a> <br>
                    <a target="_blank" href="https://www.zrdc.org">ZRDC</a> <br>
                </p>
            </div>
        </center>
        </div>
    </footer>


</body>

</html>
