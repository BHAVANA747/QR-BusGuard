<?php
session_start();
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>About Us - QR BusGuard</title>
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
            </div>
            <ul class="nav-links">
                <li><a href="index.php">HOME</a></li>
                <?php if(isset($_SESSION['user_id'])): ?>
                    <li><a href="dashboard_<?php echo $_SESSION['role']; ?>.php">DASHBOARD</a></li>
                <?php else: ?>
                    <li><a href="login.php">LOGIN</a></li>
                <?php endif; ?>
                <li><a href="about.php" class="active">ABOUT</a></li>
                <li><a href="contact.php">CONTACT US</a></li>
            </ul>
        </div>
    </header>

    <main>
        <div class="card" style="text-align: center; margin-top: 2rem;">
            <h2 style="color:blue;">About QR BusGuard</h2>
            <br>
            <p style="max-width: 800px; margin: 0 auto; line-height: 1.8; color:white;">
               <strong>BMTC (Bangalore Metropolitan Transport Corporation)</strong> is one of the largest and most reliable public bus services in Bengaluru, connecting lakhs of passengers daily across urban and suburban routes. In our project QR BusGuard, we integrate BMTC services with QR-based route lookup and emergency alert features to improve passenger safety and travel experience using digital tools.
                <br><br>
                By leveraging <strong>QR Code technology</strong> and <strong>Artificial Intelligence (OpenCV)</strong>, we bridge the gap between passengers, conductors, drivers, and administration.
                <br><br>
                <strong style="color:blue;">Key Goals:</strong>
            </p>
            <div class="features-grid" style="margin-top: 2rem; text-align: left; color:white;">
                <div class="card" style="box-shadow: none; border: 1px solid #eee;">
                    <h4 style="color:blue;"><i class="fas fa-shield-alt" style="color:red;"></i> Safety</h4>
                    <p>Instant theft reporting directly to admin.</p>
                </div>
                <div class="card" style="box-shadow: none; border: 1px solid #eee;">
                    <h4 style="color:blue;"><i class="fas fa-hand-holding-heart" style="color:blue;"></i> Recovery</h4>
                    <p style="color:white;">AI-driven lost item matching to return belongings.</p>
                </div>
                <div class="card" style="box-shadow: none; border: 1px solid #eee;">
                    <h4 style="color:blue;"><i class="fas fa-tools" style="color:orange;"></i> Reliability</h4>
                    <p style="color:white;">Crowdsourced maintenance reporting for better buses.</p>
                </div>
            </div>
        </div>
    </main>

    <footer>
        <p>&copy; <?php echo date("Y"); ?> QR BusGuard. All rights reserved.</p>
    </footer>

</body>
</html>
