<?php
session_start();
include '../internet/connect_ka.php';

$data = json_decode(file_get_contents("php://input"), true);

if (!isset($_SESSION['email']) || !isset($data['notification_id'])) {
    echo json_encode(["success" => false, "message" => "Unauthorized"]);
    exit;
}

$notification_id = $data['notification_id'];

$sql = "UPDATE pet SET seen = 1 WHERE id = ?";
$stmt = $conn->prepare($sql);
$stmt->bind_param("i", $notification_id);

if ($stmt->execute()) {
    echo json_encode(["success" => true]);
} else {
    echo json_encode(["success" => false, "message" => "Failed to update notification"]);
}
?>

