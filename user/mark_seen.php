<?php
session_start();
include '../internet/connect_ka.php';

if (!isset($_SESSION['email'])) {
    echo json_encode(["success" => false, "message" => "User not logged in"]);
    exit;
}

$user_email = $_SESSION['email'];

// ✅ I-update ang lahat ng seen = 0 pets para maging seen = 1
$sql = "UPDATE pet SET seen = 1 WHERE email = ? AND status = 'Approved' AND seen = 0";
$stmt = $conn->prepare($sql);
$stmt->bind_param("s", $user_email);

if ($stmt->execute()) {
    echo json_encode(["success" => true]);
} else {
    echo json_encode(["success" => false, "message" => "Failed to update"]);
}
?>
