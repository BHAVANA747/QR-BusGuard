<?php
session_start();
require 'db_connect.php';

if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'conductor') {
    header("Location: login.php");
    exit();
}

$message = "";
$match_result = null;
$matched_passenger = null;

// Handle Found Item Upload
if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_FILES["found_image"])) {
    $conductor_id = $_SESSION['user_id'];
    // Fetch a valid bus ID (Demo workaround: just get the first bus)
    $bus_query = "SELECT id FROM buses LIMIT 1";
    $bus_result = $conn->query($bus_query);
    if ($bus_result->num_rows > 0) {
        $bus_row = $bus_result->fetch_assoc();
        $bus_id = $bus_row['id'];
    } else {
        $bus_id = 1; // Fallback if DB empty (unlikely after setup)
    }
    
    // Upload Found Image
    $target_dir = "uploads/found/";
    $found_image_name = time() . "_" . basename($_FILES["found_image"]["name"]);
    $found_image_path = $target_dir . $found_image_name;
    
    if (move_uploaded_file($_FILES["found_image"]["tmp_name"], $found_image_path)) {
        // Insert into Found Items
        $sql = "INSERT INTO found_items (conductor_id, bus_id, image_path, status) VALUES ('$conductor_id', '$bus_id', '$found_image_path', 'Unclaimed')";
        if($conn->query($sql) === TRUE) {
            $found_id = $conn->insert_id;
            
            // --- AI MATCHING LOGIC ---
            // Fetch all 'Lost' items to compare
            $sql_lost = "SELECT * FROM lost_items WHERE status='Lost'";
            $result_lost = $conn->query($sql_lost);
            
            $best_score = -1.0;
            $best_match_id = null;
            
            // Absolute paths for Python
            $abs_found_path = __DIR__ . "/" . $found_image_path;
            
            while($lost_row = $result_lost->fetch_assoc()) {
                $abs_lost_path = __DIR__ . "/" . $lost_row['image_path'];
                
                // Call Python Script
                // Assuming python is in PATH, if not need absolute path e.g., C:\xampp\python\python.exe
                // Using generic 'python', user said they installed it.
                // Command: python python/image_match.py <lost> <found>
                $command = "python python/image_match.py \"$abs_lost_path\" \"$abs_found_path\"";
                $output = shell_exec($command);
                $json = json_decode($output, true);
                
                if ($json && isset($json['score'])) {
                    if ($json['score'] > $best_score) {
                        $best_score = $json['score'];
                        if ($json['is_match']) {
                            $best_match_id = $lost_row['id'];
                        }
                    }
                }
            }
            
            if ($best_match_id) {
                // Fetch Passenger Details
                $msg_sql = "SELECT u.name, u.phone, l.description FROM lost_items l JOIN users u ON l.user_id = u.id WHERE l.id='$best_match_id'";
                $matched_passenger = $conn->query($msg_sql)->fetch_assoc();
                $match_result = "Match Found! Score: " . round($best_score, 2);
                
                // Update found item with match
                $conn->query("UPDATE found_items SET matched_lost_item_id='$best_match_id' WHERE id='$found_id'");
            } else {
                $message = "<div class='alert alert-info'>Item Uploaded. No matching lost report found yet.</div>";
            }
            
        } else {
            $message = "<div class='alert alert-error'>DB Error: " . $conn->error . "</div>";
        }
    } else {
        $message = "<div class='alert alert-error'>Upload Failed.</div>";
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Conductor Dashboard - QR BusGuard</title>
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
                <li><a href="#" class="active">CONDUCTOR</a></li>
                <li><a href="logout.php">LOGOUT</a></li>
            </ul>
        </div>
    </header>

    <main>
        <h2 style="margin-bottom: 20px;">Conductor Dashboard</h2>
        
        <div style="display: flex; gap: 2rem; flex-wrap: wrap;">
            <!-- Left: Lost Reports List -->
            <div style="flex: 1; min-width: 300px;">
                <div class="card" style="text-align: left;">
                    <h3><i class="fas fa-list"></i> Recent Lost Reports</h3>
                    <?php
                    $sql = "SELECT l.*, u.name FROM lost_items l JOIN users u ON l.user_id = u.id WHERE l.status='Lost' ORDER BY l.report_date DESC LIMIT 5";
                    $result = $conn->query($sql);
                    if ($result->num_rows > 0) {
                        echo "<ul style='list-style:none; padding:0;color:white;'>";
                        while($row = $result->fetch_assoc()) {
                            echo "<li style='border-bottom:1px solid #eee; padding: 10px 0;color:white;'>";
                            echo "<strong>" . $row['item_name'] . "</strong><br>";
                            echo "<small>Reported by: " . $row['name'] . "</small><br>";
                            echo "<img src='" . $row['image_path'] . "' style='width:50px; height:50px; object-fit:cover; border-radius:4px; margin-top:5px;color:white;'>";
                            // Delete button (Red Trash Icon)
                            echo "<a href='manage_report.php?action=delete&type=lost&id=" . $row['id'] . "' onclick='return confirm(\"Delete this report?\")' style='float:right; color:#dc3545; font-size:1.2rem; margin-top:10px;' title='Delete'><i class='fas fa-trash'></i></a>";
                            echo "</li>";
                        }
                        echo "</ul>";
                    } else {
                        echo "<p style='color:white;'>No recent lost reports.</p>";
                    }
                    ?>
                </div>
            </div>

            <!-- Right: Upload Found Item & Match -->
            <div style="flex: 1; min-width: 300px;">
                <div class="form-container" style="max-width: 100%; margin:0;">
                    <h3><i class="fas fa-camera"></i> Verify Found Item</h3>
                    <p>Upload a photo of an item found on the bus to check for matches.</p>
                    
                    <?php echo $message; ?>
                    
                    <?php if ($matched_passenger): ?>
                        <div class="alert alert-success" style="border-left: 5px solid #28a745;">
                            <h4><i class="fas fa-check-circle"></i> AI MATCH FOUND!</h4>
                            <p><strong>Score:</strong> <?php echo round($best_score, 2); ?></p>
                            <hr style="margin: 10px 0; border:0; border-top:1px solid #c3e6cb;">
                            <p><strong>Owner:</strong> <?php echo $matched_passenger['name']; ?></p>
                            <p><strong>Phone:</strong> <?php echo $matched_passenger['phone']; ?></p>
                            <p><strong>Description:</strong> <?php echo $matched_passenger['description']; ?></p>
                            <form action="confirm_return.php" method="POST" style="margin-top:15px;">
                                <input type="hidden" name="found_id" value="<?php echo $found_id; ?>">
                                
                                <label style="display:block; margin-top:10px;">Pick-up Location:</label>
                                <select name="return_location" required style="width:100%; padding:8px; margin-bottom:5px;">
                                    <option value="Majestic Bus Station">Majestic Bus Station</option>
                                    <option value="Shivajinagar Bus Station">Shivajinagar Bus Station</option>
                                    <option value="K.R. Market">K.R. Market</option>
                                    <option value="Shantinagar TTMC">Shantinagar TTMC</option>
                                    <option value="Banashankari TTMC">Banashankari TTMC</option>
                                    <option value="Jayanagar TTMC">Jayanagar TTMC</option>
                                    <option value="Yeshwanthpur TTMC">Yeshwanthpur TTMC</option>
                                    <option value="Peenya Basaveshwara Bus Station">Peenya Basaveshwara Bus Station</option>
                                    <option value="Vijayanagar TTMC">Vijayanagar TTMC</option>
                                    <option value="Domlur TTMC">Domlur TTMC</option>
                                    <option value="Whitefield TTMC">Whitefield TTMC</option>
                                    <option value="Kengeri TTMC">Kengeri TTMC</option>
                                    <option value="Bannerghatta National Park">Bannerghatta National Park</option>
                                    <option value="Silk Board Junction">Silk Board Junction</option>
                                    <option value="Electronic City Phase 1">Electronic City Phase 1</option>
                                    <option value="Marathahalli Bridge">Marathahalli Bridge</option>
                                    <option value="Tin Factory">Tin Factory</option>
                                    <option value="Hebbal Bus Stop">Hebbal Bus Stop</option>
                                    <option value="Mekhri Circle">Mekhri Circle</option>
                                    <option value="Central Silk Board">Central Silk Board</option>
                                </select>

                                <label style="display:block;">Pick-up Date:</label>
                                <input type="date" name="return_date" required style="width:100%; padding:8px; margin-bottom:5px;">

                                <label style="display:block;">Pick-up Time:</label>
                                <div style="display: flex; gap: 5px; margin-bottom: 10px;">
                                    <select name="return_hour" required style="padding:8px; flex:1;">
                                        <option value="" disabled selected>Hr</option>
                                        <?php for($i=1; $i<=12; $i++) echo "<option value='".sprintf("%02d", $i)."'>$i</option>"; ?>
                                    </select>
                                    <select name="return_min" required style="padding:8px; flex:1;">
                                        <option value="" disabled selected>Min</option>
                                        <?php for($i=0; $i<=55; $i+=5) echo "<option value='".sprintf("%02d", $i)."'>".sprintf("%02d", $i)."</option>"; ?>
                                    </select>
                                    <select name="return_ampm" required style="padding:8px; flex:1;">
                                        <option value="AM">AM</option>
                                        <option value="PM">PM</option>
                                    </select>
                                </div>

                                <button type="submit" class="btn-primary" style="background-color: #28a745; width:100%">SCHEDULE & CONFIRM RETURN</button>
                            </form>
                        </div>
                    <?php endif; ?>

                    <form action="" method="POST" enctype="multipart/form-data">
                        <div class="form-group">
                            <label>Upload Found Item Image</label>
                            <input type="file" name="found_image" required accept="image/*">
                        </div>
                        <button type="submit" class="btn-primary" style="width: 100%;">ANALYZE & MATCH</button>
                    </form>
                </div>
            </div>
        </div>
    </main>
</body>
</html>
