<?php
session_start();
include '../internet/connect_ka.php';

if ($_SESSION['role'] !== 'admin') {
    header("Location: login.php");
    exit();
}

if ($_SERVER["REQUEST_METHOD"] === "POST") {
    $ticket_number = $_POST['ticket_number'];

    // Update ticket status to "Resolved"
    $stmt = $conn->prepare("UPDATE tickets SET status = 'Resolved' WHERE ticket_number = ?");
    $stmt->bind_param("s", $ticket_number);
    $stmt->execute();

    header("Location: admin_dashboard.php");
    exit();
}
?>
