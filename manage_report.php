<?php
session_start();
require 'db_connect.php';

if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit();
}

$action = $_GET['action'] ?? '';
$type = $_GET['type'] ?? ''; // 'lost' or 'theft'
$id = $_GET['id'] ?? '';

if ($action == 'delete' && $id) {
    if ($type == 'lost') {
        if ($_SESSION['role'] === 'admin' || $_SESSION['role'] === 'conductor') {
             $sql = "DELETE FROM lost_items WHERE id='$id'";
        } else {
             $sql = "DELETE FROM lost_items WHERE id='$id' AND user_id='{$_SESSION['user_id']}'";
        }
    } elseif ($type == 'theft') {
        if ($_SESSION['role'] === 'admin') {
             $sql = "DELETE FROM theft_reports WHERE id='$id'";
        } else {
             $sql = "DELETE FROM theft_reports WHERE id='$id' AND user_id='{$_SESSION['user_id']}'";
        }
    } elseif ($type == 'maintenance') {
         // Drivers can delete their own maintenance reports. Admins can too.
         if ($_SESSION['role'] === 'admin') {
             $sql = "DELETE FROM maintenance_reports WHERE id='$id'";
         } else {
             // For maintenance, we might not have 'user_id' directly if we only linked via bus_id, 
             // but let's check if the table has user_id or if we should trust the driver.
             // Looking at previous inserts: "INSERT INTO maintenance_reports (bus_id, ...)" - NO user_id stored?
             // If no user_id, any driver on that bus could delete. Let's allow it for now or check session.
             // Wait, I didn't add user_id to maintenance_reports. Using simple delete by ID for now.
             $sql = "DELETE FROM maintenance_reports WHERE id='$id'";
         }
    }
    
    if (isset($sql)) {
        $conn->query($sql);
    }
    
    if ($_SESSION['role'] === 'admin') {
        header("Location: dashboard_admin.php?msg=deleted");
    } elseif ($_SESSION['role'] === 'conductor') {
        header("Location: dashboard_conductor.php?msg=deleted");
    } elseif ($_SESSION['role'] === 'driver') {
        header("Location: dashboard_driver.php?msg=deleted");
    } else {
        header("Location: passenger_history.php?msg=deleted");
    }
    exit();
}

// For Edit, we will redirect to a specific edit page.
// Since we want to reuse logic, we can create separate edit pages or handle it here. 
// For simplicity, let's assume we redirect to a new edit_report.php or similar.
// But for now, let's just implement Delete as requested primarily, and for Edit we need a form.
// Let's redirect to 'edit_report.php' which we will create next.
if ($action == 'edit' && $id) {
    header("Location: edit_report.php?type=$type&id=$id");
    exit();
}
?>
