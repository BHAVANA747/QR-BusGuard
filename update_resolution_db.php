<?php
require 'db_connect.php';

// Add columns for Advanced Resolution
$sql1 = "ALTER TABLE theft_reports 
ADD COLUMN proof_image VARCHAR(255) NULL,
ADD COLUMN admin_response TEXT NULL";

$sql2 = "ALTER TABLE maintenance_reports 
ADD COLUMN admin_response TEXT NULL";

$conn->query($sql1);
$conn->query($sql2);

echo "Database updated for Advanced Resolution System.";
?>
