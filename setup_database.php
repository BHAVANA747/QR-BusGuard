<?php
$servername = "localhost";
$username = "root";
$password = "";

// Create connection
$conn = new mysqli($servername, $username, $password);

// Check connection
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// Create database
$sql = "CREATE DATABASE IF NOT EXISTS qr_busguard_db";
if ($conn->query($sql) === TRUE) {
    echo "Database created successfully\n";
} else {
    echo "Error creating database: " . $conn->error . "\n";
}

$conn->select_db("qr_busguard_db");

// Users Table
$sql = "CREATE TABLE IF NOT EXISTS users (
    id INT(6) UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    name VARCHAR(100) NOT NULL,
    email VARCHAR(100) NOT NULL UNIQUE,
    phone VARCHAR(15) NOT NULL,
    password VARCHAR(255) NOT NULL,
    role ENUM('passenger', 'conductor', 'driver', 'admin') NOT NULL,
    reg_date TIMESTAMP DEFAULT CURRENT_TIMESTAMP
)";
if ($conn->query($sql) === TRUE) echo "Table users created successfully\n";
else echo "Error creating table users: " . $conn->error . "\n";

// Buses Table
$sql = "CREATE TABLE IF NOT EXISTS buses (
    id INT(6) UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    bus_number VARCHAR(20) NOT NULL UNIQUE,
    route_no VARCHAR(50) NOT NULL,
    qr_code VARCHAR(100)
)";
if ($conn->query($sql) === TRUE) echo "Table buses created successfully\n";
else echo "Error creating table buses: " . $conn->error . "\n";

// Theft Reports Table
$sql = "CREATE TABLE IF NOT EXISTS theft_reports (
    id INT(6) UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    user_id INT(6) UNSIGNED,
    bus_id INT(6) UNSIGNED,
    bus_stop VARCHAR(100),
    description TEXT,
    status ENUM('Pending', 'In Progress', 'Resolved') DEFAULT 'Pending',
    report_date TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (user_id) REFERENCES users(id),
    FOREIGN KEY (bus_id) REFERENCES buses(id)
)";
if ($conn->query($sql) === TRUE) echo "Table theft_reports created successfully\n";
else echo "Error creating table theft_reports: " . $conn->error . "\n";

// Maintenance Reports Table
$sql = "CREATE TABLE IF NOT EXISTS maintenance_reports (
    id INT(6) UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    user_id INT(6) UNSIGNED,
    bus_id INT(6) UNSIGNED,
    issue_type VARCHAR(50),
    description TEXT,
    status ENUM('Reported', 'Scheduled', 'Fixed') DEFAULT 'Reported',
    report_date TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (user_id) REFERENCES users(id),
    FOREIGN KEY (bus_id) REFERENCES buses(id)
)";
if ($conn->query($sql) === TRUE) echo "Table maintenance_reports created successfully\n";
else echo "Error creating table maintenance_reports: " . $conn->error . "\n";

// Lost Items Table
$sql = "CREATE TABLE IF NOT EXISTS lost_items (
    id INT(6) UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    user_id INT(6) UNSIGNED,
    bus_id INT(6) UNSIGNED,
    item_name VARCHAR(100),
    description TEXT,
    image_path VARCHAR(255),
    status ENUM('Lost', 'Found', 'Returned') DEFAULT 'Lost',
    report_date TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (user_id) REFERENCES users(id),
    FOREIGN KEY (bus_id) REFERENCES buses(id)
)";
if ($conn->query($sql) === TRUE) echo "Table lost_items created successfully\n";
else echo "Error creating table lost_items: " . $conn->error . "\n";

// Found Items Table (Uploaded by Conductor)
$sql = "CREATE TABLE IF NOT EXISTS found_items (
    id INT(6) UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    conductor_id INT(6) UNSIGNED,
    bus_id INT(6) UNSIGNED,
    image_path VARCHAR(255),
    matched_lost_item_id INT(6) UNSIGNED NULL,
    status ENUM('Unclaimed', 'Claimed') DEFAULT 'Unclaimed',
    found_date TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (conductor_id) REFERENCES users(id),
    FOREIGN KEY (bus_id) REFERENCES buses(id)
)";
if ($conn->query($sql) === TRUE) echo "Table found_items created successfully\n";
else echo "Error creating table found_items: " . $conn->error . "\n";


// Insert Default Admin if not exists
$checkAdmin = "SELECT * FROM users WHERE email='admin@bmtc.com'";
$result = $conn->query($checkAdmin);
if ($result->num_rows == 0) {
    // Password is 'admin123'
    $adminPass = password_hash('admin123', PASSWORD_DEFAULT); 
    $sql = "INSERT INTO users (name, email, phone, password, role) VALUES ('Admin', 'admin@bmtc.com', '9999999999', '$adminPass', 'admin')";
    if ($conn->query($sql) === TRUE) echo "Default Admin user created\n";
}

// Insert Dummy Buses if not exists
$checkBus = "SELECT * FROM buses LIMIT 1";
$result = $conn->query($checkBus);
if ($result->num_rows == 0) {
    $sql = "INSERT INTO buses (bus_number, route_no) VALUES 
    ('KA-01-F-1234', '500-D'),
    ('KA-57-F-5678', '335-E'),
    ('KA-05-F-9012', 'V-500CA')";
    if ($conn->query($sql) === TRUE) echo "Dummy buses created\n";
}

$conn->close();
?>
