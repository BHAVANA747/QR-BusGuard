<?php
session_start();
$message = "";
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // In a real app, this would send an email or save to DB
    $message = "<div class='alert alert-success'>Thank you for contacting us! We will get back to you soon.</div>";
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Contact Us - QR BusGuard</title>
    <link rel="stylesheet" href="css/style.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
</head>
<body>

    <header>
        <div class="navbar">
            <div class="logo-container">
                <img src="images/logo.jpg" alt="Logo" style="width:50px; height:50px; object-fit:contain;">
                <span class="logo-text">QR BusGuard</span>
            </div>
            <ul class="nav-links">
                <li><a href="index.php">HOME</a></li>
                <li><a href="about.php">ABOUT</a></li>
                <li><a href="contact.php" class="active">CONTACT US</a></li>
            </ul>
        </div>
    </header>

    <main>
        <div class="form-container">
            <h2 style="text-align: center;"><i class="fas fa-envelope"></i> Contact Us</h2>
            <br>
            <?php echo $message; ?>
            <form action="" method="POST">
                <div class="form-group">
                    <label>Your Name</label>
                    <input type="text" name="name" required placeholder="Enter your name">
                </div>
                <div class="form-group">
                    <label>Your Email</label>
                    <input type="email" name="email" required placeholder="Enter your email">
                </div>
                <div class="form-group">
                    <label>Message</label>
                    <textarea name="msg" rows="5" required placeholder="How can we help you?"></textarea>
                </div>
                <button type="submit" class="btn-primary" style="width: 100%;">SEND MESSAGE</button>
            </form>
            
            <div style="margin-top: 2rem; text-align: center;">
                <p><i class="fas fa-map-marker-alt"></i> Kempegowda Bus Station, Majestic, Bangalore</p>
                <p><i class="fas fa-phone"></i> +91 80 1234 5678</p>
                <p><i class="fas fa-envelope"></i> support@qrbusguard.com</p>
            </div>
        </div>
    </main>

    <footer>
        <p>&copy; <?php echo date("Y"); ?> QR BusGuard. All rights reserved.</p>
    </footer>

</body>
</html>
