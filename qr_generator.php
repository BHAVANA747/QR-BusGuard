<?php
session_start();
require 'db_connect.php';

// Try to get local IP
$localIP = gethostbyname(gethostname());

// If that gives 127.0.0.1 or similar, try to parse from specific command (Windows specific)
if ($localIP == '127.0.0.1' || $localIP == '::1') {
    $ip_output = shell_exec("ipconfig");
    if (preg_match("/IPv4 Address.*: (192\.168\.\d+\.\d+)/", $ip_output, $matches)) {
        $localIP = $matches[1];
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>QR Code Manager - QR BusGuard</title>
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
                <li><a href="dashboard_admin.php">BACK TO DASHBOARD</a></li>
            </ul>
        </div>
    </header>

    <main>
<?php
// Try to get all local IPs
$ips = [];
$ip_output = shell_exec("ipconfig");
if (preg_match_all("/IPv4 Address.*: (192\.168\.\d+\.\d+|10\.\d+\.\d+\.\d+|172\.(?:1[6-9]|2\d|3[0-1])\.\d+\.\d+)/", $ip_output, $matches)) {
    $ips = array_unique($matches[1]);
}
// Fallback
if (empty($ips)) {
    $ips[] = gethostbyname(gethostname());
}

// Current selected IP
$selectedIP = isset($_GET['ip']) ? $_GET['ip'] : reset($ips);
?>
        <div style="text-align: center; margin-bottom: 2rem;">
            <h2>Bus QR Code Generator</h2>
            <p>Scan these codes with any phone on the same Wi-Fi network.</p>
            
            <div class="alert alert-info" style="display:inline-block; text-align:left;">
                <form action="" method="GET">
                    <label><strong>Select Your Wi-Fi IP:</strong></label>
                    <select name="ip" onchange="this.form.submit()" style="padding:5px; margin-left:10px;">
                        <?php foreach($ips as $ip): ?>
                            <option value="<?php echo $ip; ?>" <?php if($ip == $selectedIP) echo 'selected'; ?>>
                                <?php echo $ip; ?>
                            </option>
                        <?php endforeach; ?>
                    </select>
                </form>
                <small style="display:block; margin-top:5px; color:#555;">Try different IPs if one doesn't work!</small>
            </div>
        </div>

        <div class="features-grid">
            <?php
            $sql = "SELECT * FROM buses";
            $result = $conn->query($sql);
            while($row = $result->fetch_assoc()) {
                // Construct URL
                // Format: http://<IP>/Project/scan_handler.php?bus=<BUS_NUMBER>
                $url = "http://" . $selectedIP . "/Project/scan_handler.php?bus=" . urlencode($row['bus_number']);
                
                // QR Server API (Reliable)
                $qr_src = "https://api.qrserver.com/v1/create-qr-code/?size=200x200&data=" . urlencode($url);
                
                echo "<div class='card'>";
                echo "<h3>" . $row['bus_number'] . "</h3>";
                echo "<p style='color:white;'>Route: " . $row['route_no'] . "</p>";
                echo "<img src='$qr_src' alt='QR Code' border='0'>";
                echo "<br><a href='$url' target='_blank' style='display:block; margin-top:10px; font-size:0.8rem;'>Test Link</a>";
                echo "</div>";
            }
            ?>
        </div>
    </main>
</body>
</html>
