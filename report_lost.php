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
    $item_name = $_POST['item_name'];
    $description = $_POST['description'];
    
    // Image Upload
    $target_dir = "uploads/lost/";
    $image_name = time() . "_" . basename($_FILES["item_image"]["name"]);
    $target_file = $target_dir . $image_name;
    $uploadOk = 1;
    $imageFileType = strtolower(pathinfo($target_file,PATHINFO_EXTENSION));

    // Check if image file is a actual image or fake image
    $check = getimagesize($_FILES["item_image"]["tmp_name"]);
    if($check !== false) {
        $uploadOk = 1;
    } else {
        $message = "<div class='alert alert-error'>File is not an image.</div>";
        $uploadOk = 0;
    }

    if ($uploadOk == 1) {
        if (move_uploaded_file($_FILES["item_image"]["tmp_name"], $target_file)) {
            $sql = "INSERT INTO lost_items (user_id, bus_id, item_name, description, image_path) VALUES ('$user_id', '$bus_id', '$item_name', '$description', '$target_file')";
            
            if ($conn->query($sql) === TRUE) {
                $message = "<div class='alert alert-success'>Lost Item Reported! Please keep checking your status.</div>";
            } else {
                $message = "<div class='alert alert-error'>Error: " . $conn->error . "</div>";
            }
        } else {
            $message = "<div class='alert alert-error'>Sorry, there was an error uploading your file.</div>";
        }
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Report Lost Item - QR BusGuard</title>
    <link rel="stylesheet" href="css/style.css">
</head>
<body>
    <header>
        <div class="navbar"><div class="logo-container">
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
            <h2 style="color: #007bff; text-align: center;"><i class="fas fa-search"></i> Report Lost Item</h2>
            <p style="text-align: center; margin-bottom: 20px;">Bus: <strong><?php echo $_SESSION['bus_number']; ?></strong></p>
            <?php echo $message; ?>
            
            <form action="" method="POST" enctype="multipart/form-data">
                <div class="form-group">
                    <label>Item Name</label>
                    <input type="text" name="item_name" required placeholder="e.g. Blue Backpack">
                </div>
                <div class="form-group">
                    <label>Description (Brand, Content, etc.)</label>
                    <textarea name="description" rows="3" required></textarea>
                </div>
                <div class="form-group">
                    <label>Upload Image (Important for AI Match)</label>
                    <input type="file" name="item_image" required accept="image/*">
                </div>
                <button type="submit" class="btn-primary" style="background-color: #007bff; width: 100%;">SUBMIT REPORT</button>
            </form>
        </div>
    </main>
</body>
</html>
