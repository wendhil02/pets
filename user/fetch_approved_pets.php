<?php
session_start();
include '../internet/connect_ka.php';

if (!isset($_SESSION['email'])) {
    echo json_encode([]);
    exit;
}

$user_email = $_SESSION['email'];

$sql = "SELECT id, petname, status, status_changed_at, seen FROM pet 
        WHERE email = ? AND status = 'Approved' ORDER BY status_changed_at DESC";

$stmt = $conn->prepare($sql);
$stmt->bind_param("s", $user_email);
$stmt->execute();
$result = $stmt->get_result();

$pets = [];
while ($row = $result->fetch_assoc()) {
    $pets[] = [
        "id" => $row["id"],
        "petname" => $row["petname"],
        "approved_at" => date("F j, Y g:i A", strtotime($row["status_changed_at"])),
        "seen" => $row["seen"] // Track whether it is new or already seen
    ];
}

echo json_encode($pets);
?>

