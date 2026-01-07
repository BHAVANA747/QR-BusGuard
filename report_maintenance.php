<?php
session_start();
require 'db_connect.php';

if (!isset($_SESSION['user_id']) || !isset($_SESSION['bus_id'])) {
    header("Location: dashboard_passenger.php");
    exit();
}

$message = "";

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $user_id = $_SESSION['user_id'];
    $bus_id = $_SESSION['bus_id'];
    $issue_type = $_POST['issue_type'];
    $description = $_POST['description'];

    $sql = "INSERT INTO maintenance_reports (user_id, bus_id, issue_type, description) VALUES ('$user_id', '$bus_id', '$issue_type', '$description')";

    if ($conn->query($sql) === TRUE) {
        $message = "<div class='alert alert-success'>Maintenance Issue Reported!</div>";
    } else {
        $message = "<div class='alert alert-error'>Error: " . $conn->error . "</div>";
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Report Maintenance - QR BusGuard</title>
    <link rel="stylesheet" href="css/style.css">
</head>
<body>
    <header>
        <div class="navbar">
            <div class="logo-container">
                <img src="images/logo.jpg" alt="Logo" style="width:50px; height:50px; object-fit:contain;">
            </div>
            <span class="logo-text">QR BusGuard</span>
            <ul class="nav-links">
                <li><a href="scan_handler.php">Back to Bus</a></li>
            </ul>
        </div>
    </header>

    <main>
        <div class="form-container">
            <h2 style="color: #fd7e14; text-align: center;"><i class="fas fa-wrench"></i> Report Maintenance</h2>
            <p style="text-align: center; margin-bottom: 20px;">Bus: <strong><?php echo $_SESSION['bus_number']; ?></strong></p>
            <?php echo $message; ?>
            
            <form action="" method="POST">
                <div class="form-group">
                    <label>Issue Type</label>
                    <select name="issue_type" required>
                        <option value="" disabled selected>Select Issue</option>
                        <option value="Broken Seat">Broken Seat</option>
                        <option value="Lighting Issue">Lighting Issue</option>
                        <option value="Window Broken">Window Broken</option>
                        <option value="Engine Noise">Engine Noise</option>
                        <option value="Other">Other</option>
                    </select>
                </div>
                <div class="form-group">
                    <label>Description</label>
                    <textarea name="description" rows="4" placeholder="Describe the issue..."></textarea>
                </div>
                <button type="submit" class="btn-primary" style="background-color: #fd7e14; width: 100%;">SUBMIT REPORT</button>
            </form>
        </div>
    </main>
</body>
</html>
