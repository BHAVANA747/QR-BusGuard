<?php
session_start();
require 'db_connect.php';

if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['found_id'])) {
    
    $found_id = $_POST['found_id'];
    $return_location = $_POST['return_location'];
    $return_date = $_POST['return_date'];
    // Combine Time inputs
    $hour = $_POST['return_hour'];
    $min = $_POST['return_min'];
    $ampm = $_POST['return_ampm'];
    $return_time = date("H:i:s", strtotime("$hour:$min $ampm"));
    
    // Update Found Item Status
    $sql_found = "UPDATE found_items SET status='Claimed' WHERE id='$found_id'";
    $conn->query($sql_found);

    // Get the matched lost item ID
    $sql_get = "SELECT matched_lost_item_id FROM found_items WHERE id='$found_id'";
    $result = $conn->query($sql_get);
    if ($row = $result->fetch_assoc()) {
        $lost_id = $row['matched_lost_item_id'];
        
        // Update Lost Item Status AND Return Details
        $sql_lost = "UPDATE lost_items SET 
            status='Found', 
            return_location='$return_location', 
            return_date='$return_date', 
            return_time='$return_time' 
            WHERE id='$lost_id'";
            
        $conn->query($sql_lost);
    }
    
    // Redirect back with success
    header("Location: dashboard_conductor.php?success=1");
    exit();
}
?>
