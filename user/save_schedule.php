<?php
session_start();
include '../internet/connect_ka.php';

header("Content-Type: application/json");

$data = json_decode(file_get_contents("php://input"), true);

if (!isset($_SESSION['email']) || !isset($data['pet_id']) || !isset($data['schedule_date'])) {
    echo json_encode(["success" => false, "message" => "Invalid request."]);
    exit;
}

$user_email = $_SESSION['email'];
$pet_id = $data['pet_id'];
$schedule_date = $data['schedule_date'];

// ✅ Update pet table with schedule
$sql = "UPDATE pet SET schedule_date = ? WHERE id = ? AND email = ?";
$stmt = $conn->prepare($sql);
$stmt->bind_param("sis", $schedule_date, $pet_id, $user_email);

if ($stmt->execute()) {
    echo json_encode(["success" => true]);
} else {
    echo json_encode(["success" => false, "message" => "Database error."]);
}
?>
