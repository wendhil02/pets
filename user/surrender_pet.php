<?php
session_start();
include '../internet/connect_ka.php';



if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (!isset($_POST['pet_id'])) {
        echo json_encode(["success" => false, "message" => "Pet ID is missing."]);
        exit;
    }

    $pet_id = intval($_POST['pet_id']);
    $update_query = "UPDATE pet SET status='pending' WHERE id = ?";
    $stmt = $conn->prepare($update_query);
    $stmt->bind_param("i", $pet_id);

    if ($stmt->execute()) {
        echo json_encode(["success" => true, "message" => "Pet marked as pending."]);
    } else {
        echo json_encode(["success" => false, "message" => "Failed to update status."]);
    }

    $stmt->close();
    $conn->close();
}
?>

