<?php
include '../internet/connect_ka.php';

$data = json_decode(file_get_contents("php://input"), true);

if (!isset($data['pet_ids']) || !isset($data['schedule_date'])) {
    echo json_encode(["success" => false, "message" => "Invalid request"]);
    exit;
}

$pet_ids = $data['pet_ids'];
$schedule_date = $data['schedule_date'];

foreach ($pet_ids as $pet_id) {
    // Check if already scheduled
    $check = $conn->prepare("SELECT schedule_date FROM pet WHERE id = ?");
    $check->bind_param("i", $pet_id);
    $check->execute();
    $check->bind_result($existing_schedule);
    $check->fetch();
    $check->close();

    if ($existing_schedule) {
        echo json_encode(["success" => false, "message" => "Pet ID $pet_id is already scheduled"]);
        exit;
    }

    $stmt = $conn->prepare("UPDATE pet SET schedule_date = ? WHERE id = ?");
    $stmt->bind_param("si", $schedule_date, $pet_id);
    $stmt->execute();
}

echo json_encode(["success" => true]);
?>

