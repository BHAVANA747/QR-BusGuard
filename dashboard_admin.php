<?php
session_start();
require 'db_connect.php';

if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'admin') {
    header("Location: login.php");
    exit();
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Admin Dashboard - QR BusGuard</title>
    <link rel="stylesheet" href="css/style.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
</head>
<body>
    <header>
        <div class="navbar"><div class="logo-container">
                <img src="images/logo.jpg" alt="Logo" style="width:50px; height:50px; object-fit:contain;">
            <span class="logo-text">QR BusGuard ADMIN</span>
</div>
            <ul class="nav-links">
                <li><a href="#" class="active">DASHBOARD</a></li>
                <li><a href="qr_generator.php">QR MANAGER</a></li>
                <li><a href="logout.php">LOGOUT</a></li>
            </ul>
        </div>
    </header>

    <main>
        <div style="display:flex; justify-content:space-between; align-items:center; margin-bottom:20px;">
            <h2>System Overview</h2>
            <a href="theft_records.php" class="btn-primary" style="background:#444;"><i class="fas fa-archive"></i> View Theft Records</a>
        </div>

        <div style="display: flex; gap: 2rem; flex-wrap: wrap;">
            <!-- Theft Alerts -->
            <div class="card" style="flex: 1; text-align: left; border-top: 4px solid #dc3545;">
                <h3><i class="fas fa-exclamation-triangle" style="color:#dc3545;"></i> Theft Alerts</h3>
                <table style="width:100%; margin-top:15px; border-collapse: collapse;">
                    <tr style="background:#f8f9fa;">
                        <th style="padding:10px;">Bus</th>
                        <th style="padding:10px;">Type / Stop</th>
                        <th style="padding:10px;">Time / Proof</th>
                        <th style="padding:10px;">Status</th>
                        <th style="padding:10px;">Action</th>
                    </tr>
                    <?php
                    $sql = "SELECT t.*, b.bus_number FROM theft_reports t JOIN buses b ON t.bus_id = b.id ORDER BY t.report_date DESC";
                    $result = $conn->query($sql);
                    while($row = $result->fetch_assoc()) {
                        echo "<tr>";
                        echo "<td style='padding:10px; border-bottom:1px solid #eee;color:white;'>" . $row['bus_number'] . "</td>";
                        
                        echo "<td style='padding:10px; border-bottom:1px solid #eee;color:white;'>";
                        echo "<strong>" . ($row['theft_type'] ? $row['theft_type'] : 'Theft') . "</strong><br>";
                        echo "<small>" . $row['bus_stop'] . "</small>";
                        echo "</td>";

                        echo "<td style='padding:10px; border-bottom:1px solid #eee; font-size:0.9em;color:white;'>";
                        echo ($row['incident_time'] ? date("d M H:i", strtotime($row['incident_time'])) : '-') . "<br>";
                        if ($row['proof_image']) echo "<a href='view_proof.php?file=" . urlencode($row['proof_image']) . "' target='_blank' style='color:blue;'>View Proof</a>";
                        echo "</td>";
                        echo "<td style='padding:10px; border-bottom:1px solid #eee;color:white;'>" . $row['status'] . "</td>";
                        
                        echo "<td style='padding:10px; border-bottom:1px solid #eee;color:white;'>";
                        echo "<a href='update_status.php?type=theft&id=" . $row['id'] . "' class='btn-primary' style='padding:5px 10px; font-size:0.8rem; display:block; margin-bottom:5px; text-align:center;color:white;'>Update</a>";
                        echo "<a href='copy_record.php?id=" . $row['id'] . "' class='btn-primary' style='padding:5px 10px; font-size:0.8rem; background:#6c757d; display:block; margin-bottom:5px; text-align:center;color:white;'>Copy</a>";
                        echo "<a href='manage_report.php?action=delete&type=theft&id=" . $row['id'] . "' class='btn-primary' style='padding:5px 10px; font-size:0.8rem; background:#dc3545; display:block; text-align:center;color:white;' onclick='return confirm(\"Delete this report?\")'>Delete</a>";
                        echo "</td>";
                        
                        echo "</tr>";
                    }
                    ?>
                </table>
            </div>

            <!-- Driver Maintenance Reports -->
            <div class="card" style="flex: 1; min-width: 300px; text-align: left; border-top: 5px solid #fd7e14;">
                <h3><i class="fas fa-tools"></i> Driver Maintenance Reports</h3>
                <table style="width:100%; border-collapse: collapse; margin-top: 10px;">
                    <tr style="background:#f8f9fa;">
                        <th style="padding:10px;">Bus</th>
                        <th style="padding:10px;">Issue / Proof</th>
                        <th style="padding:10px;">Status</th>
                        <th style="padding:10px;">Action</th>
                    </tr>
                    <?php
                    $sql_m = "SELECT m.*, b.bus_number FROM maintenance_reports m JOIN buses b ON m.bus_id = b.id ORDER BY m.report_date DESC";
                    $result_m = $conn->query($sql_m);
                    while($row = $result_m->fetch_assoc()) {
                        echo "<tr>";
                        echo "<td style='padding:10px; border-bottom:1px solid #eee;color:white;'>" . $row['bus_number'] . "</td>";
                        
                        echo "<td style='padding:10px; border-bottom:1px solid #eee;color:white;'>";
                        echo "<strong>" . $row['issue_type'] . "</strong><br>";
                        if ($row['proof_image']) echo "<a href='view_proof.php?file=" . urlencode($row['proof_image']) . "' target='_blank' style='font-size:0.8em; color:blue;'>View Image</a>";
                        else echo "<small>No Image</small>";
                        echo "</td>";

                        echo "<td style='padding:10px; border-bottom:1px solid #eee;color:white;'>" . $row['status'] . "</td>";
                        
                        echo "<td style='padding:10px; border-bottom:1px solid #eee;color:white;'>";
                        if ($row['status'] != 'Scheduled') {
                            echo "<a href='schedule_service.php?id=" . $row['id'] . "' class='btn-primary' style='padding:5px 10px; font-size:0.8rem; background:#007bff;'>Reply</a>";
                        } else {
                            echo "<span style='color:green;'><i class='fas fa-check'></i> Scheduled</span>";
                        }
                        echo "</td>";
                        echo "</tr>";
                    }
                    ?>
                </table>
            </div>

        </div>
    </main>
</body>
</html>
