<?php
session_start();
require 'db_connect.php';

if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'driver') {
    die("Access Denied");
}

$id = $_GET['id'] ?? null;
if (!$id) die("Invalid ID");

$message = "";

// Fetch existing report
$sql = "SELECT * FROM maintenance_reports WHERE id='$id'";
$result = $conn->query($sql);
$report = $result->fetch_assoc();

if (!$report) die("Report not found");

// Update Logic
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $issue_type = $_POST['issue_type'];
    $description = $_POST['description'];
    
    // Existing logic for file upload if needed, but let's keep it simple for edit
    // Allowing re-upload if they want to replace image
    $proof_image_path = $report['proof_image']; // Default to existing
    if (isset($_FILES['proof_image']) && $_FILES['proof_image']['error'] == 0) {
        $target_dir = "uploads/maintenance_proofs/";
        $file_name = time() . "_" . basename($_FILES["proof_image"]["name"]);
        $target_file = $target_dir . $file_name;
        if (move_uploaded_file($_FILES["proof_image"]["tmp_name"], $target_file)) {
            $proof_image_path = $target_file;
        }
    }

    $sql_update = "UPDATE maintenance_reports SET 
                   issue_type='$issue_type', 
                   description='$description', 
                   proof_image='$proof_image_path' 
                   WHERE id='$id'";

    if ($conn->query($sql_update) === TRUE) {
        header("Location: dashboard_driver.php?msg=updated");
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
    <title>Edit Report - QR BusGuard</title>
    <link rel="stylesheet" href="css/style.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
</head>
<body>
    <header>
        <div class="navbar">
            <div class="logo-container">
                <img src="images/logo.jpg" alt="Logo" style="width:50px; height:50px; object-fit:contain;">
            <span class="logo-text">EDIT REPORT</span>
</div>
            <ul class="nav-links">
                <li><a href="dashboard_driver.php">CANCEL</a></li>
            </ul>
        </div>
    </header>

    <main>
        <div class="form-container">
            <h2><i class="fas fa-edit"></i> Edit Maintenance Report</h2>
            <?php echo $message; ?>

            <form action="" method="POST" enctype="multipart/form-data">
                <div class="form-group">
                    <label>Issue Type</label>
                    <select name="issue_type" required>
                        <option value="Brake Failure" <?php if($report['issue_type']=='Brake Failure') echo 'selected'; ?>>Brake Failure</option>
                        <option value="Engine Noise" <?php if($report['issue_type']=='Engine Noise') echo 'selected'; ?>>Engine Noise</option>
                        <option value="Lighting Issues" <?php if($report['issue_type']=='Lighting Issues') echo 'selected'; ?>>Lighting Issues</option>
                        <option value="Wiper/Window Issues" <?php if($report['issue_type']=='Wiper/Window Issues') echo 'selected'; ?>>Wiper/Window Issues</option>
                        <option value="Other" <?php if($report['issue_type']=='Other') echo 'selected'; ?>>Other</option>
                    </select>
                </div>

                <div class="form-group">
                    <label>Description</label>
                    <textarea name="description" rows="4" required><?php echo $report['description']; ?></textarea>
                </div>

                <div class="form-group">
                    <label>Update Photo (Optional)</label>
                    <?php if ($report['proof_image']) echo "<br><small>Current: <a href='view_proof.php?file=".urlencode($report['proof_image'])."&source=driver' target='_blank'>View Image</a></small>"; ?>
                    <input type="file" name="proof_image" accept="image/*">
                </div>

                <button type="submit" class="btn-primary" style="background-color: #28a745; width: 100%;">UPDATE REPORT</button>
            </form>
        </div>
    </main>
</body>
</html>
