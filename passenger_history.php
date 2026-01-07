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
    <title>My History - QR BusGuard</title>
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
            <ul class="nav-links">
                <li><a href="dashboard_passenger.php">BACK TO DASHBOARD</a></li>
                <li><a href="logout.php">LOGOUT</a></li>
            </ul>
        </div>
    </header>

    <main>
        <h2 style="text-align: center; margin-bottom: 2rem;">My Activity History</h2>

        <div class="card" style="margin-bottom: 2rem; text-align: left;" style="color:white;">
            <h3><i class="fas fa-search-location" style="color:#007bff;"></i> Lost Item Reports</h3>
            <table style="width:100%; border-collapse: collapse; margin-top: 10px;">
                <tr style="background:#eee;">
                    <th style="padding:10px;">Your Item</th>
                    <th style="padding:10px;">Status</th>
                    <th style="padding:10px;">Found Item (Verification)</th>
                    <th style="padding:10px;">Return Details</th>
                    <th style="padding:10px;">Actions</th>
                </tr>
                <?php
                // Join to get the Found Item Image if a match exists
                $sql = "SELECT l.*, f.image_path as found_image_path 
                        FROM lost_items l 
                        LEFT JOIN found_items f ON f.matched_lost_item_id = l.id 
                        WHERE l.user_id='$user_id' 
                        ORDER BY l.report_date DESC";
                        
                $result = $conn->query($sql);
                if ($result->num_rows > 0) {
                    while($row = $result->fetch_assoc()) {
                        $statusColor = ($row['status'] == 'Found') ? 'green' : 'orange';
                        
                        echo "<tr>";
                        echo "<td style='padding:10px; border-bottom:1px solid #eee; vertical-align:top;color:white;'>";
                        echo "<strong>" . $row['item_name'] . "</strong><br>";
                        echo "<img src='" . $row['image_path'] . "' style='width:60px; height:60px; object-fit:cover; border-radius:4px; margin-top:5px; border:1px solid #ddd;'>";
                        echo "</td>";

                        echo "<td style='padding:10px; border-bottom:1px solid #eee; font-weight:bold; color:$statusColor; vertical-align:top;color:white;'>" . $row['status'] . "</td>";
                        
                        echo "<td style='padding:10px; border-bottom:1px solid #eee; vertical-align:top;'>";
                        if ($row['status'] == 'Found' && !empty($row['found_image_path'])) {
                             echo "<img src='" . $row['found_image_path'] . "' style='width:60px; height:60px; object-fit:cover; border-radius:4px; border:2px solid green;' title='Found Item'>";
                             echo "<br><small style='color:green;'>Matched by AI</small>";
                        } elseif ($row['status'] == 'Found') {
                            echo "<em>Image not available</em>";
                        } else {
                             echo "-";
                        }
                        echo "</td>";

                        echo "<td style='padding:10px; border-bottom:1px solid #eee; vertical-align:top; color:white;'>";
                        if ($row['status'] == 'Found') {
                            echo "<strong>Location:</strong> " . ($row['return_location'] ? $row['return_location'] : 'N/A') . "<br>";
                            echo "<strong>Date:</strong> " . ($row['return_date'] ? $row['return_date'] : 'N/A') . "<br>";
                            echo "<strong>Time:</strong> " . ($row['return_time'] ? date("g:i A", strtotime($row['return_time'])) : 'N/A');
                        } else {
                            echo "<em>Waiting for update...</em>";
                        }
                        echo "</td>";
                        
                        echo "<td style='padding:10px; border-bottom:1px solid #eee; vertical-align:top;color:white;'>";
                        echo "<a href='manage_report.php?action=edit&type=lost&id=" . $row['id'] . "' style='color:blue; margin-right:10px;'><i class='fas fa-edit'></i> Edit</a>";
                        echo "<a href='manage_report.php?action=delete&type=lost&id=" . $row['id'] . "' style='color:red;' onclick=\"return confirm('Delete this report?')\"><i class='fas fa-trash'></i> Delete</a>";
                        echo "</td>";

                        echo "</tr>";
                    }
                } else {
                    echo "<tr><td colspan='5' style='padding:10px; color:white;'>No items reported lost.</td></tr>";
                }
                ?>
            </table>
        </div>

        <div class="card" style="margin-bottom: 2rem; text-align: left;">
            <h3><i class="fas fa-exclamation-triangle" style="color:#dc3545;"></i> Theft Alerts</h3>
            <table style="width:100%; border-collapse: collapse; margin-top: 10px;">
                <tr style="background:#eee;">
                    <th style="padding:10px;">Location</th>
                    <th style="padding:10px;">Status</th>
                    <th style="padding:10px;">Date</th>
                    <th style="padding:10px;">Actions</th>
                </tr>
                <?php
                $sql = "SELECT * FROM theft_reports WHERE user_id='$user_id' ORDER BY report_date DESC";
                $result = $conn->query($sql);
                if ($result->num_rows > 0) {
                    while($row = $result->fetch_assoc()) {
                        echo "<tr>";
                        echo "<td style='padding:10px; border-bottom:1px solid #eee;color:white;'>" . $row['bus_stop'] . "</td>";
                        echo "<td style='padding:10px; border-bottom:1px solid #eee;color:white;'>" . $row['status'] . "</td>";
                        echo "<td style='padding:10px; border-bottom:1px solid #eee;color:white;'>" . $row['report_date'] . "</td>";
                        echo "<td style='padding:10px; border-bottom:1px solid #eee;color:white;'>";
                        echo "<a href='manage_report.php?action=edit&type=theft&id=" . $row['id'] . "' style='color:blue; margin-right:10px;'><i class='fas fa-edit'></i> Edit</a>";
                        echo "<a href='manage_report.php?action=delete&type=theft&id=" . $row['id'] . "' style='color:red;' onclick=\"return confirm('Delete this report?')\"><i class='fas fa-trash'></i> Delete</a>";
                        echo "</td>";
                        echo "</tr>";
                    }
                } else {
                    echo "<tr><td colspan='4' style='padding:10px; color:white;'>No theft alerts sent.</td></tr>";
                }
                ?>
            </table>
        </div>

    </main>
</body>
</html>
