<?php
session_start();
require 'db_connect.php';

if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit();
}

$user_id = $_SESSION['user_id'];
$message = "";

// Handle Profile Update
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $name = $_POST['name'];
    $email = $_POST['email'];
    $phone = $_POST['phone'];
    
    $sql = "UPDATE users SET name='$name', email='$email', phone='$phone' WHERE id='$user_id'";
    
    if ($conn->query($sql) === TRUE) {
        $_SESSION['name'] = $name; // Update session name
        $message = "<div class='alert alert-success'>Profile updated successfully!</div>";
    } else {
        $message = "<div class='alert alert-error'>Error updating profile: " . $conn->error . "</div>";
    }
}

// Fetch Current Data
$sql = "SELECT * FROM users WHERE id='$user_id'";
$result = $conn->query($sql);
$user = $result->fetch_assoc();
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Edit Profile - QR BusGuard</title>
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
                <li><a href="dashboard_<?php echo $_SESSION['role']; ?>.php">BACK TO DASHBOARD</a></li>
            </ul>
        </div>
    </header>

    <main>
        <div class="form-container">
            <h2 style="text-align:center; margin-bottom: 20px;">Edit Profile</h2>
            <?php echo $message; ?>
            
            <form action="" method="POST">
                <div class="form-group">
                    <label>Full Name</label>
                    <input type="text" name="name" value="<?php echo $user['name']; ?>" required>
                </div>
                <div class="form-group">
                    <label>Email Address</label>
                    <input type="email" name="email" value="<?php echo $user['email']; ?>" required>
                </div>
                <div class="form-group">
                    <label>Phone Number</label>
                    <input type="text" name="phone" value="<?php echo $user['phone']; ?>" required>
                </div>
                <button type="submit" class="btn-primary" style="width:100%">UPDATE PROFILE</button>
            </form>
        </div>
    </main>
</body>
</html>
