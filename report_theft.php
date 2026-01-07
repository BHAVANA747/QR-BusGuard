<?php
session_start();
require 'db_connect.php';

if (!isset($_SESSION['user_id']) || !isset($_SESSION['bus_id'])) {
    header("Location: dashboard_passenger.php");
    exit();
}

$message = "";
$bus_stop = "";

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $user_id = $_SESSION['user_id'];
    $bus_id = $_SESSION['bus_id'];
    $bus_stop = $_POST['bus_stop'];
    $description = $_POST['description'];
    $theft_type = $_POST['theft_type'];
    
    // Combine Date and Time components
    $date = $_POST['incident_date'];
    $hour = $_POST['incident_hour'];
    $min = $_POST['incident_min'];
    $ampm = $_POST['incident_ampm'];
    
    // Convert to 24-hour format for DB
    $time_string = "$dateStr $hour:$min $ampm";
    $incident_time = date("Y-m-d H:i:s", strtotime("$date $hour:$min $ampm"));
    
    $proof_image_path = NULL;

    // Handle Image Upload
    if (isset($_FILES['proof_image']) && $_FILES['proof_image']['error'] == 0) {
        $target_dir = "uploads/theft_proofs/";
        if (!file_exists($target_dir)) {
            mkdir($target_dir, 0777, true);
        }
        $file_name = time() . "_" . basename($_FILES["proof_image"]["name"]);
        $target_file = $target_dir . $file_name;
        
        if (move_uploaded_file($_FILES["proof_image"]["tmp_name"], $target_file)) {
            $proof_image_path = $target_file;
        }
    }

    $sql = "INSERT INTO theft_reports (user_id, bus_id, bus_stop, description, theft_type, incident_time, proof_image, status) 
            VALUES ('$user_id', '$bus_id', '$bus_stop', '$description', '$theft_type', '$incident_time', '$proof_image_path', 'Pending')";

    if ($conn->query($sql) === TRUE) {
        $message = "<div class='alert alert-success'>Alert Sent! Admin has been notified.</div>";
    } else {
        $message = "<div class='alert alert-error'>Error: " . $conn->error . "</div>";
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Report Theft - QR BusGuard</title>
    <link rel="stylesheet" href="css/style.css">
</head>
<body>
    <header>
        <div class="navbar">
            <div class="logo-container">
                <img src="images/logo.jpg" alt="Logo" style="width:50px; height:50px; object-fit:contain;">
            <span class="logo-text">QR BusGuard</span>
</div>
            <ul class="nav-links">
                <li><a href="scan_handler.php">Back to Bus</a></li>
            </ul>
        </div>
    </header>

    <main>
        <div class="form-container">
            <h2 style="color: #dc3545; text-align: center;"><i class="fas fa-exclamation-triangle"></i> Theft Emergency</h2>
            <p style="text-align: center; margin-bottom: 20px;">Bus: <strong><?php echo $_SESSION['bus_number']; ?></strong></p>
            <?php echo $message; ?>
            
            <form action="" method="POST" enctype="multipart/form-data">
                <div class="form-group">
                    <label>Theft Type</label>
                    <select name="theft_type" required>
                        <option value="" disabled selected>Select Theft Type</option>
                        <option value="Stolen Jewelry">Stolen Jewelry (Gold Chain/Snatching)</option>
                        <option value="Pickpocketing">Pickpocketing (Wallet/Cash)</option>
                        <option value="Mobile Theft">Mobile Theft (Phone Stolen)</option>
                        <option value="Bag Tampering">Bag Tampering</option>
                    </select>
                </div>

                <div class="form-group">
                    <label>Current Bus Stop / Location</label>
                    <select name="bus_stop" required>
                        <option value="" disabled selected>Select Current Stop</option>
                        <option value="Majestic">Majestic</option>
                        <option value="Shivajinagar">Shivajinagar</option>
                        <option value="K.R. Market">K.R. Market</option>
                        <option value="Silk Board">Silk Board</option>
                        <option value="Marathahalli">Marathahalli</option>
                        <option value="Tin Factory">Tin Factory</option>
                        <option value="K.R. Puram">K.R. Puram</option>
                        <option value="Hebbal">Hebbal</option>
                        <option value="Banashankari">Banashankari</option>
                        <option value="Electronic City">Electronic City</option>
                        <option value="Whitefield">Whitefield</option>
                        <option value="Jayanagar">Jayanagar</option>
                        <option value="Indiranagar">Indiranagar</option>
                        <option value="Koramangala">Koramangala</option>
                        <option value="M.G. Road">M.G. Road</option>
                        <option value="Yeshwanthpur">Yeshwanthpur</option>
                    </select>
                </div>

                <div class="form-group">
                    <label>Incident Date</label>
                    <input type="date" name="incident_date" required>
                </div>

                <div class="form-group">
                    <label>Incident Time</label>
                    <div style="display: flex; gap: 10px;">
                        <select name="incident_hour" style="flex:1;" required>
                            <option value="" disabled selected>Hr</option>
                            <?php for($i=1; $i<=12; $i++) echo "<option value='".sprintf("%02d", $i)."'>$i</option>"; ?>
                        </select>
                        <select name="incident_min" style="flex:1;" required>
                            <option value="" disabled selected>Min</option>
                            <?php for($i=0; $i<=55; $i+=5) echo "<option value='".sprintf("%02d", $i)."'>".sprintf("%02d", $i)."</option>"; ?>
                        </select>
                        <select name="incident_ampm" style="flex:1;" required>
                            <option value="AM">AM</option>
                            <option value="PM">PM</option>
                        </select>
                    </div>
                </div>

                <div class="form-group">
                    <label>Incident Details</label>
                    <textarea name="description" rows="4" placeholder="Briefly describe what happened..."></textarea>
                </div>

                <div class="form-group">
                    <label>Upload Proof </label>
                    <input type="file" name="proof_image" accept="image/*" required>
                </div>
                <button type="submit" class="btn-primary" style="background-color: #dc3545; width: 100%;">SEND ALERT</button>
            </form>
        </div>
    </main>
</body>
</html>
