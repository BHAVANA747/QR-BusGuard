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
    <title>Theft Records Archive - QR BusGuard</title>
    <link rel="stylesheet" href="css/style.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
</head>
<body>
    <header>
        <div class="navbar">
            <div class="logo-container">
                <img src="images/logo.jpg" alt="Logo" style="width:50px; height:50px; object-fit:contain;">
            <span class="logo-text">THEFT RECORDS</span>
</div>
            <ul class="nav-links">
                <li><a href="dashboard_admin.php">BACK TO DASHBOARD</a></li>
            </ul>
        </div>
    </header>

    <main>
        <h2 style="margin-bottom:20px;">Archived Theft Records</h2>
        <div class="card" style="text-align:left;">
            <table style="width:100%; border-collapse: collapse;">
                <tr style="background:#ddd;">
                    <th style="padding:10px;">ID</th>
                    <th style="padding:10px;">Type</th>
                    <th style="padding:10px;">Stop</th>
                    <th style="padding:10px;">Time</th>
                    <th style="padding:10px;">Proof</th>
                    <th style="padding:10px;">Status (At Copy)</th>
                    <th style="padding:10px;">Archived At</th>
                </tr>
                <?php
                $sql = "SELECT * FROM theft_records ORDER BY archived_at DESC";
                $result = $conn->query($sql);
                if ($result->num_rows > 0) {
                    while($row = $result->fetch_assoc()) {
                        echo "<tr>";
                        echo "<td style='padding:10px; border-bottom:1px solid #eee;color:white;'>#" . $row['original_report_id'] . "</td>";
                        echo "<td style='padding:10px; border-bottom:1px solid #eee;color:white;'>" . $row['theft_type'] . "</td>";
                        echo "<td style='padding:10px; border-bottom:1px solid #eee;color:white;'>" . $row['bus_stop'] . "</td>";
                        echo "<td style='padding:10px; border-bottom:1px solid #eee;color:white;'>" . $row['incident_time'] . "</td>";
                        echo "<td style='padding:10px; border-bottom:1px solid #eee;color:white;'>";
                        if ($row['proof_image']) {
                            echo "<a href='view_proof.php?file=" . urlencode($row['proof_image']) . "' target='_blank'>View Proof</a>";
                        } else {
                            echo "-";
                        }
                        echo "</td>";
                        echo "<td style='padding:10px; border-bottom:1px solid #eee;color:white;'>" . $row['status'] . "</td>";
                        echo "<td style='padding:10px; border-bottom:1px solid #eee; font-size:0.9em; color:#666;color:white;'>" . $row['archived_at'] . "</td>";
                        echo "</tr>";
                    }
                } else {
                    echo "<tr><td colspan='7' style='padding:10px;color:white;'>No archived records.</td></tr>";
                }
                ?>
            </table>
        </div>
    </main>
</body>
</html>
