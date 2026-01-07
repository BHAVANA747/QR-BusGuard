<?php
session_start();
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>QR BusGuard - Smart Public Transport</title>
    <link rel="stylesheet" href="css/style.css">
    <!-- FontAwesome for Icons -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    <link href="https://fonts.googleapis.com/css2?family=Segoe+UI:wght@400;600&display=swap" rel="stylesheet">
</head>
<body>

    <!-- Header & Navigation -->
    <header>
        <div class="navbar">
            <div class="logo-container">
                <img src="images/logo.jpg" alt="Logo" style="width:50px; height:50px; object-fit:contain;">
                <span class="logo-text">QR BusGuard</span>
            </div>
            <ul class="nav-links">
                <li><a href="index.php" class="active">HOME</a></li>
                <?php if(isset($_SESSION['user_id'])): ?>
                    <li><a href="dashboard_<?php echo $_SESSION['role']; ?>.php">DASHBOARD</a></li>
                    <li><a href="logout.php">LOGOUT</a></li>
                <?php else: ?>
                    <li><a href="register.php">REGISTER</a></li>
                    <li><a href="login.php">LOGIN</a></li>
                <?php endif; ?>
                <li><a href="about.php">ABOUT</a></li>
                <li><a href="contact.php">CONTACT US</a></li>
            </ul>
        </div>
    </header>

    <!-- Main Content -->
    <main>
        
        <!-- Hero Section -->
        <section class="hero">
            <div class="hero-content">
                <h1>Welcome to QR BusGuard</h1>
                <p>AI-Powered Emergency Alert & Vehicle Care Platform for BMTC Services</p>
                <?php if(!isset($_SESSION['user_id'])): ?>
                    <a href="register.php" class="btn-primary">Get Started <i class="fas fa-arrow-right"></i></a>
                <?php else: ?>
                    <a href="dashboard_<?php echo $_SESSION['role']; ?>.php" class="btn-primary">Go to Dashboard <i class="fas fa-tachometer-alt"></i></a>
                <?php endif; ?>
            </div>
        </section>

        <!-- Features Grid -->
        <section class="features-grid">
            <div class="card">
                <div class="card-icon"><i class="fas fa-exclamation-triangle"></i></div>
                <h3>Theft Emergency</h3>
                <p style="color:white;">Passenger reporting for theft incidents. Instant alerts to administration.</p>
            </div>
            <div class="card">
                <div class="card-icon"><i class="fas fa-search-location"></i></div>
                <h3>Lost & Found</h3>
                <p style="color:white;">AI-powered image matching to reunite you with passenger lost belongings.</p>
            </div>
            <div class="card">
                <div class="card-icon"><i class="fas fa-tools"></i></div>
                <h3>Smart Maintenance</h3>
                <p style="color:white;">Digital reporting for bus issues ensuring safer and smoother rides for Driver.</p>
            </div>
        </section>

        <div class="page-nav">
            <span><!-- Placeholder for left --></span>
            <a href="register.php" class="nav-arrow">Next: Register <i class="fas fa-chevron-right"></i></a>
        </div>

    </main>

    <!-- Footer -->
    <footer>
        <p>&copy; <?php echo date("Y"); ?> QR BusGuard - BMTC Services. All rights reserved.</p>
    </footer>

</body>
</html>
