<?php
require 'db_connect.php';

// Add columns for return details to lost_items table
$sql = "ALTER TABLE lost_items 
ADD COLUMN return_location VARCHAR(100) NULL,
ADD COLUMN return_date DATE NULL,
ADD COLUMN return_time TIME NULL";

if ($conn->query($sql) === TRUE) {
    echo "Table 'lost_items' updated successfully.";
} else {
    echo "Error updating table: " . $conn->error;
}
$conn->close();
?>
