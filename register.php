<?php
session_start();
require 'db_connect.php';

$message = "";

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $name = $_POST['name'];
    $email = $_POST['email'];
    $phone = $_POST['phone'];
    $password = password_hash($_POST['password'], PASSWORD_DEFAULT);
    $role = $_POST['role'];

    $check = "SELECT * FROM users WHERE email='$email'";
    $result = $conn->query($check);

    if ($result->num_rows > 0) {
        $message = "<div class='alert alert-error'>Email already exists!</div>";
    } else {
        $sql = "INSERT INTO users (name, email, phone, password, role) VALUES ('$name', '$email', '$phone', '$password', '$role')";
        
        if ($conn->query($sql) === TRUE) {
            $message = "<div class='alert alert-success'>Registration successful! <a href='login.php'>Login here</a></div>";
        } else {
            $message = "<div class='alert alert-error'>Error: " . $conn->error . "</div>";
        }
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Register - QR BusGuard</title>
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
                <li><a href="register.php" class="active">REGISTER</a></li>
                <li><a href="login.php">LOGIN</a></li>
            </ul>
        </div>
    </header>

    <main>
        <div class="form-container">
            <h2 style="text-align:center; margin-bottom: 20px;">Create Account</h2>
            <?php echo $message; ?>
            <form action="" method="POST">
                <div class="form-group">
                    <label>Full Name</label>
                    <input type="text" name="name" required>
                </div>
                <div class="form-group">
                    <label>Email Address</label>
                    <input type="email" name="email" required>
                </div>
                <div class="form-group">
                    <label>Phone Number</label>
                    <input type="text" name="phone" pattern="[0-9]{10}" maxlength="10" required class="form-control">

                </div>
                <div class="form-group">
                    <label>Password</label>
                    <input type="password" name="password" required>
                    
                </div>
                <div class="form-group">
                    <label>Register As</label>
                    <select name="role" required>
                        <option value="passenger">Passenger</option>
                        <option value="conductor">Conductor</option>
                        <option value="driver">Driver</option>
                        <option value="admin">Admin</option>
                    </select>
                </div>
                <button type="submit" class="btn-primary" style="width:100%">Register</button>
            </form>
        </div>

        <div class="page-nav">
            <a href="index.php" class="nav-arrow"><i class="fas fa-chevron-left"></i> Previous: Home</a>
            <a href="login.php" class="nav-arrow">Next: Login <i class="fas fa-chevron-right"></i></a>
        </div>
    </main>
    
    <footer>
        <p>&copy; <?php echo date("Y"); ?> QR BusGuard. All rights reserved.</p>
    </footer>
</body>
</html>
