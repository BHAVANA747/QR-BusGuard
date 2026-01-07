<?php
session_start();
require 'db_connect.php';

if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'admin') {
    die("Access Denied");
}

$id = $_GET['id'] ?? null;

if ($id) {
    // 1. Fetch Original Report
    $sql_fetch = "SELECT * FROM theft_reports WHERE id='$id'";
    $result = $conn->query($sql_fetch);
    
    if ($result->num_rows > 0) {
        $row = $result->fetch_assoc();
        
        // 2. Prepare Data
        $original_id = $row['id'];
        $user_id = $row['user_id'];
        $bus_id = $row['bus_id'];
        $bus_stop = $conn->real_escape_string($row['bus_stop']);
        $theft_type = $conn->real_escape_string($row['theft_type']);
        $description = $conn->real_escape_string($row['description']);
        $proof_image = $conn->real_escape_string($row['proof_image']);
        $incident_time = $row['incident_time'];
        $status = $row['status'];
        $admin_response = $conn->real_escape_string($row['admin_response']);
        
        // 3. Insert into Records
        $sql_copy = "INSERT INTO theft_records 
        (original_report_id, user_id, bus_id, bus_stop, theft_type, description, proof_image, incident_time, status, admin_response) 
        VALUES 
        ('$original_id', '$user_id', '$bus_id', '$bus_stop', '$theft_type', '$description', '$proof_image', '$incident_time', '$status', '$admin_response')";
        
        if ($conn->query($sql_copy) === TRUE) {
             header("Location: dashboard_admin.php?msg=copied");
        } else {
             echo "Error copying: " . $conn->error;
        }
    } else {
        echo "Report not found.";
    }
} else {
    echo "ID missing.";
}
?>
