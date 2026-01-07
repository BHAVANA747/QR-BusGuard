<?php
require 'db_connect.php';

// Add columns for Driver Maintenance Workflow
$sql = "ALTER TABLE maintenance_reports 
ADD COLUMN proof_image VARCHAR(255) NULL,
ADD COLUMN service_date DATE NULL,
ADD COLUMN service_time TIME NULL,
ADD COLUMN service_center VARCHAR(255) NULL";

if ($conn->query($sql) === TRUE) {
    echo "Database updated for Maintenance Service Workflow.";
} else {
    echo "Error (columns might exist): " . $conn->error;
}
?>
