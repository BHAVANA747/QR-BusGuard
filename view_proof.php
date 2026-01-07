<?php
session_start();
// Simple secure image viewer
if (!isset($_GET['file'])) {
    $error = "No file specified.";
} else {
    // sanitize
    $file = str_replace("\0", '', $_GET['file']);
    
    // disallow suspicious traversal
    if (strpos($file, '..') !== false) {
        $error = "Invalid file path.";
    } else {
        $uploads_dir = realpath(__DIR__ . '/uploads');
        $requested = realpath(__DIR__ . '/' . $file);

        if ($requested && $uploads_dir && strpos($requested, $uploads_dir) === 0 && file_exists($requested)) {
            $image_url = htmlspecialchars($file);
            $file_name = basename($requested);
        } else {
            $error = "File not found or not allowed.";
        }
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>View Proof - QR BusGuard</title>
    <link rel="stylesheet" href="css/style.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    <style>
        .viewer { text-align:center; padding:20px; }
        .viewer img { max-width: 100%; max-height: calc(100vh - 120px); border:1px solid #ddd; border-radius:4px; box-shadow: 0 4px 8px rgba(0,0,0,0.1); }
        /* Custom Header Button Styles */
        .btn-header-back {
            color: white;
            text-decoration: none;
            font-weight: 500;
            margin-right: 20px;
        }
        .btn-header-download {
            background-color: #dc3545; /* Red */
            color: white;
            padding: 8px 15px;
            border-radius: 4px;
            text-decoration: none;
            font-weight: 500;
            transition: background 0.3s;
        }
        .btn-header-download:hover {
            background-color: #c82333;
        }
    </style>
</head>
<body>
    <header>
        <div class="navbar">
            <div class="logo-container">
                <img src="images/logo.jpg" alt="Logo" style="width:50px; height:50px; object-fit:contain;">
            <span class="logo-text">VIEW PROOF</span>
    </div>
            <ul class="nav-links" style="display:flex; align-items:center;">
                <!-- Back Link in Menu Bar -->
                <?php
                $back_url = "dashboard_admin.php"; // Default
                if (isset($_GET['source']) && $_GET['source'] == 'driver') {
                    $back_url = "dashboard_driver.php";
                }
                ?>
                <li><a href="<?php echo $back_url; ?>" class="btn-header-back"><i class="fas fa-arrow-left"></i> BACK</a></li>
                <!-- Download Button (Red) in Menu Bar (Top Right) -->
                <?php if (!isset($error)): ?>
                <li><a href="<?php echo $image_url; ?>" download class="btn-header-download"><i class="fas fa-download"></i> DOWNLOAD</a></li>
                <?php endif; ?>
            </ul>
        </div>
    </header>

    <main>
        <div class="viewer">
            <?php if (isset($error)): ?>
                <div class="card" style="max-width:500px; margin:50px auto; color:#dc3545;">
                    <h3><i class="fas fa-exclamation-circle"></i> Error</h3>
                    <p><?php echo htmlspecialchars($error); ?></p>
                    <a href="dashboard_admin.php" class="btn-primary" style="margin-top:15px; display:inline-block;">Return to Dashboard</a>
                </div>
            <?php else: ?>
                <!-- Just the image, no extra buttons here -->
                <img src="<?php echo $image_url; ?>" alt="Proof Image">
            <?php endif; ?>
        </div>
    </main>
</body>
</html>