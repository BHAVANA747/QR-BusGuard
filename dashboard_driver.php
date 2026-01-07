<?php
session_start();
require 'db_connect.php';

if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'driver') {
    header("Location: login.php");
    exit();
}

// Simple logic to get a bus for the driver (In a real app, driver would be assigned a bus)
// For demo, just pick the first bus or a session bus if set
$bus_id = 1; 
$bus_number = "KA-01-F-1234"; // Default or specific
if(isset($_SESSION['bus_number'])) {
    $bus_number = $_SESSION['bus_number']; // if set during login
}

$message = "";

// Handle Maintenance Report Submission
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $issue_type = $_POST['issue_type'];
    $description = $_POST['description'];
    $proof_image_path = NULL;

    // Handle Image Upload
    if (isset($_FILES['proof_image']) && $_FILES['proof_image']['error'] == 0) {
        $target_dir = "uploads/maintenance_proofs/";
        if (!file_exists($target_dir)) {
            mkdir($target_dir, 0777, true);
        }
        $file_name = time() . "_" . basename($_FILES["proof_image"]["name"]);
        $target_file = $target_dir . $file_name;
        
        if (move_uploaded_file($_FILES["proof_image"]["tmp_name"], $target_file)) {
            $proof_image_path = $target_file;
        }
    }

    $sql = "INSERT INTO maintenance_reports (bus_id, issue_type, description, proof_image, status) 
            VALUES ('$bus_id', '$issue_type', '$description', '$proof_image_path', 'Reported')";

    if ($conn->query($sql) === TRUE) {
        $message = "<div class='alert alert-success'>Maintenance Report Submitted! Admin notified.</div>";
    } else {
        $message = "<div class='alert alert-error'>Error: " . $conn->error . "</div>";
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Driver Dashboard - QR BusGuard</title>
    <link rel="stylesheet" href="css/style.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
</head>
<body>
    <header>
        <div class="navbar">
            <div class="logo-container">
                <img src="images/logo.jpg" alt="Logo" style="width:50px; height:50px; object-fit:contain;">
            <span class="logo-text">QR BusGuard DRIVER</span>
</div>
            <ul class="nav-links">
                <li><a href="#" class="active">DASHBOARD</a></li>
                <li><a href="logout.php">LOGOUT</a></li>
            </ul>
        </div>
    </header>

    <main>
        <div style="display: flex; justify-content: space-between; align-items: flex-start; gap: 2rem; flex-wrap: wrap;">
            
            <!-- Left: Report Form -->
            <div class="form-container" style="flex: 1; margin: 0; min-width: 350px;">
                <h2 style="color: #fd7e14; margin-bottom: 10px;"><i class="fas fa-wrench"></i> Report Maintenance</h2>
                <p style="margin-bottom: 20px;"><strong>Bus Number:</strong> <?php echo $bus_number; ?></p>
                
                <?php echo $message; ?>

                <form action="" method="POST" enctype="multipart/form-data">
                    <div class="form-group">
                        <label>Issue Type</label>
                        <select name="issue_type" required>
                            <option value="" disabled selected>Select Issue</option>
                            <option value="Brake Failure">Brake Failure</option>
                            <option value="Engine Noise">Engine Noise</option>
                            <option value="Lighting Issues">Lighting Issues</option>
                            <option value="Wiper/Window Issues">Wiper/Window Issues</option>
                            <option value="Other">Other</option>
                        </select>
                    </div>

                    <div class="form-group">
                        <label>Issue Description</label>
                        <textarea name="description" rows="4" placeholder="Describe the problem..." required></textarea>
                    </div>
                    <div class="form-group">
                    <label>Admin Department</label>
                    <select name="admin_department" required>
                    <option disabled selected>Select Admin</option>
                    <option value="Lost & Found Admin - Customer Support">Lost & Found - Customer Support</option>
                    <option value="Maintenance Admin - Depot Engineering">Maintenance - Depot Engineering</option>
                    <option value="Route Admin - Scheduling Wing">Route Admin - Scheduling</option>
                    </select>
                   </div>
                    <div class="form-group">
                        <label>Upload Photo (Optional)</label>
                        <input type="file" name="proof_image" accept="image/*">
                    </div>

                    <button type="submit" class="btn-primary" style="background-color: #fd7e14; width: 100%;">SUBMIT REPORT</button>
                </form>
            </div>

            <!-- Right: My Reports & Service Schedule -->
            <div class="card" style="flex: 1.5; text-align: left; margin: 0; min-width: 400px; border-top: 5px solid #007bff;">
                <h3><i class="fas fa-history"></i> My Reports & Service Status</h3>
                
                <table style="width:100%; border-collapse: collapse; margin-top: 15px;">
                    <tr style="background:#eee;">
                        <th style="padding:10px;">Issue</th>
                        <th style="padding:10px;">Status</th>
                        <th style="padding:10px;">Service Schedule</th>
                        <th style="padding:10px;">Action</th>
                    </tr>
                    <?php
                    // Fetch reports for this specific BUS (assuming driver drives this bus)
                    $sql_reports = "SELECT * FROM maintenance_reports WHERE bus_id='$bus_id' ORDER BY report_date DESC";
                    $result = $conn->query($sql_reports);
                    
                    if ($result->num_rows > 0) {
                        while($row = $result->fetch_assoc()) {
                            echo "<tr>";
                            
                            // Issue Column
                            echo "<td style='padding:10px; border-bottom:1px solid #eee; vertical-align: middle;color:white;'>";
                            echo "<strong>" . $row['issue_type'] . "</strong><br>";
                            echo "<span style='font-size:0.9em; color:#555;'>" . $row['report_date'] . "</span><br>";
                            echo "</td>";

                            // Status Column
                            $statusColor = ($row['status'] == 'Scheduled') ? 'green' : (($row['status'] == 'Reported') ? 'orange' : 'black');
                            echo "<td style='padding:10px; border-bottom:1px solid #eee; font-weight:bold; color:$statusColor; vertical-align: middle;'>" . $row['status'] . "</td>";

                            // Service Schedule Column (Admin Reply)
                            echo "<td style='padding:10px; border-bottom:1px solid #eee; vertical-align: middle;'>";
                            if ($row['status'] == 'Scheduled') {
                                echo "<div style='background:#d4edda; padding:8px; border-radius:4px; border:1px solid #c3e6cb;'>";
                                echo "<strong>Center:</strong> " . $row['service_center'] . "<br>";
                                echo "<strong>Date:</strong> " . $row['service_date'] . "<br>";
                                echo "<strong>Time:</strong> " . date("h:i A", strtotime($row['service_time']));
                                echo "</div>";
                            } else {
                                echo "<em style='color:#999;'>Waiting for Admin...</em>";
                            }
                            echo "</td>";

                            // Actions Column
                            echo "<td style='padding:10px; border-bottom:1px solid #eee; text-align:right; vertical-align: middle; white-space: nowrap;'>";
                            echo "<div style='display: inline-flex; gap: 5px;'>"; // Use flex container for alignment
                            if ($row['proof_image']) {
                                echo "<a href='view_proof.php?file=" . urlencode($row['proof_image']) . "&source=driver' class='btn-primary' style='padding:6px; font-size:1em; width:32px; height:32px; display:inline-flex; align-items:center; justify-content:center; background:#007bff;' title='View Image'><i class='fas fa-image'></i></a>";
                            }
                            // Edit & Delete
                            echo "<a href='edit_maintenance.php?id=" . $row['id'] . "' class='btn-primary' style='padding:6px; font-size:1em; width:32px; height:32px; display:inline-flex; align-items:center; justify-content:center; background:#28a745;' title='Edit'><i class='fas fa-edit'></i></a>";
                            echo "<a href='manage_report.php?action=delete&type=maintenance&id=" . $row['id'] . "' class='btn-primary' style='padding:6px; font-size:1em; width:32px; height:32px; display:inline-flex; align-items:center; justify-content:center; background:#dc3545;' onclick='return confirm(\"Delete this report?\")' title='Delete'><i class='fas fa-trash'></i></a>";
                            echo "</div>";
                            echo "</td>";

                            echo "</tr>";
                        }
                    } else {
                        echo "<tr><td colspan='3' style='padding:10px;'>No reports submitted.</td></tr>";
                    }
                    ?>
                </table>
            </div>

        </div>
    </main>
</body>
</html>
