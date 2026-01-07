<?php
session_start();
require 'db_connect.php';

if (!isset($_SESSION['user_id'])) {
    // If not logged in, redirect to login with a return URL (simplified for now, just login)
    header("Location: login.php");
    exit();
}

if (isset($_GET['bus'])) {
    $bus_number = $_GET['bus'];
    
    // Verify bus exists
    $sql = "SELECT * FROM buses WHERE bus_number='$bus_number'";
    $result = $conn->query($sql);

    if ($result->num_rows > 0) {
        $bus = $result->fetch_assoc();
        $_SESSION['bus_id'] = $bus['id'];
        $_SESSION['bus_number'] = $bus['bus_number'];
    } else {
        echo "Bus not found!";
        exit();
    }
} elseif (!isset($_SESSION['bus_id'])) {
    echo "No bus scanned!";
    exit();
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Bus Service Portal - QR BusGuard</title>
    <link rel="stylesheet" href="css/style.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    <style>
        .service-btn {
            display: block;
            width: 100%;
            padding: 20px;
            margin-bottom: 20px;
            text-align: center;
            font-size: 1.5rem;
            border-radius: 10px;
            text-decoration: none;
            color: white;
            transition: transform 0.2s;
        }
        .service-btn:hover { transform: scale(1.02); }
        .bg-red { background-color: #dc3545; }
        .bg-blue { background-color: #007bff; }
        .bg-orange { background-color: #fd7e14; }
        .icon-large { font-size: 2rem; margin-right: 10px; }
    </style>
</head>
<body>
    <header>
        <div class="navbar">
            <div class="logo-container">
                <img src="images/logo.jpg" alt="Logo" style="width:50px; height:50px; object-fit:contain;">
                <span class="logo-text">QR BusGuard</span>
            </div>
            <ul class="nav-links">
                <li><a href="dashboard_passenger.php">DASHBOARD</a></li>
                <li><a href="logout.php">LOGOUT</a></li>
            </ul>
        </div>
    </header>

    <main>
        <div class="form-container" style="max-width: 600px;">
            <div style="text-align:center; margin-bottom: 2rem;">
                <h2>Bus: <?php echo $_SESSION['bus_number']; ?></h2>
                <h2>Route: Majestic to Peenya 2nd Stage [265A]</h2>
                <p>Welcome! How can we help you today?</p>
            </div>

            <a href="report_theft.php" class="service-btn bg-red">
                <i class="fas fa-bell icon-large"></i> THEFT EMERGENCY
            </a>

            <a href="report_lost.php" class="service-btn bg-blue">
                <i class="fas fa-search-location icon-large"></i> REPORT LOST ITEM
            </a>


            
        </div>
    </main>

    <footer>
        <p>&copy; <?php echo date("Y"); ?> QR BusGuard. All rights reserved.</p>
    </footer>
</body>
</html>
