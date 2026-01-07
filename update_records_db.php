<?php
require 'db_connect.php';

// 1. Add columns to theft_reports (checking if not exists logic is complex in pure SQL, relying on error suppression or manual check)
// Since we might have added some in previous step (which was cancelled/didn't run fully), we just try adding.
$sql_alter = "ALTER TABLE theft_reports 
ADD COLUMN theft_type VARCHAR(100) NULL,
ADD COLUMN incident_time DATETIME NULL";
// 'proof_image' and 'admin_response' might already exist from previous attempt, if not we add them. 
// A robust way in raw PHP without procedures is just to run separate queries.

$conn->query("ALTER TABLE theft_reports ADD COLUMN proof_image VARCHAR(255) NULL");
$conn->query("ALTER TABLE theft_reports ADD COLUMN admin_response TEXT NULL");
$conn->query($sql_alter);

// 2. Create theft_records table
$sql_create = "CREATE TABLE IF NOT EXISTS theft_records (
    id INT(11) AUTO_INCREMENT PRIMARY KEY,
    original_report_id INT(11),
    user_id INT(11),
    bus_id INT(11),
    bus_stop VARCHAR(255),
    theft_type VARCHAR(100),
    description TEXT,
    proof_image VARCHAR(255),
    incident_time DATETIME,
    status VARCHAR(50),
    admin_response TEXT,
    archived_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
)";

if ($conn->query($sql_create) === TRUE) {
    echo "Theft Records table created/verified.";
} else {
    echo "Error: " . $conn->error;
}
?>
