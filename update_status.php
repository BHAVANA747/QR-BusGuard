<?php
session_start();
require 'db_connect.php';

if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'admin') {
    header("Location: login.php");
    exit();
}

$type = $_GET['type']; // 'theft' or 'maintenance'
$id = $_GET['id'];

if ($type == 'theft') {
    $sql = "UPDATE theft_reports SET status = CASE WHEN status='Pending' THEN 'In Progress' ELSE 'Resolved' END WHERE id='$id'";
    $conn->query($sql);
    header("Location: dashboard_admin.php");
} elseif ($type == 'maintenance') {
     $sql = "UPDATE maintenance_reports SET status = CASE WHEN status='Reported' THEN 'Scheduled' ELSE 'Fixed' END WHERE id='$id'";
    $conn->query($sql);
    header("Location: dashboard_admin.php");
} else {
    echo "Invalid Request";
}
?>
