<?php
session_start();
require 'db_connect.php';

if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'admin') {
    die("Access Denied");
}

$id = $_GET['id'] ?? null;
if (!$id) die("Invalid ID");

$message = "";

// Fetch Report Details
$sql = "SELECT m.*, b.bus_number FROM maintenance_reports m JOIN buses b ON m.bus_id = b.id WHERE m.id='$id'";
$result = $conn->query($sql);
$report = $result->fetch_assoc();

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $service_center = $_POST['service_center'];
    $service_date = $_POST['service_date'];
    
    // Combine Time inputs
    $hour = $_POST['service_hour'];
    $min = $_POST['service_min'];
    $ampm = $_POST['service_ampm'];
    $service_time = date("H:i:s", strtotime("$hour:$min $ampm"));

    $sql_update = "UPDATE maintenance_reports SET 
                   status='Scheduled', 
                   service_center='$service_center', 
                   service_date='$service_date', 
                   service_time='$service_time' 
                   WHERE id='$id'";

    if ($conn->query($sql_update) === TRUE) {
        header("Location: dashboard_admin.php?msg=scheduled");
        exit();
    } else {
        $message = "<div class='alert alert-error'>Error: " . $conn->error . "</div>";
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Schedule Service - QR BusGuard</title>
    <link rel="stylesheet" href="css/style.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
</head>
<body>
    <header>
        <div class="navbar">
            <div class="logo-container">
                <img src="images/logo.jpg" alt="Logo" style="width:50px; height:50px; object-fit:contain;">
            <span class="logo-text">SCHEDULE SERVICE</span>
</div>
            <ul class="nav-links">
                <li><a href="dashboard_admin.php">BACK TO DASHBOARD</a></li>
            </ul>
        </div>
    </header>

    <main>
        <div class="form-container">
            <h2>Reply to Driver Report</h2>
            <div class="card" style="margin-bottom:20px; text-align:left; border-left:4px solid #fd7e14;">
                <p><strong>Bus:</strong> <?php echo $report['bus_number']; ?></p>
                <p><strong>Issue:</strong> <?php echo $report['issue_type']; ?></p>
                <p><strong>Description:</strong> <?php echo $report['description']; ?></p>
            </div>

            <?php echo $message; ?>

            <form action="" method="POST">
                <div class="form-group">
                    <label>Select Service Center</label>
                    <select name="service_center" required>
                        <option value="" disabled selected>Select Verified Center</option>
                        <option value="BMW Authorized Service">BMW Authorized Service (Whitefield)</option>
                        <option value="Mercedes-Benz Service">Mercedes-Benz Service (Electronic City)</option>
                        <option value="Volvo Bus Service Center">Volvo Bus Service Center (Peenya)</option>
                        <option value="Ashok Leyland Service">Ashok Leyland Service (Hosur Road)</option>
                        <option value="General Depot Workshop">General Depot Workshop (Majestic)</option>
                    </select>
                </div>

                <div class="form-group">
                    <label>Service Date</label>
                    <input type="date" name="service_date" required min="<?php echo date('Y-m-d'); ?>">
                </div>

                <div class="form-group">
                    <label>Reporting Time</label>
                    <div style="display: flex; gap: 5px;">
                        <select name="service_hour" required style="padding:10px; flex:1;">
                            <option value="" disabled selected>Hr</option>
                            <?php for($i=1; $i<=12; $i++) echo "<option value='".sprintf("%02d", $i)."'>$i</option>"; ?>
                        </select>
                        <select name="service_min" required style="padding:10px; flex:1;">
                            <option value="" disabled selected>Min</option>
                            <?php for($i=0; $i<=55; $i+=5) echo "<option value='".sprintf("%02d", $i)."'>".sprintf("%02d", $i)."</option>"; ?>
                        </select>
                        <select name="service_ampm" required style="padding:10px; flex:1;">
                            <option value="AM">AM</option>
                            <option value="PM">PM</option>
                        </select>
                    </div>
                </div>

                <button type="submit" class="btn-primary" style="background-color: #007bff; width: 100%;">CONFIRM SCHEDULE</button>
            </form>
        </div>
    </main>
</body>
</html>
