<?php
session_start();
require 'db_connect.php';

$message = "";

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $email = $_POST['email'];
    $password = $_POST['password'];

    $sql = "SELECT * FROM users WHERE email='$email'";
    $result = $conn->query($sql);

    if ($result->num_rows == 1) {
        $row = $result->fetch_assoc();
        if (password_verify($password, $row['password'])) {
            $_SESSION['user_id'] = $row['id'];
            $_SESSION['name'] = $row['name'];
            $_SESSION['role'] = $row['role'];
            
            header("Location: dashboard_" . $row['role'] . ".php");
            exit();
        } else {
            $message = "<div class='alert alert-error'>Invalid password!</div>";
        }
    } else {
        $message = "<div class='alert alert-error'>User not found!</div>";
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login - QR BusGuard</title>
    <link rel="stylesheet" href="css/style.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
</head>
<body>
    <header>
        <div class="navbar">
            <div class="logo-container">
                <div class="logo-container">
                <img src="images/logo.jpg" alt="Logo" style="width:50px; height:50px; object-fit:contain;">
                <span class="logo-text">QR BusGuard</span>
</div>
            </div>
            <ul class="nav-links">
                <li><a href="index.php">HOME</a></li>
                <li><a href="register.php">REGISTER</a></li>
                <li><a href="login.php" class="active">LOGIN</a></li>
            </ul>
        </div>
    </header>

    <main>
        <div class="form-container">
            <h2 style="text-align:center; margin-bottom: 20px;">Login</h2>
            <?php echo $message; ?>
            <form action="" method="POST">
                <div class="form-group">
                    <label>Email Address</label>
                    <input type="email" name="email" required>
                </div>
                <div class="form-group">
                    <label>Password</label>
                    <input type="password" name="password" required>
                </div>
                <button type="submit" class="btn-primary" style="width:100%">Login</button>
            </form>
            <p style="text-align:center; margin-top:15px;">
                Don't have an account? <a href="register.php">Register here</a>
            </p>
        </div>

        <div class="page-nav">
            <a href="register.php" class="nav-arrow"><i class="fas fa-chevron-left"></i> Previous: Register</a>
            <span><!-- No next here --></span>
        </div>
    </main>

    <footer>
        <p>&copy; <?php echo date("Y"); ?> QR BusGuard. All rights reserved.</p>
    </footer>
</body>
</html>
