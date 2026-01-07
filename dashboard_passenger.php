<?php
session_start();
require 'db_connect.php';

if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'passenger') {
    header("Location: login.php");
    exit();
}

$user_id = $_SESSION['user_id'];
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Passenger Dashboard - QR BusGuard</title>
    <link rel="stylesheet" href="css/style.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
</head>
<body>
    <header>
        <div class="navbar">
            <div class="logo-container">
                <div class="logo-container">
                <img src="images/logo.jpg" alt="Logo" style="width:50px; height:50px; object-fit:contain;">
                <span class="logo-text">QR BusGuard</span>
</div>
            </div>
            <ul class="nav-links">
                <li><a href="index.php">HOME</a></li>
                <li><a href="#" class="active">PASSENGER DASHBOARD</a></li>
                <li><a href="logout.php">LOGOUT</a></li>
            </ul>
        </div>
    </header>

    <main>
        <h2 style="text-align:center; margin-bottom: 20px;">Welcome, <?php echo $_SESSION['name']; ?></h2>

        <!-- Bus Scan Simulation -->
        <div class="card" style="margin-bottom: 2rem; border-left: 5px solid #003399;">
            <h3><i class="fas fa-qrcode"></i> Simulate Bus Scan</h3>
            <p style="color:white;">Select a bus to simulate scanning its QR code:</p>
            <div style="display: flex; gap: 10px; justify-content: center; margin-top: 15px;">
                <!-- These links simulate scanning a QR code which would usually pass a bus_id or number -->
                <a href="scan_handler.php?bus=KA-01-F-2427" class="btn-primary">Bus KA-01-F-2427</a>
                <a href="scan_handler.php?bus=KA-57-F-3179" class="btn-primary">Bus KA-57-F-3179</a>
            </div>
        </div>

        <div class="features-grid">
            <div class="card">
                <div class="card-icon"><i class="fas fa-exclamation-circle"></i></div>
                <h3>Reference History</h3>
                <p style="color:white;">View your past reports and their status.</p>
                <a href="passenger_history.php" class="btn-primary">View History</a>
            </div>
             <div class="card">
                <div class="card-icon"><i class="fas fa-user-edit"></i></div>
                <h3>Profile</h3>
                <p style="color:white;">Update your contact details.</p>
                <a href="edit_profile.php" class="btn-primary">Edit Profile</a>
            </div>
        </div>
        
    </main>

    <footer>
        <p>&copy; <?php echo date("Y"); ?> QR BusGuard. All rights reserved.</p>
    </footer>
</body>
</html>
