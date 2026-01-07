<?php
session_start();
require 'db_connect.php';

if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit();
}

$type = $_GET['type'] ?? '';
$id = $_GET['id'] ?? '';
$user_id = $_SESSION['user_id'];
$message = "";
$data = null;

// Fetch Data
if ($type == 'lost') {
    $sql = "SELECT * FROM lost_items WHERE id='$id' AND user_id='$user_id'";
    $result = $conn->query($sql);
    $data = $result->fetch_assoc();
} elseif ($type == 'theft') {
    $sql = "SELECT * FROM theft_reports WHERE id='$id' AND user_id='$user_id'";
    $result = $conn->query($sql);
    $data = $result->fetch_assoc();
}

if (!$data) {
    echo "Report not found or permission denied.";
    exit();
}

// Handle Update
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    if ($type == 'lost') {
        $name = $_POST['item_name'];
        $desc = $_POST['description'];
        $sql = "UPDATE lost_items SET item_name='$name', description='$desc' WHERE id='$id'";
    } elseif ($type == 'theft') {
        $stop = $_POST['bus_stop'];
        $desc = $_POST['description'];
        $sql = "UPDATE theft_reports SET bus_stop='$stop', description='$desc' WHERE id='$id'";
    }
    
    if ($conn->query($sql) === TRUE) {
        $message = "<div class='alert alert-success'>Report updated! <a href='passenger_history.php'>Go Back</a></div>";
        // Refresh data
        if ($type == 'lost') $data['item_name'] = $name;
        if ($type == 'theft') $data['bus_stop'] = $stop;
        $data['description'] = $desc;
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
</head>
<body>
    <header>
        <div class="navbar">
            <div class="logo-container">
                <img src="images/logo.jpg" alt="Logo" style="width:50px; height:50px; object-fit:contain;">
            <span class="logo-text">QR BusGuard</span>
</div>
            <ul class="nav-links">
                <li><a href="passenger_history.php">BACK TO HISTORY</a></li>
            </ul>
        </div>
    </header>

    <main>
        <div class="form-container">
            <h2>Edit <?php echo ucfirst($type); ?> Report</h2>
            <?php echo $message; ?>
            
            <form action="" method="POST">
                <?php if ($type == 'lost'): ?>
                    <div class="form-group">
                        <label>Item Name</label>
                        <input type="text" name="item_name" value="<?php echo $data['item_name']; ?>" required>
                    </div>
                <?php elseif ($type == 'theft'): ?>
                    <div class="form-group">
                        <label>Bus Stop</label>
                        <input type="text" name="bus_stop" value="<?php echo $data['bus_stop']; ?>" required>
                    </div>
                <?php endif; ?>
                
                <div class="form-group">
                    <label>Description</label>
                    <textarea name="description" rows="4" required><?php echo $data['description']; ?></textarea>
                </div>
                
                <button type="submit" class="btn-primary" style="width:100%">UPDATE REPORT</button>
            </form>
        </div>
    </main>
</body>
</html>
